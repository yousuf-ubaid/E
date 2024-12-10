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

<head>
    <style>
        .table-container {
            width: 100%;
            margin-bottom: 20px;
        }

        .highlight-gray {
            background-color: #D3D3D3;
            font-weight: bold;
        }


        .table-header {
            background-color: #F8C471;
            font-weight: bold;
            text-align: center;
        }

        input[type="text"] {
            width: 100%;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        /* .remarks {
            background-color: #FFFACD;
        } */

        /* .required-spares {
            font-weight: bold;
            color: red;
            text-align: center;
        } */
    </style>
</head>

<body>
    <form role="form" id="template_form" class="form-horizontal">
        <header class="head-title">
            <h5>
                <?php echo $vehicleDetails ?: ''; ?>
            </h5>
        </header>
        <div class="container mt-5">
            <div class="table-container">
                <table class="table table-bordered text-center">
                    <thead>

                        <tr class="table-header">
                            <th>No.</th>
                            <th>Serial Number</th>
                            <th>Description</th>
                            <th>OEM</th>
                            <th>OD</th>
                            <th>Length</th>
                            <th>Lobe</th>
                            <th>Stage</th>
                            <th>Current Location</th>
                            <th>Motor No</th>
                            <th>Hours</th>
                            <th>Length</th>
                            <th>Status</th>
                            <th>STATOR MINOR</th>
                            <th>Reline Date</th>
                            <th>Remarks</th>

                        </tr>

                    </thead>
                    <tbody>

                        <tr class="highlight-gray">
                            <td colspan="16" class="text-center font-weight-bold">9 5/8" Stator </td>
                        </tr>

                        <!-- Record Set 1 -->
                        <tr id="9_5_8_stator_1">
                            <td id="9_5_8_stator_1_no">
                                <input type="text" id="9_5_8_stator_1_no_in" name="9_5_8_stator_1_no">
                            </td>
                            <td id="9_5_8_stator_1_sn">
                                <input type="text" id="9_5_8_stator_1_sn_in" name="9_5_8_stator_1_sn">
                            </td>

                            <td id="9_5_8_stator_1_desc">
                                <input type="text" id="9_5_8_stator_1_desc_in" name="9_5_8_stator_1_desc">
                            </td>
                            <td id="9_5_8_stator_1_oem">
                                <input type="text" id="9_5_8_stator_1_oem_in" name="9_5_8_stator_1_oem">
                            </td>
                            <td id="9_5_8_stator_1_od">
                                <input type="text" id="9_5_8_stator_1_od_in" name="9_5_8_stator_1_od">
                            </td>
                            <td id="9_5_8_stator_1_length">
                                <input type="text" id="9_5_8_stator_1_length_in" name="9_5_8_stator_1_length">
                            </td>
                            <td id="9_5_8_stator_1_lobe">
                                <input type="text" id="9_5_8_stator_1_lobe_in" name="9_5_8_stator_1_lobe">
                            </td>
                            <td id="9_5_8_stator_1_stage">
                                <input type="text" id="9_5_8_stator_1_stage_in" name="9_5_8_stator_1_stage">
                            </td>
                            <td id="9_5_8_stator_1_loc">
                                <input type="text" id="9_5_8_stator_1_loc_in" name="9_5_8_stator_1_loc">
                            </td>
                            <td id="9_5_8_stator_1_motor_no">
                                <input type="text" id="9_5_8_stator_1_motor_no_in" name="9_5_8_stator_1_motor_no">
                            </td>
                            <td id="9_5_8_stator_1_hours">
                                <input type="text" id="9_5_8_stator_1_hours_in" name="9_5_8_stator_1_hours">
                            </td>
                            <td id="9_5_8_stator_1_length_2">
                                <input type="text" id="9_5_8_stator_1_length_2_in" name="9_5_8_stator_1_length_2">
                            </td>
                            <td id="9_5_8_stator_1_stat">
                                <input type="text" id="9_5_8_stator_1_stat_in" name="9_5_8_stator_1_stat">
                            </td>
                            <td id="9_5_8_stator_1_minor">
                                <input type="text" id="9_5_8_stator_1_minor_in" name="9_5_8_stator_1_minor">
                            </td>
                            <td id="9_5_8_stator_1_reline">
                                <input type="date" id="9_5_8_stator_1_reline_in" name="9_5_8_stator_1_reline">
                            </td>
                            <td id="9_5_8_stator_1_rem">
                                <input type="text" id="9_5_8_stator_1_rem_in" name="9_5_8_stator_1_rem">
                            </td>
                        </tr>

                        <!-- Record Set 2 -->
                        <tr id="9_5_8_stator_2">
                            <td id="9_5_8_stator_2_no">
                                <input type="text" id="9_5_8_stator_2_no_in" name="9_5_8_stator_2_no">
                            </td>
                            <td id="9_5_8_stator_2_sn">
                                <input type="text" id="9_5_8_stator_2_sn_in" name="9_5_8_stator_2_sn">
                            </td>

                            <td id="9_5_8_stator_2_desc">
                                <input type="text" id="9_5_8_stator_2_desc_in" name="9_5_8_stator_2_desc">
                            </td>
                            <td id="9_5_8_stator_2_oem">
                                <input type="text" id="9_5_8_stator_2_oem_in" name="9_5_8_stator_2_oem">
                            </td>
                            <td id="9_5_8_stator_2_od">
                                <input type="text" id="9_5_8_stator_2_od_in" name="9_5_8_stator_2_od">
                            </td>
                            <td id="9_5_8_stator_2_length">
                                <input type="text" id="9_5_8_stator_2_length_in" name="9_5_8_stator_2_length">
                            </td>
                            <td id="9_5_8_stator_2_lobe">
                                <input type="text" id="9_5_8_stator_2_lobe_in" name="9_5_8_stator_2_lobe">
                            </td>
                            <td id="9_5_8_stator_2_stage">
                                <input type="text" id="9_5_8_stator_2_stage_in" name="9_5_8_stator_2_stage">
                            </td>
                            <td id="9_5_8_stator_2_loc">
                                <input type="text" id="9_5_8_stator_2_loc_in" name="9_5_8_stator_2_loc">
                            </td>
                            <td id="9_5_8_stator_2_motor_no">
                                <input type="text" id="9_5_8_stator_2_motor_no_in" name="9_5_8_stator_2_motor_no">
                            </td>
                            <td id="9_5_8_stator_2_hours">
                                <input type="text" id="9_5_8_stator_2_hours_in" name="9_5_8_stator_2_hours">
                            </td>
                            <td id="9_5_8_stator_2_length_2">
                                <input type="text" id="9_5_8_stator_2_length_2_in" name="9_5_8_stator_2_length_2">
                            </td>
                            <td id="9_5_8_stator_2_stat">
                                <input type="text" id="9_5_8_stator_2_stat_in" name="9_5_8_stator_2_stat">
                            </td>
                            <td id="9_5_8_stator_2_minor">
                                <input type="text" id="9_5_8_stator_2_minor_in" name="9_5_8_stator_2_minor">
                            </td>
                            <td id="9_5_8_stator_2_reline">
                                <input type="date" id="9_5_8_stator_2_reline_in" name="9_5_8_stator_2_reline">
                            </td>
                            <td id="9_5_8_stator_2_rem">
                                <input type="text" id="9_5_8_stator_2_rem_in" name="9_5_8_stator_2_rem">
                            </td>
                        </tr>

                        <!-- Record Set 3 -->
                        <tr id="9_5_8_stator_3">
                            <td id="9_5_8_stator_3_no">
                                <input type="text" id="9_5_8_stator_3_no_in" name="9_5_8_stator_3_no">
                            </td>
                            <td id="9_5_8_stator_3_sn">
                                <input type="text" id="9_5_8_stator_3_sn_in" name="9_5_8_stator_3_sn">
                            </td>

                            <td id="9_5_8_stator_3_desc">
                                <input type="text" id="9_5_8_stator_3_desc_in" name="9_5_8_stator_3_desc">
                            </td>
                            <td id="9_5_8_stator_3_oem">
                                <input type="text" id="9_5_8_stator_3_oem_in" name="9_5_8_stator_3_oem">
                            </td>
                            <td id="9_5_8_stator_3_od">
                                <input type="text" id="9_5_8_stator_3_od_in" name="9_5_8_stator_3_od">
                            </td>
                            <td id="9_5_8_stator_3_length">
                                <input type="text" id="9_5_8_stator_3_length_in" name="9_5_8_stator_3_length">
                            </td>
                            <td id="9_5_8_stator_3_lobe">
                                <input type="text" id="9_5_8_stator_3_lobe_in" name="9_5_8_stator_3_lobe">
                            </td>
                            <td id="9_5_8_stator_3_stage">
                                <input type="text" id="9_5_8_stator_3_stage_in" name="9_5_8_stator_3_stage">
                            </td>
                            <td id="9_5_8_stator_3_loc">
                                <input type="text" id="9_5_8_stator_3_loc_in" name="9_5_8_stator_3_loc">
                            </td>
                            <td id="9_5_8_stator_3_motor_no">
                                <input type="text" id="9_5_8_stator_3_motor_no_in" name="9_5_8_stator_3_motor_no">
                            </td>
                            <td id="9_5_8_stator_3_hours">
                                <input type="text" id="9_5_8_stator_3_hours_in" name="9_5_8_stator_3_hours">
                            </td>
                            <td id="9_5_8_stator_3_length_2">
                                <input type="text" id="9_5_8_stator_3_length_2_in" name="9_5_8_stator_3_length_2">
                            </td>
                            <td id="9_5_8_stator_3_stat">
                                <input type="text" id="9_5_8_stator_3_stat_in" name="9_5_8_stator_3_stat">
                            </td>
                            <td id="9_5_8_stator_3_minor">
                                <input type="text" id="9_5_8_stator_3_minor_in" name="9_5_8_stator_3_minor">
                            </td>
                            <td id="9_5_8_stator_3_reline">
                                <input type="date" id="9_5_8_stator_3_reline_in" name="9_5_8_stator_3_reline">
                            </td>
                            <td id="9_5_8_stator_3_rem">
                                <input type="text" id="9_5_8_stator_3_rem_in" name="9_5_8_stator_3_rem">
                            </td>
                        </tr>

                        <!-- --------------------------------------------------------------------------------------------------------------- -->


                        <tr class="highlight-gray">
                            <td colspan="16" class="text-center font-weight-bold">8" Stators </td>
                        </tr>
                        <!-- Record Set 1 -->
                        <tr id="8_stator_1">
                            <td id="8_stator_1_no">
                                <input type="text" id="8_stator_1_no_in" name="8_stator_1_no">
                            </td>
                            <td id="8_stator_1_sn">
                                <input type="text" id="8_stator_1_sn_in" name="8_stator_1_sn">
                            </td>

                            <td id="8_stator_1_desc">
                                <input type="text" id="8_stator_1_desc_in" name="8_stator_1_desc">
                            </td>
                            <td id="8_stator_1_oem">
                                <input type="text" id="8_stator_1_oem_in" name="8_stator_1_oem">
                            </td>
                            <td id="8_stator_1_od">
                                <input type="text" id="8_stator_1_od_in" name="8_stator_1_od">
                            </td>
                            <td id="8_stator_1_length">
                                <input type="text" id="8_stator_1_length_in" name="8_stator_1_length">
                            </td>
                            <td id="8_stator_1_lobe">
                                <input type="text" id="8_stator_1_lobe_in" name="8_stator_1_lobe">
                            </td>
                            <td id="8_stator_1_stage">
                                <input type="text" id="8_stator_1_stage_in" name="8_stator_1_stage">
                            </td>
                            <td id="8_stator_1_loc">
                                <input type="text" id="8_stator_1_loc_in" name="8_stator_1_loc">
                            </td>
                            <td id="8_stator_1_motor_no">
                                <input type="text" id="8_stator_1_motor_no_in" name="8_stator_1_motor_no">
                            </td>
                            <td id="8_stator_1_hours">
                                <input type="text" id="8_stator_1_hours_in" name="8_stator_1_hours">
                            </td>
                            <td id="8_stator_1_length_2">
                                <input type="text" id="8_stator_1_length_2_in" name="8_stator_1_length_2">
                            </td>
                            <td id="8_stator_1_stat">
                                <input type="text" id="8_stator_1_stat_in" name="8_stator_1_stat">
                            </td>
                            <td id="8_stator_1_minor">
                                <input type="text" id="8_stator_1_minor_in" name="8_stator_1_minor">
                            </td>
                            <td id="8_stator_1_reline">
                                <input type="date" id="8_stator_1_reline_in" name="8_stator_1_reline">
                            </td>
                            <td id="8_stator_1_rem">
                                <input type="text" id="8_stator_1_rem_in" name="8_stator_1_rem">
                            </td>
                        </tr>

                        <!-- Record Set 2 -->
                        <tr id="8_stator_2">
                            <td id="8_stator_2_no">
                                <input type="text" id="8_stator_2_no_in" name="8_stator_2_no">
                            </td>
                            <td id="8_stator_2_sn">
                                <input type="text" id="8_stator_2_sn_in" name="8_stator_2_sn">
                            </td>

                            <td id="8_stator_2_desc">
                                <input type="text" id="8_stator_2_desc_in" name="8_stator_2_desc">
                            </td>
                            <td id="8_stator_2_oem">
                                <input type="text" id="8_stator_2_oem_in" name="8_stator_2_oem">
                            </td>
                            <td id="8_stator_2_od">
                                <input type="text" id="8_stator_2_od_in" name="8_stator_2_od">
                            </td>
                            <td id="8_stator_2_length">
                                <input type="text" id="8_stator_2_length_in" name="8_stator_2_length">
                            </td>
                            <td id="8_stator_2_lobe">
                                <input type="text" id="8_stator_2_lobe_in" name="8_stator_2_lobe">
                            </td>
                            <td id="8_stator_2_stage">
                                <input type="text" id="8_stator_2_stage_in" name="8_stator_2_stage">
                            </td>
                            <td id="8_stator_2_loc">
                                <input type="text" id="8_stator_2_loc_in" name="8_stator_2_loc">
                            </td>
                            <td id="8_stator_2_motor_no">
                                <input type="text" id="8_stator_2_motor_no_in" name="8_stator_2_motor_no">
                            </td>
                            <td id="8_stator_2_hours">
                                <input type="text" id="8_stator_2_hours_in" name="8_stator_2_hours">
                            </td>
                            <td id="8_stator_2_length_2">
                                <input type="text" id="8_stator_2_length_2_in" name="8_stator_2_length_2">
                            </td>
                            <td id="8_stator_2_stat">
                                <input type="text" id="8_stator_2_stat_in" name="8_stator_2_stat">
                            </td>
                            <td id="8_stator_2_minor">
                                <input type="text" id="8_stator_2_minor_in" name="8_stator_2_minor">
                            </td>
                            <td id="8_stator_2_reline">
                                <input type="date" id="8_stator_2_reline_in" name="8_stator_2_reline">
                            </td>
                            <td id="8_stator_2_rem">
                                <input type="text" id="8_stator_2_rem_in" name="8_stator_2_rem">
                            </td>
                        </tr>

                        <!-- Record Set 3 -->
                        <tr id="8_stator_3">
                            <td id="8_stator_3_no">
                                <input type="text" id="8_stator_3_no_in" name="8_stator_3_no">
                            </td>
                            <td id="8_stator_3_sn">
                                <input type="text" id="8_stator_3_sn_in" name="8_stator_3_sn">
                            </td>

                            <td id="8_stator_3_desc">
                                <input type="text" id="8_stator_3_desc_in" name="8_stator_3_desc">
                            </td>
                            <td id="8_stator_3_oem">
                                <input type="text" id="8_stator_3_oem_in" name="8_stator_3_oem">
                            </td>
                            <td id="8_stator_3_od">
                                <input type="text" id="8_stator_3_od_in" name="8_stator_3_od">
                            </td>
                            <td id="8_stator_3_length">
                                <input type="text" id="8_stator_3_length_in" name="8_stator_3_length">
                            </td>
                            <td id="8_stator_3_lobe">
                                <input type="text" id="8_stator_3_lobe_in" name="8_stator_3_lobe">
                            </td>
                            <td id="8_stator_3_stage">
                                <input type="text" id="8_stator_3_stage_in" name="8_stator_3_stage">
                            </td>
                            <td id="8_stator_3_loc">
                                <input type="text" id="8_stator_3_loc_in" name="8_stator_3_loc">
                            </td>
                            <td id="8_stator_3_motor_no">
                                <input type="text" id="8_stator_3_motor_no_in" name="8_stator_3_motor_no">
                            </td>
                            <td id="8_stator_3_hours">
                                <input type="text" id="8_stator_3_hours_in" name="8_stator_3_hours">
                            </td>
                            <td id="8_stator_3_length_2">
                                <input type="text" id="8_stator_3_length_2_in" name="8_stator_3_length_2">
                            </td>
                            <td id="8_stator_3_stat">
                                <input type="text" id="8_stator_3_stat_in" name="8_stator_3_stat">
                            </td>
                            <td id="8_stator_3_minor">
                                <input type="text" id="8_stator_3_minor_in" name="8_stator_3_minor">
                            </td>
                            <td id="8_stator_3_reline">
                                <input type="date" id="8_stator_3_reline_in" name="8_stator_3_reline">
                            </td>
                            <td id="8_stator_3_rem">
                                <input type="text" id="8_stator_3_rem_in" name="8_stator_3_rem">
                            </td>
                        </tr>


                        <tr class="highlight-gray">
                            <td colspan="16" class="text-center font-weight-bold">6 3/4" & 6 1/2" Stator</td>
                        </tr>

                        <!-- Record Set 1 -->
                        <!-- Record Set 1 -->
                        <tr id="6_3_4_stator_1">
                            <td id="6_3_4_stator_1_no">
                                <input type="text" id="6_3_4_stator_1_no_in" name="6_3_4_stator_1_no">
                            </td>
                            <td id="6_3_4_stator_1_sn">
                                <input type="text" id="6_3_4_stator_1_sn_in" name="6_3_4_stator_1_sn">
                            </td>

                            <td id="6_3_4_stator_1_desc">
                                <input type="text" id="6_3_4_stator_1_desc_in" name="6_3_4_stator_1_desc">
                            </td>
                            <td id="6_3_4_stator_1_oem">
                                <input type="text" id="6_3_4_stator_1_oem_in" name="6_3_4_stator_1_oem">
                            </td>
                            <td id="6_3_4_stator_1_od">
                                <input type="text" id="6_3_4_stator_1_od_in" name="6_3_4_stator_1_od">
                            </td>
                            <td id="6_3_4_stator_1_length">
                                <input type="text" id="6_3_4_stator_1_length_in" name="6_3_4_stator_1_length">
                            </td>
                            <td id="6_3_4_stator_1_lobe">
                                <input type="text" id="6_3_4_stator_1_lobe_in" name="6_3_4_stator_1_lobe">
                            </td>
                            <td id="6_3_4_stator_1_stage">
                                <input type="text" id="6_3_4_stator_1_stage_in" name="6_3_4_stator_1_stage">
                            </td>
                            <td id="6_3_4_stator_1_loc">
                                <input type="text" id="6_3_4_stator_1_loc_in" name="6_3_4_stator_1_loc">
                            </td>
                            <td id="6_3_4_stator_1_motor_no">
                                <input type="text" id="6_3_4_stator_1_motor_no_in" name="6_3_4_stator_1_motor_no">
                            </td>
                            <td id="6_3_4_stator_1_hours">
                                <input type="text" id="6_3_4_stator_1_hours_in" name="6_3_4_stator_1_hours">
                            </td>
                            <td id="6_3_4_stator_1_Length_2">
                                <input type="text" id="6_3_4_stator_1_Length_2_in" name="6_3_4_stator_1_Length_2">
                            </td>
                            <td id="6_3_4_stator_1_stat">
                                <input type="text" id="6_3_4_stator_1_stat_in" name="6_3_4_stator_1_stat">
                            </td>
                            <td id="6_3_4_stator_1_minor">
                                <input type="text" id="6_3_4_stator_1_minor_in" name="6_3_4_stator_1_minor">
                            </td>
                            <td id="6_3_4_stator_1_reline">
                                <input type="date" id="6_3_4_stator_1_reline_in" name="6_3_4_stator_1_reline">
                            </td>
                            <td id="6_3_4_stator_1_rem">
                                <input type="text" id="6_3_4_stator_1_rem_in" name="6_3_4_stator_1_rem">
                            </td>
                        </tr>

                        <!-- Record Set 2 -->
                        <tr id="6_3_4_stator_2">
                            <td id="6_3_4_stator_2_no">
                                <input type="text" id="6_3_4_stator_2_no_in" name="6_3_4_stator_2_no">
                            </td>
                            <td id="6_3_4_stator_2_sn">
                                <input type="text" id="6_3_4_stator_2_sn_in" name="6_3_4_stator_2_sn">
                            </td>

                            <td id="6_3_4_stator_2_desc">
                                <input type="text" id="6_3_4_stator_2_desc_in" name="6_3_4_stator_2_desc">
                            </td>
                            <td id="6_3_4_stator_2_oem">
                                <input type="text" id="6_3_4_stator_2_oem_in" name="6_3_4_stator_2_oem">
                            </td>
                            <td id="6_3_4_stator_2_od">
                                <input type="text" id="6_3_4_stator_2_od_in" name="6_3_4_stator_2_od">
                            </td>
                            <td id="6_3_4_stator_2_length">
                                <input type="text" id="6_3_4_stator_2_length_in" name="6_3_4_stator_2_length">
                            </td>
                            <td id="6_3_4_stator_2_lobe">
                                <input type="text" id="6_3_4_stator_2_lobe_in" name="6_3_4_stator_2_lobe">
                            </td>
                            <td id="6_3_4_stator_2_stage">
                                <input type="text" id="6_3_4_stator_2_stage_in" name="6_3_4_stator_2_stage">
                            </td>
                            <td id="6_3_4_stator_2_loc">
                                <input type="text" id="6_3_4_stator_2_loc_in" name="6_3_4_stator_2_loc">
                            </td>
                            <td id="6_3_4_stator_2_motor_no">
                                <input type="text" id="6_3_4_stator_2_motor_no_in" name="6_3_4_stator_2_motor_no">
                            </td>
                            <td id="6_3_4_stator_2_hours">
                                <input type="text" id="6_3_4_stator_2_hours_in" name="6_3_4_stator_2_hours">
                            </td>
                            <td id="6_3_4_stator_2_Length_2">
                                <input type="text" id="6_3_4_stator_2_Length_2_in" name="6_3_4_stator_2_Length_2">
                            </td>
                            <td id="6_3_4_stator_2_stat">
                                <input type="text" id="6_3_4_stator_2_stat_in" name="6_3_4_stator_2_stat">
                            </td>
                            <td id="6_3_4_stator_2_minor">
                                <input type="text" id="6_3_4_stator_2_minor_in" name="6_3_4_stator_2_minor">
                            </td>
                            <td id="6_3_4_stator_2_reline">
                                <input type="date" id="6_3_4_stator_2_reline_in" name="6_3_4_stator_2_reline">
                            </td>
                            <td id="6_3_4_stator_2_rem">
                                <input type="text" id="6_3_4_stator_2_rem_in" name="6_3_4_stator_2_rem">
                            </td>
                        </tr>

                        <!-- Record Set 3 -->
                        <tr id="6_3_4_stator_3">
                            <td id="6_3_4_stator_3_no">
                                <input type="text" id="6_3_4_stator_3_no_in" name="6_3_4_stator_3_no">
                            </td>
                            <td id="6_3_4_stator_3_sn">
                                <input type="text" id="6_3_4_stator_3_sn_in" name="6_3_4_stator_3_sn">
                            </td>

                            <td id="6_3_4_stator_3_desc">
                                <input type="text" id="6_3_4_stator_3_desc_in" name="6_3_4_stator_3_desc">
                            </td>
                            <td id="6_3_4_stator_3_oem">
                                <input type="text" id="6_3_4_stator_3_oem_in" name="6_3_4_stator_3_oem">
                            </td>
                            <td id="6_3_4_stator_3_od">
                                <input type="text" id="6_3_4_stator_3_od_in" name="6_3_4_stator_3_od">
                            </td>
                            <td id="6_3_4_stator_3_length">
                                <input type="text" id="6_3_4_stator_3_length_in" name="6_3_4_stator_3_length">
                            </td>
                            <td id="6_3_4_stator_3_lobe">
                                <input type="text" id="6_3_4_stator_3_lobe_in" name="6_3_4_stator_3_lobe">
                            </td>
                            <td id="6_3_4_stator_3_stage">
                                <input type="text" id="6_3_4_stator_3_stage_in" name="6_3_4_stator_3_stage">
                            </td>
                            <td id="6_3_4_stator_3_loc">
                                <input type="text" id="6_3_4_stator_3_loc_in" name="6_3_4_stator_3_loc">
                            </td>
                            <td id="6_3_4_stator_3_motor_no">
                                <input type="text" id="6_3_4_stator_3_motor_no_in" name="6_3_4_stator_3_motor_no">
                            </td>
                            <td id="6_3_4_stator_3_hours">
                                <input type="text" id="6_3_4_stator_3_hours_in" name="6_3_4_stator_3_hours">
                            </td>
                            <td id="6_3_4_stator_3_Length_2">
                                <input type="text" id="6_3_4_stator_3_Length_2_in" name="6_3_4_stator_3_Length_2">
                            </td>
                            <td id="6_3_4_stator_3_stat">
                                <input type="text" id="6_3_4_stator_3_stat_in" name="6_3_4_stator_3_stat">
                            </td>
                            <td id="6_3_4_stator_3_minor">
                                <input type="text" id="6_3_4_stator_3_minor_in" name="6_3_4_stator_3_minor">
                            </td>
                            <td id="6_3_4_stator_3_reline">
                                <input type="date" id="6_3_4_stator_3_reline_in" name="6_3_4_stator_3_reline">
                            </td>
                            <td id="6_3_4_stator_3_rem">
                                <input type="text" id="6_3_4_stator_3_rem_in" name="6_3_4_stator_3_rem">
                            </td>
                        </tr>
                        <tr class="highlight-gray">
                            <td colspan="16" class="text-center font-weight-bold"> 4 3/4" Stator</td>
                        </tr>

                        <!-- Record Set 1 -->
                        <tr id="4_3_4_stator_1">
                            <td id="4_3_4_stator_1_no">
                                <input type="text" id="4_3_4_stator_1_no_in" name="4_3_4_stator_1_no">
                            </td>
                            <td id="4_3_4_stator_1_sn">
                                <input type="text" id="4_3_4_stator_1_sn_in" name="4_3_4_stator_1_sn">
                            </td>

                            <td id="4_3_4_stator_1_desc">
                                <input type="text" id="4_3_4_stator_1_desc_in" name="4_3_4_stator_1_desc">
                            </td>
                            <td id="4_3_4_stator_1_oem">
                                <input type="text" id="4_3_4_stator_1_oem_in" name="4_3_4_stator_1_oem">
                            </td>
                            <td id="4_3_4_stator_1_od">
                                <input type="text" id="4_3_4_stator_1_od_in" name="4_3_4_stator_1_od">
                            </td>
                            <td id="4_3_4_stator_1_length">
                                <input type="text" id="4_3_4_stator_1_length_in" name="4_3_4_stator_1_length">
                            </td>
                            <td id="4_3_4_stator_1_lobe">
                                <input type="text" id="4_3_4_stator_1_lobe_in" name="4_3_4_stator_1_lobe">
                            </td>
                            <td id="4_3_4_stator_1_stage">
                                <input type="text" id="4_3_4_stator_1_stage_in" name="4_3_4_stator_1_stage">
                            </td>
                            <td id="4_3_4_stator_1_loc">
                                <input type="text" id="4_3_4_stator_1_loc_in" name="4_3_4_stator_1_loc">
                            </td>
                            <td id="4_3_4_stator_1_motor_no">
                                <input type="text" id="4_3_4_stator_1_motor_no_in" name="4_3_4_stator_1_motor_no">
                            </td>
                            <td id="4_3_4_stator_1_hours">
                                <input type="text" id="4_3_4_stator_1_hours_in" name="4_3_4_stator_1_hours">
                            </td>
                            <td id="4_3_4_stator_1_Length_2">
                                <input type="text" id="4_3_4_stator_1_Length_2_in" name="4_3_4_stator_1_Length_2">
                            </td>
                            <td id="4_3_4_stator_1_stat">
                                <input type="text" id="4_3_4_stator_1_stat_in" name="4_3_4_stator_1_stat">
                            </td>
                            <td id="4_3_4_stator_1_minor">
                                <input type="text" id="4_3_4_stator_1_minor_in" name="4_3_4_stator_1_minor">
                            </td>
                            <td id="4_3_4_stator_1_reline">
                                <input type="date" id="4_3_4_stator_1_reline_in" name="4_3_4_stator_1_reline">
                            </td>
                            <td id="4_3_4_stator_1_rem">
                                <input type="text" id="4_3_4_stator_1_rem_in" name="4_3_4_stator_1_rem">
                            </td>
                        </tr>

                        <!-- Record Set 2 -->
                        <tr id="4_3_4_stator_2">
                            <td id="4_3_4_stator_2_no">
                                <input type="text" id="4_3_4_stator_2_no_in" name="4_3_4_stator_2_no">
                            </td>
                            <td id="4_3_4_stator_2_sn">
                                <input type="text" id="4_3_4_stator_2_sn_in" name="4_3_4_stator_2_sn">
                            </td>

                            <td id="4_3_4_stator_2_desc">
                                <input type="text" id="4_3_4_stator_2_desc_in" name="4_3_4_stator_2_desc">
                            </td>
                            <td id="4_3_4_stator_2_oem">
                                <input type="text" id="4_3_4_stator_2_oem_in" name="4_3_4_stator_2_oem">
                            </td>
                            <td id="4_3_4_stator_2_od">
                                <input type="text" id="4_3_4_stator_2_od_in" name="4_3_4_stator_2_od">
                            </td>
                            <td id="4_3_4_stator_2_length">
                                <input type="text" id="4_3_4_stator_2_length_in" name="4_3_4_stator_2_length">
                            </td>
                            <td id="4_3_4_stator_2_lobe">
                                <input type="text" id="4_3_4_stator_2_lobe_in" name="4_3_4_stator_2_lobe">
                            </td>
                            <td id="4_3_4_stator_2_stage">
                                <input type="text" id="4_3_4_stator_2_stage_in" name="4_3_4_stator_2_stage">
                            </td>
                            <td id="4_3_4_stator_2_loc">
                                <input type="text" id="4_3_4_stator_2_loc_in" name="4_3_4_stator_2_loc">
                            </td>
                            <td id="4_3_4_stator_2_motor_no">
                                <input type="text" id="4_3_4_stator_2_motor_no_in" name="4_3_4_stator_2_motor_no">
                            </td>
                            <td id="4_3_4_stator_2_hours">
                                <input type="text" id="4_3_4_stator_2_hours_in" name="4_3_4_stator_2_hours">
                            </td>
                            <td id="4_3_4_stator_2_Length_2">
                                <input type="text" id="4_3_4_stator_2_Length_2_in" name="4_3_4_stator_2_Length_2">
                            </td>
                            <td id="4_3_4_stator_2_stat">
                                <input type="text" id="4_3_4_stator_2_stat_in" name="4_3_4_stator_2_stat">
                            </td>
                            <td id="4_3_4_stator_2_minor">
                                <input type="text" id="4_3_4_stator_2_minor_in" name="4_3_4_stator_2_minor">
                            </td>
                            <td id="4_3_4_stator_2_reline">
                                <input type="date" id="4_3_4_stator_2_reline_in" name="4_3_4_stator_2_reline">
                            </td>
                            <td id="4_3_4_stator_2_rem">
                                <input type="text" id="4_3_4_stator_2_rem_in" name="4_3_4_stator_2_rem">
                            </td>
                        </tr>

                        <!-- Record Set 3 -->
                        <tr id="4_3_4_stator_3">
                            <td id="4_3_4_stator_3_no">
                                <input type="text" id="4_3_4_stator_3_no_in" name="4_3_4_stator_3_no">
                            </td>
                            <td id="4_3_4_stator_3_sn">
                                <input type="text" id="4_3_4_stator_3_sn_in" name="4_3_4_stator_3_sn">
                            </td>

                            <td id="4_3_4_stator_3_desc">
                                <input type="text" id="4_3_4_stator_3_desc_in" name="4_3_4_stator_3_desc">
                            </td>
                            <td id="4_3_4_stator_3_oem">
                                <input type="text" id="4_3_4_stator_3_oem_in" name="4_3_4_stator_3_oem">
                            </td>
                            <td id="4_3_4_stator_3_od">
                                <input type="text" id="4_3_4_stator_3_od_in" name="4_3_4_stator_3_od">
                            </td>
                            <td id="4_3_4_stator_3_length">
                                <input type="text" id="4_3_4_stator_3_length_in" name="4_3_4_stator_3_length">
                            </td>
                            <td id="4_3_4_stator_3_lobe">
                                <input type="text" id="4_3_4_stator_3_lobe_in" name="4_3_4_stator_3_lobe">
                            </td>
                            <td id="4_3_4_stator_3_stage">
                                <input type="text" id="4_3_4_stator_3_stage_in" name="4_3_4_stator_3_stage">
                            </td>
                            <td id="4_3_4_stator_3_loc">
                                <input type="text" id="4_3_4_stator_3_loc_in" name="4_3_4_stator_3_loc">
                            </td>
                            <td id="4_3_4_stator_3_motor_no">
                                <input type="text" id="4_3_4_stator_3_motor_no_in" name="4_3_4_stator_3_motor_no">
                            </td>
                            <td id="4_3_4_stator_3_hours">
                                <input type="text" id="4_3_4_stator_3_hours_in" name="4_3_4_stator_3_hours">
                            </td>
                            <td id="4_3_4_stator_3_Length_2">
                                <input type="text" id="4_3_4_stator_3_Length_2_in" name="4_3_4_stator_3_Length_2">
                            </td>
                            <td id="4_3_4_stator_3_stat">
                                <input type="text" id="4_3_4_stator_3_stat_in" name="4_3_4_stator_3_stat">
                            </td>
                            <td id="4_3_4_stator_3_minor">
                                <input type="text" id="4_3_4_stator_3_minor_in" name="4_3_4_stator_3_minor">
                            </td>
                            <td id="4_3_4_stator_3_reline">
                                <input type="date" id="4_3_4_stator_3_reline_in" name="4_3_4_stator_3_reline">
                            </td>
                            <td id="4_3_4_stator_3_rem">
                                <input type="text" id="4_3_4_stator_3_rem_in" name="4_3_4_stator_3_rem">
                            </td>
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