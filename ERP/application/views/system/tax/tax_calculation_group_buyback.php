<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->load->helpers('expense_claim');
$this->lang->load('common', $primaryLanguage);
$this->lang->load('tax', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
$title = $this->lang->line('tax_tax_authority');
echo head_page($this->lang->line('tax_formula_group'), false);
/*echo head_page('Tax Authority', true);*/
$main_category_arr = all_main_category_drop();
/*echo head_page('Supplier Master', true);*/
$supplier_arr = all_authority_drop(false);
$customerCategory    = party_category(2, false);
$currncy_arr    = all_currency_new_drop(false);
?>
<div id="filter-panel" class="collapse filter-panel">

</div>
<div class="row">
    <div class="col-md-5">

    </div>
    <div class="col-md-7 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="openCalculationGroup()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new'); ?>
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="calculation_group_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 40%"><?php echo $this->lang->line('common_description'); ?></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('tax_type'); ?></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action'); ?></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="tax_calculation_group_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" id="calculationHead"></h3>
            </div>
            <form role="form" id="taxcalculationmaster_form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="taxCalculationformulaID" name="taxCalculationformulaID">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('tax_type'); ?> </label>
                            <div class="col-sm-6">
                                <select name="taxType" class="form-control" id="taxType" data-bv-field="taxType">
                                    <option value="" selected="selected"><?php echo $this->lang->line('common_select'); ?> </option>
                                    <option value="1"><?php echo $this->lang->line('tax_sales_tax'); ?> </option>
                                    <option value="2"><?php echo $this->lang->line('tax_purchase_tax'); ?> </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description'); ?></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" rows="2" id="Description" name="Description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('tax_is_claimable'); ?></label>
                            <div class="col-sm-6">
                                <div class="skin skin-square">
                                    <div class="skin-section" id="extraColumns">
                                        <input id="isClaimable" type="checkbox" data-caption="" class="columnSelected" name="isClaimable" value="1" checked>
                                        <label for="checkbox">
                                            &nbsp;
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="tax_item_master_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" id=""><?php echo $this->lang->line('tax_link_item_master'); ?></h3>
            </div>
            <form role="form" id="taxitemmaster_form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="taxCalculationformulaID_item" name="taxCalculationformulaID_item">
                    <input type="hidden" class="form-control" id="taxCalculationtype_item" name="taxCalculationtype_item">
                    <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group col-sm-3">
                            <label> <?php echo $this->lang->line('transaction_main_category'); ?> </label><!--Main Category-->
                            <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="LoadMainCategory()"'); ?>
                        </div>
                        <div class="form-group col-sm-3">
                            <label><?php echo $this->lang->line('transaction_sub_category'); ?> </label><!--Sub Category-->
                            <select name="subcategoryID" id="subcategoryID" class="form-control searchbox" onchange="LoadSubSubCategory()">
                                <option value=""><?php echo $this->lang->line('transaction_select_category'); ?> </option>
                                <!--Select Category-->
                            </select>
                        </div>
                        <div class="form-group col-sm-3">
                            <label><?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?> </label><!--Sub Sub Category-->
                            <select name="subsubcategoryID" id="subsubcategoryID" class="form-control searchbox" onclick="Otable.draw();">
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
                    <div class="table-responsive">
                        <table id="item_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 12%"><?php echo $this->lang->line('transaction_main_category'); ?></th>
                                <th style="min-width: 12%"><?php echo $this->lang->line('transaction_sub_category'); ?></th>
                                <th style="min-width: 12%"><?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?> </th>
                                <th><?php echo $this->lang->line('common_description'); ?></th>
                                <th style="min-width: 10%"><?php echo $this->lang->line('tax_secondary_code'); ?></th>
                                <th style="min-width: 65px"><button type="button" data-text="Add" onclick="addItemTax()" class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('common_add_item'); ?>
                                    </button></th>
                                <th style="min-width: 2%">&nbsp;</th>
                                <th style="min-width: 2%">&nbsp;</th>
                            </tr>
                            </thead>
                        </table>
                    </div>


                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?></button>

                </div>
            </form>
        </div>
    </div>
</div>



<script type="text/javascript">
    var Otable;
    var Otables;
    var selectedItemsSync = [];
    var selectedItemsnotSync = [];
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/tax/tax_calculation_group_buyback', '', '<?php echo $this->lang->line('tax_formula_group'); ?>');
        });
        calculation_group_table();


        $('#taxcalculationmaster_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                taxType: {validators: {notEmpty: {message: "<?php echo $this->lang->line('tax_type_is_required'); ?>"}}},
                Description: {validators: {notEmpty: {message: "<?php echo $this->lang->line('common_description_is_required'); ?>"}}}
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
                url: "<?php echo site_url('TaxCalculationGroup/save_tax_calculation_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    myAlert(data[0],data[1]);
                    if(data[0]=='s'){
                        $("#tax_calculation_group_model").modal("hide");
                        Otable.draw();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });
        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
    });

    function calculation_group_table() {
         Otable = $('#calculation_group_table').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('TaxCalculationGroup/fetch_calculation_group'); ?>",
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
                {"mData": "taxCalculationformulaID"},
                {"mData": "Description"},
                {"mData": "type_detail"},
                {"mData": "edit"}
            ],
             "columnDefs": [{"searchable": false, "targets": [0,2]}],
             "fnServerData": function (sSource, aoData, fnCallback) {
                /*aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                aoData.push({"name": "supplierCode", "value": $("#supplierCode").val()});
                aoData.push({"name": "currency", "value": $("#currency").val()});*/
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

    function delete_authority(id) {
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
                    data: {'taxAuthourityMasterID': id},
                    url: "<?php echo site_url('Authority/delete_authority'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        Otable.draw();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function openCalculationGroup(){
        $('#taxCalculationformulaID').val('');
        $('#taxcalculationmaster_form')[0].reset();
        $('#taxcalculationmaster_form').bootstrapValidator('resetForm', true);
        $('#calculationHead').html('<?php echo $this->lang->line('tax_create_formula'); ?>');
        $("#tax_calculation_group_model").modal({backdrop: "static"});
    }

    function open_calculation_group_edit(id){
        $('#taxCalculationformulaID').val(id);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'taxCalculationformulaID': id},
            url: "<?php echo site_url('TaxCalculationGroup/load_calculation_group'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#calculationHead').html('<?php echo $this->lang->line('tax_update_formula'); ?>');
                $('#Description').val(data['Description']);
                $('#taxType').val(data['taxType']);
                $("#tax_calculation_group_model").modal({backdrop: "static"});
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function assign_items(taxCalculationformulaID,taxType){
        $('#taxCalculationformulaID_item').val(taxCalculationformulaID);
        $('#taxCalculationtype_item').val(taxType);
        $("#tax_item_master_model").modal({backdrop: "static"});
         selectedItemsSync = [];
         selectedItemsnotSync = [];
        load_item_datatable(taxCalculationformulaID,taxType);
    }

    function load_item_datatable(taxCalculationformulaID,taxType){
        Otables = $('#item_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('TaxCalculationGroup/fetch_item'); ?>",
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
                $('.item-iCheck').iCheck('uncheck');
                if (selectedItemsSync.length > 0) {
                    $.each(selectedItemsSync, function (index, value) {
                        $("#selectItem_" + value).iCheck('check');
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
                {"mData": "SubSubCategoryDescription"},
                {"mData": "item_inventryCode"},
                {"mData": "seconeryItemCode"},
                {"mData": "edit"},
                {"mData": "itemDescription"},
                {"mData": "itemSystemCode"}
            ],
            "columnDefs": [{"targets": [2, 3,6], "orderable": false}, {
                "visible": false,
                "searchable": true,
                "targets": [7, 8]
            }, {"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#mainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#subcategoryID").val()});
                aoData.push({"name": "subsubcategoryID", "value": $("#subsubcategoryID").val()});
                aoData.push({"name": "taxType", "value": taxType});
                aoData.push({"name": "taxCalculationformulaID", "value": taxCalculationformulaID});
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

    function LoadMainCategory() {
        $('#subcategoryID').val("");
        $('#subsubcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        load_sub_cat();
        Otable.draw();
    }

    function LoadSubSubCategory() {
        //$('#subcategoryID').val("");
        $('#subsubcategoryID').val("");
        //$('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        load_itemMaster_subsubCategory();
        Otable.draw();
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
                    mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('tax_select_option'); ?>'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
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
                    mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('tax_select_option'); ?>'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function clearSearchFilter(){
        $('#mainCategoryID').val("");
        $('#subcategoryID').val("");
        $('#subsubcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        $('#subcategoryID').append($('<option>', {value: '', text: "<?php echo $this->lang->line('tax_select_sub_category'); ?>"}));
        $('#subsubcategoryID').append($('<option>', {value: '', text: "<?php echo $this->lang->line('tax_select_sub_category'); ?>"}));
        Otable.draw();
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
            selectedItemsnotSync.push($(item).val());
            var i = selectedItemsSync.indexOf(value);
            if (i != -1) {
               var chk= selectedItemsSync.splice(i, 1);
            }
        }
    }

    function addItemTax(){
        selectedItemsSync = [];
        $('.columnSelected:checked').each(function(){
            var thisVal = $(this).val();
            selectedItemsSync.push(thisVal);
        });
       var taxCalculationformulaID= $('#taxCalculationformulaID_item').val();
       var taxType= $('#taxCalculationtype_item').val();

            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'selectedItems':selectedItemsSync,'taxCalculationformulaID':taxCalculationformulaID,'taxType':taxType,'selectedItemsnotSync':selectedItemsnotSync},
                url :"<?php echo site_url('TaxCalculationGroup/update_item_taxid'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    myAlert(data[0],data[1])
                },error : function(){
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });


    }




</script>