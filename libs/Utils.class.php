<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utils
 *
 * @author Kameliya
 */
class Utils {

    public static $seed = 0;

    public static function dump() {
        $seed = self::$seed++;
        if (func_num_args() == 0)
            return;
        $dbt = debug_backtrace();
        $dbt = $dbt[0];
        $root = realpath('..');
        $dbt['file'] = str_replace($root, '', $dbt['file']);
        $file = $dbt['file'];
        $line = $dbt['line'];
        echo "<div style='color: #fff; font-size: 10px; padding: 2px; font-weight: bold; border: 1px #900 solid; background-color: #900;'><div style='float:right; cursor: pointer' onclick='el = document.getElementById(\"dbg_main_$seed\"); if( !el ) return; if( el.style.display == \"block\" ) el.style.display = \"none\"; else el.style.display = \"block\"'>$line</div>$file</div><pre id='dbg_main_$seed' style='padding: 10px; color:red; background: #fff; border: 1px #999 solid; margin-top: 0px; display: block' class='nms-dump'>";
        for ($i = 0; $i < func_num_args(); $i++) {
            echo "<div style='padding-top: 10px; color: #666; border-bottom: 1px #ddd dashed;'><div style='float: right; padding-right: 10px;'><a style='text-decoration: none; font-weight: bold; color: #900;' href='#' onclick='el=document.getElementById(\"dbg_content_$seed\_$i\"); if( !el ) return; if( el.style.display == \"block\" ) el.style.display = \"none\"; else el.style.display = \"block\"'>[X]</a></div>Var: $i (" . gettype(func_get_arg($i)) . ")</div>";
            echo '<div style="display: block;" id="dbg_content_' . $seed . '_' . $i . '">';
            print_r(func_get_arg($i));
            echo '</div>';
        }
        echo "</pre>";
    }

}

?>
