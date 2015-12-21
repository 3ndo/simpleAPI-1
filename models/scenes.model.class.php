<?php
class scenesModel extends Models{
    
    private $scene = null;
    private $prev_scene = null;
    private $next_scene = null;
    private $params = null;
    
    public function __construct($params) {
        parent::__construct();
        $this->init($params);
    }
    
    public function init($params = null)
    {
        if (is_object($params) && !empty($params)) {
            $this->params = $params;
            $this->scene = $this->setScene();
            $this->prev_scene = $this->setPreviousScene();
            $this->next_scene = $this->setNextScene();
        }
    }
    public function setScene() {
        
        $records = array();
        $sql = "SELECT * FROM scenes AS sce INNER JOIN waypoints AS way ON sce.scene_name = way.scenename
                WHERE sce.trail_id = '" . $this->params->trailid . "' and way.trail_id = '" . $this->params->trailid . "'";
        if (!empty($this->params->start))
            $sql .= " AND sce.scene_name = 'scene_" . $this->params->start . "'";
            $sql .=" ORDER BY scene_name LIMIT 1";
        $records = $this->db->run($sql);
        if (sizeof($records) > 0) {
            return (object) $records[0];
        } else {
            logDebugMessages($sql);
            return false;
        }
    }
    public function getScene() {
        return $this->scene;
    }
    

    public function setPreviousScene() {

        $records = array();
        if(!$this->scene || (empty($this->params->trailid))) return false;
        
        $sql = "SELECT * FROM scenes AS s
                WHERE s.trail_id = '{$this->params->trailid}' AND title < '{$this->scene->title}'
                ORDER BY title DESC 
                LIMIT 1";
        $records = $this->db->run($sql);
        if (sizeof($records) > 0) {
            return (object) $records[0];
        }  else {
            logDebugMessages($sql);
            return false;
        } 
    }
    
    public function getPrevScene() {
        return $this->prev_scene;
    }
    
    public function setNextScene() {

        $records = array();
        if(!$this->scene || (empty($this->params->trailid))) return false;
        $sql = "SELECT * FROM scenes AS s
                WHERE s.trail_id = '{$this->params->trailid}' AND title > '{$this->scene->title}'
                ORDER BY title ASC
                LIMIT 1";
        $records = $this->db->run($sql);
        if (sizeof($records) > 0) {
            return (object) $records[0];
        } else {
            logDebugMessages($sql);
            return false;
        }
    }
   

    function getNextScene() {
        return $this->next_scene;
    }


}

