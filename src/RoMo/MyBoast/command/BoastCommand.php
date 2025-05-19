<?php

declare(strict_types=1);

namespace RoMo\MyBoast\command;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use RoMo\MyBoast\MyBoast;
use naeng\CooltimeCore\CooltimeCore;
use Generator;
use kim\present\awaitcommand\AwaitPluginCommand;
use kim\present\koritemname\KorItemName;

class BoastCommand extends AwaitPluginCommand{

    private const COOLTIME = 300; // 5분 쿨타임

    public function __construct(Plugin $plugin){
        parent::__construct($plugin, '자랑', '손에 든 아이템을 자랑합니다', '자랑', ['boast']);
        $this->setPermission('myboast.boast.permission');
    }

    public function onExecute(CommandSender $sender, string $commandLabel, array $args) : Generator{
        if(!$sender instanceof Player){
            $sender->sendMessage('§l§6 • §r§7인게임에서만 가능합니다.');
            return;
        }

        $item = $sender->getInventory()->getItemInHand();
        if($item->isNull()){
            $sender->sendMessage('§l§6 • §r§7아이템을 손에 들어주세요!');
            return;
        }

        // CooltimeCore 사용하여 쿨타임 확인
        if(class_exists(CooltimeCore::class)){
            $identifier = "boast-{$sender->getXuid()}";
            $cooltime = (yield from CooltimeCore::get($identifier)) ?? self::COOLTIME + 1; // 없으면 쿨타임 만료된 것으로 처리

            if($cooltime < self::COOLTIME){
                $remainTime = self::COOLTIME - $cooltime;
                $remainMinutes = floor($remainTime / 60);
                $remainSeconds = floor($remainTime % 60);
                $sender->sendMessage("§l§6 • §r§7아직 쿨타임이 남아있습니다. 남은 시간: {$remainMinutes}분 {$remainSeconds}초");
                return;
            }

            // 쿨타임 설정
            if(!(yield from CooltimeCore::create($identifier))){
                $sender->sendMessage("§l§6 • §r§7쿨타임 데이터베이스가 정상적으로 응답하지 않았습니다. 다시 시도해 주세요.");
                return;
            }
        }

        // KorItemName 플러그인을 사용하여 한글 이름 가져오기
        $itemName = $item->getName(); // 기본 이름
        $itemCount = $item->getCount();

        // KorItemName 클래스가 존재하는지 확인 후 사용
        if(class_exists(KorItemName::class)){
            $itemName = KorItemName::translate($item);
        }

        // 자랑 메시지 전송 (한글 이름 사용)
        Server::getInstance()->broadcastMessage("§l§6 ! §r§f{$sender->getName()}님이 {$itemName} {$itemCount}개를 자랑하고 있습니다!");
    }
}