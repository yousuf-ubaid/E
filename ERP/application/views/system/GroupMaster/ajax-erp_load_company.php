<div class="form-group col-sm-3" >
    <label class=" control-label"> Company</label>
    <div class="">
        <?php echo form_dropdown('companyIDdrp[]', customer_company_link($groupCustomerMasterID,false), '', 'class="form-control"  id="companyIDdrp"  multiple="multiple" "'); ?>
    </div>
</div>
<div class="form-group col-sm-3" >
    <label class=" control-label"> &nbsp;</label>
    <div>
        <button type="button" onclick="load_all_companies_customers()" class="btn btn-primary btn-sm"> Select Companies
        </button>
    </div>
</div>
<script>
    $(document).ready(function () {
    $('#companyIDdrp').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });
    });
</script>


