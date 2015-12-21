<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 */
ini_set('error_reporting', E_ALL ^ E_DEPRECATED ^E_NOTICE);
ini_set('display_errors', 'On');
ini_set('default_charset', 'UTF-8');
mb_regex_encoding('UTF-8');
DEFINE("BASE_PATH", (dirname(__FILE__)));
DEFINE("DEV_MOD", true);
//header("Content-Type: text/html; charset=utf-8");
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/connect.php';
require_once BASE_PATH . '/helpers/helper.functions.php';


// set routing protokol - auto- normal query string, string => seo friendly url-s

function __autoload($class_name) {
//class directories
    $directories = array(
        BASE_PATH . '/libs/',
        BASE_PATH . '/includes/',
        BASE_PATH . '/models/',
        BASE_PATH . '/controlers/',
        BASE_PATH . '/objects/',
        BASE_PATH . '/classes/'
    );
   
//for each directory
    foreach ($directories as $directory) {
//see if the file exsists
        $file_name = $class_name;
        if (strrpos(strtolower($file_name), "model")>0) {
            $file_name = str_replace("Model", ".model", $file_name);
        }
       
        if (file_exists($directory . $file_name . '.class.php')) {
            try {
                require_once($directory . $file_name . '.class.php' );
//only require the class once, so quit after to save effort (if you got more, then name them something else
                return;
            } catch (Exception $e) {
                echo $e->getMessage(), "\n";
                // should log errors
            }
        }
    }
}

?>
