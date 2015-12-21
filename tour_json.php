<?php

$trail_id = (!empty($_REQUEST['trailid'])) ? (int)$_REQUEST['trailid']:null;
$start = (int)$_REQUEST['start'];
if(empty($trail_id)) {
   // exit;
}
header('Content-Type: application/json; charset=utf-8');

//connect to wordpress DB
$link1 = mysql_connect("t360rdsmedium.co9l1p04yzvh.us-east-1.rds.amazonaws.com","t360wp","q93847o56fo3bc9q");
mysql_select_db("terrain360wp_dev", $link1);

//connect to /maintain DB
$link = mysql_connect("t360rdsmedium.co9l1p04yzvh.us-east-1.rds.amazonaws.com", "maintain", "WSD5BcJyCYP3sT9z");
mysql_select_db("master_terrain360_dev", $link);


//////  <!-- ////////////// GET DATA ///////////////// -->
//get trail
$q = mysql_query("select * from wp_posts where ID = '".$trail_id."' LIMIT 1", $link1);
$trail = mysql_fetch_object($q);

//get total rows
$q = mysql_query("select count(*) from scenes as sce join waypoints as way on sce.scene_name = way.scenename
    where sce.trail_id = '".$trail_id."' and way.trail_id = '".$trail_id."'", $link);
$r = mysql_fetch_array($q);
$totrecs = $r[0];

//get scene
$q = mysql_query("select * from scenes as sce join waypoints as way on sce.scene_name = way.scenename
    where sce.trail_id = '".$trail_id."' and way.trail_id = '".$trail_id."'
    and sce.scene_name = 'scene_".str_pad($start, 3, "0", STR_PAD_LEFT)."' order by scene_name
    LIMIT 1", $link);
$row = mysql_fetch_object($q);

//get prev. scene
$q2 = mysql_query("SELECT * FROM scenes AS s
    WHERE s.trail_id = '".$trail_id."' AND title < '".$row->title."'
    ORDER BY title DESC LIMIT 1");
$prev_scene = mysql_fetch_object($q2);

//get next. scene
$q3 = mysql_query("SELECT * FROM scenes AS s
    WHERE s.trail_id = '".$trail_id."' AND title > '".$row->title."'
    ORDER BY title ASC LIMIT 1");
$next_scene = mysql_fetch_object($q3);


//get settings
$Settingssql = mysql_query("select * from settings where trail_id = ".$trail_id." ", $link);
$settings = mysql_fetch_array($Settingssql);

//get settings for connected trails if such
if(!empty($settings['next_trail'])) {
    $sql = mysql_query("select * from wp_posts where post_type = 'trails' and post_title = '".mysql_real_escape_string($settings['next_trail'])."'", $link1);
    $next_trail = mysql_fetch_object($sql);
}

//get prev.trail
$Settingssql1 = mysql_query("select * from settings where next_trail = '".$trail->post_title."' LIMIT 1", $link);
$settings1 = mysql_fetch_array($Settingssql1);
if(!empty($settings1['trail_id'])) {
    $sql = mysql_query("select * from wp_posts where post_type = 'trails' and ID = '".mysql_real_escape_string($settings1['trail_id'])."'", $link1);
    $prev_trail = mysql_fetch_object($sql);
}



//close database connection
mysql_close($link);
mysql_close($link1);

//get speed and autorotate settings
$autorotate = (!empty($settings['autorotate']) && $settings['autorotate']) ? 'True' :  'False';
$speed = !empty($settings['speed']) ? $settings['speed'] : '1.0';

//broken compass workaround
if(empty($row->name3) && strpos($row->name2, 'pointer')) {
    $row->name3 = $row->name2;
    $row->name2 = str_replace('_pointer', '', $row->name2);
}

// $row = $this->scene
// START OUTPUT
$output_json = array();
$output_json['trail_name'] = $trail->post_title;
$output_json['prev_scene'] = $prev_scene->scene_name;
$output_json['curr_scene'] = $row->scene_name;
$output_json['next_scene'] = $next_scene->scene_name;
$output_json['elevation'] = $row->elevation;
$output_json['latitude'] = $row->latitude;
$output_json['longitude'] = $row->longitude;
$output_json['bearing'] = $row->bearing;
$output_json['hlookat'] = $row->view_hlookat;
$output_json['vlookat'] = $row->view_vlookat;
$output_json['mobile_url'] = $row->mobile;
$output_json['nexttrail_url'] = '';
$output_json['prevtrail_url'] = '';
var_dump($output_json);
if(isset($next_trail) && !empty($next_trail->guid)) {
    //workaround for using wp posts table with mixed domain prefixes - .terrain360, www.terrain360, wp.terrain360
    if($_SERVER['HTTP_HOST'] == 'wp.terrain360.com') {
        $next_trail->guid = str_replace('www.terrain360', 'wp.terrain360', $next_trail->guid);
    } else {
        $next_trail->guid = str_replace('wp.terrain360', 'www.terrain360', $next_trail->guid);
    }
    $output_json['nexttrail_url'] = $next_trail->guid;
}
if(isset($prev_trail) && !empty($prev_trail->guid)) {
    if($_SERVER['HTTP_HOST'] == 'wp.terrain360.com') {
        $prev_trail->guid = str_replace('www.terrain360', 'wp.terrain360', $prev_trail->guid);
    } else {
        $prev_trail->guid = str_replace('wp.terrain360', 'www.terrain360', $prev_trail->guid);
    }
    $output_json['prevtrail_url'] = $prev_trail->guid;
}

//echo "<pre>".print_r($output_json, true)."</pre>";exit;
echo str_replace('\/', '/', json_encode($output_json));