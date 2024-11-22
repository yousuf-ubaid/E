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
$physical= fetch_physical_utilization();
$statuscon = fetch_status_utilization();
$com_arr = fetch_com_utilization();
?>
    <style>
       
    </style>
   
   
    <div>
    <?php echo form_open('', 'role="form" id="doc_form"'); ?>

                <div class="row">
                    <div class="form-group col-sm-4">
                    
                        <label for="docNumber">DocNumber <?php required_mark(); ?></label>
                        <input type="text" name="docNumber" id="docNumber"  class="form-control"disabled readonly>
                    </div>
                    <div class="form-group col-sm-4">
                            <label for="">
                                <?php echo $this->lang->line('fleet_document_Date'); ?><?php required_mark(); ?></label>

                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="documentDate"
                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                     id="documentDate" class="form-control" readonly disabled required>
                            </div>
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="jobNumber">JobNumber <?php required_mark(); ?></label>
                        <input type="text" name="jobNumber" id="jobNumber" class="form-control" readonly disabled required>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="description">Description <?php required_mark(); ?></label>
                        <input type="text" name="description" id="description" class="form-control" readonly disabled required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                            <label for="rig">
                            <?php echo $this->lang->line('fleet_rig'); ?><!--Rig--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('rig', $rig, '', 'class="form-control select2 rig required " disabled id="rig"'); ?>
                    </div>
                   

                    <div class="form-group col-sm-4">
                            <label for="well">
                            <?php echo $this->lang->line('fleet_well'); ?><!--Well--> <?php required_mark(); ?></label>
                            <input type="text" name="well" id="well" class="form-control" disabled readonly>
                    </div>
                    
                </div>
                <hr>
                    <div class="row">
                        <div class="col-md-3">
                        <label for="">
                        <?php echo $this->lang->line('fleet_asset_util');?></label>
                        </div>
                        <div class="col-md-9 text-right">
                          
                        </div>
                    </div>
                    <hr>


    <table class="table table-bordered table-condensed no-color" id="asset_table">
        <thead>
        <tr>
            <th style="min-width: 10%">Code</th>
            <th style="min-width: 5%">Serial Number</th>
            <th style="min-width: 15%">Description</th>
            <th style="min-width: 8%">Thread Condition</th>
            <th style="min-width: 8%" >Physical Condition</th>
            <th style="min-width: 8%"> Status</th>
            <th style="min-width: 20%">Date From</th>
            <th style="min-width: 20%">Date To</th>
            <th style="min-width: 5%">Total Hours</th>
            
        </tr>
        </thead>
        <tbody id="table_body_asset">
        
        </tbody>
    </table>
<hr>
                    <div class="row">
                        <div class="col-md-3">
                        <label for="">
                        <?php echo $this->lang->line('fleet_component_utilization');?></label>
                        </div>
                        <div class="col-md-9 text-right">
                       
                        </div>
                    </div>
                    <hr>


    <table class="table table-bordered table-condensed no-color" id="component_table">
        <thead>
        <tr>
        <th style="min-width: 20%">Code</th>
            <th style="min-width: 5%">Serial Number</th>
            <th style="min-width: 15%">Description</th>
            <th style="min-width: 8%">Thread Condition</th>
            <th style="min-width: 8%" >Physical Condition</th>
            <th style="min-width: 8%"> Status</th>
            <th style="min-width: 20%">Date From</th>
            <th style="min-width: 20%">Date To</th>
            <th style="min-width: 5%">Total Hours</th>
        
        </tr>
        </thead>
        <tbody>
        <tr>
           
           
        </tr>
        </tbody>
    </table>
    <hr>
    <div class="row">

                <div class="text-right m-t-xs">
    
                <button type="button" class="btn btn-primary" id="cancelButton">Cancel</button>


                    
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
                    id="myModalLabel">  </h4>
                <!--Purchase Order Base-->
            </div>
            <div class="modal-body">
                <div class="row">
                   
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                            <tr>
                            <th style="min-width: 20%">Code</th>
                            <th style="min-width: 5%">Serial Number</th>
                            <th style="min-width: 15%">Description</th>
                            <th style="min-width: 8%">Thread Condition</th>
                            <th style="min-width: 8%" >Physical Condition</th>
                            <th style="min-width: 8%"> Status</th>
                            <th style="min-width: 20%">Date From</th>
                            <th style="min-width: 20%">Date To</th>
                            <th style="min-width: 5%">Total Hours</th>
                           
                            </tr>
                            </thead>
                            <tbody >
                                <tr>
                                    <td style="min-width: 20%"><?php echo form_dropdown('asset_code_edit', $asset_arr, '', 'class="form- selct_val select2 " id="asset_code_edit" onchange ="updateselecteditem(this)" required'); ?></td>
                                    <td style="min-width: 5%"><input type="text" name="serial_no_edit" id="serial_no_edit" value="" class="form-control serial" required></td>
                                    <td style="min-width: 15%"><input type="text" name="description_edit" id="description_edit"value="" class="form-control assetdes" required></td>
                                    <td style="min-width: 8%"><?php echo form_dropdown('thread_condition_edit', $thread, ' ', 'class="form-control select2 " id="thread_condition_edit" required'); ?></td>
                                    <td style="min-width: 8%"><?php echo form_dropdown('physical_condition_edit', $physical, ' ', 'class="form-control select2" id="physical_condition_edit" required'); ?></td>
                                    <td style="min-width: 8%"><?php echo form_dropdown('status_edit', $statuscon, ' ', 'class="form-control select2 " id="status_edit" required'); ?></td>
                                    <td style="min-width: 20%"><input type="text" name="date_from_edit" value="" class="form-control datepicker_edit"id="date_from_edit" required></td>
                                    <td style="min-width: 20%"><input type="text" name="date_to_edit" value="" class="form-control datepicker_edit" id="date_to_edit" required></td>
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
                <button type="button" class="btn btn-primary" onclick="save_asset_line_item_edit()"
                        ><?php echo $this->lang->line('common_save_change'); ?> </button>
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
                    id="myModalLabel">  </h4>
                <!--Purchase Order Base-->
            </div>
            <div class="modal-body">
                <div class="row">
                   
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                            <tr>
                            <th style="min-width: 20%">Code</th>
                            <th style="min-width: 5%">Serial Number</th>
                            <th style="min-width: 15%">Description</th>
                            <th style="min-width: 8%">Thread Condition</th>
                            <th style="min-width: 8%" >Physical Condition</th>
                            <th style="min-width: 8%"> Status</th>
                            <th style="min-width: 20%">Date From</th>
                            <th style="min-width: 20%">Date To</th>
                            <th style="min-width: 5%">Total Hours</th>
                           
                            </tr>
                            </thead>
                            <tbody >
                                <tr>
                                    <td style="min-width: 20%"><?php echo form_dropdown('asset_code_edit_com', $com_arr, '', 'class="form- selct_val select2 " id="asset_code_edit_com" onchange ="updateselectedcomitem(this)" required'); ?></td>
                                    <td style="min-width: 5%"><input type="text" name="serial_no_edit_com" id="serial_no_edit_com" value="" class="form-control comserial" required></td>
                                    <td style="min-width: 15%"><input type="text" name="description_edit_com" id="description_edit_com"value="" class="form-control comdes" required></td>
                                    <td style="min-width: 8%"><?php echo form_dropdown('thread_condition_edit_com', $thread, ' ', 'class="form-control select2 " id="thread_condition_edit_com" required'); ?></td>
                                    <td style="min-width: 8%"><?php echo form_dropdown('physical_condition_edit_com', $physical, ' ', 'class="form-control select2" id="physical_condition_edit_com" required'); ?></td>
                                    <td style="min-width: 8%"><?php echo form_dropdown('status_edit_com', $statuscon, ' ', 'class="form-control select2 " id="status_edit_com" required'); ?></td>
                                    <td style="min-width: 20%"><input type="text" name="date_from_edit_com" value="" class="form-control datepicker_edit"id="date_from_edit_com"  required></td>
                                    <td style="min-width: 20%"><input type="text" name="date_to_edit_com" value="" class="form-control datepicker_edit" id="date_to_edit_com" required></td>
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
                <button type="button" class="btn btn-primary" onclick="save_com_line_item_edit()"
                        ><?php echo $this->lang->line('common_save_change'); ?> </button>
                <!--Save changes-->
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
           
            $('.datepic input, .datepicker').datetimepicker({
                // useCurrent: false,
                // format: '<?php echo $date_format_policy; ?>'
            });
            document.getElementById('cancelButton').addEventListener('click', function() {
        fetchPage('system/Fleet_Management/fleet_utilization', '', 'Asset Utilization');
    });

            $('.headerclose').click(function () {
            
            });
            $('.select2').select2();

         
            // $('#asset_table tbody').append(row);

            // Reinitialize datepickers and select2
            $('.datepicker').datetimepicker({
                // your datepicker options
            });
            $('.select2').select2();

            // $('#update').hide();
    
    
 
           var p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            MasterID = p_id;
            fetch_doc_header_detail(MasterID);
            // load_uti(MasterID);
            edit_uti(MasterID);
            edit_component_uti(MasterID);
          
        }
       else{
        generateDocNumber();
       }

   


        
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
        var row = `<tr>
           <td style="min-width: 20%"><?php echo form_dropdown('asset_code[]', $asset_arr, ' ', 'class="form- selct_val select2 assetcode" onchange ="updateselecteditem(this)" required'); ?></td>
            <td style="min-width: 5%"><input type="text" name="serial" class="form-control serial" required></td>
            <td style="min-width: 15%"><input type="text" name="assetdes" class="form-control assetdes" required></td>
            <td style="min-width: 8%"><?php echo form_dropdown('thread_condition[]', $thread, ' ', 'class="form-control select2 thread" required'); ?></td>
            <td style="min-width: 8%"><?php echo form_dropdown('physical_condition[]', $physical, ' ', 'class="form-control select2 physical" required'); ?></td>
            <td style="min-width: 8%"><?php echo form_dropdown('status[]', $statuscon, ' ', 'class="form-control select2 statuscon" required'); ?></td>
            <td style="min-width: 25%"><input type="text" name="date_from[]" value="<?php echo $current_date; ?>" class="form-control datepicker datefrom" required></td>
            <td style="min-width: 25%"><input type="text" name="date_to[]" value="<?php echo $current_date; ?>" class="form-control datepicker dateto" required></td>
            <td style="min-width: 5%"><input type="text" name="total_hours[]" class="form-control hours" required></td>
            <td style="min-width: 10%"><button type="button" onclick="removeRow(this)"><i class="glyphicon glyphicon-trash" style="color: red;"></i></button></td>
        </tr>`;
        $('#asset_table tbody').append(row);
        
        $('.datepicker').datetimepicker({
                format: 'YYYY-MM-DD HH:mm' // Use the PHP variable for the time format only
            }).on('dp.change', calculateHours);


        $('.select2').select2();

       
    }
    
    function updateselecteditem(ths) {
    var selectedOptionText = $(ths).find('option:selected').text();
    var result = selectedOptionText.split('|');
    var row = $(ths).closest('tr');
    row.find('.assetdes').val(result[1]);
    row.find('.serial').val(result[2]);
}

function updateselectedcomitem(ths) {
    var selectedOptionText = $(ths).find('option:selected').text();
    var result = selectedOptionText.split('|');
    var row = $(ths).closest('tr');
    row.find('.comdes').val(result[1]);
    row.find('.comserial').val(result[2]);
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

        var totalHours = hours ;

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

        var totalHours = hours ;

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

        var totalHours = hours ;

        row.find('input[name="hours_edit_com"]').val(totalHours);
    }
}


// $('.datepicker').datetimepicker({
//     format: 'YYYY-MM-DD HH:mm' // Use the consistent date and time format
// }).on('dp.change', calculateHours);

    function add_more_com_row() {
        var row = `<tr>
            <td> <?php echo form_dropdown('com_code[]', $com_arr, ' ', 'class="form-control select2 comcode" onchange ="updateselectedcomitem(this)" required'); ?></td>
            <td><input type="text" name="serial_number[]" class="form-control comserial" required></td>
            <td><input type="text" name="com_description[]" class="form-control comdes" required></td>
             <td><?php echo form_dropdown('thread_condition[]', $thread, ' ', 'class="form-control select2 thread"  required'); ?></td>
            <td><?php echo form_dropdown('physical_condition[]', $physical, ' ', 'class="form-control select2 physical"  required'); ?></td>
            <td><?php echo form_dropdown('status[]', $statuscon, ' ', 'class="form-control select2 statuscon"  required'); ?></td>
            <td><input type="text" name="date_from[]" class="form-control datepicker datefrom" required></td>
            <td><input type="text" name="date_to[]" class="form-control datepicker dateto" required></td>
            <td><input type="text" name="total_hours[]" class="form-control hours" required ></td>
            <td><button type="button" onclick="removeRow(this)"><i class="glyphicon glyphicon-trash" style="color: red;"></i></button></td>
        </tr>`;
        $('#component_table tbody').append(row);
        $('.datepicker').datetimepicker({
                format: 'YYYY-MM-DD HH:mm' // Use the PHP variable for the time format only
            }).on('dp.change', calculateHours);

        $('.select2').select2();
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


    // Send data via AJAX
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
            rigname: result[1],
            well: well,
            assets: assetData,
           coms: comData,
         masterid : MasterID
        },
        url: "<?php echo site_url('fleet/save_uti'); ?>", // Replace 'controller' with your actual controller name
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
}


// function load_uti(MasterId) {
//     if (MasterId) {
       
//         $.ajax({
//             async: true,
//             type: 'post',
//             dataType: 'json',
//             data: {'masterId': MasterId},
//             url: "<?php echo site_url('fleet/load_uti'); ?>",
//             beforeSend: function() {
//                 startLoad();
//             },
//             success: function(data) {
//                 if (!jQuery.isEmptyObject(data)) {
//                     $('#docNumber').val(data['docNumber']);
//                     $('#documentDate').val(data['documentDate']);
//                     $('#jobNumber').val(data['jobNumber']);
//                     $('#description').val(data['description']);
//                     $('#rig').val(data['rig']).change();
//                     $('#well').val(data['well']);
                    
//                     // Clear existing table rows
//                     $('#asset_table tbody').empty();
//                     $('#component_table tbody').empty();
                    
//                     // Populate asset data
//                     data['assets'].forEach(function(asset) {
//                         // var activityCode = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="edit_activityCode_\'+key+\'"'), form_dropdown('activityCode[]', $asset_arr , '', 'class="form-control select2 activityCode"  required"')) ?>';
//                         var row = `<tr data-id="${asset.id}">
                        
//                             <td><input type="text" class="form-control asset" value="${asset.asset_id}"></td>
//                             <td><input type="text" class="form-control serial" value="${asset.serial_number}"></td>
//                             <td><input type="text" class="form-control assetdes" value="${asset.description}"></td>
//                             <td><input type="text" class="form-control thread" value="${asset.thread_condition_id}"></td>
//                             <td><input type="text" class="form-control physical" value="${asset.physical_condition_id}"></td>
//                             <td><input type="text" class="form-control statuscon" value="${asset.status_id}"></td>
//                             <td><input type="text" class="form-control datefrom" value="${asset.date_time_from}"></td>
//                             <td><input type="text" class="form-control dateto" value="${asset.date_time_to}"></td>
//                             <td><input type="text" class="form-control hours" value="${asset.hours}" readonly></td>
                            
//                         </tr>`;
//                         $('#asset_table tbody').append(row);
//                         $('.select2').select2();
//                         $('#edit_activityCode_'+key).val(asset[asset.asset_id]).change();

//                     });
                    
//                     // Populate component data
//                     data['coms'].forEach(function(com) {
//                         var row = `<tr data-id="${com.id}">
                       
//                             <td><input type="text" class="form-control comcode" value="${com.asset_id}"></td>
//                             <td><input type="text" class="form-control comserial" value="${com.serial_number}"></td>
//                             <td><input type="text" class="form-control comdes" value="${com.description}"></td>
//                             <td><input type="text" class="form-control thread" value="${com.thread_condition_id}"></td>
//                             <td><input type="text" class="form-control physical" value="${com.physical_condition_id}"></td>
//                             <td><input type="text" class="form-control datefrom" value="${com.date_time_from}"></td>
//                             <td><input type="text" class="form-control dateto" value="${com.date_time_to}"></td>
//                             <td><input type="text" class="form-control hours" value="${com.hours}" readonly></td>
//                             <td><input type="text" class="form-control statuscon" value="${com.status_id}"></td>
//                         </tr>`;
//                         $('#component_table tbody').append(row);
//                     });

//                     $('#saveButton').html('Update');
//                 }
//                 stopLoad();
//                 refreshNotifications(true);
//             },
//             error: function() {
//                 alert('An error occurred! Please try again.');
//                 stopLoad();
//                 refreshNotifications(true);
//             }
//         });
//     }
// }

function removeRow(button) {
    // Remove the row that contains the clicked button
    $(button).closest('tr').remove();
}

// function load_uti(MasterId) {
//     if (!MasterId) return;

//     $.ajax({
//         async: true,
//         type: 'post',
//         dataType: 'json',
//         data: {'masterId': MasterId},
//         url: "<?php echo site_url('fleet/load_uti'); ?>",
//         beforeSend: startLoad,
//         success: function(data) {

//             if (!jQuery.isEmptyObject(data)) {
//                 populateFields(data);
               
               
//                 // populateTables(data);
//                 $('#saveButton').html('Update');
//             }
//             stopLoad();
//             refreshNotifications(true);
//         },
//         error: function() {
//             alert('An error occurred! Please try again.');
//             stopLoad();
//             refreshNotifications(true);
//         }
//     });
// }
// function doc_view(MasterId) {
//     if (!MasterId) return;

//     $.ajax({
//         async: true,
//         type: 'post',
//         dataType: 'json',
//         data: {'masterId': MasterId},
//         url: "<?php echo site_url('fleet/load_uti'); ?>",
//         beforeSend: startLoad,
//         success: function(data) {

//             if (!jQuery.isEmptyObject(data)) {
//                 populateFields(data);
//                 $.each(data['assets'], function (key, value) {
//                     load_asset_details_edit(value);

//                 });
//                 $.each(data['coms'], function (key, value) {
//                     load_com_details_edit(value);

//                 });
               
//                 // populateTables(data);
//                 $('#saveButton').html('Cancel');
//             }
//             stopLoad();
//             refreshNotifications(true);
//         },
//         error: function() {
//             alert('An error occurred! Please try again.');
//             stopLoad();
//             refreshNotifications(true);
//         }
//     });
// }
// function populateFields(data) {
//     $('#docNumber').val(data['docNumber']);
//     $('#documentDate').val(data['documentDate']);
//     $('#jobNumber').val(data['jobNumber']);
//     $('#description').val(data['description']);
//     $('#rig').val(data['rig']).change();
//     $('#well').val(data['well']);
// }

// function populateTables(data) {
//     $('#asset_table tbody').empty();
//     $('#component_table tbody').empty();

//     data['assets'].forEach(function(asset, key) {
//         var activityCode = '<?php echo str_replace(array("\n", "<select"), array("", "<select id=\"edit_activityCode_'+key+'\""), form_dropdown("activityCode[]", $asset_arr, "", "class=\"form-control select2 activityCode\" required")) ?>';
//         var row = createRow(asset, activityCode, key, 'asset');
//         $('#asset_table tbody').append(row);
//         initializeSelect2(key, asset['assetcode']);
//     });

//     data['coms'].forEach(function(com) {
//         var row = createRow(com, '', 0, 'component');
//         $('#component_table tbody').append(row);
//     });
// }

// function createRow(item, activityCode, key, type) {
//     var commonFields = `
//         <td><input type="text" class="form-control serial" value="${item.serial_number}"></td>
//         <td><input type="text" class="form-control assetdes" value="${item.description}"></td>
//         <td><input type="text" class="form-control thread" value="${item.thread_condition_id}"></td>
//         <td><input type="text" class="form-control physical" value="${item.physical_condition_id}"></td>
//         <td><input type="text" class="form-control statuscon" value="${item.status_id}"></td>
//         <td><input type="text" class="form-control datefrom" value="${item.date_time_from}"></td>
//         <td><input type="text" class="form-control dateto" value="${item.date_time_to}"></td>
//         <td><input type="text" class="form-control hours" value="${item.hours}" readonly></td>
//     `;

//     return type === 'asset' ? 
//         `<tr data-id="${item.id}">
//             <td>${activityCode}</td>
//             ${commonFields}
//         </tr>` :
//         `<tr data-id="${item.id}">
//             <td><input type="text" class="form-control comcode" value="${item.asset_id}"></td>
//             ${commonFields}
//         </tr>`;
// }

// function initializeSelect2(key, assetId) {
//     $('.select2').select2();
//     $('#edit_activityCode_' + key).val(assetId).change();
// }

// function clearFormAndTables() {
//         $('#docNumber').val('');
//         $('#documentDate').val('');
//         $('#jobNumber').val('');
//         $('#description').val('');
//         $('#rig').val('').change();
//         $('#well').val('');
        
//         $('#asset_table tbody').empty();
//         $('#component_table tbody').empty();
//     }  

  function submit_uti() {
    var masterId = $('#masterId').val(); // Assuming you have the master ID stored in a hidden input field

        $.ajax({
            type: 'post',
            url: "<?php echo site_url('fleet/submit_uti'); ?>",
            data: { masterId: MasterID },
        dataType: 'json',
        success: function(data) {
            if (data[0] === 's') {
                swal("Success", data[1], "success");
                refreshNotifications(true);
                generateDocNumber();
             
                fetchPage('system/Fleet_Management/fleet_utilization', '', 'Asset Utilization');
            } else if (data[0] === 'i') {
                swal("Info", data[1], "info");
            } else {
                swal("Error", data[1], "error");
            }
        },
        error: function() {
            swal("Cancelled", "Try Again", "error");
        }
        });
}
  
    
// function disableEditing() {
//     $('input, select, textarea, button').prop('disabled', true);
// }


function edit_uti(MasterId) {
    if (!MasterId) return;

    $.ajax({
        async: true,
        type: 'post',
        dataType: 'html',
        data: {'masterId': MasterId},
        url: "<?php echo site_url('Fleet/edit_uti_view'); ?>",
        beforeSend: startLoad,
        success: function(data) {
            $('#asset_table tbody').html('');
            $('#asset_table tbody').append(data);
            // $('#saveButton').html('Update');
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
        data: {'masterId': MasterId},
        url: "<?php echo site_url('Fleet/edit_com_uti_view'); ?>",
        beforeSend: startLoad,
        success: function(data) {
            $('#component_table tbody').html(''); 
            $('#component_table tbody').append(data);
            // $('#saveButton').html('Update');
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
function editasset_line_record(id)
{
  
    $("#asset_edit_master_id").val(id);
   
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'id': id},
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
           
            $('.datepicker_edit').datetimepicker({
                format: 'YYYY-MM-DD HH:mm' // Use the PHP variable for the time format only
            }).on('dp.change', calculateHours_edit);

            $("#asset_edit_base_modal").modal({backdrop: "static"});
          

        // $('.select2').select2();
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
function editcomponent_line_record(id)
{
  
    $("#component_edit_master_id").val(id);
   
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'id': id},
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
            $('.datepicker_edit').datetimepicker({
                format: 'YYYY-MM-DD HH:mm' // Use the PHP variable for the time format only
            }).on('dp.change', calculateHours_edit_com);

           
            $("#component_edit_base_modal").modal({backdrop: "static"});
        // $('.select2').select2();
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
// function edit_field_uti(MasterId) {
//     if (!MasterId) return;

//     $.ajax({
//         async: true,
//         type: 'post',
//         dataType: 'html',
//         data: {'masterId': MasterId},
//         url: "<?php echo site_url('Fleet/edit_com_uti'); ?>",
//         beforeSend: startLoad,
//         success: function(data) {
//             // $('#saveButton').html('Update');
//             $('#component_table tbody').append(data);

//             $('.datepicker').datetimepicker({
//                 format: 'YYYY-MM-DD HH:mm' // Use the PHP variable for the time format only
//             }).on('dp.change', calculateHours);

//         $('.select2').select2();
//             stopLoad();
//             refreshNotifications(true);
//         },
//         error: function() {
//             alert('An error occurred! Please try again.');
//             stopLoad();
//             refreshNotifications(true);
//         }
//     });
// }


function save_asset_line_item_edit() {
        var data = $('#asset_edit_addon_form').serializeArray();
       
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Fleet/save_asset_line_item_edit'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
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
 
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
       
    }
    function save_com_line_item_edit() {
        var data = $('#component_edit_addon_form').serializeArray();
       
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Fleet/save_com_line_item_edit'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
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
 
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
       
    }
    function fetch_doc_header_detail(MasterID)
    {
        $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'masterId': MasterID},

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
              title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
              text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
              cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
          },
          function () {
              $.ajax({
                  async: true,
                  url: "<?php echo site_url('Fleet/delete_component_line_record'); ?>",
                  type: 'post',
                  dataType: 'json',
                  data: {'id': id},
                  beforeSend: function () {
                      startLoad();
                  },
                  success: function (data) {
                      stopLoad();
                      myAlert(data[0], data[1]);
                      if (data[0] == 's') {
                         edit_component_uti(MasterID);
                      }
                  }, error: function () {
                      stopLoad();
                      myAlert('e', 'error');
                  }
              });
          }
      );
    
}
function deleteasset_line_record(id) {
        swal({
              title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
              text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
              cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
          },
          function () {
              $.ajax({
                  async: true,
                  url: "<?php echo site_url('Fleet/delete_asset_line_record'); ?>",
                  type: 'post',
                  dataType: 'json',
                  data: {'id': id},
                  beforeSend: function () {
                      startLoad();
                  },
                  success: function (data) {
                      stopLoad();
                      myAlert(data[0], data[1]);
                      if (data[0] == 's') {
                        edit_uti(MasterID);
                      }
                  }, error: function () {
                      stopLoad();
                      myAlert('e', 'error');
                  }
              });
          }
      );
    
}
    </script>


