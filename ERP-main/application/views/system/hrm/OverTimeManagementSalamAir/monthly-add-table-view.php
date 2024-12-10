<?php
$com_currency = $this->common_data['company_data']['company_default_currency'];
$com_currencyDPlace = $this->common_data['company_data']['company_default_decimal'];
$ot_systemInput = ot_systemInput();
$confirmedYN = $masterData['confirmedYN'];
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_over_time', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<style>
    .time-box-div{
        width : 75px !important;
    }
</style>
<div class="row" style="margin-bottom: 1%">
    <div class="col-sm-12">
        <div class="col-sm-10 col-xs-8">
            <?php
           $add_emp =  $this->lang->line('common_add_employee');
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
                <th rowspan="2">#</th>
                <th rowspan="2"><?php echo $this->lang->line('hrms_over_time_emp_code');?><!--Emp Code--></th>
                <th rowspan="2"><div style=""><?php echo $this->lang->line('hrms_over_time_employee_name');?><!--Employee Name--></div></th>
                <th rowspan="2" style="width: 100px;"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                <?php
                foreach($ot_systemInput as $inputRow){
                    $colSpan = ($inputRow['inputType'] == 1)? 'colspan="2"' : 'colspan="3"';
                    echo '<th '.$colSpan.'>'.$inputRow['inputDescription'].'</th>';
                }
                if( $confirmedYN != 1 ){
                    $hideStyle = ( count($details) > 0 )? 'block' : 'none';
                }
                else{
                    $hideStyle = 'none';
                }
                ?>
                <th rowspan="2" style="z-index:3">
                    <?php
                    if( $confirmedYN != 1 ){
                        $hideStyle = ( count($details) > 0 )? 'block' : 'none';
                    }
                    else{
                        $hideStyle = 'none';
                    }
                    ?>
                    <span class="glyphicon glyphicon-trash" id="removeAll_emp" style="color:rgb(209, 91, 71); display: <?php echo $hideStyle ?>"
                          onclick="removeAllEmp_OT()"></span>
                </th>
            </tr>
            <tr>
                <?php
            $Hours  = $this->lang->line('common_hours');
            $Amount  = $this->lang->line('common_amount');
            $Rate  = $this->lang->line('common_rate');

                foreach($ot_systemInput as $inputRow){
                    if($inputRow['inputType'] == 1){  echo '<th>'.$Hours.'<!--Hours--></th><th>'.$Amount.'</th>'; }
                    else{  echo '<th>'.$Hours.'<!--Hours--></th><th>'.$Rate.'</th><th>'.$Amount.'</th>'; }/*Rate*//*Amount*/
                }
                ?>
            </tr>
            </thead>

            <tbody>
            <?php
            $localAmount = 0;
            $disabled = ($confirmedYN == 1)? 'disabled' : '';
            if(!empty($details)) {
                foreach ($details as $key => $row) {
                    $empID = $row['empID'];
                    $tAmount = $row['transactionAmount'];
                    $dPlaces = $row['transactionCurrencyDecimalPlaces'];
                    $val = ($tAmount == 0) ? '' : number_format($tAmount, $dPlaces);
                    $localExchangeRate = $row['companyLocalExchangeRate'];
                    $localAmount +=  round($row['companyLocalAmount'], $row['companyLocalCurrencyDecimalPlaces']);

                    if($confirmedYN != 1){
                        $detailsRow = '<span class="glyphicon glyphicon-trash traceIcon" onclick="remove_emp_OT(this, '.$row['monthlyAdditionDetailID'].')"
                                style="color:rgb(209, 91, 71);"></span>
                                <input type="hidden" name="empHiddenID[]" class="recordTB_empID" value="' . $empID . '">
                                <input type="hidden" name="empCurrencyCode[]" class="empCurrencyCode" value="' . $row['transactionCurrency'] . '">
                                <input type="hidden" name="empCurrencyDPlace[]" class="empCurrencyDPlace" value="' . $dPlaces . '">';
                    }
                    else{
                        $detailsRow = '';
                    }


                    echo '<tr data-value="'.$row['ECode'].' '.$row['empName'].' '.$row['transactionCurrency'].'">
                    <td>' . ($key + 1) . '</td>
                    <td>' . $row['ECode'] . '</td>
                    <td><div style="width:170px !important">' . $row['empName'] . '</div></td>
                    <td style="text-align:center">' . $row['transactionCurrency'] . '</td>';
                    $isDisable = ($confirmedYN != 1) ? '' : 'disabled';
                    foreach($ot_systemInput as $inputRow){
                        $rateColumn = '';
                        $inputName = '';
                        $hours = '';
                        $amount = 0;
                        $isTotalBlock = 0;
                        switch($inputRow['systemInputID']){
                            case 1:
                                $rateColumn = $row['intHRhourlyRate'];
                                $inputName = 'intHRhourlyRate';
                                $hours = $row['intHRotHours'];
                                $amount = $row['intHRAmount'];
                                break;
                            case 2:
                                $rateColumn = $row['lclLyHRhourlyRate'];
                                $inputName = 'lclLyHRhourlyRate';
                                $hours = $row['lclLyHRotHours'];
                                $amount = $row['lclLYHRAmount'];
                                break;
                            case 3:
                                $rateColumn = $row['intLyhourlyRate'];
                                $inputName = 'intLyhourlyRate';
                                $hours = $row['intLyotHours'];
                                $amount = $row['intLyAmount'];
                                break;
                            case 4:
                                $rateColumn = $row['slabMasterID']; // SlabColumn
                                $inputName = 'totalblockHours';
                                $hours = $row['totalblockHours'];
                                $amount = $row['totalblockAmount'];
                                $isTotalBlock = 1;
                                break;
                        }


                        echo '<td style="text-align:center"> '.makeTimeTextBox_OT($key, $inputName, $rateColumn, $dPlaces, $isTotalBlock, $empID, $hours, $isDisable).'</td>';

                        $slabData = '';
                        if($inputRow['inputType'] != 1) {
                            echo '<td style="text-align:right">
                                 ' . number_format($rateColumn, $dPlaces) . '
                                 <input type="hidden" name="_' . $inputName . '[]" value="' . round($rateColumn, $dPlaces) . '"/>
                              </td>';
                        }else{
                            $slabData = '<input type="hidden" name="_slabID[]" id="slab_'.$inputName.'_'.$key.'" value="' . $rateColumn . '"/>';
                        }
                        echo '<td>
                                  <input type="text" name="amount_'.$inputName.'[]" id="amount_'.$inputName.'_'.$key.'" value="'.number_format($amount, $dPlaces).'"
                                  class="number" style="width:85px" readonly > '.$slabData.'
                              </td>';
                    }

                    echo '<td><div align="right" >' . $detailsRow . '</div></td>
                 </tr>';
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4">
                    Showing <span id="showingCount"> <?php echo count($details); ?> </span> of
                    <span id="totalRowCount"> <?php echo count($details); ?> </span> entries
                </td>
                <?php
                $colsCount = 0;
                foreach($ot_systemInput as $inputRowFoot){
                    if($inputRowFoot['inputType'] != 1){
                        echo '<td>&nbsp;</td><td>&nbsp;</td><td><span id=""></span></td>';
                    }else{
                        echo '<td>&nbsp;</td><td>&nbsp;</td>';
                    }
                }
                ?>
                <td>&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<div id="res_cal"></div>

<?php
?>



<script type="text/javascript">
    $('.select2').select2();

    $('#details_table').tableHeadFixer({
        head: true,
        foot: true,
        left: 4,
        right: 0
    });

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

        $('#details_table').tableHeadFixer({
            head: true,
            foot: true,
            left: 4,
            right: 0
        });

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

    function minutesValidate_OT(obj, obj_suffix, rate, dPlaces) {
        var thisVal = $.trim(obj.value);
        var convertedVal = ( $.isNumeric(thisVal) ) ? parseFloat(thisVal) : parseFloat(0);

        if (convertedVal > 59) {
            $(obj).val('');
        }
        calculateAmount(obj_suffix, rate, dPlaces);
    }

    function minutesValidateChange(obj) {
        var thisVal = $.trim(obj.value);
        var convertedVal = ( $.isNumeric(thisVal) ) ? parseFloat(thisVal) : parseFloat(0);

        if (convertedVal > 59) {
            $(obj).val('');
        }

        thisVal = $.trim(obj.value);
        convertedVal = ( $.isNumeric(thisVal) ) ? parseFloat(thisVal) : parseFloat(0);

        var str = '';
        switch (convertedVal.toString().length) {
            case  0:
                str = '00';
                break;

            case 1:
                str = '0';
                break;

            default:
                str = '';
        }

        $(obj).val(str + '' + convertedVal);
    }

    function calculateAmount(obj_suffix, rate, dPlaces){
        var hours = convertTONumber($('#h_'+obj_suffix).val());
        var minutes = convertTONumber($('#m_'+obj_suffix).val());

        var amount = (hours*rate) + ((rate/60)*minutes);
        $('#amount_'+obj_suffix).val(amount.toFixed(dPlaces));
    }

    function calculateBlockAmount(h_m, obj_suffix, empID, dPlaces){
        var hours = convertTONumber($('#h_'+obj_suffix).val());
        var minutes = convertTONumber($('#m_'+obj_suffix).val());

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'empID': empID, 'hours':hours, 'minutes':minutes, 'dPlaces': dPlaces},
            url: "<?php echo site_url('Employee/calculateOTBlockHours'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 'e'){
                    myAlert(data[0], data[1]);
                }
                else{
                    $('#amount_'+obj_suffix).val(data[1]);
                    $('#slab_'+obj_suffix).val(data[2]);
                }

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function convertTONumber(thisVal){
        return ( $.isNumeric(thisVal) ) ? parseFloat(thisVal) : parseFloat(0);
    }

</script>

<?php
