<?php

namespace magma\trim;

use magma\trim\lib\LibTrimArmor\LibTrimArmor;
use pocketmine\form\Form as PMForm;
use pocketmine\item\Armor;
use pocketmine\player\Player;

class Form implements PMForm
{
    private Player $player;
    private Main $plugin;

    public function __construct(Player $player, Main $plugin)
    {
        $this->player = $player;
        $this->plugin = $plugin;
    }

    public function jsonSerialize(): array
    {
        return [
            "type" => "custom_form",
            "title" => $this->plugin->replaceHandler($this->player, $this->plugin->getConfig()->get("title-form")),
            "content" => [
                [
                    "type" => "dropdown",
                    "text" => $this->plugin->replaceHandler($this->player, $this->plugin->getConfig()->get("label-material")),
                    "options" => [
                        "Amethyst", "Copper", "Diamond", "Emerald", "Gold", "Iron", "Quartz", "Redstone", "Netherite", "Lapis Lazuli"
                    ]
                ],
                [
                    "type" => "dropdown",
                    "text" => $this->plugin->replaceHandler($this->player, $this->plugin->getConfig()->get("label-pattern")),
                    "options" => [
                        "Bolt", "Coast", "Dune", "Eye", "Flow", "Host", "Raiser", "Rib", "Sentry", "Shaper", "Silence", "Snout", "Spike", "Tide", "Vex", "Ward", "Wayfinder", "Wild"
                    ]
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if ($data === null) {
            return;
        }

        $material = $data[0];
        $pattern = $data[1];
        $armor = $player->getInventory()->getItemInHand();

        if ($armor instanceof Armor) {
            if ($player->getXpManager()->getXpLevel() >= $this->plugin->getConfig()->get("price")) {
                LibTrimArmor::create($armor, $this->plugin->getMaterial($material), $this->plugin->getPattern($pattern));
                $player->getInventory()->setItemInHand($armor);
                $this->plugin->messageHandler($player, $this->plugin->getConfig()->get("armor-trim-success"));
                $player->getXpManager()->subtractXpLevels($this->plugin->getConfig()->get("price"));
            } else {
                $this->plugin->messageHandler($player, $this->plugin->getConfig()->get("item-is-not-armor"));
            }
        } else {
            $this->plugin->messageHandler($player, $this->plugin->getConfig()->get("item-is-not-armor"));
        }
    }
}
