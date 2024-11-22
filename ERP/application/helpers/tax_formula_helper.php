<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



if (!function_exists('get_tax_type')) { /*get po action list*/
    function get_tax_type($con)
    {
        $status = '<center>';
           if ($con == 1) {
                $status .= 'Sales Tax';
            } else if ($con == 2) {
                $status .= 'Purchase Tax';
            }
        $status .= '</center>';
        return $status;
    }
}

if (!function_exists('formulaDecodeTax')) {
    function formulaDecodeTax($formula)
    {
        $salary_categories_arr = all_tax_formula();
        $operand_arr = operand_arr();
        $formulaText = '';

        $formula_arr = explode('|', $formula); // break the formula

        foreach ($formula_arr as $formula_row) {
            if (trim($formula_row) != '') {
                if (in_array($formula_row, $operand_arr)) { //validate is a operand

                    $formulaText .= '<li class="formula-li formula-operation" data-value="|' . $formula_row . '|" onclick="addSelectedClass(this)">';
                    $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                    $formulaText .= '<span class="formula-text-value">' . $formula_row . '</span></li>';
                } else {

                    $elementType = $formula_row[0];


                    if ($elementType == '_') {
                        /*** Number ***/
                        $numArr = explode('_', $formula_row);
                        $num = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];

                        $formulaText .= '<li class="formula-li formula-number" data-value="_' . $num . '_" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value" style="display:none">' . $num . '</span>';
                        $formulaText .= '<input type="text" class="formula-number-text" onkeyup="updateDataValue(this)" value="' . $num . '"></li>';
                    } else if ($elementType == '#') {
                        /*** Salary category ***/
                        $catArr = explode('#', $formula_row);
                        $keys = array_keys(array_column($salary_categories_arr, 'taxMasterAutoID'), $catArr[1]);
                        $new_array = array_map(function ($k) use ($salary_categories_arr) {
                            return $salary_categories_arr[$k];
                        }, $keys);

                        $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['taxDescription']) : '';

                        $formulaText .= '<li class="formula-li" data-value="#' . $catArr[1] . '" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value">' . $salaryDescription . '</span></li>';

                    }  else if ($elementType == '!') {
                        $monthlyADArr = explode('!', $formula_row);

                        if ($monthlyADArr[1]=='AMT'){
                            $description = 'Amount';
                        }else if($monthlyADArr[1]=='DIS'){ 
                            $description = 'Discount';
                        }else{ 
                            $description = 'Tax Percentage';
                        }

                        $formulaText .= '<li class="formula-li" data-value="!' . $monthlyADArr[1] . '" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value">' . $description . '</span></li>';

                    }
                }
            }
        }
        return $formulaText;
    }
}

if (!function_exists('all_tax_formula')) {
    function all_tax_formula()
    {
        $CI =& get_instance();
        $CI->db->SELECT("taxMasterAutoID,taxDescription");
        $CI->db->FROM('srp_erp_taxmaster');
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();

        return $data;
    }
}

if (!function_exists('taxChkbox')) {
    function taxChkbox($itemAutoID,$type,$taxCalculationformulaID)
    {
        $chkbox = '';
        if($type==$taxCalculationformulaID){
            //$chkbox = '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_'.$itemAutoID.'" onclick="ItemsSelectedSync(this,'.$type.','.$taxCalculationformulaID.')" name="checkedInvoice[]" type="checkbox" class="columnSelected"  value="'.$itemAutoID.'" checked><label for="checkbox">&nbsp;</label> </div></div></div>';
            $chkbox = '<div style="text-align: center;"><div class="skin skin-square"><div class="skin-section extraColumns"><input id="selectItem_'.$itemAutoID.'" type="checkbox" checked data-caption="" class="columnSelected check_customersall" onclick="ItemsSelectedSync(this)" name="checkedInvoice[]" value="'.$itemAutoID.'"></div></div></div>';
        }else{
            $chkbox = '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_'.$itemAutoID.'" onclick="ItemsSelectedSync(this)" name="checkedInvoice[]" type="checkbox" class="columnSelected check_customersall"  value="'.$itemAutoID.'" ><label for="checkbox">&nbsp;</label> </div></div></div>';
        }

        return $chkbox;
    }
}
if (!function_exists('get_tax_type_new')) { /*get po action list*/
    function get_tax_type_new($con)
    {
        $status = ' ';
           if ($con == 1) {
                $status .= 'Sales Tax';
            } else if ($con == 2) {
                $status .= 'Purchase Tax';
            }else { 
                $status .= '-';
            }
        return $status;
    }
}
if(!function_exists('load_tax_category_action')){ 
    function load_tax_category_action($taxCalculationformulaID,$taxType, $detailExist){ 
        $status = '<span class="pull-right">';
        if (!empty($taxType)) {
            $status .= '<a onclick=\'fetchPage("system/tax/tax_formula_edit",'.$taxCalculationformulaID.',"Edit Tax Formula Group","TAX"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<a onclick="assign_items('.$taxCalculationformulaID.','.$taxType.')"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link"></span></a>';
            if(empty($detailExist)) {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_formula('.$taxCalculationformulaID.')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
            }
        }else { 
            $status .= '-';
        }
        
        $status .= '</span>';

        return $status;
    }
}







