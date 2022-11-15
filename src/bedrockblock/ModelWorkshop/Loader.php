<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop;

use bedrockblock\ModelWorkshop\command\WorkshopCmd;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

final class Loader extends PluginBase{
	use SingletonTrait;

	protected function onLoad() : void{
		self::setInstance($this);
	}

	protected function onEnable() : void{
		$this->getServer()->getCommandMap()->register($this->getName(), new WorkshopCmd());
	}
}