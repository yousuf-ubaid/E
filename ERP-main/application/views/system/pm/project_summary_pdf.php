<?php
    $CI=get_instance();
 $companyID= $this->common_data['company_data']['company_id'] ;
$company=$CI->db->query("SELECT CONCAT(IF(company_address1 != '', CONCAT(company_address1,',', '<br>'), ''), IF(company_address2 != '', CONCAT(company_address2, ',','<br>'), ''), IF(company_city != '', CONCAT(company_city, ',','<br>'), ''), IF(company_province != '', CONCAT(company_province, '<br>'), ''), IF(company_country != '', CONCAT(company_country, '<br>'), ''), IF(company_postalcode != '', CONCAT(company_postalcode, '<br>'), '')) as address FROM `srp_erp_company` WHERE company_id=$companyID")->row_array();
?>
<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 80px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                            <h5><strong><?php echo $this->common_data['company_data']['company_name'].' ('.current_companyCode().').'; ?></strong></h5>
                            <div style="text-align: center"><strong><?php echo $company['address'] ?></strong></div>
                        </td>
                    </tr>




                </table>
            </td>

            <td style="width:60%;">
                <table>
                    <tr>
                        <td colspan="3">
                           <!-- <h4><strong><?php /*echo $this->common_data['company_data']['company_name'].' ('.current_companyCode().').'; */?></strong></h4>
                            <div style="text-align: center"><strong><?php /*echo $company['address'] */?></strong></div>-->
                            <h5><?php echo $master['description']?></h5>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Project Code</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['projectCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Subject</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['comment']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Customer</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['customerName']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Currency</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo fetch_currency_code($master['customerCurrencyID']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Start Date</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $master['projectDateFrom']; ?>  &nbsp;&nbsp;&nbsp; End Date &nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $master['projectDateTo']; ?></td>
                    </tr>

                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

</br>
<?php

  $sumtotalTransCurrency          = 0;
  $sumtotalCostTranCurrency       = 0;
  $sumtotalLabourTranCurrency     = 0;
  $sumtotalCostAmountTranCurrency = 0;
?>
<div class="table-responsive">
<table id="summarytable" class="borderSpace report-table-condensed">
    <thead class="report-header">
    <tr>
        <th>S.No</th>
        <th>Items</th>
        <th>Unit</th>
        <th>Qty</th>
        <th> Rate</th>
        <th> Amount</th>
    </tr>
    <tr>

    </tr>


    </thead>
    <tbody class="searchable">
    <?php



      if ($details) {
        $i = 0;
        foreach ($details as $value) {
          $i++;
          ?>

            <tr>
                <td><strong><?php echo $i ?></strong></td>
                <td style="font-weight: bold"><strong style="font-weight: bold"><?php echo $value['categoryName'] ?></strong></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

          <?php
          $CI=get_instance();
          $CI->db->select('srp_erp_boq_details.subCategoryID,headerID,srp_erp_boq_details.subCategoryName,sortOrder');
          $CI->db->from('srp_erp_boq_details');
          $CI->db->join('srp_erp_boq_subcategory',
            'srp_erp_boq_subcategory.subCategoryID = srp_erp_boq_details.subCategoryID', 'categoryID');
          $CI->db->where('headerID', $value['headerID']);
          $CI->db->where('srp_erp_boq_details.categoryID', $value['categoryID']);
          $CI->db->group_by("subCategoryID");
          $CI->db->order_by("sortOrder", "ASC");
          $subcategory = $CI->db->get()->result_array();

          if ($subcategory) {
            $x         = 0;
            $amount    = 0;
            $cost      = 0;
            $lablour   = 0;
            $totalcost = 0;
            foreach ($subcategory as $sub) {
              $x++;
              ?>
                <tr>
                    <td><strong><?php echo $i . '.' . $x ?></strong></td>
                    <td><strong style="font-weight: bold"><?php echo $sub['subCategoryName'] ?></strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

              <?php
              /**/

              $CI->db->select('detailID,categoryName,UnitID as UnitShortCode,unitRateTransactionCurrency,categoryID,totalTransCurrency,subCategoryID,subCategoryName,markUp,itemDescription,srp_erp_boq_details.unitID,Qty,unitCostTranCurrency,totalCostTranCurrency,totalLabourTranCurrency,totalCostAmountTranCurrency,srp_erp_boq_header.customerCurrencyID as customerCurrencyID');
              $CI->db->from('srp_erp_boq_details');
              $CI->db->join('srp_erp_boq_header', 'srp_erp_boq_header.headerID = srp_erp_boq_details.headerID',
                'inner');

              $CI->db->where('srp_erp_boq_header.headerID', $value['headerID']);
              $CI->db->where('srp_erp_boq_details.categoryID', $value['categoryID']);
              $CI->db->where('srp_erp_boq_details.subCategoryID', $sub['subCategoryID']);

              $subdetails = $CI->db->get()->result_array();

              if ($subdetails) {

                $y = 0;
                foreach ($subdetails as $val) {
                  $y++;
                  ?>
                    <tr>


                        <td width="10px"><?php echo $i . '.' . $x . '.' . $y ?></td>
                        <td><?php echo $val['itemDescription'] ?></td>

                        <td width="40px"><?php echo $val['UnitShortCode'] ?></td>
                        <td width="40px" style="text-align: right"><?php echo $val['Qty'] ?></td>
                      <?php
                        $amount    += $val['totalTransCurrency'];
                        $cost      += $val['totalCostTranCurrency'];
                        $lablour   += $val['totalLabourTranCurrency'];
                        $totalcost += $val['totalCostAmountTranCurrency'];

                        $sumtotalTransCurrency          += $val['totalTransCurrency'];
                        $sumtotalCostTranCurrency       += $val['totalCostTranCurrency'];
                        $sumtotalLabourTranCurrency     += $val['totalLabourTranCurrency'];
                        $sumtotalCostAmountTranCurrency += $val['totalCostAmountTranCurrency'];

                        $unitRateTransactionCurrency = number_format((float) $val['unitRateTransactionCurrency'], 2,
                          '.',
                          ',');
                        $totalTransCurrency          = number_format((float) $val['totalTransCurrency'], 2, '.', ',');
                        $unitCostTranCurrency        = number_format((float) $val['unitCostTranCurrency'], 2, '.', ',');
                        $totalCostTranCurrency       = number_format((float) $val['totalCostTranCurrency'], 2, '.',
                          ',');
                        $totalLabourTranCurrency     = number_format((float) $val['totalLabourTranCurrency'], 2, '.',
                          ',');
                        $totalCostAmountTranCurrency = number_format((float) $val['totalCostAmountTranCurrency'], 2,
                          '.',
                          ',');
                      ?>

                        <td width="140px" style="text-align: right"><?php echo $unitRateTransactionCurrency ?></td>

                        <td width="140px" style="text-align: right"><?php echo $totalTransCurrency ?></td>


                    </tr>
                  <?php
                }
              }


            }
            ?>
            <tr class="">
            <td></td>
            <td  colspan="4" class="reportsubtotal" ><b style=""> Total - <?php echo $value['categoryName'] ?></b></td>

            <td class="reportsubtotal text-right" style="text-align: right"><strong><?php echo  number_format((float) $amount, 2, '.',
                ',') ?></strong></td>


            </tr>
<?php
          }


        }
      }
?>
      <tr>
      <td class="" style="text-align:right " colspan="5"><strong style="font-weight: bold">Total</strong></td>
      <td class="reporttotalblack" style="text-align: right"><strong><?php echo number_format((float) $sumtotalTransCurrency, 2, '.', ',');
      ?></strong></td>


      </tr>
      </tbody></table>
</div>








