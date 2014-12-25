<?php

/*
  Plugin Name: Codeboxr Fixed Vertical Feedback button
  Plugin URI: http://codeboxr.com/product/fixed-vertical-feedback-button-for-wordpress
  Description: Vertical fixed feedback button for wordpress
  Author: Codeboxr
  Version: 3.2
  Author URI: http://codeboxr.com
 */
/*
  Copyright 2010-2014  Codeboxr (email : sabuj@codeboxr.com)
  Last Update: 26.12.2014

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
?>
<?php

// avoid direct calls to this file where wp core files not present
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
/*
 * Use WordPress 2.6 Constants
 */
if (!defined('WP_CONTENT_DIR')) {
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
}
if (!defined('WP_CONTENT_URL')) {
    define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
}
if (!defined('WP_PLUGIN_DIR')) {
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
}
if (!defined('WP_PLUGIN_URL')) {
    define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
}
$cbxplugin_active = is_plugin_active('wpfixedverticalfeedbackbuttonaddon/wpfixedverticalfeedbackbuttonaddon.php');

$cbxplugin_file =  file_exists(WP_PLUGIN_DIR . '/wpfixedverticalfeedbackbuttonaddon/wpfixedverticalfeedbackbuttonaddon.php');


if($cbxplugin_active && $cbxplugin_file ){
    require (WP_PLUGIN_DIR . '/wpfixedverticalfeedbackbuttonaddon/wpfixedverticalfeedbackbuttonaddon.php');
}
$cbxplugin_class = class_exists('wpfixedverticalfeedbackbuttonaddon');
//var_dump($cbxplugin_class);
require (WP_PLUGIN_DIR . '/wpfixedverticalfeedbackbutton/formIntegration.php');

/**
 * WFVFB main class
 */
class cbWordpressFixedVerticalFeedbackButton {

    var $buttondata;
    var $wpfixedverticalfeedbackbutton;
    var $defaults;
    var $wpfixedverticalfeedbackbutton_hook;

    // constructor
    function __construct() {
        // button image list
        $this->buttondata                    = array(
            'be_social_small.png'        => 162,
            'callback_caps.png'          => 113,
            'callback_small.png'         => 91,
            'care_share.png'             => 144,
            'COMENTARIOS.png'            => 151,
            'COMENTARIOS-FEEDBACK.png'   => 264,
            'contact_caps.png'           => 102,
            'contact_small.png'          => 83,
            'contact_us_caps.png'        => 132,
            'contact_us_mix.png'         => 110,
            'feedback_caps.png'          => 115,
            'feedback_mix.png'           => 97,
            'feedback_small.png'         => 90,
            'feedback_tab_white.png'     => 90,
            'requestacallback_caps.png'  => 229,
            'requestacallback_small.png' => 192
        );

        // Set the default value.
        $this->defaults                      = array('buttonno'     => 1,
            //'adminpreview' => 1,
            'buttoncon'    => array(
                'name'              => array('', '', '', '', ''),
                'right'             => array(0, 1, 0, 1, 0),
                'top'               => array(50, 50, 20, 20, 70),
                'backcolor'         => array('#0066CC', '#0066CC', '#0066CC', '#0066CC', '#0066CC'),
                'hbackcolor'        => array('#FF8B00', '#FF8B00', '#FF8B00', '#FF8B00', '#FF8B00'),
                'id'                => array('', '', '', '', ''),
                'clink'             => array('', '', '', '', ''),
                'clinktitle'        => array('', '', '', '', ''),
                'clinkopen'         => array('_blank', '_blank', '_blank', '_blank', '_blank'),
                'show'              => array(1, 1, 1, 0, 0),
                'showtype'          => array('show', 'show', 'show', 'show', 'show'),
                'postlist'          => array('', '', '', '', ''),
                'ver'               => array('1.0', '1.0', '1.0', '1.0', '1.0'),
                'buttontext'        => array('feedback_mix.png', 'contact_small.png', 'care_share.png', 'be_social_small.png', 'callback_small.png'),
                'bilink'            => array('', '', '', '', ''),
                'bctext'            => array('Feedback', 'Feedback', 'Feedback', 'Feedback', 'Feedback'),
                'biheight'          => array('', '', '', '', ''),
                'showcontactform'   => array(0, 0, 0, 0, 0),
                'form_open' 		=> array('no', 'no', 'no', 'no', 'no'),
                'choose_form'       => array('', '', '', '', ''),
            )
        );
		
        // Read the plugin options
        $this->wpfixedverticalfeedbackbutton = get_option('wpfixedverticalfeedbackbutton');
		//var_dump( $this->wpfixedverticalfeedbackbutton);
        // inject the buttons html to the site
        for ($count = 0; $count < $this->wpfixedverticalfeedbackbutton['buttonno']; $count++) {
            if ($this->wpfixedverticalfeedbackbutton['buttoncon']['show'][$count] == '1') {
                // add_action('wp_enqueue_scripts', array($this, 'wpfvfbutton_addjs'));
                add_action('wp_head', array($this, 'wpfvfbutton_addstyle'));
                add_action('wp_footer', array($this, 'wpfvfbutton_addhtml'));
                break;
            }
        }

        // activation and deactivation hooks registration
        register_activation_hook(__FILE__, array($this, 'wpfixedverticalfeedbackbutton_activate'));
        register_deactivation_hook(__FILE__, array($this, 'wpfixedverticalfeedbackbutton_deactivation'));

        $this->wpfixedverticalfeedbackbutton_hook = add_action('admin_menu', array($this, 'wpfixedverticalfeedbackbutton_admin'));   //adding menu in admin menu settings

        add_action('wp_ajax_nopriv_cbFvfbAdminFormIntegration', array('wpFvfbFormIntegration', 'cbFvfbAdminFormIntegration'));
        add_action('wp_ajax_cbFvfbAdminFormIntegration', array('wpFvfbFormIntegration', 'cbFvfbAdminFormIntegration'));

        $plugin = plugin_basename(__FILE__);
        add_filter("plugin_action_links_$plugin", array($this, 'add_wpfixedverticalfeedbackbutton_settings_link'));

    }// end of constructor


    //plugin activation action
    function wpfixedverticalfeedbackbutton_activate() {

        $this->wpfixedverticalfeedbackbutton['buttonno']     = $this->defaults['buttonno'];
        //$this->wpfixedverticalfeedbackbutton['adminpreview'] = $this->defaults['adminpreview'];

        foreach ($this->defaults['buttoncon'] as $key => $value) {
            for ($cur = 0; $cur < $this->defaults['buttonno']; $cur++)
                $this->wpfixedverticalfeedbackbutton['buttoncon'][$key][$cur] = $value[$cur];
        }

        // save the default values
        update_option('wpfixedverticalfeedbackbutton', $this->wpfixedverticalfeedbackbutton);

    }

    //plugin deactivation action
    function wpfixedverticalfeedbackbutton_deactivation() {

        //let's keep the otpion table clean
        delete_option('wpfixedverticalfeedbackbutton');

    }

    /**
     *
     */
    function wpfixedverticalfeedbackbutton_admin() {
        // adding option page for plugin
        if (function_exists('add_options_page')) {
            $page_hook = add_options_page('Codeboxr Fixed Vertical Feedback Button', 'Vertical Feedback', 'manage_options', 'wpfixedverticalfeedbackbutton', array($this, 'wpfixedverticalfeedbackbutton_admin_option'));

            /* Using registered $page_hook handle to hook script load */
            add_action('admin_print_scripts-' . $page_hook, array($this, 'wpfixedverticalfeedbackbutton_admin_js'));
            add_action('admin_print_styles-' . $page_hook, array($this, 'wpfixedverticalfeedbackbutton_admin_css'));
        }



    }

    // styles for plugin options page
    function wpfixedverticalfeedbackbutton_admin_css() {
        //wp_enqueue_style('bootstrap', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/css/bootstrap.min.css', '', '1.0');


        wp_enqueue_style('colorpickercss', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/colorpicker/css/colorpicker.css', '', '1.0');

        wp_enqueue_style('jquery-uniform-style', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/css/uniform.aristo.min.css', array());
        //wp_enqueue_style('jquery-chosen-style', WP_PLUGIN_URL.'/wpfixedverticalfeedbackbutton/css/chosen.min.css', array());
        wp_enqueue_style('jquery-selectize-style', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/css/jquery.selectize.css', array());
        wp_enqueue_style('wpfixedverticalfeedbackbuttonadmin', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/css/wpfixedverticalfeedbackbuttonadmin.css', '', '1.0');

    }

    // scripts for plugin options page
    function wpfixedverticalfeedbackbutton_admin_js() {

        wp_enqueue_script('jquery');
        //wp_enqueue_script('bootstartp-modal-js', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/bootstrap.min.js', 'jquery', '1.0', false);

        //wp_enqueue_script('colorpickerjs', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/colorpicker/js/colorpicker.js', 'jquery', '1.0', false);
        //wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker');


        wp_enqueue_script('jquery-uniform', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/jquery.uniform.js', array('jquery'));
        //wp_enqueue_script('jquery-chosen', WP_PLUGIN_URL.'/wpfixedverticalfeedbackbutton/js/chosen.jquery.js', array('jquery'));
        wp_enqueue_script('jquery-selectize', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/jquery.selectize.min.js', array('jquery'));
        wp_enqueue_script('wpfixedverticalfeedbackbuttonform', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/wpfixedverticalfeedbackbuttonform.js', 'jquery', '1.0', false);

    }

    //admin option page
    function wpfixedverticalfeedbackbutton_admin_option() {


        $thirdPartyForms = wpFvfbFormIntegration::getFormPlugins();

        $defaultval = array(
            'name'            => '',
            'right'           => 1,
            'top'             => 50,
            'backcolor'       => '#0066CC',
            'hbackcolor'      => '#FF8B00',
            'id'              => '',
            'clink'           => '',
            'clinktitle'      => '',
            'clinkopen'       => '',
            'show'            => 1,
            'showtype'        => '',
            'postlist'        => '',
            'buttontext'      => 'feedback_small.png',
            'bilink'          => '',
            'bctext'          => '',
            'biheight'        => '',
            'showcontactform' => 0,
            'form_open' 	  => 'no',
            'choose_form' => '',
            'form_listing' => '',
        );

        if (isset($_POST['uwpfixedverticalfeedbackbutton'])) {

            check_admin_referer('admin_head-settings_page_wpfixedverticalfeedbackbutton', 'wpfixedverticalfeedbackbutton');

            $this->wpfixedverticalfeedbackbutton['adminpreview'] = $_POST['adminpreview'];

            if(class_exists('wpfixedverticalfeedbackbuttonaddon')){

                wpfixedverticalfeedbackbuttonaddon::wpfixedverticalfeedbackbutton_update($defaultval,$this->defaults,$this->wpfixedverticalfeedbackbutton);
            }
            else{
                $this->wpfixedverticalfeedbackbutton['buttonno'] = $this->defaults['buttonno'];
                foreach ($defaultval as $key => $value) {
                    $this->wpfixedverticalfeedbackbutton['buttoncon'][$key][0] = trim($_POST[$key][0]);
                }
                $updatesuccess = update_option('wpfixedverticalfeedbackbutton', $this->wpfixedverticalfeedbackbutton);
            }
            //var_dump($updatesuccess);
        }//end main if

        $this->wpfixedverticalfeedbackbutton = (array) get_option('wpfixedverticalfeedbackbutton');
        

        if (isset($_POST['uwpfixedverticalfeedbackbutton'])) {
            echo '<!-- Last Action --><div id="message" class="updated fade"><p>Options updated</p></div>';
        }

        ?>
        <script type="text/javascript">
            var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <div class="wrap">
            <div class="icon32" id="icon-options-general"><br></div>
            <h2>Codeboxr Fixed Vertical Feedback Button</h2>
            <div id="poststuff" class="metabox-holder has-right-sidebar">
                <div id="post-body">
                    <div id="post-body-content">

                        <div class="stuffbox cb-fixedverticalbutton-wrapper">
                            <h3>Plugin Settings</h3>
                            <div class="inside">
                                <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                    <?php wp_nonce_field('admin_head-settings_page_wpfixedverticalfeedbackbutton', 'wpfixedverticalfeedbackbutton'); ?>

                                    <p class="submit"><input type="submit" name="uwpfixedverticalfeedbackbutton" class="button-primary" value="Save Changes" ></p>


        <?php
        //call function for unlimited button input field
        if(class_exists('wpfixedverticalfeedbackbuttonaddon')){

            wpfixedverticalfeedbackbuttonaddon::wpfixedverticalfeedbackbutton_addmore($this->wpfixedverticalfeedbackbutton['buttonno']);
        }
        for ($number = 0; $number < $this->wpfixedverticalfeedbackbutton['buttonno']; $number++) {
            $n = $number + 1;
            //echo '<div class="fvfeedbackhandler"><div class="fvfeedbackbuttontitle"><h3>'.($wpfixedverticalfeedbackbutton['buttoncon']['name'][$number]!='' ? $wpfixedverticalfeedbackbutton['buttoncon']['name'][$number]: 'Button '.$n).'</h3><span class="instr">Click to expand/collapse button properties.</span></div>
            echo '<div class="fvfeedbackhandler" id="fvfeedbackhandler' . $number . '"><h3>' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['name'][$number] != '' ? $this->wpfixedverticalfeedbackbutton['buttoncon']['name'][$number] : 'Button ' . $n) . ' (' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['show'][$number] ? 'Enabled' : 'Disabled') . ')</h3>
                <div class="fvfeedbackbuttonprop">
                    <table cellspacing="0" class="widefat post fixed">

                        <tbody>
                        <tr valign="top">
                            <td>Button name</td>
                            <td>
                            <input type="text" name="name[' . $number . ']" value="' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['name'][$number] != '' ? $this->wpfixedverticalfeedbackbutton['buttoncon']['name'][$number] : '') . '" size="30" /><br />
                            <p class="description">Name of the button</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <td>Visibility</td>
                            <td>';
                                echo '<input id="visibility-show-' . $number . '" type="radio" ' . checked($this->wpfixedverticalfeedbackbutton['buttoncon']['show'][$number], 1, false) . ' value="1" name="show[' . $number . ']" />
                                <label for="visibility-show-' . $number . '">Show</label>
                                <input id="visibility-hide-' . $number . '" type="radio" ' . checked($this->wpfixedverticalfeedbackbutton['buttoncon']['show'][$number], 0, false) . ' value="0" name="show[' . $number . ']" />
                                <label for="visibility-hide-' . $number . '">Hide</label>
                                <br/>
                                <p class="description">Show/hide button, no need deactivate plugin to hide the button for some days.</p>
                                <br/>
                                <input id="showtype-show-' . $number . '" type="radio" ' . checked($this->wpfixedverticalfeedbackbutton['buttoncon']['showtype'][$number], 'show', false) . ' value="show" name="showtype[' . $number . ']" />
                                <label for="showtype-show-' . $number . '">Show only in following post(s)</label>
                                <input id="showtype-hide-' . $number . '" type="radio" ' . checked($this->wpfixedverticalfeedbackbutton['buttoncon']['showtype'][$number], 'hide', false) . ' value="hide" name="showtype[' . $number . ']" />
                                <label for="showtype-hide-' . $number . '">Hide in following post(s)</label>

                                <br/>
                                <input type="text" name="postlist[' . $number . ']" value="' . $this->wpfixedverticalfeedbackbutton['buttoncon']['postlist'][$number] . '" placeholder="Put the post IDs here.">
                                <p class="description">Post the post IDs here to show/hide for particular pages/posts. <b>Comma(,)</b> separated list. Leave the list blank for no filter.</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <td width="30%">Horizontal Align</td>
                            <td width="70%">';
                                echo '<input autocomplete="off" id="right-1-' . $number . '" type="radio" ' . checked($this->wpfixedverticalfeedbackbutton['buttoncon']['right'][$number], 1, false) . ' value="1" name="right[' . $number . ']" />
                                        <label for="right-1-' . $number . '">Right</label>
                                        <input autocomplete="off" id="right-0-' . $number . '" type="radio" ' . checked($this->wpfixedverticalfeedbackbutton['buttoncon']['right'][$number], 0, false) . ' value="0" name="right[' . $number . ']" />
                                        <label for="right-0-' . $number . '">Left</label>
                                        <p class="description">Button positon left or right</p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>Vertical Position</td>
                                    <td>
                                    <input type="number" name="top[' . $number . ']" value="' . $this->wpfixedverticalfeedbackbutton['buttoncon']['top'][$number] . '" size="6" /><br />
                                    <p class="description">Vertical position as percentage, don\'t put %, just put something like 50 0r 60</p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>Background Color</td>
                                    <td>
                                    <input class="cbcolor" data-default-color="#0066CC" type="text" name="backcolor[' . $number . ']" value="' .((strpos($this->wpfixedverticalfeedbackbutton['buttoncon']['backcolor'][$number], '#') === FALSE)? '#': '' ). $this->wpfixedverticalfeedbackbutton['buttoncon']['backcolor'][$number] . '" size="7" /><br />
                                    <p class="description">Background color of feedback button</p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>Background Color for mouse hover</td>
                                    <td>
                                    <input class="cbcolor" data-default-color="#FF8B00" type="text" name="hbackcolor[' . $number . ']" value="' .((strpos($this->wpfixedverticalfeedbackbutton['buttoncon']['hbackcolor'][$number], '#') === FALSE)? '#': '' ). $this->wpfixedverticalfeedbackbutton['buttoncon']['hbackcolor'][$number] . '" size="7" /><br />
                                    <p class="description">Background color of feedback button when mouse hover</p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td width="30%">Button Text</td>
                                    <td width="70%" class="no-overflow">
                                        <select data-selnumber="' . $number . '" name="buttontext[' . $number . ']" class="select_buttontext select_buttontext'.$number.'">
                                            <option value="contact_small.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'contact_small.png' ? 'selected="selected"' : '' ) . ' >Contact</option>
                                            <option value="be_social_small.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'be_social_small.png' ? 'selected="selected"' : '' ) . ' >be social. share!</option>
                                            <option value="callback_caps.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'callback_caps.png' ? 'selected="selected"' : '' ) . ' >CALL BACK</option>
                                            <option value="callback_small.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'callback_small.png' ? 'selected="selected"' : '' ) . ' >call back</option>
                                            <option value="care_share.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'care_share.png' ? 'selected="selected"' : '' ) . ' >care for share?</option>
                                            <option value="COMENTARIOS-FEEDBACK.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'COMENTARIOS-FEEDBACK.png' ? 'selected="selected"' : '' ) . ' >COMENTARIOS-FEEDBACK</option>
                                            <option value="COMENTARIOS.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'COMENTARIOS.png' ? 'selected="selected"' : '' ) . ' >COMENTARIOS</option>
                                            <option value="contact_caps.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'contact_caps.png' ? 'selected="selected"' : '' ) . ' >CONTACT</option>
                                            <option value="contact_us_caps.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'contact_us_caps.png' ? 'selected="selected"' : '' ) . ' >CONTACT US</option>
                                            <option value="contact_us_mix.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'contact_us_mix.png' ? 'selected="selected"' : '' ) . ' >Contact Us</option>
                                            <option value="feedback_caps.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'feedback_caps.png' ? 'selected="selected"' : '' ) . ' >FEEDBACK</option>
                                            <option value="feedback_mix.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'feedback_mix.png' ? 'selected="selected"' : '' ) . ' >Feedback</option>
                                            <option value="feedback_small.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'feedback_small.png' ? 'selected="selected"' : '' ) . ' >feedback</option>
                                            <option value="requestacallback_caps.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'requestacallback_caps.png' ? 'selected="selected"' : '' ) . ' >REQUEST A CALL BACK</option>
                                            <option value="requestacallback_small.png" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'requestacallback_small.png' ? 'selected="selected"' : '' ) . ' >Request a call back</option>
                                            <option value="custom_img" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'custom_img' ? 'selected="selected"' : '' ) . ' >Custom Image...</option>
                                            <option value="custom_text" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$number] == 'custom_text' ? 'selected="selected"' : '' ) . ' >Custom Text...</option>
                                        </select>
                                        <div class="for_custom_image for_custom_image'.$number.'" >
                                            Image URL: <input type="text" name="bilink[' . $number . ']" value="' . $this->wpfixedverticalfeedbackbutton['buttoncon']['bilink'][$number] . '" size="25" /><br />
                                            Image height: <input type="text" name="biheight[' . $number . ']" value="' . $this->wpfixedverticalfeedbackbutton['buttoncon']['biheight'][$number] . '" size="6" />px<br />
                                            <p class="description">Select your button text. For customized text image put your image url and image height.</p>
                                        </div>
                                        <div class="for_custom_text for_custom_text'.$number.'" >
                                            Custom text:  <input type="text" name="bctext[' . $number . ']" value="' . $this->wpfixedverticalfeedbackbutton['buttoncon']['bctext'][$number] . '" size="25" />
                                        </div>
                                    </td>
                                </tr>


                                <tr valign="top">
                                    <td>Link Button to Post/Page (ID)</td>
                                    <td>
                                    <input type="text" name="id[' . $number . ']" value="' . $this->wpfixedverticalfeedbackbutton['buttoncon']['id'][$number] . '" size="6" /><br />
                                    <p class="description">Put post or page id that you want to link as feedback or contact page, normally you should put page id</p></td>
                                </tr>
                                <tr valign="top">
                                    <td>Custom link</td>
                                    <td>
                                    <input type="text" name="clink[' . $number . ']" value="' . $this->wpfixedverticalfeedbackbutton['buttoncon']['clink'][$number] . '" size="30" /><br />
                                    <p class="description">To use custom link leave the post/page id blank</p>
                                    </td>
                                </tr>';
                                if(class_exists('wpfixedverticalfeedbackbuttonaddon')){

                                    wpfixedverticalfeedbackbuttonaddon::wpfixedverticalfeedbackbutton_formintegrationfields($number,$this->wpfixedverticalfeedbackbutton,$thirdPartyForms);
                                }


                               echo '
                                <tr valign="top">
                                    <td>Link title</td>
                                    <td>
                                    <input type="text" name="clinktitle[' . $number . ']" value="' . $this->wpfixedverticalfeedbackbutton['buttoncon']['clinktitle'][$number] . '" size="30" /><br />
                                    <p class="description">Title for the anchor tag.</p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <td>Link target</td>
                                    <td>
                                    <input id="clinkopen-_blank-' . $number . '" type="radio" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['clinkopen'][$number] == '_blank' ? 'checked="checked"' : '' ) . ' value="_blank" name="clinkopen[' . $number . ']" />
                                    <label for="clinkopen-_blank-' . $number . '">Open in new tab</label>
                                    <input id="clinkopen-_self-' . $number . '" type="radio" ' . ($this->wpfixedverticalfeedbackbutton['buttoncon']['clinkopen'][$number] == '_self' ? 'checked="checked"' : '' ) . ' value="_self" name="clinkopen[' . $number . ']" />
                                    <label for="clinkopen-_self-' . $number . '">Open in same tab</label>

                                    <p class="description">Control openning link in same window or new tab.</p>
                                    </td>
                                </tr>

                                <tr valign="top"><td></td>
                                </tr>
                                 <tr valign="top"><td></td>
                                </tr>


                                <tr valign="top">
                                    <td></td>
                                    <td><input type="submit" name="uwpfixedverticalfeedbackbutton" class="button-primary" value="Save Changes" ></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="cbclear"></div>
                    </div>
                </div>';
        }
        ?>
                                </form>
                            </div> <!-- inside -->
                        </div>  <!-- stuffbox -->
                    </div>
                </div> <!-- post-body -->
                <div id="side-info-column" class="inner-sidebar">
                    <?php
                        $plugin_data = get_plugin_data(__FILE__);
                    ?>
                    <div class="postbox">
                        <h3>Plugin Info</h3>
                        <div class="inside">
                            <p>Plugin Name : <?php echo $plugin_data['Title'] ?> <?php echo $plugin_data['Version'] ?></p>
                            <p>Author : <?php echo $plugin_data['Author'] ?></p>
                            <p>Website : <a href="http://codeboxr.com" target="_blank">codeboxr.com</a></p>
                            <p>Email : <a href="mailto:info@codeboxr.com" target="_blank">info@codeboxr.com</a></p>
                            <p>Twitter : @<a href="http://twitter.com/codeboxr" target="_blank">Codeboxr</a></p>
                            <p>Facebook : <a href="http://facebook.com/codeboxr" target="_blank">http://facebook.com/codeboxr</a></p>
                            <p>Linkedin : <a href="www.linkedin.com/company/codeboxr" target="_blank">codeboxr</a></p>
                            <p>Gplus : <a href="https://plus.google.com/+codeboxr" target="_blank">Google Plus</a></p>
                            <p>Youtube : <a href="https://www.youtube.com/user/codeboxrtv" target="_blank">Codeboxr TV</a></p>
                        </div>
                    </div>
                    <div class="postbox">
                        <h3>Help & Supports</h3>
                        <div class="inside">
                            <p>Support: <a href="http://codeboxr.com/contact-us.html" target="_blank">Contact Us</a></p>
                            <p><i class="icon-envelope"></i> <a href="mailto:info@codeboxr.com">info@codeboxr.com</a></p>
                            <p><i class="icon-phone"></i> <a href="tel:008801717308615">+8801717308615</a> (CEO, Sabuj Kundu)<br></p>
                            
                        </div>
                    </div>
                    <div class="postbox">
                        <h3>Video demo</h3>
                        <div class="inside">
                            <iframe src="http://www.screenr.com/embed/2Ow7" width="260" height="158" frameborder="0"></iframe>
                        </div>
                    </div>
                    <div class="postbox">
                        <h3>Pro Version Features</h3>
                        <div class="inside">
                            <ul>
	                            <li> 
	                            	Supports 5 Different Contact forms <a href="http://wordpress.org/plugins/si-contact-form" target="_blank">Fast Secure Contact Form</a>, <a href="http://www.deliciousdays.com/cforms-plugin" target="_blank">CForms II</a>, <a href="http://wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7</a>, <a href="https://wordpress.org/plugins/ninja-forms/" target="_blank">Ninja Form</a>, <a href="http://www.gravityforms.com/" target="_blank">Gravity Form</a>
	                            </li>
	                            <li>Unlimited Buttons</li>
                            </ul>
                        </div>
                    </div>


                    <div class="postbox">
                        <h3>Codeboxr Updates</h3>
                        <div class="inside">
						        <?php

						        include_once(ABSPATH . WPINC . '/feed.php');
						        if (function_exists('fetch_feed')) {
						            $feed = fetch_feed('http://codeboxr.com/feed');
						            // $feed = fetch_feed('http://feeds.feedburner.com/codeboxr'); // this is the external website's RSS feed URL
						            if (!is_wp_error($feed)) : $feed->init();
						                $feed->set_output_encoding('UTF-8'); // this is the encoding parameter, and can be left unchanged in almost every case
						                $feed->handle_content_type(); // this double-checks the encoding type
						                $feed->set_cache_duration(21600); // 21,600 seconds is six hours
						                $limit = $feed->get_item_quantity(6); // fetches the 18 most recent RSS feed stories
						                $items = $feed->get_items(0, $limit); // this sets the limit and array for parsing the feed

						                $blocks = array_slice($items, 0, 6); // Items zero through six will be displayed here
						                echo '<ul>';
						                foreach ($blocks as $block) {
						                    $url = $block->get_permalink();
						                    echo '<li><a target="_blank" href="' . $url . '">';
						                    echo '<strong>' . $block->get_title() . '</strong></a></li>';
						                    //var_dump($block->get_description());
						                    //echo $block->get_description();
						                    //echo substr($block->get_description(),0, strpos($block->get_description(), "<br />")+4);
						                }//end foreach
						                echo '</ul>';


						            endif;
						        }
						        ?>
                        </div>
                    </div>
                   
                    <div class="postbox">
                        <h3>Codeboxr on facebook</h3>
                        <div class="inside">
                            <iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fcodeboxr&amp;width=260&amp;height=258&amp;show_faces=true&amp;colorscheme=light&amp;stream=false&amp;border_color&amp;header=false&amp;appId=558248797526834" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:260px; height:258px;" allowTransparency="true"></iframe>
                        </div>
                    </div>
                </div> <!-- side-info-column -->
            </div> <!-- poststuff -->
        </div> <!-- wrap -->

        <script type="text/javascript">
              jQuery(document).ready(function() {
                  //jQuery(".fvfeedbackbuttonprop").hide();
                  //toggle the componenet with class msg_body
                  jQuery("#fvfeedbackhandler0 h3").parent(".fvfeedbackhandler").toggleClass("fvfeedbackhandlert");
                  jQuery("#fvfeedbackhandler0 h3").next(".fvfeedbackbuttonprop").slideToggle();
                  jQuery(".fvfeedbackhandler  h3").click(function()
                  {
                      jQuery(this).parent(".fvfeedbackhandler").toggleClass("fvfeedbackhandlert");
                      jQuery(this).next(".fvfeedbackbuttonprop").slideToggle();

                  });
              });
        </script>


        <?php

    }


    //adding style for feedback button
    function wpfvfbutton_addstyle() {
        $imageurl = '';
        for ($count = 0; $count < $this->wpfixedverticalfeedbackbutton['buttonno']; $count++) {
            if ($this->wpfixedverticalfeedbackbutton['buttoncon']['show'][$count]) {
                $rightorleft = $this->wpfixedverticalfeedbackbutton['buttoncon']['right'][$count];
                if ($rightorleft == '1') {
                    $right = 'right:0;';
                } else {
                    $right = 'left:0;';
                }
                $top       = intval($this->wpfixedverticalfeedbackbutton['buttoncon']['top'][$count]);
                $backcolor = $this->wpfixedverticalfeedbackbutton['buttoncon']['backcolor'][$count];
                if ($backcolor == '') {
                    $backcolor = '#0066CC';
                }

                if(strpos($backcolor, '#' ) === FALSE){
                    $backcolor = '#'.$backcolor;
                }

                $hbackcolor = $this->wpfixedverticalfeedbackbutton['buttoncon']['hbackcolor'][$count];
                if ($hbackcolor == '') {
                    $hbackcolor = '#FF8B00';
                }

                if(strpos($hbackcolor, '#') === FALSE){
                    $hbackcolor = '#'.$hbackcolor;
                }

                if ($top > 100 || $top < 0) {
                    $top = 50;
                }

                $buttontext = $this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$count];
				

				//custom image
                if($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$count] == 'custom_img') {
                	
                    $imageurl = $this->wpfixedverticalfeedbackbutton['buttoncon']['bilink'][$count];
                    $height   = $this->wpfixedverticalfeedbackbutton['buttoncon']['biheight'][$count];
                } else {
                    $imageurl = WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/images/' . $buttontext;
					//var_dump($imageurl);
                    $height   = $this->buttondata[$buttontext];
                }
				
                $bctext = $this->wpfixedverticalfeedbackbutton['buttoncon']['bctext'][$count];
				
                if ($bctext == '') {
                	echo '<style type="text/css" media="screen">
                            div#fvfeedbackbutton' . $count . ' {
                                height:' . $height . ' px;
                                position:fixed;' . $right . '
                                text-indent:-9999px;
                                top:' . $top . '%;
                                width:22px;
                                line-height:0;
                            }

                            div#fvfeedbackbutton' . $count . ' span{
                                background:url("' . $imageurl . '") no-repeat scroll 50% 50% ' . $backcolor . ';
                                display:block;
                                height:' . $height . ' px;
                                padding:5px;
                                position:fixed;' . $right . '
                                text-indent:-9999px;
                                top:' . $top . '%;
                                width:22px;
                                line-height:0;

                            }

                            div#fvfeedbackbutton' . $count . ' span:hover {
                                background-color:' . $hbackcolor . ';

                            }
                            </style>';
							//var_dump($height);
				}
                else {

                    echo '<style type="text/css" media="screen">
                            div#fvfeedbackbutton' . $count . '{

                                position:fixed;' . $right . '
                                top:' . $top . '%;';
                    if ($rightorleft == 0) echo '-webkit-transform: rotate(-90deg);
                                -webkit-transform-origin: left top;
                                -moz-transform: rotate(-90deg);
                                -moz-transform-origin: left top;
                                -o-transform: rotate(-90deg);
                                -o-transform-origin: left top;
                                -ms-transform: rotate(-90deg);
                                -ms-transform-origin: left top;
                                filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=4);
                                }';
                    else echo '-webkit-transform: rotate(-270deg);
                                -webkit-transform-origin: right top;
                                -moz-transform: rotate(-270deg);
                                -moz-transform-origin: right top;
                                -o-transform: rotate(-270deg);
                                -o-transform-origin: right top;
                                -ms-transform: rotate(-270deg);
                                -ms-transform-origin: right top;
                                filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=4);
                                }';

                    echo '
                            div#fvfeedbackbutton' . $count . ' a{
                                text-decoration: none;
                            }

                            div#fvfeedbackbutton' . $count . ' span {
                                background-color:' . $backcolor . ';
                                display:block;
                                padding:8px;
                                font-weight: bold;
                                color:#fff;
                                font-size: 18px;
                                font-family: Arial, sans-serif;
								height:' . $height . ' px;

                            }

                            div#fvfeedbackbutton' . $count . ' span:hover {
                                background-color:' . $hbackcolor . ';

                            }
                            </style>';
                }
            }
        }

        echo '<style type="text/css" media="screen">
                div.fvfeedbackbutton {
                    z-index: 99999 !important;
                }
              </style>';

        wp_enqueue_script('jquery');
        //wp_enqueue_script('thickbox',null,array('jquery'));
        //wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');

        wp_enqueue_style('jquery.nyromodal', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/jquery.nyroModal/styles/nyroModal.css', '', '1.0', false);
        wp_enqueue_script('jquery.nyromodal', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/jquery.nyroModal/js/jquery.nyroModal.custom.min.js', array('jquery'), '1.0', false);
        wp_enqueue_script('jquery.nyromodalfrontjs', WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/wpfixedverticalfeedbackbuttonfront.js', array('jquery','jquery.nyromodal'), '1.0', false);


    }

    // button html
    function wpfvfbutton_addhtml() {
        global $post;
        // var_dump($post->ID);
        //echo '<pre>'; print_r($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext']); echo '</pre>'; die();

        $link = '';
        for ($count = 0; $count < $this->wpfixedverticalfeedbackbutton['buttonno']; $count++) {
            if ($this->wpfixedverticalfeedbackbutton['buttoncon']['show'][$count]) {
                $postlist = explode(',', $this->wpfixedverticalfeedbackbutton['buttoncon']['postlist'][$count]);

                // var_dump($postlist);
                $title = $this->wpfixedverticalfeedbackbutton['buttoncon']['clinktitle'][$count];
                $open  = $this->wpfixedverticalfeedbackbutton['buttoncon']['clinkopen'][$count];
                $link  = ($this->wpfixedverticalfeedbackbutton['buttoncon']['id'][$count] == '') ? $this->wpfixedverticalfeedbackbutton['buttoncon']['clink'][$count] : get_permalink($this->wpfixedverticalfeedbackbutton['buttoncon']['id'][$count]);

                $imageOrText = (
                        ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$count] == 'custom_img') ?
                                ( ($this->wpfixedverticalfeedbackbutton['buttoncon']['bilink'][$count] != 'custom_img') ?
                                        '<img src="' . $this->wpfixedverticalfeedbackbutton['buttoncon']['bilink'][$count] . '" style="height:' . $this->wpfixedverticalfeedbackbutton['buttoncon']['biheight'][$count] . '" alt="btnimage" />' : ''
                                ) : ( ($this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$count] == 'custom_text') ? $this->wpfixedverticalfeedbackbutton['buttoncon']['bctext'][$count] : '<img src="' . plugins_url('images/' . $this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$count], __FILE__) . '" alt="' . $this->wpfixedverticalfeedbackbutton['buttoncon']['buttontext'][$count] . '" />' )
                        );

                if (preg_match('/(^<img) (.*)(\\\?>$)/i', $imageOrText)) {
                    echo $style = '<style type="text/css">
                        div#fvfeedbackbutton' . $count . ' {
                            transform: none;
                            -webkit-transform: none;
                            -moz-transform: none;
                            -moz-transform-origin: none;
                            -o-transform: none;
                            -o-transform-origin: none;
                            -ms-transform: none;
                            -ms-transform-origin: none;
                            filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=0);

                        }
                        </style>
                    ';
                }
                if (
                    $this->wpfixedverticalfeedbackbutton['buttoncon']['showtype'][$count] == 'show' && in_array($post->ID, $postlist) ||
                    $this->wpfixedverticalfeedbackbutton['buttoncon']['showtype'][$count] == 'hide' && !in_array($post->ID, $postlist) ||
                    empty($this->wpfixedverticalfeedbackbutton['buttoncon']['postlist'][$count])
                    // || is_admin()
                ) {

                    if($this->wpfixedverticalfeedbackbutton['buttoncon']['form_open'][$count] == 'yes') {

                        $formName = $this->wpfixedverticalfeedbackbutton['buttoncon']['choose_form'][$count];
                        $formId = $this->wpfixedverticalfeedbackbutton['buttoncon']['form_listing'][$count];
                        $plugins = wpFvfbFormIntegration::getFormPlugins();

                        $method  = $plugins[ $formName ]['formDisplayFunction'];

                        //echo '<pre>'; var_dump(method_exists('wpFvfbFormIntegration', $method)); echo '</pre>'; die();
                        if(array_key_exists('classname' , $plugins[ $formName ] )){
                            $class_name = $plugins[ $formName ]['classname'];
                        }
                        else{
                            $class_name = 'wpFvfbFormIntegration';
                        }

                        if(method_exists('wpFvfbFormIntegration', $method) || method_exists($class_name , $method)) {
                            if($method == ''){

                            }
                            $form = $class_name::$method($formId);

                            //echo '<pre>'; var_dump($formId, $formName, $method); echo '</pre>'; die();

                            if(!empty($form)) {
                                $formOutput = //'<div id="wpfvfbForm_'.$count.'" class="wpfvfbFormWrapper wpfvfbForm wpfvfbForm_'.$count.'" data-btn-id="'.$count.'" >';

                                            '<div style="display:none;" id="wpfvfbForm_'.$count.'" class="fvfeedbackbuttonformwrap" >

                                                <div class="modal-body">
                                                  <div class="">'.$form.'</div>
                                                </div>
                                                <div class="form-footer">

                                                </div>
                                                <!--a class="close-reveal-modal">&#215;</a-->
                                              </div>
                                        <!--/div-->
                                        <!--script type="text/javascript" src="'.WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/jquery.reveal.js'.'"></script>
                                        <link media="all" type="text/css" href="'.WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/css/jquery.reveal.css'.'" id="nyroModal-css" rel="stylesheet">
                                        <script type="text/javascript" src="'.WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/distrib.min.js'.'"></script>
                                        <link media="all" type="text/css" href="'.WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/css/easybox.min.css'.'" id="nyroModal-css" rel="stylesheet">
                                        <script type="text/javascript" src="'.WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/jquery.nyroModal.custom.min.js'.'"></script>
                                        <link media="all" type="text/css" href="'.WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/css/nyroModal.css'.'" id="nyroModal-css" rel="stylesheet"-->
                                        '
                                        ;

                                $link = '#wpfvfbForm_'.$count;
                                $class = ' displayForm displayForm_'.$count;
                                //$anchorClass = 'nyroModal lightbox displayFormAnchor displayFormAnchor_'.$count.' ';
                                $anchorClass = ' nyroModal displayFormAnchor displayFormAnchor_'.$count.' ';
                                //$modalAttr = 'data-toggle="modal" data-target="#wpfvfbForm_'.$count.'" ';
                                $modalAttr = '';
                                //$anchorModalAttr = 'data-width="320" data-height="240" data-reveal-id="wpfvfbForm_'.$count.'" data-animation="fadeAndPop" data-animationspeed="50" data-closeonbackgroundclick="true" data-dismissmodalclass="close-reveal-modal"';
                                $anchorModalAttr = '';
                                $open = '';

                            }
                        }

                        echo apply_filters('cbx_change_form_output' , $formOutput , $formId);
                        echo '<style type="text/css">
                        .modal-backdrop {
                            position: relative !important;
                        }

                    </style>';
                        $cbwpfvb_show_form = 1;
                    }

                    else {

                        $anchorClass = 'displayFormAnchor displayFormAnchor_'.$count.' ';
                        $cbwpfvb_show_form = 0;
                        $class = $modalAttr = $formOutput = '';

                    }

                    //echo '<!-- Codeboxr fixed verticel feedback button(s) --><div '.$modalAttr.' data-btn-id="'.$count.'" class="fvfeedbackbutton '.$class.' " id="fvfeedbackbutton' . $count . '"><a class="'.$anchorClass.'" data-btn-id="'.$count.'" '.$anchorModalAttr.' href="' . $link . '" title="' . $title . '" target="' . $open . '"><span>' . $imageOrText . '</span></a></div>'; //old
                    echo '<div '.$modalAttr.'  class="fvfeedbackbutton '.$class.' " id="fvfeedbackbutton' . $count . '"><a class="'.$anchorClass.'"  '.$anchorModalAttr.' href="' . $link . '" data-count = "'.$count.'" data-show-form = "'.$cbwpfvb_show_form.'" title="' . $title . '" target="' . $open . '"><span>' . $imageOrText . '</span></a></div>'; //old


                }
            }
        }

    }

    // adding settings and support link under the plugin name in the plugin list
    function add_wpfixedverticalfeedbackbutton_settings_link($links) {
        $settings_link = '<a href="options-general.php?page=wpfixedverticalfeedbackbutton">Settings</a>';
        array_unshift($links, $settings_link);
        $support_link  = '<a href="http://codeboxr.com/contact-us.html">Support</a>';
        array_unshift($links, $support_link);
        return $links;

    }

    /**
     * Returns current plugin version.
     *
     * @return string Plugin version
     */
    function wpfixedverticalfeedbackbutton_get_plgversion() {
        if (!function_exists('get_plugins')) require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $plugin_folder = get_plugins('/' . plugin_basename(dirname(__FILE__)));
        $plugin_file   = basename(( __FILE__));
        return $plugin_folder[$plugin_file]['Version'];

    }

    /**
     * @param $valueToMatch
     * @param $defaultAttribute
     * @param $type
     * @param string $currentValue
     * @return string
     */
    function get_selection_attribute($valueToMatch, $defaultAttribute, $type, $currentValue='') {
        if( !empty($currentValue) ) {
            if ( (string) $valueToMatch == (string) $currentValue ) {
                $result = $type.'="'.$type.'" ';
            } else {
                $result = '';
            }
        } else {
            $result = $defaultAttribute;
        }
        return $result;
    }

}

//end of class


$CBWordpressFixedVerticalFeedbackButton = new cbWordpressFixedVerticalFeedbackButton();
