
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('configuration', $primaryLanguage);
if($groupID)
?>
<label class=" control-label"><?php echo $this->lang->line('config_common_sub_group') ?> <!--Sub Group--></label>
<div class="">
<?php echo form_dropdown('subGroupID', dropdown_subGroup($groupID,$All), '', 'class="form-control" onchange="loadform()" id="subGroupID" required"'); ?>
</div>


