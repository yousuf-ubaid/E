<?php


$documentCode = isset($headerData->documentCode) ? $headerData->documentCode : '';
$documentMasterAutoID = isset($headerData->documentMasterAutoID) ? '/' . str_pad($headerData->documentMasterAutoID, 3, '0', STR_PAD_LEFT) : '';
$templateNameFormatted = isset($templateName) ? ' - ' . $templateName : '';


$vehicleDetails = '';
if (isset($headerData->vehicleCode) && isset($headerData->vehDescription) && isset($headerData->SerialNo))
{
   $vehicleDetails = $headerData->vehicleCode . ' | ' . $headerData->vehDescription . ' | ' . $headerData->SerialNo;
}


$title = trim("$documentCode $documentMasterAutoID $templateNameFormatted");

echo head_page($title, false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$employeedrop = all_employee_drop();
$headerData = isset($headerData);
$documentTemplateID = isset($id);
?>

<style>
   body {
      font-family: Arial, sans-serif;
   }

   .tables-wrapper {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
   }

   .table-container {
      width: 48%;
      margin-bottom: 20px;
   }

   table {
      width: 100%;
      border-collapse: collapse;
      border: 1;
   }

   table,
   th,
   td {
      border: 1px solid #ddd;
   }

   th,
   td {
      padding: 12px;
      text-align: center;
   }

   th {
      background-color: whitesmoke;
      color: black;
   }

   input[type="text"] {
      width: 100%;
   }
</style>

<form role="form" id="template_form" class="form-horizontal">
   <header class="head-title">
      <h5>
         <?php echo $vehicleDetails ?: ''; ?>
      </h5>
   </header>
   <table id="tv_3_Motor_Serial_Number_table">
      <tr>
         <th>Motor Serial Number</th>
         <td><input type="text" id="tv_3_Motor_Serial_Number"></td>
      </tr>
   </table>
   <br>

   <table id="tv_3_component_condition_table">
      <tr>
         <th>Description</th>
         <th>Serial Number</th>
         <th>Thread Condition</th>
         <th>Physical Condition</th>
         <th>Status</th>
         <th>Used Hours</th>
      </tr>

      <!-- Rows with input fields -->
      <tr>
         <th>BEARING MANDREL</th>
         <td><input type="text" id="tv_3_bearing_mandrel_serial_number"></td>
         <td><input type="text" id="tv_3_bearing_mandrel_thread_condition"></td>
         <td><input type="text" id="tv_3_bearing_mandrel_physical_condition"></td>
         <td><input type="text" id="tv_3_bearing_mandrel_status"></td>
         <td><input type="text" id="tv_3_bearing_mandrel_used_hours"></td>
      </tr>

      <tr>
         <th>SHAFT COMPRESSION NUT</th>
         <td><input type="text" id="tv_3_shaft_compression_nut_serial_number"></td>
         <td><input type="text" id="tv_3_shaft_compression_nut_thread_condition"></td>
         <td><input type="text" id="tv_3_shaft_compression_nut_physical_condition"></td>
         <td><input type="text" id="tv_3_shaft_compression_nut_status"></td>
         <td><input type="text" id="tv_3_shaft_compression_nut_used_hours"></td>
      </tr>

      <tr>
         <th>MANDREL TORQUE TWIN NUT</th>
         <td><input type="text" id="tv_3_mandrel_torque_twin_nut_serial_number"></td>
         <td><input type="text" id="tv_3_mandrel_torque_twin_nut_thread_condition"></td>
         <td><input type="text" id="tv_3_mandrel_torque_twin_nut_physical_condition"></td>
         <td><input type="text" id="tv_3_mandrel_torque_twin_nut_status"></td>
         <td><input type="text" id="tv_3_mandrel_torque_twin_nut_used_hours"></td>
      </tr>
      <tr>
         <th>THRUST HOUSING</th>
         <td><input type="text" id="tv_3_thrust_housing_serial_number"></td>
         <td><input type="text" id="tv_3_thrust_housing_thread_condition"></td>
         <td><input type="text" id="tv_3_thrust_housing_physical_condition"></td>
         <td><input type="text" id="tv_3_thrust_housing_status"></td>
         <td><input type="text" id="tv_3_thrust_housing_used_hours"></td>
      </tr>
      <tr>
         <th>HOUSING ADAPTER</th>
         <td><input type="text" id="tv_3_housing_adapter_serial_number"></td>
         <td><input type="text" id="tv_3_housing_adapter_thread_condition"></td>
         <td><input type="text" id="tv_3_housing_adapter_physical_condition"></td>
         <td><input type="text" id="tv_3_housing_adapter_status"></td>
         <td><input type="text" id="tv_3_housing_adapter_used_hours"></td>
      </tr>
      <tr>
         <th>BEARING ADAPTER SEAL CAP</th>
         <td><input type="text" id="tv_3_bearing_adapter_seal_cap_serial_number"></td>
         <td><input type="text" id="tv_3_bearing_adapter_seal_cap_thread_condition"></td>
         <td><input type="text" id="tv_3_bearing_adapter_seal_cap_physical_condition"></td>
         <td><input type="text" id="tv_3_bearing_adapter_seal_cap_status"></td>
         <td><input type="text" id="tv_3_bearing_adapter_seal_cap_used_hours"></td>
      </tr>
      <tr>
         <th>ROTOR ADAPTER SEAL CAP</th>
         <td><input type="text" id="tv_3_rotor_adapter_seal_cap_serial_number"></td>
         <td><input type="text" id="tv_3_rotor_adapter_seal_cap_thread_condition"></td>
         <td><input type="text" id="tv_3_rotor_adapter_seal_cap_physical_condition"></td>
         <td><input type="text" id="tv_3_rotor_adapter_seal_cap_status"></td>
         <td><input type="text" id="tv_3_rotor_adapter_seal_cap_used_hours"></td>
      </tr>
      <tr>
         <th>OFFSET HOUSING</th>
         <td><input type="text" id="tv_3_offset_housing_serial_number"></td>
         <td><input type="text" id="tv_3_offset_housing_thread_condition"></td>
         <td><input type="text" id="tv_3_offset_housing_physical_condition"></td>
         <td><input type="text" id="tv_3_offset_housing_status"></td>
         <td><input type="text" id="tv_3_offset_housing_used_hours"></td>
      </tr>
      <tr>
         <th>SPLINE MANDREL</th>
         <td><input type="text" id="tv_3_spline_mandrel_serial_number"></td>
         <td><input type="text" id="tv_3_spline_mandrel_thread_condition"></td>
         <td><input type="text" id="tv_3_spline_mandrel_physical_condition"></td>
         <td><input type="text" id="tv_3_spline_mandrel_status"></td>
         <td><input type="text" id="tv_3_spline_mandrel_used_hours"></td>
      </tr>
      <tr>
         <th>LOCK HOUSING</th>
         <td><input type="text" id="tv_3_lock_housing_serial_number"></td>
         <td><input type="text" id="tv_3_lock_housing_thread_condition"></td>
         <td><input type="text" id="tv_3_lock_housing_physical_condition"></td>
         <td><input type="text" id="tv_3_lock_housing_status"></td>
         <td><input type="text" id="tv_3_lock_housing_used_hours"></td>
      </tr>
      <tr>
         <th>ROTOR CATCH STEM, 23.5"</th>
         <td><input type="text" id="tv_3_rotor_catch_stem_serial_number"></td>
         <td><input type="text" id="tv_3_rotor_catch_stem_thread_condition"></td>
         <td><input type="text" id="tv_3_rotor_catch_stem_physical_condition"></td>
         <td><input type="text" id="tv_3_rotor_catch_stem_status"></td>
         <td><input type="text" id="tv_3_rotor_catch_stem_used_hours"></td>
      </tr>
   </table>
   <br>

   <div class="tables-wrapper">
      <!-- First Table -->
      <div class="table-container">
         <table id="tv_3_lower_housing_table">
            <tr>
               <th colspan="2">LOWER HOUSING</th>
               <td colspan="2"><input type="text" id="tv_3_lower_housing"></td>
            </tr>
            <tr>
               <th rowspan="2">ID</th>
               <td><input type="text" id="tv_3_lower_housing_id_1"></td>
               <td><input type="text" id="tv_3_lower_housing_id_2"></td>
               <td><input type="text" id="tv_3_lower_housing_id_3"></td>
            </tr>
            <tr>
               <td><input type="text" id="tv_3_lower_housing_id_4"></td>
               <td><input type="text" id="tv_3_lower_housing_id_5"></td>
               <td><input type="text" id="tv_3_lower_housing_id_6"></td>
            </tr>
            <tr>
               <th>LOWEST OD:</th>
               <td><input type="text" id="tv_3_lower_housing_lowest_od"></td>
               <th>LARGEST OD:</th>
               <td><input type="text" id="tv_3_lower_housing_largest_od"></td>
            </tr>
            <tr>
               <th colspan="3">SMALLEST CLEARANCE (0.012'' - 0.040'')</th>
               <td><input type="text" id="tv_3_lower_housing_smallest_clearance"></td>
            </tr>
         </table>
      </div>

      <!-- Second Table -->
      <div class="table-container">
         <table id="tv_3_upper_housing_table">
            <tr>
               <th colspan="2">UPPER HOUSING</th>
               <td colspan="2"><input type="text" id="tv_3_upper_housing"></td>
            </tr>
            <tr>
               <th rowspan="2">ID</th>
               <td><input type="text" id="tv_3_upper_housing_id_1"></td>
               <td><input type="text" id="tv_3_upper_housing_id_2"></td>
               <td><input type="text" id="tv_3_upper_housing_id_3"></td>
            </tr>
            <tr>
               <td><input type="text" id="tv_3_upper_housing_id_4"></td>
               <td><input type="text" id="tv_3_upper_housing_id_5"></td>
               <td><input type="text" id="tv_3_upper_housing_id_6"></td>
            </tr>
            <tr>
               <th>LOWEST OD:</th>
               <td><input type="text" id="tv_3_upper_housing_lowest_od"></td>
               <th>LARGEST OD:</th>
               <td><input type="text" id="tv_3_upper_housing_largest_od"></td>
            </tr>
            <tr>
               <th colspan="3">SMALLEST CLEARANCE (0.012'' - 0.040'')</th>
               <td><input type="text" id="tv_3_upper_housing_smallest_clearance"></td>
            </tr>
         </table>
      </div>

      <!-- Third Table -->
      <div class="table-container">
         <table id="tv_3_housing_cover_table">
            <tr>
               <th colspan="2">HOUSING COVER</th>
               <td colspan="2"><input type="text" id="tv_3_housing_cover"></td>
            </tr>
            <tr>
               <th rowspan="2">ID</th>
               <td><input type="text" id="tv_3_housing_cover_id_1"></td>
               <td><input type="text" id="tv_3_housing_cover_id_2"></td>
               <td><input type="text" id="tv_3_housing_cover_id_3"></td>
            </tr>
            <tr>
               <td><input type="text" id="tv_3_housing_cover_id_4"></td>
               <td><input type="text" id="tv_3_housing_cover_id_5"></td>
               <td><input type="text" id="tv_3_housing_cover_id_6"></td>
            </tr>
            <tr>
               <th>LOWEST OD:</th>
               <td><input type="text" id="tv_3_housing_cover_lowest_od"></td>
               <th>LARGEST OD:</th>
               <td><input type="text" id="tv_3_housing_cover_largest_od"></td>
            </tr>
            <tr>
               <th colspan="3">SMALLEST CLEARANCE (0.012'' - 0.040'')</th>
               <td><input type="text" id="tv_3_housing_cover_smallest_clearance"></td>
            </tr>
         </table>
      </div>

      <!-- Fourth Table -->
      <div class="table-container">
         <table id="tv_3_upper_shaft_flow_restrictor_table">
            <tr>
               <th colspan="2">UPPER SHAFT FLOW RESTRICTOR</th>
               <td colspan="2"><input type="text" id="tv_3_upper_shaft_flow_restrictor" /></td>
            </tr>
            <tr>
               <th rowspan="2">OD</th>
               <td><input type="text" id="tv_3_upper_shaft_id_1" /></td>
               <td><input type="text" id="tv_3_upper_shaft_id_2" /></td>
               <td><input type="text" id="tv_3_upper_shaft_id_3" /></td>
            </tr>
            <tr>
               <td><input type="text" id="tv_3_upper_shaft_id_4" /></td>
               <td><input type="text" id="tv_3_upper_shaft_id_5" /></td>
               <td><input type="text" id="tv_3_upper_shaft_id_6" /></td>
            </tr>
            <tr>
               <th>LOWEST OD:</th>
               <td><input type="text" id="tv_3_upper_shaft_lowest_od" /></td>
               <th>LARGEST OD:</th>
               <td><input type="text" id="tv_3_upper_shaft_largest_od" /></td>
            </tr>
            <tr>
               <th colspan="3">LARGEST CLEARANCE (0.012'' - 0.040'')</th>
               <td><input type="text" id="tv_3_upper_shaft_largest_clearance" /></td>
            </tr>
         </table>
      </div>
   </div>
   <br>

   <table id="tv_3_component_condition_table_2">
      <tr>
         <th>Description</th>
         <th>Serial Number</th>
         <th>OD</th>
         <th>Thread Condition</th>
         <th>Hard Surface Condition</th>
         <th>Status</th>
      </tr>

      <!-- Rows with input fields -->
      <tr>
         <th>ADJUSTING RING</th>
         <td><input type="text" id="tv_3_adjusting_ring_serial_number"></td>
         <td><input type="text" id="tv_3_adjusting_ring_od"></td>
         <td><input type="text" id="tv_3_adjusting_ring_thread_condition"></td>
         <td><input type="text" id="tv_3_adjusting_ring_hard_surface_condition"></td>
         <td><input type="text" id="tv_3_adjusting_ring_status"></td>
      </tr>

      <tr>
         <th>STABILIZER SLEEVE</th>
         <td><input type="text" id="tv_3_stabilizer_sleeve_serial_number"></td>
         <td><input type="text" id="tv_3_stabilizer_sleeve_od"></td>
         <td><input type="text" id="tv_3_stabilizer_sleeve_thread_condition"></td>
         <td><input type="text" id="tv_3_stabilizer_sleeve_hard_surface_condition"></td>
         <td><input type="text" id="tv_3_stabilizer_sleeve_status"></td>
      </tr>
   </table>
   <br>

   <table id="tv_3_web_width_table">
      <tr>
         <th>Description</th>
         <th>Serial Number</th>
         <th colspan="3">WEB WIDTH (0.4"; Tolerance: 0.133")</th>
      </tr>

      <tr>
         <th rowspan="2">DRIVE SHAFT</th>
         <td rowspan="2"><input type="text" id="tv_3_drive_shaft_serial_number"></td>
         <td><input type="text" id="tv_3_drive_shaft_web_width_1"></td>
         <td><input type="text" id="tv_3_drive_shaft_web_width_2"></td>
         <td><input type="text" id="tv_3_drive_shaft_web_width_3"></td>
      </tr>
      <tr>
         <td><input type="text" id="tv_3_drive_shaft_web_width_4"></td>
         <td><input type="text" id="tv_3_drive_shaft_web_width_5"></td>
         <td><input type="text" id="tv_3_drive_shaft_web_width_6"></td>
      </tr>
      <tr>
         <th rowspan="2">BEARING ADAPTER</th>
         <td rowspan="2"><input type="text" id="tv_3_bearing_adapter_serial_number"></td>
         <td><input type="text" id="tv_3_bearing_adapter_web_width_1"></td>
         <td><input type="text" id="tv_3_bearing_adapter_web_width_2"></td>
         <td><input type="text" id="tv_3_bearing_adapter_web_width_3"></td>
      </tr>
      <tr>
         <td><input type="text" id="tv_3_bearing_adapter_web_width_4"></td>
         <td><input type="text" id="tv_3_bearing_adapter_web_width_5"></td>
         <td><input type="text" id="tv_3_bearing_adapter_web_width_6"></td>
      </tr>
      <tr>
         <th rowspan="2">ROTOR ADAPTER</th>
         <td rowspan="2"><input type="text" id="tv_3_rotor_adapter_serial_number"></td>
         <td><input type="text" id="tv_3_rotor_adapter_web_width_1"></td>
         <td><input type="text" id="tv_3_rotor_adapter_web_width_2"></td>
         <td><input type="text" id="tv_3_rotor_adapter_web_width_3"></td>
      </tr>
      <tr>
         <td><input type="text" id="tv_3_rotor_adapter_web_width_4"></td>
         <td><input type="text" id="tv_3_rotor_adapter_web_width_5"></td>
         <td><input type="text" id="tv_3_rotor_adapter_web_width_6"></td>
      </tr>
   </table>
   <br>

   <table id="tv_3_measured_approved_table">
      <tr>
         <th>Measured/Checked By:</th>
         <td><input type="text" id="tv_3_Measured_by"></td>
         <th>Approved By:</th>
         <td><input type="text" id="tv_3_Approved_by"></td>
      </tr>
      <tr>
         <th>Date</th>
         <td><input type="date" id="tv_3_Measured_date"></td>
         <th>Date</th>
         <td><input type="date" id="tv_3_Approved_date"></td>
      </tr>
   </table>

   <br>
   <button type="submit" class="btn btn-primary pull-right">Save</button>
</form>

<script type="text/javascript">
   $(document).ready(function() {
      var documentTemplateID = '<?php echo isset($id) ? $id : ''; ?>';
      var existingData = <?php echo isset($existingData) ? json_encode($existingData) : 'null'; ?>;
      if (existingData) {
         existingData.forEach(function(item) {
            var inputId = item.uniqueKeyID;
            var inputValue = item.contentValue;
            $('#' + inputId).val(inputValue);
         });
      }

      $('#template_form').on('submit', function(e) {
         e.preventDefault();
         var formData = {};
         var inputIds = [];
         var hasDuplicates = false;

         $(this).find(':input[data-required="true"]').each(function() {
            var inputId = $(this).attr('id');
            var inputValue = $(this).val();

            if (!inputValue) { 
               hasErrors = true;
               $(this).addClass('error'); 
            } else {
               $(this).removeClass('error'); 
            }
         });

        
         if (hasErrors) {
            myAlert('e', 'Error: Please fill out all required fields.');
            return;
         }

         
         $(this).find(':input').each(function() {
            var inputId = $(this).attr('id');
            var inputValue = $(this).val();

            if (inputId) {
               if (inputIds.includes(inputId)) {
                  hasDuplicates = true;
               }
               inputIds.push(inputId);
               formData[inputId] = inputValue;
            }
         });


         if (hasDuplicates) {
            myAlert('e', 'Error: Some unique key IDs are repeated.');
            return;
         }


         if (documentTemplateID) {
            $.ajax({
               url: '<?php echo site_url("Fleet/save_document_templates_details"); ?>',
               type: 'POST',
               data: {
                  documentTemplateID: documentTemplateID,
                  formData: formData
               },
               dataType: 'json',
               beforeSend: function() {
                  startLoad();
               },
               success: function(response) {
                  stopLoad();
                  myAlert(response[0], response[1]);
                  refreshNotifications(true);
                  fetchPage('system/Fleet_Management/fleet_utilization', '', 'Asset Utilization');
               },
               error: function() {
                  stopLoad();
                  myAlert('Error occurred', 'error');
                  refreshNotifications(true);
               }
            });
         } else if (existingData) {
            var editDocumentTemplateID = existingData[0].documentTemplateID;

            $.ajax({
               url: '<?php echo site_url("Fleet/edit_document_templates_details"); ?>',
               type: 'POST',
               data: {
                  documentTemplateID: editDocumentTemplateID,
                  formData: formData
               },
               dataType: 'json',
               beforeSend: function() {
                  startLoad();
               },
               success: function(response) {
                  stopLoad();
                  myAlert(response[0], response[1]);
                  refreshNotifications(true);
                  fetchPage('system/Fleet_Management/fleet_utilization', '', 'Asset Utilization');
               },
               error: function() {
                  stopLoad();
                  myAlert('Error occurred', 'error');
                  refreshNotifications(true);
               }
            });
         }
      });
   });
</script>