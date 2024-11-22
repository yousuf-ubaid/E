<?php echo head_page($_POST['page_name'], false);
$main_category_arr = all_main_category_drop();
$revenue_gl_arr = all_revenue_gl_drop();
$cost_gl_arr = all_cost_gl_drop();
$asset_gl_arr = all_asset_gl_drop();
$uom_arr = all_umo_new_drop();

$fetch_cost_account = fetch_cost_account();
$fetch_dep_gl_code = fetch_gl_code(array('masterCategory' => 'PL', 'subCategory' => 'PLE'));
$fetch_disposal_gl_code = fetch_gl_code(array('masterCategory' => 'PL'));
?>
<style>
    @font-face {
        font-family: barCodeFont;
        src: url(<?php echo base_url('font/fre3of9x.ttf') ?>);
    }

</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Item Header</a>
    <a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab">Step 2 - Item Attachments</a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="itemmaster_form"'); ?>
        <div class="row modal-body" style="padding-bottom: 0px;">
            <div class="col-sm-3" align="" style="padding-left: 0px;">
                <div class="fileinput-new thumbnail" style="margin-bottom: 4px;width: 200px; height: 150px;">
                    <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg">
                    <input type="file" name="itemImage" id="itemImage" style="display: none;"
                           onchange="loadImage(this)"/>
                </div>
                <div class="form-group col-sm-12 no-padding">
                    <!--<label for="">System Code</label>-->
                    <h4 id="edit_systemCode" style="margin-top: 2px;color: #48bbce;"></h4>
                    <div id="barCode" style="font-family: barCodeFont;">&nbsp</div>
                </div>
                <!--<div class="form-group col-sm-12 no-padding">
                    <label for="">Short Description</label>
                    <h4 id="edit_shortDescription" style="margin-bottom:0px;margin-top: 2px;color: #48bbce;"></h4>
                </div>-->
            </div>
            <div class="col-md-9" style="padding-left: 0px;">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Main Category <?php required_mark(); ?></label>
                        <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="load_sub_cat()"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Category <?php required_mark(); ?></label>
                        <select name="subcategoryID" id="subcategoryID" class="form-control searchbox"
                                onchange="load_sub_sub_cat(),load_gl_codes()">
                            <option value="">Select Category</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>Sub Category </label>
                        <select name="subSubCategoryID" id="subSubCategoryID" class="form-control searchbox">
                            <option value="">Select Category</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Short Description <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="itemName" name="itemName">
                    </div>
                    <div class="form-group col-sm-8">
                        <label>Long Description <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="itemDescription" name="itemDescription">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="">Secondary Code <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="seconeryItemCode" name="seconeryItemCode">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">Unit of Measure <?php required_mark(); ?></label>
                        <?php echo form_dropdown('defaultUnitOfMeasureID', $uom_arr, 'Each', 'class="form-control" id\="defaultUnitOfMeasureID" required'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">Selling Price <?php required_mark(); ?></label>

                        <div class="input-group">
                            <div
                                class="input-group-addon"><?php echo $this->common_data['company_data']['company_default_currency']; ?></div>
                            <input type="text" step="any" class="form-control number" id="companyLocalSellingPrice"
                                   name="companyLocalSellingPrice" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="">Barcode</label>
                <input type="text" class="form-control" id="barcode" name="barcode">
            </div>
            <div class="form-group col-sm-3">
                <label for="">Part No </label>
                <input type="text" class="form-control" id="partno" name="partno">
            </div>
            <div class="form-group col-sm-2" id="cls_maximunQty">
                <label for="">Maximum Qty</label>
                <input type="text" class="form-control number" id="maximunQty" name="maximunQty">
            </div>
            <div class="form-group col-sm-2" id="cls_minimumQty">
                <label for="">Minimum Qty</label>
                <input type="text" class="form-control number" id="minimumQty" name="minimumQty">
            </div>
            <div class="form-group col-sm-2" id="cls_reorderPoint">
                <label for="">Reorder Level</label>
                <input type="text" class="form-control number" id="reorderPoint" name="reorderPoint">
            </div>
        </div>
        <div class="row" id="inventry_row_div">
            <div class="form-group col-sm-4">
                <label for="">Revenue GL Code </label>
                <?php echo form_dropdown('revanueGLAutoID', $revenue_gl_arr, '', 'class="form-control select2" id="revanueGLAutoID" '); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="">Cost GL Code</label>
                <?php echo form_dropdown('costGLAutoID', $cost_gl_arr, '', 'class="form-control select2" id="costGLAutoID" '); ?>
            </div>
            <div class="form-group col-sm-4" id="assetGlCode_div">
                <label for="">Asset GL Code</label>
                <?php echo form_dropdown('assteGLAutoID', $asset_gl_arr, $this->common_data['controlaccounts']['INVA'], 'class="form-control select2" id="assteGLAutoID"'); ?>
            </div>
        </div>
        <div class="row hide" id="fixed_row_div">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Cost Account <?php required_mark(); ?></label>
                    <?php echo form_dropdown('COSTGLCODEdes', $fetch_cost_account, '', 'class="form-control form1 select2" id = "COSTGLCODEdes"'); ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Acc Dep GL Code <?php required_mark(); ?></label>
                    <?php echo form_dropdown('ACCDEPGLCODEdes', $fetch_cost_account, '', 'class="form-control form1 select2" id = "ACCDEPGLCODEdes"'); ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Dep GL Code <?php required_mark(); ?></label>
                    <?php echo form_dropdown('DEPGLCODEdes', $fetch_dep_gl_code, '', 'class="form-control form1 select2" id = "DEPGLCODEdes" '); ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Disposal GL Code <?php required_mark(); ?></label>
                    <?php echo form_dropdown('DISPOGLCODEdes', $fetch_disposal_gl_code, '', 'class="form-control form1 select2" id = "DISPOGLCODEdes"'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">isActive</label>

                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="checkbox_isActive" type="checkbox"
                                   data-caption="" class="columnSelected" name="isActive" value="1" checked>
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">is Sub-item Applicable </label>

                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="checkbox_isSubitemExist" type="checkbox"
                                   data-caption="" class="columnSelected" name="isSubitemExist" value="1">
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit">Save</button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-sm-8">
                <h4 class="modal-title" id="purchaseOrder_attachment_label">Modal title</h4>
                <br>
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-10"><span class="pull-right">
                      <?php echo form_open_multipart('', 'id="itemMaster_attachment_uplode_form" class="form-inline"'); ?>
                            <div class="form-group">
                                <!-- <label for="attachmentDescription">Description</label> -->
                                <input type="text" class="form-control" id="attachmentDescription"
                                       name="attachmentDescription" placeholder="Description...">
                                <input type="hidden" class="form-control" id="itm_documentSystemCode"
                                       name="documentSystemCode">
                                <input type="hidden" class="form-control" id="itm_documentID" name="documentID"
                                       value="ITM">
                                <input type="hidden" class="form-control" id="itm_document_name" name="document_name"
                                       value="Item Master">
                            </div>
                          <div class="form-group">
                              <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                   style="margin-top: 8px;">
                                  <div class="form-control" data-trigger="fileinput"><i
                                          class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                          class="fileinput-filename"></span></div>
                                  <span class="input-group-addon btn btn-default btn-file"><span
                                          class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                      aria-hidden="true"></span></span><span
                                          class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                         aria-hidden="true"></span></span><input
                                          type="file" name="document_file" id="document_file"></span>
                                  <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                     data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                              </div>
                          </div>
                          <button type="button" class="btn btn-default" onclick="itemMaster_document_uplode()"><span
                                  class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form></span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>File Name</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="purchaseOrder_attachment" class="no-padding">
                        <tr class="danger">
                            <td colspan="5" class="text-center">No Attachment Found</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var itemAutoID;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/item/erp_item_master', 'Test', 'Item Master');
        });
        $('.select2').select2();
        itemAutoID = null;
        number_validation();
        load_sub_cat();

        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            itemAutoID = p_id;
            load_item_header();
            changeFormCode();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }
        $('#itemmaster_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                /*                revanueGLAutoID: {validators: {notEmpty: {message: 'Revanue GL Code is required.'}}},
                 costGLAutoID: {validators: {notEmpty: {message: 'Cost GL Code is required.'}}},
                 assteGLAutoID: {validators: {notEmpty: {message: 'Asste GL Code is required.'}}},*/
                seconeryItemCode: {validators: {notEmpty: {message: 'Item Code is required.'}}},
                itemName: {validators: {notEmpty: {message: 'Item Name is required.'}}},
                itemDescription: {validators: {notEmpty: {message: 'Item Description is required.'}}},
                mainCategoryID: {validators: {notEmpty: {message: 'Main category is required.'}}},
                subcategoryID: {validators: {notEmpty: {message: 'Sub category is required.'}}},
                defaultUnitOfMeasureID: {validators: {notEmpty: {message: 'Unit of measure is required.'}}},
                /*                    maximunQty              : {validators: {notEmpty: {message: 'Maximun Qty is required.'}}},
                 minimumQty              : {validators: {notEmpty: {message: 'Minimum Qty is required.'}}},
                 reorderPoint            : {validators: {notEmpty: {message: 'Reorder Point is required.'}}},*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'itemAutoID', 'value': itemAutoID});
            data.push({'name': 'revanue', 'value': $('#revanueGLAutoID option:selected').text()});
            data.push({'name': 'cost', 'value': $('#costGLAutoID option:selected').text()});
            data.push({'name': 'asste', 'value': $('#assteGLAutoID option:selected').text()});
            data.push({'name': 'mainCategory', 'value': $('#mainCategoryID option:selected').text()});
            data.push({'name': 'uom', 'value': $('#defaultUnitOfMeasureID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('ItemMaster/save_itemmaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        if (data['barcode']) {
                            $('#barcode').val(data['barcode']);
                        }
                        //$("#mainCategoryID").readonly();
                        //$('#mainCategoryID').prop('readonly', true);
                        //$("#mainCategoryID").prop("disabled", false);
                        $("#defaultUnitOfMeasureID").prop("disabled", false);
                        faID = data['last_id'];

                        var imgageVal = new FormData();
                        imgageVal.append('faID', faID);

                        var files = $("#itemImage")[0].files[0];
                        imgageVal.append('files', files);

                        if (files == undefined) {
                            //$('#itemmaster_form')[0].reset();
                            $('.btn-wizard').removeClass('disabled');
                            //$('#itemmaster_form').bootstrapValidator('resetForm', true);
                            $("#itm_documentSystemCode").val(faID);
                            attachment_modal_itemMaster(faID, "Item Master", "ITM");
                            $('[href=#step2]').tab('show');
                            return false;
                        }

                        $.ajax({
                            type: 'POST',
                            dataType: 'JSON',
                            data: imgageVal,
                            contentType: false,
                            cache: false,
                            processData: false,
                            url: "<?php echo site_url('ItemMaster/item_image_upload'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                refreshNotifications(true);
                                //$('#itemmaster_form')[0].reset();
                                $('.btn-wizard').removeClass('disabled');
                                $("#itm_documentSystemCode").val(faID);
                                attachment_modal_itemMaster(faID, "Item Master", "ITM");
                                //$('#itemmaster_form').bootstrapValidator('resetForm', true);
                                $('[href=#step2]').tab('show');
                            }, error: function () {
                                alert('An Error Occurred! Please Try Again.');
                                stopLoad();
                                refreshNotifications(true);
                            }
                        });
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });
    });

    function load_item_header() {
        if (itemAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemAutoID': itemAutoID},
                url: "<?php echo site_url('ItemMaster/load_item_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {

                        $('#itemName').val(data['itemName']);
                        $('#edit_systemCode').text(data['itemSystemCode']);
                        //$('#edit_shortDescription').text(data['itemName']);
                        $('#itemDescription').val(data['itemDescription']);
                        $('#mainCategoryID').val(data['mainCategoryID']);
                        $('#mainCategoryID option:not(:selected)').prop('disabled', true);
                        $('#costGLAutoID').val(data['costGLAutoID']).change();
                        $('#costGLAutoID option:not(:selected)').prop('disabled', true);
                        $('#assteGLAutoID').val(data['assteGLAutoID']).change();
                        $('#assteGLAutoID option:not(:selected)').prop('disabled', true);
                        $('#revanueGLAutoID').val(data['revanueGLAutoID']).change();
                        $('#revanueGLAutoID option:not(:selected)').prop('disabled', true);
                        $('#partno').val(data['partNo']);
                        $('#defaultUnitOfMeasureID').val(data['defaultUnitOfMeasureID']);
                        $('#defaultUnitOfMeasureID option:not(:selected)').prop('disabled', true);
                        $('#companyLocalSellingPrice').val(data['companyLocalSellingPrice']);
                        $('#seconeryItemCode').val(data['seconeryItemCode']);
                        $('#barcode').val(data['barcode']);
                        load_sub_cat(data['subcategoryID']);
                        $('#subcategoryID').val(data['subcategoryID']);
                        load_sub_sub_cat(data['subSubCategoryID']);
                        $('#subSubCategoryID').val(data['subSubCategoryID']);
                        $("#barcode").val(data['barcode']);
                        $("#maximunQty").val(data['maximunQty']);
                        $("#minimumQty").val(data['minimumQty']);
                        $("#reorderPoint").val(data['reorderPoint']);
                        $('#COSTGLCODEdes').val(data['faCostGLAutoID']).change();
                        $('#COSTGLCODEdes option:not(:selected)').prop('disabled', true);
                        $('#ACCDEPGLCODEdes').val(data['faACCDEPGLAutoID']).change();
                        $('#ACCDEPGLCODEdes option:not(:selected)').prop('disabled', true);
                        $('#DEPGLCODEdes').val(data['faDEPGLAutoID']).change();
                        $('#DEPGLCODEdes option:not(:selected)').prop('disabled', true);
                        $('#DISPOGLCODEdes').val(data['faDISPOGLAutoID']).change();
                        $('#DISPOGLCODEdes option:not(:selected)').prop('disabled', true);
                        $('#subcategoryID option:not(:selected)').prop('disabled', true);
                        if (data['isActive'] == 1) {
                            $('#checkbox_isActive').iCheck('check');
                        } else {
                            $('#checkbox_isActive').iCheck('uncheck');
                        }
                        if (data['isSubitemExist'] == 1) {
                            $('#checkbox_isSubitemExist').iCheck('check');
                        } else {
                            $('#checkbox_isSubitemExist').iCheck('uncheck');
                        }
                        if (data['itemImage'] == 'no-image.png') {
                            $("#changeImg").attr("src", "<?php echo base_url('images/item/no-image.png'); ?>");
                        } else {
                            $("#changeImg").attr("src", "<?php echo base_url('uploads/itemMaster/'); ?>" + '/' + data['itemImage']);
                        }
                        $("#itm_documentSystemCode").val(itemAutoID);
                        attachment_modal_itemMaster(itemAutoID, "Item Master", "ITM");

                        // $('[href=#step2]').tab('show');
                        // $('a[data-toggle="tab"]').removeClass('btn-primary');
                        // $('a[data-toggle="tab"]').addClass('btn-default');
                        // $('[href=#step2]').removeClass('btn-default');
                        // $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function load_sub_cat(select_val) {
        changeFormCode();
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

    function load_gl_codes() {
        $('#revanueGLAutoID').val("");
        $('#costGLAutoID').val("");
        $('#assteGLAutoID').val("");
        $('#COSTGLCODEdes').val("");
        $('#ACCDEPGLCODEdes').val("");
        $('#DEPGLCODEdes').val("");
        $('#DISPOGLCODEdes').val("");
        itemCategoryID = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_gl_codes"); ?>',
            dataType: 'json',
            data: {'itemCategoryID': itemCategoryID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $("#revanueGLAutoID").val(data['revenueGL']).change();
                    $("#costGLAutoID").val(data['costGL']).change();
                    $("#assteGLAutoID").val(data['assetGL']).change();
                    $("#COSTGLCODEdes").val(data['faCostGLAutoID']).change();
                    $("#ACCDEPGLCODEdes").val(data['faACCDEPGLAutoID']).change();
                    $("#DEPGLCODEdes").val(data['faDEPGLAutoID']).change();
                    $("#DISPOGLCODEdes").val(data['faDISPOGLAutoID']).change();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


    function changeFormCode() {
        itemCategoryID = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_category_type_id"); ?>',
            dataType: 'json',
            data: {'itemCategoryID': itemCategoryID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    if ((data['categoryTypeID'] == 2) || (data['categoryTypeID'] == 4)) {
                        $("#assetGlCode_div").addClass("hide");
                        $("#cls_maximunQty").addClass("hide");
                        $("#cls_minimumQty").addClass("hide");
                        $("#cls_reorderPoint").addClass("hide");
                    } else {
                        $("#assetGlCode_div").removeClass("hide");
                        $("#cls_maximunQty").removeClass("hide");
                        $("#cls_minimumQty").removeClass("hide");
                        $("#cls_reorderPoint").removeClass("hide");
                    }
                    if (data['categoryTypeID'] == 3) {
                        $("#inventry_row_div").addClass("hide");
                        $("#fixed_row_div").removeClass("hide");
                    } else {
                        $("#inventry_row_div").removeClass("hide");
                        $("#fixed_row_div").addClass("hide");
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    $('#changeImg').click(function () {
        $('#itemImage').click();
    });

    /*$('#empImage').change(function(){

     });*/

    function loadImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#changeImg').attr('src', e.target.result);
            };

            reader.readAsDataURL(obj.files[0]);
        }
    }

    function attachment_modal_itemMaster(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#purchaseOrder_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " Attachments");
                    $('#purchaseOrder_attachment').empty();
                    $('#purchaseOrder_attachment').append('' +data+ '');

                    //$("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_itemMaster_attachment(itemAutoID, DocumentSystemCode) {
        if (itemAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this attachment file!",
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
                        data: {'attachmentID': itemAutoID},
                        url: "<?php echo site_url('Procurement/delete_purchaseOrder_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            attachment_modal_itemMaster(DocumentSystemCode, "Item Master", "ITM");
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function itemMaster_document_uplode() {
        var formData = new FormData($("#itemMaster_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    attachment_modal_itemMaster($('#itm_documentSystemCode').val(), 'Item Master', 'ITM');
                    $('#remove_id').click();
                    $('#attachmentDescription').val('');
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }
</script>