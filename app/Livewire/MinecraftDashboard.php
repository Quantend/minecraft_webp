<?php

namespace App\Livewire;

use Livewire\Component;

class MinecraftDashboard extends Component
{
    public $logContent = '';
    public $logPath = '/opt/minecraft/logs/latest.log';
    public $pollLogs = false;

    // Paths to JSON files
    public $banned_ips_path = '/opt/minecraft/banned-ips.json';
    public $banned_players_path = '/opt/minecraft/banned-players.json';
    public $ops_path = '/opt/minecraft/ops.json';

    // Data loaded from JSON files
    public $banned_ips = [];
    public $banned_players = [];
    public $ops = [];

    // Input fields for adding new entries
    public $newBannedIp = '';
    public $newBannedPlayer = '';
    public $newOpName = '';
    public $newOpUuid = '';

    public function mount()
    {
        $this->getLogs();
        $this->loadJsonFiles();
    }

    public function getLogs()
    {
        if (file_exists($this->logPath) && is_readable($this->logPath)) {
            $this->logContent = file_get_contents($this->logPath);
        } else {
            $this->logContent = "Log file not found or not readable.";
        }
    }

    public function clearLogs()
    {
        if (file_exists($this->logPath) && is_writable($this->logPath)) {
            file_put_contents($this->logPath, '');
            $this->logContent = '';
        } else {
            $this->logContent = "Cannot clear logs: file not found or not writable.";
        }
    }

    public function togglePoll()
    {
        $this->pollLogs = !$this->pollLogs;
    }

    // Load JSON files data into component properties
    public function loadJsonFiles()
    {
        $this->banned_ips = $this->loadJson($this->banned_ips_path);
        $this->banned_players = $this->loadJson($this->banned_players_path);
        $this->ops = $this->loadJson($this->ops_path);
    }

    private function loadJson($path)
    {
        if (file_exists($path) && is_readable($path)) {
            $content = file_get_contents($path);
            return json_decode($content, true) ?? [];
        }
        return [];
    }

    private function saveJson($path, $data)
    {
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
    }

    // Add new banned IP
    public function addBannedIp()
    {
        if ($this->newBannedIp) {
            // You might want to validate the IP here
            $this->banned_ips[] = ['ip' => $this->newBannedIp, 'created' => time(), 'source' => 'Web'];
            $this->saveJson($this->banned_ips_path, $this->banned_ips);
            $this->newBannedIp = '';
        }
    }

    public function deleteBannedIp($index)
    {
        unset($this->banned_ips[$index]);
        $this->banned_ips = array_values($this->banned_ips);
        $this->saveJson($this->banned_ips_path, $this->banned_ips);
    }

    // Add new banned player
    public function addBannedPlayer()
    {
        if ($this->newBannedPlayer) {
            $this->banned_players[] = ['uuid' => '', 'name' => $this->newBannedPlayer, 'created' => time(), 'source' => 'Web'];
            $this->saveJson($this->banned_players_path, $this->banned_players);
            $this->newBannedPlayer = '';
        }
    }

    public function deleteBannedPlayer($index)
    {
        unset($this->banned_players[$index]);
        $this->banned_players = array_values($this->banned_players);
        $this->saveJson($this->banned_players_path, $this->banned_players);
    }

    // Add new operator
    public function addOp()
    {
        if ($this->newOpName && $this->newOpUuid) {
            $this->ops[] = [
                'uuid' => $this->newOpUuid,
                'name' => $this->newOpName,
                'level' => 4,
                'bypassesPlayerLimit' => false
            ];
            $this->saveJson($this->ops_path, $this->ops);
            $this->newOpName = '';
            $this->newOpUuid = '';
        }
    }

    public function deleteOp($index)
    {
        unset($this->ops[$index]);
        $this->ops = array_values($this->ops);
        $this->saveJson($this->ops_path, $this->ops);
    }

    public function render()
    {
        return view('livewire.minecraft-dashboard', [
            'logContent' => $this->logContent,
            'bannedIps' => $this->banned_ips,
            'bannedPlayers' => $this->banned_players,
            'ops' => $this->ops,
        ]);
    }
}
