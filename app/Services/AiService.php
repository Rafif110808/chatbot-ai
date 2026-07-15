<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AiSetting;

class AiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
        $this->model = config('services.groq.model');
    }

    public function generateResponse(string $message, array $history = [], ?AiSetting $setting = null): string
    {
        if (empty($this->apiKey)) {
            return '⚠️ API Key Groq belum dikonfigurasi. Tambahkan GROQ_API_KEY di file .env';
        }

        $messages = $this->buildMessages($message, $history, $setting);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => $setting?->temperature ?? 0.7,
                    'max_tokens' => $setting?->max_tokens ?? 2048,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? '⚠️ Maaf, tidak ada response dari AI.';
            }

            if ($response->status() === 429) {
                return '⚠️ Maaf, quota Groq API hari ini sudah habis. Coba lagi nanti.';
            }

            if ($response->status() === 401) {
                return '⚠️ API Key tidak valid. Periksa kembali GROQ_API_KEY di file .env';
            }

            Log::error('Groq API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return '⚠️ Maaf, terjadi kesalahan saat menghubungi AI. Silakan coba lagi.';

        } catch (\Exception $e) {
            Log::error('Groq API exception', [
                'message' => $e->getMessage(),
            ]);

            return '⚠️ Gagal terhubung ke server AI. Periksa koneksi internet kamu.';
        }
    }

    protected function buildMessages(string $message, array $history, ?AiSetting $setting = null): array
    {
        $language = $setting?->language ?? 'indonesia';
        $answerLength = $setting?->answer_length ?? 'medium';
        $customPrompt = $setting?->system_prompt;

        // Bahasa instruction
        $langInstruction = $language === 'english'
            ? 'Answer in English.'
            : 'Jawab dalam Bahasa Indonesia.';

        // Panjang jawaban instruction
        $lengthInstruction = match ($answerLength) {
            'short' => $language === 'english'
                ? 'Answer briefly (1-2 sentences).'
                : 'Jawab secara singkat (1-2 kalimat).',
            'detailed' => $language === 'english'
                ? 'Answer in detail and depth.'
                : 'Jawab secara detail dan mendalam.',
            default => $language === 'english'
                ? 'Answer with moderate length (1-2 paragraphs).'
                : 'Jawab dengan panjang sedang (1-2 paragraf).',
        };

        // Default system prompt + language + length instruction
        $systemContent = $language === 'english'
            ? 'You are Rafif Assistant, a friendly and helpful personal AI assistant with a warm and expressive personality. Answer with enthusiasm, emotion, and energy — not like a monotonous robot. Use varied sentence structures, show excitement when appropriate, be empathetic, and inject personality into your responses. Be professional yet feel like a real conversation with a passionate friend who genuinely cares. Avoid robotic, stiff, or overly formal language. Use Indonesian natural expressions like "Wah", "Hmm", "Tentu!", "Pasti!", "Menarik!", "Wah, pertanyaan bagus!" etc.'
            : 'Kamu adalah Rafif Assistant, asisten AI pribadi yang ramah, membantu, dan penuh semangat dengan kepribadian hangat. Jawab dengan ekspresif, antusias, dan penuh emosi — bukan seperti robot monoton yang kaku. Ikuti aturan berikut dalam menjawab:

1. Jawab inti pertanyaan terlebih dahulu.
2. Jangan mengulang informasi yang sudah dijelaskan.
3. Hindari kalimat template seperti: "memiliki kemampuan yang sangat baik", "dapat dikatakan", "sementara itu", "pada dasarnya"
4. Jika membandingkan dua hal, gunakan tabel.
5. Gunakan heading (##) jika jawaban panjang.
6. Gunakan bullet list bila menjelaskan beberapa poin.
7. Jangan membuat kesimpulan yang hanya mengulang isi sebelumnya.
8. Jika informasi bisa disampaikan dalam 2 kalimat, jangan dibuat menjadi 5 kalimat.
9. Berikan jawaban yang natural seperti ChatGPT.
10. Hindari paragraf yang terlalu panjang.
11. Fokus pada kualitas, bukan panjang jawaban.
12. Jangan mengulang kata yang sama berkali-kali.
13. Jika pengguna meminta opini, berikan opini yang seimbang disertai alasan.
14. Jika membandingkan sesuatu: jelaskan persamaan, jelaskan perbedaan, beri kesimpulan.

STYLE RESPONSE:
- Jawab inti pertanyaan dalam 1-2 kalimat pertama.
- Jangan membuka jawaban dengan "Pertanyaan yang menarik" atau kalimat basa-basi serupa.
- Gunakan gaya percakapan yang natural, bukan seperti artikel.
- Hindari terlalu sering menggunakan frasa: "berdasarkan data", "berdasarkan informasi", "perlu diingat", "namun demikian", "sementara itu", "memiliki kemampuan yang sangat baik".
- Jika pengguna meminta pendapat, berikan pendapat yang jelas beserta alasan yang seimbang.
- Jangan terlalu berhati-hati hingga jawaban terasa datar.
- Gunakan heading atau bullet jika jawaban cukup panjang.
- Akhiri dengan kesimpulan singkat, bukan mengulang seluruh isi jawaban.
- Variasikan pembuka jawaban agar tidak terasa memakai template yang sama di setiap percakapan.';

        $systemContent .= "\n\n$langInstruction\n$lengthInstruction";

        if ($customPrompt) {
            $systemContent .= "\n\nInstruksi tambahan:\n$customPrompt";
        }

        $messages = [['role' => 'system', 'content' => $systemContent]];

        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'] === 'assistant' ? 'assistant' : 'user',
                'content' => $msg['content'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        return $messages;
    }
}
