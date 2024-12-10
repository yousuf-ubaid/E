<?php
$this->load->helper('buyback_helper');
foreach($companyID as $val){
    $companyid= get_company_accoding_to_id($val);
    $customerval= get_group_farmer_details($groupfarmID,$val);
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
                if(!empty($customerval)){
                    echo form_dropdown('farmID[]', dropdown_companyFarms($groupfarmID, $val,$customerval['farmID']), $customerval['farmID'], 'class="form-control select2" id="farmID_'.$val.'" required"');
                }else{
                    echo form_dropdown('farmID[]', dropdown_companyFarms($groupfarmID, $val), '', 'class="form-control select2" id="farmID_'.$val.'" required"');
                }
                ?>
            </div>
        </div>
        <div class="form-group col-sm-2" >
            <div class="">
                <button  class="btn btn-default btn-xs" onclick="clearFarmer(<?php echo $val ?>)" type="button">Clear</button>
            </div>
        </div>
    </div>
    <?php
}
?>