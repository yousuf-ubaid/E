<!--Translation added by Naseek-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$com_currency = $this->common_data['company_data']['company_default_currency'];
$com_currencyDPlace = $this->common_data['company_data']['company_default_decimal'];
$type = 'A';
$isNonPayroll = 1;
$dropDownData = declaration_drop($type, $isNonPayroll);
$confirmedYN = $masterData['det']['confirmedYN'];
$documentDate = $masterData['det']['documentDate'];

// $deductionType = 0;
// $monthlyDeductionMasterID = 0;

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

</style>
<div class="row" style="margin-bottom: 1%">
    <div class="col-sm-12">
        <div class="col-sm-10 col-xs-8">
            <?php
            $add_emp=  'Add New';
            if( $confirmedYN != 1){
                echo '<button type="button" class="btn btn-primary btn-sm saveBtn" onclick="load_employeeForModal()">
                        <i class="fa fa-file-text"></i> '.$add_emp.'<!--Add Employee-->
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
        <table class="<?php echo table_class(); ?>" id="details_table" style="margin-top: -1px; ">
            <thead>
            <tr>
                <th>#</th>
                <th><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--Emp Code--></th>
                <th><?php echo $this->lang->line('hrms_payroll_employee_name');?><!--Employee Name--></th>
                <th style="z-index:3"><?php echo $this->lang->line('hrms_payroll_grouping_type');?><!--Grouping Type--></th>
                <th style="width: 100px;"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                <th style="width: 100px"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                <th style="width: 130px"> <?php echo $this->lang->line('hrms_payroll_local_amount');?>&nbsp;(&nbsp;<?php echo $com_currency; ?>&nbsp;)&nbsp;</th>
                <th style="width: 100px;"><?php echo $this->lang->line('common_description');?></th>
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
            $monthlyDetailID = 'monthlyClaimDetailID' ;
            if(!empty($details)) {
                foreach ($details as $key => $row) {

                    $tAmount = $row['transactionAmount'];
                    $dPlaces = $row['transactionCurrencyDecimalPlaces'];
                    $val = ($tAmount == 0) ? '' : number_format($tAmount, $dPlaces);//
                    $payRate = $row['payRate'];
                    $payUnit = ($row['payUnit']) ? $row['payUnit'] : 1;//
                    $localExchangeRate = $row['companyLocalExchangeRate'];
                    $localAmount +=  round($row['companyLocalAmount'], $row['companyLocalCurrencyDecimalPlaces']);

                    if($confirmedYN != 1){
                        $detailsRow = '<span class="glyphicon glyphicon-trash traceIcon" onclick="removeEmpTB(this, '.$row[$monthlyDetailID].')"
                                    style="color:rgb(209, 91, 71);"></span>
                                    <input type="hidden" name="empHiddenID[]" class="recordTB_empID" value="' . $row['empID'] . '">
                                    <input type="hidden" name="empCurrencyCode[]" class="empCurrencyCode" value="' . $row['transactionCurrency'] . '">
                                    <input type="hidden" name="empCurrencyDPlace[]" class="empCurrencyDPlace" value="' . $dPlaces . '">
                                    <input type="hidden" name="empAccGroupID[]" class="empAccGroupID" value="' . $row['accessGroupID'] . '">';

                        $applyToAllStr = makeCopyBlock('applyToAllPosition1', 'applyToAllColsGroup');
                        $applyToAllStr1 = makeCopyBlock('applyToAllPosition2', 'applyToAllColsAmount');
                    }
                    else{
                        $detailsRow = '';
                        $applyToAllStr = '';
                        $applyToAllStr1 = '';
                    }

                    echo '<tr data-value="'.$row['ECode'].' '.$row['empName'].' '.$row['transactionCurrency'].'">
                        <td>' . ($key + 1) . '</td>
                        <td>' . $row['ECode'] . '</td>
                        <td>' . $row['empName'] . '</td>
                        <td class="tdCol">' . make_dropDown($dropDownData, $row['declarationID'], $disabled, $key) . ' '.$applyToAllStr.'</td>
                        <td style="text-align:center">' . $row['transactionCurrency'] . '</td>';

                    echo '<td class="tdCol">
                            <input type="text" name="amount[]" class="trInputs number amount" id="amount_' . $key . '"  value="' . $val . '" '.$disabled.'
                             onkeyup="empAmount(this, \'' . $key . '\', \'' . $localExchangeRate . '\')" onchange="formatAmount(this, \'' . $dPlaces . '\')">
                        </td>';

                     echo '<td>
                            <div align="right" class="localAmount" id="amountSpan_'.$key.'">
                                '.number_format($row['companyLocalAmount'], $row['companyLocalCurrencyDecimalPlaces']).'
                            </div>
                        </td>
                        <td style="text-align:right;">
                            <textarea class="form-control" name="description[]">'.$row['description'].'</textarea>
                        </td>
                        <td><div align="right" >' . $detailsRow . '</div></td>
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
                <td colspan="2" align="right"><?php echo $this->lang->line('hrms_payroll_total_amount');?><!--Total Amount--></td>
                <td align="right"><span id="totalAmount"><?php echo number_format($localAmount, $com_currencyDPlace)?></span></td>
                <td>&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>


<script type="text/javascript">
    $('#segment-arr').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $('.select2').select2();

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

    function applyToAllColsGroup(element) {
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
            var thisNum = $(element).closest('td').find('select').attr('id');
            var thisGroupID = $('#'+thisNum).val();
            thisNum = thisNum.split('_');
            thisNum = parseInt(thisNum[1]);

            while(maxLength > thisNum ){
                thisNum++;
                var thisTD = $('#groupDrop_'+thisNum);
                var isHideTR = $(thisTD).closest('tr').hasClass('hideTr');

                if( isHideTR == false){
                    $(thisTD).val(thisGroupID).change();
                }
            }
        });
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
            var thisNum_str = $(element).closest('td').find('input').attr('id');
            var thisAmount = $('#'+thisNum_str).val();
            var thisNum_str2 = thisNum_str.split('_');
            var thisNum = parseInt(thisNum_str2[1]);
            var apply_field = thisNum_str2[0];
            

            if(apply_field == 'amount'){
                while(maxLength > thisNum ){
                    thisNum++;
                    var thisTD = $('#amount_'+thisNum);
                    var isHideTR = $(thisTD).closest('tr').hasClass('hideTr');

                    if( isHideTR == false){
                        $(thisTD).val(thisAmount).keyup();
                    }
                }
            }else if(apply_field == 'amountrate'){
                while(maxLength > thisNum ){
                    thisNum++;
                    var thisTD = $('#amountrate_'+thisNum);
                    var isHideTR = $(thisTD).closest('tr').hasClass('hideTr');

                    if( isHideTR == false){
                        $(thisTD).val(thisAmount).keyup();
                    }
                }
            }else if(apply_field == 'noofunits'){
                while(maxLength > thisNum ){
                    thisNum++;
                    var thisTD = $('#noofunits_'+thisNum);
                    var isHideTR = $(thisTD).closest('tr').hasClass('hideTr');

                    if( isHideTR == false){
                        $(thisTD).val(thisAmount).keyup();
                    }
                }
            }
           
        });
    }

    function formatCalculateAmount(element){

        var rate = $(element).closest('tr').find('.amountrate').val();
        var units = $(element).closest('tr').find('.noofunits').val();
        var target_amount = $(element).closest('tr').find('.amount');

        target_amount.val(rate*units).change().trigger('onkeyup');

    }

    function generate_no_pay_records(){

        var monthlyDeductionMasterID = <?php echo $monthlyDeductionMasterID ?>;

        swal({
            title: "Are you sure?",
            text: "You want re generate no pay data",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Confirm"
        },
        function () {
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: { 'monthlyDeductionMasterID': monthlyDeductionMasterID },
                    url: "<?php echo site_url('Employee/generate_monthlyDeduction_Nopay'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        setTimeout(function(){
                            loadDetail_table()
                        }, 300);

                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                 });

        });

    }


</script>

<?php
