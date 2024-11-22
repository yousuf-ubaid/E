<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    textarea.form-control {
        padding: 5px !important;
    }

    .pulling-based-li{
        background: #547698;
    }

    .pulling-based-li > a{
        color: #ffffff !important;
    }

    .nav>li.pull-li>a:hover{
        color: #444 !important;
        cursor: pointer;
        background: #d4d3d3 !important
    }
</style>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$projectExist = project_is_exist();
$uom_arr = array('' => 'Select UOM');
$placeholder = '0.00';
$currencyID = $master['transactionCurrency'];
$currency_decimal = $master['transactionCurrencyDecimalPlaces'];
if($currencyID == 'OMR')
{
    $placeholder = '0.000';
}
$hideCost = getPolicyValues('HCG', 'All');
$showPurchasePrice = getPolicyValues('SPP', 'All');
$itemBatch = getPolicyValues('IB', 'All');
if($showPurchasePrice==' ' || $showPurchasePrice== null || empty($showPurchasePrice)){
    $showPurchasePrice = 0;
}
$readonly = '';

$advanceCostCapturing = getPolicyValues('ACC', 'All');
$activityCode_arr = get_activity_codes();

$grv_policy = getPolicyValues('CPG', 'GRV');
$show_cost_grv = getPolicyValues('HCGRV', 'All');
if($show_cost_grv == 1){
    $ele = '';
    $hidden = '';
}else {
    $ele = 'hide-elements-td2';
    $hidden = 'hide-ele';
}
if ($grvType == 'PO Base') { ?>
    <div class="row">
        <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i
                        class="fa fa-hand-o-right"></i><?php echo $this->lang->line('transaction_goods_add_item_po_base'); ?>
            </h4><h4></h4><!--Add Item PO Base-->
        </div>
        <div class="col-md-4">
            <button type="button" onclick="po_model_load()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('transaction_addpo'); ?>
            </button><!--Add PO-->
        </div>
    </div><br>
    <div class="modal fade" id="po_base_modal" role="dialog" aria-labelledby="myModalLabel"
         data-width="95%" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" style="width: 85%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"
                        id="myModalLabel"> <?php echo $this->lang->line('transaction_goods_purchase_order_base'); ?> </h4>
                    <!--Purchase Order Base-->
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="box box-widget widget-user-2">
                                <div class="widget-user-header bg-yellow">
                                    <h4> <?php echo $this->lang->line('transaction_goods_purchase_orders'); ?> </h4>
                                    <!--Purchase Orders-->
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <?php
                                        if (!empty($supplier_po)) {
                                            for ($i = 0; $i < count($supplier_po); $i++) {
                                                $id = 'pull-'.$supplier_po[$i]['purchaseOrderID'];
                                                echo '<li id="'.$id.'" title="PO Date :- ' . $supplier_po[$i]['documentDate'] . '" rel="tooltip" class="pull-li"><a onclick="fetch_po_detail_table(' . $supplier_po[$i]['purchaseOrderID'] . ')">' . $supplier_po[$i]['purchaseOrderCode'] . ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>';
                                            }
                                        } else {
                                            $norec = $this->lang->line('common_no_records_found');
                                            echo '<li><a>' . $norec . '<!--No Records found--></a></li>';
                                        }
                                        ?>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <input type="hidden" id="purchaseOrderID" name="purchaseOrderID">
                            <table class="table table-bordered table-striped table-condesed ">
                                <thead>
                                <?php
                                if($show_cost_grv == 1){ ?>
                                <tr>
                                    <th colspan='6'><?php echo $this->lang->line('common_item'); ?> </th><!--Item-->

                                    <?php if($hideCost != 1) { ?>
                                        <th colspan='2' class="<?php echo $hidden; ?>"><?php echo $this->lang->line('transaction_common_ordered_item'); ?>
                                            <span class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span>
                                        </th><!--Ordered Item-->
                                        <th class="hideCostClass <?php echo $hidden; ?>" colspan='3'><?php echo $this->lang->line('transaction_common_recived_item'); ?>
                                            <span class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span>
                                        </th><!--Received Item-->
                                    <?php }else { ?>
                                        <th class="hideCostClass hide" colspan='3'>  </th><!--Received Item-->
                                    <?php }?>
                                <tr>
                                <?php } ?>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo $this->lang->line('common_code'); ?> </th><!--Code-->
                                    <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th><!--Description-->
                                    <th><?php echo $this->lang->line('common_uom'); ?> </th><!--UOM-->

                                    <?php

                                    if($hideCost != 1) { ?>
                                        <th>Ordered Qty</th><!--Qty-->
                                        
                                            <th class="hideCostClass <?php echo $ele; ?>"><?php echo $this->lang->line('common_cost'); ?></th><!--Cost-->
                                        
                                        <th>Received Qty</th><!--Qty-->

                                    <?php } else {?>
                                    
                                        <th>PO <?php echo $this->lang->line('common_qty'); ?></th><!--Qty-->
                                        <th>GRV <?php echo $this->lang->line('common_qty'); ?> </th><!--Qty-->
                                    <?php }?>

                                    <?php if($hideCost != 1) { ?>                                       
                                        <th class="hideCostClass <?php echo $ele; ?>"><?php echo $this->lang->line('common_cost'); ?> </th><!--Cost-->
                                      
                                        <th class="groupByTaxEnable hide">Tax</th><!--Cost-->
                                        <th class="groupByTaxEnable hide">Tax Total</th><!--Cost-->
                                        <th class="hideCostClass  <?php echo $ele; ?>"><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->

                                    <?php } ?>

                                    <th style="display: none;">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody id="table_body">
                                <tr class="danger">
                                    <td colspan="9" class="text-center" id="no_rec_colspan">
                                        <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                                    <!--No Records Found-->
                                </tr>
                                </tbody>
                                <tfoot id="table_tfoot">

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button type="button" class="btn btn-primary"
                            onclick="save_po_base_items()"><?php echo $this->lang->line('common_save_change'); ?> </button>
                    <!--Save changes-->
                </div>
            </div>
        </div>
    </div>
<?php } else if ($grvType == 'LOG') { ?>
    <div class="row">
        <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i
                        class="fa fa-hand-o-right"></i><?php echo $this->lang->line('transaction_goods_add_item_po_base'); ?>
            </h4><h4></h4><!--Add Item PO Base-->
        </div>
        <div class="col-md-4">
            <button type="button" onclick="logistic_po_model_load()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('transaction_addpo'); ?>
            </button><!--Add PO-->
        </div>
    </div><br>
    <div class="modal fade" id="logistic_po_base_modal" role="dialog" aria-labelledby="myModalLabel"
         data-width="95%" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" style="width: 85%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"
                        id="myModalLabel"> <?php echo $this->lang->line('transaction_goods_purchase_order_base'); ?> </h4>
                    <!--Purchase Order Base-->
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="box box-widget widget-user-2">
                                <div class="widget-user-header bg-yellow">
                                    <h4> <?php echo $this->lang->line('transaction_goods_purchase_orders'); ?> </h4>
                                    <!--Purchase Orders-->
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <?php
                                        if (!empty($supplier_po)) {
                                            for ($i = 0; $i < count($supplier_po); $i++) {
                                                $id = 'pull-'.$supplier_po[$i]['purchaseOrderID'];
                                                echo '<li id="'.$id.'" title="PO Date :- ' . $supplier_po[$i]['documentDate'] . '" rel="tooltip" class="pull-li"><a onclick="fetch_logistic_po_detail_table(' . $supplier_po[$i]['purchaseOrderID'] . ')">' . $supplier_po[$i]['purchaseOrderCode'] . ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>';
                                            }
                                        } else {
                                            $norec = $this->lang->line('common_no_records_found');
                                            echo '<li><a>' . $norec . '<!--No Records found--></a></li>';
                                        }
                                        ?>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <input type="hidden" id="logistic_purchaseOrderID" name="purchaseOrderID">
                            <table class="table table-bordered table-striped table-condensed">
                                <thead>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 7%">Code</th>
                                <th style="min-width: 5%">Logistic Amount</th>
                                <th style="min-width: 10%">Logistic Balance</th>
                                <th style="min-width: 10%">Matching Amount</th>
                                <th style="min-width: 10%">Actual Logistic Amount</th>
                                <th style="min-width: 8%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                                </thead>
                                <tbody id="logistic_table_body">
                                <tr class="danger">
                                    <td colspan="7" class="text-center" id="no_rec_colspan">
                                        <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                                    <!--No Records Found-->
                                </tr>
                                </tbody>
                                <tfoot id="logistic_table_tfoot">

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button type="button" class="btn btn-primary"
                            onclick="save_logistic_po_base_items()"><?php echo $this->lang->line('common_save_change'); ?> </button>
                    <!--Save changes-->
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>

    <div class="row">
        <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i
                        class="fa fa-hand-o-right"></i><?php echo $this->lang->line('transaction_common_add_item_detail'); ?>
            </h4><h4></h4><!--Add Item Detail-->
        </div>
        <div class="col-md-4">
            <span class="pull-right">
            <button type="button" onclick="grv_st_bulk_detail_modal()" class="btn btn-primary"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item'); ?>  </button>
                <!--Add Item-->
                <!--<button type="button" onclick="grv_st_detail_modal()" class="btn btn-primary"><i class="fa fa-plus"></i> Add Item</button>-->
            </span>
        </div>
    </div><br>
<?php } ?>
<div aria-hidden="true" role="dialog" id="grv_st_detail_modal" data-backdrop="static"
     data-keyboard="false" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('transaction_common_edit_item_detail'); ?> </h4>
                <!--Edit Item Detail-->
            </div>
            <form role="form" id="grv_detail_form" class="form-horizontal">
                <input type="hidden" name="grvPurchaseOrderID" id="grvPurchaseOrderID">
                <input type="hidden" name="grvPurchaseOrderDetailID" id="grvPurchaseOrderDetailID">
                <input type="hidden" name="grvTaxCalculationFormulaID" id="grvTaxCalculationFormulaID">
                <input type="hidden" name="isGroupBasedTaxEnable" id="isGroupBasedTaxEnable" value="<?php echo $isGroupBasedTaxEnable?>">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="">
                        <thead>
                        <tr>
                            <th class='hide_po'
                                style="width: 35%;"><?php echo $this->lang->line('transaction_common_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <?php if ($projectExist == 1) { ?>
                                <th style="width: 140px;"><?php echo $this->lang->line('transaction_goods_received_project'); ?></th><!--Project-->
                                <th style="width: 140px;">Project Category</th>
                                <th style="width: 140px;">Project Subcategory</th>
                            <?php } ?>
                           
                            <?php if($grvTypeDoc == 2){?>
                            <th style="width: 110px;">Bal Qty</th>
                           <?php }?>

                            <?php if($hideCost != 1) { ?>
                                <th class='hide_po'
                                    style="width: 15%;"><?php echo $this->lang->line('common_uom'); ?><?php required_mark(); ?></th>
                                <!--UOM-->
                            <?php } ?>

                            <th style="width: 15%;"><?php echo $this->lang->line('common_qty'); ?><?php required_mark(); ?></th>
                            <!--Qty-->
                           <?php if($isGroupBasedTaxEnable == 1){  // Removed tax from entire grv process?>
                           
                            <th style="width: 140px;">Tax </th>
                            <th style="width: 140px;">Tax Amount</th>
                            
                            <?php  }?>
                           
                                <?php if($hideCost != 1) { ?>
                                <th style="width: 20%;"><?php echo $this->lang->line('common_unit_cost'); ?> <span
                                            class="currency"> (LKR)</span> <?php required_mark(); ?></th><!--Unit Cost-->
                                <?php if($show_cost_grv == 1){?>
                                <th style="width: 20%;"><?php echo $this->lang->line('common_net_amount'); ?> <span
                                            class="currency"> (LKR)</span> <?php required_mark(); ?></th><!--Net Amount-->
                                <?php } ?>
                            <?php } ?>
                            <?php if($itemBatch == 1){?>
                            <th><?php echo $this->lang->line('erp_grv_batch_number'); ?> </th>
                            <th><?php echo $this->lang->line('erp_grv_batch_expire'); ?>  </th>
                            <?php }?>

                            <?php if($advanceCostCapturing == 1){ ?>
                                <th style="width: 15%;">Activity Code <?php required_mark(); ?></th>
                            <?php } ?>

                            <?php if($show_cost_grv == 1){?>
                                <th>FOC</th>
                                <th style="width: 200px;"><?php echo $this->lang->line('common_comment'); ?> </th>
                                <!--Comment-->
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class='hide_po'>
                                <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" class="form-control "
                                       name="search" id="search"
                                       placeholder="Item ID, Item Description...">
                                <input type="hidden" class="form-control itemAutoID" id="itemAutoID" name="itemAutoID">
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div id="edit_div_projectID_item">
                                        <select name="projectID" id="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('transaction_goods_received_select_project'); ?> </option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <?php echo form_dropdown('project_categoryID',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit1" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                </td>
                                <td>
                                    <?php echo form_dropdown('project_subCategoryID',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit1"'); ?>
                                </td>

                            <?php } ?>
                            <?php if($grvTypeDoc == 2){?>
                            <td>
                                <input type="text" class="form-control text-right" id="bal_qty" value="" readonly>
                            </td>
                            <?php }?>
                         

                            <?php if($hideCost != 1) { ?>
                            <td class='hide_po'>
                                <?php echo form_dropdown('UnitOfMeasureID', $uom_arr, 'Each', 'class="form-control" id="UnitOfMeasureID"'); ?>
                            </td>
                            <?php } ?>
                            <td>
                                <input type="text" name="quantityRequested" onchange="change_amount_edit(this,1,<?php echo $grvTypeDoc?>);"
                                       onfocus="this.select();" onkeyup="qtyValidation()" id="quantityRequested"
                                       class="form-control number">
                                <input type="hidden" id="qty_unchanged" value="">
                            </td>
                            <?php if($isGroupBasedTaxEnable == 1){ ?>
                                <td class="lintax"><span class="pull-left" id="linetaxDescription" style=" text-align: right;margin-top: 5%;"> - </span></td>
                                <td class="lintax">
                                <input type="hidden" id="tax_type">
                                <span class="pull-right" id="linetaxamnt_edit" style="font-size: 14px;text-align: right;margin-top: 5%;">0</span>
                                
                                </td>
                            
                            <?php } ?>

                            <?php if($hideCost != 1) {?>
                                    
                            <td>
                                <?php if ($grv_policy == 0 || $isGroupBasedTaxEnable == 1) { ?>
                                    <input type="text" name="estimatedAmount" onfocus="this.select();"
                                           id="estimatedAmount" value="00"
                                           class="form-control number" readonly>
                                <?php } else { ?>
                                    <input type="text" name="estimatedAmount" onchange="change_amount_edit(this,1)"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           onfocus="this.select();" id="estimatedAmount" value="00"
                                           class="form-control number">
                                <?php } ?>
                            </td>
                            <?php if($advanceCostCapturing == 1){ ?>
                                <td>
                                    <?php echo form_dropdown('activityCode', $activityCode_arr, '', 'class="form-control select2" id="edit_activityCode" '); ?>
                                </td>
                            <?php } ?>
                        
                                <td class="<?php echo $ele; ?>">
                            <?php if ($grv_policy == 0) { ?>

                                <input type="text" name="receivedTotalAmount" onchange="change_amount_edit(this,2)"
                                       onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                       id="receivedTotalAmount" value="00"
                                       class="form-control number" readonly>
                            </td> <?php } else { ?>
                                    <input type="text" name="receivedTotalAmount" onchange="change_amount_edit(this,2)"
                                           onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                           id="receivedTotalAmount" value="00"
                                           class="form-control number" readonly>
                            <?php } }?>
                            <?php if($itemBatch == 1){?>
                            <td><input type="text" name="batchNumber" id="batchNumber" onkeyup=""
                                       onchange="" onfocus=""
                                       class="form-control  batchNumber" required></td>

                            <td><input type="date" name="expireDate" id="expireDate" onkeyup=""
                                       onchange="" onfocus=""
                                       class="form-control  expireDate" required></td>
                            <?php }?>

                            <td class="text-center <?php echo $ele; ?>">
                                <input id="isFocEdit" type="checkbox" data-caption="" class="columnSelected" name="isFoc" value="1">
                            </td>
                            <td class="<?php echo $ele; ?>">
                                <textarea class="form-control" rows="1" name="comment"
                                          placeholder="<?php echo $this->lang->line('transaction_common_item_comment'); ?>..."
                                          id="comment"></textarea><!--Item Comment-->
                            </td>
                            <td style="display: none;">
                                <textarea class="form-control" rows="1" name="remarks"
                                          placeholder="<?php echo $this->lang->line('transaction_common_item_remarks'); ?>..."
                                          id="remarks"></textarea><!--Item Remarks-->
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button class="btn btn-primary"
                            type=""><?php echo $this->lang->line('common_save_change'); ?> </button><!--Save changes-->
                </div>
            </form>
        </div>
    </div>
</div>

<div class="table-responsive">
    <?php if ($grvType == 'LOG') { ?>
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th>#</th>
            <th>PO Code</th>
            <th>Logistic Amount</th>
            <th>Logistic Balance</th>
            <th>Matching Amount</th>
            <th>Actual Logistic Amount</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <?php

                $requested_total = 0.00;
                $received_total = 0.00;

                if (!empty($detail)) {
                    for ($i = 0;
                         $i < count($detail);
                         $i++) {

                        echo '<tr>';
                        echo '<td>' . ($i + 1) . '</td>';
                        echo '<td>' . $detail[$i]['purchaseOrderCode'] . '</td>';
                        echo '<td class="text-right">' . $detail[$i]['addonAmount'] . '</td>';
                        echo '<td class="text-right">' . $detail[$i]['addonBalance'] . '</td>';
                        echo '<td class="text-right">' . $detail[$i]['matchedAmount'] . '</td>';
                        echo '<td class="text-right">' . $detail[$i]['actualLogisticAmount'] . '</td>';
                        echo '<td><a onclick="delete_item(' . $detail[$i]['grvDetailsID'] . ',\'' . $detail[$i]['purchaseOrderMastertID'] . '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr class="danger"><td colspan="7" class="text-center"><b>' . $this->lang->line('common_no_records_found') . '<!--No Records Found--></b></td></tr>';
                }
                ?>
            </tr>
        </tbody>
    </table>
    <?php } else { ?>
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th colspan="4"><?php echo $this->lang->line('transaction_common_item_details'); ?> </th><!--Item Details-->

            <?php if ( $hideCost == 0) { ?>
                <th colspan="3"><?php echo $this->lang->line('transaction_common_ordered_item'); ?> <span class="currency"> (<?php echo $master['transactionCurrency'] ?>
                    )</span><!--Ordered Item-->
                </th>
            <?php if($show_cost_grv == 1){ ?>
            <th colspan="<?php echo ($isGroupBasedTaxEnable == 1 ? '6':'4')?>"><?php echo $this->lang->line('transaction_common_recived_item'); ?> <span class="currency"> <span
                            class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span><!--Received Item-->
            </th>
            <?php } ?>
            <?php } else { ?>
                <th colspan="<?php echo ($isGroupBasedTaxEnable == 1 ? '4':'3')?>">  </th>
            <?php } ?>

        </tr>
        <tr>
            <th style="min-width: 3%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code'); ?> </th>
            <!--Item Code-->
            <th style="min-width: 23%"><?php echo $this->lang->line('common_item_description'); ?>  </th>
            <!--Item Description-->
            <?php if($advanceCostCapturing == 1){ ?>
                <th style="min-width: 10%">Activity Code</th>
            <?php } ?>
            <!-- <th style="min-width: 20%">Comment</th> -->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_uom'); ?> </th><!--UOM-->

            <?php if($itemBatch == 1){?>
                <th style="min-width: 5%"><?php echo $this->lang->line('erp_grv_batch_number'); ?> </th>
                <th style="min-width: 5%"><?php echo $this->lang->line('erp_grv_batch_expire'); ?> </th>
            <?php }?>

            <?php if ( $hideCost == 0) { ?>
                <th style="min-width: 5%">Ordered <?php echo $this->lang->line('common_qty'); ?> </th><!--Qty-->
                <?php if ( $show_cost_grv == 1) { ?>
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_unit_cost'); ?>  </th><!--Unit Cost-->
                    
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_net_amount'); ?>  </th><!--Net Amount-->
                <?php } ?>
                <th style="min-width: 5%">Received <?php echo $this->lang->line('transaction_common_qty'); ?>  </th><!--Qty-->


            <?php }else { ?>
                <th style="min-width: 5%">PO  Qty</th><!--Qty-->
                <th style="min-width: 5%">GRV Qty</th><!--Qty-->
            <?php }?>
            <?php if($show_cost_grv == 1){ ?>
                <?php if ( $hideCost == 0) { ?>                
                    <th style="min-width: 12%"><?php echo $this->lang->line('common_unit_cost'); ?> </th><!--Unit Cost-->

                    <?php if($isGroupBasedTaxEnable == 1){ ?>
                    <th style="min-width: 5%" class="groupByTaxEnable">Tax </th><!--Qty-->
                    <th style="min-width: 5%" class="groupByTaxEnable">Tax Amount</th><!--Qty-->
                    <?php }?>                
                
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_net_amount'); ?> </th><!--Net Amount-->
                <?php } ?>
            <?php } ?>
               
            
            <th style="min-width: 7%">&nbsp;</th>
        </thead>
        <tbody id="grv_table_body">
        <?php 
        
        $requested_total = 0.00;
        $received_total = 0.00;

        if (!empty($detail)) {
            for ($i = 0;
                 $i < count($detail);
                 $i++) {

                echo '<tr>';
                echo '<td>' . ($i + 1) . '</td>';
                echo '<td>' . $detail[$i]['itemSystemCode'] . '</td>';
                echo '<td>' . $detail[$i]['purchaseOrderCode'] . ' : ' . $detail[$i]['itemDescription'] . ' ' . $detail[$i]['Itemdescriptionpartno'] . '   </td>';
                if($advanceCostCapturing == 1){
                    echo '<td class="text-center">' . $detail[$i]['activityCodeName'] . '</td>';
                }
                echo '<td class="text-center">' . $detail[$i]['unitOfMeasure'] . '</td>';
                if($itemBatch == 1){
                    echo '<td class="text-center">' . $detail[$i]['batchNumber'] . '</td>';
                    echo '<td class="text-center">' . $detail[$i]['batchExpireDate'] . '</td>';
                }
                
                echo '<td class="text-center">' . $detail[$i]['requestedQty'] . '</td>';
                if ($hideCost == 0) {
                    if ($show_cost_grv == 1) {
                        echo '<td class="text-right">' . format_number($detail[$i]['receivedAmount'], $master['transactionCurrencyDecimalPlaces']) . '</td>';
                    
                        echo '<td class="text-right">' . format_number(($detail[$i]['requestedQty'] * $detail[$i]['receivedAmount']), $master['transactionCurrencyDecimalPlaces']) . '</td>';
                    }
                }
                echo '<td class="text-center">' . $detail[$i]['receivedQty'] . '</td>';
                if($show_cost_grv == 1){
                    if ( $hideCost == 0) {
                        echo '<td class="text-right">' . format_number($detail[$i]['receivedAmount'], $master['transactionCurrencyDecimalPlaces']) . '</td>';
                        if($isGroupBasedTaxEnable == 1){
                            echo ' <td style="min-width: 5%" class="groupByTaxEnable">'.$detail[$i]['Description'].'</td>';
                            if($detail[$i]['taxAmount'] > 0){
                                echo ' <td style="min-width: 5%;text-align:right" class="groupByTaxEnable"><a href="#" class="drill-down-cursor" onclick="open_tax_dd(\' \','.$detail[$i]['grvAutoID'].',\'GRV\','.$master['transactionCurrencyDecimalPlaces'].','.$detail[$i]['grvDetailsID'].',\'srp_erp_grvdetails\',\'grvDetailsID\',0,1)"> ' .format_number(($detail[$i]['taxAmount']), $master['transactionCurrencyDecimalPlaces']).' </a></td>';
                            }else {
                                echo ' <td style="min-width: 5%;text-align:right" class="groupByTaxEnable">'.format_number(($detail[$i]['taxAmount']), $master['transactionCurrencyDecimalPlaces']).'</td>';
                            }        
                            echo '<td class="text-right">' . format_number(($detail[$i]['receivedTotalAmount']+$detail[$i]['taxAmount']), $master['transactionCurrencyDecimalPlaces']) . '</td>';

                        }else {
                                echo '<td class="text-right">' . format_number(($detail[$i]['receivedTotalAmount']), $master['transactionCurrencyDecimalPlaces']) . '</td>';
                            }
                    
                    } 
                }
                echo '<td class="text-right">';

                if ($detail[$i]['isSubitemExist'] == 1) {
                    $count = isProductReference_completed($detail[$i]['grvDetailsID']);
                    $color = '';
                    if ($count > 0) {
                        $color = 'color: #dad835 !important';
                    }
                    ?>
                    <a style="<?php echo $color ?>"
                       onclick="load_itemMasterSub_modal('<?php echo $detail[$i]['grvDetailsID'] ?>','GRV')"><i
                                class="fa fa-list"></i></a>
                    &nbsp;&nbsp; | &nbsp;&nbsp;
                    <?php
                }
                echo '<a onclick="edit_item(' . $detail[$i]['grvDetailsID'] . ',\'' . $detail[$i]['purchaseOrderMastertID'] . '\');"><span class="glyphicon glyphicon-pencil"></span></a> ';
                echo '&nbsp;&nbsp; | &nbsp;&nbsp; ';
                echo '<a onclick="delete_item(' . $detail[$i]['grvDetailsID'] . ',\'' . $detail[$i]['purchaseOrderMastertID'] . '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a>';
                echo '</td>';
                echo '</tr>';
               // $requested_total += ($detail[$i]['requestedQty'] * $detail[$i]['requestedAmount']);

                $requested_total += $detail[$i]['requestedQty'] * $detail[$i]['receivedAmount'];
                if($isGroupBasedTaxEnable == 1){ 
                $received_total += (($detail[$i]['receivedTotalAmount'])+$detail[$i]['taxAmount']);
                }else { 
                    $received_total += ($detail[$i]['receivedTotalAmount']);
                }
            }
        } else { 
            $norec = $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan='.($isGroupBasedTaxEnable == 1?'13':'11').' class="text-center"><b>' . $norec . '<!--No Records Found--></b></td></tr>';
        }
        ?>
        </tbody>
        
        <?php if($show_cost_grv == 1){ ?>
            <tfoot>
        <?php if ( $hideCost == 0) { ?>
        <tr>
            <td class="text-right" colspan="6">
                <?php echo $this->lang->line('transaction_ordered_item_total'); ?><!--Ordered Item Total--> <span
                        class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></td>
            <td class="text-right total"><?php echo format_number($requested_total, $master['transactionCurrencyDecimalPlaces']); ?></td>
            <td class="text-right" colspan="<?php echo ($isGroupBasedTaxEnable == 1 ? '4':'3') - $show_cost_grv ?>">
                <?php echo $this->lang->line('transaction_recived_item_total'); ?><!--Received Item Total--> <span
                        class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></td>
            <td class="text-right total"><?php echo format_number($received_total, $master['transactionCurrencyDecimalPlaces']); ?></td>
            <td>&nbsp;</td>
        </tr>
        <?php } ?>
            </tfoot>
        <?php } ?>
        
    </table>
    <?php } ?>
</div>
<hr>
<div class="text-right m-t-xs">
    <button class="btn btn-default prev" onclick=""><?php echo $this->lang->line('common_previous'); ?></button>
    <!--Previous-->
</div>


<div aria-hidden="true" role="dialog" id="grv_st_bulk_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('transaction_common_add_item_detail'); ?></h5>
                <!--Add Item Detail-->
            </div>
            <div class="modal-body">
                <form role="form" id="grv_st_bulk_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;">
                                <?php echo $this->lang->line('transaction_common_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('transaction_goods_received_project'); ?></th><!--Project-->
                                <th>Project Category</th>
                                <th>Project Subcategory</th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_uom'); ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_unit_cost'); ?><!--Unit Cost --><span
                                        class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <?php if($itemBatch == 1){?>
                            <th><?php echo $this->lang->line('erp_grv_batch_number'); ?> </th>
                            <th><?php echo $this->lang->line('erp_grv_batch_expire'); ?>  </th>
                            <?php }?>
                            <th>FOC </th>
                            <?php if($isGroupBasedTaxEnable ==1){ ?>
                                <th class="lintax"><?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                                <th class="lintax">Tax Amount</th>
                            <?php }?>
                            <th style="width: 150px;">
                        
                            <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount --><span
                                        class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <?php if($advanceCostCapturing == 1){ ?>
                                <th style="min-width: 10%">Activity Code</th>
                            <?php } ?>
                            <th style="width: 200px;"><?php echo $this->lang->line('common_comment'); ?> </th>
                            <!--Comment-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)"
                                       class="form-control search f_search" name="search[]"
                                       placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('common_item_description'); ?> ..."
                                       id="f_search_1"><!--Item ID--><!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div class="div_projectID_item">
                                        <select name="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('transaction_goods_received_select_project'); ?> </option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <?php echo form_dropdown('project_categoryID[]',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                </td>
                                <td>
                                    <?php echo form_dropdown('project_subCategoryID[]',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                </td>
                            <?php } ?>
                            <td><?php echo form_dropdown('UnitOfMeasureID[]', $uom_arr, 'Each', 'class="form-control umoDropdown"  required'); ?></td>
                            
                            <?php if($isGroupBasedTaxEnable ==1){ ?>
                            <td><input type="text" name="quantityRequested[]" onkeyup="validatetb_row(this)"
                                       onchange="change_amount(this,1),load_line_tax_amount_grv(this)" onfocus="this.select();"
                                       class="form-control number quantityRequested" required></td>
                            
                            <td><input type="text" name="estimatedAmount[]" placeholder="<?php echo $placeholder ?>"
                                       onchange="change_amount(this,1),load_line_tax_amount_grv(this)"
                                       onkeyup="validatetb_row(this)"
                                       onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                       class="form-control number estimatedAmount"></td>
                            <?php } else {?>
                                <td><input type="text" name="quantityRequested[]" onkeyup="validatetb_row(this)"
                                       onchange="change_amount(this,1)" onfocus="this.select();"
                                       class="form-control number quantityRequested" required></td>
                               
                                <td><input type="text" name="estimatedAmount[]" placeholder="<?php echo $placeholder ?>"
                                       onchange="change_amount(this,1)"
                                       onkeyup="validatetb_row(this)"
                                       onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                       class="form-control number estimatedAmount"></td>
                            <?php }?>
                            <?php if($itemBatch == 1){?>
                            <td><input type="text" name="batchNumber[]" onkeyup=""
                                       onchange="" onfocus=""
                                       class="form-control  batchNumber" required></td>

                            <td><input type="date" name="expireDate[]" onkeyup=""
                                       onchange="" onfocus=""
                                       class="form-control  expireDate" required></td>

                            <?php }?>
                            <td class="text-center">
                                <!-- <input type='hidden' value='0' name='isFoc[]'> -->
                                <input id="isFoc" type="checkbox" data-caption="" class="columnSelected" name="isFoc[]" value="1">
                            </td>

                            <?php if($isGroupBasedTaxEnable ==1){ ?>
                                <td class="lintax">
                                    <?php echo form_dropdown('text_type[]',  array('' => 'Select Tax'), '', 'class="form-control text_type" style="width: 134px;" onchange="load_line_tax_amount_grv(this)" '); ?>
                                </td>
                                
                                <td class="lintax"><span class="linetaxamnt pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                                <td>&nbsp;<span class="net_amount pull-right"
                                                style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>

                                </td>
                                            
                          <?php }else { ?>
                            <td><input type="text" name="receivedTotalAmount[]" placeholder="<?php echo $placeholder ?>"
                                       onchange="change_amount(this,2)"
                                       onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                       class="form-control number receivedTotalAmount"></td>
                            <?php }?>

                            <?php if($advanceCostCapturing == 1){ ?>
                                <td>
                                    <?php echo form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2" id="activityCode" '); ?>
                                </td>
                            <?php } ?>
                          
                            <td><textarea class="form-control" rows="1" name="comment[]"
                                          placeholder="<?php echo $this->lang->line('transaction_common_item_comment'); ?>..."
                                ></textarea></td><!--Item Comment-->
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                <button class="btn btn-primary" type="button"
                        onclick="savePurchaseOrderDetails()"><?php echo $this->lang->line('common_save_change'); ?> </button>
                <!--Save changes-->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var search_id = 1;
    var grvAutoID;
    var grvDetailsID;
    var grvType;
    var supplierID;
    var currencyID;
    var currency_decimal;
    var projectID;
    var projectcategory;
    var projectsubcat;
    var showPurchasePrice = <?php echo $showPurchasePrice ?>;
    var isGroupByTaxEnable = 0;
    var select_VAT_value = '';
    $(document).ready(function () {
        grvDetailsID = null;
        projectID = null;

        grvAutoID = <?php echo json_encode(trim($grvAutoID)); ?>;
        grvType = <?php echo json_encode(trim($master['grvType'] ?? '')); ?>;
        currency_decimal = <?php echo json_encode(trim($master['transactionCurrencyDecimalPlaces'] ?? '')); ?>;
        supplierID = <?php echo json_encode(trim($master['supplierID'] ?? '')); ?>;
        currencyID = <?php echo json_encode(trim($master['transactionCurrency'] ?? '')); ?>;
        isGroupByTaxEnable = <?php echo json_encode(trim($isGroupBasedTaxEnable)) ?>;
        $('.currency').html(' ( ' + currencyID + ' )');
        initializeitemTypeahead(search_id);
        number_validation();
        $("#grv_detail_form").submit(function (e) {
            save_grv_detail_form(grvAutoID, grvDetailsID);
            return false;
        })
        $("[rel=tooltip]").tooltip();
    });


    function qtyValidation(){
        var bal_qty = parseFloat($("#bal_qty").val());
        var quantityRequested = parseFloat($("#quantityRequested").val());
        if(bal_qty<quantityRequested){
            swal("Warning", "Qty cannot be greater than PO balance Qty!", "warning");
        }
    }

    function save_grv_detail_form(grvAutoID, grvDetailsID) {
        var data = $("#grv_detail_form").serializeArray();
        data.push({'name': 'grvAutoID', 'value': grvAutoID});
        data.push({'name': 'grvDetailsID', 'value': grvDetailsID});
        data.push({'name': 'uom', 'value': $('#UnitOfMeasureID option:selected').text()});
        data.push({'name': 'tax_type', 'value': $('#tax_type').val()});

        var bal_qty = parseFloat($("#bal_qty").val());
        var quantityRequested = parseFloat($("#quantityRequested").val());

        if(bal_qty<quantityRequested){
            swal("Warning", "Qty cannot be greater than PO balance Qty!", "warning");
        }else{
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Grv/save_grv_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    /*$form.bootstrapValidator('resetForm', true);*/

                    refreshNotifications(true);
                    if (data['status'] == true) {

                        $('#edit_activityCode').val('').change();
                        $('#grv_st_detail_modal').modal('hide');
                        grvDetailsID = null;
                    }

                    if (data['status']) {
                        setTimeout(function () {
                            fetch_details();
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

    }

    function grv_st_detail_modal() {
        if (grvAutoID) {
            $('#grv_detail_form')[0].reset();
            /*$('#grv_detail_form').bootstrapValidator('resetForm', true);*/
            /*$("#grv_st_detail_modal").modal({backdrop: "static"});*/
            $("#grv_st_detail_modal").modal('show');
        }
    }

    function grv_st_bulk_detail_modal() {
        var mfqWarehouse = $('#mfqWarehouseAutoID').val();
        var jobID = $('#jobID').val();
        if(mfqWarehouse != '' && jobID == '') {
            myAlert('e', 'Please Assign Job Before adding Details!');
        } else {
            if (grvAutoID) {
                $('#grv_st_bulk_detail_form')[0].reset();
                $('#discount').val(0);
                $('#discount_amount').val(0);
                load_segmentBase_projectID_item();
                $('#po_detail_add_table tbody tr').not(':first').remove();
                $('.f_search').closest('tr').css("background-color", 'white');
                $('.quantityRequested').closest('tr').css("background-color", 'white');
                $('.estimatedAmount').closest('tr').css("background-color", 'white');
                $("#grv_st_bulk_detail_modal").modal({backdrop: "static"});
            }
        }
    }

    function add_more() {

        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#po_detail_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('.umoDropdown,.item_text').empty();
        appendData.find('.umoDropdown').prop('disabled', false);
        appendData.find('.umoDropdown').removeClass('uom_disabled');
        appendData.find('input').val('');
        appendData.find('input[type="checkbox"]').val('1');
        appendData.find('textarea').val('');
        appendData.find('.linetaxamnt').html('0.00');
        appendData.find('.net_amount').html('0.00');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#po_detail_add_table').append(appendData);
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        var lenght = $('#po_detail_add_table tbody tr').length - 1;

        $(".select2").select2();
        initializeitemTypeahead(search_id);
        number_validation();
    }

    function savePurchaseOrderDetails() {
        $('.uom_disabled').prop('disabled', false);
        var data = $('#grv_st_bulk_detail_form').serializeArray();
        if (grvAutoID) {
            data.push({'name': 'grvAutoID', 'value': grvAutoID});
            data.push({'name': 'grvDetailsID', 'value': grvDetailsID});
            $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                data.push({'name': 'uom[]', 'value': $(this).text()})
            });
            $('.itemAutoID').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2');
                }
            });
            $('.quantityRequested').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2');
                }
            });
            $('.estimatedAmount').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2');
                }
            });
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Grv/save_grv_st_bulk_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('.uom_disabled').prop('disabled', true);
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        grvDetailsID = null;
                        $('#activityCode').val('').change();
                        $('#grv_st_bulk_detail_modal').modal('hide');
                        setTimeout(function () {
                            fetch_details();
                        }, 300);
                    }
                }, error: function () {
                    $('.uom_disabled').prop('disabled', true);
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        } else {
            swal({
                title: "Good job!",
                text: "You clicked the button!",
                type: "success"
            });
        }
    }

    function clearitemAutoIDEdit(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('#itemAutoID').val('');
        }
    }

    function clearitemAutoID(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }

    }

    function initializeitemTypeahead(id) {
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN&documentID=<?php echo $master['documentID'] ?>',
            onSelect: function (suggestion) {
                var cont = true;
                if (cont) {
                    fetch_related_uom_id_b(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this, suggestion['isSubitemExist']);
                    if(showPurchasePrice == 1){
                        fetch_purchase_price(suggestion.companyLocalPurchasingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                    }
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                    }, 200);
                    $(this).closest('tr').find('.quantityRequested').focus();
                    $(this).closest('tr').css("background-color", 'white');
                    if(isGroupByTaxEnable == 1){ 
                        fetch_line_tax_and_vat(suggestion.itemAutoID, this)
                    }
                   
                }
            }
        });
        $('#f_search_' + id).off('focus.autocomplete');
    }

    function initializeitemTypeahead_edit() {

        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN&documentID=<?php echo $master['documentID'] ?>',
            onSelect: function (suggestion) {
                /*fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);*/ // Commented by shafry as Muba said not using this.
                fetch_related_uom_id_b(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this, suggestion['isSubitemExist']);
                // $(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                setTimeout(function () {
                    $('#search').closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                $(this).closest('tr').find('#receivedTotalAmount').focus();
            }
        });
    }

    function LoaditemUnitPrice_againtsExchangerate(LocalWacAmount, element) {
        //poID = grvAutoID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'poID': grvAutoID, 'LocalWacAmount': LocalWacAmount},
            url: "<?php echo site_url('ItemMaster/load_unitprice_exchangerate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data['amount']);
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

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

    function fetch_po_detail_table(purchaseOrderID) {
        $('.pull-li').removeClass('pulling-based-li');
        $('#pull-'+purchaseOrderID).addClass('pulling-based-li');
        if (purchaseOrderID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'purchaseOrderID': purchaseOrderID},
                url: "<?php echo site_url('Grv/fetch_po_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#table_body').empty();
                    $('#table_tfoot').empty();
                    $('#purchaseOrderID').val(purchaseOrderID);
                    x = 1;
                    var colspan = '9';

                    if (jQuery.isEmptyObject(data['detail'])) {

                        if(isGroupByTaxEnable == 1){ 
                             colspan = '11';
                        }
                        $('#table_body').append('<tr class="danger"><td colspan="'+colspan+'" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                     
                    } else {
                        tot_amount = 0;
                        receivedQty = 0;
                       var potaxamnt1 = 0;
                       var potaxamnt = 0;
                        $.each(data['detail'], function (key, value) {
                            if(value['generalTaxAmount']==null){
                                value['generalTaxAmount']=0;
                            }
                            potaxamnt = 0; //(((parseFloat(value['taxAmount'])) +( parseFloat(value['generalTaxAmount']))  )/value['requestedQty']);
                          

                            if (value['receivedQty'] != null && value['receivedQty'] !== undefined) {
                                receivedQty = value['receivedQty'];
                            }
                            if(grvType=="Standard")
                            {
                                if(isGroupByTaxEnable == 1){
                                    cost_status = '<input type="text" class="number" size="15" id="amount_' + value['purchaseOrderDetailsID'] + '" value="' + parseFloat(value['unitAmount']-(value['generalDiscountAmount']/value['requestedQty'])).toFixed(currency_decimal) + '" onkeyup="select_value(' + value['purchaseOrderDetailsID'] + ');" onkeypress="return validateFloatKeyPress(this,event);" >';
                                }else {
                                    cost_status = '<input type="text" class="number" size="15" id="amount_' + value['purchaseOrderDetailsID'] + '" value="' + parseFloat(value['unitAmount'] - (value['generalDiscountAmount'] / value['requestedQty']) + potaxamnt).toFixed(currency_decimal) + '" onkeyup="select_value(' + value['purchaseOrderDetailsID'] + ');" onkeypress="return validateFloatKeyPress(this,event)">';
                                }
                            }else
                            {
                                if(isGroupByTaxEnable == 1){
                                    cost_status = '<input  onkeyup="select_check_box(this,' + value['purchaseOrderDetailsID'] + ',' + value['unitAmount'] + ' )" type="text" class="number" size="15" id="amount_' + value['purchaseOrderDetailsID'] + '" value="' + parseFloat(value['unitAmount']-(value['generalDiscountAmount']/value['requestedQty'])) + '" onkeyup="select_value(' + value['purchaseOrderDetailsID'] + ');" onkeypress="return validateFloatKeyPress(this,event)" >';
                                }else {
                                    cost_status = '<input  onkeyup="select_check_box(this,' + value['purchaseOrderDetailsID'] + ',' + value['unitAmount'] + ' )" type="text" class="number" size="15" id="amount_' + value['purchaseOrderDetailsID'] + '" value="' + parseFloat(value['unitAmount']-(value['generalDiscountAmount']/value['requestedQty'])+potaxamnt) + '" onkeyup="select_value(' + value['purchaseOrderDetailsID'] + ');" onkeypress="return validateFloatKeyPress(this,event)">';
                                }

                            }

                            if (data['policy_po_cost_change'] == 0) {

                                if(isGroupByTaxEnable == 1){ 
                                    cost_status = '<input type="hidden" class="number" size="15" id="amount_' + value['purchaseOrderDetailsID'] + '" value="' + parseFloat(value['unitAmount']-(value['generalDiscountAmount']/value['requestedQty'])) + '" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="select_value(' + value['purchaseOrderDetailsID'] + ')" >' + parseFloat(value['unitAmount']-(value['generalDiscountAmount']/value['requestedQty']));
                                }else{ 
                                    cost_status = '<input type="hidden" class="number" size="15" id="amount_' + value['purchaseOrderDetailsID'] + '" value="' + parseFloat(value['unitAmount']-(value['generalDiscountAmount']/value['requestedQty'])+potaxamnt) + '" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="select_value(' + value['purchaseOrderDetailsID'] + ')" >' + parseFloat(value['unitAmount']-(value['generalDiscountAmount']/value['requestedQty'])+potaxamnt);
                                }

                             
                            }
                   

                            if(isGroupByTaxEnable == 1){
                            $('#table_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + (value['qtybalance']) + '<input class="hidden" id="balQty_' + value['purchaseOrderDetailsID'] + '" value="' + value['qtybalance'] + '"></td><td class="hideCostClass text-right">' + parseFloat(value['unitAmount']-(value['generalDiscountAmount']/value['requestedQty'])).toFixed(currency_decimal) + '</td><td class="text-center"><i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' +value['purchaseOrderDetailsID'] + ',' + value['unitAmount']+')" aria-hidden="true"></i> <input type="text" class="number" size="8" id="qty_' + value['purchaseOrderDetailsID'] + '" onkeyup="select_check_box(this,' + value['purchaseOrderDetailsID'] + ',' + value['unitAmount'] + ' )"></td><td class="text-center hideCostClass">' + cost_status + '</td><td>'+value['Description']+'<input class="hidden" id="taxCalculationFormulaID_' + value['purchaseOrderDetailsID'] + '" value="' + value['taxCalculationformulaID'] + '"></td><td class="hideCostClass"><p id="totTaxCal_' + value['purchaseOrderDetailsID'] + '"> </p></td><td class="text-center hideCostClass"><p id="tot_' + value['purchaseOrderDetailsID'] + '"> </p></td><td class="text-right" style="display: none;"><input class="checkbox" id="check_' + value['purchaseOrderDetailsID'] + '" type="checkbox" value="' + value['purchaseOrderDetailsID'] + '"></td></tr>');
                            }else{ 
                            $('#table_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + (value['qtybalance']) + '<input class="hidden" id="balQty_' + value['purchaseOrderDetailsID'] + '" value="' + value['qtybalance'] + '"></td><td class="hideCostClass text-right <?php echo $ele; ?>">' + parseFloat(value['unitAmount']-(value['generalDiscountAmount']/value['requestedQty'])+potaxamnt).toFixed(currency_decimal) + '</td><td class="text-center"><i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' +value['purchaseOrderDetailsID'] + ',' + value['unitAmount']+')" aria-hidden="true"></i> <input type="text" class="number" size="8" id="qty_' + value['purchaseOrderDetailsID'] + '" onkeyup="select_check_box(this,' + value['purchaseOrderDetailsID'] + ',' + value['unitAmount'] + ' )"></td><td class="text-center hideCostClass <?php echo $ele; ?>">' + cost_status + '</td><td class="text-center hideCostClass <?php echo $ele; ?> "><p id="tot_' + value['purchaseOrderDetailsID'] + '"> </p></td><td class="text-right" style="display: none;"><input class="checkbox" id="check_' + value['purchaseOrderDetailsID'] + '" type="checkbox" value="' + value['purchaseOrderDetailsID'] + '"></td></tr>');
                            }
                            if(data['hideCost'] == 1) {
                                $('.hideCostClass').addClass('hidden');
                            } else {
                                $('.hideCostClass').removeClass('hidden');
                            }

                            x++;
                            tot_amount += (parseFloat(value['totalAmount']).toFixed(currency_decimal));
                            //.formatMoney(currency_decimal, '.', ',')
                        });
                    }
                    number_validation();
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
        ;
    }

    function fetch_related_uom_id(masterUnitID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $('#UnitOfMeasureID').empty();
                var mySelect = $('#UnitOfMeasureID');
                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $("#UnitOfMeasureID").val(select_value);
                        //$('#grv_detail_form').bootstrapValidator('revalidateField', 'UnitOfMeasureID');
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_related_uom_id_b(masterUnitID, select_value, element, isSubItemExist) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.umoDropdown').empty();

                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');

                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                        if (isSubItemExist == 1) {
                            $(element).closest('tr').find('.umoDropdown').prop('disabled', true);
                            $(element).closest('tr').find('.umoDropdown').addClass('uom_disabled', true);
                        }
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function select_check_box(data, id, amount) {
        var qty = $('#qty_' + id).val();
        var bal_qty = $('#balQty_' + id).val();
        var taxAmount = 0;
        var taxCalculationFormulaID = $('#taxCalculationFormulaID_'+id).val();
        bal_qty = parseFloat(bal_qty);
        qty = parseFloat(qty);
        var purchaseOrderID =  $('#purchaseOrderID').val();
        if(qty > bal_qty ){
            
            $('#qty_' + id).val('');
            $('#tot_' + id).text('');
            swal("Warning", "Qty cannot be greater than PO balance Qty!", "warning");
        }
        else {
            $("#check_" + id).prop("checked", false);
            if (data.value >= 0) {
                $("#check_" + id).prop("checked", true);
            }
            amount = $('#amount_' + id).val();
            if (amount < 0) {
                amount = 0;
            }
            var total = qty * amount;
            var totalnew = (parseFloat(total).toFixed(currency_decimal));

            if(isGroupByTaxEnable == 1 && taxCalculationFormulaID!=0){

                linewiseTax(id,taxCalculationFormulaID,(qty*amount),purchaseOrderID,qty);
            }else { 
                $('#tot_' + id).text(totalnew);   
            }            
        }
    }

    function select_value(id) {
        var qty = $('#qty_' + id).val();
        if (qty < 0) {
            qty = 0;
        }
        amount = $('#amount_' + id).val();
        if (amount < 0) {
            amount = 0;
        }
        var total = qty * amount;
        var totalnew = (parseFloat(total).toFixed(currency_decimal));//.formatMoney(currency_decimal, '.', ',')
        $('#tot_' + id).text(totalnew);
    }


    function delete_item(id, value) {
        if (grvAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'grvDetailsID': id},
                        url: "<?php echo site_url('Grv/delete_grv_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            fetch_details();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function edit_item(id, value) {
        if (grvAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>", /*You want to edit this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit');?>", /*Edit*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'grvDetailsID': id,'purchaseOrderID': value},
                        url: "<?php echo site_url('Grv/fetch_grv_detail'); ?>",
                        beforeSend: function () {
                            $("#grv_st_detail_modal").modal('show');
                            startLoad();
                        },
                        success: function (data) {
                            grvDetailsID = data['grvDetailsID'];
                            projectID = data['projectID'];
                            projectcategory = data['project_categoryID'];
                            projectsubcat = data['project_subCategoryID'];
                            $('#search').val(data['itemDescription'] + " - " + data['itemSystemCode'] + " - "+data['seconeryItemCode']);
                            fetch_related_uom_id(data['defaultUOMID'], data['unitOfMeasureID']);
                            $('#quantityRequested').val(data['receivedQty']);
                            $('#qty_unchanged').val(data['receivedQty']);
                            $('#bal_qty').val(data['qtybalance']);
                            $('#estimatedAmount').val(data['receivedAmount']);
                            $('#receivedTotalAmount').val(data['receivedTotalAmount']);
                            $('#search_id').val(data['itemSystemCode']);
                            $('#itemSystemCode').val(data['itemSystemCode']);
                            $('#itemAutoID').val(data['itemAutoID']);
                            $('#itemDescription').val(data['itemDescription']);
                            $('#comment').val(data['comment']); 
                            $('#edit_activityCode').val(data['activityCodeID']).change(); 
                            $('#batchNumber').val(data['batchNumber']);
                            $('#expireDate').val(data['batchExpireDate']);
                            $('#remarks').val(data['remarks']);

                            if(data['isFoc'] == 1){
                                $('#isFocEdit').prop('checked',true);
                            }else{
                                $('#isFocEdit').prop('checked',false);
                            }

                            $('#grvPurchaseOrderID').val(data['purchaseOrderMastertID']);
                            $('#grvPurchaseOrderDetailID').val(data['purchaseOrderDetailsID']);
                            $('#grvTaxCalculationFormulaID').val(data['taxCalculationformulaID']);

                            if (value != 0) {
                                $('.hide_po').hide();
                            }
                          
                            if(isGroupByTaxEnable == 1){ 
                                $('#linetaxDescription').html(data['Description']);
                                $('#tax_type').val(data['taxtype']);
                                $('#linetaxamnt_edit').html( parseFloat( data['taxAmountLedger']).toFixed(currency_decimal));
                                $('#receivedTotalAmount').val( ( parseFloat(data['receivedTotalAmount']) + parseFloat(data['taxAmountLedger'])));
                            }
                         
                            /*$("#grv_st_detail_modal").modal({backdrop: "static"});*/
                            load_segmentBase_projectID_itemEdit();
                            initializeitemTypeahead_edit();
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }

    function save_po_base_items() {
        var selected = [];
        var amount = [];
        var qty = [];
        var taxCalculationMasterID = [];
        $('#table_body input:checked').each(function () {
            if ($('#amount_' + $(this).val()).val() == '') {
                swal("Cancelled", "Received cost cannot be blank !", "error");
            } else {
                selected.push($(this).val());
                amount.push($('#amount_' + $(this).val()).val());
                qty.push($('#qty_' + $(this).val()).val());
                taxCalculationMasterID.push($('#taxCalculationFormulaID_' + $(this).val()).val());
            }
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'DetailsID': selected, 'grvAutoID': grvAutoID, 'amount': amount, 'qty': qty,'taxCalculationMasterID':taxCalculationMasterID},
                url: "<?php echo site_url('Grv/save_po_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        $('#po_base_modal').modal('hide');
                        setTimeout(function () {
                            fetch_details();
                        }, 300);
                    } else {
                        myAlert('w', data['data'], 1000);
                    }

                }, error: function () {
                    $('#po_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function save_logistic_po_base_items() {
        let selected = [];
        $('#logistic_table_body input:checked').each(function () {
            selected.push($(this).val());
        });

        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'detailsID': selected, 'grvAutoID': grvAutoID},
                url: "<?php echo site_url('Grv/saveLogisticPoBaseItems'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        $('#logistic_po_base_modal').modal('hide');
                        setTimeout(function () {
                            fetch_details();
                        }, 300);
                    } else {
                        myAlert('w', data, 1000);
                    }

                }, error: function () {
                    $('#logistic_po_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }else{
            swal("Cancelled", "Please select an item", "error");
        }
    }

    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }

    //thanks: http://javascript.nwbox.com/cursor_position/
    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function load_segmentBase_projectID_income() {
        var segment = $('#segment').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase_multiple"); ?>',
            dataType: 'html',
            data: {segment: segment},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.div_projectID_item').html(data);
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function load_segmentBase_projectID_item() {
        var segment = $('#segment').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase_multiple"); ?>',
            dataType: 'html',
            data: {segment: segment},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.div_projectID_item').html(data);
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function load_segmentBase_projectID_itemEdit() {
        var segment = $('#segment').val();
        var type = 'item';
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase"); ?>',
            dataType: 'html',
            data: {segment: segment, type: type},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#edit_div_projectID_item').html(data);
                $('.select2').select2();
                if (projectID) {
                    $("#projectID_item").val(projectID).change()
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function change_amount(field, val) {

        var quantityRequested = 0;
        var estimatedAmount = 0;
        var receivedTotalAmount = 0;
        var unitamt = 0;
        if (val == 1) {
            quantityRequested = $(field).closest('tr').find('.quantityRequested').val();
            estimatedAmount = $(field).closest('tr').find('.estimatedAmount').val();
            var totamt = quantityRequested * estimatedAmount;
            
            if(isGroupByTaxEnable == 1){
            $(field).closest('tr').find('.net_amount').text(((parseFloat(totamt)).toFixed(currency_decimal)));
            }
           
            $(field).closest('tr').find('.receivedTotalAmount').val(((parseFloat(totamt)).toFixed(currency_decimal)));
            

        } else {
            quantityRequested = $(field).closest('tr').find('.quantityRequested').val();
            receivedTotalAmount = $(field).closest('tr').find('.receivedTotalAmount').val();

            if (quantityRequested) {
                unitamt = receivedTotalAmount / quantityRequested;
            }
            $(field).closest('tr').find('.estimatedAmount').val(unitamt);
        }
        var quantity = $(field).closest('tr').find('.quantityRequested').val();

    }

    function change_amount_edit(field, val,grvType) {

        if(isGroupByTaxEnable == 1){
            load_line_tax_amount_edit(field)
        }else {
            var quantityRequested = 0;
            var estimatedAmount = 0;
            var receivedTotalAmount = 0;
            var bal_qty = 0;
            var qty_unchanged = 0;
            var final_qtyBal = 0;
            if (val == 1) {

                bal_qty = $('#bal_qty').val();
                qty_unchanged = $('#qty_unchanged').val();
                final_qtyBal = parseFloat(bal_qty) + parseFloat(qty_unchanged);

                quantityRequested = $('#quantityRequested').val();
                estimatedAmount = $('#estimatedAmount').val();
                if(parseFloat(quantityRequested) > parseFloat(bal_qty) && grvType == 2){
                    $('#bal_qty').val(bal_qty);
                    $('#qty_unchanged').val('0');
                    $('#quantityRequested').val('');
                    $('#receivedTotalAmount').val('');
                    swal("Warning", "Qty cannot be greater than PO balance Qty!", "warning");
                }else{
                    if(quantityRequested == null){
                        quantityRequested = 0;
                    }

                    var totamt = quantityRequested * estimatedAmount;
                    $('#receivedTotalAmount').val(((parseFloat(totamt)).toFixed(currency_decimal)));
                    $('#qty_unchanged').val((parseFloat(quantityRequested)).toFixed(4));

                }

            } else {
                quantityRequested = $('#quantityRequested').val();
                receivedTotalAmount = $('#receivedTotalAmount').val();
                var unitamt = receivedTotalAmount / quantityRequested;
                $('#estimatedAmount').val(unitamt);
            }
        }




    }

    function validatetb_row(det) {
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }
    function load_project_segmentBase_category(element,projectID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase_category"); ?>',
            dataType: 'json',
            data: {projectID: projectID},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $(element).parent().closest('tr').find('.project_subCategoryID').empty();
                var subCat = $(element).parent().closest('tr').find('.project_subCategoryID');
                subCat.append($('<option></option>').val('').html('Select Project Subcategory'));
                $(element).parent().closest('tr').find('.project_categoryID').empty();
                var mySelect =   $(element).parent().closest('tr').find('.project_categoryID');
                mySelect.append($('<option></option>').val('').html('Select Project Category'));

                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['categoryID']).html(text['categoryCode']+' - '+text['categoryDescription']));
                    });
                    if (projectcategory) {
                        $("#project_categoryID_edit").val(projectcategory).change();
                        $("#project_categoryID_edit1").val(projectcategory).change();
                    }
                }
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }
    function fetch_project_sub_category(element,categoryID) {
        var projectID = $(element).closest('tr').find('.projectID').val();

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/fetch_project_sub_category"); ?>',
            dataType: 'json',
            data: {categoryID: categoryID,projectID:projectID},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $(element).parent().closest('tr').find('.project_subCategoryID').empty();
                var mySelect =  $(element).parent().closest('tr').find('.project_subCategoryID');
                mySelect.append($('<option></option>').val('').html('Select Project Subcategory'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['subCategoryID']).html(text['description']));
                    });
                    if (projectsubcat) {
                        $("#project_subCategoryID_edit").val(projectsubcat).change();
                        $("#project_subCategoryID_edit1").val(projectsubcat).change();

                    };
                }
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }
    function fetch_purchase_price(purchaseprice, element, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: grvAutoID,
                purchaseprice: purchaseprice,
                //unitOfMeasureID: unitOfMeasureID,
                //itemAutoID: itemAutoID,
                tableName: 'srp_erp_grvmaster',
                primaryKey: 'grvAutoID',

            },
            url: "<?php echo site_url('ItemMaster/fetch_purchase_price'); ?>",
            success: function (data) {
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data.amount);
                }
                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function po_model_load()
    {
        var mfqWarehouse = $('#mfqWarehouseAutoID').val();
        var jobID = $('#jobID').val();
        if(mfqWarehouse != '' && jobID == '') {
            myAlert('e', 'Please Assign Job Before adding Details!');
        } else {
            $('#no_rec_colspan').attr('colspan',9);
            if(isGroupByTaxEnable == 1){
              
                $('#no_rec_colspan').attr('colspan',11);
                $('#ItemColspanGroupTax').attr('colspan',8);
                $('.groupByTaxEnable').removeClass('hide');
            }
            $("#po_base_modal").modal({backdrop: "static"});
        }
    }

    function logistic_po_model_load()
    {
        $("#logistic_po_base_modal").modal({backdrop: "static"});
    }


    function fetch_line_tax_and_vat(itemAutoID, element)
    {
        select_VAT_value = '';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'grvAutoID': grvAutoID,'itemAutoID':itemAutoID},
            url: "<?php echo site_url('Grv/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.text_type').empty();
                var mySelect = $(element).parent().closest('tr').find('.text_type');
                mySelect.append($('<option></option>').val('').html('Select Tax'));
                if (!jQuery.isEmptyObject(data['tax_drop'])) {
                    $.each(data['tax_drop'], function (val, text) {
                        mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
                    });
                    if(select_VAT_value == ''){

                        if(data['selected_itemTax']!=0){
                            $(element).closest('tr').find('.text_type').val(data['selected_itemTax']).change();
                        }else{

                            $(element).closest('tr').find('.text_type').val(null).change();
                        }
                        change_amount(element,1);
                    }

                    if (select_VAT_value) {
                        $(element).closest('tr').find('.text_type').val(select_VAT_value);
                    }
                }

                load_line_tax_amount_grv(element);
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function load_line_tax_amount_grv(ths){
        var qut = $(ths).closest('tr').find('.quantityRequested').val();
        var itemAutoID = $(ths).closest('tr').find('.itemAutoID').val();
        var amount = $(ths).closest('tr').find('.estimatedAmount').val();
        var taxtype = $(ths).closest('tr').find('.text_type').val();
        var lintaxappamnt=0;
        if (jQuery.isEmptyObject(qut)) {
            qut=0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }
        lintaxappamnt=(qut*(amount));
        if (!jQuery.isEmptyObject(taxtype)) {
          
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'grvAutoID':grvAutoID, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'itemAutoID':itemAutoID,'grvDetailsID':grvDetailsID},
                url: "<?php echo site_url('Grv/load_line_tax_amount_grv'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $(ths).closest('tr').find('.linetaxamnt').text(data.toFixed(currency_decimal));
                    $(ths).closest('tr').find('.net_amount').text(((parseFloat(data)  +  parseFloat(lintaxappamnt ))).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $(ths).closest('tr').find('.linetaxamnt').text('0');
            $(ths).closest('tr').find('.net_amount').text(((parseFloat(lintaxappamnt ))).toFixed(currency_decimal));
        }

    }




    function load_line_tax_amount_edit(ths){
        var qut = $('#quantityRequested').val();
        var cost = $('#estimatedAmount').val();
        var taxtype = $('#tax_type').val();
        var itemAutoID = $('#itemAutoID').val();
        var DocType = $('#grvType').val();
        var lintaxappamnt=0;
        if (jQuery.isEmptyObject(qut)) {
            qut=0;
        }
        if(DocType == 'PO Base'){
            linewiseTaxEdit(qut);
        }else {
            if (jQuery.isEmptyObject(cost)) {
                cost=0;
            }
            if (!jQuery.isEmptyObject(taxtype)) {
                lintaxappamnt=(qut*(cost));
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'grvAutoID':grvAutoID, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'itemAutoID':itemAutoID,'grvDetailsID':grvDetailsID},
                    url: "<?php echo site_url('Grv/load_line_tax_amount_grv'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications();
                        stopLoad();
                        $('#linetaxamnt_edit').text(data.toFixed(currency_decimal));
                        $('#receivedTotalAmount').val((data+lintaxappamnt).toFixed(currency_decimal));
                        
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        stopLoad();
                    }
                });
            }else{
                $('#linetaxamnt_edit').text('0');
            }
        }



    }


    function linewiseTax(id,taxCalculationFormulaID,total,purchaseOrderID,qty){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'taxCalculationFormulaID': taxCalculationFormulaID,'total':total,'grvDetailsID':grvDetailsID,'grvAutoID':grvAutoID,'purchaseOrderID':purchaseOrderID,'purchaseOrderDetailID':id,'qty':qty},
            url: "<?php echo site_url('Grv/fetch_lineWiseTax'); ?>",
            success: function (data) {
                $('#totTaxCal_'+id).text(data);
                $('#tot_' + id).text((parseFloat(total)+parseFloat(data)).toFixed(currency_decimal));
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function linewiseTaxEdit(qty){

        var id = $('#grvPurchaseOrderDetailID').val();
        var taxCalculationFormulaID  = $('#grvTaxCalculationFormulaID').val();
        var purchaseOrderID = $('#grvPurchaseOrderID').val();
        var unitcost = $('#estimatedAmount').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'taxCalculationFormulaID': taxCalculationFormulaID,'grvDetailsID':grvDetailsID,'grvAutoID':grvAutoID,'purchaseOrderID':purchaseOrderID,'purchaseOrderDetailID':id,'qty':qty},
            url: "<?php echo site_url('Grv/fetch_lineWiseTax'); ?>",
            success: function (data) {
                $('#linetaxamnt_edit').html(parseFloat(data).toFixed(currency_decimal));
                $('#receivedTotalAmount').val((( (parseFloat(qty))* (parseFloat(unitcost)))  + (parseFloat(data))).toFixed(currency_decimal));
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }


    function setQty(orderDetailsID, amount) {
        var reqQtyId = "#balQty_"+orderDetailsID;
        var ordQtyId = "#qty_"+orderDetailsID;
        $(ordQtyId).val($(reqQtyId).val());
        var data = {value:$(ordQtyId).val()};
        select_check_box(data,orderDetailsID,amount);
    }

    function fetch_logistic_po_detail_table(purchaseOrderID) {
        $('.pull-li').removeClass('pulling-based-li');
        $('#pull-'+purchaseOrderID).addClass('pulling-based-li');
        if (purchaseOrderID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'purchaseOrderID': purchaseOrderID},
                url: "<?php echo site_url('Grv/fetchLogisticPoDetail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#logistic_table_body').empty();
                    $('#logistic_table_tfoot').empty();
                    $('#logistic_purchaseOrderID').val(purchaseOrderID);
                    x = 1;

                    if (jQuery.isEmptyObject(data)) {
                        $('#logistic_table_body').append('<tr class="danger"><td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                    } else {
                        $.each(data, function (key, value) {
                            $('#logistic_table_body').append('<tr><td>' + x + '</td><td>' + value['addonCatagory'] + '</td><td class="text-right">' + parseFloat(value['addonAmount']).toFixed(currency_decimal)  + '</td><td class="text-right">' + parseFloat(value['addonBalance']).toFixed(currency_decimal)  + '</td><td class="text-right">' + parseFloat(value['matchedAmount']).toFixed(currency_decimal) + '</td><td class="text-right">' + parseFloat(value['actualLogisticAmount']).toFixed(currency_decimal) + '</td><td class="text-center"><input class="checkbox" id="check_' + value['poLogisticID'] + '" type="checkbox" value="' + value['poLogisticID'] + '"></td></tr>');
                            x++;
                        });
                    }
                    number_validation();
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
        ;
    }




</script>