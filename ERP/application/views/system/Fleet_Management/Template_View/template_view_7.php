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

    .table-container {
        width: 90%;
        margin: 0 auto 20px;
        /* Center horizontally and add bottom margin */
        border: 1px solid #333;
        padding: 12px;
    }


    table.table-custom {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #9b9a9a;
        /* Darker border for table */
    }

    th,
    td {
        padding: 12px;
        text-align: left;
    }

    .label-cell {
        background-color: #dee2e6;
        /* Light gray background for label cells */
        font-weight: bold;
        text-align: left;


    }

    .highlight {
        font-weight: bold;
        text-align: center;

    }

    .spacer-row td {
        padding: 2px 0;

    }


    .input-field {
        width: 100%;
        /* Make the input take full width of the cell */
        height: 20px;
        /* Adjust height */
        padding: 4px;
        /* Add padding for better appearance */
        box-sizing: border-box;
        /* Ensure padding doesn't affect width */
        border: 1px solid #333;
        /* Border styling */
    }
</style>
</head>

<body>
    <form role="form" id="template_form" class="form-horizontal">
        <header class="head-title">
            <h5>
                <?php echo $vehicleDetails ?: ''; ?>
            </h5>
        </header>
        <div class="tables-wrapper">
            <div class="table-container">
                <table id="measurement_table" class="table table-bordered table-custom">
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label-cell" colspan="2">Stator Serial Number:</td>
                        <td class="highlight" colspan="2">
                            <input type="text" class="input-field" id="stator_serial_number" name="stator_serial_number">
                        </td>
                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Lobe:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="stator_lobe" name="stator_lobe">
                        </td>
                        <td class="label-cell">Stage:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="stator_stage" name="stator_stage">
                        </td>
                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Reline Date:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="stator_reline_date" name="stator_reline_date">
                        </td>
                        <td class="label-cell">Circ. Hours:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="stator_circ_hours" name="stator_circ_hours">
                        </td>
                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label-cell" colspan="2">Calibration Sleeve Size:</td>
                        <td class="highlight" colspan="2">
                            <input type="text" class="input-field" id="stator_calibration_sleeve_size" name="stator_calibration_sleeve_size">
                        </td>
                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label-cell" rowspan="2">Stator ID:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="stator_id_1" name="stator_id_1">
                        </td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="stator_id_2" name="stator_id_2">
                        </td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="stator_id_3" name="stator_id_3">
                        </td>
                    </tr>
                    <tr>
                        <td class="highlight">
                            <input type="text" class="input-field" id="stator_id_4" name="stator_id_4">
                        </td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="stator_id_5" name="stator_id_5">
                        </td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="stator_id_6" name="stator_id_6">
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell">Average ID:</td>
                        <td class="highlight" colspan="3" style="text-align: center;" id="stator_average_id">#DIV/0!</td>
                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label-cell" colspan="2">Rotor Serial Number:</td>
                        <td class="highlight" colspan="2">
                            <input type="text" class="input-field" id="rotor_serial_number" name="rotor_serial_number">
                        </td>
                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Lobe:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_lobe" name="rotor_lobe">
                        </td>
                        <td class="label-cell">Stage:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_stage" name="rotor_stage">
                        </td>
                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Recarbide Date:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_rec_date" name="rotor_rec_date">
                        </td>
                        <td class="label-cell">Circ. Hours:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_circ_hours" name="rotor_circ_hours">
                        </td>
                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label-cell" rowspan="2">Rotor Average OD:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_average_OD_1" name="rotor_average_OD_1">
                        </td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_average_OD_2" name="rotor_average_OD_2">
                        </td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_average_OD_3" name="rotor_average_OD_3">
                        </td>
                    </tr>
                    <tr>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_average_OD_4" name="rotor_average_OD_4">
                        </td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_average_OD_5" name="rotor_average_OD_5">
                        </td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_average_OD_6" name="rotor_average_OD_6">
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell" colspan="2">Average:</td>
                        <td class="highlight" colspan="2" style="text-align: center;" id="rotor_average_od">#DIV/0!</td>
                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label-cell" rowspan="2">ROTOR LOBE HEIGHT:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_lobe_height_1" name="rotor_lobe_height_1">
                        </td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_lobe_height_2" name="rotor_lobe_height_2">
                        </td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_lobe_height_3" name="rotor_lobe_height_3">
                        </td>
                    </tr>
                    <tr>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_lobe_height_4" name="rotor_lobe_height_4">
                        </td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_lobe_height_5" name="rotor_lobe_height_5">
                        </td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="rotor_lobe_height_6" name="rotor_lobe_height_6">
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell" colspan="2">Average:</td>
                        <td class="highlight" colspan="2" style="text-align: center;" id="average_rotor_lobe_height">#DIV/0!</td>
                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <th class="label-cell" style="text-align: center;">Stator Minor</th>
                        <th class="label-cell" style="text-align: center;">Rotor Mean</th>
                        <th class="label-cell" colspan="2" style="text-align: center;">Interference:</th>
                    </tr>
                    <tr>
                        <td class="highlight" style="text-align: center;" id="Stator_Minor">#DIV/0!</td>

                        <td class="highlight" style="text-align: center;" id="Rotor_Mean">#DIV/0!</td>

                        <td class="highlight" colspan="2" style="text-align: center;" id="Interference">#DIV/0!</td>

                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label-cell" colspan="2">Accepted Temperature Range:</td>
                        <td class="highlight" colspan="2">
                            <input type="text" class="input-field" id="accepted_temperature_range" name="accepted_temperature_range">
                        </td>
                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Checked By:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="checked_by" name="checked_by">
                        </td>
                        <td class="label-cell">Approved By</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="approved_by" name="approved_by">
                        </td>


                    </tr>
                    <tr>
                        <td class="label-cell">Date:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="checked_date" name="checked_date">
                        </td>
                        <td class="label-cell">Date:</td>
                        <td class="highlight">
                            <input type="text" class="input-field" id="approved_date" name="approved_date">
                        </td>
                    </tr>
                    <!-- Spacing Row -->
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                </table>
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-primary pull-right">Save</button>

    </form>
</body>

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
         var hasErrors = false;

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
               },
               error: function() {
                  stopLoad();
                  myAlert('e', 'An error occurred during the request.');
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
