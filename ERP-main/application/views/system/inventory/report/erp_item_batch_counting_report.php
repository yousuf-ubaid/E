<!---- =============================================
-- File Name : erp_item_batch_counting_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Description : This file contains Item batch wise counting.

-- REVISION HISTORY

-- =============================================-->

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$showSubItem = false;
$isRptCost = false;
$isLocCost = false;
$barcode = false;
$seconeryItemCode = false;
$partNo = false;
$extraColumn = 0;
$asof= $this->lang->line('transaction_as_of');
$items = $output['items'];
$warehouse_list = $output['warehouse_list'];
$warehouse_items_list = $output['warehouse_items'];
$warehouse_batch_items_list = $output['warehouse_batch'];

?>

<div class="row">
    <div class="col-md-12">
        <?php
        if ($type == 'html') { ?>
            <a href="#" type="button" class="btn btn-excel btn-xs pull-right" style="margin-left: 2px" onclick="excel_export()">
                <i class="fa fa-file-excel-o"></i> <?php echo $this->lang->line('common_excel');?> <!--Excel-->
            </a>
        <?php
            // echo export_buttons('tbl_item_counting', 'Item Counting', false, true);
        } ?>
    </div>
</div>
<div id="tbl_item_counting">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('transaction_item_counting');?></div><!--Item Counting-->
            <div
                class="text-center reportHeaderColor"> <?php echo "<strong><!--As of-->$asof: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong><?php echo $this->lang->line('common_filter');?> <i class="fa fa-filter"></i></strong><br>
            <strong><i><?php echo $this->lang->line('common_warehouse');?>:</i></strong> <?php echo join(" | ",$warehouse) ?>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) {
                $itemSecondaryCodePolicy = is_show_secondary_code_enabled();
                if($itemSecondaryCodePolicy){
                    $itemCodeLabel="Secondary Code";
                }else{
                    $itemCodeLabel=$this->lang->line('transaction_common_item_code');
                }
                ?>
                <table class="borderSpace report-table-condensed" id="demo">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('transaction_common_item_code'); ?></th>
                        <th><?php echo $this->lang->line('transaction_common_item_description');?></th><!--Item Description-->
                        <th><?php echo $this->lang->line('common_Location');?></th><!--Location-->
                        <th><?php echo 'Batch Code'//$this->lang->line('transaction_common_item_code'); ?></th>
                        <th><?php echo 'Batch Expiry'//$this->lang->line('transaction_common_item_code'); ?></th>
                        <?php if ($barcode) { ?>
                            <th><?php echo $this->lang->line('transaction_barcode');?></th><!--transaction_barcode-->
                        <?php } ?>
                        <?php if ($seconeryItemCode) { ?>
                            <th><?php echo $this->lang->line('erp_item_master_secondary_code');?></th><!--Secondary Code-->
                        <?php } ?>
                        <?php if ($partNo) { ?>
                            <th><?php echo $this->lang->line('transaction_part_no');?></th><!--Part No-->
                        <?php } ?>
                        <th><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
                        <th><?php echo 'Batch Qty'//$this->lang->line('transaction_qty_in_hand');?></th><!--Qty in Hand-->
                        <th><?php echo $this->lang->line('transaction_qty_in_hand');?></th><!--Qty in Hand-->
                        <?php if ($isLocCost) { ?>
                            <th><?php echo $this->lang->line('transaction_avg_cost_in_local');?></th><!--Avg Cost Local-->
                            <th><?php echo $this->lang->line('transaction_asset_value_local');?></th><!--Asset Value Local-->
                        <?php } ?>
                        <?php if ($isRptCost) { ?>
                            <th><?php echo $this->lang->line('transaction_avg_cost_rpt');?></th><!--Avg Cost Rpt-->
                            <th><?php echo $this->lang->line('transaction_asset_value_rpt');?></th><!--Asset Value Rpt-->
                        <?php } ?>
                        <th><?php echo 'Total Qty'//$this->lang->line('transaction_total_qty');?></th><!--Physical Qty-->
                    </tr>
                    </thead>
                    <?php

             
                    $count = 7;
                    $category = array();
                    // foreach ($output as $val) {
                    //     $category[$val["mainCategory"]][$val["subCategory"]][$val["subsubCategory"]][] = $val;
                    // }
                    $totalcurrentQty = 0;
                    $totallocalAssetvalue = 0;
                    $totalRptAssetvalue = 0;
                    if (!empty($items)) {
                        foreach ($items as $key => $item_val) {
                            echo "<tr>
                                    <td class='mainCategoryHead'>".$item_val['itemSystemCode']."</td>
                                    <td colspan='" . $count . "'>
                                        <div class='mainCategoryHead'>" . $item_val['itemDescription'] . "</div></td>
                                    <td class='mainCategoryHead'>".$item_val['currentStock']."</td>
                                  </tr>";
                            
        
                                foreach ($warehouse_list as $key2 => $warehouse_list_val) {
                                    $view_warehouse_list = "<tr>
                                        <td colspan='2'></td>
                                        <td class='mainCategoryHead' colspan='5'>".$warehouse_list_val['wareHouseCode']." - ".$warehouse_list_val['wareHouseDescription']." - ".$warehouse_list_val['wareHouseLocation']."</td>
                                        ";
                                    if( $warehouse_items_list[$item_val['itemAutoID']] && isset($warehouse_items_list[$item_val['itemAutoID']][$warehouse_list_val['wareHouseAutoID']])){
                                        $view_warehouse_list .= "<td colspan='" . $count . "'>
                                                <div class='mainCategoryHead'>" . $warehouse_items_list[$item_val['itemAutoID']][$warehouse_list_val['wareHouseAutoID']]['currentStock'] . "</div>
                                            </td></tr>";
                                    }else{
                                        $view_warehouse_list .= "<td colspan='" . $count . "'> 0 
                                            </td></tr>";
                                    }
                                        
                                    echo $view_warehouse_list;
                                
                                    $batch_assigned_arr = isset($warehouse_batch_items_list[$item_val['itemAutoID']][$warehouse_list_val['wareHouseAutoID']]) ? $warehouse_batch_items_list[$item_val['itemAutoID']][$warehouse_list_val['wareHouseAutoID']] : '';
                                    
                                    if($batch_assigned_arr != ''){

                                        foreach($batch_assigned_arr as $batch_detail_val){
                                            echo "<tr>
                                                <td colspan='3'></td>
                                                <td class='text-bold' class=''>".$batch_detail_val['batchNumber']."</td>
                                                <td class='text-bold' colspan='2'>".$batch_detail_val['batchExpireDate']."</td>
                                                <td class='mainCategoryHead'>".$batch_detail_val['qtr']."</td>
                                            </tr>";
                                        }
                                       


                                    }else{
                                        echo "<tr>
                                            <td colspan='3'></td>
                                            <td class='text-danger' colspan='5'>No batches in warehouse</td>
                                        </tr>";
                                    }


                                }
                        }
                    }
             
                    ?>
                <tr>
                    <td colspan='<?php echo $count; ?>'>&nbsp;</td>
                </tr>
                        
                        </table>
                        <?php
            } else {
                $norecfound=$this->lang->line('common_no_records_found');
                echo warning_message($norecfound);/*No Records Found!*/
            }
            ?>
        </div>
    </div>
</div>
<script>
    /* $(document).ready(function() {
     $('#demo').dragtable();
     });*/
</script>