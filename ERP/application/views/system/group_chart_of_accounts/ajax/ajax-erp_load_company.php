

<label class=" control-label"> Company</label>
<div class="">
<?php echo form_dropdown('companyID', chart_of_account_company_link($GLAutoID), '', 'class="form-control select2" onchange="load_comapny_chart_of_accounts()" id="companyID" required"'); ?>
</div>


