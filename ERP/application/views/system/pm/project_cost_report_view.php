<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('accounts_receivable', $primaryLanguage);

/*if ($details) { */ ?>
<div class="row" style="margin-top: 5px">
    <div class="col-md-12">

        <?php echo export_buttons('projectcostreport', 'Project Cost Report', True, false); ?>

    </div>
</div>
<div class="row" style="margin-top: 5px">
    <div class="col-md-12 " id="projectcostreport">
        <div class="reportHeaderColor" style="text-align: center">
            <strong><?php echo current_companyName(); ?></strong></div>
        <div class="reportHeader reportHeaderColor" style="text-align: center">
            <strong>Project Cost Report</strong>
        </div>
        <div style="">
            <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                <thead class="report-header">

                <tr>
                    <th rowspan="2" style="width: 28%;">Project</th>
                    <th colspan="4">Revenue</th>
                    <th colspan="4">Cost</th>
                </tr>
                <tr>
                    <th>Estimated Revenue</th>
                    <th>Actual Revenue</th>
                    <th>Variance Amount</th>
                    <th>Variance %</th>
                    <th>Estimated Cost</th>
                    <th>Actual Cost</th>
                    <th>Variance Amount</th>
                    <th>Variance %</th>
                </tr>

                </thead>
        <tbody>

                <?php

               $projectcategory = array();
                $date_format = date_format_policy();
                foreach ($projectcost as $val) {
                    $projectcategory[$val["projectName"].'|'.$val["amount"].'|'.$val['estimatedrevenue'].'|'.$val['estimatedcost'].'|'.$val['actualcost']][$val["projectID"]][] = $val;
                }
                if (!empty($projectcategory)) {

                    foreach ($projectcategory as $key => $projectcode)
                    {
                        $projectdetail = explode('|',$key);
                        $variancepercentage = 0.00;
                        $variancepercentage_cost = 0.00;
                        $varianceamount_cat = 0.00;
                        $varianceamount_cat_percentage = 0.00;
                        $costeamount_cat_percentage = 0.00;
                        $varianceamount_cat_percentage_sub = 0.00;
                        $varianceamount_cat_percentage_sub_cost = 0.00;

                        if($projectdetail[2]>0)
                        {
                            $variancepercentage =  number_format((((($projectdetail[1]*-1)-$projectdetail[2])/$projectdetail[2])*100), 2);
                        }
                        if($projectdetail[3]>0)
                        {
                            $variancepercentage_cost = number_format(((($projectdetail[3])-($projectdetail[4]))/$projectdetail[3])*100);
                        }

                        echo "<tr>
                                <td><div><strong><u>" . $projectdetail[0] . "</u></strong></div></td>
                                <td style='text-align: right'><div><strong>" . number_format(($projectdetail[2]), 2) . "</strong></div> </td>
                                <td style='text-align: right'><div><strong>" . number_format(($projectdetail[1]*-1), 2) . "</strong></div></td>
                                 <td style='text-align: right'><div><strong>" . number_format((($projectdetail[1]*-1)-$projectdetail[2]), 2) . "</strong></div></td>
                                 <td style='text-align: right'><div><strong>" . $variancepercentage. "</strong></div></td>
                                 <td style='text-align: right'><div><strong>".number_format($projectdetail[3],2)."</strong></div></td>
                                 <td style='text-align: right'><div><strong>".number_format($projectdetail[4],2)."</strong></div></td>
                                 <td style='text-align: right'><div><strong>".number_format((($projectdetail[3])-($projectdetail[4])),2)."</strong></div></td>
                                 <td style='text-align: right'><div><strong>".number_format($variancepercentage_cost,2)."</strong></div></td>
                         </tr>";
                        foreach ($projectcode as $key2 => $values) {
                            $subtotal = array();
                            foreach ($values as $key3 => $val) {
                                $catergory = $this->db->query("SELECT srp_erp_boq_category.categoryID, categoryCode, categoryDescription, IFNULL( pmcat.amount, 0 ) AS catrevenue, IFNULL( pmestrev.amt, 0 ) AS estimatedrevenue,	IFNULL( pmcatexpence.amount, 0 ) AS expence,
                                            IFNULL( pmestrev.costamt, 0 ) AS estimatedcost 
                                           FROM `srp_erp_boq_category` LEFT JOIN ( SELECT project_categoryID, sum( transactionAmount / projectExchangeRate ) AS amount FROM `srp_erp_generalledger` WHERE ( GLType = 'PLI' ) GROUP BY project_categoryID ) pmcat ON pmcat.project_categoryID = srp_erp_boq_category.categoryID
			                               LEFT JOIN ( SELECT project_categoryID, sum( transactionAmount / projectExchangeRate ) AS amount FROM `srp_erp_generalledger` WHERE ( GLType = 'PLE' ) GROUP BY project_categoryID ) pmcatexpence ON pmcatexpence.project_categoryID = srp_erp_boq_category.categoryID
			                               LEFT JOIN (SELECT IFNULL( SUM( totalTransCurrency ), 0 ) AS amt,IFNULL( SUM( totalCostAmountTranCurrency ), 0 ) AS costamt,categoryID FROM `srp_erp_boq_details`
		                                   INNER JOIN srp_erp_boq_header ON srp_erp_boq_header.headerID = `srp_erp_boq_details`.`headerID` GROUP BY categoryID) pmestrev ON pmestrev.categoryID = srp_erp_boq_category.categoryID 
		                                   WHERE projectID = {$val['projectID']}")->result_array();

                                foreach ($catergory as $cat) {

                                    $subcatergory = $this->db->query("SELECT description,categoryID,subCategoryID FROM `srp_erp_boq_subcategory` where categoryID = {$cat['categoryID']}")->result_array();
                                    if($cat['estimatedrevenue']>0)
                                    {
                                        $varianceamount_cat_percentage = number_format(((($cat['catrevenue']*-1)-$cat['estimatedrevenue'])/$cat['estimatedrevenue'])*100);
                                    }
                                    if($cat['estimatedcost'] > 0)
                                    {
                                        $costeamount_cat_percentage =  number_format(((($cat['estimatedcost']-$cat['expence'])/$cat['estimatedcost'])*100));
                                    }



                                    echo "<tr class='hoverTr'>";
                                    echo "<td style='color: #808080;'>" . $cat['categoryCode'] . ' - ' . $cat['categoryDescription'] . "</td>";
                                    echo "<td style='color: #808080;text-align: right'>".number_format($cat['estimatedrevenue'],2)."</td>";
                                    echo "<td style='color: #808080;text-align: right'>".number_format(($cat['catrevenue']*-1),2)."</td>";
                                    echo "<td style='color: #808080;text-align: right'>".number_format((($cat['catrevenue']*-1)-$cat['estimatedrevenue']),2)."</td>";
                                    echo "<td style='color: #808080;text-align: right'>".number_format($varianceamount_cat_percentage,2)."</td>";
                                    echo "<td style='color: #808080;text-align: right'>".number_format($cat['estimatedcost'],2)."</td>";
                                    echo "<td style='color: #808080;text-align: right'>".number_format($cat['expence'],2)."</td>";
                                    echo "<td style='color: #808080;text-align: right'>".number_format(($cat['estimatedcost']-$cat['expence']),2)."</td>";
                                    echo "<td style='color: #808080;text-align: right'>".number_format(($costeamount_cat_percentage),2)."</td>";
                                    foreach ($subcatergory as $subcat) {
                                        $subcatcost = $this->db->query("SELECT srp_erp_boq_subcategory.subCategoryID, description, IFNULL( pmcat.amount, 0 ) AS catrevenue, IFNULL( pmestrev.amt, 0 ) AS estimatedrevenue
                                                                        , IFNULL( pmcatexpence.amount, 0 ) AS expence, IFNULL( pmestrev.costamt, 0 ) AS estimatedcost  FROM `srp_erp_boq_subcategory`
	                                                                    LEFT JOIN ( SELECT project_subCategoryID, sum( transactionAmount / projectExchangeRate ) AS amount FROM `srp_erp_generalledger` WHERE ( GLType = 'PLI' ) GROUP BY project_subCategoryID ) pmcat ON pmcat.project_subCategoryID = srp_erp_boq_subcategory.subCategoryID
	                                                                    LEFT JOIN ( SELECT project_subCategoryID, sum( transactionAmount / projectExchangeRate ) AS amount FROM `srp_erp_generalledger` WHERE ( GLType = 'PLE' ) GROUP BY project_categoryID ) pmcatexpence ON pmcatexpence.project_subCategoryID = srp_erp_boq_subcategory.subCategoryID
	                                                                    LEFT JOIN ( SELECT IFNULL( SUM( totalTransCurrency ), 0 ) AS amt, IFNULL( SUM( totalCostAmountTranCurrency ), 0 ) AS costamt, subCategoryID 
	                                                                    FROM `srp_erp_boq_details` INNER JOIN srp_erp_boq_header ON srp_erp_boq_header.headerID = `srp_erp_boq_details`.`headerID` GROUP BY subCategoryID ) pmestrev ON pmestrev.subCategoryID = srp_erp_boq_subcategory.subCategoryID WHERE srp_erp_boq_subcategory.subCategoryID = {$subcat['subCategoryID']}")->row_array();
                                        if($subcatcost['estimatedrevenue']>0)
                                        {
                                            $varianceamount_cat_percentage_sub = number_format(((($subcatcost['catrevenue']*-1)-$subcatcost['estimatedrevenue'])/$cat['estimatedrevenue'])*100);
                                        }
                                        if($subcatcost['estimatedcost']>0)
                                        {
                                            $varianceamount_cat_percentage_sub_cost =  number_format(((($subcatcost['estimatedcost']-$subcatcost['expence'])/$subcatcost['estimatedcost'])*100));
                                        }

                                        echo "<tr class='hoverTr'>";
                                        echo "<td style='color: black'>&nbsp;&nbsp;&nbsp;" . '<i class="fa fa-arrow-right"></i > ' . $subcat['description'] . "</td>";
                                        echo "<td style='color: black;text-align: right'>".number_format($subcatcost['estimatedrevenue'],2)."</td>";
                                        echo "<td style='color: black;text-align: right'>".number_format(($subcatcost['catrevenue']*-1),2)."</td>";
                                        echo "<td style='color: black;text-align: right'>".number_format((($subcatcost['catrevenue']*-1)-$subcatcost['estimatedrevenue']),2)."</td>";
                                        echo "<td style='color: black;text-align: right'>".number_format($varianceamount_cat_percentage_sub,2)."</td>";
                                        echo "<td style='color: black;text-align: right'>".number_format($subcatcost['estimatedcost'],2)."</td>";
                                        echo "<td style='color: black;text-align: right'>".number_format($subcatcost['expence'],2)."</td>";
                                        echo "<td style='color: black;text-align: right'>".number_format(($subcatcost['estimatedcost']-$subcatcost['expence']),2)."</td>";
                                        echo "<td style='color: black;text-align: right'>".number_format($varianceamount_cat_percentage_sub_cost,2)."</td>";

                                    }
                                }
                                echo "</tr>";

                            }


                        }
                    }
                }

                ?>


                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>
</div>

<script>
    $('#tbl_rpt_salesorder').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>