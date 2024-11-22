<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_variable_pay_declaration_approval');
echo head_page($title, false);

$status_arr = [
    '0' => $this->lang->line('common_pending'),
    '1' => $this->lang->line('common_approved')
];
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved');?><!-- Approved-->
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
        <?php echo form_dropdown('approvedYN', $status_arr, '', 'class="form-control" id="approvedYN" onchange="status_change()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="variablePayApproval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="min-width: 30%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_level');?><!--Level--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="variablePay_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title;?></h4>
            </div>
            <form class="form-horizontal" id="variablePayApproval_form">
                <div class="modal-body">
                    <div id="conform_body"></div>
                    <hr>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>

                        <div class="col-sm-4">
                            <?php echo form_dropdown('approval_status', array('' => $this->lang->line('common_please_select'),'1'=>$this->lang->line('common_approved')/*'Approved'*/,'2'=>$this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="approval_status" required'); ?>
                            <input type="hidden" name="Level" id="Level">
                            <input type="hidden" name="masterID" id="masterID">
                            <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comment');?><!--Comments--></label>

                        <div class="col-sm-8">
                            <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                        <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var oTable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/variable_pay_declaration_approval', 'Test', 'Variable pay declaration Approval')
        });

        variablePay_table();

        $('#variablePayApproval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                approval_status: {validators: {notEmpty: {message: 'Status is required.'}}},
                masterID: {validators: {notEmpty: {message: 'Master ID is required.'}}},
                documentApprovedID: {validators: {notEmpty: {message: 'Document Approved ID is required.'}}}
            }
        })
        .on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/approval_variable_pay_declaration'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]) ;

                    if( data[0] == 's' ){
                        $("#variablePay_modal").modal('hide');
                        oTable.ajax.reload();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }

                }, error: function () {
                    myAlert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });


    function variablePay_table() {
        oTable = $('#variablePayApproval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_variable_pay_approval'); ?>",
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
            "columnDefs": [
                {"targets": [0,6,7], "orderable": false}
            ],
            "aoColumns": [
                {"mData": "vpMasterID"},
                {"mData": "docCode"},
                {"mData": "documentDate"},
                {"mData": "description"},
                {"mData": "trCurr"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN").val()});
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

    function fetch_approval(masterID, documentApprovedID, Level) {
        if (masterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'masterID': masterID, 'html': true, isFromApproval:'Y'},
                url: "<?php echo site_url('Employee/variable_pay_approval_confirmation_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#masterID').val(masterID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#variablePay_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');

                    stopLoad();
                }, error: function (xhr, ajaxOptions, thrownError) {
                    stopLoad();
                    myAlert('e', ''+xhr.responseText);
                }
            });
        }
    }

    function status_change(){
        oTable.ajax.reload();
    }
</script>
<?php
