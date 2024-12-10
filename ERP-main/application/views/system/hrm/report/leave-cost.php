<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_reports', $primaryLanguage);
$title = $this->lang->line('hrms_reports_leave_cost');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$sal_cat = salary_categories(['A', 'D'], 1);
$leave_arr = load_leave_type_drop();
$employee_list = employee_list_by_segment(1, 0);
$csrf = get_csrf_token_data();

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
    .select2-dropdown--below{
        z-index: 1000000002 !important;
    }

    #toast-container {
        z-index: 1000000003 !important;
    }

    #rpt_tbl tr:hover{
        background-color: #B0BED9 !important;
    }
</style>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">

<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters');?><!--Filter--></legend>
        <?php echo form_open('', 'id="rpt_form" class="form-horizontal" role="form"'); ?>
        <div class="col-sm-12">
            <div class="col-sm-2 col-xs-6">
                <label for="asOfDate" class="control-label"><?php echo $this->lang->line('hrms_reports_as_of');?><!--As Of--></label>
                <div class="input-group filterDate">
                    <span class="input-group-addon" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="asOfDate" id="asOfDate"
                           value="<?=$current_date?>" class="form-control">
                </div>
            </div>

            <div class="col-sm-2 col-xs-6">
                <label for="segmentID" class="control-label"><?php echo $this->lang->line('common_segment');?><!--Segment-->:</label>
                <?php echo form_dropdown('segmentID[]', fetch_segment(true, false), '',
                    'multiple  class="form-control select2" id="segmentID" onchange="loadEmployees()"'); ?>
            </div>

            <div class="col-sm-2 col-xs-6">
            <label for="inputData" class="control-label"><?php echo $this->lang->line('hrms_reports_employee');?></label>
                <span id="emp_container">
                    <?php echo form_dropdown('empID[]', $employee_list, '', ' class="form-control" id="empID" multiple="multiple"'); ?>
                </span>
            </div>

            <div class="col-sm-2 col-xs-6">
                <label><?php echo $this->lang->line('common_annual_leave');?></label>
                <?php echo form_dropdown('leave_type', $leave_arr, '', 'class="form-control select2" id="leave_type"'); ?>
            </div>

            <div class="col-sm-2 col-xs-6">
                <label><?php echo $this->lang->line('common_basic_gross');?></label>
                <select name="calculate_based_on[]" class="form-control" id="calculate_based_on" multiple="multiple">
                    <?php
                    foreach ($sal_cat as $cat_row){
                        echo '<option value="'.$cat_row['salaryCategoryID'].'">'.$cat_row['salaryDescription'].'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-sm-1 col-xs-6">
                <label>
                    <abbr title="<?php echo $this->lang->line('common_no_of_working_days');?>"> <?php echo $this->lang->line('common_no_of_days');?> </abbr>
                </label>
                <input name="no_of_working_days" class="form-control number" id="no_of_working_days" <?=$readonly?> value="<?=$no_of_working_days?>"
                       onkeyup="max_month_days()"/>
            </div>

            <div class="col-sm-1 col-xs-6">
                <label>&nbsp;</label>
                <button class="btn btn-primary btn-sm pull-right" style="font-size:12px; position: absolute; margin-top: 20px"
                        onclick="load_report()" type="button">
                    <?php echo $this->lang->line('common_load');?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<hr style="margin: 0px;">


<div style="width: 100%; height: 10px">&nbsp;</div>
<div id="report-container"></div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">

    $('#leave_type').select2();
    $('.number').numeric({negative: false});

    $('#calculate_based_on').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    let segmentDrop = $('#segmentID');
    segmentDrop.multiselect2({
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth:150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    segmentDrop.multiselect2('selectAll', false);
    segmentDrop.multiselect2('updateButtonText');

    let empDrop = $('#empID');
    empDrop.multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        enableFiltering: true,
        maxHeight: 200,
        numberDisplayed: 2,
        buttonWidth: '180px'
    });
    empDrop.multiselect2('selectAll', false);
    empDrop.multiselect2('updateButtonText');

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/report/leave-cost','','HRMS');
        });

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.filterDate').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });
    });

    function load_report(){
        let data = $('#rpt_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Employee/load_leave_cost_view/Y'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){
                    $('#report-container').html(data['view']);

                    $('#rpt_tbl').tableHeadFixer({
                        head: true,
                        foot: true,
                        left: 1,
                        right: 0
                    });
                }
                else{
                    myAlert(data[0], data[1]);
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function print_document(){
        let rpt_form = $('#rpt_form');
        rpt_form.attr('target', "blank");
        rpt_form.attr('action', "<?php echo site_url('Employee/load_leave_cost_view/N/Leave-Cost-print'); ?>");
        rpt_form.submit();
    }

    function max_month_days(){
        let obj = $('#no_of_working_days');

        if( parseInt(obj.val()) > 31 ){
            obj.val('');
            myAlert('w', 'Maximum days can not be greater than 31')
        }
    }

    function loadEmployees(){
        let segmentID  = segmentDrop.val();

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/employee_list_by_segment') ?>",
            data: {segmentID:segmentID, 'status':''},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 's'){

                    $("#emp_container").html( data[1] );

                    empDrop = $('#empID');

                    empDrop.multiselect2({
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true,
                        maxHeight: 200,
                        numberDisplayed: 2,
                        buttonWidth: '180px'
                    });
                    empDrop.multiselect2('selectAll', false);
                    empDrop.multiselect2('updateButtonText');

                }
                else{
                    empDrop.empty();
                    empDrop.multiselect('refresh');
                    empDrop.multiselect2({
                        includeSelectAllOption: true,
                        maxHeight: 200,
                        numberDisplayed: 1
                    });
                    empDrop.multiselect2('updateButtonText');
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    $('#rpt_tbl tbody').on('hover', 'tr', function () {
        $('#rpt_tbl tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>