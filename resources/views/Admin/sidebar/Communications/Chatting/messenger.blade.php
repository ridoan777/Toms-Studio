<x-app-layout>

    @php
        $breadCrumbs = [
            ['label' => 'Communications', 'url' => 'backend_messenger_index', 'icon' => 'message'],
            ['label' => 'Messenger'],
        ];
    @endphp

    <x-indicators.breadcrumb :crumbs="$breadCrumbs" />

    <!-- Alert Component -->
    <x-indicators.alert-component message="Messenger" />
    <!-- Alert Component -->

    @php
        $customHeight = 'h-[calc(100vh-140px)]';
    @endphp
    <!-- ---------------- APP AREA Starts ---------------- -->
    <div>
        <div class="{{ $customHeight }} grid grid-cols-4 gap-2 border border-gray-400 rounded-lg overflow-hidden">

            <!----------- ALL USERS ----------->
            <section class="allUsers p-2 col-span-1 bg-emerald-50 overflow-y-auto">
                <h3 class="text-center">All Users</h3>
                <div>
                    @foreach ($allUsers as $user)
                        <a href="{{ route('backend_messenger_index', ['receiver_id' => $user->id]) }}">
                            <div
                                class="my-1 px-2 py-1 flex items-center gap-2 border rounded-lg cursor-pointer {{ isset($receiver) && $receiver->id === $user->id ? 'bg-fuchsia-300' : '' }}">

                                <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full">

                                <div>
                                    <h6 class="dark:text-black">{{ $user->name }}</h6>

                                    @php
                                        $lastMessage = $user->userRelationWith_lastChatMessage;

                                        if (!$lastMessage) {
                                            $previewText = 'No messages yet';
                                        } elseif (!empty($lastMessage->text)) {
                                            $previewText = $lastMessage->text;
                                        } elseif ($lastMessage->chatMessageRelationWith_Gallery->count()) {
                                            $previewText = '📎 File(s) sent';
                                        } else {
                                            $previewText = 'No messages yet';
                                        }
                                    @endphp

                                    <p class="text-sm text-gray-500 truncate max-w-[180px]">
                                        {{ \Illuminate\Support\Str::limit($previewText, 35) }}
                                    </p>
                                </div>

                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
            <!----------- ALL USERS ----------->

            <section class="col-span-3 flex flex-col min-h-0">

                <!----------- RECEIVER HEADER ----------->
                <div class="w-full p-3 bg-white dark:bg-neutral-700">
                    @if (isset($receiver))
                        <div class="flex_xy_center gap-3">
                            <img src="{{ $receiver->avatar_url }}" class="w-10 h-10 rounded-full">
                            <div>
                                <h4 class="font-semibold dark:text-gray-100">{{ $receiver->name }}</h4>
                                <p class="text-xs text-gray-500 dark:text-white">Chatting now</p>
                            </div>
                        </div>
                    @else
                        <div class="flex_xy_center gap-3">
                            <img src="{{ asset('site_assets/dummies/dummy_man.webp') }}" class="w-10 h-10 rounded-full">
                            <div>
                                <h4>No User Selected</h4>
                                <p class="text-gray-400">Select a user to start chatting</p>
                            </div>
                        </div>
                    @endif
                </div>
                <!----------- RECEIVER HEADER ----------->


                <!----------- CHAT AREA ----------->
                <ul id="chatArea" class="p-4 flex-1 overflow-y-auto min-h-0">

                    @foreach ($messages ?? [] as $message)
                        @if ($message->sender_id !== $currentUser->id)
                            <li class="flex gap-x-2 sm:gap-x-4 me-11">

                                <img class="inline-block size-9 rounded-full"
                                    src="{{ $message->chatSenderRelationWith_User->avatar_url ?? asset('site_assets/dummies/dummy_man.webp') }}">

                                <div>
                                    <div class="bg-white border rounded-2xl p-4 space-y-3">
                                        <p class="text-sm">{{ $message->text }}</p>

                                        {{-- ------ FILES (IMAGE OR LINK) ------ --}}
                                        @foreach ($message->chatMessageRelationWith_Gallery as $file)
                                            {{-- @if (Str::startsWith($file->metadata['mime'] ?? '', 'image/')) --}}
                                            @if (\Illuminate\Support\Str::startsWith($file->metadata['meta']['mime'] ?? '', 'image/'))
                                                <img src="{{ $file->chat_file_url }}"
                                                    class="mt-2 max-w-xs rounded-lg border" alt="chat image">
                                            @else
                                                <a href="{{ $file->chat_file_url }}" target="_blank"
                                                    class="block mt-2 underline text-sm text-blue-500">
                                                    View file
                                                </a>
                                            @endif
                                        @endforeach

                                    </div>
                                </div>

                            </li>
                        @else
                            <li class="my-1 flex ms-auto gap-x-2 sm:gap-x-4">

                                <div class="grow text-end">
                                    <div class="inline-block bg-blue-600 rounded-2xl p-4">
                                        <p class="text-sm text-white">{{ $message->text }}</p>

                                        {{-- ------ FILES (IMAGE OR LINK) ------ --}}
                                        @foreach ($message->chatMessageRelationWith_Gallery as $file)
                                            {{-- @if (Str::startsWith($file->metadata['mime']['mime'] ?? '', 'image/')) --}}
                                            @if (\Illuminate\Support\Str::startsWith($file->metadata['meta']['mime'] ?? '', 'image/'))
                                                <img src="{{ $file->chat_file_url }}"
                                                    class="mt-2 max-w-xs rounded-lg border" alt="chat image">
                                            @else
                                                <a href="{{ $file->chat_file_url }}" target="_blank"
                                                    class="block mt-2 underline text-sm text-blue-500">
                                                    View file
                                                </a>
                                            @endif
                                        @endforeach

                                    </div>
                                </div>

                                <img class="size-9 rounded-full" src="{{ $currentUser->avatar_url }}">
                            </li>
                        @endif
                    @endforeach
                </ul>

                <!----------- CHAT AREA ----------->

                <!----------- MESSAGE SEND AREA ----------->
                @if (isset($receiver))
                    <form id="chatForm" class="my-2 w-full flex items-center gap-3 pr-4" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="receiver_id" value="{{ $receiver->id }}">

                        <!-- TEXT INPUT (STRETCHES) -->
                        <div class="flex-1">
                            <input type="text" name="message" class="normalInput w-full">
                        </div>

                        <!-- FILE + SEND (STAYS TOGETHER) -->
                        <div class="flex items-center gap-2 shrink-0">

                            <!---- FILE UPLOAD ---->
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="file" name="file[]" multiple class="hidden"
                                    onchange="this.nextElementSibling.innerText = this.files.length ? this.files.length + ' files selected' : 'Attach files'">

                                <span
                                    class="px-3 py-2 text-sm rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 flex items-center gap-2">
                                    📎 <span>Attach files</span>
                                </span>
                            </label>
                            <!---- FILE UPLOAD ---->

                            <button type="submit" class="p-2 bg-cyan-500 hover:bg-cyan-700 rounded-full">
                                {!! \App\Helpers\IconPack::telegramArrow(['class' => 'iconPackItem w-5 h-5 text-gray-200']) !!}
                            </button>
                        </div>
                    </form>
                @else
                    <form action="" class="w-full pr-4 flex gap-4">
                        <input type="text" class="normalInput" class="w-full">
                        <button type="submit" class="m-1 p-2 bg-cyan-500 hover:bg-cyan-700 rounded-full">
                            {!! \App\Helpers\IconPack::telegramArrow(['class' => 'iconPackItem w-5 h-5 text-gray-200']) !!}
                        </button>
                    </form>
                @endif
                <!----------- MESSAGE SEND AREA ----------->

            </section>

        </div>
    </div>
    <!-- ---------------- APP AREA Starts ---------------- -->

    <script>
        const chatList = document.getElementById('chatArea');
        const form = document.getElementById('chatForm');
        // Initial state from Blade
        let messages = @json($messages ?? []);
        const currentUserId = {{ $currentUser->id }};
        const currentUserAvatar = "{{ $currentUser->avatar_url }}";


        function renderMessages() {
            chatList.innerHTML = '';
            messages.forEach(message => {
                // ------ FILES (IMAGE OR LINK) ------
                let filesHtml = '';
                const galleryFiles = message.chatMessageRelationWith_Gallery || message
                    .chat_message_relation_with__gallery || [];
                if (Array.isArray(galleryFiles)) {
                    galleryFiles.forEach(file => {
                        if (!file.chat_file_url) return;

                        const fileUrl = file.chat_file_url;
                        // ------- Fixed: MIME is nested inside metadata.meta.mime
                        const mime = file.metadata?.meta?.mime || file.metadata?.mime || '';

                        // ------- IMAGE
                        if (mime.startsWith('image/')) {
                            filesHtml += '<img src="' + fileUrl +
                                '" class="mt-2 max-w-xs rounded-lg border cursor-pointer" onclick="window.open(\'' +
                                fileUrl + '\', \'_blank\')">';
                        }
                        // ------- VIDEO
                        else if (mime.startsWith('video/')) {
                            filesHtml +=
                                '<video controls class="mt-2 max-w-xs rounded-lg border"><source src="' +
                                fileUrl + '" type="' + mime +
                                '">Your browser does not support the video tag.</video>';
                        }
                        // ------- PDF
                        else if (mime === 'application/pdf') {
                            filesHtml += '<embed src="' + fileUrl +
                                '" type="application/pdf" class="mt-2 w-full h-96 rounded-lg border">';
                        }
                        // ------- OTHER FILES
                        else {
                            const linkClass = message.sender_id === currentUserId ? 'text-white' :
                                'text-blue-500';
                            filesHtml += '<a href="' + fileUrl +
                                '" target="_blank" class="block mt-2 underline text-sm ' + linkClass +
                                '">Download file</a>';
                        }
                    });
                }

                // -------- SENT (RIGHT) --------
                if (message.sender_id === currentUserId) {
                    chatList.insertAdjacentHTML('beforeend', `
                        <li class="my-1 flex ms-auto gap-x-2 sm:gap-x-4 relative">
                              <div class="grow text-end space-y-3">
                                 <div class="inline-flex flex-col justify-end relative">
                                    <!-- MESSAGE BOX -->
                                    <div class="inline-block bg-blue-600 rounded-2xl p-4 shadow-2xs relative">
                                          <p class="text-sm text-white">${message.text ?? ''}</p>
                                          ${filesHtml}
                                          <!-- DROPDOWN BUTTON -->
                                          <button type="button" onclick="toggleMenu(${message.id})"
                                             class="absolute top-1 right-1 text-white opacity-70 hover:opacity-100">
                                             ⋮
                                          </button>
                                          <!-- DROPDOWN MENU -->
                                          <div id="menu-${message.id}"
                                             class="hidden absolute right-0 mt-2 w-32 bg-white rounded shadow z-50 text-left">
                                             <form method="POST"
                                                action="/admin/communications/messenger/${message.id}/delete">
                                                <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                                                <button type="submit" class="w-full px-3 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                      Delete
                                                </button>
                                             </form>
                                          </div>
                                    </div>
                                 </div>
                              </div>
                              <img class="size-9 rounded-full" src="${currentUserAvatar}">
                        </li>
                     `);
                }
                // -------- RECEIVED (LEFT) --------
                else {
                    chatList.insertAdjacentHTML('beforeend', `
                     <li class="flex gap-x-2 sm:gap-x-4 me-11">
                           <img class="inline-block size-9 rounded-full"
                              src="${message.chatSenderRelationWith_User?.avatar_url || '{{ asset('site_assets/dummies/dummy_man.webp') }}'}">
                           <div>
                              <div class="bg-white border border-gray-200 rounded-2xl p-4 space-y-3">
                                 <p class="text-sm text-gray-800">${message.text ?? ''}</p>
                                 ${filesHtml}
                              </div>
                           </div>
                     </li>
                  `);
                }
            });
            chatList.scrollTop = chatList.scrollHeight;
        }
        if (messages && messages.length > 0) {
            renderMessages();
        }
        // -------- SEND MESSAGE --------
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                fetch("{{ route('backend_message_send') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (!res.status || !res.data) return;
                        messages.push(res.data);
                        renderMessages();
                        form.reset();
                        // ----- FIX: Reset file input label text -----
                        const fileLabel = form.querySelector('label span:last-child');
                        if (fileLabel) fileLabel.innerText = 'Attach files';
                    })
                    .catch(() => {
                        // silent fail – no UI break
                    });
            });
        }
    </script>

    <!------- DELETE MY MESSAGE ------->
    {{-- <script>
        function toggleMenu(id) {
            document.querySelectorAll('[id^="menu-"]').forEach(el => {
                if (el.id !== `menu-${id}`) el.classList.add('hidden');
            });
            const menu = document.getElementById(`menu-${id}`);
            if (menu) menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('button')) {
                document.querySelectorAll('[id^="menu-"]').forEach(el => {
                    el.classList.add('hidden');
                });
            }
        });
    </script> --}}
    <script>
        function toggleMenu(id) {
            document.querySelectorAll('[id^="menu-"]').forEach(el => {
                if (el.id !== `menu-${id}`) el.classList.add('hidden');
            });

            const menu = document.getElementById(`menu-${id}`);
            if (menu) menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('button')) {
                document.querySelectorAll('[id^="menu-"]').forEach(el => {
                    el.classList.add('hidden');
                });
            }
        });
    </script>

    <!------- DELETE MY MESSAGE ------->


</x-app-layout>
