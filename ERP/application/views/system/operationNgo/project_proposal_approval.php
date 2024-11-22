<?php
$primaryLanguage = getPrimaryLanguage();


echo head_page('Project Proposal', false); ?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    Approved
                </td>
                <td><span class="label label-danger">&nbsp;</span> Not Approved
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => 'Pending', '1' => 'Approved'), '', 'class="form-control" id="approvedYN" required onchange="project_proposal_approval()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="project_proposal_approval" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%">Code</th>
            <th style="min-width: 20%">Details</th>
            <th style="min-width: 5%">Level</th>
            <th style="min-width: 5%">Status</th>
            <th style="min-width: 10%">Action</th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="jv_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Project Proposal Approval</h4>
            </div>
            <form class="form-horizontal" id="donor_collection_form">
                <div class="modal-body">
                    <div class="col-sm-1">

                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                <div id="conform_body"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Status</label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('project_status', array('' => 'Please Select', '1' => 'Approved','2' => 'Referred-back'), '', 'class="form-control" id="project_status" required'); ?>
                                        <input type="hidden" name="Level" id="Level">
                                        <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                        <input type="hidden" name="proposalid" id="proposalid">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label">Comments</label>

                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments"
                                                  id="comments"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo $this->lang->line('common_submit'); ?><!--Submit--></button>
                                </div>
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
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/project_proposal_approval', '', 'Project Proposal');
        });
        project_proposal_approval();

        $('#donor_collection_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}}, /*Status is required*/
                Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_level_order_status_is_required');?>.'}}}, /*Level Order Status is required*/
                documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_approved_id_is_required');?>.'}}}/*Document Approved ID is required*/
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
                url: "<?php echo site_url('OperationNgo/save_project_proposal_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data == true) {
                        $("#jv_modal").modal('hide');
                        project_proposal_approval();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function project_proposal_approval() {
        var Otable = $('#project_proposal_approval').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('OperationNgo/project_proposal_table_approval'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "proposalID"},
                {"mData": "documentSystemCode"},
                {"mData": "detail"},
                {"mData": "level"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN :checked").val()});
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

    function fetch_approval(proposalID, documentApprovedID, Level) {
        if (proposalID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'proposalID': proposalID, 'html': true},
                url: "<?php echo site_url('OperationNgo/load_project_proposal_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#proposalid').val(proposalID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#jv_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }


</script>