<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Excel extends Spreadsheet
{
    public function __construct()
    {
        parent::__construct();
    }
}