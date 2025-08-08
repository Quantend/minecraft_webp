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
            <th class="border border-gray-300 p-2">Expires</th>
            <th class="border border-gray-300 p-2">Reason</th>
            <th class="border border-gray-300 p-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($bannedIps as $index => $ip)
            <tr>
                <td class="border border-gray-300 p-2">{{ $ip['ip'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">{{ date('Y-m-d H:i:s', $ip['created'] ?? 0) }}</td>
                <td class="border border-gray-300 p-2">{{ $ip['source'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">{{ $ip['expires'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">{{ $ip['reason'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">
                    <button wire:click="deleteBannedIp({{ $index }})"
                            class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Delete
                    </button>
                </td>
            </tr>
        @endforeach
        <tr>
            <td class="border border-gray-300 p-2">
                <input wire:model="newBannedIp" type="text" placeholder="New IP"
                       class="w-full p-1 border rounded"/>
            </td>
            <td class="border border-gray-300 p-2"></td>
            <td class="border border-gray-300 p-2"></td>
            <td class="border border-gray-300 p-2">
                <select wire:model.live.debounce="newExpiresIpOption" class="border rounded p-1">
                    <option value="">Select expiration</option>
                    <option value="forever">Forever</option>
                    <option value="custom">Custom date & time</option>
                </select>

                @if($newExpiresIpOption === 'custom')
                    <input wire:model="newExpiresIp" type="datetime-local" class="mt-1" />
                @endif
            </td>
            <td class="border border-gray-300 p-2">
                <input wire:model="newReasonIp" type="text" placeholder="Reason" />
            </td>
            <td class="border border-gray-300 p-2">
                <button wire:click="addBannedIp" class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">
                    Add
                </button>
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
            <th class="border border-gray-300 p-2">Expires</th>
            <th class="border border-gray-300 p-2">Reason</th>
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
                <td class="border border-gray-300 p-2">{{ $player['expires'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">{{ $player['reason'] ?? '' }}</td>
                <td class="border border-gray-300 p-2">
                    <button wire:click="deleteBannedPlayer({{ $index }})"
                            class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Delete
                    </button>
                </td>
            </tr>
        @endforeach
        <tr>
            <td class="border border-gray-300 p-2"></td>
            <td class="border border-gray-300 p-2">
                <input wire:model="newBannedPlayer" type="text" placeholder="New Player Name"
                       class="w-full p-1 border rounded"/>
            </td>
            <td class="border border-gray-300 p-2"></td>
            <td class="border border-gray-300 p-2"></td>
            <td class="border border-gray-300 p-2">
                <select wire:model.live.debounce="newExpiresPlayerOption" class="border rounded p-1">
                    <option value="">Select expiration</option>
                    <option value="forever">Forever</option>
                    <option value="custom">Custom date & time</option>
                </select>

                @if($newExpiresPlayerOption === 'custom')
                    <input wire:model="newExpiresPlayer" type="datetime-local" class="mt-1" />
                @endif
            </td>
            <td class="border border-gray-300 p-2">
                <input wire:model="newReasonPlayer" type="text" placeholder="Reason" />
            </td>
            <td class="border border-gray-300 p-2">
                <button wire:click="addBannedPlayer"
                        class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Add
                </button>
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
                    <button wire:click="deleteOp({{ $index }})"
                            class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Delete
                    </button>
                </td>
            </tr>
        @endforeach
        <tr>
            <td class="border border-gray-300 p-2"></td>
            <td class="border border-gray-300 p-2">
                <input wire:model="newOpName" type="text" placeholder="Name" class="w-full p-1 border rounded"/>
            </td>
            <td class="border border-gray-300 p-2">
                <select wire:model="newLevel" name="number">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </td>
            <td class="border border-gray-300 p-2">
                <select wire:model="newPlayerLimit" name="boolean">
                    <option value="false">No</option>
                    <option value="true">Yes</option>
                </select>
            </td>
            <td class="border border-gray-300 p-2">
                <button wire:click="addOp" class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Add
                </button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
