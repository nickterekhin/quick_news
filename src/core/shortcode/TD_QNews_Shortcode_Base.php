<?php


namespace QNews\src\core\shortcode;


abstract class TD_QNews_Shortcode_Base
{

    protected $_short_code_slug;
    protected $_default_params=array();
    protected $_options = array();
    /**
     * TD_Q_News_Shortcode_Base constructor.
     * @param $short_code_slug
     */
    public function __construct($short_code_slug)
    {
        $this->_short_code_slug = $short_code_slug;
        add_shortcode($short_code_slug,array($this,'render'));
    }

    abstract function render($attr);

    function View($viewName, array $params=array())
    {

        if(is_array($params) && count($params)) {
            extract($params);
        }
        $file = TD_QNEWS_PLUGIN_DIR . '/src/view/'. $viewName . '.php';

        ob_start();
        include( $file );
        $ret_obj= ob_get_clean();


        return $ret_obj;
    }
}