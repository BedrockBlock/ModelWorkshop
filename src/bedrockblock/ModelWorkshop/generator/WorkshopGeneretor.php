<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop\generator;

use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Generator;

final class WorkshopGeneretor extends Generator{

	public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
		if($chunkX == 0 && $chunkZ == 0) {
			/** @phpstan-var Chunk $chunk */
			$chunk = $world->getChunk($chunkX, $chunkZ);
			$y = $this->seed;
			$block = BlockLegacyIds::GLOWSTONE << Block::INTERNAL_METADATA_BITS;
			$chunk->setFullBlock(0, $y, 0, $block);
			$chunk->setFullBlock(-1, $y, 0, $block);
			$chunk->setFullBlock(0, $y, -1, $block);
			$chunk->setFullBlock(-1, $y--, -1, $block);
			$chunk->setFullBlock(0, $y, 0, $block);
			$chunk->setFullBlock(-1, $y, 0, $block);
			$chunk->setFullBlock(0, $y, -1, $block);
			$chunk->setFullBlock(-1, $y, -1, $block);
		}
	}

	public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {}
}
