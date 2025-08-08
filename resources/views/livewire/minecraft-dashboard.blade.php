<div>
    <div class="whitespace-pre-wrap bg-gray-900 text-gray-200 p-4 font-mono max-h-[400px] overflow-y-auto">
        {{ $logContent }}
    </div>
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
    <button
        wire:click="clearLogs"
        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded transition"
    >
        Clear Logs
    </button>

    <!-- Banned IPs Table -->
    <h2 class="mt-8 font-bold text-lg text-gray-800 dark:text-gray-100">Banned IPs</h2>
    <table class="w-full text-left border-collapse border border-gray-300">
        <thead>
        <tr>
            <th class="border border-gray-300 p-2">IP</th>
            <th class="border border-gray-300 p-2">Created</th>
            <th class="border border-gray-300 p-2">Source</th>
            <th class="border border-gray-300 p-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($bannedIps as $index => $ip)
            <tr>
                <td class="border border-gray-300 p-2">{{ $ip['ip'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">{{ date('Y-m-d H:i:s', $ip['created'] ?? 0) }}</td>
                <td class="border border-gray-300 p-2">{{ $ip['source'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">
                    <button wire:click="deleteBannedIp({{ $index }})" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Delete</button>
                </td>
            </tr>
        @endforeach
        <tr>
            <td class="border border-gray-300 p-2">
                <input wire:model.defer="newBannedIp" type="text" placeholder="New IP" class="w-full p-1 border rounded" />
            </td>
            <td class="border border-gray-300 p-2"></td>
            <td class="border border-gray-300 p-2"></td>
            <td class="border border-gray-300 p-2">
                <button wire:click="addBannedIp" class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Add</button>
            </td>
        </tr>
        </tbody>
    </table>

    <!-- Banned Players Table -->
    <h2 class="mt-8 font-bold text-lg text-gray-800 dark:text-gray-100">Banned Players</h2>
    <table class="w-full text-left border-collapse border border-gray-300">
        <thead>
        <tr>
            <th class="border border-gray-300 p-2">UUID</th>
            <th class="border border-gray-300 p-2">Name</th>
            <th class="border border-gray-300 p-2">Created</th>
            <th class="border border-gray-300 p-2">Source</th>
            <th class="border border-gray-300 p-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($bannedPlayers as $index => $player)
            <tr>
                <td class="border border-gray-300 p-2">{{ $player['uuid'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">{{ $player['name'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">{{ date('Y-m-d H:i:s', $player['created'] ?? 0) }}</td>
                <td class="border border-gray-300 p-2">{{ $player['source'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">
                    <button wire:click="deleteBannedPlayer({{ $index }})" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Delete</button>
                </td>
            </tr>
        @endforeach
        <tr>
            <td class="border border-gray-300 p-2"></td>
            <td class="border border-gray-300 p-2">
                <input wire:model.defer="newBannedPlayer" type="text" placeholder="New Player Name" class="w-full p-1 border rounded" />
            </td>
            <td class="border border-gray-300 p-2"></td>
            <td class="border border-gray-300 p-2"></td>
            <td class="border border-gray-300 p-2">
                <button wire:click="addBannedPlayer" class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Add</button>
            </td>
        </tr>
        </tbody>
    </table>

    <!-- Ops Table -->
    <h2 class="mt-8 font-bold text-lg text-gray-800 dark:text-gray-100">Operators</h2>
    <table class="w-full text-left border-collapse border border-gray-300">
        <thead>
        <tr>
            <th class="border border-gray-300 p-2">UUID</th>
            <th class="border border-gray-300 p-2">Name</th>
            <th class="border border-gray-300 p-2">Level</th>
            <th class="border border-gray-300 p-2">Bypasses Player Limit</th>
            <th class="border border-gray-300 p-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($ops as $index => $op)
            <tr>
                <td class="border border-gray-300 p-2">{{ $op['uuid'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">{{ $op['name'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">{{ $op['level'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">{{ $op['bypassesPlayerLimit'] ? 'Yes' : 'No' }}</td>
                <td class="border border-gray-300 p-2">
                    <button wire:click="deleteOp({{ $index }})" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Delete</button>
                </td>
            </tr>
        @endforeach
        <tr>
            <td class="border border-gray-300 p-2">
                <input wire:model.defer="newOpUuid" type="text" placeholder="UUID" class="w-full p-1 border rounded" />
            </td>
            <td class="border border-gray-300 p-2">
                <input wire:model.defer="newOpName" type="text" placeholder="Name" class="w-full p-1 border rounded" />
            </td>
            <td class="border border-gray-300 p-2">4</td>
            <td class="border border-gray-300 p-2">No</td>
            <td class="border border-gray-300 p-2">
                <button wire:click="addOp" class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Add</button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
