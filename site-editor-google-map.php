<?php
/*
Plugin Name: Site Editor Google Map
Plugin URI: http://www.siteeditor.org/extensions/google-map
Description: Site Editor Google Map added simple google map to your site with site editor
Author: Site Editor Team
Author URI: http://www.siteeditor.org
Version: 1.0.1
*/

if(!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);

if ( ! defined( 'SED_GOOGLE_MAP_PLUGIN_BASENAME' ) )
    define( 'SED_GOOGLE_MAP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'SED_GOOGLE_MAP_PLUGIN_NAME' ) )
    define( 'SED_GOOGLE_MAP_PLUGIN_NAME', trim( dirname( SED_GOOGLE_MAP_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'SED_GOOGLE_MAP_PLUGIN_DIR' ) )
    define( 'SED_GOOGLE_MAP_PLUGIN_DIR', WP_PLUGIN_DIR . DS . SED_GOOGLE_MAP_PLUGIN_NAME );

if ( ! defined( 'SED_GOOGLE_MAP_PLUGIN_URL' ) )
    define( 'SED_GOOGLE_MAP_PLUGIN_URL', WP_PLUGIN_URL . '/' . SED_GOOGLE_MAP_PLUGIN_NAME );

if ( ! defined( 'SED_GOOGLE_MAP_MODULE_DIR' ) )
    define( 'SED_GOOGLE_MAP_MODULE_DIR', SED_GOOGLE_MAP_PLUGIN_DIR . DS . 'modules' );

if ( ! defined( 'SED_GOOGLE_MAP_MODULES_URL' ) )
    define( 'SED_GOOGLE_MAP_MODULES_URL', SED_GOOGLE_MAP_PLUGIN_URL . '/modules' );

/**
 * Class SiteEditorGoogleMap
 */
final class SiteEditorGoogleMap{

    /**
     * @var object instance of SedGoogleMapProductsModules Class
     */
    public $products_modules;

    /**
     * @var array
     */
    public $modules = array();
    
    /**
     * The single instance of the class.
     *
     * @var SiteEditor
     * @since 0.9
     */
    protected static $_instance = null;

    /**
     * Main SiteEditor Instance.
     *
     * Ensures only one instance of SiteEditor is loaded or can be loaded.
     *
     * @since 0.9
     * @static
     * @see WC()
     * @return SiteEditor - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * SiteEditorGoogleMap constructor.
     */
    public function __construct(){

        //localize
        add_action( 'plugins_loaded', array(&$this, 'localization') );

        add_action( 'plugins_loaded', array($this, 'includes') );

        add_action( "sed_front_end_print_js_settings" , array( $this , 'print_js_settings' ) );

        add_filter( "sed_theme_options_panels_filter" , array( $this , 'register_theme_panels' ) , 100 );

        add_filter( "sed_theme_options_fields_filter" , array( $this , 'register_theme_fields' ) );

        add_filter("sed_modules" , array( $this , "add_modules" ) );

    }

    public function print_js_settings(){ //AIzaSyDoHumIzuSEzexl3CsF0u73UM_9CxqsPIA

        echo 'var SED_GOOGLE_MAP_MODULES_URL = "' . SED_GOOGLE_MAP_MODULES_URL . '";';

        echo 'var SED_WP_INCLUDES_URL = "' . includes_url() . '";';

        echo 'var SED_GOOGLE_API_KEY = "' . get_theme_mod( 'sed_google_api_key' , '' ) . '";';

    }

    public function register_theme_panels( $panels ){

        $panels['api_key_settings_panel'] = array(
            'title'                 =>  __('Api Key Settings',"site-editor")  ,
            'capability'            => 'edit_theme_options' ,
            'type'                  => 'inner_box' ,
            'priority'              => 100 ,
            'btn_style'             => 'menu' ,
            'has_border_box'        => false ,
            'icon'                  => 'sedico-setting' ,
            'field_spacing'         => 'sm'
        );

        return $panels;

    }

    public function register_theme_fields( $fields ){

        $fields['google_api_key'] = array(
            'setting_id'        => 'sed_google_api_key',
            'label'             => __('Google Api Key', 'site-editor'),
            'description'       => __( 'Google Api Key For Google Map' , 'site-editor' ),
            'type'              => 'text',
            'default'           => '',
            'option_type'       => 'theme_mod',
            'transport'         => 'refresh' ,
            'panel'             => 'api_key_settings_panel'
        );

        return $fields;

    }

    public function localization() {

        load_plugin_textdomain( "sed-google-map" , false, dirname( plugin_basename( __FILE__ ) ) . "/languages" );

    }

    public function includes(){

    }

    private function get_module( $module_name ){

        $module = "plugins/" . SED_GOOGLE_MAP_PLUGIN_NAME . "/modules/{$module_name}/{$module_name}.php";

        return $module;

    }

    private function get_module_path( $module_name ){

        return SED_GOOGLE_MAP_MODULE_DIR . DS . $module_name . DS . $module_name . ".php";

    }

    public function add_modules( $modules ){

        global $sed_pb_modules;

        $module_name = "google-map";

        $modules[$this->get_module( $module_name )] = $sed_pb_modules->get_module_data($this->get_module_path( $module_name ), true, true);

        return $modules;
    }

}

/**
 * Main instance of SiteEditor.
 *
 * Returns the main instance of SED to prevent the need to use globals.
 *
 * @since  0.9
 * @return SiteEditor
 */
function SedGMap() {
    return SiteEditorGoogleMap::instance();
}

SedGMap();