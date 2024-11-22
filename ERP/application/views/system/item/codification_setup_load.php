<?php
$this->load->helpers('codification');
$attribut_arr = all_codification_master_drop();
$companyID=$this->common_data['company_data']['company_id'];
?>
<?php
if (!empty($codesetup)) {
?>
<div class="row">
    <?php
    if(!empty($itemAutoID)){
        $itemAutoID=$itemAutoID;
    }else{
        $itemAutoID=0;
    }

        foreach ($codesetup as $val) {
            $attributeID=$val['attributeID'];
            $setupDetailID=$val['setupDetailID'];
            if($attributeDetailID>0){
                $attrID = $this->db->query("SELECT attributeID FROM srp_erp_itemcodificationattributedetails WHERE attributeDetailID = '{$attributeDetailID}'  ")->row_array();
                if($attrID['attributeID']==$val['masterID']){
                    $defaultDetal= load_detail_cod_drop($attributeID,$attributeDetailID);
                }else{
                    $defaultDetal= load_detail_cod_drop($attributeID,0);
                }
            }else{
                $defaultDetal= load_detail_cod_drop($attributeID,0,$itemAutoID);
            }

            ?>
            <!--load_codification_tmplat('.$setupDetailID.')-->
            <div class="form-group col-sm-2">
                <label><?php echo $val['attributeDescription'] ?></label>
                <select name="attributeDetailID[]" onchange="load_sub_codes(<?php echo $setupDetailID ?>,<?php echo $attributeID ?>,this)" id="attributeDetailID_<?php echo $setupDetailID ?>" class="form-control searchbox">
                    <option value="">Select Value </option>
                    <?php
                    foreach ($defaultDetal as $valu){
                        $selected='';
                        if($valu['attributeDetailID']==$valu['selecval']){
                            $selected='selected';
                        }
                        ?>
                        <option value="<?php echo $valu['attributeDetailID'] ?>" <?php echo $selected; ?>><?php echo $valu['detailDescription'] ?></option>
                        <?php
                    }
                    ?>

                </select>
            </div>
            <?php
        }

    ?>
</div>
<!--<input type="number" class="form-control" value="<?php /*echo $fieldLength; */?>"  placeholder="Length" onchange="update_setup_details(<?php /*echo $val['setupDetailID']*/?>,'fieldLength')"  id="fieldLength_<?php /*echo $val['setupDetailID']*/?>" name="fieldLength">-->
<?php }?>