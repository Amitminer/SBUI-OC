<?php

declare(strict_types=1);

namespace sbui;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use sbui\libs\Vecnavium\FormsUI;
use sbui\libs\Vecnavium\FormsUI\Form;
use sbui\libs\Vecnavium\FormsUI\CustomForm;
use sbui\libs\Vecnavium\FormsUI\SimpleForm;


class Main extends PluginBase implements Listener {
    
  private string $pluginVersion;

  public function onEnable() : void{
        $pluginManager = $this->getServer()->getPluginManager();
        $version = $this->pluginVersion = $this->getDescription()->getVersion();
        if($version == "1.0.0"){
            $this->getLogger()->info("sbui is enabled");
        } else {
            $this->getLogger()->warning("You have outdated version of sbui !!");
            $pluginManager->disablePlugin($this);
                }
            }

  public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        switch ($command->getName()) {
            case "sbui":
                if($sender instanceof Player) {
                           $this->sbUI($sender);
                     } else {
                             $sender->sendMessage("Use this command in-game");
                              return true;
                     }
            break;
        }
        return true;
    }
    # sbtui
  public function sbUI(Player $player){
        $form = new SimpleForm(function(Player $player, int $data = null){
            if($data === null){
                return true;
            }
            switch($data){
                case 0:
                    $this->getServer()->dispatchCommand($player, "is tp");
                break;
              
                case 1:
                    $this->getServer()->dispatchCommand($player, "is chat");
                break;
                
                case 2:
                    $this->getServer()->dispatchCommand($player, "is visit");
                break;
                
                case 3:
                    $this->getServer()->dispatchCommand($player, "is invite");
                break;
                
                case 4:
                    $this->getServer()->dispatchCommand($player, "is accept");
                break;
            }
        });
        # formss
        $form->setTitle("§l§cSkyblock Menu");
        $form->addButton("Join Island"); #0
        $form->addButton("Island Chat"); #1
        $form->addButton("Visit Island"); #2
        $form->addButton("Invite Player"); #3
        $form->addButton("Accept Invite"); #4
        $form->addButton("§l§cEXIT", 0, "textures/blocks/barrier"); #5
        $player->sendForm($form);
    }
}
