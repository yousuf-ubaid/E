<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MFQ_Dashboard_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function fetch_jobs()
    {
        $year = date('Y');
        //$sql = "SELECT a.description,a.jan,a.feb,a.mar,a.apr,a.may,a.jun,a.jul,a.aug,a.sept,a.oct,a.nov,a.dece FROM ((SELECT 'Ongoing' as description,SUM(CASE WHEN MONTH (startDate) = 1 THEN 1 ELSE 0 END) as jan,SUM(CASE WHEN MONTH (startDate) = 2 THEN 1 ELSE 0 END) as feb,SUM(CASE WHEN MONTH (startDate) = 3 THEN 1 ELSE 0 END) as mar,SUM(CASE WHEN MONTH (startDate) = 4 THEN 1 ELSE 0 END) as apr,SUM(CASE WHEN MONTH (startDate) = 5 THEN 1 ELSE 0 END) as may,SUM(CASE WHEN MONTH (startDate) = 6 THEN 1 ELSE 0 END) as jun,SUM(CASE WHEN MONTH (startDate) = 7 THEN 1 ELSE 0 END) as jul,SUM(CASE WHEN MONTH (startDate) = 8 THEN 1 ELSE 0 END) as aug,SUM(CASE WHEN MONTH (startDate) = 9 THEN 1 ELSE 0 END) as sept,SUM(CASE WHEN MONTH (startDate) = 10 THEN 1 ELSE 0 END) as oct,SUM(CASE WHEN MONTH (startDate) = 11 THEN 1 ELSE 0 END) as nov,SUM(CASE WHEN MONTH (startDate) = 12 THEN 1 ELSE 0 END) as dece FROM srp_erp_mfq_job LEFT JOIN (SELECT jobID,COUNT(*) as totCount,SUM(if(status = 1,1,0)) as completedCount,(SUM(if(status = 1,1,0))/COUNT(*)) * 100 as percentage FROM srp_erp_mfq_workflowstatus WHERE companyID = " . current_companyID() . "  GROUP BY jobID) ws ON ws.jobID = srp_erp_mfq_job.workProcessID WHERE ws.percentage < 100 AND YEAR(startDate) = $year AND srp_erp_mfq_job.companyID = " . current_companyID() . " ) UNION ALL (SELECT 'Completed' as description,SUM(CASE WHEN MONTH (startDate) = 1 THEN 1 ELSE 0 END) as jan,SUM(CASE WHEN MONTH (startDate) = 2 THEN 1 ELSE 0 END) as feb,SUM(CASE WHEN MONTH (startDate) = 3 THEN 1 ELSE 0 END) as mar,SUM(CASE WHEN MONTH (startDate) = 4 THEN 1 ELSE 0 END) as apr,SUM(CASE WHEN MONTH (startDate) = 5 THEN 1 ELSE 0 END) as may,SUM(CASE WHEN MONTH (startDate) = 6 THEN 1 ELSE 0 END) as jun,SUM(CASE WHEN MONTH (startDate) = 7 THEN 1 ELSE 0 END) as jul,SUM(CASE WHEN MONTH (startDate) = 8 THEN 1 ELSE 0 END) as aug,SUM(CASE WHEN MONTH (startDate) = 9 THEN 1 ELSE 0 END) as sept,SUM(CASE WHEN MONTH (startDate) = 10 THEN 1 ELSE 0 END) as oct,SUM(CASE WHEN MONTH (startDate) = 11 THEN 1 ELSE 0 END) as nov,SUM(CASE WHEN MONTH (startDate) = 12 THEN 1 ELSE 0 END) as dece FROM srp_erp_mfq_job LEFT JOIN (SELECT jobID,COUNT(*) as totCount,SUM(if(status = 1,1,0)) as completedCount,(SUM(if(status = 1,1,0))/COUNT(*)) * 100 as percentage FROM srp_erp_mfq_workflowstatus WHERE companyID = " . current_companyID() . "  GROUP BY jobID) ws ON ws.jobID = srp_erp_mfq_job.workProcessID WHERE ws.percentage = 100 AND YEAR(startDate) = $year AND srp_erp_mfq_job.companyID = " . current_companyID() . ")) as a";
        $sql = "Select description,sum(jan) as jan,sum(feb) as feb,sum(mar) as mar,sum(apr) as apr,sum(may) as may,sum(jun) as jun,sum(jul) as jul,sum(aug) as aug,sum(sept) as sept,sum(oct) as oct,sum(nov) as nov,sum(dece) as dece  from (SELECT
a.description,a.jan,a.feb,a.mar,a.apr,a.may,a.jun,
	a.jul,
	a.aug,
	a.sept,
	a.oct,
	a.nov,
	a.dece
FROM
	(
	(
SELECT
	'Ongoing' AS description,
	SUM( CASE WHEN MONTH ( startDate ) = 1 THEN 1 ELSE 0 END ) AS jan,
	SUM( CASE WHEN MONTH ( startDate ) = 2 THEN 1 ELSE 0 END ) AS feb,
	SUM( CASE WHEN MONTH ( startDate ) = 3 THEN 1 ELSE 0 END ) AS mar,
	SUM( CASE WHEN MONTH ( startDate ) = 4 THEN 1 ELSE 0 END ) AS apr,
	SUM( CASE WHEN MONTH ( startDate ) = 5 THEN 1 ELSE 0 END ) AS may,
	SUM( CASE WHEN MONTH ( startDate ) = 6 THEN 1 ELSE 0 END ) AS jun,
	SUM( CASE WHEN MONTH ( startDate ) = 7 THEN 1 ELSE 0 END ) AS jul,
	SUM( CASE WHEN MONTH ( startDate ) = 8 THEN 1 ELSE 0 END ) AS aug,
	SUM( CASE WHEN MONTH ( startDate ) = 9 THEN 1 ELSE 0 END ) AS sept,
	SUM( CASE WHEN MONTH ( startDate ) = 10 THEN 1 ELSE 0 END ) AS oct,
	SUM( CASE WHEN MONTH ( startDate ) = 11 THEN 1 ELSE 0 END ) AS nov,
	SUM( CASE WHEN MONTH ( startDate ) = 12 THEN 1 ELSE 0 END ) AS dece
FROM
	srp_erp_mfq_job
	LEFT JOIN (
SELECT
	jobID,
	COUNT( * ) AS totCount,
	SUM( IF ( STATUS = 1, 1, 0 ) ) AS completedCount,
	( SUM( IF ( STATUS = 1, 1, 0 ) ) / COUNT( * ) ) * 100 AS percentage
FROM
	srp_erp_mfq_workflowstatus
WHERE
	companyID = " . current_companyID() . "
GROUP BY
	jobID
	) ws ON ws.jobID = srp_erp_mfq_job.workProcessID
WHERE
	ws.percentage < 100
	AND YEAR ( startDate ) = $year
	AND srp_erp_mfq_job.companyID = " . current_companyID() . "
	) UNION ALL
	(
SELECT
	'Completed' AS description,
	SUM( CASE WHEN MONTH ( startDate ) = 1 THEN 1 ELSE 0 END ) AS jan,
	SUM( CASE WHEN MONTH ( startDate ) = 2 THEN 1 ELSE 0 END ) AS feb,
	SUM( CASE WHEN MONTH ( startDate ) = 3 THEN 1 ELSE 0 END ) AS mar,
	SUM( CASE WHEN MONTH ( startDate ) = 4 THEN 1 ELSE 0 END ) AS apr,
	SUM( CASE WHEN MONTH ( startDate ) = 5 THEN 1 ELSE 0 END ) AS may,
	SUM( CASE WHEN MONTH ( startDate ) = 6 THEN 1 ELSE 0 END ) AS jun,
	SUM( CASE WHEN MONTH ( startDate ) = 7 THEN 1 ELSE 0 END ) AS jul,
	SUM( CASE WHEN MONTH ( startDate ) = 8 THEN 1 ELSE 0 END ) AS aug,
	SUM( CASE WHEN MONTH ( startDate ) = 9 THEN 1 ELSE 0 END ) AS sept,
	SUM( CASE WHEN MONTH ( startDate ) = 10 THEN 1 ELSE 0 END ) AS oct,
	SUM( CASE WHEN MONTH ( startDate ) = 11 THEN 1 ELSE 0 END ) AS nov,
	SUM( CASE WHEN MONTH ( startDate ) = 12 THEN 1 ELSE 0 END ) AS dece
FROM
	srp_erp_mfq_job
	LEFT JOIN (
SELECT
	jobID,
	COUNT( * ) AS totCount,
	SUM( IF ( STATUS = 1, 1, 0 ) ) AS completedCount,
	( SUM( IF ( STATUS = 1, 1, 0 ) ) / COUNT( * ) ) * 100 AS percentage
FROM
	srp_erp_mfq_workflowstatus
WHERE
	companyID = " . current_companyID() . "
GROUP BY
	jobID
	) ws ON ws.jobID = srp_erp_mfq_job.workProcessID
WHERE
	ws.percentage = 100
	AND YEAR ( startDate ) = $year
	AND srp_erp_mfq_job.companyID = " . current_companyID() . "
	)
	) AS a

	UNION


	SELECT
	b.description,
	b.jan,
	b.feb,
	b.mar,
	b.apr,
	b.may,
	b.jun,
	b.jul,
	b.aug,
	b.sept,
	b.oct,
	b.nov,
	b.dece
FROM
	(
	(
SELECT
	'Ongoing' AS description,
	SUM( CASE WHEN MONTH ( documentDate ) = 1 THEN 1 ELSE 0 END ) AS jan,
	SUM( CASE WHEN MONTH ( documentDate ) = 2 THEN 1 ELSE 0 END ) AS feb,
	SUM( CASE WHEN MONTH ( documentDate ) = 3 THEN 1 ELSE 0 END ) AS mar,
	SUM( CASE WHEN MONTH ( documentDate ) = 4 THEN 1 ELSE 0 END ) AS apr,
	SUM( CASE WHEN MONTH ( documentDate ) = 5 THEN 1 ELSE 0 END ) AS may,
	SUM( CASE WHEN MONTH ( documentDate ) = 6 THEN 1 ELSE 0 END ) AS jun,
	SUM( CASE WHEN MONTH ( documentDate ) = 7 THEN 1 ELSE 0 END ) AS jul,
	SUM( CASE WHEN MONTH ( documentDate ) = 8 THEN 1 ELSE 0 END ) AS aug,
	SUM( CASE WHEN MONTH ( documentDate ) = 9 THEN 1 ELSE 0 END ) AS sept,
	SUM( CASE WHEN MONTH ( documentDate ) = 10 THEN 1 ELSE 0 END ) AS oct,
	SUM( CASE WHEN MONTH ( documentDate ) = 11 THEN 1 ELSE 0 END ) AS nov,
	SUM( CASE WHEN MONTH ( documentDate ) = 12 THEN 1 ELSE 0 END ) AS dece
FROM
	srp_erp_mfq_standardjob

WHERE
	srp_erp_mfq_standardjob.completionPercenatage < 100
	AND YEAR ( documentDate ) = $year
	AND srp_erp_mfq_standardjob.companyID = " . current_companyID() . "
	) UNION ALL
	(
SELECT
	'Completed' AS description,
	SUM( CASE WHEN MONTH ( documentDate ) = 1 THEN 1 ELSE 0 END ) AS jan,
	SUM( CASE WHEN MONTH ( documentDate ) = 2 THEN 1 ELSE 0 END ) AS feb,
	SUM( CASE WHEN MONTH ( documentDate ) = 3 THEN 1 ELSE 0 END ) AS mar,
	SUM( CASE WHEN MONTH ( documentDate ) = 4 THEN 1 ELSE 0 END ) AS apr,
	SUM( CASE WHEN MONTH ( documentDate ) = 5 THEN 1 ELSE 0 END ) AS may,
	SUM( CASE WHEN MONTH ( documentDate ) = 6 THEN 1 ELSE 0 END ) AS jun,
	SUM( CASE WHEN MONTH ( documentDate ) = 7 THEN 1 ELSE 0 END ) AS jul,
	SUM( CASE WHEN MONTH ( documentDate ) = 8 THEN 1 ELSE 0 END ) AS aug,
	SUM( CASE WHEN MONTH ( documentDate ) = 9 THEN 1 ELSE 0 END ) AS sept,
	SUM( CASE WHEN MONTH ( documentDate ) = 10 THEN 1 ELSE 0 END ) AS oct,
	SUM( CASE WHEN MONTH ( documentDate ) = 11 THEN 1 ELSE 0 END ) AS nov,
	SUM( CASE WHEN MONTH ( documentDate ) = 12 THEN 1 ELSE 0 END ) AS dece
FROM
	srp_erp_mfq_standardjob

WHERE
	srp_erp_mfq_standardjob.completionPercenatage = 100
	AND YEAR ( documentDate ) = $year
	AND srp_erp_mfq_standardjob.companyID = " . current_companyID() . "
	)
	) AS b) as tbl1 group by description";
        $result = $this->db->query($sql)->result_array();
        return $result;
    }


    function pull_from_erp()
    {
        $currentDB = $this->db->database;
        $gearsDB = $this->load->database('gearserp', true);
        $gearsDB->trans_start();
        /*link chartofaccount*/
        $gearsDB->query('INSERT INTO ' . $currentDB . '.srp_erp_chartofaccounts (GLAutoID, systemAccountCode, GLSecondaryCode,GLDescription,masterAutoID,masterAccount,masterAccountDescription,masterCategory,accountCategoryTypeID,CategoryTypeDescription,subCategory,controllAccountYN,isActive,companyID) SELECT AccountCode as GLAutoID,null as systemAccountCode,AccountCode as GLSecondaryCode,AccountDescription as GLDescription,masterAccount as masterAutoID,NULL as masterAccount,null as masterAccountDescription,catogaryBLorPL as masterCategory,null as accountCategoryTypeID,null as CategoryTypeDescription,controlAccounts as subCategory,controllAccountYN,isActive,' . current_companyID() . ' FROM gearserp.chartofaccountsassigned WHERE NOT EXISTS(SELECT * FROM ' . $currentDB . '.srp_erp_chartofaccounts WHERE ' . $currentDB . '.srp_erp_chartofaccounts.GLAutoID = gearserp.chartofaccountsassigned.AccountCode AND companyID = ' . current_companyID() . ') AND companyID="HEMT"');

        /*link customers*/
        $gearsDB->query('INSERT INTO ' . $currentDB . '.srp_erp_customermaster (customerAutoID, customerSystemCode, customerName,receivableAutoID,customerAddress1,customerAddress2,customerCountry,customerCreditLimit,isActive,companyID) SELECT customerCodeSystem as customerAutoID,CutomerCode as customerSystemCode,CustomerName as customerName,custGLaccount AS receivableAutoID,customerAddress1,customerAddress2,countryName as customerCountry,creditLimit as customerCreditLimit,isActive,' . current_companyID() . ' FROM gearserp.customerassigned LEFT JOIN gearserp.countrymaster ON countrycode = customerCountry WHERE NOT EXISTS(SELECT * FROM ' . $currentDB . '.srp_erp_customermaster WHERE ' . $currentDB . '.srp_erp_customermaster.customerAutoID = gearserp.customerassigned.customerCodeSystem  AND companyID = ' . current_companyID() . ') AND companyID="HEMT"');

        /*link mfq customers*/
        $gearsDB->query('INSERT INTO ' . $currentDB . '.srp_erp_mfq_customermaster (CustomerAutoID, customerSystemCode, customerName,customerAddress1,customerAddress2,customerCountry,isActive,companyID,isFromERP) SELECT customerAutoID, customerSystemCode, customerName,customerAddress1,customerAddress2,customerCountry,isActive,companyID,1 FROM ' . $currentDB . '.srp_erp_customermaster WHERE NOT EXISTS(SELECT * FROM ' . $currentDB . '.srp_erp_mfq_customermaster WHERE ' . $currentDB . '.srp_erp_customermaster.customerAutoID = ' . $currentDB . '.srp_erp_mfq_customermaster.CustomerAutoID  AND companyID = ' . current_companyID() . ') AND companyID = ' . current_companyID());

        /*link unit of measure*/
        $gearsDB->query('INSERT INTO ' . $currentDB . '.srp_erp_unit_of_measure (UnitID, UnitShortCode, UnitDes,companyID) SELECT UnitID, UnitShortCode, UnitDes,' . current_companyID() . ' FROM gearserp.units WHERE NOT EXISTS(SELECT * FROM ' . $currentDB . '.srp_erp_unit_of_measure WHERE ' . $currentDB . '.srp_erp_unit_of_measure.UnitID = gearserp.units.UnitID  AND companyID = ' . current_companyID() . ')');

        /*link warehouse*/
        $gearsDB->query('INSERT INTO ' . $currentDB . '.srp_erp_warehousemaster (wareHouseAutoID, wareHouseCode, wareHouseDescription,wareHouseLocation,companyID) SELECT wareHouseSystemCode, wareHouseCode, wareHouseDescription,locationName,' . current_companyID() . ' FROM gearserp.warehousemaster LEFT JOIN gearserp.erp_location ON gearserp.erp_location.locationID = gearserp.warehousemaster.wareHouseLocation WHERE NOT EXISTS(SELECT * FROM ' . $currentDB . '.srp_erp_warehousemaster WHERE ' . $currentDB . '.srp_erp_warehousemaster.wareHouseAutoID = gearserp.warehousemaster.wareHouseSystemCode  AND companyID = ' . current_companyID() . ')');

        /*link mfq warehouse*/
        $gearsDB->query('INSERT INTO ' . $currentDB . '.srp_erp_mfq_warehousemaster (wareHouseAutoID, wareHouseCode, wareHouseDescription,wareHouseLocation,companyID,isFromERP) SELECT wareHouseAutoID, wareHouseCode, wareHouseDescription,wareHouseLocation,companyID,1 FROM ' . $currentDB . '.srp_erp_warehousemaster WHERE NOT EXISTS(SELECT * FROM ' . $currentDB . '.srp_erp_mfq_warehousemaster WHERE ' . $currentDB . '.srp_erp_warehousemaster.wareHouseAutoID = ' . $currentDB . '.srp_erp_mfq_warehousemaster.wareHouseAutoID  AND companyID = ' . current_companyID() . ')');

        /*link serviceline*/
        $gearsDB->query('INSERT INTO ' . $currentDB . '.srp_erp_segment (segmentCode, description,companyID) SELECT ServiceLineCode, ServiceLineDes,' . current_companyID() . ' FROM gearserp.serviceline WHERE NOT EXISTS(SELECT * FROM ' . $currentDB . '.srp_erp_segment WHERE ' . $currentDB . '.srp_erp_segment.segmentCode = gearserp.serviceline.ServiceLineCode AND companyID = ' . current_companyID() . ') AND companyID="HEMT"');

        /*link mfq serviceline*/
        $gearsDB->query('INSERT INTO ' . $currentDB . '.srp_erp_mfq_segment (segmentID,segmentCode, description,companyID,isFromERP) SELECT segmentID,segmentCode, description,companyID,1 FROM ' . $currentDB . '.srp_erp_segment WHERE NOT EXISTS(SELECT * FROM ' . $currentDB . '.srp_erp_mfq_segment WHERE ' . $currentDB . '.srp_erp_mfq_segment.segmentID = ' . $currentDB . '.srp_erp_segment.segmentID AND companyID = ' . current_companyID() . ') AND companyID=' . current_companyID());

        /*link financecategory*/
        /* $gearsDB->query('INSERT INTO srp_erp_mfq_itemcategory (itemCategoryID, description,companyID,categoryType) SELECT itemCategoryID, categoryDescription,' . current_companyID() . ',1 FROM gearserp.financeitemcategorymaster WHERE NOT EXISTS(SELECT * FROM srp_erp_mfq_itemcategory WHERE srp_erp_mfq_itemcategory.itemCategoryID = gearserp.financeitemcategorymaster.itemCategoryID AND companyID = ' . current_companyID() . ') AND itemCategoryID = 1');*/

        /*link fixed asset*/
        /*$gearsDB->query('INSERT INTO srp_erp_fa_asset_master (faID, segmentID,segmentCode,docOriginSystemCode,docOrigin,docOriginDetailID,documentID,faAssetDept,serialNo,faCode,assetCodeS,faUnitSerialNo,assetDescription,comments,groupTO,dateAQ,dateDEP,depMonth,DEPpercentage,faCatID,faSubCatID,faSubCatID2,faSubCatID3,transactionAmount,companyLocalAmount,companyReportingAmount,auditCategory,partNumber,manufacture,unitAssign,unitAssignHistory,image,usedBy,usedByHistory,location,currentLocation,locationHistory,costGLAutoID,costGLCodeDes,ACCDEPGLAutoID,ACCDEPGLCODEdes,DEPGLAutoID,DEPGLCODEdes,companyID) SELECT faID,srp_erp_segment.segmentID,serviceLineCode,docOriginSystemCode,docOrigin,docOriginDetailID,documentID,faAssetDept,serialNo,faCode,assetCodeS,faUnitSerialNo,assetDescription,COMMENTS,groupTO,dateAQ,dateDEP,depMonth,DEPpercentage,faCatID,faSubCatID,faSubCatID2,faSubCatID3,COSTUNIT,COSTUNIT,costUnitRpt,AUDITCATOGARY,PARTNUMBER,MANUFACTURE,UNITASSIGN,UHITASSHISTORY,IMAGE,USEDBY,USEBYHISTRY,LOCATION,currentLocation,LOCATIONHISTORY,COSTGLCODE,COSTGLCODEdes,ACCDEPGLCODE,ACCDEPGLCODEdes,DEPGLCODE,DEPGLCODEdes,' . current_companyID() . ' FROM gearserp.erp_fa_asset_master LEFT JOIN srp_erp_segment ON srp_erp_segment.segmentCode = gearserp.erp_fa_asset_master.serviceLineCode WHERE NOT EXISTS(SELECT * FROM srp_erp_fa_asset_master WHERE srp_erp_fa_asset_master.faID = gearserp.erp_fa_asset_master.faID AND companyID = ' . current_companyID() . ') AND itemCategoryID = 1');*/


        /*link itemmaster*/
        $gearsDB->query('INSERT INTO ' . $currentDB . '.srp_erp_itemmaster (itemAutoID, itemSystemCode, seconeryItemCode,itemImage,itemName,itemDescription,mainCategoryID,mainCategory,subcategoryID,subSubCategoryID,itemUrl,barcode,financeCategory,partNo,defaultUnitOfMeasureID,defaultUnitOfMeasure,currentStock,reorderPoint,maximunQty,minimumQty,costGLAutoID,costSystemGLCode,costGLCode,costDescription,assteGLAutoID,assteSystemGLCode,assteGLCode,assteDescription,companyLocalWacAmount,companyReportingWacAmount,isActive,comments,companyID,companyCode) SELECT itmass.itemCodeSystem as itemAutoID,itmass.itemPrimaryCode as itemSystemCode,itmass.secondaryItemCode as seconeryItemCode,master.itemPicture as itemImage,master.itemShortDescription as itemName,itmass.itemDescription as itemDescription,itmass.financeCategoryMaster as mainCategoryID,financeitemcategorymaster.categoryDescription as mainCategory,NULL as subcategoryID,NULL as subSubCategoryID,NULL as itemUrl,itmass.barcode,(CASE itmass.financeCategoryMaster WHEN 1 then 1 WHEN 2 OR 4 then 2 ELSE 3 END) as financeCategory,itmass.secondaryItemCode as partNo,itmass.itemUnitOfMeasure as defaultUnitOfMeasureID,unit.UnitDes as defaultUnitOfMeasure,itmled.currentStock,itmass.rolQuantity as reorderPoint,itmass.maximunQty,itmass.minimumQty,financeGLcodePL as costGLAutoID,financeGLcodePL as costSystemGLCode,financeGLcodePL as costGLCode,financeitemcategorysub.categoryDescription as costDescription,financeGLcodePL as assteGLAutoID,financeGLcodePL as assteSystemGLCode,financeGLcodePL as assteGLCode,financeitemcategorysub.categoryDescription as assteDescription, itmass.wacValueLocal as companyLocalWacAmount,itmass.wacValueReporting as companyReportingWacAmount,itmass.isActive,master.itemShortDescription as comments,' . current_companyID() . ',"' . current_companyCode() . '" FROM gearserp.itemassigned itmass LEFT JOIN gearserp.itemmaster master ON master.itemCodeSystem = itmass.itemCodeSystem LEFT JOIN gearserp.units unit ON itmass.itemUnitOfMeasure = unit.UnitID LEFT JOIN (SELECT SUM(inOutQty) as currentStock,itemSystemCode FROM gearserp.erp_itemledger WHERE companyID = "HEMT" GROUP BY itemSystemCode) itmled ON itmled.itemSystemCode = itmass.itemCodeSystem LEFT JOIN gearserp.financeitemcategorymaster ON financeitemcategorymaster.itemCategoryID = master.financeCategoryMaster LEFT JOIN gearserp.financeitemcategorysub ON financeitemcategorysub.itemCategorySubID = itmass.financeCategorySub  WHERE NOT EXISTS(SELECT * FROM ' . $currentDB . '.srp_erp_itemmaster WHERE ' . $currentDB . '.srp_erp_itemmaster.itemAutoID = itmass.itemCodeSystem AND companyID = ' . current_companyID() . ') AND itmass.companyID="HEMT" AND (itmass.financeCategoryMaster = 1 OR itmass.financeCategoryMaster = 2)');

        /*link mfq itemmaster*/
        $gearsDB->query('INSERT INTO ' . $currentDB . '.srp_erp_mfq_itemmaster (itemAutoID, itemSystemCode, secondaryItemCode,itemImage,itemName,itemDescription,mainCategoryID,mainCategory,subcategoryID,subSubCategoryID,itemUrl,barcode,financeCategory,partNo,defaultUnitOfMeasureID,defaultUnitOfMeasure,currentStock,reorderPoint,maximunQty,minimumQty,costGLAutoID,costSystemGLCode,costGLCode,costDescription,assetGLAutoID,assetSystemGLCode,assetGLCode,assetDescription,companyLocalWacAmount,companyReportingWacAmount,isActive,comments,companyID,companyCode,isFromERP) SELECT itemAutoID, itemSystemCode, seconeryItemCode,itemImage,itemName,itemDescription,mainCategoryID,mainCategory,subcategoryID,subSubCategoryID,itemUrl,barcode,financeCategory,partNo,defaultUnitOfMeasureID,defaultUnitOfMeasure,currentStock,reorderPoint,maximunQty,minimumQty,costGLAutoID,costSystemGLCode,costGLCode,costDescription,assteGLAutoID,assteSystemGLCode,assteGLCode,assteDescription,companyLocalWacAmount,companyReportingWacAmount,isActive,comments,companyID,companyCode,1 FROM ' . $currentDB . '.srp_erp_itemmaster WHERE NOT EXISTS(SELECT * FROM ' . $currentDB . '.srp_erp_mfq_itemmaster WHERE ' . $currentDB . '.srp_erp_mfq_itemmaster.itemAutoID = ' . $currentDB . '.srp_erp_itemmaster.itemAutoID AND companyID = ' . current_companyID() . ')');

        /*link uom conversion*/
        $gearsDB->query('INSERT INTO ' . $currentDB . '.srp_erp_unitsconversion (unitsConversionAutoID,masterUnitID, subUnitID,conversion,companyID) SELECT unitsConversionAutoID,masterUnitID, subUnitID,conversion,' . current_companyID() . ' FROM gearserp.erp_unitsconversion WHERE NOT EXISTS(SELECT * FROM ' . $currentDB . '.srp_erp_unitsconversion WHERE ' . $currentDB . '.srp_erp_unitsconversion.unitsConversionAutoID = gearserp.erp_unitsconversion.unitsConversionAutoID AND companyID = ' . current_companyID() . ')');

        $gearsDB->trans_complete();
        if ($gearsDB->trans_status() === FALSE) {
            $gearsDB->trans_rollback();
            return array('e', "Error Occurred");
        } else {
            $gearsDB->trans_commit();
            return array('s', "Successfully data pulled");
        }
    }

    function update_wac_from_erp()
    {
        $currentDB = $this->db->database;
        $gearsDB = $this->load->database('gearserp', TRUE);
        $gearsDB->trans_start();
        $gearsDB->query("UPDATE $currentDB.srp_erp_itemmaster 
    LEFT JOIN (SELECT itmled.currentStock,itmass.wacValueLocal,itmass.wacValueReporting,itmled.itemSystemCode FROM gearserp.itemassigned as itmass LEFT JOIN (SELECT SUM(inOutQty) as currentStock,itemSystemCode FROM gearserp.erp_itemledger WHERE companyID = 'HEMT'  GROUP BY itemSystemCode) itmled ON itmled.itemSystemCode = itmass.itemCodeSystem WHERE companyID='HEMT') master ON master.itemSystemCode = $currentDB.srp_erp_itemmaster.itemAutoID
SET $currentDB.srp_erp_itemmaster.companyLocalWacAmount = master.wacValueLocal,$currentDB.srp_erp_itemmaster.companyReportingWacAmount = master.wacValueReporting,$currentDB.srp_erp_itemmaster.currentStock = master.currentStock WHERE companyID = " . current_companyID());

        $gearsDB->query("UPDATE $currentDB.srp_erp_mfq_itemmaster 
    LEFT JOIN (SELECT itmled.currentStock,itmass.wacValueLocal,itmass.wacValueReporting,itmled.itemSystemCode FROM gearserp.itemassigned as itmass LEFT JOIN (SELECT SUM(inOutQty) as currentStock,itemSystemCode FROM gearserp.erp_itemledger WHERE companyID = 'HEMT'  GROUP BY itemSystemCode) itmled ON itmled.itemSystemCode = itmass.itemCodeSystem WHERE companyID='HEMT') master ON master.itemSystemCode = $currentDB.srp_erp_mfq_itemmaster.itemAutoID
SET $currentDB.srp_erp_mfq_itemmaster.companyLocalWacAmount = master.wacValueLocal,$currentDB.srp_erp_mfq_itemmaster.companyReportingWacAmount = master.wacValueReporting,$currentDB.srp_erp_mfq_itemmaster.currentStock = master.currentStock WHERE companyID = " . current_companyID());

        $gearsDB->trans_complete();
        if ($gearsDB->trans_status() === FALSE) {
            $gearsDB->trans_rollback();
            return array('e', "Error Occurred");
        } else {
            $gearsDB->trans_commit();
            return array('s', "Successfully data pulled");
        }
    }

    function load_erp_warehouse()
    {
        $result = $this->db->query("SELECT
	wareHouseAutoID,companyID,wareHouseCode,wareHouseDescription
FROM
	srp_erp_warehousemaster
WHERE companyID =" . current_companyID())->result_array();
        return $result;
    }

    function pull_from_erp_warehouse()
    {
        $wareHouseAutoID = $this->input->post("warehouseAutoID");
        $currentDB = $this->db->database;
        $gearsDB = $this->load->database('gearserp', TRUE);
        $gearsDB->trans_start();
        /*link itemmaster*/
        $gearsDB->query('INSERT INTO ' . $currentDB . '.srp_erp_warehouseitems (wareHouseAutoID,itemAutoID,unitOfMeasureID,currentStock,companyID) SELECT  wareHouseSystemCode,itemSystemCode,unitOfMeasure,SUM(inOutQty) as currentStock,' . current_companyID() . ' FROM gearserp.erp_itemledger ledg  WHERE NOT EXISTS(SELECT * FROM ' . $currentDB . '.srp_erp_warehouseitems WHERE ' . $currentDB . '.srp_erp_warehouseitems.itemAutoID = ledg.itemSystemCode AND companyID = ' . current_companyID() . ' AND wareHouseAutoID = ' . $wareHouseAutoID . ') AND ledg.companyID="HEMT" AND ledg.wareHouseSystemCode = ' . $wareHouseAutoID . ' GROUP BY itemSystemCode');

        $gearsDB->query("UPDATE $currentDB.srp_erp_warehouseitems
    LEFT JOIN (SELECT SUM(inOutQty) as currentStock,itemSystemCode FROM gearserp.erp_itemledger WHERE companyID = 'HEMT' AND wareHouseSystemCode='.$wareHouseAutoID.' GROUP BY itemSystemCode) master ON master.itemSystemCode = $currentDB.srp_erp_warehouseitems.itemAutoID
SET $currentDB.srp_erp_warehouseitems.currentStock = master.currentStock WHERE wareHouseAutoID = '.$wareHouseAutoID.' AND companyID = " . current_companyID());

        $gearsDB->trans_complete();
        if ($gearsDB->trans_status() === FALSE) {
            $gearsDB->trans_rollback();
            return array('e', "Error Occurred");
        } else {
            $gearsDB->trans_commit();
            return array('s', "Successfully data pulled");
        }
    }

    /** Added */
    function awarded_job_status()
    {
        $clientID = $this->input->post('clientID');
        $segmentID = $this->input->post('segmentID');
        $date = format_date($this->input->post('date'));
        $companyID = current_companyID();
        $currentDate = current_date(false);

        // Open Jobs
        $this->db->select("workProcessID")
            ->from('srp_erp_mfq_job')
            ->where('srp_erp_mfq_job.companyID', $companyID)
            ->where('linkedJobID IS NOT NULL');
        if (!empty($clientID)) {
            $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
        }
        if (!empty($segmentID)) {
            $this->db->where("srp_erp_mfq_job.mfqSegmentID IN (" . implode(',', $segmentID) . ")");
        }
        if (!empty($this->input->post('date'))) {
            $this->db->where("srp_erp_mfq_job.documentDate <= '" . $date . "'");
        }
        $this->db->where('confirmedYN != 1');
        $OpenJobs_arr = $this->db->get()->result_array();
        $OpenJobs = COUNT($OpenJobs_arr);
        $workProcessID_open = join(',', array_column($OpenJobs_arr, 'workProcessID'));

        $OpenJobs_value = 0;
        if (!empty($OpenJobs_arr)) {
            $this->db->select("SUM((totalValue/expectedQty) * qty) as Value")
                ->from('srp_erp_mfq_job')
                ->where('srp_erp_mfq_job.companyID', $companyID);
            $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
						srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
						((((
							discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
							) * IFNULL( totMargin, 0 ))) - (((
							discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
							) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
						))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
					FROM
						srp_erp_mfq_estimatedetail
						JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
						)estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
            $this->db->where('srp_erp_mfq_job.workProcessID IN (' . $workProcessID_open . ')');
            $OpenJobs_value = $this->db->get()->row('Value');
        }

        // Invoiced Jobs
        $this->db->select("workProcessID")
            ->from('srp_erp_mfq_job')
            ->join('(SELECT SUM( deliveredQty ) AS deliveredQty, deliveryNoteID, srp_erp_mfq_deliverynotedetail.jobID 
						FROM srp_erp_mfq_deliverynotedetail
						JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
						WHERE deletedYn != 1 GROUP BY srp_erp_mfq_deliverynotedetail.jobID 
					) dnQty', 'dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty', 'LEFT')
            ->join('srp_erp_mfq_customerinvoicemaster inv', 'inv.deliveryNoteID = dnQty.deliveryNoteID', 'LEFT')
            ->where('srp_erp_mfq_job.companyID', $companyID)
            ->where('linkedJobID IS NOT NULL');
        if (!empty($clientID)) {
            $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
        }
        if (!empty($segmentID)) {
            $this->db->where(" srp_erp_mfq_job.mfqSegmentID IN (" . implode(',', $segmentID) . ")");
        }
        if (!empty($this->input->post('date'))) {
            $this->db->where("srp_erp_mfq_job.documentDate <= '" . $date . "'");
        }
        $this->db->where('invoiceAutoID IS NOT NULL');
        $InvoicedJobs_arr = $this->db->get()->result_array();
        $InvoicedJobs = COUNT($OpenJobs_arr);
        $workProcessID_invoiced = join(',', array_column($InvoicedJobs_arr, 'workProcessID'));

        $InvoicedJobs_value = 0;
        if (!empty($InvoicedJobs_arr)) {
            $this->db->select("SUM((totalValue/expectedQty) * qty) as Value")
                ->from('srp_erp_mfq_job')
                ->where('srp_erp_mfq_job.companyID', $companyID);
            $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
						srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
						((((
							discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
							) * IFNULL( totMargin, 0 ))) - (((
							discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
							) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
						))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
					FROM
						srp_erp_mfq_estimatedetail
						JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
						)estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
            $this->db->where('srp_erp_mfq_job.workProcessID IN (' . $workProcessID_invoiced . ')');
            $InvoicedJobs_value = $this->db->get()->row('Value');
        }

        // Delivered Jobs
        $this->db->select("workProcessID")
            ->from('srp_erp_mfq_job')
            ->join('(SELECT SUM( deliveredQty ) AS deliveredQty, deliveryNoteID, srp_erp_mfq_deliverynotedetail.jobID 
						FROM srp_erp_mfq_deliverynotedetail
						JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
						WHERE deletedYn != 1 GROUP BY srp_erp_mfq_deliverynotedetail.jobID 
					) dnQty', 'dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty')
            ->join('srp_erp_mfq_customerinvoicemaster inv', 'inv.deliveryNoteID = dnQty.deliveryNoteID', 'LEFT')
            ->where('srp_erp_mfq_job.companyID', $companyID)
            ->where('linkedJobID IS NOT NULL');
        if (!empty($clientID)) {
            $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
        }
        if (!empty($segmentID)) {
            $this->db->where(" srp_erp_mfq_job.mfqSegmentID IN (" . implode(',', $segmentID) . ")");
        }
        if (!empty($this->input->post('date'))) {
            $this->db->where("srp_erp_mfq_job.documentDate <= '" . $date . "'");
        }
        $this->db->where('invoiceAutoID IS NULL');
        $DeliveredJobs_arr = $this->db->get()->result_array();
        $DeliveredJobs = COUNT($DeliveredJobs_arr);
        $workProcessID_delivered = join(',', array_column($DeliveredJobs_arr, 'workProcessID'));

        $DeliveredJobs_value = 0;
        if (!empty($DeliveredJobs_arr)) {
            $this->db->select("SUM((totalValue/expectedQty) * qty) as Value")
                ->from('srp_erp_mfq_job')
                ->where('srp_erp_mfq_job.companyID', $companyID);
            $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
						srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
						((((
							discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
							) * IFNULL( totMargin, 0 ))) - (((
							discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
							) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
						))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
					FROM
						srp_erp_mfq_estimatedetail
						JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
						)estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
            $this->db->where('srp_erp_mfq_job.workProcessID IN (' . $workProcessID_delivered . ')');
            $DeliveredJobs_value = $this->db->get()->row('Value');
        }

        // Overdue Jobs
        $this->db->select("workProcessID")
            ->from('srp_erp_mfq_job')
            ->join('(SELECT SUM( deliveredQty ) AS deliveredQty, deliveryNoteID, srp_erp_mfq_deliverynotedetail.jobID 
						FROM srp_erp_mfq_deliverynotedetail
						JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
						WHERE deletedYn != 1 GROUP BY srp_erp_mfq_deliverynotedetail.jobID 
					) dnQty', 'dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty', 'LEFT')
            ->join('srp_erp_mfq_estimatemaster estimate', 'estimate.estimateMasterID = srp_erp_mfq_job.estimateMasterID', 'LEFT')
            ->where('srp_erp_mfq_job.companyID', $companyID)
            ->where('linkedJobID IS NOT NULL')
            ->where('srp_erp_mfq_job.confirmedYN', 1);
        if (!empty($clientID)) {
            $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
        }
        if (!empty($segmentID)) {
            $this->db->where("srp_erp_mfq_job.mfqSegmentID IN (" . implode(',', $segmentID) . ")");
        }
        if (!empty($this->input->post('date'))) {
            $this->db->where("srp_erp_mfq_job.documentDate <= '" . $date . "'");
        }
        $this->db->where('dnQty.deliveryNoteID IS NULL')
            ->where('expectedDeliveryDate <"' . $currentDate . '"');
        $overdueJobs_arr = $this->db->get()->result_array();
        $overdueJobs = COUNT($overdueJobs_arr);
        $workProcessID_overdue = join(',', array_column($overdueJobs_arr, 'workProcessID'));

        $overdueJobs_value = 0;
        if (!empty($overdueJobs_arr)) {
            $this->db->select("SUM((totalValue/expectedQty) * qty) as Value")
                ->from('srp_erp_mfq_job')
                ->where('srp_erp_mfq_job.companyID', $companyID);
            $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
						srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
						((((
							discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
							) * IFNULL( totMargin, 0 ))) - (((
							discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
							) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
						))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
					FROM
						srp_erp_mfq_estimatedetail
						JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
						)estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
            $this->db->where('srp_erp_mfq_job.workProcessID IN (' . $workProcessID_overdue . ')');
            $overdueJobs_value = $this->db->get()->row('Value');
        }

        // Closed Jobs
        $this->db->select("workProcessID")
            ->from('srp_erp_mfq_job')
            ->join('(SELECT SUM( deliveredQty ) AS deliveredQty, deliveryNoteID, srp_erp_mfq_deliverynotedetail.jobID 
						FROM srp_erp_mfq_deliverynotedetail
						JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
						WHERE deletedYn != 1 GROUP BY srp_erp_mfq_deliverynotedetail.jobID 
					) dnQty', 'dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty', 'LEFT')
            ->join('srp_erp_mfq_estimatemaster estimate', 'estimate.estimateMasterID = srp_erp_mfq_job.estimateMasterID', 'LEFT')
            ->where('srp_erp_mfq_job.companyID', $companyID)
            ->where('linkedJobID IS NOT NULL')
            ->where('srp_erp_mfq_job.confirmedYN', 1);
        if (!empty($clientID)) {
            $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
        }
        if (!empty($segmentID)) {
            $this->db->where(" srp_erp_mfq_job.mfqSegmentID IN (" . implode(',', $segmentID) . ")");
        }
        if (!empty($this->input->post('date'))) {
            $this->db->where("srp_erp_mfq_job.documentDate <= '" . $date . "'");
        }
        $this->db->where('dnQty.deliveryNoteID IS NULL')
            ->where('expectedDeliveryDate >="' . $currentDate . '"');
        $closedJobs_arr = $this->db->get()->result_array();
        $closedJobs = COUNT($closedJobs_arr);
        $workProcessID_closed = join(',', array_column($closedJobs_arr, 'workProcessID'));

        $closedJobs_value = 0;
        if (!empty($closedJobs_arr)) {
            $this->db->select("SUM((totalValue/expectedQty) * qty) as Value")
                ->from('srp_erp_mfq_job')
                ->where('srp_erp_mfq_job.companyID', $companyID);
            $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
						srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
						((((
							discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
							) * IFNULL( totMargin, 0 ))) - (((
							discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
							) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
						))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
					FROM
						srp_erp_mfq_estimatedetail
						JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
						)estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
            $this->db->where('srp_erp_mfq_job.workProcessID IN (' . $workProcessID_closed . ')');
            $closedJobs_value = $this->db->get()->row('Value');
        }

        $totalJobs = $OpenJobs_value + $InvoicedJobs_value + $DeliveredJobs_value + $overdueJobs_value + $closedJobs_value;
        $piData = [
            ['name' => 'Open Jobs',
                'count' => (int)$OpenJobs,
                'y' => ROUND((float)$OpenJobs_value, $this->common_data['company_data']['company_default_decimal']),
                'percen' => $totalJobs != 0 ? round((($OpenJobs_value / $totalJobs) * 100), 2) : 0,
                'value' => number_format((float)$OpenJobs_value, $this->common_data['company_data']['company_default_decimal'], '.', ',')
            ],
            ['name' => 'Invoiced Jobs',
                'count' => (int)$InvoicedJobs,
                'y' => ROUND((float)$InvoicedJobs_value, $this->common_data['company_data']['company_default_decimal']),
                'percen' => $InvoicedJobs_value != 0 ? round((($InvoicedJobs_value / $totalJobs) * 100), 2) : 0,
                'value' => number_format((float)$InvoicedJobs_value, $this->common_data['company_data']['company_default_decimal'], '.', ',')
            ],
            ['name' => 'Delivered Jobs',
                'count' => (int)$DeliveredJobs,
                'y' => ROUND((float)$DeliveredJobs_value, $this->common_data['company_data']['company_default_decimal']),
                'percen' => $DeliveredJobs_value != 0 ? round((($DeliveredJobs_value / $totalJobs) * 100), 2) : 0,
                'value' => number_format((float)$DeliveredJobs_value, $this->common_data['company_data']['company_default_decimal'], '.', ',')
            ],
            ['name' => 'Overdue Jobs',
                'count' => (int)$overdueJobs,
                'y' => ROUND((float)$overdueJobs_value, $this->common_data['company_data']['company_default_decimal']),
                'percen' => $overdueJobs_value != 0 ? round((($overdueJobs_value / $totalJobs) * 100), 2) : 0,
                'value' => number_format((float)$overdueJobs_value, $this->common_data['company_data']['company_default_decimal'], '.', ',')
            ],
            ['name' => 'Closed Jobs',
                'count' => (int)$closedJobs,
                'y' => ROUND((float)$closedJobs_value, $this->common_data['company_data']['company_default_decimal']),
                'percen' => $closedJobs_value != 0 ? round((($closedJobs_value / $totalJobs) * 100), 2) : 0,
                'value' => number_format((float)$closedJobs_value, $this->common_data['company_data']['company_default_decimal'], '.', ',')
            ],
        ];
        return $piData;
    }

    function awarded_job_drill_down()
    {
        $clientID = $this->input->post('clientID');
        $awardedType = $this->input->post('awardedType');
        $segmentID = $this->input->post('segmentID');
        $date = format_date($this->input->post('date'));
        $companyID = current_companyID();
        $currentDate = current_date(false);

        // Amount Query based on entry configuration
        $this->load->model('MFQ_Dashboard_modal');
        $qry = $this->MFQ_Dashboard_modal->job_entry_query();
        $query = '';
        if(!empty($qry)) {
            $query = "(SELECT SUM(totalValue) AS wipAmount, workProcessID FROM (" . join(' UNION ', $qry) . ")tbl GROUP BY workProcessID)wipCalculate";
        }

        switch ($awardedType)
        {
            case 'Open Jobs':
                $this->db->select("workProcessID, ((totalValue/expectedQty) * qty) as amount, documentCode, documentDate, qty")
                ->from('srp_erp_mfq_job')
                ->where('srp_erp_mfq_job.companyID', $companyID);
                $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
                                            srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
                                            ((((
                                                discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                                    discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                                ) * IFNULL( totMargin, 0 ))) - (((
                                                discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                                    discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                                ) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
                                            ))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
                                        FROM
                                            srp_erp_mfq_estimatedetail
                                            JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
                                )estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
                if (!empty($clientID)) {
                    $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
                }
                if (!empty($segmentID)) {
                    $this->db->where("srp_erp_mfq_job.mfqSegmentID IN (" . implode(',', $segmentID) . ")");
                }
                if (!empty($this->input->post('date'))) {
                    $this->db->where("srp_erp_mfq_job.documentDate <= '" . $date . "'");
                }
                $this->db->where('confirmedYN != 1');
                $result = $this->db->get()->result_array();
                break;

            case 'Invoiced Jobs' : 
                $this->db->select("workProcessID, ((totalValue/expectedQty) * qty) as amount, documentCode, documentDate, qty")
                        ->from('srp_erp_mfq_job')
                        ->join('(SELECT SUM( deliveredQty ) AS deliveredQty, deliveryNoteID, srp_erp_mfq_deliverynotedetail.jobID 
                                    FROM srp_erp_mfq_deliverynotedetail
                                    JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
                                    WHERE deletedYn != 1 GROUP BY srp_erp_mfq_deliverynotedetail.jobID 
                                ) dnQty', 'dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty', 'LEFT')
                        ->join('srp_erp_mfq_customerinvoicemaster inv', 'inv.deliveryNoteID = dnQty.deliveryNoteID', 'LEFT')
                        ->where('srp_erp_mfq_job.companyID', $companyID)
                        ->where('linkedJobID IS NOT NULL');
                $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
                                            srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
                                            ((((
                                                discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                                    discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                                ) * IFNULL( totMargin, 0 ))) - (((
                                                discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                                    discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                                ) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
                                            ))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
                                        FROM
                                            srp_erp_mfq_estimatedetail
                                        JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
                                )estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
                if (!empty($clientID)) {
                    $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
                }
                if (!empty($segmentID)) {
                    $this->db->where(" srp_erp_mfq_job.mfqSegmentID IN (" . implode(',', $segmentID) . ")");
                }
                if (!empty($this->input->post('date'))) {
                    $this->db->where("srp_erp_mfq_job.documentDate <= '" . $date . "'");
                }
                $this->db->where('invoiceAutoID IS NOT NULL');
                $result = $this->db->get()->result_array();

                break;

            case 'Delivered Jobs' : 
                $this->db->select("workProcessID, ((totalValue/expectedQty) * qty) as amount, documentCode, documentDate, qty")
                        ->from('srp_erp_mfq_job')
                        ->join('(SELECT SUM( deliveredQty ) AS deliveredQty, deliveryNoteID, srp_erp_mfq_deliverynotedetail.jobID 
                                    FROM srp_erp_mfq_deliverynotedetail
                                    JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
                                    WHERE deletedYn != 1 GROUP BY srp_erp_mfq_deliverynotedetail.jobID 
                                ) dnQty', 'dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty')
                        ->join('srp_erp_mfq_customerinvoicemaster inv', 'inv.deliveryNoteID = dnQty.deliveryNoteID', 'LEFT')
                        ->where('srp_erp_mfq_job.companyID', $companyID)
                        ->where('linkedJobID IS NOT NULL');
                $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
                                                srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
                                                ((((
                                                    discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                                        discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                                    ) * IFNULL( totMargin, 0 ))) - (((
                                                    discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                                        discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                                    ) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
                                                ))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
                                            FROM
                                                srp_erp_mfq_estimatedetail
                                            JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
                                    )estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
                if (!empty($clientID)) {
                    $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
                }
                if (!empty($segmentID)) {
                    $this->db->where(" srp_erp_mfq_job.mfqSegmentID IN (" . implode(',', $segmentID) . ")");
                }
                if (!empty($this->input->post('date'))) {
                    $this->db->where("srp_erp_mfq_job.documentDate <= '" . $date . "'");
                }
                $this->db->where('invoiceAutoID IS NULL');
                $result = $this->db->get()->result_array();

                break;

            case 'Overdue Jobs' : 
                $this->db->select("workProcessID, ((totalValue/expectedQty) * qty) as amount, documentCode, srp_erp_mfq_job.documentDate, qty")
                            ->from('srp_erp_mfq_job')
                            ->join('(SELECT SUM( deliveredQty ) AS deliveredQty, deliveryNoteID, srp_erp_mfq_deliverynotedetail.jobID 
                                        FROM srp_erp_mfq_deliverynotedetail
                                        JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
                                        WHERE deletedYn != 1 GROUP BY srp_erp_mfq_deliverynotedetail.jobID 
                                    ) dnQty', 'dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty', 'LEFT')
                            ->join('srp_erp_mfq_estimatemaster estimate', 'estimate.estimateMasterID = srp_erp_mfq_job.estimateMasterID', 'LEFT')
                            ->where('srp_erp_mfq_job.companyID', $companyID)
                            ->where('linkedJobID IS NOT NULL')
                            ->where('srp_erp_mfq_job.confirmedYN', 1);
                $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
                                            srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
                                            ((((
                                                discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                                    discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                                ) * IFNULL( totMargin, 0 ))) - (((
                                                discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                                    discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                                ) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
                                            ))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
                                        FROM
                                            srp_erp_mfq_estimatedetail
                                        JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
                                )estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
                if (!empty($clientID)) {
                    $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
                }
                if (!empty($segmentID)) {
                    $this->db->where("srp_erp_mfq_job.mfqSegmentID IN (" . implode(',', $segmentID) . ")");
                }
                if (!empty($this->input->post('date'))) {
                    $this->db->where("srp_erp_mfq_job.documentDate <= '" . $date . "'");
                }
                $this->db->where('dnQty.deliveryNoteID IS NULL')
                    ->where('expectedDeliveryDate <"' . $currentDate . '"');
                $result = $this->db->get()->result_array();
                
                break;
            case 'Closed Jobs' : 
                $this->db->select("workProcessID, ((totalValue/expectedQty) * qty) as amount, documentCode, srp_erp_mfq_job.documentDate, qty")
                            ->from('srp_erp_mfq_job')
                            ->join('(SELECT SUM( deliveredQty ) AS deliveredQty, deliveryNoteID, srp_erp_mfq_deliverynotedetail.jobID 
                                        FROM srp_erp_mfq_deliverynotedetail
                                        JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
                                        WHERE deletedYn != 1 GROUP BY srp_erp_mfq_deliverynotedetail.jobID 
                                    ) dnQty', 'dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty', 'LEFT')
                            ->join('srp_erp_mfq_estimatemaster estimate', 'estimate.estimateMasterID = srp_erp_mfq_job.estimateMasterID', 'LEFT')
                            ->where('srp_erp_mfq_job.companyID', $companyID)
                            ->where('linkedJobID IS NOT NULL')
                            ->where('srp_erp_mfq_job.confirmedYN', 1);
                $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
                                        srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
                                        ((((
                                            discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                                discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                            ) * IFNULL( totMargin, 0 ))) - (((
                                            discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                                discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                            ) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
                                        ))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
                                    FROM
                                        srp_erp_mfq_estimatedetail
                                    JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
                            )estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
                if (!empty($clientID)) {
                    $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
                }
                if (!empty($segmentID)) {
                    $this->db->where(" srp_erp_mfq_job.mfqSegmentID IN (" . implode(',', $segmentID) . ")");
                }
                if (!empty($this->input->post('date'))) {
                    $this->db->where("srp_erp_mfq_job.documentDate <= '" . $date . "'");
                }
                $this->db->where('dnQty.deliveryNoteID IS NULL')
                    ->where('expectedDeliveryDate >="' . $currentDate . '"');
                $result = $this->db->get()->result_array();
                
                break;

            default :
                break;

        }

        $table = '<div class="text-center reportHeaderColor reportHeader"> AWARDED JOB STATUS </div>
                <div class="table-responsive"><table id="tbl_awarded_job_drilldown" class="mfqTable table table-striped table-condensed">
                        <thead>
                            <tr>                                               
                                
                                <th style="min-width: 3%">#</th>
                                <th style="min-width: 12%">DOCUMENT CODE</th>
                                <th style="min-width: 12%">DOCUMENT DATE</th>
                                <th style="min-width: 3%">AMOUNT (' . $this->common_data['company_data']['company_default_currency'] . ')</th>
                            </tr>
                        </thead>
                    <tbody>';
        $total = 0;
        $a = 1;
        foreach ($result as $row) {
            $table .= '<tr>
                        <td >' . $a . '</td>
                        <td >' . $row['documentCode'] . '</td>
                        <td >' . $row['documentDate'] . '</td>
                        <td class="text-right">' . number_format($row['amount'],  $this->common_data['company_data']['company_default_decimal']) . '</td>
                    </tr>';
            $total += $row['amount'];
            $a++;
        }
        $table .= '<tr>
                        <td>&nbsp;</td>
                        <td colspan="2" class=""sub_total"> <strong>Total</strong> </td>
                        <td class="text-right sub_total">' . number_format($total,  $this->common_data['company_data']['company_default_decimal']) . '</td>
                    </tr>';
        $table .= ' </tbody></table></div>';
        return $table;
    }
    
    /** Added */
    function planned_job_return()
    {
        $clientID = $this->input->post('clientID');
        $dateTo = format_date($this->input->post('dateTo'));
        $dateFrom = format_date($this->input->post('dateFrom'));
        $companyID = current_companyID();

        /** Planned Job Widget */
        $this->db->select("COUNT(workProcessID) as count, srp_erp_mfq_job.mfqSegmentID, segmentCode, srp_erp_mfq_segment.description")
            ->from('srp_erp_mfq_job')
            ->join('srp_erp_mfq_segment', 'srp_erp_mfq_segment.mfqSegmentID = srp_erp_mfq_job.mfqSegmentID')
            ->where('srp_erp_mfq_job.companyID', $companyID)
            ->where('linkedJobID IS NOT NULL');
        if (!empty($clientID)) {
            $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
        }
        if (!empty($this->input->post('dateTo')) && !empty($this->input->post('dateFrom'))) {
            $this->db->where("expectedDeliveryDate BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'");
        }
        $this->db->group_by('srp_erp_mfq_job.mfqSegmentID');
        $plannedDelivery = $this->db->get()->result_array();

        $whereSeg = '';
        $selectedSegments = implode(',', array_column($plannedDelivery, 'mfqSegmentID'));
        if (!empty($selectedSegments)) {
            $whereSeg = ' AND mfqSegmentID NOT IN (' . $selectedSegments . ')';
        }
        $notselectedSegments = $this->db->query("SELECT mfqSegmentID, segmentCode, srp_erp_mfq_segment.description FROM srp_erp_mfq_segment WHERE companyID = $companyID AND masterSegmentID IS NULL $whereSeg")->result_array();

        $data['plannedDelivery'] = [];
        $plannedValueTotal = 0;
        if (!empty($plannedDelivery)) {
            foreach ($plannedDelivery AS $val) {

                $this->db->select("SUM((totalValue/expectedQty) * qty) as Value")
                    ->from('srp_erp_mfq_job')
                    ->where('srp_erp_mfq_job.companyID', $companyID)
                    ->where('srp_erp_mfq_job.mfqSegmentID', $val['mfqSegmentID'])
                    ->where('linkedJobID IS NOT NULL');
                // if(!empty($qry)) {
                // 	$this->db->join("(SELECT SUM(totalValue) AS Value, workProcessID FROM (" . join(' UNION ', $qry) . ")tbl GROUP BY workProcessID)wipCalculate", "wipCalculate.workProcessID = srp_erp_mfq_job.workProcessID", "LEFT");
                // }
                $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
						srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
						((((
							discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
							) * IFNULL( totMargin, 0 ))) - (((
							discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
							) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
						))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
					FROM
						srp_erp_mfq_estimatedetail
						JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
						)estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
                if (!empty($clientID)) {
                    $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
                }
                if (!empty($this->input->post('dateTo')) && !empty($this->input->post('dateFrom'))) {
                    $this->db->where("expectedDeliveryDate BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'");
                }
                $this->db->group_by('srp_erp_mfq_job.mfqSegmentID');
                $SegmentValue = $this->db->get()->row('Value');

                $arr['name'] = $val['description'];
                $arr['count'] = $val['count'];
                $arr['SegmentValue'] = number_format((float)$SegmentValue, $this->common_data['company_data']['company_default_decimal'], '.', ',');
                $arr['value'] = (float)$SegmentValue;
                $arr['segmentID'] = $val['mfqSegmentID'];
                // $arr['y'] = round((($val['count']/$totalJobs) * 100), 2);
                array_push($data['plannedDelivery'], $arr);
                $plannedValueTotal += (float)$SegmentValue;
            }
            foreach ($data['plannedDelivery'] as $key => $valueCalc) {
                $data['plannedDelivery'][$key]['y'] = round((($valueCalc['value'] / $plannedValueTotal) * 100), 2);
            }
        }
        $arr_seg = array();
        if (!empty($notselectedSegments)) {
            foreach ($notselectedSegments as $seg) {
                $arr_seg['name'] = $seg['description'];
                $arr_seg['count'] = 0;
                $arr_seg['SegmentValue'] = number_format((float)0, $this->common_data['company_data']['company_default_decimal'], '.', ',');
                $arr_seg['value'] = (float)0;
                $arr_seg['y'] = (float)0;
                $arr_seg['segmentID'] = $seg['mfqSegmentID'];
                array_push($data['plannedDelivery'], $arr_seg);
            }
        }

        /** Actual Job Widget */
        $this->db->select("COUNT(workProcessID) as count, srp_erp_mfq_job.mfqSegmentID, segmentCode, srp_erp_mfq_segment.description")
            ->from('srp_erp_mfq_job')
            ->join('(SELECT srp_erp_mfq_deliverynotedetail.jobID, MAX( deliveryDate ) AS deliveryDate FROM srp_erp_mfq_deliverynotedetail JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynote.deliverNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID GROUP BY srp_erp_mfq_deliverynotedetail.jobID)deliveryNote', 'deliveryNote.jobID = srp_erp_mfq_job.workProcessID')
            ->join('srp_erp_mfq_segment', 'srp_erp_mfq_segment.mfqSegmentID = srp_erp_mfq_job.mfqSegmentID')
            ->where('srp_erp_mfq_job.companyID', $companyID);
        if (!empty($clientID)) {
            $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
        }
        if (!empty($this->input->post('dateTo')) && !empty($this->input->post('dateFrom'))) {
            $this->db->where("deliveryDate BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'");
        }
        $this->db->group_by('srp_erp_mfq_job.mfqSegmentID');
        $actualDelivery = $this->db->get()->result_array();

        $whereSeg = '';
        $selectedSegments = implode(',', array_column($actualDelivery, 'mfqSegmentID'));
        if (!empty($selectedSegments)) {
            $whereSeg = ' AND mfqSegmentID NOT IN (' . $selectedSegments . ')';
            $notselectedSegments = $this->db->query("SELECT mfqSegmentID, segmentCode, srp_erp_mfq_segment.description FROM srp_erp_mfq_segment WHERE companyID = $companyID AND masterSegmentID IS NULL $whereSeg")->result_array();

            $totalJobs = array_sum(array_column($actualDelivery, 'count'));
            $data['actualDelivery'] = [];
            $actualValueTotal = 0;
            if (!empty($actualDelivery)) {
                foreach ($actualDelivery AS $val) {
                    $this->db->select("SUM((totalValue/expectedQty) * qty) as Value")
                        ->from('srp_erp_mfq_job')
                        ->join('(SELECT srp_erp_mfq_deliverynotedetail.jobID, MAX( deliveryDate ) AS deliveryDate FROM srp_erp_mfq_deliverynotedetail JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynote.deliverNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID GROUP BY srp_erp_mfq_deliverynotedetail.jobID)deliveryNote', 'deliveryNote.jobID = srp_erp_mfq_job.workProcessID')
                        ->where('srp_erp_mfq_job.mfqSegmentID', $val['mfqSegmentID'])
                        ->where('srp_erp_mfq_job.companyID', $companyID);
                    // if(!empty($qry)) {
                    // 	$this->db->join("(SELECT SUM(totalValue) AS Value, workProcessID FROM (" . join(' UNION ', $qry) . ")tbl GROUP BY workProcessID)wipCalculate", "wipCalculate.workProcessID = srp_erp_mfq_job.workProcessID", "LEFT");
                    // }
                    $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
							srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
							((((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
									discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
								) * IFNULL( totMargin, 0 ))) - (((
								discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
									discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
								) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
							))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
						FROM
							srp_erp_mfq_estimatedetail
							JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
							)estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
                    if (!empty($clientID)) {
                        $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
                    }
                    if (!empty($this->input->post('dateTo')) && !empty($this->input->post('dateFrom'))) {
                        $this->db->where("deliveryDate BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'");
                    }
                    $this->db->group_by('srp_erp_mfq_job.mfqSegmentID');
                    $SegmentValue = $this->db->get()->row('Value');


                    $arr['name'] = $val['description'];
                    $arr['count'] = $val['count'];
                    $arr['SegmentValue'] = number_format((float)$SegmentValue, $this->common_data['company_data']['company_default_decimal'], '.', ',');
                    // $arr['y'] = round((($val['count']/$totalJobs) * 100), 2);
                    $arr['value'] = (float)$SegmentValue;
                    $arr['segmentID'] = $val['mfqSegmentID'];
                    array_push($data['actualDelivery'], $arr);
                    $actualValueTotal += (float)$SegmentValue;
                }
                foreach ($data['actualDelivery'] as $key => $ActualCalc) {
                    $data['actualDelivery'][$key]['y'] = round((($ActualCalc['value'] / $actualValueTotal) * 100), 2);
                }
            }

            $arr_seg = array();
            if (!empty($notselectedSegments)) {
                foreach ($notselectedSegments as $seg) {
                    $arr_seg['name'] = $seg['description'];
                    $arr_seg['count'] = 0;
                    $arr_seg['SegmentValue'] = number_format((float)0, $this->common_data['company_data']['company_default_decimal'], '.', ',');
                    $arr_seg['value'] = (float)0;
                    $arr_seg['y'] = (float)0;
                    $arr_seg['segmentID'] = $seg['mfqSegmentID'];
                    array_push($data['actualDelivery'], $arr_seg);
                }
            }

            return $data;
        }
    }

    function planned_job_return_drill_down()
    {
        $type = $this->input->post('type');
        $segmentID = $this->input->post('segment');
        $clientID = $this->input->post('clientID');
        $dateTo = format_date($this->input->post('dateTo'));
        $dateFrom = format_date($this->input->post('dateFrom'));
        $companyID = current_companyID();

        if($type == 1) {
            $title = 'PLANNED JOB RETURN';
            $this->db->select("workProcessID, ((totalValue/expectedQty) * qty) as amount, documentCode, srp_erp_mfq_job.documentDate, qty")
                    ->from('srp_erp_mfq_job')
                    ->where('srp_erp_mfq_job.companyID', $companyID)
                    ->where('srp_erp_mfq_job.mfqSegmentID', $segmentID)
                    ->where('linkedJobID IS NOT NULL');
            $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
                                        srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
                                        ((((
                                            discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                                discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                            ) * IFNULL( totMargin, 0 ))) - (((
                                            discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                                discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                            ) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
                                        ))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
                                    FROM
                                        srp_erp_mfq_estimatedetail
                                    JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
                                )estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
            if (!empty($clientID)) {
                $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
            }
            if (!empty($this->input->post('dateTo')) && !empty($this->input->post('dateFrom'))) {
                $this->db->where("expectedDeliveryDate BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'");
            }
            $result = $this->db->get()->result_array();
    
        } else {
            $title = 'ACTUAL JOB RETURN';
            $this->db->select("workProcessID, ((totalValue/expectedQty) * qty) as amount, documentCode, srp_erp_mfq_job.documentDate, qty")
                ->from('srp_erp_mfq_job')
                ->join('(SELECT srp_erp_mfq_deliverynotedetail.jobID, MAX( deliveryDate ) AS deliveryDate FROM srp_erp_mfq_deliverynotedetail JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynote.deliverNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID GROUP BY srp_erp_mfq_deliverynotedetail.jobID)deliveryNote', 'deliveryNote.jobID = srp_erp_mfq_job.workProcessID')
                ->where('srp_erp_mfq_job.mfqSegmentID', $segmentID)
                ->where('srp_erp_mfq_job.companyID', $companyID);
            $this->db->join("( SELECT srp_erp_mfq_estimatemaster.companyLocalExchangeRate, srp_erp_mfq_estimatemaster.companyLocalCurrencyDecimalPlaces,
                                    srp_erp_mfq_estimatedetail.estimateMasterID, estimateDetailID, expectedQty, mfqItemID,
                                    ((((
                                        discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                            discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                        ) * IFNULL( totMargin, 0 ))) - (((
                                        discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) + (((
                                            discountedPrice * (( 100 + IFNULL( margin, 0 ))/ 100 )) / 100 
                                        ) * IFNULL( totMargin, 0 ))) / 100 * IFNULL(totDiscount, 0) 
                                    ))/ srp_erp_mfq_estimatemaster.companyLocalExchangeRate) totalValue
                                FROM
                                    srp_erp_mfq_estimatedetail
                                    JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID
                            )estimate", "estimate.estimateDetailID = srp_erp_mfq_job.estimateDetailID");
            if (!empty($clientID)) {
                $this->db->where("srp_erp_mfq_job.mfqCustomerAutoID IN (" . implode(',', $clientID) . ")");
            }
            if (!empty($this->input->post('dateTo')) && !empty($this->input->post('dateFrom'))) {
                $this->db->where("deliveryDate BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'");
            }
            $result = $this->db->get()->result_array();
        }
        
        $table = '<div class="text-center reportHeaderColor reportHeader">' . $title . '</div>
                <div class="table-responsive"><table id="tbl_awarded_job_drilldown" class="mfqTable table table-striped table-condensed">
                    <thead>
                        <tr>                                               
                            
                            <th style="min-width: 3%">#</th>
                            <th style="min-width: 12%">DOCUMENT CODE</th>
                            <th style="min-width: 12%">DOCUMENT DATE</th>
                            <th style="min-width: 3%">AMOUNT (' . $this->common_data['company_data']['company_default_currency'] . ')</th>
                        </tr>
                    </thead>
                <tbody>';
        $total = 0;
        $a = 1;
        foreach ($result as $row) {
            $table .= '<tr>
                        <td >' . $a . '</td>
                        <td >' . $row['documentCode'] . '</td>
                        <td >' . $row['documentDate'] . '</td>
                        <td class="text-right">' . number_format($row['amount'],  $this->common_data['company_data']['company_default_decimal']) . '</td>
                    </tr>';
            $total += $row['amount'];
            $a++;
        }
        $table .= '<tr>
                        <td>&nbsp;</td>
                        <td colspan="2" class=""sub_total"> <strong>Total</strong> </td>
                        <td class="text-right sub_total">' . number_format($total,  $this->common_data['company_data']['company_default_decimal']) . '</td>
                    </tr>';
        $table .= ' </tbody></table></div>';
        return $table;
    }

    /** Added */
    function estimate_vs_actual_job()
    {
        $companyID = current_companyID();
        $financialYr = $this->db->query("SELECT beginingDate, endingDate FROM srp_erp_companyfinanceyear WHERE CompanyID = {$companyID} AND isActive = 1 AND isCurrent = 1")->row_array();
        $endingDate = $financialYr['endingDate'];
        $beginingDate = $financialYr['beginingDate'];
        $months = get_month_list_from_date($beginingDate, $endingDate, "Y-m", "1 month", 'M');

        $feild1 = $feild2 = $feild3 = $feild4 = '';
        if (!empty($months)) {
            foreach ($months as $key => $val2) {
                $feild1 .= "ROUND(SUM(if(DATE_FORMAT(invoiceDate,'%Y-%m') = '$key',(revenueTotal/companyLocalExchangeRate), 0) ), companyLocalCurrencyDecimalPlaces)  as `" . $val2 . "`,";
                $feild2 .= "ROUND(SUM(if(DATE_FORMAT(expectedDeliveryDate,'%Y-%m') = '$key',(sellingPrice/srp_erp_mfq_estimatedetail.companyLocalExchangeRate), 0) ), srp_erp_mfq_estimatedetail.companyLocalCurrencyDecimalPlaces) as `" . $val2 . "`,";
                $feild3 .= "ROUND(SUM(if(DATE_FORMAT(expectedDeliveryDate,'%Y-%m') = '$key',(((estimatedCost/srp_erp_mfq_estimatedetail.companyLocalExchangeRate)/expectedQty) * qty), 0) ), srp_erp_mfq_estimatedetail.companyLocalCurrencyDecimalPlaces) as `" . $val2 . "`,";
                $feild4 .= "ROUND(SUM(if(DATE_FORMAT(invoiceDate,'%Y-%m') = '$key',jcCost, 0) ), companyLocalCurrencyDecimalPlaces) as `" . $val2 . "`,";
            }
        }

        $details = $this->db->query("
			SELECT $feild4 'Invoice Cost' AS description, 'Actual' AS recType
			FROM srp_erp_mfq_customerinvoicemaster
				JOIN srp_erp_mfq_customerinvoicedetails ON srp_erp_mfq_customerinvoicedetails.invoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID
				JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.deliveryNoteDetailID = srp_erp_mfq_customerinvoicedetails.deliveryNoteDetID
				JOIN (
					SELECT srp_erp_mfq_job.workProcessID, SUM(IFNULL(machineCost, 0) + IFNULL(overheadCost, 0) + IFNULL(labourCost, 0) + IFNULL(materialCost, 0))/companyLocalExchangeRate AS jcCost 
					FROM srp_erp_mfq_job
					LEFT JOIN (SELECT SUM(totalValue) AS machineCost, workProcessID FROM srp_erp_mfq_jc_machine GROUP BY workProcessID HAVING MAX(jobCardID)) machine ON machine.workProcessID = srp_erp_mfq_job.workProcessID
					LEFT JOIN (SELECT SUM(totalValue) AS overheadCost, workProcessID FROM srp_erp_mfq_jc_overhead GROUP BY workProcessID HAVING MAX( jobCardID )) overhead ON overhead.workProcessID = srp_erp_mfq_job.workProcessID
					LEFT JOIN (SELECT SUM( totalValue ) AS labourCost, workProcessID FROM srp_erp_mfq_jc_labourtask GROUP BY workProcessID HAVING MAX( jobCardID ) ) labourtask ON labourtask.workProcessID = srp_erp_mfq_job.workProcessID
					LEFT JOIN (SELECT SUM( materialCost ) AS materialCost, workProcessID FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID HAVING MAX( jobCardID ) ) material ON material.workProcessID = srp_erp_mfq_job.workProcessID 
					WHERE linkedJobID IS NOT NULL
					GROUP BY srp_erp_mfq_job.workProcessID)job ON job.workProcessID = srp_erp_mfq_deliverynotedetail.jobID
			WHERE srp_erp_mfq_customerinvoicemaster.invoiceDate BETWEEN '$beginingDate' AND '$endingDate' AND srp_erp_mfq_customerinvoicemaster.companyID = {$companyID} 
		UNION
			SELECT $feild1 'Invoice Revenue' AS description, 'Actual' AS recType
				FROM srp_erp_mfq_customerinvoicemaster
				JOIN ( SELECT SUM(transactionAmount) AS revenueTotal, invoiceAutoID FROM srp_erp_mfq_customerinvoicedetails GROUP BY invoiceAutoID ) det ON det.invoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID 
				WHERE srp_erp_mfq_customerinvoicemaster.invoiceDate BETWEEN '$beginingDate' AND '$endingDate' AND companyID = {$companyID} 
		UNION
			SELECT $feild3 'Estimate Cost' AS description, 'Estimate' AS recType
			FROM srp_erp_mfq_job
			JOIN srp_erp_mfq_estimatedetail ON srp_erp_mfq_estimatedetail.estimateDetailID = srp_erp_mfq_job.estimateDetailID
			WHERE srp_erp_mfq_job.expectedDeliveryDate BETWEEN '$beginingDate' AND '$endingDate' AND srp_erp_mfq_job.companyID = {$companyID} AND linkedJobID IS NOT NULL	
		UNION
			SELECT $feild2 'Estimate Revenue' AS description, 'Estimate' AS recType
				FROM srp_erp_mfq_job
				JOIN srp_erp_mfq_estimatedetail ON srp_erp_mfq_estimatedetail.estimateDetailID = srp_erp_mfq_job.estimateDetailID 
			WHERE srp_erp_mfq_job.expectedDeliveryDate BETWEEN '$beginingDate' AND '$endingDate' AND srp_erp_mfq_job.companyID = {$companyID} AND linkedJobID IS NOT NULL 
		")->result_array();

        $data = [];
        if (!empty($details)) {
            foreach ($details as $val) {
                $arr = array();
                unset($arr['data']);
                foreach (array_keys($val) as $det) {
                    switch ($det) {
                        case 'recType' :
                            $arr['stack'] = $val[$det];
                            break;
                        case 'description' :
                            $arr['name'] = $val[$det];
                            break;
                        default :
                            $arr['data'][] = (double)$val[$det];
                    }
                }
                array_push($data, $arr);
            }

            
        }
        return $data;
    }

    function actual_drilldown()
    {
        $companyID = current_companyID();
        $category = $this->input->post('category');
        $type = $this->input->post('type');

        switch ($category) {
            case 0: 
                $date = date('Y') . '-01';
                $beginingDate = date('Y') . '-01-01';
                $endingDate = date('Y') . '-01-31';
                break;
            case 1: 
                $date = date('Y') . '-02';
                $beginingDate = date('Y') . '-02-01';
                $endingDate = date('Y') . '-02-29';
                break;
            case 2: 
                $date = date('Y') . '-03';
                $beginingDate = date('Y') . '-03-01';
                $endingDate = date('Y') . '-03-31';
                break;
            case 3: 
                $date = date('Y') . '-04';
                $beginingDate = date('Y') . '-04-01';
                $endingDate = date('Y') . '-04-30';
                break;
            case 4: 
                $date = date('Y') . '-05';
                $beginingDate = date('Y') . '-05-01';
                $endingDate = date('Y') . '-05-31';
                break;
            case 5: 
                $date = date('Y') . '-06';
                $beginingDate = date('Y') . '-06-01';
                $endingDate = date('Y') . '-06-30';
                break;
            case 6: 
                $date = date('Y') . '-07';
                $beginingDate = date('Y') . '-07-01';
                $endingDate = date('Y') . '-07-31';
                break;
            case 7: 
                $date = date('Y') . '-08';
                $beginingDate = date('Y') . '-08-01';
                $endingDate = date('Y') . '-08-31';
                break;
            case 8: 
                $date = date('Y') . '-09';
                $beginingDate = date('Y') . '-09-01';
                $endingDate = date('Y') . '-09-30';
                break;
            case 9: 
                $date = date('Y') . '-10';
                $beginingDate = date('Y') . '-10-01';
                $endingDate = date('Y') . '-10-31';
                break;
            case 10: 
                $date = date('Y') . '-11';
                $beginingDate = date('Y') . '-11-01';
                $endingDate = date('Y') . '-11-30';
                break;
            case 11: 
                $date = date('Y') . '-12';
                $beginingDate = date('Y') . '-12-01';
                $endingDate = date('Y') . '-12-31';
                break;
        }

        if($type == 'Invoice Cost') {
            $details = $this->db->query("
                    SELECT ROUND((if(DATE_FORMAT(invoiceDate,'%Y-%m') = '$date',jcCost, 0) ), companyLocalCurrencyDecimalPlaces) as amount, 
                            documentCode, documentDate, invoiceCode, invoiceDate
                    FROM srp_erp_mfq_customerinvoicemaster
                        JOIN srp_erp_mfq_customerinvoicedetails ON srp_erp_mfq_customerinvoicedetails.invoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID
                        JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.deliveryNoteDetailID = srp_erp_mfq_customerinvoicedetails.deliveryNoteDetID
                        JOIN (
                            SELECT srp_erp_mfq_job.workProcessID, SUM(IFNULL(machineCost, 0) + IFNULL(overheadCost, 0) + IFNULL(labourCost, 0) + IFNULL(materialCost, 0))/companyLocalExchangeRate AS jcCost,
                            documentCode, documentDate
                            FROM srp_erp_mfq_job
                            LEFT JOIN (SELECT SUM(totalValue) AS machineCost, workProcessID FROM srp_erp_mfq_jc_machine GROUP BY workProcessID HAVING MAX(jobCardID)) machine ON machine.workProcessID = srp_erp_mfq_job.workProcessID
                            LEFT JOIN (SELECT SUM(totalValue) AS overheadCost, workProcessID FROM srp_erp_mfq_jc_overhead GROUP BY workProcessID HAVING MAX( jobCardID )) overhead ON overhead.workProcessID = srp_erp_mfq_job.workProcessID
                            LEFT JOIN (SELECT SUM( totalValue ) AS labourCost, workProcessID FROM srp_erp_mfq_jc_labourtask GROUP BY workProcessID HAVING MAX( jobCardID ) ) labourtask ON labourtask.workProcessID = srp_erp_mfq_job.workProcessID
                            LEFT JOIN (SELECT SUM( materialCost ) AS materialCost, workProcessID FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID HAVING MAX( jobCardID ) ) material ON material.workProcessID = srp_erp_mfq_job.workProcessID 
                            WHERE linkedJobID IS NOT NULL
                            GROUP BY srp_erp_mfq_job.workProcessID)job ON job.workProcessID = srp_erp_mfq_deliverynotedetail.jobID
                    WHERE srp_erp_mfq_customerinvoicemaster.invoiceDate BETWEEN '$beginingDate' AND '$endingDate' AND srp_erp_mfq_customerinvoicemaster.companyID = {$companyID}
            ")->result_array();


            $table = '<div class="text-center reportHeaderColor reportHeader"> INVOICE COST </div>
                    <div class="table-responsive"><table id="tbl_jobs" class="mfqTable table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th style="min-width: 12%">#</th>
                            <th style="min-width: 12%">INVOICE CODE</th>
                            <th style="min-width: 12%">INVOICE DATE</th>
                            <th style="min-width: 3%">AMOUNT (' . $this->common_data['company_data']['company_default_currency'] . ')</th>
                        </tr>
                        </thead>
                        <tbody>';
            $a = 1;
            $total = 0;
            foreach ($details as $row) {
                $table .= '<tr>
                            <td> ' . $a . ' </td>
                            <td >' . $row['invoiceCode'] . '</td>
                            <td >' . $row['invoiceDate'] . '</td>
                            <td class="text-right">' . number_format($row['amount'],  $this->common_data['company_data']['company_default_decimal']) . '</td>';
                $table .= '</tr>';
                $a++;
                $total += $row['amount'];
            }
            $table .= '<tr>
                        <td>&nbsp;</td>
                        <td colspan="2" class=""sub_total"> <strong>Total</strong> </td>
                        <td class="text-right sub_total">' . number_format($total,  $this->common_data['company_data']['company_default_decimal']) . '</td>
                    </tr>';
            $table .= ' </tbody></table></div>';
            return $table;
        } else if ($type == 'Invoice Revenue')
        {
            $details = $this->db->query("
                            SELECT ROUND((if(DATE_FORMAT(invoiceDate,'%Y-%m') = '$date', (revenueTotal/companyLocalExchangeRate), 0) ), companyLocalCurrencyDecimalPlaces)  as amount,
                                invoiceCode, invoiceDate
                            FROM srp_erp_mfq_customerinvoicemaster
                            JOIN ( SELECT SUM(transactionAmount) AS revenueTotal, invoiceAutoID FROM srp_erp_mfq_customerinvoicedetails GROUP BY invoiceAutoID ) det ON det.invoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID 
                            WHERE srp_erp_mfq_customerinvoicemaster.invoiceDate BETWEEN '$beginingDate' AND '$endingDate' AND companyID = {$companyID} 
                ")->result_array();
          

                $table = '<div class="text-center reportHeaderColor reportHeader"> INVOICE REVENUE </div>
                            <div class="table-responsive"><table id="tbl_jobs" class="mfqTable table table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th style="min-width: 12%">#</th>
                                    <th style="min-width: 12%">INVOICE CODE</th>
                                    <th style="min-width: 12%">INVOICE DATE</th>
                                    <th style="min-width: 3%">AMOUNT (' . $this->common_data['company_data']['company_default_currency'] . ')</th>
                                </tr>
                                </thead>
                                <tbody>';

            $a = 1;
            $total = 0;
            foreach ($details as $row) {
                $table .= '<tr>
                            <td> ' . $a . ' </td>
                            <td >' . $row['invoiceCode'] . '</td>
                            <td >' . $row['invoiceDate'] . '</td>
                            <td class="text-right">' . number_format($row['amount'],  $this->common_data['company_data']['company_default_decimal']) . '</td>';
                $table .= '</tr>';
                $a++;
                $total += $row['amount'];
            }
            $table .= '<tr>
                        <td>&nbsp;</td>
                        <td colspan="2" class=""sub_total"> <strong>Total</strong> </td>
                        <td class="text-right sub_total">' . number_format($total,  $this->common_data['company_data']['company_default_decimal']) . '</td>
                    </tr>';
            $table .= ' </tbody></table></div>';
            return $table;
        } else if ($type == 'Estimate Cost')
        {
           $details = $this->db->query("SELECT
                            expectedDeliveryDate, ROUND((if(DATE_FORMAT(expectedDeliveryDate,'%Y-%m') = '$date',(((estimatedCost/srp_erp_mfq_estimatedetail.companyLocalExchangeRate)/expectedQty) * qty), 0) ), srp_erp_mfq_estimatedetail.companyLocalCurrencyDecimalPlaces) as amount,
                            documentCode, documentDate
                            FROM srp_erp_mfq_job
                            JOIN srp_erp_mfq_estimatedetail ON srp_erp_mfq_estimatedetail.estimateDetailID = srp_erp_mfq_job.estimateDetailID
                            WHERE srp_erp_mfq_job.expectedDeliveryDate BETWEEN '$beginingDate' AND '$endingDate' AND srp_erp_mfq_job.companyID = {$companyID} AND linkedJobID IS NOT NULL	
                        ")->result_array();

            $table = '<div class="text-center reportHeaderColor reportHeader"> ESTIMATE COST </div>
                    <div class="table-responsive"><table id="tbl_jobs" class="mfqTable table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th style="min-width: 12%">#</th>
                            <th style="min-width: 12%">JOB CODE</th>
                            <th style="min-width: 12%">JOB DATE</th>
                            <th style="min-width: 12%">EXPECTED DELIVERY DATE</th>
                            <th style="min-width: 3%">AMOUNT (' . $this->common_data['company_data']['company_default_currency'] . ')</th>
                        </tr>
                        </thead>
                        <tbody>';

            $a = 1;
            $total = 0;
            foreach ($details as $row) {
                $table .= '<tr>
                            <td> ' . $a . ' </td>
                            <td >' . $row['documentCode'] . '</td>
                            <td >' . $row['documentDate'] . '</td>
                            <td >' . $row['expectedDeliveryDate'] . '</td>
                            <td class="text-right">' . number_format($row['amount'],  $this->common_data['company_data']['company_default_decimal']) . '</td>';
                $table .= '</tr>';
                $a++;
                $total += $row['amount'];
            }
            $table .= '<tr>
                    <td>&nbsp;</td>
                    <td colspan="3" class=""sub_total"> <strong>Total</strong> </td>
                    <td class="text-right sub_total">' . number_format($total,  $this->common_data['company_data']['company_default_decimal']) . '</td>
                </tr>';
            $table .= ' </tbody></table></div>';
            return $table;

        } else {
            $details = $this->db->query("SELECT expectedDeliveryDate,
                                 ROUND((if(DATE_FORMAT(expectedDeliveryDate,'%Y-%m') = '$date',(sellingPrice/srp_erp_mfq_estimatedetail.companyLocalExchangeRate), 0) ), srp_erp_mfq_estimatedetail.companyLocalCurrencyDecimalPlaces) as amount,
                                documentCode, documentDate
                                FROM srp_erp_mfq_job
                                JOIN srp_erp_mfq_estimatedetail ON srp_erp_mfq_estimatedetail.estimateDetailID = srp_erp_mfq_job.estimateDetailID 
                                WHERE srp_erp_mfq_job.expectedDeliveryDate BETWEEN '$beginingDate' AND '$endingDate' AND srp_erp_mfq_job.companyID = {$companyID} AND linkedJobID IS NOT NULL
                    ")->result_array();


                    $table = '<div class="text-center reportHeaderColor reportHeader"> ESTIMATE REVENUE </div>
                            <div class="table-responsive"><table id="tbl_jobs" class="mfqTable table table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th style="min-width: 12%">#</th>
                                        <th style="min-width: 12%">JOB CODE</th>
                                        <th style="min-width: 12%">JOB DATE</th>
                                        <th style="min-width: 12%">EXPECTED DELIVERY DATE</th>
                                        <th style="min-width: 3%">AMOUNT (' . $this->common_data['company_data']['company_default_currency'] . ')</th>
                                    </tr>
                                </thead>
                                <tbody>';

            $a = 1;
            $total = 0;
            foreach ($details as $row) {
                $table .= '<tr>
                            <td> ' . $a . ' </td>
                            <td >' . $row['documentCode'] . '</td>
                            <td >' . $row['documentDate'] . '</td>
                            <td >' . $row['expectedDeliveryDate'] . '</td>
                            <td class="text-right">' . number_format($row['amount'],  $this->common_data['company_data']['company_default_decimal']) . '</td>';
                $table .= '</tr>';
                $a++;
                $total += $row['amount'];
            }
            $table .= '<tr>
                    <td>&nbsp;</td>
                    <td colspan="3" class=""sub_total"> <strong>Total</strong> </td>
                    <td class="text-right sub_total">' . number_format($total,  $this->common_data['company_data']['company_default_decimal']) . '</td>
                </tr>';
            $table .= ' </tbody></table></div>';
            return $table;
        }
    }

    function job_entry_query()
    {
        $companyID = current_companyID();
        $qry = array();
        $linkMaterial = $this->db->query("SELECT manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry FROM srp_erp_mfq_costingentrysetup WHERE isEntryEnabled = 1 AND categoryID = 1 AND companyID = {$companyID}")->row_array();
        if (!empty($linkMaterial)) {
            if ($linkMaterial['manualEntry'] == 1 && $linkMaterial['linkedDocEntry'] == 1) {
                $qry[] = "SELECT
				SUM( IFNULL( materialCharge, 0 )/ companyLocalExchangeRate ) AS totalValue,
				workProcessID 
			FROM
				( SELECT MAX( jobcardID ) AS jobcardID, unitCost, workProcessID, mfqItemID, materialCharge, companyLocalExchangeRate FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID, mfqItemID ) consumption 
			WHERE
				mfqItemID != 2782 
			GROUP BY
				workProcessID";
            }
            if ($linkMaterial['linkedDocEntry'] == 1 && $linkMaterial['manualEntry'] == 0) {
                $qry[] = "SELECT (SUM( qty * srp_erp_mfq_jc_materialconsumption.unitCost)) AS totalValue, workProcessID
                        FROM 
                            (SELECT SUM( usageAmount ) AS qty, typeMasterAutoID, jobID FROM srp_erp_mfq_jc_usage WHERE linkedDocumentAutoID IS NOT NULL GROUP BY jobID, typeMasterAutoID) tbl
                        -- JOIN ( 
                        -- 	SELECT MAX( jobcardID ) AS jobcardID, unitCost, workProcessID, mfqItemID FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID, mfqItemID 
                        -- ) srp_erp_mfq_jc_materialconsumption ON srp_erp_mfq_jc_materialconsumption.mfqItemID = tbl.typeMasterAutoID AND srp_erp_mfq_jc_materialconsumption.workProcessID = tbl.jobID 
                        JOIN ( SELECT MAX( jobcardID ) AS jobcardID, workProcessID AS wID FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID ) card ON card.wID = tbl.jobID
                        JOIN ( SELECT jobcardID, unitCost, workProcessID, mfqItemID FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID, mfqItemID, jobcardID ) srp_erp_mfq_jc_materialconsumption ON srp_erp_mfq_jc_materialconsumption.mfqItemID = tbl.typeMasterAutoID AND srp_erp_mfq_jc_materialconsumption.workProcessID = tbl.jobID AND srp_erp_mfq_jc_materialconsumption.jobcardID = card.jobcardID 
                        WHERE mfqItemID != 2782
                        GROUP BY jobID";
            }
            if ($linkMaterial['linkedDocEntry'] == 0 && $linkMaterial['manualEntry'] == 1) {
                $qry[] = "SELECT (SUM( (srp_erp_mfq_jc_materialconsumption.usageQty - qty) * srp_erp_mfq_jc_materialconsumption.unitCost)) AS totalValue, workProcessID,  '' AS jobCard
                        FROM 
                            (SELECT SUM( usageAmount ) AS qty, typeMasterAutoID, jobID FROM srp_erp_mfq_jc_usage WHERE linkedDocumentAutoID IS NOT NULL GROUP BY jobID, typeMasterAutoID) tbl
                        -- JOIN ( 
                        -- 	SELECT MAX( jobcardID ) AS jobcardID, unitCost, workProcessID, mfqItemID FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID, mfqItemID 
                        -- ) srp_erp_mfq_jc_materialconsumption ON srp_erp_mfq_jc_materialconsumption.mfqItemID = tbl.typeMasterAutoID AND srp_erp_mfq_jc_materialconsumption.workProcessID = tbl.jobID 
				        JOIN ( SELECT MAX( jobcardID ) AS jobcardID, workProcessID AS wID FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID ) card ON card.wID = tbl.jobID
                        JOIN ( SELECT jobcardID, unitCost, workProcessID, mfqItemID FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID, mfqItemID, jobcardID ) srp_erp_mfq_jc_materialconsumption ON srp_erp_mfq_jc_materialconsumption.mfqItemID = tbl.typeMasterAutoID AND srp_erp_mfq_jc_materialconsumption.workProcessID = tbl.jobID AND srp_erp_mfq_jc_materialconsumption.jobcardID = card.jobcardID 
                        WHERE mfqItemID != 2782
				GROUP BY jobID";
            }
        }

        $linkLabour = $this->db->query("SELECT manualEntry FROM srp_erp_mfq_costingentrysetup WHERE isEntryEnabled = 1 AND categoryID = 2 AND companyID = {$companyID}")->row('manualEntry');
        if (!empty($linkLabour) && $linkLabour == 1) {
            $qry[] = "SELECT SUM(IFNULL(totalValue,0)/companyLocalExchangeRate) as totalValue,workProcessID FROM srp_erp_mfq_jc_labourtask GROUP BY workProcessID";
        }

        $linkOverhead = $this->db->query("SELECT manualEntry FROM srp_erp_mfq_costingentrysetup WHERE isEntryEnabled = 1 AND categoryID = 3 AND companyID = {$companyID}")->row('manualEntry');
        if (!empty($linkOverhead) && $linkOverhead == 1) {
            $qry[] = "SELECT SUM(IFNULL(totalValue,0)/companyLocalExchangeRate) as totalValue,workProcessID FROM srp_erp_mfq_jc_overhead JOIN srp_erp_mfq_overhead ON srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_overhead.overHeadID WHERE typeID = 1 GROUP BY workProcessID";
        }

        $linkMachine = $this->db->query("SELECT manualEntry FROM srp_erp_mfq_costingentrysetup WHERE isEntryEnabled = 1 AND categoryID = 4 AND companyID = {$companyID}")->row('manualEntry');
        if (!empty($linkMachine) && $linkMachine == 1) {
            $qry[] = "SELECT SUM(IFNULL(totalValue,0)/companyLocalExchangeRate) as totalValue,workProcessID FROM srp_erp_mfq_jc_machine GROUP BY workProcessID";
        }

        $linkthirdParty = $this->db->query("SELECT manualEntry FROM srp_erp_mfq_costingentrysetup WHERE isEntryEnabled = 1 AND categoryID = 5 AND companyID = {$companyID}")->row('manualEntry');
        if (!empty($linkthirdParty) && $linkthirdParty == 1) {
            $qry[] = "SELECT SUM(IFNULL(totalValue,0)/companyLocalExchangeRate) as totalValue,workProcessID FROM srp_erp_mfq_jc_overhead JOIN srp_erp_mfq_overhead ON srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_overhead.overHeadID WHERE typeID = 2 GROUP BY workProcessID";
        }

        $qry[] = "SELECT
		SUM( IFNULL( materialCharge, 0 )/ companyLocalExchangeRate ) AS totalValue, workProcessID 
				FROM ( 
					SELECT MAX( jobcardID ) AS jobcardID, unitCost, workProcessID, mfqItemID, materialCharge, companyLocalExchangeRate FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID, mfqItemID 
				)consumption 
				WHERE mfqItemID = 2782 
				GROUP BY workProcessID";

        // $qry[] = "SELECT (SUM( qty * srp_erp_mfq_jc_materialconsumption.unitCost)) AS totalValue, workProcessID, '' AS jobCard
        // 		FROM (SELECT SUM( usageAmount ) AS qty, typeMasterAutoID, jobID FROM srp_erp_mfq_jc_usage WHERE linkedDocumentAutoID IS NOT NULL GROUP BY jobID, typeMasterAutoID) tbl
        // 		JOIN (
        // 			SELECT MAX( jobcardID ) AS jobcardID, unitCost, workProcessID, mfqItemID FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID, mfqItemID
        // 		) srp_erp_mfq_jc_materialconsumption ON srp_erp_mfq_jc_materialconsumption.mfqItemID = tbl.typeMasterAutoID AND srp_erp_mfq_jc_materialconsumption.workProcessID = tbl.jobID
        // 		WHERE mfqItemID = 2782
        // 		GROUP BY jobID";

        return $qry;
    }

    function ongoing_job_wip_total()
    {
        $qry = array();
        $where = "ongoingjobs.companyID = " . current_companyID() . " AND (IF(type = 1,( jobTbl.approvedYN != 1),( `standardjob`.`completionPercenatage` != '100' OR standardjob.completionPercenatage IS NULL )))";

        $sSearch = $this->input->post('sSearch');
        $qry = $this->job_entry_query();
        if (!empty($qry)) {
            $query = " (SELECT SUM(totalValue) AS wipAmount, workProcessID FROM (" . join(' UNION ', $qry) . ")tbl GROUP BY workProcessID)wipCalculate";
        }
        if ($sSearch) {
            $where .= " AND ((ongoingjobs.documentDate Like '%$sSearch%') OR (documentCode Like '%$sSearch%') OR (seg.description Like '%$sSearch%') OR (ongoingjobs.description Like '%$sSearch%') OR (cust.CustomerName LIKE '%$sSearch%') OR (estimateCode LIKE '%$sSearch%'))";
        }

        $this->db->select("ROUND(SUM(IFNULL(wipAmount,0)), currencymaster.DecimalPlaces) AS amount, 
							ROUND(SUM((((((discountedPrice / ed.companyLocalExchangeRate) * ((100 + IFNULL(totMargin, 0))/ 100 )) * ((100 - IFNULL(totDiscount, 0))/ 100)))/ expectedQty) * qty), currencymaster.DecimalPlaces) AS estimateValue, 
							ROUND(SUM((IFNULL(machineCost, 0) + IFNULL(overheadCost, 0) + IFNULL(labourCost, 0) + IFNULL(materialCost, 0))*qty),currencymaster.DecimalPlaces) AS BOMCost, currencymaster.CurrencyCode AS CurrencyCode, currencymaster.DecimalPlaces AS DecimalPlaces", false)
            ->from('get_mfqongoingjobs ongoingjobs')
            ->join('getmfqjobpercentage percentage', 'percentage.jobID = ongoingjobs.workProcessID AND ongoingjobs.type = 1', 'left')
            ->join('srp_erp_mfq_standardjob standardjob', ' standardjob.jobAutoID = ongoingjobs.workProcessID AND ongoingjobs.type = 2', 'left');
        if (!empty($query)) {
            $this->db->join($query, 'wipCalculate.workProcessID = ongoingjobs.workProcessID', 'left');
        }
        $this->db->join('(SELECT workProcessID, bomMasterID, closedYN, approvedYN FROM srp_erp_mfq_job) jobTbl', 'jobTbl.workProcessID = ongoingjobs.workProcessID AND `type` = 1', 'left')
            ->join('srp_erp_currencymaster currencymaster', 'currencymaster.currencyID = ongoingjobs.companylocalcurrencyID', 'Left')
            ->join('(SELECT * FROM srp_erp_mfq_estimatedetail) ed', 'ed.estimateDetailID = ongoingjobs.estimateDetailID AND ongoingjobs.type = 1', 'Left')
            ->join('(SELECT * FROM srp_erp_mfq_estimatemaster) em', 'em.estimateMasterID = ed.estimateMasterID AND ongoingjobs.type = 1', 'Left')
            ->join('(SELECT SUM(totalValue/companyLocalExchangeRate) AS machineCost, bomMasterID FROM srp_erp_mfq_bom_machine GROUP BY bomMasterID) machine', 'machine.bomMasterID = jobTbl.bomMasterID AND ongoingjobs.type = 1', 'left')
            ->join('(SELECT SUM(totalValue/companyLocalExchangeRate) AS overheadCost, bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) overhead', 'overhead.bomMasterID = jobTbl.bomMasterID AND ongoingjobs.type = 1', 'left')
            ->join('(SELECT SUM(totalValue/companyLocalExchangeRate) AS labourCost, bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) labourtask', 'labourtask.bomMasterID = jobTbl.bomMasterID AND ongoingjobs.type = 1', 'left')
            ->join('(SELECT SUM(materialCost/companyLocalExchangeRate) AS materialCost, bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) material', 'material.bomMasterID = jobTbl.bomMasterID AND ongoingjobs.type = 1', 'left')
            ->where($where);
        $totalWIPAmount = $this->db->get()->row_array();
        $d = get_company_currency_decimal();
        $ret = array(
            "BOMCost" => format_number($totalWIPAmount['BOMCost'], $d),
            "estimateValue" => format_number($totalWIPAmount['estimateValue'], $d),
            "amount" => format_number($totalWIPAmount['amount'], $d),
            "CurrencyCode" => $totalWIPAmount['CurrencyCode'],
            "DecimalPlaces" => $totalWIPAmount['DecimalPlaces']
        );
        return $ret;
    }

    function load_quotation_widget()
    {

        $department = $this->input->post("department");
        $date = format_date($this->input->post('date'));
        $companyID = current_companyID();
        $whereSubmittedQuery = '';
        if (!empty($date)) {
            $whereSubmittedQuery .= " AND CAST(srp_erp_mfq_estimatemaster.approvedDate as DATE) <='" . $date . "'";
        }
        if (!empty($department)) {
            $whereSubmittedQuery .= ' AND srp_erp_mfq_estimatemaster.mfqSegmentID IN (' . implode(',', $department) . ')';
        }
        $totalSubmittedQuery = $this->db->query("SELECT
DATE_FORMAT( jbMas.awardedDate, '%d-%m-%Y' ) AS awardedDate,
ci.statusID AS statusID,
quotationStatus,
srp_erp_mfq_estimatemaster.approvedDate 
FROM
	srp_erp_mfq_customerinquiry ci	
	INNER JOIN (
SELECT
	srp_erp_mfq_estimatemaster.* 
FROM
	srp_erp_mfq_estimatemaster
	INNER JOIN ( SELECT MAX( versionLevel ), versionOrginID, MAX( estimateMasterID ) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON maxl.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID 
	) srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.ciMasterID = ci.ciMasterID
	LEFT JOIN (
SELECT
	linkedJobID,
	srp_erp_mfq_job.estimateMasterID 
FROM
	srp_erp_mfq_job
	LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID 
WHERE
	linkedJobID IS NOT NULL 
	AND ( srp_erp_mfq_job.isDeleted = 0 OR srp_erp_mfq_job.isDeleted IS NULL ) 
	) mfqjobtbl ON srp_erp_mfq_estimatemaster.estimateMasterID = mfqjobtbl.estimateMasterID
	LEFT JOIN ( SELECT workProcessID, awardedDate, documentDate, documentCode FROM srp_erp_mfq_job ) jbMas ON jbMas.workProcessID = mfqjobtbl.linkedJobID 
WHERE quotationStatus=1
AND ci.companyID=$companyID
$whereSubmittedQuery
GROUP BY
	ci.ciMasterID ");//quotationStatus=1 is Submitted.
        $totalSubmitted = $totalSubmittedQuery->num_rows();
        $data['totalSubmitted'] = $totalSubmitted;

        $whereAwardedQuery = '';
        if (!empty($date)) {
            $whereAwardedQuery .= " AND DATE_FORMAT(jbMas.awardedDate,'%Y-%m-%d') <='" . $date . "'";
        }
        if (!empty($department)) {
            $whereAwardedQuery .= ' AND srp_erp_mfq_estimatemaster.mfqSegmentID IN (' . implode(',', $department) . ')';
        }
        $totalAwardedQuery = $this->db->query("SELECT
DATE_FORMAT( jbMas.awardedDate, '%d-%m-%Y' ) AS awardedDate,
ci.statusID AS statusID,
quotationStatus,
srp_erp_mfq_estimatemaster.approvedDate 
FROM
	srp_erp_mfq_customerinquiry ci	
	INNER JOIN (
SELECT
	srp_erp_mfq_estimatemaster.* 
FROM
	srp_erp_mfq_estimatemaster
	INNER JOIN ( SELECT MAX( versionLevel ), versionOrginID, MAX( estimateMasterID ) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON maxl.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID 
	) srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.ciMasterID = ci.ciMasterID
	LEFT JOIN (
SELECT
	linkedJobID,
	srp_erp_mfq_job.estimateMasterID 
FROM
	srp_erp_mfq_job
	LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID 
WHERE
	linkedJobID IS NOT NULL 
	AND ( srp_erp_mfq_job.isDeleted = 0 OR srp_erp_mfq_job.isDeleted IS NULL ) 
	) mfqjobtbl ON srp_erp_mfq_estimatemaster.estimateMasterID = mfqjobtbl.estimateMasterID
	LEFT JOIN ( SELECT workProcessID, awardedDate, documentDate, documentCode FROM srp_erp_mfq_job ) jbMas ON jbMas.workProcessID = mfqjobtbl.linkedJobID 
WHERE quotationStatus=1
AND ci.companyID=$companyID
$whereAwardedQuery
GROUP BY
	ci.ciMasterID");
        //var_dump($this->db->last_query());exit;
        $totalAwarded = $totalAwardedQuery->num_rows();
        $data['totalAwarded'] = $totalAwarded;
        return $data;
    }

    function load_awarded_widget(){
        $date = $this->input->post('date');
        $whereAwardedQueryCurrentMonth = '';
        if(!empty($date))
        {
            $whereAwardedQueryCurrentMonth .= " AND DATE_FORMAT(jbMas.awardedDate,'%m-%Y') ='" . $date . "'";
        }
        $AwardedQueryCurrentMonth = $this->db->query("SELECT
DATE_FORMAT( jbMas.awardedDate, '%d-%m-%Y' ) AS awardedDate,
ci.statusID AS statusID,
quotationStatus,
srp_erp_mfq_estimatemaster.approvedDate 
FROM
	srp_erp_mfq_customerinquiry ci
	LEFT JOIN srp_erp_mfq_customermaster cust ON cust.mfqCustomerAutoID = ci.mfqCustomerAutoID
	LEFT JOIN srp_erp_mfq_status ON srp_erp_mfq_status.statusID = ci.statusID
	LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = ci.segmentID
	LEFT JOIN (
SELECT
	srp_erp_mfq_estimatemaster.* 
FROM
	srp_erp_mfq_estimatemaster
	INNER JOIN ( SELECT MAX( versionLevel ), versionOrginID, MAX( estimateMasterID ) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON maxl.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID 
	) srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.ciMasterID = ci.ciMasterID
	LEFT JOIN (
SELECT
	linkedJobID,
	srp_erp_mfq_job.estimateMasterID 
FROM
	srp_erp_mfq_job
	LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID 
WHERE
	linkedJobID IS NOT NULL 
	AND ( srp_erp_mfq_job.isDeleted = 0 OR srp_erp_mfq_job.isDeleted IS NULL ) 
	) mfqjobtbl ON srp_erp_mfq_estimatemaster.estimateMasterID = mfqjobtbl.estimateMasterID
	LEFT JOIN ( SELECT workProcessID, awardedDate, documentDate, documentCode FROM srp_erp_mfq_job ) jbMas ON jbMas.workProcessID = mfqjobtbl.linkedJobID 
WHERE quotationStatus=1
$whereAwardedQueryCurrentMonth
GROUP BY
	ci.ciMasterID");
        //var_dump($this->db->last_query());exit;
        $currentMonth = $AwardedQueryCurrentMonth->num_rows();
        $data['currentMonth']=$currentMonth;


        $previousMonth = date('m-Y', strtotime('01-'.$date.' -1 month'));
        //var_dump($previousMonth);exit;
        $whereAwardedQueryPreviousMonth = '';
        if(!empty($previousMonth))
        {
            $whereAwardedQueryPreviousMonth .= " AND DATE_FORMAT(jbMas.awardedDate,'%m-%Y') ='" . $previousMonth . "'";
        }
        $AwardedQueryPreviousMonth = $this->db->query("SELECT
DATE_FORMAT( jbMas.awardedDate, '%d-%m-%Y' ) AS awardedDate,
ci.statusID AS statusID,
quotationStatus,
srp_erp_mfq_estimatemaster.approvedDate 
FROM
	srp_erp_mfq_customerinquiry ci
	LEFT JOIN srp_erp_mfq_customermaster cust ON cust.mfqCustomerAutoID = ci.mfqCustomerAutoID
	LEFT JOIN srp_erp_mfq_status ON srp_erp_mfq_status.statusID = ci.statusID
	LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = ci.segmentID
	LEFT JOIN (
SELECT
	srp_erp_mfq_estimatemaster.* 
FROM
	srp_erp_mfq_estimatemaster
	INNER JOIN ( SELECT MAX( versionLevel ), versionOrginID, MAX( estimateMasterID ) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON maxl.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID 
	) srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.ciMasterID = ci.ciMasterID
	LEFT JOIN (
SELECT
	linkedJobID,
	srp_erp_mfq_job.estimateMasterID 
FROM
	srp_erp_mfq_job
	LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID 
WHERE
	linkedJobID IS NOT NULL 
	AND ( srp_erp_mfq_job.isDeleted = 0 OR srp_erp_mfq_job.isDeleted IS NULL ) 
	) mfqjobtbl ON srp_erp_mfq_estimatemaster.estimateMasterID = mfqjobtbl.estimateMasterID
	LEFT JOIN ( SELECT workProcessID, awardedDate, documentDate, documentCode FROM srp_erp_mfq_job ) jbMas ON jbMas.workProcessID = mfqjobtbl.linkedJobID 
WHERE quotationStatus=1
$whereAwardedQueryPreviousMonth
GROUP BY
	ci.ciMasterID");
        $previousMonth = $AwardedQueryPreviousMonth->num_rows();
        $data['previousMonth']=$previousMonth;

        return $data;

    }

    function load_delivery_widget()
    {
        $department = $this->input->post("department");
        $date = $this->input->post('date');
        $companyID = current_companyID();
        $actualsWhere = '';
        if (!empty($date)) {
            $actualsWhere .= " AND DATE_FORMAT(srp_erp_mfq_job.expectedDeliveryDate,'%d-%m-%Y') <='" . $date . "'";
        }
        if (!empty($department)) {
            $actualsWhere .= ' AND srp_erp_mfq_job.mfqSegmentID IN (' . implode(',', $department) . ')';
        }

        $actualsWhere .= " AND IF(DATE_FORMAT(dnQty.deliveryDate, '%d-%m-%Y')<=DATE_FORMAT(srp_erp_mfq_job.expectedDeliveryDate, '%d-%m-%Y'),1,0) = 1";

        $actualsQuery = $this->db->query("SELECT
    documentCode,
    workProcessID
FROM
    `srp_erp_mfq_job`
    INNER JOIN (
    SELECT
        SUM( deliveredQty ) AS deliveredQty,
        srp_erp_mfq_deliverynotedetail.jobID,
        deliveryNoteID,
        MAX( deliveryDate ) AS deliveryDate 
    FROM
        srp_erp_mfq_deliverynotedetail
        JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
    WHERE
        deletedYn != 1 
    GROUP BY
        srp_erp_mfq_deliverynotedetail.jobID 
    ) dnQty ON dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty
WHERE
    `srp_erp_mfq_job`.`linkedJobID` IS NOT NULL 
    AND `srp_erp_mfq_job`.`companyID` = $companyID 
	 $actualsWhere
ORDER BY
	`workProcessID`");
        $actuals = $actualsQuery->num_rows();
        $data['actuals'] = $actuals;

        $expectedWhere = '';
        if (!empty($date)) {
            $expectedWhere .= " AND DATE_FORMAT(srp_erp_mfq_job.expectedDeliveryDate,'%d-%m-%Y') <='" . $date . "'";
        }
        if (!empty($department)) {
            $expectedWhere .= ' AND srp_erp_mfq_job.mfqSegmentID IN (' . implode(',', $department) . ')';
        }

        $expectedWhere .= " AND IF(DATE_FORMAT(dnQty.deliveryDate, '%d-%m-%Y')<=DATE_FORMAT(srp_erp_mfq_job.expectedDeliveryDate, '%d-%m-%Y'),1,0) = 0";

        $expectedQuery = $this->db->query("SELECT
    documentCode,
    workProcessID
FROM
    `srp_erp_mfq_job`
    INNER JOIN (
    SELECT
        SUM( deliveredQty ) AS deliveredQty,
        srp_erp_mfq_deliverynotedetail.jobID,
        deliveryNoteID,
        MAX( deliveryDate ) AS deliveryDate 
    FROM
        srp_erp_mfq_deliverynotedetail
        JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
    WHERE
        deletedYn != 1 
    GROUP BY
        srp_erp_mfq_deliverynotedetail.jobID 
    ) dnQty ON dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty
WHERE
    `srp_erp_mfq_job`.`linkedJobID` IS NOT NULL 
    AND `srp_erp_mfq_job`.`companyID` = $companyID 
	 $expectedWhere
ORDER BY
	`workProcessID`");
        $expected = $expectedQuery->num_rows();
        $data['expected'] = $expected;

        return $data;
    }

    function quotation_submitted_drilldown()
    {

        $department = $this->input->post("department");
        $date = format_date($this->input->post('date'));
        $companyID = current_companyID();
        $whereSubmittedQuery = '';
        if (!empty($date)) {
            $whereSubmittedQuery .= " AND CAST(srp_erp_mfq_estimatemaster.approvedDate as DATE) <='" . $date . "'";
        }
        if (!empty($department)) {
            $whereSubmittedQuery .= ' AND ci.segmentID IN (' . implode(',', $department) . ')';
        }
        $totalSubmittedQuery = $this->db->query("SELECT
DATE_FORMAT( jbMas.awardedDate, '%d-%m-%Y' ) AS awardedDate,
ci.statusID AS statusID,
quotationStatus,
srp_erp_mfq_estimatemaster.approvedDate,
srp_erp_mfq_estimatemaster.estimateCode,
DATE_FORMAT(srp_erp_mfq_estimatemaster.createdDateTime, '%d-%m-%Y') AS estimateDate,
mfqsegment.segmentCode
FROM
	srp_erp_mfq_customerinquiry ci
	LEFT JOIN srp_erp_mfq_customermaster cust ON cust.mfqCustomerAutoID = ci.mfqCustomerAutoID
	LEFT JOIN srp_erp_mfq_status ON srp_erp_mfq_status.statusID = ci.statusID
	LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = ci.segmentID
	LEFT JOIN (
SELECT
	srp_erp_mfq_estimatemaster.* 
FROM
	srp_erp_mfq_estimatemaster
	INNER JOIN ( SELECT MAX( versionLevel ), versionOrginID, MAX( estimateMasterID ) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON maxl.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID 
	) srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.ciMasterID = ci.ciMasterID
	LEFT JOIN (
SELECT
	linkedJobID,
	srp_erp_mfq_job.estimateMasterID 
FROM
	srp_erp_mfq_job
	LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID 
WHERE
	linkedJobID IS NOT NULL 
	AND ( srp_erp_mfq_job.isDeleted = 0 OR srp_erp_mfq_job.isDeleted IS NULL ) 
	) mfqjobtbl ON srp_erp_mfq_estimatemaster.estimateMasterID = mfqjobtbl.estimateMasterID
	LEFT JOIN ( SELECT workProcessID, awardedDate, documentDate, documentCode FROM srp_erp_mfq_job ) jbMas ON jbMas.workProcessID = mfqjobtbl.linkedJobID 
WHERE quotationStatus=1
AND ci.companyID=$companyID
$whereSubmittedQuery
GROUP BY
	ci.ciMasterID ");//quotationStatus=1 is Submitted.
//var_dump($this->db->last_query());exit;
        $table = '<div class="table-responsive"><table id="tbl_jobs" class="mfqTable table table-striped table-condensed">
                                            <thead>
                                            <tr>                                               
                                                 
                                                <th style="min-width: 12%">ESTIMATE CODE</th>
                                                <th style="min-width: 12%">ESTIMATE DATE</th>
                                                <th style="min-width: 12%">AWARDED DATE</th>
                                                <th style="min-width: 3%">SEGMENT</th>                                               
                                               
                                            </tr>
                                            </thead>
                                            <tbody>';
        foreach ($totalSubmittedQuery->result() as $row) {
            $table .= '<tr>';
            $table .= '
                                                        <td >' . $row->estimateCode . '</td>
                                                        <td >' . $row->estimateDate . '</td>
                                                        <td >' . $row->awardedDate . '</td>
                                                        <td >' . $row->segmentCode . '</td>';
            $table .= '</tr>';
        }
        $table .= ' </tbody></table></div>';
        return $table;
    }

    function quotation_awarded_drilldown()
    {
        $department = $this->input->post("department");
        $date = format_date($this->input->post('date'));
        $companyID = current_companyID();
        $whereAwardedQuery = '';
        if (!empty($date)) {
            $whereAwardedQuery .= " AND DATE_FORMAT(jbMas.awardedDate,'%Y-%m-%d') <='" . $date . "'";
        }
        if (!empty($department)) {
            $whereAwardedQuery .= ' AND ci.segmentID IN (' . implode(',', $department) . ')';
        }
        $totalAwardedQuery = $this->db->query("SELECT
DATE_FORMAT( jbMas.awardedDate, '%d-%m-%Y' ) AS awardedDate,
ci.statusID AS statusID,
quotationStatus,
srp_erp_mfq_estimatemaster.approvedDate,
srp_erp_mfq_estimatemaster.estimateCode,
DATE_FORMAT(srp_erp_mfq_estimatemaster.createdDateTime, '%d-%m-%Y') AS estimateDate,
mfqsegment.segmentCode
FROM
	srp_erp_mfq_customerinquiry ci
	LEFT JOIN srp_erp_mfq_customermaster cust ON cust.mfqCustomerAutoID = ci.mfqCustomerAutoID
	LEFT JOIN srp_erp_mfq_status ON srp_erp_mfq_status.statusID = ci.statusID
	LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = ci.segmentID
	LEFT JOIN (
SELECT
	srp_erp_mfq_estimatemaster.* 
FROM
	srp_erp_mfq_estimatemaster
	INNER JOIN ( SELECT MAX( versionLevel ), versionOrginID, MAX( estimateMasterID ) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON maxl.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID 
	) srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.ciMasterID = ci.ciMasterID
	LEFT JOIN (
SELECT
	linkedJobID,
	srp_erp_mfq_job.estimateMasterID 
FROM
	srp_erp_mfq_job
	LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID 
WHERE
	linkedJobID IS NOT NULL 
	AND ( srp_erp_mfq_job.isDeleted = 0 OR srp_erp_mfq_job.isDeleted IS NULL ) 
	) mfqjobtbl ON srp_erp_mfq_estimatemaster.estimateMasterID = mfqjobtbl.estimateMasterID
	LEFT JOIN ( SELECT workProcessID, awardedDate, documentDate, documentCode FROM srp_erp_mfq_job ) jbMas ON jbMas.workProcessID = mfqjobtbl.linkedJobID 
WHERE quotationStatus=1
AND ci.companyID=$companyID
$whereAwardedQuery
GROUP BY
	ci.ciMasterID");

        $table = '<div class="table-responsive"><table id="tbl_jobs" class="mfqTable table table-striped table-condensed">
                                            <thead>
                                            <tr>                                               
                                                 
                                                <th style="min-width: 12%">ESTIMATE CODE</th>
                                                <th style="min-width: 12%">ESTIMATE DATE</th>
                                                <th style="min-width: 12%">AWARDED DATE</th>
                                                <th style="min-width: 3%">SEGMENT</th>                                               
                                               
                                            </tr>
                                            </thead>
                                            <tbody>';
        foreach ($totalAwardedQuery->result() as $row) {
            $table .= '<tr>';
            $table .= '
                                                        <td >' . $row->estimateCode . '</td>
                                                        <td >' . $row->estimateDate . '</td>
                                                        <td >' . $row->awardedDate . '</td>
                                                        <td >' . $row->segmentCode . '</td>';
            $table .= '</tr>';
        }
        $table .= ' </tbody></table></div>';
        return $table;
    }

    function current_month_drilldown()
    {
        $date = $this->input->post('date');
        $companyID = current_companyID();
        $whereAwardedQueryCurrentMonth = '';
        if (!empty($date)) {
            $whereAwardedQueryCurrentMonth .= " AND DATE_FORMAT(jbMas.awardedDate,'%m-%Y') ='" . $date . "'";
        }
        $AwardedQueryCurrentMonth = $this->db->query("SELECT
DATE_FORMAT( jbMas.awardedDate, '%d-%m-%Y' ) AS awardedDate,
ci.statusID AS statusID,
quotationStatus,
srp_erp_mfq_estimatemaster.approvedDate,
srp_erp_mfq_estimatemaster.estimateCode,
DATE_FORMAT(srp_erp_mfq_estimatemaster.createdDateTime, '%d-%m-%Y') AS estimateDate,
mfqsegment.segmentCode
FROM
	srp_erp_mfq_customerinquiry ci
	LEFT JOIN srp_erp_mfq_customermaster cust ON cust.mfqCustomerAutoID = ci.mfqCustomerAutoID
	LEFT JOIN srp_erp_mfq_status ON srp_erp_mfq_status.statusID = ci.statusID
	LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = ci.segmentID
	LEFT JOIN (
SELECT
	srp_erp_mfq_estimatemaster.* 
FROM
	srp_erp_mfq_estimatemaster
	INNER JOIN ( SELECT MAX( versionLevel ), versionOrginID, MAX( estimateMasterID ) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON maxl.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID 
	) srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.ciMasterID = ci.ciMasterID
	LEFT JOIN (
SELECT
	linkedJobID,
	srp_erp_mfq_job.estimateMasterID 
FROM
	srp_erp_mfq_job
	LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID 
WHERE
	linkedJobID IS NOT NULL 
	AND ( srp_erp_mfq_job.isDeleted = 0 OR srp_erp_mfq_job.isDeleted IS NULL ) 
	) mfqjobtbl ON srp_erp_mfq_estimatemaster.estimateMasterID = mfqjobtbl.estimateMasterID
	LEFT JOIN ( SELECT workProcessID, awardedDate, documentDate, documentCode FROM srp_erp_mfq_job ) jbMas ON jbMas.workProcessID = mfqjobtbl.linkedJobID 
WHERE quotationStatus=1
AND ci.companyID=$companyID
$whereAwardedQueryCurrentMonth
GROUP BY
	ci.ciMasterID");

        $table = '<div class="table-responsive"><table id="tbl_jobs" class="mfqTable table table-striped table-condensed">
                                            <thead>
                                            <tr>                                               
                                                 
                                                <th style="min-width: 12%">ESTIMATE CODE</th>
                                                <th style="min-width: 12%">AWARDED DATE</th>
                                                <th style="min-width: 3%">SEGMENT</th>                                               
                                               
                                            </tr>
                                            </thead>
                                            <tbody>';
        foreach ($AwardedQueryCurrentMonth->result() as $row) {
            $table .= '<tr>';
            $table .= '
                                                        <td >' . $row->estimateCode . '</td>
                                                        <td >' . $row->awardedDate . '</td>
                                                        <td >' . $row->segmentCode . '</td>';
            $table .= '</tr>';
        }
        $table .= ' </tbody></table></div>';
        return $table;

    }

    function previous_month_drilldown()
    {
        $date = $this->input->post('date');
        $companyID = current_companyID();
        $previousMonth = date('m-Y', strtotime('01-' . $date . ' -1 month'));
        //var_dump($previousMonth);exit;
        $whereAwardedQueryPreviousMonth = '';
        if (!empty($previousMonth)) {
            $whereAwardedQueryPreviousMonth .= " AND DATE_FORMAT(jbMas.awardedDate,'%m-%Y') ='" . $previousMonth . "'";
        }
        $AwardedQueryPreviousMonth = $this->db->query("SELECT
DATE_FORMAT( jbMas.awardedDate, '%d-%m-%Y' ) AS awardedDate,
ci.statusID AS statusID,
quotationStatus,
srp_erp_mfq_estimatemaster.approvedDate,
srp_erp_mfq_estimatemaster.estimateCode,
DATE_FORMAT(srp_erp_mfq_estimatemaster.createdDateTime, '%d-%m-%Y') AS estimateDate,
mfqsegment.segmentCode 
FROM
	srp_erp_mfq_customerinquiry ci
	LEFT JOIN srp_erp_mfq_customermaster cust ON cust.mfqCustomerAutoID = ci.mfqCustomerAutoID
	LEFT JOIN srp_erp_mfq_status ON srp_erp_mfq_status.statusID = ci.statusID
	LEFT JOIN srp_erp_mfq_segment mfqsegment ON mfqsegment.mfqSegmentID = ci.segmentID
	LEFT JOIN (
SELECT
	srp_erp_mfq_estimatemaster.* 
FROM
	srp_erp_mfq_estimatemaster
	INNER JOIN ( SELECT MAX( versionLevel ), versionOrginID, MAX( estimateMasterID ) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON maxl.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID 
	) srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.ciMasterID = ci.ciMasterID
	LEFT JOIN (
SELECT
	linkedJobID,
	srp_erp_mfq_job.estimateMasterID 
FROM
	srp_erp_mfq_job
	LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID 
WHERE
	linkedJobID IS NOT NULL 
	AND ( srp_erp_mfq_job.isDeleted = 0 OR srp_erp_mfq_job.isDeleted IS NULL ) 
	) mfqjobtbl ON srp_erp_mfq_estimatemaster.estimateMasterID = mfqjobtbl.estimateMasterID
	LEFT JOIN ( SELECT workProcessID, awardedDate, documentDate, documentCode FROM srp_erp_mfq_job ) jbMas ON jbMas.workProcessID = mfqjobtbl.linkedJobID 
WHERE quotationStatus=1
AND ci.companyID=$companyID
$whereAwardedQueryPreviousMonth
GROUP BY
	ci.ciMasterID");

        $table = '<div class="table-responsive"><table id="tbl_jobs" class="mfqTable table table-striped table-condensed">
                                            <thead>
                                            <tr>                                               
                                                 
                                                <th style="min-width: 12%">ESTIMATE CODE</th>
                                                <th style="min-width: 12%">AWARDED DATE</th>
                                                <th style="min-width: 3%">SEGMENT</th>                                               
                                               
                                            </tr>
                                            </thead>
                                            <tbody>';
        foreach ($AwardedQueryPreviousMonth->result() as $row) {
            $table .= '<tr>';
            $table .= '
                                                        <td >' . $row->estimateCode . '</td>
                                                        <td >' . $row->awardedDate . '</td>
                                                        <td >' . $row->segmentCode . '</td>';
            $table .= '</tr>';
        }
        $table .= ' </tbody></table></div>';
        return $table;
    }

    function actuals_drilldown()
    {
        $department = $this->input->post("department");
        $date = $this->input->post('date');
        $companyID = current_companyID();

        $actualsWhere = '';
        if (!empty($date)) {
            $actualsWhere .= " AND DATE_FORMAT(srp_erp_mfq_job.expectedDeliveryDate,'%d-%m-%Y') <='" . $date . "'";
        }
        if (!empty($department)) {
            $actualsWhere .= ' AND srp_erp_mfq_job.mfqSegmentID IN (' . implode(',', $department) . ')';
        }

        $actualsWhere .= " AND IF(DATE_FORMAT(dnQty.deliveryDate, '%d-%m-%Y')<=DATE_FORMAT(srp_erp_mfq_job.expectedDeliveryDate, '%d-%m-%Y'),1,0) = 1";

        $actualsQuery = $this->db->query("SELECT
    documentCode,
    workProcessID,
    dnQty.deliveryDate,
    srp_erp_mfq_job.expectedDeliveryDate
FROM
    `srp_erp_mfq_job`
    INNER JOIN (
    SELECT
        SUM( deliveredQty ) AS deliveredQty,
        srp_erp_mfq_deliverynotedetail.jobID,
        deliveryNoteID,
        MAX( deliveryDate ) AS deliveryDate 
    FROM
        srp_erp_mfq_deliverynotedetail
        JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
    WHERE
        deletedYn != 1 
    GROUP BY
        srp_erp_mfq_deliverynotedetail.jobID 
    ) dnQty ON dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty
WHERE
    `srp_erp_mfq_job`.`linkedJobID` IS NOT NULL 
    AND `srp_erp_mfq_job`.`companyID` = $companyID 
	 $actualsWhere
ORDER BY
	`workProcessID`");

        $table = '<div class="table-responsive"><table id="tbl_jobs" class="mfqTable table table-striped table-condensed">
                                            <thead>
                                            <tr>                                               
                                                 
                                                <th style="min-width: 12%">DOCUMENT CODE</th>
                                                <th style="min-width: 12%">DELIVERY DATE</th>
                                                <th style="min-width: 3%">EXPECTED DELIVERY DATE</th>                                               
                                               
                                            </tr>
                                            </thead>
                                            <tbody>';
        foreach ($actualsQuery->result() as $row) {
            $table .= '<tr>';
            $table .= '
                                                        <td >' . $row->documentCode . '</td>
                                                        <td >' . $row->deliveryDate . '</td>
                                                        <td >' . $row->expectedDeliveryDate . '</td>';
            $table .= '</tr>';
        }
        $table .= ' </tbody></table></div>';
        return $table;
    }

    function expected_drilldown()
    {
        $department = $this->input->post("department");
        $date = $this->input->post('date');
        $companyID = current_companyID();
        $expectedWhere = '';
        if (!empty($date)) {
            $expectedWhere .= " AND DATE_FORMAT(srp_erp_mfq_job.expectedDeliveryDate,'%d-%m-%Y') <='" . $date . "'";
        }
        if (!empty($department)) {
            $expectedWhere .= ' AND srp_erp_mfq_job.mfqSegmentID IN (' . implode(',', $department) . ')';
        }

        $expectedWhere .= " AND IF(DATE_FORMAT(dnQty.deliveryDate, '%d-%m-%Y')<=DATE_FORMAT(srp_erp_mfq_job.expectedDeliveryDate, '%d-%m-%Y'),1,0) = 0";

        $expectedQuery = $this->db->query("SELECT
    documentCode,
    workProcessID,
    dnQty.deliveryDate,
    IFNULL(srp_erp_mfq_job.expectedDeliveryDate, ' - ') as expectedDeliveryDate
FROM
    `srp_erp_mfq_job`
    INNER JOIN (
    SELECT
        SUM( deliveredQty ) AS deliveredQty,
        srp_erp_mfq_deliverynotedetail.jobID,
        deliveryNoteID,
        MAX( deliveryDate ) AS deliveryDate 
    FROM
        srp_erp_mfq_deliverynotedetail
        JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID 
    WHERE
        deletedYn != 1 
    GROUP BY
        srp_erp_mfq_deliverynotedetail.jobID 
    ) dnQty ON dnQty.jobID = srp_erp_mfq_job.workProcessID AND dnQty.deliveredQty = srp_erp_mfq_job.qty
WHERE
    `srp_erp_mfq_job`.`linkedJobID` IS NOT NULL 
    AND `srp_erp_mfq_job`.`companyID` = $companyID 
	 $expectedWhere
ORDER BY
	`workProcessID`");
        $table = '<div class="table-responsive"><table id="tbl_jobs" class="mfqTable table table-striped table-condensed">
                                            <thead>
                                            <tr>                                               
                                                 
                                                <th style="min-width: 12%">DOCUMENT CODE</th>
                                                <th style="min-width: 12%">DELIVERY DATE</th>
                                                <th style="min-width: 3%">EXPECTED DELIVERY DATE</th>                                               
                                               
                                            </tr>
                                            </thead>
                                            <tbody>';
        foreach ($expectedQuery->result() as $row) {
            $table .= '<tr>';
            $table .= '
                                                        <td >' . $row->documentCode . '</td>
                                                        <td >' . $row->deliveryDate . '</td>
                                                        <td >' . $row->expectedDeliveryDate . '</td>';
            $table .= '</tr>';
        }
        $table .= ' </tbody></table></div>';
        return $table;
    }


}