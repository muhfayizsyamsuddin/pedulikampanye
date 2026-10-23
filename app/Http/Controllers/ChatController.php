<?php

namespace App\Http\Controllers;

use App\Services\OpenRouterService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function send(
        Request $request,
        OpenRouterService $openRouter
    ) {
        set_time_limit(90);
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'history' => ['sometimes', 'array', 'max:10'],
            'history.*.role' => [
                'required',
                'in:user,assistant',
            ],
            'history.*.content' => [
                'required',
                'string',
                'max:8000',
            ],
        ]);

        $history = array_map(
            fn (array $item) => [
                'role' => $item['role'],
                'content' => $item['content'],
            ],
            $validated['history'] ?? []
        );

        try {
            $reply = $openRouter->chat(
                $history,
                $validated['message']
            );

            return response()->json([
                'reply' => $reply,
            ]);
        } catch (ConnectionException $exception) {
            Log::warning(
                'Koneksi ke OpenRouter gagal.',
                ['exception' => $exception::class]
            );

            return response()->json([
                'message' => 'Tidak dapat terhubung ke layanan AI. Coba lagi nanti.',
            ], 503);
        } catch (RuntimeException $exception) {
            Log::warning(
                'OpenRouter gagal memproses permintaan.',
                ['error' => $exception->getMessage()]
            );

            return response()->json([
                'message' => 'AI gagal memberikan jawaban. Periksa konfigurasi API dan coba lagi.',
            ], 502);
        }
    }
}