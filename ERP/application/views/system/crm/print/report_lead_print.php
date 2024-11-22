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
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('crm_lead_report_re');?> </div><!--Lead Report-->
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($lead)) { ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('crm_full_name');?></th><!--Full Name-->
                        <th><?php echo $this->lang->line('common_company');?></th><!--Company-->
                        <th><?php echo $this->lang->line('crm_mobile_no');?></th><!--Mobile No-->
                        <th><?php echo $this->lang->line('crm_home_no');?></th><!--Home No-->
                        <th>User Responsible</th><!--Home No-->
                        <th><?php echo $this->lang->line('common_status');?></th><!--Status-->
                        <th><?php echo $this->lang->line('common_value');?></th><!--Value-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $x = 1;
                    $total = 0;
                    foreach ($lead as $row) { ?>
                        <tr>
                            <td><?php echo $x; ?></td>
                            <td><?php echo $row['fullname'] ?></td>
                            <td><?php
                                if ($row['organization'] == '') {
                                    echo $row['linkedorganization'];
                                } else {
                                    echo $row['organization'];
                                }
                                ?></td>
                            <td><?php echo $row['phoneMobile'] ?></td>
                            <td><?php echo $row['phoneHome'] ?></td>
                            <td><?php echo $row['responsibleempname'] ?></td>
                            <td><?php echo $row['statusDescription'] ?></td>
                            <td style="text-align: right">
                                <?php
                                $companyID = current_companyID();
                                $product = $this->db->query("SELECT CurrencyCode,SUM((price / companyLocalCurrencyExchangeRate)+(subscriptionAmount / companyLocalCurrencyExchangeRate)+(ImplementationAmount / companyLocalCurrencyExchangeRate)) AS Total FROM srp_erp_crm_leadproducts INNER JOIN srp_erp_currencymaster ON srp_erp_crm_leadproducts.transactionCurrencyID = srp_erp_currencymaster.currencyID WHERE companyID = {$companyID}  AND leadID ='{$row['leadID']}' ")->row_array();
                                if (!empty($product['CurrencyCode'])) {
                                    echo $product['CurrencyCode'] . ' : ' . number_format($product['Total'], 2);
                                }
                                ?>
                                <a href="#"><?php echo ''; ?></a>
                            </td>
                        </tr>
                        <?php
                        $total += $product['Total'];
                        $x++;
                    }
                    ?>
                    </tbody>
                    <tfoot style="border-top: 1px double #0044cc;border-bottom: 1px double #0044cc;">
                    <tr>
                        <td style="min-width: 85%  !important" class="text-right sub_total" colspan="7">
                            <?php echo $this->lang->line('common_total');?>    </td><!--Total-->
                        <td style="min-width: 15% !important"
                            class="text-right total"><?php echo number_format($total, 2); ?></td>
                    </tr>
                    </tfoot>
                </table>
                <?php
            } else {
                $norecfound=$this->lang->line('common_no_records_found');
                echo warning_message($norecfound);
            }
            ?>
        </div>
    </div>
</div>