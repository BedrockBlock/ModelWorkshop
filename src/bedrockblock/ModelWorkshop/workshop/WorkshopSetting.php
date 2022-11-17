<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop\workshop;

use JsonSerializable;

final class WorkshopSetting implements JsonSerializable{

	/**
	 * @template TPivot of list{float, float, float}
	 * @param TPivot[]                      $folders
	 * @phpstan-param array<string, TPivot> $folders
	 */
	public function __construct(
		private int $yOffset,
		private int $workshopSize,
		private float $blockSize,
		private array $folders = []
	){}

	/**
	 * @template TPivot of list{float, float, float}
	 * @param array $data
	 * @phpstan-param array{
	 *     yOffset: int,
	 *     workshopSize: int,
	 *     blockSize: float,
	 *     folders: array<string, TPivot>
	 * }            $data
	 * @return WorkshopSetting
	 */
	public static function jsonDeserialize(array $data) : self{
		return new self($data['yOffset'], $data['workshopSize'], $data['blockSize'], $data['folders']);
	}

	/**
	 * @template TPivot of list{float, float, float}
	 * @return array
	 * @phpstan-return array{
	 *     yOffset: int,
	 *     workshopSize: int,
	 *     blockSize: float,
	 *     folders: array<string, TPivot>
	 * }
	 */
	public function jsonSerialize() : array{
		return [
			'yOffset' => $this->yOffset,
			'workshopSize' => $this->workshopSize,
			'blockSize' => $this->blockSize,
			'folders' => $this->folders
		];
	}

	public function getYOffset() : int{
		return $this->yOffset;
	}

	public function getWorkshopSize() : int{
		return $this->workshopSize;
	}

	public function setWorksholSize(int $size) : void{
		$this->workshopSize = $size;
	}

	public function getBlockSize() : float{
		return $this->blockSize;
	}

	public function setBlockSize(float $size) : void{
		$this->blockSize = $size;
	}

	public function existsFolder(string $name) : bool{
		return isset($this->folders[$name]);
	}

	/**
	 * @return float[]
	 * @phpstan-rerurn list{float, float, float}
	 */
	public function getFolderPivot(string $name) : array{
		return $this->folders[$name];
	}

	/** @param float[] $pivot */
	public function addFoldee(string $name, array $pivot) : void{
		$this->folders[$name] = $pivot;
	}

	public function deleteFolder(string $name) : void{
		unset($this->folders[$name]);
	}

	/**
	 * @template TPivot of list{float, float, float}
	 * @return TPivot[]
	 * @phpstan-return array<string, TPivot>
	 */
	public function getFolders() : array{
		return $this->folders;
	}
}