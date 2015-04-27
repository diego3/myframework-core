<?php

class Painel extends ProcessRequest {
    public function __construct() {
        $this->filename = 'painel';
        $this->pageTitle = 'Painel administrativo';
        if ($_GET['_action'] == 'install') {
            define('TEST_OR_INSTALL', true);
        }
    }
    
    public function isInstalled() {
        return file_exists('default/install/installed.php');
    }
    
    public function _main() {
        return true;
    } 
    
    public function _install() {
        if ($this->isInstalled()) {
            //TODO show error
            return false;
        }
        echo "<h2>CREATE default TABLES </h2>";
        $this->createDatabaseItems('default/install/');
        echo "<h2>EXECUTE SCRIPTS</h2>";
        $this->executeScripts('default/install/');
        
        echo "<h2>CREATE app TABLES </h2>";
        $this->createDatabaseItems('app/install/');
        echo "<h2>EXECUTE SCRIPTS</h2>";
        $this->executeScripts('app/install/');
    }
    
    protected function createDatabaseItems($folder) {
        $db = Factory::database();
        foreach (glob($folder . '*.sql') as $sqlfile) {
            echo "<li>{$sqlfile}</li>";
            $items = explode(';', file_get_contents($sqlfile));
            foreach ($items as $object) {
                //echo $object . '<br>';
                if (!$db->execute($object)) {
                    //echo "<pre>" . $object . "</pre><hr>";
                }
            }
        }
    }
    
    protected function executeScripts($folder) {
        foreach (glob($folder . '*.php') as $phpfile) {
            echo "<li>{$phpfile}</li>";
            require_once($phpfile);
        }
    }
}

