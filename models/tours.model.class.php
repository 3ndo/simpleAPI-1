<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class toursModel extends Models {

    private $params = null;
    private $trail = null;
    private $settings = null;
    private $prev_settings = null;
    private $scene = null;
    private $next_settings = null;
    private $next_trail = null;
    private $prev_trail = null;
    private $mapbox_style = null;
    private $weather = null;

    /*
     * params object of params
     */

    public function __construct($params = null) {
        parent::__construct();
        if (is_object($params) && !empty($params)) {
            $this->params = $params;
            $this->init($params);
        } else {
            logDebugMessages("Cannot init object " . get_class());
        }
    }

    public function setTrail() {
        $sql = "SELECT * FROM wp_posts WHERE ID = '{$this->params->trailid}' LIMIT 1";
        $records = $this->wp_db->run($sql);
        if (sizeof($records) > 0) {
            return (object) $records[0];
        } else {
            logDebugMessages($sql);
            return false;
        }
    }

    public function init($params = null) {
        $scene = new scenesModel($this->params);
        $mapbox = new mapboxModel($this->params);
        $this->trail = $this->setTrail();
        if (!$this->trail) {
            logDebugMessages("No trail found");
            return false;
        };
        $this->scene = $scene;
        $this->settings = $this->setSettings();
        $this->prev_settings = $this->setPreviousTrailSetting();
        $this->next_settings = $this->setSettingsNextTrails();
        $this->next_trail = $this->setNextTrials();
        $this->prev_trail = $this->setPreviousTrail();
        $this->mapbox_style = $mapbox->getMapboxStyle();
        $this->weather = $this->SetWeather();
    }

    public function setSettings() {
        $records = array();
        $sql = "SELECT * FROM settings WHERE trail_id = {$this->params->trailid}";
        $records = $this->db->run($sql);
        if (sizeof($records) > 0) {
            return (object) $records[0];
        } else {
            logDebugMessages($sql);
            return false;
        }
    }

    public function setSettingsNextTrails() {
        $records = array();
        $sql = "SELECT * FROM settings WHERE trail_id = {$this->params->trailid}";
        $records = $this->db->run($sql);
        if (sizeof($records) > 0) {
            return (object) $records[0];
        } else {
            logDebugMessages($sql);
            return false;
        }
    }

    /*
     * return connected trails
     * records of type of stdClass (object)
     * 
     */

    public function setNextTrials() {
        $records = array();
        if (!empty($this->settings->next_trail)) {
            $sql = "SELECT * FROM wp_posts where post_type = 'trails' and post_title = '{$this->settings->next_trail}'";
            $records = $this->wp_db->run($sql);
            if (sizeof($records) > 0) {
                return (object) $records[0];
            } else {
                logDebugMessages($sql);
                return false;
            }
        } else
            return false;
    }

    /*
     * Return setting for the next trail 
     * @param $trial = $trial->post_title
     */

    public function setPreviousTrailSetting() {
        $records = array();
        if (!$this->trail)
            return false;
        $sql = "SELECT * FROM settings where next_trail = '{$this->trail->post_title}' LIMIT 1";
        $records = $this->db->run($sql);
        if (sizeof($records) > 0) {
            return (object) $records[0];
        } else {
            logDebugMessages($sql);
            return false;
        }
    }

    public function getPreviousTrailSetting() {
        return $this->prev_settings;
    }

    /*
     * $param $settings object of type stdObject
     */

    public function setPreviousTrail() {
        $records = array();
        if (!empty($this->prev_settings->trail_id)) {
            $sql = "SELECT * FROM wp_posts WHERE post_type = 'trails' AND ID = ' {$this->prev_settings->trail_id}'";
            $records = $this->wp_db->run($sql);
            if (sizeof($records) > 0) {
                return (object) $records[0];
            } else {
                logDebugMessages($sql);
                return false;
            }
        }
    }

    public function getResult() {
        if (!$this->trail) {
            logDebugMessages("No trail found");
            return false;
        };
        $autorotate = (!empty($this->settings->autorotate) && $this->settings->autorotate) ? 'True' : 'False';
        $speed = !empty($this->settings->speed) ? $this->settings->speed : '1.0';
//$row = $this - scene 
//broken compass workaround
        if (empty($this->scene->getScene()->name3) && strpos($this->scene->getScene()->name2, 'pointer')) {
            $this->scene->getScene()->name3 = $this->scene->getScene()->name2;
            $this->scene->getScene()->name2 = str_replace('_pointer', '', $this->scene->getScene()->name2);
        }
// START OUTPUT
        $output_json = array();
        $current_scene = $this->scene->getScene();
        $output_json['ID'] = (!empty($this->trail->ID)) ? $this->trail->ID : null;
        $output_json['trail_name'] = (!empty($this->trail->post_title)) ? $this->trail->post_title : null;
        $output_json['trail_description'] = (!empty($this->trail->post_content)) ? $this->trail->post_content : null;
        $output_json['mapbox_style'] = (!empty($this->mapbox_style->mapbox_style_id)) ? $this->mapbox_style->mapbox_style_id : null;
        $output_json['prev_scene'] = (!empty($this->scene->getPrevScene()->scene_name)) ? $this->scene->getPrevScene()->scene_name : null;
        $output_json['curr_scene'] = $current_scene->scene_name;
        $output_json['next_scene'] = $this->scene->getNextScene()->scene_name;
        $output_json['elevation'] = $current_scene->elevation;
        $output_json['latitude'] = $current_scene->latitude;
        $output_json['longitude'] = $current_scene->longitude;
        $output_json['bearing'] = $current_scene->bearing;
        $output_json['hlookat'] = $current_scene->view_hlookat;
        $output_json['vlookat'] = $current_scene->view_vlookat;
        $output_json['mobile_url'] = $current_scene->mobile;
        $output_json['nexttrail_id'] = '';
        $output_json['prevtrail_id'] = '';
        if (is_object($this->next_trail) && !empty($this->next_trail->guid)) {
            //workaround for using wp posts table with mixed domain prefixes - .terrain360, www.terrain360, wp.terrain360
            if ($_SERVER['HTTP_HOST'] == 'wp.terrain360.com') {
                $this->next_trail->guid = str_replace('www.terrain360', 'wp.terrain360', $this->next_trail->guid);
            } else {

                $this->next_trail->guid = str_replace('wp.terrain360', 'www.terrain360', $this->next_trail->guid);
            }
            $next_url = parseUrl($this->next_trail->guid);
            $output_json['nexttrail_id'] = $next_url[1];
        }

        if (is_object($this->prev_trail) && !empty($this->prev_trail->guid)) {
            if ($_SERVER['HTTP_HOST'] == 'wp.terrain360.com') {
                $this->prev_trail->guid = str_replace('www.terrain360', 'wp.terrain360', $this->prev_trail->guid);
            } else {
                $this->prev_trail->guid = str_replace('wp.terrain360', 'www.terrain360', $this->prev_trail->guid);
            }
            $prev_url = parseUrl($this->prev_trail->guid);
            $output_json['prevtrail_id'] = $prev_url[1];
        }
        $output_json['zip'] = $this->weather->location->zip;
        $output_json['weather_data'] = $this->weather->weather_data;
        $output_json['t360shortURL'] = "http://t360.it/" . shortURL::encode($this->params->trailid) . $current_scene->title;
        $parks = new parksModel($this->params);
        if (!empty($parks))
            $output_json['parks'] = $parks->getAssignedParks();
        $rivers = new riversModel($this->params);
        if (!empty($rivers))
            $output_json['rivers'] = $rivers->getAssignedRivers();
        if (!empty($this->settings->series)) {
            $at_array = (object) (explode(', ', $this->settings->series));
            $output_json['series'] = $at_array;
        }
      
        return $output_json;
    }

    public function SetWeather() {
        $current_scene = $this->scene->getScene();
        $url = "http://api.wunderground.com/api/c26eb8ef02f7da65/geolookup/conditions/q/" . $current_scene->latitude . "," . $current_scene->longitude . ".json";
        $data = json_decode(file_get_contents($url));
        $result = new stdClass();
        $result->location = new stdClass();
        $result->weather_data = new stdClass();
        $result->location->zip = $data->location->zip;
        $result->weather_data->temp_f = $data->current_observation->temp_f;
        $result->weather_data->wind_mph = $data->current_observation->wind_mph;
        $result->weather_data->weather = $data->current_observation->weather;
        return $result;
    }

}
