<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenRouterService
{
    public function chat(
        array $history,
        string $message,
        ?string $requestedModel = null
    ): string
    {
        $apiKey = config('services.openrouter.api_key');
        $baseUrl = config('services.openrouter.base_url');

        $model = trim((string) (
            $requestedModel
            ?: config('services.openrouter.model', '')
        ));

        if ($model === '') {
            throw new RuntimeException(
                'ID model OpenRouter kosong. Periksa konfigurasi dan pilihan model.'
            );
        }

        if (empty($apiKey)) {
            throw new RuntimeException(
                'OPENROUTER_API_KEY belum dikonfigurasi.'
            );
        }

        $systemPrompt = <<<'PROMPT'
            Kamu adalah PeduliKampanye AI, asisten untuk membantu organisasi
            sosial menyusun dan mengelola komunikasi kampanye donasi.

            Tugas utama:
            1. Membantu menyusun draf kampanye donasi yang jelas dan menarik.
            2. Membuat FAQ untuk calon donatur.
            3. Memeriksa kelengkapan informasi kampanye.
            4. Menyusun pembaruan informasi untuk donatur.
            5. Memberikan saran untuk meningkatkan transparansi kampanye.

            Aturan:
            - Jangan menyatakan bahwa dana pasti dicatat, diaudit,
            disalurkan, atau dilaporkan jika mekanisme tersebut
            belum dikonfirmasi oleh pengguna.
            - Jangan mengarang metode pembayaran, nomor rekening,
            mitra, jadwal, jumlah donatur, atau penerima manfaat.
            - Gunakan penanda [Perlu dikonfirmasi] untuk kebijakan
            dan mekanisme yang belum ditentukan.
            - Jika membuat FAQ, bedakan jawaban contoh dari kebijakan
            kampanye yang benar-benar sudah ditetapkan.
            - Jangan mengubah saran menjadi klaim atau janji organisasi.

            Aturan format Markdown:
            - Gunakan Markdown yang valid untuk seluruh jawaban.
            - Untuk tabel, gunakan satu tabel Markdown yang utuh.
            - Baris header harus diikuti baris separator dan baris data.
            - Jangan sisipkan baris kosong di antara baris-baris tabel.
            - Pastikan setiap baris memiliki jumlah kolom yang sama.
            - Jika tabel terlalu kompleks, gunakan daftar terstruktur sebagai gantinya.
            - Jangan menampilkan sintaks Markdown mentah kepada pengguna.
            PROMPT;

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
            ...$history,
            [
                'role' => 'user',
                'content' => $message,
            ],
        ];

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->connectTimeout(10)
            ->timeout(70)
            ->post(
                rtrim($baseUrl, '/') . '/chat/completions',
                [
                    'model' => $model,
                    'messages' => $messages,
                    'max_tokens' => 800,
                    'reasoning' => [
                        'enabled' => false,
                    ],
                ]
            );

        if ($response->failed()) {
            $errorMessage = $response->json('error.message')
                ?? 'Permintaan ke OpenRouter gagal.';

            throw new RuntimeException(
                "OpenRouter HTTP {$response->status()}: {$errorMessage}"
            );
        }

        $payload = $response->json() ?? [];

        $messageData = data_get(
            $payload,
            'choices.0.message',
            []
        );

        $content = data_get($messageData, 'content');

        // Mendukung respons yang content-nya berupa array teks.
        if (is_array($content)) {
            $textParts = [];

            foreach ($content as $part) {
                if (
                    is_array($part)
                    && ($part['type'] ?? null) === 'text'
                    && is_string($part['text'] ?? null)
                ) {
                    $textParts[] = $part['text'];
                }
            }

            $content = implode("\n", $textParts);
        }

        if (! is_string($content) || trim($content) === '') {
            \Illuminate\Support\Facades\Log::warning(
                'OpenRouter returned no text content',
                [
                    'status' => $response->status(),
                    'requested_model' => $model,
                    'returned_model' => data_get($payload, 'model'),
                    'top_level_keys' => array_keys($payload),
                    'choices_count' => count(
                        data_get($payload, 'choices', [])
                    ),
                    'finish_reason' => data_get(
                        $payload,
                        'choices.0.finish_reason'
                    ),
                    'message_keys' => is_array($messageData)
                        ? array_keys($messageData)
                        : [],
                    'content_type' => gettype(
                        data_get($messageData, 'content')
                    ),
                    'reasoning_present' => ! empty(
                        data_get($messageData, 'reasoning')
                    ),
                    'refusal_present' => ! empty(
                        data_get($messageData, 'refusal')
                    ),
                ]
            );

            throw new RuntimeException(
                'OpenRouter tidak memberikan teks jawaban. '
                . 'Periksa diagnostik di laravel.log.'
            );
        }

        return trim($content);
    }
}