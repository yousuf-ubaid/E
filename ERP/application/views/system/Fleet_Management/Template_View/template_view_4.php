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
                        <tr>
                            <th rowspan="2">Description</th>
                            <th rowspan="2">Serial No</th>
                            <th rowspan="2">Total Consumption</th>
                            <th rowspan="2">Max.Hours</th>
                            <th colspan="2">Consumption</th>
                            <th colspan="2">Consumption</th>
                            <th colspan="2">Consumption</th>
                            <th colspan="2">Consumption</th>
                            <th colspan="2">Consumption</th>


                        </tr>
                        <tr>
                            <th>Rig Name</th>
                            <th>Circ. Hours</th>

                            <th>Rig Name</th>
                            <th>Circ. Hours</th>

                            <th>Rig Name</th>
                            <th>Circ. Hours</th>

                            <th>Rig Name</th>
                            <th>Circ. Hours</th>

                            <th>Rig Name</th>
                            <th>Circ. Hours</th>


                        </tr>

                    </thead>
                    <tbody>
                        <tr>
                            <td> Bearing Mandrel</td>

                            <td><input type="text" id="Bearing_Mandrel_serial_no" /></td>
                            <td><input type="text" id="Bearing_Mandrel_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Bearing_Mandrel_max_hours" disabled placeholder="2500.00" /></td>
                            <td><input type="text" id="Bearing_Mandrel_rig_name_1" /></td>
                            <td><input type="text" id="Bearing_Mandrel_circ_hourse_1" /></td>
                            <td><input type="text" id="Bearing_Mandrel_rig_name_2" /></td>
                            <td><input type="text" id="Bearing_Mandrel_circ_hourse_2" /></td>
                            <td><input type="text" id="Bearing_Mandrel_rig_name_3" /></td>
                            <td><input type="text" id="Bearing_Mandrel_circ_hourse_3" /></td>
                            <td><input type="text" id="Bearing_Mandrel_rig_name_4" /></td>
                            <td><input type="text" id="Bearing_Mandrel_circ_hourse_4" /></td>
                            <td><input type="text" id="Bearing_Mandrel_rig_name_5" /></td>
                            <td><input type="text" id="Bearing_Mandrel_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Lower Housing</td>

                            <td><input type="text" id="Lower_Housing_serial_no" /></td>
                            <td><input type="text" id="Lower_Housing_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Lower_Housing_max_hours" disabled placeholder="1500.00" /></td>
                            <td><input type="text" id="Lower_Housing_rig_name_1" /></td>
                            <td><input type="text" id="Lower_Housing_circ_hourse_1" /></td>
                            <td><input type="text" id="Lower_Housing_rig_name_2" /></td>
                            <td><input type="text" id="Lower_Housing_circ_hourse_2" /></td>
                            <td><input type="text" id="Lower_Housing_rig_name_3" /></td>
                            <td><input type="text" id="Lower_Housing_circ_hourse_3" /></td>
                            <td><input type="text" id="Lower_Housing_rig_name_4" /></td>
                            <td><input type="text" id="Lower_Housing_circ_hourse_4" /></td>
                            <td><input type="text" id="Lower_Housing_rig_name_5" /></td>
                            <td><input type="text" id="Lower_Housing_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Thrust Housing</td>

                            <td><input type="text" id="Thrust_Housing_serial_no" /></td>
                            <td><input type="text" id="Thrust_Housing_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Thrust_Housing_max_hours" disabled placeholder="1500.00" /></td>
                            <td><input type="text" id="Thrust_Housing_rig_name_1" /></td>
                            <td><input type="text" id="Thrust_Housing_circ_hourse_1" /></td>
                            <td><input type="text" id="Thrust_Housing_rig_name_2" /></td>
                            <td><input type="text" id="Thrust_Housing_circ_hourse_2" /></td>
                            <td><input type="text" id="Thrust_Housing_rig_name_3" /></td>
                            <td><input type="text" id="Thrust_Housing_circ_hourse_3" /></td>
                            <td><input type="text" id="Thrust_Housing_rig_name_4" /></td>
                            <td><input type="text" id="Thrust_Housing_circ_hourse_4" /></td>
                            <td><input type="text" id="Thrust_Housing_rig_name_5" /></td>
                            <td><input type="text" id="Thrust_Housing_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Housing Adapter</td>

                            <td><input type="text" id=" Housing_Adapter_serial_no" /></td>
                            <td><input type="text" id=" Housing_Adapter_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id=" Housing_Adapter_max_hours" disabled placeholder="2000.00" /></td>
                            <td><input type="text" id=" Housing_Adapter_rig_name_1" /></td>
                            <td><input type="text" id=" Housing_Adapter_circ_hourse_1" /></td>
                            <td><input type="text" id=" Housing_Adapter_rig_name_2" /></td>
                            <td><input type="text" id=" Housing_Adapter_circ_hourse_2" /></td>
                            <td><input type="text" id=" Housing_Adapter_rig_name_3" /></td>
                            <td><input type="text" id=" Housing_Adapter_circ_hourse_3" /></td>
                            <td><input type="text" id=" Housing_Adapter_rig_name_4" /></td>
                            <td><input type="text" id=" Housing_Adapter_circ_hourse_4" /></td>
                            <td><input type="text" id=" Housing_Adapter_rig_name_5" /></td>
                            <td><input type="text" id=" Housing_Adapter_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Sleeve Stabilizer</td>

                            <td><input type="text" id="Sleeve_Stabilizer_serial_no" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_max_hours" disabled placeholder="2000.00" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_rig_name_1" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_circ_hourse_1" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_rig_name_2" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_circ_hourse_2" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_rig_name_3" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_circ_hourse_3" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_rig_name_4" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_circ_hourse_4" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_rig_name_5" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Lock Housing</td>

                            <td><input type="text" id="Lock_Housing_serial_no" /></td>
                            <td><input type="text" id="Lock_Housing_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Lock_Housing_max_hours" disabled placeholder="1500.00" /></td>
                            <td><input type="text" id="Lock_Housing_rig_name_1" /></td>
                            <td><input type="text" id="Lock_Housing_circ_hourse_1" /></td>
                            <td><input type="text" id="Lock_Housing_rig_name_2" /></td>
                            <td><input type="text" id="Lock_Housing_circ_hourse_2" /></td>
                            <td><input type="text" id="Lock_Housing_rig_name_3" /></td>
                            <td><input type="text" id="Lock_Housing_circ_hourse_3" /></td>
                            <td><input type="text" id="Lock_Housing_rig_name_4" /></td>
                            <td><input type="text" id="Lock_Housing_circ_hourse_4" /></td>
                            <td><input type="text" id="Lock_Housing_rig_name_5" /></td>
                            <td><input type="text" id="Lock_Housing_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Offset Housing</td>

                            <td><input type="text" id="Offset_Housing_serial_no" /></td>
                            <td><input type="text" id="Offset_Housing_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Offset_Housing_max_hours" disabled placeholder="1500.00" /></td>
                            <td><input type="text" id="Offset_Housing_rig_name_1" /></td>
                            <td><input type="text" id="Offset_Housing_circ_hourse_1" /></td>
                            <td><input type="text" id="Offset_Housing_rig_name_2" /></td>
                            <td><input type="text" id="Offset_Housing_circ_hourse_2" /></td>
                            <td><input type="text" id="Offset_Housing_rig_name_3" /></td>
                            <td><input type="text" id="Offset_Housing_circ_hourse_3" /></td>
                            <td><input type="text" id="Offset_Housing_rig_name_4" /></td>
                            <td><input type="text" id="Offset_Housing_circ_hourse_4" /></td>
                            <td><input type="text" id="Offset_Housing_rig_name_5" /></td>
                            <td><input type="text" id="Offset_Housing_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Adjusting Ring</td>

                            <td><input type="text" id="Adjusting_Ring_serial_no" /></td>
                            <td><input type="text" id="Adjusting_Ring_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Adjusting_Ring_max_hours" disabled placeholder="1500.00" /></td>
                            <td><input type="text" id="Adjusting_Ring_rig_name_1" /></td>
                            <td><input type="text" id="Adjusting_Ring_circ_hourse_1" /></td>
                            <td><input type="text" id="Adjusting_Ring_rig_name_2" /></td>
                            <td><input type="text" id="Adjusting_Ring_circ_hourse_2" /></td>
                            <td><input type="text" id="Adjusting_Ring_rig_name_3" /></td>
                            <td><input type="text" id="Adjusting_Ring_circ_hourse_3" /></td>
                            <td><input type="text" id="Adjusting_Ring_rig_name_4" /></td>
                            <td><input type="text" id="Adjusting_Ring_circ_hourse_4" /></td>
                            <td><input type="text" id="Adjusting_Ring_rig_name_5" /></td>
                            <td><input type="text" id="Adjusting_Ring_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Splined_Mandrel</td>

                            <td><input type="text" id=" Splined_Mandrel_serial_no" /></td>
                            <td><input type="text" id=" Splined_Mandrel_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id=" Splined_Mandrel_max_hours" disabled placeholder="1500.00" /></td>
                            <td><input type="text" id=" Splined_Mandrel_rig_name_1" /></td>
                            <td><input type="text" id=" Splined_Mandrel_circ_hourse_1" /></td>
                            <td><input type="text" id=" Splined_Mandrel_rig_name_2" /></td>
                            <td><input type="text" id=" Splined_Mandrel_circ_hourse_2" /></td>
                            <td><input type="text" id=" Splined_Mandrel_rig_name_3" /></td>
                            <td><input type="text" id=" Splined_Mandrel_circ_hourse_3" /></td>
                            <td><input type="text" id=" Splined_Mandrel_rig_name_4" /></td>
                            <td><input type="text" id=" Splined_Mandrel_circ_hourse_4" /></td>
                            <td><input type="text" id=" Splined_Mandrel_rig_name_5" /></td>
                            <td><input type="text" id=" Splined_Mandrel_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Top Sub</td>

                            <td><input type="text" id="Top_Sub_serial_no" /></td>
                            <td><input type="text" id="Top_Sub_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Top_Sub_max_hours" disabled placeholder="2000.00" /></td>
                            <td><input type="text" id="Top_Sub_rig_name_1" /></td>
                            <td><input type="text" id="Top_Sub_circ_hourse_1" /></td>
                            <td><input type="text" id="Top_Sub_rig_name_2" /></td>
                            <td><input type="text" id="Top_Sub_circ_hourse_2" /></td>
                            <td><input type="text" id="Top_Sub_rig_name_3" /></td>
                            <td><input type="text" id="Top_Sub_circ_hourse_3" /></td>
                            <td><input type="text" id="Top_Sub_rig_name_4" /></td>
                            <td><input type="text" id="Top_Sub_circ_hourse_4" /></td>
                            <td><input type="text" id="Top_Sub_rig_name_5" /></td>
                            <td><input type="text" id="Top_Sub_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Drive shaft </td>

                            <td><input type="text" id="Drive_shaft_serial_no" /></td>
                            <td><input type="text" id="Drive_shaft_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Drive_shaft_max_hours" disabled placeholder="1500.00" /></td>
                            <td><input type="text" id="Drive_shaft_rig_name_1" /></td>
                            <td><input type="text" id="Drive_shaft_circ_hourse_1" /></td>
                            <td><input type="text" id="Drive_shaft_rig_name_2" /></td>
                            <td><input type="text" id="Drive_shaft_circ_hourse_2" /></td>
                            <td><input type="text" id="Drive_shaft_rig_name_3" /></td>
                            <td><input type="text" id="Drive_shaft_circ_hourse_3" /></td>
                            <td><input type="text" id="Drive_shaft_rig_name_4" /></td>
                            <td><input type="text" id="Drive_shaft_circ_hourse_4" /></td>
                            <td><input type="text" id="Drive_shaft_rig_name_5" /></td>
                            <td><input type="text" id="Drive_shaft_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Lower Male R bearing</td>

                            <td><input type="text" id="Lower_Male_R_bearing_serial_no" /></td>
                            <td><input type="text" id="Lower_Male_R_bearing_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Lower_Male_R_bearing_max_hours" disabled placeholder="1000.00" /></td>
                            <td><input type="text" id="Lower_Male_R_bearing_rig_name_1" /></td>
                            <td><input type="text" id="Lower_Male_R_bearing_circ_hourse_1" /></td>
                            <td><input type="text" id="Lower_Male_R_bearing_rig_name_2" /></td>
                            <td><input type="text" id="Lower_Male_R_bearing_circ_hourse_2" /></td>
                            <td><input type="text" id="Lower_Male_R_bearing_rig_name_3" /></td>
                            <td><input type="text" id="Lower_Male_R_bearing_circ_hourse_3" /></td>
                            <td><input type="text" id="Lower_Male_R_bearing_rig_name_4" /></td>
                            <td><input type="text" id="Lower_Male_R_bearing_circ_hourse_4" /></td>
                            <td><input type="text" id="Lower_Male_R_bearing_rig_name_5" /></td>
                            <td><input type="text" id="Lower_Male_R_bearing_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Lower Female R bearing</td>

                            <td><input type="text" id=" Lower_Female_R_bearing_serial_no" /></td>
                            <td><input type="text" id=" Lower_Female_R_bearing_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id=" Lower_Female_R_bearing_max_hours" disabled placeholder="1000.00" /></td>
                            <td><input type="text" id=" Lower_Female_R_bearing_rig_name_1" /></td>
                            <td><input type="text" id=" Lower_Female_R_bearing_circ_hourse_1" /></td>
                            <td><input type="text" id=" Lower_Female_R_bearing_rig_name_2" /></td>
                            <td><input type="text" id=" Lower_Female_R_bearing_circ_hourse_2" /></td>
                            <td><input type="text" id=" Lower_Female_R_bearing_rig_name_3" /></td>
                            <td><input type="text" id=" Lower_Female_R_bearing_circ_hourse_3" /></td>
                            <td><input type="text" id=" Lower_Female_R_bearing_rig_name_4" /></td>
                            <td><input type="text" id=" Lower_Female_R_bearing_circ_hourse_4" /></td>
                            <td><input type="text" id=" Lower_Female_R_bearing_rig_name_5" /></td>
                            <td><input type="text" id=" Lower_Female_R_bearing_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td>Lower bearing housing</td>

                            <td><input type="text" id="Lower_bearing_housing_serial_no" /></td>
                            <td><input type="text" id="Lower_bearing_housing_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Lower_bearing_housing_max_hours" disabled placeholder="3000.00" /></td>
                            <td><input type="text" id="Lower_bearing_housing_rig_name_1" /></td>
                            <td><input type="text" id="Lower_bearing_housing_circ_hourse_1" /></td>
                            <td><input type="text" id="Lower_bearing_housing_rig_name_2" /></td>
                            <td><input type="text" id="Lower_bearing_housing_circ_hourse_2" /></td>
                            <td><input type="text" id="Lower_bearing_housing_rig_name_3" /></td>
                            <td><input type="text" id="Lower_bearing_housing_circ_hourse_3" /></td>
                            <td><input type="text" id="Lower_bearing_housing_rig_name_4" /></td>
                            <td><input type="text" id="Lower_bearing_housing_circ_hourse_4" /></td>
                            <td><input type="text" id="Lower_bearing_housing_rig_name_5" /></td>
                            <td><input type="text" id="Lower_bearing_housing_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Upper bearing housing</td>

                            <td><input type="text" id="Upper_bearing_housing_serial_no" /></td>
                            <td><input type="text" id="Upper_bearing_housing_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Upper_bearing_housing_max_hours" disabled placeholder="3000.00" /></td>
                            <td><input type="text" id="Upper_bearing_housing_rig_name_1" /></td>
                            <td><input type="text" id="Upper_bearing_housing_circ_hourse_1" /></td>
                            <td><input type="text" id="Upper_bearing_housing_rig_name_2" /></td>
                            <td><input type="text" id="Upper_bearing_housing_circ_hourse_2" /></td>
                            <td><input type="text" id="Upper_bearing_housing_rig_name_3" /></td>
                            <td><input type="text" id="Upper_bearing_housing_circ_hourse_3" /></td>
                            <td><input type="text" id="Upper_bearing_housing_rig_name_4" /></td>
                            <td><input type="text" id="Upper_bearing_housing_circ_hourse_4" /></td>
                            <td><input type="text" id="Upper_bearing_housing_rig_name_5" /></td>
                            <td><input type="text" id="Upper_bearing_housing_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Flow diverter</td>

                            <td><input type="text" id="Flow_diverter_serial_no" /></td>
                            <td><input type="text" id="Flow_diverter_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Flow_diverter_max_hours" disabled placeholder="1000.00" /></td>
                            <td><input type="text" id="Flow_diverter_rig_name_1" /></td>
                            <td><input type="text" id="Flow_diverter_circ_hourse_1" /></td>
                            <td><input type="text" id="Flow_diverter_rig_name_2" /></td>
                            <td><input type="text" id="Flow_diverter_circ_hourse_2" /></td>
                            <td><input type="text" id="Flow_diverter_rig_name_3" /></td>
                            <td><input type="text" id="Flow_diverter_circ_hourse_3" /></td>
                            <td><input type="text" id="Flow_diverter_rig_name_4" /></td>
                            <td><input type="text" id="Flow_diverter_circ_hourse_4" /></td>
                            <td><input type="text" id="Flow_diverter_rig_name_5" /></td>
                            <td><input type="text" id="Flow_diverter_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> LOWER ABH</td>

                            <td><input type="text" id="LOWER_ABH_serial_no" /></td>
                            <td><input type="text" id="LOWER_ABH_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="LOWER_ABH_max_hours" disabled placeholder="1000.00" /></td>
                            <td><input type="text" id="LOWER_ABH_rig_name_1" /></td>
                            <td><input type="text" id="LOWER_ABH_circ_hourse_1" /></td>
                            <td><input type="text" id="LOWER_ABH_rig_name_2" /></td>
                            <td><input type="text" id="LOWER_ABH_circ_hourse_2" /></td>
                            <td><input type="text" id="LOWER_ABH_rig_name_3" /></td>
                            <td><input type="text" id="LOWER_ABH_circ_hourse_3" /></td>
                            <td><input type="text" id="LOWER_ABH_rig_name_4" /></td>
                            <td><input type="text" id="LOWER_ABH_circ_hourse_4" /></td>
                            <td><input type="text" id="LOWER_ABH_rig_name_5" /></td>
                            <td><input type="text" id="LOWER_ABH_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> UPPER ABH</td>

                            <td><input type="text" id=" UPPER_ABH_serial_no" /></td>
                            <td><input type="text" id=" UPPER_ABH_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id=" UPPER_ABH_max_hours" disabled placeholder="1500.00" /></td>
                            <td><input type="text" id=" UPPER_ABH_rig_name_1" /></td>
                            <td><input type="text" id=" UPPER_ABH_circ_hourse_1" /></td>
                            <td><input type="text" id=" UPPER_ABH_rig_name_2" /></td>
                            <td><input type="text" id=" UPPER_ABH_circ_hourse_2" /></td>
                            <td><input type="text" id=" UPPER_ABH_rig_name_3" /></td>
                            <td><input type="text" id=" UPPER_ABH_circ_hourse_3" /></td>
                            <td><input type="text" id=" UPPER_ABH_rig_name_4" /></td>
                            <td><input type="text" id=" UPPER_ABH_circ_hourse_4" /></td>
                            <td><input type="text" id=" UPPER_ABH_rig_name_5" /></td>
                            <td><input type="text" id=" UPPER_ABH_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Spacer ring</td>

                            <td><input type="text" id="Spacer_ring_serial_no" /></td>
                            <td><input type="text" id="Spacer_ring_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Spacer_ring_max_hours" disabled placeholder="1500.00" /></td>
                            <td><input type="text" id="Spacer_ring_rig_name_1" /></td>
                            <td><input type="text" id="Spacer_ring_circ_hourse_1" /></td>
                            <td><input type="text" id="Spacer_ring_rig_name_2" /></td>
                            <td><input type="text" id="Spacer_ring_circ_hourse_2" /></td>
                            <td><input type="text" id="Spacer_ring_rig_name_3" /></td>
                            <td><input type="text" id="Spacer_ring_circ_hourse_3" /></td>
                            <td><input type="text" id="Spacer_ring_rig_name_4" /></td>
                            <td><input type="text" id="Spacer_ring_circ_hourse_4" /></td>
                            <td><input type="text" id="Spacer_ring_rig_name_5" /></td>
                            <td><input type="text" id="Spacer_ring_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Top Sub</td>

                            <td><input type="text" id="Top_Sub_serial_no" /></td>
                            <td><input type="text" id="Top_Sub_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Top_Sub_max_hours" disabled placeholder="1200.00" /></td>
                            <td><input type="text" id="Top_Sub_rig_name_1" /></td>
                            <td><input type="text" id="Top_Sub_circ_hourse_1" /></td>
                            <td><input type="text" id="Top_Sub_rig_name_2" /></td>
                            <td><input type="text" id="Top_Sub_circ_hourse_2" /></td>
                            <td><input type="text" id="Top_Sub_rig_name_3" /></td>
                            <td><input type="text" id="Top_Sub_circ_hourse_3" /></td>
                            <td><input type="text" id="Top_Sub_rig_name_4" /></td>
                            <td><input type="text" id="Top_Sub_circ_hourse_4" /></td>
                            <td><input type="text" id="Top_Sub_rig_name_5" /></td>
                            <td><input type="text" id="Top_Sub_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> Sleeve Stabilizer </td>

                            <td><input type="text" id="Sleeve_Stabilizer_serial_no" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_max_hours" disabled placeholder="2000.00" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_rig_name_1" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_circ_hourse_1" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_rig_name_2" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_circ_hourse_2" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_rig_name_3" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_circ_hourse_3" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_rig_name_4" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_circ_hourse_4" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_rig_name_5" /></td>
                            <td><input type="text" id="Sleeve_Stabilizer_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> CENTER DRIVER, ADJ. REGAL</td>

                            <td><input type="text" id="CENTER_DRIVER_serial_no" /></td>
                            <td><input type="text" id="CENTER_DRIVER_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="CENTER_DRIVER_max_hours" disabled placeholder="500.00" /></td>
                            <td><input type="text" id="CENTER_DRIVER_rig_name_1" /></td>
                            <td><input type="text" id="CENTER_DRIVER_circ_hourse_1" /></td>
                            <td><input type="text" id="CENTER_DRIVER_rig_name_2" /></td>
                            <td><input type="text" id="CENTER_DRIVER_circ_hourse_2" /></td>
                            <td><input type="text" id="CENTER_DRIVER_rig_name_3" /></td>
                            <td><input type="text" id="CENTER_DRIVER_circ_hourse_3" /></td>
                            <td><input type="text" id="CENTER_DRIVER_rig_name_4" /></td>
                            <td><input type="text" id="CENTER_DRIVER_circ_hourse_4" /></td>
                            <td><input type="text" id="CENTER_DRIVER_rig_name_5" /></td>
                            <td><input type="text" id="CENTER_DRIVER_circ_hourse_5" /></td>
                        </tr>
                        <tr>
                            <td> UPPER COUPLING, ADJ. REGAL</td>

                            <td><input type="text" id="UPPER_COUPLING_serial_no" /></td>
                            <td><input type="text" id="UPPER_COUPLING_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="UPPER_COUPLING_max_hours" disabled placeholder="500.00" /></td>
                            <td><input type="text" id="UPPER_COUPLING_rig_name_1" /></td>
                            <td><input type="text" id="UPPER_COUPLING_circ_hourse_1" /></td>
                            <td><input type="text" id="UPPER_COUPLING_rig_name_2" /></td>
                            <td><input type="text" id="UPPER_COUPLING_circ_hourse_2" /></td>
                            <td><input type="text" id="UPPER_COUPLING_rig_name_3" /></td>
                            <td><input type="text" id="UPPER_COUPLING_circ_hourse_3" /></td>
                            <td><input type="text" id="UPPER_COUPLING_rig_name_4" /></td>
                            <td><input type="text" id="UPPER_COUPLING_circ_hourse_4" /></td>
                            <td><input type="text" id="UPPER_COUPLING_rig_name_5" /></td>
                            <td><input type="text" id="UPPER_COUPLING_circ_hourse_5" /></td>
                        </tr>

                        <tr>
                            <td> LOWER COUPLING, ADJ. REGAL</td>

                            <td><input type="text" id="LOWER_COUPLING_serial_no" /></td>
                            <td><input type="text" id="LOWER_COUPLING_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="LOWER_COUPLING_max_hours" disabled placeholder="500.00" /></td>
                            <td><input type="text" id="LOWER_COUPLING_rig_name_1" /></td>
                            <td><input type="text" id="LOWER_COUPLING_circ_hourse_1" /></td>
                            <td><input type="text" id="LOWER_COUPLING_rig_name_2" /></td>
                            <td><input type="text" id="LOWER_COUPLING_circ_hourse_2" /></td>
                            <td><input type="text" id="LOWER_COUPLING_rig_name_3" /></td>
                            <td><input type="text" id="LOWER_COUPLING_circ_hourse_3" /></td>
                            <td><input type="text" id="LOWER_COUPLING_rig_name_4" /></td>
                            <td><input type="text" id="LOWER_COUPLING_circ_hourse_4" /></td>
                            <td><input type="text" id="LOWER_COUPLING_rig_name_5" /></td>
                            <td><input type="text" id="LOWER_COUPLING_circ_hourse_5" /></td>
                        </tr>
                        <tr>
                            <td> UPPER DRIVER BOX, ADJ. REGAL</td>

                            <td><input type="text" id="UPPER_DRIVER_serial_no" /></td>
                            <td><input type="text" id="UPPER_DRIVER_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="UPPER_DRIVER_max_hours" disabled placeholder="500.00" /></td>
                            <td><input type="text" id="UPPER_DRIVER_rig_name_1" /></td>
                            <td><input type="text" id="UPPER_DRIVER_circ_hourse_1" /></td>
                            <td><input type="text" id="UPPER_DRIVER_rig_name_2" /></td>
                            <td><input type="text" id="UPPER_DRIVER_circ_hourse_2" /></td>
                            <td><input type="text" id="UPPER_DRIVER_rig_name_3" /></td>
                            <td><input type="text" id="UPPER_DRIVER_circ_hourse_3" /></td>
                            <td><input type="text" id="UPPER_DRIVER_rig_name_4" /></td>
                            <td><input type="text" id="UPPER_DRIVER_circ_hourse_4" /></td>
                            <td><input type="text" id="UPPER_DRIVER_rig_name_5" /></td>
                            <td><input type="text" id="UPPER_DRIVER_circ_hourse_5" /></td>
                        </tr>
                        <tr>
                            <td> ROTOR CATCH</td>

                            <td><input type="text" id="ROTOR_CATCH_serial_no" /></td>
                            <td><input type="text" id="ROTOR_CATCH_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="ROTOR_CATCH_max_hours" disabled placeholder="500.00" /></td>
                            <td><input type="text" id="ROTOR_CATCH_rig_name_1" /></td>
                            <td><input type="text" id="ROTOR_CATCH_circ_hourse_1" /></td>
                            <td><input type="text" id="ROTOR_CATCH_rig_name_2" /></td>
                            <td><input type="text" id="ROTOR_CATCH_circ_hourse_2" /></td>
                            <td><input type="text" id="ROTOR_CATCH_rig_name_3" /></td>
                            <td><input type="text" id="ROTOR_CATCH_circ_hourse_3" /></td>
                            <td><input type="text" id="ROTOR_CATCH_rig_name_4" /></td>
                            <td><input type="text" id="ROTOR_CATCH_circ_hourse_4" /></td>
                            <td><input type="text" id="ROTOR_CATCH_rig_name_5" /></td>
                            <td><input type="text" id="ROTOR_CATCH_circ_hourse_5" /></td>
                        </tr>
                        <tr>
                            <td> Transmission shaft</td>

                            <td><input type="text" id="Transmission_shaft_serial_no" /></td>
                            <td><input type="text" id="Transmission_shaft_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Transmission_shaft_max_hours" disabled placeholder="500.00" /></td>
                            <td><input type="text" id="Transmission_shaft_rig_name_1" /></td>
                            <td><input type="text" id="Transmission_shaft_circ_hourse_1" /></td>
                            <td><input type="text" id="Transmission_shaft_rig_name_2" /></td>
                            <td><input type="text" id="Transmission_shaft_circ_hourse_2" /></td>
                            <td><input type="text" id="Transmission_shaft_rig_name_3" /></td>
                            <td><input type="text" id="Transmission_shaft_circ_hourse_3" /></td>
                            <td><input type="text" id="Transmission_shaft_rig_name_4" /></td>
                            <td><input type="text" id="Transmission_shaft_circ_hourse_4" /></td>
                            <td><input type="text" id="Transmission_shaft_rig_name_5" /></td>
                            <td><input type="text" id="Transmission_shaft_circ_hourse_5" /></td>
                        </tr>
                        <tr>
                            <td> Bearing adapter</td>

                            <td><input type="text" id="Bearing_adapter_serial_no" /></td>
                            <td><input type="text" id="Bearing_adapter_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Bearing_adapter_max_hours" disabled placeholder="500.00" /></td>
                            <td><input type="text" id="Bearing_adapter_rig_name_1" /></td>
                            <td><input type="text" id="Bearing_adapter_circ_hourse_1" /></td>
                            <td><input type="text" id="Bearing_adapter_rig_name_2" /></td>
                            <td><input type="text" id="Bearing_adapter_circ_hourse_2" /></td>
                            <td><input type="text" id="Bearing_adapter_rig_name_3" /></td>
                            <td><input type="text" id="Bearing_adapter_circ_hourse_3" /></td>
                            <td><input type="text" id="Bearing_adapter_rig_name_4" /></td>
                            <td><input type="text" id="Bearing_adapter_circ_hourse_4" /></td>
                            <td><input type="text" id="Bearing_adapter_rig_name_5" /></td>
                            <td><input type="text" id="Bearing_adapter_circ_hourse_5" /></td>
                        </tr>
                        <tr>
                            <td> Rotor adapter</td>

                            <td><input type="text" id="Rotor_adapter_serial_no" /></td>
                            <td><input type="text" id="Rotor_adapter_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Rotor_adapter_max_hours" disabled placeholder="500.00" /></td>
                            <td><input type="text" id="Rotor_adapter_rig_name_1" /></td>
                            <td><input type="text" id="Rotor_adapter_circ_hourse_1" /></td>
                            <td><input type="text" id="Rotor_adapter_rig_name_2" /></td>
                            <td><input type="text" id="Rotor_adapter_circ_hourse_2" /></td>
                            <td><input type="text" id="Rotor_adapter_rig_name_3" /></td>
                            <td><input type="text" id="Rotor_adapter_circ_hourse_3" /></td>
                            <td><input type="text" id="Rotor_adapter_rig_name_4" /></td>
                            <td><input type="text" id="Rotor_adapter_circ_hourse_4" /></td>
                            <td><input type="text" id="Rotor_adapter_rig_name_5" /></td>
                            <td><input type="text" id="Rotor_adapter_circ_hourse_5" /></td>
                        </tr>
                        <tr>
                            <td> Flex shaft</td>

                            <td><input type="text" id="Flex_shaft_serial_no" /></td>
                            <td><input type="text" id="Flex_shaft_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Flex_shaft_max_hours" disabled placeholder="500.00" /></td>
                            <td><input type="text" id="Flex_shaft_rig_name_1" /></td>
                            <td><input type="text" id="Flex_shaft_circ_hourse_1" /></td>
                            <td><input type="text" id="Flex_shaft_rig_name_2" /></td>
                            <td><input type="text" id="Flex_shaft_circ_hourse_2" /></td>
                            <td><input type="text" id="Flex_shaft_rig_name_3" /></td>
                            <td><input type="text" id="Flex_shaft_circ_hourse_3" /></td>
                            <td><input type="text" id="Flex_shaft_rig_name_4" /></td>
                            <td><input type="text" id="Flex_shaft_circ_hourse_4" /></td>
                            <td><input type="text" id="Flex_shaft_rig_name_5" /></td>
                            <td><input type="text" id="Flex_shaft_circ_hourse_5" /></td>
                        </tr>
                        <tr>
                            <td> Stator adapter</td>

                            <td><input type="text" id="Stator_adapter_serial_no" /></td>
                            <td><input type="text" id="Stator_adapter_total_consumption" placeholder="0.00" /></td>
                            <td><input type="text" id="Stator_adapter_max_hours" disabled placeholder="1500.00" /></td>
                            <td><input type="text" id="Stator_adapter_rig_name_1" /></td>
                            <td><input type="text" id="Stator_adapter_circ_hourse_1" /></td>
                            <td><input type="text" id="Stator_adapter_rig_name_2" /></td>
                            <td><input type="text" id="Stator_adapter_circ_hourse_2" /></td>
                            <td><input type="text" id="Stator_adapter_rig_name_3" /></td>
                            <td><input type="text" id="Stator_adapter_circ_hourse_3" /></td>
                            <td><input type="text" id="Stator_adapter_rig_name_4" /></td>
                            <td><input type="text" id="Stator_adapter_circ_hourse_4" /></td>
                            <td><input type="text" id="Stator_adapter_rig_name_5" /></td>
                            <td><input type="text" id="Stator_adapter_circ_hourse_5" /></td>
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