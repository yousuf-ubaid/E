<?php
$this->load->helper('warehouse_helper');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('erp_warehouse_master');
echo head_page($title, false);
$glcode_Manufacturing_arr = all_imanufacturing_glcode();
$main_category_arr = all_main_category_drop();
$flowserveLanguagePolicy = getPolicyValues('LNG', 'All');
/*echo head_page('Warehouse Master', false); */ ?>
<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<!-- <div class="row">
    <div class="col-md-9"></div>
    <div class="col-md-3">
        <button type="button" onclick="resetform()"  class="btn btn-xs btn-primary pull-right" data-toggle="modal"
                data-target="#warehousemaster_model"><span
                class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create
            New
        </button>
    </div>
</div> -->
<div class="row">
    <!-- <div class="col-md-5">
        <table class="<?php //echo table_class(); ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> Confirmed /
                        Approved
                    </td>
                    <td><span class="label label-danger">&nbsp;</span> Not Confirmed
                        / Not Approved
                    </td>
                    <td><span class="label label-warning">&nbsp;</span> Refer-back
                    </td>
                </tr>
            </table>
    </div> -->
    <div class="col-md-9 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" onclick="open_warehouse_model()" class="btn btn-primary pull-right"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new'); ?><!--Create New--> </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="warehousemaster_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 20%"><?php echo $this->lang->line('erp_warehouse_master_warehouse_code'); ?> </th>
            <!--Warehouse Code-->
            <th style="min-width: 30%"><?php echo $this->lang->line('common_description'); ?></th><!--Description-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_Location'); ?> </th><!--Location-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_status'); ?> </th><!--Status-->
            <th style="min-width: 10%"><?php echo $this->lang->line('erp_warehouse_master_is_default'); ?> </th>
            <!--Is Default-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action'); ?></th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="warehousemaster_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" id="WarehouseHead"></h3>
            </div>
            <form role="form" id="warehousemaster_form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="warehouseredit" name="warehouseredit">
                    <input type="hidden" value="" id="is_pos_shifted" name="is_pos_shifted">
                    <input type="hidden" value="" id="is_mfqWarehouse_active" name="is_mfqWarehouse_active">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_code'); ?>
                                <!--Code--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="warehousecode" name="warehousecode">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description'); ?>
                                <!--Description--></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" rows="2" id="warehousedescription"
                                          name="warehousedescription"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_Location'); ?>
                                <!--Location--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="warehouselocation" name="warehouselocation">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                                <?php echo $this->lang->line('common_address'); ?><!--Address--></label>
                            <div class="col-sm-6">
                                <textarea rows="2" class="form-control" id="warehouseAddress"
                                          name="warehouseAddress"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_telephone'); ?>
                                <!--Telephone--></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="warehouseTel" name="warehouseTel">
                            </div>
                        </div>
                    </div>
                    <div class="row hide">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">
                                <?php echo $this->lang->line('erp_warehouse_pos_location'); ?><!--Pos Location--></label>
                            <div class="col-sm-6" style="">
                                <input type="checkbox" value="1" id="isPosLocation" name="isPosLocation">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- <div class="form-group">
                            <label class="col-sm-4 control-label">Is Manufacturing</label>
                            <div class="col-sm-6" style="top: 5px;">
                                <input type="checkbox" value="" id="ismanufacturing" name="ismanufacturing">
                                <input type="hidden" value="" id="ismanufacturingHN" name="ismanufacturingHN">
                            </div>
                        </div> -->

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Warehouse Type</label>
                            <div class="col-sm-6" style="">
                                <?php
                                if($flowserveLanguagePolicy == 'FlowServe'){
                                    echo form_dropdown('wareHouseType', array(''=>'Select Operation / Service Type','2'=>'Operation / Service','3'=>'Operational','4'=>'Consignment'), '', 'class="form-control select2" id="wareHouseType"'); 
                                }else{
                                    echo form_dropdown('wareHouseType', array(''=>'Select Warehouse Type','2'=>'Manufacturing','3'=>'Operational','4'=>'Consignment'), '', 'class="form-control select2" id="wareHouseType"'); 
                                } ?>
                               
                            </div>

                        </div>
                    </div>
                    <div class="row hide" id="manufacturingglcodeROW">

                        <div class="form-group">
                            <label class="col-sm-4 control-label">WIP GL Code</label>
                            <div class="col-sm-6" style="">
                                <?php echo form_dropdown('glcodeid', $glcode_Manufacturing_arr, 'Each', 'class="form-control" id="manufacturingglcode"'); ?>
                            </div>

                        </div>
                    </div>
                    <div class="row warehouseDeativate">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Is Active</label>
                            <div class="col-sm-6" style="top: 5px;">
                                <input type="checkbox" value="" id="isActive_check" name="isActive_check">
                                <input type="hidden" value="" id="isActive" name="isActive">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>




<div aria-hidden="true" role="dialog" id="bin_location_model" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Bin Location</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="wareHouseAutoIDBin" name="wareHouseAutoIDBin">
                    <input type="hidden" id="binLocationID" name="binLocationID">
                    <div class="form-group col-sm-4">
                        <label for="">Description</label>
                        <input type="text" name="Description" id="Description"  class="form-control">
                    </div>
                    <div class="form-group col-sm-4">
                        <button id="binsavebtn" onclick="save_bin_location()" style="margin-top: 26px;" class="btn btn-primary"></button>
                    </div>
                </div>
                <table id="bin_location_table" class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 30%">Description</th>
                        <th style="min-width: 2%"><?php echo $this->lang->line('common_action'); ?></th><!--Action-->
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="item_assign_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 98%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Assign Items</h5>
            </div>
            <div class="modal-body">
                <div class="m-b-md" id="wizardControl">
                    <a class="btn btn-primary" href="#step1" data-toggle="tab">Assign Items</a>
                    <a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab">Assigned Items</a>
                </div>
                <hr>

                <div class="tab-content">
                    <div id="step1" class="tab-pane active">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label> <?php echo $this->lang->line('transaction_main_category'); ?> </label><!--Main Category-->
                                    <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="LoadMainCategory()"'); ?>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('transaction_sub_category'); ?> </label><!--Sub Category-->
                                    <select name="subcategoryID" id="subcategoryID" class="form-control searchbox" onchange="LoadSubSubCategory()">
                                        <option value=""><?php echo $this->lang->line('transaction_select_category'); ?> </option>
                                        <!--Select Category-->
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?> </label><!--Sub Sub Category-->
                                    <select name="subsubcategoryID" id="subsubcategoryID" class="form-control searchbox">
                                        <option value=""><?php echo $this->lang->line('transaction_select_category'); ?> </option>
                                        <!--Select Category-->
                                    </select>
                                </div>
                                <div class="col-sm-1" id="search_cancel" style="margin-top: 2%;">
                                <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                            src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                                </div>
                            </div>
                        </div>
                            <hr>
                        <div class="row">

                            <div class="table-responsive col-md-7">
                                <div class="table-responsive">
                                    <div class="pull-right">
                                        <button class="btn btn-primary btn-sm" id="selectAllBtn" style="font-size:12px;"
                                                onclick="selectAllRows()"> Select All
                                        </button>
                                    </div>
                                    <hr style="margin-top: 5%">
                                    <table id="items_table" class="<?php echo table_class(); ?>">
                                        <thead>
                                        <tr>
                                            <th style="min-width: 5%">#</th>
                                            <th style="min-width: 12%">Main Category</th>
                                            <th style="min-width: 12%">Sub Category</th>
                                            <th style="min-width: 12%">Sub Sub Category</th>
                                            <th>Description</th>
                                            <th style="min-width: 10%">Secondary Code</th>
                                            <th style="min-width: 10%">UOM</th>
                                            <th style="min-width: 65px">Action</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="table-responsive col-md-5">
                                <div class="pull-right">
                                    <button class="btn btn-primary btn-sm" id="addAllBtn" style="font-size:12px;"
                                            onclick="saveAssignedItems()"><i class="fa fa-plus" aria-hidden="true"></i> Assign Items
                                    </button>
                                    <button class="btn btn-default btn-sm" id="clearAllBtn" style="font-size:12px;"
                                            onclick="clearAllRows()"> Clear All
                                    </button>
                                </div>
                                <hr style="margin-top: 7%">
                                <form id="item_assign_form">
                                    <input type="hidden" id="wareHouseAutoIDhnitem" name="wareHouseAutoIDhnitem">
                                    <table class="<?php echo table_class(); ?>" id="tempTB">
                                        <thead>
                                        <tr>
                                            <th style="max-width: 5%">Main Category</th>
                                            <th style="max-width: 95%">Description</th>
                                            <th>
                                                <div id="removeBtnDiv"></div>
                                            </th>
                                        </tr>
                                        </thead>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="step2" class="tab-pane">
                        <div class="table-responsive">
                            <table id="assigned_items_table" class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="min-width: 12%">Warehouse Description</th>
                                    <th style="min-width: 12%">Warehouse Location</th>
                                    <th style="min-width: 12%">Item System Code</th>
                                    <th style="min-width: 12%">Item Description</th>
                                    <th style="min-width: 10%">UOM</th>
                                    <!--<th style="min-width: 65px">Action</th>-->
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <!--<button class="btn btn-primary" type="button" onclick="saveAssignedItems()">Save changes</button>-->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var Otables;
    var Otablesa;
    var selectedItemsSync = [];
    var empTempory_arr = [];
    var tempTB = $('#tempTB').DataTable({"bPaginate": false});
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/warehousemaster_view', '', 'Warehouse Master ');
        });
        $('.skin-square input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });
        $("#subsubcategoryID").change(function () {
            Otables.draw();
        });
        warehousemasterview();
        $('#warehousemaster_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                warehousecode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_warehouse_code_is_required');?>.'}}}, /*Code is required*/
                warehousedescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}, /*Description is required*/
                warehouselocation: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_location_is_required');?>.'}}},/*Location is required*/


                /*             warehouseAddress: {validators: {notEmpty: {message: ' Address is required.'}}},
                 warehouseTel: {validators: {notEmpty: {message: ' Telephone is required.'}}}*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            var warehouse_id = $('#warehouseredit').val();
            var is_pos_shifted = $('#is_pos_shifted').val();
            var isActive = $('#isActive').val();
            var is_mfqWarehouse_active = $('#is_mfqWarehouse_active').val();

            if(warehouse_id != '' && is_pos_shifted == 1 && isActive == 0){
                
                myAlert('e','There is an ongoing pos shift for selected warehouse. close the pos shift and try again!')

            }else{
                if(warehouse_id != '' && is_mfqWarehouse_active == 1 && isActive == 0){
                    swal({
                        title: "Do you want to deactive?", /*Are you sure?*/
                        text: "related warehouse is activated in manufacturing warehose as well!", /*You want to delete this record!*/
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3c8dbc",
                        confirmButtonText: "<?php echo $this->lang->line('common_update'); ?>" /*Update*/,
                        cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>" /*cancel */
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            url: "<?php echo site_url('srp_warehouseMaster/save_warehousemaster'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                HoldOn.close();
                                refreshNotifications(true);
                                $('.btn-primary').attr('disabled',false);
                                if (data) {
                                    $("#warehousemaster_model").modal("hide");


                                    warehousemasterview();
                                    //fetchPage('system/srp_mu_suppliermaster_view','Test','Supplier Master');
                                }
                            }, error: function () {
                                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                                /*An Error Occurred! Please Try Again*/
                                HoldOn.close();
                                refreshNotifications(true);
                            }
                        });
                    });
                }else{
                    $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            url: "<?php echo site_url('srp_warehouseMaster/save_warehousemaster'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                HoldOn.close();
                                refreshNotifications(true);
                                $('.btn-primary').attr('disabled',false);
                                if (data) {
                                    $("#warehousemaster_model").modal("hide");


                                    warehousemasterview();
                                    //fetchPage('system/srp_mu_suppliermaster_view','Test','Supplier Master');
                                }
                            }, error: function () {
                                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                                /*An Error Occurred! Please Try Again*/
                                HoldOn.close();
                                refreshNotifications(true);
                            }
                        });
                }
            }
            
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

    });


    function warehousemasterview() {
        var Otable = $('#warehousemaster_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('srp_warehouseMaster/load_warehousemastertable'); ?>",
            "aaSorting": [[0, 'desc']],
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
                {"mData": "wareHouseAutoID"},
                {"mData": "wareHouseCode"},
                {"mData": "wareHouseDescription"},
                {"mData": "wareHouseLocation"},
                {"mData": "status"},
                {"mData": "default"},
                {"mData": "action"}
            ],
            "columnDefs": [{
                "targets": [4],
                "className": "td-center"
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function open_warehouse_model() {
        $('#warehousemaster_form')[0].reset();
        $('#warehousemaster_form').bootstrapValidator('resetForm', true);
        $('#WarehouseHead').html('<?php echo $this->lang->line('erp_warehouse_add_new_warehouse');?>');
        /*Add New Warehouse*/
        $('.warehouseDeativate').removeClass('hide');
        $("#warehousemaster_model").modal({backdrop: "static"});
        $("#manufacturingglcodeROW").addClass('hide');
        $("#ismanufacturingHN").val(1);
        $("#isActive").val(0);
        $('#warehouseredit').val("");
        $('#warehouselocation').attr('readonly',false);
        //document.getElementById('warehousemaster_form').reset();
    }

    function openwarehousemastermodel(id) {
        //$("#warehousemaster_model").modal("show");
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {id: id},
            url: "<?php echo site_url('srp_warehouseMaster/edit_warehouse'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                open_warehouse_model();
                $('#WarehouseHead').html('<?php echo $this->lang->line('erp_warehouse_edit_warehouse');?>');
                /*Edit Warehouse*/
                $('#warehouseredit').val(id);
                $('#warehousecode').val(data['wareHouseCode']);
                $('#warehousedescription').val(data['wareHouseDescription']);
                $('#warehouselocation').val(data['wareHouseLocation']);
                $('#warehouselocation').attr('readonly',true);
                $('#warehouseAddress').val(data['warehouseAddress']);
                $('#warehouseTel').val(data['warehouseTel']);
                if (data['isPosLocation'] == 1) {
                    $('#isPosLocation').prop('checked', true);
                    $('.warehouseDeativate').addClass('hide');
                } else {
                    $('.warehouseDeativate').removeClass('hide');
                }
                if (data['isActive'] == 1) {
                    $('#isActive_check').prop('checked', true);
                    $("#isActive").val(1);
                    $('.warehouseDeativate').removeClass('hide');
                } else {
                    $('.warehouseDeativate').removeClass('hide');
                }
                // if (data['warehouseType'] == 2) {
                //     $('#ismanufacturing').prop('checked', true);
                //     $("#ismanufacturingHN").val(2);
                //     $('#manufacturingglcodeROW').removeClass('hide');
                // }

                if (data['warehouseType'] == 2) {
                    $("#wareHouseType").val(2).change();
                }

                if (data['warehouseType'] == 3) {
                    $("#wareHouseType").val(3).change();
                }

                $("#is_pos_shifted").val(data['is_pos_shifted']);
                $("#is_mfqWarehouse_active").val(data['is_mfqWarehouse_active']);
                $('#manufacturingglcode').val(data['WIPGLAutoID']).change();

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/

            }
        });
    }

    function setDefaultWarehouse(thisID, wareHouseAutoID) {
        var checked = 0;
        if ($(thisID).is(':checked')) {
            checked = 1;
        }
        else {
            checked = 0;
        }

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {chkdVal: checked, wareHouseAutoID: wareHouseAutoID},
            url: "<?php echo site_url('srp_warehouseMaster/setDefaultWarehouse'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] = 's') {
                    warehousemasterview();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }
    /*Added by naseek*/
    $('#ismanufacturing').on('change', function(){
        if($('#ismanufacturing').is(":checked")){
            $('#manufacturingglcodeROW').removeClass('hide');
            $('#ismanufacturingHN').val(2);




        }else {

            $('#manufacturingglcodeROW').addClass('hide');
            $('#ismanufacturingHN').val(1);

            $("#manufacturingglcode").val(null).trigger("change");
        }

    });

    function openBinlocation(id){
        load_bin_location_table(id);
        $('#binsavebtn').text('Save');
        $('#Description').val('');
        $('#binLocationID').val('');
        $('#wareHouseAutoIDBin').val(id);
        $("#bin_location_model").modal({backdrop: "static"});
    }

    function load_bin_location_table(id){
        var Otable = $('#bin_location_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('srp_warehouseMaster/load_bin_location_table'); ?>",
            "aaSorting": [[0, 'desc']],
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
                {"mData": "binLocationID"},
                {"mData": "Description"},
                {"mData": "action"}
            ],
            // "columnDefs": [{
            //     "targets": [5],
            //     "orderable": false
            // }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "wareHouseAutoID", "value": id});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }



    function save_bin_location(){
        var description=$('#Description').val();
        var wareHouseAutoID=$('#wareHouseAutoIDBin').val();
        var binLocationID=$('#binLocationID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: ({Description: description,wareHouseAutoID: wareHouseAutoID,binLocationID: binLocationID}),
            url: "<?php echo site_url('srp_warehouseMaster/save_bin_location'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                var id = $('#wareHouseAutoIDBin').val();
                load_bin_location_table(id);
                $('#binLocationID').val('');
                $('#Description').val('');
                $('#binsavebtn').text('Save');
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function edit_bin_location_modal(binLocationID,description){
        $('#binLocationID').val(binLocationID);
        $('#Description').val(description);
        $('#binsavebtn').text('Update');
    }

    function delete_bin_location(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>" /*Delete*/,
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>" /*cancel */
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'binLocationID': id},
                    url: "<?php echo site_url('srp_warehouseMaster/delete_bin_location'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            var id = $('#wareHouseAutoIDBin').val();
                            load_bin_location_table(id);
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function openWarehouseitems(wareHouseAutoID){
        $('#mainCategoryID').val('');
        $('#subcategoryID').val("");
        $('#subsubcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        $('#item_assign_modal').modal('show');
        $('#wareHouseAutoIDhnitem').val(wareHouseAutoID);
        items_table();
        assigned_items_table();
    }

    function LoadMainCategory() {
        $('#subcategoryID').val("");
        $('#subsubcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        load_sub_cat();
        Otables.draw();
    }

    function load_sub_cat(select_val) {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        var subid = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function LoadSubSubCategory() {
        $('#subsubcategoryID').val("");
        $('#subsubcategoryID option').remove();
        load_itemMaster_subsubCategory();
        Otables.draw();
    }

    function load_itemMaster_subsubCategory(select_val) {
        $('#subsubcategoryID').val("");
        $('#subsubcategoryID option').remove();
        var subsubid = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subsubid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subsubcategoryID').empty();
                    var mySelect = $('#subsubcategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


    function items_table() {
        Otables = $('#items_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('srp_warehouseMaster/fetch_items'); ?>",
            "aaSorting": [[0, 'desc']],
            "lengthMenu": [
                [25, 50, 100, 200, 500, 1000],
                [25, 50, 100, 200, 500, 1000]
            ],
            "iDisplayLength": 1000,
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
                {"mData": "itemAutoID"},
                {"mData": "mainCategory"},
                {"mData": "SubCategoryDescription"},
                {"mData": "SubSubCategoryDescription"},
                {"mData": "item_inventryCode"},
                {"mData": "seconeryItemCode"},
                {"mData": "defaultUnitOfMeasure"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [2, 3], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#mainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#subcategoryID").val()});
                aoData.push({"name": "subsubcategoryID", "value": $("#subsubcategoryID").val()});
                aoData.push({"name": "wareHouseAutoID", "value": $('#wareHouseAutoIDhnitem').val()});
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

    function assigned_items_table() {
        Otablesa = $('#assigned_items_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('srp_warehouseMaster/fetch_assigned_items'); ?>",
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
                {"mData": "warehouseItemsAutoID"},
                {"mData": "wareHouseDescription"},
                {"mData": "wareHouseLocation"},
                {"mData": "itemSystemCode"},
                {"mData": "itemDescription"},
                {"mData": "unitOfMeasure"}
                /*{"mData": "edit"}*/
            ],
            "columnDefs": [{"targets": [2, 3], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "wareHouseAutoID", "value": $('#wareHouseAutoIDhnitem').val()});
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

    function clearSearchFilter(){
        $('#mainCategoryID').val("");
        $('#subcategoryID').val("");
        $('#subsubcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        $('#subcategoryID').append($('<option>', {value: '', text: 'Select Sub Category'}));
        $('#subsubcategoryID').append($('<option>', {value: '', text: 'Select Sub Category'}));
        Otables.draw();
    }

    function ItemsSelectedSync(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync);
            if (inArray == -1) {
                selectedItemsSync.push(value);
            }
        }
        else {
            var i = selectedItemsSync.indexOf(value);
            if (i != -1) {
                selectedItemsSync.splice(i, 1);
            }
        }
    }

    function saveAssignedItems(){
        if(jQuery.isEmptyObject(empTempory_arr)){
            myAlert('w','Select Item')
        }else{
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("srp_warehouseMaster/saveAssignedItems"); ?>',
                dataType: 'json',
                data: {'itemAutoID': empTempory_arr,'wareHouseAutoID': $('#wareHouseAutoIDhnitem').val()},
                async: false,
                success: function (data) {
                    myAlert(data[0],data[1]);
                    if(data[0]=='s'){
                        Otables.draw();
                        Otablesa.draw();
                        clearAllRows();
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }
    }

    function addTempTB(det) {

        var table = $('#items_table').DataTable();
        var thisRow = $(det);

        var details = table.row(thisRow.parents('tr')).data();
        var itemAutoID = details.itemAutoID;

        var inArray = $.inArray(itemAutoID, empTempory_arr);
        if (inArray == -1) {
            var empDet = '<div class="pull-right"><input type="hidden" name="itemAutoIDHiddenID[]"  class="modal_empID" value="' + itemAutoID + '">';
            //empDet += '<input type="hidden" name="last_ocGrade[]" class="modal_ocGrade" value="' + details.last_ocGrade + '">';
            empDet += '<span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" style="color:rgb(209, 91, 71);"></span> </a></div>';

            tempTB.rows.add([{
                0: details.mainCategory,
                1: details.item_inventryCode,
                2: empDet,
                3: itemAutoID
            }]).draw();

            empTempory_arr.push(itemAutoID);
        }

    }

    function selectAllRows() {
        var tempTB = $('#tempTB').DataTable();
        var emp_modalTB = $('#items_table').DataTable();
        var empDet1;
        emp_modalTB.rows().every(function (rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            var itemAutoID = data.itemAutoID;

            var inArray = $.inArray(itemAutoID, empTempory_arr);
            if (inArray == -1) {
                empDet1 = '<div class="pull-right"><input type="hidden" name="itemAutoIDHiddenID[]" class="modal_empID" value="' + itemAutoID + '">';
                //empDet1 += '<input type="hidden" name="last_ocGrade[]" class="modal_ocGrade" value="' + data.last_ocGrade + '">';
                empDet1 += '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" onclick="removeTempTB(this)"></span> </a></div>';

                tempTB.rows.add([{
                    0: data.mainCategory,
                    1: data.item_inventryCode,
                    2: empDet1,
                    3: itemAutoID
                }]).draw();

                empTempory_arr.push(itemAutoID);
            }
        });
    }

    function removeTempTB(det) {
        var table = $('#tempTB').DataTable();
        var thisRow = $(det);
        var details = table.row(thisRow.parents('tr')).data();
        itemAutoID = details[3];

        empTempory_arr = $.grep(empTempory_arr, function (data) {
            return parseInt(data) != itemAutoID
        });

        table.row(thisRow.parents('tr')).remove().draw();
    }

    function clearAllRows() {
        var table = $('#tempTB').DataTable();
        empTempory_arr = [];
        table.clear().draw();
    }
   

    $('#isActive_check').on('change', function(){

        if($('#isActive_check').is(":checked")){

            $('#isActive').val(1);

        }else {

            $('#isActive').val(0);

        }

    });

</script>