<?php

abstract class xydac_snippets
{

    public $name;
    public $label;
    public $description;
    public $type;
    public $options = '';
    public $order;
    public $contenttype = 'simple';
    public $vararray = array();

    public function __construct()
    {
        add_filter('xydac_cms_homeformoption', array($this, 'xydac_cms_homeformoption'), 10, 1);
    }

    public function xydac_cms_homeformoption($array)
    {
        array_push($array, array('name' => "xydac_ucms_form[$this->name]",
            'label' => $this->label,
            'description' => $this->description,
            'type' => $this->type,
            'options' => $this->options,
            'contenttype' => $this->contenttype,
            'array' => $this->vararray,
            'value' => xydac()->options->get($this->name)));
        return $array;
    }

    public function create_item($name, $label, $description, $type, $options = array())
    {

        $arr = array('name' => "xydac_ucms_form[$name]",
            'label' => $label,
            'type' => $type,
            'description' => $description,
            'options' => $options,
            'value' => xydac()->options->get($name));
        array_push($this->vararray, $arr);

    }

}
