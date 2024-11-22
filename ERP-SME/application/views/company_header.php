<div id="step1" class="tab-pane">
    <?php echo form_open('', 'role="form" id="company_form"'); ?>
    <div class="row">
        <div class="col-md-3">
            <center>
                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                    <img src="<?php echo base_url('images/no-logo.png'); ?>" id="img" alt="...">
                </div>
            </center>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="form-group col-sm-4">
                    <label>Company Code <span title="required field"
                                              style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                    <input type="text" class="form-control" id="companycode" name="companycode">
                </div>
                <div class="form-group col-sm-8">
                    <label>Company Name <span title="required field"
                                              style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                    <input type="text" class="form-control" id="companyname" name="companyname">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label>Company URL </label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                        <input type="text" class="form-control" id="companyurl" name="companyurl">
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label>Company Email </label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                        <input type="email" class="form-control" id="companyemail" name="companyemail">
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label>Company Phone </label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                        <input type="text" class="form-control" id="companyphone" name="companyphone">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label>Company Start Date <span title="required field"
                                                    style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="companystartdate" value="<?php echo date('Y-m-d'); ?>"
                               id="companystartdate" class="form-control" required>
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label>Company Default Currency <span title="required field"
                                                          style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                    <?php echo form_dropdown('company_default_currencyID', $currency_arr, '', 'class="form-control select2" id="company_default_currencyID" required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label>Company Reporting Currency <span title="required field"
                                                            style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                    <?php echo form_dropdown('company_reporting_currencyID', $currency_arr, '', 'class="form-control select2" id="company_reporting_currencyID" required'); ?>
                </div>
            </div>

            <div class="row">

                <div class="form-group col-sm-4">
                    <label>Timezone <span title="required field"
                                          style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                    <?php
                    $CI =& get_instance();
                    $zoneMaster = $CI->db->query("SELECT srp_erp_timezonemaster.description master, srp_erp_timezonedetail.description detail, detailID FROM srp_erp_timezonemaster INNER JOIN srp_erp_timezonedetail ON srp_erp_timezonemaster.masterID = srp_erp_timezonedetail.masterID ORDER BY srp_erp_timezonemaster.masterID,srp_erp_timezonemaster.description ASC")->result_array();
                    $groups = array();
                    foreach ($zoneMaster as $employee) {
                        $groups[$employee['master']][$employee['detailID']] = $employee['detail'];
                    }
                    echo ' <select id="timezone" name="timezone" class="form-control select2">';
                    foreach ($groups as $label => $opt) { ?>
                        <optgroup label="<?php echo $label; ?>">
                            <?php foreach ($opt as $id => $name) { ?>
                                <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                            <?php } ?>
                        </optgroup>
                        <?php
                    }
                    echo '</select>'
                    ?>

                </div>

                <div class="form-group col-sm-4"></div>

                <div class="form-group col-sm-4">
                    <label for="diskUsage">Total Upload File size</label>
                    <input type="text" class="form-control" id="diskUsage" readonly>
                </div>
            </div>

        </div>
    </div>
    <hr>
    <div class="row form-horizontal">
        <div class="col-sm-5">
            <div class="form-group">
                <label for="legalname" class="col-sm-5 control-label">School Link <span title="required field"
                                                                                        style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                <div class="col-sm-7">
                    <?php echo form_dropdown('school_id', $school_arr, '0|0', 'class="form-control" id="school_id" required'); ?>
                </div>
            </div>
            <!-- <div class="form-group">
                    <label for="legalname"  class="col-sm-5 control-label">School User</label>
                    <div class="col-sm-7">
                        <?php //echo form_dropdown('school_id', array('' =>'Select User');,'','class="form-control" id="school_id" required'); ?>
                    </div>
                </div> -->
            <div class="form-group">
                <label for="legalname" class="col-sm-5 control-label">Legal Name </label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="legalname" name="legalname">
                </div>
            </div>
            <div class="form-group">
                <label for="txtidntificationno" class="col-sm-5 control-label">Tax Identification No</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="txtidntificationno" name="txtidntificationno">
                </div>
            </div>
            <div class="form-group">
                <label for="textyear" class="col-sm-5 control-label">Tax Year</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="textyear" name="textyear">
                </div>
            </div>
            <div class="form-group">
                <label for="industryID" class="col-sm-5 control-label">industryID</label>
                <div class="col-sm-7">
                    <?php echo form_dropdown('industryID', fetch_industry(), '', 'class="form-control" id="industryID" '); ?>
                </div>
            </div>
            <!-- <div class="form-group">
                <label for="industryID" class="col-sm-5 control-label">Default Segment <span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                <div class="col-sm-7">

                </div>
            </div> -->
        </div>
        <div class="col-sm-7">
            <div class="form-group">
                <label for="per_address_1"
                       class="col-sm-4 control-label">Permanent <span title="required field"
                                                                      style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="companyaddress1" name="companyaddress1"
                           placeholder="Company Address 1" required>
                </div>
            </div>
            <div class="form-group">
                <label for="per_address_2" class="col-sm-4 control-label"></label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" id="companyaddress2" name="companyaddress2"
                           placeholder="Company Address 2" required>
                </div>
            </div>
            <div class="form-group">
                <label for="per_city" class="col-sm-4 control-label"></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="companycity" name="companycity"
                           placeholder="Company City" required>
                </div>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="companypostalcode" name="companypostalcode"
                           placeholder="Postal Code">
                </div>
            </div>
            <div class="form-group">
                <label for="per_city" class="col-sm-4 control-label"></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="companyprovince" name="companyprovince"
                           placeholder="Province">
                </div>
            </div>
            <div class="form-group">
                <label for="per_country" class="col-sm-4 control-label"></label>
                <div class="col-sm-5">
                    <div class="input-group">
                        <select class="form-control select2" name="companycountry" id="companycountry">
                            <option value="">Select Country</option>
                            <?php foreach ($countrys as $country) { ?>
                                <option value="<?php echo $country['countryID'].'|'.$country['CountryDes']; ?>"><?php echo $country['CountryDes'] . ' | ' . $country['countryShortCode']; ?></option>
                            <?php }; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="text-right m-t-xs">
        <button class="btn btn-primary" type="submit">Save & Next</button>
    </div>
    </form>
</div>

<script>
    var company_id = <?php echo $company_id ?>;
    load_company_header();
    $('.select2').select2();
    $('#company_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            companycode: {validators: {notEmpty: {message: 'Company Code is required.'}}},
            companyname: {validators: {notEmpty: {message: 'Company Name is required.'}}},
            companystartdate: {validators: {notEmpty: {message: 'Company Start Date is required.'}}},
            //companyurl                      : {validators: {notEmpty: {message: 'Company URL is required.'}}},
            //companyemail                    : {validators: {notEmpty: {message: 'Company Email is required.'}}},
            //companyphone                    : {validators: {notEmpty: {message: 'Company Phone is required.'}}},
            companyaddress1: {validators: {notEmpty: {message: 'Company Address 1 is required.'}}},
            companyaddress2: {validators: {notEmpty: {message: 'Company Address 2 is required.'}}},
            companycity: {validators: {notEmpty: {message: 'Company City is required.'}}},
            //companyprovince                 : {validators: {notEmpty: {message: 'Company Province is required.'}}},
            //companypostalcode               : {validators: {notEmpty: {message: 'Company Postal Code is required.'}}},
            company_default_currencyID: {validators: {notEmpty: {message: 'Default Currency is required.'}}},
            company_reporting_currencyID: {validators: {notEmpty: {message: 'Reporting Currency is required.'}}},
            //companyprovince                 : {validators: {notEmpty: {message: 'Company Province is required.'}}},
            companycountry: {validators: {notEmpty: {message: 'Company Country is required.'}}},
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        $("#company_default_currencyID").prop("disabled", false);
        $("#company_reporting_currencyID").prop("disabled", false);
        $("#companycode").prop("disabled", false);
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'companyid', 'value': company_id});
        data.push({'name': 'default_currency', 'value': $('#company_default_currencyID option:selected').text()});
        data.push({
            'name': 'reporting_currency',
            'value': $('#company_reporting_currencyID option:selected').text()
        });
        data.push({'name': 'industry_dec', 'value': $('#industryID option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Dashboard/save_company'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#company-name-container').show();
                    $(".company-name-header").text( $('#companyname').val() +' [ '+ $('#companycode').val() +' ] ');

                    updateBrowserUrl('#step2');
                    $('[href=#step2]').removeClass('disabled');
                    $('[href=#step2]').tab('show');
                    companyid = data['last_id'];
                    load_users_data_table();
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });


    function load_company_header() {
        if (company_id) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'companyid': company_id},
                url: "<?php echo site_url('Dashboard/load_company_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {                        
                        $("#companycode").val(data['company_code']);
                        $("#companycode").prop("disabled", true);
                        $('#companyname').val(data['company_name']);
                        $('#companystartdate').val(data['company_start_date']);
                        $('#companyurl').val(data['company_url']);
                        $('#companyemail').val(data['company_email']);
                        $('#companyphone').val(data['company_phone']);
                        $('#companyaddress1').val(data['company_address1']);
                        $('#companyaddress2').val(data['company_address2']);
                        $('#companycity').val(data['company_city']);
                        $('#legalname').val(data['legalName']);
                        $('#txtidntificationno').val(data['textIdentificationNo']);
                        $('#textyear').val(data['textYear']);
                        $('#industryID').val(data['industryID']);
                        $('#default_segment').val(data['default_segment']);
                        $('#companyprovince').val(data['company_province']);
                        $('#companypostalcode').val(data['company_postalcode']);
                        $('#companycountry').val(data['countryID']+'|'+data['company_country']).change();
                        $('#company_default_currencyID').val(data['company_default_currencyID']).change();
                        $("#company_default_currencyID").prop("disabled", true);
                        $('#company_reporting_currencyID').val(data['company_reporting_currencyID']).change();
                        $("#company_reporting_currencyID").prop("disabled", true);
                        $("#timezone").val(data['defaultTimezoneID']).change();

                        $("#diskUsage").val(data['diskUsage']);
                    }
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

</script>
