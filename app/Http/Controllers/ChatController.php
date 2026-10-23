<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Services\OpenRouterService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat', [
            'conversations' => Conversation::latest('updated_at')
                ->limit(30)
                ->get(),
            'activeConversation' => null,
            'initialMessages' => [],
        ]);
    }

    public function show(Conversation $conversation)
    {
        $initialMessages = $conversation->messages()
            ->get()
            ->map(fn (ChatMessage $message) => [
                'role' => $message->role,
                'content' => $message->content,
            ])
            ->values()
            ->all();

        return view('chat', [
            'conversations' => Conversation::latest('updated_at')
                ->limit(30)
                ->get(),
            'activeConversation' => $conversation,
            'initialMessages' => $initialMessages,
        ]);
    }

    public function send(
        Request $request,
        OpenRouterService $openRouter
    ) {
        set_time_limit(90);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'conversation_id' => [
                'nullable',
                'integer',
                'exists:conversations,id',
            ],
        ]);

        $message = trim($validated['message']);

        $conversation = ! empty($validated['conversation_id'])
            ? Conversation::findOrFail($validated['conversation_id'])
            : null;

        // Ambil maksimal 10 pesan sebelumnya sebagai konteks AI.
        $history = $conversation
            ? $conversation->messages()
                ->orderByDesc('id')
                ->limit(10)
                ->get()
                ->reverse()
                ->map(fn (ChatMessage $item) => [
                    'role' => $item->role,
                    'content' => $item->content,
                ])
                ->values()
                ->all()
            : [];

        try {
            // Panggil AI sebelum menyimpan pasangan pesan baru.
            $reply = $openRouter->chat($history, $message);

            // Buat percakapan saat pesan pertama berhasil diproses.
            if (! $conversation) {
                $title = preg_replace('/\s+/', ' ', $message);
                $title = mb_substr(trim($title), 0, 70);

                $conversation = Conversation::create([
                    'title' => $title !== '' ? $title : 'Percakapan baru',
                ]);
            }

            $conversation->messages()->create([
                'role' => 'user',
                'content' => $message,
            ]);

            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $reply,
            ]);

            $conversation->touch();

            return response()->json([
                'reply' => $reply,
                'conversation_id' => $conversation->id,
                'title' => $conversation->title,
            ]);
        } catch (ConnectionException $exception) {
            Log::warning('Koneksi ke OpenRouter gagal.', [
                'exception' => $exception::class,
            ]);

            return response()->json([
                'message' => 'Tidak dapat terhubung ke layanan AI. Coba lagi nanti.',
            ], 503);
        } catch (RuntimeException $exception) {
            Log::warning('Pemrosesan OpenRouter gagal.', [
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'AI gagal memberikan jawaban. Periksa log Laravel.',
            ], 502);
        }
    }
}