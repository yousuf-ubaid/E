<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>
<?php if($isFromPrint == 'Y') {
    ?>


    <div class="table-responsive">
        <table style="width: 100%" border="0px">
            <tbody>
            <tr>
                <td style="width:40%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px"
                                     src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:60%;" valign="top">
                    <table border="0px">
                        <tr>
                            <td colspan="2">
                                <h2>
                                    <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                                </h2>
                            </td>
                        </tr>
                        <tr>
                            <td><h4 style="margin-bottom: 0px"><?php echo $this->lang->line('hrms_payroll_pay_sheet'); ?><!--Pay Sheet--></td>
                        </tr>
                        <tr>
                            <?php $date = $masterData['payrollYear'] . "-" . $masterData['payrollMonth'] . "-01" ?>
                            <td colspan="2"><h4 style="margin-bottom: 0px"><?php echo $this->lang->line('hrms_payroll_period'); ?><!--Period-->
                                    - <?php echo date('F ` Y', strtotime($date)); ?></h4></td>
                            <!--<td align="right"> <h4 style="margin-bottom: 0px"><?php /*echo  date('Y `F',  strtotime($date )); */ ?> </h4></td>-->
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr>

<?php
}
$rowNo = 0;
$viewTD = '<th class="theadtr">#</th>';

/**** Adding a new column in the array to saw the  net salary ***/
/*$header_det[$totalTDCount]['detailType'] = 'T';
$header_det[$totalTDCount]['columnName'] = 'netSalary';
$header_det[$totalTDCount]['captionName'] = 'Net Salary';*/

$isDeleteColumn = ($isFromPrint != 'Y' && $masterData['confirmedYN'] != 1)? 'Y' : 'N';

$nonZeroColumns = $paysheetData['nonZeroColumns'];
$empDet = $paysheetData['empDet'];
$headerColumns = array();

foreach($header_det as $key=>$det){
    /**** Making the header [th] ****/


    if( $det['detailType'] == 'H'){
        $viewTD .= '<th class="theadtr">'.$det['captionName'].'</th>';
        if( $det['isCalculate'] == 1){
            $header_det[$key]['sum'] = 0;
            $headerColumns[] =  $header_det[$key];
        }else{
            $headerColumns[] = $det;
        }

    }
    else{  /**** adda new column for save the total amount except header type  ****/

        if( in_array($det['groupKey'], $nonZeroColumns) ){
            $viewTD .= '<th class="theadtr">'.$det['captionName'].'</th>';
            $header_det[$key]['sum'] = 0;
            $headerColumns[] =  $header_det[$key];
        }
    }
}
$totalTDCount = count($headerColumns) + 1;
$totalTDCount += ($isDeleteColumn != 'Y')? 1 : 0;


?>

<h5 class="selected-employee-det well well-sm" style="display: none; margin-bottom: -7px;"></h5>

    <div class="fixHeader_Div" style="margin-top: 1%"><!--margin-top: 3%-->
        <table class="<?php echo table_class();?>" id="paysheet-tb" style="margin-top: -3px">
        <thead>
        <tr>
            <?php
            if($isDeleteColumn == 'Y') {
                $viewTD.= '<th style="z-index: 9999"></th>';
            }
            echo $viewTD;
            ?>
        </tr>
        </thead>
        <?php
        $currencyCount = count($currency_groups);
        $isClosingTag_tFootOpen = ($currencyCount > 1)? '' : '<tfoot>';
        $isClosingTag_tFootClose = ($currencyCount > 1)? '' : '</tfoot>';
        foreach( $currency_groups as $groupKey=>$group ){ /**** write the currency header ****/
        $dPlace = $group['dPlace'];
        ?>
        <tr class="theadtr" style="font-size:12px">
            <th style="width: auto" colspan="<?php echo ($totalTDCount+2) ?>"> <strong><?php echo $this->lang->line('common_currency'); ?><!--Currency--> :  </strong> </th>
        </tr>

        <?php
            foreach( $empDet as $key=>$det ){  /**** Loop the employee details ****/

                $thisEmpDet = $det['empDet'];
                $thisEmpSalaryDet = $det['empSalDec'];
                $thisPayCurr = $thisEmpDet['payCurrency'];
                $val = '';
                $rowNo = $key;
                $empTr = '<td align="right">'.$key.'</td>';

                if( $thisPayCurr == $group['currency'] ) {
                    $empData = $thisEmpDet['ECode'].'&nbsp;&nbsp;'.$thisEmpDet['Ename2'];
                    foreach ($headerColumns as $key => $row) {
                        if ($row['detailType'] == 'H') {

                            if( $row['isCalculate'] == 1 ){
                                $headerColumns[$key]['sum'] += round($thisEmpDet[ $row['fieldName'] ], $dPlace);
                                $empTr .= '<td align="right">'. format_number($thisEmpDet[ $row['fieldName'] ], $dPlace) . '</td>';
                            }else{
                                switch($row['fieldName']){
                                    case 'Ename2':
                                    case 'bankDetails':
                                        $data = '<div style="width:150px">'.$thisEmpDet[ $row['fieldName'] ].'</div>';
                                        break;
                                    case 'comments':
                                        if($isFromPrint == 'Y' || $masterData['confirmedYN']){
                                            $data = $thisEmpDet[ 'payComments' ];
                                        }else{
                                            $data = '<div style="width:150px">
                                                        <input type="text" value="'.$thisEmpDet[ 'payComments' ].'"
                                                                onchange="commentUpdate(this,\''.$thisEmpDet[ 'payrollHeaderDetID' ].'\')"/>
                                                     </div>';
                                        }
                                        break;
                                    default :
                                        $data = $thisEmpDet[ $row['fieldName'] ];
                                }

                                $empTr .= '<td>' . $data . '</td>';
                            }
                        }
                        else if ($row['detailType'] == 'A' || $row['detailType'] == 'D') {
                            /************************************************************************************
                             * If 'fieldName' name equal to 'MA'=> Monthly Addition or 'MD' => Monthly Deduction
                             * than the searching key will be 'MA' OR 'MD'
                             ************************************************************************************/

                            $searchingKey = ($row['fieldName'] == 'MA' OR $row['fieldName'] == 'MD' OR $row['fieldName'] == 'LO')? $row['fieldName'] : $row['catID'];
                            $val = search_paysheetEmpDetails($thisEmpSalaryDet, $searchingKey);
                            $headerColumns[$key]['sum'] += $val;

                            $empTr .= '<td align="right">'. format_number($val, $dPlace) . '</td>';
                        }
                        else if ($row['detailType'] == 'G') {
                            $searchingKey = 'G_'.$row['payID'];
                            $val = search_paysheetEmpDetails($thisEmpSalaryDet, $searchingKey);
                            $headerColumns[$key]['sum'] += $val;

                            $empTr .= '<td align="right">' . format_number($val, $dPlace). '</td>';
                        }
                        else if ($row['detailType'] == 'T') {
                            $empTr .= '<td align="right">' . format_number($det['netSalary'], $dPlace). '</td>';
                            $headerColumns[$key]['sum'] += $det['netSalary'];
                        }
                        else if ($row['detailType'] == 'T') {
                            $searchingKey = $row['catID'];
                            $val = search_paysheetEmpDetails($thisEmpSalaryDet, $searchingKey);
                            $headerColumns[$key]['sum'] += $val;

                            $empTr .= '<td align="right">'. format_number($val, $dPlace) . '</td>';
                        }

                    }

                    if($isDeleteColumn == 'Y') {
                        $payrollID = $this->input->post('hidden_payrollID');
                        $empTr.= '<td>
                                    <a onclick="delete_PayrollEmp(this,\''.$thisEmpDet['E_ID'].'\', \''.$payrollID.'\')">
                                        <span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span>
                                    </a>
                                  </td>';
                    }

                    echo '<tr data-value="'.$empData.'">'.$empTr.'</tr>';
                }

            }
        ?>

        <?php echo $isClosingTag_tFootOpen; ?>
            <tr>
                <td>&nbsp;</td>
                <?php
                $dPlace = 3;
                array_walk($headerColumns, function(&$value, $i) use ($isFromPrint,$dPlace){

                    if( array_key_exists('sum', $value) ) {
                        echo '<td class="total t-foot" align="right">'.format_number($value['sum'], $dPlace).'</td>';
                        $value['sum']=0;
                    }
                    else {
                        $class = ($isFromPrint == 'Y')? 't-foot' : '';
                        echo '<td class="'.$class.'" align="right"></td>';
                    }
                });

                if($isDeleteColumn == 'Y') {
                    echo '<td>&nbsp;</td>';
                }
                ?>
            </tr>
        <?php echo $isClosingTag_tFootClose; ?>
        <?php
        }

        ?>
    </table>
    </div>

<?php
$isGroupAccess = getPolicyValues('PAC', 'All');
if($isGroupAccess == 1){
    if($paysheetData['empCount'] != $rowNo){
        echo '<script type="text/javascript"> msg_popup("pay-save-btn"); </script>';
    }
}
?>
<h5 class="selected-employee-det well well-sm" style="display: none;"></h5>

<script>
    var selected_employee_det = $('.selected-employee-det');

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');


        var curEmp = $(this).attr('data-value');
        if(curEmp != undefined){
            selected_employee_det.css('display', 'block');
            selected_employee_det.html(curEmp);
        }
        else{
            selected_employee_det.css('display', 'none');
        }
    });
</script>
<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-08-16
 * Time: 5:34 PM
 */