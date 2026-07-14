<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
        $this->model = config('services.groq.model');
    }

    public function generateResponse(string $message, array $history = []): string
    {
        if (empty($this->apiKey)) {
            return '⚠️ API Key Groq belum dikonfigurasi. Tambahkan GROQ_API_KEY di file .env';
        }

        $messages = $this->buildMessages($message, $history);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 2048,
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

    protected function buildMessages(string $message, array $history): array
    {
        $systemContent = 'Kamu adalah Rafif Assistant, asisten AI pribadi yang ramah, membantu, dan berbahasa Indonesia. Jawab dengan jelas, ramah, dan informatif. Gunakan bahasa Indonesia yang baik dan natural.';

        $messages = [
            [
                'role' => 'system',
                'content' => $systemContent,
            ],
        ];

        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'] === 'assistant' ? 'assistant' : 'user',
                'content' => $msg['content'],
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        return $messages;
    }
}
