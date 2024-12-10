<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('salespersonrpt', 'Sales Person Performance', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salespersonrpt">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong><?php echo $this->lang->line('sales_markating_sales_person_performance'); ?><!--Sales Person Performance--></strong></div>
            <br>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th rowspan="2" style="font-size: 18px;"><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th rowspan="2" style="font-size: 18px;"><?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
                        <th colspan="2" style="font-size: 18px;"><?php echo $this->lang->line('sales_markating_contract_salesorder'); ?><!--Contract/ Sales Order--></th>
                        <th colspan="2" style="font-size: 18px;"><?php echo $this->lang->line('sales_markating_invoiced'); ?><!--Invoiced--></th>
                        <th rowspan="2" style="font-size: 18px;"><?php echo $this->lang->line('common_balance'); ?><!--Balance--></th>
                    </tr>
                    <tr>
                        <th style="width: 9%;font-size: 18px;"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                        <th style="font-size: 18px;"><?php echo $this->lang->line('common_value'); ?><!--Value--></th>
                        <th style="width: 9%;font-size: 18px;"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                        <th style="font-size: 18px;"><?php echo $this->lang->line('common_value'); ?><!--Value--></th>
                    </tr>

                    </thead>
                    <tbody>
                    <?php
                    if ($details)
                        $totalbalance = 0;

                    {

                        if($currency==1){
                            $details = array_group_by($details, 'transactionCurrency');

                        }elseif($currency==2){
                            $details = array_group_by($details, 'companyLocalCurrency');
                            $decimalplaces = $this->common_data['company_data']['company_default_decimal'];

                        }else{
                            $details = array_group_by($details, 'companyReportingCurrency');
                            $decimalplaces = $this->common_data['company_data']['company_reporting_decimal'];
                        }


                        foreach ($details as $value) {
                            $salesOrder = 0;

                            $invoice = 0;
                            $totalamt = 0;
                            $receipt = 0;
                            $returnamt = 0;
                            $creditamount = 0;
                            $receiptamount = 0;
                            $credittot = 0;
                            $creditnettot = 0;
                            $bal = 0;
                            $balttot = 0;
                            $decimalPlace = 2;
                            $decimal = 2;
                            $balance = 0;

                            foreach ($value as $val) {

                                $companyid = current_companyID();
                                $date_format_policy = date_format_policy();
                                $datefromc = $datefrom;
                                $datetoc = $dateto;
                                $datefromconvert = input_format_date($datefromc, $date_format_policy);
                                $datetoconvert = input_format_date($datetoc, $date_format_policy);
                                $date = "";
                                $datecontract = "";
                                $totalamtlocal = 0;
                                $totalamtreporting = 0;
                                if (!empty($datefromc) && !empty($datetoc)) {
                                    $date .= " AND ( invoiceDate >= '" . $datefromconvert . " 00:00:00' AND invoiceDate <= '" . $datetoconvert . " 00:00:00')";
                                }
                                if (!empty($datefromc) && !empty($datetoc)) {
                                    $datecontract .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 00:00:00')";
                                }
                                if($val['docTye']!='2')
                                {
                                    $qtyinvoicedsales = $this->db->query("SELECT
	count(salesPersonID) AS countinvoiced
FROM
	(
SELECT
	srp_erp_customerinvoicemaster.salesPersonID,
	contractmaster.salesPersonID  as salesperid
FROM
	srp_erp_customerinvoicedetails
	JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicedetails.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID
	JOIN srp_erp_contractmaster contractmaster ON contractmaster.contractAutoID = srp_erp_customerinvoicedetails.contractAutoID 
WHERE
	srp_erp_customerinvoicemaster.companyID = $companyid 
	AND srp_erp_customerinvoicemaster.approvedYN = 1 
	AND contractmaster.approvedYN = 1	
	AND srp_erp_customerinvoicedetails.contractAutoID IS NOT NULL 
	$date
	$datecontract
	AND srp_erp_customerinvoicemaster.salesPersonID  = '{$val['salesPersonID']}'
	AND srp_erp_customerinvoicemaster.salesPersonID IS NOT NULL 
GROUP BY
	srp_erp_customerinvoicemaster.invoiceAutoID 
	) t1 
GROUP BY
	t1.salesPersonID")->row_array();
                                }else
                                {
                                    $qtyinvoicedsales = $this->db->query("SELECT
	count(salesPersonID) AS countinvoiced
FROM
	(
SELECT
	srp_erp_customerinvoicemaster.salesPersonID
FROM
	srp_erp_customerinvoicemaster
	
WHERE
	srp_erp_customerinvoicemaster.companyID = $companyid 
		and invoiceType = 'Direct'
	AND srp_erp_customerinvoicemaster.approvedYN = 1 	
	$date
	AND srp_erp_customerinvoicemaster.salesPersonID  = '{$val['salesPersonID']}'
	AND srp_erp_customerinvoicemaster.salesPersonID IS NOT NULL 

	) t1 
GROUP BY
	t1.salesPersonID")->row_array();
                                }




                                $qtycontractsales = $this->db->query("SELECT COUNT(contractAutoID) as contractcount,
contractAutoID
FROM 
(select 
cinvmaster.salesPersonID,
contractmaster.contractAutoID
from 
srp_erp_customerinvoicedetails cinvdetail 
JOIN srp_erp_customerinvoicemaster cinvmaster on cinvmaster.invoiceAutoID = cinvdetail.invoiceAutoID
JOIN srp_erp_contractmaster contractmaster on contractmaster.contractAutoID = cinvdetail.contractAutoID
where 
	cinvmaster.companyID = $companyid 
	AND cinvmaster.approvedYN = 1 
	AND contractmaster.approvedYN = 1	
	AND cinvdetail.contractAutoID IS NOT NULL 
	AND cinvmaster.salesPersonID IS NOT NULL 
	$date
	$datecontract
	AND cinvmaster.salesPersonID ='{$val['salesPersonID']}'
GROUP BY
contractmaster.contractAutoID

UNION 

select 
 contractmaster.salesPersonID,
	contractmaster.contractAutoID
from
srp_erp_contractdetails contractdetail
	LEFT JOIN srp_erp_contractmaster contractmaster ON contractmaster.contractAutoID = contractdetail.contractAutoID 
WHERE
	contractmaster.companyID = $companyid 
	AND contractmaster.approvedYN = 1	
	$datecontract
	AND contractmaster.salesPersonID = '{$val['salesPersonID']}'
	AND contractmaster.salesPersonID IS NOT NULL 
	AND contractmaster.contractAutoID NOT IN (
SELECT
	ifnull( contractAutoID, 0 ) 
FROM
	srp_erp_customerinvoicedetails invoicedetail
	JOIN srp_erp_customerinvoicemaster invoicemaster ON invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID 
WHERE
	invoicemaster.companyID = $companyid 
GROUP BY
	invoicedetail.contractAutoID 
	)
	GROUP BY
contractmaster.contractAutoID
) t1 
GROUP BY 
	t1.salesPersonID")->row_array();



                                $totalcontract = $this->db->query("select 
contract.contractAutoID
from 
srp_erp_customerinvoicedetails invoicedetail 
LEFT JOIN srp_erp_customerinvoicemaster customerinvoicemaster on customerinvoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID
JOIN ( SELECT contractAutoID, sum( transactionAmount ) AS contractamount FROM srp_erp_contractdetails GROUP BY contractAutoID ) contract ON contract.contractAutoID = invoicedetail.contractAutoID 
	LEFT JOIN srp_erp_contractmaster contractmaster ON contractmaster.contractAutoID = contract.contractAutoID 
where 
customerinvoicemaster.companyID = $companyid
AND contractmaster.approvedYN = 1
$date
$datecontract
AND customerinvoicemaster.salesPersonID IS NOT NULL 
AND customerinvoicemaster.salesPersonID = '{$val['salesPersonID']}'
GROUP BY
invoicedetail.contractAutoID
UNION 
SELECT
	contractmaster.contractAutoID
FROM
	srp_erp_contractdetails contractdetail
	LEFT JOIN srp_erp_contractmaster contractmaster ON contractmaster.contractAutoID = contractdetail.contractAutoID
	JOIN srp_erp_salespersonmaster salesperson ON salesperson.salesPersonID = contractmaster.salesPersonID 
WHERE
	contractmaster.companyID = $companyid 
	AND contractmaster.approvedYN = 1 
	$datecontract
	AND contractmaster.salesPersonID = '{$val['salesPersonID']}'
	AND contractmaster.salesPersonID IS NOT NULL 
	AND contractmaster.contractAutoID NOT IN (
SELECT
	ifnull( contractAutoID, 0 ) 
FROM
	srp_erp_customerinvoicedetails invoicedetail
	JOIN srp_erp_customerinvoicemaster invoicemaster ON invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID 
WHERE
	invoicemaster.companyID = $companyid 
GROUP BY
	invoicedetail.contractAutoID 
	) 
GROUP BY
	contractmaster.contractAutoID")->result_array();

                                foreach ($totalcontract as $value)
                                {
                                    $totalvalue = $this->db->query("select
SUM(condetails.transactionAmount)/contractmaster.companyLocalExchangeRate as totalamutlocal,
SUM(condetails.transactionAmount)/contractmaster.companyReportingExchangeRate as totalamutreporting
from 
srp_erp_contractdetails condetails 
LEFT JOIN srp_erp_contractmaster contractmaster on contractmaster.contractAutoID = condetails.contractAutoID 
where 
condetails.contractAutoID = '{$value['contractAutoID']}' ")->row_array();


                                    $totalamtlocal += $totalvalue['totalamutlocal'];
                                    $totalamtreporting += $totalvalue['totalamutreporting'];
                                }

                                ?>
                                <?php
                                if($currency==1){
                                    $value =($val["contractmastertransactionamount"]);
                                    $curr= ($val['transactionCurrency']);
                                    $invoice = ($val['invoicetransactionamount']);
                                    $invoicedecimalplaces = ($val['invoicetransactionCurrencyDecimalPlaces']);
                                    $contractdecplaces = ($val['contracttransactionCurrencyDecimalPlaces']);
                                }elseif($currency==2){

                                    $value =($val["contractmasterlocalexchange"]);

                                    $invoice = ($val['invoicelocalmamount']);
                                    $totalamt = $val['contractmasterlocalexchange'];

                                    $curr= ($val['companyLocalCurrency']);
                                    $invoicedecimalplaces = ($val['invoicecompanyLocalCurrencyDecimalPlaces']);
                                    $contractdecplaces = ($val['contractLocalCurrencyDecimalPlaces']);


                                }else{
                                    $value =($val["contractmasterreportingexchange"]);
                                    $invoice = ($val['invoicereportingamount']);
                                    $curr= ($val['companyReportingCurrency']);
                                    $invoicedecimalplaces = ($val['invoicecompanyReportingCurrencyDecimalPlaces']);
                                    $contractdecplaces = ($val['contractReportingCurrencyDecimalPlaces']);
                                    $totalamt = $val['contractmasterreportingexchange'];
                                }
                                ?>
                                <tr>
                                    <td style="font-size: 18px;"> <?php echo $val["salesPersonName"] ?>
                                    </td>
                                    <td style="font-size: 18px;"> <?php echo $curr ?></td>
                                    <?php   if($val['docTye']!='2'){?>
                                        <td width="200px" style="text-align: right;font-size: 18px;"><?php echo $qtycontractsales['contractcount'] ?> </td>
                                        <td width="200px" style="text-align: right;font-size: 18px;"><?php echo number_format($totalamt,$decimalplaces); ?></td>
                                    <?php }else { ?>
                                        <td width="200px" style="text-align: right;font-size: 18px;">-</td>
                                        <td width="200px" style="text-align: right;font-size: 18px;">-</td>
                                    <?php }?>



                                    <td width="200px" style="text-align: right;font-size: 18px;"><?php
                                        if(!empty($qtyinvoicedsales['countinvoiced'] ))
                                        {
                                            echo $qtyinvoicedsales['countinvoiced'];
                                        }else
                                        {
                                            echo '0';
                                        }
                                        ?></td>
                                    <td width="200px" style="text-align: right;font-size: 18px;"><?php echo number_format($invoice,$decimalplaces) ?></td>
                                    <?php
                                    if($val['docTye']!='2')
                                    {
                                        $balance = ($totalamt - $invoice);
                                        $totalbalance += $balance;
                                    }else {
                                        $balance = ($invoice);
                                    }

                                    ?>

                                    <?php if($val['docTye']!='2')
                                    {?>
                                        <td width="200px" style="text-align: right;font-size: 18px;"><?php echo number_format($balance,$decimalplaces)?></td>
                                    <?php }else {?>
                                        <td width="200px" style="text-align: right;font-size: 18px;"><?php echo '- ' ?></td>
                                    <?php }?>
                                </tr>
                                <?php


                            }




                            ?>

                            <?php

                        }

                    } ?>
                    <tr>
                        <td colspan="6" style="font-size: 18px;"><b><?php echo $this->lang->line('common_total'); ?><!--Total--></b></td>
                        <td class="text-right reporttotal" style="font-size: 18px;"><?php echo number_format($totalbalance,$decimalplaces) ?></td>
                    </tr>
                    </tbody>
                    <tfoot>


                    </tfoot>


                </table>
            </div>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>
    <?php
} ?>
<script>


</script>