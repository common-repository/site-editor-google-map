<?php
/*
* Module Name: Google Map
* Module URI: http://www.siteeditor.org/modules/google-map
* Description: Google Map Module For Site Editor Application
* Author: Site Editor Team
* Author URI: http://www.siteeditor.org
* Version: 1.0.0
* @package SiteEditor
* @category Core
* @author siteeditor
*/
class PBGoogleMapShortcode extends PBShortcodeClass{
  
    static $sed_counter_id = 0;
    function __construct() {
        parent::__construct( array(
                "name"        => "sed_google_map",                     //*require
                "title"       => __("Google Map","site-editor"),       //*require for toolbar
                "description" =>  __("","site-editor"),
                "icon"        => "sedico-google-map",                    //*require for icon toolbar
                "module"      =>  "google-map"                              //*require
            ) // Args
        );

    }

    function get_atts(){

        $atts = array(                            
            'setting_address'               => __('500 Terry Francois Street, 6th Floor. San Francisco, CA 94158', 'site-editor'),
            'setting_description'           => __("Stars Ideas Office","site-editor"),
            'setting_type'                  => 'roadmap',
            'setting_width'                 => 100,  // %
            'setting_height'                => 300,  //px
            'setting_zoom'                  => 14,
            'setting_map_type_control'      => true,
            'setting_pan_control'           => true,
            'setting_street_view_control'   => true,
            'setting_scrollwheel'           => false,
            'setting_scale_control'         => false,
            'setting_overlay_color'         => '' ,
            "has_cover"                     => true
        );

        return $atts;
    }

    function add_shortcode( $atts , $content = null ){

        $atts['setting_url']  = SED_GOOGLE_MAP_MODULES_URL . '/google-map/sed-gmap-custom.php?api_key=' . get_theme_mod( 'sed_google_api_key' , '' );

        $atts['setting_base_url'] = includes_url();

        $atts['setting_module_url'] = SED_GOOGLE_MAP_MODULES_URL;

        $item_settings = "";

        foreach ( $atts as $name => $value) {
            if( substr( $name , 0 , 7 ) == "setting"){

                 $setting = substr( $name,8);
                 $setting = str_replace("_", "-", $setting );
                 if(is_bool($value) && $value === true){
                   $value = "true";
                 }elseif(is_bool($value) && $value === false){
                   $value = "false";
                 }
                 $item_settings .= 'data-gmap-'. $setting .'="'.$value .'" ';

            }
        }

        $this->set_vars(array(  "item_settings" => $item_settings ));

        self::$sed_counter_id++;
        $module_html_id = "sed_progress_bar_module_html_id_" . self::$sed_counter_id;

        $this->set_vars( array(
            "module_html_id"     => $module_html_id ,   
        ));
    }

    function scripts(){
        return array(
            array("gmap-handle" , SED_GOOGLE_MAP_MODULES_URL . "/google-map/js/gmap-handle.min.js",array("jquery" ,"underscore" ),'1.0.0',true)
        );
    }

    function shortcode_settings(){

        $options_zoom = array();
            for($i = 1 ; $i < 21 ; $i++)
            {
              $options_zoom[$i] = $i ;
            }


        $this->add_panel( 'google_map_settings_panel_outer' , array(
            'title'                   =>  __('Google Map Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'inner_box' ,
            'priority'                => 9 ,
            'btn_style'               => 'menu' ,
            'has_border_box'          => false ,
            'icon'                    => 'sedico-google-map' ,
            'field_spacing'           => 'sm'
        ) );

        $this->add_panel( 'google_map_settings_panel' , array(
            'title'                   =>  __('Google Map Settings',"site-editor")  ,
            'capability'              => 'edit_theme_options' ,
            'type'                    => 'default' ,
            'parent_id'               => "google_map_settings_panel_outer",
            'priority'                => 9 ,
        ) );

        $params = array(
            'setting_address' => array(
                'type' => 'textarea',
                'label' => __('Address', 'site-editor'),
                'description'  => __('Your physical address. To have multiple map markers, separate two addresses with a vertical line. For example, 579 Allen Road Basking Ridge, NJ 07920 | Mount Arlington, NJ 07856.', 'site-editor'),
                'priority'      => 8 ,
                'panel'    => 'google_map_settings_panel_outer',

            ),
            'setting_description' => array(
                'type' => 'textarea',
                'label' => __('Description', 'site-editor'),
                'description'  => __('', 'site-editor'),
                'priority'      => 8 ,
                'panel'    => 'google_map_settings_panel_outer',
            ),
            'setting_type' => array(
                'type' => 'select',
                'label' => __('Map Type', 'site-editor'),
                'description'  => __('Can be one of these values: roadmap, terrain, hybrid, or satellite. Sets the type of Google Map to display.', 'site-editor'),
                'choices'   =>array(
                    'roadmap' => __('Roadmap', 'site-editor'),
                    'satellite' => __('Satellite', 'site-editor'),
                    'hybrid' => __('Hybrid', 'site-editor'),
                    'terrain' => __('Terrain', 'site-editor'),
                ),
                'panel'    => 'google_map_settings_panel',
            ),
            'setting_zoom' => array(
                'type' => 'select',
                'label' => __('Zoom Level', 'site-editor'),
                'description'  => __('Accepts a numerical value that represents the map zoom level. The higher the number, the more it will zoom in.', 'site-editor'),
                'choices'   => $options_zoom,
                'panel'    => 'google_map_settings_panel',
            ),

            'setting_overlay_color' => array(
                'type'  => 'color',
                'label' => __('Color', 'site-editor'),
                'description'  => __('This option allows you to set whatever color you would like for the icons.', 'site-editor'),
                "panel"     => "google_map_settings_panel",
            ),

            'setting_width' => array(
                'type' => 'number',
                  'after_field'  => '%',
                'label' => __('Width', 'site-editor'),
                'description'  => __('Accepts a percentage value. For example 50%. Sets the map\'s width.', 'site-editor'),
                'panel'    => 'google_map_settings_panel',
            ),
            'setting_height' => array(
                'type' => 'number',
                  'after_field'  => 'px',
                'label' => __('Height', 'site-editor'),
                'description'  => __('Accepts a pixel value. For example 25px. Sets the map\'s height.', 'site-editor'),
                'panel'    => 'google_map_settings_panel',
            ),
            'setting_map_type_control' => array(
                'type' => 'checkbox',
                'label' => __('Show Control map type', 'site-editor'),
                'description'  => __('This feature allows you to select whether or not to show control map type in the map. (This feature allows your users, using this control, to convert the map type (including roadmap, terrain, hybrid, or satellite) to their desired option.) ', 'site-editor'),
                'choices'   =>'',
                'panel'    => 'google_map_settings_panel',
            ),
            'setting_pan_control' => array(
                'type' => 'checkbox',
                'label' => __('Show Pan Control on Map', 'site-editor'),
                'description'  => __('This feature allows you to select whether or not to show Pan Control in the map.', 'site-editor'),
                'choices'   =>'',
                'panel'    => 'google_map_settings_panel',
            ),
            'setting_street_view_control' => array(
                'type' => 'checkbox',
                'label' => __('Show Street view control', 'site-editor'),
                'description'  => __('This feature allows you to whether or not to show street view pegman control.', 'site-editor'),
                'choices'   =>'',
                'panel'    => 'google_map_settings_panel',
            ),
            'setting_scrollwheel' => array(
                'type' => 'checkbox',
                'label' => __('Scrollwheel on Map', 'site-editor'),
                'description'  => __("Enable zooming using a mouse's scroll wheel", 'site-editor'),
                'choices'   =>'',
                'panel'    => 'google_map_settings_panel',
            ),
            'setting_scale_control' => array(
                'type' => 'checkbox',
                'label' => __('Show Scale Control on Map', 'site-editor'),
                'description'  => __('This feature allows you to select whether or not to show Scale Control in the map.', 'site-editor'),
                'choices'   =>'',
                'panel'    => 'google_map_settings_panel',
            ),

            'row_container' => array(
                'type'                => 'row_container',
                'label'               => __('Module Wrapper Settings', 'site-editor')
            ), 

            "animation"  =>  array(
                "type"                => "animation" ,
                "label"               => __("Animation Settings", "site-editor"),
                'button_style'        => 'menu' ,
                'has_border_box'      => false ,
                'icon'                => 'sedico-animation' ,
                'field_spacing'       => 'sm' ,
                'priority'            => 530 ,
            )

            //'row_container' => 'row_container'
        );

        return $params;

    }
    
    function contextmenu( $context_menu ){
        $testimonial_menu = $context_menu->create_menu( "google-map" , __("Google Map","site-editor") , 'gmap' , 'class' , 'element' , '' , "sed_google_map" , array(
            "change_skin"   => false,
            "edit_style"  =>  false ,
        ) );
    }

}

new PBGoogleMapShortcode();
global $sed_pb_app;

$sed_pb_app->register_module(array(
    "group"       => "apps" ,
    "name"        => "google-map",
    "title"       => __("Google Map","site-editor"),
    "description" => __("","site-editor"), 
    "icon"        => "sedico-google-map",
    "shortcode"   => "sed_google_map",
    //"js_plugin"   => '',
    "js_module"   => array( 'sed_google_map_module_script', 'google-map/js/gmap-ui-editor.min.js', array('sed-frontend-editor') )
));

