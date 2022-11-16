<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop\workshop;

use bedrockblock\ModelWorkshop\Loader;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;
use skymin\json\JsonFile;
use Webmozart\PathUtil\Path;

use function is_dir;

final class WorkshopManager{
	use SingletonTrait;

	private Server $server;

	public const WORLD_FOLDER = 'workshop/';
	private string $worldPath;

	private JsonFile $file;

	public function __construct(Loader $loader){
		self::setInstance($this);
		$this->server = $loader->getServer();
		$this->file = new JsonFile($loader->getDataFolder() . 'Workshop.json');
		$this->worldPath = Path::join($this->server->getDataPath(), 'worlds');
	}

	public function save() : void{
		$this->file->save();
	}

	public const DEFAULT_MODEL = [
		'format_version' => '1.12.0',
		'minecraft:geometry' => [
			'description' => [
				'identifier' => '',
				'texture_width' => 64,
				'texture_height' => 64,
				'visible_bounds_width' => 1.0,
				'visible_bounds_height' => 1.0,
				'visible_bounds_offset' => [0, 0, 0]
			],
			'bones' => []
		]
	];

	public function existsWorkshop(string $name) : bool{
		$path = Path::join($this->worldPath, self::WORLD_FOLDER . $name);
		return is_dir($path) && isset($this->file->data[$name]);
	}

	public function createWorkshop(string $name, int $yOffset, int $workshopSize, float $blockSize) : void{
		$this->server->getWorldManager()->generateWorld(self::WORLD_FOLDER . $name, WorldCreationOptions::create()
			->setSeed($yOffset)
			->setDifficulty(World::DIFFICULTY_PEACEFUL)
			->setSpawnPosition(new Vector3(0, $yOffset + 3, 0))
		);
		$this->file->data[$name] = new WorkshopSetting($yOffset, $workshopSize, $blockSize);
	}
}