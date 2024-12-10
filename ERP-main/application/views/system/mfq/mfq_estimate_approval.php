<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
$title = $this->lang->line('manufacturing_estimate');
echo head_page($title, false); ?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved'); ?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span>  <?php echo $this->lang->line('common_not_approved'); ?><!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => 'Pending', '1' => 'Approved'), '', 'class="form-control" id="approvedYN" required onchange="estimate_table()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="estimate_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th class="text-uppercase" style="min-width: 5%">#</th>
            <th class="text-uppercase" style="min-width: 10%"><?php echo $this->lang->line('common_code'); ?><!--CODE--></th>
            <th class="text-uppercase" style="min-width: 20%"><?php echo $this->lang->line('common_details'); ?><!--DETAILS--></th>
            <th class="text-uppercase" style="min-width: 5%"><?php echo $this->lang->line('common_level'); ?><!--LEVEL--></th>
            <th class="text-uppercase" style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?><!--STATUS--></th>
            <th class="text-uppercase" style="min-width: 10%"><?php echo $this->lang->line('common_action'); ?><!--ACTION--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="jv_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('manufacturing_estimate_approval'); ?><!--Estimate Approval--></h4>
            </div>
            <form class="form-horizontal" id="estimate_approval_form">
                <div class="modal-body">
                    <div class="col-sm-1">
                        <!-- Nav tabs -->
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="po_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v" data-toggle="tab" onclick="tabView()"><?php echo $this->lang->line('common_view'); ?><!--View--></a></li>
                            <li id="po_attachement_approval_Tabview_a"><a href="#Tab-profile-v" data-toggle="tab" onclick="tabAttachement()"><?php echo $this->lang->line('common_attachment'); ?><!--Attachment--></a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">

                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group col-sm-4 md-offset-2">
                                            <label class="title">Discount : </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <?php echo form_dropdown('discountView', array('1'=> 'View Discount', '0'=>'Hide Discount'), '0', ' onchange="viewDiscount_approval()" class="form-control" id="est-discountView"'); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-group" id="hide_total_row">
                                            <span class="input-group-addon">
                                                <input type="checkbox" name="hideMargin" id="hideMargin" value="0" onclick="viewDiscount_approval()">
                                            </span>
                                            <input type="text" class="form-control" disabled="" value="View Discount And Margin">
                                        </div>
                                    </div>
                                </div>

                                <div id="confirm_body"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status'); ?><!--Status--></label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('po_status', array('' => 'Please Select','1' => 'Approved', '2' => 'Referred-back'), '', 'class="form-control" id="po_status" required'); ?>
                                        <input type="hidden" name="Level" id="Level">
                                        <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                        <input type="hidden" name="estimateMasterID" id="estimateMasterID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments'); ?><!--Comments--></label>

                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit'); ?><!--Submit--></button>
                                </div>
                            </div>
                            <div class="tab-pane hide" id="Tab-profile-v">
                                <div class="table-responsive">
                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                    &nbsp <strong><?php echo $this->lang->line('manufacturing_estimate_attachment'); ?><!--Estimate Attachments--></strong>
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
                                        <tbody id="po_attachment_body" class="no-padding">
                                        <tr class="danger">
                                            <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
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

<div class="modal fade" id="BOM_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-capitalize" id="myModalLabel"><?php echo $this->lang->line('manufacturing_bom') ?><!--BOM--></h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <div id="bom_print">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_estimate_approval','','Estimate');
        });

        $('.modal').on('hidden.bs.modal', function () {
            setTimeout(function () {
                if ($('.modal').hasClass('in')) {
                    $('body').addClass('modal-open');
                }
            }, 500);
        });
        estimate_table();

        $('#estimate_approval_form').bootstrapValidator({
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
                url: "<?php echo site_url('MFQ_Estimate/save_estimate_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0],data[1]);
                    if(data[0] == 's'){
                        $("#jv_modal").modal('hide');
                        estimate_table();
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

    function estimate_table() {
        var Otable = $('#estimate_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_Estimate/fetch_estimate_approval'); ?>",
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
                {"mData": "estimateMasterID"},
                {"mData": "estimateCode"},
                {"mData": "detail"},
                {"mData": "level"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [2,5], "orderable": false}, {"targets": [0,2,3,4,5], "searchable": false}, {"targets": [1], "searchable": true}],
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

    function fetch_approval(estimateMasterID, documentApprovedID, Level) {
        if (estimateMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'estimateMasterID': estimateMasterID, 'html': true},
                url: "<?php echo site_url('MFQ_Estimate/fetch_estimate_print'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#estimateMasterID').val(estimateMasterID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#jv_modal").modal({backdrop: "static"});
                    $('#confirm_body').html(data);
                    $('#comments').val('');
                    estimate_attachment_view_modal('EST', estimateMasterID);
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
    function estimate_attachment_view_modal(documentID, documentSystemCode) {
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

    function tabAttachement(){
        $("#Tab-profile-v").removeClass("hide");
    }
    function tabView(){
        $("#Tab-profile-v").addClass("hide");
    }

    function viewItemBOM(bomMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                bomMasterID: bomMasterID,
                html: true
            },
            url: "<?php echo site_url('MFQ_Estimate/fetch_item_bom'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#bom_print").html(data);
                $("#BOM_modal").modal();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function viewDiscount_approval() {
        var estimateMasterID = $('#estimateMasterID').val();
        var discountView = $('#est-discountView').val();
        var hideMargin = ($('#hideMargin').prop('checked'))? '1' : '0';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                discountView: discountView,
                estimateMasterID: estimateMasterID,
                hideMargin: hideMargin,
                html: true
            },
            url: "<?php echo site_url('MFQ_Estimate/change_discount_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data) {
                    $('#confirm_body').html(data);
                    // $("#estimate_print_modal").modal();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
</script>