<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$avgPaymentPerProduct=0;
$company_currecny_decimal = get_company_currency_decimal();
$category = array();
$category2 = array();
?>
<style>
 
    .itemCommission {
        display: inline;
        height: 24px;
        padding: 0px;
        padding-right: 2px;
        padding-left: 2px;
        font-size: 11px;
        width: 70px;
    }
  
    .tdIn {
        width: 140px;
        padding: 2px;

    }

    tr:hover, tr.selected {
        background-color: #E3E1E7;
        opacity: 1;
        z-index: -1;
    }

    .table-striped tbody tr.highlight td {
        background-color: #E3E1E7;
    }

    body table tr.selectedRow {
        background-color: #fff2e1;
    }

    tfoot {
        font-size: 11px;
    }

    .itemCommission {
        text-align: right;
    }

    a.hoverbtn {
        margin-left: 5px;
    }

</style>


<div class="col-md-12" >
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:60%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px" src="<?php
                                echo mPDFImage.$this->common_data['company_data']['company_logo'];  ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:40%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').';  ?></strong></h3>
                                <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                                <h4>Commission Scheme Details</h4>
                            </td>
                        </tr>
         
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_department');?><!--Department--></strong></td>
                            <td><strong>:&nbsp;</strong></td>
                            <td><?php echo $master['department']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_document_date');?><!--Document Date--></strong></td>
                            <td><strong>:&nbsp;</strong></td>
                            <td><?php echo $master['documentDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_narration');?><!--Narration--></strong></td>
                            <td><strong>:&nbsp;</strong></td>
                            <td><?php echo $master['narration']; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <br>
    <hr style="margin-top: 0%">
    <?php if ( $master['approvedYN'] == 1 ){ ?>
    <div class="row" style="margin-bottom: 25px">
        <div class="col-md-4">
            <table class="<?php echo table_class(); ?>">
                <tr>
                    <td class=""><span class="bg-danger ">&nbsp;&nbsp;&nbsp;&nbsp;</span> Inactive 
                    <!--Inactive--> 
                    </td>
                </tr>   
            </table> 
        </div>
    </div>
  <?php } ?>

    <div class="" style="margin-bottom: 25px">
    <div class="">
        <table id="commissionTranferTbl_con" class="<?php echo table_class(); ?>" style="">
            <thead>
            <tr>
                <th style="min-width: 10px" rowspan="2">#</th>
                <th style="min-width: 100px;" rowspan="2">LEVEL</th><!--Item Code-->
                <?php 
                $category = array_group_by($extra['details'], 'itemCategoryID');
                foreach ($category as $key => $partyCategoryID) {
                    $category1 = array_group_by($partyCategoryID, 'itemAutoID');
                    echo '<th colspan='.count($category1).' >' . $partyCategoryID[0]['description'] . '</th>';
                }
                ?>
                <th style="min-width: 50px" rowspan="2">AVG PAYMENT PER PRODUCT </th>
            </tr>
            <tr><?php
                $category = array_group_by($extra['details'], 'itemCategoryID');
                foreach ($category as $key => $partyCategoryID) {
                    $category1 = array_group_by($partyCategoryID, 'itemAutoID');
                    foreach ($category1 as $key => $det) {
                        echo '<th>'.$det[0]['itemSystemCode'] . "<br>(" . $det[0]['seconeryItemCode'] . ")<br>" . $det[0]['itemDescription'] . '</th>';
                    }
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $category2 = array_group_by($extra['details'], 'itemAutoID');
            if(!empty($extra['designation'])) {
                $a = 1;
                $c=0;
                foreach ($extra['designation'] as $des) {
                    echo '<tr>';
                    echo '<td>' . $a . '</td>';
                    echo '<td>' . $des['DesDescription'] . '</td>';
                    if(!empty($extra['details'])) {
                        $totalCommission = 0;
                        $avgPaymentPerProduct=0;
                        foreach ($category as $key => $partyCategoryID) {
                            $category1 = array_group_by($partyCategoryID, 'itemAutoID');
                            $b = 0;
                            
                            foreach ($category1 as $key => $det) {  //print_r($key); ?>
                              
                                <?php  $bgcolor='';
                                if($master['approvedYN'] == 1 && $det[$c]['isActive'] == 0 ) {
                                    $bgcolor='bg-danger';
                                }                            
                                echo '<td class="tdIn '.$bgcolor.'" style="text-align:center;" > <input type="number" class="form-control itemCommission " type="text" name="itemCommission"
                                    data-schemeDesignationID='.$des['schemeDesignationID'].'
                                    data-designationID='.$des['designationID'].'
                                    data-schemeMasterID='.$des['schemeMasterID'].'
                                    data-schemeDetailID='.$det[$c]['schemeDetailID'].'
                                    value=' .$det[$c]['commisionAmount'].'
                                    data-itemAutoID='.$det[$c]['itemAutoID'].'
                                    data-currentCommission='.$det[$c]['commisionAmount'].' readonly>  </td>';
                                $b++;
                                $totalCommission = $totalCommission + $det[$c]['commisionAmount'];
                            }
                        }
                        $avgPaymentPerProduct = $totalCommission / count($category2);
                    } 
                    echo '<td ><span id="avgPaymentPerProduct_'.$des['designationID'].'"  disabled>'.number_format($avgPaymentPerProduct,$company_currecny_decimal).'</span></td>';
                    echo '</tr>';
                    $a++; $c++;
                }

            } else { ?>
                <tr class="danger">
                    <td colspan="14" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td><!--No Records Found-->
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
    
    <br>
</div>

<script>
    var leftAlign = 0;
    if(<?php echo count($category2)?> > 8 ){
        leftAlign = 2;
    }
    $('#commissionTranferTbl_con').tableHeadFixer({
        head: true,
        foot: true,
        left: leftAlign,
        right: 0,
        'z-index': 10
    });

    

</script>