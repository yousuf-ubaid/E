<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('configuration', $primaryLanguage);
?>
<label class=" control-label"><?php echo $this->lang->line('config_common_sub_group') ?> <!-- Sub Group--></label>
<div class="">
<?php echo form_dropdown('subGroupIDEmp', dropdown_subGroup($groupID), '', '  class="form-control" onchange="" id="subGroupIDEmp" required"'); ?>
</div>


