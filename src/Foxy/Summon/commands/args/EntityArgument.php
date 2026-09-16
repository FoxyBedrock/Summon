<?php

namespace Foxy\Summon\commands\args;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use pocketmine\entity\EntityFactory;
use ReflectionClass;
use function array_keys;
use function is_string;
use function str_contains;
use function strtolower;

class EntityArgument extends StringEnumArgument
{
    public static function getKnownIdentifiers(): array
    {
        $ids = [];
        foreach (self::getRegisteredSaveIds() as $id) {
            if (str_contains($id, ":")) {
                $ids[] = $id;
            }
        }
        return $ids;
    }

    public static function getRegisteredSaveIds(): array
    {
        $factory = EntityFactory::getInstance();
        $reflection = new ReflectionClass(EntityFactory::class);
        foreach (["creationFuncs", "creationCallbacks"] as $propertyName) {
            if (!$reflection->hasProperty($propertyName)) {
                continue;
            }
            $saveIds = [];
            foreach (array_keys($reflection->getProperty($propertyName)->getValue($factory)) as $id) {
                if (is_string($id)) {
                    $saveIds[] = $id;
                }
            }
            return $saveIds;
        }
        return [];
    }

    public function getTypeName(): string
    {
        return "entity";
    }

    public function getEnumValues(): array
    {
        return self::getKnownIdentifiers();
    }

    public function getValue(string $string): string
    {
        $needle = strtolower($string);
        foreach (self::getRegisteredSaveIds() as $id) {
            if (strtolower($id) === $needle) {
                return $id;
            }
        }
        // Nom court sans namespace : "zombie" => "minecraft:zombie"
        foreach (self::getRegisteredSaveIds() as $id) {
            if (strtolower($id) === "minecraft:" . $needle) {
                return $id;
            }
        }
        return $string;
    }

    public function parse(string $argument, CommandSender $sender): mixed
    {
        return $this->getValue($argument);
    }
}
