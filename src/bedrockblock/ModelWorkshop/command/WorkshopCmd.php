<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop\command;

use bedrockblock\ModelWorkshop\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

final class WorkshopCmd extends Command implements PluginOwned{
	use PluginOwnedTrait;

	public function __construct(){
		parent::__construct('workshop');
		$this->owningPlugin = Loader::getInstance();
		$this->setPermission('workshop.cmd');
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if(!$sender instanceof Player){
			$sender->sendMessage('In game only!');
			return false;
		}
		if(!isset($args[0])){
			$this->sendUsageMsg($sender);
			return false;
		}
		match($args[0]){
			'create' => $sender->sendMessage('create'),
			'list' => $sender->sendMessage('list'),
			'build' => $sender->sendMessage('build'),
			'folder' => $sender->sendMessage('folder'),
			'offset', 'offsetBlock' => $sender->sendMessage('offsetBlock'),
			default => $this->sendUsageMsg($sender)
		};
	}

	private function sendUsageMsg(CommandSender $sender) : void{
		$sender->sendMessage(
			"======= MAIN =======\n" .
			"/workshop create\n" .
			"/workshop list\n" .
			"== IN WOKRSHOP MAP ==\n" .
			"/workshop build\n" .
			"/workshop folder\n" .
			"/workshop offsetBlock"
		);
	}
}