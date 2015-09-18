<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Kladr extends ORM
{
    protected $_table_name = 'kladr';

    protected $structure = [
        'name' => 'NAME',
        'socr' => 'SOCR',
        'code' => 'CODE',
        'index' => 'INDEX',
        'gninmb' => 'GNINMB',
        'uno' => 'UNO',
        'ocatd' => 'OCATD',
        'status' => 'STATUS',
    ];

    public function get_name()
    {
        return $this->_table_name;
    }

    public function get_model_name()
    {
        return str_replace('Model_', '', get_class($this));
    }

    public function get_structure()
    {
        return $this->structure;
    }

}