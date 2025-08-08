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
