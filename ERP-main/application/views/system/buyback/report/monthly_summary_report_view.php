<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_monthlySummaryBuyback', 'Buyback Monthly Summary');
        } ?>
    </div>
</div>
<div id="tbl_monthlySummaryBuyback">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor">Monthly Summary</div>

            <div class="text-center reportHeaderColor"> <?php
                $datefrom = $this->lang->line('transaction_date_from');
                $dateto = $this->lang->line('transaction_date_to');

                echo "<strong>Financial Year : </strong>" . $from . " to " . $to ?></div>
        </div>
    </div>
    <div class="row" style="margin-top: 2px; padding: 1%">
        <table id="" class="borderSpace report-table-condensed" style="width: 100%">
            <thead class="report-header">
            <tr>
                <th>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</th>
                <?php
                foreach($header as $headval){
                    ?>
                    <th><?php echo $headval; ?></th>
                    <?php
                }
                ?>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if($output){
                $coltot = [];
                $grandTotal = 0;
                foreach($output as $out=> $dtl){
                    ?>
                    <tr class="hoverTr">
                        <td><?php echo $out ?></td>
                        <?php
                        $total=0;
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
                                <td style="text-align: right"><a href="#" class="drill-down-cursor"><?php
                                        if($out == 'Farmer Profit' || $out == 'Farmer Loss'){
                                            echo number_format($dtl[$headval], 2);
                                        } else{
                                            echo $dtl[$headval] ;
                                        }
                                        ?></a></td>
                                <?php
                            }
                            $total +=  $dtl[$headval];
                        }
                        ?>
                        <td class="text-right"><b><?php
                                if($out == 'Farmer Profit' || $out == 'Farmer Loss'){
                                    echo number_format($total, 2);
                                } else{
                                    echo $total;
                                }
                                ?></b></td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="15"><b><center>NO RECORDS FOUND</center></b></td></tr>';
            }?>

            </tbody>
        </table>
    </div>
</div>

<?php
/**
 * Created by PhpStorm.
 * User: l
 * Date: 4/4/2019
 * Time: 10:20 AM
 */