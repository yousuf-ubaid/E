<?php echo head_page('Item Master', TRUE);
$this->load->helper('buyback_helper');
$main_category_arr = all_main_category_drop();
$itemTypes_arr = load_buyBack_itemTypes();
$feedTypes_arr = fetch_buyback_feedTypes();
$key = array_filter($main_category_arr, function ($a) {
    return $a == 'FA | Fixed Assets';
});
unset($main_category_arr[key($key)])
?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/pagination/styles.css'); ?>" class="employee_master_styles">
<div id="filter-panel" class="collapse filter-panel">
    <div class="form-group col-sm-3">
            <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control select2" id="filter_mainCategoryID" onchange="getItemMaster_tableView(),item_table(),LoadMainCategory(),startMasterSearch()"'); ?>
        </div>
        <div class="form-group col-sm-3 hidden">
            <select name="subcategoryID" id="subcategoryID" class="form-control searchbox"
                    onchange="getItemMaster_tableView(), item_table(), startMasterSearch()">
                <option value="">Select Category</option>
            </select>
        </div>
        <div class="form-group col-sm-3">
            <?php echo form_dropdown('buybackItemType', $itemTypes_arr, 'Each', 'class="form-control" id="filter_buybackItemType" onchange="getItemMaster_tableView(),item_table(), startMasterSearch()"'); ?>
        </div>
    <br>
</div>
<div class="row">
    <div class="col-md-12 text-right" style="margin-top: 10px;">
        <div class="col-sm-4" style="">
            <div class="box-tools">
                <div class="has-feedback">
                    <input name="searchTask" type="text" class="form-control input-sm"
                           placeholder="Search..." autocomplete="off"
                           id="searchTask" onkeypress="startMasterSearch()">
                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
            </div>

        </div>
        <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
        </div>


        <div class="pull-right">
            <button type="button" data-text="Sync" id="btnSync_fromErp" class="btn button-royal "
                    onclick=""><i class="fa fa-level-down" aria-hidden="true"></i> Pull Item from ERP
            </button>
        </div>
    </div>
</div>
<div id="itemmaster" class="">
    <div class="row">
    </div>
    <hr>
    <div id="itemMasterTableData"></div>
    <div class="col-xs-12" style="padding-right: 5px;">
        <div class="pagination-content clearfix" id="emp-master-pagination" style="padding-top: 10px">
            <p id="filterDisplay"></p>

            <nav>
                <ul class="list-inline" id="pagination-ul">

                </ul>
            </nav>
        </div>
    </div>
    <div class="hidden table-responsive" id="itemMasterTbl">
        <table id="item_table" class="table table-striped table-condensed">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%">Main Category</th>
                <th style="min-width: 10%">Sub Category</th>
                <th style="min-width: 30%">Description</th>
                <th style="min-width: 15%">Secondary Code</th>
                <th style="min-width: 15%">Item Type</th>
                <th style="min-width: 15%">Feed Type</th>
                <th style="min-width: 15%">Current Stock</th>
                <th style="min-width: 50px">&nbsp;</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<div class="row col-sm-12">
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal" role="dialog" aria-labelledby="Item Master From ERP" id="itemMasterFromERP">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Items from ERP</h4>
            </div>
            <div class="modal-body">
                <div id="sysnc">
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <!--<label>Main Category</label>-->
                            <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="syncMainCategoryID" onchange="startItemSearch(), LoadMainCategorySync()"'); ?>
                        </div>
                        <div class="form-group col-sm-3">
                            <!--<label>Sub Category</label>-->
                         <!--   <select name="subcategoryID" id="subcategoryID" class="form-control searchbox"
                                    onchange="getItemMaster_tableView(), item_table(), startMasterSearch()">
                                <option value="">Select Category</option>
                            </select>-->
                            <select name="subcategoryID" id="syncSubcategoryID" class="form-control searchbox"
                                    onchange="startItemSearch()">
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div class="col-sm-1 hide" id="search_item_cancel">
                    <span class="tipped-top"><a id="cancelItemSearch" href="#" onclick="clearItemSearchFilter()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="item_table_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">&nbsp;</th>
                                <th style="min-width: 12%">Main <abbr title="Category"> Cat..</abbr></th>
                                <th style="min-width: 12%">Sub <abbr title="Category"> Cat..</abbr></th>
                                <th style="min-width: 25%">Item Description &nbsp;</th>
                                <th style="min-width: 10%"><abbr title="Secondary Code">Code</abbr></th>
                                <th style="min-width: 10%"><abbr title="Current Stock"> Curr.&nbsp;Stock</abbr></th>
                                <!--<th style="min-width: 10%"><abbr title="Weighted Average Cost"> WAC </abbr></th>-->
                                <th style="min-width: 5%; text-align: center !important;">
                                    <div class="row">
                                        <button type="button" data-text="Add" onclick="addItem()"
                                                class="btn btn-xs btn-primary">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Add Items
                                        </button>
                                        <!--<div class="skin-section extraColumnsgreen">-->
                                        <input id="isActive" type="checkbox" data-caption="" class="columnSelected" name="isActive"  onclick="sync_item_table(this)">
                                        <!--</div>-->
                                    </div>
                                </th>
                                <th> </th>
                                <th> </th>
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
<div class="modal" role="dialog" aria-labelledby="Edit Buyback Item Master" id="buyback_itemMasterEdit">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Buyback Item Type</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id=""'); ?>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title">Type</label>
                    </div>
                    <div class="form-group col-sm-8">
                        <?php echo form_dropdown('buybackItemType', $itemTypes_arr, '', 'class="form-control" id="buybackItemType" onchange="checkFeedType(this.value)"  required'); ?>
                        <input type="hidden" name="buybackItemID" id="itemType_buybackItemID">
                    </div>
                </div>
                <div class="row hide" style="margin-top: 10px;" id="div_feedType">
                    <div class="form-group col-sm-3">
                        <label class="title">Feed Type</label>
                    </div>
                    <div class="form-group col-sm-8">
                        <?php echo form_dropdown('feedType', $feedTypes_arr, '', 'class="form-control" id="feedType" '); ?>
                        <input type="hidden" name="buybackItemID" id="itemType_buybackItemID">
                    </div>
                </div>
            </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" onclick="buyback_itemType()">Update</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    function add_mainCategory(id, title) {
        $("#frm_itemAutoID").val(id);
        $("#frm_subCategory").empty();
        $("#frm_subSubCategory").empty();
        $("#subItemCategoryModal").modal('show');
        $("#modal_title_category").html(title);
        $("#categoryID").val(-1);
    }

    var per_page = 10;
    var oTable;
    var oTable2;
    var selectedItemsSync = [];
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/buyback/item_master', '', 'Item Master')
        });

       // item_table();
        getItemMaster_tableView();
        sync_item_table();

        $("#btnSync_fromErp").click(function () {
            $('#syncMainCategoryID').val('').change();
            $('#isActive').iCheck('uncheck');
            $("#itemMasterFromERP").modal('show');
        });

        $('.select2').select2();
    });

    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        var filtervalue = '#';
        getItemMaster_tableView(data_pagination, uriSegment);
    }

    function clearSearchFilter() {
        $('#filter_mainCategoryID').val('').change();
        $('#filter_buybackItemType').val('').change();
        $('#searchTask').val('');
        $('#search_cancel').addClass('hide');
        getItemMaster_tableView();
    }

    $('#searchTask').bind('input', function () {
        startMasterSearch();
    });

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getItemMaster_tableView();
    }

    function clearItemSearchFilter() {
        $('#syncMainCategoryID').val('').change();
        $('#syncSubcategoryID').val('');
        $('#searchItemTask').val('');
        $('#search_item_cancel').addClass('hide');
        sync_item_table();
    }

    $('#searchItemTask').bind('input', function () {
        startItemSearch();
    });

    function startItemSearch() {
        $('#search_item_cancel').removeClass('hide');
        sync_item_table();
    }

    function getItemMaster_tableView(pageID,uriSegment = 0) {
        var searchTask = $('#searchTask').val();
        var mainCategory = $('#filter_mainCategoryID').val();
        var subcategory = $('#subcategoryID').val();
        var buybackItemType = $('#filter_buybackItemType').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {mainCategory: mainCategory, subcategory: subcategory, buybackItemType: buybackItemType, searchTask: searchTask,'pageID':pageID},
            url: "<?php echo site_url('Buyback/loadItemMaster_table'); ?>/" + uriSegment,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#itemMasterTableData').html(data['view']);
                $('#pagination-ul').html(data.pagination);
                $('#filterDisplay').html(data.filterDisplay);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function LoadMainCategory() {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        load_sub_cat();
        item_table();
        getItemMaster_tableView();
    }

    function LoadMainCategorySync() {
        $('#syncSubcategoryID').val("");
        $('#syncSubcategoryID option').remove();
        load_sub_cat_sync();
        sync_item_table();
    }

    function item_table() {
        oTable = $('#item_table').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Buyback/fetch_buyback_item'); ?>",
            "aaSorting": [[0, 'desc']],
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
                $("[rel='tooltip']").tooltip();
            },

            "aoColumns": [
                {"mData": "buybackItemID"},
                {"mData": "mainCategory"},
                {"mData": "SubCategoryDescription"},
                {"mData": "itemName"},
                {"mData": "secondaryItemCode"},
                {"mData": "BuybackItemType"},
                {"mData": "feedName"},
                {"mData": "CurrentStock"},
                //{"mData": "item_inventryCode"},
                //{"mData": "secondaryItemCode"},
                // {"mData": "CurrentStock"},
                /*{"mData": "TotalWacAmount"},*/
                /*{"mData": "confirmed"},*/
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#filter_mainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#subcategoryID").val()});
                aoData.push({"name": "buybackItemType", "value": $("#filter_buybackItemType").val()});
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

    function sync_item_table() {
        oTable2 = $('#item_table_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Buyback/fetch_sync_item'); ?>",
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
                // $('#isActive').iCheck('uncheck');
                $('.item-iCheck').iCheck('uncheck');
                if (selectedItemsSync.length > 0) {
                    $.each(selectedItemsSync, function (index, value) {
                        $("#selectItem_" + value).iCheck('check');
                        // $("#selectItem_" + value).prop("checked", true);
                    });
                }

                if ($('#isActive').is(":checked")){
                    $('.item-iCheck').iCheck('check');
                    selectedItemsSync = [];
                    $('.columnSelected').each(function () {
                        var id = $(this).val();
                        if(id != 'on'){
                            selectedItemsSync.push(id);
                        }
                    });

                } else{
                    $('.item-iCheck').iCheck('uncheck');
                    selectedItemsSync = [];
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
                {"mData": "itemDescription"}
            ],
            "columnDefs": [{
                "visible": false,
                "searchable": true,
                "targets": [7, 8],
            }],
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

    function edit_buyback_itemMaster(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'buybackItemID': id},
            url: "<?php echo site_url('Buyback/fetch_buyback_item_masterEdit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $("#itemType_buybackItemID").val(data['buybackItemID']);
                    $('#buybackItemType').val(data['buybackItemType']);
                    if(data['buybackItemType'] == 2){
                        $('#div_feedType').removeClass('hide');
                        $('#feedType').val(data['feedType']);
                    }else{
                        $('#div_feedType').addClass('hide');
                    }
                    $("#buyback_itemMasterEdit").modal({backdrop: "static"});
                }
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }

    function delete_item_master(id) {
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
                    data: {'buybackItemID': id},
                    url: "<?php echo site_url('Buyback/delete_buyback_item_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert('s', 'Item Deleted Successfully');
                        item_table();
                        getItemMaster_tableView();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_sub_cat(select_val) {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        var subid = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option></option>').val('').html('Sub Category'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });

                    $("#subcategoryID").select2();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function load_sub_cat_sync() {
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

    function allItemSelectedSync(val){
        if(val == 1){
            $('.itemAutoID').each(function () {
                var $spy = $(this);
                alert($spy);
            });
            alert('ryu');
              /*  $('.item-iCheck').each(function () {
                var $spy = $(this).val();
                alert($spy);
                //data.push({'name': 'uom[]', 'value': $(this).text()})
                selectedItemsSync.push($spy);
            });*/
        }
    }

    function addItem() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Buyback/add_item"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync},
            async: false,
            success: function (data) {
                if (data['status']) {
                    refreshNotifications(true);
                    sync_item_table();
                    item_table();
                    getItemMaster_tableView();
                    selectedItemsSync = [];
                } else {
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function buyback_itemType() {
        var buybackItemType = $('#buybackItemType').val();
        var buybackItemID = $('#itemType_buybackItemID').val();
        var buybackFeedType = $('#feedType').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {buybackItemType: buybackItemType, buybackItemID: buybackItemID, buybackFeedType: buybackFeedType},
            url: "<?php echo site_url('Buyback/buyback_itemType_update'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#buyback_itemMasterEdit').modal('hide');
                    item_table();
                    getItemMaster_tableView();
                } else {
                    $('.btn-wizard').removeClass('disabled');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function checkFeedType(itemType) {
        if (itemType == 2) {
            $('#div_feedType').removeClass('hide');
        } else {
            $('#div_feedType').addClass('hide');
        }
    }
</script>