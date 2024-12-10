<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_leave_management_lang', $primaryLanguage);
$this->lang->load('hrms_payroll', $primaryLanguage);

$date_format_policy = date_format_policy();
$masterID = $this->input->post('page_id');
$segment_arr = fetch_segment(true, false);
$sal_cat = salary_categories(['A', 'D'], 1);
$leave_arr = load_leave_type_drop();

$title = $this->lang->line('hrms_leave_management_leave_encashment');
echo head_page($title, false);

$encash_policy = getPolicyValues('LEB', 'All'); //Leave encashment policy
$no_of_working_days = 22;
$readonly = '';
if($encash_policy == 1){
    $salaryProportionFormulaDays = getPolicyValues('SPF', 'All'); // Salary Proportion Formula
    $no_of_working_days = ($salaryProportionFormulaDays == 365)? 30.42: 30;
    $readonly = 'readonly';
}
?>
<style>
    fieldset {
        border: 1px solid silver;
        border-radius: 0px;
        padding-bottom: 15px;
        margin: 10px;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500
    }

    .row-centered {
        text-align:center;
    }

    .col-centered {
        display:inline-block;
        float:none;
        /* reset the text-align */
        text-align:left;
        /* inline-block space fix */
        margin-right:-4px;
        text-align: center;
    }
</style>


<div id="response-container"> </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="bulkDetails_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" style="width: 95%" role="document">
        <div class="modal-content">
            <form class="" id="bulk_form" autocomplete="off">
                <input type="hidden" name="masterID" id="masterID" value="<?php echo $masterID; ?>"/>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('common_add_detail');?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <fieldset style="margin-top: -10px;">
                            <legend><?php echo $this->lang->line('emp_employment_details');?> Criteria </legend>
                            <div class="container">
                                <div class="row row-centered">
                                    <div class="col-sm-2 col-xs-6 col-centered">
                                        <label><?php echo $this->lang->line('common_annual_leave');?></label>
                                        <?php echo form_dropdown('leave_type', $leave_arr, '', 'class="form-control select2" id="leave_type" required'); ?>
                                    </div>

                                    <div class="col-sm-2 col-xs-6 col-centered">
                                        <label><?php echo $this->lang->line('common_basic_gross');?></label>
                                        <select name="calculate_based_on[]" class="form-control" id="calculate_based_on" multiple="multiple">
                                            <?php
                                            foreach ($sal_cat as $cat_row){
                                                echo '<option value="'.$cat_row['salaryCategoryID'].'">'.$cat_row['salaryDescription'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-sm-2 col-xs-6 col-centered">
                                        <label>
                                            <abbr title="<?php echo $this->lang->line('common_no_of_working_days');?>"> <?php echo $this->lang->line('hrms_leave_management_no_of_day');?> </abbr>
                                        </label>
                                        <input name="no_of_working_days" class="form-control number" id="no_of_working_days"
                                            <?=$readonly?> value="<?=$no_of_working_days?>" onkeyup="max_month_days()"/>
                                    </div>

                                    <div class="form-group col-sm-1 col-xs-6 col-centered">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-primary btn-sm pull-right" style="font-size:12px; position: absolute" onclick="addAllRows()" type="button">
                                            <?php echo $this->lang->line('common_proceed');?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="row">
                                <input type="hidden" id="isEmpLoad" value="0" >
                                <div class="col-sm-2 col-xs-4 select-container">
                                    <label for="segment"> <?php echo $this->lang->line('common_segment');?><!--Segment--> </label>

                                </div>
                                <div class="col-sm-4 col-xs-4 select-container">
                                    <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control" id="segmentID"  multiple="multiple"'); ?>
                                </div>
                                <div class="col-sm-4 col-xs-4 pull-right" style="/*padding-top: 24px;*/">
                                    <button class="btn btn-primary btn-sm pull-right" id="selectAllBtn" style="font-size:12px;" onclick="selectAllRows()" type="button">
                                        <?php echo $this->lang->line('hrms_payroll_select_all');?><!--Select All-->
                                    </button>
                                    <button type="button" onclick="load_employeeForBulk_refresh()" class="btn btn-primary btn-sm pull-right" style="margin-right:10px">
                                        <?php echo $this->lang->line('common_load');?><!--Load-->
                                    </button>
                                </div>
                            </div>

                            <hr style="margin: 10px 0px 10px;" class="hidden-sm hidden-xs">
                            <div class="row">
                                <div class="table-responsive col-md-12">
                                    <table id="emp_modalTB" class="<?php echo table_class(); ?>">
                                        <thead>
                                        <tr>
                                            <th style="min-width: 5%">#</th>
                                            <th style="min-width: 25%"><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--EMP Code--></th>
                                            <th style="width:auto"><?php echo $this->lang->line('hrms_payroll_employee_name');?><!--Employee Name--></th>
                                            <th style="width:auto"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                                            <th style="width:auto"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                                            <th style="width: 5%"><div id="dataTableBtn"></div></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="<?php echo table_class(); ?>" id="tempTB">
                                        <thead>
                                        <tr>
                                            <th style="max-width: 5%"><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--EMP CODE--></th>
                                            <th style="max-width: 95%"><?php echo $this->lang->line('hrms_payroll_emp_name');?><!--EMP NAME--></th>
                                            <th style="width: 40px">
                                            <span class="glyphicon glyphicon-trash" onclick="clearAllRows()"
                                                  style="color:rgb(209, 91, 71);" title="<?php echo $this->lang->line('common_clear_all');?>"></span>
                                            </th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="addAllRows()" >
                        <?php echo $this->lang->line('common_proceed');?>
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    var en_masterID  = <?=$masterID?>;
    var emp_modalTB = $('#emp_modalTB');
    var empTemporary_arr = [];
    var tempTB = $('#tempTB').DataTable({
        "bPaginate": false,
        "columnDefs": [ {
            "targets": [2],
            "orderable": false
        } ]
    });
    var disableDate = '';
    var error_occurred_str = '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.'; /*An Error Occurred! Please Try Again*/

    $('#leave_type').select2();
    $('.number').numeric({negative: false});

    load_salary();
    $('#calculate_based_on').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $('#segmentID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $(document).ready(function () {

        $('.headerclose').click(function(){
            fetchPage('system/hrm/leave-encashment','','HRMS');
        });

        load_detail_view();
    });

    function load_detail_view(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': '<?=$masterID?>'},
            url: "<?php echo site_url('Employee/leave_encashment_and_salary_view'); ?>",
            beforeSend: function () {
                startLoad();
                $('#sal-dec-body').html('');
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#response-container').html( data['view'] );
                }
                else{
                    myAlert(data[0], data[1]);
                }
            }, error: function () {
                myAlert('e', 'Some thing went gone wrong.<p>Please try again.')
            }
        });

    }

    function max_month_days(){
        var obj = $('#no_of_working_days');

        if( parseInt(obj.val()) > 31 ){
            obj.val('');
            myAlert('w', 'Maximum days can not be greater than 31')
        }
    }

    function load_salary(){
        $.ajax({
            async: true,
                type: 'post',
                dataType: 'json',
                url: "<?php echo site_url('Employee/selectBasicSlary'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#calculate_based_on').val(data['salaryCategoryID']).change();
                }, error: function () {
                    myAlert('e', 'Some thing went gone wrong.<p>Please try again.')
                }
        });

    }

    function delete_item(detailID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'detailID': detailID, 'masterID': en_masterID},
                    url: "<?php echo site_url('Employee/delete_leave_encashment_item'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            load_detail_view();
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', error_occurred_str);
                    }
                });
            });
    }

    function delete_all_item() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete_all');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'masterID': en_masterID},
                    url: "<?php echo site_url('Employee/delete_all_leave_encashment_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    }, success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            load_detail_view();
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', error_occurred_str);
                    }
                });
            });
    }

    function open_details_modal(){
        $("#calculate_based_on").multiselect2("refresh");
        $('#bulkDetails_modal').modal('show');

        emp_modalTB.DataTable().destroy();
        var bulk_effectiveDate = $('#doc_data').val();
        load_employeeForBulk(bulk_effectiveDate);
    }

    function load_employeeForBulk_refresh(){
        var bulk_effectiveDate = $('#doc_data').val();
        load_employeeForBulk(bulk_effectiveDate);
    }

    function load_employeeForBulk(effectiveDate=null){
        emp_modalTB.DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/getEmployeesDataTable_salaryDeclaration'); ?>?entryDate="+effectiveDate,
            "aaSorting": [[1, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "ECode"},
                {"mData": "empName"},
                {"mData": "segTBCode"},
                {"mData": "CurrencyCode"},
                {"mData": "addBtn"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'isNonPayroll', 'value':$('#payrollType').val()});
                aoData.push({'name':'segmentID', 'value':$('#segmentID').val()});
                aoData.push({'name':'currencyFilter', 'value':$('#docCurrency').val()});
                aoData.push({'name':'isFromSalaryDeclaration', 'value':'1'});

                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function addTempTB(det){

        var table = $('#emp_modalTB').DataTable();
        var thisRow = $(det);

        var details = table.row(  thisRow.parents('tr') ).data();
        var empID = details.EIdNo;

        var inArray = $.inArray(empID, empTemporary_arr);
        if (inArray == -1) {
            var empDet = '<div class="pull-right"><input type="hidden" name="temp_empHiddenID[]"  class="modal_empID" value="' + details.EIdNo + '">';
            empDet += '<input type="hidden" name="temp_accGroupID[]" class="modal_accGroupID" value="' + details.accGroupID + '">';
            empDet += '<span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" style="color:rgb(209, 91, 71);"></span> </a></div>';

            tempTB.rows.add([{
                0: details.ECode,
                1: details.empName,
                2: empDet,
                3: empID
            }]).draw();
            empTemporary_arr.push(empID);
        }
    }

    function selectAllRows(){
        var tempTB = $('#tempTB').DataTable();
        var emp_modalTB = $('#emp_modalTB').DataTable();
        var empDet1;
        emp_modalTB.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            var data = this.data();
            var empID = data.EIdNo;

            var inArray = $.inArray(empID, empTemporary_arr);

            if (inArray == -1) {
                empDet1 = '<div class="pull-right"><input type="hidden" name="temp_empHiddenID[]" class="modal_empID" value="' + data.EIdNo + '">';
                empDet1 += '<input type="hidden" name="temp_accGroupID[]" class="modal_accGroupID" value="' + data.accGroupID + '">';
                empDet1 += '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" onclick="removeTempTB(this)"></span> </a></div>';

                tempTB.rows.add([{
                    0: data.ECode,
                    1: data.empName,
                    2: empDet1,
                    3: empID
                }]).draw();

                empTemporary_arr.push(empID);
            }
        } );
    }

    function removeTempTB(det){
        var table = $('#tempTB').DataTable();
        var thisRow = $(det);
        var details = table.row(  thisRow.parents('tr') ).data();
        empID = details[3];

        empTemporary_arr = $.grep(empTemporary_arr, function(data) {
            return parseInt(data) != empID
        });

        table.row( thisRow.parents('tr') ).remove().draw();
    }

    function clearAllRows(){
        var table = $('#tempTB').DataTable();
        empTemporary_arr = [];
        table.clear().draw();
    }

    function addAllRows(){
        var postData = $('#bulk_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Employee/add_leave_encashment_employees'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if( data[0] == 'e'){
                    myAlert('e', data[1]);
                }
                else if( data[0] == 'm'){
                    bootbox.alert('<div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle fa-2x"></i> Error! </strong><br/>'+data[1]+'</div>');
                }else{
                    $('#bulkDetails_modal').modal('hide');
                    setTimeout(function(){
                        load_detail_view();
                    }, 300);

                    clearAllRows();
                }
            },
            error: function () {
                myAlert('e', error_occurred_str);
                stopLoad();
            }
        });

    }

</script>


<?php
