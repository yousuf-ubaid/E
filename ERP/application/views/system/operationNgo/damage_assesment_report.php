<?php echo head_page($_POST["page_name"], false);
$this->load->helper('operation_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate = current_date(FALSE);
$startdate = date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<style>/*.fixHeader_Div {
        height: 370px;
        border: 1px solid #c0c0c0;
    }*/
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<ul class="nav nav-tabs" xmlns="http://www.w3.org/1999/html">
    <li class="active"><a href="#display" data-toggle="tab"><i class="fa fa-television"></i>
            Display </a></li>
    <li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="display">
        <?php echo form_open('', 'role="form" id="buyback_productionReport_form"'); ?>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">Project</legend>

                    <div class="row">
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-3 control-label text-left"
                                       for="employeeID">Project :</label>

                                <div class="form-group col-md-8">
                                    <?php echo form_dropdown('projectID', fetch_project_donor_drop_damage_assestment(), '',
                                        'class="form-control select2" id="projectID_filter" required'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">Date Range</legend>
                    <div class="row">
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-3 control-label text-left"
                                       for="employeeID">Date From :</label>

                                <div class="form-group col-md-8">
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="datefrom"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $start_date; ?>" id="datefrom" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-3 control-label text-left"
                                       for="employeeID">Date To :</label>

                                <div class="form-group col-md-8">
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="dateto"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">Occupation</legend>
                    <div class="row">
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-4 control-label text-left"
                                       for="employeeID">Occupation :</label>

                                <div class="form-group col-md-8">
                                    <div id="div_load_filter_occupation">
                                        <select name="occupation[]" class="form-control select2" id="filter_occupation"
                                                multiple="">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">Area</legend>
                    <div class="row">
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-4 control-label text-left"
                                       for="employeeID">Country :</label>

                                <div class="form-group col-md-8">
                                    <div id="div_load_filter_country">
                                        <select name="country" class="form-control select2" id="filter_country"
                                                multiple="">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-4 control-label text-left"
                                       for="employeeID">Province :</label>

                                <div class="form-group col-md-8">
                                    <div id="div_load_filter_province">
                                        <select name="province" class="form-control select2" id="filter_province"
                                                multiple="">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-4 control-label text-left"
                                       for="employeeID">Area / District :</label>

                                <div class="form-group col-md-8">
                                    <div id="div_load_filter_district">
                                        <select name="district" class="form-control select2" id="filter_district"
                                                multiple="">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-4 control-label text-left"
                                       for="employeeID">Jamiya Division :</label>

                                <div class="form-group col-md-8">
                                    <div id="div_load_filter_jamiya">
                                        <select name="da_jammiyahDivision" class="form-control select2"
                                                id="filter_jamiya"
                                                multiple="">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-4 control-label text-left"
                                       for="employeeID">Division :</label>

                                <div class="form-group col-md-8">
                                    <div id="div_load_filter_division">
                                        <select name="division" class="form-control select2" id="filter_division"
                                                multiple="">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-4 control-label text-left"
                                       for="employeeID">Mahalla :</label>

                                <div class="form-group col-md-8">
                                    <div id="div_load_filter_mahalla">
                                        <select name="subDivision" class="form-control select2" id="filter_subDivision"
                                                multiple="">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" style="">
                            <div class="form-group col-sm-12" style="margin-bottom: 0px">
                                <label class="col-md-4 control-label text-left"
                                       for="employeeID">GN Division :</label>

                                <div class="form-group col-md-8">
                                    <div id="div_load_filter_gnDivision">
                                        <select name="da_GnDivision" class="form-control select2"
                                                id="filter_da_GnDivision" multiple="">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="margin-top: 10px">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="generateReport('buyback_productionReport_form')" name="filtersubmit"
                        id="filtersubmit"><i
                            class="fa fa-plus"></i> Generate
                </button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<!--modal report-->
<div class="modal fade" id="finance_report_modal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 80%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Disaster Assesment Report</h4>
            </div>
            <div class="modal-body">
                <div id="reportContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--modal report-->
<div class="modal fade" id="finance_report_drilldown_modal" tabindex="2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 95%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Drill Down - <span class="myModalLabel"></span></h4>
            </div>
            <div class="modal-body">
                <div id="reportContentDrilldown"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var type;
    var url;
    var url2;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/damage_assesment_report', '', 'Damage Assesment');
        });

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });


        $('#filter_province').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_province").multiselect2('selectAll', false);
        $("#filter_province").multiselect2('updateButtonText');

        $('#filter_district').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_district").multiselect2('selectAll', false);
        $("#filter_district").multiselect2('updateButtonText');

        $('#filter_occupation').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_occupation").multiselect2('selectAll', false);
        $("#filter_occupation").multiselect2('updateButtonText');


        $('#filter_district').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_district").multiselect2('selectAll', false);
        $("#filter_district").multiselect2('updateButtonText');

        $('#filter_jamiya').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_jamiya").multiselect2('selectAll', false);
        $("#filter_jamiya").multiselect2('updateButtonText');

        $("#filter_district").multiselect2('selectAll', false);
        $("#filter_district").multiselect2('updateButtonText');

        $('#filter_division').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_division").multiselect2('selectAll', false);
        $("#filter_division").multiselect2('updateButtonText');

        $('#filter_subDivision').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_subDivision").multiselect2('selectAll', false);
        $("#filter_subDivision").multiselect2('updateButtonText');


        $('#filter_country').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_country").multiselect2('selectAll', false);
        $("#filter_country").multiselect2('updateButtonText');


        $('#filter_da_GnDivision').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#filter_da_GnDivision").multiselect2('selectAll', false);
        $("#filter_da_GnDivision").multiselect2('updateButtonText');

    });

    $("#projectID_filter").change(function () {
        if ((this.value)) {
            load_projectBase_country(this.value);
            load_projectBase_occupation(this.value);

            return false;
        }

    });

    /*call report content*/
    function generateReport(formName) {
        var reportID = $("#reportID").val();
        var fieldNameChk = [];
        var captionChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push({name: "fieldNameChk[]", value: $(this).val()});
            captionChk.push({name: "captionChk[]", value: $(this).data('caption')});
        });
        var serializeArray = $("#" + formName).serializeArray();
        var finalArray = $.merge(serializeArray, fieldNameChk, captionChk);
        var finalArray2 = $.merge(finalArray, captionChk);
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'json',
            data: finalArray2,
            url: '<?php echo site_url('OperationNgo/damage_assesment_report'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                } else {
                    $("#reportContent").html(data['view']);
                    if (data['isPieChartRequired'] == 1) {
                        generPichart(data['piData']);
                    } else {

                    }
                    $('#finance_report_modal').modal("show");
                }

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function generPichart(dataP) {
        var pieColors = (function () {
            var colors = [],
                house = '#93CBEC',
                humaninjury = '#83D88E',
                houseitems = '#FABEB1',
                businessproperty = '#4885ed',
                i;

            colors.push(Highcharts.Color(house).get());
            colors.push(Highcharts.Color(humaninjury).get());
            colors.push(Highcharts.Color(houseitems).get());
            colors.push(Highcharts.Color(businessproperty).get());
            return colors;
        }());
        Highcharts.chart('disasterasstmentrpt', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
            },
            title: {
                text: 'Disaster Assessment Summary'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    colors: pieColors,
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
                        distance: -50,
                        filter: {
                            property: 'percentage',
                            operator: '>',
                            value: 4
                        }
                    }

                }
            },
            series: [{
                name: 'Share',
                data: dataP
            }]
        });
    }

    /*call report content pdf*/


    function load_projectBase_country(projectID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {projectID: projectID},
            url: "<?php echo site_url('OperationNgo/fetch_da_report_project_based_countryDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_filter_country').html(data);
                $('#filter_country').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#filter_country").multiselect2('selectAll', false);
                $("#filter_country").multiselect2('updateButtonText');
                load_projectBase_province();
                //$('#province').val(province).change();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_projectBase_occupation(projectID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {projectID: projectID},
            url: "<?php echo site_url('OperationNgo/fetch_da_report_project_based_occupationdropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_filter_occupation').html(data);
                $('#filter_occupation').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#filter_occupation").multiselect2('selectAll', false);
                $("#filter_occupation").multiselect2('updateButtonText');
                //$('#province').val(province).change();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_projectBase_province(countyID) {
        $('#div_load_filter_province').html('');
        $('#div_load_filter_district').html('');
        $('#div_load_filter_jamiya').html('');
        $('#div_load_filter_division').html('');
        $('#div_load_filter_mahalla').html('');
        $('#div_load_filter_gnDivision').html('');
        var projectID = $('#projectID_filter').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {countyID: countyID, projectID: projectID},
            url: "<?php echo site_url('OperationNgo/fetch_da_report_project_based_provinceDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_filter_province').html(data);
                $('#filter_province').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#filter_province").multiselect2('selectAll', false);
                $("#filter_province").multiselect2('updateButtonText');
                //$('#province').val(province).change();
                load_projectBase_district();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_projectBase_district() {
        $('#div_load_filter_district').html('');
        var projectID = $('#projectID_filter').val();
        var province = $('#filter_province').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {province: province, projectID: projectID},
            url: "<?php echo site_url('OperationNgo/fetch_da_report_project_based_districtDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_filter_district').html(data);
                $('#filter_district').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#filter_district").multiselect2('selectAll', false);
                $("#filter_district").multiselect2('updateButtonText');
                //$('#province').val(province).change();
                load_projectBase_division();
                load_projectBase_jamiya();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_projectBase_jamiya() {
        $('#div_load_filter_jamiya').html('');
        var projectID = $('#projectID_filter').val();
        var district = $('#filter_district').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {district: district, projectID: projectID},
            url: "<?php echo site_url('OperationNgo/fetch_da_report_project_based_jamiyaDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_filter_jamiya').html(data);
                $('#filter_da_jammiyahDivision').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#filter_da_jammiyahDivision").multiselect2('selectAll', false);
                $("#filter_da_jammiyahDivision").multiselect2('updateButtonText');
                //$('#province').val(province).change();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_projectBase_division() {
        $('#div_load_filter_division').html('');
        var projectID = $('#projectID_filter').val();
        var district = $('#filter_district').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {district: district, projectID: projectID},
            url: "<?php echo site_url('OperationNgo/fetch_da_report_project_based_divisionDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_filter_division').html(data);
                $('#filter_division').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#filter_division").multiselect2('selectAll', false);
                $("#filter_division").multiselect2('updateButtonText');
                //$('#province').val(province).change();
                load_projectBase_mahalla();
                load_projectBase_gnDivision();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_projectBase_mahalla() {
        $('#div_load_filter_mahalla').html('');
        var projectID = $('#projectID_filter').val();
        var division = $('#filter_division').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {division: division, projectID: projectID},
            url: "<?php echo site_url('OperationNgo/fetch_da_report_project_based_mahallaDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_filter_mahalla').html(data);
                $('#filter_subDivision').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#filter_subDivision").multiselect2('selectAll', false);
                $("#filter_subDivision").multiselect2('updateButtonText');
                //$('#province').val(province).change();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_projectBase_gnDivision() {
        $('#div_load_filter_gnDivision').html('');
        var projectID = $('#projectID_filter').val();
        var division = $('#filter_division').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {division: division, projectID: projectID},
            url: "<?php echo site_url('OperationNgo/fetch_da_report_project_based_GnDivisionDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_filter_gnDivision').html(data);
                $('#filter_da_GnDivision').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#filter_da_GnDivision").multiselect2('selectAll', false);
                $("#filter_da_GnDivision").multiselect2('updateButtonText');
                //$('#province').val(province).change();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


</script>