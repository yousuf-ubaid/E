<?php
$this->load->helper('buyback_helper');
foreach($companyID as $val){
    $companyid= get_company_accoding_to_id($val);
    $subArea_val= get_group_sub_area_details($groupLocationID,$masterID,$val);
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
                if(!empty($subArea_val)){
                    echo form_dropdown('locationID[]', dropdown_company_subArea($val,$masterID,$subArea_val['locationID']), $subArea_val['locationID'], 'class="form-control select2" id="subLocationID_'.$val.'" required"');
                }else{
                    echo form_dropdown('locationID[]', dropdown_company_subArea($val,$masterID), '', 'class="form-control select2" id="subLocationID_'.$val.'" required"');
                }
                ?>
            </div>
        </div>
        <div class="form-group col-sm-2" >
            <div class="">
                <button  class="btn btn-default btn-xs" onclick="clearSubArea(<?php echo $val ?>)" type="button">Clear</button>
            </div>
        </div>
    </div>
    <?php
}
?>