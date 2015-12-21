<?php

class mapboxModel extends Models {

    private $params = null;

    public function __construct($params = null) {
        parent::__construct();
        if (is_object($params) && !empty($params)) {
            $this->params = $params;
        } else {
            logDebugMessages("Cannot init object " . get_class());
        }
    }

    public function getTrailMapboxStyle() {
        if (!empty($this->params->trailid)) {
            $sql = "SELECT * FROM mapbox_styles_assignments WHERE type = 'trail' AND identifier = {$this->params->trailid}";
            $records = $this->db->run($sql);
            if (sizeof($records) > 0) {
                return (object) $records[0];
            } else {
                logDebugMessages($sql);
                return false;
            }
        } else
            return false;
    }

    public function getMapboxDefaultStyle() {
        $sql = "SELECT * FROM mapbox_styles_assignments WHERE type = 'def_trails'";
        $records = $this->db->run($sql);
        if (sizeof($records) > 0) {
            return (object) $records[0];
        } else {
            logDebugMessages($sql);
            return false;
        }
    }

    public function getMapboxStyle() {
        if (!empty($this->params->trailid)) {
            $mapStyle = $this->getTrailMapboxStyle();
        }
        if (empty($mapStyle))
            $mapStyle = $this->getMapboxDefaultStyle();
        
        return $mapStyle;
    }

}
