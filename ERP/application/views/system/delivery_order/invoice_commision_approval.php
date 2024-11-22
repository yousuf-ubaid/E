<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = 'Invoice Commission Approval';
echo head_page($title, false);

?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span>
                    <?php echo $this->lang->line('common_approved');?>
                    <!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span>
                    <?php echo $this->lang->line('common_not_approved');?>
                    <!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('approvedYN', array('0' => $this->lang->line('common_pending') /*'Pending'*/, '1' => $this->lang->line('common_approved')/*'Approved'*/), '', 'class="form-control" id="approvedYN" required onchange="Otable.draw()"'); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="invoice_commission_approval_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 30%">Invoice Code</th> <!--Invoice Code-->
            <th style="min-width: 30%"><?php echo $this->lang->line('common_code');?></th><!--Code-->
                 <th style="min-width: 5%"><?php echo $this->lang->line('common_level');?></th><!--Level-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?></th><!--Status-->
            <th style="min-width: 15%"><?php echo $this->lang->line('common_action');?> </th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="ic_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_markating_sales_sales_commission_payment_vocher_approval');?></h4><!--Payment Voucher Approval-->
            </div>
            <form class="form-horizontal" id="ic_approval_form">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-1">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('common_view');?>
                                        <!--View-->
                                    </a>
                                </li>
                                <!-- <li role="presentation">
                                    <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                                        <?php //echo $this->lang->line('common_attachment');?>
                                       
                                    </a>
                                </li> -->
                                
                            </ul>
                        </div>

                        <!-- Tab panes -->
                        <div class="col-sm-11">
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="home">

                                    <div id="conform_body"></div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?></label><!--Status-->

                                        <div class="col-sm-4">
                                            <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' =>$this->lang->line('common_approved') /*'Approved'*/, '2' =>$this->lang->line('common_refer_back') /*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>
                                            <input type="hidden" name="Level" id="Level">
                                            <input type="hidden" name="commissionAutoID" id="commissionAutoID">
                                            <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?></label><!--Comments-->

                                        <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments"
                                                  id="comments"></textarea>
                                        </div>
                                    </div>
                                    <div class="pull-right">

                                        <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?></button><!--Submit-->
                                    </div>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="profile">

                                    <div class="table-responsive">
                                        <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                        &nbsp <strong><?php echo $this->lang->line('sales_markating_sales_sales_commission_payment_payment_voucher_attachments');?></strong><!--Payment Voucher Attachments-->
                                        <br><br>
                                        <table class="table table-striped table-condensed table-hover">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?php echo $this->lang->line('common_file_name');?></th><!--File Name-->
                                                <th><?php echo $this->lang->line('common_description');?></th><!--Description-->
                                                <th><?php echo $this->lang->line('common_type');?></th><!--Type-->
                                                <th><?php echo $this->lang->line('common_action');?></th><!--Action-->
                                            </tr>
                                            </thead>
                                            <tbody id="pv_attachment_body" class="no-padding">
                                            <tr class="danger">
                                                <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?></td><!--No Attachment Found-->
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="messages">
                                    <h4><?php echo $this->lang->line('sales_markating_sales_sales_commission_payment_sub_item_configuration');?></h4><!--Sub Item Configuration-->
                                    <div id="itemMasterSubDiv">

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="documentPageView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-backdrop="static">
        <div class="modal-dialog" role="document" style="width:90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="documentPageViewTitle">Modal title</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-1">
                                <!-- Nav tabs -->
                            </div>
                            <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                                <!-- Tab panes -->
                                <div class="zx-tab-content">
                                    <div class="zx-tab-pane active" id="home-v">
                                        <div id="loaddocumentPageView" class="col-md-12"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/sales/invoice_commission_approval', '', 'Invoice Commission Approval');
        });
        invoice_commission_table();
        $('#ic_approval_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_credit_status_is_required');?>.'}}},/*Status is required*/
                Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_level_order_status_is_required');?>.'}}},/*Level Order Status is required*/
                commissionAutoID: {validators: {notEmpty: {message: 'Invoice Commission ID is required .'}}},/* */
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
                url: "<?php echo site_url('Invoices/save_ic_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        Otable.draw();
                        $("#ic_modal").modal('hide');
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

    function invoice_commission_table() {
        Otable = $('#invoice_commission_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Invoices/fetch_invoice_commission_approval'); ?>",
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
                {"mData": "commissionAutoID"},
                {"mData": "documentSystemCode"},
                {"mData": "invoiceCode"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                //{"mData": "total_value_search"}
            ],
            "columnDefs": [ {"targets": [0,3,4,5], "searchable": false}],
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

    function fetch_approval(commissionAutoID, documentApprovedID, Level) {
        if (commissionAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'commissionAutoID': commissionAutoID, 'html': true, 'approval': 1},
                url: "<?php echo site_url('Invoices/load_invoice_commission_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#commissionAutoID').val(commissionAutoID);
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#Level').val(Level);
                    $("#ic_modal").modal({backdrop: "static"});
                    $('#conform_body').html(data);
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
    /* function tabAttachement() {
        $("#Tab-profile-v").removeClass("hide");
    } */
    function tabView() {
        $("#Tab-profile-v").addClass("hide");
    }
    
    function documentPageView_modal_IC(documentID, para1, para2, approval=1) {
        //alert("hai");
        $("#profile-v").removeClass("active");
        $("#home-v").addClass("active");
        $("#TabViewActivation_attachment").removeClass("active");
        $("#tab_itemMasterTabF").removeClass("active");
        $("#TabViewActivation_view").addClass("active");
        attachment_View_modal(documentID, para1);
        $('#loaddocumentPageView').html('');
        var siteUrl;
        var paramData = new Array();
        var title = '';
        var a_link;
        var de_link;

        $("#itemMasterSubTab_footer_div").html('');
        $(".itemMasterSubTab_footer").hide();

        switch (documentID) {

            case "IC": // Commisson Scheme
                siteUrl = "<?php echo site_url('Invoices/load_invoice_commission_confirmation'); ?>";
                paramData.push({name: 'commissionAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "Invoice Commission";
                break;

            default:
                notification('Document ID is not set .', 'w');
                return false;
        }
        paramData.push({name: 'html', value: true});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: paramData,
            url: siteUrl,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                $('#documentPageViewTitle').html(title);
                $('#loaddocumentPageView').html(data);
                $('#documentPageView').modal('show');
                $("#a_link").attr("href", a_link);
                $("#de_link").attr("href", de_link);
                $('.review').removeClass('hide');
                stopLoad();

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
       
    }

</script>