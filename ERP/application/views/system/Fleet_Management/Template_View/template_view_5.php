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
   <table id="tv_5_Motor_Serial_Number_table">
      <tr>
         <th>Motor Serial Number</th>
         <td><input type="text" id="tv_5_Motor_Serial_Number"></td>
         <th>Size</th>
         <td><input type="text" id="tv_5_Size"></td>
         <th>Location</th>
         <td><input type="text" id="tv_5_Location"></td>
         <th>Assembly Date</th>
         <td><input type="text" id="tv_5_Assembly_Date"></td>
      </tr>
   </table>
   <br>

   <table>
      <tr>
         <th colspan="2">Description</th>
         <th colspan="8">ASSEMBLY REPORT</th>
         <th colspan="3">DISASSEMBLY REPORT</th>
      </tr>

      <tr>
         <th rowspan="2">STATOR</th>
         <td><input type="text" id="tv_5_assembly_1"></td>
         <th rowspan="2">Stator Average ID: (inch)</th>
         <td><input type="text" id="tv_5_assembly_2"></td>
         <td><input type="text" id="tv_5_assembly_3"></td>
         <td><input type="text" id="tv_5_assembly_4"></td>
         <th rowspan="3">ROTOR LOBE HEIGHT (inch)</th>
         <td><input type="text" id="tv_5_assembly_5"></td>
         <td><input type="text" id="tv_5_assembly_6"></td>
         <td><input type="text" id="tv_5_assembly_7"></td>
         <th>Rig Name</th>
         <th>Well Name</th>
         <th>Circ hours</th>
      </tr>

      <tr>
         <td></td>
         <td><input type="text" id="tv_5_assembly_8"></td>
         <td><input type="text" id="tv_5_assembly_9"></td>
         <td><input type="text" id="tv_5_assembly_10"></td>
         <td><input type="text" id="tv_5_assembly_11"></td>
         <td><input type="text" id="tv_5_assembly_12"></td>
         <td><input type="text" id="tv_5_assembly_13"></td>
         <td><input type="text" id="tv_5_disassembly_1"></td>
         <td><input type="text" id="tv_5_disassembly_2"></td>
         <td><input type="text" id="tv_5_disassembly_3"></td>
      </tr>

      <tr>
         <th colspan="2">STATOR LENGTH</th>
         <th>Calibration Sleeve</th>
         <td><input type="text" id="tv_5_assembly_sleeve"></td>
         <th>Average</th>
         <td><input type="text" id="tv_5_assembly_14"></td>
         <th>Average</th>
         <td colspan="2"><input type="text" id="tv_5_assembly_15"></td>
         <th>Avrg int Dia</th>
         <th>Avrg Ext Dia</th>
         <th>BRT Hrs</th>
      </tr>

      <tr>
         <th rowspan="2">ROTOR</th>
         <td><input type="text" id="tv_5_assembly_rotor_1"></td>
         <th>Stator Minor</th>
         <th>Rotor Minor</th>
         <th colspan="2">Interference</th>
         <th rowspan="3">Rotor Average OD: (inch)</th>
         <td><input type="text" id="tv_5_assembly_rotor_2"></td>
         <td><input type="text" id="tv_5_assembly_rotor_3"></td>
         <td><input type="text" id="tv_5_assembly_rotor_4"></td>
         <td><input type="text" id="tv_5_disassembly_4"></td>
         <td><input type="text" id="tv_5_disassembly_5"></td>
         <td><input type="text" id="tv_5_disassembly_6"></td>
      </tr>

      <tr>
         <th></th>
         <td rowspan="2"><input type="text" id="tv_5_assembly_rotor_5"></td>
         <td rowspan="2"><input type="text" id="tv_5_assembly_rotor_6"></td>
         <td rowspan="2" colspan="2"><input type="text" id="tv_5_assembly_rotor_7"></td>
         <td><input type="text" id="tv_5_assembly_rotor_8"></td>
         <td><input type="text" id="tv_5_assembly_rotor_9"></td>
         <td><input type="text" id="tv_5_assembly_rotor_10"></td>
         <th colspan="2">Compression Gap</th>
         <th>Mud type</th>
      </tr>

      <tr>
         <th>Rotor Length</th>
         <td><input type="text" id="tv_5_assembly_rotor_11"></td>
         <th>Average</th>
         <td colspan="2"><input type="text" id="tv_5_assembly_rotor_12"></td>
         <td colspan="2"><input type="text" id="tv_5_assembly_rotor_13"></td>
         <td><input type="text" id="tv_5_disassembly_7"></td>
      </tr>
   </table>
   <br>

   <table>
      <tr>
         <th>Description</th>
         <th>Torque FT.LB</th>
         <th>Serial Number</th>
         <th colspan="2">Stator Reline Date:</th>
         <td colspan="2" id="tv_5_stator_reline_date"><input type="text"></td>
         <th>Stator Condition</th>
         <td id="tv_5_stator_condition"><input type="text"></td>
      </tr>
      <tr>
         <th>BEARING MANDREL</th>
         <td id="tv_5_bearing_mandrel_torque"><input type="text"></td>
         <td id="tv_5_bearing_mandrel_serial"><input type="text"></td>
         <th rowspan="2">LOBE:</th>
         <td rowspan="2" id="tv_5_lobe"><input type="text"></td>
         <th rowspan="2">STAGE</th>
         <td rowspan="2" id="tv_5_stage"><input type="text"></td>
         <th>Rotor Condition:</th>
         <td id="tv_5_rotor_condition"><input type="text"></td>
      </tr>

      <tr>
         <th>LOWER SHAFT FLOW RESTRICTOR</th>
         <td id="tv_5_lower_shaft_flow_restrictor_torque"><input type="text"></td>
         <td id="tv_5_lower_shaft_flow_restrictor_serial"><input type="text"></td>
         <th>Races Condition:</th>
         <td id="tv_5_races_condition"><input type="text"></td>
      </tr>

      <tr>
         <th>LOWER HOUSING</th>
         <td id="tv_5_lower_housing_torque"><input type="text"></td>
         <td id="tv_5_lower_housing_serial"><input type="text"></td>
         <th rowspan="2">Measured motor End Play:</th>
         <td rowspan="2" id="tv_5_measured_motor_end_play"><input type="text"></td>
         <th rowspan="2">Temperature Rating:</th>
         <td rowspan="2" id="tv_5_temperature_rating"><input type="text"></td>
         <th>Balls Condition:</th>
         <td id="tv_5_balls_condition"><input type="text"></td>
      </tr>

      <tr>
         <th>THRUST HOUSING</th>
         <td id="tv_5_thrust_housing_torque"><input type="text"></td>
         <td id="tv_5_thrust_housing_serial"><input type="text"></td>
         <th>Adjustable Condition:</th>
         <td id="tv_5_adjustable_condition"><input type="text"></td>
      </tr>

      <tr>
         <th>HOUSING ADAPTER</th>
         <td id="tv_5_housing_adapter_torque"><input type="text"></td>
         <td id="tv_5_housing_adapter_serial"><input type="text"></td>
         <th rowspan="2" colspan="2">ABH SET TO:</th>
         <td rowspan="2" colspan="2" id="tv_5_abh_set_to"><input type="text"></td>
         <th>Drive Shaft Condition:</th>
         <td id="tv_5_drive_shaft_condition"><input type="text"></td>
      </tr>

      <tr>
         <th>SLEEVE STABILIZER</th>
         <td id="tv_5_sleeve_stabilizer_torque"><input type="text"></td>
         <td id="tv_5_sleeve_stabilizer_serial"><input type="text"></td>
         <th>Comments:</th>
         <td id="tv_5_sleeve_comments"><input type="text"></td>
      </tr>

      <tr>
         <th>DRIVE ADAPTER</th>
         <td id="tv_5_drive_adapter_torque"><input type="text"></td>
         <td id="tv_5_drive_adapter_serial"><input type="text"></td>
         <th rowspan="2" colspan="2">Sleeve Size:</th>
         <td rowspan="2" colspan="2" id="tv_5_sleeve_size"><input type="text"></td>
         <th>Drive Shaft Condition:</th>
         <td id="tv_5_drive_shaft_condition_2"><input type="text"></td>
      </tr>

      <tr>
         <th>BEARING ADAPTER</th>
         <td id="tv_5_bearing_adapter_torque"><input type="text"></td>
         <td id="tv_5_bearing_adapter_serial"><input type="text"></td>
         <th colspan="2">Comments:</th>
      </tr>

      <tr>
         <th>ROTOR ADAPTER</th>
         <td id="tv_5_rotor_adapter_torque"><input type="text"></td>
         <td id="tv_5_rotor_adapter_serial"><input type="text"></td>
         <th rowspan="2">BOTTOM CONNECTION</th>
         <td rowspan="2" id="tv_5_bottom_connection"><input type="text"></td>
         <th rowspan="2">TOP CONNECTION</th>
         <td rowspan="2" id="tv_5_top_connection"><input type="text"></td>
         <td colspan="2" id="tv_5_rotor_adapter_comments"><input type="text"></td>
      </tr>

      <tr>
         <th>LOCK HOUSING</th>
         <td id="tv_5_lock_housing_torque"><input type="text"></td>
         <td id="tv_5_lock_housing_serial"><input type="text"></td>
         <td colspan="2" id="tv_5_lock_housing_comments"><input type="text"></td>
      </tr>

      <tr>
         <th>OFFSET HOUSING</th>
         <td id="tv_5_offset_housing_torque"><input type="text"></td>
         <td id="tv_5_offset_housing_serial"><input type="text"></td>
         <th rowspan="2">SET SCREW Torque/Loctite</th>
         <td rowspan="2" id="tv_5_set_screw_torque"><input type="text"></td>
         <th rowspan="2">ROT. ADPTR SEAL CAP</th>
         <td rowspan="2" id="tv_5_rot_adptr_seal_cap"><input type="text"></td>
         <td colspan="2" id="tv_5_offset_housing_comments"><input type="text"></td>
      </tr>

      <tr>
         <th>ADJUSTING RING</th>
         <td id="tv_5_adjusting_ring_torque"><input type="text"></td>
         <td id="tv_5_adjusting_ring_serial"><input type="text"></td>
         <td colspan="2" id="tv_5_adjusting_ring_comments"><input type="text"></td>
      </tr>

      <tr>
         <th>SPLINED MANDREL</th>
         <td id="tv_5_splined_mandrel_torque"><input type="text"></td>
         <td id="tv_5_splined_mandrel_serial"><input type="text"></td>
         <th rowspan="2">SEAL KIT</th>
         <td rowspan="2" id="tv_5_seal_kit"><input type="text"></td>
         <th rowspan="2">BRG ADPTR SEAL CAP</th>
         <td rowspan="2" id="tv_5_brg_adptr_seal_cap"><input type="text"></td>
         <td colspan="2" id="tv_5_splined_mandrel_comments"><input type="text"></td>
      </tr>

      <tr>
         <th>TOP SUB</th>
         <td id="tv_5_top_sub_torque"><input type="text"></td>
         <td id="tv_5_top_sub_serial"><input type="text"></td>
         <td colspan="2" id="tv_5_top_sub_comments"><input type="text"></td>
      </tr>

      <tr>
         <th>ROTOR CATCHER</th>
         <td id="tv_5_rotor_catcher_torque"><input type="text"></td>
         <td id="tv_5_rotor_catcher_serial"><input type="text"></td>
         <th rowspan="2">INNER RACES</th>
         <td rowspan="2" id="tv_5_inner_races"><input type="text"></td>
         <th rowspan="2">OUTER RACER</th>
         <td rowspan="2" id="tv_5_outer_racer"><input type="text"></td>
         <td colspan="2" id="tv_5_rotor_catcher_comments"><input type="text"></td>
      </tr>

      <tr>
         <th>OUTER COMPRESSION RING</th>
         <td id="tv_5_outer_compression_ring_torque"><input type="text"></td>
         <td id="tv_5_outer_compression_ring_serial"><input type="text"></td>
         <th colspan="2">Damaged Parts Observed</th>
      </tr>

      <tr>
         <th>THRUST HOUSING FLOW RESTRICTOR</th>
         <td id="tv_5_thrust_housing_flow_restrictor_torque"><input type="text"></td>
         <td id="tv_5_thrust_housing_flow_restrictor_serial"><input type="text"></td>
         <th rowspan="2">ROCKBIT BALLS</th>
         <td rowspan="2" id="tv_5_rockbit_balls"><input type="text"></td>
         <th rowspan="2">DRIVE BALLS</th>
         <td rowspan="2" id="tv_5_drive_balls"><input type="text"></td>
         <td colspan="2" id="tv_5_thrust_housing_comments"><input type="text"></td>
      </tr>

      <tr>
         <th>UPPER SHAFT FLOW RESTRICTOR</th>
         <td id="tv_5_upper_shaft_flow_restrictor_torque"><input type="text"></td>
         <td id="tv_5_upper_shaft_flow_restrictor_serial"><input type="text"></td>
         <td colspan="2" id="tv_5_upper_shaft_flow_comments"><input type="text"></td>
      </tr>

      <tr>
         <th>SHAFT COMPRESSION NUT</th>
         <td id="tv_5_shaft_compression_nut_torque"><input type="text"></td>
         <td id="tv_5_shaft_compression_nut_serial"><input type="text"></td>
         <th rowspan="2">THRUST BALL</th>
         <td rowspan="2" id="tv_5_thrust_ball"><input type="text"></td>
         <th rowspan="2">THRUST PIN</th>
         <td rowspan="2" id="tv_5_thrust_pin"><input type="text"></td>
         <td colspan="2" id="tv_5_shaft_compression_comments"><input type="text"></td>
      </tr>

      <tr>
         <th>TORQUE TWIN NUT</th>
         <td id="tv_5_torque_twin_nut_torque"><input type="text"></td>
         <td id="tv_5_torque_twin_nut_serial"><input type="text"></td>
         <td colspan="2" id="tv_5_torque_twin_nut_comments"><input type="text"></td>
      </tr>

      <tr>
         <th>Comments</th>
         <td colspan="8" id="tv_5_comments"><input type="text"></td>
      </tr>
   </table>
   <br>


   <table id="tv_5_Nov_Motor_Maintenance_Report_table">
      <tr>
         <th>Serviced by</th>
         <td><input type="text" id="tv_5_Serviced_by"></td>
         <th>Verified by</th>
         <td><input type="text" id="tv_5_Verified_by"></td>
         <th>Approved By:</th>
         <td><input type="text" id="tv_5_Approved_by"></td>
      </tr>
      <tr>
         <th>Date</th>
         <td><input type="date" id="tv_5_Date_Serviced_by"></td>
         <th>Date</th>
         <td><input type="date" id="tv_5_Date_Verified_by"></td>
         <th>Date</th>
         <td><input type="date" id="tv_5_Date_Approved"></td>
      </tr>
   </table>
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
