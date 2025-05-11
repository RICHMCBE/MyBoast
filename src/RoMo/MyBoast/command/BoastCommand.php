<?php

declare(strict_types=1);

namespace RoMo\MyBoast\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\Server;
use RoMo\MyBoast\MyBoast;

class BoastCommand extends Command implements PluginOwned{

    use PluginOwnedTrait;

    private Server $server;

    public function __construct(MyBoast $plugin){
        parent::__construct('자랑', '손에 든 아이템을 자랑합니다', '/자랑', ['boast']);
        $this->setPermission('myboast.boast.permission');
        $this->owningPlugin = $plugin;
        $this->server = Server::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
        if(!$sender instanceof Player){
            $sender->sendMessage('§l§6 • §r§7인게임에서만 가능합니다.');
            return;
        }

        $item = $sender->getInventory()->getItemInHand();
        if($item->isNull()){
            $sender->sendMessage('§l§6 • §r§7아이템을 손에 들어주세요!');
            return;
        }

        $this->server->broadcastMessage("§l§6 ! §r§f{$sender->getName()}님이 {$item->getName()} {$item->getCount()}개를 자랑하고 있습니다!");
    }

}