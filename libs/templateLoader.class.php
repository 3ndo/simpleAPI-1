<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
DEFINE('SMARTY_DIR', __DIR__ . DIRECTORY_SEPARATOR . '../Smarty-2.6.25/libs/');
//DEFINE('SMARTY_DIR',str_replace("\\","/",getcwd()).'/includes/Smarty-2.6.25/libs/');
require_once(__DIR__ . DIRECTORY_SEPARATOR . '../Smarty-2.6.25/libs/Smarty.class.php');

class templateLoader {

    private $smarty = null;

    public function __construct() {
        $smarty = new Smarty();
        $smarty->template_dir = __DIR__ . DIRECTORY_SEPARATOR . '../templates';
        $smarty->compile_dir = __DIR__ . DIRECTORY_SEPARATOR . '../templates_c';
        $smarty->config_dir = __DIR__ . DIRECTORY_SEPARATOR . '../config';
        $smarty->cache_dir = __DIR__ . DIRECTORY_SEPARATOR . '../cache';
        $smarty->debuging = true;
        $this->smarty = $smarty;
    }

    public function loadTemplate() {
        return $this->smarty;
    }

}

?>
