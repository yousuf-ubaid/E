<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


$this->load->helper('community_ngo_helper');
$countries_arr = load_all_countries();
$default_data = load_default_data();

if ($default_data['DD_Des']) {
    $title = $this->lang->line('communityngo_area_setup') . ' - ' . $default_data['DD_Des'];
} else {
    $title = $this->lang->line('communityngo_area_setup');
}

echo head_page($title, false);


?>
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/tree.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">


<div class="treeContainer" style="min-height: 200px;">
    <!--via ajax -->
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

                            <div class="row">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Division Type</label>

                                    <div class="col-sm-6">
                                        <?php echo form_dropdown('divisionTypeCode', array('' => 'Select', 'JD' => 'Jammiyah Division', 'DD' => 'District Division'), '', 'class="form-control select2" id="district_divisionTypeCode"'); ?>
                                    </div>
                                </div>
                            </div>

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


                            <div class="row">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Division Type</label>

                                    <div class="col-sm-6">
                                        <?php echo form_dropdown('divisionTypeCode', array('' => 'Select', 'GN' => 'GN Divison', 'MH' => 'Mahalla'), '', 'class="form-control select2" id="division_divisionTypeCode" onchange="get_divisionNo_Div()"'); ?>
                                    </div>
                                </div>

                                <div class="form-group hide" id="divisionNo_Div" style="margin-top: 6%">
                                    <label class="col-sm-4 control-label">Division No</label>

                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="divisionNo"
                                               name="divisionNo">
                                    </div>
                                </div>
                            </div>


                        <div class="row">
                            <div class="form-group" style="margin-top: 1%">
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
            fetchPage('system/communityNgo/ngo_hi_regionMaster', '', 'Area Setup');
        });

        $('.select2').select2();

        loadAreaNavigation();

    });

    function loadAreaNavigation() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('CommunityNgo/load_ngo_area_setup'); ?>",
            data: {},
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

    function get_divisionNo_Div() {

        var dropdownvalue = $("select#division_divisionTypeCode option").filter(":selected").val();

        switch (dropdownvalue) {
            case 'GN':
                $('#divisionNo_Div').removeClass('hide');
                break;

            default:
                $('#divisionNo_Div').addClass('hide');
        }
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
        $("#sub_division_parentCategory").html(description);
        $("#hd_sub_division_countryID").val(countyID);
        $("#hd_sub_division_districtID").val(division);
        $('#sub_division_modal_title').html('Add Sub Division');
        $('#hd_division_stateID').val('');
        $('#division_divisionTypeCode').val('').change();
        $('#beneficiary-sub-division-modal').modal({backdrop: 'static'});
    }

    function save_sub_division() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo site_url("CommunityNgo/new_sub_division"); ?>',
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

    function save_division() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo site_url("CommunityNgo/new_beneficiary_division"); ?>',
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
                    url: "<?php echo site_url('CommunityNgo/load_ngo_area_setupDetail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (!jQuery.isEmptyObject(data)) {
                            if (type == 'division') {
                                $('#hd_division_countryID').val(data['countyID']);
                                $('#hd_division_stateID').val(data['stateID']);
                                $('#hd_division_districtID').val(data['masterID']);
                                $('#division_modal_title').html('Update Division');
                                $('#division_shortCode').val(data['shortCode']);
                                $('#division_description').val(data['Description']);
                                $('#district_divisionTypeCode').val(data['divisionTypeCode']).change();
                                $("#beneficiary-division-modal").modal({backdrop: "static"});
                            } else if (type == 'subdivision') {
                                $('#hd_sub_division_countryID').val(data['countyID']);
                                $('#hd_sub_division_stateID').val(data['stateID']);
                                $('#hd_sub_division_districtID').val(data['masterID']);
                                $('#sub_division_modal_title').html('Update Sub Division');
                                $('#sub_division_shortCode').val(data['shortCode']);
                                $('#sub_division_description').val(data['Description']);

                                <?php
                                if (!empty($divisionPolicy)) { ?>
                                $('#division_divisionTypeCode').val(data['divisionTypeCode']).change();
                                $('#divisionNo').val(data['divisionNo']);
                                <?php
                                }
                                ?>

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


