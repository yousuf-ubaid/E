<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$fetch_master_cat = fa_asset_category(3);
$fetch_sub_cat = array();
$fetch_all_location = fetch_all_location();
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_asset_master');
echo head_page($title, false);
/*
echo head_page('Asset Master', false); */?>

<div id="assetDivMasterDiv">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
    <div id="filter-panel" class="collapse filter-panel"></div>

    <div class="row">
        <div class=" col-sm-3">
            <label for="faCatID"><?php echo $this->lang->line('assetmanagement_main_category'); ?><!--Main Category--> </label><br>
            <?php echo form_dropdown('mainCatId', $fetch_master_cat, '', "class='form-control select2' id='mainCatId' required onchange='getSubCategoryfilter(this)'"); ?>
        </div>
        <div class=" col-sm-3">
            <label for="faCatID"><?php echo $this->lang->line('assetmanagement_sub_category'); ?><!--Sub Category--></label><br>
            <?php echo form_dropdown('subCatId', $fetch_sub_cat, '', 'class="form-control" id="subCatId" onchange="feedAssetMaster()"'); ?>
        </div>
        <div class=" col-sm-2">
            <label for="segment">
                <?php echo $this->lang->line('assetmanagement_asset_location'); ?><!--Asset Location--></label>
            <?php echo form_dropdown('locationFilter', $fetch_all_location, '', 'class="form-control" id = "locationFilter" onchange="feedAssetMaster()" '); ?>
        </div>
        <div class="col-sm-2">
            <br>
            <button type="button" class="btn btn-primary"
                    onclick="clear_all_filters()"><i class="fa fa-paint-brush"></i>
                <?php echo $this->lang->line('common_clear'); ?><!-- Clear-->
            </button>
        </div>
        <div class="col-md-2">
            <br>
            <button type="button" class="btn btn-primary pull-right"
                    onclick="getAssetDetails()">
                <i class="fa fa-plus"></i> <?php echo $this->lang->line('assetmanagement_add_new_asset');?><!--Add New Asset-->
            </button><!--fetchPage('system/asset_management/add_new_asset',null,'Add New Asset','FA');-->
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="assetMaster_table" class="<?php echo table_class() ?>">
            <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th style=""><?php echo $this->lang->line('assetmanagement_asset_code');?><!--Asset Code--></th>
                <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                <th><?php echo $this->lang->line('assetmanagement_serial');?><!--Serial--> #</th>
                <th><?php echo $this->lang->line('common_cost');?><!--Cost--> (<?php echo $this->common_data['company_data']['company_default_currency']; ?>)</th>
                <th><?php echo $this->lang->line('assetmanagement_main_category');?><!--Main Category--></th>
                <th style="">&nbsp;<?php echo $this->lang->line('assetmanagement_sub_category');?><!--Sub Category--></th>
                <th style=""><?php echo $this->lang->line('common_Location');?><!--Location--></th>
                <th style=""><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
                <th style=""><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
                <th style="width: 50px;"></th>
            </tr>
            </thead>
        </table>
    </div>

</div>


<div id="assetDivDetailsMainDiv" style="display: none;">

</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="ivms_no_cong">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Asset Tracing</h4>
            </div>
            <?php echo form_open('', 'role="form" id="ivmsnoconfig"'); ?>
            <div class="modal-body">
                <input type="hidden" name="jpmasterid" id="jpmasterid">
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12">
                        <img src="<?php echo base_url('images/journeyplan/ivmsmap.jpg'); ?>" style="width: 100%; opacity: 0.3;
    filter: alpha(opacity=30);">
                        <div style="position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);font-weight:bold;font-size:22px;color: #ca0000"><strong>Tracing Not Configured</strong></div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    $(document).ready(function () {
        feedAssetMaster();
    });

    function feedAssetMaster(selectedID=null) {
        $('#assetMaster_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/load_assetmaster'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                makeTdAlign('assetMaster_table', 'right', [3]);
                makeTdAlign('assetMaster_table', 'center', [8]);
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['faID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "faID"},
                {"mData": "faCode"},
                {"mData": "assetDescription"},
                {"mData": "faUnitSerialNo"},
                {"mData": "companyLocalAmount"},
                {"mData": "masterCategoryDesc"},
                {"mData": "subCategoryDesc"},
                {"mData": "locationName"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [{
                "targets": [ 1, 2, 3, 4, 5, 6, 7, 8,9],
                "orderable": false
            }, {"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCatId", "value": $("#mainCatId").val()});
                aoData.push({"name": "subCatId", "value": $("#subCatId").val()});
                aoData.push({"name": "locationFilter", "value": $("#locationFilter").val()});
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

    function referbackAsset(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You Want to refer back!*/
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
                    data: {'faId': id},
                    url: "<?php echo site_url('AssetManagement/referback_asset'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        feedAssetMaster();
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function getAssetDetails(index) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {faId: index},
            url: "<?php echo site_url('AssetManagement/get_asset_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                toggleMasterDetails('#assetDivDetailsMainDiv','<?php echo $this->lang->line('assetmanagement_asset_master')?>')/*Asset Master*/
                $('.headerclose').attr('onclick', "toggleMasterDetails('#assetDivMasterDiv', '<?php echo $this->lang->line('assetmanagement_asset_master')?>')");/*Asset Master*/
                $('#assetDivDetailsMainDiv').html(data);
                stopLoad();

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function toggleMasterDetails(id, headerText) {
        $("div[id^='assetDiv']").not(id).hide();
        $(id).show();
        $("#box-header-title").text(headerText);
        feedAssetMaster();
    }

    function deleteAsset(faId) {
        bootbox.confirm('Are you sure? You want to delete this asset?', function (confirmed) {
            if (confirmed) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('AssetManagement/delete_asset'); ?>",
                    data: {faID: faId},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        feedAssetMaster();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                    }
                });
            }
        })
    }

    function getSubCategoryfilter(item) {
        var masterCategory = item.value;
        var thisName = item.name;
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('AssetManagement/getSubCategory'); ?>",
            data: {masterCategory: masterCategory,status:true},
            dataType: "html",
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
                if (thisName == 'mainCatId') {
                    $('#subCatId').html(data);
                } else if (thisName == 'faSubCatID') {
                    $('#faSubCatID2').html(data);
                }
                feedAssetMaster();
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }

    function clear_all_filters() {
        $('#mainCatId').val("");
        $('#subCatId').val("");
        $('#locationFilter').val("");
        feedAssetMaster();
    }

    function configure_ivms_no() {
        $('#ivms_no_cong').modal("show");
    }
</script>