<?php
/**
 * Created by PhpStorm.
 * User: JeremyMorales
 * Date: 6/22/17
 * Time: 9:49 AM
 */
namespace koth;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
class KothMain extends PluginBase
{
    private $msg;
    private $c;
    private $arena = null;
    private $fac;
    private static $instance;
    
    public function onEnable(){
        self::$instance = $this;
        @mkdir($this->getDataFolder());
        $this->msg = new Config($this->getDataFolder()."config.yml",Config::YAML,[
            "capture_time" => 100,
            "game_time" => 10,
            "reset_capture_progress" => true,
            "prefix" => "[KOTH] ",
            "starting" => "Game starting in {sec}. Join Game now! (/koth join)",
            "begin" => "KOTH Started! (/koth join)",
            "joined" => "Joined game successfully!, Be the first to capture the area now!",
            "transfer" => "149.56.127.110, 25634",
            "left" => "Left the KOTH succesfully!",
            "not_in_game" => "You're not in a koth game.",
            "win" => "{faction} | {player} has captured the area and won the event!",
            "end" => "Event has ended!",
            "not_running" => "There is no KOTH event running at the moment!",
            "still_running_title" => "KOTH Running!",
            "still_running_sub" => "Join now with /koth join !",
            "progress" => "Capturing... {percent}%",
            "end_game" => "Game Ended!",
            "game_bar" => "KOTH Time Left: {time}",
            "rewards" => [
                "givemoney {player} 1000",
                "give {player} diamond 2"
        ]
        ]);
        $this->c = new Config($this->getDataFolder()."arena.yml", Config::YAML);
        $this->getLogger()->notice("KOTH Plugin Enabled!");
        $all = $this->c->getAll();
        if (isset($all["spawns"]) && $all["p1"] && $all["p2"]){
            $this->arena = new KothArena($this,$all["spawns"],["p1" => $all["p1"], "p2" => $all["p2"]]);
            $this->getLogger()->info("KOTH Arena Loaded Successfully");
        }else{
            $this->getLogger()->alert("No arena setup! Please set one up!");
        }
        //Register Listener
        $this->getServer()->getPluginManager()->registerEvents(new KothListener($this),$this);
        //Register Command
        $this->getServer()->getCommandMap()->register("koth", new KothCommand("koth",$this));
        $this->fac = $this->getServer()->getPluginManager()->getPlugin("FactionsPro");
        if ($this->fac == null) $this->getLogger()->critical("FactionsPro Plugin not found... Disabled {faction} support!");
    }
    public static function getInstance() : self{
        return self::$instance;
    }
    public static function getKothArena(): KothArena{
        return $this->arena;
    }
    public function getFaction(Player $player){
        return $this->fac == null ? "" : $this->fac->getPlayerFaction($player->getName());
    }
    public function onDisable(){
        $arena = $this->arena;
        if ($arena instanceof KothArena){
            $arena->resetGame();
        }
    }
    public function getRewards() : array {
        $all = $this->msg->getAll();
        return isset($all["rewards"]) ? $all["rewards"] : [];
    }
    public function setPoint(Player $player, $type){
        $save = $player->getX().":".$player->getY().":".$player->getZ().":".$player->getLevel()->getName();
        $all = $this->c->getAll();
        if ($type === "spawn"){
            $all["spawns"][] = $save;
        }else{
            $all[$type] = $save;
        }
        $this->c->setAll($all);
        $this->c->save();
    }
    public function startArena() : bool {
        $arena = $this->arena;
        if ($arena instanceof KothArena) {
            $arena->preStart();
            return true;
        }
        return false;
    }
    public function forceStop() : bool {
        $arena = $this->arena;
        if ($arena instanceof KothArena) {
            $arena->resetGame();
            return true;
        }
        return false;
    }
    public function isRunning() : bool {
        $arena = $this->arena;
        if ($arena instanceof KothArena) {
            if ($arena->isRunning()) return true;
        }
        return false;
    }
    public function sendToKoth(Player $player) : bool {
        $arena = $this->arena;
        if ($arena instanceof KothArena) {
            if ($arena->isRunning()){
                $arena->addPlayer($player);
                return true;
            }
        }
        return false;
    }
    public function prefix() : string {
        $all = $this->msg->getAll();
        return isset($all["prefix"]) ? $all["prefix"] : "[KOTH] ";
    }
    public function removePlayer($player){
        $arena = $this->arena;
        if ($arena instanceof KothArena) {
            $arena->removePlayer($player);
        }
        return false;
    }
    public function getData($type) : string {
        $all = $this->msg->getAll();
        return isset($all[$type]) ? $all[$type] : "";
    }
}
