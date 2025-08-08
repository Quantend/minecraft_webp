<?php

namespace App\Livewire;

use Livewire\Component;

class MinecraftDashboard extends Component
{
    public $logContent = '';
    public $logPath = '/opt/minecraft/logs/latest.log';
    public $pollLogs = false;

    public function mount()
    {
       $this->getLogs();
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
        // inside clearLogs() before writing:
        $phpUser = trim(shell_exec('whoami'));
        $writable = is_writable($this->logPath) ? 'yes' : 'no';
        logger("PHP user: {$phpUser}, is_writable: {$writable}", ['path' => $this->logPath]);

        if (file_exists($this->logPath) && is_writable($this->logPath)) {
            // Truncate the log file
            file_put_contents($this->logPath, '');
            // Update the log content in the UI
            $this->logContent = '';
        } else {
            $this->logContent = "Cannot clear logs: file not found or not writable.";
        }
    }

    public function togglePoll()
    {
        $this->pollLogs = !$this->pollLogs;
    }

    public function render()
    {
        return view('livewire.minecraft-dashboard', [
            'logContent' => $this->logContent,
        ]);
    }
}
