<?php


namespace QNews\src\core\shortcode;


use QNews\src\services\data\TD_QNews_List;

class TD_QNews_Shortcode_Init
{
    private static $instance;
    private $_tables_data;

    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('init',array($this,'initialize'));
        $this->_tables_data = new TD_QNews_List();

    }

    function initialize()
    {
        $obj = new TD_QNews_ShortCode('td_quick_news');
    }

    public function addToContent($content)
    {
        $quick_news = $this->execute_shortcode('td_quick_news',array());
        $content.=$quick_news;
            return $content;
    }

    private function execute_shortcode($shortcode_tag, $atts, $content = null) {
        global $shortcode_tags;

        if(!isset($shortcode_tags[$shortcode_tag])) {
            return 'Shortcode doesn\'t exist';
        }

        if(is_array($shortcode_tags[$shortcode_tag])) {
            $shortcode_array = $shortcode_tags[$shortcode_tag];

            return call_user_func(array(
                $shortcode_array[0],
                $shortcode_array[1]
            ), $atts, $content, $shortcode_tag);
        }

        return call_user_func($shortcode_tags[$shortcode_tag], $atts, $content, $shortcode_tag);
    }

    function get_data()
    {
        echo $this->_tables_data->getData();
        die();
    }

}