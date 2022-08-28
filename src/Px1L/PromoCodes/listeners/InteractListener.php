<?php

declare(strict_types=1);

namespace Px1L\PromoCodes\listeners;

use Pixel\AntiVoid\Core\Main;
use pocketmine\level\Level;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;

class InteractListener implements Listener
{
    private $plugin;
    private	$nocmd = TextFormat::RED."You do not have permission to use this command";


    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onPlayerInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $inventory = $player-getInventory();
        
        if($sender->hasPermission("promo.use")){
            foreach ($plugin->config->getAll() as $name)
            {
                if ($inventory->getItemInHand()->getName() == $name)
                {
                    $item = Item::get((int)$plugin->config->getNested($name)["ID"], 0, (int)$plugin->config->getNested($name)["COUNT"]);
                    $enchantment = Enchantment::getEnchantment((int)$plugin->config->getNested($name)["ENCHANT_ID"]);
                    $enchantment->setLevel((int) $plugin->config->getNested($name)["ENCHANT_LVL"]);

                    $item->addEnchantment($enchantment);
                    
                    $inventory->setItemInHand(Item::get(Item::AIR));
                    $inventory->setItemInHand($item);

                    if ($inventory->getItemInHand()->getId() == (int)$plugin->config->getNested($name)["ID"])
                    {
                        $player->sendMessage(TextFormat::GREEN . "Congrats you lucky fucker get ready for 4 AM experience because you got ". TextFormat::GOLD .(int)$plugin->config->getNested($name)["COUNT"] .
                                                    " ". TextFormat::BLUE . $item->getName());

                        $plugin->config->remove($name);
                    }
                }
            }
        } else {
            $player->sendMessage($nocmd);
        }
    }
}

?>