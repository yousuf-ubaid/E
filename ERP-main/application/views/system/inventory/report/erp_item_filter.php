<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$date_format_policy = date_format_policy();
if ($type == 1) {
    $from = convert_date_format($this->common_data['company_data']['FYPeriodDateFrom']);
    $to = convert_date_format($this->common_data['company_data']['FYPeriodDateTo']);
} else {
    $from = convert_date_format($this->session->userdata("FYBeginingDate"));
    $to = convert_date_format($this->session->userdata("FYEndingDate"));
}
$main_category_arr = all_main_category_report_drop();
$main_category_group_arr = all_main_category_group_report_drop();
$customer_category_arr=all_customer_category_report_drop();
$customer = all_customer_drop(false,1);
$customer[0] = ('') . 'Other' . ('');
//echo $reportID;
?>
<ul class="nav nav-tabs" xmlns="http://www.w3.org/1999/html">
    <li class="active"><a href="#display" data-toggle="tab"><i class="fa fa-television"></i>
            <?php echo $this->lang->line('transaction_display'); ?></a></li><!--Display-->
    <li>
</ul>
<input type="hidden" name="reportID" value="<?php echo $reportID ?>">
<div class="tab-content">
    <div class="tab-pane active" id="display">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <?php if (($reportID != "INV_IIQ")&&($reportID != "INV_IBSO")) { ?>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?php echo $this->lang->line('transaction_date_range'); ?></legend>
                        <!--Date Range-->
                        <div class="form-group col-sm-4" style="margin-bottom: 0px">
                            <?php if ($reportID == "INV_UBG" || $reportID == "ITM_CNT" || $reportID == "INV_B_CNT" || $reportID == "INV_VAL" || $reportID == "INV_UBI") { ?>
                                <label class="col-md-3 control-label text-left"
                                       for="employeeID"><?php echo $this->lang->line('transaction_as_of'); ?>
                                    :</label><!--As of-->
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <div class='input-group date filterDate' id="">
                                            <input type='text' class="form-control" name="from" value="<?php echo $to; ?>"
                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <label class="col-md-3 control-label text-left"
                                       for="employeeID"><?php echo $this->lang->line('common_from'); ?>
                                    :</label><!--From-->
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <div class='input-group date filterDate' id="">
                                            <input type='text' class="form-control" value="<?php echo $from; ?>" name="from"
                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if ($reportID != "INV_VAL" && $reportID != "INV_UBG" && $reportID != "ITM_CNT" && $reportID != "INV_B_CNT" && $reportID != "INV_IIQ" && $reportID != "INV_UBI" && $reportID != "INV_IBSO") { ?>
                            <div class="form-group col-sm-4" style="margin-bottom: 0px">
                                <label class="col-md-2 control-label text-left"
                                       for="employeeID"><?php echo $this->lang->line('common_to'); ?>:</label><!--To-->
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <div class='input-group date filterDate' id="">
                                            <input type='text' class="form-control" value="<?php echo $to; ?>" name="to"
                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-sm-4" style="margin-bottom: 0px;">
                            <?php echo $this->lang->line('transaction_from_current_financial_period'); ?>
                        </div><!--From current financial period-->
                    </fieldset>
                <?php } ?>
                <?php if ($reportID == "ITM_FM") { ?>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border">   <?php echo $this->lang->line('common_segment');?><!--Segment--></legend>
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <?php
                            if($type == 1){
                                //$segment = array_filter(fetch_segment(true));
                                $segment = array_filter_reports(fetch_segment(true));
                            }else{
                                $segment = array_filter(fetch_group_segment(true));
                            }
                            unset($segment['']);
                            echo form_dropdown('segment[]', $segment, '', 'class="segment" id="segment" multiple="multiple"'); ?>
                        </div>
                    </fieldset>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border">Customer</legend>
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <div class="col-sm-3">
                            <label for="status_filter_customer"><?php echo $this->lang->line('common_status');?></label>
                                <?php echo form_dropdown('status_filter_customer', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter_customer" onchange="load_statusbased_customer()" '); ?>
                                
                            </div>
                            <div class="col-sm-3">
                            <label for="customerAutoID"><?php echo $this->lang->line('common_customer');?></label><br>

                                <?php
                                //echo form_dropdown('customerAutoID[]', $customer, '', 'class="customerAutoID" id="customerAutoID" multiple="multiple"');
                                ?>
                                <div id="div_load_customers">
                                    <select name="customerAutoID[]" class="form-control customerAutoID" id="customerAutoID" multiple="multiple">
                                        <?php
                                        if (!empty($customer)) {
                                            foreach ($customer as $key => $val) {
                                                echo '<option value="' . $key . '">' . $val . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                <?php } ?>
                <?php if ($reportID == "ITM_FM") { ?>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"> <?php echo $this->lang->line('transaction_report_type'); ?> </legend>
                        <!--Report Type-->
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <div class="skin skin-square">
                                <div class="skin-section">
                                    <ul class="list" style="list-style: none;">
                                        <li><input tabindex="1" type="radio" id="square-radio-1" value="1"
                                                   name="rptType" checked>
                                            <label for="square-radio-1"><?php echo $this->lang->line('common_all'); ?> </label>
                                        </li><!--All-->
                                        <li><input tabindex="2" type="radio" id="square-radio-2" value="2"
                                                   name="rptType">
                                            <label for="square-radio-2"><?php echo $this->lang->line('transaction_top_ten'); ?> </label>
                                        </li><!--Top 10-->
                                        <li><input tabindex="3" type="radio" id="square-radio-3" value="3"
                                                   name="rptType">
                                            <label for="square-radio-3"><?php echo $this->lang->line('transaction_top_twenty'); ?> </label>
                                            <!--Top 20-->
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                <?php }
                if ($reportID == "ITM_CNT" || $reportID == "INV_VAL" || $reportID == "ITM_LG" || $reportID == "INV_B_CNT") { ?>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"><?php echo $this->lang->line('common_warehouse'); ?></legend>
                        <!--Warehouse-->
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <?php
                            $location = "";
                            if ($type == 1) {
                                 //$location = array_filter(all_delivery_location_drop(true));
                                $location = array_filter(all_delivery_location_drop_with_status(true));

                            } else {
                                $location = array_filter(all_group_warehouse_drop(true));
                            }

                            unset($location['']);
                            echo form_dropdown('location[]', $location, '', 'class="location" id="location" multiple="multiple"'); ?>
                        </div>
                    </fieldset>
                <?php }
                if ($reportID != "ITM_FM" && $reportID != "INV_UBG" && $reportID != "INV_UBI") { ?>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"><?php echo $this->lang->line('transaction_items'); ?> </legend>

                        <div class="row">
                            <?php if($reportID != 'INV_IBSO') {?>
                            <div class="col-md-12">
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('transaction_main_category'); ?> <!-- Main Category--> </label>
                                    <!--Main Category-->
                                    <?php if ($type == 1) {
                                        echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="loadSub()"  multiple="multiple"');
                                    } else {
                                        echo form_dropdown('mainCategoryID', $main_category_group_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="loadSub()"  multiple="multiple"');
                                    }
                                    ?>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('transaction_sub_category'); ?> <!-- Sub Category --></label>
                                    <!--Sub Category-->
                                    <select name="subcategoryID" id="subcategoryID" class="form-control searchbox"
                                            onchange="loadSubSub()" multiple="multiple">
                                        <!--Select Category-->
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label><?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?> <!-- Sub Sub Category  --></label>
                                    <!--Sub Category-->
                                    <select name="subsubcategoryID" id="subsubcategoryID"
                                            class="form-control searchbox" multiple="multiple">
                                        <!--Select Category-->
                                    </select>
                                </div>
                                <?php if ($type == 1) { ?>
                                <div class="col-sm-2">
                                    <label for="status_filter_item"><?php echo $this->lang->line('common_item_status');?></label>
                                    <?php echo form_dropdown('status_filter_item', array('1'=>'Active','2'=>'Inactive','3'=>'All'), '', '  class="form-control" id="status_filter_item" '); ?>
                                </div>
                                <?php } ?>
                            </div>
                            <?php }else {?>
                                <div class="col-md-12">
                                    <div class="col-sm-2">
                                        <label> <?php echo $this->lang->line('transaction_main_category'); ?> <!-- Main Category--> </label>
                                        <!--Main Category-->
                                        <?php if ($type == 1) {
                                            echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="loadSub()"  multiple="multiple"');
                                        } else {
                                            echo form_dropdown('mainCategoryID', $main_category_group_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="loadSub()"  multiple="multiple"');
                                        }
                                        ?>
                                    </div>
                                    <div class="col-sm-2">
                                        <label><?php echo $this->lang->line('transaction_sub_category'); ?> <!-- Sub Category --> </label>
                                        <!--Sub Category-->
                                        <select name="subcategoryID" id="subcategoryID" class="form-control searchbox"
                                                onchange="loadSubSub()" multiple="multiple">
                                            <!--Select Category-->
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label><?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?> <!-- Sub Sub Category  --></label>
                                        <!--Sub Category-->
                                        <select name="subsubcategoryID" id="subsubcategoryID"
                                                class="form-control searchbox" multiple="multiple">
                                            <!--Select Category-->
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label><?php echo $this->lang->line('common_type_as'); ?> <!-- Type As  --></label>
                                        <!--Sub Category-->
                                        <?php  echo form_dropdown('fieldNameChk1',array('itembelowstock'=>'Below Minimum Stock ','itembelowro'=>'Item Below ROL'), 'Each', 'class="form-control" id="fieldNameChk1" ');?>
                                    </div>
                                    <?php if ($type == 1) { ?>
                                    <div class="col-sm-2">
                                        <label for="status_filter_item"><?php echo $this->lang->line('common_item_status');?></label>
                                        <?php echo form_dropdown('status_filter_item', array('1'=>'Active','2'=>'Inactive','3'=>'All'), '', '  class="form-control" id="status_filter_item" '); ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            <?php }?>
                        </div>

                        <div class="row">
                            <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                                <div class="col-sm-5">
                                    <select name="itemFrom[]" id="search" class="form-control" size="8" multiple="multiple">
                                        <?php
                                        $items = "";
                                        if ($type == 1) {
                                            $items = fetch_item_data_by_company();
                                        } else {
                                            $items = fetch_group_item_data_by_company();
                                        }
                                        if (!empty($items)) {
                                            foreach ($items as $val) {
                                                $itemSecondaryCodePolicy =is_show_secondary_code_enabled();
                                                if($itemSecondaryCodePolicy){
                                                    $item_code = $val["seconeryItemCode"];
                                                }else{
                                                    $item_code = $val["itemSystemCode"];
                                                }

                                                echo '<option value="' . $val["itemAutoID"] . '">' . $item_code . ' | ' . $val["itemDescription"] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-sm-2">
                                    <!--<button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">undo</button>-->
                                    <button type="button" id="search_rightAll" class="btn btn-block btn-sm"
                                    ><i class="fa fa-forward"></i></button>
                                    <button type="button" id="search_rightSelected" class="btn btn-block btn-sm"><i
                                                class="fa fa-chevron-right"></i></button>
                                    <button type="button" id="search_leftSelected" class="btn btn-block btn-sm"><i
                                                class="fa fa-chevron-left"></i></button>
                                    <button type="button" id="search_leftAll" class="btn btn-block btn-sm"><i
                                                class="fa fa-backward"></i></button>
                                    <!--<button type="button" id="undo_redo_redo" class="btn btn-warning btn-block">redo</button>-->
                                </div>

                                <div class="col-sm-5">
                                    <select name="itemTo[]" id="search_to" class="form-control" size="8"
                                            multiple="multiple">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                <?php }
                if ($reportID == "INV_UBG") { ?>
                    <?php   if($type == 1){ ?>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"><?php echo $this->lang->line('transaction_vendor');?><!--Vendor--></legend>
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <div class="col-sm-3">
                            <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
                                <?php echo form_dropdown('status_filter', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter" onchange="loadSupplier()" '); ?>
                            </div>
                        </div>    
                    </fieldset>
                    <?php } ?>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"><?php echo $this->lang->line('transaction_vendor'); ?></legend>
                        <!--Vendor-->
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <div class="col-sm-5">
                                <select name="vendorFrom[]" id="search" class="form-control" size="8"
                                        multiple="multiple">
                                    <?php
                                    $supplier = "";
                                    if ($type == 1) {
                                        $supplier = all_supplier_drop(true,1);
                                    } else {
                                        $supplier = all_group_supplier_drop();
                                    }
                                    unset($supplier[""]);
                                    if (!empty($supplier)) {
                                        foreach ($supplier as $key => $val) {
                                            echo '<option value="' . $key . '">' . $val . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <!--<button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">undo</button>-->
                                <button type="button" id="search_rightAll" class="btn btn-block btn-sm"
                                ><i class="fa fa-forward"></i></button>
                                <button type="button" id="search_rightSelected" class="btn btn-block btn-sm"><i
                                            class="fa fa-chevron-right"></i></button>
                                <button type="button" id="search_leftSelected" class="btn btn-block btn-sm"><i
                                            class="fa fa-chevron-left"></i></button>
                                <button type="button" id="search_leftAll" class="btn btn-block btn-sm"><i
                                            class="fa fa-backward"></i></button>
                                <!--<button type="button" id="undo_redo_redo" class="btn btn-warning btn-block">redo</button>-->
                            </div>
                            <div class="col-sm-5">
                                <select name="vendorTo[]" id="search_to" class="form-control" size="8"
                                        multiple="multiple">
                                </select>
                            </div>
                        </div>
                    </fieldset>
                <?php } ?>

                <?php if ($reportID == "INV_UBI") { ?>

                    <fieldset class="scheduler-border">

                        <div class="col-sm-3">
                            <label> <?php echo $this->lang->line('common_customer_category'); ?><!--Customer Category--> </label>
                            <!--Customer Category-->
                            <?php // if ($type == 1) {
                            echo form_dropdown('customerCategoryID',$customer_category_arr , 'Each', 'class="form-control" id="customerCategoryID" onchange="loadCustomer()"  multiple="multiple"');
                            //}
                            ?>
                        </div>
                        <div class="col-sm-3">
                            <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
                            <?php echo form_dropdown('status_filter_ubi', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" id="status_filter_ubi" onchange="loadCustomer()"'); ?>
                        </div>
                    </fieldset>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"><?php echo $this->lang->line('common_customer'); ?></legend>
                        <!--Vendor-->
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <div class="col-sm-5">
                                <select name="customerFrom[]" id="search" class="form-control" size="8"
                                        multiple="multiple">
                                    <?php
                                    $supplier = "";
                                    if ($type == 1) {
                                        $supplier = all_customer_drop(true,1);
                                    } else {
                                        $supplier = all_group_customer_drop();
                                    }
                                    unset($supplier[""]);
                                    if (!empty($supplier)) {
                                        foreach ($supplier as $key => $val) {
                                            echo '<option value="' . $key . '">' . $val . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <!--<button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">undo</button>-->
                                <button type="button" id="search_rightAll" class="btn btn-block btn-sm"
                                ><i class="fa fa-forward"></i></button>
                                <button type="button" id="search_rightSelected" class="btn btn-block btn-sm"><i
                                        class="fa fa-chevron-right"></i></button>
                                <button type="button" id="search_leftSelected" class="btn btn-block btn-sm"><i
                                        class="fa fa-chevron-left"></i></button>
                                <button type="button" id="search_leftAll" class="btn btn-block btn-sm"><i
                                        class="fa fa-backward"></i></button>
                                <!--<button type="button" id="undo_redo_redo" class="btn btn-warning btn-block">redo</button>-->
                            </div>
                            <div class="col-sm-5">
                                <select name="customerTo[]" id="search_to" class="form-control" size="8"
                                        multiple="multiple">
                                </select>
                            </div>
                        </div>
                    </fieldset>
                <?php } ?>

                <?php // if (($reportID != "INV_IIQ")&&($reportID != "INV_IBSO")) { ?>
                <?php //if ($reportID != "INV_IBSO") { ?>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"><?php echo $this->lang->line('common_extra_columns'); ?> </legend>
                        <!--Extra Columns-->
                        <div class="col-sm-4" style="margin-bottom: 0px;margin-top:10px">
                            <table class="<?php echo table_class(); ?>" id="extraColumns">
                                <?php
                                $secondaryUOM = getPolicyValues('SUOM', 'All');
                                if (!empty($columns)) {
                                    $i = 1;
                                    foreach ($columns as $val) {
                                        $checked = "";
                                        if ($val["isDefault"] == 1) {
                                            $checked = "checked";
                                        }
                                        if($type == 1){
                                            if ($val["isMandatory"] == 0) {
                                                ?>
                                                <?php
                                                if($reportID == "INV_IIQ" && ($val["caption"] == "Local Currency" || $val["caption"] == "Reporting Currency" || $val["caption"] == "Transaction Currency")){

                                                }else if ($val["caption"] == "Secondary QTY") {
                                                    if($secondaryUOM==1) {
                                                       ?>
                                                        <tr>
                                                            <td style="vertical-align: middle"><?php echo $val["caption"] ?></td>
                                                            <td>
                                                                <div class="skin skin-square">
                                                                    <div class="skin-section">
                                                                        <input tabindex="<?php echo $i; ?>"
                                                                               id="checkbox<?php echo $i; ?>" type="checkbox"
                                                                               data-caption="<?php echo $val["caption"] ?>"
                                                                               class="columnSelected" name="fieldName"
                                                                               value="<?php echo $val["fieldName"] ?>" <?php echo $checked ?>>
                                                                        <label for="checkbox<?php echo $i; ?>">
                                                                            &nbsp;
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }else{

                                                    }
                                                }else{
                                                    ?>
                                                    <tr>
                                                        <td style="vertical-align: middle"><?php echo $val["caption"] ?></td>
                                                        <td>
                                                            <div class="skin skin-square">
                                                                <div class="skin-section">
                                                                    <input tabindex="<?php echo $i; ?>"
                                                                           id="checkbox<?php echo $i; ?>" type="checkbox"
                                                                           data-caption="<?php echo $val["caption"] ?>"
                                                                           class="columnSelected" name="fieldName"
                                                                           value="<?php echo $val["fieldName"] ?>" <?php echo $checked ?>>
                                                                    <label for="checkbox<?php echo $i; ?>">
                                                                        &nbsp;
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
//                                                    }
                                                }
                                                ?>

                                                <?php
                                            } else {
                                                ?>
                                                <tr class="hide">
                                                    <td style="vertical-align: middle"><?php echo $val["caption"] ?></td>
                                                    <td>
                                                        <div class="checkbox checkbox-primary">
                                                            <input id="checkbox<?php echo $i; ?>" type="checkbox"
                                                                   data-caption="<?php echo $val["caption"] ?>"
                                                                   class="columnSelected" name="fieldName"
                                                                   value="<?php echo $val["fieldName"] ?>" <?php echo $checked ?>>
                                                            <label for="checkbox<?php echo $i; ?>">
                                                                &nbsp;
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }else{
                                            if ($val["fieldID"] == 142 || $val["fieldID"] == 148 || $val["fieldID"] == 169 || $val["fieldID"] == 172 || $val["isDefault"] == 1) {
                                                $checked = "checked";
                                            }
                                            if($val["fieldID"]!=141 && $val["fieldID"]!=202 && $val["fieldID"]!=168 && $val["fieldID"]!=173){
                                                if ($val["isMandatory"] == 0) {
                                                    ?>
                                                    <tr>
                                                        <td style="vertical-align: middle"><?php echo $val["caption"] ?></td>
                                                        <td>
                                                            <div class="skin skin-square">
                                                                <div class="skin-section">
                                                                    <input tabindex="<?php echo $i; ?>"
                                                                           id="checkbox<?php echo $i; ?>" type="checkbox"
                                                                           data-caption="<?php echo $val["caption"] ?>"
                                                                           class="columnSelected" name="fieldName"
                                                                           value="<?php echo $val["fieldName"] ?>" <?php echo $checked ?>>
                                                                    <label for="checkbox<?php echo $i; ?>">
                                                                        &nbsp;
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <tr class="hide">
                                                        <td style="vertical-align: middle"><?php echo $val["caption"] ?></td>
                                                        <td>
                                                            <div class="checkbox checkbox-primary">
                                                                <input id="checkbox<?php echo $i; ?>" type="checkbox"
                                                                       data-caption="<?php echo $val["caption"] ?>"
                                                                       class="columnSelected" name="fieldName"
                                                                       value="<?php echo $val["fieldName"] ?>" <?php echo $checked ?>>
                                                                <label for="checkbox<?php echo $i; ?>">
                                                                    &nbsp;
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                        $i++;
                                    }
                                } ?>
                            </table>
                        </div>
                        <div class="col-sm-8" style="margin-bottom: 0px;margin-top:10px">
                            <?php echo $this->lang->line('transaction_put_a_cheack_mark'); ?>
                        </div><!-- Put a check mark next to each column that you want to appear in the report-->
                    </fieldset>


                <?php //}  ?>
                <?php
                if ($reportID == "ITM_CNT") {
                    ?>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"> <?php echo $this->lang->line('transaction_sub_items'); ?> </legend>
                        <!--Sub Items-->
                        <div class="col-sm-4" style="margin-bottom: 0px;margin-top:10px">
                            <table class="<?php echo table_class(); ?>" id="extraColumns">
                                <tr>
                                    <td style="vertical-align: middle"><?php echo $this->lang->line('transaction_is_sub_item_required'); ?> </td>
                                    <!--Is Sub Item Required in the Report-->
                                    <td>
                                        <div class="skin skin-square">
                                            <div class="skin-section">
                                                <input tabindex="500"
                                                       id="checkbox500" type="checkbox"
                                                       data-caption="isSubItemRequired"
                                                       class="columnSelected" name="isSubItemRequired"
                                                       value="1">
                                                <label for="checkbox500">
                                                    &nbsp;
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-8" style="margin-bottom: 0px;margin-top:10px">
                            &nbsp;
                        </div>
                    </fieldset>
                    <?php
                }
                ?>
                    <div class="col-sm-8" style="margin-bottom: 0px;margin-top:10px">
                        <?php echo $this->lang->line('accounts_payable_reports_vl_put_a_cheack_mark');?> <!-- Put a check mark next to each column that you want to appear in the report-->
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="margin-top: 10px">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="generateReport('<?php echo $formName; ?>')" name="filtersubmit"
                        id="filtersubmit"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_generate'); ?>
                </button><!--Generate-->
            </div>
        </div>
    </div>
</div>
<script>
    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    /*$('.filterDate').datepicker({
     autoclose: true,
     forceParse: true,
     format: 'yyyy-mm-dd'
     });*/
    $('.filterDate').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    });
    $('#search').multiselect({
        search: {
            left: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />', <!--Search-->
            right: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />', <!--Search-->
        },
        afterMoveToLeft: function ($left, $right, $options) {
            $("#search_to option").prop("selected", "selected");
        }
    });
    $('.skin-square input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });
    $('#extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });
    $("#location").multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $("#mainCategoryID").multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $("#subcategoryID").multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    /* $("#subcategoryID").multiselect2('selectAll', false);
     $("#subcategoryID").multiselect2('updateButtonText');*/

    $("#subsubcategoryID").multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $("#subsubcategoryID").change(function () {
        loadItems();
    });
    $("#status_filter_item").change(function () {
        loadItems();
    });
    /* $("#subsubcategoryID").multiselect2('selectAll', false);
     $("#subsubcategoryID").multiselect2('updateButtonText');

     $("#mainCategoryID").multiselect2('selectAll', false);
     $("#mainCategoryID").multiselect2('updateButtonText');*/

    $("#location").multiselect2('selectAll', false);
    $("#location").multiselect2('updateButtonText');
    /*$('#search_rightAll').trigger('click');*/

    function loadSub() {
        $("#search_to").empty();
        loadSubCategory();
        loadItems();
    }

    function loadSubSub() {
        $("#search_to").empty();
        loadSubSubCategory();
        loadItems();
    }

    function loadSubCategory() {
        $('#subcategoryID option').remove();
        var mainCategoryID = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Report/load_subcat"); ?>',
            dataType: 'json',
            data: {'mainCategoryID': mainCategoryID,type:<?php echo $type; ?>},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
                $('#subcategoryID').multiselect2('rebuild');
                /* $("#subcategoryID").multiselect2('selectAll', false);
                 $("#subcategoryID").multiselect2('updateButtonText');*/
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function loadSubSubCategory() {
        $('#subsubcategoryID option').remove();
        var subCategoryID = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Report/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subCategoryID': subCategoryID, type:<?php echo $type; ?>},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subsubcategoryID').empty();
                    var mySelect = $('#subsubcategoryID');
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
                $('#subsubcategoryID').multiselect2('rebuild');
                /*$("#subsubcategoryID").multiselect2('selectAll', false);
                 $("#subsubcategoryID").multiselect2('updateButtonText');*/
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


    function loadItems() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Report/loadItems"); ?>',
            dataType: 'json',
            data: {
                subSubCategoryID: $('#subsubcategoryID').val(),
                mainCategoryID: $('#mainCategoryID').val(),
                subCategoryID: $('#subcategoryID').val(),
                type:<?php echo $type; ?>,
                activeStatus: $('#status_filter_item').val()

            },
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#search').empty();
                    var mySelect = $('#search');
                    $.each(data, function (val, text) {
                        var itemSecondaryCodePolicy=<?php echo is_show_secondary_code_enabled(); ?>;
                        if(itemSecondaryCodePolicy){
                            var itemCode=text['seconeryItemCode'];
                        }else{
                            var itemCode=text['itemSystemCode'];
                        }
                        mySelect.append($('<option></option>').val(text['itemAutoID']).html(itemCode + ' | ' + text['itemDescription']));
                    });
                } else {
                    $('#search').empty();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    $('#segment').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        maxHeight: '30px',
        allSelectedText: 'All Selected'
    });
    $("#segment").multiselect2('selectAll', false);
    $("#segment").multiselect2('updateButtonText');


    $('#customerAutoID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        maxHeight: '30px',
        allSelectedText: 'All Selected'
    });
    $("#customerAutoID").multiselect2('selectAll', false);
    $("#customerAutoID").multiselect2('updateButtonText');

    $("#customerCategoryID").multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });


    function loadCustomer() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Report/loadCustomer"); ?>',
            dataType: 'json',
            data: {
                customerCategoryID: $('#customerCategoryID').val(),
                type:<?php echo $type; ?>,
                activeStatus: $('#status_filter_ubi').val()
            },
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#search').empty();
                    var mySelect = $('#search');
                    $.each(data['details'], function (val, text) {
                        if(data['type']==1) {
                            mySelect.append($('<option></option>').val(text['customerAutoID']).html(text['customerSystemCode'] + ' | ' + text['customerName'] + ' | ' + text['customerCountry']));
                        }else{
                            mySelect.append($('<option></option>').val(text['groupCustomerAutoID']).html(text['groupcustomerSystemCode'] + ' | ' + text['groupCustomerName'] + ' | ' + text['customerCountry']));

                        }
                    });
                } else {
                    $('#search').empty();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function loadSupplier() {
        
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Report/loadSupplier"); ?>',
            dataType: 'json',
            data: {
                activeStatus: $('#status_filter').val(),
                type:<?php echo $type; ?>
            },
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#search').empty();
                    var mySelect = $('#search');
                    $.each(data['details'], function (val, text) {
                    if(data['type']==1) {
                        mySelect.append($('<option></option>').val(text['supplierAutoID']).html(text['supplierSystemCode'] + ' | ' + text['supplierName'] + ' | ' + text['supplierCountry']));
                    }
                    // else{
                    //     mySelect.append($('<option></option>').val(text['groupSupplierAutoID']).html(text['groupSupplierSystemCode'] + ' | ' + text['groupSupplierName'] + ' | ' + text['supplierCountry']));
                    // }
                    });
                } else {
                    $('#search').empty();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_statusbased_customer() {
        var status_filter = $('#status_filter_customer').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {type:1,activeStatus:status_filter},
            url: "<?php echo site_url('Report/load_statusbased_customer'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_customers').html(data);
               
                $('#customerAutoID').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    maxHeight: '30px',
                    allSelectedText: 'All Selected'
                });
                $("#customerAutoID").multiselect2('selectAll', false);
                $("#customerAutoID").multiselect2('updateButtonText');
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
</script>