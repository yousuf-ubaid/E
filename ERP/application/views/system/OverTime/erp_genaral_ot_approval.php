<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('hrms_payroll_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_attendance_summary_approval');
echo head_page($title, false);

/*echo head_page('Attendance Summary Approval', false);*/


?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending')/*'Pending'*/, '1' => $this->lang->line('common_approved')/*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="general_ot_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="got_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('hrms_payroll_ats_number');?><!--ATS Number--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('hrms_payroll_document_date');?><!--Document Date--></th>
            <th style="min-width: 30%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_level');?><!--Level--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="purchase_order_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_deneral_over_time_approval');?><!--General Over Time Approval--></h4>
            </div>
            <form class="form-horizontal" id="GOT_approval_form">
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="zx-tab-content">

                                <div id="conform_body"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('got_status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="got_status" required'); ?>
                                        <input type="hidden" name="Level" id="Level">
                                        <input type="hidden" name="generalOTMasterID" id="generalOTMasterID">
                                        <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comment');?><!--Comments--></label>

                                    <div class="col-sm-8">
                                <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                                </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">&nbsp;
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="OtMasterViewModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 90%">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('hrms_payroll_general_oT_view');?><!--General OT View--></h4>
            </div>
            <div class="modal-body">
                <div class="row" style="padding: 1%;" id="GOTview">

                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/OverTime/erp_genaral_ot_approval', 'Test', 'General OT Approval')
        });

        general_ot_table();
        $('#GOT_approval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                got_status: {validators: {notEmpty: {message: 'Status is required.'}}},
                //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
                generalOTMasterID: {validators: {notEmpty: {message: 'Purchase Order ID is required.'}}},
                documentApprovedID: {validators: {notEmpty: {message: 'Document Approved ID is required.'}}}
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
                url: "<?php echo site_url('OverTime/save_general_ot_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                    if(data == true){
                        $("#purchase_order_modal").modal('hide');
                        general_ot_table();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function general_ot_table() {
        var Otable = $('#got_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('OverTime/fetch_general_ot_approval'); ?>",
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


            },
            "aoColumns": [
                {"mData": "generalOTMasterID"},
                {"mData": "otCode"},
                {"mData": "documentDate"},
                {"mData": "description"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
                //{"mData": "edit"},
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN :checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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

    function fetch_approval(generalOTMasterID, documentApprovedID, Level) {
        if (generalOTMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'generalOTMasterID': generalOTMasterID, 'html': true,'approval':1},
                url: "<?php echo site_url('OverTime/fetch_over_time_template_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#generalOTMasterID').val(generalOTMasterID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#purchase_order_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function general_ot_view_model(generalOTMasterID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {generalOTMasterID: generalOTMasterID, All: 'true'},
            url: "<?php echo site_url('OverTime/fetch_over_time_template_view'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#GOTview').html(data);
                $("#OtMasterViewModal").modal({backdrop: "static"});
            }, error: function () {

            }
        });
    }
</script>