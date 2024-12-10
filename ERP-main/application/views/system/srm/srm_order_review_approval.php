<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$ship_arr = ship_to();
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_quotation_contract_approval');
echo head_page('Order Review Approval', false);

//echo head_page('Quotation / Contract Approval', false);

?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="set-poweredby">Powered by &nbsp;<a href=""><img src="https://ilooopssrm.rbdemo.live/images/logo-dark.png" width="75" alt="MaxSRM"></a></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span>
                    <?php echo $this->lang->line('common_not_approved');?>    <!--Not Approved-->
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
    <table id="oreder_review_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?> <!--Code--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_document_date');?><!--Document Date--></th>
            <th style="min-width: 30%"><?php echo $this->lang->line('sales_markating_narration');?><!--Narration--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_customer_name');?><!--Customer Name--></th>
            <th style="min-width: 20%"> Reference No</th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_level');?><!--Level--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" tabindex="-1" id="order_review_modal"  role="dialog" aria-labelledby="myModalLabel" style="overflow: scroll;">
    <div class="modal-dialog" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('common_approval');?><!--Approval--></h4>
            </div>
            <form class="form-horizontal" id="order_review_approval_form">
                <div class="modal-body">
                    <div class="col-sm-1">
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="cn_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v" data-toggle="tab" onclick="tabView()">
                                    <?php echo $this->lang->line('common_view');?>
                                    <!--View--></a></li>
                        </ul>
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="cn_attachement_approval_Tabview_vv" class=""><a href="#Tab-home-c" data-toggle="tab" onclick="tabAttachement()">
                                    Statement
                                    <!--View--></a></li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                <div id="conform_body"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">
                                        <?php echo $this->lang->line('common_status');?>
                                        <!--Status--></label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/,

                                            '2' => $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>

                                        <input type="hidden" name="code" id="code">
                                        <input type="hidden" name="Level" id="Level">
                                        <input type="hidden" name="orderreviewID" id="orderreviewID">
                                        <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label">Ship To</label>
                                    <div class="col-sm-8">
                                        <?php echo form_dropdown('shippingAddressID', $ship_arr, '1', 'class="form-control select2" id="shippingAddressID"'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label">  <?php echo $this->lang->line('common_comment');?><!--Comments--></label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"> <?php echo $this->lang->line('common_Close');?><!--Close--></button>
                                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                                </div>
                            </div>

                            <div class="zx-tab-pane hide" id="Tab-home-c">
                                <div id="conform_body1"></div>
                                
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
            fetchPage('system/quotation_contract/quotation_contract_approval', '', 'Quotation / contract');
        });
        order_review_table();

        $('#order_review_approval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_credit_status_is_required');?>.'}}},/*Status is required*/
                Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_level_order_status_is_required');?>.'}}},/*Level Order Status is required*/
                orderreviewID: {validators: {notEmpty: {message: 'Order Review Id is required.'}}},
                documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_level_document_approved_id_is_required');?>.'}}}/*Document Approved ID is required*/
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
                url: "<?php echo site_url('Srm_master/save_order_review_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if(data == true){
                        $("#order_review_modal").modal('hide');
                        order_review_table();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }


                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('.select2').select2();
    });

    function order_review_table() {
         Otable = $('#oreder_review_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Srm_master/fetch_order_review_approval'); ?>",
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
                {"mData": "orderreviewID"},
                {"mData": "contractCode"},
                {"mData": "contractDate"},
                {"mData": "narration"},
                {"mData": "customerName"},
                {"mData": "referenceNo"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}


                //{"mData": "edit"},
            ],
            "columnDefs": [{"targets": [0,7], "searchable": false}],
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

    function fetch_approval(orderreviewID, documentApprovedID, Level,code) {
        $("#Tab-home-c").addClass("hide");
        $("#Tab-home-v").removeClass("hide");

        $("#Tab-home-c").removeClass("active");
        $("#Tab-home-v").addClass("active");

        $("#cn_attachement_approval_Tabview_vv").removeClass("active");
        $("#cn_attachement_approval_Tabview_v").addClass("active");
        if (orderreviewID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'orderreviewID': orderreviewID, 'html': true},
                url: "<?php echo site_url('Srm_master/load_ordereview_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                   $('#orderreviewID').val(orderreviewID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#code').val(code);
                    $('#Level').val(Level);
                    $("#order_review_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    $('#shippingAddressID').val('').change();

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


    function tabAttachement(){
        $("#Tab-home-c").removeClass("hide");
        $("#Tab-home-v").addClass("hide");

        $("#Tab-home-c").addClass("active");
        $("#Tab-home-v").removeClass("active");

        $("#cn_attachement_approval_Tabview_v").removeClass("active");
        $("#cn_attachement_approval_Tabview_vv").addClass("active");

        var inquiryMasterID = $('#inquiryMasterID').val();
        var reviewMasterID = $('#reviewMasterID').val();

        var template = '';
        $('#conform_body1').html('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryID: inquiryMasterID,orderreviewID: reviewMasterID,template:template},
            url: "<?php echo site_url('srm_master/order_review_detail_view_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#conform_body1').html(data);
                $('.re_check').attr('disabled', true);
                stopLoad();
                // $('#pending-li').removeClass('active');
                // $('#or_ongoing').removeClass('active');
                // $('#statement-li').addClass('active');
                // $('#statement').addClass('active');
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function tabView(){
        $("#Tab-home-c").addClass("hide");
        $("#Tab-home-v").removeClass("hide");

        $("#Tab-home-c").removeClass("active");
        $("#Tab-home-v").addClass("active");

        $("#cn_attachement_approval_Tabview_vv").removeClass("active");
        $("#cn_attachement_approval_Tabview_v").addClass("active");
    }
</script>