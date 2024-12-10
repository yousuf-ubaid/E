

<label class=" control-label"> Company</label>
<div class="">
<?php echo form_dropdown('companyID', supplier_company_link($groupSupplierMasterID), '', 'class="form-control select2" onchange="load_comapny_suppliers()" id="companyID" required"'); ?>
</div>


