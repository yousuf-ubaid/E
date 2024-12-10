<?php
$primaryLanguage = getPrimaryLanguage();
//$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = 'Item Received History';
echo head_page($title, false);
$date_format_policy = date_format_policy();
//$supplier_arr = all_supplier_drop(false);
//$po_drop = all_po_drop();
$items=fetch_item_dropdown(false);
$supplier =all_supplier_drop(false,1);
$supplier[0] = ('') . 'Sundry' . ('');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
        <div class="box-tools pull-right">
            <button id="" onclick="openColumnSelection()" class="btn btn-box-tool " ><i class="fa fa-plus"></i></button>
        </div>
        <?php echo form_open('', ' name="item_recieved_history_filter_frm" id="item_recieved_history_filter_frm" class="form-group" role="form"'); ?>
        <div class="col-md-12">
            <div class="form-group col-sm-2">
                <label>Currency </label>
                <select name="currency" class="form-control" id="currency"  onchange="get_item_recieved_tableView()">
                    <option value="Local">Local Currency</option>
                    <option value="Reporting">Reporting Currency</option>
                </select>
            </div>
            <div class="form-group col-sm-3">
                <label for="">Document Types</label>

                <?php echo form_dropdown('documentID[]', array('GRV'=>'GRV | Goods Received Voucher','PV'=>'PV | payment Voucher','BSI'=>'BSI | Supplier Invoice'), '', 'class="form-control" id="documentID" multiple="multiple"'); ?>
            </div>
            <div class="form-group col-sm-2 ">
                    <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
                    <?php echo form_dropdown('status_filter', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter" '); ?>
            </div>
            <div class="form-group col-sm-3">
                <label>Supplier </label>
                <?php //echo form_dropdown('supplier[]', $supplier, 'Each', 'class="form-control" multiple id="supplier" '); ?>
                <div id="div_load_supplier">
                    <select name="supplier[]" class="form-control" id="supplier" multiple="">
                        <?php
                            if (!empty($supplier)) {
                                foreach ($supplier as $key => $val) {
                                    echo '<option value="' . $key . '">' . $val . '</option>';
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-2">
                <label for="status_filter_item"><?php echo $this->lang->line('common_item_status');?></label>
                <?php echo form_dropdown('status_filter_item', array('1'=>'Active','2'=>'Inactive','3'=>'All'), '', '  class="form-control" id="status_filter_item" '); ?>
            </div>
            <div class="form-group col-sm-3">
                <label>Items </label><br>
                <?php //echo form_dropdown('items[]', $items, 'Each', 'class="form-control" multiple id="items" '); ?>
                <div id="div_load_item">
                    <select name="items[]" class="form-control" id="items" multiple="multiple">
                        <?php
                            if (!empty($items)) {
                                foreach ($items as $key => $val) {
                                    echo '<option value="' . $key . '">' . $val . '</option>';
                                }
                            }
                        ?>
                    </select>
                </div>            
            </div>
            <div class="form-group col-sm-2 hide" id="columSelectionDiv">
                <label for="">Extra Columns</label>
                <?php echo form_dropdown('columSelectionDrop[]', array('barcode'=>'Barcode','partNo'=>'Part No'), '', 'class="form-control" onchange="get_item_recieved_tableView()" id="columSelectionDrop" multiple="multiple"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for=""></label>
                <button style="margin-top:28px " type="button" onclick="get_item_recieved_tableView()" class="btn btn-primary btn-xs">
                    Generate
                </button>
            </div>

        </div>

        <?php echo form_close(); ?>
    </fieldset>
</div>


    <div id="Load_item_received_history_table" style=""></div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">

    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $(document).ready(function () {
        $('#columSelectionDiv').addClass('hide');
        $('#columSelection').val();

        $("#items").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#items").multiselect2('selectAll', false);
        $("#items").multiselect2('updateButtonText');

        $("#supplier").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#supplier").multiselect2('selectAll', false);
        $("#supplier").multiselect2('updateButtonText');

        $("#documentID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#documentID").multiselect2('selectAll', false);
        $("#documentID").multiselect2('updateButtonText');

        $("#status_filter").change(function () {
            load_statusbase_supplier()
        });
        $("#status_filter_item").change(function () {
            load_statusbase_item()
        });
        $("#columSelectionDrop").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/inventory/report/received_history', '', 'Item Received History');
        });
        get_item_recieved_tableView();
    });


    function get_item_recieved_tableView()
    {
        var data = $("#item_recieved_history_filter_frm").serialize();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/load_item_received_history'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#Load_item_received_history_table').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function openColumnSelection(){
        if ($('#columSelectionDiv').hasClass('hide')) {
            $('#columSelectionDiv').removeClass('hide');
        }else{
            $('#columSelectionDiv').addClass('hide');
        }
    }
    $("#columSelectionDrop").change(function () {
        if ((this.value)) {
            get_item_recieved_tableView(this.value);
            return false;
        }
    });

    function load_statusbase_supplier() {
        var status_filter = $('#status_filter').val();
        if (status_filter) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {activeStatus: status_filter},
                url: "<?php echo site_url('Inventory/fetch_supplier'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_supplier').html(data);

                    $('#supplier').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        //enableFiltering: true
                        buttonWidth: '220px',
                        maxHeight: '30px',
                        numberDisplayed: 1
                    });
                    $("#supplier").multiselect2('selectAll', false);
                    $("#supplier").multiselect2('updateButtonText');
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        } 

    }

    function load_statusbase_item() {
        var status_filter_item = $('#status_filter_item').val();
        if (status_filter_item) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {activeStatus: status_filter_item},
                url: "<?php echo site_url('Inventory/fetch_statusbase_item'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_item').html(data);

                    $("#items").multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        buttonWidth: '180px',
                        maxHeight: '30px'
                    });
                    $("#items").multiselect2('selectAll', false);
                    $("#items").multiselect2('updateButtonText');
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        } 

    }
</script>