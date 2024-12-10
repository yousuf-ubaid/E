

<label class=" control-label"> Company</label>
<div class="">
<?php echo form_dropdown('companyID', uom_company_link($groupUOMMasterID), '', 'class="form-control select2" onchange="load_comapny_uom()" id="companyID" required"'); ?>
</div>


