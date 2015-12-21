<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class riverModel extends Models {

    private $params = null;
    private $river = null;
    private $allTrailAssigned = null;
    private $subSections = null;

    public function __construct($params) {
        parent::__construct();
        if (!empty($params)) {
            $this->params = $params;
            $this->river = $this->setRiver();
            $this->allTrailAssigned = $this->setAllTrailsAssigned();
            $this->subSections = $this->setSubSections();
        }
    }

    public function setRiver() {
        $records = array();
        $sql = "SELECT * FROM wp_posts WHERE wp_posts.ID = {$this->params->riverid}";
        $records = $this->wp_db->run($sql);
        if (sizeof($records) > 0) {
            return (object) $records[0];
        } else {
            logDebugMessages($sql);
            return false;
        }
    }

    public function setAllTrailsAssigned() {
        $result = array();
        $sql = "SELECT meta.post_id as trail_id, posts.post_title as name FROM wp_postmeta as meta 
            INNER JOIN wp_posts as posts ON posts.ID = meta.post_id
            WHERE meta.meta_key = 'assigned_river' 
            AND meta.meta_value like '%{$this->params->riverid}%' and posts.post_status = 'publish'";
        $records = $this->wp_db->run($sql);
        if (sizeof($records) > 0) {
            foreach ($records as $key => $val) {
                $data[$val["trail_id"]] = $val["name"];
            }
            return $data;
        } else {
            logDebugMessages($sql);
            return false;
        }
    }

    public function setSubSections() {
        $trails_ids = implode(",", array_keys($this->allTrailAssigned));
        $data = array();
        if (!empty($trails_ids)) {
            $sql = "SELECT trail_id, series from settings WHERE trail_id in ({$trails_ids})";
            $records = $this->db->run($sql);
            if (sizeof($records) > 0) {
                foreach ($records as $key => $val) {
                    if (!empty($val["series"])) {
                        $series_arr = explode(",",$val["series"]);
                    
                        foreach ($series_arr as $k => $seria)
                            if (!in_array(trim($seria), $data) && (!empty($seria)))
                                $data[] = trim($seria);
                    }
                }
                return $data;
            } else {
                logDebugMessages($sql);
                return false;
            }
        } else
            return false;
    }

    public function getResult() {
        $result['river_id'] = $this->river->ID;
        $result['river_name'] = $this->river->post_title;
        $result['all_trails_assigned'] = (object) $this->allTrailAssigned;
        $result['sub_sections'] = (object) $this->subSections;

        return $result;
    }

}
