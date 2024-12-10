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
    .table-custom th {
        border: 1px solid black;
        text-align: center;
        vertical-align: middle;
    }


    .table-custom thead th {
        background-color: #f8f9fa;
    }

    .table-container {
        width: 85%;
        margin-bottom: 20px;
    }

    th,
    td {
        padding: 12px;
        text-align: left;
    }

    input[type="text"] {
        width: 100%;
    }

    /* Center the part name */
    .part-name td {
        background-color: #ddd;
        text-align: center;
        vertical-align: middle;

    }
</style>

<body>
    <form role="form" id="template_form" class="form-horizontal">
        <header class="head-title">
            <h5>
                <?php echo $vehicleDetails ?: ''; ?>
            </h5>
        </header>
        <div class="container mt-5">

            <!-- Make the table responsive -->
            <div class="table-container">
                <table id="used_motor_parts" class="table table-bordered table-custom" style="width: 100%">
                    <thead>
                        <tr rowspan="4">
                            <!-- Adjust the header -->

                        </tr>
                    </thead>
                    <tbody>
                        <!-- Mandrel -->
                        <tr class="part-name">
                            <td>1</td>
                            <td colspan="3">Mandrel</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>ID:</td>
                            <td><input type="text" id="mandrel_id" /></td>
                            <td>&lt; 2"</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lower radial OD:</td>
                            <td><input type="text" id="mandrel_radial_od" /></td>
                            <td>&gt; 4.538"</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lower body OD:</td>
                            <td><input type="text" id="mandrel_body_od" /></td>
                            <td>&gt; 6.424"</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lower thread wall thickness:</td>
                            <td><input type="text" id="mandrel_thickness" /></td>
                            <td>&gt; 21.13 mm</td>
                        </tr>

                        <!-- Flow diverter -->
                        <tr class="part-name">
                            <td>2</td>
                            <td colspan="3">Flow diverter</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lower Thread Wall thickness:</td>
                            <td><input type="text" id="flow_diverter_lower_thickness" /></td>
                            <td>&gt; 10.74 mm</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Upper Thread Wall thickness:</td>
                            <td><input type="text" id="flow_diverter_upper_thickness" /></td>
                            <td>&gt; 12.12 mm</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Body OD:</td>
                            <td><input type="text" id="flow_diverter_body_od" /></td>
                            <td>&gt; 87.25 mm</td>
                        </tr>

                        <!-- Lower bearing housing -->
                        <tr class="part-name">
                            <td>3</td>
                            <td colspan="3">Lower bearing housing</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lower Thread Wall thickness:</td>
                            <td><input type="text" id="lower_housing_lower_thickness" /></td>
                            <td>&gt; 5.03 mm</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Lower Body OD:</td>
                            <td><input type="text" id="lower_housing_lower_body_od" /></td>
                            <td>&gt; 6.835"</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Upper Body OD:</td>
                            <td><input type="text" id="lower_housing_upper_body_od" /></td>
                            <td>&gt; 6.736"</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Upper Thread Wall thickness:</td>
                            <td><input type="text" id="lower_housing_upper_thickness" /></td>
                            <td>&gt; 8.23 mm</td>
                        </tr>

                        <!-- Lower Male R bearing -->
                        <tr class="part-name">
                            <td>4</td>
                            <td colspan="3">Lower Male R bearing</td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Upper ID:</td>
                            <td><input type="text" id="lower_male_bearing_upper_id" /></td>
                            <td>&lt; 3.440"</td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Upper Wall thickness:</td>
                            <td><input type="text" id="lower_male_bearing_upper_thickness" /></td>
                            <td>&gt; 10.11 mm</td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>ID:</td>
                            <td><input type="text" id="lower_male_bearing_id" /></td>
                            <td>&lt; 4.739"</td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Pull out dowel port:</td>
                            <td><input type="text" id="lower_male_bearing_dowel_port" /></td>
                            <td>&lt; 25.10 mm</td>

                        </tr>

                        <!-- Lower female R bearing -->
                        <tr class="part-name">
                            <td>5</td>
                            <td colspan="3">Lower female R bearing</td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Thread to Body:</td>
                            <td><input type="text" id="lower_female_thread_body" /></td>
                            <td>&gt; 5.03 mm</td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>OD:</td>
                            <td><input type="text" id="lower_female_od" /></td>
                            <td>&gt; 6.835"</td>

                        </tr>

                        <!-- Upper bearing housing -->
                        <tr class="part-name">
                            <td>6</td>
                            <td colspan="3">Upper bearing housing</td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Thread to Body:</td>
                            <td><input type="text" id="upper_bearing_housing_thread_body" /></td>
                            <td>&gt; 7.98 mm</td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>OD:</td>
                            <td><input type="text" id="upper_bearing_housing_od" /></td>
                            <td>&gt; 6.697"</td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Upper ID:</td>
                            <td><input type="text" id="upper_bearing_housing_upper_id" /></td>
                            <td>&lt; 4.858"</td>

                        </tr>

                        <!-- Claw shaft -->
                        <tr class="part-name">
                            <td>7</td>
                            <td colspan="3">Claw shaft</td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Center driver:</td>
                            <td><input type="text" id="claw_shaft_center_driver" /></td>
                            <td rowspan="4">Maximum wear 4.57 mm</td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Upper driver:</td>
                            <td><input type="text" id="claw_shaft_upper_driver" /></td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Lower coupling:</td>
                            <td><input type="text" id="claw_shaft_lower_coupling" /></td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Upper coupling:</td>
                            <td><input type="text" id="claw_shaft_upper_coupling" /></td>

                        </tr>
                    </tbody>
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

            $(this).find('input[type="text"]').each(function() {
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