<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('ecommerce', $primaryLanguage);
    $title = $this->lang->line('ecommerce_sales_data');
    $this->load->helper('erp_data_sync');
    $this->lang->line($title, $primaryLanguage);
    echo head_page($title, false);

    $date_format_policy = date_format_policy();
    $current_date = current_format_date();
    $cdate=current_date(FALSE);
    $startdate =date('Y-m-d', strtotime('-3 months',strtotime($cdate)));
    $start_date = convert_date_format($startdate);

    $getServiceTypes = service_type_get();

?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group col-sm-6">
            <label for=""><?php echo $this->lang->line('common_date') . ' ' . $this->lang->line('common_from'); ?></label>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="datefrom"
                        data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                        value="<?php echo $start_date; ?>" id="datefrom" class="form-control" onchange="sales_client_mapping_table()">
            </div>
        </div>
        <div class="form-group col-sm-6">
            <label for=""><?php echo $this->lang->line('common_date') . ' ' . $this->lang->line('common_to'); ?></label>
            <div class="input-group datepicto">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="dateto"
                        data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                        value="<?php echo $current_date; ?>" id="dateto" class="form-control" onchange="sales_client_mapping_table()">
            </div>
        </div>
    </div>
    <!-- <div class="col-md-4 text-center">
        &nbsp; Service Type
    </div> -->
    <div class="col-md-2">
        <label for=""><?php echo 'Service type'?></label>
        <!-- array('TMDONE' => 'TMDONE', 'RECOVERY' => 'RECOVERY','MARKET PLACE'=>'MARKET PLACE','PICKUP'=>'PICKUP') -->
        <?php echo form_dropdown('service_type', $getServiceTypes, '', 'class="form-control" id="service_type" required onchange="sales_client_mapping_table()"'); ?>
    </div>
    <div class="form-group col-md-4">
        
        <label for=""><?php echo 'Store'?></label>
        <br>
        <?php echo form_dropdown('storeType[]', all_supplier_drop_systemCode(FALSE,1), '', ' class="form-control btn-block" multiple="multiple" id="storeType" onchange="sales_client_mapping_table()" required'); ?>
    </div>

    <div class="form-group col-md-2">
       <button class="btn btn-success" onclick="update_master_data()"><i class="fa fa-cog"></i> Master Update</button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="clent_data" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
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
            <th style="min-width: 10%">Drop Fee</th><!--Code-->
            <th style="min-width: 10%">Receivable Balance</th><!--Code-->
            <!-- <th style="min-width: 10%">Item Code</th> -->
            <!-- <th style="min-width: 10%">Tablet Fee</th> -->
            <!-- <th style="min-width: 10%">Tablet Fee Settlement</th> -->
            <!-- <th style="min-width: 10%">Renewal Fee</th> -->
            <!-- <th style="min-width: 10%">Renewal Fee Settlement</th> -->
            <!-- <th style="min-width: 10%">Registration Fee</th> -->
            <!-- <th style="min-width: 10%">Registration Fee Settlement</th> -->
            <!-- <th style="min-width: 10%">Grouping</th> -->
            <!-- <th style="min-width: 10%">Campaign Fee</th> -->
            <!-- <th style="min-width: 10%">Campaign Fee Settlement</th>
            <th style="min-width: 10%">Refunds</th>
            <th style="min-width: 10%">Other</th> -->
            <th style="min-width: 10%">Vendor Free Delivery</th><!--Code-->
            <th style="min-width: 10%">Vendor Bearing Discount</th><!--Code-->
            <th style="min-width: 10%">Adjusted Vendor Settlement</th><!--Code-->
            <th style="min-width: 10%">Status</th><!--Code-->
            <th style="min-width: 10%">Manage</th>
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

<div class="modal fade" id="order_edit_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Order manage';?></h4><!--Invoice Approval-->
            </div>
            <form class="form-horizontal">
                <div class="modal-body" id="order_manage">
                    
                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="order_edit_modal_all" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Order manage';?></h4><!--Invoice Approval-->
            </div>
            <form class="form-horizontal" id="dn_form_update_all">
                <div class="modal-body" id="order_manage">
                    
                    <div id="filter-panel" class="filter-panel"><div>

                    <div class="form-group">

                        <label class="col-sm-2 control-label">Date From Selected </label><!--Comments-->
                        <div class="col-sm-10">
                            <input type="text" class="form-control" disabled  id="datefrom_selected" />
                        </div>

                    </div>

                    <div class="form-group">

                        <label class="col-sm-2 control-label">Date to Selected </label><!--Comments-->
                        <div class="col-sm-10">
                            <input type="text" class="form-control" disabled  id="dateto_selected" />
                        </div>

                    </div>

                    <div class="form-group">

                        <label class="col-sm-2 control-label">Service Type Selected </label><!--Comments-->
                        <div class="col-sm-10">
                            <input type="text" class="form-control" disabled  id="service_type_selected" />
                        </div>

                    </div>

                    <div class="form-group">

                        <label class="col-sm-2 control-label">Stores Selected </label><!--Comments-->
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="4" id="stores_selected" disabled></textarea>
                        </div>

                    </div>

                  

                    <div class="table-responsive">
                        <table id="clent_double_entry" class="<?php echo table_class() ?>">
                        <thead>
                            <tr>
                                <th style="min-width: 10%">Edit Field</th><!--Code-->
                                <th style="min-width: 10%">Value</th><!--Code-->
                            </tr>
                        </thead>
                        <tbody>
                            
                            <tr>
                                <td>
                                    <span class="text-bold">Delivery fee</span>
                                    <input type="hidden" name="fields[]" value="delivery_fee" />
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right ex_input"  name="edit_fields[]" id="delivery_fee" />
                                </td>
                               
                            </tr>

                            <tr>
                                <td><span class="text-bold">Credits</span>
                                    <input type="hidden" name="fields[]" value="credit" />
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right ex_input"  name="edit_fields[]" id="credit" />
                                </td>
                                
                            </tr>

                            <tr>
                                <td>
                                    <span class="text-bold">Discounts</span>
                                    <input type="hidden" name="fields[]" value="discount" />
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right ex_input" name="edit_fields[]" id="discount" />
                                </td>
                               
                            </tr>

                            <tr>
                                <td>
                                    <span class="text-bold">3rd Party Commission</span>
                                    <input type="hidden" name="fields[]" value="tmdone_commission" />
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right ex_input" name="edit_fields[]" id="tmdone_commission" />
                                </td>
                                
                            </tr>

                            
                            <tr>
                                <td>
                                    <span class="text-bold">Bank Charges</span>
                                    <input type="hidden" name="fields[]" value="bank_charges" />
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right ex_input" name="edit_fields[]" id="bank_charges" />
                                </td>
                               
                            </tr>

                            <tr>
                                <td>
                                    <span class="text-bold">Bank Charges VAT</span>
                                    <input type="hidden" name="fields[]" value="bank_charges_vat" />
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right ex_input" name="edit_fields[]" id="bank_charges_vat" />
                                </td>
                                
                            </tr>

                            <tr>
                                <td>
                                    <span class="text-bold">Total Adjustment</span>
                                    <input type="hidden" name="fields[]" value="total_adjustment" />
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right ex_input" name="edit_fields[]" id="total_adjustment" />
                                </td>
                                
                            </tr>

                            <tr>
                                <td>
                                    <span class="text-bold">TmDone Adjustment</span>
                                    <input type="hidden" name="fields[]" value="tmdone_adjustment" />
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right ex_input" name="edit_fields[]" id="tmdone_adjustment" />
                                </td>
                                
                            </tr>

                            <tr>
                                <td>
                                    <span class="text-bold">Driver Adjustment</span>
                                    <input type="hidden" name="fields[]" value="driver_adjustment" />
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right ex_input" name="edit_fields[]" id="driver_adjustment" />
                                </td>
                                
                            </tr>

                            <tr>
                                <td>
                                    <span class="text-bold">Vendor Adjustment.</span>
                                    <input type="hidden" name="fields[]" value="vendor_adjustment" />
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right ex_input" name="edit_fields[]" id="vendor_adjustment" />
                                </td>
                                
                            </tr>

                            <tr>
                                <td>
                                    <span class="text-bold">Vendor Free Delivery.</span>
                                    <input type="hidden" name="fields[]" value="vendor_free_delivery" />
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right ex_input" name="edit_fields[]" id="vendor_free_delivery" />
                                </td>
                        
                            </tr>

                            <tr>
                                <td>
                                    <span class="text-bold">Vendor Barring Fee.</span>
                                    <input type="hidden" name="fields[]" value="vendor_barring_fee" />
                                </td>
                                <td>
                                    <input type="text" class="form-control text-right ex_input" name="edit_fields[]" id="vendor_barring_fee" />
                                </td>
                        
                            </tr>
                            
                            
                            </tbody>
                            </table>
                        <hr>
                        <button class="btn btn-success" type="submit" id="btnSubmit">
                            <i class="fa fa-check"></i> Update
                        </button>

                
                    </div>

                    </div>

                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- <script type="text/javascript" src="https://datatables.net/release-datatables/extensions/FixedColumns/js/dataTables.fixedColumns.js"> </script> -->

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

        //date format
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            sales_client_mapping_table();
        });
        $('.datepicto').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            sales_client_mapping_table();
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
                "sAjaxSource": "<?php echo site_url('DataSync/fetch_client_data'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings,data) {
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
                    // var total_bill = (data.net_vendor_bill != null) ? data.net_vendor_bill : 0;
                    // var tmdone_commission = (data.tmdone_commission != null) ? data.tmdone_commission : 0;
                    // var vat_tmdone_commission = (data.vat_tmdone_commission != null) ? data.vat_tmdone_commission : 0;
                    // var bank_charges = (data.bank_charges != null) ? data.bank_charges : 0;
                    // var bank_charges_vat = (data.bank_charges_vat != null) ? data.bank_charges_vat : 0;
                    // var vendor_free_delivery = (data.vendor_free_delivery != null) ? data.vendor_free_delivery : 0;
                    // var vendor_adjustment = (data.vendor_adjustment != null) ? data.vendor_adjustment : 0;
                    // var vendor_barring_fee = (data.vendor_barring_fee != null) ? data.vendor_barring_fee : 0;

                    // var adjusted_value = (parseFloat(total_bill) - ( parseFloat(tmdone_commission) + parseFloat(vat_tmdone_commission) + parseFloat(bank_charges) + parseFloat(bank_charges_vat) + parseFloat(vendor_free_delivery) + parseFloat(vendor_adjustment) + parseFloat(vendor_barring_fee) )).toFixed(3);
                    
              //      $('td:eq(60)', row).html( adjusted_value );

                },
                "aoColumns": [
                    {"mData": "id"},
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
                    {"mData": "drop_fee"},
                    {"mData": "receivable_balance"},
                    // {"mData": "item_code"},
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
                    {"mData": "vendor_free_delivery"},
                    {"mData": "vendor_barring_fee"},
                    {"mData": "adjusted_settlement"},
                    {"mData": "process"},
                    {"mData": "de_model"},
                    // {
                    //     "mData": "edit_ui"
                    // }
                    // {"mData": "erp_cr_dr"},
                    // {"mData": "client_sales_header"},
                    // {"mData": "erp_column_name"},
                    // {"mData": "delete"},
                    // {"mData": "edit"}
                ],
                "columnDefs": [{"searchable": false, "targets": [0]}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({ "name": "service_type","value": $("#service_type").val()});
                    aoData.push({ "name": "storeType","value": $("#storeType").val()});
                    aoData.push({ "name": "datefrom","value": $("#datefrom").val()});
                    aoData.push({ "name": "dateto","value": $("#dateto").val()});

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

        function hideActionButtons(value){
            if(value == 1){
                $('#btn-section').css('display','block');
            }else{
                var sales_id = $('#sales_id').val();
                load_sales_docuemnt_log(sales_id);
                $('#btn-section').css('display','none');
            }
        }

        function double_entry_view(id){

            load_sales_double_entry_view(id);
            load_sales_double_entry_summary(id);
            load_sales_docuemnt_log(id);
            // load_sales_double_entry_summary_all(id);

            $('#sales_return_approval_modal').modal('toggle');

        }

        function load_sales_double_entry_view(id){

            $.ajax({
                async: true,
                type: 'post',
               // dataType: 'json',
                data: {'client_sales_id': id},
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

            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {'client_sales_id': id},
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
          


            // if(double_entry_balance == 1){
            //     swal({
            //         icon: 'warning',
            //         type: "warning",
            //         title: 'Oops...',
            //         text: "Credit and Debit sides are not balanced properly, Can't continue",
            //     })
            // }else{

            // swal({
            //         title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            //         text: "Do you want to proceed",/*You want to delete this customer!*/
            //         type: "warning",
            //         showCancelButton: true,
            //         confirmButtonColor: "#DD6B55",
            //         confirmButtonText: "Confirm",/*Delete*/
            //         cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            // },
            

            // }
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

            // if(double_entry_balance == 1){
            //     swal({
            //         icon: 'warning',
            //         type: "warning",
            //         title: 'Oops...',
            //         text: "Credit and Debit sides are not balanced properly, Can't continue",
            //     })
            // }else{

            // swal({
            //         title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            //         text: "Do you want to proceed",/*You want to delete this customer!*/
            //         type: "warning",
            //         showCancelButton: true,
            //         confirmButtonColor: "#DD6B55",
            //         confirmButtonText: "Confirm",/*Delete*/
            //         cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            // },
            // function () {
               
            // });

            // }
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

            // if(double_entry_balance == 1){
            //     swal({
            //         icon: 'warning',
            //         type: "warning",
            //         title: 'Oops...',
            //         text: "Credit and Debit sides are not balanced properly, Can't continue",
            //     })
            // }else{

            // swal({
            //         title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            //         text: "Do you want to proceed",/*You want to delete this customer!*/
            //         type: "warning",
            //         showCancelButton: true,
            //         confirmButtonColor: "#DD6B55",
            //         confirmButtonText: "Confirm",/*Delete*/
            //         cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            // },
            // function () {
                
            // });

            // }
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

            // if(double_entry_balance == 1){
            //     swal({
            //         icon: 'warning',
            //         type: "warning",
            //         title: 'Oops...',
            //         text: "Credit and Debit sides are not balanced properly, Can't continue",
            //     })
            // }else{

            // swal({
            //         title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            //         text: "Do you want to proceed",/*You want to delete this customer!*/
            //         type: "warning",
            //         showCancelButton: true,
            //         confirmButtonColor: "#DD6B55",
            //         confirmButtonText: "Confirm",/*Delete*/
            //         cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            // },
            // function () {
               
            // });
            // }

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

    //////////////////////////////////////////////////////////////////////////////
        function manage_edit_order(id){
            //$('#order_edit_modal').modal('toggle');

            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {'order_id': id},
                url: "<?php echo site_url('DataSync/load_order_edit_form'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#order_manage').html(data);
                    $('#order_edit_modal').modal('toggle');
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

    /////////////////////////////////////////////////////////////////////////////////

        function update_master_data(){

            // order_edit_modal
            var storeType = $('#storeType option:selected').toArray().map(item => item.text).join(' , ')
            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var service_type = $('#service_type option:selected').text();

            $('.ex_input').each(function(){
                this.value = "";
            })

            if(storeType == ''){
                storeType = 'Apply for all';
            }

            $('#stores_selected').empty().text(storeType);
            $('#datefrom_selected').empty().val(datefrom);
            $('#dateto_selected').empty().val(dateto);
            $('#service_type_selected').empty().val(service_type);

            $('#order_edit_modal_all').modal('toggle');

        }


    ////////////////////////////////////////////////////////////////////////////////

        $('#dn_form_update_all').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
               
            },
        }).on('success.form.bv', function (e) {

            e.preventDefault();
            $('#btnSubmit').prop('disabled',false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            var storeType = $('#storeType').val();

            if(storeType == '' || storeType == null){
                storeType = 'all';
            }

            data.push({'name': 'datefrom', 'value': $('#datefrom').val()});
            data.push({'name': 'dateto', 'value': $('#dateto').val()});
            data.push({'name': 'service_type', 'value': $('#service_type option:selected').val()});
            data.push({'name': 'storeType', 'value': storeType }); 
            // data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            // data.push({'name': 'SupplierDetails', 'value': $('#supplier option:selected').text()});
            // data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: data,
                url: "<?php echo site_url('DataSync/edit_order_filtered_all'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    stopLoad();
                    sales_client_mapping_table();
                    $('#order_edit_modal_all').modal('toggle');
                    
                    
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        });

</script>