<?php
/**
 * Created by PhpStorm.
 * User: JeremyMorales
 * Date: 6/22/17
 * Time: 12:22 PM
 */
namespace koth;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
class KothCommand extends Command
{
    private $plugin;
    public function __construct($name, KothMain $main)
    {
        parent::__construct($name, "");
        $this->plugin = $main;
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if ($sender instanceof Player){
            if (isset($args[0])){
                if (strtolower($args[0]) === "join"){
                     $this->plugin->getServer()->broadcastMessage("§5$sender §dhas joined KOTH. Game will be starting shortly..");
                    if ($this->plugin->sendToKoth($sender)){
                        $sender->sendMessage($this->plugin->getData("joined"));
                        return true;
                    }else{
                        $sender->sendMessage($this->plugin->getData("not_running"));
                    }
                    return true;
                }elseif (strtolower($args[0]) === "leave") return true;
                    if($this->plugin->removePlayer($sender)){
                       $sender->sendMessage($this->plugin->getData("left"));
                       return true;
                } else if (strtolower($args[0]) === "setspawn"){
                    if (!$sender->hasPermission("koth.start")) return true;
                    $this->plugin->setPoint($sender,"spawn");
                    $sender->sendMessage("Successfully Added spawnpoint!");
                    return true;
                } else if (strtolower($args[0]) === "p1"){
                    if (!$sender->hasPermission("koth.start")) return true;
                    $this->plugin->setPoint($sender,"p1");
                    $sender->sendMessage("Successfully Added p1 point (make sure to set p2)");
                } else if (strtolower($args[0]) === "p2"){
                    if (!$sender->hasPermission("koth.start")) return true;
                    $this->plugin->setPoint($sender,"p2");
                    $sender->sendMessage("Successfully Added p2 point!");
                } else if (strtolower($args[0]) === "start"){
                    if (!$sender->hasPermission("koth.start")) return true;
                    if ($this->plugin->startArena()){
                        $sender->sendMessage("KOTH Event Started!");
                    }else{
                        $sender->sendMessage("No KOTH Arena fully setup...");
                    }
                } else if (strtolower($args[0]) === "stop"){
                    if (!$sender->hasPermission("koth.stop")) return true;
                    if ($this->plugin->forceStop()){
                        $sender->sendMessage("KOTH Event Force stopped");
                    }else{
                        $sender->sendMessage("No KOTH Arena fully setup...");
                    }
                } else{
                    if ($sender->isOp()) $this->sendHelp($sender);
                    if (!$sender->isOp()) $sender->sendMessage($this->plugin->prefix()."Join game with /koth join");
                }
            }else{
                if ($sender->isOp()) $this->sendHelp($sender);
                if (!$sender->isOp()) $sender->sendMessage($this->plugin->prefix()."Join game with /koth join");
            }
        }else{
            if (isset($args[0])){
                if (strtolower($args[0]) === "start"){
                    if ($this->plugin->startArena()){
                        $sender->sendMessage("KOTH Event Started!");
                    }else{
                        $sender->sendMessage("No KOTH Arena fully setup...");
                    }
                    return true;
                } else if (strtolower($args[0]) === "stop"){
                    if ($this->plugin->forceStop()){
                        $sender->sendMessage("KOTH Event Force stopped");
                    }else{
                        $sender->sendMessage("No KOTH Arena fully setup...");
                    }
                    return true;
                }
            }
               $this->sendHelp($sender);
               return true;
        }
        return true;
            }
    public function sendHelp(CommandSender $sender){
        $sender->sendMessage("---KOTH Commands---");
        $sender->sendMessage("Make sure to run first 3 commands to fully setup Arena");
        $sender->sendMessage("1) /koth setspawn - set as many spawn points as your want!");
        $sender->sendMessage("2) /koth p1 - set point 1 for capture area");
        $sender->sendMessage("3) /koth p2 - set point 2 for capture area");
        $sender->sendMessage("/koth start - starts KOTH Match");
        $sender->sendMessage("/koth stop - force stop KOTH Math");
        $sender->sendMessage("Reload server or restart to setup Arena fully!");
    }
}
