<?php

namespace Foxy\Summon\listeners;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use function end;
use function explode;

class BoneRemoveListener implements Listener
{
    /** @var array<string, true> Joueurs avec le mode os actif */
    private static array $enabled = [];

    public static function isEnabled(Player $player): bool
    {
        return isset(self::$enabled[$player->getName()]);
    }

    public static function toggle(Player $player): bool
    {
        $name = $player->getName();
        if (isset(self::$enabled[$name])) {
            unset(self::$enabled[$name]);
            return false;
        }
        self::$enabled[$name] = true;
        return true;
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        if (!$damager instanceof Player || !$damager->hasPermission("unsummon.command")) {
            return;
        }
        if (!self::isEnabled($damager)) {
            return;
        }
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            return;
        }
        if (!$this->isBone($damager->getInventory()->getItemInHand())) {
            return;
        }

        $event->cancel();
        $description = $this->describe($entity);
        $entity->close();
        $damager->sendMessage("§aRemoved entity: §f" . $description . "§a.");
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        unset(self::$enabled[$event->getPlayer()->getName()]);
    }

    private function isBone(Item $item): bool
    {
        static $boneTypeId = null;
        $boneTypeId ??= VanillaItems::BONE()->getTypeId();
        return $item->getTypeId() === $boneTypeId;
    }

    private function describe(Entity $entity): string
    {
        if ($entity->getNameTag() !== "") {
            return $entity->getNameTag();
        }
        $parts = explode("\\", $entity::class);
        return end($parts);
    }
}
