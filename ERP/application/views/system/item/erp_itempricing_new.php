<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title="Item Pricing";
echo head_page($title,false);
$address=load_addresstype_drop();
$warehouseDetails = get_warehouse_list();
$customerList = get_customermaster_list();
$itemAutoID =  trim($this->input->post('page_id'));
$uomList = get_uomlist_for_item($itemAutoID,true);
$payment_arr = get_payment_methods_available();


$default_currency=$this->common_data['company_data']['company_default_currency'];

?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    tbody {
        text-align:right;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-5">
        <table class="<?php //echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> Active
                        
                    </td>
                    <td><span class="label label-danger">&nbsp;</span> in-active
                    </td>
                   
                </tr>
        </table>
    </div>
    <div class="col-md-9">
        
    </div>

    <div class="col-md-3 text-right">
        <button type="button" onclick="open_address_model();" class="btn btn-primary pull-right" ><i class="fa fa-plus"></i> Create<!--Create Purchasing Address--> </button>
    </div>
</div><hr>

<input type="hidden" name="itemAutoID" id="itemAutoID" value="<?php echo trim($this->input->post('page_id')) ?>" />
<input type="hidden" name="itemWACamount" id="itemWACamount" value="" />

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label class="col-sm-2 control-label">Item Name</label>
            <div class="col-sm-10">
                <span id="itemName" class="form-control text-bold"></span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Item Code</label>
            <div class="col-sm-10">
                <span id="itemCode" class="form-control text-bold"></span>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label class="col-sm-3 control-label">Item Category</label>
            <div class="col-sm-9">
                <span id="itemCategory" class="form-control text-bold"></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Default Unit of Measure</label>
            <div class="col-sm-9">
                <span id="itemUOM" class="form-control text-bold"></span>
            </div>
        </div>
        
    </div>
</div>



<div class="table-responsive">
    <table id="address_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('erp_item_price_type');?></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('erp_item_price_customer');?></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('erp_uom_code');?></th>
                <th style="min-width: 15%"><?php echo $this->lang->line('erp_item_price_outlet');?></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('erp_item_price_cost');?> ( <?php echo $default_currency?> )</th>
                <th style="min-width: 11%"><?php echo $this->lang->line('erp_item_price_margin');?>(%)</th>
                <th style="min-width: 11%"><?php echo $this->lang->line('erp_item_price_sales_price');?> ( <?php echo $default_currency?> )</th>
                <th style="min-width: 11%"><?php echo $this->lang->line('erp_item_price_discount');?>(%)</th>
                <th style="min-width: 11%"><?php echo $this->lang->line('erp_item_price_rsales_price');?> ( <?php echo $default_currency?> )</th>
                <th style="min-width: 11%"><?php echo $this->lang->line('erp_item_price_profit');?> ( <?php echo $default_currency?> )</th>
                <th style="min-width: 11%">Default</th>
                <th style="min-width: 11%">Active</th>
                <th style="min-width: 8%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<div aria-hidden="true" role="dialog" id="item_price_model" class="modal fade" style="display: none;">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="itemPriceModelHeader"></h3>
            </div>
            <form role="form" id="item_price_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" class="form-control" id="itemPriceedit" name="itemPriceedit">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Type <?php required_mark(); ?></label>
                            <div class="col-sm-5">
                                <select name="type" id="type" class="form-control select2" onchange="select_pricing_type()">
                                    <option class="hide" value="">Select Type</option>
                                    <option value="Price">Price</option>
                                    <option value="Direct">Direct</option>
                                    <option value="Selected">Selected</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Customer <?php required_mark(); ?></label>
                            <div class="col-sm-5" id="customer_list_select">
                                <?php  echo form_dropdown('customer[]', $customerList,'','class="form-control" id="customer_multi" multiple="multiple" '); ?>
                            </div>
                            <div class="col-sm-5 hide" id="customer_list_all">
                                <?php  echo form_dropdown('customer', array('All'=>'All'),'','class="form-control" id="customer" '); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Outlet </label>
                            <div class="col-sm-5" id="outlet_list_select">
                                <?php  echo form_dropdown('outlet', $warehouseDetails,'','class="form-control" id="outlet"'); ?>
                            </div>
                            <div class="col-sm-5 hide" id="outlet_list_all">
                                <?php  echo form_dropdown('outlet',  array(''=>'All'),'','class="form-control" id="outlet"'); ?>
                            </div>
                        </div>

                        <div class="form-group hide" id="line_payment_method">
                            <label class="col-sm-4 control-label">Payment Method </label>
                            <div class="col-sm-5">
                                <?php  echo form_dropdown('payment_arr', $payment_arr,'','class="form-control" id="payment_arr"'); ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-4 control-label">UOM </label>
                            <div class="col-sm-5">
                                <?php  echo form_dropdown('uom', $uomList,'','class="form-control" id="uom"'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Cost  ( <?php echo $default_currency?> )<?php  required_mark();  ?></label>
                            <div class="col-sm-4">
                               <input type="text" class="form-control number" id="cost" name="cost"  placeholder="0.00" readonly>
                              
                            </div>
                            <div class="col-sm-2">
                              
                               <button type="button" onclick="change_cost_request();" class="btn btn-primary pull-right" ><i class="fa fa-edit"></i> Change Cost </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Margin(%)<?php required_mark(); ?></label>
                            <div class="col-sm-6">
                               <input type="text" class="form-control number" id="margin" name="margin"  placeholder="0.00" onkeyup="generate_sale_price()" onchange="generate_sale_price()">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Sales Price ( <?php echo $default_currency?> )<?php required_mark(); ?></label>
                            <div class="col-sm-6">
                               <input type="text" class="form-control number" id="salesprice" name="salesprice"  placeholder="0.00" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Discount(%)<?php required_mark(); ?></label>
                            <div class="col-sm-6">
                               <input type="text" class="form-control number" id="discount" name="discount" value="0"  placeholder="0.00" onkeyup="generate_r_sale_price()">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">R Sales Price ( <?php echo $default_currency?> )<?php required_mark(); ?></label>
                            <div class="col-sm-6">
                               <input type="text" class="form-control number" id="rsalesprice" name="rsalesprice"  placeholder="0.00" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Profit ( <?php echo $default_currency?> )<?php required_mark(); ?></label>
                            <div class="col-sm-6">
                               <input type="text" class="form-control number" id="profit" name="profit"  placeholder="0.00" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Default</label>
                            <div class="col-sm-6">
                            <input id="isdefault" type="checkbox"
                                            data-caption="" class="columnSelected" name="isdefault" value="1">
                                        <label for="checkbox">
                                            &nbsp;
                                        </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Active</label>
                            <div class="col-sm-6">
                            <input id="isactive" type="checkbox"
                                            data-caption="" class="columnSelected" name="isactive" value="1">
                                        <label for="checkbox">
                                            &nbsp;
                                        </label>
                            </div>
                        </div>

                        
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_close')?><!--Close--></button>
                        <a onclick="saveItemPricingDetail()" class="btn btn-primary">Save</a>
                    </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

    var company_default_decimal = <?php echo $this->common_data['company_data']['company_default_decimal'] ?>;
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/item/srp_itempricing_view','Test','item pricing');
        });

        $('.select2').select2();

        $("#customer_multi").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        loadItemDetail();
        fetch_address();
        number_validation();

    });

    function loadItemDetail(){

        var itemAutoID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemAutoID': itemAutoID},
                url: "<?php echo site_url('ItemMaster/load_item_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#itemUOM').html(data.defaultUnitOfMeasure)
                        $('#itemName').html(data.itemName)
                        $('#itemCode').html(data.itemSystemCode)
                        $('#itemCategory').html(data.mainCategory)
                        $('#itemWACamount').val(data.companyLocalWacAmount)
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

    }

    function saveItemPricingDetail() {
        var data = $('#item_price_form').serializeArray();

        var itemAutoID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if (itemAutoID) {
            data.push({'name': 'itemAutoID', 'value': itemAutoID});
            data.push({'name': 'uom', 'value': $('#uom :selected').val()});

            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('ItemMaster/save_item_pricing_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                        // $('.umoDropdown').prop("disabled", true);
                    },
                    success: function (data) {
                        stopLoad();
                        
                        if(data[0]){
                            myAlert(data[0], data[1]);
                        }
                        
                        refreshNotifications(true);
                        if (data) {
                            // setTimeout(function () {
                            //     tab_active(tabID);
                            // }, 300);
                           
                            $('#item_price_model').modal('hide');
                            $('#item_price_form')[0].reset();
                            //$('.select2').select2('');
                            fetch_address();
                        } else {
                            // $('.discount').prop('disabled', true);
                            // $('.discount_amount').prop('disabled', true);
                        }
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
        } else {
            swal({
                title: "Error !",
                text: "Something went wrong",
                type: "error"
            });
        }
    }

    function select_pricing_type(){
      
        var val = $('#type').val();
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemId':  p_id},
            url: "<?php echo site_url('ItemMaster/fetch_item_details_for_pricing'); ?>",
                success: function (data) {
                    $('#margin').prop('disabled', false);

                    if(!($('#cost').val() > 0)){
                        $('#cost').val('');
                        $('#cost').val(parseFloat(data['companyLocalWacAmount']).toFixed(2));
                    }
                   
                  
                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
        });

        if(val=="Direct"){
            
            $('#customer_list_select').addClass('hide');
            $('#customer_list_select select').prop('disabled',true);
            $('#customer_list_all select').prop('disabled',false);
            $('#customer_list_all').removeClass('hide');
            $('#outlet_list_select').removeClass('hide');
            $('#outlet_list_all').addClass('hide');
            $('#outlet_list_all #outlet').prop('disabled',true);

            $('#outlet').prop('disabled',false);
            $('#uom').prop('disabled',true);

            $('#line_payment_method').addClass('hide');

        }else if(val=="Price"){

           $('#customer_list_select').addClass('hide');
           $('#customer_list_all').removeClass('hide');
           $('#outlet_list_select').addClass('hide');
           $('#outlet_list_all').removeClass('hide');
           $('#outlet_list_all #outlet').prop('disabled',false);
           $('#uom').prop('disabled',false);
           $('#outlet').prop('disabled',true);
           $('#customer_list_all select').prop('disabled',true);
           $('#customer_list_select select').prop('disabled',true);
           $('#outlet_list_all select').prop('disabled',true);

           $('#line_payment_method').removeClass('hide');

        }else{

            $('#outlet_list_select').addClass('hide');
            $('#outlet_list_all').removeClass('hide');

            $('#customer_list_select').removeClass('hide');
            $('#customer_list_all').addClass('hide');
            $('#customer_list_all select').prop('disabled',true);
            $('#customer_list_select select').prop('disabled',false);
            $('#outlet').prop('disabled',true);
            $('#uom').prop('disabled',true);

            $('#line_payment_method').addClass('hide');

        }
    }

    function generate_sale_price(){
        $('#salesprice').val('');
        $('#discount').prop('disabled', false);

        var margin=$('#margin').val();
        var cost =$('#cost').val();
        var sale_price = parseFloat(cost)+ (parseFloat(cost)*parseFloat(margin))/100;
        $('#salesprice').val(sale_price.toFixed(company_default_decimal));

        generate_r_sale_price();

    }

    function generate_r_sale_price(){

        $('#rsalesprice').val('');
        var salesprice=$('#salesprice').val();

        var discount = $('#discount').val();

        var cost = $('#cost').val();

        var r_sale_price = parseFloat(salesprice)- (parseFloat(salesprice)*parseFloat(discount))/100;

        $('#rsalesprice').val(r_sale_price.toFixed(company_default_decimal));

        var profit=parseFloat(r_sale_price)-parseFloat(cost);

        $('#profit').val(profit.toFixed(company_default_decimal));

    }

    function fetch_address() {
        var p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        var Otable = $('#address_table').DataTable({"language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('ItemMaster/load_item_pricing_data'); ?>",
            "aaSorting": [[0, 'desc']],
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
                {"mData": "pricingAutoID"},
                {"mData": "pricingType"},
                {"mData": "customer"},
                {"mData": "uom"},
                {"mData": "outlet"},
                {"mData": "cost"},
                {"mData": "margin"},
                {"mData": "salesPrice"},
                {"mData": "discount"},
                {"mData": "rSalesPrice"},
                {"mData": "profit"},
                {"mData": "isdefault"},
                {"mData": "confirmed"},
                {"mData": "action"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                aoData.push({"name": "itemAutoID", "value": p_id});
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

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function open_address_model(set = null) {

        var itemWACamount = $('#itemWACamount').val();

        $('#itemPriceedit').val('');
        $('#cost').attr("readonly","true");
        // $('#customer').empty();
        // $('#outlet').empty();
        
        // $('#customer option:selected').prop("selected", false);
        $('#margin').prop('disabled', true);
        $('#discount').prop('disabled', true);
        $('#item_price_form')[0].reset();

        if(set){
            $('.multiselect2').prop('disabled',true);
        }else{
            $('.multiselect2').prop('disabled',false);
        }

        $('#type').val('').change();
        $('#cost').val(parseFloat(itemWACamount).toFixed(2));

        $('#itemPriceModelHeader').html('<div class="h4 text-bold">Add Item Pricing</div>');
       // $('#item_price_form').bootstrapValidator('resetForm', true);
        $("#item_price_model").modal({backdrop: "static"});
    }


    function openaddressmodel(id){
       
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {id:id},
            url: "<?php echo site_url('ItemMaster/edit_item_pricing'); ?>",
            success: function (data) {
                open_address_model('edit');
                
                $('#discount').prop('disabled', false);
                $('#itemPriceModelHeader').html('<div class="h4 text-bold">Edit Item Pricing</div>');
                $('#itemPriceedit').val(id);
                $('#type').val(data['pricingType']).change();
                
                $('#outlet').val(data['wareHouseAutoID']).change();
                $('#cost').val(data['cost']);
                $('#margin').val(data['margin']);
                $('#salesprice').val(data['salesPrice']);
                $('#discount').val(data['discount']);
                $('#rsalesprice').val(data['rSalesPrice']);
                $('#profit').val(data['profit']);
                $('#payment_arr').val(data['paymentMethod']).change();

                $( "#margin" ).prop("disabled", false );

                $('#customer_multi').val(data['customer']).change();
                

                if(data['pricingType'] == 'Selected'){

                    $('#customer_list_select').removeClass('hide');
                    $('#customer_list_all').addClass('hide');
                    $('#customer_list_all select').prop('disabled',true);
                    $('#customer_list_select select').prop('disabled',false);
                    $('#outlet').prop('disabled',true);
                    $('#uom').prop('disabled',true);

                }else if(data['pricingType'] == 'Price'){

                    $('#customer_list_select').addClass('hide');
                    $('#customer_list_all').removeClass('hide');
                    $('#outlet_list_select').addClass('hide');
                    $('#outlet_list_all').removeClass('hide');
                    $('#uom').prop('disabled',false);
                    $('#outlet').prop('disabled',true);
                    $('#customer_list_all select').prop('disabled',true);
                    $('#outlet_list_all select').prop('disabled',true);
                    $('#customer_list_select select').prop('disabled',true);



                }else{

                    $('#customer_list_select').addClass('hide');
                    $('#customer_list_select select').prop('disabled',true);
                    $('#customer_list_all select').prop('disabled',true);
                    $('#customer_list_all').removeClass('hide');
                    $('#outlet_list_select').removeClass('hide');
                    $('#outlet_list_select select').prop('disabled',false);
                    $('#outlet_list_all').addClass('hide');
                    $('#outlet_list_all select').prop('disabled',true);
                 //   $('#outlet').prop('disabled',false);
                    $('#uom').prop('disabled',true);

                }

                if(data['isDefault']==1){
                    $( "#isdefault" ).prop( "checked", true );
                }

                if(data['isActive']==1){
                    $( "#isactive" ).prop( "checked", true );
                }
            }, 
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again')?>.');
            }
        });
    }

    function deleteaddress(id){
        swal({   title: "<?php echo $this->lang->line('common_are_you_sure');?>",/* Are you sure? */
            text: "<?php echo $this->lang->line('procuement_you_want_to_delete_this_file');?>",/* You want to delete this file ! */
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/* Delete */
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>",
            closeOnConfirm: true },
            function(){
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {id:id},
                    url: "<?php echo site_url('ItemMaster/delete_item_pricing'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if(data){
                            fetch_address();
                            //fetchPage('system/srp_address_view','Test','Address');
                        }
                    }, 
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.'); /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
    }

    function change_cost_request(){
        swal({   title: "<?php echo $this->lang->line('common_are_you_sure');?>",/* Are you sure? */
            text: "You want to change this cost",/* You want to  ! */
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",/* confirm */
            cancelButtonText: "No",
            closeOnConfirm: true },
            function(){
             
               var edit= $('#itemPriceedit').val();

               if(edit!=''){
                    $('#discount').val(0);
                    $('#margin').val(0).change();
                    //$('#salesprice').val('');
                    //$('#salesprice').attr('disabled',true);
                    //$('#discount').val('');
                    //$('#rsalesprice').val('');
                    //$('#rsalesprice').attr('disabled',true);
                   // $('#profit').val('');
               }

               $('#cost').removeAttr("readonly");
              
            });
    }
</script>