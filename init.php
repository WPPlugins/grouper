<?php
/**
 * @package Grouper
 * @version 1.2.1
 */
defined('ABSPATH') or exit();

/**
 * 
 */
require_once plugin_dir_path(__file__) . 'utils.php';

/**
 * 
 */
class slwsu_grouper_init {

    public $wp_v;
    public $php_v;
    public $wp_status;
    public $php_status;
    public $grp_nom;
    public $grp_id;

    /**
     * 
     */
    public function __construct($grp_nom, $wp_v = "", $php_v = "") {
        add_action('admin_head', array($this, 'add_admin_css'));

        $this->grp_nom = $grp_nom;
        $this->grp_id = slwsu_grouper_utils::str_to_id($grp_nom);

        $this->wp_v = $wp_v;
        $this->php_v = $php_v;

        if ("" !== $this->wp_v && "" !== $this->php_v):
            $this->wp_status = slwsu_grouper_utils::check_version('wp', $wp_v);
            $this->php_status = slwsu_grouper_utils::check_version('php', $php_v);
            if (false === $this->wp_status or false === $this->php_status):
                add_action('admin_notices', array($this, 'add_admin_message_erreur_version'));
            endif;
        endif;
    }

    /**
     * 
     */
    public function add_admin_menu() {
        if (empty($GLOBALS['admin_page_hooks'][$this->grp_id])):
            add_menu_page($this->grp_nom, $this->grp_nom, 'manage_options', $this->grp_id, array($this, 'add_admin_page'), 'dashicons-admin-plugins', 65);
        endif;
    }

    /**
     * 
     */
    public function add_admin_page() {
        ?>
        <div class="wrap">
            <a class="grouper-modal-link" style="text-decoration:none; font-weight:bold;" href="#openModal"><?php echo __('About', 'grpp'); ?> <span class="dashicons dashicons-info"></span></a>
            <h1>Grouper</h1>
            <?php
            if (isset($_GET['settings-updated'])) {
                delete_transient('grp_proto_plugin_options');
                ?>
                <div id="message" class="updated">
                    <p><strong><?php echo __('Settings saved', 'grp') ?></strong></p>
                </div>
                <?php
            }
            ?>
            <div id="openModal" class="grouper-modal">
                <div>
                    <a href="#grouper-modal-close" title="Close" class="grouper-modal-close"><span class="dashicons dashicons-dismiss"></span></a>
                    <h2><?php echo __('About', 'grp'); ?></h2>
                    <p><span class="dashicons dashicons-admin-users"></span> Steeve Lefebvre - slWsu</p>
                    <p><span class="dashicons dashicons-admin-site"></span> <?php echo __('More information', 'grp'); ?> : <a href="<?php echo 'https://web-startup.fr/grouper/'; ?>" target="_blank"><?php _e('plugin page', 'grp'); ?></a></p>
                    <p><span class="dashicons dashicons-admin-tools"></span> <?php echo __('Development for the web', 'grp'); ?> : HTML, PHP, JS, WordPress</p>
                    <h2><?php echo __('Support', 'grp'); ?></h2>
                    <p><span class="dashicons dashicons-email-alt"></span> <?php echo __('Ask your question', 'grp'); ?></p>
                    <?php
                    if ($_POST['submit']) {
                        $to = 'steeve.lfbvr@gmail.com';
                        $subject = "Support Grouper !!!";

                        global $current_user;
                        $roles = implode(", ", $current_user->roles);

                        $message = "From: " . get_bloginfo('name') . " - " . get_bloginfo('home') . " - " . get_bloginfo('admin_email') . "\n";
                        $message .= "By : " . strip_tags($_POST['nom']) . " - " . $_POST['email'] . " - " . $roles . "\n";
                        $message .= strip_tags($_POST['message']) . "\n";

                        if (wp_mail($to, $subject, $message)):
                            echo '<p class="grouper-contact-valide"><strong>' . __('Your message has been sent !', 'grp') . '</strong></p>';
                        else:
                            echo '<p class="grouper-contact-error">' . __('Something went wrong, go back and try again !', 'grp') . '</p>';
                        endif;
                    }
                    ?>
                    <form id="grouper-contact" action="" method="post">
                        <fieldset>
                            <input id="nom" name="nom" type="text" placeholder="<?php echo __('Your name', 'grp'); ?>" required="required">
                        </fieldset>
                        <fieldset>
                            <input id="email" name="email" type="email" placeholder="<?php echo __('Your Email Address', 'grp'); ?>" required="required">
                        </fieldset>
                        <fieldset>
                            <textarea id="message" name="message" placeholder="<?php echo __('Request for assistance, translation or new functionality, here.', 'grp'); ?>" required="required"></textarea>
                        </fieldset>
                        <fieldset>
                            <input id="submit" name="submit" type="submit" value="<?php echo __('Send', 'grp'); ?>" class="button button-primary" type="submit" id="grouper-contact-submit" data-submit="<?php echo __('...sending', 'grp'); ?>" />
                        </fieldset>
                    </form>
                </div>
            </div>
            <h2><?php echo __('Extensions page compatible with Grouper.', 'grp') ?></h2>
            <?php $this->remote_plugins(); ?>
            <p>
                <?php echo '<a href="https://web-startup.fr/grouper/" target="_blank">' . __('More information here.', 'grp') . '</a>'; ?>
            </p>
            <script>
                var bouton = document.getElementsByClassName("accordion");
                for (var i = 0; i < bouton.length; i++) {
                    bouton[i].onclick = function () {

                        for (j = 0; j < bouton.length; j++) {
                            var all = bouton[j].nextElementSibling;
                            all.style.display = "none";
                            bouton[j].classList.remove("active");
                        }

                        this.classList.toggle("active");

                        var panel = this.nextElementSibling;
                        if (panel.style.display === "block") {
                            panel.style.display = "none";
                        } else {
                            panel.style.display = "block";
                        }
                    };
                }
            </script>
        </div>
        <?php
    }

    /**
     * 
     */
    private function remote_plugins() {
        $local_langue = get_locale();
        $grouper_languages = array('fr_FR', 'en_EN');

        if (in_array($local_langue, $grouper_languages)):
            $grouper_langue = $local_langue;
        else:
            $grouper_langue = 'en_EN';
        endif;

        $request = wp_remote_get('https://www.web-startup.fr/services/grouper/plugins-' . $grouper_langue . '.json');
        if (is_wp_error($request)) {
            return false;
        }

        $body = wp_remote_retrieve_body($request);
        $datas = json_decode($body);

        if (!empty($datas)) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            foreach ($datas as $data):
                $slug = slwsu_grouper_utils::str_to_id($data->nom);
                $infos = wp_remote_get("http://api.wordpress.org/plugins/info/1.0/$slug");
                $plugin = unserialize($infos['body']);

                if (file_exists(WP_PLUGIN_DIR . '/' . $slug . '/')):
                    $style = ' style="color:orange" ';
                    $message = __('Installed, inactive', 'grp');
                    $action = '- <a href="plugins.php">'. __('Activate', 'grp') .'</a>';
                    if (is_plugin_active($slug . '/' . $slug . '.php')):
                        $style = ' style="color:#46b450" ';
                        $message = __('Activated', 'grp');
                        $action = '';
                    endif;
                else:
                    $style = ' style="color:#dc3232" ';
                    $message = __('Not installed', 'grp');
                    $action = '- <a href="https://web-startup.fr/grouper/">' . __('More information', 'grp') . '</a>.';
                    endif;
                ?>
                <button class="accordion">
                    <span class="dashicons dashicons-admin-plugins"></span>
                    <strong><?php echo $plugin->name; ?></strong>
                    <div class="alignright">
                        <span class="dashicons dashicons-shield"></span> 
                        <span<?php echo $style; ?>><strong><?php echo $plugin->version; ?></strong></span>
                    </div>
                </button>
                <div class="panel">
                    <p>
                        <?php echo $data->description . '<br />'; ?>
                        <?php echo '<span' . $style . '>' . $message . ' ' . $action . '</span>'; ?>
                    </p>
                </div>
                <div style="height:8px;">&nbsp;</div>
                <?php
            endforeach;
        }
    }

    /**
     * 
     */
    public function add_admin_css() {
        echo '<style>
            button.accordion {
                background-color: #ddd;
                color: #444;
                cursor: pointer;
                padding: 12px;
                width: 100%;
                text-align: left;
                border: none;
                outline: none;
                transition: 0.4s;
                margin-bottom: 0;
            }

            button.accordion.active, button.accordion:hover {
                color: #efefef;
                background-color: #444444;
            }

            div.panel {
                display: none;
                padding: 12px;
                margin: 0 !important;
                background-color: white;
            }    
            div.panel p {
                margin: 0;
                margin-bottom: 6px;
            }





            #toplevel_page_' . $this->grp_id . ' .wp-submenu .wp-first-item {
                display: none;
            }

            .grouper-modal-link{
                position: relative;
                float: right;
            }
            .grouper-modal {
                position: fixed;
                font-family: Arial, Helvetica, sans-serif;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background: rgba(0,0,0,0.8);
                z-index: 99999;
                opacity:0;
                -webkit-transition: opacity 250ms ease-in;
                -moz-transition: opacity 250ms ease-in;
                transition: opacity 250ms ease-in;
                pointer-events: none;
            }

            .grouper-modal:target {
                opacity:1;
                pointer-events: auto;
            }

            .grouper-modal > div {
                width: 400px;
                background: #fff;
                margin: 7% auto;
                position: relative;
                border-radius: 10px;
                padding: 5px 20px 13px 20px;
                background: -o-linear-gradient(bottom, rgb(245,245,245) 25%, rgb(232,232,232) 63%);
                background: -moz-linear-gradient(bottom, rgb(245,245,245) 25%, rgb(232,232,232) 63%);
                background: -webkit-linear-gradient(bottom, rgb(245,245,245) 25%, rgb(232,232,232) 63%);
            }

            .grouper-modal-close {
                top: 10px;
                right: 10px;
                font-weight: bold;
                position: absolute;
                text-align: center;
                text-decoration: none;
            }

            .grouper-modal-close:hover { color: #333; }
            

            #grouper-contact input[type="text"],
            #grouper-contact input[type="email"],
            #grouper-contact input[type="url"],
            #grouper-contact textarea,
            #grouper-contact button[type="submit"] {
                font:400 12px/16px "Open Sans", Helvetica, Arial, sans-serif;
            }

            fieldset {
                border: medium none !important;
                margin: 0 0 6px;
                min-width: 100%;
                padding: 0;
                width: 100%;
            }

            #grouper-contact input[type="text"],
            #grouper-contact input[type="email"],
            #grouper-contact input[type="tel"],
            #grouper-contact input[type="url"],
            #grouper-contact textarea {
                width:100%;
                border:1px solid #CCC;
                background:#FFF;
                margin:0 0 5px;
                padding:10px;
            }

            #grouper-contact input[type="text"]:hover,
            #grouper-contact input[type="email"]:hover,
            #grouper-contact input[type="tel"]:hover,
            #grouper-contact input[type="url"]:hover,
            #grouper-contact textarea:hover {
                -webkit-transition:border-color 0.3s ease-in-out;
                -moz-transition:border-color 0.3s ease-in-out;
                transition:border-color 0.3s ease-in-out;
                border:1px solid #AAA;
            }

            #grouper-contact textarea {
                height:100px;
                max-width:100%;
                resize:none;
                margin-bottom: 0px;
            }

            #grouper-contact input:focus,
            #grouper-contact textarea:focus {
                outline:0;
                border:1px solid #999;
            }

            ::-webkit-input-placeholder { color:#888; }
            :-moz-placeholder { color:#888; }
            ::-moz-placeholder { color:#888; }
            :-ms-input-placeholder { color:#888; }


            .grouper-contact-valide, .grouper-contact-error{
                padding: 8px;
                background-color: white;
            }
            .grouper-contact-valide{
                border-left: 4px solid #46b450;
            }
            .grouper-contact-error{
                border-left: 4px solid #dc3232;
            }

        </style>';
    }

    /**
     * 
     */
    public function add_admin_message_erreur_version() {
        unset($_GET['activate']);
        ?>
        <div id="message" class="error">
            <p>
                <?php
                if (false === $this->wp_status):
                    printf(__('This Plugin requires at least version of WORDPRESS', 'grp') . ' ' . $this->wp_v . '+, ' . __('You use version', 'grp') . ' %s.', $GLOBALS['wp_version']);
                endif;
                if (false === $this->php_status):
                    printf(__('This Plugin requires at least version of PHP', 'grp') . ' ' . $this->php_v . '+, ' . __('You use version', 'grp') . ' %s.', phpversion());
                endif;
                ?>
            </p>
        </div>
        <?php
    }

}
