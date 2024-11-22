<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
    <label  class=""><?php echo $this->lang->line('hrms_attendancecategories')?></label>
    <div >
        <?php echo form_dropdown('overtimeCategoryID[]', all_ot_category_drop($companyID), '', 'class="form-control" multiple onchange="diablebutton()"  id="overtimeCategoryID"'); ?>
    </div>


<script>
    $('#overtimeCategoryID').multiselect2({
        includeSelectAllOption: true,
        enableFiltering: true,
        onChange: function (element, checked) {
        }
    });
</script>

