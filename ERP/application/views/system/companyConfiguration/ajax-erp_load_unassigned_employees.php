<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
?>

<label class=" control-label"> <?php echo $this->lang->line('common_employee') ?> <!--Employee--></label>
<div class="">
    <?php echo form_dropdown('empID[]', all_not_assigned_employee_to_subGroup($companyempGroupID), '', 'class="form-control" multiple="multiple"  id="empID" required"'); ?>
</div>

<script>
    $('#empID').multiselect2({
        enableFiltering: true,
        /* filterBehavior: 'value',*/
        includeSelectAllOption: true,
        enableCaseInsensitiveFiltering: true,
        maxHeight: 200
    });
</script>
