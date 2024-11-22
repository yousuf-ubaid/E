<?php
/** Translation added  */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('profile_expense_claim_approval');
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
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending'), '1' => $this->lang->line('common_approved')), '', 'class="form-control" id="approvedYN" required onchange="expense_claim_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="expanse_claim_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?> <!--EC Number--></th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_details'); ?><!--Details--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('profile_total_value'); ?><!--Total Value--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="expense_claim_Approval_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('profile_expense_claim_approval'); ?><!--Expense Claim Approval--></h4>
            </div>
            <form class="form-horizontal" id="ec_approval_form">
                <div class="modal-body">
                    <div class="col-sm-1">
                        <!-- Nav tabs -->
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="po_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v" data-toggle="tab" onclick="tabView()">
                                    <?php echo $this->lang->line('common_view'); ?><!--View--></a></li>
                            <li id="po_attachement_approval_Tabview_a"><a href="#Tab-profile-v" data-toggle="tab" onclick="tabAttachement()">
                                    <?php echo $this->lang->line('common_attachments'); ?><!--Attachment--></a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                <div id="conform_body"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status'); ?><!--Status--></label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('ec_status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="ec_status" required'); ?>
                                        <!-- <input type="hidden" name="Level" id="Level"> -->
                                        <input type="hidden" name="expenseClaimMasterAutoID" id="expenseClaimMasterAutoID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label">
                                        <?php echo $this->lang->line('common_comment'); ?><!--Comments--></label>

                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo $this->lang->line('common_submit'); ?><!--Submit--></button>
                                </div>
                            </div>
                            <div class="tab-pane hide" id="Tab-profile-v">
                                <div class="table-responsive">
                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                    &nbsp; <strong><?php echo $this->lang->line('common_expense_claim_attachments'); ?><!--Expense Claim Attachments--></strong>
                                    <br><br>
                                    <table class="table table-striped table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                                            <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                            <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                                            <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                                        </tr>
                                        </thead>
                                        <tbody id="ec_attachment_body" class="no-padding">
                                        <tr class="danger">
                                            <td colspan="5" class="text-center">
                                                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Attachment Found--></td>
                                        </tr>
                                        </tbody>
                                    </table>
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
    var expenseclaimListSync = [];
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/expenseClaim/expense_claim_approval', 'Test', '<?php echo $title; ?>')
        });

        expense_claim_table();

        $('#ec_approval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                ec_status: {validators: {notEmpty: {message: 'Expense Claim Status is required.'}}},
                expenseClaimMasterAutoID: {validators: {notEmpty: {message: 'Expense Claim ID is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'expenseclaimListSync[]', 'value': expenseclaimListSync});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('ExpenseClaim/save_expense_Claim_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                    //if(data == true){
                        $("#expense_claim_Approval_modal").modal('hide');
                        expense_claim_table();
                        // $form.bootstrapValidator('disableSubmitButtons', false);
                    //}

                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

    });
    function expense_claim_selected_check(selected_YN) {
       var value = $(selected_YN).val();
       if ($(selected_YN).is(':checked')) {
        var i = expenseclaimListSync.indexOf(value);
           if (i != -1) {
               expenseclaimListSync.splice(i, 1);
           }
       }
       else {
        var inArray = $.inArray(value, expenseclaimListSync);
           if (inArray == -1) {
            expenseclaimListSync.push(value);
           }

       }
       }
    function expense_claim_table() {
        var Otable = $('#expanse_claim_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('ExpenseClaim/fetch_expanse_claim_approval'); ?>",
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
                {"mData": "expenseClaimMasterAutoID"},
                {"mData": "expenseClaimCode"},
                {"mData": "Ec_detail"},
                {"mData": "total_value"},
                {"mData": "approved"},
                {"mData": "edit"}
                //{"mData": "edit"},
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "columnDefs": [{"targets": [0], "searchable": false}],
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

    function fetch_approval(expenseClaimMasterAutoID) {
        if (expenseClaimMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'expenseClaimMasterAutoID': expenseClaimMasterAutoID, 'html': true,'approval':1},
                url: "<?php echo site_url('ExpenseClaim/load_expense_claim_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#ec_status').val('').change();
                    $('#expenseClaimMasterAutoID').val(expenseClaimMasterAutoID);
                    $("#expense_claim_Approval_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                   expenseClaim_attachment_View_modal('EC',expenseClaimMasterAutoID);
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