<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
$title = $this->lang->line('manufacturing_job');
echo head_page($title, false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();?>
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
        <?php echo form_dropdown('approvedYN', array('0' => 'Pending', '1' => 'Approved'), '', 'class="form-control" id="approvedYN" required onchange="job_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="job_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th class="text-uppercase" style="min-width: 10%"><?php echo $this->lang->line('common_code');?><!--CODE--></th>
            <th class="text-uppercase" style="min-width: 20%"><?php echo $this->lang->line('common_details');?><!--DETAILS--></th>
            <th class="text-uppercase" style="min-width: 5%"><?php echo $this->lang->line('common_level');?><!--LEVEL--></th>
            <th class="text-uppercase" style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--STATUS--></th>
            <th class="text-uppercase" style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--ACTION--></th>
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
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('manufacturing_job_approval');?><!--Job Approval--></h4>
            </div>
            <div class="modal-body">
                <div class="col-sm-1">
                    <!-- Nav tabs -->
                    <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                        <li id="po_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v"
                                                                                     data-toggle="tab"
                                                                                     onclick="tabView()"><?php echo $this->lang->line('common_view');?><!--View--></a></li>
                        <li id="po_attachement_approval_Tabview_a"><a href="#Tab-checklist-v" data-toggle="tab"
                                                                      onclick="tabCheckList()"><?php echo 'Checklist';?><!--Attachment--></a>
                        <li id="po_attachement_approval_Tabview_a"><a href="#Tab-profile-v" data-toggle="tab"
                                                                      onclick="tabAttachement()"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a>
                        

                        </li>
                    </ul>
                </div>
                <div class="col-sm-11">
                    <div class="zx-tab-content">
                        <div class="zx-tab-pane active" id="Tab-home-v">
                            <div id="confirm_body"></div>
                            <hr>
                            <form class="form-horizontal" id="job_approval_form">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('po_status', array('' => 'Please Select', '1' => 'Approved', '2' => 'Referred-back'), '', 'class="form-control" id="po_status" required'); ?>
                                        <input type="hidden" name="Level" id="Level">
                                        <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                        <input type="hidden" name="workProcessID" id="workProcessID2">
                                        <input type="hidden" name="jobcardID" id="jobcardID">
                                        <input type="hidden" name="maxLevel" id="maxLevel" value="0">
                                    </div>
                                </div>
                                <div class="form-group" id="financeDate">
                                    <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('manufacturing_finance_date');?><!--Finance Date--></label>

                                    <div class="col-sm-8">
                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                <div class='input-group date filterDate' id="">
                                                    <input type='text' class="form-control"
                                                           name="postingFinanceDate"
                                                           id="postingFinanceDate"
                                                           value="<?php echo $current_date; ?>"
                                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                    <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>

                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments"
                                                  id="comments"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane hide" id="Tab-profile-v">
                            <div class="table-responsive">
                                <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                &nbsp <strong><?php echo $this->lang->line('manufacturing_job_attachment');?><!--Job Attachments--></strong>
                                <br><br>
                                <table class="table table-striped table-condensed table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $this->lang->line('common_file_name');?><!--File Name--></th>
                                        <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                                        <th><?php echo $this->lang->line('common_type');?><!--Type--></th>
                                        <th><?php echo $this->lang->line('common_action');?><!--Action--></th>
                                    </tr>
                                    </thead>
                                    <tbody id="po_attachment_body" class="no-padding">
                                    <tr class="danger">
                                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane hide" id="Tab-checklist-v">
                            <div class="table-responsive">
                                <div id="checklist_div"> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">&nbsp;
            </div>

        </div>
    </div>
</div>


<div class="modal" tabindex="-1" role="dialog" id="invalidinvoicemodal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('manufacturing_stock_insufficient');?><!--Stock Insufficient--></h4>
            </div>
            <div class="modal-body">
                <div >
                    <table  class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('manufacturing_item_code');?><!--Item Code--></th>
                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <th><?php echo $this->lang->line('manufacturing_current_stock');?><!--Current Stock--></th>
                        </tr>
                        </thead>
                        <tbody id="errormsg">
                        </tbody>
                    </table>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="linkedDoc_notApproved_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="50%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Document Not Approved</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="" class="table table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="min-width: 12%">Document System Code</th>
                                    <th style="min-width: 12%">Document Type</th>
                                    <th style="min-width: 12%">Document Date</th>
                                </tr>
                                </thead>
                                <tbody id="table_body_linkedDoc">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    var workProcessIDTemp = '';
    $(document).ready(function () {
        $('.filterDate').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });
        $('#financeDate').hide();
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_job_approval', '', 'Job');
        });
        job_table();

        $('#job_approval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: 'Status is required.'}}},
                Level: {validators: {notEmpty: {message: 'Level Order Status is required.'}}},
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
                url: "<?php echo site_url('MFQ_Job/save_job_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $("#jv_modal").modal('hide');
                        job_table();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    } else if(data[2]) {
                        $("#jv_modal").modal('hide');
                        $('#errormsg').empty();
                        $.each(data[2], function (key, value) {
                            $('#errormsg').append('<tr><td>' + value['itemCode'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['currentStock'] + '</td></tr>');
                        });
                        $('#invalidinvoicemodal').modal('show');
                    } else if (data[3]) {
                    $("#closeDateModal").modal("hide");
                    $("#table_body_linkedDoc").html("");
                    var i = 1;
                    $.each(data[3], function (k, v) {
                        $("#table_body_linkedDoc").append("<tr><td>" + i + "</td><td>" + v.documentCode + "</td><td>" + v.documentType + "</td><td>" + v.documentDate + "</td></tr>");
                        i++;
                    });
                    $("#linkedDoc_notApproved_modal").modal();
                }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function job_table() {
        var Otable = $('#job_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_Job/fetch_job_approval'); ?>",
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
                {"mData": "workProcessID"},
                {"mData": "documentCode"},
                {"mData": "detail"},
                {"mData": "level"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [2,5], "orderable": false},{"targets": [0,2,3,4,5], "searchable": false}],
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

    function fetch_approval(workProcessID, documentApprovedID, Level, jobID,finalApproval,financeDate) {
        if (workProcessID) {

            workProcessIDTemp = workProcessID;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'workProcessID': workProcessID, jobCardID: jobID, 'html': true},
                url: "<?php echo site_url('MFQ_Job/fetch_job_approval_print'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $("#jv_modal").modal({backdrop: "static"});
                    $('#confirm_body').html(data);
                    $('#workProcessID2').val(workProcessID);
                    $('#jobcardID').val(jobID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $('#comments').val('');
                    if(Level == finalApproval){
                        $('#maxLevel').val(1);
                        $("#financeDate").val(financeDate).change();
                        $('#financeDate').show();
                    }else{
                        $('#maxLevel').val(0);
                        $('#financeDate').show();
                    }
                    //job_attachment_view_modal('JOB', workProcessID);
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

    function tabCheckList(){

        $("#Tab-profile-v").addClass("hide");
        $("#Tab-checklist-v").removeClass("hide");

        $.ajax({
            // async: true,
            type: 'post',
           // dataType: 'html',
            data: {'workProcessID': workProcessIDTemp},
            url: "<?php echo site_url('MFQ_Job/fetch_checklist'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                $('#checklist_div').empty();
                $('#checklist_div').html(data);


            }, error: function () {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });

    }

    function job_attachment_view_modal(documentID, documentSystemCode) {
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
                    $('#po_attachment_body').empty();
                    $('#po_attachment_body').append('' +data+ '');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function tabAttachement() {
        $("#Tab-profile-v").removeClass("hide");
        $("#Tab-checklist-v").addClass("hide");
    }

    function tabView() {
        $("#Tab-profile-v").addClass("hide");
        $("#Tab-checklist-v").addClass("hide");
    }


</script>