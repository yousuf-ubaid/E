<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_maraketing_masters_customer_price_setup');
echo head_page($title, false);
?>


    <div id="filter-panel" class="collapse filter-panel">
    </div>
    <div class="row" style="margin: 1%">
        <ul class="nav nav-tabs mainpanel">
            <li class="active">
                <a class="buybackTab" onclick="" id="" data-id="0" href="#step1" data-toggle="tab" aria-expanded="true"><span><i class="fa fa-cog tachometerColor" aria-hidden="true" style="color: #50749f;font-size: 16px;"></i>&nbsp;<?php echo $this->lang->line('sales_maraketing_masters_price_setup');?> <!--Price Setup--></span></a>
            </li>
            <li class="">
                <a class="buybackTab" onclick="CustomerPriceListTableView()" id="" data-id="0" href="#step2" data-toggle="tab" aria-expanded="true"><span><i class="fa fa-list tachometerColor" aria-hidden="true" style="color: #50749f;font-size: 16px;"></i>&nbsp;<?php echo $this->lang->line('sales_maraketing_masters_customer_price_list');?> <!--Customer Price List--></span></a>
            </li>
        </ul>
    </div>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">

            <div class="row addTableView">
                <div class="col-md-12 animated zoomIn">
                    <div class="row" style="margin-top: 10px;">
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
                        <div class="col-md-4 text-center">
                            &nbsp;
                        </div>
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-primary pull-right"
                                    onclick="fetchPage('system/customer/erp_new_Customer_priceSetup_new',null,'<?php echo $this->lang->line('sales_maraketing_masters_add_customer_price_setup');?>','CPS');"><!--Add Customer Price Setup--><i
                                    class="fa fa-plus"></i><?php echo $this->lang->line('sales_maraketing_masters_creaet_new_price_setup');?> <!-- Create New Price Setup-->
                            </button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                        <div class="table-responsive">
                            <table id="customer_PriceSetup_tbl" class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 12%"><?php echo $this->lang->line('common_document_code');?> <!--Document Code--></th>
                                    <th style="width: 10%"><?php echo $this->lang->line('common_document_date');?> <!--Document Date--></th>
                                    <th style="width: 12%"><?php echo $this->lang->line('common_narration');?> <!--Narration--></th>
                                    <th style="width: 10%"><?php echo $this->lang->line('common_confirmed');?> <!--Confirmed--></th>
                                    <th style="width: 10%"><?php echo $this->lang->line('common_approved');?> <!--Approved--></th>
                                    <th style="width: 10%"><?php echo $this->lang->line('common_action');?> <!--Action--></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <br>
        </div>
        <div id="step2" class="tab-pane">
            <div class="row" style="margin: 20px;">
                <div class="table-responsive">
                    <table id="customerWise_PriceList_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 80%"><?php echo $this->lang->line('sales_maraketing_masters_customer_code');?> <!--Customer Code--></th>
                            <th style="width: 5%"><?php echo $this->lang->line('common_action');?> <!--Action--></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!--modal view-->
    <div class="modal fade" id="CustomerPriceSetup_View_modal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" style="width: 90%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_maraketing_masters_customer_sales_price_setup');?> <!--Customer Sales Prices Setup--></h4>
                </div>
                <div class="modal-body">
                    <div id="CustomerPriceSetup_View"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_close');?> <!--Close--></button>
                </div>
            </div>
        </div>
    </div>
    <!-- model fade For viewing customer wise item price -->
    <div class="modal fade" id="CusSalesPriceList_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
        <div class="modal-dialog" role="document" style="width: 90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="SalesPriceViewTitle"><?php echo $this->lang->line('sales_maraketing_masters_sales_prices');?> <!--Sales Prices--></h4>
                </div>
                <div class="modal-body">
                    <?php echo form_open('', 'role="form" id="CustomerSalesPriceView_form"'); ?>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-sm-12">
                            <div id="CusSalesPriceList_view"></div>
                        </div>
                    </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_close');?> <!--Close--></button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/customer/customer_price_setup_new', 'Customer Price Setup','CUS');
            });
            CustomerPriceSetupTable();
        });

        function CustomerPriceSetupTable() {
            Otable = $('#customer_PriceSetup_tbl').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Customer/fetch_customerPriceSetup_new'); ?>",
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
                    {"mData": "cpsAutoID"},
                    {"mData": "documentSystemCode"},
                    {"mData": "documentDate"},
                    {"mData": "narration"},
                    {"mData": "confirmed"},
                    {"mData": "approved"},
                    {"mData": "edit"}
                ],

                "fnServerData": function (sSource, aoData, fnCallback) {
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

        function CustomerPriceListTableView() {
            Otable = $('#customerWise_PriceList_table').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "StateSave": true,
                "sAjaxSource": "<?php echo site_url('Customer/fetch_customerWisePrice_new'); ?>",
                "aaSorting": [[1, 'asc']],
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
                    {"mData": "edit"}
                ],
                "columnDefs": [{"targets": [0], "searchable": false}],
                "fnServerData": function (sSource, aoData, fnCallback) {
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

        function delete_Customer_priceSetup(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure')?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete')?>",/*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete')?>"/*Delete*/
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'cpsAutoID': id},
                        url: "<?php echo site_url('Customer/delete_customerSalesPrice_document'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            CustomerPriceSetupTable();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function referback_Customer_priceSetup(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure')?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_refer_back')?>",/*You want to refer back!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes')?>"/*Yes*/
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'cpsAutoID': id},
                        url: "<?php echo site_url('Customer/referback_Customer_priceSetup'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                CustomerPriceSetupTable();
                            }
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function Customer_priceSetup_DocumentView_new(cpsAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'cpsAutoID': cpsAutoID, 'html': true},
                url: "<?php echo site_url('Customer/load_Customer_PriceConfirmation_new'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#CustomerPriceSetup_View').html(data);
                    $("#CustomerPriceSetup_View_modal").modal({backdrop: "static"});

                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again')?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }

        function attach_CustomerWisePrice_modal(id) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Customer/Fetch_ItemSalesPriceDetails"); ?>',
                dataType: 'html',
                data: {'customerAutoID': id, 'view' : 1},
                async: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#CusSalesPriceList_view').html(data);
                    $("#CusSalesPriceList_model").modal({backdrop: "static"});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ItemPriceTable_view').html(xhr.responseText);

                }
            });
        }

    </script>


