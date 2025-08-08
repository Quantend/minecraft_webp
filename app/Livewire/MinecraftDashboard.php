<?php

namespace App\Livewire;

use Livewire\Component;

class MinecraftDashboard extends Component
{
    public $logContent = '';

    public function mount()
    {
        $logPath = '/opt/minecraft/logs/latest.log';

        if (file_exists($logPath) && is_readable($logPath)) {
            $this->logContent = file_get_contents($logPath);
        } else {
            $this->logContent = "Log file not found or not readable.";
        }
    }

    public function render()
    {
        return view('livewire.minecraft-dashboard', [
            'logContent' => $this->logContent,
        ]);
    }
}
