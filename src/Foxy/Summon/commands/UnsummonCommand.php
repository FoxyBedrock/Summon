<?php

namespace Foxy\Summon\commands;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use Foxy\Summon\listeners\BoneRemoveListener;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

class UnsummonCommand extends BaseCommand
{
    public function __construct(Plugin $plugin)
    {
        parent::__construct(
            $plugin,
            "unsummon",
            "Enable or disable the bone erase mode (hit entities with a bone to remove them)"
        );
    }

    protected function prepare(): void
    {
        $this->setPermission("unsummon.command");
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        /** @var Player $sender (garanti par InGameRequiredConstraint) */
        if (BoneRemoveListener::toggle($sender)) {
            $sender->sendMessage("§aBone erase mode enabled. Hit an entity with a bone to remove it.");
        } else {
            $sender->sendMessage("§7Bone erase mode disabled.");
        }
    }
}
