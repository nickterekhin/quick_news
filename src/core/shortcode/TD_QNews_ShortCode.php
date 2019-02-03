<?php


namespace QNews\src\core\shortcode;


class TD_QNews_ShortCode extends TD_QNews_Shortcode_Base
{
    public function __construct($short_code_slug)
    {
        parent::__construct($short_code_slug);
        $this->_default_params  = array(

        );
    }


    function render($attr)
    {
        $this->_options = shortcode_atts($this->_default_params,$attr);

        return $this->View('quick_news',array('obj'=>$this,'params'=>$this->_options));
    }
}