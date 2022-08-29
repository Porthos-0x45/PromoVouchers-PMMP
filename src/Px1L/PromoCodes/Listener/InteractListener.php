<?php

declare(strict_types=1);

namespace Px1L\PromoCodes\Listener;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use Px1L\PromoCodes\Core\Main;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;
use pocketmine\item\ItemFactory;

class InteractListener implements Listener
{
    const NOPERMS = TextFormat::RED . "You do not have permission to use this command";

    public function __construct(private Main $plugin)
    {
    }

    public function onPlayerInteract(PlayerInteractEvent $event)
    {
        $plugin = $this->plugin;
        $player = $event->getPlayer();
        $inventory = $player->getInventory();
        $lore = array(
            TextFormat::AQUA . "Voucher created by " . $player->getName(),
            TextFormat::RESET . " ",
            TextFormat::RED . TextFormat::ITALIC . "Plugin by Px1L"
        );

        if ($player->hasPermission("promo.use")) {
            if ($plugin->config->get($inventory->getItemInHand()->getName()) != null) {
                if ($inventory->getItemInHand()->getLore() == $lore) {
                    $name = $inventory->getItemInHand()->getName();

                    $item = ItemFactory::getInstance()->get(
                        (int)$plugin->config->getNested($name)["ID"],
                        (int)$plugin->config->getNested($name)["META"],
                        (int)$plugin->config->getNested($name)["COUNT"]
                    );

                    if ($plugin->config->getNested($name)["ENCHANT"] == true) {
                        $enchantment = EnchantmentIdMap::getInstance()->fromId((int)$plugin->config->getNested($name)["ENCHANT_ID"]);
                        $enchantment->setLevel((int) $plugin->config->getNested($name)["ENCHANT_LVL"]);

                        $item->addEnchantment($enchantment);
                    }

                    //$inventory->setItemInHand(ItemFactory::getInstance()->get(ItemIds::AIR));
                    //$inventory->setItemInHand($item);

                    $inventory->removeItem($inventory->getItemInHand());
                    $inventory->addItem($item);

                    if ($inventory->getItemInHand()->getId() == (int)$plugin->config->getNested($name)["ID"]) {
                        $player->sendMessage(TextFormat::GREEN . "Congrats you lucky f u c k e r get ready for 4 AM experience because you got " . TextFormat::GOLD . (int)$plugin->config->getNested($name)["COUNT"] .
                            " " . TextFormat::BLUE . $item->getName());

                        $plugin->config->remove($name);
                        $plugin->config->save();

                        $plugin->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender($plugin->getServer(), $plugin->getServer()->getLanguage()), "save-all");
                        $event->cancel(true);
                    }
                }
            }
        } else {
            $player->sendMessage($this::NOPERMS);
        }
    }
}
