<?php

declare(strict_types=1);

namespace Px1L\PromoCodes\Listener;

use pocketmine\data\bedrock\EnchantmentIdMap;
use Px1L\PromoCodes\Core\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class InteractListener implements Listener
{
    public $nocmd = TextFormat::RED . "You do not have permission to use this command";

    public function __construct(private Main $plugin)
    {
    }

    public function onPlayerInteract(PlayerInteractEvent $event)
    {
        $plugin = $this->plugin;
        $player = $event->getPlayer();
        $inventory = $player->getInventory();


        if ($player->hasPermission("promo.use")) {
            foreach ($plugin->config->getAll() as $name) {
                $player->sendMessage("NAME: " . $name . " ITEM: " . $inventory->getItemInHand()->getName());
                if ($inventory->getItemInHand()->getName() == $name) {
                    $item = ItemFactory::getInstance()->get((int)$plugin->config->getNested($name)["ID"], 0, (int)$plugin->config->getNested($name)["COUNT"]);

                    if ($plugin->config->getNested($name)["ENCHANT_ID"] == null) {
                        $enchantment = EnchantmentIdMap::getInstance()->fromId((int)$plugin->config->getNested($name)["ENCHANT_ID"]);
                        $enchantment->setLevel((int) $plugin->config->getNested($name)["ENCHANT_LVL"]);

                        $item->addEnchantment($enchantment);
                    }

                    $inventory->setItemInHand(ItemFactory::getInstance()->get(ItemIds::AIR));
                    $inventory->setItemInHand($item);

                    if ($inventory->getItemInHand()->getId() == (int)$plugin->config->getNested($name)["ID"]) {
                        $player->sendMessage(TextFormat::GREEN . "Congrats you lucky fucker get ready for 4 AM experience because you got " . TextFormat::GOLD . (int)$plugin->config->getNested($name)["COUNT"] .
                            " " . TextFormat::BLUE . $item->getName());

                        $plugin->config->remove($name);
                    }
                }
            }
        } else {
            $player->sendMessage($this->nocmd);
        }
    }
}
