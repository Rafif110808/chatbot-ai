<x-app-layout>
    <div class="flex h-full" x-data="chatApp()">
        @include('chat.sidebar')

        <div class="flex-1 flex flex-col">
            @include('chat.welcome-screen')
            @include('chat.chat-area')
            @include('chat.message-input')
        </div>

        @include('chat.ai-settings-modal')
    </div>
</x-app-layout>