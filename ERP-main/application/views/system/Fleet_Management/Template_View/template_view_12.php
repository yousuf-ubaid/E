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
                            <th>Size</th>
                            <th>Length</th>
                            <th>Lobe</th>
                            <th>Stage</th>
                            <th>Current Location</th>
                            <th>Motor No</th>
                            <th>Rotor Mean</th>
                            <th>Status</th>
                            <th>Plating Type</th>
                            <th>Coating Date</th>
                            <th>Remarks</th>

                        </tr>

                    </thead>
                    <tbody>

                        <tr class="highlight-gray">
                            <td colspan="16" class="text-center font-weight-bold">9 5/8" Rotor </td>
                        </tr>
                        <!-- Record Set 1 -->
                        <tr id="9_5_8_roto_1">
                            <td id="9_5_8_roto_1_no">
                                <input type="text" id="9_5_8_roto_1_no_in" name="9_5_8_roto_1_no">
                            </td>
                            <td id="9_5_8_roto_1_sn">
                                <input type="text" id="9_5_8_roto_1_sn_in" name="9_5_8_roto_1_sn">
                            </td>
                            <td id="9_5_8_roto_1_desc">
                                <input type="text" id="9_5_8_roto_1_desc_in" name="9_5_8_roto_1_desc">
                            </td>
                            <td id="9_5_8_roto_1_oem">
                                <input type="text" id="9_5_8_roto_1_oem_in" name="9_5_8_roto_1_oem">
                            </td>
                            <td id="9_5_8_roto_1_size">
                                <input type="text" id="9_5_8_roto_1_size_in" name="9_5_8_roto_1_size">
                            </td>
                            <td id="9_5_8_roto_1_length">
                                <input type="text" id="9_5_8_roto_1_length_in" name="9_5_8_roto_1_length">
                            </td>
                            <td id="9_5_8_roto_1_lobe">
                                <input type="text" id="9_5_8_roto_1_lobe_in" name="9_5_8_roto_1_lobe">
                            </td>
                            <td id="9_5_8_roto_1_stage">
                                <input type="text" id="9_5_8_roto_1_stage_in" name="9_5_8_roto_1_stage">
                            </td>
                            <td id="9_5_8_roto_1_loc">
                                <input type="text" id="9_5_8_roto_1_loc_in" name="9_5_8_roto_1_loc">
                            </td>
                            <td id="9_5_8_roto_1_motor_no">
                                <input type="text" id="9_5_8_roto_1_motor_no_in" name="9_5_8_roto_1_motor_no">
                            </td>
                            <td id="9_5_8_roto_1_rotor_mean">
                                <input type="text" id="9_5_8_roto_1_rotor_mean_in" name="9_5_8_roto_1_rotor_mean">
                            </td>
                            <td id="9_5_8_roto_1_stat">
                                <input type="text" id="9_5_8_roto_1_stat_in" name="9_5_8_roto_1_stat">
                            </td>
                            <td id="9_5_8_roto_1_plating_type">
                                <input type="text" id="9_5_8_roto_1_plating_type_in" name="9_5_8_roto_1_plating_type">
                            </td>
                            <td id="9_5_8_roto_1_coating_date">
                                <input type="date" id="9_5_8_roto_1_coating_date_in" name="9_5_8_roto_1_coating_date">
                            </td>
                            <td id="9_5_8_roto_1_rem">
                                <input type="text" id="9_5_8_roto_1_rem_in" name="9_5_8_roto_1_rem">
                            </td>
                        </tr>

                        <!-- Record Set 2 -->
                        <tr id="9_5_8_roto_2">
                            <td id="9_5_8_roto_2_no">
                                <input type="text" id="9_5_8_roto_2_no_in" name="9_5_8_roto_2_no">
                            </td>
                            <td id="9_5_8_roto_2_sn">
                                <input type="text" id="9_5_8_roto_2_sn_in" name="9_5_8_roto_2_sn">
                            </td>
                            <td id="9_5_8_roto_2_desc">
                                <input type="text" id="9_5_8_roto_2_desc_in" name="9_5_8_roto_2_desc">
                            </td>
                            <td id="9_5_8_roto_2_oem">
                                <input type="text" id="9_5_8_roto_2_oem_in" name="9_5_8_roto_2_oem">
                            </td>
                            <td id="9_5_8_roto_2_size">
                                <input type="text" id="9_5_8_roto_2_size_in" name="9_5_8_roto_2_size">
                            </td>
                            <td id="9_5_8_roto_2_length">
                                <input type="text" id="9_5_8_roto_2_length_in" name="9_5_8_roto_2_length">
                            </td>
                            <td id="9_5_8_roto_2_lobe">
                                <input type="text" id="9_5_8_roto_2_lobe_in" name="9_5_8_roto_2_lobe">
                            </td>
                            <td id="9_5_8_roto_2_stage">
                                <input type="text" id="9_5_8_roto_2_stage_in" name="9_5_8_roto_2_stage">
                            </td>
                            <td id="9_5_8_roto_2_loc">
                                <input type="text" id="9_5_8_roto_2_loc_in" name="9_5_8_roto_2_loc">
                            </td>
                            <td id="9_5_8_roto_2_motor_no">
                                <input type="text" id="9_5_8_roto_2_motor_no_in" name="9_5_8_roto_2_motor_no">
                            </td>
                            <td id="9_5_8_roto_2_rotor_mean">
                                <input type="text" id="9_5_8_roto_2_rotor_mean_in" name="9_5_8_roto_2_rotor_mean">
                            </td>
                            <td id="9_5_8_roto_2_stat">
                                <input type="text" id="9_5_8_roto_2_stat_in" name="9_5_8_roto_2_stat">
                            </td>
                            <td id="9_5_8_roto_2_plating_type">
                                <input type="text" id="9_5_8_roto_2_plating_type_in" name="9_5_8_roto_2_plating_type">
                            </td>
                            <td id="9_5_8_roto_2_coating_date">
                                <input type="date" id="9_5_8_roto_2_coating_date_in" name="9_5_8_roto_2_coating_date">
                            </td>
                            <td id="9_5_8_roto_2_rem">
                                <input type="text" id="9_5_8_roto_2_rem_in" name="9_5_8_roto_2_rem">
                            </td>
                        </tr>

                        <!-- Record Set 3 -->
                        <tr id="9_5_8_roto_3">
                            <td id="9_5_8_roto_3_no">
                                <input type="text" id="9_5_8_roto_3_no_in" name="9_5_8_roto_3_no">
                            </td>
                            <td id="9_5_8_roto_3_sn">
                                <input type="text" id="9_5_8_roto_3_sn_in" name="9_5_8_roto_3_sn">
                            </td>
                            <td id="9_5_8_roto_3_desc">
                                <input type="text" id="9_5_8_roto_3_desc_in" name="9_5_8_roto_3_desc">
                            </td>
                            <td id="9_5_8_roto_3_oem">
                                <input type="text" id="9_5_8_roto_3_oem_in" name="9_5_8_roto_3_oem">
                            </td>
                            <td id="9_5_8_roto_3_size">
                                <input type="text" id="9_5_8_roto_3_size_in" name="9_5_8_roto_3_size">
                            </td>
                            <td id="9_5_8_roto_3_length">
                                <input type="text" id="9_5_8_roto_3_length_in" name="9_5_8_roto_3_length">
                            </td>
                            <td id="9_5_8_roto_3_lobe">
                                <input type="text" id="9_5_8_roto_3_lobe_in" name="9_5_8_roto_3_lobe">
                            </td>
                            <td id="9_5_8_roto_3_stage">
                                <input type="text" id="9_5_8_roto_3_stage_in" name="9_5_8_roto_3_stage">
                            </td>
                            <td id="9_5_8_roto_3_loc">
                                <input type="text" id="9_5_8_roto_3_loc_in" name="9_5_8_roto_3_loc">
                            </td>
                            <td id="9_5_8_roto_3_motor_no">
                                <input type="text" id="9_5_8_roto_3_motor_no_in" name="9_5_8_roto_3_motor_no">
                            </td>
                            <td id="9_5_8_roto_3_rotor_mean">
                                <input type="text" id="9_5_8_roto_3_rotor_mean_in" name="9_5_8_roto_3_rotor_mean">
                            </td>
                            <td id="9_5_8_roto_3_stat">
                                <input type="text" id="9_5_8_roto_3_stat_in" name="9_5_8_roto_3_stat">
                            </td>
                            <td id="9_5_8_roto_3_plating_type">
                                <input type="text" id="9_5_8_roto_3_plating_type_in" name="9_5_8_roto_3_plating_type">
                            </td>
                            <td id="9_5_8_roto_3_coating_date">
                                <input type="date" id="9_5_8_roto_3_coating_date_in" name="9_5_8_roto_3_coating_date">
                            </td>
                            <td id="9_5_8_roto_3_rem">
                                <input type="text" id="9_5_8_roto_3_rem_in" name="9_5_8_roto_3_rem">
                            </td>
                        </tr>

                        <!-- --------------------------------------------------------------------------------------------------------------- -->


                        <tr class="highlight-gray">
                            <td colspan="16" class="text-center font-weight-bold">8" Rotor </td>
                        </tr>

                        <!-- Record Set 1 -->
                        <tr id="8_roto_1">
                            <td id="8_roto_1_no">
                                <input type="text" id="8_roto_1_no_in" name="8_roto_1_no">
                            </td>
                            <td id="8_roto_1_sn">
                                <input type="text" id="8_roto_1_sn_in" name="8_roto_1_sn">
                            </td>
                            <td id="8_roto_1_desc">
                                <input type="text" id="8_roto_1_desc_in" name="8_roto_1_desc">
                            </td>
                            <td id="8_roto_1_oem">
                                <input type="text" id="8_roto_1_oem_in" name="8_roto_1_oem">
                            </td>
                            <td id="8_roto_1_size">
                                <input type="text" id="8_roto_1_size_in" name="8_roto_1_size">
                            </td>
                            <td id="8_roto_1_length">
                                <input type="text" id="8_roto_1_length_in" name="8_roto_1_length">
                            </td>
                            <td id="8_roto_1_lobe">
                                <input type="text" id="8_roto_1_lobe_in" name="8_roto_1_lobe">
                            </td>
                            <td id="8_roto_1_stage">
                                <input type="text" id="8_roto_1_stage_in" name="8_roto_1_stage">
                            </td>
                            <td id="8_roto_1_loc">
                                <input type="text" id="8_roto_1_loc_in" name="8_roto_1_loc">
                            </td>
                            <td id="8_roto_1_motor_no">
                                <input type="text" id="8_roto_1_motor_no_in" name="8_roto_1_motor_no">
                            </td>
                            <td id="8_roto_1_rotor_mean">
                                <input type="text" id="8_roto_1_rotor_mean_in" name="8_roto_1_rotor_mean">
                            </td>
                            <td id="8_roto_1_stat">
                                <input type="text" id="8_roto_1_stat_in" name="8_roto_1_stat">
                            </td>
                            <td id="8_roto_1_plating_type">
                                <input type="text" id="8_roto_1_plating_type_in" name="8_roto_1_plating_type">
                            </td>
                            <td id="8_roto_1_coating_date">
                                <input type="date" id="8_roto_1_coating_date_in" name="8_roto_1_coating_date">
                            </td>
                            <td id="8_roto_1_rem">
                                <input type="text" id="8_roto_1_rem_in" name="8_roto_1_rem">
                            </td>
                        </tr>

                        <!-- Record Set 2 -->
                        <tr id="8_roto_2">
                            <td id="8_roto_2_no">
                                <input type="text" id="8_roto_2_no_in" name="8_roto_2_no">
                            </td>
                            <td id="8_roto_2_sn">
                                <input type="text" id="8_roto_2_sn_in" name="8_roto_2_sn">
                            </td>
                            <td id="8_roto_2_desc">
                                <input type="text" id="8_roto_2_desc_in" name="8_roto_2_desc">
                            </td>
                            <td id="8_roto_2_oem">
                                <input type="text" id="8_roto_2_oem_in" name="8_roto_2_oem">
                            </td>
                            <td id="8_roto_2_size">
                                <input type="text" id="8_roto_2_size_in" name="8_roto_2_size">
                            </td>
                            <td id="8_roto_2_length">
                                <input type="text" id="8_roto_2_length_in" name="8_roto_2_length">
                            </td>
                            <td id="8_roto_2_lobe">
                                <input type="text" id="8_roto_2_lobe_in" name="8_roto_2_lobe">
                            </td>
                            <td id="8_roto_2_stage">
                                <input type="text" id="8_roto_2_stage_in" name="8_roto_2_stage">
                            </td>
                            <td id="8_roto_2_loc">
                                <input type="text" id="8_roto_2_loc_in" name="8_roto_2_loc">
                            </td>
                            <td id="8_roto_2_motor_no">
                                <input type="text" id="8_roto_2_motor_no_in" name="8_roto_2_motor_no">
                            </td>
                            <td id="8_roto_2_rotor_mean">
                                <input type="text" id="8_roto_2_rotor_mean_in" name="8_roto_2_rotor_mean">
                            </td>
                            <td id="8_roto_2_stat">
                                <input type="text" id="8_roto_2_stat_in" name="8_roto_2_stat">
                            </td>
                            <td id="8_roto_2_plating_type">
                                <input type="text" id="8_roto_2_plating_type_in" name="8_roto_2_plating_type">
                            </td>
                            <td id="8_roto_2_coating_date">
                                <input type="date" id="8_roto_2_coating_date_in" name="8_roto_2_coating_date">
                            </td>
                            <td id="8_roto_2_rem">
                                <input type="text" id="8_roto_2_rem_in" name="8_roto_2_rem">
                            </td>
                        </tr>

                        <!-- Record Set 3 -->
                        <tr id="8_roto_3">
                            <td id="8_roto_3_no">
                                <input type="text" id="8_roto_3_no_in" name="8_roto_3_no">
                            </td>
                            <td id="8_roto_3_sn">
                                <input type="text" id="8_roto_3_sn_in" name="8_roto_3_sn">
                            </td>
                            <td id="8_roto_3_desc">
                                <input type="text" id="8_roto_3_desc_in" name="8_roto_3_desc">
                            </td>
                            <td id="8_roto_3_oem">
                                <input type="text" id="8_roto_3_oem_in" name="8_roto_3_oem">
                            </td>
                            <td id="8_roto_3_size">
                                <input type="text" id="8_roto_3_size_in" name="8_roto_3_size">
                            </td>
                            <td id="8_roto_3_length">
                                <input type="text" id="8_roto_3_length_in" name="8_roto_3_length">
                            </td>
                            <td id="8_roto_3_lobe">
                                <input type="text" id="8_roto_3_lobe_in" name="8_roto_3_lobe">
                            </td>
                            <td id="8_roto_3_stage">
                                <input type="text" id="8_roto_3_stage_in" name="8_roto_3_stage">
                            </td>
                            <td id="8_roto_3_loc">
                                <input type="text" id="8_roto_3_loc_in" name="8_roto_3_loc">
                            </td>
                            <td id="8_roto_3_motor_no">
                                <input type="text" id="8_roto_3_motor_no_in" name="8_roto_3_motor_no">
                            </td>
                            <td id="8_roto_3_rotor_mean">
                                <input type="text" id="8_roto_3_rotor_mean_in" name="8_roto_3_rotor_mean">
                            </td>
                            <td id="8_roto_3_stat">
                                <input type="text" id="8_roto_3_stat_in" name="8_roto_3_stat">
                            </td>
                            <td id="8_roto_3_plating_type">
                                <input type="text" id="8_roto_3_plating_type_in" name="8_roto_3_plating_type">
                            </td>
                            <td id="8_roto_3_coating_date">
                                <input type="date" id="8_roto_3_coating_date_in" name="8_roto_3_coating_date">
                            </td>
                            <td id="8_roto_3_rem">
                                <input type="text" id="8_roto_3_rem_in" name="8_roto_3_rem">
                            </td>
                        </tr>


                        <tr class="highlight-gray">
                            <td colspan="16" class="text-center font-weight-bold">6 3/4" Rotor </td>
                        </tr>

                        <!-- Record Set 1 -->
                        <tr id="6_3_4_roto_1">
                            <td id="6_3_4_roto_1_no">
                                <input type="text" id="6_3_4_roto_1_no_in" name="6_3_4_roto_1_no">
                            </td>
                            <td id="6_3_4_roto_1_sn">
                                <input type="text" id="6_3_4_roto_1_sn_in" name="6_3_4_roto_1_sn">
                            </td>
                            <td id="6_3_4_roto_1_desc">
                                <input type="text" id="6_3_4_roto_1_desc_in" name="6_3_4_roto_1_desc">
                            </td>
                            <td id="6_3_4_roto_1_oem">
                                <input type="text" id="6_3_4_roto_1_oem_in" name="6_3_4_roto_1_oem">
                            </td>
                            <td id="6_3_4_roto_1_size">
                                <input type="text" id="6_3_4_roto_1_size_in" name="6_3_4_roto_1_size">
                            </td>
                            <td id="6_3_4_roto_1_length">
                                <input type="text" id="6_3_4_roto_1_length_in" name="6_3_4_roto_1_length">
                            </td>
                            <td id="6_3_4_roto_1_lobe">
                                <input type="text" id="6_3_4_roto_1_lobe_in" name="6_3_4_roto_1_lobe">
                            </td>
                            <td id="6_3_4_roto_1_stage">
                                <input type="text" id="6_3_4_roto_1_stage_in" name="6_3_4_roto_1_stage">
                            </td>
                            <td id="6_3_4_roto_1_loc">
                                <input type="text" id="6_3_4_roto_1_loc_in" name="6_3_4_roto_1_loc">
                            </td>
                            <td id="6_3_4_roto_1_motor_no">
                                <input type="text" id="6_3_4_roto_1_motor_no_in" name="6_3_4_roto_1_motor_no">
                            </td>
                            <td id="6_3_4_roto_1_rotor_mean">
                                <input type="text" id="6_3_4_roto_1_rotor_mean_in" name="6_3_4_roto_1_rotor_mean">
                            </td>
                            <td id="6_3_4_roto_1_stat">
                                <input type="text" id="6_3_4_roto_1_stat_in" name="6_3_4_roto_1_stat">
                            </td>
                            <td id="6_3_4_roto_1_plating_type">
                                <input type="text" id="6_3_4_roto_1_plating_type_in" name="6_3_4_roto_1_plating_type">
                            </td>
                            <td id="6_3_4_roto_1_coating_date">
                                <input type="date" id="6_3_4_roto_1_coating_date_in" name="6_3_4_roto_1_coating_date">
                            </td>
                            <td id="6_3_4_roto_1_rem">
                                <input type="text" id="6_3_4_roto_1_rem_in" name="6_3_4_roto_1_rem">
                            </td>
                        </tr>

                        <!-- Record Set 2 -->
                        <tr id="6_3_4_roto_2">
                            <td id="6_3_4_roto_2_no">
                                <input type="text" id="6_3_4_roto_2_no_in" name="6_3_4_roto_2_no">
                            </td>
                            <td id="6_3_4_roto_2_sn">
                                <input type="text" id="6_3_4_roto_2_sn_in" name="6_3_4_roto_2_sn">
                            </td>
                            <td id="6_3_4_roto_2_desc">
                                <input type="text" id="6_3_4_roto_2_desc_in" name="6_3_4_roto_2_desc">
                            </td>
                            <td id="6_3_4_roto_2_oem">
                                <input type="text" id="6_3_4_roto_2_oem_in" name="6_3_4_roto_2_oem">
                            </td>
                            <td id="6_3_4_roto_2_size">
                                <input type="text" id="6_3_4_roto_2_size_in" name="6_3_4_roto_2_size">
                            </td>
                            <td id="6_3_4_roto_2_length">
                                <input type="text" id="6_3_4_roto_2_length_in" name="6_3_4_roto_2_length">
                            </td>
                            <td id="6_3_4_roto_2_lobe">
                                <input type="text" id="6_3_4_roto_2_lobe_in" name="6_3_4_roto_2_lobe">
                            </td>
                            <td id="6_3_4_roto_2_stage">
                                <input type="text" id="6_3_4_roto_2_stage_in" name="6_3_4_roto_2_stage">
                            </td>
                            <td id="6_3_4_roto_2_loc">
                                <input type="text" id="6_3_4_roto_2_loc_in" name="6_3_4_roto_2_loc">
                            </td>
                            <td id="6_3_4_roto_2_motor_no">
                                <input type="text" id="6_3_4_roto_2_motor_no_in" name="6_3_4_roto_2_motor_no">
                            </td>
                            <td id="6_3_4_roto_2_rotor_mean">
                                <input type="text" id="6_3_4_roto_2_rotor_mean_in" name="6_3_4_roto_2_rotor_mean">
                            </td>
                            <td id="6_3_4_roto_2_stat">
                                <input type="text" id="6_3_4_roto_2_stat_in" name="6_3_4_roto_2_stat">
                            </td>
                            <td id="6_3_4_roto_2_plating_type">
                                <input type="text" id="6_3_4_roto_2_plating_type_in" name="6_3_4_roto_2_plating_type">
                            </td>
                            <td id="6_3_4_roto_2_coating_date">
                                <input type="date" id="6_3_4_roto_2_coating_date_in" name="6_3_4_roto_2_coating_date">
                            </td>
                            <td id="6_3_4_roto_2_rem">
                                <input type="text" id="6_3_4_roto_2_rem_in" name="6_3_4_roto_2_rem">
                            </td>
                        </tr>

                        <!-- Record Set 3 -->
                        <tr id="6_3_4_roto_3">
                            <td id="6_3_4_roto_3_no">
                                <input type="text" id="6_3_4_roto_3_no_in" name="6_3_4_roto_3_no">
                            </td>
                            <td id="6_3_4_roto_3_sn">
                                <input type="text" id="6_3_4_roto_3_sn_in" name="6_3_4_roto_3_sn">
                            </td>
                            <td id="6_3_4_roto_3_desc">
                                <input type="text" id="6_3_4_roto_3_desc_in" name="6_3_4_roto_3_desc">
                            </td>
                            <td id="6_3_4_roto_3_oem">
                                <input type="text" id="6_3_4_roto_3_oem_in" name="6_3_4_roto_3_oem">
                            </td>
                            <td id="6_3_4_roto_3_size">
                                <input type="text" id="6_3_4_roto_3_size_in" name="6_3_4_roto_3_size">
                            </td>
                            <td id="6_3_4_roto_3_length">
                                <input type="text" id="6_3_4_roto_3_length_in" name="6_3_4_roto_3_length">
                            </td>
                            <td id="6_3_4_roto_3_lobe">
                                <input type="text" id="6_3_4_roto_3_lobe_in" name="6_3_4_roto_3_lobe">
                            </td>
                            <td id="6_3_4_roto_3_stage">
                                <input type="text" id="6_3_4_roto_3_stage_in" name="6_3_4_roto_3_stage">
                            </td>
                            <td id="6_3_4_roto_3_loc">
                                <input type="text" id="6_3_4_roto_3_loc_in" name="6_3_4_roto_3_loc">
                            </td>
                            <td id="6_3_4_roto_3_motor_no">
                                <input type="text" id="6_3_4_roto_3_motor_no_in" name="6_3_4_roto_3_motor_no">
                            </td>
                            <td id="6_3_4_roto_3_rotor_mean">
                                <input type="text" id="6_3_4_roto_3_rotor_mean_in" name="6_3_4_roto_3_rotor_mean">
                            </td>
                            <td id="6_3_4_roto_3_stat">
                                <input type="text" id="6_3_4_roto_3_stat_in" name="6_3_4_roto_3_stat">
                            </td>
                            <td id="6_3_4_roto_3_plating_type">
                                <input type="text" id="6_3_4_roto_3_plating_type_in" name="6_3_4_roto_3_plating_type">
                            </td>
                            <td id="6_3_4_roto_3_coating_date">
                                <input type="date" id="6_3_4_roto_3_coating_date_in" name="6_3_4_roto_3_coating_date">
                            </td>
                            <td id="6_3_4_roto_3_rem">
                                <input type="text" id="6_3_4_roto_3_rem_in" name="6_3_4_roto_3_rem">
                            </td>
                        </tr>
                        <tr class="highlight-gray">
                            <td colspan="16" class="text-center font-weight-bold"> 6 1/2" ROTOR RHQ</td>
                        </tr>

                        <!-- Record Set 1 -->
                        <tr id="6_1_2_roto_rhq_1">
                            <td id="6_1_2_roto_rhq_1_no">
                                <input type="text" id="6_1_2_roto_rhq_1_no_in" name="6_1_2_roto_rhq_1_no">
                            </td>
                            <td id="6_1_2_roto_rhq_1_sn">
                                <input type="text" id="6_1_2_roto_rhq_1_sn_in" name="6_1_2_roto_rhq_1_sn">
                            </td>
                            <td id="6_1_2_roto_rhq_1_desc">
                                <input type="text" id="6_1_2_roto_rhq_1_desc_in" name="6_1_2_roto_rhq_1_desc">
                            </td>
                            <td id="6_1_2_roto_rhq_1_oem">
                                <input type="text" id="6_1_2_roto_rhq_1_oem_in" name="6_1_2_roto_rhq_1_oem">
                            </td>
                            <td id="6_1_2_roto_rhq_1_size">
                                <input type="text" id="6_1_2_roto_rhq_1_size_in" name="6_1_2_roto_rhq_1_size">
                            </td>
                            <td id="6_1_2_roto_rhq_1_length">
                                <input type="text" id="6_1_2_roto_rhq_1_length_in" name="6_1_2_roto_rhq_1_length">
                            </td>
                            <td id="6_1_2_roto_rhq_1_lobe">
                                <input type="text" id="6_1_2_roto_rhq_1_lobe_in" name="6_1_2_roto_rhq_1_lobe">
                            </td>
                            <td id="6_1_2_roto_rhq_1_stage">
                                <input type="text" id="6_1_2_roto_rhq_1_stage_in" name="6_1_2_roto_rhq_1_stage">
                            </td>
                            <td id="6_1_2_roto_rhq_1_loc">
                                <input type="text" id="6_1_2_roto_rhq_1_loc_in" name="6_1_2_roto_rhq_1_loc">
                            </td>
                            <td id="6_1_2_roto_rhq_1_motor_no">
                                <input type="text" id="6_1_2_roto_rhq_1_motor_no_in" name="6_1_2_roto_rhq_1_motor_no">
                            </td>
                            <td id="6_1_2_roto_rhq_1_rotor_mean">
                                <input type="text" id="6_1_2_roto_rhq_1_rotor_mean_in" name="6_1_2_roto_rhq_1_rotor_mean">
                            </td>
                            <td id="6_1_2_roto_rhq_1_stat">
                                <input type="text" id="6_1_2_roto_rhq_1_stat_in" name="6_1_2_roto_rhq_1_stat">
                            </td>
                            <td id="6_1_2_roto_rhq_1_plating_type">
                                <input type="text" id="6_1_2_roto_rhq_1_plating_type_in" name="6_1_2_roto_rhq_1_plating_type">
                            </td>
                            <td id="6_1_2_roto_rhq_1_coating_date">
                                <input type="date" id="6_1_2_roto_rhq_1_coating_date_in" name="6_1_2_roto_rhq_1_coating_date">
                            </td>
                            <td id="6_1_2_roto_rhq_1_rem">
                                <input type="text" id="6_1_2_roto_rhq_1_rem_in" name="6_1_2_roto_rhq_1_rem">
                            </td>
                        </tr>

                        <!-- Record Set 2 -->
                        <tr id="6_1_2_roto_rhq_2">
                            <td id="6_1_2_roto_rhq_2_no">
                                <input type="text" id="6_1_2_roto_rhq_2_no_in" name="6_1_2_roto_rhq_2_no">
                            </td>
                            <td id="6_1_2_roto_rhq_2_sn">
                                <input type="text" id="6_1_2_roto_rhq_2_sn_in" name="6_1_2_roto_rhq_2_sn">
                            </td>
                            <td id="6_1_2_roto_rhq_2_desc">
                                <input type="text" id="6_1_2_roto_rhq_2_desc_in" name="6_1_2_roto_rhq_2_desc">
                            </td>
                            <td id="6_1_2_roto_rhq_2_oem">
                                <input type="text" id="6_1_2_roto_rhq_2_oem_in" name="6_1_2_roto_rhq_2_oem">
                            </td>
                            <td id="6_1_2_roto_rhq_2_size">
                                <input type="text" id="6_1_2_roto_rhq_2_size_in" name="6_1_2_roto_rhq_2_size">
                            </td>
                            <td id="6_1_2_roto_rhq_2_length">
                                <input type="text" id="6_1_2_roto_rhq_2_length_in" name="6_1_2_roto_rhq_2_length">
                            </td>
                            <td id="6_1_2_roto_rhq_2_lobe">
                                <input type="text" id="6_1_2_roto_rhq_2_lobe_in" name="6_1_2_roto_rhq_2_lobe">
                            </td>
                            <td id="6_1_2_roto_rhq_2_stage">
                                <input type="text" id="6_1_2_roto_rhq_2_stage_in" name="6_1_2_roto_rhq_2_stage">
                            </td>
                            <td id="6_1_2_roto_rhq_2_loc">
                                <input type="text" id="6_1_2_roto_rhq_2_loc_in" name="6_1_2_roto_rhq_2_loc">
                            </td>
                            <td id="6_1_2_roto_rhq_2_motor_no">
                                <input type="text" id="6_1_2_roto_rhq_2_motor_no_in" name="6_1_2_roto_rhq_2_motor_no">
                            </td>
                            <td id="6_1_2_roto_rhq_2_rotor_mean">
                                <input type="text" id="6_1_2_roto_rhq_2_rotor_mean_in" name="6_1_2_roto_rhq_2_rotor_mean">
                            </td>
                            <td id="6_1_2_roto_rhq_2_stat">
                                <input type="text" id="6_1_2_roto_rhq_2_stat_in" name="6_1_2_roto_rhq_2_stat">
                            </td>
                            <td id="6_1_2_roto_rhq_2_plating_type">
                                <input type="text" id="6_1_2_roto_rhq_2_plating_type_in" name="6_1_2_roto_rhq_2_plating_type">
                            </td>
                            <td id="6_1_2_roto_rhq_2_coating_date">
                                <input type="date" id="6_1_2_roto_rhq_2_coating_date_in" name="6_1_2_roto_rhq_2_coating_date">
                            </td>
                            <td id="6_1_2_roto_rhq_2_rem">
                                <input type="text" id="6_1_2_roto_rhq_2_rem_in" name="6_1_2_roto_rhq_2_rem">
                            </td>
                        </tr>

                        <!-- Record Set 3 -->
                        <tr id="6_1_2_roto_rhq_3">
                            <td id="6_1_2_roto_rhq_3_no">
                                <input type="text" id="6_1_2_roto_rhq_3_no_in" name="6_1_2_roto_rhq_3_no">
                            </td>
                            <td id="6_1_2_roto_rhq_3_sn">
                                <input type="text" id="6_1_2_roto_rhq_3_sn_in" name="6_1_2_roto_rhq_3_sn">
                            </td>
                            <td id="6_1_2_roto_rhq_3_desc">
                                <input type="text" id="6_1_2_roto_rhq_3_desc_in" name="6_1_2_roto_rhq_3_desc">
                            </td>
                            <td id="6_1_2_roto_rhq_3_oem">
                                <input type="text" id="6_1_2_roto_rhq_3_oem_in" name="6_1_2_roto_rhq_3_oem">
                            </td>
                            <td id="6_1_2_roto_rhq_3_size">
                                <input type="text" id="6_1_2_roto_rhq_3_size_in" name="6_1_2_roto_rhq_3_size">
                            </td>
                            <td id="6_1_2_roto_rhq_3_length">
                                <input type="text" id="6_1_2_roto_rhq_3_length_in" name="6_1_2_roto_rhq_3_length">
                            </td>
                            <td id="6_1_2_roto_rhq_3_lobe">
                                <input type="text" id="6_1_2_roto_rhq_3_lobe_in" name="6_1_2_roto_rhq_3_lobe">
                            </td>
                            <td id="6_1_2_roto_rhq_3_stage">
                                <input type="text" id="6_1_2_roto_rhq_3_stage_in" name="6_1_2_roto_rhq_3_stage">
                            </td>
                            <td id="6_1_2_roto_rhq_3_loc">
                                <input type="text" id="6_1_2_roto_rhq_3_loc_in" name="6_1_2_roto_rhq_3_loc">
                            </td>
                            <td id="6_1_2_roto_rhq_3_motor_no">
                                <input type="text" id="6_1_2_roto_rhq_3_motor_no_in" name="6_1_2_roto_rhq_3_motor_no">
                            </td>
                            <td id="6_1_2_roto_rhq_3_rotor_mean">
                                <input type="text" id="6_1_2_roto_rhq_3_rotor_mean_in" name="6_1_2_roto_rhq_3_rotor_mean">
                            </td>
                            <td id="6_1_2_roto_rhq_3_stat">
                                <input type="text" id="6_1_2_roto_rhq_3_stat_in" name="6_1_2_roto_rhq_3_stat">
                            </td>
                            <td id="6_1_2_roto_rhq_3_plating_type">
                                <input type="text" id="6_1_2_roto_rhq_3_plating_type_in" name="6_1_2_roto_rhq_3_plating_type">
                            </td>
                            <td id="6_1_2_roto_rhq_3_coating_date">
                                <input type="date" id="6_1_2_roto_rhq_3_coating_date_in" name="6_1_2_roto_rhq_3_coating_date">
                            </td>
                            <td id="6_1_2_roto_rhq_3_rem">
                                <input type="text" id="6_1_2_roto_rhq_3_rem_in" name="6_1_2_roto_rhq_3_rem">
                            </td>
                        </tr>

                        <tr class="highlight-gray">
                            <td colspan="16" class="text-center font-weight-bold"> 4 3/4" ROTOR</td>
                        </tr>

                        <!-- Record Set 1 -->
                        <tr id="4_3_4_roto_1">
                            <td id="4_3_4_roto_1_no">
                                <input type="text" id="4_3_4_roto_1_no_in" name="4_3_4_roto_1_no">
                            </td>
                            <td id="4_3_4_roto_1_sn">
                                <input type="text" id="4_3_4_roto_1_sn_in" name="4_3_4_roto_1_sn">
                            </td>
                            <td id="4_3_4_roto_1_desc">
                                <input type="text" id="4_3_4_roto_1_desc_in" name="4_3_4_roto_1_desc">
                            </td>
                            <td id="4_3_4_roto_1_oem">
                                <input type="text" id="4_3_4_roto_1_oem_in" name="4_3_4_roto_1_oem">
                            </td>
                            <td id="4_3_4_roto_1_size">
                                <input type="text" id="4_3_4_roto_1_size_in" name="4_3_4_roto_1_size">
                            </td>
                            <td id="4_3_4_roto_1_length">
                                <input type="text" id="4_3_4_roto_1_length_in" name="4_3_4_roto_1_length">
                            </td>
                            <td id="4_3_4_roto_1_lobe">
                                <input type="text" id="4_3_4_roto_1_lobe_in" name="4_3_4_roto_1_lobe">
                            </td>
                            <td id="4_3_4_roto_1_stage">
                                <input type="text" id="4_3_4_roto_1_stage_in" name="4_3_4_roto_1_stage">
                            </td>
                            <td id="4_3_4_roto_1_loc">
                                <input type="text" id="4_3_4_roto_1_loc_in" name="4_3_4_roto_1_loc">
                            </td>
                            <td id="4_3_4_roto_1_motor_no">
                                <input type="text" id="4_3_4_roto_1_motor_no_in" name="4_3_4_roto_1_motor_no">
                            </td>
                            <td id="4_3_4_roto_1_rotor_mean">
                                <input type="text" id="4_3_4_roto_1_rotor_mean_in" name="4_3_4_roto_1_rotor_mean">
                            </td>
                            <td id="4_3_4_roto_1_stat">
                                <input type="text" id="4_3_4_roto_1_stat_in" name="4_3_4_roto_1_stat">
                            </td>
                            <td id="4_3_4_roto_1_plating_type">
                                <input type="text" id="4_3_4_roto_1_plating_type_in" name="4_3_4_roto_1_plating_type">
                            </td>
                            <td id="4_3_4_roto_1_coating_date">
                                <input type="date" id="4_3_4_roto_1_coating_date_in" name="4_3_4_roto_1_coating_date">
                            </td>
                            <td id="4_3_4_roto_1_rem">
                                <input type="text" id="4_3_4_roto_1_rem_in" name="4_3_4_roto_1_rem">
                            </td>
                        </tr>

                        <!-- Record Set 2 -->
                        <tr id="4_3_4_roto_2">
                            <td id="4_3_4_roto_2_no">
                                <input type="text" id="4_3_4_roto_2_no_in" name="4_3_4_roto_2_no">
                            </td>
                            <td id="4_3_4_roto_2_sn">
                                <input type="text" id="4_3_4_roto_2_sn_in" name="4_3_4_roto_2_sn">
                            </td>
                            <td id="4_3_4_roto_2_desc">
                                <input type="text" id="4_3_4_roto_2_desc_in" name="4_3_4_roto_2_desc">
                            </td>
                            <td id="4_3_4_roto_2_oem">
                                <input type="text" id="4_3_4_roto_2_oem_in" name="4_3_4_roto_2_oem">
                            </td>
                            <td id="4_3_4_roto_2_size">
                                <input type="text" id="4_3_4_roto_2_size_in" name="4_3_4_roto_2_size">
                            </td>
                            <td id="4_3_4_roto_2_length">
                                <input type="text" id="4_3_4_roto_2_length_in" name="4_3_4_roto_2_length">
                            </td>
                            <td id="4_3_4_roto_2_lobe">
                                <input type="text" id="4_3_4_roto_2_lobe_in" name="4_3_4_roto_2_lobe">
                            </td>
                            <td id="4_3_4_roto_2_stage">
                                <input type="text" id="4_3_4_roto_2_stage_in" name="4_3_4_roto_2_stage">
                            </td>
                            <td id="4_3_4_roto_2_loc">
                                <input type="text" id="4_3_4_roto_2_loc_in" name="4_3_4_roto_2_loc">
                            </td>
                            <td id="4_3_4_roto_2_motor_no">
                                <input type="text" id="4_3_4_roto_2_motor_no_in" name="4_3_4_roto_2_motor_no">
                            </td>
                            <td id="4_3_4_roto_2_rotor_mean">
                                <input type="text" id="4_3_4_roto_2_rotor_mean_in" name="4_3_4_roto_2_rotor_mean">
                            </td>
                            <td id="4_3_4_roto_2_stat">
                                <input type="text" id="4_3_4_roto_2_stat_in" name="4_3_4_roto_2_stat">
                            </td>
                            <td id="4_3_4_roto_2_plating_type">
                                <input type="text" id="4_3_4_roto_2_plating_type_in" name="4_3_4_roto_2_plating_type">
                            </td>
                            <td id="4_3_4_roto_2_coating_date">
                                <input type="date" id="4_3_4_roto_2_coating_date_in" name="4_3_4_roto_2_coating_date">
                            </td>
                            <td id="4_3_4_roto_2_rem">
                                <input type="text" id="4_3_4_roto_2_rem_in" name="4_3_4_roto_2_rem">
                            </td>
                        </tr>

                        <!-- Record Set 3 -->
                        <tr id="4_3_4_roto_3">
                            <td id="4_3_4_roto_3_no">
                                <input type="text" id="4_3_4_roto_3_no_in" name="4_3_4_roto_3_no">
                            </td>
                            <td id="4_3_4_roto_3_sn">
                                <input type="text" id="4_3_4_roto_3_sn_in" name="4_3_4_roto_3_sn">
                            </td>
                            <td id="4_3_4_roto_3_desc">
                                <input type="text" id="4_3_4_roto_3_desc_in" name="4_3_4_roto_3_desc">
                            </td>
                            <td id="4_3_4_roto_3_oem">
                                <input type="text" id="4_3_4_roto_3_oem_in" name="4_3_4_roto_3_oem">
                            </td>
                            <td id="4_3_4_roto_3_size">
                                <input type="text" id="4_3_4_roto_3_size_in" name="4_3_4_roto_3_size">
                            </td>
                            <td id="4_3_4_roto_3_length">
                                <input type="text" id="4_3_4_roto_3_length_in" name="4_3_4_roto_3_length">
                            </td>
                            <td id="4_3_4_roto_3_lobe">
                                <input type="text" id="4_3_4_roto_3_lobe_in" name="4_3_4_roto_3_lobe">
                            </td>
                            <td id="4_3_4_roto_3_stage">
                                <input type="text" id="4_3_4_roto_3_stage_in" name="4_3_4_roto_3_stage">
                            </td>
                            <td id="4_3_4_roto_3_loc">
                                <input type="text" id="4_3_4_roto_3_loc_in" name="4_3_4_roto_3_loc">
                            </td>
                            <td id="4_3_4_roto_3_motor_no">
                                <input type="text" id="4_3_4_roto_3_motor_no_in" name="4_3_4_roto_3_motor_no">
                            </td>
                            <td id="4_3_4_roto_3_rotor_mean">
                                <input type="text" id="4_3_4_roto_3_rotor_mean_in" name="4_3_4_roto_3_rotor_mean">
                            </td>
                            <td id="4_3_4_roto_3_stat">
                                <input type="text" id="4_3_4_roto_3_stat_in" name="4_3_4_roto_3_stat">
                            </td>
                            <td id="4_3_4_roto_3_plating_type">
                                <input type="text" id="4_3_4_roto_3_plating_type_in" name="4_3_4_roto_3_plating_type">
                            </td>
                            <td id="4_3_4_roto_3_coating_date">
                                <input type="date" id="4_3_4_roto_3_coating_date_in" name="4_3_4_roto_3_coating_date">
                            </td>
                            <td id="4_3_4_roto_3_rem">
                                <input type="text" id="4_3_4_roto_3_rem_in" name="4_3_4_roto_3_rem">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

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