<?php
$this->load->helpers('codification');
$attribut_arr = all_codification_master_drop();
?>
<div class="row">
    <?php
    if (!empty($setupDetail)) {
        foreach ($setupDetail as $val) {
            $setupDetailID=$val['setupDetailID'];
            $attributeID=$val['attributeID'];
            $fieldLength=$val['fieldLength'];
            ?>
            <div class="form-inline col-sm-2">
                <?php  echo form_dropdown('attributeID', $attribut_arr,$attributeID,'class="form-control" onchange="update_setup_details('.$setupDetailID.',\'attributeID\')" id="attributeID_'.$setupDetailID.'"'); ?>
            </div>
            <?php
        }
    }
    ?>
</div>
<!--<input type="number" class="form-control" value="<?php /*echo $fieldLength; */?>"  placeholder="Length" onchange="update_setup_details(<?php /*echo $val['setupDetailID']*/?>,'fieldLength')"  id="fieldLength_<?php /*echo $val['setupDetailID']*/?>" name="fieldLength">-->