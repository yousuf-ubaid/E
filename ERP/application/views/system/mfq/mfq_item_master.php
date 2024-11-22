<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_item_master_head');
echo head_page($title, false);
$main_category_arr = all_main_category_drop();
$key = array_filter($main_category_arr, function ($a) {
    return $a == 'FA | Fixed Assets';
});
unset($main_category_arr[key($key)]);

$company = getPolicyValues('LNG', 'All'); 

?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"> -->

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-12 text-right"></div>
</div>
<br>
<div class="pull-right">
    <button type="button" data-text="Add" id="btnAdd" onclick="fetchPage('system/mfq/item-master/manage-item','','Item')" class="btn btn-sm btn-default">
        <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
    </button>
    <button type="button" data-text="Sync" id="btnSync_fromErp" class="btn button-royal">
        <i class="fa fa-level-down" aria-hidden="true"></i>  <?php echo $this->lang->line('manufacturing_pull_item_from_erp'); ?><!--Pull Item from ERP-->
    </button>
    <a href="#" type="button" class="btn btn-success btn-sm pull-right" style="margin-left: 2px" onclick="excel_Export_items()"><i class="fa fa-file-excel-o"></i><?php echo $this->lang->line('common_excel'); ?></a>
</div>
<div id="itemmaster">
    <form id="item_master_filter_form">
        <div class="row">
            <div class="form-group col-sm-3">
                <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="LoadMainCategory()"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <select name="subcategoryID" id="subcategoryID" class="form-control searchbox"
                        onchange="item_table()">
                    <option value=""><?php echo $this->lang->line('manufacturing_select_category'); ?><!--Select Category--></option>
                </select>
            </div>
            <div class="form-group col-sm-3">
                <span class="input-req" title="Required Field">
                    <?php if($company == 'FlowServe') { ?>
                        <select name="itemType" class="form-control" onchange="item_table()" id="itemType" required="">
                            <option value=""><?php echo $this->lang->line('manufacturing_select_item_type'); ?><!--Select Item Type--></option>
                            <option value="1">Parts</option>
                            <option value="3">Repaire / Other</option>
                            <option value="2">Full Service</option>
                        </select>

                    <?php } else{ ?> 
                        <select name="itemType" class="form-control" onchange="item_table()" id="itemType" required="">
                            <option value=""><?php echo $this->lang->line('manufacturing_select_item_type'); ?><!--Select Item Type--></option>
                            <option value="1">Raw Material</option>
                            <option value="3">Semi Finish good</option>
                            <option value="2">Finish good</option>
                        </select>

                    <?php } ?>
                </span>
            </div>
        </div>
    </form>
    <hr>
    <div class="table-responsive" id="itemMasterTbl">
        <table id="item_table" class="table table-striped table-condensed">
            <thead>
            <tr>
                <th style="min-width: 5%">&nbsp;</th>
                <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('manufacturing_main'); ?><!--MAIN--> <abbr title="<?php echo $this->lang->line('manufacturing_category_title'); ?>"> <?php echo $this->lang->line('manufacturing_category'); ?><!--CAT..--></abbr></th>
                <th class="text-uppercase" style="min-width: 11%"><?php echo $this->lang->line('manufacturing_sub'); ?><!--SUB--> <abbr title="<?php echo $this->lang->line('manufacturing_category_title'); ?>"> <?php echo $this->lang->line('manufacturing_category'); ?><!--CAT..--></abbr></th>
                <th class="text-uppercase" style="min-width: 10%"><?php echo $this->lang->line('manufacturing_sub_sub'); ?><!--SUB SUB--> <abbr title="<?php echo $this->lang->line('manufacturing_category_title'); ?>"> <?php echo $this->lang->line('manufacturing_category'); ?><!--CAT..--></abbr></th>
                <th class="text-uppercase" style="min-width: 20%"><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                <th class="text-uppercase" style="min-width: 10%"><abbr title="<?php echo $this->lang->line('manufacturing_secondary_code'); ?>"><?php echo $this->lang->line('common_code'); ?><!--CODE--></abbr></th>
                <th class="text-uppercase" style="min-width: 10%"><abbr title="<?php echo $this->lang->line('manufacturing_current_stock'); ?>"> <?php echo $this->lang->line('manufacturing_current_stock_title'); ?><!--CURR.&nbsp;STOCK--></abbr></th>
                <th class="text-uppercase" style="min-width: 10%"><abbr title="<?php echo $this->lang->line('common_description'); ?>"><?php echo $this->lang->line('manufacturing_link_item'); ?><!--LINK&nbsp;ITEM--></abbr></th>
                <!--<th style="min-width: 10%"><abbr title="Weighted Average Cost"> WAC </abbr></th>-->
                <!--<th style="min-width: 5%"></th>-->
                <th style="min-width: 10%">&nbsp;</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="Item Category Modal"
     id="item_category_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Image Upload </h4>
            </div>
            <div class="modal-body">
                <center>
                    <form id="img_uplode_form">
                        <input type="hidden" id="img_item_id" name="item_id">

                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail" style="width: 250px; height: 150px;">
                                <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="item_img" alt="...">
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail"
                                 style="max-width: 250px; max-height: 150px;"></div>
                            <div>
                        <span class="btn btn-default btn-file">
                            <span class="fileinput-new">Select image</span>
                            <span class="fileinput-exists">Change</span>
                            <input type="file" name="img_file" onchange="img_uplode()">
                        </span>
                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                            </div>
                        </div>
                    </form>
                </center>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Item Master From ERP"
     id="itemMasterFromERP">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('manufacturing_item_from_erp'); ?><!--Items from ERP--> </h4>
            </div>
            <div class="modal-body">
                <div id="sysnc">
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <!--<label>Main Category</label>-->
                            <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="syncMainCategoryID" onchange="LoadMainCategorySync()"'); ?>
                        </div>
                        <div class="form-group col-sm-3">
                            <!--<label>Sub Category</label>-->
                            <select name="subcategoryID" id="syncSubcategoryID" class="form-control searchbox"
                                    onchange="sync_item_table()">
                                <option value="">Select Category</option>
                            </select>
                        </div>

                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="item_table_sync" class="table table-striped table-condensed">
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
                                    <button type="button" data-text="Add" onclick="addItem()"
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

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Sub Item Category Modal" data-backdrop="static"
     data-keyboard="false"
     id="subItemCategoryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times text-red"></i></span></button>
                <h4 class="modal-title" id="modal_title_category">Title </h4>
            </div>
            <div class="modal-body">
                <form id="frm_mfq_assign_categories">
                    <input type="hidden" value="0" id="frm_itemAutoID" name="itemAutoID">


                    <header class="head-title">
                        <h2>Categories </h2>
                    </header>

                    <div class="row">

                        <div class="form-group col-sm-4">
                            <label class="title">Main</label>
                        </div>
                        <div class="form-group col-sm-6">
                        <span class="input-req"
                              title="Required Field">
                            <?php echo form_dropdown('categoryID', get_mfq_category_drop(), '', 'class="form-control" id="categoryID"  required'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                        </div>
                    </div>


                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Sub </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <select name="subCategory" required class="form-control" id="frm_subCategory">
                                    <option value=""></option>
                                </select>
                                <span class="input-req-inner"></span>
                                <!--<input type="text" name="description" id="sub_category_description"
                                       class="form-control" required>
                                <span class="input-req-inner"></span>-->
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Sub Sub </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                               <select name="subSubCategory" class="form-control" id="frm_subSubCategory">
                                    <option value=""></option>
                                </select>
                                <!--<span class="input-req-inner"></span>-->
                                <!--    <input type="text" name="description" id="sub_category_description"
                                           class="form-control" required>
                                    <span class="input-req-inner"></span>-->
                            </span>
                        </div>
                    </div>
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" onclick="assign_itemCategory_children()" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Add
                </button>

                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
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
                <input type="hidden" id="mfqItemID">
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

<div class="modal fade" id="item_qa_qc_modal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close modalclose1" id="modalclose" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="color:blueviolet;">QA QC Model</h4>
            </div>
            <?php echo form_open('', 'role="form" id="qa_qc_category_form"'); ?>
                <div class="modal-body">
                    <input type="hidden" id="mfqItemautoID" name="mfqItemautoID" val="">
                    <div id="new_field" class="">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">
                            <table class="table table-bordered" id="sub-add-tb" style="margin-bottom:10px;">
                                    <thead>
                                        <tr>
                                            <th style="min-width:80%;">Add more fields &nbsp;&nbsp;<i class="fa fa-hand-o-right" style="color:blueviolet;" aria-hidden="true"></i></th>
                                            <th style="min-width:10%;"></th>
                                            <th style="min-width:10%;"><button type="button" class="btn btn-primary btn-xs" onclick="add_more_sub()" ><i class="fa fa-plus"></i></button></th>
                                        </tr>
                                    </thead>
                                    <tbody id="field_tbody">
                                        <!-- <tr>
                                            <td><input type="text" class="form-control new-items" name="inputfField[]" id="inputfField" placeholder="Enter Field Here"></td>
                                            <td><input class="skin-section extraColumns" id="isDefault" type="checkbox" data-caption="" name="isDefault" value="1" title="is Active" tooltip="rel"></td>
                                            <td>&nbsp;</td>
                                        </tr> -->
                                    </tbody>
                            </table>
                        </div>
                    </div>  
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm modalclose1" id="modalclose" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button>
                <button class="btn btn-primary pull-right" type="button" onclick="save_qa_qc_field()" id="save_btn"><?php echo $this->lang->line('common_save'); ?></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="<?php echo base_url('plugins/iCheck/icheck.js'); ?>"></script>
<script type="text/javascript">

    //Bind event handlers
//     $(document).on('change', '.extraColumns', function() {
//         var m = $(this).attr('id').split('_');
//         var isActive_exist_id = x[1];

//         if ($(this).is(':checked')) {
//             change_isActive(isActive_exist_id, 0);
//         } else {
//             change_isActive(isActive_exist_id, 1);
//         }
//     });


//     function change_isActive(isActive_exist_id, check_value) {
//     //var iD = id.split('_');
//     //var x = iD[1];
//     var isActive_exist;
//     var isActive;

//     if(isActive_exist_id){
//         isActive_exist = {
//             isActive_exist_id : check_value
//         };
//     }else{
//         var i;
//         isActive = {
//             i : check_value
//         };
//         i++;
//     }

    
//     if(id){
//         isActive = {
//             id : check_value
//         };
//     }
//     esle{
//         var length = isActive.length() + 1;
//         isActive = {
//             length : check_value
//         };
//     }

//     // var isActive = {
//     //     x: check_value
//     // };
//     // Additional logic to handle isActive change
// }

    $('.extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });

    // $('input').on('ifChecked', function (event) {
    //     change_isActive(this.value,0);
    // });

    // $('input').on('ifUnchecked', function (event) {
    //     change_isActive(this.value,1);
    // });

	// var isActive=0;
    // if ($('#isActive').is(':checked')){
    //     isActive=1;
    // }
    // else{
    //     isActive=0;
    // }

    function add_more_sub(){
        var appendData = '<tr class="tb-tr"><td><input type="text" name="inputfField[]" id="inputfField" class="form-control new-items" placeholder="Enter Field Here" /></td>';
        appendData += '<td><input class="skin-section extraColumns" id="isActive_" type="checkbox" data-caption="" class="columnSelected" name="isActive[]" value="" title="is Active" tooltip="rel"></td>';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<a onclick="remove_field(this)"><span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></a></td></tr>';

        $('#field_tbody').prepend(appendData);
    }

    function qa_qc_add_model(id)  {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'id': id},
            url: "<?php echo site_url('MFQ_ItemMaster/load_qa_qc'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                if (!jQuery.isEmptyObject(data)) {
                    //clear table body
                    $('#field_tbody').html('');

                    //setup first row in table body  
                        var appendData1 = '';
                            appendData1 = '<tr class="tb-tr">';
                            appendData1 += '<td><input type="text" value="" name="inputfField[]" id="inputfField" class="form-control new-items" placeholder="Enter Field Here" /></td>';
                            appendData1 += '<td><input class="skin-section extraColumns" id="isActive_" type="checkbox" data-caption="" class="columnSelected" name="isActive[]" value="1" title="is Active" tooltip="rel"></td>';
                            appendData1 += '<td align="center" style="vertical-align: middle">';
                            appendData1 += '<a onclick="remove_field(this)"><span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></a></td></tr>';
                        $('#field_tbody').append(appendData1);

                    //setup existing data as 1st,2nd,3rd...etc rows
                    $.each(data.documentcustomfields, function (i, v) {
                        var check = (v.isActive == 1) ? 'checked': '';
                        
                        var appendData = '';
                        appendData = '<tr class="tb-tr"><td><input type="text" value="'+ v.checklistDescription + '" name="inputfField_exist[]" id="inputfField_exist" class="form-control new-items" placeholder="Enter Field Here" /><input type="hidden" value="'+ v.id + '" name="inputfField_exist_ID[]" id="inputfField_exist_ID" class="form-control new-items" /></td>';
                        appendData += '<td><input class="skin-section extraColumns" id="isActive_'+ v.id +'" type="checkbox" data-caption="" class="columnSelected" name="isActive[]" value="1" title="is Active" tooltip="rel"  ' + check + '></td>';
                        appendData += '<td align="center" style="vertical-align: middle">';
                        appendData += '<a onclick="delete_field(this,' + v.id + ')"><span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></a></td></tr>';
                        $('#field_tbody').append(appendData);
                    });

                }else{
                //setup first row in table body  
                var appendData1 = '';
                            appendData1 = '<tr class="tb-tr">';
                            appendData1 += '<td><input type="text" value="" name="inputfField[]" id="inputfField" class="form-control new-items" placeholder="Enter Field Here" /></td>';
                            appendData1 += '<td><input class="skin-section extraColumns" id="isActive_" type="checkbox" data-caption="" class="columnSelected" name="isActive[]" value="1" title="is Active" tooltip="rel"></td>';
                            appendData1 += '<td align="center" style="vertical-align: middle">';
                            appendData1 += '<a onclick="delete_field(this)"><span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></a></td></tr>';
                        $('#field_tbody').append(appendData1);
                }

                $('#mfqItemautoID').val(id);
                $('#item_qa_qc_modal').modal({ backdrop: "static" });

                stopLoad();
                refreshNotifications(true);

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again') ?>');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    // $('.extraColumns').on('ifChecked', function (event) {
    //     change_isDefault(this.value,0);
    // });

    // $('.extraColumns').on('ifUnchecked', function (event) {
    //     change_isDefault(this.value,1);
    // });

    // var isActive;
    // change_isDefault(id, chechk_value){
    //     isActive = [
    //         id: chechk_value,
    //     ];
    // }

    function save_qa_qc_field() {
        var data = $('#qa_qc_category_form').serializeArray();
        data.push({"name": "isActive_a", "value": isActive});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_itemMaster/save_qa_qc_field'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] = 's') {
                    $('#item_qa_qc_modal').modal('hide');
                }
            },
            error: function (/*jqXHR, textStatus, errorThrown*/) {
                myAlert('e','common an error occurred on save QA QC Please try again');
                //myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_field(obj, id){
        if(id){
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'id': id},
                        url: "<?php echo site_url('MFQ_itemMaster/delete_qa_qc_field'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert('s', 'Field Deleted Successfully');
                            $(obj).closest('tr').remove();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        else{
            $('.remove-tr').closest('tr').remove();
        }
        
    };

    function remove_field(obj){
        $(obj).closest('tr').remove();
    }


    function assign_itemCategory_children() {
        /* swal({
         title: "Are you sure?",
         text: "This item have already assigned to a category, are you sure you want to change the categories?",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "Change"
         },
         function () {*/
        update_itemCategory();
        /* });*/

    }


    function update_itemCategory() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#frm_mfq_assign_categories").serialize(),
            url: "<?php echo site_url('MFQ_ItemMaster/assign_itemCategory_children'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.error == 0) {
                    myAlert('s', data.message);
                    $("#subItemCategoryModal").modal('hide');
                    item_table();
                } else {
                    myAlert('e', data.message);
                }

            }, error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', "Code" + xhr.status + " : Error : " + thrownError)
            }
        });
    }

    function add_mainCategory(id, title) {
        $("#frm_itemAutoID").val(id);
        $("#frm_subCategory").empty();
        $("#frm_subSubCategory").empty();
        $("#subItemCategoryModal").modal('show');
        $("#modal_title_category").html(title);
        $("#categoryID").val(-1);
    }


    var oTable;
    var oTable2;
    var selectedItemsSync = [];
    $(document).ready(function () {
        $("#categoryID").change(function (e) {
            var categoryID = $("#categoryID").val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {parentID: categoryID},
                url: "<?php echo site_url('MFQ_ItemMaster/get_mfq_subCategory'); ?>",
                beforeSend: function () {
                    $("#frm_subCategory").empty();
                    $("#frm_subSubCategory").empty();
                },

                success: function (data) {
                    if (data) {
                        $("#frm_subCategory").append('<option value="-1">Select</option>');
                        $.each(data, function (key, value) {
                            $("#frm_subCategory").append('<option value="' + value['itemCategoryID'] + '">' + value['description'] + '</option>');
                        });
                    }
                }, error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', "Code" + xhr.status + " : Error : " + thrownError)
                }
            });
        });

        $("#frm_subCategory").change(function (e) {
            var subCategoryID = $("#frm_subCategory").val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {parentID: subCategoryID},
                url: "<?php echo site_url('MFQ_ItemMaster/get_mfq_subCategory'); ?>",
                beforeSend: function () {
                    $("#frm_subSubCategory").empty();
                },

                success: function (data) {
                    if (data) {
                        $("#frm_subSubCategory").append('<option value="-1">Select</option>');
                        $.each(data, function (key, value) {
                            $("#frm_subSubCategory").append('<option value="' + value['itemCategoryID'] + '">' + value['description'] + '</option>');
                        });
                    }
                }, error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', "Code" + xhr.status + " : Error : " + thrownError)
                }
            });
        });

        $.fn.extend({
            toggleText: function (a, b, c, d) {
                if (this.data("text") == c) {
                    this.data("text", d);
                    this.html(b);
                }
                else {
                    this.data("text", c);
                    this.html(a);
                }
            }
        });

        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_item_master', '', 'Item Master')
        });

        item_table();
        sync_item_table();
        link_item_table();


        $("#btnSync_fromErp").click(function () {
            sync_item_table();
            $("#itemMasterFromERP").modal('show');
        });


    });


    function LoadMainCategory() {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        load_sub_cat();
        item_table();
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
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_ItemMaster/fetch_item'); ?>",
            "order": [[0, "desc"]],
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
                {"mData": "mfqItemID"},
                {"mData": "mfq_category"},
                {"mData": "mfq_subCategory"},
                {"mData": "mfq_subSubCategory"},
                {"mData": "item_inventryCode"},
                {"mData": "secondaryItemCode"},
                {"mData": "CurrentStock"},
                {"mData": "itemmasterDescription"},
                /*{"mData": "TotalWacAmount"},*/
                /*{"mData": "confirmed"},*/
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [0,1,2,3,4,5,6,7], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#mainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#subcategoryID").val()});
                aoData.push({"name": "itemType", "value": $("#itemType").val()});
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
            "sAjaxSource": "<?php echo site_url('MFQ_ItemMaster/fetch_sync_item'); ?>",
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

    function link_item_table() {
        oTable2 = $('#item_table_link').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_ItemMaster/fetch_link_item'); ?>",
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

    function change_img(item_id, img) {
        $('#img_uplode_form')[0].reset();
        $('#img_uplode_form').bootstrapValidator('resetForm', true);
        $('#img_item_id').val(item_id);
        $('#item_img').attr('src', img);
        $("#item_category_modal").modal({backdrop: "static"});
    }

    function img_uplode() {
        var data = new FormData($('#img_uplode_form')[0]);
        $.ajax({
            url: "<?php echo site_url('ItemMaster/img_uplode'); ?>",
            type: 'post',
            data: data,
            mimeType: "multipart/form-data",
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#img_uplode_form')[0].reset();
                $('#img_uplode_form').bootstrapValidator('resetForm', true);
                $("#item_category_modal").modal('hide');
                stopLoad();
                refreshNotifications(true);
                item_table();
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_item_master(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText:"<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'itemAutoID': id},
                    url: "<?php echo site_url('MFQ_ItemMaster/delete_item'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        item_table();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function changeitemactive(id) {

        var compchecked = 0;
        if ($('#itemchkbox_' + id).is(":checked")) {
            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {itemAutoID: id, chkedvalue: compchecked},
                url: "<?php echo site_url('MFQ_ItemMaster/changeitemactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        item_table();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }
        else if (!$('#itemchkbox_' + id).is(":checked")) {

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {itemAutoID: id, chkedvalue: 0},
                url: "<?php echo site_url('MFQ_ItemMaster/changeitemactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        item_table();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

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

    function addItem() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_ItemMaster/add_item"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync},
            async: false,
            success: function (data) {
                if (data['status']) {
                    refreshNotifications(true);
                    item_table();
                    sync_item_table();
                    selectedItemsSync = [];
                } else {
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function linkItem() {
        var selectedVal = $("input:radio.radioChk:checked");
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_ItemMaster/link_item"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedVal.val(),mfqItemID:$('#mfqItemID').val()},
            async: false,
            success: function (data) {
                if (data['status']) {
                    refreshNotifications(true);
                    item_table();
                    $("#LinkitemMasterFromERP").modal('hide');
                } else {
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function link_item_master(id) {
        $('#mfqItemID').val(id);
        $("#LinkitemMasterFromERP").modal('show');
    }

    function LoadMainCategoryLink() {
        $('#linksubcategoryID').val("");
        $('#linksubcategoryID option').remove();
        load_sub_cat_link();
        link_item_table();
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

    function excel_Export_items() {
        var form = document.getElementById('item_master_filter_form');
        form.target = '_blank';
        form.action = '<?php echo site_url('MFQ_ItemMaster/export_excel_item_master'); ?>';
        form.submit();
    }

    // function qa_qc_add_model(item_id) {
    //     $('#img_uplode_form')[0].reset();
    //     $('#img_uplode_form').bootstrapValidator('resetForm', true);
    //     $('#qa_qc').val('');
    //     $("#item_qa_qc_modal").modal({backdrop: "static"});
    // }

    function add_qa_qc_details(){

    }
</script>