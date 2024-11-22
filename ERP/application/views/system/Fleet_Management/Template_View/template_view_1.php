<?php


$documentCode = isset($headerData->documentCode) ? $headerData->documentCode : '';
$documentMasterAutoID = isset($headerData->documentMasterAutoID) ? '/' . str_pad($headerData->documentMasterAutoID, 3, '0', STR_PAD_LEFT) : '';
$templateNameFormatted = isset($templateName) ? ' - ' . $templateName : '';


$vehicleDetails = '';
if (isset($headerData->vehicleCode) && isset($headerData->vehDescription) && isset($headerData->SerialNo)) {
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
      text-align: left;
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
   <table id="tv_1_Motor_Serial_Number_table">
      <tr>
         <th>Motor Serial Number</th>
         <td><input type="text" id="tv_1_Motor_Serial_Number"></td>
      </tr>
   </table>
   <br>

   <table id="tv_1_component_condition_table">
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
         <th>DRIVE SHAFT</th>
         <td><input type="text" id="tv_1_drive_shaft_serial_number"></td>
         <td><input type="text" id="tv_1_drive_shaft_thread_condition"></td>
         <td><input type="text" id="tv_1_drive_shaft_physical_condition"></td>
         <td><input type="text" id="tv_1_drive_shaft_status"></td>
         <td><input type="text" id="tv_1_drive_shaft_used_hours"></td>
      </tr>

      <tr>
         <th>LOWER MALE R BEARING</th>
         <td><input type="text" id="tv_1_lower_male_bearing_serial_number"></td>
         <td><input type="text" id="tv_1_lower_male_bearing_thread_condition"></td>
         <td><input type="text" id="tv_1_lower_male_bearing_physical_condition"></td>
         <td><input type="text" id="tv_1_lower_male_bearing_status"></td>
         <td><input type="text" id="tv_1_lower_male_bearing_used_hours"></td>
      </tr>

      <!-- Repeat the rows for a total of 14 rows -->
      <tr>
         <th>LOWER FEMALE R BEARING</th>
         <td><input type="text" id="tv_1_lower_female_bearing_serial_number"></td>
         <td><input type="text" id="tv_1_lower_female_bearing_thread_condition"></td>
         <td><input type="text" id="tv_1_lower_female_bearing_physical_condition"></td>
         <td><input type="text" id="tv_1_lower_female_bearing_status"></td>
         <td><input type="text" id="tv_1_lower_female_bearing_used_hours"></td>
      </tr>
      <tr>
         <th>LOWER BEARING HOUSING</th>
         <td><input type="text" id="tv_1_lower_bearing_housing_serial_number"></td>
         <td><input type="text" id="tv_1_lower_bearing_housing_thread_condition"></td>
         <td><input type="text" id="tv_1_lower_bearing_housing_physical_condition"></td>
         <td><input type="text" id="tv_1_lower_bearing_housing_status"></td>
         <td><input type="text" id="tv_1_lower_bearing_housing_used_hours"></td>
      </tr>
      <tr>
         <th>UPPER BEARING HOUSING</th>
         <td><input type="text" id="tv_1_upper_bearing_housing_serial_number"></td>
         <td><input type="text" id="tv_1_upper_bearing_housing_thread_condition"></td>
         <td><input type="text" id="tv_1_upper_bearing_housing_physical_condition"></td>
         <td><input type="text" id="tv_1_upper_bearing_housing_status"></td>
         <td><input type="text" id="tv_1_upper_bearing_housing_used_hours"></td>
      </tr>
      <tr>
         <th>FLOW DIVERTER</th>
         <td><input type="text" id="tv_1_flow_diverter_serial_number"></td>
         <td><input type="text" id="tv_1_flow_diverter_thread_condition"></td>
         <td><input type="text" id="tv_1_flow_diverter_physical_condition"></td>
         <td><input type="text" id="tv_1_flow_diverter_status"></td>
         <td><input type="text" id="tv_1_flow_diverter_used_hours"></td>
      </tr>
      <tr>
         <th>LOWER ABH</th>
         <td><input type="text" id="tv_1_lower_abh_serial_number"></td>
         <td><input type="text" id="tv_1_lower_abh_thread_condition"></td>
         <td><input type="text" id="tv_1_lower_abh_physical_condition"></td>
         <td><input type="text" id="tv_1_lower_abh_status"></td>
         <td><input type="text" id="tv_1_lower_abh_used_hours"></td>
      </tr>
      <tr>
         <th>UPPER ABH</th>
         <td><input type="text" id="tv_1_upper_abh_serial_number"></td>
         <td><input type="text" id="tv_1_upper_abh_thread_condition"></td>
         <td><input type="text" id="tv_1_upper_abh_physical_condition"></td>
         <td><input type="text" id="tv_1_upper_abh_status"></td>
         <td><input type="text" id="tv_1_upper_abh_used_hours"></td>
      </tr>
      <tr>
         <th>SPACER RING</th>
         <td><input type="text" id="tv_1_spacer_ring_serial_number"></td>
         <td><input type="text" id="tv_1_spacer_ring_thread_condition"></td>
         <td><input type="text" id="tv_1_spacer_ring_physical_condition"></td>
         <td><input type="text" id="tv_1_spacer_ring_status"></td>
         <td><input type="text" id="tv_1_spacer_ring_used_hours"></td>
      </tr>
      <tr>
         <th>LOWER DRIVER</th>
         <td><input type="text" id="tv_1_lower_driver_serial_number"></td>
         <td><input type="text" id="tv_1_lower_driver_thread_condition"></td>
         <td><input type="text" id="tv_1_lower_driver_physical_condition"></td>
         <td><input type="text" id="tv_1_lower_driver_status"></td>
         <td><input type="text" id="tv_1_lower_driver_used_hours"></td>
      </tr>
      <tr>
         <th>CENTER DRIVE</th>
         <td><input type="text" id="tv_1_center_drive_serial_number"></td>
         <td><input type="text" id="tv_1_center_drive_thread_condition"></td>
         <td><input type="text" id="tv_1_center_drive_physical_condition"></td>
         <td><input type="text" id="tv_1_center_drive_status"></td>
         <td><input type="text" id="tv_1_center_drive_used_hours"></td>
      </tr>
      <tr>
         <th>UPPER DRIVER</th>
         <td><input type="text" id="tv_1_upper_driver_serial_number"></td>
         <td><input type="text" id="tv_1_upper_driver_thread_condition"></td>
         <td><input type="text" id="tv_1_upper_driver_physical_condition"></td>
         <td><input type="text" id="tv_1_upper_driver_status"></td>
         <td><input type="text" id="tv_1_upper_driver_used_hours"></td>
      </tr>
      <tr>
         <th>UPPER DRIVE BOX</th>
         <td><input type="text" id="tv_1_upper_drive_box_serial_number"></td>
         <td><input type="text" id="tv_1_upper_drive_box_thread_condition"></td>
         <td><input type="text" id="tv_1_upper_drive_box_physical_condition"></td>
         <td><input type="text" id="tv_1_upper_drive_box_status"></td>
         <td><input type="text" id="tv_1_upper_drive_box_used_hours"></td>
      </tr>
      <tr>
         <th>TOP SUB</th>
         <td><input type="text" id="tv_1_top_sub_serial_number"></td>
         <td><input type="text" id="tv_1_top_sub_thread_condition"></td>
         <td><input type="text" id="tv_1_top_sub_physical_condition"></td>
         <td><input type="text" id="tv_1_top_sub_status"></td>
         <td><input type="text" id="tv_1_top_sub_used_hours"></td>
      </tr>
   </table>
   <br>

   <div class="tables-wrapper">
      <!-- LOWER FEMALE R BEARING Details -->
      <div class="table-container">
         <table id="tv_1_lower_female_bearing_details_table">
            <tr>
               <th colspan="2">LOWER FEMALE R BEARING</th>
               <td colspan="2"><input type="text" id="tv_1_lower_female_bearing_details" /></td>
            </tr>
            <tr>
               <th rowspan="2">ID</th>
               <td><input type="text" id="tv_1_lower_female_bearing_id_1" /></td>
               <td><input type="text" id="tv_1_lower_female_bearing_id_2" /></td>
               <td><input type="text" id="tv_1_lower_female_bearing_id_3" /></td>
            </tr>
            <tr>
               <td><input type="text" id="tv_1_lower_female_bearing_id_4" /></td>
               <td><input type="text" id="tv_1_lower_female_bearing_id_5" /></td>
               <td><input type="text" id="tv_1_lower_female_bearing_id_6" /></td>
            </tr>
            <tr>
               <th>LOWEST ID:</th>
               <td><input type="text" id="tv_1_lower_female_bearing_lowest_id" /></td>
               <th>LARGEST ID:</th>
               <td><input type="text" id="tv_1_lower_female_bearing_largest_id" /></td>
            </tr>
            <tr>
               <th colspan="3">SMALLEST CLEARANCE (0.012'' - 0.070'')</th>
               <td><input type="text" id="tv_1_lower_female_bearing_smallest_clearance" /></td>
            </tr>
         </table>
      </div>

      <!-- LOWER MALE R BEARING Details -->
      <div class="table-container">
         <table id="tv_1_lower_male_bearing_details_table">
            <tr>
               <th colspan="2">LOWER MALE R BEARING</th>
               <td colspan="2"><input type="text" id="tv_1_lower_male_bearing_details" /></td>
            </tr>
            <tr>
               <th rowspan="2">OD</th>
               <td><input type="text" id="tv_1_lower_male_bearing_od_1" /></td>
               <td><input type="text" id="tv_1_lower_male_bearing_od_2" /></td>
               <td><input type="text" id="tv_1_lower_male_bearing_od_3" /></td>
            </tr>
            <tr>
               <td><input type="text" id="tv_1_lower_male_bearing_od_4" /></td>
               <td><input type="text" id="tv_1_lower_male_bearing_od_5" /></td>
               <td><input type="text" id="tv_1_lower_male_bearing_od_6" /></td>
            </tr>
            <tr>
               <th>LOWEST OD:</th>
               <td><input type="text" id="tv_1_lower_male_bearing_lowest_od" /></td>
               <th>LARGEST OD:</th>
               <td><input type="text" id="tv_1_lower_male_bearing_largest_od" /></td>
            </tr>
            <tr>
               <th colspan="3">LARGEST CLEARANCE (0.012'' - 0.070'')</th>
               <td><input type="text" id="tv_1_lower_male_bearing_largest_clearance" /></td>
            </tr>
         </table>
      </div>

      <!-- UPPER ABH Details -->
      <div class="table-container">
         <table id="tv_1_upper_abh_details_table">
            <tr>
               <th colspan="2">UPPER ABH</th>
               <td colspan="2"><input type="text" id="tv_1_upper_abh_details" /></td>
            </tr>
            <tr>
               <th rowspan="2">ID</th>
               <td><input type="text" id="tv_1_upper_abh_id_1" /></td>
               <td><input type="text" id="tv_1_upper_abh_id_2" /></td>
               <td><input type="text" id="tv_1_upper_abh_id_3" /></td>
            </tr>
            <tr>
               <td><input type="text" id="tv_1_upper_abh_id_4" /></td>
               <td><input type="text" id="tv_1_upper_abh_id_5" /></td>
               <td><input type="text" id="tv_1_upper_abh_id_6" /></td>
            </tr>
            <tr>
               <th>LOWEST ID:</th>
               <td><input type="text" id="tv_1_upper_abh_lowest_id" /></td>
               <th>LARGEST ID:</th>
               <td><input type="text" id="tv_1_upper_abh_largest_id" /></td>
            </tr>
            <tr>
               <th colspan="3">SMALLEST CLEARANCE (0.012'' - 0.070'')</th>
               <td><input type="text" id="tv_1_upper_abh_smallest_clearance" /></td>
            </tr>
         </table>
      </div>

      <!-- FLOW DIVERTER Details -->
      <div class="table-container">
         <table id="tv_1_flow_diverter_details_table">
            <tr>
               <th colspan="2">FLOW DIVERTER</th>
               <td colspan="2"><input type="text" id="tv_1_flow_diverter_details" /></td>
            </tr>
            <tr>
               <th rowspan="2">OD</th>
               <td><input type="text" id="tv_1_flow_diverter_od_1" /></td>
               <td><input type="text" id="tv_1_flow_diverter_od_2" /></td>
               <td><input type="text" id="tv_1_flow_diverter_od_3" /></td>
            </tr>
            <tr>
               <td><input type="text" id="tv_1_flow_diverter_od_4" /></td>
               <td><input type="text" id="tv_1_flow_diverter_od_5" /></td>
               <td><input type="text" id="tv_1_flow_diverter_od_6" /></td>
            </tr>
            <tr>
               <th>LOWEST OD:</th>
               <td><input type="text" id="tv_1_flow_diverter_lowest_od" /></td>
               <th>LARGEST OD:</th>
               <td><input type="text" id="tv_1_flow_diverter_largest_od" /></td>
            </tr>
            <tr>
               <th colspan="3">LARGEST CLEARANCE (0.012'' - 0.070'')</th>
               <td><input type="text" id="tv_1_flow_diverter_largest_clearance" /></td>
            </tr>
         </table>
      </div>
   </div>
   <br>

   <table id="tv_1_measured_approved_table">
      <tr>
         <th>Measured/Checked By:</th>
         <td><input type="text" data-required="true" id="tv_1_Measured"></td>
         <th>Approved By:</th>
         <td><input type="text" id="tv_1_Approved"></td>
      </tr>
      <tr>
         <th>Date</th>
         <td><input type="date" id="tv_1_Measured_date"></td>
         <th>Date</th>
         <td><input type="date" id="tv_1_Approved_date"></td>
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