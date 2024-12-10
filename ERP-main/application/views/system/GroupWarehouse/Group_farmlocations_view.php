<?php echo head_page('Buyback Area Setup', false);

$policydescription = getPolicydescription_masterid(7);
$policyvalue = getgrouppolicyvalues($policydescription['grouppolicymasterID']);
$policyvalue_detail = getPolicydescription_values_detail($policydescription['grouppolicymasterID']);
?>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary "
                onclick="create_buybackArea()"><i
                class="fa fa-plus"></i> Create Main Area
        </button>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-4">
        <label for=""><?php echo $policydescription['groupPolicyDescription'] ?></label>
    </div>
    <div class="col-md-1">
        <?php echo form_dropdown('isallow',$policyvalue, $policyvalue_detail['value'], 'class="form-control" id="isallow" onchange="updatepolicy_area(this.value)" '); ?>
    </div>
</div>
<hr>
<div id="Buyback_area"></div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade bs-example-modal-lg" id="add_group_area_modal" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">
                    Create New Area</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="frm_CreateNewCage">
                    <input type="text" name="groupLocationID" id="groupLocationID" class="form-control hidden">
                    <div class="form-group">
                        <label for="fuelType" class="col-sm-3 control-label"> Area Description </label>
                        <div class="col-sm-7">
                            <input type="text" name="mainArea" id="mainArea" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="Create_new_area_group()">
                    <?php echo $this->lang->line('common_save'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="add_group_subarea_modal" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">
                    Create New Sub Area</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="">
                    <input type="text" name="sub_groupLocationID" id="sub_groupLocationID" class="form-control hidden">
                    <input type="text" name="masterID" id="masterID" class="form-control hidden">
                    <div class="form-group">
                        <label for="fuelType" class="col-sm-3 control-label"> Sub Area : </label>
                        <div class="col-sm-7">
                            <input type="text" name="subArea" id="subArea" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="Create_new_subarea_group()">
                    <?php echo $this->lang->line('common_save'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="AreaLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="Area_link_form"'); ?>
            <input type="hidden" name="LinkArea_groupLocationID" id="LinkArea_groupLocationID">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Area Link <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <label for=""><h4>Area :- </h4></label>
                        <label style="color: cornflowerblue;font-size: 1.2em;" id="AreaDescription"></label>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadCompanyArea">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave">Add Link
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="SubAreaLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="subArea_link_form"'); ?>
            <input type="hidden" name="LinkSubArea_groupLocationID" id="LinkSubArea_groupLocationID">
            <input type="hidden" name="masterID" id="masterID">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Sub Area Link <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <label for=""><h4>Sub Area :- </h4></label>
                        <label style="color: cornflowerblue;font-size: 1.2em;" id="SubAreaDescription"></label>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadCompanySubArea">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSubAreaSave">Add Link
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="areaDuplicateModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="area_duplicate_form"'); ?>
            <input type="hidden" name="groupLocationIDDuplicatehn" id="groupLocationIDDuplicatehn">
            <input type="hidden" name="masterIDDuplicatehn" id="masterIDDuplicatehn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Area Replicate <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadComapnyAreaDuplicate">

                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSavedareaDup">Replicate
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="invalidinvoicemodal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Area or Sub Area not linked</h4>
            </div>
            <div class="modal-body">
                <div >
                    <table  class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th >Company</th>
                            <th>Message</th>
                        </tr>
                        </thead>
                        <tbody id="errormsg">

                        </tbody>
                    </table>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<script>
    $( document ).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/GroupWarehouse/Group_farmlocations_view', '', 'Buyback Area Setup');
        });

        Group_area_table();

        $('#Area_link_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //companyID: {validators: {notEmpty: {message: 'Company is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Buyback/save_area_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSave').attr('disabled', false);
                        if (data[0] == 's') {
                            /*load_segment_details_table();
                             load_company($('#groupSegmentID').val());
                             $('#companyID').val('').change();*/
                            load_all_companies_area();
                            $('#AreaLinkModal').modal('hide');
                        }

                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });

        $('#subArea_link_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //companyID: {validators: {notEmpty: {message: 'Company is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Buyback/save_subarea_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSave').attr('disabled', false);
                        if (data[0] == 's') {
                            /*load_segment_details_table();
                             load_company($('#groupSegmentID').val());
                             $('#companyID').val('').change();*/
                            load_all_companies_sub_area();
                            $('#SubAreaLinkModal').modal('hide');
                        }

                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });

        $('#area_duplicate_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //companyID: {validators: {notEmpty: {message: 'Company is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Buyback/save_area_duplicate'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSavedup').attr('disabled',false);
                        if (data[0] == 's') {
                            load_all_companies_duplicate();
                            $('#areaDuplicateModal').modal('hide');
                        }
                        if (jQuery.isEmptyObject(data[2])) {
                            $('#areaDuplicateModal').modal('hide');
                        } else {
                            $('#errormsg').empty();
                            $.each(data[2], function (key, value) {
                                $('#errormsg').append('<tr><td>' + value['companyname'] + '</td><td>' + value['message'] + '</td></tr>');
                            });
                            $('#invalidinvoicemodal').modal('show');
                            $('#areaDuplicateModal').modal('hide');
                        }


                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });

    });

    function Group_area_table() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
           // data: data,
            url: "<?php echo site_url('Buyback/Group_area_table_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#Buyback_area').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function create_buybackArea() {
        $('#mainArea').val('');
        $('#groupLocationID').val('');
        $('#add_group_area_modal').modal('show');
    }

    function edit_group_main_area(groupLocationID, mainArea){
        $('#groupLocationID').val(groupLocationID);
        $('#mainArea').val(mainArea);
        $('#add_group_area_modal').modal('show');
    }

    function Create_new_area_group(){
        var groupLocationID = $('#groupLocationID').val();
        var area = $('#mainArea').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'area' : area, 'groupLocationID': groupLocationID},
            url: "<?php echo site_url('Buyback/Create_new_area_group'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#add_group_area_modal').modal('hide');
                    Group_area_table();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function create_group_sub_area(groupLocationID){
        $('#sub_groupLocationID').val('');
        $('#masterID').val('');
        $('#masterID').val(groupLocationID);
        $('#subArea').val('');
        $('#add_group_subarea_modal').modal('show');
    }

    function edit_group_sub_area(groupLocationID, masterID, subArea){
        $('#sub_groupLocationID').val(groupLocationID);
        $('#masterID').val(masterID);
        $('#subArea').val(subArea);
        $('#add_group_subarea_modal').modal('show');
    }

    function Create_new_subarea_group(){
        var groupLocationID = $('#sub_groupLocationID').val();
        var masterID = $('#masterID').val();
        var subArea = $('#subArea').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'subArea' : subArea, 'masterID' : masterID, 'groupLocationID': groupLocationID},
            url: "<?php echo site_url('Buyback/Create_new_subarea_group'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#add_group_subarea_modal').modal('hide');
                    Group_area_table();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function link_group_area(groupLocationID) {
        $('#AreaLinkModal').modal({backdrop: "static"});
        $('#companyID').val('').change();
        $('#LinkArea_groupLocationID').val(groupLocationID);
        $('#btnSave').attr('disabled', false);
        load_all_companies_area();
        load_area_header(groupLocationID);
    }

    function load_all_companies_area() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupLocationID: $('#LinkArea_groupLocationID').val()},
            url: "<?php echo site_url('Buyback/load_all_companies_area'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadCompanyArea').removeClass('hidden');
                $('#loadCompanyArea').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }

    function clearArea(id) {
        $('#locationID_' + id).val('').change();
    }

    function clearSubArea(id) {
        $('#subLocationID_' + id).val('').change();
    }

    function load_area_header(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'groupLocationID': id},
            url: "<?php echo site_url('Buyback/load_group_area_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#AreaDescription').html(data['description']);
                    $('#SubAreaDescription').html(data['description']);
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function link_group_sub_area(groupLocationID, masterID) {
        $('#SubAreaLinkModal').modal({backdrop: "static"});
        $('#companyID').val('').change();
        $('#LinkSubArea_groupLocationID').val(groupLocationID);
        $('#masterID').val(masterID);
        $('#btnSubAreaSave').attr('disabled', false);
        load_all_companies_sub_area();
        load_area_header(groupLocationID);
    }

    function load_all_companies_sub_area() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'masterID' : $('#masterID').val(), 'groupLocationID' : $('#LinkSubArea_groupLocationID').val()},
            url: "<?php echo site_url('Buyback/load_all_companies_subarea'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadCompanySubArea').removeClass('hidden');
                $('#loadCompanySubArea').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }

    function load_duplicate_area(groupLocationID, masterID = null){
        $('#areaDuplicateModal').modal({backdrop: "static"});
        $('#groupLocationIDDuplicatehn').val(groupLocationID);
        $('#masterIDDuplicatehn').val(masterID);
        $('#btnSavedareaDup').attr('disabled', false);
        load_all_companies_duplicate();
    }
    function load_all_companies_duplicate(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupLocationID: $('#groupLocationIDDuplicatehn').val()},
            url: "<?php echo site_url('Buyback/load_all_area_duplicate'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnyAreaDuplicate').removeClass('hidden');
                $('#loadComapnyAreaDuplicate').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }

    function updatepolicy_area(value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {policyValue: value,groupPolicymasterID:7},
            url: "<?php echo site_url('Buyback/update_grop_policy_area'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if (data[0] == 's') {

                }
            }, error: function () {

            }
        });
    }
</script>