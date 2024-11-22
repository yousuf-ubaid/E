<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_asset_category');
echo head_page($title, false); ?>
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/tree.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">

<form id="mfq_itemCategory" method="post">
    <input type="hidden" value="2" name="categoryType">
    <div class="row">
        <div class="form-group col-sm-3">
            <label class="title"><?php echo $this->lang->line('manufacturing_master_category') ?><!--Master Category--> </label>
        </div>
        <div class="form-group col-sm-3">
        <span class="input-req" title="Required Field">
            <input type="text" name="description" id="master_category_description" class="form-control" required>
            <span class="input-req-inner"></span>
        </span>
        </div>

        <div class="form-group col-sm-3">
            <button type="button" onclick="save_itemCategory()" class="btn btn-primary btn-sm"><i
                    class="fa fa-plus"></i>
                <?php echo $this->lang->line('common_add') ?><!--Add-->
            </button>
        </div>
    </div>
</form>
<div class="treeContainer" style="min-height: 200px;">
    <!--via ajax -->
</div>

<script>
    $(document).ready(function () {
        load_mfq_category();
    });

    function load_mfq_category() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('mfq_masters/load_mfq_category'); ?>",
            data: {categoryType: 2},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $(".treeContainer").html(data);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function save_itemCategory() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('mfq_masters/save_itemCategory'); ?>",
            data: $("#mfq_itemCategory").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.error == 0) {
                    $("#mfq_itemCategory")[0].reset();
                    myAlert('s', data.message);
                    load_mfq_category();
                } else if (data.error == 1) {
                    myAlert('e', data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function save_itemCategory_children() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('mfq_masters/save_itemCategory'); ?>",
            data: $("#mfq_itemCategory_children").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.error == 0) {
                    $("#mfq_itemCategory_children")[0].reset();
                    myAlert('s', data.message);
                    load_mfq_category();
                    $("#itemCategoryAddModal").modal('hide');
                } else if (data.error == 1) {
                    myAlert('e', data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function add_subCategoryModal(masterID, level, description) {
        $("#itemCategoryAddModal").modal('show');
        $("#parentCategory").html(description);
        $("#subCategory_masterID").val(masterID);
        $("#subCategory_levelNo").val(parseInt(level) + 1);
        setTimeout(function () {
            $("#sub_category_description").focus();
        }, 500);

    }

    function minusStyle() {

        !function ($) {

            // Le left-menu sign
            /*for older jquery version */
            $('#left ul.nav li.parent > a > span.sign').click(function () {
                $(this).find('i:first').toggleClass("fa fa-circle-thin");
            });

            $(document).on("click", "#left ul.nav li.parent > a > span.sign", function () {
                //$(this).find('i:first').toggleClass("fa fa-circle-thin");
            });

            // Open Le current menu
            //$("#left ul.nav li.parent.active > a > span.sign").find('i:first').addClass("fa fa-circle-thin");
            $("#left ul.nav li.current").parents('ul.children').addClass("in");


        }(window.jQuery);
    }
    function edit_subCategoryModal(masterID, level, description) {
        $("#itemCategoryeditModal").modal('show');
        $("#edit_sub_category_description").val(description);

        $("#parentCategory").html(description);
        $("#edit_subCategory_masterID").val(masterID);
        $("#subCategory_levelNo").val(parseInt(level) + 1);
        setTimeout(function () {
            $("#edit_sub_category_description").focus();
        }, 500);

    }
    function update_assetCategory() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('mfq_masters/update_itemCategory'); ?>",
            data: $("#edit_mfq_itemCategory").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.error == 0) {

                    myAlert('s', data.message);
                    load_mfq_category();
                    //  $("#itemCategoryAddModal").modal('hide');

                } else if (data.error == 1) {
                    myAlert('e', data.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

</script>
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Item Category edit modal" id="itemCategoryeditModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('manufacturing_edit_sub_category') ?><!--Edit Sub Category--></h4>
            </div>
            <div class="modal-body">
                <form id="edit_mfq_itemCategory" method="post">
                    <input type="hidden" value="" id="edit_subCategory_masterID" name="masterID">
                    <input type="hidden" value="1" id="subCategory_levelNo" name="levelNo">
                    <input type="hidden" value="1" name="categoryType">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <header class="head-title">
                                <h2 id="parentCategory"><!--via JS --></h2>
                            </header>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title"><?php echo $this->lang->line('manufacturing_category_description') ?><!--Category Description--> </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <input type="text" name="description" id="edit_sub_category_description"
                                       class="form-control" required>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>

                        <div class="form-group col-sm-2">
                            <button type="button" onclick="update_assetCategory()" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_update') ?><!--Update-->
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Item Category Add modal" id="itemCategoryAddModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('manufacturing_add_sub_category') ?><!--Add Sub Category--></h4>
            </div>
            <div class="modal-body">
                <form id="mfq_itemCategory_children" method="post">
                    <input type="hidden" value="0" id="subCategory_masterID" name="masterID">
                    <input type="hidden" value="1" id="subCategory_levelNo" name="levelNo">
                    <input type="hidden" value="2" name="categoryType">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <header class="head-title">
                                <h2 id="parentCategory"><!--via JS --></h2>
                            </header>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title"><?php echo $this->lang->line('manufacturing_category_description') ?><!--Category Description--> </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <input type="text" name="description" id="sub_category_description"
                                       class="form-control" required>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>

                        <div class="form-group col-sm-2">
                            <button type="button" onclick="save_itemCategory_children()" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add') ?><!--Add-->
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>

        </div>
    </div>
</div>
