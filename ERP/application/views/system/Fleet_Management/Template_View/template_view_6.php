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
      padding: 5px;
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

   <table id="tv_6_Motor_Serial_Number_table">
      <tr>
         <th>Motor Serial Number</th>
         <td><input type="text" id="tv_6_Motor_Serial_Number"></td>
         <th>Size</th>
         <td><input type="text" id="tv_6_Size"></td>
         <th>Location</th>
         <td><input type="text" id="tv_6_Location"></td>
         <th>Assembly Date</th>
         <td><input type="text" id="tv_6_Assembly_Date"></td>
      </tr>
   </table>
   <br>

   <table>
      <tr>
         <th colspan="2">DESCRIPTION</th>
         <th colspan="8">ASSEMBLY REPORT</th>
         <th colspan="3">DISASSEMBLY REPORT</th>
      </tr>

      <tr>
         <th rowspan="2">STATOR</th>
         <td><input type="text" id="tv_6_assembly_1"></td>
         <th rowspan="2">Stator Average ID: (inch)</th>
         <td><input type="text" id="tv_6_assembly_2"></td>
         <td><input type="text" id="tv_6_assembly_3"></td>
         <td><input type="text" id="tv_6_assembly_4"></td>
         <th rowspan="3">ROTOR LOBE HEIGHT (inch)</th>
         <td><input type="text" id="tv_6_assembly_5"></td>
         <td><input type="text" id="tv_6_assembly_6"></td>
         <td><input type="text" id="tv_6_assembly_7"></td>
         <th>Rig Name</th>
         <th>Well Name</th>
         <th>Circ hours</th>
      </tr>

      <tr>
         <td></td>
         <td><input type="text" id="tv_6_assembly_8"></td>
         <td><input type="text" id="tv_6_assembly_9"></td>
         <td><input type="text" id="tv_6_assembly_10"></td>
         <td><input type="text" id="tv_6_assembly_11"></td>
         <td><input type="text" id="tv_6_assembly_12"></td>
         <td><input type="text" id="tv_6_assembly_13"></td>
         <td><input type="text" id="tv_6_disassembly_1"></td>
         <td><input type="text" id="tv_6_disassembly_2"></td>
         <td><input type="text" id="tv_6_disassembly_3"></td>
      </tr>

      <tr>
         <th colspan="2">STATOR LENGTH</th>
         <th>Calibration Sleeve</th>
         <td><input type="text" id="tv_6_assembly_sleeve"></td>
         <th>Average</th>
         <td><input type="text" id="tv_6_assembly_14"></td>
         <th>Average</th>
         <td colspan="2"><input type="text" id="tv_6_assembly_15"></td>
         <th>Avrg int Dia</th>
         <th>Avrg Ext Dia</th>
         <th>BRT Hrs</th>
      </tr>

      <tr>
         <th rowspan="2">ROTOR</th>
         <td><input type="text" id="tv_6_assembly_rotor_1"></td>
         <th>Stator Minor</th>
         <th>Rotor Mean</th>
         <th colspan="2">Interference</th>
         <th rowspan="3">Rotor Average OD: (inch)</th>
         <td><input type="text" id="tv_6_assembly_rotor_2"></td>
         <td><input type="text" id="tv_6_assembly_rotor_3"></td>
         <td><input type="text" id="tv_6_assembly_rotor_4"></td>
         <td><input type="text" id="tv_6_disassembly_4"></td>
         <td><input type="text" id="tv_6_disassembly_5"></td>
         <td><input type="text" id="tv_6_disassembly_6"></td>
      </tr>

      <tr>
         <th></th>
         <td rowspan="2"><input type="text" id="tv_6_assembly_rotor_5"></td>
         <td rowspan="2"><input type="text" id="tv_6_assembly_rotor_6"></td>
         <td rowspan="2" colspan="2"><input type="text" id="tv_6_assembly_rotor_7"></td>
         <td><input type="text" id="tv_6_assembly_rotor_8"></td>
         <td><input type="text" id="tv_6_assembly_rotor_9"></td>
         <td><input type="text" id="tv_6_assembly_rotor_10"></td>
         <th colspan="2">Compression Gap</th>
         <th>Mud type</th>
      </tr>

<table>
   <tr>
      <th>Description</th>
      <th>Torque FT.LB</th>
      <th>Serial Number</th>
      <th colspan="2">Stator Reline Date:</th>
      <td colspan="2"><input type="text" id="tv_6_stator_reline_date"></td>
      <th>Stator Condition</th>
      <td><input type="text" id="tv_6_stator_condition"></td>
   </tr>
   <tr>
      <th>Driveshaft Mandrel</th>
      <td><input type="text" id="tv_6_driveshaft_mandrel_torque"></td>
      <td><input type="text" id="tv_6_driveshaft_mandrel_serial"></td>
      <th rowspan="2">LOBE:</th>
      <td rowspan="2"><input type="text" id="tv_6_lobe"></td>
      <th rowspan="2">STAGE</th>
      <td rowspan="2"><input type="text" id="tv_6_stage"></td>
      <th>Rotor Condition:</th>
      <td><input type="text" id="tv_6_rotor_condition"></td>
   </tr>

   <tr>
      <th>Lower Male Bearing</th>
      <td><input type="text" id="tv_6_lower_male_bearing_torque"></td>
      <td><input type="text" id="tv_6_lower_male_bearing_serial"></td>
      <th>Races Condition:</th>
      <td><input type="text" id="tv_6_races_condition"></td>
   </tr>

   <tr>
      <th>Lower Female Bearing</th>
      <td><input type="text" id="tv_6_lower_female_torque"></td>
      <td><input type="text" id="tv_6_lower_female_serial"></td>
      <th rowspan="2">Measured motor End Play:</th>
      <td rowspan="2"><input type="text" id="tv_6_measured_motor_end_play"></td>
      <th rowspan="2">Temperature Rating:</th>
      <td rowspan="2"><input type="text" id="tv_6_temperature_rating"></td>
      <th>Balls Condition:</th>
      <td><input type="text" id="tv_6_balls_condition"></td>
   </tr>

   <tr>
      <th>Lower Bearing Housing</th>
      <td><input type="text" id="tv_6_lower_bearing_housing_torque"></td>
      <td><input type="text" id="tv_6_lower_bearing_housing_serial"></td>
      <th>Adjustable Condition:</th>
      <td><input type="text" id="tv_6_adjustable_condition"></td>
   </tr>

   <tr>
      <th>Flow Diverter</th>
      <td><input type="text" id="tv_6_flow_diverter_torque"></td>
      <td><input type="text" id="tv_6_flow_diverter_serial"></td>
      <th rowspan="2" colspan="2">ABH SET TO:</th>
      <td rowspan="2" colspan="2"><input type="text" id="tv_6_abh_set_to"></td>
      <th>Drive Shaft Condition:</th>
      <td><input type="text" id="tv_6_drive_shaft_condition"></td>
   </tr>

   <tr>
      <th>Upper Bearing Housing</th>
      <td><input type="text" id="tv_6_upper_bearing_housing_torque"></td>
      <td><input type="text" id="tv_6_upper_bearing_housing_serial"></td>
      <th>Comments:</th>
      <td><input type="text" id="tv_6_sleeve_comments"></td>
   </tr>

   <tr>
      <th>Lower Driver</th>
      <td><input type="text" id="tv_6_lower_driver_torque"></td>
      <td><input type="text" id="tv_6_lower_driver_serial"></td>
      <th rowspan="2" colspan="2">Sleeve Size:</th>
      <td rowspan="2" colspan="2"><input type="text" id="tv_6_sleeve_size"></td>
      <th>Drive Shaft Condition:</th>
      <td><input type="text" id="tv_6_drive_shaft_condition_2"></td>
   </tr>

   <tr>
      <th>Center Drive</th>
      <td><input type="text" id="tv_6_center_driver_torque"></td>
      <td><input type="text" id="tv_6_center_driver_serial"></td>
      <th colspan="2">Comments:</th>
   </tr>

   <tr>
      <th>Lower ABH</th>
      <td><input type="text" id="tv_6_lower_abh_torque"></td>
      <td><input type="text" id="tv_6_lower_abh_serial"></td>
      <th rowspan="2">BOTTOM CONNECTION</th>
      <td rowspan="2"><input type="text" id="tv_6_bottom_connection"></td>
      <th rowspan="2">TOP CONNECTION</th>
      <td rowspan="2"><input type="text" id="tv_6_top_connection"></td>
      <td colspan="2"><input type="text" id="tv_6_rotor_adapter_comments"></td>
   </tr>

   <tr>
      <th>Upper Drive Box</th>
      <td><input type="text" id="tv_6_upper_drive_box_torque"></td>
      <td><input type="text" id="tv_6_upper_drive_box_serial"></td>
      <td colspan="2"><input type="text" id="tv_6_upper_drive_box_comments"></td>
   </tr>

   <tr>
      <th>Upper Driver</th>
      <td><input type="text" id="tv_6_upper_drive_torque"></td>
      <td><input type="text" id="tv_6_upper_drive_serial"></td>
      <th rowspan="2">CENTRE DRIVE</th>
      <td rowspan="2"><input type="text" id="tv_6_center_drive"></td>
      <th rowspan="2">UPPER DRIVE</th>
      <td rowspan="2"><input type="text" id="tv_6_upper_drive"></td>
      <td colspan="2"><input type="text" id="tv_6_upper_drive_comments"></td>
   </tr>

   <tr>
      <th>Spacer Ring</th>
      <td><input type="text" id="tv_6_spacer_ring_torque"></td>
      <td><input type="text" id="tv_6_spacer_ring_serial"></td>
      <td colspan="2"><input type="text" id="tv_6_spacer_ring_comments"></td>
   </tr>

   <tr>
      <th>Upper ABH</th>
      <td><input type="text" id="tv_6_splined_mandrel_torque"></td>
      <td><input type="text" id="tv_6_splined_mandrel_serial"></td>
      <th rowspan="2">LOWER DRIVE</th>
      <td rowspan="2"><input type="text" id="tv_6_lower_drive"></td>
      <th rowspan="2">UPPER BOX</th>
      <td rowspan="2"><input type="text" id="tv_6_upper_drive"></td>
      <td colspan="2"><input type="text" id="tv_6_splined_mandrel_comments"></td>
   </tr>

   <tr>
      <th>Top Sub</th>
      <td><input type="text" id="tv_6_top_sub_torque"></td>
      <td><input type="text" id="tv_6_top_sub_serial"></td>
      <td colspan="2"><input type="text" id="tv_6_top_sub_comments"></td>
   </tr>

   <tr>
      <th>Catcher Rod</th>
      <td><input type="text" id="tv_6_rotor_catcher_torque"></td>
      <td><input type="text" id="tv_6_rotor_catcher_serial"></td>
      <th rowspan="2">INNER RACES</th>
      <td rowspan="2"><input type="text" id="tv_6_inner_races"></td>
      <th rowspan="2">OUTER RACER</th>
      <td rowspan="2"><input type="text" id="tv_6_outer_racer"></td>
      <td colspan="2"><input type="text" id="tv_6_rotor_catcher_comments"></td>
   </tr>

   <tr>
      <th>Catcher Ring</th>
      <td><input type="text" id="tv_6_outer_compression_ring_torque"></td>
      <td><input type="text" id="tv_6_outer_compression_ring_serial"></td>
      <th colspan="2">Damaged Parts Observed</th>
   </tr>

   <tr>
      <th>Catcher Nut</th>
      <td><input type="text" id="tv_6_thrust_housing_flow_restrictor_torque"></td>
      <td><input type="text" id="tv_6_thrust_housing_flow_restrictor_serial"></td>
      <th rowspan="2">ROCKBIT BALLS</th>
      <td rowspan="2"><input type="text" id="tv_6_rockbit_balls"></td>
      <th rowspan="2">DRIVE BALLS</th>
      <td rowspan="2"><input type="text" id="tv_6_drive_balls"></td>
      <td colspan="2"><input type="text" id="tv_6_thrust_housing_comments"></td>
   </tr>

   <tr>
      <th>Sleeve Stabilizer</th>
      <td><input type="text" id="tv_6_upper_shaft_flow_restrictor_torque"></td>
      <td><input type="text" id="tv_6_upper_shaft_flow_restrictor_serial"></td>
      <td colspan="2"><input type="text" id="tv_6_upper_shaft_flow_comments"></td>
   </tr>

   <tr>
      <th></th>
      <td></td>
      <td></td>
      <th rowspan="2">CATCH PIN</th>
      <td rowspan="2"><input type="text" id="tv_6_thrust_catch_pin"></td>
      <th rowspan="2">CATCH</th>
      <td rowspan="2"><input type="text" id="tv_6_thrust_catch"></td>
      <td colspan="2"><input type="text" id="tv_6_shaft_compression_comments"></td>
   </tr>

   <tr>
      <th></th>
      <td></td>
      <td></td>
      <td colspan="2"><input type="text" id="tv_6_torque_twin_nut_comments"></td>
   </tr>

   <tr>
      <th>Comments</th>
      <td colspan="8"><input type="text" id="tv_6_comments"></td>
   </tr>
</table>
<br>


<table id="tv_6_Bico_Motor_Maintenance_Report_table">
   <tr>
      <th>Serviced by</th>
      <td><input type="text" id="tv_6_Serviced_by"></td>
      <th>Verified by</th>
      <td><input type="text" id="tv_6_Verified_by"></td>
      <th>Approved By:</th>
      <td><input type="text" id="tv_6_Approved_by"></td>
   </tr>
   <tr>
      <th>Date</th>
      <td><input type="date" id="tv_6_Date_Serviced_by"></td>
      <th>Date</th>
      <td><input type="date" id="tv_6_Date_Verified_by"></td>
      <th>Date</th>
      <td><input type="date" id="tv_6_Date_Approved"></td>
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
