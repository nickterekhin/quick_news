<?php


namespace QNews\src\core\init;


use QNews\src\core\shortcode\TD_QNews_Shortcode_Init;

class TD_QNews_Init
{
    private static $instance;
    private $_shortcode;

    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->_shortcode = TD_QNews_Shortcode_Init::getInstance();
    }
    function init()
    {
        add_action('wp_enqueue_scripts',array($this,'init_front_resources'),12);
        //add_action('qode_after_wrapper_inner',array($this,'render_short_code'));



            add_action('wp_ajax_get_quick_news_data', array($this->_shortcode, 'get_data'));

            add_action('wp_ajax_nopriv_get_quick_news_data', array($this->_shortcode, 'get_data'));


        //add_action('wp_ajax_nopriv_get_quick_news_data',array($this->_shortcode,'get_data'));
        //add_action('wp_ajax_get_quick_news_data',array($this->_shortcode,'get_data'));
       /* if(is_admin())
        {
            add_action('admin_menu', array($this,'init_admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'init_admin_resources'),12);
        }*/

    }

    function init_front_resources()
    {

        wp_enqueue_style( 'td_qn-tables', TD_QNEWS_PLUGIN_URL. 'assets/css/libs/datatables.min.css' );
        // wp_enqueue_style( 'td_qn-icons', TD_QNEWS_PLUGIN_URL. '/assets/css/libs/all.min.css' );
        wp_enqueue_style( 'td_qn-styles', TD_QNEWS_PLUGIN_URL. 'assets/css/qn_styles.css' );

       //wp_enqueue_script( 'td_qn-jquery', TD_QNEWS_PLUGIN_URL. '/assets/js/jquery-3.3.1.min.js' );
        wp_enqueue_script( 'td_qn-jquery-ui', TD_QNEWS_PLUGIN_URL. 'assets/js/ui/jquery-ui.min.js' );
        wp_enqueue_script( 'td_qn-notify', TD_QNEWS_PLUGIN_URL. 'assets/js/notify.js',array('jquery'));
        wp_enqueue_script( 'td_qn-tables-js', TD_QNEWS_PLUGIN_URL. 'assets/js/datatables.js' );
        wp_enqueue_script( 'td_qn-custom-tables-js', TD_QNEWS_PLUGIN_URL. 'assets/js/td_qn-tables.js' );
        wp_enqueue_script( 'td_qn-custom', TD_QNEWS_PLUGIN_URL. 'assets/js/td-qn-app.js' );


    }
    function render_short_code($content)
    {
        $content = $this->_shortcode->addToContent($content);

         echo $content;
    }
}