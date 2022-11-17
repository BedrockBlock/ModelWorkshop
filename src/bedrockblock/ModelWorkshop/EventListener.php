<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop;

use bedrockblock\ModelWorkshop\workshop\WorkshopManager;
use pocketmine\block\Concrete;
use pocketmine\block\StainedHardenedClay;
use pocketmine\block\Wool;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use skymin\event\EventHandler;

final class EventListener implements Listener{

	#[EventHandler(EventPriority::LOWEST)]
	public function onPlace(BlockPlaceEvent $ev) : void{
		$block = $ev->getBlock();
		if(
			WorkshopManager::getInstance()->existsWorkshop($block->getPosition()->getWorld()->getFolderName()) &&
			!(
				$block instanceof Concrete ||
				$block instanceof StainedHardenedClay ||
				$block instanceof Wool
			)
		){
			$ev->cancel();
		}
	}
}