<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="form-group col-sm-12">
   <table class="<?php echo table_class() ?>">
       <thead>
       <tr>
           <td><?php echo $this->lang->line('inventory_attribute'); ?></td>
           <td><?php echo $this->lang->line('inventory_assign'); ?></td>
           <td><?php echo $this->lang->line('inventory_is_mandatory'); ?></td>
       </tr>
       </thead>
       <tbody>
       <?php
       $a=array();
       foreach($attributAssign as $assign){
           $a[$assign['companyAttributeID']]=$assign['systemAttributeID'];
       }

       $b=array();
       foreach($attributAssign as $mandatory){
           $b[$assign['companyAttributeID']]=$assign['systemAttributeID'];
       }
       foreach($attributes as $val){
           ?>
           <tr>
               <td><?php echo $val['attributeDescription'];  ?><input type="hidden" name="systemAttributeID[]" value="<?php echo $val['systemAttributeID'];  ?>"></td>
               <?php

               if (in_array($val['systemAttributeID'], $a))
               {
                   $keys = array_keys($a, $val['systemAttributeID']);
                   ?>
                   <td><input type="hidden" id="companyAttributeID_<?php echo $val['systemAttributeID'];  ?>" name="companyAttributeID[]" value="<?php echo $keys[0]; ?>"><div class="skin skin-square" style="margin-top: 5%;"><div class="skin-section extraColumns"><input id="isdefault_<?php echo $val['systemAttributeID'];  ?>" type="checkbox" data-caption="" class="columnSelected" name="isdefault[]" value="<?php echo $val['systemAttributeID'];  ?>" checked><label for="checkbox">&nbsp;</label></div><input type="hidden" id="default_<?php echo $val['systemAttributeID'];  ?>" name="default[]" value="1"></td>
               <?php
               }
               else
               {
                   ?>
                   <td><input type="hidden" id="companyAttributeID_<?php echo $val['systemAttributeID'];  ?>" name="companyAttributeID[]" value=""><div class="skin skin-square" style="margin-top: 5%;"><div class="skin-section extraColumns"><input id="isdefault_<?php echo $val['systemAttributeID'];  ?>" type="checkbox" data-caption="" class="columnSelected" name="isdefault[]" value="<?php echo $val['systemAttributeID'];  ?>"><label for="checkbox">&nbsp;</label></div><input type="hidden" id="default_<?php echo $val['systemAttributeID'];  ?>" name="default[]" value="0"></td>
                   <?php
               }

               if (in_array($val['systemAttributeID'], $a))
               {
                   $keys = array_keys($a, $val['systemAttributeID']);
                   $this->db->select('isMandatory');
                   $this->db->from('srp_erp_companyattributeassign');
                   $this->db->where('companyAttributeID',$keys[0]);
                   $isMandatory = $this->db->get()->row_array();
                   if($isMandatory['isMandatory']==1){
                       ?>
                       <td><select name="isMandatory[]" class="form-control" id="isMandatory_<?php echo $val['systemAttributeID'];  ?>">
                               <option value="0"><?php echo $this->lang->line('common_no'); ?></option>
                               <option value="1" selected><?php echo $this->lang->line('common_yes'); ?></option>
                           </select></td>
                       <?php
                   }else{
                       ?>
                       <td><select name="isMandatory[]" class="form-control" id="isMandatory_<?php echo $val['systemAttributeID'];  ?>">
                               <option value="0"><?php echo $this->lang->line('common_no'); ?></option>
                               <option value="1"><?php echo $this->lang->line('common_yes'); ?></option>
                           </select></td>
                       <?php
                   }
               }
               else
               {
                   ?>
                   <td><select name="isMandatory[]" class="form-control" id="isMandatory_<?php echo $val['systemAttributeID'];  ?>">
                           <option value="0"><?php echo $this->lang->line('common_no'); ?></option>
                           <option value="1"><?php echo $this->lang->line('common_yes'); ?></option>
                       </select></td>
                   <?php
               }
               ?>

           </tr>
       <?php
       }
       ?>
       </tbody>
   </table>
</div>

<script>
    $('.extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });


    $('.columnSelected').on('ifChecked', function(event){
        update_default($(this).val())
    });
    $('.columnSelected').on('ifUnchecked', function(event){
        update_default($(this).val())
    });

    function update_default(systemAttributeID){
        if ($('#isdefault_'+systemAttributeID).is(':checked')){
            $('#default_'+systemAttributeID).val(1);
        }else{
            $('#default_'+systemAttributeID).val(0);
        }
    }
</script>



