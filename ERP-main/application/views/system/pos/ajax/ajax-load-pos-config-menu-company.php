<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$locations = load_pos_location_drop();
?>
<style>
    .clsMenuCategory {
        cursor: pointer
    }

    .clsGold {
        color: #daa520
    }

    .head1 {
        font-size: 16px;
        font-weight: 700
    }

    .clsGray {
        color: #a6a6a6
    }

    .imgThumb {
        height: 25px;
        width: 25px
    }

    .thumbnail_custom {
        position: relative;
        z-index: 0
    }

    .thumbnail_custom:hover {
        background-color: transparent;
        z-index: 50
    }

    .thumbnail_custom span {
        position: absolute;
        background-color: #ffffe0;
        padding: 5px;
        left: -1000px;
        border: 1px dashed gray;
        visibility: hidden;
        color: #000;
        text-decoration: none
    }

    .thumbnail_custom span img {
        border-width: 0;
        padding: 2px
    }

    .thumbnail_custom:hover span {
        visibility: visible;
        top: 0;
        left: 60px
    }

    .headStyle2 {
        color: #000 !important
    }

    .headStyle2:hover {
        text-decoration: underline !important;
        color: #3c8dbc !important
    }

    .myBreadcrumb {
        display: inline-block;
        border: 1px solid #d6d6d6;
        overflow: hidden;
        border-radius: 5px
    }

    .myBreadcrumb > * {
        text-decoration: none;
        outline: 0;
        display: block;
        float: left;
        font-size: 12px;
        line-height: 36px;
        color: #000;
        padding: 0 10px 0 30px;
        background: #fff;
        position: relative;
        transition: all .5s
    }

    .myBreadcrumb > * div {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis
    }

    .myBreadcrumb > :first-child {
        padding-left: 10px;
        border-radius: 5px 0 0 5px
    }

    .myBreadcrumb > :first-child i {
        vertical-align: sub
    }

    .myBreadcrumb > :last-child {
        border-radius: 0 5px 5px 0;
        padding-right: 8px
    }

    .myBreadcrumb a.active, .myBreadcrumb a:hover {
        background: #0277BD;
        color: #fff
    }

    .myBreadcrumb a.active:after, .myBreadcrumb a:hover:after {
        background: #0277BD
    }

    .myBreadcrumb > :after {
        content: '';
        position: absolute;
        top: 0;
        right: -18px;
        width: 36px;
        height: 36px;
        transform: scale(.707) rotate(45deg);
        -ms-transform: scale(.707) rotate(45deg);
        -webkit-transform: scale(.707) rotate(45deg);
        z-index: 1;
        background: #fff;
        box-shadow: 2px -2px 0 2px rgba(0, 0, 0, .4), 3px -3px 0 2px rgba(255, 255, 255, .1);
        border-radius: 0 5px 0 50px;
        transition: all .5s
    }

    .myBreadcrumb > :last-child:after {
        content: none
    }

    .myBreadcrumb > :before {
        border-radius: 100%;
        width: 20px;
        height: 20px;
        line-height: 20px;
        margin: 8px 0;
        position: absolute;
        top: 0;
        left: 30px;
        background: #fff;
        background: linear-gradient(#444, #222);
        font-weight: 700;
        box-shadow: 0 0 0 1px #ccc
    }

    .myBreadcrumb > :nth-child(n+2) {
        display: none
    }

    @media (max-width: 767px) {
        .myBreadcrumb > :nth-last-child(-n+4) {
            display: block
        }

        .myBreadcrumb > * div {
            max-width: 100px
        }
    }

    @media (min-width: 768px) {
        .myBreadcrumb > :nth-last-child(-n+6) {
            display: block
        }

        .myBreadcrumb > * div {
            max-width: 175px
        }
    }
</style>


<div class="" style="background-color: #ffffff; padding:8px;" id="menuCategoryList">
    <div id="menuCategoryTitle">
            <span class="head1" onclick="refreshCategory()">
            <i class="fa fa-cutlery clswhite" aria-hidden="true"></i>
                <a href="#" class="language-ico">
                    <?php echo $this->lang->line('pos_config_menu_categories'); ?>
                </a>
                <!--Menu Categories--> &nbsp;&nbsp;
        </span>
        <button style="display: none;" id="goBackButton" class="btn btn-link"
                onclick="prev_gotoCategory(<?php echo $parentID ? $parentID : 0; ?>,<?php echo $parentLevel && $parentLevel > 0 ? $parentLevel : 0; ?>);"
                rel="tooltip"
                title="" data-original-title="Go Back"><i class="fa fa-arrow-left fa-2x" aria-hidden="true"></i>
            <!--go Back--></button>
        <button class="btn btn-xs btn-default pull-right" type="button" onclick="add_menuCategoryModal();">
            <?php echo $this->lang->line('pos_config_add_category'); ?><!-- Add Category-->
        </button>
        <span class="pull-right">&nbsp;</span>
        <button class="btn btn-xs btn-primary pull-right" type="button" onclick="edit_item_cost();">
             Edit Item Cost
        </button>
        <span class="pull-right">&nbsp;</span>
        <button class="btn btn-xs btn-primary pull-right" type="button" onclick="copyMenues();">
            Clone Menus
        </button>
        <hr>
        <hr>
    </div>
    <div>
        <?php echo isset($breadcrumbs) ? $breadcrumbs : '' ?>
    </div>
    <div>
        <table class="<?php echo table_class_pos(2); ?>" id="table_menuCategory_company">
            <thead>
            <tr>
                <th><?php echo $this->lang->line('common_image'); ?><!--Image--></th>
                <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                <th><?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></th>
                <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                <th><?php echo $this->lang->line('pos_config_is_pack'); ?><!--is Pack--></th>
                <th><?php echo $this->lang->line('pos_config_category_colour'); ?><!--Category Colour--></th>
                <th><?php echo $this->lang->line('pos_config_sortoder'); ?><!--sortOder--></th>
                <th>Show Image</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (isset($menuCategoryList) && !empty($menuCategoryList)) {
                $i = 1;
                foreach ($menuCategoryList as $menuCategory) {
                    ?>
                    <tr id="menuRow_<?php echo $menuCategory['menuCategoryID'] ?>">
                        <td class="clsMenuCategory"
                            onclick="checkSubExist('<?php echo $menuCategory['menuCategoryID'] ?>','<?php echo $menuCategory['masterLevelID']; ?>','<?php echo $menuCategory['levelNo']; ?>')">
                            <a class="thumbnail_custom" href="#thumb">
                                <img src="<?php echo base_url($menuCategory['image']) ?>" class="imgThumb img-rounded"/>
                                <span><img style="max-width: 300px !important;"
                                           src="<?php echo base_url($menuCategory['image']) ?>"/></span>
                            </a>

                        </td>

                        <td class="clsMenuCategory"
                            onclick="checkSubExist('<?php echo $menuCategory['menuCategoryID'] ?>','<?php echo $menuCategory['masterLevelID']; ?>','<?php echo $menuCategory['levelNo']; ?>')">
                                    <span style="font-size:14px !important;">
                                        <?php echo $menuCategory['menuCategoryDescription'] ?>
                                    </span>
                        </td>
                        <td class="clsMenuCategory"
                            onclick="checkSubExist('<?php echo $menuCategory['menuCategoryID'] ?>','<?php echo $menuCategory['masterLevelID']; ?>','<?php echo $menuCategory['levelNo']; ?>')">
                            <span
                                    style="font-size:12px !important;"><?php
                                echo $menuCategory['GLDesc'];
                                ?></span>
                        </td>
                        <td class="clsMenuCategory"
                            onclick="checkSubExist('<?php echo $menuCategory['menuCategoryID'] ?>','<?php echo $menuCategory['masterLevelID']; ?>','<?php echo $menuCategory['levelNo']; ?>')"
                            style="text-align: center; font-size:13px !important;">
                            <?php if ($menuCategory['isActive'] == 1) { ?>
                                <span
                                        class="label label-success"><?php echo $this->lang->line('common_active'); ?><!--Active--></span>
                            <?php } else { ?>
                                <span
                                        class="label label-default"><?php echo $this->lang->line('pos_config_in'); ?><!--in-->-
                                    <?php echo $this->lang->line('common_active'); ?><!--Active--></span>
                            <?php } ?>
                        </td>

                        <!--is Pax -->
                        <td style="text-align: center;">

                            <input class="mySwitch" type="checkbox"
                                   id="isPax_<?php echo $menuCategory['menuCategoryID'] ?>" name="isPax"
                                   onchange="updateIsPaxValue(<?php echo $menuCategory['menuCategoryID'] ?>,'mc')"
                                   data-size="mini"
                                   data-on-text="<i class='fa fa-coffee text-purple'></i> <?php echo $this->lang->line('pos_config_pax'); ?>"
                                   data-handle-width="50" data-off-color="default" data-on-color="default"
                                   data-off-text="<?php echo $this->lang->line('common_no'); ?>"
                                   data-label-width="0" <?php if ($menuCategory['isPack'] == 1) {
                                echo 'checked';
                            } ?> /><!--Pax-->


                        </td>

                        <td style="text-align: center">
                            <?php
                            if (!empty($menuCategory['bgColor'])) {
                                echo '<i class="fa fa-square fa-2x" style="color:' . $menuCategory['bgColor'] . '"></i>';
                            }
                            ?>
                        </td>

                        <td><?php echo col_sortOrderMenu($menuCategory['menuCategoryID'], $menuCategory['sortOrder'], 'mc') ?></td>
                        <td style="text-align: center;">
                            <input class="mySwitch" type="checkbox"
                                   id="showImageYN_<?php echo $menuCategory['menuCategoryID'] ?>" name="showImageYN"
                                   onchange="update_showImageYN(<?php echo $menuCategory['menuCategoryID'] ?>,'mc')"
                                   data-size="mini"
                                   data-on-text="<i class='fa fa-image text-green'></i> On"
                                   data-handle-width="50" data-off-color="default" data-on-color="default"
                                   data-off-text="Off"
                                   data-label-width="0" <?php if ($menuCategory['showImageYN'] == 1) {
                                echo 'checked';
                            } ?> /><!-- /show image-->
                        </td>

                        <td style="text-align: left;">
                            <button class="btn btn-xs btn-danger"
                                    onclick="deleteCategory(<?php echo $menuCategory['menuCategoryID'] ?>)"><i
                                        class="fa fa-trash"></i></button>
                            &nbsp;&nbsp;

                            <button class="btn btn-xs btn-default"
                                    onclick="editCategory(<?php echo $menuCategory['menuCategoryID'] ?>)"><i
                                        class="fa fa-edit"></i></button>
                            &nbsp;&nbsp;

                            <?php
                            $t = check_category_subItemExist($menuCategory['menuCategoryID']);
                            echo $t ? '<button rel="tooltip" title="Add Sub Category" class="btn btn-xs btn-default" onclick="add_subSubCategory(' . $menuCategory['menuCategoryID'] . ',' . ($menuCategory['levelNo'] + 1) . ')"> <i class="fa fa-plus"></i> </button>' : '';
                            ?>

                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="copyMenuesModal" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg" style="width:33%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="CommonEdit_Title">Clone Menus to Another Warehouse</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label>From Warehouse</label>
                        <select class=" filters" required name="outletID_f[]" id="outletID_f">
                            <?php
                            echo '<option value="-1">Select</option>';
                            foreach ($locations as $loc) {
                                echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . '-' . $loc['wareHouseDescription'] . '-'. $loc['wareHouseLocation']. '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-sm-12">
                        <label>To Warehouse</label>
                        <select class=" filters" required name="outletID_t[]" id="outletID_t">
                            <?php
                            echo '<option value="-1">Select</option>';
                            foreach ($locations as $loc) {
                                echo '<option value="' . $loc['wareHouseAutoID'] . '">' . $loc['wareHouseCode'] . '-' . $loc['wareHouseDescription'] . '-'. $loc['wareHouseLocation']. '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="form-group col-sm-12" style="padding-left: 0px">
                        <button class="btn btn-primary"
                                onclick="copyMenuesFunction()" type="button">
                            Clone</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="outputListOfMenusDetail">
    <!--Menu Detail Content-->
</div>
<div id="outputListOfMenus" style="display: none; background-color: #ffffff;">
    <!--Menu Content  -->
</div>
<script>
    $(document).ready(function (e) {
        //var table = $("#table_menuCategory_company").dataTable();
        $('#table_menuCategory_company').dataTable({
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "fnDrawCallback": function (oSettings) {
                $(".mySwitch").bootstrapSwitch();
            }
        });
        $("#segmentConfigID_addMenu").val('<?php echo $id ?>');

        $("#outletID_t").select2({});
        $("#outletID_f").select2({});
    });

    function edit_item_cost() {
        fetchPage('system/pos/settings/menu-item-cost-edit', '', 'Bulk Edit Item Cost ')
    }
    
    function copyMenues() {
        $("#outletID_f").val("-1").trigger('change');
        $("#outletID_t").val("-1").trigger('change');
        $("#copyMenuesModal").modal('show');
    }

    function copyMenuesFunction(){
        if(validateCopyMenuForm()){
            var fromWH = $("#outletID_f").val();
            var toWH = $("#outletID_t").val();
            startLoad();
            $.ajax({
                dataType: "json",
                type: "POST",
                url: "<?php echo site_url('Pos_config/copy_menus_to_warehouse'); ?>",
                data: {fromID: fromWH, toID: toWH},
                success: function (data) {
                    stopLoad();
                    myAlert('s','Successfully Copied Menues.')/*Corporate Objective Saved Successfully*/
                    $("#copyMenuesModal").modal('hide');
                }
            });
        }
    }

    function validateCopyMenuForm(){
        var isValid = true;
        var fromWH = $("#outletID_f").val();
        var toWH = $("#outletID_t").val();

        if(fromWH == -1 || toWH == -1){
            isValid = false;
            myAlert('e','Warehouse Fields are Required.');
        }

        // if(toWH == -1){
        //     isValid = false;
        //     myAlert('e','To Warehouse Cannot be Blank.');
        // }

        if((toWH == fromWH) && (fromWH != -1)){
            isValid = false;
            myAlert('e','Cannot Copy to Same Warehouse.');
        }

        return isValid;

    }
</script>