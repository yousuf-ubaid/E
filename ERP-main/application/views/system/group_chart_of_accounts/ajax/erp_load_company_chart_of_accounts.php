<div class="row">
    <div class="col-md-12">
        <div style="font-size: 16px; font-weight: 700;"><?php echo $extra['chartofacccountdetails']['systemAccountCode'].' - '.$extra['chartofacccountdetails']['GLSecondaryCode'].' - '.$extra['chartofacccountdetails']['GLDescription'] ?></div>
    </div>
</div>
<br>
<?php
foreach($companyID as $val){
    $companyid= get_company_accoding_to_id($val);
    $chartval= get_group_chartofaccounts_details($groupChartofAccountMasterID,$val);
    ?>
    <div class="row">
        <div class="form-group col-sm-5" >
            <div class="">
                <input type="text" class="form-control" id="companyID" value="<?php echo $companyid['company_code'].' | '.$companyid['company_name'] ?>" name="companyID[]" readonly>
                <!--<input type="hidden" class="form-control" id="id" value="<?php /*echo $val */?>" name="id">-->
                <input type="hidden" class="form-control" id="companyIDgrp" value="<?php echo $val ?>" name="companyIDgrp[]">
            </div>
        </div>
        <div class="form-group col-sm-5" >
            <div class="">
                <?php
                if(!empty($chartval)){
                    echo form_dropdown('chartofAccountID[]', dropdown_companychartofAccounts($val,$chartval['chartofAccountID'],$groupChartofAccountMasterID,$masterAccountYN), $chartval['chartofAccountID'], 'class="form-control select2" id="chartofAccountID_'.$val.'" required"');
                }else{
                    echo form_dropdown('chartofAccountID[]', dropdown_companychartofAccounts($val,'',$groupChartofAccountMasterID,$masterAccountYN), '', 'class="form-control select2" id="chartofAccountID_'.$val.'" required"');
                }
                ?>
            </div>
        </div>
        <div class="form-group col-sm-2" >
            <div class="">
                <button  class="btn btn-default btn-xs" onclick="clearcustomer(<?php echo $val ?>)" type="button">Clear</button>
            </div>
        </div>
    </div>
    <?php
}
?>






