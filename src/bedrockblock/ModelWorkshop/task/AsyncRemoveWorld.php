<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop\task;

use pocketmine\scheduler\AsyncTask;

use function dir;
use function is_dir;
use function unlink;

final class AsyncRemoveWorld extends AsyncTask{

	public function __construct(private string $path){}

	public function onRun() : void{
		$this->removeDir($this->path);
	}

	private function removeDir(string $path) : void{
		$dirs = dir($path);

		while(false !== ($entry = $dirs->read())) {
			if(($entry != '.') && ($entry != '..')) {
				if(is_dir($path.'/'.$entry)) {
					$this->removeDir($path.'/'.$entry);
				} else {
					unlink($path .'/'.$entry);
				}
			}
		}
		$dirs->close();

		if(is_dir($path)){
			rmdir($path);
		}
	}
}