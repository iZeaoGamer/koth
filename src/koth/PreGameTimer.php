<?php
/**
 * Created by PhpStorm.
 * User: JeremyMorales
 * Date: 6/22/17
 * Time: 10:51 AM
 */

namespace koth;

use pocketmine\scheduler\PluginTask;

class PreGameTimer extends PluginTask
{
    private $arena;
    private $plugin;
    private $time = 30;

    public function __construct(KothMain $owner, KothArena $arena)
    {
        parent::__construct($owner);
        $this->arena = $arena;
        $this->plugin = $owner;
    }

    public function onRun(int $currentTick)
    {
        $msg = $this->plugin->getData("starting");
        $msg = str_replace("{sec}",$this->time,$msg);
        $msg = $this->plugin->prefix().$msg;
        if ($this->time == 30 || $this->time == 15 || $this->time < 6){
            $this->plugin->getServer()->broadcastMessage($msg);
        }
        $this->time--;
        if ($this->time <1){
            $this->arena->startGame();
            $this->plugin->getServer()->broadcastMessage($this->plugin->prefix().$this->plugin->getData("begin"));
            $this->getHandler()->cancel();
        }

        $this->arena->sendPopup("Gaming Starting in.. ".$this->time);
    }

}
