<?php

declare(strict_types=1);

namespace Reports;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener
{

    private $config;
    const PREFIX = "§7[§4REPORT§7]§f ";
    private $r;

    public function oJoin(PlayerJoinEvent $e)
    {
        $oyuncu = strtolower($e->getPlayer()->getName());
        $this->config = new Config($this->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
        if (!$this->config->get($player)) {
            $this->config->set($player, 0);
            $this->config->save();
            return true;
        }
        if ($this->config->get($player . "-time")) {
            if ($this->config->get($player . "-time") < time()) {
                $this->config->remove($player . "-time");
                $this->config->save();
                return true;
            }  
        }
    }

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "data");
        @mkdir($this->getDataFolder() . "reports");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() == "report") {
            if (isset($args[0]) && isset($args[1])) {
                $oyuncu = strtolower($sender->getName());
                $this->config = new Config($this->getDataFolder() . "data/" . strtolower($player) . ".yml", Config::YAML);
                if ($this->config->get($oyuncu) < 5) {
                    $this->config->set($oyuncu, $this->config->get($oyuncu) + 1);
                    $this->config->set($oyuncu . "-time", strtotime('+1 day'));
                    $this->config->save();
                    $this->r = new Config($this->getDataFolder() . "reports/" . strtolower($player) . ".yml", Config::YAML);
                    $ilk = explode(":", $args[1]);
                    $son = implode(" ", $ilk);
                    $this->r->set(strtolower($args[0]), $son);
                    $this->r->save();
                    $sender->sendMessage(self::PREFIX . TextFormat::BLUE . "Report Thank you for reporting");
                    $sender->sendMessage(self::PREFIX . TextFormat::LIGHT_PURPLE . "To be reviewed by the authorities!");
                    return true;
                } else {
                    $sender->sendMessage(self::PREFIX."§4 You can report only 5 people daily.");
                    return false;
                }
            } else {
                $this->yardim($sender);
                return false;
            }
        }
        return true;
    }

    private function yardim($s)
    {
        $s->sendMessage(self::PREFIX);
        $s->sendMessage(TextFormat::GREEN , "/report <oyuncu> <sebep> : We will let the player know!");
        $s->sendMessage(TextFormat::DARK_RED . "ATTENTION: Use '" .
                        TextFormat::GREEN . ": " .
                        TextFormat::DARK_RED . "' instead of space when writing the reason! \ " .
                        TextFormat::BLUE . " / report");
        $s->sendMessage(self::PREFIX);
        return true;
    }

}
