<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop\thread;

use bedrockblock\ModelWorkshop\workshop\WorkshopSetting;
use pocketmine\block\Block;
use pocketmine\thread\Thread;
use pocketmine\world\format\Chunk;
use pocketmine\world\World;

final class BuildThread extends Thread{

	private const CHUNK_SIZE = 4;

	/** @var Chunk|null[][] */
	private array $chunks;

	private int $yOffset;

	private float $blockSize;

	/**
	 * @template TPivot of list{float, float, float}
	 * @var TPivot[]
	 * @phpstan-var array<string, TPivot>
	 */
	private array $folders;

	public function __construct(World $world, WorkshopSetting $setting){
		$this->yOffset = $setting->getYOffset();
		$this->blockSize = $setting->getBlockSize();
		$this->folders = $setting->getFolders();
		$size = $setting->getWorkshopSize();
		$chunks = [];
	}

	public function onRun() : void{
		// TODO: Implement onRun() method.
	}

}