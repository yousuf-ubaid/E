
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
                  ?>
                    <input type="checkbox"  id="checkedCompanies" name="checkedCompanies[]" value="<?php echo $val ?>" checked>
                <?php
                }else{
                    ?>
                    <input type="checkbox"  id="checkedCompanies" name="checkedCompanies[]" value="<?php echo $val ?>">
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}
?>






