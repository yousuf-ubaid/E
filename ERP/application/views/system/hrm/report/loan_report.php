<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_loan_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
//$title = $this->lang->line('hrms_reports_employee_pay_scale_report');
$title = "Loan Report";
echo head_page($title, false);
$loanTypes = load_loan_types_arr();
?>
<style>
    .bgc {
        background-color: #e1f1e1;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters');?><!--Filter--></legend>
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_loan" id="frm_rpt_loan" class="form-horizontal" role="form"'); ?>
        <div class="row">
            <label for="inputData" class="col-md-1 control-label" style="width: 80px; text-align: left;"><?php echo $this->lang->line('common_employee');?></label>
            <div class="col-md-2" id="">
                <?php
                $employee = all_employee_drop(False);
                if (isset($employee)) {
                    foreach ($employee as $row) {
                        $employee_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
                    }
                }
                echo form_dropdown('empID[]', $employee_arr, '', 'id="empID" multiple="multiple" class="form-control mid-width wrapItems "');
                ?>
            </div>
            <label for =""  class="col-md-1 control-label" style="width: 95px; text-align: left;">
                <?php echo $this->lang->line('hrms_loan_type');?><!--Loan Type--></label>
            <div class="col-md-2">
                <?php echo form_dropdown('loanType[]', $loanTypes, '', 'class="form-control mid-width wrapItems" multiple="multiple" id="loanTypeID"'); ?>
            </div>
                <button style="margin-top: 5px" type="button" onclick="get_loan()" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_search');?><!--Search--></button>
        </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<hr style="margin: 0px;">
<div id="div_loan">


</div>
<div class="modal fade " id="document_drilldown_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">
                   Loan </h4>
            </div>
            <div class="modal-body" id="div_drilldown_body">
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    $('#empID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        maxHeight: 200,
        numberDisplayed: 0
    });
    $("#empID").multiselect2('selectAll', false);
    $("#empID").multiselect2('updateButtonText');

    $('#loanTypeID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        maxHeight: 200,
        numberDisplayed: 0
    });
    $("#loanTypeID").multiselect2('selectAll', false);
    $("#loanTypeID").multiselect2('updateButtonText');

    $('.headerclose').click(function () {

        fetchPage('system/hrm/report/loan_report','','Loan Report');
    });

    $(document).ready(function (e) {
      get_loan();

        /*$('.filterDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });*/
    });

    function get_loan() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Loan/get_loan_report') ?>",
            data: $("#frm_rpt_loan").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_loan").html(data);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }


function document_drilldown(id) {

    $.ajax({
        async: true,
        type: 'post',
        dataType: 'html',
        data: {'emploanID': id},
        url: "<?php echo site_url('Loan/get_drilldown_details'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            $("#div_drilldown_body").html(data);    
            $('#document_drilldown_model').modal('show');

        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });     


  }
</script>
