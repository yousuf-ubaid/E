<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('srm_supplier_master');
echo head_page($title, false);

/*echo head_page('Supplier Master', false);*/
$this->load->helper('srm_helper');
$supplier_arr = all_srm_supplier_drop();
$currncy_arr = all_srm_supplie_Currency_drop();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
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

    .alpha-box {
        font-size: 14px;
        line-height: 25px;
        list-style: none outside none;
        margin: 0 0 0 12px;
        padding: 0 0 0;
        text-align: center;
        text-transform: uppercase;
        width: 24px;
    }

    ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    .alpha-box li a {
        text-decoration: none;
        color: #555;
        padding: 4px 8px 4px 8px;
    }

    .alpha-box li a.selected {
        color: #fff;
        font-weight: bold;
        background-color: #4b8cf7;
    }

    .alpha-box li a:hover {
        color: #000;
        font-weight: bold;
        background-color: #ddd;
    }
</style>
<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right size-sm"
                onclick="fetchPage('system/srm/supplier/srm_create_supplier',null,'<?php echo $this->lang->line('srm_add_new_supplier');?>','SUP');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('srm_new_supplier');?><!--New Supplier-->
        </button><!--Add New Supplier-->

        <button type="button" data-text="Sync" id="btnSync_fromErp" class="btn button-royal btn-default size-sm mr-1 "
                onclick=""><i class="fa fa-level-down" aria-hidden="true"></i> Pull From ERP
        </button>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-sm-4" style="margin-left: 2%;">
                    <div class="box-tools">
                        <div class="has-feedback">
                            <input name="searchTask" type="text" class="form-control input-sm"
                                   placeholder="<?php echo $this->lang->line('srm_search_suppliers');?>"
                                   id="searchTask" onkeypress="startMasterSearch()">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span><!--Search Suppliers-->
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <?php echo form_dropdown('status', array('-1' =>  $this->lang->line('srm_select')/*'Select'*/, '1' =>  $this->lang->line('common_active')/*'Active'*/, '0' =>  $this->lang->line('srm_not_active')/*'Not Active'*/), '', 'class="form-control" id="filter_status" onchange="startMasterSearch()"'); ?>
                </div>
                <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-sm-11">
                    <div id="outputSupplierMasterTbl"></div>
                </div>
                <div class="col-sm-1">
                    <ul class="alpha-box">
                        <li><a href="#" class="suppliersorting selected" id="sorting_1"
                               onclick="load_supplier_filter('#',1)">#</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_2"
                               onclick="load_supplier_filter('A',2)">A</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_3"
                               onclick="load_supplier_filter('B',3)">B</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_4"
                               onclick="load_supplier_filter('C',4)">C</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_5"
                               onclick="load_supplier_filter('D',5)">D</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_6"
                               onclick="load_supplier_filter('E',6)">E</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_7"
                               onclick="load_supplier_filter('F',7)">F</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_8"
                               onclick="load_supplier_filter('G',8)">G</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_9"
                               onclick="load_supplier_filter('H',9)">H</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_10"
                               onclick="load_supplier_filter('I',10)">I</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_11"
                               onclick="load_supplier_filter('J',11)">J</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_12"
                               onclick="load_supplier_filter('K',12)">K</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_13"
                               onclick="load_supplier_filter('L',13)">L</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_14"
                               onclick="load_supplier_filter('M',14)">M</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_15"
                               onclick="load_supplier_filter('N',15)">N</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_16"
                               onclick="load_supplier_filter('O',16)">O</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_17"
                               onclick="load_supplier_filter('P',17)">P</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_18"
                               onclick="load_supplier_filter('Q',18)">Q</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_19"
                               onclick="load_supplier_filter('R',19)">R</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_20"
                               onclick="load_supplier_filter('S',20)">S</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_21"
                               onclick="load_supplier_filter('T',21)">T</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_22"
                               onclick="load_supplier_filter('U',22)">U</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_23"
                               onclick="load_supplier_filter('V',23)">V</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_24"
                               onclick="load_supplier_filter('W',24)">W</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_25"
                               onclick="load_supplier_filter('X',25)">X</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_26"
                               onclick="load_supplier_filter('Y',26)">Y</a></li>
                        <li><a href="#" class="suppliersorting" id="sorting_27"
                               onclick="load_supplier_filter('Z',27)">Z</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade in" id="modal_viewSupplier" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('srm_invoice_approval');?></h4><!--Invoice Approval-->
            </div>
            <form class="form-horizontal bv-form" id="srm_suplier_search" novalidate="novalidate">
                <button type="submit" class="bv-hidden-submit" style="display: none; width: 0px; height: 0px;"></button>
                <div class="modal-body">
                    <div id="modal_body_viewSupplier"></div>

                </div>

                <div class="modal-footer">
                </div>

            </form>
        </div>
    </div>
</div>


<div class="modal fade in" id="modal_viewitem" role="dialog" aria-labelledby="myModalLabe2" aria-hidden="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('srm_supplier_items');?></h4><!--Supplier Items-->
            </div>
            <form class="form-horizontal bv-form" id="model_form_suplieritem" novalidate="novalidate">

                <div class="modal-body">
                    <form class="form-horizontal">
                        <input type="hidden" id="srm_subpplierID" value="">
                        <fieldset>


                            <button type="button" id="" name="" class="btn btn-primary btn-sm" style="float:right;"
                                    onclick="load_supplier_itemsmaster();"><i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('common_add_item');?>
                            </button> <!--Add item-->
                            <div class="form-group">

                                <div class="col-md-4">


                                </div>
                            </div>

                        </fieldset>
                    </form>


                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                        <tr>
                            <!--                            <td>#</td>-->
                            <td><?php echo $this->lang->line('srm_main_category');?></td><!--Main Category-->
                            <td><?php echo $this->lang->line('srm_sub_category');?></td><!--Sub Category-->
                            <td><?php echo $this->lang->line('common_description');?></td><!--Description-->
                            <td><?php echo $this->lang->line('srm_item_code');?></td><!--Item Code-->
                            <td><?php echo $this->lang->line('srm_secondary_code');?></td><!--Secondary Code-->
                            <td><?php echo $this->lang->line('common_status');?></td><!--Status-->
                            <td>&nbsp;</td>
                        </tr>
                        </thead>
                        <tbody id="tableBody_subpplierItem"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade in" id="modal_itemmaster" role="dialog" aria-labelledby="myModalLabe3" aria-hidden="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('srm_items_search');?></h4><!--Items Search-->
            </div>


            <div class="modal-body">

                <form class="form-inline">

                    <div class="pb-10">Search Item: <input id="keyword_itemDescription" name="itemID" type="text"
                                             placeholder="search"
                                             onkeyup="load_item_master();">
                        &nbsp;&nbsp;&nbsp;
                        <button type="button" onclick="clearmaste_search()"  class="btn btn-default"><?php echo $this->lang->line('common_clear');?>
                        </button><!--Clear-->


                    </div>

                </form>
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                    <tr>
                        <td><?php echo $this->lang->line('srm_main_category');?></td><!--Main Catego-->
                        <td><?php echo $this->lang->line('srm_sub_category');?></td><!--Sub Category-->
                        <td><?php echo $this->lang->line('srm_system_code');?></td><!--System Code-->
                        <td><?php echo $this->lang->line('srm_secondary_code');?></td><!--Secondary Code-->
                        <td><?php echo $this->lang->line('common_description');?></td><!--Description-->
                        <td></td>
                    </tr>
                    </thead>
                    <tbody id="tableBody_Itemmaster"></tbody>
                </table>
            </div>


        </div>
        <div class="modal-footer">
        </div>

    </div>
</div>



<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Crew From ERP"
     id="linksupplierFromERP">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Supplier from ERP </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="mfqCustomerAutoID">
                <div id="sysnc">
                    <div class="table-responsive">
                        <table id="link_supplier_table_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">&nbsp;</th>
                                <th >Name</abbr></th>
                                <th >Address</th>
                                <th >Country</th>
                                <th >Telephone</th>
                                <th >@Email</th>
                                <th style="min-width: 5%; text-align: center !important;">
                                    <button type="button" data-text="Add" onclick="addSupplier()"
                                            class="btn btn-sm btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i> Add Supplier
                                    </button>
                                </th>
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
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    var oTable2;
    var selectedItemsSync = [];
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/srm/srm_suppliermaster', '', 'Supplier Master');
        });
        load_supplier_filter('#', 1);
        //load_supplierMasterTable();


        $("#btnSync_fromErp").click(function () {
            sync_supplier_table();
            $("#linksupplierFromERP").modal('show');
        });

    });

    function load_supplierMasterTable(filtervalue) {
        var searchTask = $('#searchTask').val();
        var status = $('#filter_status').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'searchTask': searchTask, 'filtervalue': filtervalue, status: status},
            url: "<?php echo site_url('srm_master/fetch_supplier_all'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#outputSupplierMasterTbl').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_supplier(id) {
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
                    data: {'supplierID': id},
                    url: "<?php echo site_url('srm_master/delete_supplier'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        load_supplierMasterTable();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        load_supplierMasterTable();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.suppliersorting').removeClass('selected');
        $('#searchTask').val('');
        $('#filter_status').val(-1);
        $('#sorting_1').addClass('selected');
        load_supplierMasterTable();
    }

    function load_supplier_filter(value, id) {
        $('.suppliersorting').removeClass('selected');
        $('#sorting_' + id).addClass('selected');
        if (value != '#') {
            $('#search_cancel').removeClass('hide');
        }
        load_supplierMasterTable(value)
    }

    function load_supplier_items_details(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'supplierID': id},
            url: "<?php echo site_url('srm_master/load_supplier_items_details'); ?>",
            beforeSend: function () {
                startLoad();
                $("#modal_viewitem").modal('show');
                $("#tableBody_subpplierItem").empty();
            },
            success: function (data) {
                stopLoad();
                $("#srm_subpplierID").val(id);
                if (jQuery.isEmptyObject(data)) {
                    $('#tableBody_subpplierItem').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                } else {
                    $.each(data, function (val, text) {
                        /*text['itemDescription']*/

                        var del = '<i class="fa fa-trash" onclick="delete_supplier_item(' + text['supplierItemID'] + ')"></i>';
                        if (text['isActive'] == 1) {
                            var active = '<span class="label label-success">&nbsp;</span>';
                        } else {
                            var active = '<span class="label label-success">&nbsp;</span>';
                        }

                        $("#tableBody_subpplierItem").append('<tr id="row_' + text['supplierItemID'] + '"><td >' + text['mainCategory'] + '</td><td >' + text['catDescription'] + '</td><td>' + text['itemDescription'] + '</td><td>' + text['itemSystemCode'] + '</td><td>' + text['seconeryItemCode'] + '</td><td style="text-align: center">' + active + '</td><td style="text-align: center">' + del + '</td></tr>');

                    });
                }

                //$("#myModalLabe2").html(data);

            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function load_supplier_itemsmaster() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {supplierID: $("#srm_subpplierID").val()},
            url: "<?php echo site_url('srm_master/load_supplier_itemsmaster'); ?>",
            beforeSend: function () {
                startLoad();
                $("#modal_itemmaster").modal('show');
                $("#tableBody_Itemmaster").empty();
            },
            success: function (data) {
                stopLoad();
                $("#tableBody_Itemmaster").empty();
                $.each(data, function (val, text) {
                    if (text['supplierItemID'] > 0) {
                        $("#tableBody_Itemmaster").append('<tr><td>' + text['mainCategory'] + '</td><td>' + text['catDescription'] + '</td><td >' + text['itemSystemCode'] + '</td><td>' + text['seconeryItemCode'] + '</td><td>' + text['itemDescription'] + '</td><td><button class="btn btn-xs btn-default" id="save_itm_btn" type="submit" disabled onclick="save_supplierItem(' + text['itemAutoID'] + ');"><?php echo $this->lang->line('srm_added');?></button></td></tr>');<!--Added-->
                    } else {
                        $("#tableBody_Itemmaster").append('<tr><td>' + text['mainCategory'] + '</td><td>' + text['catDescription'] + '</td><td >' + text['itemSystemCode'] + '</td><td>' + text['seconeryItemCode'] + '</td><td>' + text['itemDescription'] + '</td><td><button class="btn btn-xs btn-primary" id="save_itm_btn" type="submit" onclick="save_supplierItem(' + text['itemAutoID'] + ');"><?php echo $this->lang->line('common_add');?></button></td></tr>');<!--Add-->
                    }
                });
                //$("#myModalLabe2").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function save_supplierItem(itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID, 'supplierAutoID': $('#srm_subpplierID').val()},
            url: "<?php echo site_url('srm_master/save_supplierItem'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                load_supplier_itemsmaster();
                if (data["error"] == 1) {
                    myAlert('e', data["message"]);
                } else if (data['error'] == 0) {
                    load_supplier_items_details(data['code']);
                    myAlert('s', data["message"]);
                }
            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_supplier_item(id) {
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
                    data: {'supplierItemID': id},
                    url: "<?php echo site_url('srm_master/delete_supplier_item'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data["error"] == 1) {
                            myAlert('e', data["message"]);
                        } else if (data['error'] == 0) {
                            $('#row_' + id).hide();
                            myAlert('s', data["message"]);
                        }

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_item_master() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'supplierID': $("#srm_subpplierID").val(), 'keyword': $("#keyword_itemDescription").val()},
            url: "<?php echo site_url('srm_master/load_supplier_itemsmaster'); ?>",
            beforeSend: function () {
                //startLoad();
//                $("#modal_itemmaster").modal('show')
                $("#tableBody_Itemmaster").empty();
            },
            success: function (data) {
                //stopLoad();
                $("#tableBody_Itemmaster").empty();
                $.each(data, function (val, text) {
                    if (text['supplierItemID'] > 0) {
                        $("#tableBody_Itemmaster").append('<tr><td >' + text['mainCategory'] + '</td><td>' + text['catDescription'] + '</td><td>' + text['itemSystemCode'] + '</td><td>' + text['seconeryItemCode'] + '</td><td>' + text['itemDescription'] + '</td><td><button class="btn btn-xs btn-default" id="save_itm_btn" type="submit" disabled onclick="save_supplierItem(' + text['itemAutoID'] + ');"><?php echo $this->lang->line('srm_added');?></button></td></tr>');<!--Added-->
                    } else {
                        $("#tableBody_Itemmaster").append('<tr><td >' + text['mainCategory'] + '</td><td>' + text['catDescription'] + '</td><td>' + text['itemSystemCode'] + '</td><td>' + text['seconeryItemCode'] + '</td><td>' + text['itemDescription'] + '</td><td><button class="btn btn-xs btn-primary" id="save_itm_btn" type="submit" onclick="save_supplierItem(' + text['itemAutoID'] + ');"><?php echo $this->lang->line('common_add');?></button></td></tr>');<!--Add-->
                    }
                });


                //$("#myModalLabe2").html(data);

            }, error: function (jqXHR, textStatus, errorThrown) {
                //stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function clearmaste_search(){
        $('#keyword_itemDescription').val('');
        load_item_master();
    }


    function sync_supplier_table() {
        oTable2 = $('#link_supplier_table_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('srm_master/fetch_sync_supplier'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {paginate: {previous: '‹‹', next: '››'}},

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
                {"mData": "supplierAutoID"},
                {"mData": "supplierName"},
                {"mData": "supplierAddress1"},
                {"mData": "countryDiv"},
                {"mData": "supplierTelephone"},
                {"mData": "supplierEmail"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
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

    function addSupplier() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("srm_master/add_suppliers"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync},
            async: false,
            success: function (data) {
                if (data['status']) {
                    refreshNotifications(true);
                    load_supplierMasterTable();

                    sync_supplier_table();
                    selectedItemsSync = [];
                } else {
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


</script>