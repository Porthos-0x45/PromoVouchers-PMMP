<?php

namespace Px1L\PromoCodes\Core;

use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use Px1L\PromoCodes\listners\InteractListener;

class Main extends PluginBase {

    public $config;

    public function onLoad() : void
    {
		$this->getServer()->getLogger()->info(TextFormat::RED."I NEED SLEEEEP!!!!!!!!!!!!!");
    }

    public function onEnable() : void{
		$this->getServer()->getLogger()->info(TextFormat::RED."I NEED SLEEEEP!!!!!!!!!!!!!");

		$this->getServer()->getPluginManager()->registerEvents(new InteractListener($this), $this)
		$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->saveDefaultConfig();
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        $player = $this->getServer()->getPlayer($sender->getName());
	    $server = $this->getServer();
	    $nocmd = TextFormat::RED."You do not have permission to use this command";

        if ($cmd->getName() === "promo")
        {
            if ($sender instanceof Player)
            {
                if($sender->hasPermission("promo.create")){

                    if (!isset($args[0]))
                    {
                        $player->sendMessage(TextFormat::GOLD . "/promo <name>");
                    }
    
                    else {
                        if (count($args) > 0 && count($args) == 1)
                        {
                            $name = $args[0];
                            $inventory = $player->getInventory();
                            $item = $player->getInventory()->getItemInHand();
                            $enchantment = Enchantment::getEnchantment(17);
                            $voucher = Item::get(339, 0, 1);
                            
                            $enchantment->setLevel(255);
    
                            $voucher->addEnchantment($enchantment);
                            $voucher->setCustomName($name);
    
                            $config->setNested($name . ".ID",    $item->getId());
                            $config->setNested($name . ".COUNT", $item->getCount());
                            if ($item->hasEnchantments())
                            {
                                foreach ($item->getEnchantments() as $enchantment) {
                                    $config->setNested($name . ".ENCHANT_ID", $enchantment->getId());
                                    $config->setNested($name . ".ENCHANT_LVL", $enchantment->getLevel());
                                }
                            }
    
                            $config->save();
    
                            $inventory->setItemInHand(Item::get(Item::AIR));
                            $inventory->addItem($voucher);

                            $player->sendMessage(TextFormat::GREEN . "Nice! You made a promo voucher with name: " . $name);
                        }
                        else {
                            $player->sendMessage(TextFormat::GOLD . "/promo <name>");

                        }
                    }
                }
                else {
                    $player->sendMessage($nocmd);
                }

            } else {
                $sender->sendMessage("Nope you need to be in game kido");
            }
        }
    }
}

?>