

<label class=" control-label"> Company</label>
<div class="">
<?php echo form_dropdown('companyID', itemcategory_company_link($itemCategoryID), '', 'class="form-control select2" onchange="load_comapny_itemcategories()" id="companyID" required"'); ?>
</div>


