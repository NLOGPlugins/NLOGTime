<?php

namespace nlog\NLOGTime\event;

use pocketmine\event\Listener;
use nlog\NLOGTime\Main;
use pocketmine\command\PluginCommand;

class EventListener implements Listener {
	
	private $plugin;
	
	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}
	
	public function registerCommand($name, $permission = "true", $description = "", $usage = "") {
		$command = new PluginCommand($name, $this->plugin);
		$command->setDescription($description);
		$command->setPermission($permission);
		$command->setUsage($usage);
		$this->plugin->getServer()->getCommandMap()->register($name, $command);
	}
	
}