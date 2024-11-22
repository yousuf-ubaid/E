<?php
/** Translation added  */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = 'Personal Application Approvals';
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
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending'), '1' => $this->lang->line('common_approved')), '', 'class="form-control select2" id="approvedYN" required onchange="personal_approval_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="personal_application_approve_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?></th>
            <th style="min-width: 30%"><?php echo $this->lang->line('common_details'); ?><!--Details--></th>
            <th style="min-width: 10%">Action Type</th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="personal_application_approval_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Personal Application Approval</h4>
            </div>
            <form class="form-horizontal" id="PAA_approval_form">
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
                                    &nbsp; <strong>Personal Action Attachments</strong>
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

<!-- approval user model -->
<div class="modal fade" id="paa_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ap_user_label"><?php echo $this->lang->line('profile_approval_users'); ?><!--Approval user--></h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt><?php echo $this->lang->line('profile_document_code'); ?><!--Document code--></dt>
                    <dd id="c_document_code">...</dd>
                    <dt><?php echo $this->lang->line('common_date'); ?><!--Document Date--></dt>
                    <dd id="c_document_date">...</dd>
                    <dt><?php echo $this->lang->line('common_confirmed_date'); ?><!--Confirmed Date--></dt>
                    <dd id="c_confirmed_date">...</dd>
                    <dt><?php echo $this->lang->line('common_confirmed_by'); ?><!--Confirmed By-->&nbsp;&nbsp;</dt>
                    <dd id="c_conformed_by">...</dd>
                </dl>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th><?php echo $this->lang->line('common_level'); ?><!--Level--></th>
                        <th><?php echo $this->lang->line('common_approved_date'); ?><!--Approved Date--></th>
                        <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                        <th><?php echo $this->lang->line('common_comment'); ?><!--Comments--></th>
                    </tr>
                    </thead>
                    <tbody id="ap_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_confirm'); ?><!--Document not approved yet--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/hrm/personal_application_approval', 'Test', '<?php echo $title; ?>')
        });

        personal_approval_table();

        $('#PAA_approval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                po_status: {validators: {notEmpty: {message: 'Personal Application Status is required.'}}},
                id: {validators: {notEmpty: {message: 'Personal Application ID is required.'}}}
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
                url: "<?php echo site_url('Employee/personal_application_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                    //if(data == true){
                        $("#personal_application_approval_model").modal('hide');
                        personal_approval_table();
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

    //ok
    function personal_approval_table() {
        var Otable = $('#personal_application_approve_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_personal_application_approval'); ?>",
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
                {"mData": "id"},
                {"mData": "documentCode"},
                {"mData": "PAA_detail"},
                {"mData": "actionType"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "columnDefs": [{"targets": [0], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN option:selected").val()});
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

    //ok
    function fetch_approval(id, approvedID, level) {
        if (id) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'id': id, 'html': true,'approval':1},
                url: "<?php echo site_url('Employee/load_personal_action_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#paa_status').val('').change();
                    $('#id').val(id);
                    $('#level').val(level);
                    $('#documentApprovedID').val(approvedID);
                    $("#personal_application_approval_model").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                   paa_attachment_View_modal('PAA',id);
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

    //ok
    function paa_attachment_View_modal(documentID, documentSystemCode) {
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

 
    function fetch_approval_user_modal_pa(documentID, documentSystemCode) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
            url: '<?php echo site_url('Employee/fetch_approval_user_modal_pa'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#ap_user_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp; Approval user');
                $('#ap_user_body').empty();
                x = 1;

                if (jQuery.isEmptyObject(data)) {
                    $('#ap_user_body').append('<tr class="danger"><td colspan="3" class="text-center"><b>No Records Found</b></td></tr>');
                } else {

                     $.each(data['approved'], function(i, item) {
                        comment = ' - ';
                        approvedDate = ' - ';
                        if (item['approvedComments']) {
                            comment = item['approvedComments'];
                        }
                        if (item['approvedDate']) {
                            approvedDate = item['approvedDate'];
                        }
                        bePlanVar = (item['approvedYN'] == true) ? '<span class="label label-success">&nbsp;</span>' : '<span class="label label-danger">&nbsp;</span>';
                        $('#ap_user_body').append('<tr><td>' + x + '</td><td>'  +  item['Ename2'] + '</td><td class="text-center"> Level '  + item['approvalLevelID'] + '</td><td class="text-center">  ' + approvedDate + '</td><td class="text-center">' + bePlanVar + '</td><td>' + comment + '</td></tr>');
                     x++;
                    });
                }

                $("#paa_user_modal").modal({backdrop: "static", keyboard: true});
                $("#c_document_code").html(data['documentCode']);
                $("#c_document_date").html(data['documentDate']);
                $("#c_confirmed_date").html(data['confirmedDate']);
                $("#c_conformed_by").html(data['confirmedByName']);
                stopLoad();
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                // swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
</script>