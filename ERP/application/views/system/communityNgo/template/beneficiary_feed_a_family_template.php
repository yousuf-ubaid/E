<?php

$this->load->helper('community_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();

$countryCode_arr = all_country_codes();
$currency_arr = all_currency_new_drop();
$com_title = fetch_com_title();
$beneficiaryTypes = fetch_com_beneficiary_types();

$countries_arr = fetch_all_countries();
$hof_master = fetch_headsOf_family();
$statemaster = all_statemaster();
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>BENEFICIARY NAME & DETAILS</h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="" id="linkcontact_system">
                <div class="form-group col-sm-2">
                    <label class="title">System Reference No</label>
                </div>
                <div class="form-group col-sm-3">
                  <span class="input-req" title="Required Field">
                      <input type="text" name="systemCode" id="systemCode" class="form-control"
                             placeholder="System Reference No" readonly>
                      <input type="hidden" name="templateType" value="feedAFamily">
                  <span class="input-req-inner"></span></span>
                </div>
                <div class="form-group col-sm-1 search_cancel" style="width: 3%;" id="contact_text">
                    <i class="fa fa-link" onclick="linkContact()" title="Link to Beneficiary" aria-hidden="true"
                       style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
                </div>
            </div>
            <div class="hide" id="linkcontact_text">
                <div class="form-group col-sm-2">
                    <label class="title">Search Beneficiary</label>
                </div>
                <div class="form-group col-sm-3">
                    <input type="text" class="form-control f_search valcontact" name="contactname"
                           id="contactname"
                           placeholder="Search Beneficiary Name..">
                    <input type="hidden" name="contactID" id="contactID">
                </div>
                <div class="col-sm-1 search_cancel" style="width: 3%;">
                    <i class="fa fa-external-link" onclick="unlinkContact()" title="Unlink to Beneficiary"
                       aria-hidden="true"
                       style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
                </div>
            </div>
            <div class="form-group col-sm-2" style="margin-left: 5%;">
                <label class="title">Secondary Reference No</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <input type="text" name="secondaryCode" id="secondaryCode" class="form-control valueHelp disableHelp"
                       placeholder="Secondary Reference No">
                     <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Beneficiary Type</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('benificiaryType', $beneficiaryTypes, '',
                        'class="form-control select2 valueHelp disableHelp" id="benificiaryType" '); ?>
                    <span class="input-req-inner"></span></span>
            </div>

            <div class="form-group col-sm-2">
                <label class="title">Registered Date</label>
            </div>
            <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="registeredDate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="registeredDate" class="form-control valueHelp disableHelp">
                        </div>
                            <span class="input-req-inner" style="z-index: 100;"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Community Member</label>
            </div>
            <div class="form-group col-sm-4">
                  <span class="input-req" title="Required Field">
                <select onchange="fatch_comMemDel();" id="Com_MasterID" class="form-control select2"
                        name="Com_MasterID">
                    <option data-currency=""
                            value=""><?php echo $this->lang->line('CommunityNgo_select_family'); ?><!--Select leader--></option>
                    <?php

                    if (!empty($hof_master)) {
                        foreach ($hof_master as $val) {

                            ?>
                            <option value="<?php echo $val['Com_MasterID'] ?>"><?php echo $val['CName_with_initials'] ?></option>
                            <?php

                        }
                    }
                    ?>
                </select>
                       <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Title</label>
            </div>
            <div class="form-group col-sm-4">
                <!--hidden feild to capture FamMasterID-->
                <input type="number" name="FamMasterID" id="FamMasterID" value="" style="display: none;">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('emp_title', $com_title, '',
                        'class="form-control select2 valueHelp disableHelp" id="emp_title" '); ?>
                    <span class="input-req-inner"></span></span>
            </div>
            <div class="form-group col-sm-2">
            </div>
            <div class="form-group col-sm-4">

            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Full Name</label>
            </div>
            <div class="form-group col-sm-4">
             <span class="input-req" title="Required Field">
            <input type="text" name="fullName" id="fullName" class="form-control valueHelp disableHelp" placeholder="Full Name"
                   required>
                      <span class="input-req-inner"></span></span>
            </div>

            <div class="form-group col-sm-2">
                <label class="title">Name with Initials</label>
            </div>
            <div class="form-group col-sm-4">
                     <span class="input-req" title="Required Field">
            <input type="text" name="nameWithInitials" id="nameWithInitials" class="form-control valueHelp disableHelp"
                   placeholder="Name with Initials" required>
            <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Date of Birth</label>
            </div>
            <div class="form-group col-sm-4">
            <span class="input-req" title="Required Field">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="dateOfBirth"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="dateOfBirth" class="form-control valueHelp disableHelp">
                        </div>
                            <span class="input-req-inner" style="z-index: 100;"></span></span>
            </div>

            <div class="form-group col-sm-2">
                <label class="title">Email</label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="email" id="email" class="form-control valueHelp disableHelp" placeholder="Email">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Phone (Primary)</label>
            </div>
            <div class="form-group col-sm-1" style="width: 12%">
                                     <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('countryCodePrimary', $countryCode_arr, '', 'class="form-control valueHelp disableHelp" id="countryCodePrimary"'); ?>
                                         <span class="input-req-inner"></span></span>
            </div>
            <div class="form-group col-sm-1" style="padding-left: 0px;">
                <input type="text" name="phoneAreaCodePrimary" id="phoneAreaCodePrimary" class="form-control valueHelp disableHelp"
                       placeholder="Area Code">
            </div>
            <div class="form-group col-sm-3" style="padding-left: 0px;">
                  <span class="input-req" title="Required Field">
            <input type="text" name="phonePrimary" id="phonePrimary" class="form-control valueHelp disableHelp" placeholder="Phone Number"
                   required>
                      <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Phone (Secondary)</label>
            </div>
            <div class="form-group col-sm-1" style="width: 12%">
                <?php echo form_dropdown('countryCodeSecondary', $countryCode_arr, '', 'class="form-control select2 valueHelp disableHelp" id="countryCodeSecondary"'); ?>
            </div>
            <div class="form-group col-sm-1" style="padding-left: 0px;">
                <input type="text" name="phoneAreaCodeSecondary" id="phoneAreaCodeSecondary" class="form-control"
                       placeholder="Area Code">
            </div>
            <div class="form-group col-sm-3" style="padding-left: 0px;">
                <input type="text" name="phoneSecondary" id="phoneSecondary" class="form-control"
                       placeholder="Phone Number">
            </div>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>ADDRESS</h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Country</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <?php echo form_dropdown('countryID', $countries_arr, '', 'class="form-control select2" id="countryID"'); ?>
                    <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Province / State</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div id="div_load_province">
                    <span class="input-req" title="Required Field">
                <?php echo form_dropdown('province', $statemaster, '', 'class="form-control select2" id="province"'); ?>
                        <span class="input-req-inner"></span></span>
                </div>
                    <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Area / District</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div id="div_load_district">
                        <span class="input-req" title="Required Field">
                <?php echo form_dropdown('district', $statemaster, '', 'class="form-control select2" id="district"'); ?>
                            <span class="input-req-inner"></span></span>
                </div>
                    <span class="input-req-inner"></span></span>

            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Division</label>
            </div>
            <div class="form-group col-sm-4">
                 <span class="input-req" title="Required Field">
                <div id="div_load_division">

                        <span class="input-req" title="Required Field">
                <?php echo form_dropdown('division', $statemaster, '', 'class="form-control select2" id="division"'); ?>
                            <span class="input-req-inner"></span></span>
                </div>
                     <span class="input-req-inner"></span></span>

            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Mahalla</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div id="div_load_sub_division">
                        <span class="input-req" title="Required Field">
                <?php echo form_dropdown('subDivision', $statemaster, '', 'class="form-control select2" id="subDivision"'); ?>
                            <span class="input-req-inner"></span></span>
                </div>
                    <span class="input-req-inner"></span></span>

            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Postal Code</label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="postalcode" id="postalcode" class="form-control valueHelp disableHelp" placeholder="Postal Code">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2">
                <label class="title">Address</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field"><textarea class="form-control valueHelp disableHelp" id="address"
                                                                         name="address" rows="2"></textarea><span
                        class="input-req-inner"></span></span>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>BENEFICIARY FAMILY DESCRIPTION</h2>
                </header>
                <div class="row">
                    <div class="form-group col-sm-10" style="margin-top: 5px;">
                        <textarea class="form-control" rows="5" name="familyDescription"
                                  id="familyDescription"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.select2').select2();
        Inputmask().mask(document.querySelectorAll("input"));

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#campaign_header_form').bootstrapValidator('revalidateField', 'startdate');

        });

        initializeBeneficiaryTypeahead();

        tinymce.init({
            selector: "#familyDescription",
            height: 200,
            browser_spellcheck: true,
            plugins: [
                "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
            ],
            toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
            toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
            toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",

            menubar: false,
            toolbar_items_size: 'small',

            style_formats: [{
                title: 'Bold text',
                inline: 'b'
            }, {
                title: 'Red text',
                inline: 'span',
                styles: {
                    color: '#ff0000'
                }
            }, {
                title: 'Red header',
                block: 'h1',
                styles: {
                    color: '#ff0000'
                }
            }, {
                title: 'Example 1',
                inline: 'span',
                classes: 'example1'
            }, {
                title: 'Example 2',
                inline: 'span',
                classes: 'example2'
            }, {
                title: 'Table styles'
            }, {
                title: 'Table row 1',
                selector: 'tr',
                classes: 'tablerow1'
            }],

            templates: [{
                title: 'Test template 1',
                content: 'Test 1'
            }, {
                title: 'Test template 2',
                content: 'Test 2'
            }]
        });

    });



    $('#add-beneficiary-type').click(function () {
        $('#beneficiary-type-description').val('');
        $('#beneficiary-type-modal').modal({backdrop: 'static'});
    });


    function fatch_comMemDel() {
        var Com_MasterID = document.getElementById('Com_MasterID').value;

        if (Com_MasterID == "" || Com_MasterID == null) {
        } else {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: "CommunityNgo/searchCommunityMem",
                data: {Com_MasterID: Com_MasterID},
                success: function (datum) {

                    $('#fullName').val( datum.fullName );
                    $('#nameWithInitials').val(datum.CName_with_initials);
                    $('#emp_title').val(datum.TitleID).change();
                    $('#dateOfBirth').val(datum.CDOB);
                    $('#FamMasterID').val(datum.FamMasterID);
                    $('#email').val(datum.EmailID);
                    $('#countryCodePrimary').val(datum.CountryCodePrimary).change();
                    $('#phoneAreaCodePrimary').val(datum.AreaCodePrimary);
                    $('#phonePrimary').val(datum.TP_Mobile);
                    $('#countryCodeSecondary').val(datum.countryCodeSecondary).change();
                    $('#phoneAreaCodeSecondary').val(datum.AreaCodeSecondary);
                    $('#address').val(datum.C_Address);
                    $('#phoneSecondary').val(datum.TP_home);
                    $('#countryID').val(datum.countyID).change();
                    $('#province').val(datum.province).change();
                    $('#district').val(datum.district).change();
                    $('#division').val(datum.division).change();
                    $('#subDivision').val(datum.subDivision).change();

                }
            });

        }
    }

    function save_beneficiary_type() {
        var type = $.trim($('#beneficiary-type-description').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'type': type},
            url: '<?php echo site_url("CommunityNgo/new_comBeneficiary_type"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                var beneficiary_type = $('#benificiaryType');
                if (data[0] == 's') {
                    beneficiary_type.append('<option value="' + data[2] + '">' + type + '</option>');
                    beneficiary_type.val(data[2]);
                    $('#beneficiary-type-modal').modal('hide');
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }


    $('#add-province').click(function () {
        $('#country_description').val('');
        $('#beneficiary-manage-country-modal').modal({backdrop: 'static'});
    });

    function save_province() {
        var countryID = $.trim($('#countryID').val());
        var description = $.trim($('#country_description').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'description': description, countryID: countryID},
            url: '<?php echo site_url("CommunityNgo/new_comBeneficiary_province"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                var province = $('#province');
                if (data[0] == 's') {

                    $('#beneficiary-manage-country-modal').modal('hide');
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    $('#add-district').click(function () {
        $('#description_district').val('');
        $('#beneficiary-district-modal').modal({backdrop: 'static'});
    });

    function save_district() {
        var countryID = $.trim($('#countryID').val());
        var province = $.trim($('#province').val());
        var description = $.trim($('#description_district').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'description': description, countryID: countryID, province: province},
            url: '<?php echo site_url("CommunityNgo/new_comBeneficiary_district"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {

                    $('#beneficiary-district-modal').modal('hide');
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    $('#add-division').click(function () {
        $('#description_division').val('');
        $('#beneficiary-division-modal').modal({backdrop: 'static'});
    });

    function save_division() {
        var countryID = $.trim($('#countryID').val());
        var province = $.trim($('#province').val());
        var district = $.trim($('#district').val());
        var description = $.trim($('#description_division').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'description': description, countryID: countryID, province: province, district: district},
            url: '<?php echo site_url("CommunityNgo/new_comBeneficiary_division"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                var province = $('#province');
                if (data[0] == 's') {

                    $('#beneficiary-division-modal').modal('hide');
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function linkContact() {
        $('#linkcontact_text').removeClass('hide');
        $('#linkcontact_system').addClass('hide');
    }

    function unlinkContact() {
        $("#benificiaryType").val(null).trigger("change");
        $("#benificiaryType").prop("disabled", false);
        $('#contactname').val('');
        $('#edit_beneficiary').val('');
        $('#contactID').val('');
        $('.valueHelp').val('');
        $(".disableHelp").prop("readonly", false);
        $("#secondaryCode").prop("readonly", false);
        $("#emp_title").val(null).trigger("change");
        $("#emp_title").prop("disabled", false);
       // $("#EconStateID").val(null).trigger("change");
       // $("#EconStateID").prop("disabled", false);
        $("#countryID").prop("disabled", false);
        $("#province").prop("disabled", false);
        $("#district").prop("disabled", false);
        $("#division").prop("disabled", false);
        $("#subDivision").prop("disabled", false);
        $("#countryCodePrimary").prop("disabled", false);
        $('#linkcontact_text').addClass('hide');
        $('#linkcontact_system').removeClass('hide');
    }

    function initializeBeneficiaryTypeahead() {
        var projectID = $('#projectID').val();
        $('#contactname').autocomplete({
            serviceUrl: '<?php echo site_url();?>CommunityNgo/fetch_comBeneficiary_search/?&projectID=' + projectID,
            onSelect: function (suggestion) {
                $('#edit_beneficiary').val(suggestion.benificiaryID);
                $('#contactID').val(suggestion.benificiaryID);
                $('#secondaryCode').val(suggestion.secondaryCode);
                $(".valueHelp").prop("readonly", true);
                $('#benificiaryType').val(suggestion.benificiaryType).change();
                $("#benificiaryType").prop("disabled", true);
                $('#registeredDate').val(suggestion.registeredDate);
                $('#dateOfBirth').val(suggestion.dateOfBirth);
                $('#emp_title').val(suggestion.titleID).change();
                $("#emp_title").prop("disabled", true);
               // $('#EconStateID').val(suggestion.EconStateID).change();
               // $("#EconStateID").prop("disabled", true);
                $('#nameWithInitials').val(suggestion.nameWithInitials);
                $('#fullName').val(suggestion.fullName);
                $('#email').val(suggestion.email);
                $('#countryID').val(suggestion.countryID).change();
                $("#countryID").prop("disabled", true);
                $("#countryCodePrimary").prop("disabled", true);
                province = suggestion['province'];
                district = suggestion['district'];
                division = suggestion['division'];
                subDivision = suggestion['subDivision'];
                $('#postalcode').val(suggestion.postalcode);
                //$("#postalcode").prop("readonly", true);
                $('#address').val(suggestion.address);
                $('#countryCodePrimary').val(suggestion.phoneCountryCodePrimary);
                $('#phoneAreaCodePrimary').val(suggestion.phoneAreaCodePrimary);
                $('#phonePrimary').val(suggestion.phonePrimary);
                setTimeout(function(){
                    $("#province").prop("disabled", true);
                    $("#district").prop("disabled", true);
                    $("#division").prop("disabled", true);
                    $("#subDivision").prop("disabled", true);
                }, 1000);
            }
        });
    }


</script>