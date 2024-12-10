<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], FALSE);
$this->load->helper('operation_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$all_states_arr = all_states();
$countries_arr = load_all_countrys();
$countryCode_arr = all_country_codes();
$contractor_arr = fetch_ngo_contractor();

$segment_arr = segment_drop();
$revenue_gl = all_revenue_gl_drop();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: #060606
    }

    span.input-req-inner {
        width: 20px;
        height: 40px;
        position: absolute;
        overflow: hidden;
        display: block;
        right: 4px;
        top: -15px;
        -webkit-transform: rotate(135deg);
        -ms-transform: rotate(135deg);
        transform: rotate(135deg);
    }

    span.input-req-inner:before {
        font-size: 20px;
        content: "*";
        top: 15px;
        right: 1px;
        color: #fff;
        position: absolute;
        z-index: 2;
        cursor: default;
    }

    span.input-req-inner:after {
        content: '';
        width: 35px;
        height: 35px;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        background: #f45640;
        position: absolute;
        top: 7px;
        right: -29px;
    }

    .search_cancel {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 3px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }
</style>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Sub Project - Header</a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_ngo_document()" data-toggle="tab">Documents</a>
</div>
<br>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="contact_form"'); ?>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>PROJECT NAME AND DETAIL</h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('operationngo_project_name'); ?><!--Project Name--></label>
                    </div>
                    <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field"><input type="text" name="projectName"
                                                                                     id="projectName"
                                                                                     class="form-control"
                                                                                     placeholder="<?php echo $this->lang->line('operationngo_project_name'); ?>"
                                                                                     required><span
                                       class="input-req-inner"></span></span><!--Project Name-->
                        <input type="hidden" name="ngoProjectID" id="ngoProjectID_edit">
                        <input type="hidden" id="ngoProjectID_masterID_hn" name="masterID">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Estimated Start Date</label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                                            <div class="input-group startdateDatepic">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="text" name="startDate" id="startDate"
                                                       class="form-control dateFields frm_input">
                                            </div>
                                  <span class="input-req-inner" style="z-index: 10;"></span></span>

                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Estimated End Date</label>
                    </div>
                    <div class="form-group col-sm-4">
                                     <span class="input-req" title="Required Field">
                        <div class="input-group startdateDatepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="endDate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="endDate" class="form-control" required>
                        </div>
                                         <span class="input-req-inner" style="z-index: 10;"></span></span>
                    </div>

                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Detail Description</label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field"><textarea class="form-control" id="description"
                                                                         name="description" rows="4"></textarea><span
                        class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Total Number of Houses</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="number" name="totalNumberofHouses" id="totalNumberofHouses" class="form-control">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Floor Area</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea class="form-control" id="floorArea" name="floorArea" rows="2"></textarea>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Cost of a House</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea class="form-control" id="costofhouse" name="costofhouse" rows="2"></textarea>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Additional Cost</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea class="form-control" id="additionalCost" name="additionalCost" rows="2"></textarea>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Estimated Completion Time for a House</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="number" name="EstimatedDays" id="EstimatedDays" class="form-control">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-1">
                        &nbsp
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Contractor</label>

                    </div>
                    <div class="form-group col-sm-4">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="add-contractor"
                                        style="height: 29px; padding: 2px 10px;">
                                    <i class="fa fa-plus" style="font-size: 11px"></i>
                                </button>
                            </span>
                        <?php echo form_dropdown('contractorID', $contractor_arr, '', 'class="form-control" id="contractorID"'); ?>
                    </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-6">
                <div class="text-right m-t-xs">
                    <button id="save_btn" class="btn btn-primary" type="submit">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                </div>
            </div>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-12">
                <div id="beneficiary_document"></div>
            </div>
        </div>
        <br>

        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous');?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="beneficiary_system_code_generator()"><?php echo $this->lang->line('common_confirm');?><!--Confirm--></button>
        </div>
    </div>
</div>
<div class="modal fade" id="add-contractor-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">New Contractor</h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Contractor</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="contractor_name" name="contractor_name">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="save-btn-contractor">Save</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script type="text/javascript">
    var ngoProjectID = null;
    $(document).ready(function () {
        $('.headerclose').click(function () {

            fetchPage('system/operationNgo/project_master', '', 'Projects');
        });
        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));

        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        master_id = <?php echo json_encode(trim($this->input->post('policy_id'))); ?>;
        if (p_id) {
            ngoProjectID = p_id;
            load_donor_header();
            fetch_project_image();

        }
        if (master_id) {
            $('#ngoProjectID_masterID_hn').val(master_id);
        }

        $('#contact_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                projectName: {validators: {notEmpty: {message: 'Project Name is required.'}}},
                description: {validators: {notEmpty: {message: 'Detail Description is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('OperationNgo/save_ngo_project_subcategory'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetchPage('system/operationNgo/project_master', '', 'Projects');
                    } else {
                        $('.btn-primary').removeAttr('disabled');
                    }

                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });

    });

    $('#add-contractor').click(function () {
        $('#contractor_name').val('');
        $('#add-contractor-modal').modal({backdrop: 'static'});
    });


    function load_donor_header() {
        if (ngoProjectID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'ngoProjectID': ngoProjectID},
                url: "<?php echo site_url('OperationNgo/load_donor_project_data'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#ngoProjectID_edit').val(ngoProjectID);
                        $('#projectName').val(data['projectName']);
                        $('#description').val(data['description']);
                        $('#startDate').val(data['startDate']);
                        $('#endDate').val(data['endDate']);
                        $('#totalNumberofHouses').val(data['totalNumberofHouses']);
                        $('#floorArea').val(data['floorArea']);
                        $('#costofhouse').val(data['costofhouse']);
                        $('#additionalCost').val(data['additionalCost']);
                        $('#EstimatedDays').val(data['EstimatedDays']);
                        $('#contractorID').val(data['contractorID']);
                        $('#save_btn').html('Update');

                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }


    $('#save-btn-contractor').click(function (e) {
        e.preventDefault();
        var contractorName = $.trim($('#contractor_name').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractorName': contractorName},
            url: '<?php echo site_url("OperationNgo/save_ngo_contractor"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                var contractor_drop = $('#contractor_name');
                if (data[0] == 's') {
                    contractor_drop.append('<option value="' + data[2] + '">' + contractorName + '</option>');
                    contractor_drop.val(data[2]);
                    $('#add-contractor-modal').modal('hide');

                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });

    function fetch_project_image() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {ngoProjectID: ngoProjectID},
            url: '<?php echo site_url("OperationNgo/load_project_image_view"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#beneficiary_document').html(data);
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

</script>