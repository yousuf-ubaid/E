<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('fleet_lang', $primaryLanguage);

$this->load->helper('community_ngo_helper');
$this->load->helper('fleet_helper');

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$rig = fetch_rig();
$asset_arr = fetch_asset_utilization();
$thread = fetch_thread_utilization();
$physical = fetch_physical_utilization();
$statuscon = fetch_status_utilization();
$com_arr = fetch_com_utilization();
$inspection_templates = array('' => 'Select Template');
?>
<style>
    .bigdrop {
        width: 30% !important;
    }
</style>


<div>
    <?php echo form_open('', 'role="form" id="doc_form"'); ?>

    <div class="row">
        <div class="form-group col-sm-4">

            <label for="docNumber">DocNumber <?php required_mark(); ?></label>
            <input type="text" name="docNumber" id="docNumber" class="form-control" readonly>
        </div>
        <div class="form-group col-sm-4">
            <label for="">
                <?php echo $this->lang->line('fleet_document_Date'); ?><?php required_mark(); ?></label>

            <div class="input-group datepic">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="documentDate"
                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                    id="documentDate" class="form-control" required>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="form-group col-sm-4">
            <label for="jobNumber">JobNumber <?php required_mark(); ?></label>
            <input type="text" name="jobNumber" id="jobNumber" class="form-control" required>
        </div>
        <div class="form-group col-sm-4">
            <label for="description">Description <?php required_mark(); ?></label>
            <input type="text" name="description" id="description" class="form-control" required>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-4">
            <label for="rig">
                <?php echo $this->lang->line('fleet_rig'); ?><!--Rig--></label>
            <?php echo form_dropdown('rig', $rig, '', 'class="form-control select2 required" id="rig"'); ?>
        </div>


        <div class="form-group col-sm-4">
            <label for="well">
                <?php echo $this->lang->line('fleet_well'); ?><!--Well--></label>
            <input type="text" name="well" id="well" class="form-control">
        </div>

    </div>
    <hr>
    <div class="row">
        <div class="col-md-3">
            <label for="">
                <?php echo $this->lang->line('fleet_asset_util'); ?></label>
        </div>
        <div class="col-md-9 text-right">
            <button type="button" id="addRowBtn" class="btn btn-primary btn-sm pull-right" onclick="add_more_asset_row() "
                style="margin-right: 4px"><i class="fa fa-plus"></i> <?php echo $this->lang->line('fleet_Add'); ?><!--New Asset-->
            </button>
        </div>
    </div>
    <hr>


    <table class="table table-bordered table-condensed no-color small-font-table" id="asset_table">
        <thead>
            <tr>
                <th style="width: 10%">Code</th>
                <th style="width: 8%">Serial Number</th>
                <th style="width: 15%">Description</th>
                <th style="width: 8%">Thread Condition</th>
                <th style="width: 8%">Physical Condition</th>
                <th style="width: 8%">Status</th>
                <th style="width: 15%">Date From</th>
                <th style="width: 15%">Date To</th>
                <th style="width: 5%">Total Hours</th>
                <th style="width: 5%">Actions</th>
            </tr>
        </thead>
        <tbody id="table_body_asset">

        </tbody>
    </table>
    <hr>
    <div class="row">
        <div class="col-md-3">
            <label for="">
                <?php echo $this->lang->line('fleet_component_utilization'); ?></label>
        </div>
        <div class="col-md-9 text-right">
            <button type="button" id="addRowBtn" class="btn btn-primary btn-sm pull-right" onclick="add_more_com_row()"
                style="margin-right: 4px"><i class="fa fa-plus"></i> <?php echo $this->lang->line('fleet_Add'); ?><!--New Asset-->
            </button>
        </div>
    </div>
    <hr>


    <table class="table table-bordered table-condensed no-color small-font-table" id="component_table">
        <thead>
            <tr>
                <th style="width: 10%">Code</th>
                <th style="width: 8%">Serial Number</th>
                <th style="width: 15%">Description</th>
                <th style="width: 8%">Thread Condition</th>
                <th style="width: 8%">Physical Condition</th>
                <th style="width: 8%">Status</th>
                <th style="width: 15%">Date From</th>
                <th style="width: 15%">Date To</th>
                <th style="width: 5%">Total Hours</th>
                <th style="width: 5%">Actions</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <hr>
    <div class="row">

        <div class="text-right m-t-xs">
            <button id="saveButton" class="btn btn-primary" type="button" onclick="save_new_uti()">Save</button>

            <input type="hidden" id="action" name="action" value="">
            <input type="hidden" id="masterId" name="masterId" value="">
            <button id="submitButton" class="btn btn-primary" type="button" onclick="submit_uti()">Submit</button>
            <!-- <button class="btn btn-primary" id="submit-button" type="button">Submit</button>submit_utli -->

        </div>
    </div>
    </form>
</div>




<div class="modal fade" id="asset_edit_base_modal" role="dialog" aria-labelledby="myModalLabel"
    data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <?php echo form_open('', 'role="form" id="asset_edit_addon_form"'); ?>
            <input type="hidden" name="asset_edit_master_id" id="asset_edit_master_id">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"
                    id="myModalLabel"> </h4>
                <!--Purchase Order Base-->
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-condensed small-font-table">
                            <thead>
                                <tr>
                                    <th style="width: 10%">Code</th>
                                    <th style="width: 10%">Serial Number</th>
                                    <th style="width: 15%">Description</th>
                                    <th style="width: 10%">Thread Condition</th>
                                    <th style="width: 10%">Physical Condition</th>
                                    <th style="width: 10%">Status</th>
                                    <th style="width: 15%">Date From</th>
                                    <th style="width: 15%">Date To</th>
                                    <th style="width: 5%">Total Hours</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="min-width: 10%">
                                        <?php echo form_dropdown('asset_code_edit', $asset_arr, '', 'class="form-control select2" id="asset_code_edit" onchange="updateselecteditem(this)" required'); ?>
                                    </td>
                                    <td style="min-width: 10%"><input type="text" name="serial_no_edit" id="serial_no_edit" value="" class="form-control serial" readonly required></td>
                                    <td style="min-width: 15%"><input type="text" name="description_edit" id="description_edit" value="" class="form-control assetdes" readonly required></td>
                                    <td style="min-width: 10%"><?php echo form_dropdown('thread_condition_edit', $thread, '', 'class="form-control select2" id="thread_condition_edit" required'); ?></td>
                                    <td style="min-width: 10%"><?php echo form_dropdown('physical_condition_edit', $physical, '', 'class="form-control select2" id="physical_condition_edit" required'); ?></td>
                                    <td style="min-width: 10%"><?php echo form_dropdown('status_edit', $statuscon, '', 'class="form-control select2" id="status_edit" required'); ?></td>
                                    <td style="min-width: 15%"><input type="text" name="date_from_edit" value="" class="form-control datepicker_edit" id="date_from_edit" required></td>
                                    <td style="min-width: 15%"><input type="text" name="date_to_edit" value="" class="form-control datepicker_edit" id="date_to_edit" required></td>
                                    <td style="min-width: 5%"><input type="text" name="hours_edit" value="" class="form-control" id="hours_edit" required></td>
                                </tr>

                            </tbody>
                            <tfoot id="table_addon_foot">

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                <button type="button" class="btn btn-primary" onclick="save_asset_line_item_edit()"><?php echo $this->lang->line('common_save_change'); ?> </button>
                <!--Save changes-->
            </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="component_edit_base_modal" role="dialog" aria-labelledby="myModalLabel"
    data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <?php echo form_open('', 'role="form" id="component_edit_addon_form"'); ?>
            <input type="hidden" name="component_edit_master_id" id="component_edit_master_id">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"
                    id="myModalLabel"> </h4>
                <!--Purchase Order Base-->
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-condensed small-font-table">
                            <thead>
                                <tr>
                                    <th style="width: 10%">Code</th>
                                    <th style="width: 10%">Serial Number</th>
                                    <th style="width: 15%">Description</th>
                                    <th style="width: 10%">Thread Condition</th>
                                    <th style="width: 10%">Physical Condition</th>
                                    <th style="width: 10%">Status</th>
                                    <th style="width: 15%">Date From</th>
                                    <th style="width: 15%">Date To</th>
                                    <th style="width: 5%">Total Hours</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="min-width: 10%"><?php echo form_dropdown('asset_code_edit_com', $com_arr, '', 'class="form- selct_val select2 " id="asset_code_edit_com" onchange ="updateselectedcomitem(this)" required'); ?></td>
                                    <td style="min-width: 10%"><input type="text" name="serial_no_edit_com" id="serial_no_edit_com" value="" class="form-control comserial" required></td>
                                    <td style="min-width: 15%"><input type="text" name="description_edit_com" id="description_edit_com" value="" class="form-control comdes" required></td>
                                    <td style="min-width: 10%"><?php echo form_dropdown('thread_condition_edit_com', $thread, ' ', 'class="form-control select2 " id="thread_condition_edit_com" required'); ?></td>
                                    <td style="min-width: 10%"><?php echo form_dropdown('physical_condition_edit_com', $physical, ' ', 'class="form-control select2" id="physical_condition_edit_com" required'); ?></td>
                                    <td style="min-width: 10%"><?php echo form_dropdown('status_edit_com', $statuscon, ' ', 'class="form-control select2 " id="status_edit_com" required'); ?></td>
                                    <td style="min-width: 15%"><input type="text" name="date_from_edit_com" value="" class="form-control datepicker_edit" id="date_from_edit_com" required></td>
                                    <td style="min-width: 15%"><input type="text" name="date_to_edit_com" value="" class="form-control datepicker_edit" id="date_to_edit_com" required></td>
                                    <td style="min-width: 5%"><input type="text" name="hours_edit_com" value="" class="form-control" id="hours_edit_com" required></td>
                                </tr>
                            </tbody>
                            <tfoot id="table_addon_foot">

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                <button type="button" class="btn btn-primary" onclick="save_com_line_item_edit()"><?php echo $this->lang->line('common_save_change'); ?> </button>
                <!--Save changes-->
            </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="template_model" class=" modal fade bs-example-modal-lg"
    style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Templates</h5>
            </div>
            <form role="form" id="template_form" class="form-horizontal">
                <div class="modal-body">

                    <!-- <input type="hidden" class="form-control" id="templateID" name="templateID"> -->


                    <div class="row" id="loadTemplates">
                        <div class="form-group col-sm-12" style="margin-left: 0px;">
                            <label for="loadTemplates">Templates</label>
                            <button type="button" class="btn btn-primary-new pull-right" onclick="openChecklist()"><i class="fa fa-plus"></i>
                            </button>

                        </div>

                        <div class="table-responsive" style="width: 90%; margin-left:20px;">
                            <table class="table table-striped table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Template Name<!--File Name--></th>
                                        <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                        <th><!-- Action --></th>
                                    </tr>
                                </thead>
                                <tbody id="templateTbody" class="no-padding">
                                    <tr class="danger">
                                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_records_found'); ?><!--No record Found--></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="modal-footer">

                        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    </div>
            </form>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" tabindex="-1" id="checklist_model" class=" modal fade bs-example-modal-lg"
    style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="ChecklistHead"></h5>
            </div>
            <form role="form" id="checklist_form" class="form-horizontal">
                <div class="modal-body">

                    <div class="row">
                        <div class="form-group col-sm-4" style="margin-left: 0px;">
                            <label for="checklistdescription"><?php echo $this->lang->line('common_description'); ?><!--Description--></label>
                            <textarea class="form-control" id="description" name="description" style="width:255px;"
                                rows="2"></textarea>
                            <input type="hidden" id="checklistID" name="checklistID" class="form-control">
                        </div>
                    </div>


                    <div class="row" id="templateMasterID">
                        <div class="form-group col-sm-6" style="margin-left: 0px;">
                            <label for="paymentTerms">Select Template</label>
                            <?php echo form_dropdown('assetID', $inspection_templates, '', "class='form-control select2' id='assetID' style='width:255px;'
                                rows='2'"); ?>

                        </div>
                    </div>





                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save'); ?><!--Save--> <span
                                class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
                        <button onclick="resetChecklistModel()" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    </div>
            </form>
        </div>
    </div>
</div>



<script type="text/javascript">
    var search_id = 1;
    var fleetData = <?php echo json_encode(fetch_asset_utilization()); ?>;

    var MasterID;


    $(document).ready(function() {

        // Initialize date picker
        $('.datepic input, .datepicker').datetimepicker({
        });


        $('.headerclose').click(function() {

        });
        $('.select2').select2();



        // Reinitialize datepickers and select2
        $('.datepicker').datetimepicker({

        });
        $('.select2').select2();

        var p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            MasterID = p_id;
            fetch_doc_header_detail(MasterID);
            // load_uti(MasterID);
            edit_uti(MasterID);
            edit_component_uti(MasterID);

        } else {
            generateDocNumber();
            MasterID = null;
        }

        $('#checklist_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid'); ?>.',
            excluded: [':disabled'],
            fields: {
                description: {
                    validators: {
                        notEmpty: {
                            message: 'description is Required.'
                        }
                    }
                },
                assetID: {
                    validators: {
                        notEmpty: {
                            message: 'template is Required.'
                        }
                    }
                }
            }

        }).on('success.form.bv', function(e) {
            e.preventDefault();

            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({
                'name': 'documentMasterAutoID',
                'value': MasterID
            });
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Fleet/save_templates'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    refreshNotifications(true);
                    if (data) {
                        getTemplates();
                        resetChecklistModel();
                        $('#checklist_form').bootstrapValidator('resetForm', true);
                    }
                },
                error: function() {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });



    });

    function generateDocNumber() {
        // Fetch the latest doc number from the backend
        $.ajax({
            url: "<?php echo site_url('fleet/get_latest_doc_number'); ?>", // Adjust URL as needed
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // Check if response contains a valid document number
                var latestNumber = response.doc_number || 'UT/000';

                // Extract the current number part from the latest document number
                var currentNumber = parseInt(latestNumber.replace('UT/', ''), 10) || 0;

                // Generate the new number
                var newNumber = currentNumber + 1;
                var formattedNumber = newNumber.toString().padStart(3, '0');
                var docNumber = 'UT/' + formattedNumber;
                // Set the new number in the input field
                $('#docNumber').val(docNumber);
                },
            error: function() {
                alert("Failed to fetch the latest document number.");
            }
        });
    }

    function add_more_asset_row() {
        // Generate a unique ID based on the current timestamp
        var uniqueId = Date.now();

        var row = `
        <tr>
            <td>
                <?php echo form_dropdown('asset_code[]', $asset_arr, '', 'id="assetcode_${uniqueId}" class="form-control assetcode" onchange="updateselecteditem(this)" required'); ?>
            </td>
            <td>
                <input type="text" name="serial[]" class="form-control serial" readonly required>
            </td>
            <td>
                <input type="text" name="assetdes[]" class="form-control assetdes" readonly required>
            </td>
            <td><?php echo form_dropdown('thread_condition[]', $thread, '', 'id="thread_${uniqueId}" class="form-control select2 thread" required'); ?></td>
            <td><?php echo form_dropdown('physical_condition[]', $physical, '', 'id="physical_${uniqueId}" class="form-control select2 physical" required'); ?></td>
            <td><?php echo form_dropdown('status[]', $statuscon, '', 'id="statuscon_${uniqueId}" class="form-control select2 statuscon" required'); ?></td>
            <td><input type="text" name="date_from[]" value="<?php echo $current_date; ?>" class="form-control datepicker datefrom" required></td>
            <td><input type="text" name="date_to[]" value="<?php echo $current_date; ?>" class="form-control datepicker dateto" required></td>
            <td><input type="text" name="total_hours[]" class="form-control hours" required></td>
            <td><button type="button" onclick="removeRow(this)"><i class="glyphicon glyphicon-trash" style="color: red;"></i></button></td>
        </tr>`;

        $('#asset_table tbody').append(row);

        // Initialize datepicker for the newly added row
        $(`#asset_table tbody tr:last .datepicker`).datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        }).on('dp.change', calculateHours);

        // Initialize Select2 for the asset code dropdown with custom templates
        $(`#assetcode_${uniqueId}`).select2({
            templateResult: formatOption,
            templateSelection: formatSelection,
            dropdownCssClass: 'bigdrop'
        });

        // Initialize Select2 for other dropdowns without custom templates
        $(`#thread_${uniqueId}, #physical_${uniqueId}, #statuscon_${uniqueId}`).select2();

        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }

            var text = option.text.split('|');
            return $('<span>' + text[0] + '|' + text[1] + '|' + text[2] + '</span>');
        }

        function formatSelection(option) {
            if (!option.id) {
                return option.text;
            }

            var text = option.text.split('|');
            return text[0]; // Display only the code in the selected text
        }
    }

    function updateselecteditem(ths) {
        var selectedOptionText = $(ths).find('option:selected').text();
        var result = selectedOptionText.split('|');
        var row = $(ths).closest('tr');

        if (selectedOptionText === "<?php echo $this->lang->line('common_select_description'); ?>") {
            // Clear the Serial Number and Description fields if the placeholder option is selected
            row.find('.serial').val('');
            row.find('.assetdes').val('');
        } else {
            // Populate the Serial Number and Description fields
            row.find('.serial').val(result[2].trim());
            row.find('.assetdes').val(result[1].trim());
        }
    }


    function updateselectedcomitem(ths) {
        var selectedOptionText = $(ths).find('option:selected').text();
        var result = selectedOptionText.split('|');
        var row = $(ths).closest('tr');
        // row.find('.comdes').val(result[1].trim());
        // row.find('.comserial').val(result[2].trim());
        if (selectedOptionText === "<?php echo $this->lang->line('common_select_description'); ?>") {
            // Clear the Serial Number and Description fields if the placeholder option is selected
            row.find('.comserial').val('');
            row.find('.comdes').val('');
        } else {
            // Populate the Serial Number and Description fields
            row.find('.comserial').val(result[2].trim());
            row.find('.comdes').val(result[1].trim());
        }
    }

    function calculateHours() {
        var row = $(this).closest('tr');
        var dateFrom = row.find('input[name="date_from[]"]').val();
        var dateTo = row.find('input[name="date_to[]"]').val();


        if (dateFrom && dateTo) {
            var from = moment(dateFrom, "YYYY-MM-DD HH:mm"); // Parse date and time
            var to = moment(dateTo, "YYYY-MM-DD HH:mm"); // Parse date and time


            var ms = to.diff(from);
            var duration = moment.duration(ms);

            var hours = Math.floor(duration.asHours());
            var minutes = duration.minutes();

            var totalHours = hours;

            row.find('input[name="total_hours[]"]').val(totalHours);
        }
    }

    function calculateHours_edit() {
        var row = $(this).closest('tr');
        var dateFrom = row.find('input[name="date_from_edit"]').val();
        var dateTo = row.find('input[name="date_to_edit"]').val();
        if (dateFrom && dateTo) {
            var from = moment(dateFrom, "YYYY-MM-DD HH:mm"); // Parse date and time
            var to = moment(dateTo, "YYYY-MM-DD HH:mm"); // Parse date and time


            var ms = to.diff(from);
            var duration = moment.duration(ms);

            var hours = Math.floor(duration.asHours());
            var minutes = duration.minutes();

            var totalHours = hours;

            row.find('input[name="hours_edit"]').val(totalHours);
        }
    }

    function calculateHours_edit_com() {
        var row = $(this).closest('tr');
        var dateFrom = row.find('input[name="date_from_edit_com"]').val();
        var dateTo = row.find('input[name="date_to_edit_com"]').val();
        if (dateFrom && dateTo) {
            var from = moment(dateFrom, "YYYY-MM-DD HH:mm"); // Parse date and time
            var to = moment(dateTo, "YYYY-MM-DD HH:mm"); // Parse date and time


            var ms = to.diff(from);
            var duration = moment.duration(ms);

            var hours = Math.floor(duration.asHours());
            var minutes = duration.minutes();

            var totalHours = hours;

            row.find('input[name="hours_edit_com"]').val(totalHours);
        }
    }


    // $('.datepicker').datetimepicker({
    //     format: 'YYYY-MM-DD HH:mm' // Use the consistent date and time format
    // }).on('dp.change', calculateHours);

    function add_more_com_row() {
        // Generate a unique ID based on the current timestamp
        var uniqueId = Date.now();

        var row = `
        <tr>
            <td>
                <?php echo form_dropdown('com_code[]', $com_arr, '', 'id="comcode_${uniqueId}" class="form-control comcode" onchange="updateselectedcomitem(this)" required'); ?>
            </td>
            <td>
                <input type="text" name="serial_number[]" class="form-control comserial" required>
            </td>
            <td>
                <input type="text" name="com_description[]" class="form-control comdes" required>
            </td>
            <td>
                <?php echo form_dropdown('thread_condition[]', $thread, '', 'id="thread_${uniqueId}" class="form-control select2 thread" required'); ?>
            </td>
            <td>
                <?php echo form_dropdown('physical_condition[]', $physical, '', 'id="physical_${uniqueId}" class="form-control select2 physical" required'); ?>
            </td>
            <td>
                <?php echo form_dropdown('status[]', $statuscon, '', 'id="statuscon_${uniqueId}" class="form-control select2 statuscon" required'); ?>
            </td>
            <td>
                <input type="text" name="date_from[]" value="<?php echo $current_date; ?>" class="form-control datepicker datefrom" required>
            </td>
            <td>
                <input type="text" name="date_to[]" value="<?php echo $current_date; ?>" class="form-control datepicker dateto" required>
            </td>
            <td>
                <input type="text" name="total_hours[]" class="form-control hours" required>
            </td>
            <td>
                <button type="button" onclick="removeRow(this)">
                    <i class="glyphicon glyphicon-trash" style="color: red;"></i>
                </button>
            </td>
        </tr>`;

        $('#component_table tbody').append(row);

        // Initialize datepicker for the newly added row
        $(`#component_table tbody tr:last .datepicker`).datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        }).on('dp.change', calculateHours);

        // Initialize Select2 for the com_code dropdown with custom templates
        $(`#comcode_${uniqueId}`).select2({
            templateResult: formatOption,
            templateSelection: formatSelection,
            dropdownCssClass: 'bigdrop'
        });

        // Initialize Select2 for other dropdowns without custom templates
        $(`#thread_${uniqueId}, #physical_${uniqueId}, #statuscon_${uniqueId}`).select2();

        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }

            var text = option.text.split('|');
            return $('<span>' + text[0] + '|' + text[1] + '|' + text[2] + '</span>');
        }

        function formatSelection(option) {
            if (!option.id) {
                return option.text;
            }

            var text = option.text.split('|');
            return text[0]; // Display only the code in the selected text
        }
    }



    function save_new_uti() {


        // Collect form data
        var docNumber = $('#docNumber').val();
        var documentDate = $('#documentDate').val();
        var jobNumber = $('#jobNumber').val();
        var description = $('#description').val();
        var rig = $('#rig').val();
        var result = $('#rig option:selected').text().split('|');
        var well = $('#well').val();


        if (!docNumber) {
            myAlert("e", "Document Number is required.");
            return;
        }
        if (!documentDate) {
            myAlert("e", "Document Date is required.");
            return;
        }
        if (!jobNumber) {
            myAlert("e", "Job Number is required.");
            return;
        }
        if (!description) {
            myAlert("e", "Description is required.");
            return;
        }

        // Collect data from dynamically added rows
        var assetData = [];
        $('#asset_table tbody tr').each(function() {
            var row = $(this);
            var asset = {
                id: row.data('id'),
                asset_id: row.find('.assetcode').val(),
                serial_number: row.find('.serial').val(),
                description: row.find('.assetdes').val(),
                thread_condition_id: row.find('.thread').val(),
                physical_condition_id: row.find('.physical').val(),
                date_time_from: row.find('.datefrom').val(),
                date_time_to: row.find('.dateto').val(),
                hours: row.find('.hours').val(),
                status_id: row.find('.statuscon').val()
            };
            assetData.push(asset);
        });
        var comData = [];
        $('#component_table tbody tr').each(function() {
            var row = $(this);
            var com = {
                id: row.data('id'),
                asset_id: row.find('.comcode').val(),
                serial_number: row.find('.comserial').val(),
                description: row.find('.comdes').val(),
                thread_condition_id: row.find('.thread').val(),
                physical_condition_id: row.find('.physical').val(),
                date_time_from: row.find('.datefrom').val(),
                date_time_to: row.find('.dateto').val(),
                hours: row.find('.hours').val(),
                status_id: row.find('.statuscon').val()
            };
            comData.push(com);
        });


        //    if(isValid = true){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                docNumber: docNumber,
                documentDate: documentDate,
                jobNumber: jobNumber,
                description: description,
                rig: rig,
                rigname: result[1] ?? null,
                well: well,
                assets: assetData,
                coms: comData,
                masterid: MasterID
            },
            url: "<?php echo site_url('Fleet/save_uti'); ?>", // Replace 'controller' with your actual controller name
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                if (data[0] == 's') {
                    swal("Success", data[1], "success");
                    // $('#masterId').val(data[2]);
                    // MasterID = data[2];
                    refreshNotifications(true);
                    $('#saveButton').prop('disabled', true);

                    //   fetchPage('system/Fleet_Management/fleet_utilization', '', 'Asset Utilization');
                } else {
                    swal("Error", data[1], "error");
                }
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Try Again", "error");
            }
        });
    } // Send data via AJAX




    function removeRow(button) {
        // Remove the row that contains the clicked button
        $(button).closest('tr').remove();
    }


    function submit_uti() {
        var isValid = true;

        // Check if there are any assets or components
        var assetCount = $('#asset_table tbody tr').length;
        var componentCount = $('#component_table tbody tr').length;

        if (assetCount == 0 && componentCount == 0) {
            isValid = false;
            myAlert('e', 'You must add at least one asset or component.');
            return false;
        }

        $('#asset_table tbody tr').each(function() {
            var row = $(this);

            // Validate asset fields
            if (row.find('.assetcode').val() == '') {
                isValid = false;
                myAlert('e', 'Asset ID is required.');
                row.find('.assetcode').focus();
                return false;
            }
            if (row.find('.serial').val() == '') {
                isValid = false;
                myAlert('e', 'Serial number is required.');
                row.find('.serial').focus();
                return false;
            }
            if (row.find('.assetdes').val() == '') {
                isValid = false;
                myAlert('e', 'Asset description is required.');
                row.find('.assetdes').focus();
                return false;
            }
            if (row.find('.thread').val() == '') {
                isValid = false;
                myAlert('e', 'Thread condition is required.');
                row.find('.thread').focus();
                return false;
            }
            if (row.find('.physical').val() == '') {
                isValid = false;
                myAlert('e', 'Physical condition is required.');
                row.find('.physical').focus();
                return false;
            }
            if (row.find('.datefrom').val() == '') {
                isValid = false;
                myAlert('e', 'Date from is required.');
                row.find('.datefrom').focus();
                return false;
            }
            if (row.find('.dateto').val() == '') {
                isValid = false;
                myAlert('e', 'Date to is required.');
                row.find('.dateto').focus();
                return false;
            }
            if (row.find('.hours').val() == '') {
                isValid = false;
                myAlert('e', 'Hours is required.');
                row.find('.hours').focus();
                return false;
            }
            if (row.find('.statuscon').val() == '') {
                isValid = false;
                myAlert('e', 'Status condition is required.');
                row.find('.statuscon').focus();
                return false;
            }
        });

        $('#component_table tbody tr').each(function() {
            var row = $(this);

            // Validate component fields
            if (row.find('.comcode').val() == '') {
                isValid = false;
                myAlert('e', 'Component ID is required.');
                row.find('.comcode').focus();
                return false;
            }
            if (row.find('.comserial').val() == '') {
                isValid = false;
                myAlert('e', 'Component serial number is required.');
                row.find('.comserial').focus();
                return false;
            }
            if (row.find('.comdes').val() == '') {
                isValid = false;
                myAlert('e', 'Component description is required.');
                row.find('.comdes').focus();
                return false;
            }
            if (row.find('.thread').val() == '') {
                isValid = false;
                myAlert('e', 'Component thread condition is required.');
                row.find('.thread').focus();
                return false;
            }
            if (row.find('.physical').val() == '') {
                isValid = false;
                myAlert('e', 'Component physical condition is required.');
                row.find('.physical').focus();
                return false;
            }
            if (row.find('.datefrom').val() == '') {
                isValid = false;
                myAlert('e', 'Component date from is required.');
                row.find('.datefrom').focus();
                return false;
            }
            if (row.find('.dateto').val() == '') {
                isValid = false;
                myAlert('e', 'Component date to is required.');
                row.find('.dateto').focus();
                return false;
            }
            if (row.find('.hours').val() == '') {
                isValid = false;
                myAlert('e', 'Hours is required.');
                row.find('.hours').focus();
                return false;
            }
            if (row.find('.statuscon').val() == '') {
                isValid = false;
                myAlert('e', 'Status condition is required.');
                row.find('.statuscon').focus();
                return false;
            }
        });

        if (isValid) {
            var proceedWithUnclosed = false;
            var masterId = $('#masterId').val();

            function sendAjaxRequest() {
                // Update the proceed flag in the data
                var data = {
                    masterId: MasterID,
                    proceedWithUnclosed: proceedWithUnclosed ? 1 : 0 // Send as integer (1 or 0)

                };

                $.ajax({
                    type: 'post',
                    url: "<?php echo site_url('fleet/submit_uti'); ?>",
                    data: data,
                    dataType: 'json',
                    beforeSend: function() {
                        // startLoad(); // Optional, if you have a loading spinner
                    },
                    success: function(data) {
                        // stopLoad(); // Optional, if you have a loading spinner
                        if (data[0] === 'w' && !proceedWithUnclosed) {
                            swal({
                                title: "Warning",
                                text: data[1],
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Yes",
                                cancelButtonText: "Cancel",
                                closeOnConfirm: true,
                                closeOnCancel: true
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    proceedWithUnclosed = true;
                                    sendAjaxRequest(); // Retry submission with updated flag
                                }
                            });
                        } else {
                            myAlert(data[0], data[1]);
                            // Handle other responses
                            if (data[0] == 's') {
                                // Success
                                refreshNotifications(true);
                                generateDocNumber();
                                fetchPage('system/Fleet_Management/fleet_utilization', '', 'Asset Utilization');
                            }
                        }
                    },
                    error: function() {
                        swal("Cancelled", "Try Again", "error");
                    }
                });
            }

            // Initial AJAX call
            sendAjaxRequest();
        }
    }





    function edit_uti(MasterId) {
        if (!MasterId) return;

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'masterId': MasterId
            },
            url: "<?php echo site_url('Fleet/edit_uti'); ?>",
            beforeSend: startLoad,
            success: function(data) {
                $('#asset_table tbody').html('');
                $('#asset_table tbody').append(data);
                $('#saveButton').html('Update');
                $('.datepicker').datetimepicker({
                    format: 'YYYY-MM-DD HH:mm' // Use the PHP variable for the time format only
                }).on('dp.change', calculateHours);

                $('.select2').select2();
                stopLoad();
                refreshNotifications(true);
            },
            error: function() {
                alert('An error occurred! Please try again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function edit_component_uti(MasterId) {
        if (!MasterId) return;

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'masterId': MasterId
            },
            url: "<?php echo site_url('Fleet/edit_com_uti'); ?>",
            beforeSend: startLoad,
            success: function(data) {
                $('#component_table tbody').html('');
                $('#component_table tbody').append(data);
                $('#saveButton').html('Update');
                $('.datepicker').datetimepicker({
                    format: 'YYYY-MM-DD HH:mm' // Use the PHP variable for the time format only
                }).on('dp.change', calculateHours);

                $('.select2').select2();
                stopLoad();
                refreshNotifications(true);
            },
            error: function() {
                alert('An error occurred! Please try again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function editasset_line_record(id) {

        $("#asset_edit_master_id").val(id);

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'id': id
            },
            url: "<?php echo site_url('Fleet/fetch_editasset_line_record'); ?>",
            beforeSend: startLoad,
            success: function(data) {
                $("#asset_code_edit").val(data['asset_id']).change();
                $("#serial_no_edit").val(data['serial_number']);
                $("#description_edit").val(data['description']);
                $("#thread_condition_edit").val(data['thread_condition_id']).change();
                $("#physical_condition_edit").val(data['physical_condition_id']).change();
                $("#status_edit").val(data['status_id']).change();
                $("#date_from_edit").val(data['date_time_from']);
                $("#date_to_edit").val(data['date_time_to']);
                $("#hours_edit").val(data['hours']);

                // Initialize datepicker
                $('.datepicker_edit').datetimepicker({
                    format: 'YYYY-MM-DD HH:mm'
                }).on('dp.change', calculateHours_edit);

                // Initialize Select2 with custom templates
                $('#asset_code_edit').select2({
                    templateResult: formatOption,
                    templateSelection: formatSelection,
                    dropdownCssClass: 'bigdrop'
                });

                $("#asset_edit_base_modal").modal({
                    backdrop: "static"
                });

                stopLoad();
                refreshNotifications(true);
            },
            error: function() {
                alert('An error occurred! Please try again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function checklist_record(id) {
        // Set the checklistID and assetID in the hidden fields
        $("#checklistID").val(id);
        // $("#templateID").val(id);
        // Open the template modal
        $("#template_model").modal({
            backdrop: "static"
        });
        getTemplates();
    }

    function getTemplates() {
        var checklistID = $('#checklistID').val();

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {
                checklistID: checklistID
            },
            url: "<?php echo site_url('Fleet/getTemplates'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                $('#templateTbody').empty();

                if (data.length === 0) {
                    var row = `
                        <tr class="danger">
                            <td colspan="4" class="text-center"><?php echo $this->lang->line('common_no_records_found'); ?></td>
                        </tr>
                    `;
                    $('#templateTbody').append(row);
                } else {
                    $.each(data, function(index, item) {
                        var row = `
                            <tr>
                                <td>${index + 1}</td>  <!-- # Numbering -->
                                <td>${item.templateName}</td>  <!-- Segment Code -->
                                <td>${item.description}</td>  <!-- Description -->
                                 <td class="text-center">
                         <a  onclick="viewPage('${item.pageLink}','${item.id}','${item.templateName}')" title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
         data-original-title="View">
                           <!-- Eye icon for View -->
                        </a> &nbsp;&nbsp;| &nbsp;&nbsp;
                        <a class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" onclick="deleteTemplate(${item.id})" title="Delete" rel="tooltip">
                             <!-- Trash icon for Delete -->
                        </a>
                        
                    </td>
                            </tr>
                        `;
                        $('#templateTbody').append(row);
                    });
                }
            },
            error: function() {
                myAlert('e', 'Something went wrong');
            }
        });
    }

    function openChecklist() {
        var templateID = $('#checklistID').val();


        $.ajax({
            url: "<?php echo site_url('Fleet/fetch_inspection_templates') ?>", // Update with your controller path
            method: 'POST',
            data: {
                utilizationDetailID: templateID
            },
            success: function(response) {

                $('#assetID').html(response);
            },
            error: function() {
                alert('Error loading templates.');
            }
        });


        $('#checklist_model').modal({
            backdrop: 'static',
            keyboard: false
        });


        $('#checklistID').val(templateID);
    }

    // Option formatting functions
    function formatOption(option) {
        if (!option.id) {
            return option.text;
        }

        var text = option.text.split('|');
        return $('<span>' + text[0] + '|' + text[1] + '|' + text[2] + '</span>');
    }

    function formatSelection(option) {
        if (!option.id) {
            return option.text;
        }

        var text = option.text.split('|');
        return text[0]; // Display only the code in the selected text
    }


    function editcomponent_line_record(id) {

        $("#component_edit_master_id").val(id);

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'id': id
            },
            url: "<?php echo site_url('Fleet/fetch_editcomponent_line_record'); ?>",
            beforeSend: startLoad,
            success: function(data) {

                $("#asset_code_edit_com").val(data['asset_id']).change();
                $("#serial_no_edit_com").val(data['serial_number']);
                $("#description_edit_com").val(data['description']);
                $("#thread_condition_edit_com").val(data['thread_condition_id']).change();
                $("#physical_condition_edit_com").val(data['physical_condition_id']).change();
                $("#status_edit_com").val(data['status_id']).change();
                $("#date_from_edit_com").val(data['date_time_from']);
                $("#date_to_edit_com").val(data['date_time_to']);
                $("#hours_edit_com").val(data['hours']);

                // Initialize datepicker
                $('.datepicker_edit').datetimepicker({
                    format: 'YYYY-MM-DD HH:mm'
                }).on('dp.change', calculateHours_edit_com);

                // Initialize Select2 with custom templates
                $('#asset_code_edit_com').select2({
                    templateResult: formatOption_com,
                    templateSelection: formatSelection_com,
                    dropdownCssClass: 'bigdrop'
                });

                $("#component_edit_base_modal").modal({
                    backdrop: "static"
                });

                stopLoad();
                refreshNotifications(true);
            },
            error: function() {
                alert('An error occurred! Please try again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    // Option formatting functions
    function formatOption_com(option) {
        if (!option.id) {
            return option.text;
        }

        var text = option.text.split('|');
        return $('<span>' + text[0] + '|' + text[1] + '|' + text[2] + '</span>');
    }

    function formatSelection_com(option) {
        if (!option.id) {
            return option.text;
        }

        var text = option.text.split('|');
        return text[0]; // Display only the code in the selected text
    }


    function save_asset_line_item_edit() {
        var data = $('#asset_edit_addon_form').serializeArray();

        // Add MasterID to the data being sent
        data.push({
            name: 'master_id',
            value: MasterID
        });

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Fleet/save_asset_line_item_edit'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                refreshNotifications(true);
                if (data) {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        // purchaseOrderDetailsID = null;
                        edit_uti(MasterID);
                        $('#asset_edit_base_modal').modal('hide');
                    }
                }
            },
            error: function() {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function save_com_line_item_edit() {
        var data = $('#component_edit_addon_form').serializeArray();
        // Add MasterID to the data being sent
        data.push({
            name: 'master_id',
            value: MasterID
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Fleet/save_com_line_item_edit'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                refreshNotifications(true);
                if (data) {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        // purchaseOrderDetailsID = null;
                        edit_component_uti(MasterID);
                        $('#component_edit_base_modal').modal('hide');

                    }
                }

            },
            error: function() {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function fetch_doc_header_detail(MasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'masterId': MasterID
            },

            url: "<?php echo site_url('Fleet/fetch_doc_header_detail'); ?>",
            beforeSend: startLoad,
            success: function(data) {
                $('#docNumber').val(data['doc_number']);
                $('#documentDate').val(data['date']);
                $('#jobNumber').val(data['job_num']);
                $('#description').val(data['description']);
                $('#rig').val(data['rig_id']).change();
                $('#well').val(data['well_name']);
                // $('#saveButton').html('Update');
                stopLoad();
                refreshNotifications(true);
            },
            error: function() {
                alert('An error occurred! Please try again.');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function deletecomponent_line_record(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Fleet/delete_component_line_record'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'id': id
                    },
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            edit_component_uti(MasterID);
                        }
                    },
                    error: function() {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );

    }

    function deleteasset_line_record(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Fleet/delete_asset_line_record'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'id': id
                    },
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            edit_uti(MasterID);
                        }
                    },
                    error: function() {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );

    }

    function resetChecklistModel() {
        $('#checklist_model').modal("hide");
        // $('#checklist_model input, #checklist_model select, #checklist_model textarea').val('');
        $('#checklist_model #description').val('');
        $('#assetID').val('').change();


    }

    function deleteTemplate(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Fleet/deleteTemplate'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'id': id
                    },
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        getTemplates();
                    },
                    error: function() {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );

    }

    function viewPage(pagelink, id, templateName) {
        fetchPage('system/Fleet_Management/Template_View/templateMaster', '', 'Inspection');

        $("#template_model").modal('hide');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                pagelink: pagelink,
                id: id,
                templateName: templateName
            },
            url: "<?php echo site_url('Fleet/load_document_templates_details'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                refreshNotifications(true);

                if (data.status === 'success') {
                    // Populate the templateBodyView with the returned HTML
                    $('#templateBodyView').html(data.html);
                } else {
                    alert(data.message || 'An unexpected error occurred.');
                }
            },
            error: function() {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
</script>