<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('procurement_po_purchase_order_tracking');
echo head_page($title, false);
$date_format_policy = date_format_policy();
$supplier_arr = all_supplier_drop(false,1);
$po_drop = all_po_drop();
if($this->session->userdata("companyType") == 1){
     $segment_arr = fetch_segment(true,false);
}else{
    $segment_arr = fetch_group_segment(true,false);
}
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<form id="PO_tracking_filter_frm">
<div class="row">
<div class="form-group col-sm-3">
            <label for=""><?php echo $this->lang->line('common_date_from'); ?><!-- From Date --></label>
            <br>
           
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="dateFrom"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="dateFrom" class="form-control"
                       value="">
            </div>
          
      
</div>
<div class="form-group col-sm-3">
            <label for=""><?php echo $this->lang->line('common_date_to'); ?><!-- To Date --></label>
            <br>
            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="dateTo"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="dateTo" class="form-control"
                       value="">
            </div>
        </div>
        <div class="form-group col-sm-2 ">
            <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
            <?php echo form_dropdown('status_filter', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter" '); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="">   <?php echo $this->lang->line('common_supplier_name'); ?><!--Supplier Name--></label>
            <br>
            <?php //echo form_dropdown('supplierAutoID[]', $supplier_arr, '', 'class="form-control" id="supplierAutoID" onchange="startMasterSearch()" multiple="multiple"'); ?>
            <div id="div_load_supplier">
                <select name="supplierAutoID[]" class="form-control" id="supplierAutoID" multiple="">
                    <?php
                        if (!empty($supplier_arr)) {
                            foreach ($supplier_arr as $key => $val) {
                                echo '<option value="' . $key . '">' . $val . '</option>';
                            }
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-group col-sm-3">
        <label class="col-md-4 control-label text-left"
                       for="employeeID"> PO <?php echo $this->lang->line('common_code'); ?> <!--PO Code--></label><br>
            <div id="div_load_poautoID">
                    <select name="poautoID" class="form-control" id="poautoID" multiple="multiple"></select>
                </div>
        </div>

</div>
<div class="row">


<div class="form-group col-sm-3">
<label for="supplierPrimaryCode"><?php echo $this->lang->line('common_segment'); ?> <!-- Segment --></label><br>
<?php echo form_dropdown('SegmentAutoID[]', $segment_arr, '', 'class="form-control" id="SegmentAutoID" onchange="startMasterSearch()" multiple="multiple"'); ?>
        </div>

        <div class="col-sm-1">
            <label>&nbsp;</label>
            <div class="hide" id="search_cancel">
                <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
            </div>
        </div>
    </div>



</form>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div id="Load_PO_tracking_table" style="margin: 10px"></div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">

    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/procurement/erp_purchase_order_tracking_report', '', 'Purchase Order Tracking Details');
        });

        get_PO_tracking_tableView();
        load_supplierbase_pocode();
        $('#supplierAutoID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '220px',
            maxHeight: '30px'
        });


        $('#poautoID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '220px',
            maxHeight: '30px'
        });

        $('#SegmentAutoID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '220px',
            maxHeight: '30px'
        });
       
   
    });
        

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (e) {
        startMasterSearch();
    });

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');

        get_PO_tracking_tableView();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('#dateFrom').val('');
        $('#dateTo').val('');

        $('#supplierAutoID').multiselect2('deselectAll', false);
        $('#supplierAutoID').multiselect2('updateButtonText');
        $('#poautoID').multiselect2('deselectAll', false);
        $('#poautoID').multiselect2('updateButtonText');
        
        $('#SegmentAutoID').multiselect2('deselectAll', false);
        $('#SegmentAutoID').multiselect2('updateButtonText');

        get_PO_tracking_tableView();

    }

    function get_PO_tracking_tableView() {
        var data = $("#PO_tracking_filter_frm").serialize();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Procurement/load_purchase_order_tracking'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#Load_PO_tracking_table').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $("#supplierAutoID").change(function () {
        load_supplierbase_pocode()


    });

    $("#status_filter").change(function () {
        load_statusbase_supplier()
    });
    function load_supplierbase_pocode() {
        var suppierID = $('#supplierAutoID').val();
        if (suppierID) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {suppierID: suppierID},
                url: "<?php echo site_url('Procurement/fetch_pocode'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_poautoID').html(data);
                    $('#poautoID').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        //enableFiltering: true
                        buttonWidth: '220px',
                        maxHeight: '30px',
                        numberDisplayed: 1
                    });
                    $("#poautoID").multiselect2('selectAll', false);
                    $("#poautoID").multiselect2('updateButtonText');
                    //$('#province').val(province).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        } else {
            $('#div_load_poautoID').html('   <div id="div_load_poautoID">\n' +
                '                        <select name="poautoID" ' +
                'class="form-control" id="poautoID" multiple="multiple"></select>\n' +
                '                    </div>');
            $('#poautoID').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                buttonWidth: '220px',
                maxHeight: '30px',
                numberDisplayed: 1
            });
            $("#poautoID").multiselect2('selectAll', false);
            $("#poautoID").multiselect2('updateButtonText');

        }

    }

    function excel_Export_PO_tracking() {
        var form = document.getElementById('PO_tracking_filter_frm');
        form.target = '_blank';
        form.action = '<?php echo site_url('Procurement/export_purchase_order_tracking_excel'); ?>';
        form.submit();
    }

    function load_statusbase_supplier() {
        var status_filter = $('#status_filter').val();
        if (status_filter) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {activeStatus: status_filter},
                url: "<?php echo site_url('Procurement/fetch_supplier'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_supplier').html(data);

                    $('#supplierAutoID').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        //enableFiltering: true
                        buttonWidth: '220px',
                        maxHeight: '30px',
                        numberDisplayed: 1
                    });
                    $("#supplierAutoID").multiselect2('selectAll', false);
                    $("#supplierAutoID").multiselect2('updateButtonText');
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        } 

    }

    
</script>