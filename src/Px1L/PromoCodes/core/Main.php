<?php

declare(strict_types=1);

namespace Px1L\PromoCodes\Core;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use Px1L\PromoCodes\Listener\InteractListener;

class Main extends PluginBase
{
    public $config;

    public function onLoad(): void
    {
        $this->getServer()->getLogger()->info(TextFormat::RED . "I NEED SLEEEEP!!!!!!!!!!!!!");
    }

    public function onEnable(): void
    {
        $this->getServer()->getLogger()->info(TextFormat::RED . "I NEED SLEEEEP!!!!!!!!!!!!!");

        $this->getServer()->getPluginManager()->registerEvents(new InteractListener($this), $this);

        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->saveDefaultConfig();
    }


    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool
    {
        $player = $this->getServer()->getPlayerByPrefix($sender->getName());
        $nocmd = TextFormat::RED . "You do not have permission to use this command";
        $config = $this->config;

        if ($cmd->getName() == "promo") {
            if ($sender instanceof Player) {
                if ($sender->hasPermission("promo.create")) {

                    if (!isset($args[0])) {
                        $player->sendMessage(TextFormat::GOLD . "/promo create <name> | /promo remove <name> | /promo get <name>");
                    } else {
                        if (count($args) > 0 && count($args) == 2) {
                            if ($args[0] == "create") {
                                $name = $args[1];

                                if ($name != $config->exists($name)) {
                                    $inventory = $player->getInventory();
                                    $item = $player->getInventory()->getItemInHand();
                                    $voucher = ItemFactory::getInstance()->get(ItemIds::PAPER);
                                    $enchantments = new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::UNBREAKING), 255);

                                    $voucher->addEnchantment($enchantments);
                                    $voucher->setCustomName($name);
                                    $voucher->setLore(array(
                                        TextFormat::AQUA . "Voucher created by " . $player->getName(),
                                        TextFormat::RESET . " ",
                                        TextFormat::RED . TextFormat::ITALIC . "Plugin by Px1L"
                                    ));

                                    $config->setNested($name . ".ID",    $item->getId());
                                    $config->setNested($name . ".COUNT", $item->getCount());
                                    $config->setNested($name . ".META", $item->getMeta());

                                    if ($item->hasEnchantments()) {
                                        $config->setNested($name . ".ENCHANT", true);
                                        foreach ($item->getEnchantments() as $enchantment) {
                                            $config->setNested($name . ".ENCHANT_ID", EnchantmentIdMap::getInstance()->toId($enchantment));
                                            $config->setNested($name . ".ENCHANT_LVL", $enchantment->getLevel());
                                        }
                                    } else {
                                        $config->setNested($name . ".ENCHANT", false);
                                    }

                                    $config->save();

                                    $inventory->removeItem($inventory->getItemInHand());
                                    $inventory->addItem($voucher);

                                    $player->sendMessage(TextFormat::GREEN . "Nice! You made a promo voucher with name: " . $name);
                                    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "save-all");
                                } else {
                                    $player->sendMessage(TextFormat::GOLD . "'" . $name . "'" . TextFormat::RED . " already exists. Try different name.");
                                }
                            } else if ($args[0] == "remove") {
                                $name = $args[1];
                                if ($name == $config->exists($name)) {
                                    $config->remove($name);
                                    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "save-all");
                                } else {
                                    $player->sendMessage(TextFormat::AQUA . $name . TextFormat::RED . " Doesn't exists!");
                                }
                            } else if ($args[0] == "get") {
                                $name = $args[1];

                                if ($name == $config->exists($name)) {

                                    $inventory = $player->getInventory();
                                    $item = $player->getInventory()->getItemInHand();
                                    $voucher = ItemFactory::getInstance()->get(ItemIds::PAPER);
                                    $enchantments = new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::UNBREAKING), 255);

                                    $voucher->addEnchantment($enchantments);
                                    $voucher->setCustomName($name);
                                    $voucher->setLore(array(
                                        TextFormat::AQUA . "Voucher created by " . $player->getName(),
                                        TextFormat::RESET . " ",
                                        TextFormat::RED . TextFormat::ITALIC . "Plugin by Px1L"
                                    ));

                                    if ($item !== VanillaItems::AIR())
                                        $inventory->removeItem($inventory->getItemInHand());

                                    $inventory->addItem($voucher);
                                    $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "save-all");
                                } else {
                                    $player->sendMessage(TextFormat::AQUA . $name . TextFormat::RED . " Doesn't exists!");
                                }
                            }
                        } else {
                            $player->sendMessage(TextFormat::GOLD . "/promo create <name> | /promo remove <name> | /promo get <name>");
                        }
                    }
                } else {
                    $player->sendMessage($nocmd);
                }
            } else {
                $sender->sendMessage("Nope you need to be in game kido");
            }
        }
        return true;
    }
}
