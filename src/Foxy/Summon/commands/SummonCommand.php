<?php

namespace Foxy\Summon\commands;

use CortexPE\Commando\args\BlockPositionArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use Foxy\Summon\commands\args\EntityArgument;
use pocketmine\command\CommandSender;
use pocketmine\entity\EntityFactory;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\world\Position;
use function count;
use function implode;
use function round;
use function str_contains;
use function strtolower;

class SummonCommand extends BaseCommand
{
    public function __construct(Plugin $plugin)
    {
        parent::__construct(
            $plugin,
            "summon",
            "Summon any registered entity at your position or at the given coordinates"
        );
    }

    protected function prepare(): void
    {
        $this->setPermission("summon.command");
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->registerArgument(0, new EntityArgument("entity"));
        $this->registerArgument(1, new BlockPositionArgument("pos", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        /** @var Player $sender (garanti par InGameRequiredConstraint) */
        /** @var string $saveId */
        $saveId = $args["entity"];
        $world = $sender->getWorld();

        /** @var Vector3|null $blockPos */
        $blockPos = $args["pos"] ?? null;
        if ($blockPos !== null) {
            $pos = new Position($blockPos->getX() + 0.5, $blockPos->getY(), $blockPos->getZ() + 0.5, $world);
        } else {
            $pos = $sender->getPosition();
        }

        $nbt = new CompoundTag();
        $nbt->setTag("Pos", new ListTag([
            new DoubleTag($pos->getX()),
            new DoubleTag($pos->getY()),
            new DoubleTag($pos->getZ()),
        ]));
        $nbt->setTag("Motion", new ListTag([
            new DoubleTag(0.0),
            new DoubleTag(0.0),
            new DoubleTag(0.0),
        ]));
        $nbt->setTag("Rotation", new ListTag([
            new FloatTag($sender->getLocation()->getYaw()),
            new FloatTag($sender->getLocation()->getPitch()),
        ]));
        $nbt->setString(EntityFactory::TAG_IDENTIFIER, $saveId);

        $entity = EntityFactory::getInstance()->createFromData($world, $nbt);
        if ($entity === null) {
            $sender->sendMessage("§cUnknown entity: §f" . $saveId);
            $this->sendSuggestions($sender, $saveId);
            return;
        }
        $entity->spawnToAll();

        $sender->sendMessage(
            "§aSummoned §f" . $saveId . " §aat " .
            round($pos->getX(), 1) . " " . round($pos->getY(), 1) . " " . round($pos->getZ(), 1) . "."
        );
    }

    private function sendSuggestions(Player $sender, string $saveId): void
    {
        $needle = strtolower($saveId);
        $matches = [];
        foreach (EntityArgument::getKnownIdentifiers() as $id) {
            if (str_contains(strtolower($id), $needle)) {
                $matches[] = $id;
            }
        }
        if (count($matches) > 0) {
            $sender->sendMessage("§7Did you mean: §f" . implode("§7, §f", $matches));
        }
    }
}
