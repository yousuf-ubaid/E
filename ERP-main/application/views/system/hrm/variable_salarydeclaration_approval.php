<!--Translation added by naseek
-->

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_variable_salary_declaration_approval');
echo head_page($title, false)
?>
<style>
    .right-align{ text-align: right; }
</style>
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
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending'), '1' =>$this->lang->line('common_approved')), '', 'class="form-control" id="approvedYN" required onchange="test()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="salary_declaration_approval_table" class="<?php echo table_class() ?>">
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
<div class="modal fade" id="salaryDeclaration_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_salary_declaration_approval');?><!--Salary Declaration Approval--></h4>
            </div>
            <form class="form-horizontal" id="salaryDeclaration_approval_form">
                <div class="modal-body">
                    <input type="hidden" name="isVariable" value="1" />
                    <div id="conform_body"></div>
                    <hr>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>

                        <div class="col-sm-4">
                            <?php echo form_dropdown('approval_status', array('' => $this->lang->line('common_please_select'),'1'=>$this->lang->line('common_approved')/*'Approved'*/,'2'=>$this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="approval_status" required'); ?>
                            <input type="hidden" name="Level" id="Level">
                            <input type="hidden" name="salaryOrderID" id="salaryOrderID">
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
            fetchPage('system/hrm/salarydeclaration_approval', 'Test', 'Salary declaration Approval')
        });
        salaryDeclaration_table();
        $('#salaryDeclaration_approval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                approval_status: {validators: {notEmpty: {message: 'Salary Declaration Status is required.'}}},
                //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
                salaryOrderID: {validators: {notEmpty: {message: 'Master ID is required.'}}},
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
                url: "<?php echo site_url('Employee/save_salary_declaration_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]) ;

                    if( data[0] == 's' ){
                        $("#salaryDeclaration_modal").modal('hide');
                        salaryDeclaration_table();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }

                }, error: function () {
                    myAlert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });

    function test(){
        oTable.draw();
    }

    function salaryDeclaration_table() {
         oTable = $('#salary_declaration_approval_table').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_variable_salary_declaration_approval'); ?>",
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
                {"mData": "masterID"},
                {"mData": "docCode"},
                {"mData": "newDocumentDate"},
                {"mData": "Description"},
                {"mData": "transactionCurrency"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN").val()});
                aoData.push({"name": "isVariable", "value": 1});
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

    function fetch_approval(declarationMasterID, documentApprovedID, Level) {
        if (declarationMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'declarationMasterID': declarationMasterID, 'html': true, isFromApproval:'Y','isVariable':1},
                url: "<?php echo site_url('Employee/load_salary_approval_confirmation_view_variable'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#salaryOrderID').val(declarationMasterID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#salaryDeclaration_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    //purchaseOrder_attachment_View_modal('PO',purchaseOrderID);
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

</script>
