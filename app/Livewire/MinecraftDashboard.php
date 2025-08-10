<?php

namespace App\Livewire;

use Livewire\Component;

class MinecraftDashboard extends Component
{
    public $logContent = '';
    public $logPath = '/opt/minecraft/logs/latest.log';
    public $pollLogs = false;
    public $restartToggle = false;
    public $backupToggle = false;
    public $updateToggle = false;
    public $newWorldToggle = false;
    public $seed = null;

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
    public $newExpiresIpOption;
    public $newExpiresIp = '';
    public $newReasonIp = '';
    public $newBannedPlayer = '';
    public $newExpiresPlayerOption;
    public $newExpiresPlayer = '';
    public $newReasonPlayer = '';
    public $newOpName = '';
    public $newLevel = 4;
    public $newPlayerLimit = false;

    public function mount()
    {
        $this->getLogs();
        $this->loadJsonFiles();
    }

    public function resetToggle()
    {
        $this->restartToggle = false;
        $this->backupToggle = false;
        $this->updateToggle = false;
        $this->newWorldToggle = false;
    }

    public function restartServerConfirm()
    {
        $this->restartToggle = true;
    }

    public function restartServer()
    {
        $this->restartToggle = false;
        exec('sudo /bin/systemctl restart minecraft.service');
        session()->flash('message', 'Restarted server');
    }

    public function restoreBackupConfirm()
    {
        $this->backupToggle = true;
    }

    public function restoreBackup()
    {
        $this->backupToggle = false;
        exec('sudo /usr/local/bin/restore_minecraft_backup.sh');
        session()->flash('message', 'Restored last backup');
    }

    public function updateMinecraftServerConfirm()
    {
        $this->updateToggle = true;
    }

    public function updateMinecraftServer()
    {
        $this->updateToggle = false;
        exec('sudo /usr/local/bin/update_minecraft.sh');
        session()->flash('message', 'Updated Minecraft server (if there is an update)');
    }

    public function newWorldConfirm()
    {
        $this->newWorldToggle = true;
    }

    public function newWorld()
    {
        $this->updateToggle = false;
        if ($this->seed) {
            exec("sudo /usr/local/bin/new_world.sh " . escapeshellarg($this->seed));
            session()->flash('message', "Created new world with seed {$this->seed}");
        } else {
            exec('sudo /usr/local/bin/new_world.sh');
            session()->flash('message', 'Created new world with random seed');
        }
        $this->seed = null;
    }

    private function getUuidFromUsername($username)
    {
        $url = "https://playerdb.co/api/player/minecraft/" . urlencode($username);
        $json = @file_get_contents($url);
        if ($json === false) {
            return null; // error fetching
        }

        $data = json_decode($json, true);

        if (isset($data['data']['player']['id'])) {
            return $data['data']['player']['id'];
        }

        return null; // uuid not found
    }

    public function getLogs()
    {
        $maxBytes = 50 * 1024; // 50 KB max

        if (file_exists($this->logPath) && is_readable($this->logPath)) {
            $fileSize = filesize($this->logPath);
            $handle = fopen($this->logPath, 'r');

            if ($fileSize > $maxBytes) {
                // Seek to the last $maxBytes of the file
                fseek($handle, -$maxBytes, SEEK_END);
            } else {
                fseek($handle, 0);
            }

            $this->logContent = fread($handle, $maxBytes);
            fclose($handle);

            // Optional: prepend info about truncation if file was too big
            if ($fileSize > $maxBytes) {
                $this->logContent = "[... showing last {$maxBytes} bytes of log ...]\n" . $this->logContent;
            }
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
        if ($this->newExpiresIpOption === 'forever') {
            $expires = 'forever';
        } elseif ($this->newExpiresIpOption === 'custom' && !empty($this->newExpiresIp)) {
            // Convert to full timestamp
            $expires = date('Y-m-d H:i:s O', strtotime($this->newExpiresIp));
        }

        if ($this->newBannedIp) {
            // You might want to validate the IP here
            $this->banned_ips[] = [
                'ip' => $this->newBannedIp,
                'created' => date('Y-m-d H:i:s O'),
                'source' => 'Web',
                'expires' => $expires ?? 'forever',
                'reason' => $this->newReasonIp,
            ];
            $this->saveJson($this->banned_ips_path, $this->banned_ips);
            $this->newBannedIp = '';
            $this->newExpiresIp = '';
            $this->newReasonIp = '';
            $this->newExpiresIpOption = '';
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
        if ($this->newExpiresPlayerOption === 'forever') {
            $expires = 'forever';
        } elseif ($this->newExpiresPlayerOption === 'custom' && !empty($this->newExpiresPlayer)) {
            // Convert to full timestamp
            $expires = date('Y-m-d H:i:s O', strtotime($this->newExpiresPlayer));
        }

        if ($this->newBannedPlayer) {
            $uuid = $this->getUuidFromUsername($this->newBannedPlayer);
            $this->banned_players[] = [
                'uuid' => $uuid ?? '',
                'name' => $this->newBannedPlayer,
                'created' => date('Y-m-d H:i:s O'),
                'source' => 'Web',
                'expires' => $expires ?? 'forever',
                'reason' => $this->newReasonPlayer,
            ];
            $this->saveJson($this->banned_players_path, $this->banned_players);
            $this->newBannedPlayer = '';
            $this->newExpiresPlayer = '';
            $this->newReasonPlayer = '';
            $this->newExpiresPlayerOption = '';
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
        if ($this->newOpName) {
            $uuid = $this->getUuidFromUsername($this->newOpName);
            if ($uuid) {
                $this->ops[] = [
                    'uuid' => $uuid,
                    'name' => $this->newOpName,
                    'level' => $this->newLevel,
                    'bypassesPlayerLimit' => $this->newPlayerLimit
                ];
                $this->saveJson($this->ops_path, $this->ops);
                $this->newOpName = '';
            } else {
                // Handle error: UUID not found
                session()->flash('error', 'Could not find UUID for this player name.');
            }
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
