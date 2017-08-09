<?php

/*
  Plugin Name: Grouper
  Plugin URI: http://web-startup.fr/
  Description: Grouper allows to gather the plugins developed by @webstartup in a single link of the wordpress admin menu.
  Version: 1.2.1
  Author: Steeve Lefebvre
  Author URI: web-startup.fr
  License: GPLv2 or later
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
  Text Domain: grp
  Contributors: webstartup, Benoti, citoyensdebout

  ------------------------------------------------------------------------------
  Note pour les anglophones : quand un code commenté en anglais me plait
  et qu'aucune traduction n'est disponible, je dois me démerder.
  Merci de bien vouloir me rendre la pareille :-þ
 */

defined('ABSPATH') or exit();

__('Grouper', 'grp');
__('Grouper allows to gather the plugins developed by @webstartup in a single link of the wordpress admin menu.', 'grp');

class slwsu_grouper {

    public function __construct() {
        // Hook
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        add_action('plugins_loaded', array($this, 'text_domain'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'setting_links'));
    }

    /**
     * Languages
     */
    public static function text_domain() {
        load_plugin_textdomain('grp', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Liens
     */
    public function setting_links($aLinks) {
        $links[] = '<a href="https://web-startup.fr/grouper/">' . __('Page', 'grp') . '</a>';
        return array_merge($links, $aLinks);
    }

    /**
     * Activation
     */
    public static function activate() {
        add_option('slwsu_is_active_grouper', 'true');
    }

    /**
     * Deactivationn
     */
    public static function deactivate() {
        delete_option('slwsu_is_active_grouper', 'true');
    }

}

new slwsu_grouper;
