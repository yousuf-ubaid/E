<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div id="tbl_unbilled_grv">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('crm_opportunity_report_re');?> </div><!--Opportunity Report-->
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($opportunity)) { ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name');?></th><!--Name-->
                        <th><?php echo $this->lang->line('crm_closing_date');?></th><!--Closing Date-->
                        <th>User Responsible</th>
                        <th><?php echo $this->lang->line('common_status');?></th><!--Status-->
                        <th><?php echo $this->lang->line('common_value');?></th><!--Value-->
                    </tr>
                    </thead>
                    <tbody>

                    <?php

                    $x = 1;
                    $total = 0;
                    $currency = '';
                    $Grandtotal = 0;

                    foreach ($opportunity as $val) {
                        $opportunitynew[$val["CurrencyCode"]][] = $val;

                    }
                    if (!empty($opportunitynew)) {

                        foreach ($opportunitynew as $key => $val1) {
                            $subtotal = 0;

                            foreach ($val1 as $key2 => $opportunityrep) {
                                $subtotal += $opportunityrep['transactionAmount'];
                                echo "<tr class='hoverTr'>";
                                echo "<td>" . $x . "</td>";
                                echo '<td>' . $opportunityrep['opportunityName'] . '</td>';
                                echo '<td>' . $opportunityrep['forcastCloseDate'] . '</td>';
                                echo '<td>' . $opportunityrep['responsiblePerson'] . '</td>';
                                echo '<td>' . $opportunityrep['statusDescription'] . '</td>';
                                echo '<td style="text-align: right;">' . number_format($opportunityrep['transactionAmount'], 2) . '</td>';
                                echo "</tr>";
                                $x++;

                            }
                            $Grandtotal +=$subtotal;
                            echo "<tr>";
                            echo "<td class='' colspan='4' style='font-weight: bold;'> </td>";
                            echo "<td class=''  style='font-weight: bold;'>Sub Total (".$opportunityrep['CurrencyCode'].")</td>";
                            echo "<td class='reporttotal' align='right' style='font-weight: bold;'>".number_format($subtotal, 2)."<!--Net Balance--></td>";
                            echo "</tr>";
                        }



                    }
                    ?>


                    </tbody>
                    <br>
                    <tfoot>
                    <tr>
                        <td style="min-width: 85%  !important" class="text-right sub_total" colspan="5">
                            <?php echo $this->lang->line('common_total');?>   </td><!--Total-->
                        <td style="min-width: 15% !important"
                            class="text-right total"><?php echo number_format($Grandtotal, 2); ?></td>
                    </tr>
                    </tfoot>
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