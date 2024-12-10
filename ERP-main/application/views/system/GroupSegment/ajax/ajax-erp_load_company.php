

<label class=" control-label"> Company</label>
<div class="">
<?php echo form_dropdown('companyID', segment_company_link($groupSegmentID), '', 'class="form-control select2" onchange="load_comapny_segment()" id="companyID" required"'); ?>
</div>


