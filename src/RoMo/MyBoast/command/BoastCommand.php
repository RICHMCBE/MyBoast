<?php

declare(strict_types=1);

namespace RoMo\MyBoast\command;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use cherrychip\iteminfochatapi\ItemInfoChatAPI;
use cherrychip\iteminfochatapi\MessageFormatType;
use Generator;
use kim\present\awaitcommand\AwaitPluginCommand;

class BoastCommand extends AwaitPluginCommand{

    private const COOLTIME = 1;

    /** @var array<string, int> */
    private static array $lastBoastAt = [];

    public function __construct(Plugin $plugin){
        parent::__construct($plugin, '자랑', '손에 든 아이템을 자랑합니다', '자랑', ['boast']);
        $this->setPermission('myboast.boast.permission');
    }

    public function onExecute(CommandSender $sender, string $commandLabel, array $args) : Generator{
        if(false){
            yield;
        }

        if(!$sender instanceof Player){
            $sender->sendMessage('§l§6 • §r§7인게임에서만 가능합니다.');
            return;
        }

        $item = $sender->getInventory()->getItemInHand();
        if($item->isNull()){
            $sender->sendMessage('§l§6 • §r§7아이템을 손에 들어주세요!');
            return;
        }

        $identifier = $sender->getXuid();
        $now = time();
        $lastAt = self::$lastBoastAt[$identifier] ?? 0;
        $elapsed = $now - $lastAt;
        if($elapsed < self::COOLTIME){
            $remainTime = self::COOLTIME - $elapsed;
            $remainMinutes = floor($remainTime / 60);
            $remainSeconds = floor($remainTime % 60);
            $sender->sendMessage("§l§6 • §r§7아직 쿨타임이 남아있습니다. 남은 시간: {$remainMinutes}분 {$remainSeconds}초");
            return;
        }
        self::$lastBoastAt[$identifier] = $now;

        $itemName = ItemInfoChatAPI::resolveItemName($item);
        $itemCount = $item->getCount();

        $itemProcessingText = ItemInfoChatAPI::buildItemProcessingText($item, $itemName);
        $chatPrefix = '§l§6 ! §r§f';
        $adverb = "{$chatPrefix}{$sender->getName()}님이";
        $itemTitle = "{$itemName} {$itemCount}개";
        $ending = '를 자랑하고 있습니다!';
        $description = $itemProcessingText;

        $encodedMessage = ItemInfoChatAPI::formatMessage(MessageFormatType::RESOURCE_PACK, $adverb, $itemTitle, $ending, $item, $description);
        Server::getInstance()->broadcastMessage($encodedMessage);
    }
}
