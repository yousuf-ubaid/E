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

        .highlight-green {
            background-color: #99FF99;
            font-weight: bold;
        }

        .highlight-yellow {
            background-color: #FFFF99;
            font-weight: bold;
        }

        .highlight-red {
            background-color: #FF6666;
            font-weight: bold;
        }

        .highlight-dark_green {
            background-color: #32CD32;
            font-weight: bold;
        }

        .highlight-blue {
            background-color: #87CEFA;
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
                        <tr>
                            <th>assets</th>
                            <th colspan="11"></th>
                        </tr>
                        <tr class="table-header">
                            <th>No.</th>
                            <th>Serial Number</th>
                            <th>Ray Asset Number</th>
                            <th>Description</th>
                            <th>OEM</th>
                            <th>OD</th>
                            <th>Bottom Connection</th>
                            <th>TopbConnection</th>
                            <th>Stabilizer Size</th>
                            <th>Current Location</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>

                    </thead>
                    <tbody>

                        <tr class="highlight-green">
                            <td colspan="12" class="text-center font-weight-bold">Motor at Rig Sites Section</td>
                        </tr>

                        <!-- Motor at Rig Sites Section -->
                        <tr id="mtr_1">
                            <td id="mtr_1_no">
                                <input type="text" id="mtr_1_no_in" name="mtr_1_no">
                            </td>
                            <td id="mtr_1_sn">
                                <input type="text" id="mtr_1_sn_in" name="mtr_1_sn">
                            </td>
                            <td id="mtr_1_ran">
                                <input type="text" id="mtr_1_ran_in" name="mtr_1_ran">
                            </td>
                            <td id="mtr_1_desc">
                                <input type="text" id="mtr_1_desc_in" name="mtr_1_desc">
                            </td>
                            <td id="mtr_1_oem">
                                <input type="text" id="mtr_1_oem_in" name="mtr_1_oem">
                            </td>
                            <td id="mtr_1_od">
                                <input type="text" id="mtr_1_od_in" name="mtr_1_od">
                            </td>
                            <td id="mtr_1_bc">
                                <input type="text" id="mtr_1_bc_in" name="mtr_1_bc">
                            </td>
                            <td id="mtr_1_tc">
                                <input type="text" id="mtr_1_tc_in" name="mtr_1_tc">
                            </td>
                            <td id="mtr_1_ss">
                                <input type="text" id="mtr_1_ss_in" name="mtr_1_ss">
                            </td>
                            <td id="mtr_1_loc">
                                <input type="text" id="mtr_1_loc_in" name="mtr_1_loc">
                            </td>
                            <td id="mtr_1_stat">
                                <input type="text" id="mtr_1_stat_in" name="mtr_1_stat">
                            </td>
                            <td id="mtr_1_rem">
                                <input type="text" id="mtr_1_rem_in" name="mtr_1_rem">
                            </td>
                        </tr>

                        <tr id="mtr_2">
                            <td id="mtr_2_no">
                                <input type="text" id="mtr_2_no_in" name="mtr_2_no">
                            </td>
                            <td id="mtr_2_sn">
                                <input type="text" id="mtr_2_sn_in" name="mtr_2_sn">
                            </td>
                            <td id="mtr_2_ran">
                                <input type="text" id="mtr_2_ran_in" name="mtr_2_ran">
                            </td>
                            <td id="mtr_2_desc">
                                <input type="text" id="mtr_2_desc_in" name="mtr_2_desc">
                            </td>
                            <td id="mtr_2_oem">
                                <input type="text" id="mtr_2_oem_in" name="mtr_2_oem">
                            </td>
                            <td id="mtr_2_od">
                                <input type="text" id="mtr_2_od_in" name="mtr_2_od">
                            </td>
                            <td id="mtr_2_bc">
                                <input type="text" id="mtr_2_bc_in" name="mtr_2_bc">
                            </td>
                            <td id="mtr_2_tc">
                                <input type="text" id="mtr_2_tc_in" name="mtr_2_tc">
                            </td>
                            <td id="mtr_2_ss">
                                <input type="text" id="mtr_2_ss_in" name="mtr_2_ss">
                            </td>
                            <td id="mtr_2_loc">
                                <input type="text" id="mtr_2_loc_in" name="mtr_2_loc">
                            </td>
                            <td id="mtr_2_stat">
                                <input type="text" id="mtr_2_stat_in" name="mtr_2_stat">
                            </td>
                            <td id="mtr_2_rem">
                                <input type="text" id="mtr_2_rem_in" name="mtr_2_rem">
                            </td>
                        </tr>

                        <tr id="mtr_3">
                            <td id="mtr_3_no">
                                <input type="text" id="mtr_3_no_in" name="mtr_3_no">
                            </td>
                            <td id="mtr_3_sn">
                                <input type="text" id="mtr_3_sn_in" name="mtr_3_sn">
                            </td>
                            <td id="mtr_3_ran">
                                <input type="text" id="mtr_3_ran_in" name="mtr_3_ran">
                            </td>
                            <td id="mtr_3_desc">
                                <input type="text" id="mtr_3_desc_in" name="mtr_3_desc">
                            </td>
                            <td id="mtr_3_oem">
                                <input type="text" id="mtr_3_oem_in" name="mtr_3_oem">
                            </td>
                            <td id="mtr_3_od">
                                <input type="text" id="mtr_3_od_in" name="mtr_3_od">
                            </td>
                            <td id="mtr_3_bc">
                                <input type="text" id="mtr_3_bc_in" name="mtr_3_bc">
                            </td>
                            <td id="mtr_3_tc">
                                <input type="text" id="mtr_3_tc_in" name="mtr_3_tc">
                            </td>
                            <td id="mtr_3_ss">
                                <input type="text" id="mtr_3_ss_in" name="mtr_3_ss">
                            </td>
                            <td id="mtr_3_loc">
                                <input type="text" id="mtr_3_loc_in" name="mtr_3_loc">
                            </td>
                            <td id="mtr_3_stat">
                                <input type="text" id="mtr_3_stat_in" name="mtr_3_stat">
                            </td>
                            <td id="mtr_3_rem">
                                <input type="text" id="mtr_3_rem_in" name="mtr_3_rem">
                            </td>
                        </tr>
                        <!-- --------------------------------------------------------------------------------------------------------------- -->


                        <tr class="highlight-dark_green">
                            <td colspan="12" class="text-center font-weight-bold">Ready Motor at Workshop Section</td>
                        </tr>

                        <!-- Ready Motor at Workshop Section -->
                        <tr id="rmw_1">
                            <td id="rmw_1_no">
                                <input type="text" id="rmw_1_no_in" name="rmw_1_no">
                            </td>
                            <td id="rmw_1_sn">
                                <input type="text" id="rmw_1_sn_in" name="rmw_1_sn">
                            </td>
                            <td id="rmw_1_ran">
                                <input type="text" id="rmw_1_ran_in" name="rmw_1_ran">
                            </td>
                            <td id="rmw_1_desc">
                                <input type="text" id="rmw_1_desc_in" name="rmw_1_desc">
                            </td>
                            <td id="rmw_1_oem">
                                <input type="text" id="rmw_1_oem_in" name="rmw_1_oem">
                            </td>
                            <td id="rmw_1_od">
                                <input type="text" id="rmw_1_od_in" name="rmw_1_od">
                            </td>
                            <td id="rmw_1_bc">
                                <input type="text" id="rmw_1_bc_in" name="rmw_1_bc">
                            </td>
                            <td id="rmw_1_tc">
                                <input type="text" id="rmw_1_tc_in" name="rmw_1_tc">
                            </td>
                            <td id="rmw_1_ss">
                                <input type="text" id="rmw_1_ss_in" name="rmw_1_ss">
                            </td>
                            <td id="rmw_1_loc">
                                <input type="text" id="rmw_1_loc_in" name="rmw_1_loc">
                            </td>
                            <td id="rmw_1_stat">
                                <input type="text" id="rmw_1_stat_in" name="rmw_1_stat">
                            </td>
                            <td id="rmw_1_rem">
                                <input type="text" id="rmw_1_rem_in" name="rmw_1_rem">
                            </td>
                        </tr>

                        <tr id="rmw_2">
                            <td id="rmw_2_no">
                                <input type="text" id="rmw_2_no_in" name="rmw_2_no">
                            </td>
                            <td id="rmw_2_sn">
                                <input type="text" id="rmw_2_sn_in" name="rmw_2_sn">
                            </td>
                            <td id="rmw_2_ran">
                                <input type="text" id="rmw_2_ran_in" name="rmw_2_ran">
                            </td>
                            <td id="rmw_2_desc">
                                <input type="text" id="rmw_2_desc_in" name="rmw_2_desc">
                            </td>
                            <td id="rmw_2_oem">
                                <input type="text" id="rmw_2_oem_in" name="rmw_2_oem">
                            </td>
                            <td id="rmw_2_od">
                                <input type="text" id="rmw_2_od_in" name="rmw_2_od">
                            </td>
                            <td id="rmw_2_bc">
                                <input type="text" id="rmw_2_bc_in" name="rmw_2_bc">
                            </td>
                            <td id="rmw_2_tc">
                                <input type="text" id="rmw_2_tc_in" name="rmw_2_tc">
                            </td>
                            <td id="rmw_2_ss">
                                <input type="text" id="rmw_2_ss_in" name="rmw_2_ss">
                            </td>
                            <td id="rmw_2_loc">
                                <input type="text" id="rmw_2_loc_in" name="rmw_2_loc">
                            </td>
                            <td id="rmw_2_stat">
                                <input type="text" id="rmw_2_stat_in" name="rmw_2_stat">
                            </td>
                            <td id="rmw_2_rem">
                                <input type="text" id="rmw_2_rem_in" name="rmw_2_rem">
                            </td>
                        </tr>

                        <tr id="rmw_3">
                            <td id="rmw_3_no">
                                <input type="text" id="rmw_3_no_in" name="rmw_3_no">
                            </td>
                            <td id="rmw_3_sn">
                                <input type="text" id="rmw_3_sn_in" name="rmw_3_sn">
                            </td>
                            <td id="rmw_3_ran">
                                <input type="text" id="rmw_3_ran_in" name="rmw_3_ran">
                            </td>
                            <td id="rmw_3_desc">
                                <input type="text" id="rmw_3_desc_in" name="rmw_3_desc">
                            </td>
                            <td id="rmw_3_oem">
                                <input type="text" id="rmw_3_oem_in" name="rmw_3_oem">
                            </td>
                            <td id="rmw_3_od">
                                <input type="text" id="rmw_3_od_in" name="rmw_3_od">
                            </td>
                            <td id="rmw_3_bc">
                                <input type="text" id="rmw_3_bc_in" name="rmw_3_bc">
                            </td>
                            <td id="rmw_3_tc">
                                <input type="text" id="rmw_3_tc_in" name="rmw_3_tc">
                            </td>
                            <td id="rmw_3_ss">
                                <input type="text" id="rmw_3_ss_in" name="rmw_3_ss">
                            </td>
                            <td id="rmw_3_loc">
                                <input type="text" id="rmw_3_loc_in" name="rmw_3_loc">
                            </td>
                            <td id="rmw_3_stat">
                                <input type="text" id="rmw_3_stat_in" name="rmw_3_stat">
                            </td>
                            <td id="rmw_3_rem">
                                <input type="text" id="rmw_3_rem_in" name="rmw_3_rem">
                            </td>
                        </tr>
                        <tr class="highlight-yellow">
                            <td colspan="12" class="text-center font-weight-bold">Motors Under Service Section</td>
                        </tr>

                        <!-- Motors Under Service Section -->
                        <tr id="mus_1">
                            <td id="mus_1_no">
                                <input type="text" id="mus_1_no_in" name="mus_1_no">
                            </td>
                            <td id="mus_1_sn">
                                <input type="text" id="mus_1_sn_in" name="mus_1_sn">
                            </td>
                            <td id="mus_1_ran">
                                <input type="text" id="mus_1_ran_in" name="mus_1_ran">
                            </td>
                            <td id="mus_1_desc">
                                <input type="text" id="mus_1_desc_in" name="mus_1_desc">
                            </td>
                            <td id="mus_1_oem">
                                <input type="text" id="mus_1_oem_in" name="mus_1_oem">
                            </td>
                            <td id="mus_1_od">
                                <input type="text" id="mus_1_od_in" name="mus_1_od">
                            </td>
                            <td id="mus_1_bc">
                                <input type="text" id="mus_1_bc_in" name="mus_1_bc">
                            </td>
                            <td id="mus_1_tc">
                                <input type="text" id="mus_1_tc_in" name="mus_1_tc">
                            </td>
                            <td id="mus_1_ss">
                                <input type="text" id="mus_1_ss_in" name="mus_1_ss">
                            </td>
                            <td id="mus_1_loc">
                                <input type="text" id="mus_1_loc_in" name="mus_1_loc">
                            </td>
                            <td id="mus_1_stat">
                                <input type="text" id="mus_1_stat_in" name="mus_1_stat">
                            </td>
                            <td id="mus_1_rem">
                                <input type="text" id="mus_1_rem_in" name="mus_1_rem">
                            </td>
                        </tr>

                        <tr id="mus_2">
                            <td id="mus_2_no">
                                <input type="text" id="mus_2_no_in" name="mus_2_no">
                            </td>
                            <td id="mus_2_sn">
                                <input type="text" id="mus_2_sn_in" name="mus_2_sn">
                            </td>
                            <td id="mus_2_ran">
                                <input type="text" id="mus_2_ran_in" name="mus_2_ran">
                            </td>
                            <td id="mus_2_desc">
                                <input type="text" id="mus_2_desc_in" name="mus_2_desc">
                            </td>
                            <td id="mus_2_oem">
                                <input type="text" id="mus_2_oem_in" name="mus_2_oem">
                            </td>
                            <td id="mus_2_od">
                                <input type="text" id="mus_2_od_in" name="mus_2_od">
                            </td>
                            <td id="mus_2_bc">
                                <input type="text" id="mus_2_bc_in" name="mus_2_bc">
                            </td>
                            <td id="mus_2_tc">
                                <input type="text" id="mus_2_tc_in" name="mus_2_tc">
                            </td>
                            <td id="mus_2_ss">
                                <input type="text" id="mus_2_ss_in" name="mus_2_ss">
                            </td>
                            <td id="mus_2_loc">
                                <input type="text" id="mus_2_loc_in" name="mus_2_loc">
                            </td>
                            <td id="mus_2_stat">
                                <input type="text" id="mus_2_stat_in" name="mus_2_stat">
                            </td>
                            <td id="mus_2_rem">
                                <input type="text" id="mus_2_rem_in" name="mus_2_rem">
                            </td>
                        </tr>

                        <tr id="mus_3">
                            <td id="mus_3_no">
                                <input type="text" id="mus_3_no_in" name="mus_3_no">
                            </td>
                            <td id="mus_3_sn">
                                <input type="text" id="mus_3_sn_in" name="mus_3_sn">
                            </td>
                            <td id="mus_3_ran">
                                <input type="text" id="mus_3_ran_in" name="mus_3_ran">
                            </td>
                            <td id="mus_3_desc">
                                <input type="text" id="mus_3_desc_in" name="mus_3_desc">
                            </td>
                            <td id="mus_3_oem">
                                <input type="text" id="mus_3_oem_in" name="mus_3_oem">
                            </td>
                            <td id="mus_3_od">
                                <input type="text" id="mus_3_od_in" name="mus_3_od">
                            </td>
                            <td id="mus_3_bc">
                                <input type="text" id="mus_3_bc_in" name="mus_3_bc">
                            </td>
                            <td id="mus_3_tc">
                                <input type="text" id="mus_3_tc_in" name="mus_3_tc">
                            </td>
                            <td id="mus_3_ss">
                                <input type="text" id="mus_3_ss_in" name="mus_3_ss">
                            </td>
                            <td id="mus_3_loc">
                                <input type="text" id="mus_3_loc_in" name="mus_3_loc">
                            </td>
                            <td id="mus_3_stat">
                                <input type="text" id="mus_3_stat_in" name="mus_3_stat">
                            </td>
                            <td id="mus_3_rem">
                                <input type="text" id="mus_3_rem_in" name="mus_3_rem">
                            </td>
                        </tr>
                        <tr class="highlight-blue">
                            <td colspan="12" class="text-center font-weight-bold">Bearing Section</td>
                        </tr>

                        <!-- Bearing Section -->
                        <tr id="brg_1">
                            <td id="brg_1_no">
                                <input type="text" id="brg_1_no_in" name="brg_1_no">
                            </td>
                            <td id="brg_1_sn">
                                <input type="text" id="brg_1_sn_in" name="brg_1_sn">
                            </td>
                            <td id="brg_1_ran">
                                <input type="text" id="brg_1_ran_in" name="brg_1_ran">
                            </td>
                            <td id="brg_1_desc">
                                <input type="text" id="brg_1_desc_in" name="brg_1_desc">
                            </td>
                            <td id="brg_1_oem">
                                <input type="text" id="brg_1_oem_in" name="brg_1_oem">
                            </td>
                            <td id="brg_1_od">
                                <input type="text" id="brg_1_od_in" name="brg_1_od">
                            </td>
                            <td id="brg_1_bc">
                                <input type="text" id="brg_1_bc_in" name="brg_1_bc">
                            </td>
                            <td id="brg_1_tc">
                                <input type="text" id="brg_1_tc_in" name="brg_1_tc">
                            </td>
                            <td id="brg_1_ss">
                                <input type="text" id="brg_1_ss_in" name="brg_1_ss">
                            </td>
                            <td id="brg_1_loc">
                                <input type="text" id="brg_1_loc_in" name="brg_1_loc">
                            </td>
                            <td id="brg_1_stat">
                                <input type="text" id="brg_1_stat_in" name="brg_1_stat">
                            </td>
                            <td id="brg_1_rem">
                                <input type="text" id="brg_1_rem_in" name="brg_1_rem">
                            </td>
                        </tr>

                        <tr id="brg_2">
                            <td id="brg_2_no">
                                <input type="text" id="brg_2_no_in" name="brg_2_no">
                            </td>
                            <td id="brg_2_sn">
                                <input type="text" id="brg_2_sn_in" name="brg_2_sn">
                            </td>
                            <td id="brg_2_ran">
                                <input type="text" id="brg_2_ran_in" name="brg_2_ran">
                            </td>
                            <td id="brg_2_desc">
                                <input type="text" id="brg_2_desc_in" name="brg_2_desc">
                            </td>
                            <td id="brg_2_oem">
                                <input type="text" id="brg_2_oem_in" name="brg_2_oem">
                            </td>
                            <td id="brg_2_od">
                                <input type="text" id="brg_2_od_in" name="brg_2_od">
                            </td>
                            <td id="brg_2_bc">
                                <input type="text" id="brg_2_bc_in" name="brg_2_bc">
                            </td>
                            <td id="brg_2_tc">
                                <input type="text" id="brg_2_tc_in" name="brg_2_tc">
                            </td>
                            <td id="brg_2_ss">
                                <input type="text" id="brg_2_ss_in" name="brg_2_ss">
                            </td>
                            <td id="brg_2_loc">
                                <input type="text" id="brg_2_loc_in" name="brg_2_loc">
                            </td>
                            <td id="brg_2_stat">
                                <input type="text" id="brg_2_stat_in" name="brg_2_stat">
                            </td>
                            <td id="brg_2_rem">
                                <input type="text" id="brg_2_rem_in" name="brg_2_rem">
                            </td>
                        </tr>

                        <tr id="brg_3">
                            <td id="brg_3_no">
                                <input type="text" id="brg_3_no_in" name="brg_3_no">
                            </td>
                            <td id="brg_3_sn">
                                <input type="text" id="brg_3_sn_in" name="brg_3_sn">
                            </td>
                            <td id="brg_3_ran">
                                <input type="text" id="brg_3_ran_in" name="brg_3_ran">
                            </td>
                            <td id="brg_3_desc">
                                <input type="text" id="brg_3_desc_in" name="brg_3_desc">
                            </td>
                            <td id="brg_3_oem">
                                <input type="text" id="brg_3_oem_in" name="brg_3_oem">
                            </td>
                            <td id="brg_3_od">
                                <input type="text" id="brg_3_od_in" name="brg_3_od">
                            </td>
                            <td id="brg_3_bc">
                                <input type="text" id="brg_3_bc_in" name="brg_3_bc">
                            </td>
                            <td id="brg_3_tc">
                                <input type="text" id="brg_3_tc_in" name="brg_3_tc">
                            </td>
                            <td id="brg_3_ss">
                                <input type="text" id="brg_3_ss_in" name="brg_3_ss">
                            </td>
                            <td id="brg_3_loc">
                                <input type="text" id="brg_3_loc_in" name="brg_3_loc">
                            </td>
                            <td id="brg_3_stat">
                                <input type="text" id="brg_3_stat_in" name="brg_3_stat">
                            </td>
                            <td id="brg_3_rem">
                                <input type="text" id="brg_3_rem_in" name="brg_3_rem">
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