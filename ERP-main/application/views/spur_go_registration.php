<?php
$this->load->view('include/spur_go_header', $title);
$financeyearmonth = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December')
?>
    <style>
        .register {
            background: -webkit-linear-gradient(left, #669959, #00c6ff);
            margin-top: 0%;
            padding: 7%;

        }

        .register-left {
            text-align: center;
            color: #fff;
            margin-top: 4%;
        }

        .register-left input {
            border: none;
            border-radius: 1.5rem;
            padding: 2%;
            width: 60%;
            background: #f8f9fa;
            font-weight: bold;
            color: #383d41;
            margin-top: 30%;
            margin-bottom: 3%;
            cursor: pointer;
        }

        .register-right {
            background: #f8f9fa;
            border-top-left-radius: 10% 50%;
            border-bottom-left-radius: 10% 50%;
        }

        .register-left img {
            margin-top: 15%;
            margin-bottom: 5%;
            width: 50%;
            -webkit-animation: mover 2s infinite alternate;
            animation: mover 1s infinite alternate;
        }

        @-webkit-keyframes mover {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(-20px);
            }
        }

        @keyframes mover {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(-20px);
            }
        }

        .register-left p {
            font-weight: lighter;
            padding: 12%;
            margin-top: -9%;
        }

        .register .register-form {
            padding: 10%;
            margin-top: 8%;
        }

        .btnRegister {
            float: right;
            margin-top: 10%;
            border: none;
            border-radius: 1.5rem;
            padding: 2%;
            background: #0062cc;
            color: #fff;
            font-weight: 600;
            width: 50%;
            cursor: pointer;
        }

        .register .nav-tabs {
            margin-top: 3%;
            border: none;
            background: #0062cc;
            border-radius: 1.5rem;
            width: 28%;
            float: right;
        }

        .register .nav-tabs .nav-link {
            padding: 2%;
            height: 34px;
            font-weight: 600;
            color: #fff;
            border-top-right-radius: 1.5rem;
            border-bottom-right-radius: 1.5rem;
        }

        .register .nav-tabs .nav-link:hover {
            border: none;
        }

        .register .nav-tabs .nav-link.active {
            width: 100px;
            color: #0062cc;
            border: 2px solid #0062cc;
            border-top-left-radius: 1.5rem;
            border-bottom-left-radius: 1.5rem;
        }

        .register-heading {
            text-align: center;
            margin-top: 1%;
            margin-bottom: -15%;
            color: #495057;
        }
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
        }
        .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
            padding: 2px 5px !important;
            padding-top: 2px !important;
            padding-right: 5px !important;
            padding-bottom: 2px !important;
            padding-left: 5px !important;
            height: 37px !important;
        }

    </style>
<?php echo form_open('', 'role="form" id="spur_go_signupcompany"'); ?>
    <div class="register">
        <div class="row" style="margin-top: -2%">
            <div class="col-md-3 register-left">
                <img src="<?php echo base_url('images/spur-logo-200.png') ?>" alt=""/>
                <h3>Welcome</h3>
                <p><b style="font-weight: bold;">Simple cloud based accounting solution for a growing business</b></p>
            </div>

            <div class="col-md-9 register-right">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <h3 class="register-heading">Spur Go Registration</h3>
                        <div class="row register-form">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Company Code <span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                                    <input type="text" class="form-control" id="companycode" name="companycode" autocomplete="off" PLACEHOLDER="Ex: RC">
                                </div>
                            </div>
                             <div class="col-md-4">
                                 <div class="form-group">
                                     <label>Company Name <span title="required field"
                                                               style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                                     <input type="text" class="form-control" id="companyname" name="companyname" autocomplete="off" placeholder="Ryersen Holdings LLC">
                                 </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Website </label>
                                    <input type="text" class="form-control" id="companyurl" name="companyurl" autocomplete="off" placeholder="www.companyname.com">
                                </div>
                            </div>
                            <div class="col-md-4" style="margin-top: -2%">
                                <div class="form-group">
                                    <label>Email </label>
                                    <input type="email" class="form-control" id="companyemail" name="companyemail" autocomplete="off" placeholder="companyname@mail.com">
                                </div>
                            </div>

                            <div class="col-md-4"  style="margin-top: -2%">
                                <div class="form-group">
                                    <label>Phone </label>
                                    <input type="text" class="form-control" id="companyphone" name="companyphone" autocomplete="off" placeholder="757-860-6915">
                                </div>
                            </div>
                            <div class="col-md-4"  style="margin-top: -2%">
                            <div class="form-group">
                                <label>Currency <span title="required field"
                                                              style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                                <?php echo form_dropdown('company_default_currencyID', $currency_arr, '', 'class="form-control select2" id="company_default_currencyID" required'); ?>
                            </div>
                            </div>
                            <div class="col-md-4"  style="margin-top: -2%">
                            <div class="form-group">
                                <label>Address 1 <span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                                <input type="text" class="form-control" id="companyaddress1" name="companyaddress1" autocomplete="off" placeholder="4709 Shadowmar Drive">

                            </div>
                            </div>

                            <div class="col-md-4"  style="margin-top: -2%">
                            <div class="form-group">
                                <label>Address 2 </label>
                                <input type="text" class="form-control" id="companyaddress2" name="companyaddress2" autocomplete="off" placeholder="Kenner, LA">
                            </div>
                            </div>

                            <div class="col-md-4"  style="margin-top: -2%">
                            <div class="form-group">
                                <label>City <span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                                <input type="text" class="form-control" id="companycity" name="companycity" autocomplete="off" placeholder="Los Angeles">
                            </div>
                            </div>

                            <div class="col-md-4"  style="margin-top: -2%">
                            <div class="form-group">
                                <label>Postal Code <span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                                <input type="text" class="form-control" id="companypostalcode" name="companypostalcode" autocomplete="off" placeholder="70062">
                            </div>
                            </div>
                            <div class="col-md-4"  style="margin-top: -2%">
                            <div class="form-group">
                                <label>Country <span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                                <select class="form-control select2" name="companycountry" id="companycountry">
                                    <option value="">Select Country</option>
                                    <?php foreach ($countrys as $country) { ?>
                                        <option value="<?php echo $country['countryID'].'|'.$country['CountryDes']; ?>"><?php echo $country['CountryDes'] . ' | ' . $country['countryShortCode']; ?></option>
                                    <?php }; ?>
                                </select>
                            </div>
                            </div>
                            <div class="col-md-4"  style="margin-top: -2%">
                            <div class="form-group">
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

                            <div class="col-md-4"  style="margin-top: -2%">
                                <div class="form-group">
                                    <label>Finance year begining month <span title="required field"
                                                          style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                                    <?php echo form_dropdown('financeyearmonth', $financeyearmonth, '', 'class="form-control select2" id="financeyearmonth" required'); ?>

                                </div>
                            </div>

                            <div class="col-md-12" >
                                <div class="form-group">
                                <div class="skin skin-square item-iCheck">
                                    <div class="skin-section extraColumns"><input id="Istermsofservice" type="checkbox"
                                                                                  class="Istermsofservice" value="1" ><label
                                                for="checkbox">&nbsp;I agree to the Spur GO <a href="#" onclick="terms_and_conditions();">Terms of Service</a></label></div>
                                </div>
                            </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
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
                                                <td class="text-right"> <input type="text" name="nameprimary" id="nameprimary" class="form-control" autocomplete="off"></td>
                                                <td class="text-right"> <input type="text" name="usernameprimary" id="usernameprimary" class="form-control" autocomplete="off"></td>
                                                <td class="text-right"> <input type="password" name="passwordprimary" id="passwordprimary" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right">2.&nbsp;</td>
                                                <td class="text-right"> <input type="text" name="namesec" id="namesec" class="form-control" autocomplete="off"></td>
                                                <td class="text-right"> <input type="text" name="usernamesec" id="usernamesec" class="form-control" autocomplete="off"></td>
                                                <td class="text-right"> <input type="password" name="passwordsec" id="passwordsec" class="form-control"></td>
                                            </tr>

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="text-right m-t-xs" style="width: 50%">
                                    <input type="button" name="submit" id="submit" class="form-submit" style="margin: 0;position: absolute;-ms-transform: translateY(-50%);transform: translateY(20%);
        margin-left: -20%;width: 45%;" onclick="save_company_detail();" value="Sign up"/>
                                </div>
                            </div>


                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>


    </div>
    </form>
<div class="modal fade " id="termandcondition" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Spur Go Terms and Conditions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"  data-dismiss="modal">Agree</button>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view('include/footer_spur_go');
?>


<script type="text/javascript">
    $(document).ready(function () {
        $('.select2').select2();
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
    });

    function save_company_detail() {
        var primaryusername = $('#usernameprimary').val();
        var value = 0;
        if ($('#Istermsofservice').is(":checked"))
        {
            value = 1;
        }
        var data = $('#spur_go_signupcompany').serializeArray();
        data.push({'name': 'default_currency', 'value': $('#company_default_currencyID option:selected').text()});
        data.push({'name': 'reporting_currency', 'value': $('#company_default_currencyID option:selected').text() });
        data.push({'name': 'istermsYN', 'value':value});
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
                    swal({
                            title: "Success",
                            text: "A confirmation email sent to "+primaryusername,
                            type: "success",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            showCancelButton: false,
                            confirmButtonText: "OK",
                        },
                        function(){
                            location.reload();
                        });
                    setTimeout(function () {
                        location.reload();
                    }, 3500);
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
    function terms_and_conditions() {
        $('#termandcondition').modal('show');
    }
</script>

