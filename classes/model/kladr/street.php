<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Kladr_Street extends Model_Kladr
{
    protected $_table_name = 'kladr_street';

    protected $structure = [
        'name' => 'NAME',
        'socr' => 'SOCR',
        'code' => 'CODE',
        'index' => 'INDEX',
        'gninmb' => 'GNINMB',
        'uno' => 'UNO',
        'ocatd' => 'OCATD',
    ];
}