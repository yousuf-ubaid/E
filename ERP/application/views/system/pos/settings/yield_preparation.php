<link rel="stylesheet" href="<?php echo base_url('plugins/iCheck/minimal/_all.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<script src="<?php echo base_url('plugins/iCheck/icheck.min.js') ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<?php
$customerType = all_customer_type();
$unitType = all_umo_new_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$date_format_policy = date_format_policy();
$current_date = current_format_date();

$item_arr = dropdown_yeield_preparationitems(current_companyID(), null, false);
?>


<div class="box box-warning">
    <div class="row">
        <div class="col-sm-12">
            <div class="col-sm-5">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <div class="form-group ">
                            <label for="supplierPrimaryCode">From</label><br>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="IncidateDateFrom"
                                       data-inputmask="'alias': '<?php echo $date_format_policy; ?>'"
                                       size="16" onchange="otable.draw()" value="" id="IncidateDateFrom"
                                       class="input-small">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <div class="form-group ">
                            <label for="supplierPrimaryCode">&nbsp;<?php echo $this->lang->line('common_to'); ?>&nbsp;
                                <!--To-->&nbsp;&nbsp;</label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="IncidateDateTo"
                                       data-inputmask="'alias': '<?php echo $date_format_policy; ?>'"
                                       size="16" onchange="otable.draw()" value="" id="IncidateDateTo"
                                       class="input-small">
                            </div>

                        </div>
                    </div>
                </div>


            </div>


            <div class="form-group col-sm-3">
                <label for="supplierPrimaryCode"> Item Name</label><br>
                <?php echo form_dropdown('itemAutoID[]', $item_arr, '', 'class="form-control" id="itemAutoID" onchange="otable.draw()" multiple="multiple"'); ?>
            </div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                <label> Status</label><br>
                <select name="statusFilter" onchange="otable.draw()" class="form-control select2" id="statusFilter">
                    <option value="-1" selected>All</option>
                    <option value="0">Open</option>
                    <option value="1">Confirmed</option>
                </select>
                <?php //echo form_dropdown('itemAutoID[]', $item_arr, '', 'class="form-control select2" id="statusFilter"'); ?>

            </div>
            <div class="form-group col-sm-2">
                <button type="button" class="btn btn-primary"
                        onclick="clear_all_filters()" style="margin-top: 15%;"><i class="fa fa-paint-brush"></i>
                    <?php echo $this->lang->line('common_clear'); ?><!-- Clear-->
                </button>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="nav-tabs-custom">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="posContainer">
                        <h4 style="font-size:16px; font-weight: 800;">
                            <?php echo $this->lang->line('pos_config_yield_preparation'); ?><!--Yield Preparation-->
                            - <?php echo current_companyCode() ?>
                            <span class="btn btn-primary btn-xs pull-right"
                                  onclick="fetchPage('system/pos/settings/add_yield_preparation',null,'Add Yield Preparation','YPRP')"><i
                                        class="fa fa-plus"></i>
                                <?php echo $this->lang->line('common_update_add_new'); ?><!--Add New--></span>
                        </h4>
                        <table class="<?php echo table_class_pos(1) ?>" id="tbl_yieldPreparation"
                               style="font-size:12px;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('pos_config_yield_number'); ?><!--Yield Number--> </th>
                                <th><?php echo $this->lang->line('common_item'); ?><!--Item--> </th>
                                <th>Document Date</th>
                                <th>outlet</th>
                                <th><?php echo $this->lang->line('common_uom'); ?><!--UOM--> </th>
                                <th><?php echo $this->lang->line('common_qty'); ?><!--Qty--> </th>
                                <th><?php echo $this->lang->line('common_narration'); ?><!--Narration--></th>
                                <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
    </div>
</div>
<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<script>
    var otable;
    $(document).ready(function (e) {
        $("#statusFilter").select2();
        fetchYieldPrepartion();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            otable.draw()
        });
        $('#itemAutoID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('#itemAutoID').multiselect2('selectAll', false);
        $('#itemAutoID').multiselect2('updateButtonText');

    });

    function fetchYieldPrepartion() {
        otable = $('#tbl_yieldPreparation').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('POS_yield_preparation/fetch_yield_preparation'); ?>",
            "aaSorting": [[0, 'desc']],
            "aoColumnDefs": [{"bSortable": false, "aTargets": [6, 7]}],
            /*"language": {
             processing: '<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
             },*/
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['yieldPreparationID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "yieldPreparationID"},
                {"mData": "documentSystemCode"},
                {"mData": "item"},
                {"mData": "documentDate"},
                {"mData": "wareHouseLocation"},
                {"mData": "uom"},
                {"mData": "qty"},
                {"mData": "narration"},
                {"mData": "status"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "itemAutoID", "value": $("#itemAutoID").val()});
                aoData.push({"name": "confirmedYN", "value": $("#statusFilter").val()});
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

    function delete_yield_preparation(yieldPreparationID) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'yieldPreparationID': yieldPreparationID},
                    url: "<?php echo site_url('POS_yield_preparation/delete_yield_preparation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            otable.draw();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters() {
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#statusFilter').val(-1).change();
        $('#itemAutoID').multiselect2('deselectAll', false);
        $('#itemAutoID').multiselect2('updateButtonText');
        otable.draw();
    }
</script>
