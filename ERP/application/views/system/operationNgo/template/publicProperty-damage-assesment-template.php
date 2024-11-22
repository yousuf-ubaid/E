<?php
$this->load->helper('operation_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$all_states_arr = all_states();
$countries_arr = load_all_countrys();
$countryCode_arr = all_country_codes();
$currency_arr = all_currency_new_drop();
$propertyTypes = fetch_propertyDmg_types();
?>
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>PROPERTY NAME & DETAILS</h2>
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
                  <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-1 search_cancel hide" style="width: 3%;" id="contact_text">
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
                    <label class="title">Property Type</label>
                </div>
                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('propertyType', $propertyTypes, '',
                        'class="form-control select2 valueHelp disableHelp" onchange="load_subProperty(this.value)" id="propertyType" '); ?>
                    <span class="input-req-inner"></span></span>
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Sub Property</label>
                </div>
                <div class="form-group col-sm-4">
                <div id="div_load_subPropertyId">
                    <select name="subPropertyId" class="form-control" id="subPropertyId">
                        <option value="" selected="selected">Select a Sub Property</option>
                    </select>
                </div>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Property Name</label>
                </div>
                <div class="form-group col-sm-4">
                  <span class="input-req" title="Required Field">
            <input type="text" name="PropertyName" id="PropertyName" class="form-control valueHelp disableHelp"
                   placeholder="Property Name"
                   required>
                      <span class="input-req-inner"></span></span>
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Property Short Code</label>
                </div>
                <div class="form-group col-sm-4">
                  <span class="input-req" title="Required Field">
            <input type="text" name="PropertyShortCode" id="PropertyShortCode" class="form-control valueHelp disableHelp"
                   placeholder="Property Short Code" required>
            <span class="input-req-inner"></span></span>
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Commencement Date</label>
                </div>
                <div class="form-group col-sm-4">
                  <span class="input-req" title="Required Field">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="commencementDate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="commencementDate"
                                   class="form-control valueHelp disableHelp">
                        </div>
                            <span class="input-req-inner" style="z-index: 100;"></span></span>
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Email</label>
                </div>
                <div class="form-group col-sm-4">
                    <input type="text" name="email" id="email" class="form-control valueHelp disableHelp"
                           placeholder="Email">
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Is Own Land</label>
                </div>
                <div class="form-group col-sm-4">
                    <div class="skin-section extraColumns">
                        <label class="radio-inline">
                            <div class="skin-section extraColumnsgreen">
                                <label for="checkbox">Yes&nbsp;&nbsp;</label>
                                <input id="ownLandAvailableYes" type="radio" data-caption="" class="columnSelected"
                                       name="ownLandAvailable" value="1">
                            </div>
                        </label>
                        <label class="radio-inline">
                            <div class="skin-section extraColumnsgreen">
                                <label for="checkbox">No&nbsp;&nbsp;</label>
                                <input id="ownLandAvailableNo" type="radio" data-caption="" class="columnSelected"
                                       name="ownLandAvailable" value="2">
                            </div>
                        </label>
                    </div>
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Comments</label>
                </div>
                <div class="form-group col-sm-4">
                <textarea class="form-control" id="ownLandAvailableComments" name="ownLandAvailableComments"
                          rows="2"></textarea>
                </div>
            </div>
            <div class="row" style="margin-top: 15px;">
                <div class="form-group col-sm-2">
                    <label class="title">Total Sq Ft</label>
                </div>
                <div class="form-group col-sm-4">
                    <input type="text" name="totalSqFt" id="totalSqFt" class="form-control" placeholder="Total Sq Ft"
                           value="460 x Rs : 1,410.00">
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Total Cost</label>
                </div>
                <div class="form-group col-sm-4" style="width: 32%">
                    <input type="text" name="totalCost" id="totalCost" class="form-control" placeholder="Total Cost"
                           value="648,600.00 + 50,000.00 = 698,600 /=">
                </div>
            </div>
            <div class="row" style="margin-top: 15px;">
                <div class="form-group col-sm-2">
                    <label class="title">Phone (Primary)</label>
                </div>
                <div class="form-group col-sm-1" style="width: 12%">
                                     <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('countryCodePrimary', $countryCode_arr, '', 'class="form-control valueHelp disableHelp" id="countryCodePrimary"'); ?>
                                         <span class="input-req-inner"></span></span>
                </div>
                <div class="form-group col-sm-1" style="padding-left: 0px;">
                    <input type="text" name="phoneAreaCodePrimary" id="phoneAreaCodePrimary"
                           class="form-control valueHelp disableHelp"  onkeypress="return validateFloatKeyPress(this,event)"
                           placeholder="Area Code">
                </div>
                <div class="form-group col-sm-2" style="padding-left: 0px;">
                  <span class="input-req" title="Required Field">
            <input type="text" name="phonePrimary" id="phonePrimary" class="form-control valueHelp disableHelp"
                   placeholder="Phone Number"  onkeypress="return validateFloatKeyPress(this,event)">
                      <span class="input-req-inner"></span></span>
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Registered Date</label>
                </div>
                <div class="form-group col-sm-3">
                          <span class="input-req" title="Required Field">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="registeredDate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="registeredDate"
                                   class="form-control valueHelp disableHelp">
                        </div>
                            <span class="input-req-inner" style="z-index: 100;"></span></span>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Phone (Secondary)</label>
                </div>
                <div class="form-group col-sm-1" style="width: 12%">
                    <?php echo form_dropdown('countryCodeSecondary', $countryCode_arr, '', 'class="form-control" id="countryCodeSecondary"'); ?>
                </div>
                <div class="form-group col-sm-1" style="padding-left: 0px;">
                    <input type="text" name="phoneAreaCodeSecondary" id="phoneAreaCodeSecondary" class="form-control"
                           placeholder="Area Code"  onkeypress="return validateFloatKeyPress(this,event)">
                </div>
                <div class="form-group col-sm-2" style="padding-left: 0px;">
                    <input type="text" name="phoneSecondary" id="phoneSecondary" class="form-control"
                           placeholder="Phone Number"  onkeypress="return validateFloatKeyPress(this,event)">
                </div>
                <div class="form-group col-sm-2">
                </div>
                <div class="form-group col-sm-3">


                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Recommended By</label>
                </div>
                <div class="form-group col-sm-4">
                    <input type="text" name="recommendedBy" id="recommendedBy" class="form-control valueHelp disableHelp"
                           placeholder="Recommend By">
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Recommended Date</label>
                </div>
                <div class="form-group col-sm-4">
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="recommendedDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="recommendedDate"
                               class="form-control valueHelp disableHelp">
                    </div>

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
                <?php echo form_dropdown('countryID', $countries_arr, '', 'class="form-control select2 valueHelp disableHelp" onchange="loadcountry_Province(this.value)" id="countryID"'); ?>
                    <span class="input-req-inner"></span></span>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Province / State</label>
                </div>
                <div class="form-group col-sm-4">
                    <?php /*echo form_dropdown('state', $all_states_arr, '', 'class="form-control select2" id="state"'); */ ?>
                    <!--                <div class="input-group">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button" id="add-province"
                                                            style="height: 29px; padding: 2px 10px;">
                                                        <i class="fa fa-plus" style="font-size: 11px"></i>
                                                    </button>
                                                </span>
                                    </div>-->
                    <span class="input-req" title="Required Field">
                <div id="div_load_province">
                    <select name="province" class="form-control" id="province">
                        <option value="" selected="selected">Select a Province</option>
                    </select>
                </div>
                    <span class="input-req-inner"></span></span>


                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Area / District</label>
                </div>
                <div class="form-group col-sm-4">
                    <!--                <div class="input-group">
                    <!--                            <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button" id="add-district"
                                                            style="height: 29px; padding: 2px 10px;">
                                                        <i class="fa fa-plus" style="font-size: 11px"></i>
                                                    </button>
                                                </span>
                </div>
                -->
                    <span class="input-req" title="Required Field">
                <div id="div_load_district">
                    <select name="district" class="form-control" id="district">
                        <option value="" selected="selected">Select a District</option>
                    </select>
                </div>
                    <span class="input-req-inner"></span></span>

                </div>
            </div>
            <?php
            $districtPolicy = fetch_ngo_policies('JD');
            if (!empty($districtPolicy)) { ?>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Jamiya Division</label>
                    </div>
                    <div class="form-group col-sm-4">
                 <span class="input-req" title="Required Field">
                <div id="div_load_jamiya_division">
                    <select name="division" class="form-control" id="jamiya_division">
                        <option value="" selected="selected">Select a Division</option>
                    </select>
                </div>
                     <span class="input-req-inner"></span></span>
                    </div>
                </div>
            <?php }
            ?>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Division</label>
                </div>
                <div class="form-group col-sm-4">
                    <!--                <div class="input-group">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button" id="add-division"
                                                            style="height: 29px; padding: 2px 10px;">
                                                        <i class="fa fa-plus" style="font-size: 11px"></i>
                                                    </button>
                                                </span>
                                    </div>-->
                    <span class="input-req" title="Required Field">
                <div id="div_load_division">
                    <select name="division" class="form-control" id="division">
                        <option value="" selected="selected">Select a Division</option>
                    </select>
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
                    <select name="subDivision" class="form-control" id="subDivision">
                        <option value="" selected="selected">Select a Mahalla</option>
                    </select>
                </div>

                     <span class="input-req-inner"></span></span></span>

                </div>
            </div>
            <?php
            $districtPolicy = fetch_ngo_policies('GN');
            if (!empty($districtPolicy)) { ?>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">GN Division</label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div id="div_load_gs_sub_division">
                    <select name="subDivision" class="form-control" id="subDivision">
                        <option value="" selected="selected">Select a GN Division</option>
                    </select>
                </div>
                     <span class="input-req-inner"></span>  </span>

                    </div>
                </div>
            <?php }
            ?>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Postal Code</label>
                </div>
                <div class="form-group col-sm-4">
                    <input type="text" name="postalcode" id="postalcode" class="form-control valueHelp disableHelp"
                           placeholder="Postal Code">
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-2">
                    <label class="title">Address</label>
                </div>
                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field"><textarea class="form-control valueHelp disableHelp"
                                                                         id="address"
                                                                         name="address" rows="2"></textarea><span
                        class="input-req-inner"></span></span>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>HELP AID</h2>
                    </header>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="monthly_expenditure_body"></div>
                        </div>
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>REASON IN BRIEF</h2>
                    </header>
                    <div class="row">
                        <div class="form-group col-sm-10" style="margin-top: 5px;">
                        <textarea class="form-control" rows="5" name="reasoninBrief"
                                  id="familyDescription"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
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

            $('.extraColumnsgreen input').iCheck({
                checkboxClass: 'icheckbox_square_relative-green',
                radioClass: 'iradio_square_relative-green',
                increaseArea: '20%'
            });

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

            fetch_monthly_expenditure_header();

        });

        function load_subProperty(propertyType) {
            $('#div_load_subPropertyId').html('');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {propertyType: propertyType},
                url: "<?php echo site_url('OperationNgo/fetch_property_based_subDropdown'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_subPropertyId').html(data);
                    $('.select2').select2();
                    $('#subPropertyId').val(subPropertyId).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadcountry_Province(countyID) {
            $('#div_load_division').html('');
            $('#div_load_sub_division').html('');
            $('#div_load_province').html('');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {countyID: countyID},
                url: "<?php echo site_url('OperationNgo/fetch_province_based_countryDropdown'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_province').html(data);
                    $('.select2').select2();
                    $('#province').val(province).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadcountry_District(masterID) {
            $('#div_load_district').html('');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('OperationNgo/fetch_province_based_districtDropdown'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_district').html(data);
                    $('.select2').select2();
                    $('#district').val(district).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadcountry_Division(masterID) {
            $('#div_load_division').html('');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('OperationNgo/fetch_division_based_districtDropdown'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_division').html(data);
                    $('.select2').select2();
                    $('#division').val(division).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadcountry_jamiya_Division(masterID) {
            $('#div_load_jamiya_division').html('');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('OperationNgo/fetch_jamiya_division_based_districtDropdown'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_jamiya_division').html(data);
                    $('.select2').select2();
                    $('#da_jammiyahDivision').val(jamiyaDivision).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadcountry_gs_sub_Division(masterID) {
            $('#div_load_gs_sub_division').html('');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('OperationNgo/fetch_jamiya_sub_division_based_divisionDropdown'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_gs_sub_division').html(data);
                    $('.select2').select2();
                    $('#da_GnDivision').val(gnDivision).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadcountry_sub_Division(masterID) {
            $('#div_load_sub_division').html('');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('OperationNgo/fetch_sub_division_based_divisionDropdown'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_sub_division').html(data);
                    $('.select2').select2();
                    $('#subDivision').val(subDivision).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
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
                url: '<?php echo site_url("OperationNgo/new_beneficiary_province"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    var province = $('#province');
                    if (data[0] == 's') {
                        loadcountry_Province(countryID);
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
                url: '<?php echo site_url("OperationNgo/new_beneficiary_district"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        loadcountry_District(province);
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
                url: '<?php echo site_url("OperationNgo/new_beneficiary_division"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    var province = $('#province');
                    if (data[0] == 's') {
                        loadcountry_Division(district);
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
            $("#propertyType").val(null).trigger("change");
            $("#propertyType").prop("disabled", false);
            $('#contactname').val('');
            $('#edit_beneficiary').val('');
            $('#contactID').val('');
            $('.valueHelp').val('');
            $(".disableHelp").prop("readonly", false);
            $("#secondaryCode").prop("readonly", false);
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
                serviceUrl: '<?php echo site_url();?>OperationNgo/fetch_property_relate_search/?&projectID=' + projectID,
                onSelect: function (suggestion) {
                    $('#edit_beneficiary').val(suggestion.publicPropertyBeneID);
                    $('#contactID').val(suggestion.publicPropertyBeneID);
                    $('#secondaryCode').val(suggestion.secondaryCode);
                    $(".valueHelp").prop("readonly", true);
                    $('#propertyType').val(suggestion.propertyType).change();
                    $("#propertyType").prop("disabled", true);
                    $('#subPropertyId').val(suggestion.subPropertyId).change();
                    $("#subPropertyId").prop("disabled", true);
                    $('#registeredDate').val(suggestion.registeredDate);
                    $('#commencementDate').val(suggestion.commencementDate);
                    $('#PropertyShortCode').val(suggestion.PropertyShortCode);
                    $('#PropertyName').val(suggestion.PropertyName);
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
                    setTimeout(function () {
                        $("#province").prop("disabled", true);
                        $("#district").prop("disabled", true);
                        $("#division").prop("disabled", true);
                        $("#subDivision").prop("disabled", true);
                    }, 1000);
                }
            });
        }


    </script>
<?php
