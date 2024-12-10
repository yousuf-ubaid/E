

<label class=" control-label"> Company</label>
<div class="">
<?php echo form_dropdown('companyID', warehouse_company_link($wareHouseAutoID), '', 'class="form-control select2" onchange="load_comapny_warehouse()" id="companyID" required"'); ?>
</div>


