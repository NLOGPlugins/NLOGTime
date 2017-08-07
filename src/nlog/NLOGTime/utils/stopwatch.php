<?php

namespace nlog\NLOGTime\utils;

use nlog\NLOGTime\Main;
use pocketmine\Server;
use pocketmine\scheduler\PluginTask;

class stopwatch {
	
	private $owner;
	public $sender, $time;
	
	public function __construct($owner, $sender) {
		
		$this->owner = $owner;
		$this->sender = $sender;
		$this->time = 0;
		
		$this->onTask();
	}
	
	public function onTask() {
		
		if (!($this->owner->queue [$this->sender->getName()])) return;
		$time = Main::getInstance()->getTimeFromSeconds($this->time);
		$this->time++;
		$this->sender->sendMessage($time);
		
		Server::getInstance()->getScheduler()->scheduleDelayedTask(new class($this->owner, $this) extends PluginTask {

				private $task;

				public function __construct($owner, $task) {
					parent::__construct($owner);
					$this->task = $task;
				}

				public function onRun($currentTick) {
					$this->task->onTask();
				}

			}, 20);
		
	}
	
}
