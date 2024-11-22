<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->load->helper('erp_data_sync');
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('ecommerce', $primaryLanguage);
    $title = $this->lang->line('ecommerce_sales_data');
    $this->lang->line($title, $primaryLanguage);
    echo head_page('Processed Complete View', false);

    $posting_id = $data_arr;
    $getServiceTypes = service_type_get();
    
    if($posting_id){
        $posting_detials = fetch_all_manual_posting_settings_data($posting_id);

        if($posting_detials){
            $dateFrom = date('Y-m-d',strtotime($posting_detials['date_from']));
            $dateTo = date('Y-m-d',strtotime($posting_detials['date_to']));
        }
    }
?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> Processed Successfully </td><!--Approved-->
                <td><span class="label label-danger">&nbsp;</span> Processed Failed </td><!-- Not Approved-->
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
        <?php echo form_dropdown('processedYN', array('1' => 'Processed Successfully' , '2' => 'Processed Failed' ), '', 'class="form-control" id="processedYN" required onchange="Otable.draw()"'); ?>
    </div>
</div>
<hr>
<div id="filter-panel" class="collapse filter-panel"></div>
    <!-- <div class="row">
        <?php if($posting_detials['failed'] == 0 ){ ?>
            <div class="alert alert-success text-center">
                <p><i class="fa fa-check"></i> Posting is Successfull with <?php echo $posting_detials['failed'] ?> Errors and <?php echo $posting_detials['success'] ?> Posted Orders</p>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning text-center">
                <p><i class="fa fa-user"></i> Posting is Successfull with <?php echo $posting_detials['failed'] ?> Errors and <?php echo $posting_detials['success'] ?> Posted Orders</p>
            </div>
        <?php } ?>
    </div> -->
</div>

<div id="filter-panel" class="filter-panel">
    <input type="hidden" name="posting_id" id="posting_id" value="<?php echo $posting_id ?>" /> 
        <table class="<?php echo table_class() ?>">
                <tr>
                    <td> 
                        <label for="inputEmail3" class="col-sm-4 control-label">Doc ID</label>

                        <div class="col-sm-8">

                            <div class="col-sm-8">
                                
                                    <input type="text" class="form-control" name="doc_id" id="doc_id" value="<?php echo $posting_detials['doc_id'] ?>" />
                              
                            </div>
                               
                        </div>
                    </td>

                    <td> 
                        <label for="inputEmail3" class="col-sm-4 control-label">Posting Type</label><!--Status-->

                        <div class="col-sm-8">
                            <?php echo form_dropdown('posting_type', array('1' =>'Manual'/*'Approved'*/),  isset($posting_detials['posting_type']) ? $posting_detials['posting_type']: '', 'class="form-control" id="posting_type" disabled'); ?>
                        </div>
                    </td><!--Approved-->
                </tr>
              
                <tr>
                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Description</label><!--Status-->

                        <div class="col-sm-8">
                            <textarea class="form-control" rows="3" name="comments" id="comments"><?php echo isset($posting_detials['description']) ? $posting_detials['description'] : '' ?></textarea>
                        </div>
                    </td>
                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Service Type <br> </label><!--Status-->

                        <div class="col-sm-8">
                            <?php echo form_dropdown('service_type', array('' =>  'Please Select'/*'Please Select'*/,'1' =>'TMDONE'/*'Approved'*/, '2' =>'MARKET PLACE'/*'Referred-back'*/,'3' => 'PICKUP','4' => 'RECOVERY'), isset($posting_detials['service_type']) ? $posting_detials['service_type']: '', 'class="form-control" id="service_type" required'); ?>
                        </div>
                    </td><!--Approved-->

                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Mode of Collection <br> </label><!--Status-->

                        <div class="col-sm-8">
                            <?php echo form_dropdown('mode_collection', array('' =>  'Please Select'/*'Please Select'*/,'1' =>'All','2' =>'Cash', '3'=> 'Card'), isset($posting_detials['mode_collection']) ? $posting_detials['mode_collection']: '', 'class="form-control" id="mode_collection" required'); ?>
                        </div>
                    </td><!--Approved-->
                </tr>
                <tr>
                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Date From</label><!--Status-->

                        <div class="col-sm-8">
                        <input type="date" name="IncidateDateTo" data-inputmask="'alias': '<?php  ?>'" size="16" onchange="Otable.draw()" value="<?php echo isset($dateFrom) ? $dateFrom : '' ?>" id="dateFrom" class="input-small form-control" disabled>
                        </div>
                    </td>
                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Date To <br> </label><!--Status-->

                        <div class="col-sm-8">
                        <input type="date" name="IncidateDateTo" data-inputmask="'alias': '<?php  ?>'" size="16" onchange="Otable.draw()" value="<?php echo isset($dateTo) ? $dateTo : '' ?>" id="dateTo" class="input-small form-control" disabled>
                        </div>
                    </td>

                    
                <tr>
        </table>
</div>

<hr>
<div id="filter-panel" class="collapse filter-panel"></div>





<div class="table-responsive">
    
    <div class="row">
        
        <div class="form-group col-md-6">
            
            <label for=""><?php echo 'Filter From Store'?></label>
            <br>
            <?php echo form_dropdown('storeType[]', all_supplier_drop_systemCode(FALSE,1), '', ' class="form-control btn-block" multiple="multiple" id="storeType" onchange="sales_client_mapping_table()" required'); ?>
        </div>

    </div>

    <hr>

    <table id="clent_data" class="<?php echo table_class() ?>">
                            
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%">Summary</th><!--Code-->
            <th style="min-width: 10%">Process</th><!--Code-->
            <th style="min-width: 10%">Company Name</th>
            <th style="min-width: 10%">Service Type</th><!--Code-->
            <th style="min-width: 10%">Store ID</th>
            <th style="min-width: 10%">Store</th><!--Code-->
            <th style="min-width: 10%">Customer ID</th>
            <th style="min-width: 10%">Customer</th><!--Code-->
            <th style="min-width: 10%">Customer Tel</th><!--Code-->
            <th style="min-width: 10%">Order</th><!--Code-->
            <th style="min-width: 10%">Zone</th>
            <th style="min-width: 10%">Date Time</th><!--Code-->
            <th style="min-width: 10%">Completed Time</th><!--Code-->
            <th style="min-width: 10%">Bank ID</th><!--Code-->
            <th style="min-width: 10%">Bank Name</th><!--Code-->
            <th style="min-width: 10%">Payment</th><!--Code-->
            <th style="min-width: 10%">Status</th><!--Code-->
            <th style="min-width: 10%">Cuurency</th><!--Code-->
            <th style="min-width: 10%">Order Total</th><!--Code-->
            <th style="min-width: 10%">Delivery Fee</th><!--Code-->
            <th style="min-width: 10%">Actual Delivery Fee</th><!--Code-->
            <th style="min-width: 10%">Municipality Tax</th><!--Code-->
            <th style="min-width: 10%">Municipality Tax VAT</th><!--Code-->
            <th style="min-width: 10%">Tourism Tax</th><!--Code-->
            <th style="min-width: 10%">Tourism Tax VAT</th><!--Code-->
            <th style="min-width: 10%">VAT on Order</th><!--Code-->
            <th style="min-width: 10%">Delivery Fee VAT</th><!--Code-->
            <th style="min-width: 10%">Total Bill</th><!--Code-->
            <th style="min-width: 10%">Discount</th><!--Code-->
            <th style="min-width: 10%">Credit</th><!--Code-->
            <th style="min-width: 10%">Net Vendor Bill</th><!--Code-->
            <th style="min-width: 10%">Net Collection</th><!--Code-->
            <th style="min-width: 10%">Adjustment Type</th><!--Code-->
            <th style="min-width: 10%">Adjustment Reason</th><!--Code-->
            <th style="min-width: 10%">Total Adjustment</th><!--Code-->
            <th style="min-width: 10%">TM Done Adjustment</th><!--Code-->
            <th style="min-width: 10%">Vendor Adjustment</th><!--Code-->
            <th style="min-width: 10%">Driver Adjustment</th><!--Code-->
            <th style="min-width: 10%">Gross Payable</th><!--Code-->
            <th style="min-width: 10%">Commission Percentage (%)</th><!--Code-->
            <th style="min-width: 10%">Fixed Commission</th><!--Code-->
            <th style="min-width: 10%">Commissionable Income</th><!--Code-->
            <th style="min-width: 10%">3rd Party Commission</th><!--Code-->
            <th style="min-width: 10%">3rd Party Commission VAT</th><!--Code-->
            <th style="min-width: 10%">Bank Charges</th><!--Code-->
            <th style="min-width: 10%">Bank Charges VAT</th><!--Code-->
            <th style="min-width: 10%">Vendor Settlement</th><!--Code-->
            <th style="min-width: 10%">Card Payment Reference</th><!--Code-->
            <th style="min-width: 10%">Driver Name</th>
            <th style="min-width: 10%">Driver ID</th>
            <th style="min-width: 10%">Points Reedemed</th><!--Code-->
            <th style="min-width: 10%">Cash Collected</th><!--Code-->
            <th style="min-width: 10%">Credit Card</th><!--Code-->
            <th style="min-width: 10%">TM Credits</th><!--Code-->
            <th style="min-width: 10%">3PL Company ID</th>
            <th style="min-width: 10%">TM Done Driver ID</th>
            <th style="min-width: 10%">Delivery Cost</th><!--Code-->
            <th style="min-width: 10%">Vendor Free Delivery</th><!--Code-->
            <th style="min-width: 10%">Adjusted Vendor Settlement</th><!--Code-->
            <!-- <th style="min-width: 10%">Tablet Fee</th> -->
            <!-- <th style="min-width: 10%">Tablet Fee Settlement</th> -->
            <!-- <th style="min-width: 10%">Renewal Fee</th> -->
            <!-- <th style="min-width: 10%">Renewal Fee Settlement</th> -->
            <!-- <th style="min-width: 10%">Registration Fee</th> -->
            <!-- <th style="min-width: 10%">Registration Fee Settlement</th> -->
            <!-- <th style="min-width: 10%">Grouping</th> -->
            <!-- <th style="min-width: 10%">Campaign Fee</th> -->
            <!-- <th style="min-width: 10%">Campaign Fee Settlement</th> -->
            <!-- <th style="min-width: 10%">Refunds</th> -->
            <!-- <th style="min-width: 10%">Other</th> -->
            
            <!-- <th style="min-width: 10%">Action</th> -->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>



<div class="modal fade" id="sales_return_approval_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_markating_view_sales_return_approval');?></h4><!--Invoice Approval-->
            </div>
            <form class="form-horizontal" id="pv_approval_form">
                <div class="modal-body">
                    <div class="col-sm-1">
                        <!-- Nav tabs -->
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="slr_attachement_approval_Tabview_v" class="active">
                                <a href="#Tab-home-v" data-toggle="tab" onclick="hideActionButtons(1)">View</a><!--View-->
                            </li>
                            <!-- <li id="slr_attachement_approval_Tabview_a">
                                <a href="#Tab-profile-v1" data-toggle="tab">Summary</a>
                            </li> -->
                            <li id="slr_attachement_approval_Tabview_a">
                                <a href="#Tab-profile-v" data-toggle="tab" onclick="hideActionButtons(1)">Entry</a><!--Attachment-->
                            </li>
                            <li id="slr_attachement_approval_Tabview_a">
                                <a href="#Tab-profile-log" data-toggle="tab" onclick="hideActionButtons(2)">Log</a><!--Attachment-->
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                <div id="de_view"></div>
                            </div>

                            <div class="zx-tab-pane" id="Tab-profile-v1">
                                <div class="table-responsive">
                                    <div id="de_summary_all"></div>
                                </div>
                            </div>

                            <div class="zx-tab-pane" id="Tab-profile-v">
                                <div class="table-responsive">
                                    <div id="de_summary"></div>
                                </div>
                            </div>

                            <div class="zx-tab-pane" id="Tab-profile-log">
                                <div class="table-responsive">
                                    <div id="de_summary_log"></div>
                                </div>
                            </div>

                            <div class="pull-right mt-3 btn-section" id="btn-section">

                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                                <!-- <button type="button" class="btn btn-primary" onclick="proceed_double_entry()"><i class="fa fa-cog"></i> Proceed</button> -->
                                <!-- <button type="button" class="btn btn-primary" onclick="proceed_customer_double_entry()">Customer</button> -->
                                <!-- <button type="button" class="btn btn-primary" onclick="proceed_3pL_double_entry()">3PL Vendor</button> -->
                                <!-- <button type="button" class="btn btn-primary" onclick="proceed_3pL_customer_double_entry()">3PL Customer</button> -->
                                <!-- <button type="button" class="btn btn-primary" onclick="proceed_direct()">Direct Receipt</button> -->
                                <!-- <button type="button" class="btn btn-primary" onclick="proceed_journel_voucher()">Journel Voucher</button> -->
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

        sales_client_mapping_table();

          //multi select init
          $('#storeType').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: false,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });


        function sales_client_mapping_table() {
            var Otable = $('#clent_data').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "iDisplayLength": 25,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('DataSync/fetch_processed_data'); ?>",
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
                "fnRowCallback": function (row, data) {
                   // var val = $('input[name="item_quantity"]', row).val();
                    var total_bill = (data.order_total != null) ? data.order_total : 0;
                    var tmdone_commission = (data.tmdone_commission != null) ? data.tmdone_commission : 0;
                    var vat_tmdone_commission = (data.vat_tmdone_commission != null) ? data.vat_tmdone_commission : 0;
                    var bank_charges = (data.bank_charges != null) ? data.bank_charges : 0;
                    var bank_charges_vat = (data.bank_charges_vat != null) ? data.bank_charges_vat : 0;
                    var vendor_free_delivery = (data.vendor_free_delivery != null) ? data.vendor_free_delivery : 0;
                    var vendor_adjustment = (data.vendor_adjustment != null) ? data.vendor_adjustment : 0;

                    var adjusted_value = (parseFloat(total_bill) - ( parseFloat(tmdone_commission) + parseFloat(vat_tmdone_commission) + parseFloat(bank_charges) + parseFloat(bank_charges_vat) + parseFloat(vendor_free_delivery) + parseFloat(vendor_adjustment) )).toFixed(3);
                    
                    $('td:eq(59)', row).html( adjusted_value );

                },
                "aoColumns": [
                    {"mData": "id"},
                    {"mData": "de_model"},
                    {"mData": "process"},
                    {"mData": "company_name"},
                    {"mData": "service_type"},
                    {"mData": "store_id"},
                    {"mData": "store"},
                    {"mData": "customer_id"},
                    {"mData": "customer"},
                    {"mData": "customer_tel"},
                    {"mData": "order"},
                    {"mData": "zone"},
                    {"mData": "date_time"},
                    {"mData": "completed_time"},
                    {"mData": "bank_id"},
                    {"mData": "bank_name"},
                    {"mData": "payment"},
                    {"mData": "status"},
                    {"mData": "currency"},
                    {"mData": "order_total"},
                    {"mData": "delivery_fee"},
                    {"mData": "actual_delivery_fee"},
                    {"mData": "Municipality_tax"},
                    {"mData": "Municipality_tax_vat"},
                    {"mData": "tourism_tax"},
                    {"mData": "tourism_tax_vat"},
                    {"mData": "vat_on_order"},
                    {"mData": "vat_delivery_fee"},
                    {"mData": "total_bill"},
                    {"mData": "discount"},
                    {"mData": "credit"},
                    {"mData": "net_vendor_bill"},
                    {"mData": "net_collection"},
                    {"mData": "adjustment_type"},
                    {"mData": "adjustment_reason"},
                    {"mData": "total_adjustment"},
                    {"mData": "tmdone_adjustment"},
                    {"mData": "vendor_adjustment"},
                    {"mData": "driver_adjustment"},
                    {"mData": "gross_payable"},
                    {"mData": "commission_percentage"},
                    {"mData": "fixed_commission"},
                    {"mData": "commissionable_income"},
                    {"mData": "tmdone_commission"},
                    {"mData": "vat_tmdone_commission"},
                    {"mData": "bank_charges"},
                    {"mData": "bank_charges_vat"},
                    {"mData": "vendor_settlement"},
                    {"mData": "card_payment_reference"},
                    {"mData": "driver_name"},
                    {"mData": "driver_id"},
                    {"mData": "points_redeemed"},
                    {"mData": "cash_collected"},
                    {"mData": "credit_card"},
                    {"mData": "tm_credits"},
                    {"mData": "3pl_company_id"},
                    {"mData": "tm_done_driver_id"},
                    {"mData": "delivery_cost"},
                    {"mData": "vendor_free_delivery"},
                    {"mData": "vendor_free_delivery"},
                    // {"mData": "tablet_fee"},
                    // {"mData": "tablet_fee_settlement"},
                    // {"mData": "renewal_fee"},
                    // {"mData": "renewal_fee_settlement"},
                    // {"mData": "registration_fee"},
                    // {"mData": "registration_fee_settlement"},
                    // {"mData": "grouping"},
                    // {"mData": "campaign_fee"},
                    // {"mData": "campaign_fee_settlement"},
                    // {"mData": "refunds"},
                    // {"mData": "other"},
                    
                   
                    // {
                    //     "mData": "edit_ui"
                    // }
                    // {"mData": "erp_cr_dr"},
                    // {"mData": "client_sales_header"},
                    // {"mData": "erp_column_name"},
                    // {"mData": "delete"},
                    // {"mData": "edit"}
                ],
                "columnDefs": [{"searchable": true, "targets": [0]}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({ "name": "dateFrom","value": $("#dateFrom").val()});
                    aoData.push({ "name": "dateTo","value": $("#dateTo").val()});
                    aoData.push({ "name": "processedYN","value": $("#processedYN").val()});
                    aoData.push({ "name": "doc_id","value": $("#doc_id").val()});
                    aoData.push({ "name": "service_type","value": $("#service_type").val()});
                    aoData.push({ "name": "mode_collection","value": $("#mode_collection").val()});
                    aoData.push({ "name": "storeType","value": $("#storeType").val()});
                    
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

        function double_entry_view(id){

            load_sales_double_entry_view(id);
            load_sales_double_entry_summary(id);
            load_sales_docuemnt_log(id);
            // load_sales_double_entry_summary_all(id);

            $('#sales_return_approval_modal').modal('toggle');

        }

        function load_sales_double_entry_view(id){

            var posting_id = $('#doc_id').val();

            $.ajax({
                async: true,
                type: 'post',
            // dataType: 'json',
                data: {'client_sales_id': id,'posting_id': posting_id},
                url: "<?php echo site_url('DataSync/load_double_entry'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                
                //   $('#de_view').empty();
                    $('#de_view').html(data);
                }, error: function () {
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });

        }

        function load_sales_double_entry_summary(id){

            var posting_id = $('#doc_id').val();

            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {'client_sales_id': id, 'posting_id': posting_id},
                url: "<?php echo site_url('DataSync/load_double_entry_summary'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#de_summary').html(data);
                }, error: function () {
                    stopLoad();
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });

        }

        function load_sales_docuemnt_log(id){
            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {'client_sales_id': id},
                url: "<?php echo site_url('DataSync/load_double_entry_log_summary'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#de_summary_log').html(data);
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

////////////////////////////////////////////////////////////////////////////////

        function load_sales_double_entry_summary_all(id){

            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {'client_sales_id': id},
                url: "<?php echo site_url('DataSync/load_double_entry_summary_all'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#de_summary_all').html(data);
                }, error: function () {
                    stopLoad();
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });

        }

        function proceed_double_entry(){

            var sales_id = $('#sales_id').val();
            var double_entry_balance = $('#double_entry_balance').val();
            $('#sales_return_approval_modal').modal('toggle');

            if(double_entry_balance == 1){
                swal({
                    icon: 'warning',
                    type: "warning",
                    title: 'Oops...',
                    text: "Credit and Debit sides are not balanced properly, Can't continue",
                })
            }else{

                    swal({
                            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                            text: "Do you want to proceed",/*You want to delete this customer!*/
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Confirm",/*Delete*/
                            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                    },
                    function () {
                        supplier_double_entry_add()
                            .then (proceed_customer_double_entry())
                            .then (proceed_3pL_double_entry())
                            .then (proceed_3pL_customer_double_entry())
                            .then (proceed_direct())
                            .then (proceed_journel_voucher());
                    
                    });

            }
        }

        async function proceed_customer_double_entry(){

            var sales_id = $('#sales_id').val();
            var double_entry_balance = $('#double_entry_balance').val();
            // $('#sales_return_approval_modal').modal('toggle');
            refreshNotifications(true);

            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'sales_id': sales_id},
                    url: "<?php echo site_url('DataSync/add_general_customer'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        //sales_client_mapping_table();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
            });


        }

        async function proceed_3pL_double_entry(){

            var sales_id = $('#sales_id').val();
            var double_entry_balance = $('#double_entry_balance').val();
            //$('#sales_return_approval_modal').modal('toggle');
            refreshNotifications(true);

            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'sales_id': sales_id},
                    url: "<?php echo site_url('DataSync/add_general_3PL_vendor'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        // sales_client_mapping_table();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
            });

        }

        async function proceed_3pL_customer_double_entry(){

            var sales_id = $('#sales_id').val();
            var double_entry_balance = $('#double_entry_balance').val();
            //  $('#sales_return_approval_modal').modal('toggle');
            refreshNotifications(true);

            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'sales_id': sales_id},
                    url: "<?php echo site_url('DataSync/add_general_customer_3pl'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        //sales_client_mapping_table();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
            });

        }

        async function proceed_direct(){
            var sales_id = $('#sales_id').val();
            var double_entry_balance = $('#double_entry_balance').val();
            //$('#sales_return_approval_modal').modal('toggle');
            refreshNotifications(true);

            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'sales_id': sales_id},
                    url: "<?php echo site_url('DataSync/direct_invoice'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        //sales_client_mapping_table();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
            });

        }

        async function proceed_journel_voucher(){
            var sales_id = $('#sales_id').val();
            var double_entry_balance = $('#double_entry_balance').val();
            // $('#sales_return_approval_modal').modal('toggle');
            refreshNotifications(true);

            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'sales_id': sales_id},
                    url: "<?php echo site_url('DataSync/journel_voucher'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        sales_client_mapping_table();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
            });

        }

/////////////////////////////////////////////////////////////////////////

        async function supplier_double_entry_add(){
            var sales_id = $('#sales_id').val();
            refreshNotifications(true);

            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'sales_id': sales_id},
                    url: "<?php echo site_url('DataSync/add_general_ledger'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                    // sales_client_mapping_table();
                    
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
            });
        }

//////////////////////////////////////////////////////////////////////////////
        function reGenerateInvoice(invoiceType){

            var sales_id = $('#sales_id').val();

            if(invoiceType == 1){
                supplier_double_entry_add();
            }else if(invoiceType == 2){
                proceed_customer_double_entry();
            }else if(invoiceType == 3){
                proceed_3pL_double_entry();
            }else if(invoiceType == 4){
                proceed_3pL_customer_double_entry();
            }else if(invoiceType == 5){
                proceed_direct();
            }else if(invoiceType == 1){
                proceed_journel_voucher();
            }

            refreshNotifications(true);

            load_sales_docuemnt_log(sales_id);

        }

        function hideActionButtons(value){
            if(value == 1){
                $('#btn-section').css('display','block');
            }else{
                var sales_id = $('#sales_id').val();
                load_sales_docuemnt_log(sales_id);
                $('#btn-section').css('display','none');
            }
        }




</script>