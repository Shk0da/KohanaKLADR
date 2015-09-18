<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Kladr_Socr extends Model_Kladr
{
    protected $_table_name = 'kladr_socr';

    protected $structure = [
        'LEVEL' => 'LEVEL',
        'SCNAME' => 'SCNAME',
        'SOCRNAME' => 'SOCRNAME',
        'KOD_T_ST' => 'KOD_T_ST',
    ];
}