<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->load->helper('erp_data_sync');
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('ecommerce', $primaryLanguage);
    $title = $this->lang->line('ecommerce_sales_data');
    $this->lang->line($title, $primaryLanguage);
    echo '<h4 class="box-title" id="box-header-title">Sales Data Settings</h4>';

    $country = load_country_drop();
    $currncy_arr = all_currency_new_drop();
    $customerCategory = fetch_segment();// party_category(1, true);
    $company_country = array();
    $company_bank = company_bank_account_drop();
    $settings = get_clent_ecommerce_settings();

    if (isset($country)) {
        foreach ($country as $row) {
            $company_country[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
        }
    }

?>

<div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-12 text-center">
            &nbsp;
        </div>
    
    </div>
</div>

<div class="table-responsive">
    <table id="clent_data" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 10%">Company Name</th><!--Code-->
            <th style="min-width: 10%">Setting</th><!--Code-->
            <th style="min-width: 10%">Value</th><!--Code-->
            <th style="min-width: 10%">Action</th><!--Code-->
        </tr>
        </thead>
        <tbod>

            <tr class="text-center">
                <td> <?php echo $this->common_data['company_data']['company_name'] ?></td>
                <td> Company Driver ID </td>
                <td> <input type="text" value="<?php echo $settings['company_driver_id'] ?>" name="company_driver_id" id="company_driver_id" class="form-control text-center" /> </td>
                <td  class="text-center"> <button class="btn btn-success" onclick="update_setting('company_driver_id')"> Update</button> </td>
            <tr>

            <tr class="text-center">
                <td> <?php echo $this->common_data['company_data']['company_name'] ?></td>
                <td> Company Country </td>
                <td>
                    <?php echo form_dropdown('company_country', $company_country, $settings['company_country'], 'class="form-control select2" id="company_country"'); ?>
                </td>
                <td  class="text-center"> <button class="btn btn-success" onclick="update_setting('company_country')"> Update</button> </td>
            <tr>

            <tr class="text-center">
                <td> <?php echo $this->common_data['company_data']['company_name'] ?></td>
                <td> Company Currency </td>
                <td> 
                    <?php echo form_dropdown('company_currency',$currncy_arr, $settings['company_currency'], 'class="form-control select2" id="company_currency"'); ?>
                </td>
                <td  class="text-center"> <button class="btn btn-success" onclick="update_setting('company_currency')"> Update</button> </td>
            <tr>

            <tr class="text-center">
                <td> <?php echo $this->common_data['company_data']['company_name'] ?></td>
                <td> Company Category </td>
                <td> 
                    <?php echo form_dropdown('company_category', $customerCategory, $settings['company_category'], 'class="form-control select2" id="company_category"'); ?>
                </td>
                <td  class="text-center"> <button class="btn btn-success" onclick="update_setting('company_category')"> Update</button> </td>
            <tr>

            <tr class="text-center">
                <td> <?php echo $this->common_data['company_data']['company_name'] ?></td>
                <td> Company Bank </td>
                <td> 
                    <?php echo form_dropdown('company_bank_id', $company_bank, $settings['company_bank_id'], 'class="form-control select2" id="company_bank_id"'); ?>
                </td>
                <td  class="text-center"> <button class="btn btn-success" onclick="update_setting('company_bank_id')"> Update</button> </td>
            <tr>
        </tbody>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<!-- Scripting area -->
<script type="text/javascript">

load_data_settings();
$('.select2').select2();

function load_data_settings() {

    $.ajax({
        async: true,
        type: 'post',
        // dataType: 'json',
        data: {},
        url: "<?php echo site_url('DataSync/load_client_setting'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            let settings = JSON.parse(data);
            $('#gl_code').val(settings.supplier_gl_code);
            
        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });

}

function update_setting(type){

   // let updated_gl_code = $('#gl_code').val();
    let company_driver_id = $('#company_driver_id').val();
    let company_country = $('#company_country').val();
    let currncy_arr = $('#company_currency').val();
    let customerCategory = $('#company_category').val();
    let company_bank_id = $('#company_bank_id').val();

    $.ajax({
        async: true,
        type: 'post',
        // dataType: 'json',
        data: {'company_driver_id':company_driver_id, 'company_country': company_country,'company_currency' : currncy_arr,'company_category': customerCategory,'company_bank_id': company_bank_id ,'updated_type': type },
        url: "<?php echo site_url('DataSync/update_client_setting'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            refreshNotifications(true);
            
        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });

}

</script>