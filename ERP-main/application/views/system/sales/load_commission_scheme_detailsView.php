<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$avgPaymentPerProduct=0;
$company_currecny_decimal = get_company_currency_decimal();
$category = array();
$category2 = array();
/* $itempulled = 0;
if($extra['details']){
    $itempulled = 1;
} */
$noofItems = 0 ;
?>
<style>
  
    tr:hover, tr.selected {
        background-color: #E3E1E7;
        opacity: 1;
        z-index: -1;
    }
    .itemCommission {
        display: inline;
        height: 24px;
        padding: 0px;
        padding-right: 2px;
        padding-left: 2px;
        font-size: 11px;
        width: 70px;
    }
    .sortOrder{
        display: inline;
        height: 24px;
        padding: 0px;
        padding-right: 2px;
        padding-left: 2px;
        font-size: 11px;
        width: 30px;
    }
    .tdIn {
        width: 140px;
        padding: 2px;
    }

</style>

<div class="" style="margin-bottom: 25px">
    <div class="">
        <table id="commissionTranferTbl" class="<?php echo table_class(); ?>" style="">
            <thead>
            <tr>
                <th style="min-width: 10px" rowspan="2">#</th>
                <th style="min-width: 100px;" rowspan="2">LEVEL</th>
                <th style="min-width: 100px;" rowspan="2">Sort Order</th> 
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
                        echo '<th>'.$det[0]['itemSystemCode'] . "<br>(" . $det[0]['seconeryItemCode'] . ")<br>" . $det[0]['itemDescription'] . '
                        &nbsp;<a onclick="delete_cs_item('.$key.', '.$det[0]['schemeMasterID'].');"><span title="" rel="tooltip" class="glyphicon glyphicon-trash delete-icon" data-original-title="Delete"></span></a>
                                            
                            </th>';
                    }
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            if(!empty($extra['details'])) {
                $rowdet = array_group_by($extra['details'], 'designationID');
                $items = array_group_by($extra['details'], 'itemAutoID');
                $noofItems=count($items);
                $a=1;
                $b=0;
                foreach($rowdet as $desid => $det){
                    $totalCommission = 0;
                    $avgPaymentPerProduct=0;
                    echo '<tr>';
                    echo '<td>'.$a.'</td>';
                    echo '<td>'.$det[0]['DesDescription'].'</td>';
                    echo '<td class="" style="text-align:center;" > <input type="text" class="number form-control  sortOrder " type="text" name="sortOrder"
                            data-schemeDesignationID='.$det[$b]['schemeDesignationID'].'   
                            value='.$det[$b]['sortOrder'].' > </td>';
                    foreach($det as $key=>$column){
                        echo '<td class="tdIn" style="text-align:center;" > <input type="text" class="number form-control itemCommission " type="text" name="itemCommission"
                                                           data-schemeDesignationID='.$column['schemeDesignationID'].'
                                                           data-designationID='.$column['designationID'].'
                                                           data-schemeMasterID='.$column['schemeMasterID'].'
                                                           data-schemeDetailID='.$column['schemeDetailID'].'
                                                           value=' .$column['commisionAmount'].'
                                                           data-itemAutoID='.$column['itemAutoID'].'
                                                           data-currentCommission='.$column['commisionAmount'].' > 
                                                           </td>';  
                        
                       $totalCommission = $totalCommission + $column['commisionAmount'];
               
                   }
                   $avgPaymentPerProduct = $totalCommission / $noofItems;
                   echo '<td ><span id="avgPaymentPerProduct_'.$column['designationID'].'"  readonly>'.number_format($avgPaymentPerProduct,$company_currecny_decimal).'</span></td>';
                   echo '</tr>';
                    $a++;
                }
                $b++;
                
            }else if(!empty($extra['designation'])){
                $a=1;
                foreach ($extra['designation'] as $des) {
                    echo '<tr>';
                    echo '<td>' . $a . '</td>';
                    echo '<td>' . $des['DesDescription'] . '</td>';
                    echo '<td class="" style="text-align:center;" > <input type="text" class="number form-control  sortOrder " type="text" name="sortOrder"
                    data-schemeDesignationID='.$des['schemeDesignationID'].'   
                    value='.$des['sortOrder'].' > </td>';
                    echo '<td ><span id="avgPaymentPerProduct_'.$des['designationID'].'"  readonly>'.number_format($avgPaymentPerProduct,$company_currecny_decimal).'</span></td>';
                    
                    echo '</tr>';
                    $a++; 
                }
            }else{ ?>
                <tr class="danger">
                    <td colspan="14" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td><!--No Records Found-->
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    //var itempulled =  <?php //echo $itempulled ?> ;
    $( document ).ready(function() {
        
       /*   if(itempulled){
            $(".sortOrder").prop( "disabled", true );
        }else{
            $(".sortOrder").prop( "disabled", false );
        } */ 
        number_validation();
    });
    var leftAlign = 0;
    if(<?php echo $noofItems?> > 8 ){
        leftAlign = 3;
        //leftAlign = 2;

    }
    $('#commissionTranferTbl').tableHeadFixer({
        head: true,
        foot: true,
        left: leftAlign,
        right: 0,
        'z-index': 10
    });

 
    $(".itemCommission").change(function () {
        if ($(this).val() == "") {
            $(this).val(0);
        }
        var schemeDesignationID = $(this).attr('data-schemeDesignationID');
        var designationID = $(this).attr('data-designationID');
        var itemAutoID = $(this).attr('data-itemAutoID');
        var schemeMasterID = $(this).attr('data-schemeMasterID');
        var schemeDetailID = $(this).attr('data-schemeDetailID');
        var commissoinAmount = $(this).val();
        $(this).val(parseFloat(commissoinAmount));
        update_commission_amount(schemeMasterID, schemeDesignationID, schemeDetailID,designationID, itemAutoID, commissoinAmount, this);
    });
    
    function update_commission_amount(schemeMasterID, schemeDesignationID, schemeDetailID,designationID, itemAutoID, commissoinAmount, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                schemeMasterID: schemeMasterID,
                schemeDesignationID: schemeDesignationID,
                schemeDetailID: schemeDetailID,
                designationID: designationID,
                itemAutoID: itemAutoID,
                commissoinAmount: commissoinAmount
            },
            url: "<?php echo site_url('CommissionScheme/update_commission_amount'); ?>",
            beforeSend: function () {
                /*startLoad();*/
            },
            success: function (data) {
                var commission = $('#avgPaymentPerProduct_' + designationID).text();
                $('#avgPaymentPerProduct_' + designationID).html(data);
                $(element).attr('data-currentCommission',commissoinAmount);
            },
            error: function () {

            }
        });
    }

function delete_cs_item(itemAutoID,schemeID){
    swal({
        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
        text: "You want to delete this item",/*You want to delete this file!*/
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
    },
    function () {
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'itemAutoID':itemAutoID,'schemeID':schemeID},
            url :"<?php echo site_url('CommissionScheme/delete_sc_item'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    if(data[2] && data[2].length > 0){
                        $("#designation").multiselect2("disable");
                    }else{
                        $("#designation").multiselect2("enable");
                    } 
                    fetch_detail();
                }
                
            },error : function(){
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            }
        });
    });   

}

$(".sortOrder").change(function () {
        if ($(this).val() == "") {
            $(this).val(0);
        }
        var schemeDesignationID = $(this).attr('data-schemeDesignationID');
        var sortOrder = $(this).val();
        $(this).val(parseFloat(sortOrder));
        update_sort_order(schemeDesignationID, sortOrder, this);
    });
    function update_sort_order(schemeDesignationID, sortOrder, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                schemeDesignationID: schemeDesignationID,
                sortOrder:sortOrder
            },
            url: "<?php echo site_url('CommissionScheme/update_sort_order'); ?>",
            beforeSend: function () {
                
            },
            success: function (data) {
                fetch_detail();
            },
            error: function () {

            }
        });
    }    
</script>
