<?php

namespace nlog\NLOGTime\utils;

use pocketmine\Server;
use pocketmine\scheduler\PluginTask;
use nlog\NLOGTime\Main;

class Timer{

	private $owner;
	public $sender, $time;

	public function __construct($owner, $sender, $time) {
		$this->owner = $owner;
		$this->sender = $sender;
		$this->time = $time;
			
		$this->onTask();
	}

	public function onTask() {
		
		if ($this->time === 0) {
			$this->sender->sendMessage("Turn off timer");
			return true;
		}
		$time = Main::getInstance()->getTimeFromSeconds($this->time);
		$this->sender->sendMessage("{$time}");
		$this->time--;
		
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