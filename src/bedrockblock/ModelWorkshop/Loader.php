<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop;

use bedrockblock\ModelWorkshop\command\WorkshopCmd;
use bedrockblock\ModelWorkshop\generator\WorkshopGeneretor;
use bedrockblock\ModelWorkshop\workshop\WorkshopManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\generator\GeneratorManager;

final class Loader extends PluginBase{
	use SingletonTrait;

	private WorkshopManager $manager;

	protected function onLoad() : void{
		self::setInstance($this);
	}

	protected function onEnable() : void{
		GeneratorManager::getInstance()->addGenerator(WorkshopGeneretor::class, 'workshop', static fn() => null);
		$this->getServer()->getCommandMap()->register($this->getName(), new WorkshopCmd());
		$this->manager = new WorkshopManager($this);
	}

	protected function onDisable() : void{
		$this->manager->save();
	}

	public function getManager() : WorkshopManager{
		return $this->manager;
	}
}