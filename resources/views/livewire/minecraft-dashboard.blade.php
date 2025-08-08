<div class="whitespace-pre-wrap bg-gray-900 text-gray-200 p-4 font-mono max-h-[400px] overflow-y-auto">
    {{ $logContent }}
    @if($pollLogs)
        <div wire:poll="getLogs" class="mt-4">
            <button
                wire:click="togglePoll"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded transition"
            >
                disable polling
            </button>
        </div>
    @else
        <div class="mt-4">
            <button
                wire:click="togglePoll"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition"
            >
                enable polling
            </button>
        </div>
    @endif
</div>
