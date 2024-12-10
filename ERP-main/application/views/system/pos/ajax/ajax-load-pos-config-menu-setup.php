<?php
/*echo '<pre>';
print_r($menuCategoryList);
echo '</pre>';*/
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<style>
    .clsMenuCategory {
        cursor: pointer;
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
<div><!--class="container"-->
    <div class="row">
        <div class="col-md-12" style="background-color: #ffffff;">
            <div id="menuCategoryTitle">
                <span style="font-size: 16px; font-weight: 700">
                    <i class="fa fa-cutlery" style="color:goldenrod;" aria-hidden="true"></i>
                    <?php echo $this->lang->line('pos_config_menu_categories'); ?><!--Menu Categories--> &nbsp;&nbsp;
                </span>
                <button style="display: none;" id="goBackButton" class="btn btn-link"
                        onclick="prev_gotoCategory(<?php echo $parentID ? $parentID : 0; ?>,<?php echo $parentLevel && $parentLevel > 0 ? $parentLevel : 0; ?>,<?php echo $wareHouseAutoID;?>);"
                        rel="tooltip"
                        title="" data-original-title="Go Back"><i class="fa fa-arrow-left fa-2x" aria-hidden="true"></i>
                    <!--go Back--></button>
                <input type="hidden" id="level_no" value="0">
                <input type="hidden" id="master_level_id" value="0">
                <button class="btn btn-xs btn-default" type="button" onclick="add_menuCategory_setupModal(<?php echo $wareHouseAutoID;?>);">
                    <?php echo $this->lang->line('pos_config_add_category'); ?><!--Add Category-->
                </button>
                <hr>
            </div>
            <div>
                <?php echo isset($breadcrumbs) ? $breadcrumbs : '' ?>
            </div>
            <div id="menuCategoryList_setup">
                <table class="<?php echo table_class_pos(2); ?>" id="table_menuCategory">
                    <thead>
                    <tr>
                        <th style="width:50px;"><?php echo $this->lang->line('common_image'); ?><!--Image--></th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($menuCategoryList) && !empty($menuCategoryList)) {
                        $i = 1;
                        foreach ($menuCategoryList as $menuCategory) {
                            ?>
                            <tr id="menuRow_<?php echo $menuCategory['autoID'] ?>">
                                <td class="clsMenuCategory"
                                    onclick="checkSubExist('<?php echo $menuCategory['menuCategoryID'] ?>','<?php echo $menuCategory['masterLevelID']; ?>','<?php echo $menuCategory['levelNo']; ?>','<?php echo $menuCategory['warehouseID'] ?>')">
                                    <img src="<?php echo base_url($menuCategory['image']) ?>"
                                         style="height: 25px; width:25px;"
                                         alt="<?php echo $menuCategory['menuCategoryDescription'] ?>">
                                </td>
                                <td class="clsMenuCategory"
                                    onclick="checkSubExist('<?php echo $menuCategory['menuCategoryID'] ?>','<?php echo $menuCategory['masterLevelID']; ?>','<?php echo $menuCategory['levelNo']; ?>','<?php echo $menuCategory['warehouseID'] ?>')">
                                    <span style="font-size:14px !important;">
                                        <?php echo $menuCategory['menuCategoryDescription'] ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">

                                    <?php if ($menuCategory['Active'] == 1) { ?>
                                        <input type="checkbox"
                                               id="menueCategoryIsactive_<?php echo $menuCategory['autoID'] ?>"
                                               name="menueCategoryIsactive"
                                               onchange="changeMenueCategoryIsactive(<?php echo $menuCategory['autoID'] ?>)"
                                               data-size="mini"
                                               data-on-text="<?php echo $this->lang->line('common_active'); ?>"
                                               data-handle-width="45" data-off-color="danger" data-on-color="success"
                                               data-off-text="<?php echo $this->lang->line('pos_config_deactive'); ?>"
                                               data-label-width="0" checked><!--Active--><!--Deactive-->
                                    <?php } else if ($menuCategory['Active'] == 0) { ?>
                                        <input type="checkbox"
                                               id="menueCategoryIsactive_<?php echo $menuCategory['autoID'] ?>"
                                               name="menueCategoryIsactive"
                                               onchange="changeMenueCategoryIsactive(<?php echo $menuCategory['autoID'] ?>)"
                                               data-size="mini" data-on-text="Active" data-handle-width="45"
                                               data-off-color="danger" data-on-color="success" data-off-text="Deactive"
                                               data-label-width="0">
                                    <?php } ?>
                                    &nbsp;
                                    |
                                    &nbsp;
                                    <button class="btn btn-danger btn-xs"
                                            onclick="delete_menue_Category(<?php echo $menuCategory['autoID'] ?>)"
                                            rel="tooltip" title="" data-original-title="Delete"><i
                                                class="fa fa-trash"></i></button>

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
    </div>

    <div id="outputListOfMenus_setup" style="display: none; background-color: #ffffff;">
        <!--Menu Content  -->
    </div>
</div>


<script>
    $(document).ready(function (e) {
        $("#table_menuCategory").dataTable({
            "fnDrawCallback": function (oSettings) {
                $("[name='menueCategoryIsactive']").bootstrapSwitch();
            }
        });
        $("#segmentConfigID_menuSetup").val('<?php echo isset($id) ? $id : 0 ?>');
        setTimeout(function () {
            $("[name='menueCategoryIsactive']").bootstrapSwitch();
        }, 100);
        $('#table_menuCategory').on('page.dt', function () {
            setTimeout(function () {
                $("[name='menueCategoryIsactive']").bootstrapSwitch();
            }, 100);
        });
        $("#outletConfigModalTitle").html('<?php echo isset($outletName) ? $outletName : 'Menu'; ?>');
    });

    $('select').on('change', function () {
        $("#segmentConfigID_menuSetup").val('<?php echo isset($id) ? $id : 0 ?>');
        setTimeout(function () {
            $("[name='menueCategoryIsactive']").bootstrapSwitch();
        }, 100);
        $('#table_menuCategory').on('page.dt', function () {
            setTimeout(function () {
                $("[name='menueCategoryIsactive']").bootstrapSwitch();
            }, 100);
        });
    });

</script>