<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    textarea.form-control {
        padding: 5px !important;
    }
</style>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$projectExist = project_is_exist();
$uom_arr = array('' => 'Select UOM');
$buybackinvoice = array('' => 'Select Purchase Order ');
$showPurchasePrice = getPolicyValues('SPP', 'All');
if($showPurchasePrice==' ' || $showPurchasePrice== null || empty($showPurchasePrice)){
    $showPurchasePrice = 0;
}
if ($grvType == 'PO Base') { ?>
    <div class="row">
        <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i
                        class="fa fa-hand-o-right"></i><?php echo $this->lang->line('transaction_goods_add_item_po_base'); ?>
            </h4><h4></h4><!--Add Item PO Base-->
        </div>
        <div class="col-md-4">
            <button type="button" data-toggle="modal" data-target="#po_base_modal" id="pobasebtn" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('transaction_addpo'); ?>
            </button><!--Add PO-->
        </div>
    </div><br>
    <div class="modal fade" id="po_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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

                            <div class="col-md-2">
                                <?php echo form_dropdown('salespurchaseorder',$buybackinvoice, '', 'class="form-control select2 salespurchaseorder" id="salespurchaseorder" onchange="fetch_po_detail_table(this.value)"'); ?>
                            </div>
                        <br>
                        <br>
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped table-condesed ">
                                <thead>
                                <tr>
                                    <th colspan='4'><?php echo $this->lang->line('common_item'); ?> </th><!--Item-->
                                    <th colspan='2'><?php echo $this->lang->line('transaction_common_ordered_item'); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span>
                                    </th><!--Ordered Item-->
                                    <th colspan='7'><?php echo $this->lang->line('transaction_common_recived_item'); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span>
                                    </th><!--Received Item-->
                                <tr>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo $this->lang->line('common_code'); ?> </th><!--Code-->
                                    <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th>
                                    <!--Description-->
                                    <th><?php echo $this->lang->line('common_uom'); ?> </th><!--UOM-->
                                    <th><?php echo $this->lang->line('common_qty'); ?></th><!--Qty-->
                                    <th><?php echo $this->lang->line('common_cost'); ?></th><!--Cost-->
                                    <th>No Item </th><!--Qty-->
                                    <th>Gross Qty	 </th><!--Qty-->
                                    <th>Buckets </th><!--Qty-->
                                    <th>B weight </th><!--Qty-->
                                    <th><?php echo $this->lang->line('common_qty'); ?> </th><!--Qty-->

                                    <th><?php echo $this->lang->line('common_cost'); ?> </th><!--Cost-->
                                    <th><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                                    <th style="display: none;">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody id="table_body">
                                <tr class="danger">
                                    <td colspan="9" class="text-center">
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
<div aria-hidden="true" role="dialog" tabindex="-1" id="grv_st_detail_modal" data-backdrop="static"
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
                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="">
                        <thead>
                        <tr>
                            <th class='hide_po'
                                style="width: 250px;"><?php echo $this->lang->line('transaction_common_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('transaction_goods_received_project'); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th class='hide_po'
                                style="width: 150px;"><?php echo $this->lang->line('common_uom'); ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <th style="width:200px;" >No of Birds<?php required_mark(); ?></th>
                            <th style="width:200px;" >Gross Weight<?php required_mark(); ?></th>
                            <th style="width:200px;" >Buckets<?php required_mark(); ?></th>
                            <th style="width:200px;">B weight<?php required_mark(); ?></th>

                            <th style="width: 150px;">Net Weight</th>
                            <!--Qty-->
                            <th style="width: 150px;"><?php echo $this->lang->line('common_unit_cost'); ?> <span
                                        class="currency"> (LKR)</span> <?php required_mark(); ?></th><!--Unit Cost-->
                            <th style="width: 150px;"><?php echo $this->lang->line('common_net_amount'); ?> <span
                                        class="currency"> (LKR)</span> <?php required_mark(); ?></th><!--Net Amount-->
                            <th style="width: 200px;"><?php echo $this->lang->line('common_comment'); ?> </th>
                            <!--Comment-->
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
                            <?php } ?>
                            <td class='hide_po'>
                                <?php echo form_dropdown('UnitOfMeasureID', $uom_arr, 'Each', 'class="form-control" id="UnitOfMeasureID"'); ?>
                            </td>
                            <td class="typestock"><input type="text" onfocus="this.select();" name="noOfItems"
                                                         onkeyup="checkCurrentStock(this)" placeholder="0.00"
                                                         class="form-control number input-mini noOfItems" id="edit_noOfItems">
                            </td>
                            <td class="typestock"><input type="text" onfocus="this.select();" name="grossQty"
                                                         onkeyup="checkCurrentStock(this),cal_deduction_netQtyEdit_buyback(this)" placeholder="0.00"
                                                         class="form-control number input-mini grossQty" id="edit_grossQty">
                            </td>
                            <td class="typestock"><input type="text" onfocus="this.select();" name="noOfUnits"
                                                         onkeyup="checkCurrentStock(this),cal_deduction_netQtyEdit_buyback(this)" placeholder="0.00"
                                                         class="form-control number input-mini noOfUnits" id="edit_noOfUnits">
                            </td>
                            <td class="typestock"><!--<input type="text" onfocus="this.select();" name="deduction"
                                                         onkeyup="cal_deduction_netQty(this),cal_deduction_netQtyEdit_buyback()" placeholder="0.00"
                                                         class="form-control number input-mini deduction" id="edit_deduction">-->
                                <?php echo form_dropdown('deductionedit',all_bucketweight_drop(),' ', 'class="form-control deduction input-mini" onchange="cal_deduction_netQtyEdit_buyback(this)" id="edit_deduction"  required'); ?>
                            </td>

                            <td>
                                <input type="text" name="quantityRequested" onchange="change_amount_edit(this,1)"
                                       onfocus="this.select();" id="quantityRequested"
                                       class="form-control number">
                            </td>
                            <td>
                                <?php if (isset($this->common_data['company_policy']['CPG']['GRV'][0]["policyvalue"]) == 0) { ?>
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
                            <td>
                                <input type="text" name="receivedTotalAmount" onchange="change_amount_edit(this,2)"
                                       onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                       id="receivedTotalAmount" value="00"
                                       class="form-control number">
                            </td>
                            <td>
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
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th colspan="4"><?php echo $this->lang->line('transaction_common_item_details'); ?> </th><!--Item Details-->
            <th colspan="3"><?php echo $this->lang->line('transaction_common_ordered_item'); ?> <span class="currency"> (<?php echo $master['transactionCurrency'] ?>
                    )</span><!--Ordered Item-->
            </th>
            <th colspan="6"><?php echo $this->lang->line('transaction_common_recived_item'); ?> <span class="currency"> <span
                            class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span><!--Received Item-->
            </th>


            <th>&nbsp;</th>
        </tr>
        <tr>
            <th style="min-width: 3%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code'); ?> </th>
            <!--Item Code-->
            <th style="min-width: 23%"><?php echo $this->lang->line('common_item_description'); ?>  </th>
            <!--Item Description-->
            <!-- <th style="min-width: 20%">Comment</th> -->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_uom'); ?> </th><!--UOM-->
            <th style="min-width: 5%">Net Weight </th><!--Qty-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_unit_cost'); ?>  </th><!--Unit Cost-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_net_amount'); ?>  </th><!--Net Amount-->
            <th style="min-width: 10%">Gross Weight</th>
            <th style="min-width: 10%">Buckets</th>
            <th style="min-width: 10%">B weight</th>
            <th style="min-width: 5%">Net Weight</th><!--Qty-->
            <th style="min-width: 12%"><?php echo $this->lang->line('common_unit_cost'); ?> </th><!--Unit Cost-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_net_amount'); ?> </th><!--Net Amount-->
            <th style="min-width: 7%">&nbsp;</th>
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php $requested_total = 0.00;
        $received_total = 0.00;


        /*echo '<pre>';
        print_r($detail);
        echo '</pre>';*/

        if (!empty($detail)) {
            for ($i = 0;
                 $i < count($detail);
                 $i++) {
                echo '<tr>';
                echo '<td>' . ($i + 1) . '</td>';
                echo '<td>' . $detail[$i]['itemSystemCode'] . '</td>';
                echo '<td>' . $detail[$i]['purchaseOrderCode'] . ' : ' . $detail[$i]['itemDescription'] . '</td>';
                echo '<td class="text-center">' . $detail[$i]['unitOfMeasure'] . '</td>';
                echo '<td class="text-center">' . $detail[$i]['requestedQty'] . '</td>';
                echo '<td class="text-right">' . format_number($detail[$i]['requestedAmount'], $master['transactionCurrencyDecimalPlaces']) . '</td>';
                echo '<td class="text-right">' . format_number(($detail[$i]['requestedQty'] * $detail[$i]['requestedAmount']), $master['transactionCurrencyDecimalPlaces']) . '</td>';
                echo '<td class="text-right">' . $detail[$i]['grossQty'].'</td>';
                echo '<td class="text-right">' . $detail[$i]['noOfUnits']. '</td>';
                echo '<td class="text-right">' . $detail[$i]['deduction'] . '</td>';
                echo '<td class="text-center">' . $detail[$i]['receivedQty'] . '</td>';
                echo '<td class="text-right">' . format_number($detail[$i]['receivedAmount'], $master['transactionCurrencyDecimalPlaces']) . '</td>';
                echo '<td class="text-right">' . format_number($detail[$i]['receivedTotalAmount'], $master['transactionCurrencyDecimalPlaces']) . '</td>';

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
                $requested_total += ($detail[$i]['requestedQty'] * $detail[$i]['requestedAmount']);
                $received_total += ($detail[$i]['receivedTotalAmount']);
            }
        } else {
            $norec = $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="14" class="text-center"><b>' . $norec . '<!--No Records Found--></b></td></tr>';
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right" colspan="6">
                <?php echo $this->lang->line('transaction_ordered_item_total'); ?><!--Ordered Item Total--> <span
                        class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></td>
            <td class="text-right total"><?php echo format_number($requested_total, $master['transactionCurrencyDecimalPlaces']); ?></td>
            <td class="text-right" colspan="5">
                <?php echo $this->lang->line('transaction_recived_item_total'); ?><!--Received Item Total--> <span
                        class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></td>
            <td class="text-right total"><?php echo format_number($received_total, $master['transactionCurrencyDecimalPlaces']); ?></td>
            <td colspan="4">&nbsp;

            </td>
        </tr>
        </tfoot>
    </table>
</div>
<hr>
<div class="text-right m-t-xs">
    <button class="btn btn-default prev" onclick=""><?php echo $this->lang->line('common_previous'); ?></button>
    <!--Previous-->
</div>
<div aria-hidden="true" role="dialog" tabindex="-1" id="grv_st_bulk_detail_modal" class="modal fade"
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
                                <th><?php echo $this->lang->line('transaction_goods_received_project'); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_uom'); ?><?php required_mark(); ?></th>
                            <!--UOM-->

                            <th style="width:200px;" >No of Birds<?php required_mark(); ?></th>
                            <th style="width:200px;" >Gross Weight<?php required_mark(); ?></th>
                            <th style="width:200px;" >Buckets<?php required_mark(); ?></th>
                            <th style="width:200px;">B weight<?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                Net Weight<?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_unit_cost'); ?><!--Unit Cost --><span
                                        class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount --><span
                                        class="currency"> (LKR)</span> <?php required_mark(); ?></th>

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
                            <?php } ?>
                            <td><?php echo form_dropdown('UnitOfMeasureID[]', $uom_arr, 'Each', 'class="form-control umoDropdown"  required'); ?></td>

                            <td><input type="text" onfocus="this.select();" name="noOfItems[]"
                                       onkeyup="checkCurrentStock(this)" placeholder="0.00"
                                       class="form-control number input-mini noOfItems" required>
                            </td>
                            <td><input type="text" onfocus="this.select();" name="grossQty[]"
                                       onkeyup="checkCurrentStock(this),cal_deduction_netQty(this)" placeholder="0.00"
                                       class="form-control number input-mini grossQty" required>
                            </td>
                            <td><input type="text" onfocus="this.select();" name="noOfUnits[]"
                                       onkeyup="checkCurrentStock(this),cal_deduction_netQty(this)" placeholder="0.00"
                                       class="form-control number input-mini noOfUnits" required>
                            </td>
                            <td>  <?php echo form_dropdown('deduction[]',all_bucketweight_drop(),' ', 'class="form-control deduction input-mini"  onchange="cal_deduction_netQty(this)"  required'); ?>
                            </td>
                            <td><input type="text" name="quantityRequested[]" onkeyup="validatetb_row(this)"
                                       onchange="change_amount(this,1)" onfocus="this.select();"
                                       class="form-control number quantityRequested" required></td>
                            <td><input type="text" name="estimatedAmount[]" placeholder="0.00"
                                       onchange="change_amount(this,1)"
                                       onkeyup="validatetb_row(this)"
                                       onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                       class="form-control number estimatedAmount"></td>
                            <td><input type="text" name="receivedTotalAmount[]" placeholder="0.00"
                                       onchange="change_amount(this,2)"
                                       onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                       class="form-control number receivedTotalAmount"></td>


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
    var showPurchasePrice = <?php echo $showPurchasePrice ?>;
    $(document).ready(function () {
        $('select.select2').select2();
        grvDetailsID = null;
        projectID = null;

        grvAutoID = <?php echo json_encode(trim($grvAutoID)); ?>;
        grvType = <?php echo json_encode(trim($master['grvType'] ?? '')); ?>;
        currency_decimal = <?php echo json_encode(trim($master['transactionCurrencyDecimalPlaces'] ?? '')); ?>;
        supplierID = <?php echo json_encode(trim($master['supplierID'] ?? '')); ?>;
        currencyID = <?php echo json_encode(trim($master['transactionCurrency'] ?? '')); ?>;
        $('.currency').html(' ( ' + currencyID + ' )');
        initializeitemTypeahead(search_id);
        number_validation();
        $("#grv_detail_form").submit(function (e) {
            save_grv_detail_form(grvAutoID, grvDetailsID);
            return false;
        })
        $("[rel=tooltip]").tooltip();
    });


    function save_grv_detail_form(grvAutoID, grvDetailsID) {
        var data = $("#grv_detail_form").serializeArray();
        data.push({'name': 'grvAutoID', 'value': grvAutoID});
        data.push({'name': 'grvDetailsID', 'value': grvDetailsID});
        data.push({'name': 'uom', 'value': $('#UnitOfMeasureID option:selected').text()});
        $('select[name="deductionedit"] option:selected').each(function () {
            data.push({'name': 'deductionvalue', 'value': $(this).text()})
        })
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Grv/save_grv_detail_buyback'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                /*$form.bootstrapValidator('resetForm', true);*/

                refreshNotifications(true);
                if (data['status'] == true) {

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

    function grv_st_detail_modal() {
        if (grvAutoID) {
            $('#grv_detail_form')[0].reset();
            /*$('#grv_detail_form').bootstrapValidator('resetForm', true);*/
            /*$("#grv_st_detail_modal").modal({backdrop: "static"});*/
            $("#grv_st_detail_modal").modal('show');
        }
    }

    function grv_st_bulk_detail_modal() {
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
        appendData.find('textarea').val('');
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

            $('select[name="deduction[]"] option:selected').each(function () {
                data.push({'name': 'deductionvalue[]', 'value': $(this).text()})
            })
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Grv/save_grv_st_bulk_detail_buyback'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('.uom_disabled').prop('disabled', true);
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        grvDetailsID = null;
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
        /** var item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Procurement/fetch_itemrecode/?q=%QUERY"
        });

         item.initialize();
         $('#search').typeahead(null, {
            minLength: 3,
            highlight: true,
            displayKey: 'Match',
            source: item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            $('#itemAutoID').val(datum.itemAutoID);
            fetch_related_uom_id(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID);
            $('#grv_detail_form').bootstrapValidator('revalidateField', 'itemAutoID');
            $('#grv_detail_form').bootstrapValidator('revalidateField', 'UnitOfMeasureID');
        });

         $('.search').typeahead(null, {
            displayKey: 'Match',
            source: item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            $(this).closest('tr').find('.itemAutoID').val(datum.itemAutoID);
            //LoaditemUnitPrice_againtsExchangerate(datum.companyLocalWacAmount, this);
            fetch_related_uom_id_b(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID, this);
        });*/


        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN',
            onSelect: function (suggestion) {
                var cont = true;
                $('.itemAutoID').each(function () {
                    if (this.value) {
                        if (this.value == suggestion.itemAutoID) {
                            $('#f_search_' + id).val('');
                            $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                            $('#f_search_' + id).closest('tr').find('.umoDropdown').val('');
                            $('#f_search_' + id).closest('tr').find('.quantityRequested').val('');
                            $('#f_search_' + id).closest('tr').find('.estimatedAmount').val('');
                            $('#f_search_' + id).closest('tr').find('.receivedTotalAmount').val('');
                            myAlert('w', 'Selected item is already selected');
                            cont = false;
                        }
                    }
                });
                if (cont) {
                    /*fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);*/// Commented by shafry as Muba said not using this.
                    fetch_related_uom_id_b(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this, suggestion['isSubitemExist']);
                    //$(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                    if(showPurchasePrice == 1){
                        fetch_purchase_price(suggestion.companyLocalPurchasingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                    }
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                    }, 200);
                    $(this).closest('tr').find('.quantityRequested').focus();
                    $(this).closest('tr').css("background-color", 'white');
                }
            }
        });
        $('#f_search_' + id).off('focus.autocomplete');


    }

    function initializeitemTypeahead_edit() {

        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN',
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
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                        <!--No Records Found-->
                    } else {
                        tot_amount = 0;
                        receivedQty = 0;
                        $.each(data['detail'], function (key, value) {
                            if (value['receivedQty'] != null && value['receivedQty'] !== undefined) {
                                receivedQty = value['receivedQty'];
                            }
                            if(grvType=="Standard")
                            {
                                cost_status = '<input type="text" class="number" size="15" id="amount_' + value['purchaseOrderDetailsID'] +
                                    '" value="' + parseFloat(value['unitAmount']).toFixed(currency_decimal) + '" onkeyup="select_value(' + value['purchaseOrderDetailsID'] + ')" >';
                            }else
                            {
                                cost_status = '<input type="text" class="number" size="15" id="amount_' + value['purchaseOrderDetailsID'] +
                                    '" value="' + parseFloat(value['unitAmount']) + '" onkeyup="select_value(' + value['purchaseOrderDetailsID'] + ')" >';
                            }

                            if (data['policy_po_cost_change'] == 0) {
                                cost_status = '<input type="hidden" class="number" size="15" id="amount_' + value['purchaseOrderDetailsID'] + '" value="' + parseFloat(value['unitAmount']) + '" onkeyup="select_value(' + value['purchaseOrderDetailsID'] + ')" >' + parseFloat(value['unitAmount']);
                            }


                            $('#table_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + (value['requestedQty']) + '</td><td class="text-right">' + parseFloat(value['unitAmount']).toFixed(currency_decimal) + '</td><td class="text-center"><input type="text" class="number" size="8" id="noitem_' + value['purchaseOrderDetailsID'] + '"  ></td><td class="text-center"><input type="text" class="number" size="8" value='+(value['requestedQty'])+' id="grossqty_' + value['purchaseOrderDetailsID'] + '"  onchange="calbucketweight(' + value['purchaseOrderDetailsID'] + ',this,' + value['unitAmount'] + ')" ></td><td class="text-center"><input type="text" class="number" size="8" id="buckets_' + value['purchaseOrderDetailsID'] + '" onchange="calbucketweight(' + value['purchaseOrderDetailsID'] + ',this,' + value['unitAmount'] + ')"  ></td><td class="text-center"><select class="whre_bweight" style="width: 110px;" onchange="calbucketweight(' + value['purchaseOrderDetailsID'] + ',this,' + value['unitAmount'] + ')"  id="whre_bweight_' + value['purchaseOrderDetailsID'] + '"><option value=" ">Select B weigh</option></select></td><td class="text-center"><input type="text" class="number" size="8" id="qty_' + value['purchaseOrderDetailsID'] + '"  onchange="select_check_box(this,' + value['purchaseOrderDetailsID'] + ',' + value['unitAmount'] + ' )" ></td><td class="text-center">' + cost_status + '</td><td class="text-center"><p id="tot_' + value['purchaseOrderDetailsID'] + '"> </p></td><td class="text-right" style="display: none;"><input class="checkbox" id="check_' + value['purchaseOrderDetailsID'] + '" type="checkbox" value="' + value['purchaseOrderDetailsID'] + '"></td></tr>');
                            x++;
                            tot_amount += (parseFloat(value['totalAmount']).toFixed(currency_decimal));
                            //.formatMoney(currency_decimal, '.', ',')
                            if (!jQuery.isEmptyObject(data['bucketweightdrop'])) {
                                $('.whre_bweight').empty();
                                var mySelect = $('.whre_bweight');
                                mySelect.append($('<option></option>').val('').html('Select B weight'));
                                $.each(data['bucketweightdrop'], function (val, text) {
                                    mySelect.append($('<option></option>').val(text['weightAutoID']).html(text['bucketWeight']));
                                });
                            }
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
        var qty = $('#qty_'+id).val();
        $("#check_" + id).prop("checked", false);
        if (data.value > 0) {
            $("#check_" + id).prop("checked", true);
        }
        amount = $('#amount_' + id).val();
        if (amount < 0) {
            amount = 0;
            }
        var total = qty * amount;
        var totalnew = parseFloat(total).formatMoney(currency_decimal, '.', ',');
        $('#tot_' + id).text(totalnew);
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
        var totalnew = parseFloat(total);//.formatMoney(currency_decimal, '.', ',')
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
                        data: {'grvDetailsID': id},
                        url: "<?php echo site_url('Grv/fetch_grv_detail'); ?>",
                        beforeSend: function () {
                            $("#grv_st_detail_modal").modal('show');
                            startLoad();
                        },
                        success: function (data) {
                            grvDetailsID = data['grvDetailsID'];
                            projectID = data['projectID'];
                            $('#search').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                            fetch_related_uom_id(data['defaultUOMID'], data['unitOfMeasureID']);
                            $('#quantityRequested').val(data['receivedQty']);
                            $('#estimatedAmount').val(data['receivedAmount']);
                            $('#receivedTotalAmount').val(data['receivedTotalAmount']);
                            $('#search_id').val(data['itemSystemCode']);
                            $('#itemSystemCode').val(data['itemSystemCode']);
                            $('#itemAutoID').val(data['itemAutoID']);
                            $('#itemDescription').val(data['itemDescription']);
                            $('#comment').val(data['comment']);
                            $('#remarks').val(data['remarks']);

                            $('#edit_noOfItems').val(data['noOfItems']);
                            $('#edit_grossQty').val(data['grossQty']);
                            $('#edit_noOfUnits').val(data['noOfUnits']);
                            $('#edit_deduction').val(data['bucketWeightID']).change();
                            if (value != 0) {
                                $('.hide_po').hide();
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
        var noofitems = [];
        var grossqty = [];
        var buckets = [];
        var bucketweightID = [];
        var bucketweight = [];

        $('#table_body input:checked').each(function () {
            if ($('#amount_' + $(this).val()).val() == '') {
                swal("Cancelled", "Received cost cannot be blank !", "error");
            } else {
                selected.push($(this).val());
                amount.push($('#amount_' + $(this).val()).val());
                qty.push($('#qty_' + $(this).val()).val());
                noofitems.push($('#noitem_' + $(this).val()).val());
                grossqty.push($('#grossqty_' + $(this).val()).val());
                buckets.push($('#buckets_' + $(this).val()).val());
                bucketweightID.push($('#whre_bweight_' + $(this).val()).val());
                bucketweight.push($('#whre_bweight_' + $(this).val() + ' option:selected').text());
            }
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'DetailsID': selected, 'grvAutoID': grvAutoID, 'amount': amount, 'qty': qty,'noofitems':noofitems,'grossqty':grossqty,'buckets':buckets, 'bucketweightID':bucketweightID,'bucketweight':bucketweight
                    },
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

    function change_amount_edit(field, val) {
        var quantityRequested = 0;
        var estimatedAmount = 0;
        var receivedTotalAmount = 0;
        if (val == 1) {
            quantityRequested = $('#quantityRequested').val();
            estimatedAmount = $('#estimatedAmount').val();
            var totamt = quantityRequested * estimatedAmount;
            $('#receivedTotalAmount').val(((parseFloat(totamt)).toFixed(currency_decimal)));
        } else {
            quantityRequested = $('#quantityRequested').val();
            receivedTotalAmount = $('#receivedTotalAmount').val();
            var unitamt = receivedTotalAmount / quantityRequested;
            $('#estimatedAmount').val(unitamt);
        }
    }

    function validatetb_row(det) {
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }
    function checkCurrentStock(det) {
        var currentStock = $(det).closest('tr').find('.currentstock').val();
        if (det.value > parseFloat(currentStock)) {
            myAlert('w', 'Transfer quantity should be less than or equal to current stock');
            $(det).val(0);
        }

        if(det.value > 0)
        {
            $(det).closest('tr').css("background-color",'white');
        }
    }
    function cal_deduction_netQty(element) {
        var grossQty = parseFloat($(element).closest('tr').find('.grossQty').val());
        var bucketweight = parseFloat($(element).closest('tr').find('.noOfUnits').val());
        var deduction = parseFloat($(element).closest('tr').find('.deduction :selected').text());

        if (grossQty) {
            $(element).closest('tr').find('.quantityRequested').val((parseFloat(grossQty - (bucketweight * deduction)).toFixed(2)));

        }
    }
    function cal_deduction_netQtyEdit_buyback() {
        var grossQty = parseFloat($('#edit_grossQty').val());
        var deduction = parseFloat($('#edit_deduction :selected').text());
        var bucketweight = parseFloat($('#edit_noOfUnits').val());
        if (grossQty) {
            $('#quantityRequested').val((parseFloat(grossQty - (bucketweight * deduction)).toFixed(2)));

        }
    }
    function fetch_sales_order_base()
    {
        $('#salespurchaseorder').empty();
        var mySelect = $('#salespurchaseorder');
        mySelect.append($('<option></option>').val('').html('Select Sales Order Base'));
        <?php if (!empty($supplier_po)) {
        foreach($supplier_po as $val) {?>
        mySelect.append($('<option></option>').val(<?php echo $val['purchaseOrderID']?>).html('<?php echo $val['purchaseOrderCode']?>'));
        <?php   }
        }?>
        //onchange="calbucketweight(this.value

    }
    $( "#pobasebtn" ).click(function() {
        fetch_sales_order_base();
    });
    function calbucketweight(buccketweight,id,uniamount)
    {

        var grossqty = $('#grossqty_' + buccketweight).val();
        var buckets = $('#buckets_' + buccketweight).val();
        var price = $('#amount_' + buccketweight).val();
        var deduction = parseFloat($(id).closest('tr').find('.whre_bweight :selected').text());
        var qtynew = ((parseFloat(grossqty - (buckets * deduction)).toFixed(2)));
        $('#qty_'+ buccketweight).val(qtynew);
        select_check_box(id,buccketweight, uniamount);
    /*    var total = qtynew * price;
        var totalnew = parseFloat(total).formatMoney(currency_decimal, '.', ',');
        $('#tot_' + id).text(totalnew);*/

        /*var totalnew = parseFloat(qtynew * price).formatMoney(2, '.', '');
         $('#tot_' + id).text(totalnew);*/

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
</script>