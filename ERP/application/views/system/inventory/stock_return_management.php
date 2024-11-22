<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('transaction_purchase_return');
echo head_page($title, true);


/*echo head_page('Purchase Return', true);*/
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
?>

<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <div class="custom_padding">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?> </label><br><!--Date-->
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from');?> </label><!--From-->
                <input type="text" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateFrom"
                       class="input-small">
                <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('common_to');?> <!--To-->&nbsp&nbsp</label>
                <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateTo"
                       class="input-small">

            </div>

        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_supplier_name');?> </label><br><!--Supplier Name-->
            <?php echo form_dropdown('supplierPrimaryCode[]', $supplier_arr, '', 'class="form-control" id="supplierPrimaryCode" onchange="Otable.draw()" multiple="multiple" style="height: 30px"'); ?>
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status');?> </label><br><!--Status-->

            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' => $this->lang->line('common_all')/*'All'*/, '1' =>$this->lang->line('common_draft') /*'Draft'*/, '2' => $this->lang->line('common_confirmed')/*'Confirmed'*/, '3' => $this->lang->line('common_approved')/*'Approved'*/,'4'=>'Refer-back'), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?> <!--Clear-->
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?> / <?php echo $this->lang->line('common_approved');?>

                </td><!--Confirmed--><!--Approved-->
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?>
                    / <?php echo $this->lang->line('common_not_approved');?>
                </td><!--Not Confirmed--><!--Not Approved-->
                <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('transaction_common_refer_back');?>
                </td><!--Refer-back-->
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right"
                onclick="fetchPage('system/inventory/erp_stock_return',null,'<?php echo $this->lang->line('transaction_add_new_purchase_return')?>','GRV');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('transaction_create_purchase_return');?>
        </button><!--Create Purchase Return-->
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="stock_return_table2" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('transaction_sr_transfer');?> </th><!--SR Code-->
            <th><?php echo $this->lang->line('common_supplier_name');?> </th><!--Supplier Name-->
            <th style="min-width: 15%"><?php echo $this->lang->line('common_warehouse');?> </th><!--Warehouse-->
            <th style="min-width: 15%"><?php echo $this->lang->line('common_reference_no');?></th><!--Reference No-->
            <!--<th style="min-width: 10%">Currency</th>-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_date');?> </th><!--Date-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?> </th><!--Confirmed-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?> </th><!--Approved-->
            <th style="min-width: 6%"><?php echo $this->lang->line('common_action');?>  </th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var grvAutoID;
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/inventory/stock_return_management', 'Test', 'Purchase Return');
        });
        grvAutoID = null;
        number_validation();
        stock_return_table();

        $('#supplierPrimaryCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));
    });

    function stock_return_table(selectedID=null) {

         Otable = $('#stock_return_table2').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Inventory/fetch_stock_return_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['stockReturnAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "stockReturnAutoID"},
                {"mData": "stockReturnCode"},
                {"mData": "supplierName"},
                {"mData": "sr_detail"},
                {"mData": "referenceNo"},
                /*{"mData": "transactionCurrency"},*/
                {"mData": "returnDate"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "wareHouseLocation"}
                //{"mData": "edit"},
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "columnDefs": [{"targets": [8], "orderable": false},{"visible":false,"searchable": true,"targets": [9] },{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "supplierPrimaryCode", "value": $("#supplierPrimaryCode").val()});
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

    function delete_item(id, value) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'stockReturnID': id},
                    url: "<?php echo site_url('Inventory/delete_purchase_return'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        Otable.draw();
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                        stopLoad();
                    }
                });
            });
    }

    // function show_addon(id){
    //     //$('#addon_form')[0].reset();
    //     //$('#addon_form').bootstrapValidator('resetForm', true);
    //     grvAutoID = id;
    //     fetch_addons(id);
    // }

    // function fetch_addons(id){

    // }

    // function addon_form_reset(){
    //     $('#description').val('');
    //     $('#uom').val('Each');
    //     $('#qty').val(0);
    //     $('#supplier').val('');
    //     $('#unit_cost').val(0);
    //     $('#sub_total').html(0.00);
    // }

    // $(".number").keyup(function(){
    //     var qty         = parseFloat($('#qty').val());
    //     var unit_cost   = parseFloat($('#unit_cost').val());
    //     $('#sub_total').html(parseFloat(qty*unit_cost).toFixed(2));
    // });

    function referback_stock_return(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'stockReturnAutoID': id},
                    url: "<?php echo site_url('Inventory/referback_stock_return'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters(){
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#status').val("all");
        $('#supplierPrimaryCode').multiselect2('deselectAll', false);
        $('#supplierPrimaryCode').multiselect2('updateButtonText');
        Otable.draw();
    }

    function reOpen_contract(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_re_open');?>",/*You want to re open!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'stockReturnAutoID':id},
                    url :"<?php echo site_url('Inventory/re_open_stock_return'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        Otable.draw();
                        stopLoad();
                        refreshNotifications(true);
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>