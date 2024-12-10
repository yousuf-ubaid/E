<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$datefrom = $this->lang->line('accounts_receivable_common_date_from');
$dateto = $this->lang->line('accounts_receivable_common_date_to');
$currency = $this->lang->line('common_currency');
$netbalance = $this->lang->line('accounts_receivable_common_net_balance');
$subtot = $this->lang->line('accounts_receivable_common_sub_tot');

$grandTotal = array();

?>
<!--<style>
    .fixed_header tbody{
        display:block;
        overflow:auto;
        height:300px;
        width:100%;
    }
    .fixed_header thead tr{
        display:block;
        width: auto;
    }
    .fixed_header thead tr th{
        width: inherit;
    }
</style>-->
    <div class="row">
        <div class="col-md-12">
            <?php if ($type == 'html') {
                echo export_buttons('tbl_batchAging_ledger', 'Farm Ledger');
            } ?>
        </div>
    </div>
    <div id="tbl_batchAging_ledger">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center reportHeaderColor">
                    <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
                </div>
                <div class="text-center reportHeader reportHeaderColor">Batch Aging</div>
                <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>As of : </strong>" . $from ?></div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <strong>Filters <i class="fa fa-filter"></i></strong><br>
                <strong><i>Sub Locations :</i></strong> <?php echo join(",", $output['location']) ?>
            </div>
        </div>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <?php
                if (!empty($output['details'])) { ?>
                    <div class="fixHeader_Div">
                        <table class="fixed_header borderSpace report-table-condensed" id="tbl_report">
                            <thead class="report-header">
                            <tr>
                                <th>Farmer</th>
                                <th>Batch</th>
                                <?php
                                if (!empty($aging)) {
                                    foreach ($aging as $val2) {
                                        echo "<th>" . $val2 . "</th>";
                                    }
                                }
                                ?>
                                <th><?php echo $this->lang->line('common_total');?><!--Total--></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(!empty($output['details'])){
                                foreach ($output['details'] as $val){

                                    $chicksAge = $this->db->query("SELECT dpm.dispatchedDate,batch.closedDate FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 1 LEFT JOIN srp_erp_buyback_batch batch ON batch.batchMasterID = dpm.batchMasterID WHERE dpm.batchMasterID ={$val['batchMasterID']} ORDER BY dpm.dispatchAutoID ASC")->row_array();
                                    if (!empty($chicksAge)) {
                                        $chicksTotal = $this->db->query("SELECT COALESCE (sum( dpd.qty ), 0 ) AS chicksTotal
FROM srp_erp_buyback_dispatchnote dpm
	INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID
WHERE dpm.batchMasterID = {$val['batchMasterID']} AND dpd.buybackItemType = 1 AND dpm.approvedYN = 1 AND dpm.confirmedYN = 1")->row_array();

                                        $retutnqty = $this->db->query("SELECT COALESCE (sum( dpdr.qty ), 0 ) AS returnqty, dispatchAutoID, dpdr.returnAutoID
                                                      FROM srp_erp_buyback_dispatchreturndetails dpdr 
                                                      LEFT JOIN srp_erp_buyback_dispatchreturn retun on retun.returnAutoID = dpdr.returnAutoID
                                                      WHERE approvedYN  = 1 AND confirmedYN = 1 AND dpdr.buybackItemType = 1 AND retun.batchMasterID = {$val['batchMasterID']} GROUP BY dispatchAutoID")->row_array();

                                        $grn = $this->db->query("SELECT COALESCE (sum( grndetail.noOfBirds ), 0 ) AS grn
FROM srp_erp_buyback_grn grn 
LEFT JOIN srp_erp_buyback_grndetails grndetail on grndetail.grnAutoID = grn.grnAutoID
WHERE 
approvedYN  = 1
AND confirmedYN = 1
AND grn.batchMasterID = {$val['batchMasterID']}
")->row_array();
                                        $mortality = $this->db->query("SELECT COALESCE(sum(noOfBirds), 0) AS deadChicksTotal FROM srp_erp_buyback_mortalitymaster mm INNER JOIN srp_erp_buyback_mortalitydetails md ON mm.mortalityAutoID = md.mortalityAutoID WHERE batchMasterID ={$val['batchMasterID']} AND confirmedYN = 1")->row_array();

                                        $chicksbalance =($chicksTotal['chicksTotal']) - ($retutnqty['returnqty'] + $grn['grn'] + $mortality['deadChicksTotal']) ;


                                        $dStart = new DateTime($chicksAge['dispatchedDate']);
                                        if ($chicksAge['closedDate'] != ' ') {
                                            $dEnd = new DateTime($chicksAge['closedDate']);
                                        } else {
                                            $dEnd = new DateTime($from);
                                        }
                                        $dDiff = $dStart->diff($dEnd);
                                        $newFormattedDate = $dDiff->days + 1;
                                        ?>
                                        <tr class="hoverTr">
                                        <td><?php echo $val['farmSystemCode'] . '-' . $val['description']; ?></td>
                                        <td><a onclick="generateProductionReport_preformance(<?php echo $val['batchMasterID']; ?>)"><?php echo $val['batchCode']; ?></a></td>
                                        <?php
                                        if (!empty($aging)) {
                                            foreach ($aging as $val2) {
                                                $ageRange = (explode("-", $val2));

                                                if (isset($ageRange[1]) && ($newFormattedDate >= $ageRange[0] && $newFormattedDate <= $ageRange[1])) {
                                                    echo '<td class="text-right">' . $chicksbalance . '</td>';
                                                    $grandTotal[$val2][] = $chicksbalance;
                                                } else if(!isset($ageRange[1])){
                                                    $ageRange1 = (explode(">", $ageRange[0]));
                                                    if($newFormattedDate >= $ageRange1[1]){
                                                        echo '<td class="text-right">' . $chicksbalance . '</td>';
                                                        $grandTotal[$val2][] = $chicksbalance;
                                                    } else{
                                                        echo '<td class="text-right"> 0 </td>';
                                                        $grandTotal[$val2][] = 0;
                                                    }
                                                } else{
                                                    echo '<td class="text-right"> 0 </td>';
                                                    $grandTotal[$val2][] = 0;
                                                }
                                            }
                                        }
                                        echo '<td class="text-right">' . $chicksbalance . '</td>';
                                        $grandTotal["total"][] = $chicksbalance;
                                    }
                                   ?>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                          <tfoot>
                            <tr>
                                <td colspan='<?php echo $count; ?>'>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Grand Total</strong></td>
                            <?php

                                if (!empty($aging)) {
                                    foreach ($aging as $value) {
                                            echo "<td class='text-right reporttotal'>" . array_sum($grandTotal[$value]) . "</td>";
                                    }
                                }
                            echo "<td class='text-right reporttotal'>" . array_sum($grandTotal["total"]) . "</td>";
                                echo "</tr>";
                            ?>
                            </tfoot>
                        </table>
                    </div>
                    <?php
                } else {
                    $norec=$this->lang->line('common_no_records_found');
                    echo warning_message($norec);/*No Records Found!*/
                }
                ?>
            </div>
        </div>
    </div>

<?php
