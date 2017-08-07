<?php

namespace nlog\NLOGTime;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use nlog\NLOGTime\utils\stopwatch;
use nlog\NLOGTime\utils\Timer;
use pocketmine\event\player\PlayerQuitEvent;
use nlog\NLOGTime\event\EventListener;

class Main extends PluginBase implements Listener {
	
	private static $instance = null;
	
	private $event;
	public $queue = [ ];
	
	public function onLoad() {
		date_default_timezone_set("Asia/Seoul");
		self::$instance = $this;
	}
	
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		
		$this->event = new EventListener($this);
		$this->event->registerCommand("watch", "true", "시간에 관련된 명령어");
	}
	
	public static function getInstance() {
		return self::$instance;
	}
	
	public function getTimeFromSeconds($seconds) {
		$h = sprintf("%02d", intval($seconds) / 3600);
		$tmp = $seconds % 3600;
		$m = sprintf("%02d", $tmp / 60);
		$s = sprintf("%02d", $tmp % 60);
	
		return $h.':'.$m.':'.$s;
	}
	
	public function startsWith($haystack, $needle) {
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}
	
	public function onCommand(CommandSender $sender,Command $cmd, $label,array $args) {
		if (strtolower($cmd->getName()) === "watch") {
			if (!isset($args[0])) {
				$sender->sendMessage("/watch stopwatch <start | stop>");
				$sender->sendMessage("/watch timer <time>");
				$sender->sendMessage("/watch view");
				return true;
			}
			switch ($args[0]) {
				case "stopwatch":
					if (!isset($args[1])) {
						$sender->sendMessage("/watch stopwatch <start | stop>");
						return true;
					}
					if ($args[1] !== "start" && $args[1] !== "stop") {
						$sender->sendMessage("/watch stopwatch <start | stop>");
						return true;
					}
					if ($args[1] === "start") {
						$this->queue [$sender->getName()] = true;
						$sender->sendMessage("start stopwatch");
						new stopwatch($this, $sender);
						return true;
					}
					if ($args[1] === "stop") {
						if ($this->queue [$sender->getName()]) {
							$this->queue [$sender->getName()] = false;
							return true;
						}else{
							$sender->sendMessage("Your stopwatch doesn't start");
							return true;
						}
					}
				case "timer":
					if (!isset($args[1])) {
						$sender->sendMessage("/watch stopwatch <time>");
						return true;
					}
					if (!is_numeric($args[1])) {
						$sender->sendMessage("Enter numuric number");
						return true;
					}
					/*if ($this->startsWith($args[1], "-")) {
						$sender->sendMessage("You can't enter negative number");
						return true;
					}*/
					if ($args[1] < 1) {
						$sender->sendMessage("Please enter at least 1");
						return true;
					}
					$sender->sendMessage("Start timer. Time set {$args[1]}");
					new Timer($this, $sender, $args[1]);
					return true;
				case "view":
					$sender->sendMessage(date("Y-m-d h-m-s"));
					return true;
				default:
					$sender->sendMessage("/watch stopwatch <start | stop>");
					$sender->sendMessage("/watch timer <time>");
					$sender->sendMessage("/watch view");
					return true;
			}
		}
	}
	
	public function onQuitEvent (PlayerQuitEvent $ev) {
		if (isset($this->queue [$ev->getPlayer()->getName()])) {
			$this->queue [$ev->getPlayer()->getName()] = false;
		}
	}
	
	
	
}