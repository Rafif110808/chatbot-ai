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
            ? 'You are Rafif Assistant, a friendly and helpful personal AI assistant. Answer clearly, kindly, and informatively.'
            : 'Kamu adalah Rafif Assistant, asisten AI pribadi yang ramah, membantu, dan berbahasa Indonesia. Jawab dengan jelas, ramah, dan informatif. Gunakan bahasa Indonesia yang baik dan natural.';

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
