<?php
$push['description'] = 'Un Categorized';
$push['groupID'] = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
array_push($header, $push);

?>

<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_weekly_sales_analysis_Report', 'Sales Analysis Report', true,false);
        } ?>
    </div>
</div>
<div id="tbl_weekly_sales_analysis_Report">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('sales_markating_sales_analysis_report');?> <!--Sales Analysis Report--></div>
            <!--Item Wise Sales Report-->
            <div class="text-center reportHeaderColor">(

                <?php if($dataFilter == 1 ||$dataFilter == 2 || $dataFilter == 3){
                    echo $this->lang->line('sales_markating_sub_sub_category_wise');//Sub Sub Category wise
                } else if($dataFilter == 4){
                    echo $this->lang->line('sales_markating_customer_category_wise');//Customer Category wise
                } else if($dataFilter == 6 || $dataFilter == 7){
                    echo $this->lang->line('sales_markating_area_wise');//Area wise
                } ?>)</div>
            <div class="text-center reportHeaderColor"> <?php
                $datefrom = $this->lang->line('transaction_date_from');
                $dateto = $this->lang->line('transaction_date_to');
                $financialyear =$this->lang->line('common_financial_year');?>
                <?php echo "<strong>".$financialyear.": </strong>" . $from . " ".$this->lang->line('common_to')." " . $to ?>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 10px ">
            <strong><?php echo $this->lang->line('common_filter');?> <!--Filters--> <i class="fa fa-filter"></i></strong><br>
            <strong><i><?php echo $this->lang->line('common_warehouse');?> <!--Warehouse-->:</i></strong> <?php echo join(",", $warehouse) ?>
            <?php if(!empty($customerCategory)){
                $cuscat = array_column($customerCategory, 'categoryDescription');
             echo  '<br><strong><i>'.$this->lang->line('common_customer_category').':</i></strong>' . join(",", $cuscat);
            }
           ?>
        </div>
    </div>
    <div class="row" style="margin-top: 2px;">
        <div class="col-md-12">
        <table id="week_sale_detail" class="borderSpace report-table-condensed">
            <thead class="report-header" style="">
            <tr style="">
                <?php
                echo '<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$this->lang->line('common_date').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>';
                foreach($header as $headval){
                    ?>
                    <th><?php echo $headval['description']; ?></th>
                    <?php
                }
                ?>
                <th><?php echo $this->lang->line('common_total');?> <!--Total--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if($output){
            $coltot = [];
            $result = array_group_by($output, 'weekvalue');
            $subTot = array();
            $grandTotal = 0;
            foreach ($result as $key => $dtl) {
                $total = 0;
                echo '<tr class="hoverTr">';
                echo '<td>' . $key . '</td>';
                foreach ($header as $category) {
                    $cat_id = $category['groupID'];
                    $temp_arr = array_column($dtl, 'groupID');
                    $key = array_search($cat_id, $temp_arr);
                        if ($key !== false) {
                            echo '<td class="text-right"><a href="#" class="drill-down-cursor">' . number_format($dtl[$key]['total_value'], $dtl[$key]['transactionCurrencyDecimalPlaces']) . '</a></td>';
                            $total += $dtl[$key]['total_value'];
                            $subTot[$cat_id][] = $dtl[$key]['total_value'];
                        } else {
                            echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="openSalesAnalysis_weeklyDoc()">' . number_format(0, $dtl[$key]['transactionCurrencyDecimalPlaces']) . '</a></td>';
                            $subTot[$cat_id][] = 0;
                        }
                }
                echo '<td class="text-right"><b>' . number_format($total, 2) . ' </b></td>';
                echo '</tr>';
            } ?>
            <tr>
                <td colspan="0"><b>Total</b></td>
                <?php foreach ($header as $category) {
                    $cat_id = $category['groupID'];
                    $grandTotal += array_sum($subTot[$cat_id]);
                   ?>
                    <td class="reporttotal text-right"><?php echo number_format(array_sum($subTot[$cat_id]), 2); ?></td>
            <?php }
            ?>
                <td class="reporttotal text-right"><?php echo number_format($grandTotal, 2); ?></td>
                <?php
                }else {
              echo '<tr><td colspan="14"><b><center>'.$this->lang->line('common_no_records_found').'</center></b></td></tr>';
            }
            ?>

            </tbody>
        </table>
        </div>
    </div>
</div>

<!--onclick="openSalesAnalysis_weeklyDoc(' . $dtl[$key]['groupID'] . ',' . $dtl[$key]['weekID'] . ')"-->
<!--onclick="openSalesAnalysis_weeklyDoc()"-->
<script>
    $('#week_sale_detail').tableHeadFixer({
        head: true,
        foot: true,
        left: 1,
        right: 0,
        'z-index': 10
    });
</script>