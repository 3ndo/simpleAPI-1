<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class parksModel extends Models {

    private $params = null;

    public function __construct($params) {
        parent::__construct();
        if (!empty($params))
            $this->params = $params;
    }

    public function getAssignedParks() {
        $records = null;
        $result = array();
        $parks = null;
        $sql = "SELECT meta_value FROM wp_posts LEFT JOIN wp_postmeta ON wp_posts.id = wp_postmeta.post_id WHERE wp_posts.ID = {$this->params->trailid} and wp_postmeta.meta_key =  'assigned_park'";
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
            $parks = $this->getParkDetatls($result);
            return (object) $parks;
        } else {
            logDebugMessages($sql);
            return false;
        }
    }

    public function getParkDetatls($parks_array = null) {
        $records = array();
        $parks_ids = implode(",", $parks_array);
        if (!empty($parks_ids)) {
           // $sql = "SELECT * FROM park_settings WHERE park_id in (" . $parks_ids . ")";
            $sql = "SELECT id as ID, post_title as name  FROM wp_posts WHERE id in (" . $parks_ids . ")";
            $records = $this->wp_db->run($sql);

            if (sizeof($records) > 0) {
                return $records[0];
            } else {
                logDebugMessages($sql);
                return false;
            }
        }
    }

}
