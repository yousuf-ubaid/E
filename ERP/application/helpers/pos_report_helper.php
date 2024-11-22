<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('till_report_numberFormat')) {
    function till_report_numberFormat($amount)
    {
        $decimal = get_company_currency_decimal();
        if ($amount > 0) {
            $output = '<div class="pull-right">' . number_format($amount, $decimal) . '</div>';
        } else {
            $output = '<div class="pull-right">' . $amount . '</div>';
        }
        return $output;
    }
}

if (!function_exists('till_report_numberFormat_dif')) {
    function till_report_numberFormat_dif($amount)
    {
        $decimal = get_company_currency_decimal();
        if ($amount < 0) {
            $output = '<div class="pull-right text-red"><strong>' . number_format($amount, $decimal) . '</strong></div>';
        } else if ($amount > 0) {
            $output = '<div class="pull-right"><strong>' . number_format($amount, $decimal) . '</strong></div>';

        } else {
            $output = '<div class="pull-right">' . $amount . '</div>';
        }
        return $output;
    }
}




