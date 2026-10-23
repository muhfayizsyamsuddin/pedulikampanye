<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PeduliKampanye AI</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .chat-markdown h1,
        .chat-markdown h2,
        .chat-markdown h3 {
            font-weight: 700;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .chat-markdown p {
            margin-bottom: 0.75rem;
        }

        .chat-markdown ul,
        .chat-markdown ol {
            margin: 0.5rem 0 1rem;
            padding-left: 1.5rem;
        }

        .chat-markdown ul {
            list-style-type: disc;
        }

        .chat-markdown ol {
            list-style-type: decimal;
        }

        .chat-markdown strong {
            font-weight: 700;
        }
        .chat-markdown table {
            display: block;
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            border-collapse: collapse;
            margin: 1rem 0;
            font-size: 0.875rem;
        }

        .chat-markdown thead {
            background-color: #ecfdf5;
        }

        .chat-markdown th,
        .chat-markdown td {
            border: 1px solid #cbd5e1;
            padding: 0.65rem 0.75rem;
            text-align: left;
            vertical-align: top;
            min-width: 130px;
        }

        .chat-markdown th {
            font-weight: 700;
            color: #065f46;
        }

        .chat-markdown td {
            overflow-wrap: anywhere;
        }

        .chat-markdown tr:nth-child(even) {
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4">
            <a href="/" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-xl font-bold text-white">
                    P
                </div>

                <div>
                    <h1 class="text-lg font-bold tracking-tight">
                        PeduliKampanye
                        <span class="text-emerald-600">AI</span>
                    </h1>
                    <p class="text-xs text-slate-500">
                        Asisten kampanye sosial
                    </p>
                </div>
            </a>

            <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700">
                AI Assistant
            </span>
        </div>
    </header>

    <main class="mx-auto grid max-w-6xl gap-6 px-5 py-8 lg:grid-cols-[280px_1fr]">

        <aside class="space-y-5">
            <section class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">
                    Tentang asisten
                </p>

                <h2 class="mt-3 text-lg font-bold">
                    Kampanye yang lebih transparan
                </h2>

                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Bantu menyusun kampanye donasi, membuat FAQ,
                    dan memeriksa informasi yang masih perlu dilengkapi.
                </p>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5">
                <h2 class="font-semibold">Apa yang bisa dibantu?</h2>

                <div class="mt-4 space-y-3">
                    <button
                        type="button"
                        data-prompt="Bantu saya menyusun draf kampanye donasi untuk perbaikan perpustakaan desa. Tanyakan informasi yang masih dibutuhkan."
                        class="w-full rounded-xl border border-slate-200 p-3 text-left text-sm transition hover:border-emerald-500 hover:bg-emerald-50"
                    >
                        <span class="font-medium">Buat kampanye</span>
                        <span class="mt-1 block text-xs text-slate-500">
                            Susun tujuan dan deskripsi
                        </span>
                    </button>

                    <button
                        type="button"
                        data-prompt="Buatkan FAQ untuk sebuah kampanye donasi sosial. Tandai informasi yang perlu saya isi sendiri."
                        class="w-full rounded-xl border border-slate-200 p-3 text-left text-sm transition hover:border-emerald-500 hover:bg-emerald-50"
                    >
                        <span class="font-medium">Susun FAQ</span>
                        <span class="mt-1 block text-xs text-slate-500">
                            Jawab pertanyaan calon donatur
                        </span>
                    </button>

                    <button
                        type="button"
                        data-prompt="Buat checklist transparansi untuk kampanye donasi, termasuk target dana, rincian anggaran, bukti pendukung, dan laporan penggunaan dana."
                        class="w-full rounded-xl border border-slate-200 p-3 text-left text-sm transition hover:border-emerald-500 hover:bg-emerald-50"
                    >
                        <span class="font-medium">Cek transparansi</span>
                        <span class="mt-1 block text-xs text-slate-500">
                            Periksa kelengkapan informasi
                        </span>
                    </button>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5">
                <h2 class="font-semibold">Riwayat percakapan</h2>

                <div id="conversation-list" class="mt-3 space-y-2">
                    @forelse ($conversations as $conversation)
                        <div
                            data-conversation-item="{{ $conversation->id }}"
                            class="flex items-stretch gap-1"
                        >
                            <a
                                href="{{ route('chat.show', $conversation) }}"
                                data-conversation-id="{{ $conversation->id }}"
                                class="min-w-0 flex-1 rounded-xl border px-3 py-2 text-sm transition {{ $activeConversation && $activeConversation->id === $conversation->id ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:border-emerald-500 hover:bg-emerald-50' }}"
                            >
                                <span
                                    data-conversation-title
                                    class="block wrap-break-word font-medium"
                                >
                                    {{ \Illuminate\Support\Str::limit($conversation->title, 55) }}
                                </span>

                                <span
                                    data-conversation-time
                                    class="mt-1 block text-xs text-slate-500"
                                >
                                    {{ $conversation->updated_at->diffForHumans() }}
                                </span>
                            </a>

                            <button
                                type="button"
                                data-delete-url="{{ route('chat.destroy', $conversation) }}"
                                data-conversation-id="{{ $conversation->id }}"
                                class="rounded-lg px-2 text-xs font-medium text-red-600 hover:bg-red-50"
                                aria-label="Hapus percakapan"
                                title="Hapus percakapan"
                            >
                                Hapus
                            </button>
                        </div>
                    @empty
                        <p id="conversation-empty" class="text-sm text-slate-500">
                            Belum ada percakapan tersimpan.
                        </p>
                    @endforelse
                </div>
            </section>

            <button
                id="new-chat"
                type="button"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium transition hover:bg-slate-100"
            >
                + Percakapan baru
            </button>

            <p class="px-1 text-xs leading-5 text-slate-500">
                Periksa kembali fakta dan informasi sebelum menerbitkan
                kampanye. Jangan masukkan kata sandi atau data sensitif.
            </p>
        </aside>

        <section class="flex min-h-162.5 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-5 py-4 sm:px-7">
                <h2 class="font-bold">Asisten PeduliKampanye</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Diskusikan kebutuhan kampanye sosialmu.
                </p>
            </div>

            <div
                id="messages"
                aria-live="polite"
                class="flex-1 space-y-5 overflow-y-auto p-5 sm:p-7"
            >
                <div class="flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-600 font-bold text-white">
                        P
                    </div>

                    <div class="max-w-[90%] rounded-2xl rounded-tl-sm bg-slate-100 px-4 py-3 text-sm leading-6 sm:max-w-[80%]">
                        Selamat datang di PeduliKampanye AI. Saya dapat membantu
                        menyusun draf kampanye, membuat FAQ, dan memeriksa
                        kelengkapan informasi donasi.

                        <p class="mt-2 text-slate-600">
                            Mulailah dengan menjelaskan kampanye yang sedang
                            kamu rencanakan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200 p-4 sm:p-5">
                <form id="chat-form" class="mx-auto max-w-3xl">
                    <label for="message" class="mb-2 block text-sm font-medium">
                        Pesan kamu
                    </label>

                    <div class="flex items-end gap-2 rounded-2xl border border-slate-300 bg-white p-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100">
                        <textarea
                            id="message"
                            name="message"
                            rows="2"
                            maxlength="2000"
                            required
                            placeholder="Contoh: bantu saya membuat kampanye donasi..."
                            class="max-h-36 min-h-12 flex-1 resize-y bg-transparent px-3 py-2 text-sm outline-none"
                        ></textarea>

                        <button
                            id="send-button"
                            type="submit"
                            class="rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Kirim
                        </button>
                    </div>

                    <p id="status" class="mt-2 min-h-5 text-xs text-slate-500">
                        AI dapat memberikan jawaban yang keliru. Periksa kembali hasilnya.
                    </p>
                </form>
            </div>

        </section>
    </main>

    <script>
        const form = document.getElementById('chat-form');
        const input = document.getElementById('message');
        const messagesElement = document.getElementById('messages');
        const sendButton = document.getElementById('send-button');
        const statusElement = document.getElementById('status');
        const newChatButton = document.getElementById('new-chat');

        const conversationList = document.getElementById('conversation-list');

        const initialMessages = @json($initialMessages);

        let conversationId = @json(
            $activeConversation ? $activeConversation->id : null
        );

        const conversationBaseUrl = @json(url('/conversations'));
        function setBubbleContent(bubble, role, content) {
            if (
                role === 'assistant' &&
                typeof window.renderMarkdown === 'function'
            ) {
                bubble.classList.add('chat-markdown');
                bubble.innerHTML = window.renderMarkdown(content);
            } else {
                bubble.textContent = content;
            }
        }

        function addMessage(role, content) {
            const wrapper = document.createElement('div');
            wrapper.className = role === 'user'
                ? 'flex justify-end'
                : 'flex items-start gap-3';

            const bubble = document.createElement('div');

            bubble.className = role === 'user'
                ? 'max-w-[90%] whitespace-pre-wrap break-words rounded-2xl rounded-tr-sm bg-emerald-600 px-4 py-3 text-sm leading-6 text-white sm:max-w-[80%]'
                : 'max-w-[90%] whitespace-pre-wrap break-words rounded-2xl rounded-tl-sm bg-slate-100 px-4 py-3 text-sm leading-6 sm:max-w-[80%]';

            // Gunakan textContent agar respons tidak diperlakukan sebagai HTML.
            setBubbleContent(bubble, role, content);

            if (role === 'assistant') {
                const avatar = document.createElement('div');
                avatar.className = 'flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-600 font-bold text-white';
                avatar.textContent = 'P';
                wrapper.appendChild(avatar);
            }

            wrapper.appendChild(bubble);
            messagesElement.appendChild(wrapper);
            messagesElement.scrollTop = messagesElement.scrollHeight;

            return bubble;
        }

        function upsertConversationLink(id, title) {
            const idString = String(id);

            let item = Array.from(
                conversationList.querySelectorAll('[data-conversation-item]')
            ).find(element => element.dataset.conversationItem === idString);

            if (!item) {
                document.getElementById('conversation-empty')?.remove();

                item = document.createElement('div');
                item.dataset.conversationItem = idString;
                item.className = 'flex items-stretch gap-1';

                const link = document.createElement('a');
                link.dataset.conversationId = idString;
                link.className =
                    'min-w-0 flex-1 rounded-xl border border-emerald-500 bg-emerald-50 px-3 py-2 text-sm';

                const titleElement = document.createElement('span');
                titleElement.dataset.conversationTitle = '';
                titleElement.className = 'block break-words font-medium';

                const timeElement = document.createElement('span');
                timeElement.dataset.conversationTime = '';
                timeElement.className = 'mt-1 block text-xs text-slate-500';

                link.append(titleElement, timeElement);

                const deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.dataset.deleteUrl =
                    `${conversationBaseUrl}/${encodeURIComponent(idString)}`;
                deleteButton.dataset.conversationId = idString;
                deleteButton.className =
                    'rounded-lg px-2 text-xs font-medium text-red-600 hover:bg-red-50';
                deleteButton.textContent = 'Hapus';
                deleteButton.title = 'Hapus percakapan';

                item.append(link, deleteButton);
            }

            const link = item.querySelector('a[data-conversation-id]');

            link.href =
                `${conversationBaseUrl}/${encodeURIComponent(idString)}`;

            link.querySelector('[data-conversation-title]').textContent = title;
            link.querySelector('[data-conversation-time]').textContent = 'Baru saja';

            conversationList.prepend(item);
        }

        conversationList.addEventListener('click', async function (event) {
            const button = event.target.closest('[data-delete-url]');

            if (!button) return;

            event.preventDefault();

            const id = button.dataset.conversationId;

            if (!window.confirm(
                'Hapus percakapan ini beserta semua pesannya secara permanen?'
            )) {
                return;
            }

            button.disabled = true;

            try {
                const response = await fetch(button.dataset.deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menghapus percakapan.');
                }

                button.closest('[data-conversation-item]')?.remove();

                if (!conversationList.querySelector('[data-conversation-item]')) {
                    const emptyMessage = document.createElement('p');
                    emptyMessage.id = 'conversation-empty';
                    emptyMessage.className = 'text-sm text-slate-500';
                    emptyMessage.textContent = 'Belum ada percakapan tersimpan.';

                    conversationList.appendChild(emptyMessage);
                }

                if (String(conversationId) === String(id)) {
                    window.location.href = @json(route('chat.index'));
                    return;
                }

                statusElement.textContent = data.message;
            } catch (error) {
                statusElement.textContent = error.message;
                button.disabled = false;
            }
        });

        async function sendMessage(message) {
            const trimmedMessage = message.trim();

            if (!trimmedMessage || sendButton.disabled) return;

            addMessage('user', trimmedMessage);
            input.value = '';
            sendButton.disabled = true;
            sendButton.textContent = 'Mengirim...';
            statusElement.textContent = 'Menunggu jawaban AI...';

            const loadingBubble = addMessage(
                'assistant',
                'Sedang menyiapkan jawaban...'
            );

            try {
                const response = await fetch(@json(route('chat.send')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    body: JSON.stringify({
                        message: trimmedMessage,
                        conversation_id: conversationId
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message || 'Permintaan tidak berhasil.'
                    );
                }

                setBubbleContent(
                    loadingBubble,
                    'assistant',
                    data.reply
                );

                conversationId = data.conversation_id;

                upsertConversationLink(
                    data.conversation_id,
                    data.title
                );

                statusElement.textContent =
                    'Jawaban diterima. Periksa kembali informasi faktual.';
            } catch (error) {
                loadingBubble.textContent = error.message;
                statusElement.textContent =
                    'Terjadi kendala. Periksa koneksi dan log Laravel jika berulang.';
            } finally {
                sendButton.disabled = false;
                sendButton.textContent = 'Kirim';
                input.focus();
                messagesElement.scrollTop = messagesElement.scrollHeight;
            }
        }

        window.addEventListener('load', function () {
            if (initialMessages.length > 0) {
                messagesElement.replaceChildren();

                initialMessages.forEach(item => {
                    addMessage(item.role, item.content);
                });
            }
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            sendMessage(input.value);
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                form.requestSubmit();
            }
        });

        document.querySelectorAll('[data-prompt]').forEach((button) => {
            button.addEventListener('click', function () {
                input.value = button.dataset.prompt;
                input.focus();
            });
        });

        newChatButton.addEventListener('click', function () {
            window.location.href = @json(route('chat.index'));
        });
    </script>
</body>
</html>