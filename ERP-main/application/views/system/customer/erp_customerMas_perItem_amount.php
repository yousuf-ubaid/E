<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_maraketing_masters_customer_master');
echo head_page($title, true);

/*echo head_page('Customer Master',true);*/
$customer_arr =all_customer_drop(false);
$customerCategory    = party_category(1, false);
$currncy_arr    = all_currency_new_drop(false);
$main_category_arr = all_main_category_drop();
$usergroupcompanywiseallow = getPolicyValuesgroup('CM','All');

?>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_customer_name');?> </label><br><!--Customer Name-->
            <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="Otable.draw(), ODeltable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_category');?> </label><br><!--Category-->
            <?php echo form_dropdown('category[]', $customerCategory, '', 'class="form-control" id="category" onchange="Otable.draw(), ODeltable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_currency');?> </label><br><!--Currency-->
            <?php echo form_dropdown('currency[]', $currncy_arr, '', 'class="form-control" id="currency" onchange="Otable.draw(), ODeltable.draw()" multiple="multiple"'); ?>
        </div>

        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">&nbsp;</label><br>

            <button type="button" class="btn btn-sm btn-primary pull-right"
                    onclick="clear_all_filters()" style=""><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?>
            </button><!--Clear-->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td>
                    <span class="glyphicon glyphicon-stop" style="color:green; font-size:15px;"></span> <?php echo $this->lang->line('common_active');?>
                </td><!--Active-->
                <td>
                    <span class="glyphicon glyphicon-stop" style="color:red; font-size:15px;"></span><?php echo $this->lang->line('common_in_active');?>
                </td><!--Inactive-->
            </tr>
        </table>
    </div>
    <?php if($usergroupcompanywiseallow == 0){?>
        <button type="button" class="btn btn-primary pull-right" onclick="createcustomer()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('sales_maraketing_masters_create_customer');?>  </button>
    <?php } else if ($usergroupcompanywiseallow != 0) { ?>
        <div class="col-md-7 text-right">
            <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/customer/erp_Customer_master_buyback',null,'Add New Customer','CUS');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('sales_maraketing_masters_create_customer');?>  </button><!--Create Customer-->
        </div>
    <?php }?>

</div><hr>


<div class="row" style="padding-left: 2%">
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#customers" data-toggle="tab" onclick="Otable.draw()"><?php echo $this->lang->line('sales_maraketing_masters_customer');?></a></li>
        <li><a href="#deleted" data-toggle="tab" onclick="ODeltable.draw()"><?php echo $this->lang->line('sales_maraketing_masters_deleted_customer');?></a></li>
    </ul>
</div>
<br>
<div class="tab-content">
    <div class="tab-pane active" id="customers">
        <div class="table-responsive">
            <table id="customer_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 3%">#</th>
                    <th style="min-width: 12%"><?php echo $this->lang->line('sales_maraketing_masters_customer_code');?></th><!--Customer Code-->
                    <th style="min-width: 40%"><?php echo $this->lang->line('sales_maraketing_masters_customer_details');?> </th><!--Customer Details-->
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_category');?></th><!--Category-->
                    <th style="min-width: 15%"><?php echo $this->lang->line('sales_maraketing_masters_customer_balance');?></th><!--Balance-->
                    <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?></th><!--Status-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
                </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="tab-pane" id="deleted">
        <div class="table-responsive">
            <table id="customer_deleted_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 3%">#</th>
                    <th style="min-width: 12%"><?php echo $this->lang->line('sales_maraketing_masters_customer_code');?></th><!--Customer Code-->
                    <th style="min-width: 40%"><?php echo $this->lang->line('sales_maraketing_masters_customer_details');?> </th><!--Customer Details-->
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_category');?></th><!--Category-->
                    <th style="min-width: 15%"><?php echo $this->lang->line('sales_maraketing_masters_customer_balance');?></th><!--Balance-->
                    <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?></th><!--Status-->
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- model fade For viewing customer wise item price -->
<div class="modal fade" id="SalesPriceView_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <div class="modal-dialog" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="SalesPriceViewTitle">Sales Prices</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="CustomerSalesPriceView_form"'); ?>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12">
                        <div id="ItemSalesPriceTable_view"></div>
                    </div>
                </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
    var Otable;
    var ODeltable;
    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/customer/erp_customerMas_perItem_amount','','Customer Master');
        });
        $('#customerCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('#category').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('#currency').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        customer_table();
        customer_deleted_table();
    });

    function customer_table(){
        Otable = $('#customer_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Customer/fetch_customer_buyback_item'); ?>",
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
                {"mData": "customerAutoID"},
                {"mData": "customerSystemCode"},
                {"mData": "customer_detail"},
                {"mData": "categoryDescription"},
                {"mData": "amt"},
                {"mData": "confirmed"},
                {"mData": "edit"},
                {"mData": "customerName"},
                {"mData": "customerAddress1"},
                {"mData": "customerAddress2"},
                {"mData": "customerCountry"},
                {"mData": "secondaryCode"},
                {"mData": "customerCurrency"},
                {"mData": "customerEmail"},
                {"mData": "customerTelephone"},
                {"mData": "Amount_search"}
            ],
            "columnDefs": [{"targets": [6], "orderable": false},{"visible":false,"searchable": true,"targets": [7,8,9,10,11,12,13,14,15] }, {"searchable": false, "targets": [0,2,4,5,6]}],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                aoData.push({"name": "customerCode", "value": $("#customerCode").val()});
                aoData.push({"name": "category", "value": $("#category").val()});
                aoData.push({"name": "currency", "value": $("#currency").val()});
                aoData.push({"name": "deletedYN", "value": 0});
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

    function customer_deleted_table(){
        ODeltable = $('#customer_deleted_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Customer/fetch_customer_buyback_item'); ?>",
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
                {"mData": "customerAutoID"},
                {"mData": "customerSystemCode"},
                {"mData": "customer_detail"},
                {"mData": "categoryDescription"},
                {"mData": "amt"},
                {"mData": "confirmed"},
                {"mData": "edit"},
                {"mData": "customerName"},
                {"mData": "customerAddress1"},
                {"mData": "customerAddress2"},
                {"mData": "customerCountry"},
                {"mData": "secondaryCode"},
                {"mData": "customerCurrency"},
                {"mData": "customerEmail"},
                {"mData": "customerTelephone"},
                {"mData": "Amount"}
            ],
            "columnDefs": [{"targets": [5], "orderable": false},{"visible":false,"searchable": true,"targets": [6,7,8,9,10,11,12,13,14,15] }],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                aoData.push({"name": "customerCode", "value": $("#customerCode").val()});
                aoData.push({"name": "category", "value": $("#category").val()});
                aoData.push({"name": "currency", "value": $("#currency").val()});
                aoData.push({"name": "deletedYN", "value": 1});
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

    function delete_customer(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('sales_maraketing_masters_you_want_to_delete_this_customer');?>",/*You want to delete this customer!*/
                type: "warning",
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
                    data : {'customerAutoID':id},
                    url :"<?php echo site_url('Customer/delete_customer'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        refreshNotifications(true);
                        Otable.draw();
                        ODeltable.draw();
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters() {
        $('#customerCode').multiselect2('deselectAll', false);
        $('#customerCode').multiselect2('updateButtonText');
        $('#category').multiselect2('deselectAll', false);
        $('#category').multiselect2('updateButtonText');
        $('#currency').multiselect2('deselectAll', false);
        $('#currency').multiselect2('updateButtonText');
        Otable.draw();
        ODeltable.draw();
    }

    function attach_salePrice_modal(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Customer/Fetch_ItemSalesPriceDetails"); ?>',
            dataType: 'html',
            data: {'customerAutoID': id, 'view': 0},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#ItemSalesPriceTable_view').html(data);
                $("#SalesPriceView_model").modal({backdrop: "static"});
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#ItemPriceTable_view').html(xhr.responseText);

            }
        });
    }
    function createcustomer() {
        swal(" ", "You do not have permission to create  customer master at company level,please contact your system administrator.", "error");
    }

</script>