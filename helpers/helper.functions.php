<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * create drop down select usinng array $data = {start_value=>n, end_value=>n, step_value=>n,  }
 */

function create_drop_down_selelect($data) {
    $tepml = new templateLoader();
    $html = $templ->loadCommonTemplates('drop_down_element.php', $data);
    return $html;
}


function mysql_catchquery($query, $emsg = 'Error submitting the query') {
    $result = mysql_query($query);

    if ($result !== false) {
        return $result;
    } else
        throw new Exception(mysql_error());
}

function exec_sql($sql) {
    try {
        $res = mysql_catchquery($sql);

        return $res;
    } catch (Exception $e) {
        logDebugMessages($e->getMessage());
        return false;
    }
}

function logDebugMessages($message, $show = false) {
    log_errors($message);
    if (!$show)
        ob_start();
    print '<br>Information log: <hr><pre>';
    debug_print_backtrace();
    print '</pre>';
    if (!$show)
        log_errors(ob_get_clean());
}

function log_errors($str) {
    $date = date("Y_m_d");
    $path = dirname(dirname((__FILE__)));

    $myFile = $path . '/logs/sql_errors_' . $date . ".txt";
    if (!is_dir($path . '/logs/'))
        mkdir($path . '/logs/', 0777);
    if (is_dir($path . '/logs/')) {
        if (!file_exists($myFile)) {
            fopen($myFile, 'c+');
            chmod($myFile, 0777);
        }
        $fh = fopen($myFile, 'a+');
        fwrite($fh, date("Y-m-d H:i:s") . ' / ' . $str . "\n");
        fclose($fh);
    }
}

function renderPostData($post) {
    $data = array();
    if (is_array($post) && sizeof($post > 0)) {
        foreach ($post as $key => $val) {
            $data[$key] = addslashes($val);
        }
    }
    return $data;
}


function test_query_execution_time($sql, $debug = false, $output = false) {

    $start = microtime(true);
    $q = exec_sql($sql);
    $time = microtime(true) - $start;

    if ($debug) {
        $debug = "$sql<br/>$time<br/><br/>";
        if ($output) {
            print $debug;
        } else {
            log_query($debug);
        }
    }
    return $q;
}

function getTime() {
    $time = microtime();
    $timearray = explode(" ", $time);
    $time = $timearray[1] + $timearray[0];
    return $time;
}

function PageLoadInfo($page) {
    $time_to_load = 0;
    $end_time = getTime();

    $time_to_load = $end_time - $_SESSION{'start_time'};
    $totaltime = round($time_to_load, 5);

    echo 'time to load: ' . $totaltime;
}

function ageCalcualtion($date = null) {
    $diff = null;
    $age = null;
    $current = time();
    if (!empty($date)) {
        $diff = $current - strtotime($date);
        $age = floor($diff / 31556926);
    }
    return $age;
}

function timeConversion($time) {
    if (!empty($time)) {
        $time = str_replace(":", ".", $time) * 100;
        $temp_time = date("H:i", strtotime($time));
    }
    return $temp_time;
}

function timeConversion12to24($time) {
    if (!empty($time)) {
        $temp_time = date("H:i", strtotime($time));
    }
    return $temp_time;
}

if (!function_exists('remove_invisible_characters')) {

    function remove_invisible_characters($str, $url_encoded = TRUE) {
        $non_displayables = array();

        // every control character except newline (dec 10)
        // carriage return (dec 13), and horizontal tab (dec 09)

        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/'; // url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }

}
function dump_val($val = null) {
    if (!empty($val)) {
        echo '<pre>';
        var_dump($val);
        echo '</pre>';
    }
}

function parseUrl($url = '')
{
    $result = preg_split('(p=)', $url ,PREG_SPLIT_DELIM_CAPTURE ); 
    return $result;
}
 
// ------------------------------------------------------------------------
?>