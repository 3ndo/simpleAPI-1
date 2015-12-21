<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class riversModel extends Models {

    private $params = null;
    private $river = null;
    
    public function __construct($params) {
        parent::__construct();
        if (!empty($params))
        {
            $this->params = $params;
        }
    }

    public function getAssignedRivers() {
        $records = null;
        $result = array();
        $parks = null;
        $sql = "SELECT meta_value FROM wp_posts LEFT JOIN wp_postmeta ON wp_posts.id = wp_postmeta.post_id WHERE wp_posts.ID = {$this->params->trailid} and wp_postmeta.meta_key =  'assigned_river'";
        $records = $this->wp_db->run($sql);
        if (sizeof($records) > 0) {
            foreach ($records[0] as $key => $val) {
                $tmp = unserialize($val);
                   if (!empty($tmp)) {
                    foreach ($tmp as $k => $v) {
                        array_push($result, $v);
                    }
                }
            }
            $rivers = $this->getRiversDetatls($result);
            return (object) $rivers;
        } else {
            logDebugMessages($sql);
            return false;
        }
    }

    public function getRiversDetatls($rivers_array = null) {
        // how to get details
        $records = array();
        $rivers_ids = implode(",", $rivers_array);
        if (!empty($rivers_ids)) {
           // $sql = "SELECT * FROM park_settings WHERE park_id in (" . $parks_ids . ")";
            $sql = "SELECT id as ID, post_title as name  FROM wp_posts WHERE id in (" . $rivers_ids . ")";
            $records = $this->wp_db->run($sql);

            if (sizeof($records) > 0) {
                return $records;
            } else {
                logDebugMessages($sql);
                return false;
            }
        }
    }
    
    
}
