<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fleet_asset_master');
// echo head_page($title, false);
echo head_page($_POST['page_name'], FALSE);
$this->load->helper('fleet_helper');

$fetch_all_location = fetch_all_location();
$getAll_sub = load_asset_sub();
$getAll_main = load_asset_main();
$load_asset_main_filter = load_asset_main_filter();
$load_asset_sub_filter = load_asset_sub_filter();
$Model_arr = array('' => 'Select Sub Category');
?>


<input type="hidden" name="vehicleMasterID" id="vehicleMasterID">
<div class="row">
    <div class="col-md-3">
        <table class="<?php echo table_class() ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_active'); ?> </td><!--Active-->
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_in_active'); ?></td><!-- In-Active-->
            </tr>
        </table>
    </div>
    <div class="col-md-9 text-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="fetchPage('system/Fleet_Management/load_asset_edit_view', '')"
            style="margin-right: 4px"><i class="fa fa-plus"></i> <?php echo $this->lang->line('fleet_new_asset'); ?><!--New Asset-->
        </button>
    </div>
</div>

<hr>


<div class="row">
    <div class=" col-sm-3">
        <label for="faCatID"><?php echo $this->lang->line('_fleet_main_category'); ?><!--Main Category--> </label><br>

        <?php echo form_dropdown('vehicalebrand', $load_asset_main_filter, '', "class='form-control select2' id='vehicalebrand' required onchange='getSubCategoryfilter(this); oTable.draw()'"); ?>
    </div>
    <div class=" col-sm-3">
        <label for="fasubID"><?php echo $this->lang->line('_fleet_sub_category'); ?><!--Sub Category--></label><br>

        <?php echo form_dropdown('vehicalemodel', $load_asset_sub_filter, '', "class='form-control select2' id='vehicalemodel' onchange='usersTable()'"); ?>
    </div>
    <div class=" col-sm-2">
        <label for="segment">
            <?php echo $this->lang->line('asset_location'); ?><!--Asset Location--></label>
        <?php echo form_dropdown('locationFilter', $fetch_all_location, '', 'class="form-control select2" id ="locationFilter" onchange="usersTable()" '); ?>
    </div>
    <div class="col-sm-2">
        <br>
        <button type="button" class="btn btn-primary"
            onclick="clear_all_filters()"><i class="fa fa-paint-brush"></i>
            <?php echo $this->lang->line('common_clear'); ?><!-- Clear-->
        </button>
    </div>

</div>
<hr>





<div class="table-responsive">
    <table id="vehicle_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 2%">#</th>
                <th style="min-width:10%"><?php echo $this->lang->line('common_code'); ?></th><!--Code-->
                <th style="min-width: 6%"><?php echo $this->lang->line('fleet_serial_no'); ?></th><!--Serial No-->
                <th style="min-width: 10%"><?php echo $this->lang->line('fleet_type'); ?></th><!--Type-->
                <th style="min-width: 10%"><?php echo $this->lang->line('_fleet_main_category'); ?></th><!--Serial No-->
                <th style="min-width: 10%"><?php echo $this->lang->line('_fleet_sub_category'); ?></th><!--Type-->

                <th style="min-width: 15%"><?php echo $this->lang->line('common_description'); ?> </th><!--Description-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_location'); ?></th><!--Location-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?></th><!--Status-->
                <th style="min-width: 10%"><?php echo $this->lang->line('asset_status'); ?></th><!--Asset Status-->

                <th style="min-width: 6%"><?php echo $this->lang->line('common_action'); ?></th><!--Action-->
            </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script>
    var vehicleMasterID;
    var vehicalebrandid;
    var vehiclemodelid;
    //  var NewVehicleAddForm = $('#NewVehicleAddForm');
    var oTable;

    $(document).ready(function() {
        $('.select2').select2();
        $('.headerclose').click(function() {
            fetchPage('system/Fleet_Management/maintenance_asset', '', 'Asset Master');
        });
        usersTable();
    });
    usersTable();

    function usersTable() {
        oTable = $('#vehicle_table').DataTable({

            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Fleet/fetch_assets'); ?>",
            "aaSorting": [
                [0, 'desc']
            ],
            "fnInitComplete": function() {},

            "fnDrawCallback": function(oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i;
                    (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['vehicleMasterID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },

            "aoColumns": [{
                    "mData": "vehicleMasterID"
                },
                {
                    "mData": "vehicleCode"
                },
                {
                    "mData": "assetSerialNo"
                },
                {
                    "data": "asset_type_id",
                    "render": function(data, type, row) {
                        return data == 1 ? 'Asset' : 'Component';
                    }
                },
                {
                    "mData": "brand_description"
                },
                {
                    "mData": "model_description"
                },
                {
                    "mData": "vehDescription"
                },
                {
                    "mData": "locationName"
                },
                {
                    "mData": "isActive"
                },
                {
                    "mData": "assetStatusDesc",
                    "bSortable": false, // To disable sorting for this column if needed
                    "mRender": function(data, type, row) {
                        return data; // Ensures that the HTML is rendered as-is
                    },
                    "createdCell": function(td, cellData, rowData, row, col) {
                        $(td).css('text-align', 'center'); // Center the text
                    }
                },
                {
                    "mData": "action"
                },
            ],
            //   "columnDefs": [{"searchable": false, "targets": [0,9,10]}],
            "fnServerData": function(sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "vehicalebrand",
                    "value": $("#vehicalebrand").val()
                });
                aoData.push({
                    "name": "vehicalemodel",
                    "value": $("#vehicalemodel").val()
                });
                aoData.push({
                    "name": "locationFilter",
                    "value": $("#locationFilter").val()
                });
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
    function getSubCategoryfilter(VehicleBrand) {
        var masterCategory = VehicleBrand.value;
        var thisName = VehicleBrand.name;
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Fleet/getSub'); ?>",
            data: {
                'VehicleBrand': masterCategory
            },
            dataType: "html",
            cache: false,
            beforeSend: function() {},
            success: function(data) {
                // Log data to verify response
                if (thisName == 'vehicalebrand') {
                    $('#vehicalemodel').html(data);
                } else if (thisName == 'faSubCatID') {
                    $('#faSubCatID2').html(data);
                }

                usersTable();
            },
            error: function(jqXHR, textStatus, errorThrown) {}
        });
    }

    function fetch_sub_all_types(VehicleBrand) {


        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'VehicleBrand': VehicleBrand
            },
            url: "<?php echo site_url('Fleet/fetch_sub_all'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                $('#div_loadmodel').html(data);
                $('.select2').select2();
                stopLoad();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function fetch_Model_detail() {
        var vehicalemodel_id = $('#vehicle_model').val();
        if (vehicalemodel_id == '') {
            //swal("", "Select An Employee", "error");
            myAlert('e', 'Select A Model');
        } else {
            vehiclemodelid = vehicalemodel_id;
            var vehicalemodel = $("#vehicle_model option:selected").text();

            $('#vehicleModel').val($.trim(vehicalemodel)).trigger('input');
            $('#vehicleModel').prop('readonly', true);

        }
    }

    function delete_asset(id, description) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Fleet/delete_assetMaster'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'vehicleMasterID': id
                    },
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            usersTable();
                        }
                    },
                    error: function() {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }



    function Edit_vehicles(id, element) {
        $.ajax({
            async: true,
            url: "*/<?php // echo site_url('Fleet/Save_vehicle'); 
                    ?>/*",
            type: 'post',
            dataType: 'json',
            data: {
                'vehicleMasterID': id
            },
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    usersTable();
                }
            },
            error: function() {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    function clear_all_filters() {
        $('#vehicalebrand').val("");
        $('#vehicalemodel').val("");
        $('#locationFilter').val("");
        usersTable();
    }
</script>