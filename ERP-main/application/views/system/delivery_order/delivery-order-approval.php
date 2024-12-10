<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_marketing_delivery_order_approval');
echo head_page($title, false);
$status_arr = array(
    '0' => $this->lang->line('common_pending')/*'Pending'*/,
    '1' => $this->lang->line('common_approved')/*'Approved'*/
);
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_approved');?> <!--Approved--> </td>
                <td><span class="label label-danger">&nbsp;</span>   <?php echo $this->lang->line('common_not_approved');?><!--Not Approved--> </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center"> &nbsp; </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', $status_arr, '', 'class="form-control" id="approvedYN" required onchange="oTable.draw()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="approval_tbl" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 3%">#</th>
            <th style="min-width: 10%"> <?php echo $this->lang->line('common_code');?></th>
            <th style="min-width: 12%"><?php echo $this->lang->line('common_document_date');?></th>
            <th style="min-width: 28%"><?php echo $this->lang->line('sales_markating_narration');?><!--Narration--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('sales_markating_party_name');?><!--Party Name--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_reference_no');?><!--Reference No--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_value');?><!--Value--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_level');?><!--Level--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
            <th style="width: 3px"></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="approval_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 90%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title;?></h4>
            </div>
            <form class="form-horizontal" id="approval_form">
                <div class="modal-body" style="overflow: overlay;">
                    <div class="col-sm-1">
                        <!-- Nav tabs -->
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="view-tab-container" class="active">
                                <a href="#view-tab" data-toggle="tab" onclick="tabView()"><?php echo $this->lang->line('common_view');?><!--View--></a>
                            </li>
                            <li id="attachment-tab-container">
                                <a href="#attachment-tab" data-toggle="tab" onclick="tabAttachment()"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="view-tab">
                                <div id="conform_body"></div>

                                <hr/>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' =>  $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                        <input type="hidden" name="level" id="level">
                                        <input type="hidden" name="orderAutoID" id="orderAutoID">
                                        <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>

                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane hide" id="attachment-tab">
                                <div class="table-responsive">
                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                    &nbsp <strong><?php echo $this->lang->line('sales_markating_invoice_attachments');?></strong><!--Invoice Attachments-->
                                    <br><br>
                                    <table class="table table-striped table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo $this->lang->line('common_file_name');?></th><!--File Name-->
                                            <th><?php echo $this->lang->line('common_description');?></th></th><!--Description-->
                                            <th><?php echo $this->lang->line('common_type');?></th><!--Type-->
                                            <th><?php echo $this->lang->line('common_action');?></th><!--Action-->
                                        </tr>
                                        </thead>
                                        <tbody id="cinv_attachment_body" class="no-padding">
                                        <tr class="danger">
                                            <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?></td><!--No Attachment Found-->
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                        <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?></button><!--Submit-->
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
            fetchPage('system/delivery_order/delivery-order-approval', '', 'Invoice Approval');
        });

        fetch_approval_tbl();

        $('#approval_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_credit_status_is_required');?>.'}}},/*Status is required*/
                level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_level_order_status_is_required');?>.'}}},/*Level Order Status is required*/
                orderAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_payment_voucher_id_is_required');?>.'}}}/*Payment Voucher ID is required*/,
                documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_level_document_approved_id_is_required');?>.'}}}/*Document Approved ID is required*/
            }
        }).
        on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Delivery_order/approve_delivery_order'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        fetch_approval_tbl();
                        $("#approval_modal").modal('hide');
                    }

                    if(!$.isEmptyObject(data['in-suf-qty'])){
                        $('#insufficient_item_body').html('');
                        $.each(data['in-suf-items'], function (item, value) {
                            $('#insufficient_item_body').append('<tr><td>' + value['itemSystemCode'] + '</td> <td>' + value['itemDescription'] + '</td> <td>' + value['availableStock'] + '</td></tr>')
                        });
                        $("#insufficient_item_modal").modal({backdrop: "static"});
                    }

                }, error: function () {
                    stopLoad();
                    myAlert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                }
            });
        });
    });

    function fetch_approval_tbl() {
        oTable = $('#approval_tbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Delivery_order/fetch_delivery_order_approval'); ?>",
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
                {"mData": "DOAutoID"},
                {"mData": "DOCode_str"},
                {"mData": "DODate"},
                {"mData": "narration"},
                {"mData": "customerName"},
                {"mData": "referenceNo"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},

                {"mData": "DOCode"}
            ],
            "columnDefs": [{"visible":false,"searchable": true,"targets": [10], "orderable": false},{"targets": [0,8,9], "orderable": false, "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "approvedYN", "value": $("#approvedYN").val()});

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

    function fetch_approval(orderAutoID, documentApprovedID, Level) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'orderAutoID': orderAutoID, 'html': true,'approval': 1},
            url: "<?php echo site_url('Delivery_order/load_order_confirmation_view_delivered'); ?>",
            beforeSend: function () {
                $('#approval_form')[0].reset();
                $('#approval_form').bootstrapValidator('resetForm', true);
                startLoad();
            },
            success: function (data) {
                $("#attachment-tab").addClass("hide");
                $('#orderAutoID').val(orderAutoID);
                $('#documentApprovedID').val(documentApprovedID);
                $('#level').val(Level);
                $("#approval_modal").modal({backdrop: "static"});
                $('#conform_body').html(data);
                $('#comments').val('');
                load_delivery_order_attachment('DO', orderAutoID);
                stopLoad();
            }, error: function () {
                stopLoad();
                myAlert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function load_delivery_order_attachment(documentID, documentSystemCode) {
        $("#attachment-tab").removeClass("active");
        $("#view-tab").addClass("active");
        $("#attachment-tab-container").removeClass("active");
        $("#view-tab-container").addClass("active");
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode,'confirmedYN': 0},
                success: function (data) {
                    $('#cinv_attachment_body').empty().append('' +data+ '');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function tabAttachment(){
        $("#attachment-tab").removeClass("hide");
    }
    function tabView(){
        $("#attachment-tab").addClass("hide");
    }
    function delivered(DocumentID,DOAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                DocumentID: DocumentID,
                DOAutoID: DOAutoID
            },
            url: "<?php echo site_url('Delivery_order/load_order_confirmation_view_delivered'); ?>",
            beforeSend: function () {
                $("#deliveredTab_footer_div").html('<div  class="text-center" style="margin: 10px 0px;"><i class="fa fa-refresh fa-spin"></i></div>');
            },
            success: function (data) {
                $("#deliveredTab_footer_div").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#deliveredTab_footer_div").html('<br>Message:<br/> ' + errorThrown);
            }
        });
    }
</script>


<?php
