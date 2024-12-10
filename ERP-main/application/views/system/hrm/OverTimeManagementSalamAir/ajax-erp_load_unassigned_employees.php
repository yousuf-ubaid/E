<label class="col-sm-4 control-label"> Employee</label>
<div class="col-sm-4">
    <div class="input-group">
    <?php echo form_dropdown('empID[]', all_not_assigned_employee_to_OT_group($otGroupID), '', 'class="form-control" multiple="multiple"  id="empID" required"'); ?>
</div>
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
