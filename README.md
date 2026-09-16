# Summon

Summon any registered entity with a single command, and remove them WorldEdit-style with a bone.

Summon automatically detects **every entity registered on the server** - vanilla entities plus anything registered by other plugins - so there is nothing to configure and no list to maintain.

## Features

- `/summon <entity> [x y z]` - spawn any registered entity at your position or at given coordinates (relative `~` coordinates supported)
- Automatic entity discovery: the full entity registry is read at runtime, so entities added by other plugins are summonable too
- Forgiving input: case-insensitive matching, short names (`zombie` -> `minecraft:zombie`) and "Did you mean" suggestions for unknown names
- `/unsummon` - toggle the bone erase mode (per player, off by default)
- Bone erase mode: hold a bone, hit an entity, it is removed without taking damage. Players are never affected
- In-game auto-completion of every known entity identifier

## Requirements

- PocketMine-MP API 5.0.0 (developed and tested on Axolotl-PM 5.48.0)
- [Commando](https://github.com/CortexPE/Commando) virion

> **Note:** the bone erase mode uses the new-generation item API (`Item::getTypeId()`), available on Axolotl-PM and other PM6-generation forks. On stock PocketMine-MP 5.x, `/summon` works but the bone erase mode does not.

## Commands

| Command | Description | Usage | Permission |
| ------- | ----------- | ----- | ---------- |
| `summon` | Summon an entity | `/summon <entity> [x y z]` | `summon.command` |
| `unsummon` | Toggle the bone erase mode | `/unsummon` | `unsummon.command` |

### Examples

```
/summon minecraft:zombie
/summon zombie                 # short names work too
/summon villager 100 64 200    # absolute coordinates
/summon cow ~3 ~ ~-2           # relative coordinates
```

## Bone erase mode

1. Run `/unsummon` - you should see "Bone erase mode enabled"
2. Hold a **bone** and hit any entity - it is instantly removed
3. Run `/unsummon` again to disable it

The mode is per player, disabled by default, and never applies to other players.

## Permissions

| Permission | Description | Default |
| ---------- | ----------- | ------- |
| `summon.command` | Allows you to summon entities | op |
| `unsummon.command` | Allows you to toggle the bone erase mode and remove entities with a bone | op |

## Installation

1. Install the **Commando** virion: drop `Commando.phar` into your server's `virions/` folder
2. Drop `Summon.phar` (or the plugin folder) into `plugins/`
3. Restart the server

## Building

Clone this repository and build a phar with [DevTools](https://github.com/pmmp/DevTools):

```
/makeplugin Summon
```

## License

MIT - see [LICENSE](LICENSE).
