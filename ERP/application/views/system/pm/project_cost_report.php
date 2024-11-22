<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('accounts_receivable', $primaryLanguage);
$title = $this->lang->line('sales_markating_sales_order_report');
$date_format_policy = date_format_policy();
$financeyear_arr = all_financeyear_drop(true);
$segment_arr = fetch_segment(true,false);
$from = convert_date_format(current_date());
$projectID = load_all_project();
echo head_page('Project Cost', false);
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
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_projectcostreport" id="frm_rpt_projectcostreport" class="form-group" role="form"'); ?>
        <div class="col-md-12">
            <div class="form-group col-sm-2">
                <label for="">Project</label>
                <?php echo form_dropdown('project[]', $projectID, '', 'multiple  class="form-control select2" id="project"  required'); ?>
            </div>
            <div class="form-group col-sm-1">
                <label for=""></label>
                <button style="margin-top: 28px" type="button" onclick="get_project_cost_report()"
                        class="btn btn-primary btn-xs">
                    <?php echo $this->lang->line('common_generate'); ?></button>
            </div>


        </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<br>
<hr style="margin: 0px;">
<div id="div_project_cost">
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="returndrilldownModal" tabindex="2" role="dialog" aria-labelledby="myModalLabel" style="z-index: 10000;">
    <div class="modal-dialog" role="document" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title drilldown-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <table id="tbl_rpt_salesreturn" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_document_code'); ?></th>
                        <th><?php echo $this->lang->line('common_document_date'); ?></th>
                        <th><?php echo $this->lang->line('common_currency'); ?></th>
                        <th><?php echo $this->lang->line('common_amount'); ?></th>
                    </tr>
                    </thead>
                    <tbody id="salesreturn">

                    </tbody>
                    <tfoot id="salesreturnfooter" class="table-borded">

                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="sumarydrilldownModal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> <?php echo $this->lang->line('accounts_receivable_rs_cad_revenue_summary_drill_down'); ?></h4>
            </div>
            <div class="modal-body" id="sumarydd">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $('#project').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#project").multiselect2('selectAll', false);
    $("#project").multiselect2('updateButtonText');

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {

    });
    $('.headerclose').click(function () {
        fetchPage('system/pm/project_cost_report', '', 'Project Cost')
    });
    $(document).ready(function (e) {
        get_project_cost_report();

        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
    });

    function get_project_cost_report() {
        var data = $("#frm_rpt_projectcostreport").serialize();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/get_project_cost_report') ?>",
            data: data,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_project_cost").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

  /*  function generateReportPdf() {
        var form = document.getElementById('frm_rpt_customer_balance_summary');
        form.target = '_blank';
        form.action = '<?php echo site_url('Report/get_customer_balance_report_pdf'); ?>';
        form.submit();
    }
*/

 /*   function opencollectionsummaryDD(date,currency,segment,customerid){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/get_collection_details_drilldown_report') ?>",
            data: {'date': date,'currency': currency,'customerID': customerid,'segment': segment},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#sumarydd").html(data);
                $('#sumarydrilldownModal').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }*/


  /*  function opencollectionsummaryPriviousDD(datebegin,dateend,currency,segment,customerid){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/get_collection_previous_details_drilldown_report') ?>",
            data: {'datebegin': datebegin,'dateend': dateend,'currency': currency,'customerID': customerid,'segment': segment},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#sumarydd").html(data);
                $('#sumarydrilldownModal').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }*/

</script>
