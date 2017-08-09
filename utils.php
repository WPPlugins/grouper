<?php

/**
 * @package Grouper
 * @version 1.2.1
 */
defined('ABSPATH') or exit();

/**
 * 
 */
class slwsu_grouper_utils {

    /**
     * 
     */
    public static function str_to_id($str, $charset = 'utf-8') {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
        $str = preg_replace('# #', '-', $str);

        $str = strtolower($str);

        return $str;
    }

    /**
     * 
     */
    public static function check_version($type, $val) {
        if ('wp' === $type):
            $wp_status = slwsu_grouper_utils::check($GLOBALS['wp_version'], $val);
            return (true === $wp_status) ? true : false;
        elseif ('php' === $type):
            $php_status = slwsu_grouper_utils::check(phpversion(), $val);
            return (true === $php_status) ? true : false;
        endif;
    }

    /**
     * 
     */
    public static function check($curent, $min) {
        return version_compare($curent, $min) < 0 ? false : true;
    }

}
