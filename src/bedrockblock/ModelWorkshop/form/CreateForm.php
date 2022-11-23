<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop\command;

use bedrockblock\ModelWorkshop\workshop\WorkshopManager;
use pocketmine\form\Form;
use pocketmine\player\Player;

use function is_numeric;
use function trim;

final class CreateForm implements Form{

	public function jsonSerialize() : array{
		return [
			'type' => 'custom_form',
			'title' => 'Create Workshop',
			'content' => [
				[
					'type' => 'input',
					'text' => 'model name',
					'placeholder' => 'string'
				],
				[
					'type' => 'input',
					'text' => 'offset Y',
					'default' => '64',
					'placeholder' => 'positive int'
				],
				[
					'type' => 'input',
					'text' => 'workshop size',
					'default' => '16',
					'placeholder' => 'positive int'
				],
				[
					'type' => 'input',
					'text' => 'block size',
					'default' => '1',
					'placeholder' => 'positive int'
				],
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if($data === null) return;
		for($key = 0; $key < 4; $key ++){
			if(!isset($data[$key])){
				$player->sendMessage('');
				return;
			}
			if($key === 0){
				$name = trim($data[0]);
				if($name === ''){
					$player->sendMessage('');
					return;
				}
				$data[0] = $name;
				continue;
			}
			if(!is_numeric($data[$key])){
				$player->sendMessage('');
				return;
			}
			$num = (int) $data[$key];
			if($num < 1){
				$player->sendMessage('');
			}
			$data[$key] = $num;
		}
		WorkshopManager::getInstance()->createWorkshop($data[0], $data[1], $data[2], $data[3]);
	}
}