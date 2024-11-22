<?php
echo head_page('Section Master', true);
?>
<style>
    fieldset {
        border: 1px solid silver;
        border-radius: 5px;
        padding: 1%;
        padding-bottom: 15px;
        margin: 10px 15px;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500
    }
</style>


<div class="row">
    <div class="col-md-9">&nbsp;</div>
    <div class="col-md-3">
        <button class="btn btn-success-new size-sm">
            <i class="fa fa-plus"></i>Class and group
        </button>
        <button style="margin-right: 2px;" type="button" onclick="fetchPage('system/school/masters/section_create_envoy','','HRMS', '', '', '<?php echo $page_url; ?>')" class="btn btn-info-new size-sm pull-left">
            <i class="fa fa-plus"></i> Add
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="sectionM_envoy" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="width: 10px">SN</th>
                <th style="width: auto"><?php echo $this->lang->line('section'); ?>Section</th>
                <th style="width: auto"><?php echo $this->lang->line('principal_category'); ?>Principal Category</th>
                <th style="width: auto"><?php echo $this->lang->line('Syl_sort)order'); ?>Sort Order</th>
                <th style="width: 40px"><?php echo $this->lang->line('common_action'); ?></th>
            </tr>
        </thead>
    </table>
</div>