<?php
foreach($companyID as $val){
    $companyid= get_company_accoding_to_id($val);
    $supplierval= get_group_supplier_details($groupSupplierMasterID,$val);
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
                if(!empty($supplierval)){
                    echo form_dropdown('SupplierMasterID[]', dropdown_companySuppliers($val,$supplierval['SupplierMasterID']), $supplierval['SupplierMasterID'], 'class="form-control select2" id="SupplierMasterID_'.$val.'" required"');
                }else{
                    echo form_dropdown('SupplierMasterID[]', dropdown_companySuppliers($val), '', 'class="form-control select2" id="SupplierMasterID'.$val.'" required"');
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

