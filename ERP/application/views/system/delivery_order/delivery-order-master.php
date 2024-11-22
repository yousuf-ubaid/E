<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$current_date = current_format_date();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
$title = $this->lang->line('sales_marketing_delivery_order');
//$this->lang->load('sales_markating_approval', $primaryLanguage);

echo head_page($title, true);

$customer_arr = all_customer_drop(false);
$date_format_policy = date_format_policy();
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$financeyear_arr = all_financeyear_drop(true);
$qty_validate = getPolicyValues('VSQ', 'All');
$show_price_delivery_order = getPolicyValues('HPDO', 'All');
$doc_status = [
    'all' =>$this->lang->line('common_all'), '1' =>$this->lang->line('sales_markating_transaction_customer_draft'),
    '2' =>$this->lang->line('common_confirmed'), '3' =>$this->lang->line('common_approved'),'4'=>'Refer-back'
];
$SalesPerson = all_sales_person_drop();
?>
    <link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>
    <div id="filter-panel" class="collapse filter-panel">
        <?php echo form_open('', 'role="form" id="do_filter_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <div class="custom_padding">
                    <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?> </label><br><!--Date-->
                    <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from');?> </label><!--From-->
                    <input type="text" name="filter_date_from" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="del_order_tbl.draw()" value="" id="filter_date_from"
                           class="input-small">
                    <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('sales_markating_transaction_to');?>&nbsp;&nbsp;</label><!--To-->
                    <input type="text" name="filter_date_to" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="del_order_tbl.draw()" value="" id="filter_date_to"
                           class="input-small">
                </div>

            </div>
            <div class="form-group col-sm-4">
                <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_customer_name');?> </label> <br><!--Customer Name-->
                <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="del_order_tbl.draw()" multiple="multiple"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status');?> </label><br><!--Status-->

                <div style="width: 60%;">
                    <?php echo form_dropdown('status', $doc_status, '', 'class="form-control" id="status" onchange="del_order_tbl.draw()"'); ?></div>
                <button type="button" class="btn btn-primary pull-right"
                        onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?> <!--Clear-->
                </button>
            </div>
        </div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-5">
            <table class="<?php echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?> / <?php echo $this->lang->line('common_approved');?>
                        <!--Confirmed--> <!--Approved-->
                    </td>
                    <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?>
                        /<?php echo $this->lang->line('common_not_approved');?>                      <!-- Not Confirmed--><!--Not Approved-->
                    </td>
                    <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?> <!--Refer-back-->
                    </td>
                </tr>
            </table>
        </div>
        <!-- <div class="col-md-4 text-center">
            &nbsp;
        </div> -->
        <div class="col-md-7 text-right">
            <button type="button" class="btn btn-primary-new size-sm " onclick="open_delivery_order(null)">
                <i class="fa fa-plus"></i> <?php echo $this->lang->line('sales_marketing_create_delivery_order');?>
            </button>
            <a href="#" type="button" class="btn btn-excel btn-success-new size-sm" style="margin-left: 2px" onclick="excel_export()">
                <i class="fa fa-file-excel-o"></i> <?php echo $this->lang->line('common_excel');?><!--Excel-->
            </a>
        </div>
    </div><hr>
    <div class="table-responsive">
        <table id="delivery_order_tb" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 12%"><?php echo $this->lang->line('common_code');?></th><!--Invoice Code-->
                <th style="min-width: 40%"><?php echo $this->lang->line('common_details');?></th><!--Details-->
                <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_transaction_total_value');?></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?></th><!--Confirmed-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?></th><!--Approved-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?></th><!--Status-->
                <th style="width: 155px"><?php echo $this->lang->line('common_action');?></th><!--Action-->
            </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade" id="Email_modal" role="dialog" aria-labelledby="myModalLabel" data-width="95%" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" style="width: 50%">
            <form method="post" id="Send_Email_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <input type="hidden" name="invoiceid" id="email_invoiceid" value="">
                        <h4 class="modal-title" id="EmailHeader"><?php echo $this->lang->line('common_email');?><!--Email--></h4>
                    </div>
                    <div class="modal-body">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_1"  data-toggle="tab" aria-expanded="false">Send Email</a></li>
                                <li class=""><a href="#tab_2" onclick="load_mail_history()"  data-toggle="tab" aria-expanded="true">History</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab_1">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="emailContent">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                            </div>
                                            <div class="col-md-4">
                                            </div>
                                        </div>
                                        <div class="append_data_nw">
                                            <div class="row removable-div-nw" id="mr_1" style="margin-top: 10px;">
                                                <div class="col-sm-1">
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="email" name="email" id="email" class="form-control"
                                                           placeholder="example@example.com" style="margin-left: -10px">
                                                </div>
                                                <div class="col-sm-1 remove-tdnw">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" onclick="SendQuotationMail()">Send Email</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                                <div class="tab-pane " id="tab_2">
                                    <div class="modal-body">
                                        <table id="mailhistory" class="<?php echo table_class() ?>">
                                            <thead>
                                            <tr>
                                                <th style="">#</th>
                                                <th style="">documentID</th>
                                                <th style="">Sent By</th>
                                                <th style="">Sent to Email</th>
                                                <th style="">Sent Date time</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="tracing_modal" role="dialog" aria-labelledby="myModalLabel"
         data-width="95%" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" style="width: 50%">
            <div class="modal-content">
                <div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>-->
                    <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;"><?php echo $this->lang->line('common_document_tracing');?><!--Document Tracing-->   <button class="btn btn-default pull-right"  onclick="print_tracing_view()"><i class="fa fa-print"></i></button></h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="tracingId" name="tracingId">
                    <input type="hidden" id="tracingCode" name="tracingCode">
                    <div id="mcontainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="deleteDocumentTracing()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" aria-labelledby="myLargeModalLabel" id="deliverystatus_drilldown">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="usergroup-title"><?php echo $this->lang->line('sales_marketing_delivery_status')?><!--Delivery Status--></h4>
                </div>
                <?php echo form_open('', 'role="form" id="delivery_order_Status"'); ?>
                <input type="hidden" class="form-control" id="DOAutoIddo" name="DOAutoIddo">
                <input type="hidden" class="form-control" id="confirmedYN" name="confirmedYN">
                <input type="hidden" class="form-control" id="approvedYN" name="approvedYN">
                <div class="modal-body">
                    <div class="row" style="margin-top: 10px; margin-left: 15px" id="statusdelivery">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title"><?php echo $this->lang->line('common_status')?><!--Status--><?php required_mark(); ?> :</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('statusdo', array('0'=>'Not Delivered', '1'=>'Sent to Delivery', '2'=>'Delivered'), '', 'class="form-control select2" id="statusdo" required'); ?>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="row hide" style="margin-top: 10px; margin-left: 15px" id="drivername">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title"><?php echo $this->lang->line('sales_marketing_drievr_name')?><!--Driver Name--><?php required_mark(); ?> :</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <input type="text" class="form-control" id="driver_name" name="driver_name">
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="row hide" style="margin-top: 10px; margin-left: 15px" id="delivereddate">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title"><?php echo $this->lang->line('transaction_common_delivered_date')?><!--Delivered Date--><?php required_mark(); ?> :</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="delivereddatedo"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="delivereddatedo" class="form-control">
                                </div>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="row hide" style="margin-top: 10px; margin-left: 15px" id="comment">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title"><?php echo $this->lang->line('common_comment')?><!--Comment--> :</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <textarea class="form-control" rows="3" id="commentdo" name="commentdo"></textarea>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="row hide" style="margin: 10px" id="DeliveredQtyUpdate_view">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title"><?php echo $this->lang->line('sales_marketing_updated_qty')?><!--Update Qty--> :</label>
                        </div>
                        <div id="DeliveredQtyUpdate" class="col-sm-12">
                            <table class="table table-bordered table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th class='theadtr' style="min-width: 35%"><?php echo $this->lang->line('common_item')?><!--Item--></th>
                                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
                                        <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('common_qty');?><!--Qty--></th>
                                        <th class='theadtr' style="min-width: 5%">Park Qty</th> 
                                        <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('sales_marketing_delivered_qty')?><!--Delivered Qty--></th>
                                </thead>
                                <tbody id="Update_table_body">
                                    <tr class="danger">
                                        <td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td><!--No Records Found-->
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer" id="update_status">
                        <button type="button" class="btn btn-sm btn-primary" onclick="updatedostatus()"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span><?php echo $this->lang->line('common_update')?><!--Update-->  </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="generate_invoice_modal" role="dialog" aria-labelledby="myModalLabel"
         data-width="95%" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" style="width: 50%">
            <form method="post" id="generate_invoice_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;"><?php echo $this->lang->line('sales_markating_transaction_create_invoice')?><!--Create Invoice--></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="DOType" name="DOType">
                        <input type="hidden" id="GI_DOAutoID" name="GI_DOAutoID">
                        <input type="hidden" id="segmentID" name="segmentID">
                        <input type="hidden" id="customerID" name="customerID">
                        <input type="hidden" id="transactionCurrencyID" name="transactionCurrencyID">
                        <input type="hidden" id="transactionAmount_delivered" name="transactionAmount_delivered">
                        <input type="hidden" id="invoiced_amount" name="invoiced_amount">
                        <div class="row">
                            <div class="col-sm-7"><span style="color: black;font-family: sans-serif;" id="DO_Bal"></span></div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label><?php echo $this->lang->line('sales_markating_transaction_invoice_date')?><!--Invoice Date--> <?php required_mark(); ?></label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="INV_Date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="INV_Date"
                                           class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice_due_date')?><!--Invoice Due Date --><?php required_mark(); ?></label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="INV_Due_Date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="INV_Due_Date"
                                           class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label><?php echo $this->lang->line('sales_markating_transaction_sales_person');?> </label><!--Sales Person-->
                                <?php echo form_dropdown('salesperson', $SalesPerson, '', 'class="form-control select2" id="salesperson"'); ?>
                        </div>
                        </div>
                        <div class="row">
                            <?php
                            if($financeyearperiodYN==1){
                                ?>
                                <div class="form-group col-sm-4">
                                    <label for="financeyear"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_year')?><!--Financial Year--> <?php required_mark(); ?></label>
                                    <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="financeyear"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_period')?><!--Financial Period --><?php required_mark(); //?></label>
                                    <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                                </div>
                            <?php } ?>
                            <div class="form-group col-sm-4">
                                <label><?php echo $this->lang->line('common_reference')?><!--Reference--> </label>
                                <input type="text" name="INV_reference" id="INV_reference" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="contactPersonName"><?php echo $this->lang->line('sales_markating_transaction_document_contact_person_name');?> </label><!--Contact Person Name-->
                            <input type="text" class="form-control " id="contactPersonName" name="contactPersonName">
                        </div>
                        <div class="form-group col-sm-4">
                            <label for=""><?php echo $this->lang->line('sales_markating_transaction_document_persons_telephone_number');?> </label><!--Person's Telephone Number-->

                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                <input type="text" class="form-control " id="contactPersonNumber" name="contactPersonNumber">
                            </div>
                        </div>
                            <div class="form-group col-sm-4">
                                <label><?php echo $this->lang->line('common_narration')?><!--Narration--> </label>
                                <input type="text" name="INV_narration" id="INV_narration" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save')?><!--Save--></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close')?><!--Close--></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php echo footer_page('Right foot','Left foot',false); ?>
    <script type="text/javascript">
        var orderID = null;
        var del_order_tbl;


        $(document).ready(function() {
            $('.headerclose').click(function(){
                fetchPage('system/delivery_order/delivery-order-master','','Delivery Order');
            });
            $('.select2').select2();
            $('.modal').on('hidden.bs.modal', function () {
                setTimeout(function () {
                    if ($('.modal').hasClass('in')) {
                        $('body').addClass('modal-open');
                    }
                }, 500);
            });

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {

            });
            number_validation();
            delivery_order_tb();

            $('#customerCode').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
            });

            Inputmask().mask(document.querySelectorAll("input"));

            FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
            periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
            fetch_finance_year_period(FinanceYearID, periodID);

            $('#generate_invoice_form').bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
                excluded: [':disabled'],
                fields: {
                    INV_Date: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_marketing_invoice_date_required');?>.'}}},/*Invoice Date Required*/
                    INV_Due_Date: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_marketing_invoice_date_required');?>.'}}}/*Invoice Due Date Required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
                data.push({'name': 'DOAutoID', 'value': $('#GI_DOAutoID').val()});
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                        text: "<?php echo $this->lang->line('sales_marketing_you_want_to_create_this_invoice');?>",/*You want to create this Invoice!*/
                        type: "warning",/*warning*/
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            url: "<?php echo site_url('Delivery_order/generate_invoice_from_DO_header'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                if(data[0]=='s'){
                                    $("#DOType").val('');
                                    $("#GI_DOAutoID").val('');
                                    $("#segmentID").val('');
                                    $("#customerID").val('');
                                    $("#transactionCurrencyID").val('');
                                    $("#transactionAmount_delivered").val('');
                                    $("#invoiced_amount").val('');
                                    $("#INV_narration").val('');
                                    $("#financeyear").val('').change();
                                    $("#financeyear_period").val('').change();
                                    $('#salesperson').val('').change();
                                    $('#contactPersonName').val('');
                                    $('#contactPersonNumber').val('');
                                    $("#generate_invoice_modal").modal('hide');
                                    myAlert(data[0],data[1]);
                                    stopLoad();
                                }else{
                                    stopLoad();
                                    myAlert(data[0],data[1]);
                                }
                            },
                            error: function () {
                                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                                stopLoad();
                            }
                        });
                    });
            });

        });

        function delivery_order_tb(selectedID=null){
            del_order_tbl = $('#delivery_order_tb').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Delivery_order/fetch_delivery_orders'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                    var tmp_i   = oSettings._iDisplayStart;
                    var iLen    = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        if( parseInt(oSettings.aoData[x]._aData['DOAutoID']) == selectedRowID ){
                            var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                            $(thisRow).addClass('dataTable_selectedTr');
                        }
                        x++;
                    }
                    $('.deleted').css('text-decoration', 'line-through');
                    $('.deleted div').css('text-decoration', 'line-through');
                },
                "aoColumns": [
                    {"mData": "DOAutoID"},
                    {"mData": "DOCode"},
                    {"mData": "invoice_detail"},
                    {"mData": "total_value"},
                    {"mData": "confirmed"},
                    {"mData": "approved"},
                    {"mData": "status"},
                    {"mData": "edit"},
                    {"mData": "narration"},
                    {"mData": "cus_name"},
                    {"mData": "DODate"},
                    {"mData": "invoiceDueDate"},
                    {"mData": "DOType"},
                    {"mData": "referenceNo"},
                    {"mData": "total_value_search"}
                ],
                "columnDefs": [{"targets": [6], "orderable": false},{"visible":false,"searchable": true,"targets": [8,9,10,11,12,13,14] },{"targets": [0,2,3], "visible": true,"searchable": false}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "date_from", "value": $("#filter_date_from").val()});
                    aoData.push({"name": "date_to", "value": $("#filter_date_to").val()});
                    aoData.push({"name": "status", "value": $("#status").val()});
                    aoData.push({"name": "customer_code", "value": $("#customerCode").val()});
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

        function delete_item(id){
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async : true,
                        type : 'post',
                        dataType : 'json',
                        data : {'DOAutoID':id},
                        url :"<?php echo site_url('Delivery_order/delete_delivery_order_master'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success : function(data){
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                del_order_tbl.draw();
                            }
                        },error : function(){
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            stopLoad();
                        }
                    });
                });
        }

        function refer_back_delivery_order(id){
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*/!*Are you sure?*!/*/
                    text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async : true,
                        type : 'post',
                        dataType : 'json',
                        data : {'DOAutoID':id},
                        url :"<?php echo site_url('Delivery_order/refer_back_delivery_order'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success : function(data){
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                del_order_tbl.draw();
                            }
                        },error : function(){
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            stopLoad();
                        }
                    });
                });
        }

        function clear_all_filters(){
            $('#filter_date_from').val("");
            $('#filter_date_to').val("");
            $('#status').val("all");
            $('#customerCode').multiselect2('deselectAll', false);
            $('#customerCode').multiselect2('updateButtonText');
            del_order_tbl.draw();
        }

        function reOpen_contract(id){
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_re_open');?>",/*You want to re open!*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async : true,
                        type : 'post',
                        dataType : 'json',
                        data : {'DOAutoID':id},
                        url :"<?php echo site_url('Delivery_order/re_open_invoice'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success : function(data){
                            del_order_tbl.draw();
                            stopLoad();
                            refreshNotifications(true);
                        },error : function(){
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function Generate_Invoice(id){
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "You want to Generate invoice!",/*You want to re open!*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'DOAutoID':id},
                    url :"<?php echo site_url('Delivery_order/check_DO_matched'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        if(data[0] == 'w'){
                            myAlert(data[0], data[1]);
                        } else {
                            var balAmount = parseFloat(data[1]['transactionAmount']) - parseFloat(data[1]['invoiced_amount']);
                            $('#DO_Bal').html('Delivery Order Balance :- '+ parseFloat(balAmount).toFixed(data[1]['transactionCurrencyDecimalPlaces']) + ' ('+ data[1]['transactionCurrency'] +')' ) ;
                            $("#DOType").val(data[1]['DOType']);
                            $("#GI_DOAutoID").val(data[1]['DOAutoID']);
                            $("#segmentID").val(data[1]['segmentID']);
                            $("#customerID").val(data[1]['customerID']);
                            $("#transactionCurrencyID").val(data[1]['transactionCurrencyID']);
                            $("#transactionAmount_delivered").val(data[1]['transactionAmount']);
                            $("#invoiced_amount").val(data[1]['invoiced_amount']);
                            $('#salesperson').val(data[1]['salesPersonID']).change();
                            $('#contactPersonName').val(data[1]['contactPersonName']);
                            $('#contactPersonNumber').val(data[1]['contactPersonNumber']);

                            $("#INV_narration").val('Invoice For :  ' + data[1]['DOCode']);
                            $("#INV_reference").val(data[1]['referenceNo']);

                            setTimeout (function () {
                                $("#financeyear").val(<?php echo $this->common_data['company_data']['companyFinanceYearID'] ?>).change();
                                $("#financeyear_period").val(<?php echo $this->common_data['company_data']['companyFinancePeriodID'] ?>).change();
                            }, 1000);
                           

                            $("#generate_invoice_modal").modal('show');

                        }

                        refreshNotifications(true);
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
        }

        function send_email(id) {
            $('#email_invoiceid').val(id);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'DOAutoID': id},
                url: "<?php echo site_url('Delivery_order/invoiceloademail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $("#Email_modal").modal();
                    if (!jQuery.isEmptyObject(data)) {
                        $('#email').val(data['customerEmail']);
                    }
                    load_mail_history();
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }

        function SendQuotationMail() {
            var form_data = $("#Send_Email_form").serialize();
            swal({
                    title: "Are You Sure?",
                    text: "You Want To Send This Mail",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: form_data,
                        url: "<?php echo site_url('Delivery_order/send_do_email'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                $("#Email_modal").modal('hide');
                                save_document_email_history(data[2],data[4],data[3]);
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function confirm_order_front(id) {
            swal({
                    title: "Are you sure?",
                    text: "You want to confirm this document?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: true
                },
                function () {
                    $.ajax({
                        data: {'orderAutoID': id},
                        url: "<?php echo site_url('Delivery_order/order_confirmation'); ?>",
                        type: 'post',
                        dataType: 'json',
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                del_order_tbl.draw();
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            stopLoad();
                            myAlert('e', xhr.responseText);
                        }
                    });
                });
        }

        function traceDocument(DOAutoID,DocumentID){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'DOAutoID': DOAutoID,'DocumentID': DocumentID},
                url: "<?php echo site_url('Tracing/trace_do_document'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    //myAlert(data[0], data[1]);
                    $(window).scrollTop(0);
                    load_document_tracing(DOAutoID,DocumentID);
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

        function load_document_tracing(id,DocumentID){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'purchaseOrderID': id,'DocumentID': DocumentID},
                url: "<?php echo site_url('Tracing/select_tracing_documents'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#mcontainer").empty();
                    $("#mcontainer").html(data);
                    $("#tracingId").val(id);
                    $("#tracingCode").val(DocumentID);

                    $("#tracing_modal").modal('show');

                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

        function deleteDocumentTracing(){
            var purchaseOrderID=$("#tracingId").val();
            var DocumentID=$("#tracingCode").val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'purchaseOrderID': purchaseOrderID,'DocumentID': DocumentID},
                url: "<?php echo site_url('Tracing/deleteDocumentTracing'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#tracing_modal").modal('hide');
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });

        }

        function open_delivery_order(orderID){
            fetchPage('system/delivery_order/delivery-order-header', orderID, '','DO');
        }

        $('.table-row-select tbody').on('click', 'tr', function () {
            $('.table-row-select tr').removeClass('dataTable_selectedTr');
            $(this).toggleClass('dataTable_selectedTr');
        });

        function load_mail_history(){
            var Otables = $('#mailhistory').DataTable({"language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Delivery_order/load_mail_history'); ?>",
                aaSorting: [[0, 'desc']],
                "bFilter": false,
                "bInfo": false,
                "bLengthChange": false,
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i   = oSettings._iDisplayStart;
                    var iLen    = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }
                },
                "aoColumns": [
                    {"mData": "autoID"},
                    {"mData": "contractCode"},
                    {"mData": "ename"},
                    {"mData": "toEmailAddress"},
                    {"mData": "sentDateTime"}

                ],
                //"columnDefs": [{"targets": [0], "visible": false,"searchable": true}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "DoAutoID", "value": $("#email_invoiceid").val()});
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

        $("#statusdo").change(function () {
            if (this.value == 1) {
                $('#drivername').removeClass('hide');
                $('#delivereddate').removeClass('hide');
                $('#comment').removeClass('hide');
                $('#DeliveredQtyUpdate_view').addClass('hide');
            } else if (this.value == 2) {
                if(($('#confirmedYN').val() == 1) && ($('#approvedYN').val() != 1) ) {
                    $('#drivername').removeClass('hide');
                    $('#delivereddate').removeClass('hide');
                    $('#comment').removeClass('hide');
                    $('#DeliveredQtyUpdate_view').removeClass('hide');
                    fetch_deliveredQty_update();
                } else if (($('#approvedYN').val() != 1) && ($('#confirmedYN').val() != 1)) {
                    $("#statusdo").val(0).change();
                    myAlert('w', 'Please Confirm the Document before updating the status to Delivered')
                }
            } else  {
                $('#drivername').addClass('hide');
                $('#delivereddate').addClass('hide');
                $('#comment').addClass('hide');
                $('#DeliveredQtyUpdate_view').addClass('hide');
            }
        });

        function generatedeliverystatus_drilldown(code,autoID,deliveredStatus, approvedYN, confirmedYN) {
            //$('#statusdo').val(null).trigger('change');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'autoID': autoID},
                url: "<?php echo site_url('Delivery_order/deliveryorder_collectionheader'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#statusdo').attr('disabled',false);
                    $('#DOAutoIddo').attr('disabled',false);
                    $('#driver_name').attr('disabled',false);
                    $('#delivereddatedo').attr('disabled',false);
                    setTimeout(function () {
                        $('#statusdo').val(data['status']).change();
                    }, 150);


                    $('#DOAutoIddo').val(autoID);
                    $('#driver_name').val(data['driverName']);
                    $('#delivereddatedo').val(data['deliveredDate']);
                    $('#confirmedYN').val(confirmedYN);
                    $('#approvedYN').val(approvedYN);

                    if(data['status']== 1)
                    {
                        $('#drivername').removeClass('hide');
                        $('#delivereddate').removeClass('hide');
                        $('#comment').removeClass('hide');
                        $('#commentdo').val(data['deliveryComment']);
                        $('#DeliveredQtyUpdate_view').addClass('hide');

                    }else if(data['status'] == 2)
                    {
                        $('#drivername').removeClass('hide');
                        $('#delivereddate').removeClass('hide');
                        $('#comment').removeClass('hide');
                        $('#commentdo').val(data['deliveryComment']);
                        $('#DeliveredQtyUpdate_view').removeClass('hide');
                        fetch_deliveredQty_update();
                    } else
                    {
                        $('#drivername').addClass('hide');
                        $('#delivereddate').addClass('hide');
                        $('#comment').addClass('hide');
                        $('#commentdo').val('');
                        $('#DeliveredQtyUpdate_view').addClass('hide');
                    }
                    if(approvedYN == 1) {
                        $('#statusdo').attr('disabled',true);
                        $('#DOAutoIddo').attr('disabled',true);
                        $('#driver_name').attr('disabled',true);
                        $('#delivereddatedo').attr('disabled',true);
                        $('#update_status').attr('hidden',true);
                    } else {
                        $('#update_status').attr('hidden',false);
                    }

                    $('#deliverystatus_drilldown').modal("show");
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }
        
        function fetch_deliveredQty_update() {
            var autoID = $('#DOAutoIddo').val();
            if(autoID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'DOAutoID': autoID},
                    url: "<?php echo site_url('Delivery_order/fetch_DO_delivered_item_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#Update_table_body').empty();
                        x = 1;
                        if (jQuery.isEmptyObject(data)) {
                            $('#Update_table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                            
                        } else {
                            var  str = '';
                            var x = 1;
                            $.each(data, function (key, value) {
                                var updatableQTY = value['balance'];

                                str += '<tr><td class="hide">' +
                                    '<input type="hidden" class="number" size="15" id="' + value['DODetailsAutoID'] + '" name="DODetailsAutoID[]" value="' + value['DODetailsAutoID'] + '">' +
                                    '<input type="hidden" class="number" size="15" id="' + value['itemAutoID'] + '" name="itemAutoID[]" value="' + value['itemAutoID'] + '">' +
                                    '<input type="hidden" class="number" size="15" id="detailID_' + value['DODetailsAutoID'] + '" name="detailID_' + value['DODetailsAutoID'] + '" value="' + value['DODetailsAutoID'] + '">' +
                                    '<input type="hidden" class="number" size="15" id="pulledQty_' + value['DODetailsAutoID'] + '" name="pulledQty_' + value['DODetailsAutoID'] + '" value="' + value['DODetailsAutoID'] + '">' +
                                    '</td>';
                                str +=  '<td>' + value['itemSystemCode'] + ' | ' + value['itemDescription'] + '</td>';
                                str +=  '<td>' + value['unitOfMeasure'] + '</td>';
                                str +=  '<td>' + parseFloat(value['requestedQty']) + '</td>';
                                str +=  '<td id="parkQty_' + value['DODetailsAutoID'] + '" ></td>'; 
                                getParkQty(value['DODetailsAutoID'],value['itemAutoID'],value['deliveredQty'],value['requestedQty'],value['wareHouseAutoID'],value['UOM'],value['DOAutoID']);

                                if(value['approvedYN'] == 1) {
                                    str +=  '<td><input class="number" size="10" id="delivered_' + value['DODetailsAutoID'] + '" id="delivered_' + value['itemAutoID'] + '" name="delivered_' + value['DODetailsAutoID'] + '" value="' + parseFloat(value['deliveredQty']) + '" onkeyup="validate_qty_DO('+value['DODetailsAutoID'] + ',' + value['itemAutoID'] + ',this.value, ' + value['requestedQty'] + updatableQTY + ')" readonly></td>';
                                } else {
                                    str +=  '<td><input class="number" size="10" id="delivered_' + value['DODetailsAutoID'] + '" id="delivered_' + value['itemAutoID'] + '" name="delivered_' + value['DODetailsAutoID'] + '" value="' + parseFloat(value['deliveredQty']) + '" onkeyup="validate_qty_DO('+value['DODetailsAutoID'] + ',' + value['itemAutoID'] + ',this.value, ' + value['requestedQty'] + ',' + updatableQTY +')"></td>';
                                }
                                str +=  '</tr>';

                                $('#Update_table_body').empty();
                                $('#Update_table_body').append(str);
                                x++;
                            });
                        }
                        number_validation();
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }
        }

        function validate_qty_DO(DODetailsAutoID,itemAutoID,Qty, requestedQty, updatableQTY) {
            if(requestedQty < Qty) {
                $('#delivered_' + DODetailsAutoID).val('');
                myAlert('w', 'Delivered Qty cannot be greater than Requested Qty');
            }
            Qtypolicy = 0;
            var Qtypolicy = <?php echo $qty_validate; ?>;
            if(Qtypolicy == 1){
                if(updatableQTY < Qty) {
                    $('#delivered_' + DODetailsAutoID).val('');
                    myAlert('w', 'Total Delivered Qty cannot be greater than Contracted Qty');
                }
            }
            var pulledQty=$('#pulledQty_'+DODetailsAutoID).text();
           
            if( parseFloat(pulledQty) < Qty){
                $('#delivered_' + DODetailsAutoID).val('');

                myAlert('w', 'Delivered Qty cannot be greater than pulled Qty');
            }
        }

        function updatedostatus()
        {
            var data = $('#delivery_order_Status').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Delivery_order/update_deliveryorder_collectiondetails'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == "s") {
                        del_order_tbl.draw();
                        $('#deliverystatus_drilldown').modal("hide");
                    } 
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });

        }

        function fetch_finance_year_period(companyFinanceYearID, select_value) {
            
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyFinanceYearID': companyFinanceYearID},
                url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
                success: function (data) {
                   // $('#financeyear_period').empty();
                    var mySelect = $('#financeyear_period');
                    mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                        });
                        if (select_value) {
                            $("#financeyear_period").val(select_value).change();
                        }
                        ;
                    }
                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
            });
        }

        function excel_export() {
            var form = document.getElementById('do_filter_form');
            form.target = '_blank';
            form.method = 'post';
            form.post = $('#do_filter_form').serializeArray();
            form.action = '<?php echo site_url('Delivery_order/export_excel_do'); ?>';
            form.submit();
         }

        function getParkQty(DODetailsAutoID,itemAutoID,deliveredQty,requestedQty,wareHouse,UOM,DOAutoID){
        

        $('#parkQty_'+DODetailsAutoID).text(0);
        if(wareHouse) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemAutoID': itemAutoID, 'wareHouseAutoID': wareHouse,'documentAutoID':DOAutoID,'documentID':'DO','documentDetAutoID':DODetailsAutoID},
                url: "<?php echo site_url('Receipt_voucher/fetch_rv_warehouse_item_deduct_qty'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                   
                    if(data['error'] == 0){
                        $('#parkQty_'+DODetailsAutoID).text(data['parkQty']);
                        $('#pulledQty_'+DODetailsAutoID).text(data['pulledstock']);
                    }else{
                        $('#parkQty_'+DODetailsAutoID).text(0);
                        $('#pulledQty_'+DODetailsAutoID).text(0);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }
    }
    </script>

<?php
