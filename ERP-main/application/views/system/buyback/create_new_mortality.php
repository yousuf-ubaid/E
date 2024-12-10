<?php echo head_page($_POST['page_name'], false);
$this->load->helper('buyback_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$farms_arr = load_all_farms();
$batch_arr = array('' => 'Select Batch');
$mortality_causes_arr = load_buyBack_mortality_Causes();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Mortality Header</a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="load_confirmation();" data-toggle="tab">Step 2 -
        Mortality Confirmation</a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="mortality_header_form"'); ?>
        <input type="hidden" name="mortalityAutoID" id="mortalityAutoID_edit">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>MORTALITY HEADER</h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Farm</label>
                    </div>
                    <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                <?php echo form_dropdown('farmID', $farms_arr, '', 'class="form-control select2" id="farmID" onchange="fetch_farmBatch(this.value)" required'); ?>
                                <span class="input-req-inner"></span></span>

                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Batch</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                            <div id="div_loadBatch">
                                <?php echo form_dropdown('batchMasterID', $batch_arr, 'Each', 'class="form-control select2" '); ?>
                            </div>
                            <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Document Date</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="documentDate"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="documentDate" class="form-control"
                                       required>
                            </div>
                        <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Narration</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-12">
                        <button class="btn btn-primary pull-right" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <br>

        <div class="row addTableView">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>MORTALITY DETAILS</h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="mortality_new_modal()">
                            <i class="fa fa-plus"></i> Add New
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-10">
                        <div id="Mortality_Detial_birds"></div>
                    </div>
                    <div class="col-sm-2">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>
    <div id="step2" class="tab-pane">
        <div id="confirm_body"></div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev">Previous</button>
            <button class="btn btn-primary " onclick="save_draft()">Save as Draft</button>
            <button class="btn btn-success submitWizard" onclick="confirmation()">Confirm</button>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="mortality_add_new_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">
                    <b>Add New Mortality</b> &nbsp;&nbsp; <input id="farmerBatchName" name="farmerBatchName" size="80" style="border: none" readonly>
                </h5>
            </div>
            <div class="modal-body">
                <form role="form" id="mortality_add_item_form" class="form-horizontal">
                    <input type="hidden" name="mortalityAutoID" id="mortalityAutoID_edit_itemAdd">
                    <input type="hidden" name="currentbirds" id="currentbirds">
                    <div class="row">
                        <div class="col-md-6">
                            <div style="font-size: 16px; font-weight: 700;">Current Birds : <label id="currenct"> </label></div>

                        </div>
                    </div>

                    <table class="table table-bordered table-condensed no-color" id="po_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;">Mortality Cause <?php required_mark(); ?></th>
                            <th style="width: 150px;">No of Birds <?php required_mark(); ?></th>
                            <th style="width: 150px;">Unit Cost<?php required_mark(); ?></th>
                            <th style="width: 150px;">Total Amount<?php required_mark(); ?></th>
                            <th style="width: 200px;">Comment</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo form_dropdown('causeID[]', $mortality_causes_arr, '', 'class="form-control mortalityCausesDropdown"  required'); ?></td>
                            <td><input type="text" name="noOfBirds[]" id="noOfBirds" onfocus="this.select();"
                                       class="form-control number noOfBirds" onkeyup="greaterthantest(this); calculateTotalCost_mortality(this)" required></td>
                            <td><input type="text" name="unitCost[]" id="unitCost" onfocus="this.select();"
                                       class="form-control number unitCost" required disabled></td>
                            <td><input type="text" name="totalCost[]" id="totalCost" onfocus="this.select();"
                                       class="form-control number totalCost" required></td>
                            <td><textarea class="form-control" rows="1" name="comment[]" placeholder="Remarks..."
                                ></textarea></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_mortality_birds()">Save changes
                </button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="mortality_detail_edit_modal" data-backdrop="static"
     data-keyboard="false" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Mortality Birds</h4>
            </div>
            <form role="form" id="mortality_edit_item_form" class="form-horizontal">
                <input type="hidden" name="mortalityAutoID" id="mortalityAutoID_edit_itemEdit">
                <input type="hidden" name="mortalityDetailID" id="mortalityDetailID_edit">

                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="">
                        <thead>
                        <tr>
                            <th style="width: 250px;">Mortality Cause <?php required_mark(); ?></th>
                            <th style="width: 150px;">No of Birds <?php required_mark(); ?></th>
                            <th style="width: 150px;">Unit Cost<?php required_mark(); ?></th>
                            <th style="width: 150px;">Total Amount<?php required_mark(); ?></th>
                            <th style="width: 200px;">Comment</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <?php echo form_dropdown('causeID', $mortality_causes_arr, '', 'class="form-control" id="causeID_edit"'); ?>
                            </td>
                            <td>
                                <input type="text" name="noOfBirds" onfocus="this.select(); calculateTotalCost_mortality(this);" id="noOfBirds_edit"
                                       class="form-control number noOfBirds_edit">
                            </td>
                            <td><input type="text" name="unitCost" id="unitCost_edit" onfocus="this.select();"
                                       class="form-control number unitCost_edit" required disabled></td>
                            <td><input type="text" name="totalCost" id="totalCost_edit" onfocus="this.select();"
                                       class="form-control number totalCost_edit" onkeyup="" required></td>
                            <td>
                                <textarea class="form-control" rows="1" name="remarks" placeholder="Item Remarks ..."
                                          id="remarks_edit"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="update_mortality_birds()">Save changes
                </button>
            </div>

        </div>
    </div>
</div>
<script>

    var mortalityAutoID;
    var mortalityDetailID;

    $(document).ready(function () {
        number_validation();
        $('.headerclose').click(function () {
            fetchPage('system/buyback/mortality_master', '', 'GRN')
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });

        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            mortalityAutoID = p_id;
            load_mortality_header();
            load_confirmation();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            $('.addTableView').addClass('hide');
        }

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });

        $('#mortality_header_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                documentDate: {validators: {notEmpty: {message: 'Document Date is required.'}}},
                farmID: {validators: {notEmpty: {message: 'Farm ID is required.'}}},
                //batchMasterID: {validators: {notEmpty: {message: 'Batch is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#farmID").prop("disabled", false);
            $("#batchMasterID").prop("disabled", false);
            $("#documentDate").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Buyback/save_mortality_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        mortalityAutoID = data[2];
                        $('#mortalityAutoID_edit_itemAdd').val(mortalityAutoID);
                        $('.addTableView').removeClass('hide');
                        get_mortalityBird_tableView(mortalityAutoID);
                        $('.btn-wizard').removeClass('disabled');
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });

    function fetch_farmBatch(farmID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {farmID: farmID},
            url: "<?php echo site_url('Buyback/fetch_farm_BatchesDropdown_mortality'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_loadBatch').html(data);
                $('.select2').select2();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_mortality_header() {
        if (mortalityAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'mortalityAutoID': mortalityAutoID},
                url: "<?php echo site_url('Buyback/load_mortality_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        mortalityAutoID = data['mortalityAutoID'];
                        $('#mortalityAutoID_edit').val(mortalityAutoID);
                        $('#mortalityAutoID_edit_itemAdd').val(mortalityAutoID);
                        //$('#grnAutoID_edit_itemEdit').val(grnAutoID);
                        $('#dispatchedDate').val(data['documentDate']);
                        $('#narration').val(data['Narration']);
                        $('#farmID').val(data['farmID']).change();
                        batchMasterID = data['batchMasterID'];
                        setTimeout(function () {
                            $('#batchMasterID').val(batchMasterID).change();
                        }, 500);
                        get_mortalityBird_tableView(mortalityAutoID);

                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
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
    }

    function mortality_new_modal() {
        if (mortalityAutoID) {
            loadbirdcurrent(mortalityAutoID,batchMasterID);
            farmBatchName(batchMasterID);
            fetchBirdUnitCost(batchMasterID);
            $('#mortality_add_item_form')[0].reset();
            $('#po_detail_add_table tbody tr').not(':first').remove();
            $("#mortality_add_new_modal").modal({backdrop: "static"});
        }
    }

    function farmBatchName(batchMasterID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'batchid': batchMasterID},
            url: "<?php echo site_url('Buyback/fetchFarmBatch_grn'); ?>",
            success: function (data)
            {
                if(data){
                    $('#farmerBatchName').val(data);
                }
            }
        });
    }

    function fetchBirdUnitCost(batchMasterID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'batchid': batchMasterID},
            url: "<?php echo site_url('Buyback/fetchBirdUnitCost'); ?>",
            success: function (data)
            {
                if(data){
                    $('#unitCost').val(data);
                }
            }
        });
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function add_more() {
        var appendData = $('#po_detail_add_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#po_detail_add_table').append(appendData);
        var lenght = $('#po_detail_add_table tbody tr').length - 1;
        number_validation();
    }

    function save_mortality_birds() {
        var data = $("#mortality_add_item_form").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/save_mortality_birds_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    mortalityDetailID = null;
                    $('#mortality_add_new_modal').modal('hide');
                    setTimeout(function () {
                        get_mortalityBird_tableView(mortalityAutoID);
                    }, 300);
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function get_mortalityBird_tableView(mortalityAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {mortalityAutoID: mortalityAutoID},
            url: "<?php echo site_url('Buyback/load_mortalityBird_detail_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#Mortality_Detial_birds').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function disableMortalitycolumn() {
        $("#documentDate").prop("disabled", true);
        $("#farmID").prop("disabled", true);
        $("#batchMasterID").prop("disabled", true);
    }

    function enableMortalitycolumn() {
        $("#farmID").prop("disabled", false);
        $("#batchMasterID").prop("disabled", false);
        $("#documentDate").prop("disabled", false);
    }

    function update_mortality_birds() {
        var data = $("#mortality_edit_item_form").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/update_mortality_birds_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $("#mortality_detail_edit_modal").modal('hide');
                    get_mortalityBird_tableView(mortalityAutoID);
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function edit_mortality_bird(id) {
        if (mortalityAutoID) {
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
                        data: {'mortalityDetailID': id},
                        url: "<?php echo site_url('Buyback/fetch_mortality_bird_detail'); ?>",
                        beforeSend: function () {
                            $("#mortality_detail_edit_modal").modal('show');
                            startLoad();
                        },
                        success: function (data) {
                            mortalityDetailID = data['mortalityDetailID'];
                            $('#mortalityDetailID_edit').val(data['mortalityDetailID']);
                            $('#causeID_edit').val(data['causeID']);
                            $('#noOfBirds_edit').val(data['noOfBirds']);
                            $('#remarks_edit').val(data['remarks']);
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }

    function delete_mortality_bird(id) {
        if (mortalityAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'mortalityDetailID': id},
                        url: "<?php echo site_url('Buyback/delete_mortality_birds_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            get_mortalityBird_tableView(mortalityAutoID);
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function load_confirmation() {
        if (mortalityAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'mortalityAutoID': mortalityAutoID, 'html': true},
                url: "<?php echo site_url('Buyback/load_mortality_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#confirm_body').html(data);
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function save_draft() {
        if (mortalityAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to save this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save as Draft"
                },
                function () {
                    fetchPage('system/buyback/mortality_master', mortalityAutoID, 'GRN');
                });
        }
    }

    function confirmation() {
        if (mortalityAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want confirm this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'mortalityAutoID': mortalityAutoID},
                        url: "<?php echo site_url('Buyback/mortality_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            myAlert(data[0], data[1]);
                            stopLoad();
                            if (data[0] == 's') {
                                fetchPage('system/buyback/mortality_master', '', 'buyback');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
    function loadbirdcurrent() {
       var batchMasterID =  $('#batchMasterID').val();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'batchMasterID': batchMasterID},
                url: "<?php echo site_url('Buyback/fetchbirdscount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                   $('#currenct').html(data);
                   $('#currentbirds').val(data);
                    stopLoad();

                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
    }
    function greaterthantest(det)
    {
        var currentbirds =$('#currentbirds').val();
        if(det.value > parseFloat(currentbirds)){
            myAlert('w','Birds shoud not be greater than the current birds');
            $(det).val('');
        }
    }

    function calculateTotalCost_mortality(element)
    {
        var Qty = parseFloat($(element).closest('tr').find('.noOfBirds').val());
        var unitcost = parseFloat($(element).closest('tr').find('.unitCost').val());
        $(element).closest('tr').find('.totalCost').val(((Qty * unitcost)).toFixed(2))
    }

</script>