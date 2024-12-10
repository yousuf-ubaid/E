<?php
    $primaryLanguage = getPrimaryLanguage();
    $this->load->helper('erp_data_sync');
    $this->load->library('sequence');
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('ecommerce', $primaryLanguage);
    $title = $this->lang->line('ecommerce_sales_data');
    $this->lang->line($title, $primaryLanguage);
    echo head_page('<button class="btn btn-success" onclick="go_back()"><i class="fa fa-arrow-left"></i></button> Sales Data Manual Posting', false);
    
    $doc_id = $this->sequence->sequence_generator('ABSI/');
    $date_formated = date('Y-m-d',strtotime(current_date()));
    $get_service_types = service_type_get(1);

    $posting_id = $data_arr;

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
    <div class="col-md-12">
        <form class="form-horizontal" id="sales_column_posting">

            <input type="hidden" name="posting_id" id="posting_id" value="<?php echo $posting_id ?>" />

            <table class="<?php echo table_class() ?>">
                <tr>
                    <td> 
                        <label for="inputEmail3" class="col-sm-4 control-label">Doc ID</label>

                        <div class="col-sm-8">

                            <div class="col-sm-8">
                                <?php if(!isset($posting_detials)) { ?>
                                    <input type="text" class="form-control" name="doc_id" id="doc_id" value="<?php echo $doc_id ?>" />
                                <?php } else { ?>
                                    <input type="text" class="form-control" name="doc_id" id="doc_id" value="<?php echo $posting_detials['doc_id'] ?>" />
                                <?php } ?>
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
                        <label for="inputEmail3" class="col-sm-4 control-label">Date</label><!--Status-->

                        <div class="col-sm-8">
                            <input type="date" name="IncidateDateTo" data-inputmask="'alias': '<?php  ?>'" size="16" onchange="Otable.draw()" value="<?php echo $date_formated ?>" id="date" class="input-small form-control">
                        </div>
                    </td>

                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Service Type <br> </label><!--Status-->

                        <div class="col-sm-8">
                            <?php echo form_dropdown('service_type',$get_service_types, isset($posting_detials['service_type']) ? $posting_detials['service_type']: '', 'class="form-control" id="service_type" required'); ?>
                        </div>
                    </td><!--array('' =>  'Please Select'/*'Please Select'*/,'1' =>'TMDONE'/*'Approved'*/, '2' =>'MARKET PLACE','3'=>'PICKUP','4'=>'RECOVERY')-->

                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Mode of Collection <br> </label><!--Status-->

                        <div class="col-sm-8">
                            <?php echo form_dropdown('mode_collection', array('' =>  'Please Select'/*'Please Select'*/,'1' =>'All','2' =>'Cash', '3'=> 'Card'), isset($posting_detials['mode_collection']) ? $posting_detials['mode_collection']: '', 'class="form-control" id="mode_collection" required'); ?>
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
                </tr>
                <tr>
                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Date From</label><!--Status-->

                        <div class="col-sm-8">
                        <input type="date" name="IncidateDateTo" data-inputmask="'alias': '<?php  ?>'" size="16" onchange="Otable.draw()" value="<?php echo isset($dateFrom) ? $dateFrom : '' ?>" id="dateFrom" class="input-small form-control">
                        </div>
                    </td>
                    <td>
                        <label for="inputEmail3" class="col-sm-4 control-label">Date To <br> </label><!--Status-->

                        <div class="col-sm-8">
                        <input type="date" name="IncidateDateTo" data-inputmask="'alias': '<?php  ?>'" size="16" onchange="Otable.draw()" value="<?php echo isset($dateTo) ? $dateTo : '' ?>" id="dateTo" class="input-small form-control" disabled>
                        </div>
                    </td>


                  

                    <td>
                        <button class="btn btn-success" type="button" id="load_posting_btn"> <i class="fa fa-search-plus"></i> &nbsp Load </button>
                        <?php if(isset($posting_detials)) { ?>
                            <button class="btn btn-success" type="button" id="update_posting"> <i class="fa fa-key"></i> &nbsp Update </button>
                        <?php } else { ?>
                            <button class="btn btn-success" type="button" id="proceed_posting"> <i class="fa fa-plus"></i> &nbsp Create </button>
                        <?php } ?>
                    </td>
                <tr>
            </table>
        </form>
    </div>
    
</div>
<hr>

<div class="table-responsive">
    <table id="clent_data" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <!-- <th style="min-width: 10%">Company Name</th> -->
            <th style="min-width: 10%">Service Type</th><!--Code-->
            <!-- <th style="min-width: 10%">Store ID</th>Code -->
            <!-- <th style="min-width: 10%">Store</th>Code -->
            <!-- <th style="min-width: 10%">Customer ID</th>Code -->
            <th style="min-width: 10%">Customer</th><!--Code-->
            <th style="min-width: 10%">Customer Tel</th><!--Code-->
            <th style="min-width: 10%">Order</th><!--Code-->
            <!-- <th style="min-width: 10%">Zone</th>Code -->
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
            <!-- <th style="min-width: 10%">Driver Name</th>Code -->
            <!-- <th style="min-width: 10%">Driver ID</th>Code -->
            <th style="min-width: 10%">Points Reedemed</th><!--Code-->
            <th style="min-width: 10%">Cash Collected</th><!--Code-->
            <th style="min-width: 10%">Credit Card</th><!--Code-->
            <th style="min-width: 10%">TM Credits</th><!--Code-->
            <!-- <th style="min-width: 10%">3PL Company ID</th>Code -->
            <!-- <th style="min-width: 10%">TM Done Driver ID</th>Code -->
            <th style="min-width: 10%">Delivery Cost</th><!--Code-->
            <th style="min-width: 10%">Tablet Fee</th><!--Code-->
            <th style="min-width: 10%">Tablet Fee Settlement</th><!--Code-->
            <th style="min-width: 10%">Renewal Fee</th><!--Code-->
            <th style="min-width: 10%">Renewal Fee Settlement</th><!--Code-->
            <th style="min-width: 10%">Registration Fee</th><!--Code-->
            <th style="min-width: 10%">Registration Fee Settlement</th><!--Code-->
            <th style="min-width: 10%">Grouping</th><!--Code-->
            <th style="min-width: 10%">Campaign Fee</th><!--Code-->
            <th style="min-width: 10%">Campaign Fee Settlement</th><!--Code-->
            <th style="min-width: 10%">Refunds</th><!--Code-->
            <th style="min-width: 10%">Other</th><!--Code-->
            <th style="min-width: 10%">Edit</th><!--Code-->
          
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>







<script type="text/javascript">

    function go_back(){
        fetchPage('system/ecommerce/sales_order_posting','Test','Sales Order Posting');
    }

    $('#load_posting_btn').click(() => {

        var dateFrom = $('#dateFrom').val();
        var dateTo = $('#dateTo').val();

        if(dateFrom == '' || dateTo == ''){
            swal({
                    icon: 'warning',
                    type: "warning",
                    title: 'Oops...',
                    text: "Select date from and to",
            })
        }else{
            sales_client_mapping_table();
        }

    });

    sales_client_mapping_table();

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
            "sAjaxSource": "<?php echo site_url('DataSync/fetch_client_data_posting'); ?>",
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
                {"mData": "id"},
                // {"mData": "company_name"},
                {"mData": "service_type"},
                // {"mData": "store_id"},
                // {"mData": "store"},
                // {"mData": "customer_id"},
                {"mData": "customer"},
                {"mData": "customer_tel"},
                {"mData": "order"},
                // {"mData": "zone"},
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
                // {"mData": "driver_name"},
                // {"mData": "driver_id"},
                {"mData": "points_redeemed"},
                {"mData": "cash_collected"},
                {"mData": "credit_card"},
                {"mData": "tm_credits"},
                // {"mData": "3pl_company_id"},
                // {"mData": "tm_done_driver_id"},
                {"mData": "delivery_cost"},
                {"mData": "tablet_fee"},
                {"mData": "tablet_fee_settlement"},
                {"mData": "renewal_fee"},
                {"mData": "renewal_fee_settlement"},
                {"mData": "registration_fee"},
                {"mData": "registration_fee_settlement"},
                {"mData": "grouping"},
                {"mData": "campaign_fee"},
                {"mData": "campaign_fee_settlement"},
                {"mData": "refunds"},
                {"mData": "other"},
                {"mData": "process"},
                // {"mData": "de_model"},
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
                aoData.push({ "name": "dateFrom","value": $("#dateFrom").val()});
                aoData.push({ "name": "dateTo","value": $("#dateTo").val()});
                aoData.push({ "name": "service_type","value": $("#service_type").val()});
                aoData.push({ "name": "mode_collection","value": $("#mode_collection").val()});
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

    $('#proceed_posting').click(() => {
        var dateFrom = $('#dateFrom').val();
        var dateTo = $('#dateTo').val();
        var comments = $('#comments').val();
        var date = $('#date').val();
        var doc_id = $('#doc_id').val();
        var service_type = $('#service_type').val();
        var mode_collection = $('#mode_collection').val();

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
            
            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {'dateFrom': dateFrom,'dateTo' : dateTo,'comments' : comments, 'date': date, 'doc_id' : doc_id, 'service_type': service_type,'mode_collection': mode_collection},
                url: "<?php echo site_url('DataSync/confirm_daily_posting'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    sales_client_mapping_table();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });
            
        });

    })


    $('#update_posting').click(() => {

        var dateFrom = $('#dateFrom').val();
        var dateTo = $('#dateTo').val();
        var comments = $('#comments').val();
        var date = $('#date').val();
        var doc_id = $('#doc_id').val();
        var posting_id = $('#posting_id').val();
        var service_type = $('#service_type').val();
        var mode_collection = $('#mode_collection').val();

        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "Do you want to update this posting.",/*You want to delete this customer!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Confirm",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            
            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {'dateFrom': dateFrom,'dateTo' : dateTo,'comments' : comments, 'date': date, 'doc_id' : doc_id,'posting_id': posting_id,'service_type': service_type,'mode_collection': mode_collection},
                url: "<?php echo site_url('DataSync/confirm_daily_posting'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                }
            });
            
        });
    })

    /////////////////////////////////////////////////////////////////////////////////

    $('#dateFrom').change(function(){
        var dateFrom = $('#dateFrom').val();
        $('#dateTo').val(dateFrom);
    })

</script>