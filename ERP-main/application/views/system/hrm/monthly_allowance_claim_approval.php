<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('hrms_payroll_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = 'Monthly Allowance Claim Approval';
$titleTab2 = $this->lang->line('hrms_payroll_general_cancellation_approval');
echo head_page($title, false);

$status_arr = [
    '0' => $this->lang->line('common_pending'),
    '1' => $this->lang->line('common_approved')
];
$leaveTypes = leaveTypes_drop();
?>

<style type="text/css">
.frm_input{
    height: 28px;
    font-size: 12px;
}

.panel-body {
    margin-bottom: 20px;
    background-color: #ffffff;
    border: 1px solid #dddddd;
    border-radius: 4px;
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
}
</style>

<div id="filter-panel" class="collapse filter-panel"></div>


<div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
    <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
        <li class="active">
            <a href="#approvelTab" data-toggle="tab" aria-expanded="true"><?php echo $title;?> </a>
        </li>
        <li class="">
            <a href="#cancellationAppTab" data-toggle="tab" aria-expanded="false"><?php echo $titleTab2;?></a>
        </li>
    </ul>
    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">

        <div class="tab-pane active disabled" id="approvelTab">
            <div class="row">
                <div class="col-md-5">
                    <table class="<?php echo table_class(); ?>">
                        <tbody>
                        <tr>
                            <td>
                                <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_approved');?><!--Approved-->
                            </td>
                            <td>
                                <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>  <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4 text-center">
                    &nbsp;
                </div>
                <div class="col-md-3 text-center">
                    <?php echo form_dropdown('approvedYN', $status_arr, '','class="form-control" id="approvedYN" onchange="expence_claim_approval_table()"'); ?>
                </div>
            </div><hr>
            <div class="table-responsive">
                <table id="expence_claim_approval_table" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        
                        <th style="width: 5%">#</th>
                        <th style="width: 12%"><?php echo $this->lang->line('hrms_payroll_employee');?></th>
                        <th style="width: 10%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                        <th style="width: 15%"><?php echo $this->lang->line('common_type');?></th>
                        <th style="width: 20%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                        <th style="width: 8%">Document Date<!--Date--></th>
                        <th style="width: 8%">From Date<!--From Date--></th>
                        <th style="width: 8%">To Date<!--To Date--></th>
                        <th style="width: 8%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                        <th style="width: 6%">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="tab-pane" id="cancellationAppTab">
            <div class="row">
                <div class="col-md-5">
                    <table class="<?php echo table_class(); ?>">
                        <tbody>
                        <tr>
                            <td>
                                <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_approved');?><!--Approved-->
                            </td>
                            <td>
                                <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>  <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4 text-center">
                    &nbsp;
                </div>
                <div class="col-md-3 text-center">
                    <?php echo form_dropdown('approvedCancelYN', $status_arr, '','class="form-control" id="approvedCancelYN" onchange="expense_claim_approval_cancel_table()"'); ?>
                </div>
            </div><hr>
            <div class="table-responsive">
                <table id="expense_claim_approval_cancel_table" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 10%"><?php echo $this->lang->line('hrms_payroll_employee');?></th>
                        <th style="width: 10%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
                        <th style="width: 15%"><?php echo $this->lang->line('common_type');?></th>
                        <th style="width: 20%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                        <th style="width: 8%">Document Date<!--Date--></th>
                        <th style="width: 8%">From Date<!--From Date--></th>
                        <th style="width: 8%">To Date<!--To Date--></th>
                        <th style="width: 8%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                        <th style="width: 8%">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="expence_claim_approval_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Monthly Allowance Approval</h4>
            </div>
            <form class="form-horizontal" id="expense_claim_approval_form">
                <div class="modal-body">
                    <div class="col-sm-1">
                        <!-- Nav tabs -->
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v" data-toggle="tab" onclick="tabView()">
                                    <?php echo $this->lang->line('common_view'); ?><!--View--></a></li>
                            <li id="attachement_approval_Tabview_a"><a href="#Tab-profile-v" data-toggle="tab" onclick="tabAttachement()">
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
                                        <?php echo form_dropdown('po_status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="paa_status" required'); ?>
                                        <input type="hidden" name="level" id="level">
                                        <input type="hidden" name="id" id="id">
                                        <input type="hidden" name="documentApprovedID" id="documentApprovedID">
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
                                    &nbsp; <strong>Monthly Allowance Claim Attachments</strong>
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
                                        <tbody id="attachment_body_conf" class="no-padding">
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

<?php echo footer_page('Right foot','Left foot',false); ?>

<script type="text/javascript">

    var entitleSpan = $('#entitleSpan');
    var takenSpan = $('#takenSpan');
    var balanceSpan = $('#balanceSpan');
    var policySpan = $('#policySpan');

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/monthly_allowance_claim_approval','','HRMS');
        });

        expence_claim_approval_table();

        //expense_claim_approval_cancel_table();

        $('#expense_claim_approval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                po_status: {validators: {notEmpty: {message: 'Monthly Allowance Claim Status is required.'}}},
                id: {validators: {notEmpty: {message: 'Monthly Allowance Claim ID is required.'}}},
                level: {validators: {notEmpty: {message: 'Monthly Allowance Claim level is required.'}}}
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
                url: "<?php echo site_url('Employee/monthlyAllowance_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                    $("#expence_claim_approval_model").modal('hide');
                    expence_claim_approval_table();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
            
       
    });


    function expence_claim_approval_table(){
        var Otable = $('#expence_claim_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_monthly_allowance_approval'); ?>",
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
                {"mData": "masterID"},
                {"mData": "employee"},
                {"mData": "monthlyClaimCode"},
                {"mData": "typeDescription"},
                {"mData": "des"},
                {"mData": "documentDate"},
                {"mData": "startDate"},
                {"mData": "endDate"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [0,6,7], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "approvedYN","value": $("#approvedYN :selected").val()});
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

    function expense_claim_approval_cancel_table(){
        var Otable = $('#expense_claim_approval_cancel_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_expence_claim_cancellation_approval'); ?>",
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
                {"mData": "leaveMasterID"},
                {"mData": "documentCode"},
                {"mData": "empName"},
                {"mData": "startDate"},
                {"mData": "endDate"},
                {"mData": "levelNo"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [0,6,7], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "approvedYN","value": $("#approvedCancelYN :selected").val()});
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

    function display(a){
        if (a >= 0) {
            var hours = Math.trunc(a/60);
            var minutes = a % 60;

            return hours +"h :"+ minutes+"m";
        }
        else{
            a=Math.abs(a);
            var hours = Math.trunc(a/60);
            var minutes = a % 60;

            return "-"+hours +"h :"+ minutes+"m";
        }

    }

    function fetch_approval(id, approvedID, level) {
        if (id) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'id': id, 'html': true,'approval':1},
                url: "<?php echo site_url('Employee/monthly_allowance_print'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#paa_status').val('').change();
                    $('#id').val(id);
                    $('#level').val(level);
                    $('#documentApprovedID').val(approvedID);
                    $("#expence_claim_approval_model").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    attachment_modal_conf(id, 'MAC');
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

    function attachment_modal_conf(documentSystemCode, documentID ) {
        $("#Tab-profile-v").removeClass("active");
        $("#Tab-home-v").addClass("active");
        $("#attachement_approval_Tabview_a").removeClass("active");
        $("#attachement_approval_Tabview_v").addClass("active");
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode,'confirmedYN': 1},
                success: function (data) {
                    $('#attachment_body_conf').empty();
                    $('#attachment_body_conf').append('' +data+ '');

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


<?php
