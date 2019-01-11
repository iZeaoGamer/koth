<?php
/**
 * Created by PhpStorm.
 * User: JeremyMorales
 * Date: 6/22/17
 * Time: 10:26 AM
 */

namespace koth;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;

class KothListener implements Listener{

    private $plugin;

    public function __construct(KothMain $main)
    {
        $this->plugin = $main;
    }

    public function onRespawn(PlayerRespawnEvent $ev){


        if ($this->plugin->isRunning()){
            $p = $ev->getPlayer();
            $p->addTitle($this->plugin->getData("still_running_title"),$this->plugin->getData("still_running_sub"));
             KothMain::getInstance()->getPlugin()->sendRandomSpot($p);
        }
    }
    public function onJoin(PlayerJoinEvent $event){
        if($this->plugin->isRunning()){
        if($this->plugin->addPlayer($event->getPlayer())){
            KothMain::getInstance()->getKothArena()->sendRandomSpot($event->getPlayer());
        }
    }
    }
    public function onCommand(PlayerCommandPreprocessEvent $ev){
        $cmd = $ev->getMessage()[0];
        if ($cmd === "/spawn" || $cmd === "/hub" || $cmd === "/lobby"){
            $this->plugin->removePlayer($ev->getPlayer());
        }
    }

}
