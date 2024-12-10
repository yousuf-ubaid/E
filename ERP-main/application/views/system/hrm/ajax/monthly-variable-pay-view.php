<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$com_currency = $this->common_data['company_data']['company_default_currency'];
$com_currencyDPlace = $this->common_data['company_data']['company_default_decimal'];
$type = ( $this->input->post('type_m') == 'MA')? 'A' : 'D' ;
$isNonPayroll = ($masterData['isNonPayroll'] == 'Y' )? 2 : 1;
$dropDownData = declaration_drop($type, $isNonPayroll);
$confirmedYN = $masterData['confirmedYN'];
$segment_arr = fetch_segment(true, false);

if($isGroupAccess == 1){
    if($totalEntries != count($details)){
        $confirmedYN = 1;
        if($masterData['confirmedYN'] != 1){
            echo '<script type="text/javascript"> msg_popup("saveBtn"); </script>';
        }

    }
}
?>

<style>
    .arrowDown {
        vertical-align: sub;
        font-size: 13px;
    }

    .applyToAll {
        display: none;
        vertical-align: top;
        position: absolute;
    }

    .applyToAllPosition1 { margin-top: -27px; }

    .applyToAllPosition2 { margin-top: -20px; }

    .applyToAllPosition1 .btn-xs { padding: 3px 3px; }

    .applyToAllPosition2 .btn-xs { padding: 0px 3px; }

    .unit-input{ text-align: right }
</style>

<div class="row" style="margin-bottom: 1%">
    <div class="col-sm-12">
        <div class="col-sm-10 col-xs-8">
            <?php
            $add_emp=  $this->lang->line('hrms_payroll_add_employee');
            if( $confirmedYN != 1){
                echo '<button type="button" class="btn btn-primary btn-sm saveBtn" onclick="openEmployeeModal()">
                        <i class="fa fa-fw fa-user"></i> '.$add_emp.'<!--Add Employee-->
                      </button>';
            }
            ?>
        </div>
        <div class="col-sm-2 col-xs-4">
            <input type="text" class="form-control" id="searchItem" value="" placeholder="<?php echo $this->lang->line('common_search');?>"><!--Search-->
        </div>
    </div>
</div>

<div class="table-responsive">
    <div style="height: 500px">
        <table class="<?php echo table_class(); ?>" id="details_table" style="margin-top: -1px; /*margin-bottom: -1px*/">
            <thead>
            <tr>
                <th>#</th>
                <th><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--Emp Code--></th>
                <th><?php echo $this->lang->line('hrms_payroll_employee_name');?><!--Employee Name--></th>
                <th style="width: 100px;"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                <th style="width: 100px;"><?php echo $this->lang->line('hrms_payroll_declaration_amount');?></th>
                <th style="width: 100px"><?php echo $this->lang->line('common_no_of_unit');?><!--Amount--></th>
                <th style="width: 100px"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                <th style="width: 100px;"><?php echo $this->lang->line('hrms_payroll_ex_rate');?><!--Ex.Rate--></th>
                <th style="width: 130px"> <?php echo $this->lang->line('hrms_payroll_local_amount');?><!--Local&nbsp;Amount-->&nbsp;(&nbsp;<?php echo $com_currency; ?>&nbsp;)&nbsp;</th>
                <th style="z-index:3">
                    <?php
                    if( $confirmedYN != 1 ){
                        $hideStyle = ( count($details) > 0 )? 'block' : 'none';
                    }
                    else{
                        $hideStyle = 'none';
                    }
                    ?>
                    <span class="glyphicon glyphicon-trash" id="removeAll_emp" style="color:rgb(209, 91, 71); display: <?php echo $hideStyle ?>"
                          onclick="removeAll_emp()"></span>
                </th>
            </tr>
            </thead>

            <tbody>
            <?php
            $localAmount = 0;
            $disabled = ($confirmedYN == 1)? 'disabled' : '';
            $monthlyDetailID = ( $this->input->post('type_m') == 'MA')? 'monthlyAdditionDetailID' : 'monthlyDeductionDetailID' ;
            if(!empty($details)) {
                foreach ($details as $key => $row) {

                    $tAmount = $row['transactionAmount'];
                    $decAmount = $row['declarationAmount'];
                    $dPlaces = $row['transactionCurrencyDecimalPlaces'];
                    $val = ($tAmount == 0) ? '' : number_format($tAmount, $dPlaces);
                    $localExchangeRate = $row['companyLocalExchangeRate'];
                    $localAmount +=  round($row['companyLocalAmount'], $row['companyLocalCurrencyDecimalPlaces']);

                    if($confirmedYN != 1){
                        $detailsRow = '<span class="glyphicon glyphicon-trash traceIcon" onclick="removeEmpTB(this, '.$row[$monthlyDetailID].')"
                                        style="color:rgb(209, 91, 71);"></span>
                                        <input type="hidden" name="empHiddenID[]" class="recordTB_empID" value="' . $row['empID'] . '">                                        
                                        <input type="hidden" name="empCurrencyCode[]" class="empCurrencyCode" value="' . $row['transactionCurrency'] . '">
                                        <input type="hidden" name="empCurrencyDPlace[]" class="empCurrencyDPlace" value="' . $dPlaces . '">
                                        <input type="hidden" name="empAccGroupID[]" class="empAccGroupID" value="' . $row['accessGroupID'] . '">
                                        <input type="hidden" name="categoryID[]" class="categoryID_empID" value="' . $row['categoryID'] . '">
                                        <input type="hidden" name="decDetID[]" class="decDetID_empID" value="' . $row['declarationDetID'] . '">
                                        <input type="hidden" name="decAmount[]" class="decAmount_empID" value="' . $row['declarationAmount'] . '">';

                        $applyToAllStr1 = makeCopyBlock('applyToAllPosition2', 'applyToAllColsAmount');
                    }
                    else{
                        $detailsRow = '';
                        $applyToAllStr1 = '';
                    }


                    echo '<tr data-value="'.$row['ECode'].' '.$row['empName'].' '.$row['transactionCurrency'].'">
                            <td>' . ($key + 1) . '</td>
                            <td>' . $row['ECode'] . '</td>
                            <td>' . $row['empName'] . '</td>
                            <td style="text-align:center">' . $row['transactionCurrency'] . '</td>
                            <td class="tdCol">
                                <div align="right"> '.number_format($row['declarationAmount'], $dPlaces).' </div>
                            </td>                   
                            <td class="tdCol">
                                <input type="text" name="unit[]" class="trInputs unit-input" id="unit_' . $key . '"  value="' . $row['noOfUnits'] . '" '.$disabled.'
                                 onkeyup="uniteUpdate(this, \'' . $key . '\', \''.$decAmount.'\', \''.$dPlaces.'\', \'' . $localExchangeRate . '\')"  >
                                '.$applyToAllStr1.'
                            </td>
                            <td class="tdCol">
                                <input type="hidden" name="amount[]" id="amount_' . $key . '"  value="' . $val . '" >
                                <div align="right" id="emp_amountSpan_'.$key.'">
                                    '.number_format($tAmount, $dPlaces).'
                                </div>
                            </td>
                            <td style="text-align:right;">' . $localExchangeRate . '</td>
                            <td>
                                <div align="right" class="localAmount" id="amountSpan_'.$key.'">
                                    '.number_format($row['companyLocalAmount'], $row['companyLocalCurrencyDecimalPlaces']).'
                                </div>
                            </td>
                            <td><div align="right" >' . $detailsRow . '</div></td>
                         </tr>';
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5">
                    Showing <span id="showingCount"> <?php echo count($details); ?> </span> of
                    <span id="totalRowCount"> <?php echo count($details); ?> </span> entries
                </td>
                <td colspan="3" align="right"><?php echo $this->lang->line('hrms_payroll_total_amount');?><!--Total Amount--></td>
                <td align="right"><span id="totalAmount"><?php echo number_format($localAmount, $com_currencyDPlace)?></span></td>
                <td>&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>



<script type="text/javascript">
    $('.select2').select2();

    $('.unit-input').numeric();

    $('#details_table').tableHeadFixer({
        head: true,
        foot: true,
        left: 1,
        right: 0
    });

    $(document).ready(function () {
        $(".tdCol").hover(function () {
            $(".applyToAll").hide();
            $(this).closest('td').find('span').show()
        })
    });

    function uniteUpdate(det, info, decAmount, dPlace, exchangeRate){

        var unit = $.trim(det.value);
        if(unit.length == 1){
            if(unit == '-'){
                unit = 0;
            }
        }
        var amount = (unit == '')? 0: unit;
        amount = unit * decAmount;

        var localAmount = amount / parseFloat(exchangeRate);
        $('#amount_'+info).val( commaSeparateNumber( amount, dPlace) );
        $('#emp_amountSpan_'+info).text( commaSeparateNumber( amount, dPlace) );
        $('#amountSpan_'+info).text( commaSeparateNumber( localAmount, dPlace) );
        setTimeout(function(){  getTotalAmount(); },100);
    }

    function getNumberAndValidate(thisVal, dPlace=2) {
        thisVal = $.trim(thisVal);
        thisVal = parseFloat(thisVal.replace(/,/g, ""));
        thisVal = thisVal.toFixed(dPlace);

        if ($.isNumeric(thisVal)) {
            return parseFloat(thisVal);
        }
        else {
            return parseFloat(0);
        }
    }

    $('#searchItem').keyup(function () {

        var searchKey = $.trim($(this).val()).toLowerCase();
        var tableTR = $('#details_table tbody>tr');
        tableTR.removeClass('hideTr evenTR oddTR');

        tableTR.each(function () {
            var dataValue = '' + $(this).attr('data-value') + '';
            dataValue = dataValue.toLocaleLowerCase();

            if (searchKey != '') {
                if (dataValue.indexOf('' + searchKey + '') == -1) {
                    $(this).addClass('hideTr');
                }
            }
            else {

            }
        });

        applyRowNumbers();
        getTotalAmount();

    });

    function applyRowNumbers() {
        var m = 1;
        $('#details_table tbody>tr').each(function (i) {
            if (!$(this).hasClass('hideTr')) {
                var isEvenRow = ( m % 2 );
                if (isEvenRow == 0) {
                    $(this).addClass('evenTR');
                } else {
                    $(this).addClass('oddTR');
                }

                $(this).find('td:eq(0)').html(m);
                m += 1;
            }
        });

        $('#showingCount').text((m - 1));
    }

    function applyToAllColsAmount(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var maxLength = ($('#details_table tbody tr').length) - 1;
                var thisNum = $(element).closest('td').find('input').attr('id');
                var thisAmount = $('#'+thisNum).val();
                thisNum = thisNum.split('_');
                thisNum = parseInt(thisNum[1]);

                while(maxLength > thisNum ){
                    thisNum++;
                    var thisTD = $('#amount_'+thisNum);
                    var isHideTR = $(thisTD).closest('tr').hasClass('hideTr');

                    if( isHideTR == false){
                        $(thisTD).val(thisAmount).keyup();
                    }
                }
            });
    }

</script>

<?php
