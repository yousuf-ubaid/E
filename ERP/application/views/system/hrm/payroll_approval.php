<!--Translation added by Naseek-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_approval');
echo head_page($title, false);
?>

<style type="text/css">
    #load-paysheet {
        /*height: 200px;
        overflow-y: auto;*/
    }

    .fixHeader_Div {
        height: 400px;
        border: 1px solid #c0c0c0;
    }

    .t-foot {
        background: #c0c0c0
    }
</style>


<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tbody>
            <tr>
                <td>
                    <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved'); ?><!--Approved-->
                </td>
                <td>
                    <span class="label label-danger"
                          style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_not_approved'); ?>
                    <!--Not Approved-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending'), '1' => $this->lang->line('common_approved')), '', 'class="form-control" id="approvedYN" required onchange="payroll_table_approval()"'); ?>
    </div>
</div>
<hr>

<div class="table-responsive">
    <table id="payroll_table_approval" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('hrms_payroll_month'); ?><!--Payroll Month--></th>
            <th style="min-width: 40%"><?php echo $this->lang->line('hrms_payroll_narration'); ?><!--Narration--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('hrms_payroll_level'); ?><!--Level--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="paysheetApprove_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('hrms_payroll_approval'); ?><!--Payroll Approval--> <span
                            id="pay-sheet-month"> </span></h4>
            </div>
            <form class="form-horizontal" id="payroll_approval_form">
                <div class="modal-body">

                    <div class="pull-right">
                        <label><span id="payrollHeaderDet" style="display: none;"> </span> </label>
                        <span class="no-print pull-right">
                            <a class="btn btn-default btn-sm" id="payrollAccountReview" target="_blank" href=""
                               style="">
                                <span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;
                                <?php echo $this->lang->line('hrms_payroll_account_review_ent'); ?><!--Account Review Entries--> </a>
                        </span>
                    </div>

                    <div class="panel-body" id="load-paysheet">

                    </div>
                    <hr>
                    <div class="form-group form_items">
                        <label for="status" class="col-sm-2 control-label">
                            <?php echo $this->lang->line('common_status'); ?><!--Status--></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select'), '1' => $this->lang->line('common_approved'), '2' => $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control controlCls" id="status" required'); ?><!-- /*Please Select*/-->
                            <!--'/*Approved*/'-->
                            <input type="hidden" name="level" id="level">
                            <input type="hidden" name="hiddenPaysheetID" id="hiddenPaysheetID">
                            <input type="hidden" name="hiddenPaysheetCode" id="hiddenPaysheetCode">
                        </div>
                    </div>
                    <div class="form-group form_items">
                        <label for="comments" class="col-sm-2 control-label">
                            <?php echo $this->lang->line('common_comment'); ?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control controlCls" rows="3" name="comments" id="comments"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm form_items">
                        <?php echo $this->lang->line('common_submit'); ?><!--Submit--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">


    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/payroll_approval', '', 'HRMS');
        });
        payroll_table_approval();

        $('#payroll_approval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_payroll_status_is_required'); ?>.'}}},/*Status is required*/
                Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_payroll_level_order_status_is_required'); ?>.'}}},/*Level Order Status is required*/
                //comments                : {validators : {notEmpty:{message:'Comments are required.'}}},
                hiddenPaysheetID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_payroll_payroll_id_is_required'); ?>.'}}}/*Payroll ID is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Template_paysheet/paysheetApproval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        $("#paysheetApprove_modal").modal('hide');
                        payroll_table_approval();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }

                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });

    function payroll_table_approval() {
        var Otable = $('#payroll_table_approval').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Template_paysheet/fetch_paysheets_conformation'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "payrollMasterID"},
                {"mData": "documentCode"},
                //{"mData": "documentCode_str"},
                {"mData": "payrollMonth"},
                {"mData": "narration"},
                {"mData": "level"},
                {"mData": "approved"},
                {"mData": "edit"}

            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN :selected").val()});
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

    function load_paysheetApproval(payrollID, approvalLevel, payMonth, payrollCode, appYN) {
        $('.form_items').show();
        $('#payroll_approval_form')[0].reset();
        $('#payroll_approval_form').bootstrapValidator('resetForm', true);

        $('#hiddenPaysheetID').val(payrollID);
        $('#hiddenPaysheetCode').val(payrollCode);
        $('#level').val(approvalLevel);
        $('#status').val(1);

        /*url: ' echo site_url('template_paySheet/paysheetDetail') ?>',*/

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Template_paysheet/templateDetails_view') ?>',
            data: {'hidden_payrollID': payrollID, 'from_approval': 'Y'},
            dataType: 'html',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (appYN == 1) {
                    $('.form_items').hide();
                } else {
                    $('.form_items').show();
                }
                //$('#pay-sheet-month').html(' &nbsp;&nbsp; '+payMonth);
                $("#paysheetApprove_modal").modal({backdrop: "static"});
                $('#load-paysheet').html(data);

                var printUrl = '<?php echo site_url('Template_paysheet/payrollAccountReview'); ?>';
                $('#payrollAccountReview').attr('href', printUrl + '/' + payrollID + '/N/' + payrollCode + '-' + payMonth);

                $('#paysheet-tb').tableHeadFixer({
                    head: true,
                    foot: true,
                    left: 0,
                    right: 0,
                    'z-index': 0
                });
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function payroll_details(loanID) {
        var $loanDet = ['view', loanID];
        fetchPage('system/hrm/pay_sheet', 0, 'Load', '', $loanDet);
    }

</script>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-12-01
 * Time: 12:05 PM
 */