<?php
$this->load->view('include/spur_go_header', $title);
?>
<style>
    .form-submit {
        width: 100%;
        border-radius: 5px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        -o-border-radius: 5px;
        -ms-border-radius: 5px;
        padding: 17px 20px;
        box-sizing: border-box;
        font-size: 14px;
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        border: none;
        background-image: -moz-linear-gradient(to left, #74ebd5, #9face6);
        background-image: -ms-linear-gradient(to left, #74ebd5, #9face6);
        background-image: -o-linear-gradient(to left, #74ebd5, #9face6);
        background-image: -webkit-linear-gradient(to left, #74ebd5, #9face6);
        background-image: linear-gradient(to left, #74ebd5, #9face6);
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<div style="padding: 5%;margin: 0 auto;">
    <?php echo form_open('', 'role="form" id="spur_go_signupcompany"'); ?>

    <header class="head-title">
        <h2>Company Detail</h2>
    </header>
    <div class="row">
        <div class="col-md-2">
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="form-group col-sm-4">
                    <label>Company Code<span title="required field"
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
                    <label>Company Currency <span title="required field"
                                                  style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                    <?php echo form_dropdown('company_default_currencyID', $currency_arr, '', 'class="form-control select2" id="company_default_currencyID" required'); ?>
                </div>

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
            </div>
        </div>
    </div>
    <br>
    <header class="head-title">
        <h2>Address</h2>
    </header>
    <div class="row">
        <div class="col-md-2">
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="form-group col-sm-4">
                    <label>Company Address 1 <span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                    <input type="text" class="form-control" id="companyaddress1" name="companyaddress1">
                </div>
                <div class="form-group col-sm-4">
                    <label>Company Address 2 <span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                    <input type="email" class="form-control" id="companyaddress2" name="companyaddress2">
                </div>
                <div class="form-group col-sm-4">
                    <label>Company City <span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                    <input type="text" class="form-control" id="companycity" name="companycity">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label>Company Postal Code <span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                    <input type="text" class="form-control" id="companypostalcode" name="companypostalcode">
                </div>
                <div class="form-group col-sm-4">
                    <label>Province </label>
                    <input type="text" class="form-control" id="province" name="province">
                </div>
                <div class="form-group col-sm-4">
                    <label>Company Country <span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
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
    <br>
    <header class="head-title">
        <h2>Users</h2>
    </header>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class='thead'>
                <tr>
                    <th class='theadtr'>#</th>
                    <th style="width: 26%" class="text-center theadtr">Name</th>
                    <th  class='theadtr'>User Name</th>
                    <th  class='theadtr'>Password</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-right">1.&nbsp;</td>
                    <td class="text-right"> <input type="text" name="nameprimary" id="nameprimary" class="form-control"></td>
                    <td class="text-right"> <input type="text" name="usernameprimary" id="usernameprimary" class="form-control"></td>
                    <td class="text-right"> <input type="password" name="passwordprimary" id="passwordprimary" class="form-control"></td>
                </tr>
                <tr>
                    <td class="text-right">2.&nbsp;</td>
                    <td class="text-right"> <input type="text" name="namesec" id="namesec" class="form-control"></td>
                    <td class="text-right"> <input type="text" name="usernamesec" id="usernamesec" class="form-control"></td>
                    <td class="text-right"> <input type="password" name="passwordsec" id="passwordsec" class="form-control"></td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>

    <br>
    <hr>

    <div class="row">
    </div>
    <div class="text-right m-t-xs" style="width: 50%">
        <input type="button" name="submit" id="submit" class="form-submit" style="margin: 0;position: absolute;-ms-transform: translateY(-50%);transform: translateY(20%);
        margin-left: -20%;width: 45%;" onclick="save_company_detail();" value="Sign up"/>
    </div>
    </form>
</div>

<?php
$this->load->view('include/footer_spur_go');
?>

<script type="text/javascript">
    $(document).ready(function () {
        $(".select2").select2();
    });

    function save_company_detail() {
        var data = $('#spur_go_signupcompany').serializeArray();
        data.push({'name': 'default_currency', 'value': $('#company_default_currencyID option:selected').text()});
        data.push({'name': 'reporting_currency', 'value': $('#company_default_currencyID option:selected').text() });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Spur_go/Savespurgo_Details'); ?>",
            beforeSend: function () {
                startLoad_spur_go();
                $("#holdon-overlay").css("background", "#000").delay(150);
            },
            success: function (data) {
                stopLoad_spur_go();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    location.reload();
                }
                stopLoad_spur_go();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad_spur_go();

            }
        });
    }
    function validatepassword_strength_primary(password) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'password': password},
            url: "<?php echo site_url('Spur_go/validatepassword_strength'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if (data[0] == 'e') {
                    $('#passwordprimary').val('');

                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');


            }
        });
    }
    function validatepassword_strength_sec(password) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data:{'password':password},
                url: "<?php echo site_url('Spur_go/validatepassword_strength'); ?>",
                beforeSend: function () {
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if (data[0] == 'e') {
                        $('#passwordsec').val('');

                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');


                }
            });
    }
</script>


