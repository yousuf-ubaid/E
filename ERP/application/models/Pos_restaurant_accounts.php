<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*

============================================================

-- File Name : Pos_restaurant.php
-- Project Name : POS
-- Module Name : POS Restaurant model
-- Create date : 25 - October 2016
-- Description : SME POS System.

--REVISION HISTORY
--Date: 25 - Oct 2016 : comment started
--Date: 08 - Sep 2017 : Accounts Double entries corrected  (BANK LEDGER INSERT, ITEM LEDGER INSERT , ITEM MASTER update , item ledger update)

============================================================

*/

class Pos_restaurant_accounts extends ERP_Model
{
    // public $outletID;

    function __construct()
    {
        parent::__construct();
        //$this->outletID = get_outletID();
    }

    function get_bill_payments_bank($shiftID)
    {
        $userID = current_userID();
        $user = current_user();
        $datetime = format_date_mysql_datetime();
        $userGroup = user_group();
        $pc = current_pc();


        $q = "SELECT
                     'POSR' AS documentCode,
                     menusalesmaster.shiftID AS documentMasterAutoID,
                     concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                     CURDATE() AS documentdate,
                     YEAR (curdate()) AS documentYear,
                     MONTH (curdate()) AS documentMonth,
                     'POS Sales' AS documentNarration,
                     '' AS chequeNumber,
                     menusalesmaster.bankGLAutoID AS GLAutoID,
                     chartOfAccount.systemAccountCode AS systemGLCode,
                     chartOfAccount.GLSecondaryCode AS GLCode,
                     chartOfAccount.GLDescription AS GLDescription,
                     chartOfAccount.subCategory AS GLType,
                     'dr' AS amount_type,
                     '0' AS isFromItem,
                    IF (
                        menusalesmaster.isOnTimeCommision = 1,
                        sum(item.salesPriceNetTotal) - ((
                            sum(
                                item.salesPriceNetTotal * (deliveryCommission) / 100
                            )
                        )+(
                                    sum(
                                        item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                    )
                                )+(
                                    sum(
                                        (
                                            (item.salesPriceNetTotal - (
                                                item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                            )
                                        ) )* menusalesmaster.promotionDiscount / 100
                                    )
                                )),(
                        sum(item.salesPriceNetTotal)-(((
                                    sum(
                                        item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                    )
                                )+(
                                    sum(
                                        (
                                            (item.salesPriceNetTotal - (
                                                item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                            )
                                        ) )* menusalesmaster.promotionDiscount / 100
                                    )
                                ))))
                    ) AS transactionAmount,
                     menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                     menusalesmaster.transactionCurrency AS transactionCurrency,
                     '1' AS transactionExchangeRate,
                     currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                     company.company_default_currencyID AS companyLocalCurrencyID,
                     company.company_default_currency AS companyLocalCurrency,
                     getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    IF (
                        menusalesmaster.isOnTimeCommision = 1,
                        sum(item.salesPriceNetTotal) - ((
                            sum(
                                item.salesPriceNetTotal * (deliveryCommission) / 100
                            )
                        )+(
                                    sum(
                                        item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                    )
                                )+(
                                    sum(
                                        (
                                            (item.salesPriceNetTotal - (
                                                item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                            )
                                        ) )* menusalesmaster.promotionDiscount / 100
                                    )
                                )),(
                        sum(item.salesPriceNetTotal)-(((
                                    sum(
                                        item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                    )
                                )+(
                                    sum(
                                        (
                                            (item.salesPriceNetTotal - (
                                                item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                            )
                                        ) )* menusalesmaster.promotionDiscount / 100
                                    )
                                ))))) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    
                     getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                     company.company_reporting_currencyID AS companyReportingCurrencyID,
                     company.company_reporting_currency AS companyReportingCurrency,
                     getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    IF (
                        menusalesmaster.isOnTimeCommision = 1,
                        sum(item.salesPriceNetTotal) - ((
                            sum(
                                item.salesPriceNetTotal * (deliveryCommission) / 100
                            )
                        )+(
                                    sum(
                                        item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                    )
                                )+(
                                    sum(
                                        (
                                            (item.salesPriceNetTotal - (
                                                item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                            )
                                        ) )* menusalesmaster.promotionDiscount / 100
                                    )
                                )),(
                        sum(item.salesPriceNetTotal)-(((
                                    sum(
                                        item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                    )
                                )+(
                                    sum(
                                        (
                                            (item.salesPriceNetTotal - (
                                                item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                            )
                                        ) )* menusalesmaster.promotionDiscount / 100
                                    )
                                )))))
                     / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    
                    
                    
                     getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                     menusalesmaster.segmentID AS segmentID,
                     menusalesmaster.segmentCode AS segmentCode,
                     menusalesmaster.companyID AS companyID,
                     menusalesmaster.companyCode AS companyCode,
                     '" . $userGroup . "' AS createdUserGroup,
                    '" . $pc . "'  AS createdPCID,
                    '" . $userID . "'  createdUserID,
                    '" . $datetime . "'  createdDateTime,
                    '" . $user . "'  createdUserName,
                    '' AS modifiedPCID,
                    '' AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    '' AS modifiedUserName,
                    '" . $datetime . "'  `timestamp`
                    FROM
                        srp_erp_pos_menusalesitems item
                    LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
                    LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesmaster.bankGLAutoID
                    LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                    LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                    LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID #WHERE
                    WHERE
                        menusalesmaster.shiftID = '" . $shiftID . "' and menusalesmaster.isHold=0
                    GROUP BY
                        menusalesmaster.bankGLAutoID, menusalesmaster.isOnTimeCommision";

        $result = $this->db->query($q)->result_array();

        return $result;

    }

    function get_bill_payments_sales_commission($shiftID)
    {
        $userID = current_userID();
        $user = current_user();
        $datetime = format_date_mysql_datetime();
        $userGroup = user_group();
        $pc = current_pc();


        $q = 'SELECT \'POSR\' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( \'POSR/\', warehousemaster.wareHouseCode, \'/\', menusalesmaster.shiftID ) AS documentSystemCode,
                CURDATE() AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                \'POS Sales - Sales Commission\' AS documentNarration,
                \'\' AS chequeNumber,
                 -- GL Fields 
                menusalesmaster.commissionGLAutoID as GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                \'dr\' AS amount_type,
                \'0\' AS isFromItem,
                -- transaction currency fields
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                \'1\' AS transactionExchangeRate,
                (sum(item.menuSalesPrice * (deliveryCommission)/100)) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                -- company local currency fields 
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_default_currencyID ,menusalesmaster.companyID ) AS companyLocalExchangeRate,
                ((sum(item.menuSalesPrice * (deliveryCommission)/100)))/(getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_default_currencyID ,menusalesmaster.companyID ))  AS companyLocalAmount,
                getDecimalPlaces(company.company_default_currencyID) AS companyLocalCurrencyDecimalPlaces,
                -- company reporting currencyfields
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_reporting_currencyID ,menusalesmaster.companyID) AS companyReportingExchangeRate,
                 ((sum(item.menuSalesPrice * (deliveryCommission)/100)))/(getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_reporting_currencyID ,menusalesmaster.companyID )) AS companyReportingAmount ,
                    getDecimalPlaces(company.company_reporting_currencyID) AS companyReportingCurrencyDecimalPlaces,
                
                -- AS confirmedByEmpID,
                -- AS confirmedByName,
                -- AS confirmedDate,
                -- AS approvedDate,
                -- AS approvedbyEmpID,
                -- AS approvedbyEmpName,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                "' . $userGroup . '" AS createdUserGroup,
                "' . $pc . '" AS createdPCID,
                "' . $userID . '" AS createdUserID,
                "' . $datetime . '" AS createdDateTime,
                "' . $user . '" AS createdUserName,
                "" AS modifiedPCID,
                "" AS modifiedUserID,
                NULL AS modifiedDateTime,
                "" AS modifiedUserName,
                "' . $datetime . '" AS `timestamp`
                    
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesmaster.commissionGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            WHERE
                menusalesmaster.shiftID = "' . $shiftID . '" 
            GROUP BY
                menusalesmaster.commissionGLAutoID';
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;

    }

    function get_bill_payments_inventory($shiftID)
    {
        $userID = current_userID();
        $user = current_user();
        $datetime = format_date_mysql_datetime();
        $userGroup = user_group();
        $pc = current_pc();

        $q = "SELECT 'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat(
                    'POSR/',
                    warehousemaster.wareHouseCode,
                    '/',
                    menusalesmaster.shiftID
                ) AS documentSystemCode,
                CURDATE() AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - Inventory' AS documentNarration,
                '' AS chequeNumber,
              item.assetGLAutoID as GLAutoID,
              chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                (
                    sum(
                        itemmaster.companyLocalWacAmount * (
                            (item.qty * item.menuSalesQty )* (
                                getUoMConvertion (
                                    uom.UnitID,
                                    itemmaster.defaultUnitOfMeasureID,
                                    menusalesmaster.companyID
                                )
                            )
                        )
                    )
                ) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
              company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate (
                    menusalesmaster.transactionCurrencyID,
                    company.company_default_currencyID,
                    menusalesmaster.companyID
                ) AS companyLocalExchangeRate,
                (
                    (
                        sum(
                            itemmaster.companyLocalWacAmount * (
                                (item.qty * item.menuSalesQty )* (
                                    getUoMConvertion (
                                        uom.UnitID,
                                        itemmaster.defaultUnitOfMeasureID,
                                        menusalesmaster.companyID
                                    )
                                )
                            )
                        )
                    ) *- 1
                ) / (
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    )
                ) AS companyLocalAmount,
                getDecimalPlaces (
                    company.company_default_currencyID
                ) AS companyLocalCurrencyDecimalPlaces,
              company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate (
                    menusalesmaster.transactionCurrencyID,
                    company.company_reporting_currencyID,
                    menusalesmaster.companyID
                ) AS companyReportingExchangeRate,
                (
                    (
                        sum(
                            itemmaster.companyLocalWacAmount * (
                                (item.qty * item.menuSalesQty )* (
                                    getUoMConvertion (
                                        uom.UnitID,
                                        itemmaster.defaultUnitOfMeasureID,
                                        menusalesmaster.companyID
                                    )
                                )
                            )
                        )
                    ) *- 1
                ) / (
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    )
                ) AS companyReportingAmount,
                getDecimalPlaces (
                    company.company_reporting_currencyID
                ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                '" . $userGroup . "' AS createdUserGroup,
            '" . $pc . "' createdPCID,
            '" . $userID . "' createdUserID,
            '" . $datetime . "' createdDateTime,
            '" . $user . "' createdUserName,
            '' AS modifiedPCID,
            '' AS modifiedUserID,
            NULL AS modifiedDateTime,
            '' AS modifiedUserName,
            '" . $datetime . "' `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM and uom.companyID=menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "' and menusalesmaster.isHold=0
            GROUP BY
                item.assetGLAutoID;";

        $result = $this->db->query($q)->result_array();

        //echo $this->db->last_query();
        return $result;

    }

    function get_bill_payments_cogs($shiftID)
    {
        $userID = current_userID();
        $user = current_user();
        $datetime = format_date_mysql_datetime();
        $userGroup = user_group();
        $pc = current_pc();


        $q = 'SELECT \'POSR\' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( \'POSR/\', warehousemaster.wareHouseCode, \'/\', menusalesmaster.shiftID ) AS documentSystemCode,
                CURDATE() AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                \'POS Sales - Sales Commission\' AS documentNarration,
                \'\' AS chequeNumber,
                 -- GL Fields 
                item.costGLAutoID as GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                \'dr\' AS amount_type,
                \'0\' AS isFromItem,
                -- transaction currency fields
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                \'1\' AS transactionExchangeRate,
                ((sum(itemmaster.companyLocalWacAmount * (item.qty*(getUoMConvertion(uom.UnitID,itemmaster.defaultUnitOfMeasureID,menusalesmaster.companyID)))))*menusalesitem.qty) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                -- company local currency fields 
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_default_currencyID ,menusalesmaster.companyID ) AS companyLocalExchangeRate,
                ((sum(itemmaster.companyLocalWacAmount * (item.qty*(getUoMConvertion(uom.UnitID,itemmaster.defaultUnitOfMeasureID,menusalesmaster.companyID))))))/(getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_default_currencyID ,menusalesmaster.companyID ))  AS companyLocalAmount,
                getDecimalPlaces(company.company_default_currencyID) AS companyLocalCurrencyDecimalPlaces,
                -- company reporting currencyfields
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_reporting_currencyID ,menusalesmaster.companyID) AS companyReportingExchangeRate,
                 ((sum(itemmaster.companyLocalWacAmount * (item.qty*(getUoMConvertion(uom.UnitID,itemmaster.defaultUnitOfMeasureID,menusalesmaster.companyID))))))/(getExchangeRate(menusalesmaster.transactionCurrencyID , company.company_reporting_currencyID ,menusalesmaster.companyID )) AS companyReportingAmount ,
                    getDecimalPlaces(company.company_reporting_currencyID) AS companyReportingCurrencyDecimalPlaces,
                
                -- AS confirmedByEmpID,
                -- AS confirmedByName,
                -- AS confirmedDate,
                -- AS approvedDate,
                -- AS approvedbyEmpID,
                -- AS approvedbyEmpName,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                "' . $userGroup . '" AS createdUserGroup,
                "' . $pc . '" AS createdPCID,
                "' . $userID . '" AS createdUserID,
                "' . $datetime . '" AS createdDateTime,
                "' . $user . '" AS createdUserName,
                "" AS modifiedPCID,
                "" AS modifiedUserID,
                NULL AS modifiedDateTime,
                "" AS modifiedUserName,
                "' . $datetime . '" AS `timestamp`
                    
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_pos_menusalesitems menusalesitem on menusalesitem.menuSalesID = menusalesmaster.menuSalesID
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.costGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            WHERE
                menusalesmaster.shiftID = "' . $shiftID . '" 
            GROUP BY
                item.costGLAutoID';
        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;

    }

    function get_bill_payments_revenue($shiftID)
    {
        $userID = current_userID();
        $user = current_user();
        $datetime = format_date_mysql_datetime();
        $userGroup = user_group();
        $pc = current_pc();
        $q = "SELECT
        'POSR' AS documentCode,
    menusalesmaster.shiftID AS documentMasterAutoID,
    concat(
        'POSR/',
        warehousemaster.wareHouseCode,
        '/',
        menusalesmaster.shiftID
    ) AS documentSystemCode,
    CURDATE() AS documentdate,
    YEAR (curdate()) AS documentYear,
    MONTH (curdate()) AS documentMonth,
    'POS Sales' AS documentNarration,
    '' AS chequeNumber,
    item.revenueGLAutoID AS GLAutoID,
    chartOfAccount.systemAccountCode AS systemGLCode,
    chartOfAccount.GLSecondaryCode AS GLCode,
    chartOfAccount.GLDescription AS GLDescription,
    chartOfAccount.subCategory AS GLType,
    'cr' AS amount_type,
    '0' AS isFromItem,
    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
    menusalesmaster.transactionCurrency AS transactionCurrency,
    '1' AS transactionExchangeRate,
    abs(sum(ifnull(item.salesPriceNetTotal,0))-sum(ifnull(item.TAXAmount,0))) *- 1 AS transactionAmount,
    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
    company.company_default_currencyID AS companyLocalCurrencyID,
    company.company_default_currency AS companyLocalCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_default_currencyID,
        menusalesmaster.companyID
    ) AS companyLocalExchangeRate,
    ((
        abs(sum(ifnull(item.salesPriceNetTotal,0))-sum(ifnull(item.TAXAmount,0))) *- 1
    ) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_default_currencyID,
            menusalesmaster.companyID
        )
    )) AS companyLocalAmount,
    getDecimalPlaces (
        company.company_default_currencyID
    ) AS companyLocalCurrencyDecimalPlaces,
    company.company_reporting_currencyID as companyReportingCurrencyID,
    company.company_reporting_currency AS companyReportingCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_reporting_currencyID,
        menusalesmaster.companyID
    ) AS companyReportingExchangeRate,
    ((
        abs(sum(ifnull(item.salesPriceNetTotal,0))-sum(ifnull(item.TAXAmount,0))) *- 1
    ) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_reporting_currencyID,
            menusalesmaster.companyID
        )
    )) AS companyReportingAmount,
    getDecimalPlaces (
        company.company_reporting_currencyID
    ) AS companyReportingCurrencyDecimalPlaces,
    menusalesmaster.segmentID AS segmentID,
    menusalesmaster.segmentCode AS segmentCode,
    menusalesmaster.companyID AS companyID,
    menusalesmaster.companyCode AS companyCode,
    '" . $userGroup . "' AS createdUserGroup,
'" . $pc . "' createdPCID,
'" . $userID . "' createdUserID,
'" . $datetime . "' createdDateTime,
'" . $user . "' createdUserName,
'' AS modifiedPCID,
'' AS modifiedUserID,
NULL AS modifiedDateTime,
'' AS modifiedUserName,
'" . $datetime . "' `timestamp`
FROM
    srp_erp_pos_menusalesitems item
LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
WHERE
    menusalesmaster.shiftID = '" . $shiftID . "' and menusalesmaster.isHold=0
GROUP BY
    revenueGLAutoID;";

        $result = $this->db->query($q)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function get_cost_of_good_sold($shiftID)
    {
        $userID = current_userID();
        $user = current_user();
        $datetime = format_date_mysql_datetime();
        $userGroup = user_group();
        $pc = current_pc();

        $q = "SELECT 'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat(
                    'POSR/',
                    warehousemaster.wareHouseCode,
                    '/',
                    menusalesmaster.shiftID
                ) AS documentSystemCode,
                CURDATE() AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - COGS' AS documentNarration,
                '' AS chequeNumber,
              item.costGLAutoID as GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'dr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                (
                    sum(
                        itemmaster.companyLocalWacAmount * (
                            (item.qty * item.menuSalesQty) * (
                                getUoMConvertion (
                                    uom.UnitID,
                                    itemmaster.defaultUnitOfMeasureID,
                                    menusalesmaster.companyID
                                )
                            )
                        )
                    )
                ) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate (
                    menusalesmaster.transactionCurrencyID,
                    company.company_default_currencyID,
                    menusalesmaster.companyID
                ) AS companyLocalExchangeRate,
                (
                    (
                        sum(
                            itemmaster.companyLocalWacAmount * (
                                (item.qty * item.menuSalesQty) * (
                                    getUoMConvertion (
                                        uom.UnitID,
                                        itemmaster.defaultUnitOfMeasureID,
                                        menusalesmaster.companyID
                                    )
                                )
                            )
                        )
                    )
                ) / (
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    )
                ) AS companyLocalAmount,
                getDecimalPlaces (
                    company.company_default_currencyID
                ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID as companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate (
                    menusalesmaster.transactionCurrencyID,
                    company.company_reporting_currencyID,
                    menusalesmaster.companyID
                ) AS companyReportingExchangeRate,
                (
                    (
                        sum(
                            itemmaster.companyLocalWacAmount * (
                                (item.qty * item.menuSalesQty) * (
                                    getUoMConvertion (
                                        uom.UnitID,
                                        itemmaster.defaultUnitOfMeasureID,
                                        menusalesmaster.companyID
                                    )
                                )
                            )
                        )
                    )
                ) / (
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    )
                ) AS companyReportingAmount,
                getDecimalPlaces (
                    company.company_reporting_currencyID
                ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                '" . $userGroup . "' AS createdUserGroup,
                '" . $pc . "' createdPCID,
                '" . $userID . "' createdUserID,
                '" . $datetime . "' createdDateTime,
                '" . $user . "' createdUserName,
                '' AS modifiedPCID,
                '' AS modifiedUserID,
                NULL AS modifiedDateTime,
                '' AS modifiedUserName,
                '" . $datetime . "' `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.costGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "' AND menusalesmaster.isHold=0
            GROUP BY
                item.costGLAutoID";

        $result = $this->db->query($q)->result_array();

        return $result;

    }

    function get_bankGL($shiftID)
    {
        $userID = current_userID();
        $user = current_user();
        $datetime = format_date_mysql_datetime();
        $userGroup = user_group();
        $pc = current_pc();

        $q = "SELECT
                 'POSR' AS documentCode,
                 menusalesmaster.shiftID AS documentMasterAutoID,
                 concat(
                    'POSR',
                    warehousemaster.wareHouseCode,
                    '',
                    menusalesmaster.shiftID
                ) AS documentSystemCode,
                 CURDATE() AS documentdate,
                 YEAR (curdate()) AS documentYear,
                 MONTH (curdate()) AS documentMonth,
                 'POS Sales' AS documentNarration,
                 '' AS chequeNumber,
                 menusalesmaster.bankGLAutoID AS GLAutoID,
                 chartOfAccount.systemAccountCode AS systemGLCode,
                 chartOfAccount.GLSecondaryCode AS GLCode,
                 chartOfAccount.GLDescription AS GLDescription,
                 chartOfAccount.subCategory AS GLType,
                 'dr' AS amount_type,
                 '0' AS isFromItem,
                IF (
                    menusalesmaster.isOnTimeCommision = 1,
                    sum(item.salesPriceNetTotal) - ((
                        sum(
                            item.salesPriceNetTotal * (deliveryCommission) / 100
                        )
                    )+(
                                sum(
                                    item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                )
                            )+(
                                sum(
                                    (
                                        (item.salesPriceNetTotal - (
                                            item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                        )
                                    ) )* menusalesmaster.promotionDiscount / 100
                                )
                            )),(
                    sum(item.salesPriceNetTotal)-(((
                                sum(
                                    item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                )
                            )+(
                                sum(
                                    (
                                        (item.salesPriceNetTotal - (
                                            item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                        )
                                    ) )* menusalesmaster.promotionDiscount / 100
                                )
                            ))))
                ) AS transactionAmount,
                 menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                 menusalesmaster.transactionCurrency AS transactionCurrency,
                 '1' AS transactionExchangeRate,
                 currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                 company.company_default_currencyID AS companyLocalCurrencyID,
                 company.company_default_currency AS companyLocalCurrency,
                 getExchangeRate (
                    menusalesmaster.transactionCurrencyID,
                    company.company_default_currencyID,
                    menusalesmaster.companyID
                ) AS companyLocalExchangeRate,
                IF (
                    menusalesmaster.isOnTimeCommision = 1,
                    sum(item.salesPriceNetTotal) - ((
                        sum(
                            item.salesPriceNetTotal * (deliveryCommission) / 100
                        )
                    )+(
                                sum(
                                    item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                )
                            )+(
                                sum(
                                    (
                                        (item.salesPriceNetTotal - (
                                            item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                        )
                                    ) )* menusalesmaster.promotionDiscount / 100
                                )
                            )),(
                    sum(item.salesPriceNetTotal)-(((
                                sum(
                                    item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                )
                            )+(
                                sum(
                                    (
                                        (item.salesPriceNetTotal - (
                                            item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                        )
                                    ) )* menusalesmaster.promotionDiscount / 100
                                )
                            ))))) / (
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    )
                ) AS companyLocalAmount,
                
                 getDecimalPlaces (
                    company.company_default_currencyID
                ) AS companyLocalCurrencyDecimalPlaces,
                 company.company_reporting_currencyID AS companyReportingCurrencyID,
                 company.company_reporting_currency AS companyReportingCurrency,
                 getExchangeRate (
                    menusalesmaster.transactionCurrencyID,
                    company.company_reporting_currencyID,
                    menusalesmaster.companyID
                ) AS companyReportingExchangeRate,
                IF (
                    menusalesmaster.isOnTimeCommision = 1,
                    sum(item.salesPriceNetTotal) - ((
                        sum(
                            item.salesPriceNetTotal * (deliveryCommission) / 100
                        )
                    )+(
                                sum(
                                    item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                )
                            )+(
                                sum(
                                    (
                                        (item.salesPriceNetTotal - (
                                            item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                        )
                                    ) )* menusalesmaster.promotionDiscount / 100
                                )
                            )),(
                    sum(item.salesPriceNetTotal)-(((
                                sum(
                                    item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                )
                            )+(
                                sum(
                                    (
                                        (item.salesPriceNetTotal - (
                                            item.salesPriceNetTotal * menusalesmaster.discountPer / 100
                                        )
                                    ) )* menusalesmaster.promotionDiscount / 100
                                )
                            )))))
                 / (
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    )
                ) AS companyReportingAmount,
                
                
                
                 getDecimalPlaces (
                    company.company_reporting_currencyID
                ) AS companyReportingCurrencyDecimalPlaces,
                 menusalesmaster.segmentID AS segmentID,
                 menusalesmaster.segmentCode AS segmentCode,
                 menusalesmaster.companyID AS companyID,
                 menusalesmaster.companyCode AS companyCode,
                 '" . $userGroup . "' AS createdUserGroup,
                '" . $pc . "' createdPCID,
                '" . $userID . "' createdUserID,
                '" . $datetime . "' createdDateTime,
                '" . $user . "' createdUserName,
                '' AS modifiedPCID,
                '' AS modifiedUserID,
                NULL AS modifiedDateTime,
                '' AS modifiedUserName,
                '" . $datetime . "' `timestamp`
                FROM
                    srp_erp_pos_menusalesitems item
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesmaster.bankGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID #WHERE
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' and menusalesmaster.isHold=0
                GROUP BY
                    menusalesmaster.bankGLAutoID, menusalesmaster.isOnTimeCommision";


        $result = $this->db->query($q)->result_array();

        return $result;

    }

    function get_commission_GL($shiftID)
    {
        $userID = current_userID();
        $user = current_user();
        $datetime = format_date_mysql_datetime();
        $userGroup = user_group();
        $pc = current_pc();

        $q = "SELECT
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Sales Commission' AS documentNarration,
                    '' AS chequeNumber,
                    menusalesmaster.commissionGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    (
                        sum(
                            item.menuSalesPrice * (deliveryCommission) / 100
                        )
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            sum(
                                item.menuSalesPrice * (deliveryCommission) / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            sum(
                                item.menuSalesPrice * (deliveryCommission) / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    '" . $userGroup . "' AS createdUserGroup,
                    '" . $pc . "' createdPCID,
                    '" . $userID . "' createdUserID,
                    '" . $datetime . "' createdDateTime,
                    '" . $user . "' createdUserName,
                    '' AS modifiedPCID,
                    '' AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    '' AS modifiedUserName,
                    '" . $datetime . "' `timestamp`
                FROM
                    srp_erp_pos_menusalesitems item
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesmaster.commissionGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0
                AND (
                    menusalesmaster.deliveryCommission IS NOT NULL
                    AND
                 menusalesmaster.deliveryCommission <> 0 
                )
                GROUP BY
                    menusalesmaster.commissionGLAutoID;";


        $result = $this->db->query($q)->result_array();

        return $result;

    }

    function get_commissionPayable_GL($shiftID)
    {
        $userID = current_userID();
        $user = current_user();
        $datetime = format_date_mysql_datetime();
        $userGroup = user_group();
        $pc = current_pc();

        $q = "SELECT
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Sales Commission Payables' AS documentNarration,
                    '' AS chequeNumber,
                    menusalesmaster.commissionGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    (
                        sum(
                            item.menuSalesPrice * (deliveryCommission) / 100
                        ) *-1
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            sum(
                                item.menuSalesPrice * (deliveryCommission) / 100
                            ) *-1
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            sum(
                                item.menuSalesPrice * (deliveryCommission) / 100
                            ) *-1
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    '" . $userGroup . "' AS createdUserGroup,
                    '" . $pc . "' createdPCID,
                    '" . $userID . "' createdUserID,
                    '" . $datetime . "' createdDateTime,
                    '" . $user . "' createdUserName,
                    '' AS modifiedPCID,
                    '' AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    '' AS modifiedUserName,
                    '" . $datetime . "' `timestamp`
                FROM
                    srp_erp_pos_menusalesitems item
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesmaster.commisionLiabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' and menusalesmaster.isOnTimeCommision=0
                AND menusalesmaster.isHold = 0
                AND (
                    menusalesmaster.deliveryCommission IS NOT NULL
                    AND
                 menusalesmaster.deliveryCommission <> 0 
                )
                GROUP BY
                    menusalesmaster.commissionGLAutoID;";


        $result = $this->db->query($q)->result_array();

        return $result;

    }

    /** UPDATE Exceeded item
     *
     * Logic implemented dicussion with Hisham, Rilwan, Reyaas
     * Created on 2018-01-27
     *
     * function 1 : update_itemExceededRecord
     * changes done by mushtaq discussed with Hisham
     * Updated on 2019-01-17 JIRA SME-1279
     */
    function update_itemExceededRecord($shiftID, $isCreditSales = false)
    {
        if ($isCreditSales) {
            $creditSales = 1;
        } else {
            $creditSales = 0;
        }

        $outletID = get_outletID();

        $companyID = $this->common_data['company_data']['company_id'];
        $exceedglid = $this->db->query("SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE companyID =$companyID AND controlAccountType = 'IEXC'")->row_array();
        $exceedGlAutoID = $exceedglid['GLAutoID'];
        $q = "INSERT INTO srp_erp_itemexceeded (
                documentCode,
                documentAutoID,
                documentSystemCode,
                itemAutoID,
                warehouseAutoID,
                assetGLAutoID,
                costGLAutoID,
                exceededQty,
                updatedQty,
                balanceQty,
                defaultUOMID,
                defaultUOM,
                unitOfMeasureID,
                unitOfMeasure,
                conversionRateUOM,
                unitCost,
                isFromCreditSales,
                transactionAmount,
                companyLocalAmount,
                companyReportingAmount,
                documentDate,
                companyID,
                segmentID,
                segmentCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`,
                exceededGLAutoID
                ) (
                SELECT
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentAutoID,
                    concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                    item.itemAutoID AS itemAutoID,
                    warehousemaster.wareHouseAutoID AS warehouseAutoID,
                    item.assetGLAutoID AS assetGLAutoID,
                    item.costGLAutoID AS costGLAutoID,
                    (
                    sum(
                    ( IFNULL( item.menuSalesQty, 0 ) * item.qty ) / getUoMConvertion ( item.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID )
                    ) - IFNULL(warehouseitems.currentStock,0)
                    ) AS exceededQty,
                    0 AS updatedQty,
                    (
                    sum(
                    ( IFNULL( item.menuSalesQty, 0 ) * item.qty ) / getUoMConvertion ( item.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID )
                    ) - IFNULL(warehouseitems.currentStock,0)
                    ) AS balanceQty,
                    itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
                    itemmaster.defaultUnitOfMeasure AS defaultUOM,
                    item.UOMID AS unitOfMeasureID,
                    item.UOM AS unitOfMeasure,
                    getUoMConvertion ( item.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID ) AS conversionRateUOM,
                    ( item.cost / item.qty ) AS unitCost,
                    '" . $creditSales . "' AS isFromCreditSales,
                    abs(IFNULL( ( SUM(item.cost) / sum(item.qty / getUoMConvertion ( item.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID )) ), 0 ) * ( sum( ( IFNULL(item.menuSalesQty, 0) * item.qty ) ) - IFNULL( warehouseitems.currentStock, 0 ) ) ) AS transactionAmount,
                    abs(IFNULL( ( SUM(item.cost) / sum(item.qty / getUoMConvertion ( item.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID )) ), 0 ) * ( sum( ( IFNULL(item.menuSalesQty, 0) * item.qty ) ) - IFNULL( warehouseitems.currentStock, 0 ) ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                    abs(IFNULL( ( SUM(item.cost) / sum(item.qty / getUoMConvertion ( item.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID )) ), 0 ) * ( sum( ( IFNULL(item.menuSalesQty, 0) * item.qty ) ) - IFNULL( warehouseitems.currentStock, 0 ) ) ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                    shiftDetail.startTime AS documentDate,
                    menusalesmaster.companyID AS companyID,
                    segmentConfig.segmentID AS segmentID,
                    segmentConfig.segmentCode AS segmentCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    NOW( ) AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    CURRENT_TIMESTAMP ( ) AS `timestamp`,
                    '" . $exceedGlAutoID . "' AS exceedGLAutoID
                FROM
                    srp_erp_pos_menusalesitemdetails item
                LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
                LEFT JOIN (
                    SELECT
                        if(SUM(
            transactionQTY / convertionRate
        ) < 0,0,SUM(
            transactionQTY / convertionRate
        )) AS currentStock,wareHouseAutoID,itemAutoID
                    FROM
                        srp_erp_itemledger
                    WHERE
                        wareHouseAutoID =  $outletID
                GROUP BY
                    itemAutoID,wareHouseAutoID
                ) warehouseitems ON warehouseitems.itemAutoID = item.itemAutoID
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID and menusalesmaster.wareHouseAutoID = item.warehouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitID = item.UOMID AND uom.companyID = menusalesmaster.companyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID and shiftDetail.wareHouseID=menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_pos_segmentconfig segmentConfig ON segmentConfig.wareHouseAutoID = warehousemaster.wareHouseAutoID
                WHERE
                    menusalesmaster.shiftID =  $shiftID
                    AND menusalesmaster.isHold = 0
                    AND menusalesmaster.isVoid = 0
                    AND menusalesmaster.isCreditSales =  $creditSales
                    AND warehousemaster.wareHouseAutoID = $outletID
                GROUP BY
                    itemAutoID
                HAVING
                    exceededQty > 0
                ORDER BY
                    documentAutoID DESC
                    )";

        $result = $this->db->query($q);
        return $result;
    }

    function update_itemExceededRecord_creditSales($shiftID)
    {

        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_itemexceeded (
                documentCode,
                documentAutoID,
                documentSystemCode,
                itemAutoID,
                warehouseAutoID,
                assetGLAutoID,
                costGLAutoID,
                exceededQty,
                updatedQty,
                balanceQty,
                defaultUOMID,
                defaultUOM,
                unitOfMeasureID,
                unitOfMeasure,
                conversionRateUOM,
                unitCost,
                isFromCreditSales,
                transactionAmount,
                companyLocalAmount,
                companyReportingAmount,
                documentDate,
                companyID,
                segmentID,
                segmentCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp` 
                ) (
                SELECT
                    'CINV' AS documentCode,
                    menusalesmaster.shiftID AS documentAutoID,
                    concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                    item.itemAutoID AS itemAutoID,
                    warehousemaster.wareHouseAutoID AS warehouseAutoID,
                    item.assetGLAutoID AS assetGLAutoID,
                    item.costGLAutoID AS costGLAutoID,
                    (
                    sum(
                    ( IFNULL( item.menuSalesQty, 0 ) * item.qty ) / getUoMConvertion ( item.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID )
                    ) - warehouseitems.currentStock
                    ) AS exceededQty,
                    0 AS updatedQty,
                    (
                    sum(
                    ( IFNULL( item.menuSalesQty, 0 ) * item.qty ) / getUoMConvertion ( item.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID )
                    ) - warehouseitems.currentStock
                    ) AS balanceQty,
                    itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
                    itemmaster.defaultUnitOfMeasure AS defaultUOM,
                    item.UOMID AS unitOfMeasureID,
                    item.UOM AS unitOfMeasure,
                    getUoMConvertion ( item.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID ) AS conversionRateUOM,
                    ( item.cost / item.qty ) AS unitCost,
                    1 AS isFromCreditSales,
                    abs(
                    IFNULL( ( item.cost / item.qty ), 0 ) * (
                    sum(
                    ( IFNULL( item.menuSalesQty, 0 ) * item.qty ) / getUoMConvertion ( item.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID )
                    ) - warehouseitems.currentStock
                    )
                    ) AS transactionAmount,
                    abs(
                    (
                    IFNULL( ( item.cost / item.qty ), 0 ) * (
                    sum(
                    ( IFNULL( item.menuSalesQty, 0 ) * item.qty ) / getUoMConvertion ( item.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID )
                    ) - warehouseitems.currentStock
                    )
                    )
                    ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                    abs(
                    (
                    IFNULL( ( item.cost / item.qty ), 0 ) * (
                    sum(
                    ( IFNULL( item.menuSalesQty, 0 ) * item.qty ) / getUoMConvertion ( item.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID )
                    ) - warehouseitems.currentStock
                    )
                    )
                    ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                    shiftDetail.startTime AS documentDate,
                    menusalesmaster.companyID AS companyID,
                    segmentConfig.segmentID AS segmentID,
                    segmentConfig.segmentCode AS segmentCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    NOW( ) AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    CURRENT_TIMESTAMP ( ) AS `timestamp`
                FROM
                    srp_erp_pos_menusalesitemdetails item
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
                    LEFT JOIN srp_erp_warehouseitems warehouseitems ON itemmaster.itemAutoID = warehouseitems.itemAutoID
                    LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
                    LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
                    LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                    LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                    LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM
                    AND uom.companyID = menusalesmaster.companyID
                    LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                    LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                    LEFT JOIN srp_erp_pos_segmentconfig segmentConfig ON segmentConfig.wareHouseAutoID = warehousemaster.wareHouseAutoID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                    AND menusalesmaster.isHold = 0
                    AND menusalesmaster.isVoid = 0
                    AND menusalesmaster.isCreditSales = 1
                    AND warehousemaster.wareHouseAutoID = '" . $outletID . "'
                    AND warehouseitems.wareHouseAutoID = '" . $outletID . "'
                GROUP BY
                    itemAutoID
                HAVING
                    exceededQty > 0
                ORDER BY
                    documentAutoID DESC
                    )";

        $result = $this->db->query($q);
        return $result;
    }

    /*changes done by mushtaq discussed with Hisham
    Updated on 2019-01-17 JIRA SME-1279 */
    function update_itemExceededRecord_creditSales_menuSalesID($shiftID, $menuSalesID)
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $exceedglid = $this->db->query("SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE companyID =$companyID AND controlAccountType = 'IEXC'")->row_array();
        $exceedGlAutoID = $exceedglid['GLAutoID'];
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_itemexceeded (
                documentCode,
                documentAutoID,
                documentSystemCode,
                itemAutoID,
                warehouseAutoID,
                assetGLAutoID,
                costGLAutoID,
                exceededQty,
                updatedQty,
                balanceQty,
                defaultUOMID,
                defaultUOM,
                unitOfMeasureID,
                unitOfMeasure,
                conversionRateUOM,
                unitCost,
                isFromCreditSales,
                transactionAmount,
                companyLocalAmount,
                companyReportingAmount,
                documentDate,
                companyID,
                segmentID,
                segmentCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`,
                exceededGLAutoID
                ) (
                SELECT
	'POSR' AS documentCode,
	menusalesmaster.shiftID AS documentAutoID,
	concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
	item.itemAutoID AS itemAutoID,
	warehousemaster.wareHouseAutoID AS warehouseAutoID,
	menusalesitemdetails.assetGLAutoID AS assetGLAutoID,
	menusalesitemdetails.costGLAutoID AS costGLAutoID,		
	(
	sum(
	( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) / getUoMConvertion ( menusalesitemdetails.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID ) 
	) - IFNULL( warehouseitems.currentStock, 0 ) 
	) AS exceededQty,
	0 AS updatedQty,
	(
	sum(
	( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) / getUoMConvertion ( menusalesitemdetails.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID ) 
	) - IFNULL( warehouseitems.currentStock, 0 ) 
	) AS balanceQty,
	itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
	itemmaster.defaultUnitOfMeasure AS defaultUOM,
	itemmaster.defaultUnitOfMeasureID AS unitOfMeasureID,
	itemmaster.defaultUnitOfMeasure AS unitOfMeasure,
	1 AS conversionRateUOM,
	(
	abs(
	IFNULL( ( SUM( menusalesitemdetails.cost ) / sum( menusalesitemdetails.qty ) ), 0 ) * (
	sum( ( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) ) - IFNULL( warehouseitems.currentStock, 0 ) 
	) 
	) / (
	sum(
	( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) / getUoMConvertion ( menusalesitemdetails.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID ) 
	) - IFNULL( warehouseitems.currentStock, 0 ) 
	) 
	) AS unitCost,
	1 AS isFromCreditSales,
	abs(
	IFNULL( ( SUM( menusalesitemdetails.cost ) / sum( menusalesitemdetails.qty ) ), 0 ) * (
	sum( ( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) ) - IFNULL( warehouseitems.currentStock, 0 ) 
	) 
	) AS transactionAmount,
	abs(
	IFNULL( ( SUM( menusalesitemdetails.cost ) / sum( menusalesitemdetails.qty ) ), 0 ) * (
	sum( ( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) ) - IFNULL( warehouseitems.currentStock, 0 ) 
	) 
	) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
	abs(
	IFNULL( ( SUM( menusalesitemdetails.cost ) / sum( menusalesitemdetails.qty ) ), 0 ) * (
	sum( ( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) ) - IFNULL( warehouseitems.currentStock, 0 ) 
	) 
	) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
	shiftDetail.startTime AS documentDate,
	menusalesmaster.companyID AS companyID,
	segmentConfig.segmentID AS segmentID,
	segmentConfig.segmentCode AS segmentCode,
	menusalesmaster.createdUserGroup AS createdUserGroup,
	menusalesmaster.createdPCID AS createdPCID,
	menusalesmaster.createdUserID AS createdUserID,
	NOW( ) AS createdDateTime,
	menusalesmaster.createdUserName AS createdUserName,
	CURRENT_TIMESTAMP ( ) AS `timestamp`,
	'".$exceedGlAutoID."' AS exceedGLAutoID 
FROM
srp_erp_itemledger_review item	
	LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
	LEFT JOIN (
SELECT
IF
	( SUM( transactionQTY / convertionRate ) < 0, 0, SUM( transactionQTY / convertionRate ) ) AS currentStock,
	wareHouseAutoID,
	itemAutoID 
FROM
	srp_erp_itemledger 
WHERE
	wareHouseAutoID = $outletID 
GROUP BY
	itemAutoID,
	wareHouseAutoID 
	) warehouseitems ON warehouseitems.itemAutoID = item.itemAutoID
	LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.pos_menusalesID 
	AND menusalesmaster.wareHouseAutoID = item.warehouseAutoID
	LEFT JOIN srp_erp_pos_menusalesitemdetails menusalesitemdetails ON menusalesitemdetails.menuSalesItemDetailID=item.pos_menusalesItemDetailID
	LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
	LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
	LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
	LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitID = menusalesitemdetails.UOMID 
	AND uom.companyID = menusalesmaster.companyID
	LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
	LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID 
	AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
	LEFT JOIN srp_erp_pos_segmentconfig segmentConfig ON segmentConfig.wareHouseAutoID = warehousemaster.wareHouseAutoID 
WHERE
	menusalesmaster.shiftID = $shiftID
	AND menusalesmaster.isHold = 0 
	AND menusalesmaster.isVoid = 0 
	AND menusalesmaster.isCreditSales = 1 
  AND warehousemaster.wareHouseAutoID = $outletID 
GROUP BY
	itemAutoID 
HAVING
	exceededQty > 0 
ORDER BY
	documentAutoID DESC 
                    )";

        $result = $this->db->query($q);
        return $result;
    }

    /** Start New GL Entries Review
     *  Edit by Madura
     */
    /** 1. REVENUE Review */
    function update_revenue_generalLedger_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID
            ) (SELECT
                menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - Revenue' AS documentNarration,
                '' AS chequeNumber,
                item.revenueGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID )  )) AS companyLocalAmount, getDecimalPlaces ( company.company_default_currencyID  ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID as companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID )  )) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                shiftDetail.createdUserGroup AS createdUserGroup,
            shiftDetail.createdPCID as  createdPCID,
            shiftDetail.createdUserID as  createdUserID,
            shiftDetail.startTime as createdDateTime,
            shiftDetail.createdUserName as createdUserName,
            NULL AS modifiedPCID,
            NULL AS modifiedUserID,
            NULL AS modifiedDateTime,
            null AS modifiedUserName,
            CURRENT_TIMESTAMP() as `timestamp`,
            item.menuSalesItemID as pos_menusalesItemID,
	        menusalesmaster.menuSalesID as pos_menusalesID,
            menusalesmaster.shiftID as pos_shiftID
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID AND menusalesmaster.wareHouseAutoID = item.wareHouseAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND menusalesmaster.wareHouseAutoID = shiftDetail.wareHouseID 
            
            WHERE
                menusalesmaster.menuSalesID = '" . $invoiceID . "' AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'  AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
            GROUP BY
                revenueGLAutoID, item.menuSalesItemID);";

        $result = $this->db->query($q);
        //echo $this->db->last_query();
        return $result;
    }

    /** 2. BANK OR CASH Review */
    function update_bank_cash_generalLedger_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID )
                (SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Bank' AS documentNarration,
                    '' AS chequeNumber,
                    payments.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(payments.amount) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID createdUserID,
                    shiftDetail.startTime createdDateTime,
                    menusalesmaster.createdUserName createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID
                FROM
                    srp_erp_pos_menusalespayments payments 
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON payments.menuSalesID = menusalesmaster.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = payments.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID  
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID                
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "'  
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND payments.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND payments.paymentConfigMasterID!=7 
                    AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    payments.GLCode);";

        $result = $this->db->query($q);
        return $result;
    }

    /** 3. COGS Review */
    function update_cogs_generalLedger_review($invoiceID, $isCreditSales = true, $log = false)
    {
        $outletID = get_outletID();
        if ($log) {
            echo '<strong>update_cogs_generalLedger</strong><br/>';
        }

        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID )            
            (SELECT
                menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - COGS' AS documentNarration,
                '' AS chequeNumber,
                item.costGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'dr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                  ( sum( IFNULL(item.cost,0) * item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                  ( sum( IFNULL(item.cost,0) *  item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP() AS  `timestamp`,
                item.menuSalesItemID as pos_menusalesItemID,
                menusalesmaster.menuSalesID as pos_menusalesID,
                menusalesmaster.shiftID as pos_shiftID
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID  AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.costGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
            WHERE
                menusalesmaster.menuSalesID = '" . $invoiceID . "'
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND item.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0   
                AND menusalesmaster.isVoid = 0";

        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 0";
        }
        $q .= " GROUP BY
                item.costGLAutoID, item.menuSalesItemID)";

        $result = $this->db->query($q);
        if ($log) {
            echo $q . '<br/><br/>';

        }
        return $result;
    }

    /** 4. INVENTORY Review */
    function update_inventory_generalLedger_review($invoiceID, $isCreditSales = true, $log = false)
    {
        $outletID = get_outletID();
        if ($log) {
            echo '<strong>update_inventory_generalLedger</strong><br/>';
        }
        $q = "INSERT INTO srp_erp_generalledger_review(
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID )
            (SELECT
                menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - Inventory' AS documentNarration,
                '' AS chequeNumber,
                item.assetGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                abs(( sum(IFNULL(item.cost, 0) * item.menuSalesQty) )) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                ( abs(  sum(IFNULL(item.cost, 0) *item.menuSalesQty) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                ( abs( sum(IFNULL(item.cost, 0) * item.menuSalesQty) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP () `timestamp`,
                item.menuSalesItemID as pos_menusalesItemID,
                menusalesmaster.menuSalesID as pos_menusalesID,
                menusalesmaster.shiftID as pos_shiftID
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID  AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
            
            WHERE
                menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.menuSalesID = '" . $invoiceID . "'
                AND item.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0   
                AND menusalesmaster.isVoid = 0";
        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 0";
        }
        $q .= " GROUP BY
                item.assetGLAutoID, item.menuSalesItemID
                HAVING transactionAmount !=0)";

        if ($log) {
            echo $q . '<br/><br/>';
        }
        $result = $this->db->query($q);
        return $result;
    }

    function update_exceededGL_generalLedger_review($invoiceID, $isCreditSales = true, $log = false)
    {
        $outletID = get_outletID();
        $companyID = current_companyID();
        $exceedglid = $this->db->query("SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE companyID =$companyID AND controlAccountType = 'IEXC'")->row_array();
        $exceedGlAutoID = $exceedglid['GLAutoID'];
        if ($log) {
            echo '<strong>update_exceededGL_generalLedger</strong><br/>';
        }
        $q = "INSERT INTO srp_erp_generalledger_review(
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID
            )
            (SELECT
	shiftDetail.wareHouseID,
	'POSR' AS documentCode,
	shiftDetail.shiftID AS documentMasterAutoID,
	exceed.documentSystemCode,
	DATE_FORMAT(
		shiftDetail.startTime,
		'%Y-%m-%d'
	) AS documentdate,
	YEAR (curdate()) AS documentYear,
	MONTH (curdate()) AS documentMonth,
	'POS Sales - Exceeded' AS documentNarration,
	'' AS chequeNumber,
	'" . $exceedGlAutoID . "' AS GLAutoID,
	chartOfAccount.systemAccountCode AS systemGLCode,
	chartOfAccount.GLSecondaryCode AS GLCode,
	chartOfAccount.GLDescription AS GLDescription,
	chartOfAccount.subCategory AS GLType,
	'cr' AS amount_type,
	'0' AS isFromItem,
	menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
	menusalesmaster.transactionCurrency AS transactionCurrency,
	'1' AS transactionExchangeRate,
	abs(
		sum(
			(
				IFNULL(exceed.transactionAmount, 0)
			)
		)
	) *- 1 AS transactionAmount,
	currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
	company.company_default_currencyID AS companyLocalCurrencyID,
	company.company_default_currency AS companyLocalCurrency,
	getExchangeRate (
		menusalesmaster.transactionCurrencyID,
		company.company_default_currencyID,
		menusalesmaster.companyID
	) AS companyLocalExchangeRate,
	(
		abs(
			sum(
				(
					IFNULL(exceed.transactionAmount, 0)
				)
			)
		) *- 1
	) / (
		getExchangeRate (
			menusalesmaster.transactionCurrencyID,
			company.company_default_currencyID,
			menusalesmaster.companyID
		)
	) AS companyLocalAmount,
	getDecimalPlaces (
		company.company_default_currencyID
	) AS companyLocalCurrencyDecimalPlaces,
	company.company_reporting_currencyID AS companyReportingCurrencyID,
	company.company_reporting_currency AS companyReportingCurrency,
	getExchangeRate (
		menusalesmaster.transactionCurrencyID,
		company.company_reporting_currencyID,
		menusalesmaster.companyID
	) AS companyReportingExchangeRate,
	(
		abs(
			sum(
				(
					IFNULL(exceed.transactionAmount, 0)
				)
			)
		) *- 1
	) / (
		getExchangeRate (
			menusalesmaster.transactionCurrencyID,
			company.company_reporting_currencyID,
			menusalesmaster.companyID
		)
	) AS companyReportingAmount,
	getDecimalPlaces (
		company.company_reporting_currencyID
	) AS companyReportingCurrencyDecimalPlaces,
	menusalesmaster.segmentID AS segmentID,
	menusalesmaster.segmentCode AS segmentCode,
	menusalesmaster.companyID AS companyID,
	menusalesmaster.companyCode AS companyCode,
	menusalesmaster.createdUserGroup AS createdUserGroup,
	menusalesmaster.createdPCID AS createdPCID,
	menusalesmaster.createdUserID AS createdUserID,
	shiftDetail.startTime AS createdDateTime,
	menusalesmaster.createdUserName AS createdUserName,
	NULL AS modifiedPCID,
	NULL AS modifiedUserID,
	NULL AS modifiedDateTime,
	NULL AS modifiedUserName,
	CURRENT_TIMESTAMP () `timestamp`,
	0 as pos_menusalesItemID,
    menusalesmaster.menuSalesID as pos_menusalesID,
    menusalesmaster.shiftID as pos_shiftID
    FROM
        srp_erp_pos_shiftdetails shiftDetail
    LEFT JOIN srp_erp_itemexceeded exceed ON shiftDetail.shiftID = exceed.documentAutoID
    AND shiftDetail.wareHouseID = exceed.warehouseAutoID
    AND exceed.documentCode = 'POSR'
    LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = '" . $exceedGlAutoID . "'
    LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.shiftID = shiftDetail.shiftID
    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
    LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
    LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
    WHERE
        menusalesmaster.menuSalesID = '" . $invoiceID . "'
    AND exceed.wareHouseAutoID = '" . $outletID . "' ";

        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 0";
        }
        $q .= " HAVING transactionAmount !=0)";
        $result = $this->db->query($q);
        return $result;
    }
    /** ---------------------- */


    /** 5. TAX Review */
    function update_tax_generalLedger_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                taxMasterAutoID,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - TAX' AS documentNarration,
                    menusalesTax.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    menusalesTax.taxmasterID as taxmasterID,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID
                FROM
                    srp_erp_pos_menusalestaxes menusalesTax
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = menusalesTax.menuSalesID 
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesTax.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "'
                AND menusalesmaster.isHold = 0 
                AND menusalesmaster.isVoid = 0 
                AND menusalesmaster.isCreditSales = 0 
                AND menusalesTax.wareHouseAutoID = '" . $outletID . "' 
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                GROUP BY
                    chartOfAccount.GLAutoID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 6. COMMISSION EXPENSE Review  */
    function update_commissionExpense_generalLedger_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Sales Commission' AS documentNarration,
                    customers.expenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        menusalesmaster.deliveryCommissionAmount
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            IFNULL(menusalesmaster.deliveryCommissionAmount,0)
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.expenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "'  AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                AND (
                    menusalesmaster.deliveryCommission IS NOT NULL
                    AND menusalesmaster.deliveryCommission <> 0
                )
                AND menusalesmaster.isDelivery = 1 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    customers.expenseGLAutoID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 7. COMMISSION PAYABLE Review */
    function update_commissionPayable_generalLedger_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                    wareHouseAutoID,
                    documentCode,
                    documentMasterAutoID,
                    documentSystemCode,
                    documentDate,
                    documentYear,
                    documentMonth,
                    documentNarration,
                    GLAutoID,
                    systemGLCode,
                    GLCode,
                    GLDescription,
                    GLType,
                    amount_type,
                    isFromItem,
                    transactionCurrency,
                    transactionExchangeRate,
                    transactionAmount,
                    transactionCurrencyID,
                    transactionCurrencyDecimalPlaces,
                    companyLocalCurrencyID,
                    companyLocalCurrency,
                    companyLocalExchangeRate,
                    companyLocalAmount,
                    companyLocalCurrencyDecimalPlaces,
                    companyReportingCurrencyID,
                    companyReportingCurrency,
                    companyReportingExchangeRate,
                    companyReportingAmount,
                    companyReportingCurrencyDecimalPlaces,
                    segmentID,
                    segmentCode,
                    companyID,
                    companyCode,
                    createdUserGroup,
                    createdPCID,
                    createdUserID,
                    createdDateTime,
                    createdUserName,
                    modifiedPCID,
                    modifiedUserID,
                    modifiedDateTime,
                    modifiedUserName,
                    `timestamp`,
                    pos_menusalesItemID,
                    pos_menusalesID,
                    pos_shiftID
                )( 
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Sales Commission Payable' AS documentNarration,
                    customers.liabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                menusalesmaster.deliveryCommissionAmount
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                               IFNULL( menusalesmaster.deliveryCommissionAmount,0)
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.liabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "'  AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                AND (
                    menusalesmaster.deliveryCommission IS NOT NULL
                    AND menusalesmaster.deliveryCommission <> 0
                )
                AND menusalesmaster.isDelivery = 1
                AND menusalesmaster.isOnTimeCommision = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    customers.liabilityGLAutoID
                );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 8. ROYALTY PAYABLE Review */
    function update_royaltyPayable_generalLedger_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Royalty Payable' AS documentNarration,
                    franchisemaster.royaltyLiabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyLiabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "' AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    franchisemaster.royaltyLiabilityGLAutoID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 9. ROYALTY EXPENSES Review */
    function update_royaltyExpenses_generalLedger_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Royalty Expenses' AS documentNarration,
                    franchisemaster.royaltyExpenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        ( IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0) ) * (
                            franchisemaster.royaltyPercentage / 100
                        )
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyExpenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID 
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID

                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "' AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    franchisemaster.royaltyExpenseGLAutoID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 10. SERVICE CHARGE Review */
    function update_serviceCharge_generalLedger_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Service Charge' AS documentNarration,
                    servicecharge.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(
                                servicecharge.serviceChargeAmount,
                                0
                            )
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID
                FROM
                    srp_erp_pos_menusalesservicecharge servicecharge
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = servicecharge.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = servicecharge.GLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND servicecharge.wareHouseAutoID = '" . $outletID . "' 
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    chartOfAccount.GLAutoID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** 11. Outlet TAX Review */
    function update_outlet_tax_generalLedger_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                taxMasterAutoID,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Outlet - TAX' AS documentNarration,
                    menusalesTax.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    menusalesTax.taxmasterID as taxmasterID,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID
                FROM
                    srp_erp_pos_menusalesoutlettaxes menusalesTax
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = menusalesTax.menuSalesID 
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesTax.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "'
                AND menusalesmaster.isHold = 0 
                AND menusalesmaster.isVoid = 0 
                AND menusalesmaster.isCreditSales = 0 
                AND menusalesTax.wareHouseAutoID = '" . $outletID . "' 
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                GROUP BY
                    chartOfAccount.GLAutoID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 12. OWN DELIVERY */
    function update_own_delivery_generalLedger_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Own Delivery' AS documentNarration,
                    menusalesmaster.deliveryRevenueGLID as GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(
                                menusalesmaster.ownDeliveryAmount,
                                0
                            )
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        menusalesmaster.ownDeliveryAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        menusalesmaster.ownDeliveryAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID
                FROM
                srp_erp_pos_menusalesmaster menusalesmaster
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesmaster.deliveryRevenueGLID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.menuSalesID = '$invoiceID' 
                    AND menusalesmaster.wareHouseAutoID = '$outletID'
                   
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
                AND menusalesmaster.ownDeliveryAmount>0
                GROUP BY
                    chartOfAccount.GLAutoID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** BANK LEDGER Review  */
    function update_bankLedger_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_bankledger_review (
                wareHouseAutoID,
                documentDate,
                transactionType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                bankCurrencyID,
                bankCurrency,
                bankCurrencyExchangeRate,
                bankCurrencyAmount,
                bankCurrencyDecimalPlaces,
                modeofPayment,
                memo,
                bankName,
                bankGLAutoID,
                bankSystemAccountCode,
                bankGLSecondaryCode,
                documentMasterAutoID,
                documentType,
                documentSystemCode,
                createdPCID,
                companyID,
                companyCode,
                segmentID,
                segmentCode,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timeStamp`,
                pos_shiftID,
                pos_menusalesID,
                pos_menusalesPaymetID
                )
                (SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d')  AS documentDate,
                    '1' AS transactionType,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(payments.amount) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    chartOfAccount.bankCurrencyID AS bankCurrencyID,
                    chartOfAccount.bankCurrencyCode AS bankCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        chartOfAccount.bankCurrencyID,
                        menusalesmaster.companyID
                    ) AS bankCurrencyExchangeRate,
                    (SUM(payments.amount)) /(
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            chartOfAccount.bankCurrencyID,
                            menusalesmaster.companyID
                        )
                    )  AS bankCurrencyAmount,
                    chartOfAccount.bankCurrencyDecimalPlaces AS bankCurrencyDecimalPlaces,
                    '1' AS modeofPayment,
                    'payment collection from POSR' AS memo,
                    chartOfAccount.bankName AS bankName,
                    chartOfAccount.GLAutoID AS bankGLAutoID,
                    chartOfAccount.systemAccountCode AS bankSystemAccountCode,
                    chartOfAccount.GLSecondaryCode AS bankGLSecondaryCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    'RV' AS documentType,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime createdDateTime,
                    menusalesmaster.createdUserName createdUserName,
                    CURRENT_TIMESTAMP () `timestamp`,
                    menusalesmaster.shiftID as pos_shiftID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    payments.menuSalesPaymentID as pos_menusalesPaymetID
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_menusalespayments payments ON payments.menuSalesID = menusalesmaster.menuSalesID AND payments.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = payments.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND payments.paymentConfigMasterID!=7 
                    AND payments.paymentConfigMasterID!=25
                GROUP BY
                    payments.GLCode);";

        $result = $this->db->query($q);
        return $result;
    }

    /**Credit Sales Double Entry Review */
    /** 1. CREDIT SALES  - REVENUE Review */
    function update_revenue_generalLedger_credit_sales_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID,
                isCreditSales
            ) (SELECT
                menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                menusalesmaster.documentSystemCode  AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - Revenue',' - ',menusalesmaster.invoiceCode) AS documentNarration,
                '' AS chequeNumber,
                item.revenueGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID )  )) AS companyLocalAmount, getDecimalPlaces ( company.company_default_currencyID  ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID as companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID )  )) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                shiftDetail.createdUserGroup AS createdUserGroup,
            shiftDetail.createdPCID as  createdPCID,
            shiftDetail.createdUserID as  createdUserID,
            shiftDetail.startTime as createdDateTime,
            shiftDetail.createdUserName as createdUserName,
            NULL AS modifiedPCID,
            NULL AS modifiedUserID,
            NULL AS modifiedDateTime,
            null AS modifiedUserName,
            CURRENT_TIMESTAMP() as `timestamp`,
            item.menuSalesItemID as pos_menusalesItemID,
            menusalesmaster.menuSalesID as pos_menusalesID,
            menusalesmaster.shiftID as pos_shiftID,
            1 as isCreditSales
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID AND menusalesmaster.wareHouseAutoID = item.wareHouseAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND menusalesmaster.wareHouseAutoID = shiftDetail.wareHouseID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
            WHERE
                menusalesmaster.menuSalesID = '" . $invoiceID . "'   
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "' 
                AND item.wareHouseAutoID = '" . $outletID . "' 
                AND menusalesmaster.isHold = 0 
                AND menusalesmaster.isVoid = 0 
                AND menusalesmaster.isCreditSales = 1
            GROUP BY
                revenueGLAutoID, menusalesmaster.menuSalesID, item.menuSalesItemID);";

        $result = $this->db->query($q);
        //echo $this->db->last_query();
        return $result;
    }

    /** 2. CREDIT SALES  - COGS Review */
    function update_cogs_generalLedger_credit_sales_review($invoiceID, $isCreditSales = true)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID,
                isCreditSales
            )
            
            (SELECT
                menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                menusalesmaster.documentSystemCode AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - COGS',' - ',menusalesmaster.invoiceCode) AS documentNarration,
                '' AS chequeNumber,
                item.costGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'dr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                  ( sum( IFNULL(item.cost,0) * item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                  ( sum( IFNULL(item.cost,0) *  item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP() AS  `timestamp`,
                item.menuSalesItemID as pos_menusalesItemID,
                menusalesmaster.menuSalesID as pos_menusalesID,
                menusalesmaster.shiftID as pos_shiftID,
                1 as isCreditSales
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.costGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
            WHERE
                menusalesmaster.menuSalesID = '" . $invoiceID . "'  
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND item.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0   
                AND menusalesmaster.isVoid = 0 ";
        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 1 ";
        }
        $q .= " GROUP BY
                item.costGLAutoID, menusalesmaster.menuSalesID, item.menuSalesItemID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 3. CREDIT SALES  - INVENTORY Review */
    function update_inventory_generalLedger_credit_sales_review($invoiceID, $isCreditSales, $shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review(
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID,
                isCreditSales
            )
            (SELECT
                menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                menusalesmaster.documentSystemCode AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - Inventory', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                '' AS chequeNumber,
                item.assetGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                abs(( sum( IFNULL(item.cost,0) *   item.menuSalesQty  )-IFNULL(itmexceed.amount,0) )) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                ( abs( sum( IFNULL(item.cost,0) * item.menuSalesQty    )-IFNULL(itmexceed.amount,0) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                ( abs( sum( IFNULL(item.cost,0) *  item.menuSalesQty    )-IFNULL(itmexceed.amount,0) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                abs(( sum( IFNULL(item.cost,0) *   item.menuSalesQty  )-IFNULL(itmexceed.amount,0) )) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP () `timestamp`,
                item.menuSalesItemID as pos_menusalesItemID,
                menusalesmaster.menuSalesID as pos_menusalesID,
                menusalesmaster.shiftID as pos_shiftID,
                1 as isCreditSales
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
            LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS amount,
		IFNULL(SUM(exceededQty),0) AS excdqty,
		warehouseAutoID,
		assetGLAutoID,
		documentAutoID

	FROM
		srp_erp_itemexceeded
	WHERE
		documentCode = 'POSR'
	AND documentAutoID = '" . $shiftID . "'
	AND warehouseAutoID = '" . $outletID . "'
	GROUP BY
		documentAutoID,
		warehouseAutoID,
		assetGLAutoID
) itmexceed ON menusalesmaster.shiftID = itmexceed.documentAutoID AND item.warehouseAutoID = itmexceed.warehouseAutoID AND item.assetGLAutoID=itmexceed.assetGLAutoID
            WHERE
                menusalesmaster.menuSalesID = '" . $invoiceID . "' 
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND item.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0   
                AND menusalesmaster.isVoid = 0";
        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 1 ";
        }
        $q .= " GROUP BY
                item.assetGLAutoID, menusalesmaster.menuSalesID, item.menuSalesItemID)";

        $result = $this->db->query($q);
        return $result;
    }
    /*function update_inventory_generalLedger_credit_sales_review($invoiceID, $isCreditSales = true, $log = false)
    {
        $companyID = current_companyID();
        $exceedglid = $this->db->query("SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE companyID =$companyID AND controlAccountType = 'IEXC'")->row_array();
        $exceedGlAutoID = $exceedglid['GLAutoID'];
        if ($log) {
            echo '<strong>update_exceededGL_generalLedger_credit_sales</strong><br/>';
        }
        $q = "INSERT INTO srp_erp_generalledger_review(
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID,
                isCreditSales
            )
            (SELECT
	shiftDetail.wareHouseID,
	'CINV' AS documentCode,
	shiftDetail.shiftID AS documentMasterAutoID,
	exceed.documentSystemCode,
	DATE_FORMAT(
		shiftDetail.startTime,
		'%Y-%m-%d'
	) AS documentdate,
	YEAR (curdate()) AS documentYear,
	MONTH (curdate()) AS documentMonth,
	'POS Credit Sales - Exceeded' AS documentNarration,
	'' AS chequeNumber,
	'" . $exceedGlAutoID . "' AS GLAutoID,
	chartOfAccount.systemAccountCode AS systemGLCode,
	chartOfAccount.GLSecondaryCode AS GLCode,
	chartOfAccount.GLDescription AS GLDescription,
	chartOfAccount.subCategory AS GLType,
	'cr' AS amount_type,
	'0' AS isFromItem,
	menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
	menusalesmaster.transactionCurrency AS transactionCurrency,
	'1' AS transactionExchangeRate,
	abs(
		sum(
			(
				IFNULL(exceed.transactionAmount, 0)
			)
		)
	) *- 1 AS transactionAmount,
	currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
	company.company_default_currencyID AS companyLocalCurrencyID,
	company.company_default_currency AS companyLocalCurrency,
	getExchangeRate (
		menusalesmaster.transactionCurrencyID,
		company.company_default_currencyID,
		menusalesmaster.companyID
	) AS companyLocalExchangeRate,
	(
		abs(
			sum(
				(
					IFNULL(exceed.transactionAmount, 0)
				)
			)
		) *- 1
	) / (
		getExchangeRate (
			menusalesmaster.transactionCurrencyID,
			company.company_default_currencyID,
			menusalesmaster.companyID
		)
	) AS companyLocalAmount,
	getDecimalPlaces (
		company.company_default_currencyID
	) AS companyLocalCurrencyDecimalPlaces,
	company.company_reporting_currencyID AS companyReportingCurrencyID,
	company.company_reporting_currency AS companyReportingCurrency,
	getExchangeRate (
		menusalesmaster.transactionCurrencyID,
		company.company_reporting_currencyID,
		menusalesmaster.companyID
	) AS companyReportingExchangeRate,
	(
		abs(
			sum(
				(
					IFNULL(exceed.transactionAmount, 0)
				)
			)
		) *- 1
	) / (
		getExchangeRate (
			menusalesmaster.transactionCurrencyID,
			company.company_reporting_currencyID,
			menusalesmaster.companyID
		)
	) AS companyReportingAmount,
	getDecimalPlaces (
		company.company_reporting_currencyID
	) AS companyReportingCurrencyDecimalPlaces,
	menusalesmaster.segmentID AS segmentID,
	menusalesmaster.segmentCode AS segmentCode,
	menusalesmaster.companyID AS companyID,
	menusalesmaster.companyCode AS companyCode,
	menusalesmaster.createdUserGroup AS createdUserGroup,
	menusalesmaster.createdPCID AS createdPCID,
	menusalesmaster.createdUserID AS createdUserID,
	shiftDetail.startTime AS createdDateTime,
	menusalesmaster.createdUserName AS createdUserName,
	NULL AS modifiedPCID,
	NULL AS modifiedUserID,
	NULL AS modifiedDateTime,
	NULL AS modifiedUserName,
	CURRENT_TIMESTAMP () `timestamp`,
	0 as pos_menusalesItemID,
    menusalesmaster.menuSalesID as pos_menusalesID,
    menusalesmaster.shiftID as pos_shiftID,
    1 as isCreditSales
    FROM
        srp_erp_pos_shiftdetails shiftDetail
    LEFT JOIN srp_erp_itemexceeded exceed ON shiftDetail.shiftID = exceed.documentAutoID
    AND shiftDetail.wareHouseID = exceed.warehouseAutoID
    AND exceed.documentCode = 'POSR'
    LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = '" . $exceedGlAutoID . "'
    LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.shiftID = shiftDetail.shiftID
    AND menusalesmaster.wareHouseAutoID = '" . $this->outletID . "'
    LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
    LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
    WHERE
        menusalesmaster.menuSalesID = '" . $invoiceID . "'
    AND exceed.wareHouseAutoID = '" . $this->outletID . "' ";

        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 1";
        }
        $q .= " HAVING transactionAmount !=0)";
        $result = $this->db->query($q);
        return $result;
    }*/

    /** 4.  CREDIT SALES - TAX Review */
    function update_tax_generalLedger_credit_sales_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID,
                isCreditSales
                                          
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - TAX', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    menusalesTax.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID,
                    1 as isCreditSales
                FROM
                    srp_erp_pos_menusalestaxes menusalesTax
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = menusalesTax.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesTax.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "'
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesTax.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    chartOfAccount.GLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 5.  CREDIT SALES - COMMISSION EXPENSE Review */
    function update_commissionExpense_generalLedger_credit_sales_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
				partyAutoID,
				partySystemCode,
				partyName,
				partyCurrencyID,
				partyCurrency,
				partyExchangeRate,
				partyCurrencyAmount,
				partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID,
                isCreditSales
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Sales Commission', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    customers.expenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        menusalesmaster.deliveryCommissionAmount
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            IFNULL(menusalesmaster.deliveryCommissionAmount,0)
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    sum( menusalesmaster.deliveryCommissionAmount ) / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID,
                    1 as isCreditSales
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.expenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID  AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                    AND ( menusalesmaster.deliveryCommission IS NOT NULL AND menusalesmaster.deliveryCommission <> 0 )
                    AND menusalesmaster.isDelivery = 1 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    customers.expenseGLAutoID, menusalesmaster.menuSalesID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 6.  CREDIT SALES - COMMISSION PAYABLE Review */
    function update_commissionPayable_generalLedger_credit_sales_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                    wareHouseAutoID,
                    documentCode,
                    documentMasterAutoID,
                    documentSystemCode,
                    documentDate,
                    documentYear,
                    documentMonth,
                    documentNarration,
                    GLAutoID,
                    systemGLCode,
                    GLCode,
                    GLDescription,
                    GLType,
                    amount_type,
                    isFromItem,
                    transactionCurrency,
                    transactionExchangeRate,
                    transactionAmount,
                    transactionCurrencyID,
                    transactionCurrencyDecimalPlaces,
                    companyLocalCurrencyID,
                    companyLocalCurrency,
                    companyLocalExchangeRate,
                    companyLocalAmount,
                    companyLocalCurrencyDecimalPlaces,
                    companyReportingCurrencyID,
                    companyReportingCurrency,
                    companyReportingExchangeRate,
                    companyReportingAmount,
                    companyReportingCurrencyDecimalPlaces,
                    partyType,
                    partyAutoID,
                    partySystemCode,
                    partyName,
                    partyCurrencyID,
                    partyCurrency,
                    partyExchangeRate,
                    partyCurrencyAmount,
                    partyCurrencyDecimalPlaces,
                    segmentID,
                    segmentCode,
                    companyID,
                    companyCode,
                    createdUserGroup,
                    createdPCID,
                    createdUserID,
                    createdDateTime,
                    createdUserName,
                    modifiedPCID,
                    modifiedUserID,
                    modifiedDateTime,
                    modifiedUserName,
                    `timestamp`,
                    pos_menusalesItemID,
                    pos_menusalesID,
                    pos_shiftID,
                    isCreditSales
                )( 
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Sales Commission Payable', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    customers.liabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                menusalesmaster.deliveryCommissionAmount
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                               IFNULL( menusalesmaster.deliveryCommissionAmount,0)
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
					customermaster.customerAutoID AS partyAutoID,
					customermaster.customerSystemCode AS partySystemCode,
					customermaster.customerName AS partyName,
					customermaster.customerCurrencyID AS partyCurrencyID,
					customermaster.customerCurrency AS partyCurrency,
					getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
					ABS( sum( menusalesmaster.deliveryCommissionAmount ) ) * - 1  /  ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
					customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID,
                    1 as isCreditSales
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.liabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                    AND (
                        menusalesmaster.deliveryCommission IS NOT NULL
                        AND menusalesmaster.deliveryCommission <> 0
                    )
                    AND menusalesmaster.isDelivery = 1
                    AND menusalesmaster.isOnTimeCommision = 0 AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    customers.liabilityGLAutoID, menusalesmaster.menuSalesID
                );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 7.  CREDIT SALES - ROYALTY PAYABLE Review */
    function update_royaltyPayable_generalLedger_credit_sales_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID,
                isCreditSales
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Royalty Payable', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    franchisemaster.royaltyLiabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
					customermaster.customerAutoID AS partyAutoID,
					customermaster.customerSystemCode AS partySystemCode,
					customermaster.customerName AS partyName,
					customermaster.customerCurrencyID AS partyCurrencyID,
					customermaster.customerCurrency AS partyCurrency,
					getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
					ABS(
                        sum(
                            (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) * - 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
					customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID,
                    1 as isCreditSales
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyLiabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    franchisemaster.royaltyLiabilityGLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 8.  CREDIT SALES - ROYALTY EXPENSES Review */
    function update_royaltyExpenses_generalLedger_credit_sales_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID,
                isCreditSales
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Royalty Expenses', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    franchisemaster.royaltyExpenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        ( IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0) ) * (
                            franchisemaster.royaltyPercentage / 100
                        )
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
					customermaster.customerAutoID AS partyAutoID,
					customermaster.customerSystemCode AS partySystemCode,
					customermaster.customerName AS partyName,
					customermaster.customerCurrencyID AS partyCurrencyID,
					customermaster.customerCurrency AS partyCurrency,
					getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
					sum(
                        ( IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0) ) * (
                            franchisemaster.royaltyPercentage / 100
                        )
                    ) / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
					customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID,
                    1 as isCreditSales
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyExpenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    franchisemaster.royaltyExpenseGLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 9. CREDIT SALES -  SERVICE CHARGE Review */
    function update_serviceCharge_generalLedger_credit_sales_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID,
                isCreditSales                                          
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Service Charge', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    servicecharge.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(
                                servicecharge.serviceChargeAmount,
                                0
                            )
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    abs(
                        sum(
                            ifnull(
                                servicecharge.serviceChargeAmount,
                                0
                            )
                        )
                    ) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID,
                    1 as isCreditSales
                FROM
                    srp_erp_pos_menusalesservicecharge servicecharge
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = servicecharge.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = servicecharge.GLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND servicecharge.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    chartOfAccount.GLAutoID, menusalesmaster.menuSalesID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS Review */
    function update_creditSales_generalLedger_credit_sales_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                subLedgerType,
                subLedgerDesc,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID,
                isCreditSales
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    '' AS chequeNumber,
                    chartOfAccount.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(payments.amount) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    payments.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate (
                        customermaster.customerCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS partyExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            customermaster.customerCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    3 AS subLedgerType,
                    'AR' AS subLedgerDesc,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID createdUserID,
                    shiftDetail.startTime createdDateTime,
                    menusalesmaster.createdUserName createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP() `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID,
                    1 as isCreditSales
                FROM
                    srp_erp_pos_menusalespayments payments
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON payments.menuSalesID = menusalesmaster.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = payments.customerAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customermaster.receivableAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID 
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND payments.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0
                    AND menusalesmaster.isVoid = 0
                    AND payments.paymentConfigMasterID = 7 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    chartOfAccount.GLAutoID,
                    payments.customerAutoID, menusalesmaster.menuSalesID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** 11.  CREDIT SALES - OUTLET TAX Review */
    function update_outlet_tax_generalLedger_credit_sales_review($invoiceID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger_review (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp,
                pos_menusalesItemID,
                pos_menusalesID,
                pos_shiftID,
                isCreditSales
                                          
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - OUTLET TAX', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    menusalesTax.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    0 as pos_menusalesItemID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    menusalesmaster.shiftID as pos_shiftID,
                    1 as isCreditSales
                FROM
                    srp_erp_pos_menusalesoutlettaxes menusalesTax
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = menusalesTax.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesTax.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.menuSalesID = '" . $invoiceID . "'
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesTax.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    chartOfAccount.GLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** CREDIT SALES - ITEM LEDGER Review */
    function update_itemLedger_credit_sales_review($invoiceID, $isCreditSales = true)
    {
        $outletID = get_outletID();
        $curDate = date('Y-m-d');
        $q = "SELECT
                    financeyear.companyFinanceYearID AS companyFinanceYearID,
                CONCAT(financeyear.beginingDate , \" - \" , financeyear.endingDate ) AS companyFinanceYear,
                financeyear.beginingDate AS FYBegin,
                financeyear.endingDate  AS FYEnd,
                financeperiod.dateFrom AS FYPeriodDateFrom,
                financeperiod.dateTo  AS FYPeriodDateTo
                
                FROM
                    srp_erp_companyfinanceyear financeyear
                INNER JOIN  srp_erp_companyfinanceperiod financeperiod  ON financeperiod.companyFinanceYearID = financeyear.companyFinanceYearID
                WHERE
                    financeyear.companyID = '" . current_companyID() . "'
                AND financeyear.isActive = 1
                AND financeyear.beginingDate < '" . $curDate . "'
                AND financeyear.endingDate > '" . $curDate . "'
                AND financeperiod.isActive =1
                AND financeperiod.dateFrom < '" . $curDate . "'
                AND financeperiod.dateTo > '" . $curDate . "'";
        $financeYear = $this->db->query($q)->row_array();

        $companyFinanceYearID = isset($financeYear['companyFinanceYearID']) ? $financeYear['companyFinanceYearID'] : null;
        $companyFinanceYear = isset($financeYear['companyFinanceYear']) ? $financeYear['companyFinanceYear'] : null;
        $FYBegin = isset($financeYear['FYBegin']) ? $financeYear['FYBegin'] : null;
        $FYEnd = isset($financeYear['FYEnd']) ? $financeYear['FYEnd'] : null;
        $FYPeriodDateFrom = isset($financeYear['FYPeriodDateFrom']) ? $financeYear['FYPeriodDateFrom'] : null;
        $FYPeriodDateTo = isset($financeYear['FYPeriodDateTo']) ? $financeYear['FYPeriodDateTo'] : null;


        $q = "INSERT INTO srp_erp_itemledger_review (
                documentID,
                documentAutoID,
                documentCode,
                documentSystemCode,
                documentDate,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                FYPeriodDateFrom,
                FYPeriodDateTo,
                wareHouseAutoID,
                wareHouseCode,
                wareHouseLocation,
                wareHouseDescription,
                itemAutoID,
                itemSystemCode,
                ItemSecondaryCode,
                itemDescription,
                defaultUOMID,
                defaultUOM,
                transactionUOMID,
                transactionUOM,
                transactionQTY,
                convertionRate,
                currentStock,
                PLGLAutoID,
                PLSystemGLCode,
                PLGLCode,
                PLDescription,
                PLType,
                BLGLAutoID,
                BLSystemGLCode,
                BLGLCode,
                BLDescription,
                BLType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalWacAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingWacAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                narration,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`,
                pos_shiftID,
                pos_menusalesID,
                pos_menusalesItemID,
                pos_menusalesItemDetailID,
                isCreditSales
            )(
                SELECT
                    'CINV' AS documentID,
                    menusalesmaster.documentMasterAutoID AS documentAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d')  AS documentDate,
                    '" . $companyFinanceYearID . "' AS companyFinanceYearID,
                    '" . $companyFinanceYear . "' AS companyFinanceYear,
                    '" . $FYBegin . "' AS FYBegin,
                    '" . $FYEnd . "' AS FYEnd,
                    '" . $FYPeriodDateFrom . "' AS FYPeriodDateFrom,
                    '" . $FYPeriodDateTo . "' AS FYPeriodDateTo,
                    warehousemaster.wareHouseAutoID AS wareHouseAutoID,
                    warehousemaster.wareHouseCode AS wareHouseCode,
                    warehousemaster.wareHouseLocation AS wareHouseLocation,
                    warehousemaster.wareHouseDescription AS wareHouseDescription,
                    itemdetail.itemAutoID AS itemAutoID,
                    itemmaster.itemSystemCode AS itemSystemCode,
                    itemmaster.seconeryItemCode AS seconeryItemCode,
                    itemmaster.itemDescription AS itemDescription,
                    itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
                    itemmaster.defaultUnitOfMeasure AS defaultUOM,
                    itemmaster.defaultUnitOfMeasureID AS transactionUOMID,
	                itemmaster.defaultUnitOfMeasure AS transactionUOM,
                    (
		sum(
			(
				itemdetail.qty * itemdetail.menuSalesQty
			) / (
				getUoMConvertion (
					itemdetail.UOMID,
					itemmaster.defaultUnitOfMeasureID,
					menusalesmaster.companyID
				)
			)
		) 
	) *- 1 AS transactionQTY,
                    1 AS convertionRate,
                    itemmaster.currentStock AS currentStock,
                    itemmaster.costGLAutoID AS PLGLAutoID,
                    itemmaster.costSystemGLCode AS PLSystemGLCode,
                    itemmaster.costGLCode AS PLGLCode,
                    itemmaster.costDescription AS PLDescription,
                    itemmaster.costType AS PLType,
                    itemmaster.assteGLAutoID AS BLGLAutoID,
                    itemmaster.assteSystemGLCode AS BLSystemGLCode,
                    itemmaster.assteGLCode AS BLGLCode,
                    itemmaster.assteDescription AS BLDescription,
                    itemmaster.assteType AS BLType,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    (ifnull(sum(itemdetail.cost*itemdetail.menuSalesQty),0)) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                           (ifnull(sum(itemdetail.cost*itemdetail.menuSalesQty),0))
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        ) 
                    )*- 1 AS companyLocalAmount,
                    (itemdetail.cost*itemdetail.menuSalesQty) AS companyLocalWacAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (ifnull(sum(itemdetail.cost*itemdetail.menuSalesQty),0)) 
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        ) 
                    )*-1 AS companyReportingAmount,
                    (itemdetail.cost*itemdetail.menuSalesQty) / getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingWacAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    'POS Sales' AS narration,
                    shiftDetail.createdUserGroup AS createdUserGroup,
                    shiftDetail.createdPCID AS createdPCID,
                    shiftDetail.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    shiftDetail.createdUserName AS createdUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    menusalesmaster.shiftID as pos_shiftID,
                    menusalesmaster.menuSalesID as pos_menusalesID,
                    itemdetail.menuSalesItemID as pos_menusalesItemID,                                        
                    itemdetail.menuSalesItemDetailID as pos_menusalesItemDetailID,
                    1 as isCreditSales
                FROM
                    srp_erp_pos_menusalesitemdetails AS itemdetail
                LEFT JOIN srp_erp_itemmaster AS itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID AND menusalesmaster.wareHouseAutoID = itemdetail.warehouseAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID	
                WHERE
                    menusalesmaster.menusalesID = '" . $invoiceID . "'
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND itemdetail.warehouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 ";
        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 1 ";
        }
        $q .= " AND menusalesmaster.isVoid = 0
                GROUP BY
                    itemdetail.itemAutoID, menusalesmaster.menuSalesID, itemdetail.UOMID
                    HAVING transactionQTY !=0
            )";
        $result = $this->db->query($q);
        return $result;
    }

    function update_itemLedger_review($invoiceID, $isCreditSales = true)
    {
        $outletID = get_outletID();
        $curDate = date('Y-m-d');
        $q = "SELECT
                    financeyear.companyFinanceYearID AS companyFinanceYearID,
                CONCAT(financeyear.beginingDate , \" - \" , financeyear.endingDate ) AS companyFinanceYear,
                financeyear.beginingDate AS FYBegin,
                financeyear.endingDate  AS FYEnd,
                financeperiod.dateFrom AS FYPeriodDateFrom,
                financeperiod.dateTo  AS FYPeriodDateTo
                
                FROM
                    srp_erp_companyfinanceyear financeyear
                INNER JOIN  srp_erp_companyfinanceperiod financeperiod  ON financeperiod.companyFinanceYearID = financeyear.companyFinanceYearID
                WHERE
                    financeyear.companyID = '" . current_companyID() . "'
                AND financeyear.isActive = 1
                AND financeyear.beginingDate < '" . $curDate . "'
                AND financeyear.endingDate > '" . $curDate . "'
                AND financeperiod.isActive =1
                AND financeperiod.dateFrom < '" . $curDate . "'
                AND financeperiod.dateTo > '" . $curDate . "'";
        $financeYear = $this->db->query($q)->row_array();

        $companyFinanceYearID = isset($financeYear['companyFinanceYearID']) ? $financeYear['companyFinanceYearID'] : null;
        $companyFinanceYear = isset($financeYear['companyFinanceYear']) ? $financeYear['companyFinanceYear'] : null;
        $FYBegin = isset($financeYear['FYBegin']) ? $financeYear['FYBegin'] : null;
        $FYEnd = isset($financeYear['FYEnd']) ? $financeYear['FYEnd'] : null;
        $FYPeriodDateFrom = isset($financeYear['FYPeriodDateFrom']) ? $financeYear['FYPeriodDateFrom'] : null;
        $FYPeriodDateTo = isset($financeYear['FYPeriodDateTo']) ? $financeYear['FYPeriodDateTo'] : null;


        $q = "INSERT INTO srp_erp_itemledger_review (
                documentID,
                documentAutoID,
                documentCode,
                documentSystemCode,
                documentDate,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                FYPeriodDateFrom,
                FYPeriodDateTo,
                wareHouseAutoID,
                wareHouseCode,
                wareHouseLocation,
                wareHouseDescription,
                itemAutoID,
                itemSystemCode,
                ItemSecondaryCode,
                itemDescription,
                defaultUOMID,
                defaultUOM,
                transactionUOMID,
                transactionUOM,
                transactionQTY,
                convertionRate,
                currentStock,
                PLGLAutoID,
                PLSystemGLCode,
                PLGLCode,
                PLDescription,
                PLType,
                BLGLAutoID,
                BLSystemGLCode,
                BLGLCode,
                BLDescription,
                BLType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalWacAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingWacAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                narration,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`,
                pos_shiftID,
                pos_menusalesID,
                pos_menusalesItemID,
                pos_menusalesItemDetailID
            )(
                SELECT
                'POSR' AS documentID,
                menusalesmaster.shiftID AS documentAutoID,
                'POSR' AS documentCode,
                concat(
                    'POSR/',
                    warehousemaster.wareHouseCode,
                    '/',
                    menusalesmaster.shiftID
                ) AS documentSystemCode,
                DATE_FORMAT(
                    shiftDetail.startTime,
                    '%Y-%m-%d'
                ) AS documentDate,
                '" . $companyFinanceYearID . "' AS companyFinanceYearID,
                '" . $companyFinanceYear . "' AS companyFinanceYear,
                '" . $FYBegin . "' AS FYBegin,
                '" . $FYEnd . "' AS FYEnd,
                '" . $FYPeriodDateFrom . "' AS FYPeriodDateFrom,
                '" . $FYPeriodDateTo . "' AS FYPeriodDateTo,
                warehousemaster.wareHouseAutoID AS wareHouseAutoID,
                warehousemaster.wareHouseCode AS wareHouseCode,
                warehousemaster.wareHouseLocation AS wareHouseLocation,
                warehousemaster.wareHouseDescription AS wareHouseDescription,
                itemdetail.itemAutoID AS itemAutoID,
                itemmaster.itemSystemCode AS itemSystemCode,
                itemmaster.seconeryItemCode AS seconeryItemCode,
                itemmaster.itemDescription AS itemDescription,
                itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
                itemmaster.defaultUnitOfMeasure AS defaultUOM,
                itemmaster.defaultUnitOfMeasureID AS transactionUOMID,
                itemmaster.defaultUnitOfMeasure AS transactionUOM,
                
                (
                    sum(
                        (
                            itemdetail.qty * itemdetail.menuSalesQty
                        ) / (
                            getUoMConvertion (
                                itemdetail.UOMID,
                                itemmaster.defaultUnitOfMeasureID,
                                menusalesmaster.companyID
                            )
                        )
                ) *- 1 )
                AS transactionQTY,
                1 AS convertionRate,
                itemmaster.currentStock AS currentStock,
                itemmaster.costGLAutoID AS PLGLAutoID,
                itemmaster.costSystemGLCode AS PLSystemGLCode,
                itemmaster.costGLCode AS PLGLCode,
                itemmaster.costDescription AS PLDescription,
                itemmaster.costType AS PLType,
                itemmaster.assteGLAutoID AS BLGLAutoID,
                itemmaster.assteSystemGLCode AS BLSystemGLCode,
                itemmaster.assteGLCode AS BLGLCode,
                itemmaster.assteDescription AS BLDescription,
                itemmaster.assteType AS BLType,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                IF (
                (
                    ifnull(
                        sum(
                            itemdetail.cost * itemdetail.menuSalesQty
                        ),
                        0
                    )
                ) <= 0,
                0,
                (
                    ifnull(
                        sum(
                            itemdetail.cost * itemdetail.menuSalesQty
                        ),
                        0
                    )
                ) *- 1
            ) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate (
                    menusalesmaster.transactionCurrencyID,
                    company.company_default_currencyID,
                    menusalesmaster.companyID
                ) AS companyLocalExchangeRate,
                IF (
                (
                    (
                        ifnull(
                            sum(
                                itemdetail.cost * itemdetail.menuSalesQty
                            ),
                            0
                        )
                    ) / getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    )
                ) <= 0,
                0,
                (
                    (
                        ifnull(
                            sum(
                                itemdetail.cost * itemdetail.menuSalesQty
                            ),
                            0
                        ) 
                    ) / getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    )
                ) *- 1
            ) AS companyLocalAmount,
            (itemdetail.cost*itemdetail.menuSalesQty) AS companyLocalWacAmount,
                getDecimalPlaces (
                    company.company_default_currencyID
                ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate (
                    menusalesmaster.transactionCurrencyID,
                    company.company_reporting_currencyID,
                    menusalesmaster.companyID
                ) AS companyReportingExchangeRate,
                IF (
                (
                    (
                        ifnull(
                            sum(
                                itemdetail.cost * itemdetail.menuSalesQty
                            ),
                            0
                        ) 
                    ) / getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    )
                ) <= 0,
                0,
                (
                    (
                        ifnull(
                            sum(
                                itemdetail.cost * itemdetail.menuSalesQty
                            ),
                            0
                        ) 
                    ) / getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    )
                ) *- 1
            ) AS companyReportingAmount,
            (itemdetail.cost*itemdetail.menuSalesQty) / getExchangeRate (
                    menusalesmaster.transactionCurrencyID,
                    company.company_reporting_currencyID,
                    menusalesmaster.companyID
                ) AS companyReportingWacAmount,
                getDecimalPlaces (
                    company.company_reporting_currencyID
                ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                'POS Sales' AS narration,
                shiftDetail.createdUserGroup AS createdUserGroup,
                shiftDetail.createdPCID AS createdPCID,
                shiftDetail.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                shiftDetail.createdUserName AS createdUserName,
                CURRENT_TIMESTAMP () AS `timestamp`,
                menusalesmaster.shiftID as pos_shiftID,
                menusalesmaster.menuSalesID as pos_menusalesID,
                itemdetail.menuSalesItemID as pos_menusalesItemID,                                        
                itemdetail.menuSalesItemDetailID as pos_menusalesItemDetailID
                    FROM
                        srp_erp_pos_menusalesitemdetails AS itemdetail
                    LEFT JOIN srp_erp_itemmaster AS itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
                    LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
                    AND menusalesmaster.wareHouseAutoID = itemdetail.warehouseAutoID
                    LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                    LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                    LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                    LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                    WHERE
                        menusalesmaster.menusalesID = '" . $invoiceID . "'
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND itemdetail.warehouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0";
        if ($isCreditSales) {
            $q .= "  AND menusalesmaster.isCreditSales = 0 ";
        }

        $q .= " AND menusalesmaster.isVoid = 0  
                GROUP BY
                    itemdetail.itemAutoID
                    HAVING transactionQTY !=0
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** End New GL Entries Review */

    /******* New GL Entries ****/
    /** 1. REVENUE */
    function update_revenue_generalLedger($shiftID)
    {
        $outletID = get_outletID();

        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp
            ) (SELECT
                menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - Revenue' AS documentNarration,
                '' AS chequeNumber,
                item.revenueGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID )  )) AS companyLocalAmount, getDecimalPlaces ( company.company_default_currencyID  ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID as companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID )  )) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                shiftDetail.createdUserGroup AS createdUserGroup,
            shiftDetail.createdPCID as  createdPCID,
            shiftDetail.createdUserID as  createdUserID,
            shiftDetail.startTime as createdDateTime,
            shiftDetail.createdUserName as createdUserName,
            NULL AS modifiedPCID,
            NULL AS modifiedUserID,
            NULL AS modifiedDateTime,
            null AS modifiedUserName,
            CURRENT_TIMESTAMP() as `timestamp`
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID AND menusalesmaster.wareHouseAutoID = item.wareHouseAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND menusalesmaster.wareHouseAutoID = shiftDetail.wareHouseID 
            
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "' AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'  AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
            GROUP BY
                revenueGLAutoID);";


        $result = $this->db->query($q);
        //echo $this->db->last_query();
        return $result;
    }

    /** 2. BANK OR CASH */
    function update_bank_cash_generalLedger($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
                )
                (SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Bank' AS documentNarration,
                    '' AS chequeNumber,
                    payments.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(payments.amount) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID createdUserID,
                    shiftDetail.startTime createdDateTime,
                    menusalesmaster.createdUserName createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () `timestamp`
                FROM
                    srp_erp_pos_menusalespayments payments 
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON payments.menuSalesID = menusalesmaster.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = payments.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID  
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'  
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND payments.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND payments.paymentConfigMasterID!=7 
                    AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    payments.GLCode);";

        $result = $this->db->query($q);
        return $result;
    }

    /** 3. COGS */
    function update_cogs_generalLedger($shiftID, $isCreditSales = true, $log = false)
    {
        $outletID = get_outletID();
        if ($log) {
            echo '<strong>update_cogs_generalLedger</strong><br/>';
        }

        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )
            
            (SELECT
                menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - COGS' AS documentNarration,
                '' AS chequeNumber,
                item.costGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'dr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                  ( sum( IFNULL(item.cost,0) * item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                  ( sum( IFNULL(item.cost,0) *  item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP() AS  `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID  AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.costGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "' 
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND item.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0   
                AND menusalesmaster.isVoid = 0";

        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 0";
        }
        $q .= " GROUP BY
                item.costGLAutoID)";

        $result = $this->db->query($q);
        if ($log) {
            echo $q . '<br/><br/>';

        }
        return $result;
    }


    /**
     * Deduct Item Exceeded
     * @param $shiftID
     * @return mixed
     *
     */
    function itemExceeded_adjustment_generalLedger_cogs($shiftID, $isCreditSales = false)
    {
        if ($isCreditSales) {
            $creditSales = 1;
        } else {
            $creditSales = 0;
        }

        $q = "UPDATE srp_erp_generalledger AS T1, (
                SELECT
                    gl.generalLedgerAutoID,
                IF
                    (
                    sum( IFNULL( gl.transactionAmount, 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) > 0,
                    sum( IFNULL( gl.transactionAmount, 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ),
                    0 
                    ) AS transactionAmount,
                IF
                    (
                    sum( IFNULL( gl.transactionAmount, 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) > 0,
                    sum( IFNULL( gl.transactionAmount, 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) / gl.companyLocalExchangeRate,
                    0 
                    ) AS companyReportingAmount,
                IF
                    (
                    sum( IFNULL( gl.transactionAmount, 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) > 0,
                    sum( IFNULL( gl.transactionAmount, 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) / gl.companyReportingExchangeRate,
                    0 
                    ) AS companyLocalAmount 
                FROM
                    srp_erp_itemexceeded ie
                    LEFT JOIN srp_erp_generalledger gl ON gl.GLAutoID = ie.costGLAutoID
                WHERE
                    ie.documentAutoID = '" . $shiftID . "' 
                    AND gl.documentMasterAutoID =  '" . $shiftID . "'  AND gl.documentCode = 'POSR'
                    AND ie.isFromCreditSales = '" . $creditSales . "'
                GROUP BY
                    ie.costGLAutoID
                    ) AS T2 
                    SET T1.transactionAmount = T2.transactionAmount ,
                    T1.companyLocalAmount = T2.companyLocalAmount,
                    T1.companyReportingAmount = T2.companyReportingAmount
                  WHERE
                    T1.generalLedgerAutoID = T2.generalLedgerAutoID";

        $result = $this->db->query($q);
        return $result;
    }

    function itemExceeded_adjustment_generalLedger_cogs_creditSales($shiftID)
    {


    }

    function itemExceeded_adjustment_generalLedger_inventory($shiftID, $isCreditSales = false)
    {
        if ($isCreditSales) {
            $creditSales = 1;
        } else {
            $creditSales = 0;
        }

        $q = "UPDATE srp_erp_generalledger AS T1, (
                SELECT
                    gl.generalLedgerAutoID,
                IF
                    (
                    sum( IFNULL( abs(gl.transactionAmount), 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) > 0,
                    abs(sum( IFNULL( abs(gl.transactionAmount), 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) )) *-1,
                    0 
                    ) AS transactionAmount,
                IF
                    (
                    sum( IFNULL( abs(gl.transactionAmount), 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) > 0,
                    abs(sum( IFNULL( abs(gl.transactionAmount), 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) / gl.companyLocalExchangeRate) *-1,
                    0 
                    ) AS companyReportingAmount,
                IF
                    (
                    sum( IFNULL( abs(gl.transactionAmount), 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) > 0,
                    abs(sum( IFNULL( abs(gl.transactionAmount), 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) / gl.companyReportingExchangeRate)*-1,
                    0 
                    ) AS companyLocalAmount 
                FROM
                    srp_erp_itemexceeded ie
                    LEFT JOIN srp_erp_generalledger gl ON gl.GLAutoID = ie.assetGLAutoID
                WHERE
                    ie.documentAutoID = '" . $shiftID . "' 
                    AND gl.documentMasterAutoID =  '" . $shiftID . "'  AND gl.documentCode = 'POSR'
                    AND ie.isFromCreditSales = '" . $creditSales . "'
                GROUP BY
                    ie.assetGLAutoID
                    ) AS T2 
                    SET T1.transactionAmount = T2.transactionAmount ,
                    T1.companyLocalAmount = T2.companyLocalAmount
                  WHERE
                    T1.generalLedgerAutoID = T2.generalLedgerAutoID";

        $result = $this->db->query($q);
        return $result;
    }

    /** 4. INVENTORY
     * changes done by mushtaq discussed with Hisham
     * Updated on 2019-01-17 JIRA SME-1279
     */
    function update_inventory_generalLedger($shiftID, $isCreditSales = true, $log = false)
    {
        $outletID = get_outletID();
        if ($log) {
            echo '<strong>update_inventory_generalLedger</strong><br/>';
        }
        $q = "INSERT INTO srp_erp_generalledger(
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp
            )
            (SELECT
                menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - Inventory' AS documentNarration,
                '' AS chequeNumber,
                item.assetGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                abs(( sum(IFNULL(item.cost, 0) * item.menuSalesQty)-IFNULL(itmexceed.amount,0) )) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                ( abs(  sum(IFNULL(item.cost, 0) *item.menuSalesQty)-IFNULL(itmexceed.amount,0)  ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                ( abs( sum(IFNULL(item.cost, 0) * item.menuSalesQty)-IFNULL(itmexceed.amount,0) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP () `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID  AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
            LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS amount,
		IFNULL(SUM(exceededQty),0) AS excdqty,
		warehouseAutoID,
		assetGLAutoID,
		documentAutoID

	FROM
		srp_erp_itemexceeded
	WHERE
		documentCode = 'POSR'
	AND documentAutoID = '" . $shiftID . "'
	AND warehouseAutoID = '" . $outletID . "'
	GROUP BY
		documentAutoID,
		warehouseAutoID,
		assetGLAutoID
) itmexceed ON menusalesmaster.shiftID = itmexceed.documentAutoID AND item.warehouseAutoID = itmexceed.warehouseAutoID AND item.assetGLAutoID=itmexceed.assetGLAutoID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'  
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND item.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0   
                AND menusalesmaster.isVoid = 0";
        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 0";
        }
        $q .= " GROUP BY
                item.assetGLAutoID HAVING transactionAmount !=0)";

        if ($log) {
            echo $q . '<br/><br/>';
        }
        $result = $this->db->query($q);
        return $result;
    }

    /** 5. TAX */
    function update_tax_generalLedger($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`,
                taxMasterAutoID
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - TAX' AS documentNarration,
                    menusalesTax.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`,
                    menusalesTax.taxmasterID as taxmasterID
                FROM
                    srp_erp_pos_menusalestaxes menusalesTax
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = menusalesTax.menuSalesID 
                AND menusalesmaster.wareHouseAutoID = '" . get_outletID() . "'
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesTax.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0 
                AND menusalesmaster.isVoid = 0 
                AND menusalesmaster.isCreditSales = 0 
                AND menusalesTax.wareHouseAutoID = '" . $outletID . "' 
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                GROUP BY
                    chartOfAccount.GLAutoID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 6. COMMISSION EXPENSE  */
    function update_commissionExpense_generalLedger($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Sales Commission' AS documentNarration,
                    customers.expenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        menusalesmaster.deliveryCommissionAmount
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            IFNULL(menusalesmaster.deliveryCommissionAmount,0)
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.expenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'  AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                AND (
                    menusalesmaster.deliveryCommission IS NOT NULL
                    AND menusalesmaster.deliveryCommission <> 0
                )
                AND menusalesmaster.isDelivery = 1 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    customers.expenseGLAutoID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 7. COMMISSION PAYABLE */
    function update_commissionPayable_generalLedger($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                    wareHouseAutoID,
                    documentCode,
                    documentMasterAutoID,
                    documentSystemCode,
                    documentDate,
                    documentYear,
                    documentMonth,
                    documentNarration,
                    GLAutoID,
                    systemGLCode,
                    GLCode,
                    GLDescription,
                    GLType,
                    amount_type,
                    isFromItem,
                    transactionCurrency,
                    transactionExchangeRate,
                    transactionAmount,
                    transactionCurrencyID,
                    transactionCurrencyDecimalPlaces,
                    companyLocalCurrencyID,
                    companyLocalCurrency,
                    companyLocalExchangeRate,
                    companyLocalAmount,
                    companyLocalCurrencyDecimalPlaces,
                    companyReportingCurrencyID,
                    companyReportingCurrency,
                    companyReportingExchangeRate,
                    companyReportingAmount,
                    companyReportingCurrencyDecimalPlaces,
                    segmentID,
                    segmentCode,
                    companyID,
                    companyCode,
                    createdUserGroup,
                    createdPCID,
                    createdUserID,
                    createdDateTime,
                    createdUserName,
                    modifiedPCID,
                    modifiedUserID,
                    modifiedDateTime,
                    modifiedUserName,
                    `timestamp`
                )( 
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Sales Commission Payable' AS documentNarration,
                    customers.liabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                menusalesmaster.deliveryCommissionAmount
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                               IFNULL( menusalesmaster.deliveryCommissionAmount,0)
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.liabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'  AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                AND (
                    menusalesmaster.deliveryCommission IS NOT NULL
                    AND menusalesmaster.deliveryCommission <> 0
                )
                AND menusalesmaster.isDelivery = 1
                AND menusalesmaster.isOnTimeCommision = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    customers.liabilityGLAutoID
                );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 8. ROYALTY PAYABLE */
    function update_royaltyPayable_generalLedger($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Royalty Payable' AS documentNarration,
                    franchisemaster.royaltyLiabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyLiabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    franchisemaster.royaltyLiabilityGLAutoID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 9. ROYALTY EXPENSES */
    function update_royaltyExpenses_generalLedger($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Royalty Expenses' AS documentNarration,
                    franchisemaster.royaltyExpenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        ( IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0) ) * (
                            franchisemaster.royaltyPercentage / 100
                        )
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyExpenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID 
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID

                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    franchisemaster.royaltyExpenseGLAutoID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 10. SERVICE CHARGE */
    function update_serviceCharge_generalLedger($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Sales - Service Charge' AS documentNarration,
                    servicecharge.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(
                                servicecharge.serviceChargeAmount,
                                0
                            )
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesservicecharge servicecharge
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = servicecharge.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = servicecharge.GLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND servicecharge.wareHouseAutoID = '" . $outletID . "' 
                AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 0
                GROUP BY
                    chartOfAccount.GLAutoID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** 11. CREDIT CUSTOMER PAYMENTS */
    function update_creditSales_generalLedger($shiftID)
    {
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                subLedgerType,
                subLedgerDesc,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    'POSR' AS documentCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    'POS Credit Sales' AS documentNarration,
                    '' AS chequeNumber,
                    chartOfAccount.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(payments.amount) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    payments.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate (
                        customermaster.customerCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS partyExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            customermaster.customerCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    3 AS subLedgerType,
                    'AR' AS subLedgerDesc,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID createdUserID,
                    shiftDetail.startTime createdDateTime,
                    menusalesmaster.createdUserName createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP() `timestamp`
                FROM
                    srp_erp_pos_menusalespayments payments
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON payments.menuSalesID = menusalesmaster.menuSalesID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = payments.customerAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customermaster.receivableAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0
                AND menusalesmaster.isVoid = 0
                AND payments.paymentConfigMasterID = 7
                GROUP BY
                    chartOfAccount.GLAutoID,
                    payments.customerAutoID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** BANK LEDGER  */
    function update_bankLedger($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_bankledger (
                wareHouseAutoID,
                documentDate,
                transactionType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                bankCurrencyID,
                bankCurrency,
                bankCurrencyExchangeRate,
                bankCurrencyAmount,
                bankCurrencyDecimalPlaces,
                modeofPayment,
                memo,
                bankName,
                bankGLAutoID,
                bankSystemAccountCode,
                bankGLSecondaryCode,
                documentMasterAutoID,
                documentType,
                documentSystemCode,
                createdPCID,
                companyID,
                companyCode,
                segmentID,
                segmentCode,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timeStamp`
                )
                (SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d')  AS documentDate,
                    '2' AS transactionType,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(payments.amount) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    chartOfAccount.bankCurrencyID AS bankCurrencyID,
                    chartOfAccount.bankCurrencyCode AS bankCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        chartOfAccount.bankCurrencyID,
                        menusalesmaster.companyID
                    ) AS bankCurrencyExchangeRate,
                    (SUM(payments.amount)) /(
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            chartOfAccount.bankCurrencyID,
                            menusalesmaster.companyID
                        )
                    )  AS bankCurrencyAmount,
                    chartOfAccount.bankCurrencyDecimalPlaces AS bankCurrencyDecimalPlaces,
                    '1' AS modeofPayment,
                    'payment collection from POSR' AS memo,
                    chartOfAccount.bankName AS bankName,
                    chartOfAccount.GLAutoID AS bankGLAutoID,
                    chartOfAccount.systemAccountCode AS bankSystemAccountCode,
                    chartOfAccount.GLSecondaryCode AS bankGLSecondaryCode,
                    menusalesmaster.shiftID AS documentMasterAutoID,
                    'RV' AS documentType,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime createdDateTime,
                    menusalesmaster.createdUserName createdUserName,
                    CURRENT_TIMESTAMP () `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_menusalespayments payments ON payments.menuSalesID = menusalesmaster.menuSalesID AND payments.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = payments.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND payments.paymentConfigMasterID!=7 
                    AND payments.paymentConfigMasterID!=25
                GROUP BY
                    payments.GLCode);";

        $result = $this->db->query($q);
        return $result;
    }

    /** ITEM MASTER STOCK UPDATE */
    function update_itemMasterNewStock($shiftID)
    {
        $outletID = get_outletID();
        $q = "SELECT
                    itemmaster.itemAutoID,
                    -- menusalesmaster.wareHouseAutoID,
                    -- itemmaster.currentStock,
                    -- sum(itemdetail.qty*itemdetail.menuSalesQty) as qty,
                    -- itemdetail.UOMID,
                    -- itemmaster.defaultUnitOfMeasure,
                    -- itemdetail.UOM,
                    -- getUoMConvertion (
                    -- 	itemdetail.UOMID,
                    -- 	itemmaster.defaultUnitOfMeasureID,
                    -- 	menusalesmaster.companyID
                    -- ) AS convertionRate,
                    -- SUM(
                    -- 	(itemdetail.qty * item.qty) / (
                    -- 		getUoMConvertion (
                    -- 			itemdetail.UOMID,
                    -- 			itemmaster.defaultUnitOfMeasureID,
                    -- 			menusalesmaster.companyID
                    -- 		)
                    -- 	)
                    -- ) AS usedStock, 
                 
                
                itemmaster.currentStock - SUM(
                        (
                            (
                                itemdetail.qty * itemdetail.menuSalesQty
                            ) / (
                                getUoMConvertion (
                                    itemdetail.UOMID,
                                    itemmaster.defaultUnitOfMeasureID,
                                    menusalesmaster.companyID
                                )
                            )
                        )
                    ) as currentStock
                FROM
                    srp_erp_pos_menusalesitemdetails itemdetail
                INNER JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID 
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID AND menusalesmaster.wareHouseAutoID = itemdetail.warehouseAutoID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'  
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "' 
                    AND itemdetail.warehouseAutoID = '" . $outletID . "' 
                    AND menusalesmaster.isCreditSales = 0
                    AND menusalesmaster.isHold = 0    
                    AND menusalesmaster.isVoid = 0
                GROUP BY
                    itemmaster.itemAutoID";
        $result = $this->db->query($q)->result_array();

        /** New stock information */
        if (!empty($result)) {
            /**update new stock */
            $this->db->update_batch('srp_erp_itemmaster', $result, 'itemAutoID');
        }

        return $result;
    }

    /** WAREHOUSE ITEM MASTER STOCK UPDATE */
    function update_warehouseItemMasterNewStock($shiftID)
    {
        $outletID = get_outletID();
        $q = "SELECT
                warehouseitem.warehouseItemsAutoID,
                -- itemdetail.itemAutoID,
             
            warehouseitem.currentStock - SUM(
                    (
                        (itemdetail.qty * itemdetail.menuSalesQty) / (
                            getUoMConvertion (
                                itemdetail.UOMID,
                                itemmaster.defaultUnitOfMeasureID,
                                menusalesmaster.companyID
                            )
                        )
                    )
                ) as currentStock 
            FROM
                srp_erp_pos_menusalesitemdetails itemdetail
            INNER JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
            LEFT JOIN srp_erp_warehouseitems warehouseitem ON warehouseitem.itemAutoID = itemmaster.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID AND menusalesmaster.wareHouseAutoID = itemdetail.warehouseAutoID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "' 
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND itemdetail.warehouseAutoID = '" . $outletID . "'
                AND menusalesmaster.wareHouseAutoID = warehouseitem.wareHouseAutoID 
                AND menusalesmaster.isCreditSales = 0
                AND menusalesmaster.isHold = 0    
                AND menusalesmaster.isVoid = 0
            GROUP BY
                itemmaster.itemAutoID,
                menusalesmaster.wareHouseAutoID;";
        $result = $this->db->query($q)->result_array();

        /** New stock information */
        if (!empty($result)) {
            /**update new stock */
            $this->db->update_batch('srp_erp_warehouseitems', $result, 'warehouseItemsAutoID');
        }

        return $result;
    }

    /** ITEM LEDGER
     * changes done by mushtaq discussed with Hisham
     * Updated on 2019-01-17 JIRA SME-1279
     */
    function update_itemLedger($shiftID, $isCreditSales = true)
    {
        $outletID = get_outletID();

        $curDate = date('Y-m-d');
        $q = "SELECT
                    financeyear.companyFinanceYearID AS companyFinanceYearID,
                CONCAT(financeyear.beginingDate , \" - \" , financeyear.endingDate ) AS companyFinanceYear,
                financeyear.beginingDate AS FYBegin,
                financeyear.endingDate  AS FYEnd,
                financeperiod.dateFrom AS FYPeriodDateFrom,
                financeperiod.dateTo  AS FYPeriodDateTo
                
                FROM
                    srp_erp_companyfinanceyear financeyear
                INNER JOIN  srp_erp_companyfinanceperiod financeperiod  ON financeperiod.companyFinanceYearID = financeyear.companyFinanceYearID
                WHERE
                    financeyear.companyID = '" . current_companyID() . "'
                AND financeyear.isActive = 1
                AND financeyear.beginingDate < '" . $curDate . "'
                AND financeyear.endingDate > '" . $curDate . "'
                AND financeperiod.isActive =1
                AND financeperiod.dateFrom < '" . $curDate . "'
                AND financeperiod.dateTo > '" . $curDate . "'";
        $financeYear = $this->db->query($q)->row_array();

        $companyFinanceYearID = isset($financeYear['companyFinanceYearID']) ? $financeYear['companyFinanceYearID'] : null;
        $companyFinanceYear = isset($financeYear['companyFinanceYear']) ? $financeYear['companyFinanceYear'] : null;
        $FYBegin = isset($financeYear['FYBegin']) ? $financeYear['FYBegin'] : null;
        $FYEnd = isset($financeYear['FYEnd']) ? $financeYear['FYEnd'] : null;
        $FYPeriodDateFrom = isset($financeYear['FYPeriodDateFrom']) ? $financeYear['FYPeriodDateFrom'] : null;
        $FYPeriodDateTo = isset($financeYear['FYPeriodDateTo']) ? $financeYear['FYPeriodDateTo'] : null;


        $q = "INSERT INTO srp_erp_itemledger (
                documentID,
                documentAutoID,
                documentCode,
                documentSystemCode,
                documentDate,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                FYPeriodDateFrom,
                FYPeriodDateTo,
                wareHouseAutoID,
                wareHouseCode,
                wareHouseLocation,
                wareHouseDescription,
                itemAutoID,
                itemSystemCode,
                ItemSecondaryCode,
                itemDescription,
                defaultUOMID,
                defaultUOM,
                transactionUOMID,
                transactionUOM,
                transactionQTY,
                convertionRate,
                currentStock,
                PLGLAutoID,
                PLSystemGLCode,
                PLGLCode,
                PLDescription,
                PLType,
                BLGLAutoID,
                BLSystemGLCode,
                BLGLCode,
                BLDescription,
                BLType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalWacAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingWacAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                narration,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`
            )(
                SELECT
	'POSR' AS documentID,
	menusalesmaster.shiftID AS documentAutoID,
	'POSR' AS documentCode,
	concat(
		'POSR/',
		warehousemaster.wareHouseCode,
		'/',
		menusalesmaster.shiftID
	) AS documentSystemCode,
	DATE_FORMAT(
		shiftDetail.startTime,
		'%Y-%m-%d'
	) AS documentDate,
	'" . $companyFinanceYearID . "' AS companyFinanceYearID,
	'" . $companyFinanceYear . "' AS companyFinanceYear,
	'" . $FYBegin . "' AS FYBegin,
	'" . $FYEnd . "' AS FYEnd,
	'" . $FYPeriodDateFrom . "' AS FYPeriodDateFrom,
	'" . $FYPeriodDateTo . "' AS FYPeriodDateTo,
	warehousemaster.wareHouseAutoID AS wareHouseAutoID,
	warehousemaster.wareHouseCode AS wareHouseCode,
	warehousemaster.wareHouseLocation AS wareHouseLocation,
	warehousemaster.wareHouseDescription AS wareHouseDescription,
	itemdetail.itemAutoID AS itemAutoID,
	itemmaster.itemSystemCode AS itemSystemCode,
	itemmaster.seconeryItemCode AS seconeryItemCode,
	itemmaster.itemDescription AS itemDescription,
	itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
	itemmaster.defaultUnitOfMeasure AS defaultUOM,
	itemmaster.defaultUnitOfMeasureID AS transactionUOMID,
	itemmaster.defaultUnitOfMeasure AS transactionUOM,
	IF (
	(
		sum(
			(
				itemdetail.qty * itemdetail.menuSalesQty
			) / (
				getUoMConvertion (
					itemdetail.UOMID,
					itemmaster.defaultUnitOfMeasureID,
					menusalesmaster.companyID
				)
			)
		)
	) < itmlegr.itemLegQty,
	(
		sum(
			(
				itemdetail.qty * itemdetail.menuSalesQty
			) / (
				getUoMConvertion (
					itemdetail.UOMID,
					itemmaster.defaultUnitOfMeasureID,
					menusalesmaster.companyID
				)
			)
		)
	) *- 1,
	itmlegr.itemLegQty *- 1
) AS transactionQTY,
	1 AS convertionRate,
	itemmaster.currentStock AS currentStock,
	itemmaster.costGLAutoID AS PLGLAutoID,
	itemmaster.costSystemGLCode AS PLSystemGLCode,
	itemmaster.costGLCode AS PLGLCode,
	itemmaster.costDescription AS PLDescription,
	itemmaster.costType AS PLType,
	itemmaster.assteGLAutoID AS BLGLAutoID,
	itemmaster.assteSystemGLCode AS BLSystemGLCode,
	itemmaster.assteGLCode AS BLGLCode,
	itemmaster.assteDescription AS BLDescription,
	itemmaster.assteType AS BLType,
	menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
	menusalesmaster.transactionCurrency AS transactionCurrency,
	'1' AS transactionExchangeRate,
	IF (
	(
		ifnull(
			sum(
				itemdetail.cost * itemdetail.menuSalesQty
			),
			0
		) - IFNULL(itmexceed.amount, 0)
	) <= 0,
	0,
	(
		ifnull(
			sum(
				itemdetail.cost * itemdetail.menuSalesQty
			),
			0
		) - IFNULL(itmexceed.amount, 0)
	) *- 1
) AS transactionAmount,
	currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
	company.company_default_currencyID AS companyLocalCurrencyID,
	company.company_default_currency AS companyLocalCurrency,
	getExchangeRate (
		menusalesmaster.transactionCurrencyID,
		company.company_default_currencyID,
		menusalesmaster.companyID
	) AS companyLocalExchangeRate,
	IF (
	(
		(
			ifnull(
				sum(
					itemdetail.cost * itemdetail.menuSalesQty
				),
				0
			) - IFNULL(itmexceed.amount, 0)
		) / getExchangeRate (
			menusalesmaster.transactionCurrencyID,
			company.company_default_currencyID,
			menusalesmaster.companyID
		)
	) <= 0,
	0,
	(
		(
			ifnull(
				sum(
					itemdetail.cost * itemdetail.menuSalesQty
				),
				0
			) - IFNULL(itmexceed.amount, 0)
		) / getExchangeRate (
			menusalesmaster.transactionCurrencyID,
			company.company_default_currencyID,
			menusalesmaster.companyID
		)
	) *- 1
) AS companyLocalAmount,
	itemmaster.companyLocalWacAmount AS companyLocalWacAmount,
	getDecimalPlaces (
		company.company_default_currencyID
	) AS companyLocalCurrencyDecimalPlaces,
	company.company_reporting_currencyID AS companyReportingCurrencyID,
	company.company_reporting_currency AS companyReportingCurrency,
	getExchangeRate (
		menusalesmaster.transactionCurrencyID,
		company.company_reporting_currencyID,
		menusalesmaster.companyID
	) AS companyReportingExchangeRate,
	IF (
	(
		(
			ifnull(
				sum(
					itemdetail.cost * itemdetail.menuSalesQty
				),
				0
			) - IFNULL(itmexceed.amount, 0)
		) / getExchangeRate (
			menusalesmaster.transactionCurrencyID,
			company.company_reporting_currencyID,
			menusalesmaster.companyID
		)
	) <= 0,
	0,
	(
		(
			ifnull(
				sum(
					itemdetail.cost * itemdetail.menuSalesQty
				),
				0
			) - IFNULL(itmexceed.amount, 0)
		) / getExchangeRate (
			menusalesmaster.transactionCurrencyID,
			company.company_reporting_currencyID,
			menusalesmaster.companyID
		)
	) *- 1
) AS companyReportingAmount,
	itemmaster.companyLocalWacAmount / getExchangeRate (
		menusalesmaster.transactionCurrencyID,
		company.company_reporting_currencyID,
		menusalesmaster.companyID
	) AS companyReportingWacAmount,
	getDecimalPlaces (
		company.company_reporting_currencyID
	) AS companyReportingCurrencyDecimalPlaces,
	menusalesmaster.segmentID AS segmentID,
	menusalesmaster.segmentCode AS segmentCode,
	menusalesmaster.companyID AS companyID,
	menusalesmaster.companyCode AS companyCode,
	'POS Sales' AS narration,
	shiftDetail.createdUserGroup AS createdUserGroup,
	shiftDetail.createdPCID AS createdPCID,
	shiftDetail.createdUserID AS createdUserID,
	shiftDetail.startTime AS createdDateTime,
	shiftDetail.createdUserName AS createdUserName,
	CURRENT_TIMESTAMP () AS `timestamp`
FROM
	srp_erp_pos_menusalesitemdetails AS itemdetail
LEFT JOIN srp_erp_itemmaster AS itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
AND menusalesmaster.wareHouseAutoID = itemdetail.warehouseAutoID
LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
LEFT JOIN (
	SELECT

	IF (
		SUM(
			transactionQTY / convertionRate
		) < 0,
		0,
		SUM(
			transactionQTY / convertionRate
		)
	) AS itemLegQty,
	wareHouseAutoID,
	itemAutoID
FROM
	srp_erp_itemledger
WHERE
	wareHouseAutoID = '" . $outletID . "'
GROUP BY
	itemAutoID,
	wareHouseAutoID
) itmlegr ON itmlegr.itemAutoID = itemdetail.itemAutoID
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS amount,
		IFNULL(SUM(exceededQty), 0) AS excdqty,
		warehouseAutoID,
		assetGLAutoID,
		documentAutoID,
		itemAutoID
	FROM
		srp_erp_itemexceeded
	WHERE
		documentCode = 'POSR'
	AND documentAutoID = '" . $shiftID . "'
	AND warehouseAutoID = '" . $outletID . "'
	GROUP BY
		documentAutoID,
		warehouseAutoID,
		itemAutoID
) itmexceed ON menusalesmaster.shiftID = itmexceed.documentAutoID
AND itemdetail.warehouseAutoID = itmexceed.warehouseAutoID
AND itemdetail.itemAutoID = itmexceed.itemAutoID
WHERE
	menusalesmaster.shiftID = '" . $shiftID . "'
AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
AND itemdetail.warehouseAutoID = '" . $outletID . "'
AND menusalesmaster.isHold = 0";
        if ($isCreditSales) {
            $q .= "  AND menusalesmaster.isCreditSales = 0 ";
        }

        $q .= " AND menusalesmaster.isVoid = 0  
                GROUP BY
                    itemdetail.itemAutoID
                    HAVING transactionQTY !=0
            )";
        $result = $this->db->query($q);
        return $result;
    }


    /** ITEM LEDGER - item exceeded  */
    function update_itemLedger_itemExceeded($shiftID, $isCreditSales = true)
    {

        $curDate = date('Y-m-d');
        $q = "SELECT
                    financeyear.companyFinanceYearID AS companyFinanceYearID,
                CONCAT(financeyear.beginingDate , \" - \" , financeyear.endingDate ) AS companyFinanceYear,
                financeyear.beginingDate AS FYBegin,
                financeyear.endingDate  AS FYEnd,
                financeperiod.dateFrom AS FYPeriodDateFrom,
                financeperiod.dateTo  AS FYPeriodDateTo
                
                FROM
                    srp_erp_companyfinanceyear financeyear
                INNER JOIN  srp_erp_companyfinanceperiod financeperiod  ON financeperiod.companyFinanceYearID = financeyear.companyFinanceYearID
                WHERE
                    financeyear.companyID = '" . current_companyID() . "'
                AND financeyear.isActive = 1
                AND financeyear.beginingDate < '" . $curDate . "'
                AND financeyear.endingDate > '" . $curDate . "'
                AND financeperiod.isActive =1
                AND financeperiod.dateFrom < '" . $curDate . "'
                AND financeperiod.dateTo > '" . $curDate . "'";
        $financeYear = $this->db->query($q)->row_array();

        $companyFinanceYearID = isset($financeYear['companyFinanceYearID']) ? $financeYear['companyFinanceYearID'] : null;
        $companyFinanceYear = isset($financeYear['companyFinanceYear']) ? $financeYear['companyFinanceYear'] : null;
        $FYBegin = isset($financeYear['FYBegin']) ? $financeYear['FYBegin'] : null;
        $FYEnd = isset($financeYear['FYEnd']) ? $financeYear['FYEnd'] : null;
        $FYPeriodDateFrom = isset($financeYear['FYPeriodDateFrom']) ? $financeYear['FYPeriodDateFrom'] : null;
        $FYPeriodDateTo = isset($financeYear['FYPeriodDateTo']) ? $financeYear['FYPeriodDateTo'] : null;


        $q = "INSERT INTO srp_erp_itemledger (
                documentID,
                documentAutoID,
                documentCode,
                documentSystemCode,
                documentDate,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                FYPeriodDateFrom,
                FYPeriodDateTo,
                wareHouseAutoID,
                wareHouseCode,
                wareHouseLocation,
                wareHouseDescription,
                itemAutoID,
                itemSystemCode,
                ItemSecondaryCode,
                itemDescription,
                defaultUOMID,
                defaultUOM,
                transactionUOMID,
                transactionUOM,
                transactionQTY,
                convertionRate,
                currentStock,
                PLGLAutoID,
                PLSystemGLCode,
                PLGLCode,
                PLDescription,
                PLType,
                BLGLAutoID,
                BLSystemGLCode,
                BLGLCode,
                BLDescription,
                BLType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalWacAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingWacAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                narration,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`
            )(
                SELECT
                    'POSR' AS documentID,
                    menusalesmaster.shiftID AS documentAutoID,
                    'POSR' AS documentCode,
                    concat(
                        'POSR/',
                        warehousemaster.wareHouseCode,
                        '/',
                        menusalesmaster.shiftID
                    ) AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d')  AS documentDate,
                    '" . $companyFinanceYearID . "' AS companyFinanceYearID,
                    '" . $companyFinanceYear . "' AS companyFinanceYear,
                    '" . $FYBegin . "' AS FYBegin,
                    '" . $FYEnd . "' AS FYEnd,
                    '" . $FYPeriodDateFrom . "' AS FYPeriodDateFrom,
                    '" . $FYPeriodDateTo . "' AS FYPeriodDateTo,
                    warehousemaster.wareHouseAutoID AS wareHouseAutoID,
                    warehousemaster.wareHouseCode AS wareHouseCode,
                    warehousemaster.wareHouseLocation AS wareHouseLocation,
                    warehousemaster.wareHouseDescription AS wareHouseDescription,
                    itemdetail.itemAutoID AS itemAutoID,
                    itemmaster.itemSystemCode AS itemSystemCode,
                    itemmaster.seconeryItemCode AS seconeryItemCode,
                    itemmaster.itemDescription AS itemDescription,
                    itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
                    itemmaster.defaultUnitOfMeasure AS defaultUOM,
                    itemdetail.UOMID AS transactionUOMID,
                    itemdetail.UOM AS transactionUOM,
                    ( sum(  IFNULL(itemdetail.qty,0)  *  IFNULL(itemdetail.menuSalesQty,0) ) - sum( IFNULL(ie.exceededQty,0) )  )  *- 1 AS transactionQTY,
                    getUoMConvertion (
                        itemdetail.UOMID,
                        itemmaster.defaultUnitOfMeasureID,
                        menusalesmaster.companyID
                    ) AS convertionRate,
                    itemmaster.currentStock AS currentStock,
                    itemmaster.costGLAutoID AS PLGLAutoID,
                    itemmaster.costSystemGLCode AS PLSystemGLCode,
                    itemmaster.costGLCode AS PLGLCode,
                    itemmaster.costDescription AS PLDescription,
                    itemmaster.costType AS PLType,
                    itemmaster.assteGLAutoID AS BLGLAutoID,
                    itemmaster.assteSystemGLCode AS BLSystemGLCode,
                    itemmaster.assteGLCode AS BLGLCode,
                    itemmaster.assteDescription AS BLDescription,
                    itemmaster.assteType AS BLType,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        ifnull(
                            itemdetail.cost *  
                                ( sum(  IFNULL(itemdetail.qty,0)  *  IFNULL(itemdetail.menuSalesQty,0) ) - sum( IFNULL(ie.exceededQty,0) )  )   ,
                            0
                        ) *- 1
                    ) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            sum(
                                ifnull(
                                    itemdetail.cost * (
                                         itemdetail.menuSalesQty
                                    ),
                                    0
                                )
                            )
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        ) *- 1
                    ) AS companyLocalAmount,
                    itemmaster.companyLocalWacAmount AS companyLocalWacAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            ifnull(
                                itemdetail.cost * (
                                    itemdetail.menuSalesQty
                                ),
                                0
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        ) *- 1
                    ) AS companyReportingAmount,
                    itemmaster.companyLocalWacAmount / getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingWacAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    'POS Sales' AS narration,
                    shiftDetail.createdUserGroup AS createdUserGroup,
                    shiftDetail.createdPCID AS createdPCID,
                    shiftDetail.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    shiftDetail.createdUserName AS createdUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesitemdetails AS itemdetail
                LEFT JOIN srp_erp_itemmaster AS itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
                LEFT JOIN srp_erp_itemexceeded ie ON ie.documentAutoID = '" . $shiftID . "' AND ie.documentCode = 'POSR' AND ie.itemAutoID =  itemdetail.itemAutoID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.isHold = 0";
        if ($isCreditSales) {
            $q .= "  AND menusalesmaster.isCreditSales = 0 ";
        }

        $q .= " AND menusalesmaster.isVoid = 0  
                GROUP BY
                    itemdetail.itemAutoID
            )";

        $result = $this->db->query($q);
        return $result;
    }


    /**
     * ITEM LEDGER - item exceeded
     * @param $shiftID
     * @return mixed
     */

    function itemExceeded_adjustment_itemLedger($shiftID)
    {
        $q = "UPDATE srp_erp_itemledger AS T1,
                (
                SELECT
                    il.itemLedgerAutoID,
                    ABS(
                    sum( abs( (IFNULL( il.transactionQTY, 0 ) /il.convertionRate ) ) ) - SUM( IFNULL( ie.balanceQty, 0 ) ) 
                    ) * - 1 AS transactionQTY,
                IF
                    (
                    sum( IFNULL( abs( il.transactionAmount ), 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) > 0,
                    abs(
                    sum( IFNULL( abs( il.transactionAmount ), 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) 
                    ) * - 1,
                    0 
                    ) AS transactionAmount,
                IF
                    (
                    sum( IFNULL( abs( il.transactionAmount ), 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) > 0,
                    abs(
                    (
                    sum( IFNULL( abs( il.transactionAmount ), 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) 
                    ) / il.companyReportingExchangeRate 
                    ) * - 1,
                    0 
                    ) AS companyReportingAmount,
                IF
                    (
                    sum( IFNULL( abs( il.transactionAmount ), 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) > 0,
                    abs(
                    (
                    sum( IFNULL( abs( il.transactionAmount ), 0 ) ) - SUM( IFNULL( ie.transactionAmount, 0 ) ) 
                    ) / il.companyLocalExchangeRate 
                    ) * - 1,
                    0 
                    ) AS companyLocalAmount 
                FROM
                    srp_erp_itemexceeded ie
                    LEFT JOIN srp_erp_itemledger il ON ie.itemAutoID = il.itemAutoID  AND ie.unitOfMeasureID = il.transactionUOMID
                WHERE
                    il.documentAutoID = '" . $shiftID . "' 
                    AND ie.documentAutoID = '" . $shiftID . "'
                GROUP BY
                    ie.itemAutoID,
                    il.itemLedgerAutoID 
                    ) AS T2 
                    SET T1.transactionQTY = T2.transactionQTY,
                    T1.transactionAmount = T2.transactionAmount,
                    T1.companyReportingAmount = T2.companyReportingAmount,
                    T1.companyLocalAmount = T2.companyLocalAmount,
                    T1.is_sync = 0
                WHERE
                    T1.itemLedgerAutoID = T2.itemLedgerAutoID ";

        $result = $this->db->query($q);
        return $result;

    }

    /** Credit Sales Entries including double entries */
    function pos_credit_sales_entries($shiftID)
    {

        /** 0. CUSTOMER INVOICE - Credit Sales Entries  */
        $this->pos_generate_invoices($shiftID);

        /** 1. CREDIT SALES  - REVENUE */
        $this->update_revenue_generalLedger_credit_sales($shiftID);

        /** 2. CREDIT SALES  - COGS */
        $this->update_cogs_generalLedger_credit_sales($shiftID);
        /** 3. CREDIT SALES  - INVENTORY */
        $this->update_inventory_generalLedger_credit_sales($shiftID);
        /** 4.  CREDIT SALES - TAX */
        $this->update_tax_generalLedger_credit_sales($shiftID);
        /** 5.  CREDIT SALES - COMMISSION EXPENSE  */
        $this->update_commissionExpense_generalLedger_credit_sales($shiftID);
        /** 6.  CREDIT SALES - COMMISSION PAYABLE */
        $this->update_commissionPayable_generalLedger_credit_sales($shiftID);
        /** 7.  CREDIT SALES - ROYALTY PAYABLE */
        $this->update_royaltyPayable_generalLedger_credit_sales($shiftID);
        /** 8.  CREDIT SALES - ROYALTY EXPENSES */
        $this->update_royaltyExpenses_generalLedger_credit_sales($shiftID);
        /** 9. CREDIT SALES -  SERVICE CHARGE */
        $this->update_serviceCharge_generalLedger_credit_sales($shiftID);
        /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS */
        $this->update_creditSales_generalLedger_credit_sales($shiftID);
        /** CREDIT SALES - ITEM MASTER STOCK UPDATE */
        $this->update_itemMasterNewStock_credit_sales($shiftID);
        /** CREDIT SALES - WAREHOUSE ITEM MASTER STOCK UPDATE */
        $this->update_warehouseItemMasterNewStock_credit_sales($shiftID);
        /** CREDIT SALES - ITEM LEDGER  */
        $this->update_itemLedger_credit_sales($shiftID);
    }

    /** 0. CUSTOMER INVOICE - Credit Sales Entries
     * @param $shiftID
     * @param int $menuSalesID
     * @return array
     */
    function pos_generate_invoices($shiftID, $menuSalesID = 0)
    {
        $outletID = get_outletID();
        /** Create Invoice Header */
        $this->load->library('sequence');

        $q = "INSERT INTO srp_erp_customerinvoicemaster (
                wareHouseAutoID,
                invoiceType,
                documentID,
                posMasterAutoID,
                invoiceDate,
                invoiceDueDate,
                customerInvoiceDate,
                invoiceCode,
                referenceNo,
                invoiceNarration,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                companyFinancePeriodID,
                customerID,
                customerSystemCode,
                customerName,
                customerReceivableAutoID,
                customerReceivableSystemGLCode,
                customerReceivableGLAccount,
                customerReceivableDescription,
                customerReceivableType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                customerCurrencyID,
                customerCurrency,
                customerCurrencyExchangeRate,
                customerCurrencyAmount,
                customerCurrencyDecimalPlaces,
                confirmedYN,
                confirmedByEmpID,
                confirmedByName,
                confirmedDate,
                approvedYN,
                approvedDate,
                approvedbyEmpID,
                approvedbyEmpName,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp` 
                ) (
                SELECT
                    srp_erp_pos_shiftdetails.wareHouseID as wareHouseID,
                    'Direct' AS invoiceType,
                    'CINV' AS documentID,
                    $menuSalesID AS documentID,
                    IF(srp_erp_pos_menusalesmaster.isCreditSales=1,DATE_FORMAT( srp_erp_pos_menusalesmaster.menuSalesDate, \"%Y-%m-%d\" ),DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" )) AS invoiceDate,
                    DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" ) AS invoiceDueDate,
                    DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" ) AS customerInvoiceDate,
                    0 AS invoiceCode,
                    srp_erp_pos_menusalesmaster.invoiceCode AS referenceNo,
                    concat( 'POS Credit Sales - ', srp_erp_pos_menusalesmaster.invoiceCode ) AS invoiceNarration,
                    getCompanyFinanceYearID ( srp_erp_pos_shiftdetails.companyID ) AS companyFinanceYearID,
                    concat( FY.beginingDate, ' - ', FY.endingDate ) AS companyFinanceYear,
                    FY.beginingDate AS FYBegin,
                    FY.endingDate AS FYEnd,
                    getCompanyFinancePeriodID ( srp_erp_pos_shiftdetails.companyID ) AS companyFinancePeriodID,
                    srp_erp_pos_menusalespayments.customerAutoID AS customerID,
                    srp_erp_customermaster.customerSystemCode AS customerSystemCode,
                    srp_erp_customermaster.customerName AS customerName,
                    srp_erp_customermaster.receivableAutoID AS customerReceivableAutoID,
                    srp_erp_customermaster.receivableSystemGLCode AS customerReceivableSystemGLCode,
                    srp_erp_customermaster.receivableGLAccount AS customerReceivableGLAccount,
                    srp_erp_customermaster.receivableDescription AS customerReceivableDescription,
                    srp_erp_customermaster.receivableType AS customerReceivableType,
                    srp_erp_pos_menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    srp_erp_pos_menusalesmaster.transactionCurrency AS transactionCurrency,
                    srp_erp_pos_menusalesmaster.transactionExchangeRate AS transactionExchangeRate,
                    Sum( srp_erp_pos_menusalespayments.amount ) AS transactionAmount,
                    srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.companyLocalCurrencyID,
                    srp_erp_pos_menusalesmaster.companyLocalCurrency,
                    srp_erp_pos_menusalesmaster.companyLocalExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.companyLocalExchangeRate ) AS companyLocalAmount,
                    srp_erp_pos_menusalesmaster.companyLocalCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.companyReportingCurrencyID,
                    srp_erp_pos_menusalesmaster.companyReportingCurrency,
                    srp_erp_pos_menusalesmaster.companyReportingExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.companyReportingExchangeRate ) AS companyReportingAmount,
                    srp_erp_pos_menusalesmaster.companyReportingCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.customerCurrencyID,
                    srp_erp_pos_menusalesmaster.customerCurrency,
                    srp_erp_pos_menusalesmaster.customerCurrencyExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.customerCurrencyExchangeRate ) AS customerCurrencyAmount,
                    srp_erp_pos_menusalesmaster.customerCurrencyDecimalPlaces,
                    1 AS confirmedYN,
                    srp_erp_pos_shiftdetails.createdUserID AS confirmedByEmpID,
                    srp_erp_pos_shiftdetails.createdUserName AS confirmedByName,
                    srp_erp_pos_shiftdetails.startTime AS confirmedDate,
                    1 AS approvedYN,
                    srp_erp_pos_shiftdetails.startTime AS approvedDate,
                    srp_erp_pos_shiftdetails.createdUserID AS approvedbyEmpID,
                    srp_erp_pos_shiftdetails.createdUserName AS approvedbyEmpName,
                    srp_erp_pos_menusalesmaster.segmentID AS segmentID,
                    srp_erp_pos_menusalesmaster.segmentCode AS segmentCode,
                    srp_erp_pos_menusalesmaster.companyID,
                    srp_erp_pos_menusalesmaster.companyCode,
                    srp_erp_pos_shiftdetails.createdUserGroup,
                    srp_erp_pos_shiftdetails.createdPCID,
                    srp_erp_pos_shiftdetails.createdUserID,
                    srp_erp_pos_shiftdetails.startTime AS createdDateTime,
                    srp_erp_pos_shiftdetails.createdUserName,
                    srp_erp_pos_shiftdetails.`timestamp` AS `timestamp` 
                FROM
                    srp_erp_pos_menusalesmaster
                    LEFT JOIN srp_erp_pos_menusalespayments 
                    ON srp_erp_pos_menusalespayments.menuSalesID = srp_erp_pos_menusalesmaster.menuSalesID
                    AND srp_erp_pos_menusalespayments.wareHouseAutoID=srp_erp_pos_menusalesmaster.wareHouseAutoID
                    LEFT JOIN srp_erp_pos_shiftdetails 
                    ON srp_erp_pos_menusalesmaster.shiftID = srp_erp_pos_shiftdetails.shiftID
                    AND srp_erp_pos_menusalespayments.wareHouseAutoID=srp_erp_pos_shiftdetails.wareHouseID
                    LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID
                    LEFT JOIN srp_erp_companyfinanceyear FY ON FY.companyFinanceYearID = getCompanyFinanceYearID ( srp_erp_pos_shiftdetails.companyID ) 
                WHERE
                    srp_erp_pos_menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND srp_erp_pos_menusalesmaster.menuSalesID = '" . $menuSalesID . "'
                    AND srp_erp_pos_menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND srp_erp_pos_menusalesmaster.isCreditSales = 1 
                    AND srp_erp_pos_menusalespayments.paymentConfigMasterID = 7 
                    AND srp_erp_pos_menusalesmaster.isVoid = 0
                    AND srp_erp_pos_menusalesmaster.isHold = 0
                    
                GROUP BY
                    srp_erp_pos_menusalesmaster.menuSalesID 
                ORDER BY
                    srp_erp_pos_menusalesmaster.menuSalesID DESC 
                    )";


        $this->db->query($q);
        $insert_id = $this->db->insert_id();
        $row_count = $this->db->affected_rows();
        $result = array();
        $i = 0;
        while (true) {
            if ($row_count == $i) {
                break;

            } else if ($i > 99) {
                break;
            }
            $result[$i] = $insert_id;
            $insert_id++;
            $i++;
        }
        if (!empty($result)) {


            $tmpData = array();
            $i2 = 0;
            $where = ' WHERE (';
            foreach ($result as $id) {
                $tmpData[$i2]['invoiceAutoID'] = $id;
                $tmpData[$i2]['invoiceCode'] = $this->sequence->sequence_generator('CINV');
                $where .= ' srp_erp_customerinvoicemaster.invoiceAutoID = ' . $id . ' OR';
                $i2++;

            }
            $where = trim($where, ' OR');
            $where .= ')';
            //var_dump($tmpData);
            $this->db->update_batch('srp_erp_customerinvoicemaster', $tmpData, 'invoiceAutoID');

            if ($row_count > 0) {
                /** Create Invoice Detail */
                $q = "INSERT INTO srp_erp_customerinvoicedetails (
                    invoiceAutoID,
                    `type`,
                    description,
                    transactionAmount,
                    companyLocalAmount,
                    companyReportingAmount,
                    customerAmount,
                    segmentID,
                    segmentCode,
                    companyID,
                    companyCode,
                    createdUserGroup,
                    createdPCID,
                    createdUserID,
                    createdDateTime,
                    createdUserName,
                    `timestamp` 
                    ) (
                    SELECT
                        srp_erp_customerinvoicemaster.invoiceAutoID,
                        'GL' AS `type`,
                        srp_erp_customerinvoicemaster.referenceNo AS description,
                        srp_erp_customerinvoicemaster.transactionAmount,
                        srp_erp_customerinvoicemaster.companyLocalAmount,
                        srp_erp_customerinvoicemaster.companyReportingAmount,
                        srp_erp_customerinvoicemaster.customerCurrencyAmount,
                        srp_erp_customerinvoicemaster.segmentID,
                        srp_erp_customerinvoicemaster.segmentCode,
                        srp_erp_customerinvoicemaster.companyID,
                        srp_erp_customerinvoicemaster.companyCode,
                        srp_erp_customerinvoicemaster.createdUserGroup,
                        srp_erp_customerinvoicemaster.createdPCID,
                        srp_erp_customerinvoicemaster.createdUserID,
                        srp_erp_customerinvoicemaster.createdDateTime,
                        srp_erp_customerinvoicemaster.createdUserName,
                        srp_erp_customerinvoicemaster.`timestamp` 
                    FROM
                        srp_erp_customerinvoicemaster 
                    " . $where . " LIMIT " . $row_count . " )";
                //echo $q;
                $this->db->query($q);

                /** Document Approved Table Entries */
                $q2 = "INSERT INTO srp_erp_documentapproved (
                    `departmentID`,
                    `documentID`,
                    `documentSystemCode`,
                    `documentCode`,
                    `documentDate`,
                    `approvalLevelID`,
                    `roleID`,
                    `approvalGroupID`,
                    `roleLevelOrder`,
                    `docConfirmedDate`,
                    `docConfirmedByEmpID`,
                    `table_name`,
                    `table_unique_field_name`,
                    `approvedEmpID`,
                    `approvedYN`,
                    `approvedDate`,
                    `approvedComments`,
                    `approvedPC`,
                    `companyID`,
                    `companyCode`,
                    `timeStamp` 
                    ) (
                    SELECT
                        'CINV' as `departmentID`,
                        srp_erp_customerinvoicemaster.documentID as `documentID`,
                        srp_erp_customerinvoicemaster.invoiceAutoID as `documentSystemCode`,
                        srp_erp_customerinvoicemaster.invoiceCode as `documentCode`,
                        srp_erp_customerinvoicemaster.invoiceDate as `documentDate`,
                        1 as `approvalLevelID `,
                        1 as `roleID`,
                        0 as `approvalGroupID`,
                        1 as `roleLevelOrder`,
                        srp_erp_customerinvoicemaster.invoiceDate as `docConfirmedDate`,
                        srp_erp_customerinvoicemaster.createdUserID as `docConfirmedByEmpID`,
                        'srp_erp_customerinvoicemaster' as `table_name`,
                        'invoiceAutoID' as `table_unique_field_name`,
                        srp_erp_customerinvoicemaster.createdUserID as `approvedEmpID`,
                        1 as `approvedYN`,
                        srp_erp_customerinvoicemaster.createdDateTime as `approvedDate`,
                        'Approved from POS' as `approvedComments`,
                        srp_erp_customerinvoicemaster.createdPCID as `approvedPC`,
                        srp_erp_customerinvoicemaster.companyID as `companyID`,
                        srp_erp_customerinvoicemaster.companyCode as `companyCode`,
                        srp_erp_customerinvoicemaster.createdDateTime as `timeStamp`
                    FROM
                        srp_erp_customerinvoicemaster 
                    " . $where . " LIMIT " . $row_count . " )";

                $this->db->query($q2);

                /** update menu sales master  */
                $q3 = "UPDATE srp_erp_pos_menusalesmaster AS t1,
                        (
                        SELECT
                            srp_erp_pos_menusalesmaster.menuSalesID AS menuSalesID,
                            srp_erp_customerinvoicemaster.invoiceCode AS invoiceCode,
                            srp_erp_customerinvoicemaster.invoiceAutoID AS invoiceAutoID
                        FROM
                            srp_erp_pos_menusalesmaster
                            INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_pos_menusalesmaster.invoiceCode = srp_erp_customerinvoicemaster.referenceNo 
                        WHERE
                            srp_erp_pos_menusalesmaster.shiftID =  '" . $shiftID . "'  
                            ) AS t2 
                            SET t1.documentSystemCode = t2.invoiceCode,  t1.documentMasterAutoID= t2.invoiceAutoID
                        WHERE
                            t1.menuSalesID = t2.menuSalesID 
                            AND t1.shiftID =  '" . $shiftID . "' ";
                /*echo $q3;
                exit;*/
                $this->db->query($q3);
            }

        }
        //var_dump($result);
        return $result;
    }

    function pos_generate_invoices_on_bill_submit($shiftID, $menuSalesID = 0)
    {
        /** Create Invoice Header */
        $this->load->library('sequence');

        $q = "INSERT INTO srp_erp_customerinvoicemaster (
                wareHouseAutoID,
                invoiceType,
                documentID,
                posMasterAutoID,
                invoiceDate,
                invoiceDueDate,
                customerInvoiceDate,
                invoiceCode,
                referenceNo,
                invoiceNarration,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                companyFinancePeriodID,
                customerID,
                customerSystemCode,
                customerName,
                customerReceivableAutoID,
                customerReceivableSystemGLCode,
                customerReceivableGLAccount,
                customerReceivableDescription,
                customerReceivableType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                customerCurrencyID,
                customerCurrency,
                customerCurrencyExchangeRate,
                customerCurrencyAmount,
                customerCurrencyDecimalPlaces,
                confirmedYN,
                approvedYN,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`,
                isSytemGenerated
                ) (
                SELECT
                    srp_erp_pos_shiftdetails.wareHouseID as wareHouseID,
                    'Direct' AS invoiceType,
                    'CINV' AS documentID,
                    $menuSalesID AS posMasterAutoID,
                    IF(srp_erp_pos_menusalesmaster.isCreditSales=1,DATE_FORMAT( srp_erp_pos_menusalesmaster.menuSalesDate, \"%Y-%m-%d\" ),DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" )) AS invoiceDate,
                    IF(srp_erp_pos_menusalesmaster.isCreditSales=1,DATE_FORMAT( srp_erp_pos_menusalesmaster.menuSalesDate, \"%Y-%m-%d\" ),DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" )) AS invoiceDueDate,
                    IF(srp_erp_pos_menusalesmaster.isCreditSales=1,DATE_FORMAT( srp_erp_pos_menusalesmaster.menuSalesDate, \"%Y-%m-%d\" ),DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" )) AS customerInvoiceDate,
                    0 AS invoiceCode,
                    srp_erp_pos_menusalesmaster.invoiceCode AS referenceNo,
                    concat( 'POS Credit Sales - ', srp_erp_pos_menusalesmaster.invoiceCode ) AS invoiceNarration,
                    getCompanyFinanceYearID ( srp_erp_pos_shiftdetails.companyID ) AS companyFinanceYearID,
                    concat( FY.beginingDate, ' - ', FY.endingDate ) AS companyFinanceYear,
                    FY.beginingDate AS FYBegin,
                    FY.endingDate AS FYEnd,
                    getCompanyFinancePeriodID ( srp_erp_pos_shiftdetails.companyID ) AS companyFinancePeriodID,
                    srp_erp_pos_menusalespayments.customerAutoID AS customerID,
                    srp_erp_customermaster.customerSystemCode AS customerSystemCode,
                    srp_erp_customermaster.customerName AS customerName,
                    srp_erp_customermaster.receivableAutoID AS customerReceivableAutoID,
                    srp_erp_customermaster.receivableSystemGLCode AS customerReceivableSystemGLCode,
                    srp_erp_customermaster.receivableGLAccount AS customerReceivableGLAccount,
                    srp_erp_customermaster.receivableDescription AS customerReceivableDescription,
                    srp_erp_customermaster.receivableType AS customerReceivableType,
                    srp_erp_pos_menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    srp_erp_pos_menusalesmaster.transactionCurrency AS transactionCurrency,
                    srp_erp_pos_menusalesmaster.transactionExchangeRate AS transactionExchangeRate,
                    Sum( srp_erp_pos_menusalespayments.amount ) AS transactionAmount,
                    srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.companyLocalCurrencyID,
                    srp_erp_pos_menusalesmaster.companyLocalCurrency,
                    srp_erp_pos_menusalesmaster.companyLocalExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.companyLocalExchangeRate ) AS companyLocalAmount,
                    srp_erp_pos_menusalesmaster.companyLocalCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.companyReportingCurrencyID,
                    srp_erp_pos_menusalesmaster.companyReportingCurrency,
                    srp_erp_pos_menusalesmaster.companyReportingExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.companyReportingExchangeRate ) AS companyReportingAmount,
                    srp_erp_pos_menusalesmaster.companyReportingCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.customerCurrencyID,
                    srp_erp_pos_menusalesmaster.customerCurrency,
                    srp_erp_pos_menusalesmaster.customerCurrencyExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.customerCurrencyExchangeRate ) AS customerCurrencyAmount,
                    srp_erp_pos_menusalesmaster.customerCurrencyDecimalPlaces,
                    0 AS confirmedYN,
                    0 AS approvedYN,
                    srp_erp_pos_menusalesmaster.segmentID AS segmentID,
                    srp_erp_pos_menusalesmaster.segmentCode AS segmentCode,
                    srp_erp_pos_menusalesmaster.companyID,
                    srp_erp_pos_menusalesmaster.companyCode,
                    srp_erp_pos_shiftdetails.createdUserGroup,
                    srp_erp_pos_shiftdetails.createdPCID,
                    srp_erp_pos_shiftdetails.createdUserID,
                    srp_erp_pos_shiftdetails.startTime AS createdDateTime,
                    srp_erp_pos_shiftdetails.createdUserName,
                    srp_erp_pos_shiftdetails.`timestamp` AS `timestamp`,
                    1 AS isSytemGenerated 
                FROM
                    srp_erp_pos_menusalesmaster
                    LEFT JOIN srp_erp_pos_menusalespayments 
                    ON srp_erp_pos_menusalespayments.menuSalesID = srp_erp_pos_menusalesmaster.menuSalesID
                    AND srp_erp_pos_menusalespayments.wareHouseAutoID=srp_erp_pos_menusalesmaster.wareHouseAutoID
                    LEFT JOIN srp_erp_pos_shiftdetails 
                    ON srp_erp_pos_menusalesmaster.shiftID = srp_erp_pos_shiftdetails.shiftID
                    AND srp_erp_pos_menusalespayments.wareHouseAutoID=srp_erp_pos_shiftdetails.wareHouseID
                    LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID
                    LEFT JOIN srp_erp_companyfinanceyear FY ON FY.companyFinanceYearID = getCompanyFinanceYearID ( srp_erp_pos_shiftdetails.companyID ) 
                WHERE
                    srp_erp_pos_menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND srp_erp_pos_menusalesmaster.menuSalesID = '" . $menuSalesID . "'
                    AND srp_erp_pos_menusalesmaster.wareHouseAutoID = '" . get_outletID() . "'
                    AND srp_erp_pos_menusalesmaster.isCreditSales = 1 
                    AND srp_erp_pos_menusalespayments.paymentConfigMasterID = 7 
                    AND srp_erp_pos_menusalesmaster.isVoid = 0
                    AND srp_erp_pos_menusalesmaster.isHold = 0
                    
                GROUP BY
                    srp_erp_pos_menusalesmaster.menuSalesID 
                ORDER BY
                    srp_erp_pos_menusalesmaster.menuSalesID DESC 
                    )";


        $this->db->query($q);
        $insert_id = $this->db->insert_id();
        $row_count = $this->db->affected_rows();
        $result = array();
        $i = 0;
        while (true) {
            if ($row_count == $i) {
                break;

            } else if ($i > 99) {
                break;
            }
            $result[$i] = $insert_id;
            $insert_id++;
            $i++;
        }
        if (!empty($result)) {


            $tmpData = array();
            $i2 = 0;
            $where = ' WHERE (';
            foreach ($result as $id) {
                $tmpData[$i2]['invoiceAutoID'] = $id;
                $tmpData[$i2]['invoiceCode'] = $this->sequence->sequence_generator('CINV');
                $where .= ' srp_erp_customerinvoicemaster.invoiceAutoID = ' . $id . ' OR';
                $i2++;

            }
            $where = trim($where, ' OR');
            $where .= ')';
            //var_dump($tmpData);
            $this->db->update_batch('srp_erp_customerinvoicemaster', $tmpData, 'invoiceAutoID');

            if ($row_count > 0) {
                /** Create Invoice Detail */
                $q = "INSERT INTO srp_erp_customerinvoicedetails (
                    invoiceAutoID,
                    `type`,
                    description,
                    transactionAmount,
                    companyLocalAmount,
                    companyReportingAmount,
                    customerAmount,
                    segmentID,
                    segmentCode,
                    companyID,
                    companyCode,
                    createdUserGroup,
                    createdPCID,
                    createdUserID,
                    createdDateTime,
                    createdUserName,
                    `timestamp` 
                    ) (
                    SELECT
                        srp_erp_customerinvoicemaster.invoiceAutoID,
                        'GL' AS `type`,
                        srp_erp_customerinvoicemaster.referenceNo AS description,
                        srp_erp_customerinvoicemaster.transactionAmount,
                        srp_erp_customerinvoicemaster.companyLocalAmount,
                        srp_erp_customerinvoicemaster.companyReportingAmount,
                        srp_erp_customerinvoicemaster.customerCurrencyAmount,
                        srp_erp_customerinvoicemaster.segmentID,
                        srp_erp_customerinvoicemaster.segmentCode,
                        srp_erp_customerinvoicemaster.companyID,
                        srp_erp_customerinvoicemaster.companyCode,
                        srp_erp_customerinvoicemaster.createdUserGroup,
                        srp_erp_customerinvoicemaster.createdPCID,
                        srp_erp_customerinvoicemaster.createdUserID,
                        srp_erp_customerinvoicemaster.createdDateTime,
                        srp_erp_customerinvoicemaster.createdUserName,
                        srp_erp_customerinvoicemaster.`timestamp` 
                    FROM
                        srp_erp_customerinvoicemaster 
                    " . $where . " LIMIT " . $row_count . " )";
                //echo $q;
                $this->db->query($q);

                /** Document Approved Table Entries */

                /** update menu sales master  */
                $q3 = "UPDATE srp_erp_pos_menusalesmaster AS t1,
                        (
                        SELECT
                            srp_erp_pos_menusalesmaster.menuSalesID AS menuSalesID,
                            srp_erp_customerinvoicemaster.invoiceCode AS invoiceCode,
                            srp_erp_customerinvoicemaster.invoiceAutoID AS invoiceAutoID
                        FROM
                            srp_erp_pos_menusalesmaster
                            INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_pos_menusalesmaster.invoiceCode = srp_erp_customerinvoicemaster.referenceNo 
                        WHERE
                            srp_erp_pos_menusalesmaster.shiftID =  '" . $shiftID . "'  
                            ) AS t2 
                            SET t1.documentSystemCode = t2.invoiceCode,  t1.documentMasterAutoID= t2.invoiceAutoID
                        WHERE
                            t1.menuSalesID = t2.menuSalesID 
                            AND t1.shiftID =  '" . $shiftID . "' ";
                /*echo $q3;
                exit;*/
                $this->db->query($q3);
            }

        }
        //var_dump($result);
        return $result;
    }

    function document_approved_entries_for_invoices($invoiceAutoID)
    {
        $q2 = "INSERT INTO srp_erp_documentapproved (
                    `departmentID`,
                    `documentID`,
                    `documentSystemCode`,
                    `documentCode`,
                    `documentDate`,
                    `approvalLevelID`,
                    `roleID`,
                    `approvalGroupID`,
                    `roleLevelOrder`,
                    `docConfirmedDate`,
                    `docConfirmedByEmpID`,
                    `table_name`,
                    `table_unique_field_name`,
                    `approvedEmpID`,
                    `approvedYN`,
                    `approvedDate`,
                    `approvedComments`,
                    `approvedPC`,
                    `companyID`,
                    `companyCode`,
                    `timeStamp` 
                    ) (
                    SELECT
                        'CINV' as `departmentID`,
                        srp_erp_customerinvoicemaster.documentID as `documentID`,
                        srp_erp_customerinvoicemaster.invoiceAutoID as `documentSystemCode`,
                        srp_erp_customerinvoicemaster.invoiceCode as `documentCode`,
                        srp_erp_customerinvoicemaster.invoiceDate as `documentDate`,
                        1 as `approvalLevelID `,
                        1 as `roleID`,
                        0 as `approvalGroupID`,
                        1 as `roleLevelOrder`,
                        srp_erp_customerinvoicemaster.invoiceDate as `docConfirmedDate`,
                        srp_erp_customerinvoicemaster.createdUserID as `docConfirmedByEmpID`,
                        'srp_erp_customerinvoicemaster' as `table_name`,
                        'invoiceAutoID' as `table_unique_field_name`,
                        srp_erp_customerinvoicemaster.createdUserID as `approvedEmpID`,
                        1 as `approvedYN`,
                        srp_erp_customerinvoicemaster.createdDateTime as `approvedDate`,
                        'Approved from POS' as `approvedComments`,
                        srp_erp_customerinvoicemaster.createdPCID as `approvedPC`,
                        srp_erp_customerinvoicemaster.companyID as `companyID`,
                        srp_erp_customerinvoicemaster.companyCode as `companyCode`,
                        srp_erp_customerinvoicemaster.createdDateTime as `timeStamp`
                    FROM
                        srp_erp_customerinvoicemaster
                    WHERE 
                        srp_erp_customerinvoicemaster.invoiceAutoID=$invoiceAutoID
                    )";

        $this->db->query($q2);
    }

    function pos_generate_invoices_billID($shiftID, $menuSalesID)
    {
        /** Create Invoice Header */
        $this->load->library('sequence');

        $q = "INSERT INTO srp_erp_customerinvoicemaster (
                invoiceType,
                documentID,
                invoiceDate,
                invoiceDueDate,
                customerInvoiceDate,
                invoiceCode,
                referenceNo,
                invoiceNarration,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                companyFinancePeriodID,
                customerID,
                customerSystemCode,
                customerName,
                customerReceivableAutoID,
                customerReceivableSystemGLCode,
                customerReceivableGLAccount,
                customerReceivableDescription,
                customerReceivableType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                customerCurrencyID,
                customerCurrency,
                customerCurrencyExchangeRate,
                customerCurrencyAmount,
                customerCurrencyDecimalPlaces,
                confirmedYN,
                confirmedByEmpID,
                confirmedByName,
                confirmedDate,
                approvedYN,
                approvedDate,
                approvedbyEmpID,
                approvedbyEmpName,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp` 
                ) (
                SELECT
                    'Direct' AS invoiceType,
                    'CINV' AS documentID,
                    IF(srp_erp_pos_menusalesmaster.isCreditSales=1,DATE_FORMAT( srp_erp_pos_menusalesmaster.menuSalesDate, \"%Y-%m-%d\" ),DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" )) AS invoiceDate,
                    DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" ) AS invoiceDueDate,
                    DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" ) AS customerInvoiceDate,
                    0 AS invoiceCode,
                    srp_erp_pos_menusalesmaster.invoiceCode AS referenceNo,
                    concat( 'POS Credit Sales - ', srp_erp_pos_menusalesmaster.invoiceCode ) AS invoiceNarration,
                    getCompanyFinanceYearID ( srp_erp_pos_shiftdetails.companyID ) AS companyFinanceYearID,
                    concat( FY.beginingDate, ' - ', FY.endingDate ) AS companyFinanceYear,
                    FY.beginingDate AS FYBegin,
                    FY.endingDate AS FYEnd,
                    getCompanyFinancePeriodID ( srp_erp_pos_shiftdetails.companyID ) AS companyFinancePeriodID,
                    srp_erp_pos_menusalespayments.customerAutoID AS customerID,
                    srp_erp_customermaster.customerSystemCode AS customerSystemCode,
                    srp_erp_customermaster.customerName AS customerName,
                    srp_erp_customermaster.receivableAutoID AS customerReceivableAutoID,
                    srp_erp_customermaster.receivableSystemGLCode AS customerReceivableSystemGLCode,
                    srp_erp_customermaster.receivableGLAccount AS customerReceivableGLAccount,
                    srp_erp_customermaster.receivableDescription AS customerReceivableDescription,
                    srp_erp_customermaster.receivableType AS customerReceivableType,
                    srp_erp_pos_menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    srp_erp_pos_menusalesmaster.transactionCurrency AS transactionCurrency,
                    srp_erp_pos_menusalesmaster.transactionExchangeRate AS transactionExchangeRate,
                    Sum( srp_erp_pos_menusalespayments.amount ) AS transactionAmount,
                    srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.companyLocalCurrencyID,
                    srp_erp_pos_menusalesmaster.companyLocalCurrency,
                    srp_erp_pos_menusalesmaster.companyLocalExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.companyLocalExchangeRate ) AS companyLocalAmount,
                    srp_erp_pos_menusalesmaster.companyLocalCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.companyReportingCurrencyID,
                    srp_erp_pos_menusalesmaster.companyReportingCurrency,
                    srp_erp_pos_menusalesmaster.companyReportingExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.companyReportingExchangeRate ) AS companyReportingAmount,
                    srp_erp_pos_menusalesmaster.companyReportingCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.customerCurrencyID,
                    srp_erp_pos_menusalesmaster.customerCurrency,
                    srp_erp_pos_menusalesmaster.customerCurrencyExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.customerCurrencyExchangeRate ) AS customerCurrencyAmount,
                    srp_erp_pos_menusalesmaster.customerCurrencyDecimalPlaces,
                    1 AS confirmedYN,
                    srp_erp_pos_shiftdetails.createdUserID AS confirmedByEmpID,
                    srp_erp_pos_shiftdetails.createdUserName AS confirmedByName,
                    srp_erp_pos_shiftdetails.startTime AS confirmedDate,
                    1 AS approvedYN,
                    srp_erp_pos_shiftdetails.startTime AS approvedDate,
                    srp_erp_pos_shiftdetails.createdUserID AS approvedbyEmpID,
                    srp_erp_pos_shiftdetails.createdUserName AS approvedbyEmpName,
                    srp_erp_pos_menusalesmaster.segmentID AS segmentID,
                    srp_erp_pos_menusalesmaster.segmentCode AS segmentCode,
                    srp_erp_pos_menusalesmaster.companyID,
                    srp_erp_pos_menusalesmaster.companyCode,
                    srp_erp_pos_shiftdetails.createdUserGroup,
                    srp_erp_pos_shiftdetails.createdPCID,
                    srp_erp_pos_shiftdetails.createdUserID,
                    srp_erp_pos_shiftdetails.startTime AS createdDateTime,
                    srp_erp_pos_shiftdetails.createdUserName,
                    srp_erp_pos_shiftdetails.`timestamp` AS `timestamp` 
                FROM
                    srp_erp_pos_menusalesmaster
                    LEFT JOIN srp_erp_pos_menusalespayments ON srp_erp_pos_menusalespayments.menuSalesID = srp_erp_pos_menusalesmaster.menuSalesID
                    LEFT JOIN srp_erp_pos_shiftdetails ON srp_erp_pos_menusalesmaster.shiftID = srp_erp_pos_shiftdetails.shiftID
                    LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID
                    LEFT JOIN srp_erp_companyfinanceyear FY ON FY.companyFinanceYearID = getCompanyFinanceYearID ( srp_erp_pos_shiftdetails.companyID ) 
                WHERE
                    srp_erp_pos_menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND srp_erp_pos_menusalesmaster.isCreditSales = 1 
                    AND srp_erp_pos_menusalespayments.paymentConfigMasterID = 7 
                    AND srp_erp_pos_menusalesmaster.isVoid = 0
                    AND srp_erp_pos_menusalesmaster.isHold = 0
                    AND srp_erp_pos_menusalesmaster.isHold = '" . $menuSalesID . "')";


        $this->db->query($q);
        $insert_id = $this->db->insert_id();
        $row_count = $this->db->affected_rows();
        $result = array();
        $i = 0;
        while (true) {
            if ($row_count == $i) {
                break;

            } else if ($i > 99) {
                break;
            }
            $result[$i] = $insert_id;
            $insert_id++;
            $i++;
        }
        if (!empty($result)) {


            $tmpData = array();
            $i2 = 0;
            $where = ' WHERE (';
            foreach ($result as $id) {
                $tmpData[$i2]['invoiceAutoID'] = $id;
                $tmpData[$i2]['invoiceCode'] = $this->sequence->sequence_generator('CINV');
                $where .= ' srp_erp_customerinvoicemaster.invoiceAutoID = ' . $id . ' OR';
                $i2++;

            }
            $where = trim($where, ' OR');
            $where .= ')';
            //var_dump($tmpData);
            $this->db->update_batch('srp_erp_customerinvoicemaster', $tmpData, 'invoiceAutoID');

            if ($row_count > 0) {
                /** Create Invoice Detail */
                $q = "INSERT INTO srp_erp_customerinvoicedetails (
                    invoiceAutoID,
                    `type`,
                    description,
                    transactionAmount,
                    companyLocalAmount,
                    companyReportingAmount,
                    customerAmount,
                    segmentID,
                    segmentCode,
                    companyID,
                    companyCode,
                    createdUserGroup,
                    createdPCID,
                    createdUserID,
                    createdDateTime,
                    createdUserName,
                    `timestamp` 
                    ) (
                    SELECT
                        srp_erp_customerinvoicemaster.invoiceAutoID,
                        'GL' AS `type`,
                        srp_erp_customerinvoicemaster.referenceNo AS description,
                        srp_erp_customerinvoicemaster.transactionAmount,
                        srp_erp_customerinvoicemaster.companyLocalAmount,
                        srp_erp_customerinvoicemaster.companyReportingAmount,
                        srp_erp_customerinvoicemaster.customerCurrencyAmount,
                        srp_erp_customerinvoicemaster.segmentID,
                        srp_erp_customerinvoicemaster.segmentCode,
                        srp_erp_customerinvoicemaster.companyID,
                        srp_erp_customerinvoicemaster.companyCode,
                        srp_erp_customerinvoicemaster.createdUserGroup,
                        srp_erp_customerinvoicemaster.createdPCID,
                        srp_erp_customerinvoicemaster.createdUserID,
                        srp_erp_customerinvoicemaster.createdDateTime,
                        srp_erp_customerinvoicemaster.createdUserName,
                        srp_erp_customerinvoicemaster.`timestamp` 
                    FROM
                        srp_erp_customerinvoicemaster 
                    " . $where . " LIMIT " . $row_count . " )";
                //echo $q;
                $this->db->query($q);

                /** Document Approved Table Entries */
                $q2 = "INSERT INTO srp_erp_documentapproved (
                    `departmentID`,
                    `documentID`,
                    `documentSystemCode`,
                    `documentCode`,
                    `documentDate`,
                    `approvalLevelID`,
                    `roleID`,
                    `approvalGroupID`,
                    `roleLevelOrder`,
                    `docConfirmedDate`,
                    `docConfirmedByEmpID`,
                    `table_name`,
                    `table_unique_field_name`,
                    `approvedEmpID`,
                    `approvedYN`,
                    `approvedDate`,
                    `approvedComments`,
                    `approvedPC`,
                    `companyID`,
                    `companyCode`,
                    `timeStamp` 
                    ) (
                    SELECT
                        'CINV' as `departmentID`,
                        srp_erp_customerinvoicemaster.documentID as `documentID`,
                        srp_erp_customerinvoicemaster.invoiceAutoID as `documentSystemCode`,
                        srp_erp_customerinvoicemaster.invoiceCode as `documentCode`,
                        srp_erp_customerinvoicemaster.invoiceDate as `documentDate`,
                        1 as `approvalLevelID `,
                        1 as `roleID`,
                        0 as `approvalGroupID`,
                        1 as `roleLevelOrder`,
                        srp_erp_customerinvoicemaster.invoiceDate as `docConfirmedDate`,
                        srp_erp_customerinvoicemaster.createdUserID as `docConfirmedByEmpID`,
                        'srp_erp_customerinvoicemaster' as `table_name`,
                        'invoiceAutoID' as `table_unique_field_name`,
                        srp_erp_customerinvoicemaster.createdUserID as `approvedEmpID`,
                        1 as `approvedYN`,
                        srp_erp_customerinvoicemaster.createdDateTime as `approvedDate`,
                        'Approved from POS' as `approvedComments`,
                        srp_erp_customerinvoicemaster.createdPCID as `approvedPC`,
                        srp_erp_customerinvoicemaster.companyID as `companyID`,
                        srp_erp_customerinvoicemaster.companyCode as `companyCode`,
                        srp_erp_customerinvoicemaster.createdDateTime as `timeStamp`
                    FROM
                        srp_erp_customerinvoicemaster 
                    " . $where . " LIMIT " . $row_count . " )";

                $this->db->query($q2);

                /** update menu sales master  */
                $q3 = "UPDATE srp_erp_pos_menusalesmaster AS t1,
                        (
                        SELECT
                            srp_erp_pos_menusalesmaster.menuSalesID AS menuSalesID,
                            srp_erp_customerinvoicemaster.invoiceCode AS invoiceCode,
                            srp_erp_customerinvoicemaster.invoiceAutoID AS invoiceAutoID
                        FROM
                            srp_erp_pos_menusalesmaster
                            INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_pos_menusalesmaster.invoiceCode = srp_erp_customerinvoicemaster.referenceNo 
                        WHERE
                            srp_erp_pos_menusalesmaster.shiftID =  '" . $shiftID . "'  
                            ) AS t2 
                            SET t1.documentSystemCode = t2.invoiceCode,  t1.documentMasterAutoID= t2.invoiceAutoID
                        WHERE
                            t1.menuSalesID = t2.menuSalesID 
                            AND t1.shiftID =  '" . $shiftID . "' ";
                $this->db->query($q3);
            }

        }
        return $result;
    }


    /**Credit Sales Double Entry */
    /** 1. CREDIT SALES  - REVENUE */
    function update_revenue_generalLedger_credit_sales($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp
            ) (SELECT
                menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                menusalesmaster.documentSystemCode  AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - Revenue',' - ',menusalesmaster.invoiceCode) AS documentNarration,
                '' AS chequeNumber,
                item.revenueGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID )  )) AS companyLocalAmount, getDecimalPlaces ( company.company_default_currencyID  ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID as companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID )  )) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                shiftDetail.createdUserGroup AS createdUserGroup,
            shiftDetail.createdPCID as  createdPCID,
            shiftDetail.createdUserID as  createdUserID,
            shiftDetail.startTime as createdDateTime,
            shiftDetail.createdUserName as createdUserName,
            NULL AS modifiedPCID,
            NULL AS modifiedUserID,
            NULL AS modifiedDateTime,
            null AS modifiedUserName,
            CURRENT_TIMESTAMP() as `timestamp`
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID AND menusalesmaster.wareHouseAutoID = item.wareHouseAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND menusalesmaster.wareHouseAutoID = shiftDetail.wareHouseID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'   
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "' 
                AND item.wareHouseAutoID = '" . $outletID . "' 
                AND menusalesmaster.isHold = 0 
                AND menusalesmaster.isVoid = 0 
                AND menusalesmaster.isCreditSales = 1
            GROUP BY
                revenueGLAutoID, menusalesmaster.menuSalesID);";

        $result = $this->db->query($q);
        //echo $this->db->last_query();
        return $result;
    }

    /** 1. CREDIT SALES  - REVENUE - bill ID */
    function update_revenue_generalLedger_credit_sales_menuSalesID($shiftID, $menuSalesID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp
            ) (SELECT
                    'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                menusalesmaster.documentSystemCode  AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - Revenue',' - ',menusalesmaster.invoiceCode) AS documentNarration,
                '' AS chequeNumber,
                item.revenueGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID )  )) AS companyLocalAmount, getDecimalPlaces ( company.company_default_currencyID  ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID as companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                (( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID )  )) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                shiftDetail.createdUserGroup AS createdUserGroup,
            shiftDetail.createdPCID as  createdPCID,
            shiftDetail.createdUserID as  createdUserID,
            shiftDetail.startTime as createdDateTime,
            shiftDetail.createdUserName as createdUserName,
            NULL AS modifiedPCID,
            NULL AS modifiedUserID,
            NULL AS modifiedDateTime,
            null AS modifiedUserName,
            CURRENT_TIMESTAMP() as `timestamp`
            FROM
                srp_erp_pos_menusalesitems item
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "' and menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND menusalesmaster.isCreditSales = 1 and menusalesmaster.menuSalesID = '" . $menuSalesID . "'
            GROUP BY
                revenueGLAutoID, menusalesmaster.menuSalesID);";

        $result = $this->db->query($q);
        return $result;
    }

    /** 2. CREDIT SALES  - COGS */
    function update_cogs_generalLedger_credit_sales($shiftID, $isCreditSales = true)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )
            
            (SELECT
                menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                menusalesmaster.documentSystemCode AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - COGS',' - ',menusalesmaster.invoiceCode) AS documentNarration,
                '' AS chequeNumber,
                item.costGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'dr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                  ( sum( IFNULL(item.cost,0) * item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                  ( sum( IFNULL(item.cost,0) *  item.menuSalesQty    ) )  / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP() AS  `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.costGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'  
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND item.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0   
                AND menusalesmaster.isVoid = 0 ";
        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 1 ";
        }
        $q .= " GROUP BY
                item.costGLAutoID, menusalesmaster.menuSalesID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 3. CREDIT SALES  - INVENTORY
     *
     * changes done by mushtaq discussed with Hisham
     * Updated on 2019-01-17 JIRA SME-1279
     */
    function update_inventory_generalLedger_credit_sales($shiftID, $isCreditSales = true)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger(
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp
            )
            (SELECT
                menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                'CINV' AS documentCode,
                menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                menusalesmaster.documentSystemCode AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                concat('POS Credit Sales - Inventory', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                '' AS chequeNumber,
                item.assetGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                abs(( sum( IFNULL(item.cost,0) *   item.menuSalesQty  )-IFNULL(itmexceed.amount,0) )) *- 1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                ( abs( sum( IFNULL(item.cost,0) * item.menuSalesQty    )-IFNULL(itmexceed.amount,0) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                ( abs( sum( IFNULL(item.cost,0) *  item.menuSalesQty    )-IFNULL(itmexceed.amount,0) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                'CUS' AS partyType,
                customermaster.customerAutoID AS partyAutoID,
                customermaster.customerSystemCode AS partySystemCode,
                customermaster.customerName AS partyName,
                customermaster.customerCurrencyID AS partyCurrencyID,
                customermaster.customerCurrency AS partyCurrency,
                getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                abs(( sum( IFNULL(item.cost,0) *   item.menuSalesQty  )-IFNULL(itmexceed.amount,0) )) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP () `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
            LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS amount,
		IFNULL(SUM(exceededQty),0) AS excdqty,
		warehouseAutoID,
		assetGLAutoID,
		documentAutoID

	FROM
		srp_erp_itemexceeded
	WHERE
		documentCode = 'CINV'
	AND warehouseAutoID = '" . $outletID . "'
	GROUP BY
		documentAutoID,
		warehouseAutoID,
		assetGLAutoID
) itmexceed ON menusalesmaster.documentMasterAutoID = itmexceed.documentAutoID AND item.warehouseAutoID = itmexceed.warehouseAutoID AND item.assetGLAutoID=itmexceed.assetGLAutoID

            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "' 
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND item.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.isHold = 0   
                AND menusalesmaster.isVoid = 0";
        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 1 ";
        }
        $q .= " GROUP BY
                item.assetGLAutoID, menusalesmaster.menuSalesID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 4.  CREDIT SALES - TAX */
    function update_tax_generalLedger_credit_sales($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - TAX', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    menusalesTax.GLCode AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(menusalesTax.taxAmount, 0)
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    abs(
                        sum(
                            ifnull(menusalesTax.taxAmount, 0)
                        )
                    ) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalestaxes menusalesTax
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = menusalesTax.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesTax.GLCode
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesTax.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    chartOfAccount.GLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 5.  CREDIT SALES - COMMISSION EXPENSE  */
    function update_commissionExpense_generalLedger_credit_sales($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
				partyAutoID,
				partySystemCode,
				partyName,
				partyCurrencyID,
				partyCurrency,
				partyExchangeRate,
				partyCurrencyAmount,
				partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Sales Commission', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    customers.expenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        menusalesmaster.deliveryCommissionAmount
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            IFNULL(menusalesmaster.deliveryCommissionAmount,0)
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    sum( menusalesmaster.deliveryCommissionAmount ) / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.expenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID  AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                    AND ( menusalesmaster.deliveryCommission IS NOT NULL AND menusalesmaster.deliveryCommission <> 0 )
                    AND menusalesmaster.isDelivery = 1 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    customers.expenseGLAutoID, menusalesmaster.menuSalesID)";

        $result = $this->db->query($q);
        return $result;
    }

    /** 6.  CREDIT SALES - COMMISSION PAYABLE */
    function update_commissionPayable_generalLedger_credit_sales($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                    wareHouseAutoID,
                    documentCode,
                    documentMasterAutoID,
                    documentSystemCode,
                    documentDate,
                    documentYear,
                    documentMonth,
                    documentNarration,
                    GLAutoID,
                    systemGLCode,
                    GLCode,
                    GLDescription,
                    GLType,
                    amount_type,
                    isFromItem,
                    transactionCurrency,
                    transactionExchangeRate,
                    transactionAmount,
                    transactionCurrencyID,
                    transactionCurrencyDecimalPlaces,
                    companyLocalCurrencyID,
                    companyLocalCurrency,
                    companyLocalExchangeRate,
                    companyLocalAmount,
                    companyLocalCurrencyDecimalPlaces,
                    companyReportingCurrencyID,
                    companyReportingCurrency,
                    companyReportingExchangeRate,
                    companyReportingAmount,
                    companyReportingCurrencyDecimalPlaces,
                    partyType,
                    partyAutoID,
                    partySystemCode,
                    partyName,
                    partyCurrencyID,
                    partyCurrency,
                    partyExchangeRate,
                    partyCurrencyAmount,
                    partyCurrencyDecimalPlaces,
                    segmentID,
                    segmentCode,
                    companyID,
                    companyCode,
                    createdUserGroup,
                    createdPCID,
                    createdUserID,
                    createdDateTime,
                    createdUserName,
                    modifiedPCID,
                    modifiedUserID,
                    modifiedDateTime,
                    modifiedUserName,
                    `timestamp`
                )( 
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Sales Commission Payable', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    customers.liabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            menusalesmaster.deliveryCommissionAmount
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                menusalesmaster.deliveryCommissionAmount
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                               IFNULL( menusalesmaster.deliveryCommissionAmount,0)
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
					customermaster.customerAutoID AS partyAutoID,
					customermaster.customerSystemCode AS partySystemCode,
					customermaster.customerName AS partyName,
					customermaster.customerCurrencyID AS partyCurrencyID,
					customermaster.customerCurrency AS partyCurrency,
					getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
					ABS( sum( menusalesmaster.deliveryCommissionAmount ) ) * - 1  /  ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
					customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
                
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.liabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
                    AND (
                        menusalesmaster.deliveryCommission IS NOT NULL
                        AND menusalesmaster.deliveryCommission <> 0
                    )
                    AND menusalesmaster.isDelivery = 1
                    AND menusalesmaster.isOnTimeCommision = 0 AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    customers.liabilityGLAutoID, menusalesmaster.menuSalesID
                );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 7.  CREDIT SALES - ROYALTY PAYABLE */
    function update_royaltyPayable_generalLedger_credit_sales($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    CURDATE() AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Royalty Payable', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    franchisemaster.royaltyLiabilityGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    ABS(
                        sum(
                            (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) * - 1 AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        ABS(
                            sum(
                                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                    franchisemaster.royaltyPercentage / 100
                                )
                            )
                        ) * - 1
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
					customermaster.customerAutoID AS partyAutoID,
					customermaster.customerSystemCode AS partySystemCode,
					customermaster.customerName AS partyName,
					customermaster.customerCurrencyID AS partyCurrencyID,
					customermaster.customerCurrency AS partyCurrency,
					getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
					ABS(
                        sum(
                            (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) * - 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
					customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyLiabilityGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    franchisemaster.royaltyLiabilityGLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 8.  CREDIT SALES - ROYALTY EXPENSES */
    function update_royaltyExpenses_generalLedger_credit_sales($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyID,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Royalty Expenses', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    franchisemaster.royaltyExpenseGLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    sum(
                        ( IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0) ) * (
                            franchisemaster.royaltyPercentage / 100
                        )
                    ) AS transactionAmount,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        sum(
                            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                                franchisemaster.royaltyPercentage / 100
                            )
                        )
                    ) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
					customermaster.customerAutoID AS partyAutoID,
					customermaster.customerSystemCode AS partySystemCode,
					customermaster.customerName AS partyName,
					customermaster.customerCurrencyID AS partyCurrencyID,
					customermaster.customerCurrency AS partyCurrency,
					getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
					sum(
                        ( IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0) ) * (
                            franchisemaster.royaltyPercentage / 100
                        )
                    ) / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
					customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster menusalesmaster 
                INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyExpenseGLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    franchisemaster.royaltyExpenseGLAutoID, menusalesmaster.menuSalesID
            );";

        $result = $this->db->query($q);
        return $result;
    }

    /** 9. CREDIT SALES -  SERVICE CHARGE */
    function update_serviceCharge_generalLedger_credit_sales($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales - Service Charge', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    servicecharge.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'cr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    abs(
                        sum(
                            ifnull(
                                servicecharge.serviceChargeAmount,
                                0
                            )
                        )
                    ) *- 1 AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_default_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    (
                        (
                            abs(
                                sum(
                                    ifnull(
                                        servicecharge.serviceChargeAmount,
                                        0
                                    )
                                )
                            ) *- 1
                        ) / (
                            getExchangeRate (
                                menusalesmaster.transactionCurrencyID,
                                company.company_reporting_currencyID,
                                menusalesmaster.companyID
                            )
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    
                    'CUS' AS partyType,
                    customermaster.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate ( customermaster.customerCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS partyExchangeRate,
                    abs(
                        sum(
                            ifnull(
                                servicecharge.serviceChargeAmount,
                                0
                            )
                        )
                    ) *- 1 / ( getExchangeRate ( customermaster.customerCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    menusalesmaster.createdUserName AS createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesservicecharge servicecharge
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = servicecharge.menuSalesID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = servicecharge.GLAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = getMenuSalesCustomerAutoID(menusalesmaster.menuSalesID, '" . $outletID . "')
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND servicecharge.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 
                    AND menusalesmaster.isVoid = 0 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    chartOfAccount.GLAutoID, menusalesmaster.menuSalesID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS */
    function update_creditSales_generalLedger_credit_sales($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_generalledger (
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                partyType,
                partyAutoID,
                partySystemCode,
                partyName,
                partyCurrencyID,
                partyCurrency,
                partyExchangeRate,
                partyCurrencyAmount,
                partyCurrencyDecimalPlaces,
                subLedgerType,
                subLedgerDesc,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )(
                SELECT
                    menusalesmaster.wareHouseAutoID as wareHouseAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
                    menusalesmaster.documentSystemCode  AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                    YEAR (curdate()) AS documentYear,
                    MONTH (curdate()) AS documentMonth,
                    concat('POS Credit Sales', ' - ',menusalesmaster.invoiceCode) AS documentNarration,
                    '' AS chequeNumber,
                    chartOfAccount.GLAutoID AS GLAutoID,
                    chartOfAccount.systemAccountCode AS systemGLCode,
                    chartOfAccount.GLSecondaryCode AS GLCode,
                    chartOfAccount.GLDescription AS GLDescription,
                    chartOfAccount.subCategory AS GLType,
                    'dr' AS amount_type,
                    '0' AS isFromItem,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    SUM(payments.amount) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_default_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyLocalAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            menusalesmaster.transactionCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS companyReportingAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    'CUS' AS partyType,
                    payments.customerAutoID AS partyAutoID,
                    customermaster.customerSystemCode AS partySystemCode,
                    customermaster.customerName AS partyName,
                    customermaster.customerCurrencyID AS partyCurrencyID,
                    customermaster.customerCurrency AS partyCurrency,
                    getExchangeRate (
                        customermaster.customerCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS partyExchangeRate,
                    SUM(payments.amount) / (
                        getExchangeRate (
                            customermaster.customerCurrencyID,
                            company.company_reporting_currencyID,
                            menusalesmaster.companyID
                        )
                    ) AS partyCurrencyAmount,
                    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
                    3 AS subLedgerType,
                    'AR' AS subLedgerDesc,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    menusalesmaster.createdUserGroup AS createdUserGroup,
                    menusalesmaster.createdPCID AS createdPCID,
                    menusalesmaster.createdUserID createdUserID,
                    shiftDetail.startTime createdDateTime,
                    menusalesmaster.createdUserName createdUserName,
                    NULL AS modifiedPCID,
                    NULL AS modifiedUserID,
                    NULL AS modifiedDateTime,
                    NULL AS modifiedUserName,
                    CURRENT_TIMESTAMP() `timestamp`
                FROM
                    srp_erp_pos_menusalespayments payments
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON payments.menuSalesID = menusalesmaster.menuSalesID AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = payments.customerAutoID
                LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customermaster.receivableAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID 
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND payments.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0
                    AND menusalesmaster.isVoid = 0
                    AND payments.paymentConfigMasterID = 7 
                    AND menusalesmaster.isCreditSales = 1
                GROUP BY
                    chartOfAccount.GLAutoID,
                    payments.customerAutoID, menusalesmaster.menuSalesID
            )";

        $result = $this->db->query($q);
        return $result;
    }

    /** CREDIT SALES - ITEM MASTER STOCK UPDATE */
    function update_itemMasterNewStock_credit_sales($shiftID)
    {
        $outletID = get_outletID();
        $q = "SELECT
                    itemmaster.itemAutoID,
                    -- menusalesmaster.wareHouseAutoID,
                    -- itemmaster.currentStock,
                    -- sum(itemdetail.qty*itemdetail.menuSalesQty) as qty,
                    -- itemdetail.UOMID,
                    -- itemmaster.defaultUnitOfMeasure,
                    -- itemdetail.UOM,
                    -- getUoMConvertion (
                    -- 	itemdetail.UOMID,
                    -- 	itemmaster.defaultUnitOfMeasureID,
                    -- 	menusalesmaster.companyID
                    -- ) AS convertionRate,
                    -- SUM(
                    -- 	(itemdetail.qty * item.qty) / (
                    -- 		getUoMConvertion (
                    -- 			itemdetail.UOMID,
                    -- 			itemmaster.defaultUnitOfMeasureID,
                    -- 			menusalesmaster.companyID
                    -- 		)
                    -- 	)
                    -- ) AS usedStock, 
                 
                
                itemmaster.currentStock - SUM(
                        (
                            (
                                itemdetail.qty * itemdetail.menuSalesQty
                            ) / (
                                getUoMConvertion (
                                    itemdetail.UOMID,
                                    itemmaster.defaultUnitOfMeasureID,
                                    menusalesmaster.companyID
                                )
                            )
                        )
                    ) as currentStock
                FROM
                    srp_erp_pos_menusalesitemdetails itemdetail
                INNER JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID 
                -- LEFT JOIN srp_erp_pos_menusalesitems item ON item.menuSalesItemID = itemdetail.menuSalesItemID
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'  
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isCreditSales = 1
                    AND menusalesmaster.isHold = 0    
                    AND menusalesmaster.isVoid = 0
                GROUP BY
                    itemmaster.itemAutoID";
        $result = $this->db->query($q)->result_array();

        /** New stock information */
        if (!empty($result)) {
            /**update new stock */
            $this->db->update_batch('srp_erp_itemmaster', $result, 'itemAutoID');
        }

        return $result;
    }


    /** CREDIT SALES - ITEM MASTER STOCK UPDATE - Item Exceeded */
    function update_itemMasterNewStock_credit_sales_Item_exceeded($shiftID)
    {

        $q = "SELECT
                    itemmaster.itemAutoID,
                    if(itemmaster.currentStock - SUM(
                        (
                            (
                                itemdetail.qty * itemdetail.menuSalesQty
                            ) / (
                                getUoMConvertion (
                                    itemdetail.UOMID,
                                    itemmaster.defaultUnitOfMeasureID,
                                    menusalesmaster.companyID
                                )
                            )
                        )
                    )>0, itemmaster.currentStock - SUM(
                        (
                            (
                                itemdetail.qty * itemdetail.menuSalesQty
                            ) / (
                                getUoMConvertion (
                                    itemdetail.UOMID,
                                    itemmaster.defaultUnitOfMeasureID,
                                    menusalesmaster.companyID
                                )
                            )
                        )
                    ), 0) as currentStock
                FROM
                    srp_erp_pos_menusalesitemdetails itemdetail
                INNER JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID 
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' 
                    AND menusalesmaster.isCreditSales = 1
                    AND menusalesmaster.isHold = 0    
                    AND menusalesmaster.isVoid = 0
                GROUP BY
                    itemmaster.itemAutoID";
        $result = $this->db->query($q)->result_array();

        /** New stock information */
        if (!empty($result)) {
            /**update new stock */
            $this->db->update_batch('srp_erp_itemmaster', $result, 'itemAutoID');
        }

        return $result;
    }


    /** CREDIT SALES - WAREHOUSE ITEM MASTER STOCK UPDATE */
    function update_warehouseItemMasterNewStock_credit_sales($shiftID)
    {
        $outletID = get_outletID();
        $q = "SELECT
                warehouseitem.warehouseItemsAutoID,
                -- itemdetail.itemAutoID,
             
            warehouseitem.currentStock - SUM(
                    (
                        (itemdetail.qty * itemdetail.menuSalesQty) / (
                            getUoMConvertion (
                                itemdetail.UOMID,
                                itemmaster.defaultUnitOfMeasureID,
                                menusalesmaster.companyID
                            )
                        )
                    )
                ) as currentStock 
            FROM
                srp_erp_pos_menusalesitemdetails itemdetail
            INNER JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
            LEFT JOIN srp_erp_warehouseitems warehouseitem ON warehouseitem.itemAutoID = itemmaster.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
                AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                AND menusalesmaster.wareHouseAutoID = warehouseitem.wareHouseAutoID AND menusalesmaster.isCreditSales = 1
                AND menusalesmaster.isHold = 0    
                AND menusalesmaster.isVoid = 0
            GROUP BY
                itemmaster.itemAutoID,
                menusalesmaster.wareHouseAutoID;";
        $result = $this->db->query($q)->result_array();

        /** New stock information */
        if (!empty($result)) {
            /**update new stock */
            $this->db->update_batch('srp_erp_warehouseitems', $result, 'warehouseItemsAutoID');
        }

        return $result;
    }

    /** CREDIT SALES - WAREHOUSE ITEM MASTER STOCK UPDATE */
    function update_warehouseItemMasterNewStock_credit_sales_Item_exceeded($shiftID)
    {
        $q = "SELECT
                warehouseitem.warehouseItemsAutoID,
                IF(warehouseitem.currentStock - SUM(
                    (
                        (itemdetail.qty * itemdetail.menuSalesQty) / (
                            getUoMConvertion (
                                itemdetail.UOMID,
                                itemmaster.defaultUnitOfMeasureID,
                                menusalesmaster.companyID
                            )
                        )
                    )
                )>0, warehouseitem.currentStock - SUM(
                    (
                        (itemdetail.qty * itemdetail.menuSalesQty) / (
                            getUoMConvertion (
                                itemdetail.UOMID,
                                itemmaster.defaultUnitOfMeasureID,
                                menusalesmaster.companyID
                            )
                        )
                    )
                ),0)  as currentStock 
            FROM
                srp_erp_pos_menusalesitemdetails itemdetail
            INNER JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
            LEFT JOIN srp_erp_warehouseitems warehouseitem ON warehouseitem.itemAutoID = itemmaster.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            AND menusalesmaster.wareHouseAutoID = warehouseitem.wareHouseAutoID AND menusalesmaster.isCreditSales = 1
            AND menusalesmaster.isHold = 0    
            AND menusalesmaster.isVoid = 0
            GROUP BY
                itemmaster.itemAutoID,
                menusalesmaster.wareHouseAutoID;";
        $result = $this->db->query($q)->result_array();

        /** New stock information */
        if (!empty($result)) {
            /**update new stock */
            $this->db->update_batch('srp_erp_warehouseitems', $result, 'warehouseItemsAutoID');
        }

        return $result;
    }


    /** CREDIT SALES - ITEM LEDGER
     * changes done by mushtaq discussed with Hisham
     * Updated on 2019-01-17 JIRA SME-1279
     */
    function update_itemLedger_credit_sales($shiftID, $isCreditSales, $menuSalesID, $documentMasterAutoID)
    {
        $outletID = get_outletID();
        $curDate = date('Y-m-d');
        $q = "SELECT
                    financeyear.companyFinanceYearID AS companyFinanceYearID,
                CONCAT(financeyear.beginingDate , \" - \" , financeyear.endingDate ) AS companyFinanceYear,
                financeyear.beginingDate AS FYBegin,
                financeyear.endingDate  AS FYEnd,
                financeperiod.dateFrom AS FYPeriodDateFrom,
                financeperiod.dateTo  AS FYPeriodDateTo
                
                FROM
                    srp_erp_companyfinanceyear financeyear
                INNER JOIN  srp_erp_companyfinanceperiod financeperiod  ON financeperiod.companyFinanceYearID = financeyear.companyFinanceYearID
                WHERE
                    financeyear.companyID = '" . current_companyID() . "'
                AND financeyear.isActive = 1
                AND financeyear.beginingDate < '" . $curDate . "'
                AND financeyear.endingDate > '" . $curDate . "'
                AND financeperiod.isActive =1
                AND financeperiod.dateFrom < '" . $curDate . "'
                AND financeperiod.dateTo > '" . $curDate . "'";
        $financeYear = $this->db->query($q)->row_array();

        $companyFinanceYearID = isset($financeYear['companyFinanceYearID']) ? $financeYear['companyFinanceYearID'] : null;
        $companyFinanceYear = isset($financeYear['companyFinanceYear']) ? $financeYear['companyFinanceYear'] : null;
        $FYBegin = isset($financeYear['FYBegin']) ? $financeYear['FYBegin'] : null;
        $FYEnd = isset($financeYear['FYEnd']) ? $financeYear['FYEnd'] : null;
        $FYPeriodDateFrom = isset($financeYear['FYPeriodDateFrom']) ? $financeYear['FYPeriodDateFrom'] : null;
        $FYPeriodDateTo = isset($financeYear['FYPeriodDateTo']) ? $financeYear['FYPeriodDateTo'] : null;


        $q = "INSERT INTO srp_erp_itemledger (
                documentID,
                documentAutoID,
                documentCode,
                documentSystemCode,
                documentDate,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                FYPeriodDateFrom,
                FYPeriodDateTo,
                wareHouseAutoID,
                wareHouseCode,
                wareHouseLocation,
                wareHouseDescription,
                itemAutoID,
                itemSystemCode,
                ItemSecondaryCode,
                itemDescription,
                defaultUOMID,
                defaultUOM,
                transactionUOMID,
                transactionUOM,
                transactionQTY,
                convertionRate,
                currentStock,
                PLGLAutoID,
                PLSystemGLCode,
                PLGLCode,
                PLDescription,
                PLType,
                BLGLAutoID,
                BLSystemGLCode,
                BLGLCode,
                BLDescription,
                BLType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalWacAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingWacAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                narration,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`
            )(
                SELECT
                    'CINV' AS documentID,
                    menusalesmaster.documentMasterAutoID AS documentAutoID,
                    'CINV' AS documentCode,
                    menusalesmaster.documentSystemCode AS documentSystemCode,
                    DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d')  AS documentDate,
                    '" . $companyFinanceYearID . "' AS companyFinanceYearID,
                    '" . $companyFinanceYear . "' AS companyFinanceYear,
                    '" . $FYBegin . "' AS FYBegin,
                    '" . $FYEnd . "' AS FYEnd,
                    '" . $FYPeriodDateFrom . "' AS FYPeriodDateFrom,
                    '" . $FYPeriodDateTo . "' AS FYPeriodDateTo,
                    warehousemaster.wareHouseAutoID AS wareHouseAutoID,
                    warehousemaster.wareHouseCode AS wareHouseCode,
                    warehousemaster.wareHouseLocation AS wareHouseLocation,
                    warehousemaster.wareHouseDescription AS wareHouseDescription,
                    itemdetail.itemAutoID AS itemAutoID,
                    itemmaster.itemSystemCode AS itemSystemCode,
                    itemmaster.seconeryItemCode AS seconeryItemCode,
                    itemmaster.itemDescription AS itemDescription,
                    itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
                    itemmaster.defaultUnitOfMeasure AS defaultUOM,
                    itemdetail.UOMID AS transactionUOMID,
                    itemdetail.UOM AS transactionUOM,
                    IF (
                    (
                        sum(
                            (
                                itemdetail.qty * itemdetail.menuSalesQty
                            ) / (
                                getUoMConvertion (
                                    itemdetail.UOMID,
                                    itemmaster.defaultUnitOfMeasureID,
                                    menusalesmaster.companyID
                                )
                            )
                        )
                    ) < itmlegr.itemLegQty,
                    (
                        sum(
                            (
                                itemdetail.qty * itemdetail.menuSalesQty
                            ) / (
                                getUoMConvertion (
                                    itemdetail.UOMID,
                                    itemmaster.defaultUnitOfMeasureID,
                                    menusalesmaster.companyID
                                )
                            )
                        )
                    ) *- 1,
                    itmlegr.itemLegQty *- 1
                ) AS transactionQTY,
                    getUoMConvertion (
                        itemdetail.UOMID,
                        itemmaster.defaultUnitOfMeasureID,
                        menusalesmaster.companyID
                    ) AS convertionRate,
                    itemmaster.currentStock AS currentStock,
                    itemmaster.costGLAutoID AS PLGLAutoID,
                    itemmaster.costSystemGLCode AS PLSystemGLCode,
                    itemmaster.costGLCode AS PLGLCode,
                    itemmaster.costDescription AS PLDescription,
                    itemmaster.costType AS PLType,
                    itemmaster.assteGLAutoID AS BLGLAutoID,
                    itemmaster.assteSystemGLCode AS BLSystemGLCode,
                    itemmaster.assteGLCode AS BLGLCode,
                    itemmaster.assteDescription AS BLDescription,
                    itemmaster.assteType AS BLType,
                    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    menusalesmaster.transactionCurrency AS transactionCurrency,
                    '1' AS transactionExchangeRate,
                    IF (
                            (
                                ifnull(
                                    sum(
                                        itemdetail.cost * itemdetail.menuSalesQty
                                    ),
                                    0
                                ) - IFNULL(itmexceed.amount, 0)
                            ) < 0,
                            0,
                            (
                                ifnull(
                                    sum(
                                        itemdetail.cost * itemdetail.menuSalesQty
                                    ),
                                    0
                                ) - IFNULL(itmexceed.amount, 0)
                            ) *- 1
                        ) AS transactionAmount,
                    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                    company.company_default_currencyID AS companyLocalCurrencyID,
                    company.company_default_currency AS companyLocalCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_default_currencyID,
                        menusalesmaster.companyID
                    ) AS companyLocalExchangeRate,
                    IF (
                            (
                                (
                                    ifnull(
                                        sum(
                                            itemdetail.cost * itemdetail.menuSalesQty
                                        ),
                                        0
                                    ) - IFNULL(itmexceed.amount, 0)
                                ) / getExchangeRate (
                                    menusalesmaster.transactionCurrencyID,
                                    company.company_default_currencyID,
                                    menusalesmaster.companyID
                                )
                            ) < 0,
                            0,
                            (
                                (
                                    ifnull(
                                        sum(
                                            itemdetail.cost * itemdetail.menuSalesQty
                                        ),
                                        0
                                    ) - IFNULL(itmexceed.amount, 0)
                                ) / getExchangeRate (
                                    menusalesmaster.transactionCurrencyID,
                                    company.company_default_currencyID,
                                    menusalesmaster.companyID
                                )
                            ) *- 1
                        ) AS companyLocalAmount,
                    itemmaster.companyLocalWacAmount AS companyLocalWacAmount,
                    getDecimalPlaces (
                        company.company_default_currencyID
                    ) AS companyLocalCurrencyDecimalPlaces,
                    company.company_reporting_currencyID AS companyReportingCurrencyID,
                    company.company_reporting_currency AS companyReportingCurrency,
                    getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingExchangeRate,
                    IF (
                            (
                                (
                                    ifnull(
                                        sum(
                                            itemdetail.cost * itemdetail.menuSalesQty
                                        ),
                                        0
                                    ) - IFNULL(itmexceed.amount, 0)
                                ) / getExchangeRate (
                                    menusalesmaster.transactionCurrencyID,
                                    company.company_reporting_currencyID,
                                    menusalesmaster.companyID
                                )
                            ) < 0,
                            0,
                            (
                                (
                                    ifnull(
                                        sum(
                                            itemdetail.cost * itemdetail.menuSalesQty
                                        ),
                                        0
                                    ) - IFNULL(itmexceed.amount, 0)
                                ) / getExchangeRate (
                                    menusalesmaster.transactionCurrencyID,
                                    company.company_reporting_currencyID,
                                    menusalesmaster.companyID
                                )
                            ) *- 1
                        ) AS companyReportingAmount,
                    itemmaster.companyLocalWacAmount / getExchangeRate (
                        menusalesmaster.transactionCurrencyID,
                        company.company_reporting_currencyID,
                        menusalesmaster.companyID
                    ) AS companyReportingWacAmount,
                    getDecimalPlaces (
                        company.company_reporting_currencyID
                    ) AS companyReportingCurrencyDecimalPlaces,
                    menusalesmaster.segmentID AS segmentID,
                    menusalesmaster.segmentCode AS segmentCode,
                    menusalesmaster.companyID AS companyID,
                    menusalesmaster.companyCode AS companyCode,
                    'POS Sales' AS narration,
                    shiftDetail.createdUserGroup AS createdUserGroup,
                    shiftDetail.createdPCID AS createdPCID,
                    shiftDetail.createdUserID AS createdUserID,
                    shiftDetail.startTime AS createdDateTime,
                    shiftDetail.createdUserName AS createdUserName,
                    CURRENT_TIMESTAMP () AS `timestamp`
                FROM
                    srp_erp_pos_menusalesitemdetails AS itemdetail
                LEFT JOIN srp_erp_itemmaster AS itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID AND menusalesmaster.wareHouseAutoID = itemdetail.warehouseAutoID
                LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
                LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
                LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
                LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
                LEFT JOIN (
	SELECT

	IF (
		SUM(
			transactionQTY / convertionRate
		) < 0,
		0,
		SUM(
			transactionQTY / convertionRate
		)
	) AS itemLegQty,
	wareHouseAutoID,
	itemAutoID
FROM
	srp_erp_itemledger
WHERE
	wareHouseAutoID = '" . $outletID . "'
GROUP BY
	itemAutoID,
	wareHouseAutoID
) itmlegr ON itmlegr.itemAutoID = itemdetail.itemAutoID


                LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS amount,
		IFNULL(SUM(exceededQty),0) AS excdqty,
		warehouseAutoID,
		assetGLAutoID,
		documentAutoID,
		itemAutoID

	FROM
		srp_erp_itemexceeded
	WHERE
		documentCode = 'CINV'
	AND documentAutoID = '" . $documentMasterAutoID . "'
	AND warehouseAutoID = '" . $outletID . "'
	GROUP BY
		documentAutoID,
		warehouseAutoID,
		itemAutoID
) itmexceed ON menusalesmaster.documentMasterAutoID= itmexceed.documentAutoID AND itemdetail.warehouseAutoID = itmexceed.warehouseAutoID AND itemdetail.itemAutoID = itmexceed.itemAutoID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "'
                    AND menusalesmaster.menuSalesID = '" . $menuSalesID . "'
                    AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND itemdetail.warehouseAutoID = '" . $outletID . "'
                    AND menusalesmaster.isHold = 0 ";
        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 1 ";
        }
        $q .= " AND menusalesmaster.isVoid = 0
                GROUP BY
                    itemdetail.itemAutoID, menusalesmaster.menuSalesID, itemdetail.UOMID
                    HAVING transactionQTY !=0
            )";
        $result = $this->db->query($q);
        return $result;
    }


    /** for manual Entries  : requested by Hisham to update the old entries  */
    function pos_credit_sales_entries_manual($shiftID)
    {

        $this->db->select('*');
        $this->db->from('srp_erp_generalledger');
        $this->db->where('documentCode', 'POSR');
        $this->db->where('documentMasterAutoID', $shiftID);
        $result = $this->db->get()->result_array();
        if (empty($result)) {
            /** 0. CUSTOMER INVOICE - Credit Sales Entries  */
            $this->pos_generate_invoices($shiftID);


            /** 1. CREDIT SALES  - REVENUE */
            $this->update_revenue_generalLedger_credit_sales($shiftID);
            /** 2. CREDIT SALES  - COGS */
            $this->update_cogs_generalLedger_credit_sales($shiftID);
            /** 3. CREDIT SALES  - INVENTORY */
            $this->update_inventory_generalLedger_credit_sales($shiftID);
            /** 4.  CREDIT SALES - TAX */
            $this->update_tax_generalLedger_credit_sales($shiftID);
            /** 5.  CREDIT SALES - COMMISSION EXPENSE  */
            $this->update_commissionExpense_generalLedger_credit_sales($shiftID);
            /** 6.  CREDIT SALES - COMMISSION PAYABLE */
            $this->update_commissionPayable_generalLedger_credit_sales($shiftID);
            /** 7.  CREDIT SALES - ROYALTY PAYABLE */
            $this->update_royaltyPayable_generalLedger_credit_sales($shiftID);
            /** 8.  CREDIT SALES - ROYALTY EXPENSES */
            $this->update_royaltyExpenses_generalLedger_credit_sales($shiftID);
            /** 9. CREDIT SALES -  SERVICE CHARGE */
            $this->update_serviceCharge_generalLedger_credit_sales($shiftID);
            /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS */

            /** CREDIT SALES - ITEM LEDGER  */
            $this->update_itemLedger_credit_sales($shiftID);

            echo 'Run Successfully!<br/>' . date('Y-m-d H:i:s');
        } else {

            echo 'Record already exist!<br/>' . date('Y-m-d H:i:s');
        }


    }


    /** item Exceeded */

    /** 3. COGS */
    function update_cogs_generalLedger_itemExceeded($shiftID, $isCreditSales = true, $log = false)
    {
        if ($log) {
            echo '<strong>update_cogs_generalLedger</strong><br/>';

        }
        $q = "INSERT INTO srp_erp_generalledger (
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                `timestamp`
            )
            
            (SELECT
								'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - COGS' AS documentNarration,
                '' AS chequeNumber,
                item.costGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'dr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
                ((sum( IFNULL(item.cost,0) *   item.menuSalesQty  )) - SUM( IFNULL(ie.transactionAmount,0))) AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                  ((sum( IFNULL(item.cost,0) *   item.menuSalesQty  )) - SUM( IFNULL(ie.transactionAmount,0))) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                  ((sum( IFNULL(item.cost,0) *   item.menuSalesQty  )) - SUM( IFNULL(ie.transactionAmount,0))) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP() AS  `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.costGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
						LEFT JOIN srp_erp_itemexceeded ie ON ie.documentAutoID = '" . $shiftID . "' AND ie.documentCode = 'POSR' AND ie.costGLAutoID =  item.costGLAutoID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            AND menusalesmaster.isHold = 0   AND menusalesmaster.isVoid = 0";

        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 0";
        }
        $q .= " GROUP BY
                item.costGLAutoID)";

        $result = $this->db->query($q);
        if ($log) {
            echo $q . '<br/><br/>';

        }
        return $result;
    }

    /** 4. INVENTORY */
    function update_inventory_generalLedger_itemExceeded($shiftID, $isCreditSales = true, $log = false)
    {
        if ($log) {
            echo '<strong>update_inventory_generalLedger</strong><br/>';
        }
        $q = "INSERT INTO srp_erp_generalledger(
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp
            )
            (SELECT
                'POSR' AS documentCode,
                menusalesmaster.shiftID AS documentMasterAutoID,
                concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
                DATE_FORMAT( shiftDetail.startTime, '%Y-%m-%d') AS documentdate,
                YEAR (curdate()) AS documentYear,
                MONTH (curdate()) AS documentMonth,
                'POS Sales - Inventory' AS documentNarration,
                '' AS chequeNumber,
                item.assetGLAutoID AS GLAutoID,
                chartOfAccount.systemAccountCode AS systemGLCode,
                chartOfAccount.GLSecondaryCode AS GLCode,
                chartOfAccount.GLDescription AS GLDescription,
                chartOfAccount.subCategory AS GLType,
                'cr' AS amount_type,
                '0' AS isFromItem,
                menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                menusalesmaster.transactionCurrency AS transactionCurrency,
                '1' AS transactionExchangeRate,
								abs((sum( IFNULL(item.cost,0) *   item.menuSalesQty  )) - SUM( IFNULL(ie.transactionAmount,0))) * -1 AS transactionAmount,
                currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                company.company_default_currencyID AS companyLocalCurrencyID,
                company.company_default_currency AS companyLocalCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
                ( abs((sum( IFNULL(item.cost,0) *   item.menuSalesQty  )) - SUM( IFNULL(ie.transactionAmount,0))) * -1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
                getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
                company.company_reporting_currencyID AS companyReportingCurrencyID,
                company.company_reporting_currency AS companyReportingCurrency,
                getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
                ( abs((sum( IFNULL(item.cost,0) *   item.menuSalesQty  )) - SUM( IFNULL(ie.transactionAmount,0))) * -1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
                getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
                menusalesmaster.segmentID AS segmentID,
                menusalesmaster.segmentCode AS segmentCode,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                menusalesmaster.createdUserGroup AS createdUserGroup,
                menusalesmaster.createdPCID AS createdPCID,
                menusalesmaster.createdUserID AS createdUserID,
                shiftDetail.startTime AS createdDateTime,
                menusalesmaster.createdUserName AS createdUserName,
                NULL AS modifiedPCID,
                NULL AS modifiedUserID,
                NULL AS modifiedDateTime,
                NULL AS modifiedUserName,
                CURRENT_TIMESTAMP () `timestamp`
            FROM
                srp_erp_pos_menusalesitemdetails item
            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
            LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
            LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
            LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
            LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
            LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
						LEFT JOIN srp_erp_itemexceeded ie ON ie.documentAutoID = '" . $shiftID . "' AND ie.documentCode = 'POSR' AND ie.assetGLAutoID =  item.assetGLAutoID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            AND menusalesmaster.isHold = 0   AND menusalesmaster.isVoid = 0";
        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 0";
        }
        $q .= " GROUP BY
                item.assetGLAutoID)";

        if ($log) {
            echo $q . '<br/><br/>';
        }
        $result = $this->db->query($q);
        return $result;
    }

    /** ITEM MASTER STOCK UPDATE */
    function update_itemMasterNewStock_itemExceeded($shiftID)
    {
        $q = "SELECT
                    itemmaster.itemAutoID,
                    -- menusalesmaster.wareHouseAutoID,
                    -- itemmaster.currentStock,
                    -- sum(itemdetail.qty*itemdetail.menuSalesQty) as qty,
                    -- itemdetail.UOMID,
                    -- itemmaster.defaultUnitOfMeasure,
                    -- itemdetail.UOM,
                    -- getUoMConvertion (
                    -- 	itemdetail.UOMID,
                    -- 	itemmaster.defaultUnitOfMeasureID,
                    -- 	menusalesmaster.companyID
                    -- ) AS convertionRate,
                    -- SUM(
                    -- 	(itemdetail.qty * item.qty) / (
                    -- 		getUoMConvertion (
                    -- 			itemdetail.UOMID,
                    -- 			itemmaster.defaultUnitOfMeasureID,
                    -- 			menusalesmaster.companyID
                    -- 		)
                    -- 	)
                    -- ) AS usedStock, 
                 
                      
            
                IF( (itemmaster.currentStock - SUM(
                        (
                            (
                                itemdetail.qty * itemdetail.menuSalesQty
                            ) / (
                                getUoMConvertion (
                                    itemdetail.UOMID,
                                    itemmaster.defaultUnitOfMeasureID,
                                    menusalesmaster.companyID
                                )
                            )
                        )
                    )) >0 , itemmaster.currentStock - SUM(
                        (
                            (
                                itemdetail.qty * itemdetail.menuSalesQty
                            ) / (
                                getUoMConvertion (
                                    itemdetail.UOMID,
                                    itemmaster.defaultUnitOfMeasureID,
                                    menusalesmaster.companyID
                                )
                            )
                        )
                    ) , 0) 
                
                 as currentStock
                FROM
                    srp_erp_pos_menusalesitemdetails itemdetail
                INNER JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID #LEFT JOIN srp_erp_pos_menusalesitems item ON item.menuSalesItemID = itemdetail.menuSalesItemID
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
                WHERE
                    menusalesmaster.shiftID = '" . $shiftID . "' AND menusalesmaster.isCreditSales = 0
                    AND menusalesmaster.isHold = 0    
                    AND menusalesmaster.isVoid = 0
                GROUP BY
                    itemmaster.itemAutoID";
        $result = $this->db->query($q)->result_array();

        /** New stock information */
        if (!empty($result)) {
            /**update new stock */
            $this->db->update_batch('srp_erp_itemmaster', $result, 'itemAutoID');
        }

        return $result;
    }

    /** WAREHOUSE ITEM MASTER STOCK UPDATE */
    function update_warehouseItemMasterNewStock_itemExceeded($shiftID)
    {
        $q = "SELECT
                warehouseitem.warehouseItemsAutoID,
                -- itemdetail.itemAutoID,
             
            IF( (warehouseitem.currentStock - SUM(
                    (
                        (itemdetail.qty * itemdetail.menuSalesQty) / (
                            getUoMConvertion (
                                itemdetail.UOMID,
                                itemmaster.defaultUnitOfMeasureID,
                                menusalesmaster.companyID
                            )
                        )
                    )
                ) ) >0 , warehouseitem.currentStock - SUM(
                    (
                        (itemdetail.qty * itemdetail.menuSalesQty) / (
                            getUoMConvertion (
                                itemdetail.UOMID,
                                itemmaster.defaultUnitOfMeasureID,
                                menusalesmaster.companyID
                            )
                        )
                    )
                )  , 0 ) as currentStock 
            FROM
                srp_erp_pos_menusalesitemdetails itemdetail
            INNER JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
            LEFT JOIN srp_erp_warehouseitems warehouseitem ON warehouseitem.itemAutoID = itemmaster.itemAutoID
            LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
            WHERE
                menusalesmaster.shiftID = '" . $shiftID . "'
            AND menusalesmaster.wareHouseAutoID = warehouseitem.wareHouseAutoID AND menusalesmaster.isCreditSales = 0
            AND menusalesmaster.isHold = 0    
            AND menusalesmaster.isVoid = 0
            GROUP BY
                itemmaster.itemAutoID,
                menusalesmaster.wareHouseAutoID;";
        $result = $this->db->query($q)->result_array();

        /** New stock information */
        if (!empty($result)) {
            /**update new stock */
            $this->db->update_batch('srp_erp_warehouseitems', $result, 'warehouseItemsAutoID');
        }

        return $result;
    }

    function creditSales_adjust_inventory($shiftID)
    {

        /** item exceeded */
        $q = "SELECT * FROM srp_erp_itemexceeded  WHERE documentAutoID = '" . $shiftID . "'  AND isFromCreditSales =1";
        $result = $this->db->query($q)->result_array();

        $remainingAmount = 0;

        if (!empty($result)) {
            $data = array();
            $i = 0;
            foreach ($result as $itemExceeded) {

                $totalTransactionAmount = $itemExceeded['transactionAmount'];

                /** Adjust Inventory - Credit Sales Inventory */
                $query_generalLedger_credit_sales = "SELECT
                gl.generalLedgerAutoID, gl.GLAutoID, abs(gl.transactionAmount) as transAmount, gl.companyLocalExchangeRate as companyLocalExchangeRate, gl.companyReportingExchangeRate as companyReportingExchangeRate  
            FROM
                srp_erp_generalledger gl
                LEFT JOIN srp_erp_pos_menusalesmaster msm ON msm.documentMasterAutoID = gl.documentMasterAutoID 
            WHERE
                msm.shiftID = '" . $shiftID . "' 
                AND msm.isCreditSales = 1
                AND gl.documentNarration LIKE 'POS Credit Sales - Inventory%' 
                AND  gl.GLAutoID = '" . $itemExceeded['assetGLAutoID'] . "'  
                ORDER BY  gl.GLAutoID ";
                $glResult = $this->db->query($query_generalLedger_credit_sales)->result_array();
                if ($glResult) {

                    foreach ($glResult as $glValue) {
                        $data[$i]['generalLedgerAutoID'] = $glValue['generalLedgerAutoID'];
                        $remainingAmount = $glValue['transAmount'] - $totalTransactionAmount;
                        $transactionAmount = $remainingAmount > 0 ? $remainingAmount : 0;
                        $data[$i]['transactionAmount'] = $transactionAmount * -1;
                        $data[$i]['companyReportingAmount'] = ($transactionAmount * $glValue['companyReportingExchangeRate']) * -1;
                        $data[$i]['companyLocalAmount'] = ($transactionAmount * $glValue['companyLocalExchangeRate']) * -1;
                        $data[$i]['companyLocalAmount'] = ($transactionAmount * $glValue['companyLocalExchangeRate']) * -1;
                        $data[$i]['is_sync'] = 0;

                        $totalTransactionAmount = $totalTransactionAmount - $glValue['transAmount'];
                        $i++;
                    }
                }

            }

            if (!empty($data)) {
                $this->db->update_batch('srp_erp_generalledger', $data, 'generalLedgerAutoID');
            }
        }

    }

    function creditSales_adjust_cogs($shiftID)
    {

        /** item exceeded */
        $q = "SELECT * FROM srp_erp_itemexceeded  WHERE documentAutoID = '" . $shiftID . "'  AND isFromCreditSales =1";
        $result = $this->db->query($q)->result_array();

        $remainingAmount = 0;

        if (!empty($result)) {
            $data = array();
            $i = 0;
            foreach ($result as $itemExceeded) {

                $totalTransactionAmount = $itemExceeded['transactionAmount'];

                /** Adjust Inventory - Credit Sales Inventory */
                $query_generalLedger_credit_sales = "SELECT
                gl.generalLedgerAutoID, gl.GLAutoID, abs(gl.transactionAmount) as transAmount, gl.companyLocalExchangeRate, gl.companyReportingExchangeRate  
            FROM
                srp_erp_generalledger gl
                LEFT JOIN srp_erp_pos_menusalesmaster msm ON msm.documentMasterAutoID = gl.documentMasterAutoID 
            WHERE
                msm.shiftID = '" . $shiftID . "' 
                AND msm.isCreditSales = 1
                AND gl.documentNarration LIKE 'POS Credit Sales - COGS%' 
                AND  gl.GLAutoID = '" . $itemExceeded['costGLAutoID'] . "'  
                ORDER BY  gl.GLAutoID ";
                $glResult = $this->db->query($query_generalLedger_credit_sales)->result_array();
                if ($glResult) {

                    foreach ($glResult as $glValue) {
                        $data[$i]['generalLedgerAutoID'] = $glValue['generalLedgerAutoID'];
                        $remainingAmount = $glValue['transAmount'] - $totalTransactionAmount;
                        $transactionAmount = $remainingAmount > 0 ? $remainingAmount : 0;
                        $data[$i]['transactionAmount'] = $transactionAmount;
                        $data[$i]['companyReportingAmount'] = $transactionAmount * $glValue['companyReportingExchangeRate'];
                        $data[$i]['companyLocalAmount'] = $transactionAmount * $glValue['companyLocalExchangeRate'];
                        $data[$i]['is_sync'] = 0;

                        $totalTransactionAmount = $totalTransactionAmount - $glValue['transAmount'];
                        $i++;
                    }
                }

            }

            if (!empty($data)) {
                $this->db->update_batch('srp_erp_generalledger', $data, 'generalLedgerAutoID');
            }
        }

    }

    function creditSales_adjust_item_master($shiftID)
    {
        $q = "SELECT
                    menuSalesID,
                    documentSystemCode,
                    documentMasterAutoID,
                    shiftID 
                FROM
                    srp_erp_pos_menusalesmaster 
                WHERE
                    shiftID = '" . $shiftID . "' 
                    AND isCreditSales = 1";
        $bills = $this->db->query($q)->result_array();
        if (!empty($bills)) {
            $i = 0;
            foreach ($bills as $bill) {


                $documentMasterAutoID = $bill['documentMasterAutoID'];
                $q = "SELECT * FROM srp_erp_itemexceeded  WHERE documentAutoID = '" . $documentMasterAutoID . "'  AND isFromCreditSales =1";
                $itemExceeded = $this->db->query($q)->result_array();

                /** Adjust Item Ledger - Credit Sales */
                $query_itemLedger_credit_sales = "select * from  srp_erp_itemledger WHERE documentAutoID = '" . $documentMasterAutoID . "'";
                $itemLedger_Result = $this->db->query($query_itemLedger_credit_sales)->result_array();

                if ($itemLedger_Result) {
                    foreach ($itemLedger_Result as $itemLedger) {

                        if (!empty($itemExceeded)) {
                            foreach ($itemExceeded as $item) {
                                if ($item['itemAutoID'] == $itemLedger['itemAutoID']) {
                                    $data[$i]['itemLedgerAutoID'] = $itemLedger['itemLedgerAutoID'];

                                    if ($itemLedger['convertionRate'] > 0) {
                                        $transactionQTY = ($itemLedger['transactionQTY'] / $itemLedger['convertionRate']) * -1;
                                    } else {
                                        $transactionQTY = $itemLedger['transactionQTY'] * -1;
                                    }

                                    $new_transactionQTY = $transactionQTY - $item['exceededQty'];
                                    $data[$i]['transactionQTY'] = $new_transactionQTY != 0 ? $new_transactionQTY * -1 : 0;

                                    $transactionAmount = $itemLedger['transactionAmount'] * -1;
                                    $new_transactionAmount = $transactionAmount - $item['transactionAmount'];
                                    $data[$i]['transactionAmount'] = $new_transactionAmount != 0 ? $new_transactionAmount * -1 : 0;

                                    $data[$i]['companyReportingAmount'] = $new_transactionAmount != 0 ? ($new_transactionAmount * $itemLedger['companyReportingExchangeRate']) * -1 : 0;
                                    $data[$i]['companyLocalAmount'] = $new_transactionAmount != 0 ? ($new_transactionAmount * $itemLedger['companyLocalExchangeRate']) * -1 : 0;
                                    $data[$i]['is_sync'] = 0;

                                }
                            }
                        }
                        $i++;
                    }
                }
            }
        }

        if (!empty($data)) {
            /**update Item Ledger */
            $this->db->update_batch('srp_erp_itemledger', $data, 'itemLedgerAutoID');
        }
    }

    function insert_items_notExist_inWarehouseItem($shiftID)
    {
        $outletID = get_outletID();
        $q = "INSERT INTO srp_erp_warehouseitems ( wareHouseAutoID, wareHouseLocation, wareHouseDescription, itemAutoID, itemSystemCode, itemDescription, unitOfMeasureID, unitOfMeasure, currentStock, companyID, companyCode, `timestamp` ) (
            SELECT
                menusalesmaster.wareHouseAutoID AS wareHouseAutoID,
                w.wareHouseLocation AS wareHouseLocation,
                w.wareHouseDescription AS wareHouseDescription,
                itemmaster.itemAutoID AS itemAutoID,
                itemmaster.itemSystemCode AS itemSystemCode,
                itemmaster.itemDescription AS itemDescription,
                itemmaster.defaultUnitOfMeasureID AS unitOfMeasureID,
                itemmaster.defaultUnitOfMeasure AS unitOfMeasure,
                0 AS currentStock,
                menusalesmaster.companyID AS companyID,
                menusalesmaster.companyCode AS companyCode,
                CURDATE( ) AS `timestamp` 
            FROM
                srp_erp_pos_menusalesitemdetails itemdetail
                INNER JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = itemdetail.itemAutoID
                LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = itemdetail.menuSalesID
                LEFT JOIN srp_erp_warehousemaster w ON w.wareHouseAutoID = menusalesmaster.wareHouseAutoID 
            WHERE
              NOT EXISTS ( 
                  SELECT * FROM srp_erp_warehouseitems 
                  WHERE 
                  srp_erp_warehouseitems.companyID = menusalesmaster.companyID 
                  AND srp_erp_warehouseitems.wareHouseAutoID = menusalesmaster.wareHouseAutoID 
                  AND srp_erp_warehouseitems.itemAutoID = itemdetail.itemAutoID ) 
              AND menusalesmaster.shiftID = '" . $shiftID . "'  
              AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
              AND itemdetail.wareHouseAutoID = '" . $outletID . "'
            GROUP BY
                itemmaster.itemAutoID 
                )";
        return $this->db->query($q);
    }

    function update_exceededGL_generalLedger($shiftID, $isCreditSales = true, $log = false)
    {
        $outletID = get_outletID();
        $companyID = current_companyID();
        $exceedglid = $this->db->query("SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE companyID =$companyID AND controlAccountType = 'IEXC'")->row_array();
        $exceedGlAutoID = $exceedglid['GLAutoID'];
        if ($log) {
            echo '<strong>update_exceededGL_generalLedger</strong><br/>';
        }
        $q = "INSERT INTO srp_erp_generalledger(
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp
            )
            (SELECT
	shiftDetail.wareHouseID,
	'POSR' AS documentCode,
	shiftDetail.shiftID AS documentMasterAutoID,
	exceed.documentSystemCode,
	DATE_FORMAT(
		shiftDetail.startTime,
		'%Y-%m-%d'
	) AS documentdate,
	YEAR (curdate()) AS documentYear,
	MONTH (curdate()) AS documentMonth,
	'POS Sales - Exceeded' AS documentNarration,
	'' AS chequeNumber,
	'" . $exceedGlAutoID . "' AS GLAutoID,
	chartOfAccount.systemAccountCode AS systemGLCode,
	chartOfAccount.GLSecondaryCode AS GLCode,
	chartOfAccount.GLDescription AS GLDescription,
	chartOfAccount.subCategory AS GLType,
	'cr' AS amount_type,
	'0' AS isFromItem,
	menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
	menusalesmaster.transactionCurrency AS transactionCurrency,
	'1' AS transactionExchangeRate,
	abs(
		(
			IFNULL(exceed.transactionAmount, 0)
		)
	) *- 1 AS transactionAmount,
	currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
	company.company_default_currencyID AS companyLocalCurrencyID,
	company.company_default_currency AS companyLocalCurrency,
	getExchangeRate (
		menusalesmaster.transactionCurrencyID,
		company.company_default_currencyID,
		menusalesmaster.companyID
	) AS companyLocalExchangeRate,
	(
		abs(
			(
				IFNULL(exceed.transactionAmount, 0)
			)
		) *- 1
	) / (
		getExchangeRate (
			menusalesmaster.transactionCurrencyID,
			company.company_default_currencyID,
			menusalesmaster.companyID
		)
	) AS companyLocalAmount,
	getDecimalPlaces (
		company.company_default_currencyID
	) AS companyLocalCurrencyDecimalPlaces,
	company.company_reporting_currencyID AS companyReportingCurrencyID,
	company.company_reporting_currency AS companyReportingCurrency,
	getExchangeRate (
		menusalesmaster.transactionCurrencyID,
		company.company_reporting_currencyID,
		menusalesmaster.companyID
	) AS companyReportingExchangeRate,
	(
		abs(
			(
				IFNULL(exceed.transactionAmount, 0)
			)
		) *- 1
	) / (
		getExchangeRate (
			menusalesmaster.transactionCurrencyID,
			company.company_reporting_currencyID,
			menusalesmaster.companyID
		)
	) AS companyReportingAmount,
	getDecimalPlaces (
		company.company_reporting_currencyID
	) AS companyReportingCurrencyDecimalPlaces,
	menusalesmaster.segmentID AS segmentID,
	menusalesmaster.segmentCode AS segmentCode,
	menusalesmaster.companyID AS companyID,
	menusalesmaster.companyCode AS companyCode,
	menusalesmaster.createdUserGroup AS createdUserGroup,
	menusalesmaster.createdPCID AS createdPCID,
	menusalesmaster.createdUserID AS createdUserID,
	shiftDetail.startTime AS createdDateTime,
	menusalesmaster.createdUserName AS createdUserName,
	NULL AS modifiedPCID,
	NULL AS modifiedUserID,
	NULL AS modifiedDateTime,
	NULL AS modifiedUserName,
	CURRENT_TIMESTAMP () `timestamp`
FROM
	srp_erp_pos_shiftdetails shiftDetail
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		IFNULL(SUM(exceededQty), 0) AS excdqty,
		warehouseAutoID,
		documentAutoID,
		documentSystemCode,
		itemAutoID
	FROM
		srp_erp_itemexceeded
	WHERE
		 documentCode = 'POSR'
	AND warehouseAutoID = '" . $outletID . "'
	GROUP BY
		documentAutoID,
		warehouseAutoID
) exceed ON shiftDetail.shiftID = exceed.documentAutoID AND shiftDetail.wareHouseID = exceed.warehouseAutoID
LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = '" . $exceedGlAutoID . "'
LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.shiftID = shiftDetail.shiftID
AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
WHERE
	shiftDetail.shiftID = '" . $shiftID . "'
AND exceed.wareHouseAutoID = '" . $outletID . "' ";

        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 0";
        }
        $q .= " GROUP BY
 shiftDetail.shiftID,shiftDetail.wareHouseID HAVING transactionAmount !=0)";
        $result = $this->db->query($q);
        return $result;
    }

    function update_exceededGL_generalLedger_credit_sales($shiftID, $isCreditSales = true, $log = false)
    {
        $outletID = get_outletID();
        $companyID = current_companyID();
        $exceedglid = $this->db->query("SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE companyID =$companyID AND controlAccountType = 'IEXC'")->row_array();
        $exceedGlAutoID = $exceedglid['GLAutoID'];
        if ($log) {
            echo '<strong>update_exceededGL_generalLedger_credit_sales</strong><br/>';
        }
        $q = "INSERT INTO srp_erp_generalledger(
                wareHouseAutoID,
                documentCode,
                documentMasterAutoID,
                documentSystemCode,
                documentDate,
                documentYear,
                documentMonth,
                documentNarration,
                chequeNumber,
                GLAutoID,
                systemGLCode,
                GLCode,
                GLDescription,
                GLType,
                amount_type,
                isFromItem,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                modifiedPCID,
                modifiedUserID,
                modifiedDateTime,
                modifiedUserName,
                timestamp
            )
            (SELECT
	shiftDetail.wareHouseID,
	'CINV' AS documentCode,
	menusalesmaster.documentMasterAutoID AS documentMasterAutoID,
	exceed.documentSystemCode,
	DATE_FORMAT(
		shiftDetail.startTime,
		'%Y-%m-%d'
	) AS documentdate,
	YEAR (curdate()) AS documentYear,
	MONTH (curdate()) AS documentMonth,
	'POS Credit Sales - Exceeded' AS documentNarration,
	'' AS chequeNumber,
	'" . $exceedGlAutoID . "' AS GLAutoID,
	chartOfAccount.systemAccountCode AS systemGLCode,
	chartOfAccount.GLSecondaryCode AS GLCode,
	chartOfAccount.GLDescription AS GLDescription,
	chartOfAccount.subCategory AS GLType,
	'cr' AS amount_type,
	'0' AS isFromItem,
	menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
	menusalesmaster.transactionCurrency AS transactionCurrency,
	'1' AS transactionExchangeRate,
	abs(
		(
			IFNULL(exceed.transactionAmount, 0)
		)
	) *- 1 AS transactionAmount,
	currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
	company.company_default_currencyID AS companyLocalCurrencyID,
	company.company_default_currency AS companyLocalCurrency,
	getExchangeRate (
		menusalesmaster.transactionCurrencyID,
		company.company_default_currencyID,
		menusalesmaster.companyID
	) AS companyLocalExchangeRate,
	(
		abs(
			(
				IFNULL(exceed.transactionAmount, 0)
			)
		) *- 1
	) / (
		getExchangeRate (
			menusalesmaster.transactionCurrencyID,
			company.company_default_currencyID,
			menusalesmaster.companyID
		)
	) AS companyLocalAmount,
	getDecimalPlaces (
		company.company_default_currencyID
	) AS companyLocalCurrencyDecimalPlaces,
	company.company_reporting_currencyID AS companyReportingCurrencyID,
	company.company_reporting_currency AS companyReportingCurrency,
	getExchangeRate (
		menusalesmaster.transactionCurrencyID,
		company.company_reporting_currencyID,
		menusalesmaster.companyID
	) AS companyReportingExchangeRate,
	(
		abs(
			(
				IFNULL(exceed.transactionAmount, 0)
			)
		) *- 1
	) / (
		getExchangeRate (
			menusalesmaster.transactionCurrencyID,
			company.company_reporting_currencyID,
			menusalesmaster.companyID
		)
	) AS companyReportingAmount,
	getDecimalPlaces (
		company.company_reporting_currencyID
	) AS companyReportingCurrencyDecimalPlaces,
	menusalesmaster.segmentID AS segmentID,
	menusalesmaster.segmentCode AS segmentCode,
	menusalesmaster.companyID AS companyID,
	menusalesmaster.companyCode AS companyCode,
	menusalesmaster.createdUserGroup AS createdUserGroup,
	menusalesmaster.createdPCID AS createdPCID,
	menusalesmaster.createdUserID AS createdUserID,
	shiftDetail.startTime AS createdDateTime,
	menusalesmaster.createdUserName AS createdUserName,
	NULL AS modifiedPCID,
	NULL AS modifiedUserID,
	NULL AS modifiedDateTime,
	NULL AS modifiedUserName,
	CURRENT_TIMESTAMP () `timestamp`
FROM
	srp_erp_pos_shiftdetails shiftDetail

LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = '" . $exceedGlAutoID . "'
LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.shiftID = shiftDetail.shiftID
AND menusalesmaster.wareHouseAutoID = '" . $outletID . "'

LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		IFNULL(SUM(exceededQty), 0) AS excdqty,
		warehouseAutoID,
		documentAutoID,
		documentSystemCode,
		itemAutoID
	FROM
		srp_erp_itemexceeded
	WHERE
		 documentCode = 'CINV'
	AND warehouseAutoID = '" . $outletID . "'
	GROUP BY
		documentAutoID,
		warehouseAutoID
) exceed ON menusalesmaster.documentMasterAutoID = exceed.documentAutoID AND menusalesmaster.wareHouseAutoID = exceed.warehouseAutoID

LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
WHERE
	shiftDetail.shiftID = '" . $shiftID . "'
AND exceed.wareHouseAutoID = '" . $outletID . "' ";

        if ($isCreditSales) {
            $q .= " AND menusalesmaster.isCreditSales = 1";
        }
        $q .= " GROUP BY
 menusalesmaster.menuSalesID,menusalesmaster.wareHouseAutoID
HAVING
	transactionAmount != 0)";
        $result = $this->db->query($q);
        return $result;
    }


    /** UPDATE Exceeded item
     * function  : update_itemExceededRecord_fromReview
     * function created by mushtaq discussed with Hisham
     * Created on 2019-04-8 JIRA SME-1603
     */
    function update_itemExceededRecord_fromReview($shiftID, $isCreditSales = false)
    {
        if ($isCreditSales) {
            $creditSales = 1;
        } else {
            $creditSales = 0;
        }

        $outletID = get_outletID();

        $companyID = $this->common_data['company_data']['company_id'];
        $exceedglid = $this->db->query("SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE companyID =$companyID AND controlAccountType = 'IEXC'")->row_array();
        $exceedGlAutoID = $exceedglid['GLAutoID'];
        $q = "INSERT INTO srp_erp_itemexceeded (
                documentCode,
                documentAutoID,
                documentSystemCode,
                itemAutoID,
                warehouseAutoID,
                assetGLAutoID,
                costGLAutoID,
                exceededQty,
                updatedQty,
                balanceQty,
                defaultUOMID,
                defaultUOM,
                unitOfMeasureID,
                unitOfMeasure,
                conversionRateUOM,
                unitCost,
                isFromCreditSales,
                transactionAmount,
                companyLocalAmount,
                companyReportingAmount,
                documentDate,
                companyID,
                segmentID,
                segmentCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`,
                exceededGLAutoID
                ) (
                SELECT
	'POSR' AS documentCode,
	menusalesmaster.shiftID AS documentAutoID,
	concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
	item.itemAutoID AS itemAutoID,
	warehousemaster.wareHouseAutoID AS warehouseAutoID,
	menusalesitemdetails.assetGLAutoID AS assetGLAutoID,
	menusalesitemdetails.costGLAutoID AS costGLAutoID,		
	(
	sum(
	( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) / getUoMConvertion ( menusalesitemdetails.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID ) 
	) - IFNULL( warehouseitems.currentStock, 0 ) 
	) AS exceededQty,
	0 AS updatedQty,
	(
	sum(
	( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) / getUoMConvertion ( menusalesitemdetails.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID ) 
	) - IFNULL( warehouseitems.currentStock, 0 ) 
	) AS balanceQty,
	itemmaster.defaultUnitOfMeasureID AS defaultUOMID,
	itemmaster.defaultUnitOfMeasure AS defaultUOM,
	itemmaster.defaultUnitOfMeasureID AS unitOfMeasureID,
	itemmaster.defaultUnitOfMeasure AS unitOfMeasure,
	1 AS conversionRateUOM,
	(
	abs(
	IFNULL( ( SUM( SUBSTRING(menusalesitemdetails.cost,1,16)) / sum( menusalesitemdetails.qty ) ), 0 ) * (
	sum( ( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) ) - IFNULL( warehouseitems.currentStock, 0 ) 
	) 
	) / (
	sum(
	( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) / getUoMConvertion ( menusalesitemdetails.UOMID, itemmaster.defaultUnitOfMeasureID, item.companyID ) 
	) - IFNULL( warehouseitems.currentStock, 0 ) 
	) 
	) AS unitCost,
	'".$creditSales."' AS isFromCreditSales,
	abs(
	IFNULL( ( SUM( SUBSTRING(menusalesitemdetails.cost,1,16)) / sum( menusalesitemdetails.qty ) ), 0 ) * (
	sum( ( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) ) - IFNULL( warehouseitems.currentStock, 0 ) 
	) 
	) AS transactionAmount,
	abs(
	IFNULL( ( SUM( SUBSTRING(menusalesitemdetails.cost,1,16)) / sum( menusalesitemdetails.qty ) ), 0 ) * (
	sum( ( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) ) - IFNULL( warehouseitems.currentStock, 0 ) 
	) 
	) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
	abs(
	IFNULL( ( SUM( SUBSTRING(menusalesitemdetails.cost,1,16)) / sum( menusalesitemdetails.qty ) ), 0 ) * (
	sum( ( IFNULL( menusalesitemdetails.menuSalesQty, 0 ) * menusalesitemdetails.qty ) ) - IFNULL( warehouseitems.currentStock, 0 ) 
	) 
	) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
	shiftDetail.startTime AS documentDate,
	menusalesmaster.companyID AS companyID,
	segmentConfig.segmentID AS segmentID,
	segmentConfig.segmentCode AS segmentCode,
	menusalesmaster.createdUserGroup AS createdUserGroup,
	menusalesmaster.createdPCID AS createdPCID,
	menusalesmaster.createdUserID AS createdUserID,
	NOW( ) AS createdDateTime,
	menusalesmaster.createdUserName AS createdUserName,
	CURRENT_TIMESTAMP ( ) AS `timestamp`,
	'".$exceedGlAutoID."' AS exceedGLAutoID 
FROM
srp_erp_itemledger_review item	
	LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
	LEFT JOIN (
SELECT
IF
	( SUM( transactionQTY / convertionRate ) < 0, 0, SUM( transactionQTY / convertionRate ) ) AS currentStock,
	wareHouseAutoID,
	itemAutoID 
FROM
	srp_erp_itemledger 
WHERE
	wareHouseAutoID = $outletID 
GROUP BY
	itemAutoID,
	wareHouseAutoID 
	) warehouseitems ON warehouseitems.itemAutoID = item.itemAutoID
	LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.pos_menusalesID 
	AND menusalesmaster.wareHouseAutoID = item.warehouseAutoID
	LEFT JOIN srp_erp_pos_menusalesitemdetails menusalesitemdetails ON menusalesitemdetails.menuSalesItemDetailID=item.pos_menusalesItemDetailID
	LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
	LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
	LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
	LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitID = menusalesitemdetails.UOMID 
	AND uom.companyID = menusalesmaster.companyID
	LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
	LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID 
	AND shiftDetail.wareHouseID = menusalesmaster.wareHouseAutoID
	LEFT JOIN srp_erp_pos_segmentconfig segmentConfig ON segmentConfig.wareHouseAutoID = warehousemaster.wareHouseAutoID 
WHERE
	menusalesmaster.shiftID = $shiftID
	AND menusalesmaster.isHold = 0 
	AND menusalesmaster.isVoid = 0 
	AND menusalesmaster.isCreditSales = $creditSales 
  AND warehousemaster.wareHouseAutoID = $outletID 
GROUP BY
	itemAutoID 
HAVING
	exceededQty > 0 
ORDER BY
	documentAutoID DESC
                    )";

        $result = $this->db->query($q);
        return $result;
    }




    /** End New GL Entries Review */

    /******* Insert item ledger from item ledger review ****/
    function update_itemLedger_fromReview($shiftID, $menuSalesID = 0, $isCreditSales = 0)
    {
        $outletID = get_outletID();
        $where = '';
        if ($menuSalesID > 0) {
            $where = ' AND pos_menusalesID=' . $menuSalesID;
        }
        $companyID = current_companyID();

        /*$results = $this->db->query("SELECT srp_erp_itemledger_review.*,srp_erp_pos_menusalesmaster.documentMasterAutoID AS docID,
	srp_erp_pos_menusalesmaster.documentSystemCode AS docCode,sum(srp_erp_itemledger_review.transactionQTY/srp_erp_itemledger_review.convertionRate)+ IFNULL(itmexceed.excdqty, 0) as convertedtransactionQTY,sum(srp_erp_itemledger_review.currentStock) as currentStocksum,sum(srp_erp_itemledger_review.transactionAmount)+IFNULL(itmexceed.amount, 0) as transactionAmountsum,sum(srp_erp_itemledger_review.companyLocalAmount)+ (IFNULL(itmexceed.amount, 0)/srp_erp_itemledger_review.companyLocalExchangeRate) as companyLocalAmountsum,sum(srp_erp_itemledger_review.companyLocalWacAmount) as companyLocalWacAmountsum,sum(srp_erp_itemledger_review.companyReportingAmount)+ (IFNULL(itmexceed.amount, 0)/srp_erp_itemledger_review.companyReportingExchangeRate) as companyReportingAmountsum,sum(srp_erp_itemledger_review.companyReportingWacAmount) as companyReportingWacAmountsum, sum(srp_erp_itemledger_review.partyCurrencyAmount) as partyCurrencyAmountsum FROM srp_erp_itemledger_review
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS amount,
		IFNULL(SUM(exceededQty), 0) AS excdqty,
		srp_erp_itemexceeded.warehouseAutoID,
		assetGLAutoID,
		documentAutoID,
		itemAutoID,
		srp_erp_pos_menusalesmaster.menuSalesID AS menuslsid
	FROM
		srp_erp_itemexceeded
	LEFT JOIN srp_erp_pos_menusalesmaster ON srp_erp_itemexceeded.documentAutoID = srp_erp_pos_menusalesmaster.documentMasterAutoID
	WHERE
		srp_erp_itemexceeded.documentCode = 'POSR'
	AND srp_erp_itemexceeded.documentAutoID = '" . $shiftID . "'
	AND srp_erp_itemexceeded.warehouseAutoID = '" . $this->outletID . "'
	GROUP BY
		srp_erp_itemexceeded.documentAutoID,
		srp_erp_itemexceeded.warehouseAutoID,
		srp_erp_itemexceeded.itemAutoID
) itmexceed ON srp_erp_itemledger_review.pos_menusalesID = itmexceed.menuslsid
AND srp_erp_itemledger_review.wareHouseAutoID = itmexceed.warehouseAutoID
AND srp_erp_itemledger_review.itemAutoID = itmexceed.itemAutoID

LEFT JOIN srp_erp_pos_menusalesmaster ON srp_erp_itemledger_review.pos_menusalesID = srp_erp_pos_menusalesmaster.menuSalesID
AND srp_erp_itemledger_review.wareHouseAutoID = srp_erp_pos_menusalesmaster.wareHouseAutoID
WHERE srp_erp_itemledger_review.companyID = $companyID AND pos_shiftID = $shiftID AND srp_erp_itemledger_review.warehouseAutoID =  $this->outletID  AND srp_erp_itemledger_review.isCreditSales = $isCreditSales  $where GROUP BY srp_erp_itemledger_review.itemAutoID,srp_erp_itemledger_review.wareHouseAutoID")->result_array();*/


$results = $this->db->query("SELECT srp_erp_itemledger_review.*,sum(srp_erp_itemledger_review.transactionQTY)+ IFNULL(itmexceed.excdqty, 0) as convertedtransactionQTY,sum(srp_erp_itemledger_review.currentStock) as currentStocksum,sum(srp_erp_itemledger_review.transactionAmount)+ IFNULL(SUBSTRING(itmexceed.amount,1,16), 0) as transactionAmountsum,sum(srp_erp_itemledger_review.companyLocalAmount)+ (IFNULL(SUBSTRING(itmexceed.amount,1,16), 0)/srp_erp_itemledger_review.companyLocalExchangeRate) as companyLocalAmountsum,sum(srp_erp_itemledger_review.companyLocalWacAmount) as companyLocalWacAmountsum,sum(srp_erp_itemledger_review.companyReportingAmount)+ (IFNULL(SUBSTRING(itmexceed.amount,1,16), 0)/srp_erp_itemledger_review.companyReportingExchangeRate) as companyReportingAmountsum,sum(srp_erp_itemledger_review.companyReportingWacAmount) as companyReportingWacAmountsum, sum(srp_erp_itemledger_review.partyCurrencyAmount) as partyCurrencyAmountsum FROM srp_erp_itemledger_review
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS amount,
		IFNULL(SUM(exceededQty), 0) AS excdqty,
		warehouseAutoID,
		assetGLAutoID,
		documentAutoID,
		itemAutoID
	FROM
		srp_erp_itemexceeded
	WHERE
		documentCode = 'POSR'
	AND documentAutoID = '" . $shiftID . "'
	AND warehouseAutoID = '" . $outletID . "'
	GROUP BY
		documentAutoID,
		warehouseAutoID,
		itemAutoID
) itmexceed ON srp_erp_itemledger_review.pos_shiftID = itmexceed.documentAutoID
AND srp_erp_itemledger_review.wareHouseAutoID = itmexceed.warehouseAutoID
AND srp_erp_itemledger_review.itemAutoID = itmexceed.itemAutoID LEFT JOIN srp_erp_pos_menusalesmaster ON srp_erp_itemledger_review.pos_menusalesID = srp_erp_pos_menusalesmaster.menuSalesID
AND srp_erp_itemledger_review.wareHouseAutoID = srp_erp_pos_menusalesmaster.wareHouseAutoID WHERE srp_erp_itemledger_review.companyID = $companyID AND srp_erp_pos_menusalesmaster.isVoid=0 AND pos_shiftID = $shiftID AND srp_erp_itemledger_review.warehouseAutoID =  $outletID  AND srp_erp_itemledger_review.isCreditSales = $isCreditSales  $where GROUP BY srp_erp_itemledger_review.itemAutoID,srp_erp_itemledger_review.wareHouseAutoID")->result_array();
        
        $itmlgr = '';
        if (!empty($results)) {
            foreach ($results as $result) {
                //if($result['transactionAmountsum']!=0){
                if (round($result['convertedtransactionQTY'], 2) != 0) {
                    $data['documentID'] = $result['documentID'];
                    $data['documentAutoID'] = $result['documentAutoID'];
                    $data['documentCode'] = $result['documentCode'];
                    $data['documentSystemCode'] = $result['documentSystemCode'];
                    $data['documentDate'] = $result['documentDate'];
                    $data['referenceNumber'] = $result['referenceNumber'];
                    $data['companyFinanceYearID'] = $result['companyFinanceYearID'];
                    $data['companyFinanceYear'] = $result['companyFinanceYear'];
                    $data['FYBegin'] = $result['FYBegin'];
                    $data['FYEnd'] = $result['FYEnd'];
                    $data['FYPeriodDateFrom'] = $result['FYPeriodDateFrom'];
                    $data['FYPeriodDateTo'] = $result['FYPeriodDateTo'];
                    $data['wareHouseAutoID'] = $result['wareHouseAutoID'];
                    $data['wareHouseCode'] = $result['wareHouseCode'];
                    $data['wareHouseLocation'] = $result['wareHouseLocation'];
                    $data['wareHouseDescription'] = $result['wareHouseDescription'];
                    $data['projectID'] = $result['projectID'];
                    $data['projectExchangeRate'] = $result['projectExchangeRate'];
                    $data['itemAutoID'] = $result['itemAutoID'];
                    $data['itemSystemCode'] = $result['itemSystemCode'];
                    $data['ItemSecondaryCode'] = $result['ItemSecondaryCode'];
                    $data['itemDescription'] = $result['itemDescription'];
                    $data['defaultUOMID'] = $result['defaultUOMID'];
                    $data['defaultUOM'] = $result['defaultUOM'];
                    $data['transactionUOMID'] = $result['transactionUOMID'];
                    $data['transactionUOM'] = $result['transactionUOM'];
                    $data['transactionQTY'] = $result['convertedtransactionQTY'];
                    $data['convertionRate'] = $result['convertionRate'];
                    $data['currentStock'] = $result['currentStocksum'];
                    $data['PLGLAutoID'] = $result['PLGLAutoID'];
                    $data['PLSystemGLCode'] = $result['PLSystemGLCode'];
                    $data['PLGLCode'] = $result['PLGLCode'];
                    $data['PLDescription'] = $result['PLDescription'];
                    $data['PLType'] = $result['PLType'];
                    $data['BLGLAutoID'] = $result['BLGLAutoID'];
                    $data['BLSystemGLCode'] = $result['BLSystemGLCode'];
                    $data['BLGLCode'] = $result['BLGLCode'];
                    $data['BLDescription'] = $result['BLDescription'];
                    $data['BLType'] = $result['BLType'];
                    $data['transactionCurrencyID'] = $result['transactionCurrencyID'];
                    $data['transactionCurrency'] = $result['transactionCurrency'];
                    $data['transactionExchangeRate'] = $result['transactionExchangeRate'];
                    $data['transactionAmount'] = $result['transactionAmountsum'];
                    $data['transactionCurrencyDecimalPlaces'] = $result['transactionCurrencyDecimalPlaces'];
                    $data['companyLocalCurrencyID'] = $result['companyLocalCurrencyID'];
                    $data['companyLocalCurrency'] = $result['companyLocalCurrency'];
                    $data['companyLocalExchangeRate'] = $result['companyLocalExchangeRate'];
                    $data['companyLocalAmount'] = $result['companyLocalAmountsum'];
                    $data['companyLocalWacAmount'] = $result['companyLocalWacAmountsum'];
                    $data['companyLocalCurrencyDecimalPlaces'] = $result['companyLocalCurrencyDecimalPlaces'];
                    $data['companyReportingCurrencyID'] = $result['companyReportingCurrencyID'];
                    $data['companyReportingCurrency'] = $result['companyReportingCurrency'];
                    $data['companyReportingExchangeRate'] = $result['companyReportingExchangeRate'];
                    $data['companyReportingAmount'] = $result['companyReportingAmountsum'];
                    $data['companyReportingWacAmount'] = $result['companyReportingWacAmountsum'];
                    $data['companyReportingCurrencyDecimalPlaces'] = $result['companyReportingCurrencyDecimalPlaces'];
                    $data['partyCurrencyID'] = $result['partyCurrencyID'];
                    $data['partyCurrency'] = $result['partyCurrency'];
                    $data['partyCurrencyExchangeRate'] = $result['partyCurrencyExchangeRate'];
                    $data['partyCurrencyAmount'] = $result['partyCurrencyAmountsum'];
                    $data['partyCurrencyDecimalPlaces'] = $result['partyCurrencyDecimalPlaces'];
                    $data['salesPrice'] = $result['salesPrice'];
                    $data['confirmedYN'] = $result['confirmedYN'];
                    $data['confirmedByEmpID'] = $result['confirmedByEmpID'];
                    $data['confirmedByName'] = $result['confirmedByName'];
                    $data['confirmedDate'] = $result['confirmedDate'];
                    $data['approvedYN'] = $result['approvedYN'];
                    $data['approvedDate'] = $result['approvedDate'];
                    $data['approvedbyEmpID'] = $result['approvedbyEmpID'];
                    $data['approvedbyEmpName'] = $result['approvedbyEmpName'];
                    $data['segmentID'] = $result['segmentID'];
                    $data['segmentCode'] = $result['segmentCode'];
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['narration'] = $result['narration'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['expenseGLAutoID'] = $result['expenseGLAutoID'];
                    $data['expenseGLCode'] = $result['expenseGLCode'];
                    $data['expenseSystemGLCode'] = $result['expenseSystemGLCode'];
                    $data['expenseGLDescription'] = $result['expenseGLDescription'];
                    $data['expenseGLType'] = $result['expenseGLType'];
                    $data['revenueGLAutoID'] = $result['revenueGLAutoID'];
                    $data['revenueGLCode'] = $result['revenueGLCode'];
                    $data['revenueSystemGLCode'] = $result['revenueSystemGLCode'];
                    $data['revenueGLDescription'] = $result['revenueGLDescription'];
                    $data['revenueGLType'] = $result['revenueGLType'];
                    $data['assetGLAutoID'] = $result['assetGLAutoID'];
                    $data['assetGLCode'] = $result['assetGLCode'];
                    $data['assetSystemGLCode'] = $result['assetSystemGLCode'];
                    $data['assetGLDescription'] = $result['assetGLDescription'];
                    $data['assetGLType'] = $result['assetGLType'];
                    $data['is_sync'] = $result['is_sync'];
                    $data['id_store'] = $result['id_store'];

                    $itmlgr = $this->db->insert('srp_erp_itemledger', $data);
                }

            }
        }

        //echo $this->db->last_query();
        return $itmlgr;
    }


    /******* Insert item ledger from item ledger review ****/
    function update_generalLedger_fromReview($shiftID, $menuSalesID = 0, $isCreditSales = 0)
    {
        $outletID = get_outletID();
        $where = '';
        if ($menuSalesID > 0) {
            $where = ' AND pos_menusalesID=' . $menuSalesID;
        }

        $companyID = current_companyID();

//LEFT JOIN srp_erp_pos_menusalesmaster ON srp_erp_generalledger_review.pos_menusalesID = srp_erp_pos_menusalesmaster.menuSalesID AND srp_erp_generalledger_review.wareHouseAutoID = srp_erp_pos_menusalesmaster.wareHouseAutoID
        //AND srp_erp_pos_menusalesmaster.isVoid=0
        $results = $this->db->query(
            "SELECT srp_erp_generalledger_review.*,
sum(srp_erp_generalledger_review.transactionAmount)+ IFNULL(itmexceed.amount,0) as transactionAmountsum,
sum(srp_erp_generalledger_review.companyLocalAmount)+ IFNULL(itmexceed.amount,0) as companyLocalAmountsum,
sum(srp_erp_generalledger_review.companyReportingAmount)+ IFNULL(itmexceed.amount,0) as companyReportingAmountsum 
FROM srp_erp_generalledger_review
LEFT JOIN srp_erp_pos_menusalesmaster ON srp_erp_generalledger_review.pos_menusalesID = srp_erp_pos_menusalesmaster.menuSalesID AND srp_erp_generalledger_review.wareHouseAutoID = srp_erp_pos_menusalesmaster.wareHouseAutoID
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS amount,
		IFNULL(SUM(exceededQty), 0) AS excdqty,
		warehouseAutoID,
		assetGLAutoID,
		documentAutoID,
		documentCode as dcode,
		itemAutoID
	FROM
		srp_erp_itemexceeded
	WHERE
		documentCode = 'POSR'
	AND documentAutoID = '" . $shiftID . "'
	AND warehouseAutoID = '" . $outletID . "'
	GROUP BY
		documentAutoID,
		warehouseAutoID,
		assetGLAutoID
) itmexceed ON srp_erp_generalledger_review.pos_shiftID = itmexceed.documentAutoID 
AND srp_erp_generalledger_review.warehouseAutoID = itmexceed.warehouseAutoID 
AND srp_erp_generalledger_review.GLAutoID=itmexceed.assetGLAutoID 
AND srp_erp_generalledger_review.documentCode = itmexceed.dcode

WHERE srp_erp_generalledger_review.companyID = $companyID 
AND srp_erp_pos_menusalesmaster.isVoid=0
AND srp_erp_generalledger_review.warehouseAutoID =  $outletID
AND pos_shiftID = $shiftID 
AND srp_erp_generalledger_review.isCreditSales = $isCreditSales $where 
GROUP BY GLAutoID,pos_shiftID")->result_array();
        $genlgr = '';
        if (!empty($results)) {
            foreach ($results as $result) {
                if ($result['transactionAmountsum'] != 0) {
                    $data['wareHouseAutoID'] = $result['wareHouseAutoID'];
                    $data['documentCode'] = $result['documentCode'];
                    $data['documentMasterAutoID'] = $result['documentMasterAutoID'];
                    $data['documentDetailAutoID'] = $result['documentDetailAutoID'];
                    $data['documentSystemCode'] = $result['documentSystemCode'];
                    $data['documentType'] = $result['documentType'];
                    $data['documentDate'] = $result['documentDate'];
                    $data['documentYear'] = $result['documentYear'];
                    $data['documentMonth'] = $result['documentMonth'];
                    $data['projectID'] = $result['projectID'];
                    $data['projectExchangeRate'] = $result['projectExchangeRate'];
                    $data['documentNarration'] = $result['documentNarration'];
                    $data['chequeNumber'] = $result['chequeNumber'];
                    $data['GLAutoID'] = $result['GLAutoID'];
                    $data['systemGLCode'] = $result['systemGLCode'];
                    $data['GLCode'] = $result['GLCode'];
                    $data['GLDescription'] = $result['GLDescription'];
                    $data['GLType'] = $result['GLType'];
                    $data['amount_type'] = $result['amount_type'];
                    $data['isFromItem'] = $result['isFromItem'];
                    $data['transactionCurrencyID'] = $result['transactionCurrencyID'];
                    $data['transactionCurrency'] = $result['transactionCurrency'];
                    $data['transactionExchangeRate'] = $result['transactionExchangeRate'];
                    $data['transactionAmount'] = $result['transactionAmountsum'];
                    $data['transactionCurrencyDecimalPlaces'] = $result['transactionCurrencyDecimalPlaces'];
                    $data['companyLocalCurrencyID'] = $result['companyLocalCurrencyID'];
                    $data['companyLocalCurrency'] = $result['companyLocalCurrency'];
                    $data['companyLocalExchangeRate'] = $result['companyLocalExchangeRate'];
                    $data['companyLocalAmount'] = $result['companyLocalAmountsum'];
                    $data['companyLocalCurrencyDecimalPlaces'] = $result['companyLocalCurrencyDecimalPlaces'];
                    $data['companyReportingCurrencyID'] = $result['companyReportingCurrencyID'];
                    $data['companyReportingCurrency'] = $result['companyReportingCurrency'];
                    $data['companyReportingExchangeRate'] = $result['companyReportingExchangeRate'];
                    $data['companyReportingAmount'] = $result['companyReportingAmountsum'];
                    $data['companyReportingCurrencyDecimalPlaces'] = $result['companyReportingCurrencyDecimalPlaces'];
                    $data['partyContractID'] = $result['partyContractID'];
                    $data['partyType'] = $result['partyType'];
                    $data['partyAutoID'] = $result['partyAutoID'];
                    $data['partySystemCode'] = $result['partySystemCode'];
                    $data['partyName'] = $result['partyName'];
                    $data['partyCurrencyID'] = $result['partyCurrencyID'];
                    $data['partyCurrency'] = $result['partyCurrency'];
                    $data['partyExchangeRate'] = $result['partyExchangeRate'];
                    $data['partyCurrencyAmount'] = $result['partyCurrencyAmount'];
                    $data['partyCurrencyDecimalPlaces'] = $result['partyCurrencyDecimalPlaces'];
                    $data['subLedgerType'] = $result['subLedgerType'];
                    $data['subLedgerDesc'] = $result['subLedgerDesc'];
                    $data['taxMasterAutoID'] = $result['taxMasterAutoID'];
                    $data['partyVatIdNo'] = $result['partyVatIdNo'];
                    $data['is_sync'] = $result['is_sync'];
                    $data['id_store'] = $result['id_store'];
                    $data['isAddon'] = $result['isAddon'];
                    $data['confirmedByEmpID'] = $result['confirmedByEmpID'];
                    $data['confirmedByName'] = $result['confirmedByName'];
                    $data['confirmedDate'] = $result['confirmedDate'];
                    $data['approvedDate'] = $result['approvedDate'];
                    $data['approvedbyEmpID'] = $result['approvedbyEmpID'];
                    $data['approvedbyEmpName'] = $result['approvedbyEmpName'];
                    $data['segmentID'] = $result['segmentID'];
                    $data['segmentCode'] = $result['segmentCode'];
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['createdUserName'] = $this->common_data['current_user'];

                    $genlgr = $this->db->insert('srp_erp_generalledger', $data);
                }

            }

        }

        //echo $this->db->last_query();
        return $genlgr;
    }


    function update_bankLedger_fromReview($shiftID)
    {
        $companyID = current_companyID();
        $outletID = get_outletID();
        $results = $this->db->query("SELECT *,sum(partyCurrencyAmount) as partyCurrencyAmountSum,sum(clearedAmount) as clearedAmountSum,sum(transactionAmount) as transactionAmountSum,sum(bankCurrencyAmount) as bankCurrencyAmountSum FROM srp_erp_bankledger_review WHERE companyID = $companyID AND wareHouseAutoID = $outletID AND pos_shiftID = $shiftID AND isCreditSales = 0 GROUP BY bankGLAutoID,pos_shiftID")->result_array();

        $banklgr = '';
        if (!empty($results)) {
            foreach ($results as $result) {
                $data['wareHouseAutoID'] = $result['wareHouseAutoID'];
                $data['documentDate'] = $result['documentDate'];
                $data['transactionType'] = $result['transactionType'];
                $data['partyType'] = $result['partyType'];
                $data['partyAutoID'] = $result['partyAutoID'];
                $data['partyCode'] = $result['partyCode'];
                $data['partyName'] = $result['partyName'];
                $data['partyCurrencyID'] = $result['partyCurrencyID'];
                $data['partyCurrency'] = $result['partyCurrency'];
                $data['partyCurrencyExchangeRate'] = $result['partyCurrencyExchangeRate'];
                $data['partyCurrencyDecimalPlaces'] = $result['partyCurrencyDecimalPlaces'];
                $data['partyCurrencyAmount'] = $result['partyCurrencyAmountSum'];
                $data['transactionCurrencyID'] = $result['transactionCurrencyID'];
                $data['transactionCurrency'] = $result['transactionCurrency'];
                $data['transactionExchangeRate'] = $result['transactionExchangeRate'];
                $data['transactionAmount'] = $result['transactionAmountSum'];
                $data['transactionCurrencyDecimalPlaces'] = $result['transactionCurrencyDecimalPlaces'];
                $data['bankCurrencyID'] = $result['bankCurrencyID'];
                $data['bankCurrency'] = $result['bankCurrency'];
                $data['bankCurrencyExchangeRate'] = $result['bankCurrencyExchangeRate'];
                $data['bankCurrencyAmount'] = $result['bankCurrencyAmountSum'];
                $data['bankCurrencyDecimalPlaces'] = $result['bankCurrencyDecimalPlaces'];
                $data['modeofPayment'] = $result['modeofPayment'];
                $data['chequeNo'] = $result['chequeNo'];
                $data['chequeDate'] = $result['chequeDate'];
                $data['isThirdPartyCheque'] = $result['isThirdPartyCheque'];
                $data['thirdPartyName'] = $result['thirdPartyName'];
                $data['thirdPartyInfo'] = $result['thirdPartyInfo'];
                $data['remainIn'] = $result['remainIn'];
                $data['memo'] = $result['memo'];
                $data['bankName'] = $result['bankName'];
                $data['bankGLAutoID'] = $result['bankGLAutoID'];
                $data['bankSystemAccountCode'] = $result['bankSystemAccountCode'];
                $data['bankGLSecondaryCode'] = $result['bankGLSecondaryCode'];
                $data['documentMasterAutoID'] = $result['documentMasterAutoID'];
                $data['documentType'] = $result['documentType'];
                $data['documentSystemCode'] = $result['documentSystemCode'];
                $data['clearedYN'] = $result['clearedYN'];
                $data['clearedDate'] = $result['clearedDate'];
                $data['clearedAmount'] = $result['clearedAmountSum'];
                $data['clearedBy'] = $result['clearedBy'];
                $data['bankRecMonthID'] = $result['bankRecMonthID'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['segmentID'] = $result['segmentID'];
                $data['segmentCode'] = $result['segmentCode'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['is_sync'] = $result['is_sync'];
                $data['id_store'] = $result['id_store'];

                $banklgr = $this->db->insert('srp_erp_bankledger', $data);
            }

        }
        return $banklgr;
    }


    function pos_generate_invoices_from_review($shiftID, $menuSalesID = 0)
    {
        $outletID = get_outletID();
        /** Create Invoice Header */
        $this->load->library('sequence');

        $q = "INSERT INTO srp_erp_customerinvoicemaster (
                wareHouseAutoID,
                invoiceType,
                documentID,
                posMasterAutoID,
                invoiceDate,
                invoiceDueDate,
                customerInvoiceDate,
                invoiceCode,
                referenceNo,
                invoiceNarration,
                companyFinanceYearID,
                companyFinanceYear,
                FYBegin,
                FYEnd,
                companyFinancePeriodID,
                customerID,
                customerSystemCode,
                customerName,
                customerReceivableAutoID,
                customerReceivableSystemGLCode,
                customerReceivableGLAccount,
                customerReceivableDescription,
                customerReceivableType,
                transactionCurrencyID,
                transactionCurrency,
                transactionExchangeRate,
                transactionAmount,
                transactionCurrencyDecimalPlaces,
                companyLocalCurrencyID,
                companyLocalCurrency,
                companyLocalExchangeRate,
                companyLocalAmount,
                companyLocalCurrencyDecimalPlaces,
                companyReportingCurrencyID,
                companyReportingCurrency,
                companyReportingExchangeRate,
                companyReportingAmount,
                companyReportingCurrencyDecimalPlaces,
                customerCurrencyID,
                customerCurrency,
                customerCurrencyExchangeRate,
                customerCurrencyAmount,
                customerCurrencyDecimalPlaces,
                confirmedYN,
                confirmedByEmpID,
                confirmedByName,
                confirmedDate,
                approvedYN,
                approvedDate,
                approvedbyEmpID,
                approvedbyEmpName,
                segmentID,
                segmentCode,
                companyID,
                companyCode,
                createdUserGroup,
                createdPCID,
                createdUserID,
                createdDateTime,
                createdUserName,
                `timestamp`
                ) (
                SELECT
                    srp_erp_pos_shiftdetails.wareHouseID as wareHouseID,
                    'Direct' AS invoiceType,
                    'CINV' AS documentID,
                    $menuSalesID AS documentID,
                    IF(srp_erp_pos_menusalesmaster.isCreditSales=1,DATE_FORMAT( srp_erp_pos_menusalesmaster.menuSalesDate, \"%Y-%m-%d\" ),DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" )) AS invoiceDate,
                    DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" ) AS invoiceDueDate,
                    DATE_FORMAT( srp_erp_pos_shiftdetails.startTime, \"%Y-%m-%d\" ) AS customerInvoiceDate,
                    0 AS invoiceCode,
                    srp_erp_pos_menusalesmaster.invoiceCode AS referenceNo,
                    concat( 'POS Credit Sales - ', srp_erp_pos_menusalesmaster.invoiceCode ) AS invoiceNarration,
                    getCompanyFinanceYearID ( srp_erp_pos_shiftdetails.companyID ) AS companyFinanceYearID,
                    concat( FY.beginingDate, ' - ', FY.endingDate ) AS companyFinanceYear,
                    FY.beginingDate AS FYBegin,
                    FY.endingDate AS FYEnd,
                    getCompanyFinancePeriodID ( srp_erp_pos_shiftdetails.companyID ) AS companyFinancePeriodID,
                    srp_erp_pos_menusalespayments.customerAutoID AS customerID,
                    srp_erp_customermaster.customerSystemCode AS customerSystemCode,
                    srp_erp_customermaster.customerName AS customerName,
                    srp_erp_customermaster.receivableAutoID AS customerReceivableAutoID,
                    srp_erp_customermaster.receivableSystemGLCode AS customerReceivableSystemGLCode,
                    srp_erp_customermaster.receivableGLAccount AS customerReceivableGLAccount,
                    srp_erp_customermaster.receivableDescription AS customerReceivableDescription,
                    srp_erp_customermaster.receivableType AS customerReceivableType,
                    srp_erp_pos_menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
                    srp_erp_pos_menusalesmaster.transactionCurrency AS transactionCurrency,
                    srp_erp_pos_menusalesmaster.transactionExchangeRate AS transactionExchangeRate,
                    Sum( srp_erp_pos_menusalespayments.amount ) AS transactionAmount,
                    srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.companyLocalCurrencyID,
                    srp_erp_pos_menusalesmaster.companyLocalCurrency,
                    srp_erp_pos_menusalesmaster.companyLocalExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.companyLocalExchangeRate ) AS companyLocalAmount,
                    srp_erp_pos_menusalesmaster.companyLocalCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.companyReportingCurrencyID,
                    srp_erp_pos_menusalesmaster.companyReportingCurrency,
                    srp_erp_pos_menusalesmaster.companyReportingExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.companyReportingExchangeRate ) AS companyReportingAmount,
                    srp_erp_pos_menusalesmaster.companyReportingCurrencyDecimalPlaces,
                    srp_erp_pos_menusalesmaster.customerCurrencyID,
                    srp_erp_pos_menusalesmaster.customerCurrency,
                    srp_erp_pos_menusalesmaster.customerCurrencyExchangeRate,
                    ( Sum( srp_erp_pos_menusalespayments.amount ) / srp_erp_pos_menusalesmaster.customerCurrencyExchangeRate ) AS customerCurrencyAmount,
                    srp_erp_pos_menusalesmaster.customerCurrencyDecimalPlaces,
                    1 AS confirmedYN,
                    srp_erp_pos_shiftdetails.createdUserID AS confirmedByEmpID,
                    srp_erp_pos_shiftdetails.createdUserName AS confirmedByName,
                    srp_erp_pos_shiftdetails.startTime AS confirmedDate,
                    1 AS approvedYN,
                    srp_erp_pos_shiftdetails.startTime AS approvedDate,
                    srp_erp_pos_shiftdetails.createdUserID AS approvedbyEmpID,
                    srp_erp_pos_shiftdetails.createdUserName AS approvedbyEmpName,
                    srp_erp_pos_menusalesmaster.segmentID AS segmentID,
                    srp_erp_pos_menusalesmaster.segmentCode AS segmentCode,
                    srp_erp_pos_menusalesmaster.companyID,
                    srp_erp_pos_menusalesmaster.companyCode,
                    srp_erp_pos_shiftdetails.createdUserGroup,
                    srp_erp_pos_shiftdetails.createdPCID,
                    srp_erp_pos_shiftdetails.createdUserID,
                    srp_erp_pos_shiftdetails.startTime AS createdDateTime,
                    srp_erp_pos_shiftdetails.createdUserName,
                    srp_erp_pos_shiftdetails.`timestamp` AS `timestamp`
                FROM
                    srp_erp_pos_menusalesmaster
                    LEFT JOIN srp_erp_pos_menusalespayments ON srp_erp_pos_menusalespayments.menuSalesID = srp_erp_pos_menusalesmaster.menuSalesID
                    LEFT JOIN srp_erp_pos_shiftdetails ON srp_erp_pos_menusalesmaster.shiftID = srp_erp_pos_shiftdetails.shiftID
                    LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalespayments.customerAutoID = srp_erp_customermaster.customerAutoID
                    LEFT JOIN srp_erp_companyfinanceyear FY ON FY.companyFinanceYearID = getCompanyFinanceYearID ( srp_erp_pos_shiftdetails.companyID )
                WHERE
                    srp_erp_pos_menusalesmaster.shiftID = '" . $shiftID . "'
                    AND srp_erp_pos_menusalesmaster.menuSalesID = '" . $menuSalesID . "'
                    AND srp_erp_pos_menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND srp_erp_pos_menusalesmaster.isCreditSales = 1
                    AND srp_erp_pos_menusalespayments.paymentConfigMasterID = 7
                    AND srp_erp_pos_menusalesmaster.isVoid = 0
                    AND srp_erp_pos_menusalesmaster.isHold = 0

                GROUP BY
                    srp_erp_pos_menusalesmaster.menuSalesID
                ORDER BY
                    srp_erp_pos_menusalesmaster.menuSalesID DESC
                    )";


        $this->db->query($q);
        $insert_id = $this->db->insert_id();
        $row_count = $this->db->affected_rows();
        $result = array();
        $i = 0;
        while (true) {
            if ($row_count == $i) {
                break;

            } else if ($i > 99) {
                break;
            }
            $result[$i] = $insert_id;
            $insert_id++;
            $i++;
        }
        if (!empty($result)) {


            $tmpData = array();
            $i2 = 0;
            $where = ' WHERE (';
            foreach ($result as $id) {
                $tmpData[$i2]['invoiceAutoID'] = $id;
                $tmpData[$i2]['invoiceCode'] = $this->sequence->sequence_generator('CINV');
                $where .= ' srp_erp_customerinvoicemaster.invoiceAutoID = ' . $id . ' OR';
                $i2++;

            }
            $where = trim($where, ' OR');
            $where .= ')';
            //var_dump($tmpData);
            $this->db->update_batch('srp_erp_customerinvoicemaster', $tmpData, 'invoiceAutoID');

            if ($row_count > 0) {
                /** Create Invoice Detail */
                $q = "INSERT INTO srp_erp_customerinvoicedetails (
                    invoiceAutoID,
                    `type`,
                    description,
                    transactionAmount,
                    companyLocalAmount,
                    companyReportingAmount,
                    customerAmount,
                    segmentID,
                    segmentCode,
                    companyID,
                    companyCode,
                    createdUserGroup,
                    createdPCID,
                    createdUserID,
                    createdDateTime,
                    createdUserName,
                    `timestamp`
                    ) (
                    SELECT
                        srp_erp_customerinvoicemaster.invoiceAutoID,
                        'GL' AS `type`,
                        srp_erp_customerinvoicemaster.referenceNo AS description,
                        srp_erp_customerinvoicemaster.transactionAmount,
                        srp_erp_customerinvoicemaster.companyLocalAmount,
                        srp_erp_customerinvoicemaster.companyReportingAmount,
                        srp_erp_customerinvoicemaster.customerCurrencyAmount,
                        srp_erp_customerinvoicemaster.segmentID,
                        srp_erp_customerinvoicemaster.segmentCode,
                        srp_erp_customerinvoicemaster.companyID,
                        srp_erp_customerinvoicemaster.companyCode,
                        srp_erp_customerinvoicemaster.createdUserGroup,
                        srp_erp_customerinvoicemaster.createdPCID,
                        srp_erp_customerinvoicemaster.createdUserID,
                        srp_erp_customerinvoicemaster.createdDateTime,
                        srp_erp_customerinvoicemaster.createdUserName,
                        srp_erp_customerinvoicemaster.`timestamp`
                    FROM
                        srp_erp_customerinvoicemaster
                    " . $where . " LIMIT " . $row_count . " )";
                //echo $q;
                $this->db->query($q);

                /** Document Approved Table Entries */
                $q2 = "INSERT INTO srp_erp_documentapproved (
                    `departmentID`,
                    `documentID`,
                    `documentSystemCode`,
                    `documentCode`,
                    `documentDate`,
                    `approvalLevelID`,
                    `roleID`,
                    `approvalGroupID`,
                    `roleLevelOrder`,
                    `docConfirmedDate`,
                    `docConfirmedByEmpID`,
                    `table_name`,
                    `table_unique_field_name`,
                    `approvedEmpID`,
                    `approvedYN`,
                    `approvedDate`,
                    `approvedComments`,
                    `approvedPC`,
                    `companyID`,
                    `companyCode`,
                    `timeStamp`
                    ) (
                    SELECT
                        'CINV' as `departmentID`,
                        srp_erp_customerinvoicemaster.documentID as `documentID`,
                        srp_erp_customerinvoicemaster.invoiceAutoID as `documentSystemCode`,
                        srp_erp_customerinvoicemaster.invoiceCode as `documentCode`,
                        srp_erp_customerinvoicemaster.invoiceDate as `documentDate`,
                        1 as `approvalLevelID `,
                        1 as `roleID`,
                        0 as `approvalGroupID`,
                        1 as `roleLevelOrder`,
                        srp_erp_customerinvoicemaster.invoiceDate as `docConfirmedDate`,
                        srp_erp_customerinvoicemaster.createdUserID as `docConfirmedByEmpID`,
                        'srp_erp_customerinvoicemaster' as `table_name`,
                        'invoiceAutoID' as `table_unique_field_name`,
                        srp_erp_customerinvoicemaster.createdUserID as `approvedEmpID`,
                        1 as `approvedYN`,
                        srp_erp_customerinvoicemaster.createdDateTime as `approvedDate`,
                        'Approved from POS' as `approvedComments`,
                        srp_erp_customerinvoicemaster.createdPCID as `approvedPC`,
                        srp_erp_customerinvoicemaster.companyID as `companyID`,
                        srp_erp_customerinvoicemaster.companyCode as `companyCode`,
                        srp_erp_customerinvoicemaster.createdDateTime as `timeStamp`
                    FROM
                        srp_erp_customerinvoicemaster
                    " . $where . " LIMIT " . $row_count . " )";

                $this->db->query($q2);

                /** update menu sales master  */
                $q3 = "UPDATE srp_erp_pos_menusalesmaster AS t1,
                        (
                        SELECT
                            srp_erp_pos_menusalesmaster.menuSalesID AS menuSalesID,
                            srp_erp_customerinvoicemaster.invoiceCode AS invoiceCode,
                            srp_erp_customerinvoicemaster.invoiceAutoID AS invoiceAutoID
                        FROM
                            srp_erp_pos_menusalesmaster
                            INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_pos_menusalesmaster.invoiceCode = srp_erp_customerinvoicemaster.referenceNo
                        WHERE
                            srp_erp_pos_menusalesmaster.shiftID =  '" . $shiftID . "'
                            ) AS t2
                            SET t1.documentSystemCode = t2.invoiceCode,  t1.documentMasterAutoID= t2.invoiceAutoID
                        WHERE
                            t1.menuSalesID = t2.menuSalesID
                            AND t1.shiftID =  '" . $shiftID . "' ";
                /*echo $q3;
                exit;*/
                $this->db->query($q3);
            }

            /*$this->update_itemLedger_fromReview($shiftID,$menuSalesID,1);
            $this->update_generalLedger_fromReview($shiftID,$menuSalesID,1);*/

        }
        //var_dump($result);
        return $result;
    }


    function update_itemLedger_fromReview_creditsales($shiftID, $menuSalesID, $documentMasterAutoID)
    {
        $outletID = get_outletID();
        $where = '';
        if ($menuSalesID > 0) {
            $where = ' AND pos_menusalesID=' . $menuSalesID;
        }
        $companyID = current_companyID();

        $results = $this->db->query("SELECT srp_erp_itemledger_review.*,srp_erp_pos_menusalesmaster.documentMasterAutoID AS docID,
	srp_erp_pos_menusalesmaster.documentSystemCode AS docCode,sum(srp_erp_itemledger_review.transactionQTY/srp_erp_itemledger_review.convertionRate)+ IFNULL(itmexceed.excdqty, 0) as convertedtransactionQTY,sum(srp_erp_itemledger_review.currentStock) as currentStocksum,sum(srp_erp_itemledger_review.transactionAmount)+ IFNULL(itmexceed.amount, 0) as transactionAmountsum,sum(srp_erp_itemledger_review.companyLocalAmount)+ (IFNULL(itmexceed.amount, 0)/srp_erp_itemledger_review.companyLocalExchangeRate) as companyLocalAmountsum,sum(srp_erp_itemledger_review.companyLocalWacAmount) as companyLocalWacAmountsum,sum(srp_erp_itemledger_review.companyReportingAmount)+ (IFNULL(itmexceed.amount, 0)/srp_erp_itemledger_review.companyReportingExchangeRate) as companyReportingAmountsum,sum(srp_erp_itemledger_review.companyReportingWacAmount) as companyReportingWacAmountsum, sum(srp_erp_itemledger_review.partyCurrencyAmount) as partyCurrencyAmountsum FROM srp_erp_itemledger_review
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS amount,
		IFNULL(SUM(exceededQty), 0) AS excdqty,
		srp_erp_itemexceeded.warehouseAutoID,
		assetGLAutoID,
		documentAutoID,
		itemAutoID,
		srp_erp_pos_menusalesmaster.menuSalesID AS menuslsid
	FROM
		srp_erp_itemexceeded
	LEFT JOIN srp_erp_pos_menusalesmaster ON srp_erp_itemexceeded.documentAutoID = srp_erp_pos_menusalesmaster.documentMasterAutoID
	WHERE
		srp_erp_itemexceeded.documentCode = 'CINV'
	AND srp_erp_itemexceeded.documentAutoID = '" . $documentMasterAutoID . "'
	AND srp_erp_itemexceeded.warehouseAutoID = '" . $outletID . "'
	GROUP BY
		srp_erp_itemexceeded.documentAutoID,
		srp_erp_itemexceeded.warehouseAutoID,
		srp_erp_itemexceeded.itemAutoID
) itmexceed ON srp_erp_itemledger_review.pos_menusalesID = itmexceed.menuslsid
AND srp_erp_itemledger_review.wareHouseAutoID = itmexceed.warehouseAutoID
AND srp_erp_itemledger_review.itemAutoID = itmexceed.itemAutoID

LEFT JOIN srp_erp_pos_menusalesmaster ON srp_erp_itemledger_review.pos_menusalesID = srp_erp_pos_menusalesmaster.menuSalesID
AND srp_erp_itemledger_review.wareHouseAutoID = srp_erp_pos_menusalesmaster.wareHouseAutoID
WHERE srp_erp_itemledger_review.companyID = $companyID AND srp_erp_pos_menusalesmaster.isVoid=0 AND pos_shiftID = $shiftID AND srp_erp_itemledger_review.warehouseAutoID =  $outletID  AND srp_erp_itemledger_review.isCreditSales = 1  $where GROUP BY srp_erp_itemledger_review.itemAutoID,srp_erp_itemledger_review.wareHouseAutoID")->result_array();
        $itmlgr = '';
        if (!empty($results)) {
            foreach ($results as $result) {
                //if($result['transactionAmountsum']!=0){
                if (round($result['convertedtransactionQTY'], 2) != 0) {
                    $data['documentID'] = $result['documentID'];
                    $data['documentAutoID'] = $result['docID'];
                    $data['documentCode'] = $result['documentCode'];
                    $data['documentSystemCode'] = $result['docCode'];
                    $data['documentDate'] = $result['documentDate'];
                    $data['referenceNumber'] = $result['referenceNumber'];
                    $data['companyFinanceYearID'] = $result['companyFinanceYearID'];
                    $data['companyFinanceYear'] = $result['companyFinanceYear'];
                    $data['FYBegin'] = $result['FYBegin'];
                    $data['FYEnd'] = $result['FYEnd'];
                    $data['FYPeriodDateFrom'] = $result['FYPeriodDateFrom'];
                    $data['FYPeriodDateTo'] = $result['FYPeriodDateTo'];
                    $data['wareHouseAutoID'] = $result['wareHouseAutoID'];
                    $data['wareHouseCode'] = $result['wareHouseCode'];
                    $data['wareHouseLocation'] = $result['wareHouseLocation'];
                    $data['wareHouseDescription'] = $result['wareHouseDescription'];
                    $data['projectID'] = $result['projectID'];
                    $data['projectExchangeRate'] = $result['projectExchangeRate'];
                    $data['itemAutoID'] = $result['itemAutoID'];
                    $data['itemSystemCode'] = $result['itemSystemCode'];
                    $data['ItemSecondaryCode'] = $result['ItemSecondaryCode'];
                    $data['itemDescription'] = $result['itemDescription'];
                    $data['defaultUOMID'] = $result['defaultUOMID'];
                    $data['defaultUOM'] = $result['defaultUOM'];
                    $data['transactionUOMID'] = $result['transactionUOMID'];
                    $data['transactionUOM'] = $result['transactionUOM'];
                    $data['transactionQTY'] = $result['convertedtransactionQTY'];
                    $data['convertionRate'] = $result['convertionRate'];
                    $data['currentStock'] = $result['currentStocksum'];
                    $data['PLGLAutoID'] = $result['PLGLAutoID'];
                    $data['PLSystemGLCode'] = $result['PLSystemGLCode'];
                    $data['PLGLCode'] = $result['PLGLCode'];
                    $data['PLDescription'] = $result['PLDescription'];
                    $data['PLType'] = $result['PLType'];
                    $data['BLGLAutoID'] = $result['BLGLAutoID'];
                    $data['BLSystemGLCode'] = $result['BLSystemGLCode'];
                    $data['BLGLCode'] = $result['BLGLCode'];
                    $data['BLDescription'] = $result['BLDescription'];
                    $data['BLType'] = $result['BLType'];
                    $data['transactionCurrencyID'] = $result['transactionCurrencyID'];
                    $data['transactionCurrency'] = $result['transactionCurrency'];
                    $data['transactionExchangeRate'] = $result['transactionExchangeRate'];
                    $data['transactionAmount'] = $result['transactionAmountsum'];
                    $data['transactionCurrencyDecimalPlaces'] = $result['transactionCurrencyDecimalPlaces'];
                    $data['companyLocalCurrencyID'] = $result['companyLocalCurrencyID'];
                    $data['companyLocalCurrency'] = $result['companyLocalCurrency'];
                    $data['companyLocalExchangeRate'] = $result['companyLocalExchangeRate'];
                    $data['companyLocalAmount'] = $result['companyLocalAmountsum'];
                    $data['companyLocalWacAmount'] = $result['companyLocalWacAmountsum'];
                    $data['companyLocalCurrencyDecimalPlaces'] = $result['companyLocalCurrencyDecimalPlaces'];
                    $data['companyReportingCurrencyID'] = $result['companyReportingCurrencyID'];
                    $data['companyReportingCurrency'] = $result['companyReportingCurrency'];
                    $data['companyReportingExchangeRate'] = $result['companyReportingExchangeRate'];
                    $data['companyReportingAmount'] = $result['companyReportingAmountsum'];
                    $data['companyReportingWacAmount'] = $result['companyReportingWacAmountsum'];
                    $data['companyReportingCurrencyDecimalPlaces'] = $result['companyReportingCurrencyDecimalPlaces'];
                    $data['partyCurrencyID'] = $result['partyCurrencyID'];
                    $data['partyCurrency'] = $result['partyCurrency'];
                    $data['partyCurrencyExchangeRate'] = $result['partyCurrencyExchangeRate'];
                    $data['partyCurrencyAmount'] = $result['partyCurrencyAmountsum'];
                    $data['partyCurrencyDecimalPlaces'] = $result['partyCurrencyDecimalPlaces'];
                    $data['salesPrice'] = $result['salesPrice'];
                    $data['confirmedYN'] = $result['confirmedYN'];
                    $data['confirmedByEmpID'] = $result['confirmedByEmpID'];
                    $data['confirmedByName'] = $result['confirmedByName'];
                    $data['confirmedDate'] = $result['confirmedDate'];
                    $data['approvedYN'] = $result['approvedYN'];
                    $data['approvedDate'] = $result['approvedDate'];
                    $data['approvedbyEmpID'] = $result['approvedbyEmpID'];
                    $data['approvedbyEmpName'] = $result['approvedbyEmpName'];
                    $data['segmentID'] = $result['segmentID'];
                    $data['segmentCode'] = $result['segmentCode'];
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['narration'] = $result['narration'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['expenseGLAutoID'] = $result['expenseGLAutoID'];
                    $data['expenseGLCode'] = $result['expenseGLCode'];
                    $data['expenseSystemGLCode'] = $result['expenseSystemGLCode'];
                    $data['expenseGLDescription'] = $result['expenseGLDescription'];
                    $data['expenseGLType'] = $result['expenseGLType'];
                    $data['revenueGLAutoID'] = $result['revenueGLAutoID'];
                    $data['revenueGLCode'] = $result['revenueGLCode'];
                    $data['revenueSystemGLCode'] = $result['revenueSystemGLCode'];
                    $data['revenueGLDescription'] = $result['revenueGLDescription'];
                    $data['revenueGLType'] = $result['revenueGLType'];
                    $data['assetGLAutoID'] = $result['assetGLAutoID'];
                    $data['assetGLCode'] = $result['assetGLCode'];
                    $data['assetSystemGLCode'] = $result['assetSystemGLCode'];
                    $data['assetGLDescription'] = $result['assetGLDescription'];
                    $data['assetGLType'] = $result['assetGLType'];
                    $data['is_sync'] = $result['is_sync'];
                    $data['id_store'] = $result['id_store'];

                    $itmlgr = $this->db->insert('srp_erp_itemledger', $data);
                }

            }
        }

        //echo $this->db->last_query();
        return $itmlgr;
    }


    function update_generalLedger_fromReview_creditsales($shiftID, $menuSalesID, $documentMasterAutoID)
    {
        $outletID = get_outletID();
        $where = '';
        if ($menuSalesID > 0) {
            $where = ' AND pos_menusalesID=' . $menuSalesID;
        }

        $companyID = current_companyID();

        $results = $this->db->query("SELECT srp_erp_generalledger_review.*,srp_erp_pos_menusalesmaster.documentMasterAutoID AS docID,
	srp_erp_pos_menusalesmaster.documentSystemCode AS docCode,sum(srp_erp_generalledger_review.transactionAmount)+ IFNULL(itmexceed.amount,0) as transactionAmountsum,sum(srp_erp_generalledger_review.companyLocalAmount)+ IFNULL(itmexceed.amount,0) as companyLocalAmountsum,sum(srp_erp_generalledger_review.companyReportingAmount)+ IFNULL(itmexceed.amount,0) as companyReportingAmountsum FROM srp_erp_generalledger_review
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS amount,
		IFNULL(SUM(exceededQty), 0) AS excdqty,
		srp_erp_itemexceeded.warehouseAutoID,
		assetGLAutoID,
		documentAutoID,
		itemAutoID,
		srp_erp_pos_menusalesmaster.menuSalesID AS menuslsid
	FROM
		srp_erp_itemexceeded
	LEFT JOIN srp_erp_pos_menusalesmaster ON srp_erp_itemexceeded.documentAutoID = srp_erp_pos_menusalesmaster.documentMasterAutoID
	WHERE
		srp_erp_itemexceeded.documentCode = 'CINV'
	AND srp_erp_itemexceeded.documentAutoID = '" . $documentMasterAutoID . "'
	AND srp_erp_itemexceeded.warehouseAutoID = '" . $outletID . "'
	GROUP BY
		srp_erp_itemexceeded.documentAutoID,
		srp_erp_itemexceeded.warehouseAutoID,
		srp_erp_itemexceeded.assetGLAutoID
) itmexceed ON srp_erp_generalledger_review.pos_menusalesID = itmexceed.menuslsid 
AND srp_erp_generalledger_review.warehouseAutoID = itmexceed.warehouseAutoID 
AND srp_erp_generalledger_review.GLAutoID=itmexceed.assetGLAutoID

LEFT JOIN srp_erp_pos_menusalesmaster ON srp_erp_generalledger_review.pos_menusalesID = srp_erp_pos_menusalesmaster.menuSalesID
AND srp_erp_generalledger_review.wareHouseAutoID = srp_erp_pos_menusalesmaster.wareHouseAutoID

WHERE srp_erp_generalledger_review.companyID = $companyID AND srp_erp_pos_menusalesmaster.isVoid=0 AND srp_erp_generalledger_review.warehouseAutoID =  $outletID AND srp_erp_generalledger_review.pos_shiftID = $shiftID AND srp_erp_generalledger_review.isCreditSales = 1 AND srp_erp_generalledger_review.documentMasterAutoID = '" . $documentMasterAutoID . "' $where GROUP BY GLAutoID,pos_shiftID")->result_array();
        $genlgr = '';
        if (!empty($results)) {
            foreach ($results as $result) {
                if ($result['transactionAmountsum'] != 0) {
                    $data['wareHouseAutoID'] = $result['wareHouseAutoID'];
                    $data['documentCode'] = $result['documentCode'];
                    $data['documentMasterAutoID'] = $result['docID'];
                    $data['documentDetailAutoID'] = $result['documentDetailAutoID'];
                    $data['documentSystemCode'] = $result['docCode'];
                    $data['documentType'] = $result['documentType'];
                    $data['documentDate'] = $result['documentDate'];
                    $data['documentYear'] = $result['documentYear'];
                    $data['documentMonth'] = $result['documentMonth'];
                    $data['projectID'] = $result['projectID'];
                    $data['projectExchangeRate'] = $result['projectExchangeRate'];
                    $data['documentNarration'] = $result['documentNarration'];
                    $data['chequeNumber'] = $result['chequeNumber'];
                    $data['GLAutoID'] = $result['GLAutoID'];
                    $data['systemGLCode'] = $result['systemGLCode'];
                    $data['GLCode'] = $result['GLCode'];
                    $data['GLDescription'] = $result['GLDescription'];
                    $data['GLType'] = $result['GLType'];
                    $data['amount_type'] = $result['amount_type'];
                    $data['isFromItem'] = $result['isFromItem'];
                    $data['transactionCurrencyID'] = $result['transactionCurrencyID'];
                    $data['transactionCurrency'] = $result['transactionCurrency'];
                    $data['transactionExchangeRate'] = $result['transactionExchangeRate'];
                    $data['transactionAmount'] = $result['transactionAmountsum'];
                    $data['transactionCurrencyDecimalPlaces'] = $result['transactionCurrencyDecimalPlaces'];
                    $data['companyLocalCurrencyID'] = $result['companyLocalCurrencyID'];
                    $data['companyLocalCurrency'] = $result['companyLocalCurrency'];
                    $data['companyLocalExchangeRate'] = $result['companyLocalExchangeRate'];
                    $data['companyLocalAmount'] = $result['companyLocalAmountsum'];
                    $data['companyLocalCurrencyDecimalPlaces'] = $result['companyLocalCurrencyDecimalPlaces'];
                    $data['companyReportingCurrencyID'] = $result['companyReportingCurrencyID'];
                    $data['companyReportingCurrency'] = $result['companyReportingCurrency'];
                    $data['companyReportingExchangeRate'] = $result['companyReportingExchangeRate'];
                    $data['companyReportingAmount'] = $result['companyReportingAmountsum'];
                    $data['companyReportingCurrencyDecimalPlaces'] = $result['companyReportingCurrencyDecimalPlaces'];
                    $data['partyContractID'] = $result['partyContractID'];
                    $data['partyType'] = $result['partyType'];
                    $data['partyAutoID'] = $result['partyAutoID'];
                    $data['partySystemCode'] = $result['partySystemCode'];
                    $data['partyName'] = $result['partyName'];
                    $data['partyCurrencyID'] = $result['partyCurrencyID'];
                    $data['partyCurrency'] = $result['partyCurrency'];
                    $data['partyExchangeRate'] = $result['partyExchangeRate'];
                    $data['partyCurrencyAmount'] = $result['partyCurrencyAmount'];
                    $data['partyCurrencyDecimalPlaces'] = $result['partyCurrencyDecimalPlaces'];
                    $data['subLedgerType'] = $result['subLedgerType'];
                    $data['subLedgerDesc'] = $result['subLedgerDesc'];
                    $data['taxMasterAutoID'] = $result['taxMasterAutoID'];
                    $data['partyVatIdNo'] = $result['partyVatIdNo'];
                    $data['is_sync'] = $result['is_sync'];
                    $data['id_store'] = $result['id_store'];
                    $data['isAddon'] = $result['isAddon'];
                    $data['confirmedByEmpID'] = $result['confirmedByEmpID'];
                    $data['confirmedByName'] = $result['confirmedByName'];
                    $data['confirmedDate'] = $result['confirmedDate'];
                    $data['approvedDate'] = $result['approvedDate'];
                    $data['approvedbyEmpID'] = $result['approvedbyEmpID'];
                    $data['approvedbyEmpName'] = $result['approvedbyEmpName'];
                    $data['segmentID'] = $result['segmentID'];
                    $data['segmentCode'] = $result['segmentCode'];
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['createdUserName'] = $this->common_data['current_user'];

                    $genlgr = $this->db->insert('srp_erp_generalledger', $data);
                }

            }

        }

        //echo $this->db->last_query();
        return $genlgr;
    }

}