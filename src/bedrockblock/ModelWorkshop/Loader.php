<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop;

use bedrockblock\ModelWorkshop\command\WorkshopCmd;
use bedrockblock\ModelWorkshop\entity\ModelTestEntity;
use bedrockblock\ModelWorkshop\generator\WorkshopGeneretor;
use bedrockblock\ModelWorkshop\workshop\WorkshopManager;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\World;

final class Loader extends PluginBase{
	use SingletonTrait;

	private WorkshopManager $manager;

	protected function onLoad() : void{
		self::setInstance($this);
	}

	protected function onEnable() : void{
		GeneratorManager::getInstance()->addGenerator(WorkshopGeneretor::class, 'workshop', static fn() => null);
		EntityFactory::getInstance()->register(ModelTestEntity::class, static function(World $world, CompoundTag $nbt) : ModelTestEntity{
			return new ModelTestEntity(EntityDataHelper::parseLocation($nbt, $world), ModelTestEntity::parseSkinNBT($nbt), $nbt);
		}, ['TestModel', 'WorkshopTest']);
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