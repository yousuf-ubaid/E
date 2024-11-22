<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$main_category_arr = all_main_category_drop();
$uom_arr = all_umo_new_drop();
$seg_p = fetch_segment_v2();
?>
<style>
    .width100p {
        width: 100%;
    }

    .user-table {
        width: 100%;
    }

    .bottom10 {
        margin-bottom: 10px !important;
    }

    .btn-toolbar {
        margin-top: -2px;
    }

    table {
        max-width: 100%;
        background-color: transparent;
        border-collapse: collapse;
        border-spacing: 0;
    }
    /* .flex {
         display:;
} */
</style>


<div class="row">

    <div class="width100p">
        <section class="past-posts">
            <div class="posts-holder settings">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">
                            <i class="fa fa-building" aria-hidden="true"></i> Products
                        </div><!--Product Management-->
                        <div class="btn-toolbar btn-toolbar-small pull-right">
                            <button class="btn btn-primary btn-xs bottom10" data-toggle="modal"
                                   onclick="product_modal();">Add Product
                            </button>
                        </div>
                    </div>



                    <div class="post-area">
                        <article class="page-content">

                            <div class="system-settings">
                                <div class="table-responsive">
                                <table id="usersTable" class="table ">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                                        <th>Segment</th>
                                        <th>UOM </th><!--Description-->

                                        <!--<th>Subscription Amount</th>--><!--Description-->
                                        <!--<th>Implementation Amount </th>--><!--Description-->
                                        <!--<th>Other Amount </th>--><!--Description-->
                                        <th>Link Item </th><!--Description-->
                                        <th></th>
                                    </tr>
                                    </thead>
                                </table>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </div>

<!-- Modal -->
<div id="add-user-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('crm_add_new_user');?> </h4><!--Add New User-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="crm_employee">


                        <!-- Select Basic -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="selectbasic"><?php echo $this->lang->line('crm_select_an_employee');?> </label><!--Select an Employee-->
                            <div class="col-md-6" id="div_loaduser">


                            </div>
                        </div>

                        <!-- Button -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="singlebutton"></label>
                            <div class="col-md-4">
                                <button type="button" id="singlebutton" onclick="submitusers()" name="singlebutton" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_submit');?> </button><!--Submit-->
                            </div>
                        </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
            </div>
        </div>

    </div>
</div>

    <div id="add-product-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="product_title">Add Products</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="crm_product">
                        <input type="hidden" id="productid" name="productid">
                        <div class="row"  style="margin-top: 10px;">
                            <div class="form-group col-sm-4 col-md-offset-1">
                                <label class="title">Product</label>
                            </div>
                            <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                    <input type="text" class="form-control " id="product" name="product" placeholder="Product" required>
                    <span class="input-req-inner"></span>
                            </span>
                            </div>
                        </div>
                        <div class="row"  style="margin-top: 10px;">
                            <div class="form-group col-sm-4 col-md-offset-1">
                                <label class="title">Unit of Measure</label>
                            </div>
                            <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                   <?php echo form_dropdown('defaultUnitOfMeasureID', $uom_arr, 'Each', 'class="form-control select2" id="defaultUnitOfMeasureID" required'); ?>
                    <span class="input-req-inner"></span>
                            </span>
                            </div>
                        </div>

                        <div class="row"  style="margin-top: 10px;">
                            <div class="form-group col-sm-4 col-md-offset-1">
                                <label class="title">Segment</label>
                            </div>
                            <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                   <?php echo form_dropdown('segmentID', $seg_p, 'Each', 'class="form-control select2" id="segmentID" required'); ?>
                    <span class="input-req-inner"></span>
                            </span>
                            </div>
                        </div>

                        <!--

                        <div class="row"  style="margin-top: 10px;">
                            <div class="form-group col-sm-4 col-md-offset-1">
                                <label class="title">Subscription Amount</label>
                            </div>
                            <div class="form-group col-sm-6">

                    <input type="text" class="form-control " id="subscriptionamount" name="subscriptionamount" placeholder="Subscription Amount">

                            </span>
                            </div>
                        </div>
                        <div class="row"  style="margin-top: 10px;">
                            <div class="form-group col-sm-4 col-md-offset-1">
                                <label class="title">Implementation Amount</label>
                            </div>
                            <div class="form-group col-sm-6">
                    <input type="text" class="form-control " id="implementationamount" name="implementationamount" placeholder="Implementation Amount">

                            </span>
                            </div>
                        </div>
                        <div class="row"  style="margin-top: 10px;">
                            <div class="form-group col-sm-4 col-md-offset-1">
                                <label class="title">Other Amount</label>
                            </div>
                            <div class="form-group col-sm-6">

                    <input type="text" class="form-control " id="otheramount" name="otheramount" placeholder="Other Amount">

                            </span>
                            </div>
                        </div> -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary" onclick="submitLeadStatus();"><span class="glyphicon glyphicon-floppy-disk"
                                                                                                             aria-hidden="true"></span> Save
                    </button>
                </div>
            </div>

        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Item Master From ERP"
         id="LinkitemMasterFromERP">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Link from ERP</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ProductID">
                    <div id="sysnc">
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <!--<label>Main Category</label>-->
                                <?php echo form_dropdown('linkmainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="linkmainCategoryID" onchange="LoadMainCategoryLink()"'); ?>
                            </div>
                            <div class="form-group col-sm-3">
                                <!--<label>Sub Category</label>-->
                                <select name="linksubcategoryID" id="linksubcategoryID" class="form-control searchbox"
                                        onchange="link_item_table()">
                                    <option value="">Select Category</option>
                                </select>
                            </div>

                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table id="item_table_link" class="table table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">&nbsp;</th>
                                    <th style="min-width: 12%">Main <abbr title="Category"> Cat..</abbr></th>
                                    <th style="min-width: 12%">Sub <abbr title="Category"> Cat..</abbr></th>
                                    <th style="min-width: 25%">&nbsp;</th>
                                    <th style="min-width: 10%"><abbr title="Secondary Code">Code</abbr></th>
                                    <th style="min-width: 10%"><abbr title="Current Stock"> Curr.&nbsp;Stock</abbr></th>
                                    <!--<th style="min-width: 10%"><abbr title="Weighted Average Cost"> WAC </abbr></th>-->
                                    <th style="min-width: 5%; text-align: center !important;">
                                        <button type="button" data-text="Add" onclick="linkItem()"
                                                class="btn btn-xs btn-primary">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Add Items
                                        </button>
                                    </th>
                                    <th> </th>
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

<script>
    $(document).ready(function () {
        $('.select2').select2();
    });

    fetch_users();
    link_item_table();
    var selectedItemsSync = [];
    function product_modal()
    {
        $('#product_status_heading').text('Add Products');
        $('#crm_product')[0].reset();
        $('#crm_product').bootstrapValidator('resetForm', true);
        $('#product_title').html('Add Products')
        $('#product').val('');
        $('#productid').val('');
        $('#add-product-modal').modal('show');
    }

    function deleteproduct(productID){
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
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'productID':productID},
                    url :"<?php echo site_url('Crm/delete_product'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        refreshNotifications(true);
                        stopLoad();
                        myAlert('s','Deleted Successfully');
                        fetch_users();

                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }



    function fetch_users() {
        var Otable = $('#usersTable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Crm/fetch_product'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
                $('.xeditable').editable();
            },

                "columnDefs": [
                {"width": "2%", "searchable": false, "targets": 0},
                {"width": "7%", "targets": 1},
                {"width": "1%", "targets": 2},
            ],
            "aoColumns": [
                {"mData": "productID"},
                {"mData": "productName"},
                {"mData": "segmentID"},                
                {"mData": "UOMshort"},
                /*{"mData": "subscriptionAmountalign"},
                {"mData": "ImplementationAmountalign"},
                {"mData": "otherAmountAmountalign"},*/
                {"mData": "Itemdescription"},
                {"mData": "edit"}

            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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


    function submitLeadStatus() {
        var data = $('#crm_product').serializeArray();
        data.push({'name': 'uom', 'value': $('#defaultUnitOfMeasureID option:selected').text()});
        var product = $('#product').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('crm/srp_erp_save_product'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
              myAlert(data[0],data[1]);
                if(data[0]=='s')
                {
                    $('#product').val('');
                    $('#add-product-modal').modal('hide');
                    fetch_users();

                }

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function editproducts(productID) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {productID: productID},
            url: "<?php echo site_url('Crm/fetch_product_details'); ?>",
            beforeSend: function () {
                startLoad();
                $('#product_status_heading').text('Edit Add Products');
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#crm_product').bootstrapValidator('resetForm', true);
                    $('#product').val(data['productName']);
                    $('#productid').val(data['productID']);
                    $('#subscriptionamount').val(data['subscriptionAmount']);
                    $('#implementationamount').val(data['ImplementationAmount']);
                    $('#defaultUnitOfMeasureID').val(data['uomID']).change();
                    $('#otheramount').val(data['otherAmount']);
                    $('#add-product-modal').modal('show');
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }
    function link_item_table() {
        oTable2 = $('#item_table_link').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Crm/fetch_link_item'); ?>",
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
                {"mData": "disitem"},
            ],
            "columnDefs": [{
                "visible": false,
                "searchable": true,
                "targets": [7,8,9],
            }],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#linkmainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#linksubcategoryID").val()});
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
    function link_item_master(id) {
        $('#ProductID').val(id);
        $("#LinkitemMasterFromERP").modal('show');
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

    function load_sub_cat_link(select_val) {
        $('#linksubcategoryID').val("");
        $('#linksubcategoryID option').remove();
        var subid = $('#linkmainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#linksubcategoryID').empty();
                    var mySelect = $('#linksubcategoryID');
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
    function LoadMainCategoryLink() {
        $('#linksubcategoryID').val("");
        $('#linksubcategoryID option').remove();
        load_sub_cat_link();
        link_item_table();
    }
    function linkItem() {
        var selectedVal = $("input:radio.radioChk:checked");
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Crm/link_item"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedVal.val(),ProductID:$('#ProductID').val()},
            async: false,
            success: function (data) {
                if (data['status']) {
                    refreshNotifications(true);
                    link_item_table();
                    fetch_users();
                    $("#LinkitemMasterFromERP").modal('hide');
                } else {
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
</script>