<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_document_configuration');
echo head_page($title, false);
$page = explode('|',$this->input->post('page_name'));
$pageID = isset($page[1]) ? $page[1] : 33;
$main_category_arr = all_main_category_drop();
?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<style>
    #mfq_costing_tbl th{
        text-transform: uppercase;
        text-align: center;
    }
    table.costingTable thead th{
        color: #f76f01;
        text-align: center !important;
        font-size:12px !important;
        border-bottom: 1px double #f76f01 !important;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="m-b-md" id="wizardControl">
    <ul class="nav nav-tabs mainpanel">
        <li class="active">
            <a class="buybackTab" href="#step1" data-toggle="tab" aria-expanded="true">
                <span><i class="fa fa-cog tachometerColor" aria-hidden="true" style="color: #f76f01;font-size: 16px;">
                    </i>&nbsp;&nbsp;<?php echo $this->lang->line('manufacturing_costing_configuration');?>
                </span>
            </a>
        </li>
        <li class="">
            <a class="buybackTab" href="#step2" data-toggle="tab" aria-expanded="true">
                <span><i class="fa fa-cog tachometerColor" aria-hidden="true" style="color: #f76f01;font-size: 16px;">
                    </i>&nbsp;<?php echo $this->lang->line('manufacturing_gl_configuration');?>
                </span>
            </a>
        </li>
        <li class="">
            <a class="buybackTab" href="#step3" data-toggle="tab" aria-expanded="true">
                <span><i class="fa fa-cog tachometerColor" aria-hidden="true" style="color: #f76f01;font-size: 16px;">
                    </i>&nbsp;<?php echo $this->lang->line('manufacturing_item_configuration');?>
                </span>
            </a>
        </li>
        <li class="">
            <a class="buybackTab" href="#step4" onclick="load_mfq_company_policy()" data-toggle="tab" aria-expanded="true">
                <span><i class="fa fa-cog tachometerColor" aria-hidden="true" style="color: #f76f01;font-size: 16px;">
                    </i>&nbsp;<?php echo $this->lang->line('manufacturing_policy_configuration');?>
                </span>
            </a>
        </li>
    </ul>
</div>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <div class="row">
            <form id="frm_mfq_costing" autocomplete="off">
                <div class="col-md-12" id="mfq_costing">
                    <div class="table-responsive">
                        <table id="mfq_costing_tbl" class="table table-striped table-condensed costingTable">
                            <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th colspan="2"><?php echo $this->lang->line('manufacturing_usage_update');?><!--Usage Update--></th>
                            </tr>
                            <tr>
                                <th style="min-width: 5%">&nbsp;</th>
                                <th style="min-width: 5%"><?php echo $this->lang->line('manufacturing_job_card');?><!--JOB CARD--></th>
                                <th style="min-width: 5%"><?php echo $this->lang->line('manufacturing_for_entries');?><!--FOR ENTRIES--></th>
                                <th style="min-width: 5%"><?php echo $this->lang->line('manufacturing_manual');?><!--Manual--></th>
                                <th style="min-width: 5%"><?php echo $this->lang->line('manufacturing_linked_document');?><!--Linked Documents--></th>
                            </tr>
                            </thead>
                            <tbody id="mfq_costing_tbl_body"></tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="step2" class="tab-pane">
        <div class="pull-right">
            <button type="button" id="btn_config_new_gl" class="btn btn-primary"> Configure New GL </button>
        </div>
        <div class="row" style="margin-top: 20px;">

            <div class="col-md-12" id="mfq_costing" style="margin-top: 10px;">
                <div class="table-responsive">
                    <table id="mfq_gl_config_tbl" class="table table-striped table-condensed costingTable">
                        <thead>
                            <tr>
                                <th style="min-width: 5%">&nbsp;</th>
                                <th style="min-width: 10%">Main Category</th>
                                <th style="min-width: 10%">GL System Code</th>
                                <th style="min-width: 10%">GL Secondary Code</th>
                                <th style="min-width: 25%">Description</th>
                                <th style="min-width: 25%">&nbsp;</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <div class="pull-right">
            <button type="button" id="btn_config_new_item" class="btn btn-primary"> Configure New Item </button>
        </div>
        <div class="row" style="margin-top: 20px;">

            <div class="col-md-12" id="mfq_costing" style="margin-top: 10px;">
                <div class="table-responsive">
                    <table id="mfq_item_config_tbl" class="table table-striped table-condensed costingTable">
                        <thead>
                            <tr>
                                <th style="min-width: 5%">&nbsp;</th>
                                <th style="min-width: 10%">Main Category</th>
                                <th style="min-width: 10%">Sub Category</th>
                                <th style="min-width: 25%">Description</th>
                                <th style="min-width: 5%">Secondary Code</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="step4" class="tab-pane">
        <div class="row" style="margin: 20px;" id="companyPolicy"></div>
    </div>
</div>


<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Cofigure Item Master From ERP"
     id="itemConfigNew">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cofigure Item Master From ERP</h4>
            </div>
            <div class="modal-body">
                <div id="sysnc">
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="syncMainCategoryID" onchange="LoadMainCategorySync()"'); ?>
                        </div>
                        <div class="form-group col-sm-3">
                            <select name="subcategoryID" id="syncSubcategoryID" class="form-control searchbox" onchange="sync_item_config_table()">
                                <option value="">Select Category</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="item_config_table_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">&nbsp;</th>
                                <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_main'); ?><!--Main--> <abbr title="Category"><?php echo $this->lang->line('manufacturing_category'); ?><!--Cat..--> </abbr></th>
                                <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_sub'); ?><!--SUB--> <abbr title="Category"> Cat..</abbr></th>
                                <th style="min-width: 25%">&nbsp;</th>
                                <th style="min-width: 10%"><abbr title="Secondary Code"><?php echo $this->lang->line('common_code'); ?><!--CODE--></abbr></th>
                                <th style="min-width: 10%"><abbr title="Current Stock"><?php echo $this->lang->line('manufacturing_current_stock_title'); ?> <!--Curr.&nbsp;Stock--></abbr></th>
                                <!--<th style="min-width: 10%"><abbr title="Weighted Average Cost"> WAC </abbr></th>-->
                                <th style="min-width: 5%; text-align: center !important;">
                                    <button type="button" data-text="Add" onclick="configure_item()"
                                            class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i><?php echo $this->lang->line('common_add_item'); ?> <!--Add Items-->
                                    </button>
                                </th>
                                <th style="min-width: 10%"><abbr title="Current Stock"> </th>
                                <th style="min-width: 10%"><abbr title="Current Stock"> </th>
                                <th style="min-width: 10%"><abbr title="Current Stock"> </th>
                                <th style="min-width: 10%"><abbr title="Current Stock"> </th>
                            </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel" id="new_gl_config">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add New GL configuration</h4>
            </div>
            <?php echo form_open('', 'role="form" id="new_gl_config_form"'); ?>
            <div class="modal-body">
                <div class="row" style="margin-top: 10px;">
                    <div class="row" style="margin-top: 10px;">
                        <input class="hidden" id="configurationAutoID" name="configurationAutoID">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title"><?php echo $this->lang->line('common_item_category') ?><!--Item Category--></label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('itemCategory', array('' => 'Select Item Category', 'Inventory' => 'Inventory', 'Service' => 'Service'), '', 'class="form-control select2" id="glConfig_itemCategory" onchange="gltype_selected()"'); ?>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title"><?php echo $this->lang->line('common_gl_code') ?><!--GL Code--></label>
                    </div>
                    <div class="form-group col-sm-6" id="glConfig_glAutoID_inv_div">
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('glAutoID_inv', all_asset_gl_drop(), '', 'class="form-control select2" id="glConfig_glAutoID_inv"'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                    <div class="form-group col-sm-6" id="glConfig_glAutoID_srv_div">
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('glAutoID_srv', fetch_all_gl_codes(), '', 'class="form-control select2" id="glConfig_glAutoID_srv"'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-primary submit_gl"><?php echo $this->lang->line('common_save') ?><!--Save-->
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    var selectedItemsSync = [];
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_costing', 'Test', 'MFQ|348');
        });
        $(".select2").select2();

        $('#new_gl_config_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                itemCategory: {validators: {notEmpty: {message: 'Item Category is required.'}}}
                // glAutoID: {validators: {notEmpty: {message: 'GL Code is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('MFQ_Costing/save_new_gl_configuration'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#new_gl_config').modal('hide');
                        fetch_gl_configuration_table();
                    }
                    $('.submit_gl').removeClass('disabled');
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        fetch_Costing_table();
        fetch_tem_configuration_table();
        fetch_gl_configuration_table();
        
        $("#btn_config_new_item").click(function () {
            sync_item_config_table();
            $("#itemConfigNew").modal('show');
        });

        $("#btn_config_new_gl").click(function () {
            $("#configurationAutoID").val('');
            $("#glConfig_itemCategory").val('').change();
            $("#glConfig_glAutoID_inv").val('').change();
            $("#glConfig_glAutoID_srv").val('').change();
            $("#new_gl_config").modal('show');
        });
    });

    function gltype_selected() {
        if($('#glConfig_itemCategory').val() == 'Inventory') {
            $("#glConfig_glAutoID_inv_div").removeClass('hide');
            $("#glConfig_glAutoID_srv_div").addClass('hide');
        } else {
            $("#glConfig_glAutoID_inv_div").addClass('hide');
            $("#glConfig_glAutoID_srv_div").removeClass('hide');            
        }
    }

    function fetch_Costing_table() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_Costing/fetch_costing_entry_setup"); ?>',
            dataType: 'html',
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#mfq_costing_tbl_body').html(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#mfq_costing_tbl_body').html(xhr.responseText);

            }
        });
    }

    function LoadMainCategorySync() {
        $('#syncSubcategoryID').val("");
        $('#syncSubcategoryID option').remove();
        load_sub_cat_sync();
        sync_item_config_table();
    }

    function load_sub_cat_sync(select_val) {
        $('#syncSubcategoryID').val("");
        $('#syncSubcategoryID option').remove();
        var subid = $('#syncMainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#syncSubcategoryID').empty();
                    var mySelect = $('#syncSubcategoryID');
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

    function sync_item_config_table() {
        oTable2 = $('#item_config_table_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_Costing/fetch_item_config_sync_item'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },

            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $('.item-iCheck').iCheck('uncheck');
                if (selectedItemsSync.length > 0) {

                    $.each(selectedItemsSync, function (index, value) {
                        $("#selectItem_" + value).iCheck('check');

                        // $("#selectItem_" + value).prop("checked", true);
                    });
                }
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });
                $('input').on('ifChecked', function (event) {
                    ItemsSelectedSync(this);
                });
                $('input').on('ifUnchecked', function (event) {
                    ItemsSelectedSync(this);
                });

            },
            "aoColumns": [
                {"mData": "itemAutoID"},
                {"mData": "mainCategory"},
                {"mData": "SubCategoryDescription"},
                {"mData": "item_inventryCode"},
                {"mData": "seconeryItemCode"},
                {"mData": "CurrentStock"},
                /*{"mData": "TotalWacAmount"},*/
                {"mData": "edit"},
                {"mData": "itemSystemCode"},
                {"mData": "itemDescription"},
                {"mData": "defaultUnitOfMeasure"},
                {"mData": "seconeryItemCode"}



            ],
            "columnDefs": [{"visible":false,"searchable": true,"targets": [7,8,9,10] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#syncMainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#syncSubcategoryID").val()});
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

    function configure_item() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_Costing/configure_item"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync},
            async: false,
            success: function (data) {
                if (data['status']) {
                    refreshNotifications(true);
                    fetch_tem_configuration_table();
                    sync_item_config_table();
                    selectedItemsSync = [];
                } else {
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function fetch_tem_configuration_table()
    {
        Otable = $('#mfq_item_config_tbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_Costing/fetch_tem_configuration_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "fnDrawCallback": function (oSettings) {
               
            },
            "aoColumns": [
                {"mData": "itemAutoID"},
                {"mData": "mainCategory"},
                {"mData": "SubCategoryDescription"},
                {"mData": "item_inventryCode"},
                {"mData": "seconeryItemCode"}
            ],
            // "columnDefs": [{"targets": [6], "orderable": false}],
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

    function fetch_gl_configuration_table()
    {
        Otable = $('#mfq_gl_config_tbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_Costing/fetch_gl_configuration_table'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "fnDrawCallback": function (oSettings) {
               
            },
            "aoColumns": [
                {"mData": "configurationAutoID"},
                {"mData": "configurationCode"},
                {"mData": "systemAccountCode"},
                {"mData": "GLSecondaryCode"},
                {"mData": "GLDescription"},
                {"mData": "edit"}
            ],
            // "columnDefs": [{"targets": [6], "orderable": false}],
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

    function edit_gl_config(configurationAutoID, configurationCode, GLAutoID)
    {
        $("#configurationAutoID").val(configurationAutoID);
        $("#glConfig_itemCategory").val(configurationCode).change();
        $("#glConfig_glAutoID_inv").val(GLAutoID).change();
        $("#glConfig_glAutoID_srv").val(GLAutoID).change();
        $("#new_gl_config").modal('show');
    }

    function load_mfq_company_policy()
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data : {'moduleID': <?php echo $pageID?>},
            url: "<?php echo site_url('CompanyPolicy/fetch_company_policy_modulewise'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#companyPolicy').html(data);
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function ChangePolicy(element) {
        var id = $(element).attr('id'),
            value = $(element).val(),
            type = $(element).data('type')
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {id: id, value: value, type: type},
            url: "<?php echo site_url('CompanyPolicy/policy_detail_update');?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                load_mfq_company_policy();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
</script>