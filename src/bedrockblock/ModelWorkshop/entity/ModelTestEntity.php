<?php

declare(strict_types=1);

namespace bedrockblock\ModelWorkshop\entity;

use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\convert\SkinAdapterSingleton;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\PlayerSkinPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandPermissions;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\network\mcpe\protocol\types\GameMode;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\types\skin\SkinData;
use pocketmine\network\mcpe\protocol\types\UpdateAbilitiesPacketLayer;
use pocketmine\network\mcpe\protocol\UpdateAbilitiesPacket;
use pocketmine\player\Player;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use function array_fill;

final class ModelTestEntity extends Entity{

	protected Skin $skin;
	protected SkinData $skinData;
	protected UuidInterface $uuid;

	public function __construct(Location $pos, Skin $skin, ?CompoundTag $nbt = null){
		$this->setSkin($skin);
		parent::__construct($pos, $nbt);
	}

	final public static function getNetworkTypeId() : string{ return EntityIds::PLAYER; }

	final public static function parseSkinNBT(CompoundTag $nbt) : Skin{
		$skinTag = $nbt->getCompoundTag('Skin');
		if($skinTag === null){
			throw new SavedDataLoadingException('Missing skin data');
		}
		return new Skin(
			$skinTag->getString("Name"),
			($skinDataTag = $skinTag->getTag("Data")) instanceof StringTag ? $skinDataTag->getValue() : $skinTag->getByteArray("Data"), //old data (this used to be saved as a StringTag in older versions of PM)
			$skinTag->getByteArray("CapeData", ""),
			$skinTag->getString("GeometryName", ""),
			$skinTag->getByteArray("GeometryData", "")
		);
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();
		$skin = $this->skin;
		$nbt->setTag('Skin', CompoundTag::create()
			->setString('Name', $skin->getSkinId())
			->setByteArray('Data', $skin->getSkinData())
			->setByteArray('CapeData', $skin->getCapeData())
			->setString('GeometryName', $skin->getGeometryName())
			->setByteArray('GeometryData', $skin->getGeometryData())
		);
		return $nbt;
	}

	public final function attack(EntityDamageEvent $source) : void{
		if($source instanceof EntityDamageByEntityEvent){
			$damager = $source->getDamager();
			if($damager instanceof Player && $damager->hasPermission('workshop.entity.close') && $damager->isSneaking()){
				$this->close();
			}
		}
		$source->cancel();
	}

	public final function getSkin() : Skin{
		return $this->skin;
	}

	public function setSkin(Skin $skin) : void{
		$this->skin = $skin;
		$this->skinData = SkinAdapterSingleton::get()->toSkinData($skin);
	}

	/** @param Player[] $targets */
	public final function sendSkin(?array $targets = null) : void{
		$this->server->broadcastPackets($targets ?? $this->hasSpawned, [
			PlayerSkinPacket::create($this->getUniqueId(), '', '', $this->skinData)
		]);
	}

	public final function getUniqueId() : UuidInterface{
		return $this->uuid;
	}

	public function broadcastMovement(bool $teleport = false) : void{
		$pos = $this->location;
		$pk = new MovePlayerPacket();
		$pk->actorRuntimeId = $this->id;
		$pk->position = $this->getOffsetPosition($pos);
		$pk->pitch = $pos->pitch;
		$pk->yaw = $pk->headYaw = $pos->yaw;
		$pk->mode = $teleport ? MovePlayerPacket::MODE_TELEPORT : MovePlayerPacket::MODE_NORMAL;
		$pos->getWorld()->broadcastPacketToViewers($pos, $pk);
	}

	final protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(1.0, 1.0, 0.5);
	}

	final protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->uuid = Uuid::uuid3(Uuid::NIL, ((string) $this->getId()) . $this->skin->getSkinData() . $this->getNameTag());
		$this->setNameTagVisible(false);
		$this->setNameTagAlwaysVisible(false);
		$this->setInvisible();
		$this->initSkill();
	}

	final protected function initSkill() : void{ }

	protected final function sendSpawnPacket(Player $player) : void{
		$network = $player->getNetworkSession();

		$network->sendDataPacket(PlayerListPacket::add([PlayerListEntry::createAdditionEntry($this->uuid, $this->id, $this->getNameTag(), $this->skinData)]));

		$network->sendDataPacket(AddPlayerPacket::create(
			$this->getUniqueId(),
			$this->getNameTag(),
			$this->getId(),
			'',
			$this->location->asVector3(),
			$this->getMotion(),
			$this->location->pitch,
			$this->location->yaw,
			$this->location->yaw,
			ItemStackWrapper::legacy(TypeConverter::getInstance()->coreItemStackToNet(VanillaItems::AIR())),
			GameMode::SURVIVAL,
			$this->getAllNetworkData(),
			new PropertySyncData([], []),
			UpdateAbilitiesPacket::create(CommandPermissions::NORMAL, PlayerPermissions::VISITOR, $this->getId(), [
				new UpdateAbilitiesPacketLayer(
					UpdateAbilitiesPacketLayer::LAYER_BASE,
					array_fill(0, UpdateAbilitiesPacketLayer::NUMBER_OF_ABILITIES, false),
					0.0,
					0.0
				)
			]),
			[],
			'',
			DeviceOS::UNKNOWN
		));

		$network->sendDataPacket(PlayerListPacket::remove([PlayerListEntry::createRemovalEntry($this->uuid)]));
	}

}