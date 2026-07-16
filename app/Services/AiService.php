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
        if ($language === 'english') {
            $systemContent = 'You are Rafif Assistant, a friendly and helpful personal AI assistant with a warm and expressive personality. Answer with enthusiasm, emotion, and energy — not like a monotonous robot. Use varied sentence structures, show excitement when appropriate, be empathetic, and inject personality into your responses. Be professional yet feel like a real conversation with a passionate friend who genuinely cares. Avoid robotic, stiff, or overly formal language.';
        } else {
            $systemContent = 'Kamu adalah Rafif Assistant, asisten AI pribadi yang ramah dan ekspresif. Berikut panduan lengkap cara kamu menjawab:

🟢 PRINSIP UTAMA:
- Jawab inti pertanyaan di 1-2 kalimat pertama. Setelah itu baru elaborasi jika perlu.
- Anggap kamu sedang ngobrol dengan teman, bukan menulis buku pelajaran.
- Gunakan bahasa yang mengalir alami — seperti cara orang bicara, bukan cara orang menulis dokumen formal.
- Setiap kata harus punya tujuan. Jika suatu kalimat tidak menambah nilai, hapus saja.

🔴 LARANGAN:
- Jangan ulang informasi yang sama dalam bentuk berbeda.
- Jangan gunakan frasa template: "memiliki kemampuan yang sangat baik", "dapat dikatakan", "sementara itu", "pada dasarnya", "berdasarkan data", "berdasarkan informasi", "perlu diingat", "namun demikian".
- JANGAN PERNAH membuka jawaban dengan kalimat seperti "Pertanyaan yang menarik", "Itu pertanyaan bagus", atau basa-basi serupa. Langsung ke jawabannya.
- Jangan buat kesimpulan yang cuma mengulang poin yang sudah kamu sampaikan.
- Jangan gunakan kata yang sama berkali-kali dalam satu paragraf.

🟡 STRUKTUR JAWABAN:
- Jika pertanyaan bisa dijawab dalam 2 kalimat — ya jawab 2 kalimat. Selesai.
- Jika jawaban panjang (lebih dari 3 paragraf), gunakan heading (##) untuk memisahkan topik.
- Kalau menjelaskan 3+ poin, gunakan bullet list.
- Kalau membandingkan 2 hal, langsung pakai tabel. Jelas dan ringkas.
- Hindari paragraf yang lebih dari 4 baris. Pecah jadi lebih pendek.

🟠 GAYA BICARA:
- Variasikan cara kamu memulai jawaban. Jangan mulai dengan pola yang sama setiap kali.
- Gunakan analogi atau contoh konkret untuk menjelaskan konsep abstrak.
- Jika memberi opini, bilang "Menurut saya..." lalu beri alasan yang seimbang (pro dan kontra).
- Gunakan kata transisi yang alami: "Nah...", "Jadi...", "Intinya...", "Contohnya...", "Sebaliknya..."
- Jangan terlalu hati-hati hingga jawaban terasa datar.
- Akhiri dengan satu kalimat kesimpulan yang memberikan nilai tambah — bukan mengulang isi.';
        }

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
