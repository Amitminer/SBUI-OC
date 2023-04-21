<?php

declare(strict_types = 1);

namespace sbui;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\event\event;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use sbui\libs\Vecnavium\FormsUI;
use sbui\libs\Vecnavium\FormsUI\Form;
use sbui\libs\Vecnavium\FormsUI\CustomForm;
use sbui\libs\Vecnavium\FormsUI\SimpleForm;
use sbui\libs\Vecnavium\FormsUI\ModalForm;
use room17\SkyBlock\session;
use room17\SkyBlock\session\SessionLocator;

class Main extends PluginBase implements Listener {

    private string $pluginVersion;
    private bool $keepInventory = true;
    private bool $keepXp = true;

    public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $pluginManager = $this->getServer()->getPluginManager();
        $version = $this->pluginVersion = $this->getDescription()->getVersion();
        if ($version == "2.0.0") {
            $this->getLogger()->info("sbui is enabled");
        } else {
            $this->getLogger()->warning("You have outdated version of sbui !!");
            $pluginManager->disablePlugin($this);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        switch ($command->getName()) {
            case "sbui":
            if ($sender instanceof Player) {
                $playerSession = SessionLocator::getSession($sender);
                if (!$playerSession->hasIsland()) {
                    $this->createIsland($sender);
                } else {
                    $this->sbUI($sender);
                }
            } else {
                $sender->sendMessage("Use this command in-game");
            }
            break;
    }
    return true;
    }
    # sbtui
    public function sbUI(Player $player) {
        $form = new SimpleForm(function(Player $player, int $data = null) {
            if ($data === null) {
                return true;
            }
            switch ($data) {
                    case 0: // Island General
                        $this->islandGeneralUI($player);
                        break;
                    case 1: // Island Chat
                        $this->islandChatUI($player);
                        break;
                    case 2: // Island Info
                        $this->islandInfoUI($player);
                        break;
                    case 3: // Island Settings
                        $this->islandSettingsUI($player);
                        break;
                }
            });
            # formss
            $form->setTitle("§b§lSkyBlock§r§8 - §aMain Menu");
            $form->addButton("§a§lIsland General\n§r§7Manage your island",
                0,
                "textures/blocks/sapling_birch");
            $form->addButton("§a§lIsland Chat\n§r§7Chat with your island members",
                0,
                "textures/items/sign");
            $form->addButton("§a§lIsland Info\n§r§7View information about your island",
                0,
                "textures/items/book_normal");
            $form->addButton("§a§lIsland Settings\n§r§7Customize your island settings",
                0,
                "textures/items/repeater");
            $form->addButton("§l§cEXIT",
                0,
                "textures/blocks/barrier");
            $player->sendForm($form);
    }
    public function islandGeneralUI(Player $player){
    $form = new SimpleForm(function(Player $player, $data){
        $result = $data;
        if($result === null){
            return true;
        }
        switch($result){
            case 0:
                $this->getServer()->dispatchCommand($player, "is go");
                break;
            case 1:
                $this->visitUI($player);
                break;
            case 2:
                $this->coopUI($player);
                break;
            case 3:
                $this->getServer()->dispatchCommand($player, "is accept");
                break;
            case 4:
                $this->getServer()->dispatchCommand($player, "is deny");
                break;
            case 5:
                $this->sbUI($player);
        }
        return true;
    });
    $form->setTitle("§b§lSkyBlock§r§8 - §aIsland General");
    $form->setContent("§7Select an option below:");
    $form->addButton("§aGo to Island", 0, "textures/items/diamond");
    $form->addButton("§aVisit another Island", 0, "textures/items/emerald");
    $form->addButton("§aIsland Co-op", 0, "textures/items/book_normal");
    $form->addButton("§aAccept Island invite", 0, "textures/items/gold_ingot");
    $form->addButton("§cDeny Island invite", 0, "textures/items/redstone_dust");
    $form->addButton("§l§cEXIT", 0,"textures/blocks/barrier");
    $player->sendForm($form);
    }
    public function visitUI(Player $player){
    $form = new SimpleForm(function(Player $player, $data){
        $result = $data;
        if($result === null){
            return true;
        }
        $this->getServer()->dispatchCommand($player, "is visit " . $result);
        return true;
    });
    $form->setTitle("§b§lSkyBlock§r§8 - §aVisit another Island");
    $form->setContent("§7Select the Island you want to visit:");
    $islandNames = array();
    foreach($this->getServer()->getOnlinePlayers() as $player) {
    $islandNames[] = $player->getName();
    }
    $em = "textures/items/emerald";
    foreach($islandNames as $name){
        $form->addButton($name, 0, $em, $name);
    }
    $player->sendForm($form);
    }
    public function coopUI(Player $player){
    $form = new SimpleForm(function(Player $player, $data){
        $result = $data;
        if($result === null){
            return true;
        }
        $this->getServer()->dispatchCommand($player, "is cooperate " . $result);
        return true;
    });
    $form->setTitle("§b§lSkyBlock§r§8 - §aIsland Cooperate");
    $form->setContent("§7Select the player you want to co-operate with:");
    $players = $this->getServer()->getOnlinePlayers();
    foreach($players as $p) {
        if($p !== $player) {
            $form->addButton($p->getName(), 0, "", $p->getName());
        }
    }
    $player->sendForm($form);
    }
    public function islandChatUI(Player $player){
    $form = new SimpleForm(function(Player $player, $data){
        if($data === null){
            return true;
        }
        if($data === 0){ // Enable Chat button pressed
            $this->getServer()->dispatchCommand($player, "is chat on");
        } elseif($data === 1){ // Disable Chat button pressed
            $this->getServer()->dispatchCommand($player, "is chat off");
        } else { // Exit button pressed
            $this->sbUI($player);
        }
        return true;
    });
    $form->setTitle("§b§lSkyBlock§r§8 - §aIsland Chat");
    $form->setContent("§7Select an option:");
    $form->addButton("§a§lEnable Chat", 0, "textures/items/dye_powder_lime");
    $form->addButton("§c§lDisable Chat", 0, "textures/items/dye_powder_red");
    $form->addButton("§l§cEXIT", 0, "textures/blocks/barrier");
    $player->sendForm($form);
    }
    public function islandInfoUI(Player $player) {
    $form = new SimpleForm(function(Player $player, $data) {
        if($data === null) {
            return true;
        }
        $cmd = "";
        switch($data) {
            case 0:
                $this->getServer()->dispatchCommand($player,"is members");
                break;
            case 1:
                $this->getServer()->dispatchCommand($player,"is blocks");
                break;
            case 2:
                $this->getServer()->dispatchCommand($player,"is category");
                break;
            case 3:
                $this->sbUI($player);
                break;
        }
    });
    $form->setTitle("§b§lSkyBlock§r§8 - §aIsland Info");
    $form->setContent("§7Select an option below:");
    $form->addButton("§b§lIsland Members§r\n§7View members on your island", 0, "textures/items/paper", "0");
    $form->addButton("§a§lIsland Blocks§r\n§7View the amount of each block on your island", 0, "textures/items/diamond_pickaxe", "1");
    $form->addButton("§6§lIsland Category§r\n§7View your island category", 0, "textures/items/book_normal", "2");
    $form->addButton("§l§cEXIT", 0, "textures/blocks/barrier", "exit");
    $player->sendForm($form);
    }
    
    public function islandSettingsUI(Player $player){
    $form = new SimpleForm(function(Player $player, $data){
        $result = $data;
        if($result === null){
            return true;
        }
        switch($result){
            case 0:
                // Lock or unlock island
                $form = new SimpleForm(function(Player $player, $data){
                    $result = $data;
                    if($result === null){
                        return true;
                    }
                    if($result === 0){
                        $this->getServer()->dispatchCommand($player, "is lock");
                    }else{
                        $this->getServer()->dispatchCommand($player, "is lock");
                    }
                    return true;
                });
                $form->setTitle("§b§lSkyBlock§r§8 - §aLock or Unlock Island");
                $form->setContent("§7Select the option you want:");
                $form->addButton("§aLock Island");
                $form->addButton("§aUnlock Island");
                $player->sendForm($form);
                break;
            case 1:
                // Promote player
                $form = new CustomForm(function(Player $player, $data){
                    if($data === null){
                        return true;
                    }
                    $this->getServer()->dispatchCommand($player, "is promote " . $data[0]);
                    return true;
                });
                $form->setTitle("§b§lSkyBlock§r§8 - §aPromote Player");
                $form->addInput("§7Enter the username of the player to promote:", "", "");
                $player->sendForm($form);
                break;
            case 2:
                // Invite player
                $form = new CustomForm(function(Player $player, $data){
                    if($data === null){
                        return true;
                    }
                    $this->getServer()->dispatchCommand($player, "is invite " . $data[0]);
                    return true;
                });
                $form->setTitle("§b§lSkyBlock§r§8 - §aInvite Player");
                $form->addInput("§7Enter the username of the player to invite:", "", "");
                $player->sendForm($form);
                break;
            case 3:
                // Kick player
                $form = new CustomForm(function(Player $player, $data){
                    if($data === null){
                        return true;
                    }
                    $this->getServer()->dispatchCommand($player, "is kick " . $data[0]);
                    return true;
                });
                $form->setTitle("§b§lSkyBlock§r§8 - §aKick Player");
                $form->addInput("§7Enter the username of the player to kick:", "", "");
                $player->sendForm($form);
                break;
            case 4:
                // Banish player
                $form = new CustomForm(function(Player $player, $data){
                    if($data === null){
                        return true;
                    }
                    $this->getServer()->dispatchCommand($player, "is banish " . $data[0]);
                    return true;
                });
                $form->setTitle("§b§lSkyBlock§r§8 - §aBanish Player");
                $form->addInput("§7Enter the username of the player to banish:", "", "");
                $player->sendForm($form);
                break;
            case 5:
                // Set island spawn
                $this->getServer()->dispatchCommand($player, "is setspawn");
                break;
            case 6:
                $this->dangerzoneUI($player);
                break;
            case 7:
                $this->sbUI($player);
                break;
        }
        return true;
    });
    $form->setTitle("§b§lSkyBlock§r§8 - §aIsland Settings");
    $form->setContent("§7Choose what to do:");
    $form->addButton("§aLock/Unlock Island",0,"textures/blocks/trip_wire_source");
    $form->addButton("§aPromote Player", 0, "textures/items/book_writable");
    $form->addButton("§aInvite Player", 0, "textures/items/paper");
    $form->addButton("§aKick Player", 0, "textures/items/iron_boots");
    $form->addButton("§aBanish Player", 0, "textures/items/iron_sword");
    $form->addButton("§aSet Island Spawn", 0, "textures/items/bed_red");
    $form->addButton("§cDanger Zone", 0, "textures/blocks/tnt_side");
    $form->addButton("§l§cEXIT", 0, "textures/blocks/barrier");
    $player->sendForm($form);
    }
    public function dangerZoneUI(Player $player) {
    $form = new SimpleForm(function(Player $player, ?int $data) {
        if($data === null) {
            return;
        }
        switch($data) {
            case 0:
                $this->sbUI($player);
            case 1:
                $this->leaveIslandUI($player);
                break;
            case 2:
                $this->transferIslandUI($player);
                break;
            case 3:
                $this->disbandIslandUI($player);
                break;
        }
    });
    $form->setTitle("§cDanger Zone");
    $form->setContent("§7Choose an action:");
    $form->addButton("§l§cEXIT", 0, "textures/blocks/barrier");
    $form->addButton("§cLeave Island");
    $form->addButton("§cTransfer Island");
    $form->addButton("§cDisband/Delete Island");
    $form->sendToPlayer($player);
    }
    public function leaveIslandUI(Player $player) {
    $form = new ModalForm(function(Player $player, ?bool $data) {
        if($data === null) {
            return;
        }
        if($data) {
            $this->getServer()->dispatchCommand($player, "is leave");
        }
    });
    $form->setTitle("§cLeave Island");
    $form->setContent("§7Are you sure you want to leave the island?");
    $form->setButton1("§aYes");
    $form->setButton2("§cNo");
    $form->sendToPlayer($player);
    }
    public function transferIslandUI(Player $player) {
    $form = new CustomForm(function(Player $player, ?array $data) {
        if($data === null) {
            return;
        }
        $playerName = $data[0];
        $this->getServer()->dispatchCommand($player, "is transfer " . $playerName);
    });
    $form->setTitle("§cTransfer Island");
    $form->addInput("§7Enter the name of the player you want to transfer the island to:");
    $form->sendToPlayer($player);
    }
    public function disbandIslandUI(Player $player) {
    $form = new ModalForm(function(Player $player, ?bool $data) {
        if($data === null) {
            return;
        }
        if($data) {
            $this->getServer()->dispatchCommand($player, "is disband");
        }
    });
    $form->setTitle("§cDisband Island");
    $form->setContent("§7Are you sure you want to disband the island? This action cannot be undone.");
    $form->setButton1("§aYes");
    $form->setButton2("§cNo");
    $form->sendToPlayer($player);
    }
    public function createIsland(Player $player) {
        $form = new SimpleForm(function(Player $player, int $data = null) {
            if ($data === null) {
                return true;
            }
            switch ($data) {
                    case 0:
                        $this->getServer()->dispatchCommand($player, "is create");
                    break;
                }
            });
            $form->setTitle("§b§lSkyBlock§r§8 - §aCreate Island");
            $form->addButton("§a§lCreate an Island\n§r§7Start a new island", 0, "textures/items/nether_star");
            $form->addButton("§l§cEXIT", 0, "textures/blocks/barrier");
            $player->sendForm($form);
    }
}