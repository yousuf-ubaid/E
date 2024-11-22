<?php
echo head_page('Syllabus Master', true);
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
    <div class="col-md-10">&nbsp;</div>
    <div class="col-md-2">
        <button class="btn btn-warning-new size-sm" onclick="exportXML()">
            <i class="fa fa-upload"></i> Export XML
        </button>
        <button style="margin-right: 2px;" type="button" onclick="fetchPage('system/school/masters/syllabus_create_envoy','','HRMS', '', '', '<?php echo $page_url; ?>')" class="btn btn-info-new size-sm pull-left">
            <i class="fa fa-plus"></i> Add
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="syllabusM_envoy" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="width: 10px">SN</th>
                <th style="width: auto"><?php echo $this->lang->line('Syllabus'); ?>Syllabus</th>
                <th style="width: auto;"><?php echo $this->lang->line('Syl_Default'); ?>Default</th>
                <th style="width: auto"><?php echo $this->lang->line('Syl_sort)order'); ?>Sort Order</th>
                <th style="width: 40px"><?php echo $this->lang->line('common_action'); ?></th>
            </tr>
        </thead>
    </table>
</div>