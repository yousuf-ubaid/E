<?php

class Api_pos_model extends ERP_Model
{
/*1919111119191919111111113030*/
    function posSalesWithItems_details($dateFrom, $dateTo, $outletID, $companyID)
    {
        $combined_list = array(
            'AppCode' => 'POS-02',
            'PropertyCode' => 'CCB1',
            'ClientID' => 'CCB1-PS-19-00000150',
            'ClientSecret' => 'B0jtekrfdoDMa5U0mwTscw==',
            'POSInterfaceCode' => 'CCB1-PS-19-00000150',
            'BatchCode' => date('2020121913302760'),
            'PosSales' => array()
        );

        $date = "";
        if (!empty($dateFrom) && !empty($dateTo)) {
            $date = " AND DATE(invoice.createdDateTime) BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "' ";
        }

        $master = $this->db->query("SELECT
                                            invoice.invoiceID,
                                            'CCB1' AS PropertyCode,
                                            'CCB1-PS-19-00000150' AS POSInterfaceCode,
                                             DATE_FORMAT(invoice.createdDateTime,\"%d/%m/%Y\") AS ReceiptDate,
                                             DATE_FORMAT(invoice.createdDateTime,\"%h:%i:%s\") AS ReceiptTime,
                                             invoice.invoiceCode
                                             AS ReceiptNo,
                                             det.itemCount AS NoOfItems,
                                             invoice.transactionCurrency AS SalesCurrency,
                                            invoice.netTotal AS TotalSalesAmtB4Tax, 
                                            invoice.netTotal AS TotalSalesAmtAfterTax, 
                                            '0' AS SalesTaxRate,
                                            '0' AS ServiceChargeAmt,
                                            invoice.paidAmount AS PaymentAmt,
                                            invoice.transactionCurrency AS PaymentCurrency,
                                            'Cash' AS PaymentMethod,
                                            'Sales' AS SalesType
                                            FROM
                                                srp_erp_pos_invoice AS invoice
                                            LEFT JOIN ( SELECT COUNT( itemAutoID ) AS itemCount, invoiceID FROM srp_erp_pos_invoicedetail GROUP BY invoiceID ) det ON det.invoiceID = invoice.invoiceID
                                            WHERE
                                                invoice.companyID = {$companyID} 
                                                AND isVoid = 0 
                                                AND invoice.wareHouseAutoID = {$outletID}
                                                {$date}
                                            ORDER BY
                                                invoiceID DESC")->result_array();

        if(!empty($master)){
            foreach ($master AS $val) {
                $x = array();
                $q = $this->db->query("SELECT itemDescription AS ItemDesc, transactionAmount AS ItemAmt, generalDiscountAmount AS ItemDiscountAmt 
                                            FROM srp_erp_pos_invoicedetail 
                                            WHERE companyID = {$companyID} AND invoiceID = {$val['invoiceID']}")->result_array();
                foreach (array_keys($val) as $mas){
                    $x[$mas] = $val[$mas];
                }
                $x['Items'] = $q;
                array_push($combined_list['PosSales'],$x);
            }
        }

        return $combined_list;
    }
}
