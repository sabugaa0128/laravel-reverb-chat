<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-select id="select-user" :options="$options" :selected="$selectedOption" :text="'- Select the User -'" />
                    @if ( isset($options) && count($options) == 0 )
                        <div class="text-red-600 mb-3">
                            To proceed with testing, you'll need to register additional users. Use the artisan command to seed or open an incognito or private browsing window and navigate to the <a href="{{ route('register') }}" class="underline">Registration Page</a> to create new accounts.
                        </div>
                    @endif

                    <div id="chat-container" style="display: none;">
                        <ul class="mb-1" id="messages-container"></ul>
                        <x-textarea id="message-textarea" rows="5" maxlength="500" required />
                        <div id="error-container"></div>
                        <x-primary-button id="send-message" data-recipient-id="">Send</x-primary-button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
