<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('transaction_stock_adjustment');
echo head_page($title, true);


/*echo head_page('Stock Adjustment', true);*/
$location_arr = all_delivery_location_drop(false);
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
                <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('common_to');?> &nbsp&nbsp</label><!--To-->
                <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateTo"
                       class="input-small">

            </div>

        </div>
        <div class="form-group col-sm-4">
            <label><?php echo $this->lang->line('common_Location');?> </label><br><!--Location-->
            <?php echo form_dropdown('location[]', $location_arr, '', 'class="form-control" id="location" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-4">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status');?> </label><br><!--Status-->

            <div style="width: 60%;">
                <?php echo form_dropdown('status', array('all' =>$this->lang->line('common_all') /*'All'*/, '1' => $this->lang->line('common_draft')/*'Draft'*/, '2' => $this->lang->line('common_confirmed')/*'Confirmed'*/, '3' =>$this->lang->line('common_approved') /*'Approved'*/,'4'=>'Refer-back'), '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="clear_all_filters()" style="margin-top: -10%;"><i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?>
            </button><!--Clear-->
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
                onclick="fetchPage('system/inventory/erp_stock_adjustment',null,'<?php echo $this->lang->line('transaction_add_new_stock_adjustment');?>','SA');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('transaction_create_stock_adjustment');?>
        </button><!--Create Stock Adjustment--><!--Add New Stock Adjustment-->
</div>
</div>
<hr>
<div class="table-responsive">
    <table id="stock_adjustment_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('transaction_stock_adjustment_code');?></th><!--SA Code-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_date');?> </th><!--Date-->
            <th style="min-width: 25%"><?php echo $this->lang->line('common_Location');?> </th><!--Location-->
            <th style="min-width: 20%"><?php echo $this->lang->line('common_comments');?></th><!--Comments-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?> </th><!--Confirmed-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?> </th><!--Approved-->
            <th style="min-width: 6%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/inventory/stock_agistment_management', 'Test', 'Stock Adjustment');
        });
        stock_adjustment_table();

        $('#location').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));
    });

    function stock_adjustment_table(selectedID=null) {
         Otable = $('#stock_adjustment_table').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Inventory/fetch_stock_adjustment_table'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['stockAdjustmentAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "stockAdjustmentAutoID"},
                {"mData": "stockAdjustmentCode"},
                {"mData": "stockAdjustmentDate"},
                {"mData": "st_detail"},
                {"mData": "details"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "wareHouseCode"},
                {"mData": "wareHouseLocation"},
                {"mData": "wareHouseDescription"},
                {"mData": "comment"},
                {"mData": "referenceNo"}
                //{"mData": "edit"},
            ],
            "columnDefs": [{"targets": [7], "orderable": false},{"visible":false,"searchable": true,"targets": [8,9,10,11,12] }, {"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "location", "value": $("#location").val()});
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
                    data: {'stock_auto_id': id},
                    url: "<?php echo site_url('Inventory/delete_stock_adjustment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        Otable.draw();
                        stopLoad();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function referback_stock_adjustment(id) {
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
                    data: {'stockAdjustmentAutoID': id},
                    url: "<?php echo site_url('Inventory/referback_stock_adjustment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        Otable.draw();
                        stopLoad();

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters() {
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#status').val("all");
        $('#location').multiselect2('deselectAll', false);
        $('#location').multiselect2('updateButtonText');
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
                    data : {'stockAdjustmentAutoID':id},
                    url :"<?php echo site_url('Inventory/re_open_stock_adjestment'); ?>",
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