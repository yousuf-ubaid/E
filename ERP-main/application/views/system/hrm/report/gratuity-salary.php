<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_gratuity_salary');
echo head_page($title, false);

$gratuity_arr = gratuity_drop(false);

$current_date = current_format_date();
$date_format_policy = date_format_policy();
?>
<style>

</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters');?><!--Filter--></legend>
        <?php echo form_open('', ' name="frm-rpt" id="frm-rpt" class="form-horizontal" role="form"'); ?>
        <div class="col-md-12">
            <label for="inputCodforn" class="col-md-1 control-label"><?php echo $this->lang->line('hrms_reports_as_of');?><!--As Of--></label>
            <div class="col-md-2">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" class="form-control" id="as_of_date" name="as_of_date"  value="<?php echo $current_date; ?>"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required />
                </div>
            </div>
            <label for="inputData" class="col-md-2 control-label"><?php echo $this->lang->line('hrms_reports_gratuity_type');?></label>
            <div class="col-md-2">
                <?php echo form_dropdown('gratuityID[]', $gratuity_arr, '', 'multiple  class="form-control select2" id="gratuityID" required'); ?>
            </div>

            <label for="inputData" class="col-md-2 control-label"><?php echo $this->lang->line('common_previous_month');?></label>
            <div class="col-md-2">
               <input type="checkBox" id="previousMonth" name="previousMonth" value="1">
            </div>


            <button style="margin-top: 5px" type="button" onclick="load_report()" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_load');?></button>
        </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>

<hr style="margin: 0px;">


<div id="div_ajax_response" style="margin-top: 15px"></div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    $('#gratuityID').multiselect2({
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth:150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#gratuityID").multiselect2('selectAll', false);
    $("#gratuityID").multiselect2('updateButtonText');

    $('.headerclose').click(function () {
        fetchPage('system/hrm/report/gratuity-salary','','Gratuity Salary')
    });

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    });

    $(document).ready(function (e) {
        //load_report();
    });

    function load_report() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/get_gratuity_salary_report') ?>",
            data: $("#frm-rpt").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_ajax_response").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }
</script>

<?php
