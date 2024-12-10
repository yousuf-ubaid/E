<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if(!empty($locations))
{
    foreach ($locations as $var){

    }
}?>
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_itemWiseSalesReport', 'Item Wise Sales Report');
        } ?>
    </div>
</div>
<div id="tbl_itemWiseSalesReport">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('sales_markating_item_wise_sales_report');?></div>
            <!--Item Wise Sales Report-->
            <div class="text-center reportHeaderColor"> <?php
                $datefrom = $this->lang->line('transaction_date_from');
                $dateto = $this->lang->line('transaction_date_to');
                $financialyear =$financialyear =$this->lang->line('common_financial_year');
                echo "<strong>" . $financialyear . ": </strong>" . $from . " " . $this->lang->line('common_to') . " " . $to ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 10px ">
            <strong><?php echo $this->lang->line('common_filter');?> <!--Filters-->  <i class="fa fa-filter"></i></strong><br>
            <strong><i><?php echo $this->lang->line('common_warehouse');?> <!--Warehouse-->:</i></strong> <?php echo join(",", $warehouse) ?>
        </div>

    </div>
    <div class="row" style="margin-top: 2px;">
        <table id="" class="borderSpace report-table-condensed" style="width: 100%">
            <thead class="report-header">
            <tr>
                <?php
                echo '<th>'.$this->lang->line('common_description').' <!--Description--></th>';
                foreach($header as $headval){
                    ?>
                    <th><?php echo $headval; ?></th>
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
                $grandTotal = 0;
                foreach($output as $dtl){
                    ?>
                    <tr class="hoverTr">
                        <?php
                        $total=0;
                        echo '<td>' . $dtl['description'] . '</td>';
                        foreach($header as $key => $headval){
                            if($dtl[$headval] < 0){
                                $dtl[$headval] = $dtl[$headval] * (-1);
                            }
                            $coltot[$key][] = $dtl[$headval];
                            if($dtl[$headval] == 0){
                                ?>
                                <td style="text-align: right"><?php  echo $dtl[$headval] ; ?></td>
                                <?php
                            }else{
                                ?>
                                <td style="text-align: right"><a href="#" class="drill-down-cursor" onclick="openItemwise_salesDoc('<?php echo $key ?>',<?php echo $dtl['groupID'] ?>)"><?php  echo $dtl[$headval] ; ?></a></td>
                                <?php
                            }
                            $total +=  $dtl[$headval];
                            $grandTotal += $dtl[$headval];
                        }
                        ?>
                        <td class="text-right"><b><?php echo $total ?></b></td>
                    </tr>
                    <?php
                }
                echo '<tr><td><b>'.$this->lang->line('common_total').' <!--Total--></b></td>';
                foreach($header as $key => $headval){
                   $tot=array_sum($coltot[$key]);

                   echo '<td class="text-right reporttotal ">' . $tot . '</td>';
                }
                echo '<td class="text-right reporttotal">' . $grandTotal . '</td></tr>';
            } else {
              echo '<tr><td colspan="14"><b><center>'.$this->lang->line('common_no_records_found').'</center></b></td></tr>';
            }
            ?>

            </tbody>
        </table>
    </div>
</div>


<?php
