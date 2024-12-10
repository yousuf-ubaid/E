<!--Translation added by Naseek-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$com_currency = $this->common_data['company_data']['company_default_currency'];
$com_currencyDPlace = $this->common_data['company_data']['company_default_decimal'];
$type = ( $this->input->post('type_m') == 'MA')? 'A' : 'D' ;
$isNonPayroll = ($masterData['isNonPayroll'] == 'Y' )? 2 : 1;
if($masterData['pullType'] == 1){
    $dropDownData = declaration_drop($type, $isNonPayroll,$isVariable,1);
}else{
    $dropDownData = declaration_drop($type, $isNonPayroll,$isVariable);
}

$group_select = array();
foreach($dropDownData as $groups){
    $group_select[$groups['monthlyDeclarationID']] = $groups['monthlyDeclaration'];
}

$confirmedYN = $masterData['confirmedYN'];

$deductionType = 0;
$monthlyDeductionMasterID = 0;

if($type == 'D'){
    $deductionType = $masterData['deductionType'];
    $monthlyDeductionMasterID = $masterData['monthlyDeductionMasterID'];
}

$segment_arr = fetch_segment(true, false);

if($isGroupAccess == 1){
    if($totalEntries != count($details)){
        $confirmedYN = 1;
        if($masterData['confirmedYN'] != 1){
            echo '<script type="text/javascript"> msg_popup("saveBtn"); </script>';
        }

    }
}

$disabled_readonly = '';
if($masterData['pullType'] == 1){
    $disabled_readonly .= ' readonly ';
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

    .drop_va{}

</style>

<div class="row" style="margin-bottom: 1%">
    <div class="col-sm-12">
        <div class="col-sm-10 col-xs-8">
            <?php
            $add_emp=  $this->lang->line('hrms_payroll_add_employee');
            $uploadExcel =  $this->lang->line('hrms_payroll_excel_upload');
            $downloadExcel =  $this->lang->line('hrms_payroll_excel_download');
            $downloadUrl =  site_url('Employee/download_csv');
            $add_update=  $this->lang->line('hrms_payroll_update_attandance');
            
            if( $confirmedYN != 1){
                if($masterData['pullType'] == 1){
                    $bar = '<div class="row">';
                    $bar .= '<div class="col-md-3">
                          <button type="button" class="btn btn-primary btn-sm updatePull" id="updatePull" onclick="updateAttandanceRelatedRecords()">
                            <i class="fa fa-fw fa-user"></i> '.$add_update.'<!--Add Employee-->
                          </button></div>';

                    $bar .= '<div class="col-md-3">
                                <div class="form-group">
                                <label for=""> Select Group Type<!--Segment--> </label>'.
                                form_dropdown('groupList[]', $group_select, $selected_groups, 'class="form-control" id="dropdown_arr"  multiple="multiple" onchange="loadDetail_table()"')
                            .'</div></div>';
                    
                    $bar .= '<div class="col-md-3">
                            <div class="form-group">
                            <label for=""> Select Employee<!--Segment--> </label>'.
                                form_dropdown('empList[]', $emp_arr, $selected_emp, 'class="form-control" id="emp_arr"  multiple="multiple" onchange="loadDetail_table()"')
                        .'</div></div>';

                    $bar .= '</div>';
                      
                    echo $bar;
                }else{

                    echo '<button type="button" class="btn btn-primary btn-sm saveBtn" onclick="openEmployeeModal()">
                            <i class="fa fa-fw fa-user"></i> '.$add_emp.'<!--Add Employee-->
                        </button>
                        <button type="button" class="btn btn-success btn-sm saveBtn" onclick="open_uploadModal()">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp; '.$uploadExcel.'<!--Upload Excel-->
                        </button>
                        <button type="button" class="btn btn-success btn-sm"  onclick="openDownloadTemplate_modal()">
                            <i class="fa fa-cloud-download" aria-hidden="true"></i>&nbsp; '.$downloadExcel.'<!--Upload Excel-->
                        </button>
                        ';

                }
               

                if($deductionType == 'NoPay' && $masterData['documentID'] != 'MA'){
                    echo ' <button type="button" class="btn btn-success btn-sm"  onclick="generate_no_pay_records()">
                    <i class="fa fa-cloud-download" aria-hidden="true"></i>&nbsp; Regenerate
                    </button>';
                }

                /*<button type="button" class="btn btn-success btn-sm"  onclick="window.open(\''.$downloadUrl.'\')">
                        <i class="fa fa-cloud-download" aria-hidden="true"></i>&nbsp; '.$downloadExcel.'<!--Upload Excel-->
                      </button>*/
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
                <th style="z-index:3"><?php echo $this->lang->line('hrms_payroll_grouping_type');?><!--Grouping Type--></th>
                <th style="width: 100px;"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                <th style="width: 100px;"><?php echo $this->lang->line('common_rate');?></th>
                <th style="width: 100px"><?php echo $this->lang->line('common_no_of_unit');?><!--Amount--></th>
                <th style="width: 100px"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                <th style="width: 100px;"><?php echo $this->lang->line('hrms_payroll_ex_rate');?><!--Ex.Rate--></th>
                <th style="width: 130px"> <?php echo $this->lang->line('hrms_payroll_local_amount');?><!--Local&nbsp;Amount-->&nbsp;(&nbsp;<?php echo $com_currency; ?>&nbsp;)&nbsp;</th>
                <th style="width: 100px;"><?php echo $this->lang->line('common_description');?><!--Ex.Rate--></th>
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
                    $dPlaces = $row['transactionCurrencyDecimalPlaces'];
                    $val = ($tAmount == 0) ? '' : number_format($tAmount, $dPlaces);
                    $payRate = $row['payRate'];
                    $payUnit = ($row['payUnit']) ? $row['payUnit'] : 1;
                    $localExchangeRate = $row['companyLocalExchangeRate'];
                    $localAmount += round(
                            $row['companyLocalAmount'] ?? 0,
                            $row['companyLocalCurrencyDecimalPlaces'] ?? 0
                        );

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


                    echo '<tr data-value="'.$row['ECode'].' '.$row['empName'].' '.$row['transactionCurrency'].' '.$row['description'].'">
                        <td>' . ($key + 1) . '</td>
                        <td>' . $row['ECode'] . '</td>
                        <td>' . $row['empName'] . '</td>
                        <td class="tdCol">' . make_dropDown($dropDownData, $row['declarationID'], $disabled, $key) . ' '.$applyToAllStr.'</td>
                        <td style="text-align:center">' . $row['transactionCurrency'] . '</td>
                        <td class="tdCol" style="text-align:center"> 
                            <input type="text" name="amount_rate[]" class="trInputs number amountrate" id="amountrate_' . $key . '"  value="' . $payRate . '" '.$disabled.'
                            onkeyup="formatCalculateAmount(this)" onchange="formatCalculateAmount(this, \'' . $dPlaces . '\')">
                        </td>
                        <td class="tdCol" style="text-align:center">
                            <input type="text" name="no_of_units[]" class="trInputs number noofunits" id="noofunits_' . $key . '"  value="' . $payUnit . '" '.$disabled.'
                            onkeyup="formatCalculateAmount(this)" onchange="formatCalculateAmount(this, \'' . $dPlaces . '\')" '.$disabled_readonly.'>'.$applyToAllStr1.'
                        </td>
                        <td class="tdCol">
                            <input type="text" name="amount[]" class="trInputs number amount" readonly id="amount_' . $key . '"  value="' . $val . '" '.$disabled.'
                             onkeyup="empAmount(this, \'' . $key . '\', \'' . $localExchangeRate . '\')" onchange="formatAmount(this, \'' . $dPlaces . '\')">
                        </td>
                        <td style="text-align:right;">' . $localExchangeRate . '</td>
                        <td>
                            <div align="right" class="localAmount" id="amountSpan_'.$key.'">
                               '.number_format($row['companyLocalAmount'] ?? 0, $row['companyLocalCurrencyDecimalPlaces'] ?? 0).'
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
                <td colspan="3" align="right"><?php echo $this->lang->line('hrms_payroll_total_amount');?><!--Total Amount--></td>
                <td align="right"><span id="totalAmount"><?php echo number_format($localAmount, $com_currencyDPlace)?></span></td>
                <td>&nbsp;</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>



<div class="modal fade" id="excelUpload_Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_employee_upload_form'); ?><!--Employee upload form--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open_multipart('', 'id="employeeUpload_form" class="form-inline"'); ?>
                    <div class="col-sm-12" style="margin-left: 3%">
                        <div class="form-group">
                            <input type="hidden" name="masterID" value="<?php echo $this->input->post('masterID');?>">
                            <input type="hidden" name="type_m" value="<?php echo $this->input->post('type_m');?>">
                            <input type="hidden" name="docDate" id="docDate" value="">
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                 style="margin-top: 8px;">
                                <div class="form-control" data-trigger="fileinput" style="min-width: 200px; width: 100%;
                                    border-bottom-left-radius: 3px !important; border-top-left-radius: 3px !important; ">
                                    <i class="glyphicon glyphicon-file color fileinput-exists"></i>
                                    <span class="fileinput-filename"></span>
                                </div>
                                <span class="input-group-addon btn btn-default btn-file">
                                    <span class="fileinput-new"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></span>
                                    <span class="fileinput-exists"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span></span>
                                    <input type="file" name="excelUpload_file" id="excelUpload_file" accept=".csv">
                                </span>
                                <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id" data-dismiss="fileinput">
                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                </a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-default" onclick="excel_upload()">
                            <span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="col-sm-12" style="margin-left: 3%; color: red">
                        <?php echo $this->lang->line('hrms_payroll_excel_upload_msg1'); ?><br/>
                        <?php echo $this->lang->line('hrms_payroll_excel_upload_msg2'); ?>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close');?>
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="downloadTemplate_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $downloadExcel;?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open('', 'id="downloadTemplate_form" class="form-inline"'); ?>
                    <div class="col-sm-12" style="margin-left: 3%">
                        <div class="form-group">
                            <label for="segment"> <?php echo $this->lang->line('common_segment');?><!--Segment--> </label>
                            <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segment-arr"  multiple="multiple"'); ?>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="downloadTemplate()">
                    <i class="fa fa-cloud-download" aria-hidden="true"></i> <?php echo $downloadExcel;?>
                </button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close');?>
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="excelUploadMsg_Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <i class="fa fa-info-circle" aria-hidden="true"></i> <strong>Info</strong>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-10" id="upload-msg-div"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $('#emp_arr').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $('#dropdown_arr').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    function openDownloadTemplate_modal(){
        $("#segment-arr").multiselect2('selectAll', false);
        $("#segment-arr").multiselect2('updateButtonText');
        $('#downloadTemplate_modal').modal('show');
    }

    function downloadTemplate(){

        if($('#segment-arr').val() == null){
            bootbox.alert('<div class="alert alert-danger" style="margin-top: 20px;">Please select at least one segment to proceed.</div>');
            return false;
        }

        var form= document.getElementById('downloadTemplate_form');
        form.target='_blank';
        form.action='<?php echo site_url('Employee/download_csv'); ?>';
        form.submit();
    }

   // $('.select2').select2();

    // $('#details_table').tableHeadFixer({
    //     head: true,
    //     foot: true,
    //     left: 1,
    //     right: 0
    // });

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

    function open_uploadModal(){
        $('#excelUpload_Modal').modal('show');
        var desDate = $('#desDate').val();
        $('#docDate').val(desDate);
    }

    function excel_upload(){
        var formData = new FormData($("#employeeUpload_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Employee/monthlyAddDeduction_excelUpload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (data[0] == 's' || data[0] == 'e') {
                    myAlert(data[0], data[1]);
                }

                if (data[0] == 'm') {
                    $('#excelUploadMsg_Modal').modal('show');
                    $('#upload-msg-div').html(data[1]);

                }

                if (data[0] == 's') {
                    $('#excelUpload_Modal').modal('hide');

                    setTimeout(function(){
                        loadDetail_table()
                    }, 300);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'error');
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

    // updateAttandanceRelatedRecords();

    function updateAttandanceRelatedRecords(){

        var monthlyAdditionsMasterID = <?php echo ($masterData['monthlyAdditionsMasterID']) ? $masterData['monthlyAdditionsMasterID'] : 1 ?>;
        var pullType = <?php echo ($masterData['pullType']) ? $masterData['pullType'] : 1 ?>;

        if(pullType != 1){
            return false;
        }

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: { 'monthlyAdditionsMasterID': monthlyAdditionsMasterID },
            url: "<?php echo site_url('Employee/generate_monthlyAdditionAttandance'); ?>",
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

    }


</script>

<?php
