<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('operationngo_ngo_area_setup');
echo head_page($title, false);

/*echo head_page('NGO Area Setup', false);*/
$this->load->helper('operation_ngo_helper');
$countries_arr = load_all_countrys();
?>
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/tree.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="form-group col-sm-1">
                <label class="title">Country</label>
            </div>
            <div class="form-group col-sm-3">
                <?php echo form_dropdown('countryID', $countries_arr, '', 'class="form-control select2" onchange="loadAreaNavigation(\'ngoCountryID\')" id="countryID"'); ?>
            </div>
            <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
            </div>
        </div>
    </div>
</div>

<div class="treeContainer" style="min-height: 200px;">
    <!--via ajax -->
</div>

<div class="modal fade" id="beneficiary-manage-country-modal" role="dialog" data-keyboard="false"
     data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="province_modal_title">New Province / State</h4>
            </div>
            <form id="frm_area_province" method="post">
                <input type="hidden" name="hd_province_countryID" id="hd_province_countryID">
                <input type="hidden" name="hd_province_stateID" id="hd_province_stateID">

                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <header class="head-title">
                                <h2 id="province_parentCategory"><!--via JS --></h2>
                            </header>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Short Code</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="province_shortCode"
                                       name="province_shortCode">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 1%">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Description</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="province_description"
                                       name="province_description">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="save_province()">Save</button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="beneficiary-district-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="district_modal_title">New District</h4>
            </div>
            <div class="modal-body">
                <form id="frm_area_district" method="post">
                    <input type="hidden" name="hd_district_countryID" id="hd_district_countryID">
                    <input type="hidden" name="hd_district_provinceID" id="hd_district_provinceID">
                    <input type="hidden" name="hd_district_stateID" id="hd_district_stateID">

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <header class="head-title">
                                    <h2 id="district_parentCategory"><!--via JS --></h2>
                                </header>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Short Code</label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="district_shortCode"
                                           name="district_shortCode">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Description</label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="district_description"
                                           name="district_description">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="save_district()">Save</button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="beneficiary-division-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="division_modal_title">New Division</h4>
            </div>
            <div class="modal-body">
                <form id="frm_area_division" method="post">
                    <input type="hidden" name="hd_division_countryID" id="hd_division_countryID">
                    <input type="hidden" name="hd_division_districtID" id="hd_division_districtID">
                    <input type="hidden" name="hd_division_stateID" id="hd_division_stateID">

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <header class="head-title">
                                    <h2 id="division_parentCategory"><!--via JS --></h2>
                                </header>
                            </div>
                        </div>
                        <?php
                        $districtPolicy = fetch_ngo_policies('JD');
                        if (!empty($districtPolicy)) { ?>
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Division Type</label>

                                    <div class="col-sm-6">
                                        <?php echo form_dropdown('divisionTypeCode', array('' => 'Select', 'JD' => 'Jammiyah Division', 'DD' => 'District Division'), '', 'class="form-control select2" id="district_divisionTypeCode"'); ?>
                                    </div>
                                </div>
                            </div>
                        <?php }
                        ?>
                        <div class="row" style="margin-top: 1%">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Short Code</label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="division_shortCode"
                                           name="division_shortCode">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Description</label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="division_description"
                                           name="division_description">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="save_division()">Save</button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="beneficiary-sub-division-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="sub_division_modal_title">New Sub Division</h4>
            </div>
            <div class="modal-body">
                <form id="frm_area_sub_division" method="post">
                    <input type="hidden" name="hd_sub_division_countryID" id="hd_sub_division_countryID">
                    <input type="hidden" name="hd_sub_division_districtID" id="hd_sub_division_districtID">
                    <input type="hidden" name="hd_sub_division_stateID" id="hd_sub_division_stateID">

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <header class="head-title">
                                    <h2 id="sub_division_parentCategory"><!--via JS --></h2>
                                </header>
                            </div>
                        </div>
                        <?php
                        $divisionPolicy = fetch_ngo_policies('GN');
                        if (!empty($divisionPolicy)) { ?>
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Division Type</label>

                                    <div class="col-sm-6">
                                        <?php echo form_dropdown('divisionTypeCode', array('' => 'Select', 'GN' => 'GN Divison', 'MH' => 'Mahalla'), '', 'class="form-control select2" id="division_divisionTypeCode"'); ?>
                                    </div>
                                </div>
                            </div>
                        <?php }
                        ?>
                        <div class="row" style="margin-top: 1%">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Short Code</label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="sub_division_shortCode"
                                           name="sub_division_shortCode">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Description</label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="sub_division_description"
                                           name="sub_division_description">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="save_sub_division()">Save</button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/ngo_area_setup', '', 'Area Setup');
        });

        $('.select2').select2();

        loadAreaNavigation();

        var ngoCountryID = localStorage.ngoCountryID;

        if (ngoCountryID != undefined) {
            if (ngoCountryID != 'null') {
                $('#countryID').val(localStorage.ngoCountryID).change();
            }
        }

    });

    function loadAreaNavigation(countryID) {
        if (countryID != undefined) {
            window.localStorage.setItem(countryID, $('#countryID').val());
            $('#search_cancel').removeClass('hide');
        } else {
            $('#search_cancel').addClass('hide');
        }
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('OperationNgo/load_ngo_area_setup'); ?>",
            data: {countryID: localStorage.ngoCountryID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $(".treeContainer").html(data);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
    }

    function load_save_province(countyID, description) {
        $('#frm_area_province')[0].reset();
        $("#province_parentCategory").html(description);
        $("#hd_province_countryID").val(countyID);
        $('#province_modal_title').html('Add Province / State');
        //$("#hd_province_provinceID").val(provinceID);
        $('#hd_province_stateID').val('');
        $('#beneficiary-manage-country-modal').modal({backdrop: 'static'});
    }

    function load_save_district(countyID, provinceID, description) {
        $('#frm_area_district')[0].reset();
        $("#district_parentCategory").html(description);
        $("#hd_district_countryID").val(countyID);
        $("#hd_district_provinceID").val(provinceID);
        $('#hd_district_stateID').val('');
        $('#district_modal_title').html('Add District');
        $('#beneficiary-district-modal').modal({backdrop: 'static'});
    }

    function load_save_division(countyID, districtID, description) {
        $('#frm_area_division')[0].reset();
        $("#district_divisionTypeCode").val('').trigger("change");
        $("#division_parentCategory").html(description);
        $("#hd_division_countryID").val(countyID);
        $("#hd_division_districtID").val(districtID);
        $('#division_modal_title').html('Add Division');
        $('#hd_division_stateID').val('');
        $('#beneficiary-division-modal').modal({backdrop: 'static'});
    }

    function load_save_sub_division(countyID, division, description) {
        $('#frm_area_sub_division')[0].reset();
        $("#division_divisionTypeCode").val('').trigger("change");
        $("#sub_division_parentCategory").html(description);
        $("#hd_sub_division_countryID").val(countyID);
        $("#hd_sub_division_districtID").val(division);
        $('#sub_division_modal_title').html('Add Sub Division');
        $('#hd_division_stateID').val('');
        $('#beneficiary-sub-division-modal').modal({backdrop: 'static'});
    }

    function save_province() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo site_url("OperationNgo/new_beneficiary_province"); ?>',
            data: $("#frm_area_province").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    loadAreaNavigation();
                    $('#beneficiary-manage-country-modal').modal('hide');
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function save_district() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo site_url("OperationNgo/new_beneficiary_district"); ?>',
            data: $("#frm_area_district").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    loadAreaNavigation();
                    $('#beneficiary-district-modal').modal('hide');
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function save_division() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo site_url("OperationNgo/new_beneficiary_division"); ?>',
            data: $("#frm_area_division").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    loadAreaNavigation();
                    $('#beneficiary-division-modal').modal('hide');
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function save_sub_division() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo site_url("OperationNgo/new_beneficiary_sub_division"); ?>',
            data: $("#frm_area_sub_division").serialize(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    loadAreaNavigation();
                    $('#beneficiary-sub-division-modal').modal('hide');
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $("#countryID").val(null).trigger("change");
        window.localStorage.removeItem("ngoCountryID");
        loadAreaNavigation();
    }

    function editAreaSetup(stateID, type) {
        swal({
                title: "Are you sure?",
                text: "You want to edit this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Edit"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'stateID': stateID},
                    url: "<?php echo site_url('OperationNgo/load_ngo_area_setupDetail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (!jQuery.isEmptyObject(data)) {
                            if (type == 'province') {
                                $('#hd_province_countryID').val(data['countyID']);
                                $('#hd_province_stateID').val(data['stateID']);
                                $('#province_modal_title').html('Update Province / State');
                                $('#province_shortCode').val(data['shortCode']);
                                $('#province_description').val(data['Description']);
                                $("#beneficiary-manage-country-modal").modal({backdrop: "static"});
                            } else if (type == 'district') {
                                $('#hd_district_countryID').val(data['countyID']);
                                $('#hd_district_stateID').val(data['stateID']);
                                $('#hd_district_provinceID').val(data['masterID']);
                                $('#district_modal_title').html('Update District');
                                $('#district_shortCode').val(data['shortCode']);
                                $('#district_description').val(data['Description']);
                                $("#beneficiary-district-modal").modal({backdrop: "static"});
                            } else if (type == 'division') {
                                $('#hd_division_countryID').val(data['countyID']);
                                $('#hd_division_stateID').val(data['stateID']);
                                $('#hd_division_districtID').val(data['masterID']);
                                $('#division_modal_title').html('Update Division');
                                $('#division_shortCode').val(data['shortCode']);
                                $('#division_description').val(data['Description']);
                                $("#beneficiary-division-modal").modal({backdrop: "static"});
                            } else if (type == 'subdivision') {
                                $('#hd_sub_division_countryID').val(data['countyID']);
                                $('#hd_sub_division_stateID').val(data['stateID']);
                                $('#hd_sub_division_districtID').val(data['masterID']);
                                $('#sub_division_modal_title').html('Update Sub Division');
                                $('#sub_division_shortCode').val(data['shortCode']);
                                $('#sub_division_description').val(data['Description']);
                                $("#beneficiary-sub-division-modal").modal({backdrop: "static"});
                            }
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


</script>


