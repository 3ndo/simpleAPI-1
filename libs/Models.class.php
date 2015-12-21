<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Models{

    protected $wp_db = null;
    protected $db = null;
    protected $table_name = null;

    public function __construct($wp_db = null, $db = null) {
        if (!empty($wp_db))
            $this->wp_db = $wp_db;
        else {
            $this->wp_db = $wp_db = new db(WP_DNS, WP_DB_USER, WP_DB_PASS);
           // $this->db = $db = new db(DNS, WP_DB_USER, WP_DB_PASS);
            $data = $this->wp_db->query("SET NAMES UTF8");
        }
        if (!empty($db))
            $this->db = db;
        else {
            $this->db = new db(DNS, MT_DB_USER, MT_DB_PASS);
           // $this->db = $db = new db(DNS, WP_DB_USER, WP_DB_PASS);
            $data = $this->wp_db->query("SET NAMES UTF8");
        }
    }

    public function setTableName($table_name = null) {
        if (!empty($table_name)) {
            $this->table_name = $table_name;
        }
    }

    // should move in helper.function on some there bacause we have the same function in controles.class.php
    public function setParams($params = array()) {
        if (is_array($params) && sizeof($params)) {
            $this->params = $params;
            foreach ($params as $key => $val) {
                if (!empty($val)) {
                    switch ($key) {

                        default :
                            if (!empty($key))
                                $this->$key = $val;
                            break;
                    }
                }
            }
        }
    }

    private function renderData($data = null) {
        
    }

}

?>