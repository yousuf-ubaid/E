<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('project_management', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('promana_pm_project_approval');
echo head_page($title, false);

/*echo head_page('Project Approval', false); */?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_approved');?> <!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending')/*'Pending'*/, '1' => $this->lang->line('common_approved')/*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="Otable.draw()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="project_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style=""> <?php echo $this->lang->line('promana_pm_project_code');?><!--Project Code--></th>
            <th style=""><?php echo $this->lang->line('promana_common_project_name');?><!--Project Name--></th>

            <th style=""><?php echo $this->lang->line('common_customer_name');?><!--Customer Name--></th>
            <th style=""><?php echo $this->lang->line('common_start_date');?><!--StartDate--></th>
            <th style=""><?php echo $this->lang->line('common_end_date');?><!--End Date--></th>
            <th style=""><?php echo $this->lang->line('promana_common_approved_yn');?><!--Approved YN--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
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
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('promana_pm_project_approval');?><!--Project Approval--></h4>
            </div>
            <form class="form-horizontal" id="JE_approval_form">
                <div class="modal-body">
                    <div class="col-sm-1">
                        <!-- Nav tabs -->
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="bt_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v"
                                                                                         data-toggle="tab" onclick="tabView()"><?php echo $this->lang->line('common_view');?><!--View--></a></li>
                            <li id="bt_attachement_approval_Tabview_a"><a href="#Tab-profile-v" data-toggle="tab" onclick="tabAttachement()"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                <div id="conform_body"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('status', array('1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_referred_back')/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                        <input type="hidden" name="Level" id="Level">
                                        <input type="hidden" name="headerID" id="headerID">
                                        <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>

                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                                </div>
                            </div>
                            <div class="tab-pane hide" id="Tab-profile-v">
                                <div class="table-responsive">
                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                    &nbsp <strong><?php echo $this->lang->line('common_attachments');?><!--Attachments--></strong>
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
                                        <tbody id="bt_attachment_body" class="no-padding">
                                        <tr class="danger">
                                            <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/pm/project_approval', '', 'Project Management Approval');
        });
        project_table();
        $('#JE_approval_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_project_status_is_required');?>.'}}},/*Project Status is required*/
                Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_level_order_is_required');?>.'}}},/*Level Order Status is required*/
                //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},

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
                url: "<?php echo site_url('Boq/insert_project_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                    if(data == true){
                        $("#jv_modal").modal('hide');
                        project_table();
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

    function project_table() {
        Otable = $('#project_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Boq/fetch_project_approval'); ?>",
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
                {"mData": "headerID"},
                {"mData": "projectCode"},
                {"mData": "projectName"},
                {"mData": "customerName"},
                {"mData": "projectDateFrom"},
                {"mData": "projectDateTo"},
                {"mData": "approved"},
                {"mData": "edit"}
                //{"mData": "edit"},
            ],
            "columnDefs": [{"targets": [0,6,7], "searchable": false}],
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

    function fetch_approval(headerID, documentApprovedID, Level) {
        if (headerID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'headerID': headerID, 'html': true},
                url: "<?php echo site_url('Boq/project_summary'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#headerID').val(headerID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#jv_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    bankTransfer_attachment_View_modal('BT', headerID);
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
    function bankTransfer_attachment_View_modal(documentID, documentSystemCode) {
        $("#Tab-profile-v").removeClass("active");
        $("#Tab-home-v").addClass("active");
        $("#bt_attachement_approval_Tabview_a").removeClass("active");
        $("#bt_attachement_approval_Tabview_v").addClass("active");
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode,'confirmedYN': 0},
                success: function (data) {
                    $('#bt_attachment_body').empty();
                    $('#bt_attachment_body').append('' +data+ '');

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