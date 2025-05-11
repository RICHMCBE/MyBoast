<?php

declare(strict_types=1);

namespace RoMo\MyBoast;

use pocketmine\plugin\PluginBase;
use RoMo\MyBoast\command\BoastCommand;

class MyBoast extends PluginBase{

    protected function onEnable() : void{
        $this->getServer()->getCommandMap()->register('MyBoast', new BoastCommand($this));
    }
}