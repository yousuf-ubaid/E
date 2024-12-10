<?php
/** Translation added */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('finance_budget_transfer_approval');
echo head_page($title, false);

?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved'); ?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_approved'); ?> <!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending'), '1' => $this->lang->line('common_approved')), '', 'class="form-control" id="approvedYN" required onchange="budget_transfer_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="budget_transfer_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 12%"><?php echo $this->lang->line('finance_budget_transfer_code'); ?><!--Budget Transfer Code--></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('common_created_date'); ?><!--Created Date--></th>
            <th style="min-width: 12%"><?php echo $this->lang->line('common_financial_year'); ?><!--Financial Year--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_narration'); ?><!--Narration--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="budget_transfer_Approval_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Budget Transfer Approval</h4>
            </div>
            <form class="form-horizontal" id="bdt_approval_form">
                <div class="modal-body">
                    <div class="row">
                        <div id="conform_body"></div>
                        <hr>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status'); ?><!--Status--></label>

                            <div class="col-sm-4">
                                <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                <input type="hidden" name="Level" id="Level">
                                <input type="hidden" name="budgetTransferAutoID" id="budgetTransferAutoID">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">
                                <?php echo $this->lang->line('common_comment'); ?><!--Comments--></label>

                            <div class="col-sm-8">
                                <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button type="submit" class="btn btn-primary">
                            <?php echo $this->lang->line('common_submit'); ?><!--Submit--></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/expenseClaim/expense_claim_approval', 'Test', '<?php echo $title; ?>')
        });

        budget_transfer_table();

        $('#bdt_approval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: 'Budget Transfer Status is required.'}}},
                budgetTransferAutoID: {validators: {notEmpty: {message: 'Budget Transfer ID is required.'}}}
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
                url: "<?php echo site_url('Budget_transfer/save_budget_transfer_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                    if(data == true){
                        $("#budget_transfer_Approval_modal").modal('hide');
                        budget_transfer_table();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }

                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

    });

    function budget_transfer_table() {
        var Otable = $('#budget_transfer_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Budget_transfer/fetch_budget_transfer_approval'); ?>",
            "aaSorting": [[0, 'desc']],
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
                {"mData": "budgetTransferAutoID"},
                {"mData": "documentSystemCode"},
                {"mData": "createdDate"},
                {"mData": "financeYear"},
                {"mData": "comments"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [6], "orderable": false},{"visible":true,"searchable": false,"targets": [0] }],
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

    function fetch_approval(budgetTransferAutoID,Level) {
        if (budgetTransferAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'budgetTransferAutoID': budgetTransferAutoID, 'html': true,'approval':1},
                url: "<?php echo site_url('Budget_transfer/load_budget_transfer_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#budgetTransferAutoID').val(budgetTransferAutoID);
                    $("#budget_transfer_Approval_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#Level').val(Level);
                    $('#comments').val('');
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function expenseClaim_attachment_View_modal(documentID, documentSystemCode) {
        $("#Tab-profile-v").removeClass("active");
        $("#Tab-home-v").addClass("active");
        $("#po_attachement_approval_Tabview_a").removeClass("active");
        $("#po_attachement_approval_Tabview_v").addClass("active");
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode,'confirmedYN': 0},
                success: function (data) {
                    $('#ec_attachment_body').empty();
                    $('#ec_attachment_body').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }






    function tabAttachement(){
        $("#Tab-profile-v").removeClass("hide");
    }
    function tabView(){
        $("#Tab-profile-v").addClass("hide");
    }
</script>