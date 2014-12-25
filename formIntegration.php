<?php

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Class wpFvfbFormIntegration
 */
class wpFvfbFormIntegration {

    public static $wpfixedverticalfeedbackbutton;

    public static function cbFvfbAdminFormIntegration() {

        if( isset($_POST['cbFvfbAdminFormIntegrationData']) and !empty($_POST['cbFvfbAdminFormIntegrationData']) ) {
            $returnedData = $_POST['cbFvfbAdminFormIntegrationData'];

            if( !empty($returnedData) ) {
                if( wp_verify_nonce($returnedData['nonce'], 'admin_head-settings_page_wpfixedverticalfeedbackbutton') ) {
                    echo self::constructFormListingOutput($returnedData);
                }
            }
        }

        die();
    }

    /**
     * @param $vars
     *
     * @return mixed|string|void
     */
    public static function constructFormListingOutput($vars) {
        $output = '';
        $plugins = wpFvfbFormIntegration::getFormPlugins();

        if( isset($plugins[ $vars['outsideFormId'] ]) && ( is_plugin_active($plugins[ $vars['outsideFormId'] ]['pluginFile']) ) ) {
            wpFvfbFormIntegration::$wpfixedverticalfeedbackbutton = (array) get_option('wpfixedverticalfeedbackbutton');

            $selectedFormPlugin = ( (wpFvfbFormIntegration::$wpfixedverticalfeedbackbutton['buttoncon']['choose_form'][ $vars['buttonNumber'] ]) ? wpFvfbFormIntegration::$wpfixedverticalfeedbackbutton['buttoncon']['choose_form'][ $vars['buttonNumber'] ] : '' );
            $selectedForm = ( (wpFvfbFormIntegration::$wpfixedverticalfeedbackbutton['buttoncon']['form_listing'][ $vars['buttonNumber'] ]) ? wpFvfbFormIntegration::$wpfixedverticalfeedbackbutton['buttoncon']['form_listing'][ $vars['buttonNumber'] ] : '' );

            $method  = $plugins[ $vars['outsideFormId'] ]['formListingFunction'];
            if(array_key_exists('classname' ,$plugins[ $vars['outsideFormId'] ] )){
                $class_name = $plugins[ $vars['outsideFormId'] ]['classname'];
            }
            else{
                $class_name = 'wpFvfbFormIntegration';
            }

            if(method_exists('wpFvfbFormIntegration', $method) || method_exists($class_name, $method)) {
                $list = $class_name::$method();
               // var_dump($list);

                if(!empty($list)) {
                    if(!empty($list['forms'])) {
                        if($list['multi'] === true) {
                            $output .= '<select name="form_listing[' . $vars['buttonNumber'] . ']">';
                            $output .= '<option value="">Select Form</option>';
                            foreach ($list['forms'] as $key => $form) {
                                if($selectedForm == $key) {
                                    $output .= '<option selected value="'.$key.'">'.$form.'</option>';
                                } else {
                                    $output .= '<option value="'.$key.'">'.$form.'</option>';
                                }
                            }
                            $output .= '</select>';
                        }
                    } else {
                        $output = json_encode(
                            array(
                                'error' => true,
                                'errorMessage' => __('No form has been made.', 'wpfixedverticalfeedbackbutton'),
                            )
                        );
                    }
                } else {
                    $output = json_encode(
                        array(
                            'error' => true,
                            'errorMessage' => __('Plugin is not activated or No form has been made.', 'wpfixedverticalfeedbackbutton'),
                        )
                    );
                }
            }
        }

        return $output;
    }

    /**
     * @return mixed|void
     */
    public static function getFormPlugins() {
        $plugins = array(
            'cforms' => array(
                'title' => 'CForms',
                'pluginFile' => 'cforms/cforms.php',
                'formListingFunction' => 'cformsList',
                'formDisplayFunction' => 'cformsDisplay',
            ),
            'contact-form-7' => array(
                'title' => 'Contact Form 7',
                'pluginFile' => 'contact-form-7/wp-contact-form-7.php',
                'formListingFunction' => 'cf7List',
                'formDisplayFunction' => 'cf7Display',
            ),
            'si-contact-form' => array(
                'title' => 'Fast Secure Contact Form',
                'pluginFile' => 'si-contact-form/si-contact-form.php',
                'formListingFunction' => 'sicfList',
                'formDisplayFunction' => 'sicfDisplay',
            ),

        );

        return apply_filters('cbxfixedvbtn_add_form_params' ,$plugins );
    }

    /**
     * @return array
     */
    public static function cformsList() {
        $forms = array();
        if( is_plugin_active('cforms/cforms.php') ) {
            $cformsSettings = get_option('cforms_settings');

            if(!empty($cformsSettings['global']['cforms_formcount'])) {

                $FORMCOUNT = $cformsSettings['global']['cforms_formcount'];

                for ($i=1; $i<=$FORMCOUNT; $i++){
                    $j   = ( $i > 1 )?$i:'';
                    $formlistbox[$i] = stripslashes($cformsSettings['form'.$j]['cforms'.$j.'_fname']);
                }
                if(!empty($formlistbox)) {
                    $forms['multi'] = true;
                    $forms['forms'] = $formlistbox;
                }
            }
        }
      //  var_dump($forms);
        return $forms;
    }

    /**
     * @return array
     */
    public static function cf7List() {
        $forms = array();
        if( is_plugin_active('contact-form-7/wp-contact-form-7.php') ) {
            if ( ! class_exists( 'WPCF7_Contact_Form_List_Table' ) )
                require_once WPCF7_PLUGIN_DIR . '/admin/includes/class-contact-forms-list-table.php';

            if(class_exists('WPCF7_Contact_Form_List_Table')) {
                $items = WPCF7_ContactForm::find(  );  // includes/classes.php:33
                //echo '<pre>'; print_r($items); echo '</pre>'; die();

                if(!empty($items)) {
                    $forms['multi'] = true;

                    foreach($items as $item) {
                        $forms['forms'][$item->id] = $item->title;
                    }

                } else {
                    $forms = array();
                }
            } else {
                $forms['error'][] = __('The plugin definition is changed by the plugin author. Please contact to the plugin(Feedback Button) administrator', 'wpfixedverticalfeedbackbutton');
            }
        }

        return $forms;
    }

    /**
     * @return array
     */
    public static function sicfList() {
        $forms = array();
        if( is_plugin_active('si-contact-form/si-contact-form.php') ) {
            $options = get_option( 'fs_contact_global' );

            if(!empty($options['form_list'])) {
                $forms['multi'] = true;
                $forms['forms'] = $options['form_list'];
            } else {
                $forms = array();
            }
        }

        return $forms;
    }

    /**
     * @param $formId
     *
     * @return string
     */
    public static function cformsDisplay($formId) {
        $output = '';

        if( !empty($formId) and is_plugin_active('cforms/cforms.php') ) {
            //$output = insert_cform($formId);

            $pid = cfget_pid();

            if (!is_numeric($formId)) { $formId = check_form_name($formId); }

            //if (!$pid) {
                $output = cforms('', $formId . '');

                //add_action('wp_head', create_function('', 'echo \'<!--[if lt IE 6]><script src="' . WP_PLUGIN_URL . '/wpfixedverticalfeedbackbutton/js/bootstrap.min.js"></script><![endif]-->\';'));
                cforms_style();  // cforms.php: 1119
            //}
        }

        return $output;
    }

    /**
     * @param $formId
     *
     * @return string
     */
    public static function cf7Display($formId) {
        $output = '';

        if( !empty($formId) and is_plugin_active('contact-form-7/wp-contact-form-7.php') ) {
            $wpcf7_contact_form = wpcf7_contact_form( $formId );
            $unit_tag = 'wpcf7-f' . $wpcf7_contact_form->id . '-' . $processing_within . '-o' . $unit_count;
           // $wpcf7_contact_form->unit_tag = $unit_tag;

            $_wpcf7 = array(
		'loaderUrl' => wpcf7_ajax_loader(),
		'sending' => __( 'Sending ...', 'wpcf7' ) );

            if ( defined( 'WP_CACHE' ) && WP_CACHE )
                $_wpcf7['cached'] = 1;

            if ( wpcf7_support_html5_fallback() )
                $_wpcf7['jqueryUi'] = 1;

            wp_localize_script( 'contact-form-7', '_wpcf7', $_wpcf7 );

            $output = $wpcf7_contact_form->form_html();



            $output .= '<script type="text/javascript" >'
                    . '/* <![CDATA[ */
                        var _wpcf7_fvfb = '.  json_encode($_wpcf7).';
                        if(typeof _wpcf7 == "undefined") {
                            var _wpcf7 = _wpcf7_fvfb;
                        }
                        /* ]]> */'
                    . '</script>';
            $output .= '<script type="text/javascript" src="' . wpcf7_plugin_url( 'includes/js/jquery.form.min.js' ) . '"></script>';
            $output .= '<script type="text/javascript" src="' . wpcf7_plugin_url( 'includes/js/scripts.js' ) . '"></script>';

            $output .= '<link rel="stylesheet" media="all" type="text/css" href="' . wpcf7_plugin_url( 'includes/css/styles.css' ) . '"/>';
            if ( wpcf7_is_rtl() ) {
		        $output .= '<link rel="stylesheet" media="all" type="text/css" href="' . wpcf7_plugin_url( 'includes/css/style-rtl.css' ) . '"/>';
            }

        }
      //  var_dump($output);
        return $output;
    }

    /**
     * @param $formId
     *
     * @return string
     */
    public static function sicfDisplay($formId) {
        $output = '';

        if( !empty($formId) and is_plugin_active('si-contact-form/si-contact-form.php') ) {
            $output = FSCF_Display::process_short_code( array('form' => $formId ) );
        }

        return $output;
    }

}// end of class

