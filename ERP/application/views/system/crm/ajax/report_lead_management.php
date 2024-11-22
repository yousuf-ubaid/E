<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="width100p">
    <section class="past-posts">
        <div class="posts-holder settings">
            <div class="past-info">
                <div id="toolbar">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="toolbar-title">
                                <i class="fa fa-file-text" aria-hidden="true"></i> <?php echo $this->lang->line('crm_lead_reports');?>
                            </div><!--Lead Reports-->
                        </div>
                        <div class="col-sm-4">
                             <span class="no-print pull-right" style="margin-top: -1%;margin-right: -5%;"> <a class="btn btn-danger btn-sm pull-right" style="padding: 4px 12px;font-size: 9px;" target="_blank" onclick="generateReportPdf('leadnew')">
                                <span class="fa fa-file-pdf-o" aria-hidden="true"> PDF
            </span> </a></span>
                            <span class="no-print pull-right" style="margin-top: -2%;margin-right: 1%;">
                                      <?php  echo export_buttons('leadrpt', 'Lead Report', True, false)?>
                              </span>
                       <!-- <span class="no-print pull-right" style="margin-top: -3%;margin-right: -7%;"> <a
                                class="btn btn-default btn-sm no-print pull-right" target="_blank" onclick="generateReportPdf('leadnew')">
                                <span class="glyphicon glyphicon-print" aria-hidden="true"></span> </a></span>-->
                        </div>
                    </div>
                </div>
                <div class="post-area">
                    <article class="page-content">
                        <div class="system-settings">
                            <div class="row">
                                <div class="col-sm-12" id="leadrpt">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th> <?php echo $this->lang->line('crm_full_name');?> </th><!--Full Name-->
                                            <th><?php echo $this->lang->line('common_company');?> </th><!--Company-->
                                            <th><?php echo $this->lang->line('crm_mobile_no');?> </th><!--Mobile No-->
                                            <th><?php echo $this->lang->line('crm_home_no');?> </th><!--Home No-->
                                            <th>User Responsible</th><!--Home No-->
                                            <th><?php echo $this->lang->line('common_status');?> </th><!--Status-->
                                            <th><?php echo $this->lang->line('common_value');?> </th><!--Value-->
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $x = 1;
                                        $total =0;
                                        $currency = '';
                                        if (!empty($lead)) {
                                            foreach ($lead as $row) {

                                                ?>
                                                <tr>
                                                    <td><?php echo $x; ?></td>
                                                    <td><?php echo $row['fullname'] ?></td>
                                                    <td><?php
                                                        if($row['organization'] == ''){
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
                                                        if(!empty($product['CurrencyCode'])){
                                                            $currency = $product['CurrencyCode'];
                                                            echo  number_format($product['Total'],2);
                                                        }
                                                        ?>
                                                        <a href="#"><?php echo ''; ?></a>
                                                    </td>
                                                </tr>
                                                <?php
                                                $total += $product['Total'];
                                                $x++;
                                            }
                                        }  else { ?>
                                            <tr>
                                                <td colspan="7" style="text-align: center"> <?php echo $this->lang->line('common_no_records_found');?> </td><!--No Records Found-->
                                            </tr>
                                            <?php
                                        }
                                        ?>

                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td style="min-width: 85%  !important" class="text-right sub_total" colspan="7">
                                                <?php echo $this->lang->line('common_total');?>   <?php echo "( $currency ) "?> </td><!--Total-->
                                            <td style="min-width: 15% !important"
                                                class="text-right total"><?php echo number_format($total, 2); ?></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</div>

