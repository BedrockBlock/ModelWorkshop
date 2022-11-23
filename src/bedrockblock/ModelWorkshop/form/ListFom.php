<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop\form;

use bedrockblock\ModelWorkshop\workshop\WorkshopManager;
use pocketmine\form\Form;
use pocketmine\player\Player;

final class ListFom implements Form{

	private array $workshopList;

	private array $buttons = [];

	public function __construct(){
		$this->workshopList = WorkshopManager::getInstance()->getWorkshopList();
		foreach($this->workshopList as $workshopName){
			$this->buttons[] = ['text' => $workshopName];
		}
	}

	public function jsonSerialize() : array{
		return [
			'type' => 'form',
			'title' => 'workshop list',
			'content' => 'select workshop',
			'buttons' => $this->buttons
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if($data === null) return;

	}

}