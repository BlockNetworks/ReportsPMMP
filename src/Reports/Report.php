<?php

declare(strict_types=1);

namespace Reports;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener as L;

class Main extends PluginBase implements L{
    
    /** @var Config */
    private $hak;
    /** @var PREFIX */
    public const PREFIX = "§7[§4REPORT§7]§f ";
    /** @var Reports */
    private $r;
    
    public function oJ(PlayerJoinEvent $e) {
        $oyuncu = strtolower($e->getPlayer()->getName());
        $this->hak = new Config($this->getDataFolder()."data/".strtolower($player).".yml", Config::YAML);
        if(!$this->hak->get($player)){
         $this->hak->set($player, 0);
         $this->hak->save();
         return true;
     }
        if ($this->hak->get($player."-time")) {
            if($this->hak->get($player."-time") < time()){
             $this->hak->remove($player."-time");
             $this->hak->save();
             return true;
          }  
        }
    }
    
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder()."data");
        @mkdir($this->getDataFolder()."reports");
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if($command->getName() == "report"){
            if(isset($args[0]) and isset($args[1])){
                $oyuncu = strtolower($sender->getName());
                $this->hak = new Config($this->getDataFolder()."data/".strtolower($player).".yml", Config::YAML);
                if($this->hak->get($oyuncu) < 5){
                $this->hak->set($oyuncu, $this->hak->get($oyuncu)+1);
                $this->hak->set($oyuncu."-time", strtotime('+1 day'));
                $this->hak->save();
                $this->r = new Config($this->getDataFolder()."reports/".strtolower($player).".yml", Config::YAML);
                $ilk = explode(":", $args[1]);
                $son = implode(" ", $ilk);
                $this->r->set(strtolower($args[0]), $son);
                $this->r->save();
                $sender->sendMessage(self::PREFIX."§bReport Thank you for reporting");
                $sender->sendMessage(self::PREFIX."§dTo be reviewed by the authorities!");
                return true;
                }else{
                $sender->sendMessage(self::PREFIX."§4 You can report only 5 people daily.");
                return false;
                }
            }else{
                $this->yardim($sender);
                return false;
            }
        }
        return true;
    }
    private function yardim($s){
        $s->sendMessage(self::PREFIX);
        $s->sendMessage("§a/report <oyuncu> <sebep> : We will let the player know!");
        $s->sendMessage("§4ATTENTION: Use '§a: §4' instead of space when writing the reason! \ §b / report");
        $s->sendMessage(self::PREFIX);
        return true;
    }
}
