<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = 'SRN Approval';
echo head_page($title, false);


/*echo head_page('GRV Approval', false); */?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved');?>
                </td><!-- Approved-->
                <td><span class="label label-danger">&nbsp;</span><?php echo $this->lang->line('common_not_approved');?>
                </td><!--Not Approved-->
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
    <table id="grv_table_approval" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%">SRN Code </th><!--GRV Code-->
            <th style="min-width: 10%"><?php echo $this->lang->line('transaction_common_delivered_date');?> </th><!--Delivered Date-->
            <th style="min-width: 35%"><?php echo $this->lang->line('transaction_common_narration');?> </th><!--Narration-->
            <th style="min-width: 25%"><?php echo $this->lang->line('common_supplier_name');?> </th><!--Supplier Name-->
            <th style="min-width: 20%"><?php echo $this->lang->line('common_reference_no');?> </th><!--Reference No-->
            <th style="min-width: 15%"><?php echo $this->lang->line('common_value');?></th><!--Value-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_level');?></th><!--Level-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?></th><!--Status-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="grv_modal" tabindex="-1" role="dialog" aria-labelledby="grv_modal_lbl">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('transaction_approval_grv_approval');?></h4><!--GRV Approval-->
            </div>
            <div class="modal-body">
                <form id="grv_approval_form">
                    <div class="row">
                        <div class="col-sm-1">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#tabView" aria-controls="home" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('common_view');?>
                                    </a><!--   View-->
                                </li>
                                <li role="tab_attachment">
                                    <a href="#tab_attachment" aria-controls="profile" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('common_attachment');?>
                                    </a><!-- Attachment-->
                                </li>
                                <li role="tab_">
                                    <a href="#tab_subItemMaster" aria-controls="messages" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('transaction_common_item_master_sub');?>
                                    </a><!-- Item Master Sub-->
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content col-sm-11">
                            <div role="tabpanel" class="tab-pane active" id="tabView">

                                <div id="conform_body"></div>
                                <hr>
                                <div class="form-horizontal">
                                    <div class="form-group ">
                                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?></label><!--Status-->

                                        <div class="col-sm-4">
                                            <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('transaction_common_referred_back')/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                            <input type="hidden" name="Level" id="Level">
                                            <input type="hidden" name="grvAutoID" id="grvAutoID">
                                            <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?> </label><!--Comments-->

                                        <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments"
                                                  id="comments"></textarea>
                                        </div>
                                    </div>
                                    <div class="pull-right">
                                        <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('common_submit');?> </button><!--Submit-->
                                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                                    </div>
                                </div>

                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_attachment">
                                <div class="table-responsive">
                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                    &nbsp; <strong>SRN Attachments </strong><!--GRV Attachments-->
                                    <br><br>
                                    <table class="table table-striped table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo $this->lang->line('common_file_name');?> </th><!--File Name-->
                                            <th><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                                            <th><?php echo $this->lang->line('common_type');?> </th><!--Type-->
                                            <th><?php echo $this->lang->line('common_action');?> </th><!--Action-->
                                        </tr>
                                        </thead>
                                        <tbody id="grv_attachment_body" class="no-padding">
                                        <tr class="danger">
                                            <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?> </td><!--No Attachment Found-->
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_subItemMaster">
                                <h4><?php echo $this->lang->line('transaction_common_sub_item_configuration');?> </h4><!--Sub Item Configuration-->
                                <div id="itemMasterSubDiv">

                                </div>

                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var Otable;
    function load_itemMasterSub_approval(receivedDocumentID, grvAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                receivedDocumentID: receivedDocumentID,
                grvAutoID: grvAutoID
            },
            url: "<?php echo site_url('Grv/load_itemMasterSub_approval'); ?>",
            beforeSend: function () {
                $("#itemMasterSubDiv").html('<div  class="text-center" style="margin: 10px 0px;"><i class="fa fa-refresh fa-spin"></i></div>');
            },
            success: function (data) {
                $("#itemMasterSubDiv").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#itemMasterSubDiv").html('<br>Message:<br/> ' + errorThrown);
            }
        });
    }


    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/srn/srn_approval', 'Test', 'SRN Approval');
        });

        grv_table_approval();
        $('#grv_approval_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
                Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_level_order_status_is_required');?>.'}}},/*Level Order Status is required*/
                //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
                grvAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_grv_id_is_required');?>.'}}},/*GRV ID is required*/
                documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_grv_document_id_is_required');?>.'}}}/*Document Approved ID is required*/
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
                url: "<?php echo site_url('Grv/save_grv_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data == true) {
                        $("#grv_modal").modal('hide');
                        Otable.draw();
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

    function grv_table_approval() {
        Otable = $('#grv_table_approval').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Grv/fetch_grv_approval'); ?>",
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
                {"mData": "grvAutoID"},
                {"mData": "grvPrimaryCode"},
                {"mData": "deliveredDate"},
                {"mData": "details"},
                {"mData": "supplierName"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "total_value_search"},
                {"mData": "transactionCurrency"},
                {"mData": "grvDocRefNo"},
                {"mData": "grvNarration"}
                //{"mData": "edit"},
            ],
            "columnDefs": [{"searchable": true, "orderable": false, "visible": false, "targets": [9,10,11,12]},{"searchable": false, "targets": [0,5,7,8]}],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN :checked").val()});
                aoData.push({ "name": "documentID","value": 'SRN'});
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

    function fetch_approval(grvAutoID, documentApprovedID, Level) {
        if (grvAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'grvAutoID': grvAutoID, 'html': true, 'approval': 1},
                url: "<?php echo site_url('Grv/load_grv_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $("#Tab-profile-v").addClass("hide");
                    $('#grvAutoID').val(grvAutoID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#grv_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    grv_attachment_View_modal('SRN', grvAutoID);
                    load_itemMasterSub_approval('SRN', grvAutoID);
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

    function grv_attachment_View_modal(documentID, documentSystemCode) {
        $("#Tab-profile-v").removeClass("active");
        $("#Tab-home-v").addClass("active");
        $("#grv_attachement_approval_Tabview_a").removeClass("active");
        $("#grv_attachement_approval_Tabview_v").addClass("active");
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode,'confirmedYN': 0},
                success: function (data) {
                    $('#grv_attachment_body').empty();
                    $('#grv_attachment_body').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function tabAttachement() {
        $("#Tab-profile-v").removeClass("hide");
    }
    function tabView() {
        $("#Tab-profile-v").addClass("hide");
    }
</script>