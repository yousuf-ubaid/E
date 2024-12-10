<?php echo head_page('Manage Item', false);
$mfqItemID = isset($page_id) && !empty($page_id) ? $page_id : 0;
$main_category_arr = all_main_category_drop();
?>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$company = getPolicyValues('LNG', 'All'); 

$brand_arr = get_brand_arr();
?>

<input type="hidden" id="tmp_mainCatID" value="0">
<input type="hidden" id="tmp_mainSubCatID" value="0">
<input type="hidden" id="tmp_mainSubSubCatID" value="0">

<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>


<form method="post" id="from_add_edit_crew">
    <input type="hidden" value="<?php echo $mfqItemID ?>" id="mfqItemID" name="mfqItemID"/>

    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2><?php echo $this->lang->line('manufacturing_item_information');?><!--Item Information--> </h2>
            </header>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_category');?><!--Category--></label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php if($company == 'FlowServe') { ?>
                        <select name="itemType" class="form-control" id="itemType">
                            <option value=""><?php echo $this->lang->line('common_select');?><!--Select--></option>
                            <option value="1">Parts</option>
                            <option value="2">Full Service</option>
                            <option value="3">Repaire / Other</option>
                        </select>
                    <?php } else{ ?> 
                        <select name="itemType" class="form-control" id="itemType">
                            <option value=""><?php echo $this->lang->line('common_select');?><!--Select--></option>
                            <option value="1">Raw material</option>
                            <option value="2">Finish good</option>
                            <option value="3">Semi finish good</option>
                        </select>
                    <?php } ?>
                    <span class="input-req-inner"></span>
                </span>

                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('manufacturing_finance_category');?><!--Finance Category--></label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                     <?php echo form_dropdown('mainCategoryID', $main_category_arr, '', 'class="form-control select2" id="mainCategoryID"  onchange="load_sub_cat()"'); ?>
                    <span class="input-req-inner"></span>
                </span>

                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('manufacturing_sub_category');?><!--Sub Category --></label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                     <select name="subcategoryID" id="subcategoryID" class="form-control searchbox select2"
                             onchange="load_sub_sub_cat()">
                            <option value="">Select Category</option>
                        </select>
                    <span class="input-req-inner"></span>
                </span>

                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('manufacturing_sub_sub_category');?><!--Sub sub Category--></label>
                </div>

                <div class="form-group col-sm-4">
                    <select name="subSubCategoryID" id="subSubCategoryID" class="form-control searchbox select2">
                        <option value="">Select Category</option>
                    </select>
                    </span>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_item_description');?><!--Item Description--> </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="itemName" id="itemName" class="form-control" placeholder="<?php echo $this->lang->line('manufacturing_item_name');?>"
                    >
                    <span class="input-req-inner"></span>
                </span>
                </div>

            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('manufacturing_secondary_code');?><!--Secondary Code--></label>
                </div>

                <div class="form-group col-sm-2">
                    <input type="text" name="secondaryItemCode" id="secondaryItemCode" class="form-control"
                           placeholder="<?php echo $this->lang->line('manufacturing_item_code');?>"
                    >
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('manufacturing_unit_of_measure');?><!--Units of measure--></label>
                </div>

                <div class="form-group col-sm-2">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('defaultUnitOfMeasureID', all_umo_new_drop(), '', 'class="form-control select2" id="defaultUnitOfMeasureID" '); ?>
                    <!--  --><?php /*echo form_dropdown('defaultUnitOfMeasureID', all_umo_new_drop(), '', 'class="form-control select2" id="defaultUnitOfMeasureID" '); */ ?>
                    <span class="input-req-inner"></span>
                </span>
                </div>
            </div>

            <div class="row hide unbilledservice" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">unbilled Services Gl Code</label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('unbilledServicesGLAutoID', fetch_all_gl_codes(), '', 'class="form-control select2" id="unbilledServicesGLAutoID" '); ?>
                    <!--  --><?php /*echo form_dropdown('defaultUnitOfMeasureID', all_umo_new_drop(), '', 'class="form-control select2" id="defaultUnitOfMeasureID" '); */ ?>
                    <span class="input-req-inner"></span>
                </span>
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Make</label>
                </div>
                <div class="form-group col-sm-4">
                    <span class=" col-sm-1" title="Create Brand" rel="tooltip">
                        <button type="button" class="btn btn-primary btn-xs pull-left" id="btn_add_email"
                            onclick="open_brand_model()"><i
                            class="fa fa-plus"></i></button>
                    </span>
                    <div class="col-sm-11 brand">
                        <?php echo form_dropdown('brand', $brand_arr, '', 'class="form-control select2" id="brand"  onchange=""'); ?>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2><?php echo $this->lang->line('manufacturing_categories'); ?><!--Categories--> </h2>
            </header>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('manufacturing_main'); ?><!--Main--> </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">

                    <?php echo form_dropdown('mfqCategoryID', get_mfq_category_drop(), '', 'class="form-control" id="mfqCategoryID" '); ?>
                    <span class="input-req-inner"></span>
                </span>

                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('manufacturing_sub'); ?><!--Sub-->  </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <select name="mfqSubCategoryID" class="form-control" id="frm_subCategory">
                        <option value=""></option>
                    </select>
                    <span class="input-req-inner"></span>
                </span>

                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('manufacturing_sub_sub'); ?><!--Sub Sub-->  </label>
                </div>

                <div class="form-group col-sm-4">
                    <select name="mfqSubSubCategoryID" class="form-control" id="frm_subSubCategory">
                        <option value=""></option>
                    </select>
                    <!-- <span class="input-req" title="Required Field">

                         <span class="input-req-inner"></span>
                     </span>-->

                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Packaging</label>
                </div>

                <div class="form-group col-sm-4">
                    <div class="skin-section extraColumns"><input id="packagingYN" type="checkbox" data-caption="" class="columnSelected" name="packagingYN" value="1" ><label for="checkbox">&nbsp;</label></div>
                </div>
            </div>


        </div>
    </div>

    <div class="col-md-12 animated zoomIn">
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-7">
                <div class="pull-right">
                    <button class="btn btn-primary" type="submit" id="submitItemBtn"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item');?><!--Add Item-->
                    </button>
                </div>
            </div>
        </div>
    </div>

</form>

<div class="modal fade" id="open_brand_model" role="dialog" aria-labelledby="myModalLabel"
        data-width="40%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 30%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Create Brand</h4>
            </div>
            <div class="modal-body">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp;
                    </div>
                    <div class="form-group col-sm-3">
                        <label class="title">Brand Nmae</label>
                    </div>

                    <div class="form-group col-sm-7">
                        <input type="text" name="brandName" id="brandName" class="form-control"
                            placeholder="Type brand name"
                        >
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp;
                    </div>
                    <div class="form-group col-sm-3">
                        <label class="title">isActive</label>
                    </div>

                    <div class="form-group col-sm-7">
                        <div class="skin-section extraColumns"><input id="isActive" type="checkbox" data-caption="" class="columnSelected" name="isActive" value="1" ><label for="checkbox">&nbsp;</label></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_close') ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="save_brand()">Save</button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">

    $(document).ready(function () {

        $("#mfqCategoryID").change(function (e) {
            var mfqCategoryID = $("#mfqCategoryID").val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {parentID: mfqCategoryID},
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
                    var tmpSubCatID = $("#tmp_mainSubCatID").val();
                    if (tmpSubCatID > 0) {
                        $("#frm_subCategory").val(tmpSubCatID);
                        $("#frm_subCategory").change();
                        $("#tmp_mainSubCatID").val(0);
                    }
                    ;
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

                    var tmpSubCatID = $("#tmp_mainSubCatID").val();
                    var tmp_mainSubSubCatID = $("#tmp_mainSubSubCatID").val();

                    if (tmpSubCatID > 0) {
                        $("#frm_subCategory").val(tmpSubCatID);
                        //$("#frm_subCategory").change();
                        $("#tmp_mainSubCatID").val(0);

                    }
                    setTimeout(function () {
                        $("#frm_subSubCategory").val(tmp_mainSubSubCatID);
                    }, 500);

                }, error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', "Code" + xhr.status + " : Error : " + thrownError)
                }
            });
        });


        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_item_master', '', 'Item Master');
        });

        $("#from_add_edit_crew").submit(function (e) {
            addEditItem();
            return false;
        });
        loadItemDetail();

    });

    function load_sub_cat(select_val) {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subSubCategoryID').val("");
        $('#subSubCategoryID option').remove();
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

    function load_sub_sub_cat() {
        $('#subSubCategoryID option').remove();
        $('#subSubCategoryID').val("");
        var subsubid = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subsubid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subSubCategoryID').empty();
                    var mySelect = $('#subSubCategoryID');
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


    function addEditItem() {
        var data = $("#from_add_edit_crew").serializeArray();
        $('select[name="defaultUnitOfMeasureID"] option:selected').each(function () {
            data.push({'name': 'defaultUnitOfMeasure', 'value': $(this).text()})
        });

        data.push({'name': 'mainCategory', 'value': $('#mainCategoryID option:selected').text()});

        if ($('#packagingYN').is(':checked')){
            data.push({name: 'packagingYN', value: 1});
        }else{
            data.push({name: 'packagingYN', value: 0});
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_ItemMaster/add_edit_mfq_item"); ?>',
            dataType: 'json',
            data: data,
            async: false,
            success: function (data) {
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
                else if (data['error'] == 0) {
                    if (data['code'] == 1) {
                        $("#from_add_edit_crew")[0].reset();
                        $("#mainCategoryID").val('').change();
                        $("#subcategoryID").val('').change();
                        $("#subSubCategoryID").val('').change();
                        $("#defaultUnitOfMeasureID").val('').change();
                        $("#unbilledServicesGLAutoID").val('').change();
                        $("#packagingYN").iCheck('uncheck');
                    }
                    myAlert('s', data['message']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                myAlert('e', xhr.responseText);
            }
        });
    }

    function loadItemDetail() {
        var mfqItemID = '<?php echo $mfqItemID ?>';
        if (mfqItemID > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_ItemMaster/load_mfq_itemMaster"); ?>',
                dataType: 'json',
                data: {mfqItemID: mfqItemID},
                async: false,
                success: function (data) {
                    if (data['error'] == 0) {
                        //myAlert('s', data['message']);
                        $("#submitItemBtn").html('<i class="fa fa-pencil"></i> Save Item');

                        $("#itemName").val(data['itemName']);
                        $("#secondaryItemCode").val(data['secondaryItemCode']);
                        $("#itemType").val(data['categoryTypeID']);   //$("#itemType").val(data['itemType']);
                        $("#mfqCategoryID").val(data['mfqCategoryID']);
                        $("#mfqCategoryID").change();
                        $("#mainCategoryID").val(data['mainCategoryID']);
                        $("#mainCategoryID").change();
                        $("#subcategoryID").val(data['subcategoryID']);
                        $("#subcategoryID").change();
                        $("#subSubCategoryID").val(data['subSubCategoryID']);
                        $("#subSubCategoryID").change();
                        $("#tmp_mainSubCatID").val(data['mfqSubCategoryID']);
                        $("#tmp_mainSubSubCatID").val(data['mfqSubSubCategoryID']);
                        $("#defaultUnitOfMeasureID").val(data['defaultUnitOfMeasureID']);
                        $("#defaultUnitOfMeasureID").change();
                        $('#unbilledServicesGLAutoID').val(data['unbilledServicesGLAutoID']);
                        $("#unbilledServicesGLAutoID").change();
                        if(data['packagingYN'] == 1){
                            $("#packagingYN").iCheck('check');
                        }else{
                            $("#packagingYN").iCheck('uncheck');
                        }
                        if (data['categoryTypeID'] == 2) {
                            $('.unbilledservice').removeClass('hide');
                        } else {
                            $('.unbilledservice').addClass('hide');
                        }

                        /*$("#designation").val(data['designation']);
                         $("#EEmail").val(data['EEmail']);
                         $("#EpTelephone").val(data['EpTelephone']);
                         $("#EpTelephone").val(data['EpTelephone']);*/
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        }
    }
    $("#mainCategoryID").change(function () {
        unbilledServices(this.value)
    });


    function unbilledServices(mainCategoryID) {
        if(mainCategoryID)
        {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {mainCategoryID: mainCategoryID},
                url: "<?php echo site_url('MFQ_ItemMaster/hideshownoninventory'); ?>",
                beforeSend: function () {

                    startLoad();

                },
                success: function (data) {
                    stopLoad();
                    if (!jQuery.isEmptyObject(data)) {
                        if (data['categoryTypeID']== 2) {
                            $('.unbilledservice').removeClass('hide')
                        }else {
                            $('.unbilledservice').addClass('hide')
                        }
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

    }

    function open_brand_model(){
        $('#brandName').val('');
        $('#isActive').iCheck('uncheck');
        $("#open_brand_model").modal({backdrop: "static"});
    }

    // function fetch_brand(){
    //     $.ajax({
    //         type: 'GET',
    //         // MFQ_ItemMaster/fetch_brand
    //         dataType: 'json',
    //         async: false,
    //         success: function (data) {
    //             stopLoad();
    //             //myAlert(data[0]);
    //             $('.brand').empty();
    //             $('.brand').append(data);
    //         },
    //         error: function (xhr, ajaxOptions, thrownError) {
    //             myAlert('e', xhr.responseText);
    //         }
    //     });
    // }

    function save_brand(){
        var data = [];
        data.push({'name': 'brandName', 'value': $('#brandName').val()})
        if ($('#isActive').is(':checked')){
            data.push({name: 'isActive', value: 1});
        }else{
            data.push({name: 'isActive', value: 0});
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_ItemMaster/save_brand"); ?>',
            dataType: 'json',
            data: data,
            async: false,
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
               
                if (data[0] == 's') {
                    var mfqItemID = '<?php echo $mfqItemID ?>';
                    $("#brandName").val('');
                    $("#isActive").iCheck('uncheck');
                    $("#open_brand_model").modal('hide');
                    //fetch_brand();
                    fetchPage('system/mfq/item-master/manage-item', mfqItemID, '');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                myAlert('e', xhr.responseText);
            }
        });
    }

    $('.select2').select2();


</script>