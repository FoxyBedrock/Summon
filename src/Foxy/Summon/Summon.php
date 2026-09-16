<?php

namespace Foxy\Summon;

use Foxy\Summon\commands\CommandsLoader;
use Foxy\Summon\listeners\BoneRemoveListener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Summon extends PluginBase
{
    use SingletonTrait;

    public function onLoad(): void
    {
        self::setInstance($this);
    }

    public function onEnable(): void
    {
        $commandsLoader = new CommandsLoader($this);
        $commandsLoader->onLoad();

        $this->getServer()->getPluginManager()->registerEvents(new BoneRemoveListener(), $this);
    }
}
