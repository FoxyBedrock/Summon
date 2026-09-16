<?php

namespace Foxy\Summon\commands;

use Foxy\Summon\Summon;

class CommandsLoader
{
    public function __construct(private readonly Summon $plugin)
    {
    }

    public function onLoad(): void
    {
        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        $this->plugin->getServer()->getCommandMap()->register(
            "Summon",
            new SummonCommand($this->plugin)
        );
        $this->plugin->getServer()->getCommandMap()->register(
            "Summon",
            new UnsummonCommand($this->plugin)
        );
    }
}
