<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Kladr_Doma extends Model_Kladr
{
    protected $_table_name = 'kladr_doma';

    protected $structure = [
        'name' => 'NAME',
        'korp' => 'KORP',
        'socr' => 'SOCR',
        'code' => 'CODE',
        'index' => 'INDEX',
        'gninmb' => 'GNINMB',
        'uno' => 'UNO',
        'ocatd' => 'OCATD',
    ];
}