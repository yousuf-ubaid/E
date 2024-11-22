
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
        //     $segment = join('', $this->input->post('segment'));
        //     $fn = ($this->input->post('rptType') == 9)? "generateDrilldownReportPdf({$segment})": 'generateDrilldownReportPdf()';
        //     echo export_buttons('tbl_general_ledger_tb_excel', 'General Ledger', $excel = TRUE, $pdf = TRUE, $btnSize = 'btn-xs', $functionName = $fn);
         } ?>
    </div>
</div>
<div id="tbl_general_ledger_tb">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> Drilldown Ledger</div>
            <!--<div
                class="text-center reportHeaderColor"> <?php /*echo "<strong>Date From: </strong>" . $from . " - <strong>Date To: </strong>" . $to */ ?></div>-->
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <!-- <div style="overflow: auto;height: 400px">-->
            <?php if (!empty($output)) { ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                        <th>Segment</th>
                        <th>Doc Number</th>
                        <th>Doc Type</th>
                        <th>Doc Date</th>
                        <?php
                        if($type == 'html') {
                            ?>
                            <th class='hide'>Narration</th>
                            <?php
                        }
                        ?>
                        <th>Narration</th>
                        <th>Party Name</th>
                        <th>Approved By</th>
                        <?php

                        if ($fieldName == "companyLocalAmount") {
                          // echo "<th>Amount(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                            echo "<th>Debit (" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                            echo "<th>Credit (" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                            echo "<th>Balance(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                        } else if ($fieldName == "companyReportingAmount") {
                            echo "<th>Debit (" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                            echo "<th>Credit (" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                            echo "<th>Balance(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                        }

                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $subtotal = array();
                    $category = array();
                    
                //   echo '<pre>'; print_r($output); exit;

                    /* $locKey = array_search('companyLocalAmount', array_column($fieldNameDetails, 'fieldName'));
                     $rptKey = array_search('companyReportingAmount', array_column($fieldNameDetails, 'fieldName'));*/
                    foreach ($output as $val) {
                        $category[$val["GLDescription"]][] = $val;
                    }
                    if (!empty($category)) {
                        $grandTotal = 0;
                        foreach ($category as $key => $mainCategory) {
                            echo "<tr><td colspan='4'><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                            $carryForwardBSrpt = 0;
                            $carryForwardPLrpt = 0;
                            $carryForwardBSloc = 0;
                            $carryForwardPLloc = 0;
                            $carryDebitTotal = 0;
                            $carryCreditTotal = 0;
                            $carryDebitTotalrpt = 0;
                            $carryCreditTotalrpt = 0;
                            $z = 1;
                            foreach ($mainCategory as $item) {

                                $i = 1;
                                echo "<tr class='hoverTr'>";
                                echo "<td class=''> <div style='margin-left: 30px'>" . $item["segmentID"] . "</div></td>";
                                if ($item["documentSystemCode"] != "CF Balance") {
                                    if ($type == 'html') {
                                        echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $item["documentCode"] . '\',' . $item["documentMasterAutoID"] . ')">' . $item["documentSystemCode"] . '</a></td>';
                                    }else{
                                        echo '<td><div>' . $item["documentSystemCode"] . '</div></td>';
                                    }
                                } else {
                                    echo '<td>' . $item["documentSystemCode"] . '</td>';
                                }

                                echo "<td>" . $item["document"] . "</td>";
                                echo "<td>" . $item["documentDate"] . "</td>";
                                if($type == 'html') {
                                    echo "<td class='hide'>" . $item["documentNarration"] . "</td>";
                                }
                                echo "<td>" . trim_value($item["documentNarration"], 40) . "</td>";
                                echo "<td>" . $item["partySystemCode"] . "</td>";
                                echo "<td>" . $item["approvedbyEmpName"] . "</td>";
                                if (!empty($fieldName)) {
                                    $subtotal[$fieldName][] = (float)$item[$fieldName];
                                    if ($fieldName == "companyLocalAmount") {
                                        if($item['amount_type'] == 'dr'){
                                            echo "<td class='text-right'>" . number_format($item[$fieldName], $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                            echo "<td class='text-right'>" . number_format(0, $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                            $carryDebitTotal += $item[$fieldName];
                                        }else{
                                            echo "<td class='text-right'>" . number_format(0, $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                            echo "<td class='text-right'>" . number_format(abs($item[$fieldName]), $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                            $carryCreditTotal += $item[$fieldName];
                                        }


                                       
                                        if ($item["masterCategory"] == 'BS') {
                                            if ($z == 1) {
                                                //if ($item["documentNarration"] == "CF Balance") {
                                                $carryForwardBSloc = $item[$fieldName];
                                                echo "<td class='text-right'>" . number_format($item[$fieldName], $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                                
                                            } else {
                                               
                                                echo "<td class='text-right'>" . number_format(($item[$fieldName] + $carryForwardBSloc), $item[$fieldName . "DecimalPlaces"]) . "</td>";

                                                $carryForwardBSloc += $item[$fieldName];
                                            }
                                        } else if ($item["masterCategory"] == 'PL') {
                                            if ($z == 1) {
                                            

                                                echo "<td class='text-right'>" . number_format($item[$fieldName], $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                                
                                                $carryForwardPLloc = $item[$fieldName];
                                            } else {

                                                echo "<td class='text-right'>" . number_format(($item[$fieldName] + $carryForwardPLloc), $item[$fieldName . "DecimalPlaces"]) . "</td>";

                                                $carryForwardPLloc += $item[$fieldName];

                                            }

                                        }
                                    } else if ($fieldName == "companyReportingAmount") {
                                        if($item['amount_type'] == 'dr'){
                                            echo "<td class='text-right'>" . number_format($item[$fieldName], $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                            echo "<td class='text-right'>" . number_format(0, $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                            $carryDebitTotalrpt += $item[$fieldName];
                                        }else{
                                            echo "<td class='text-right'>" . number_format(0, $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                            echo "<td class='text-right'>" . number_format(abs($item[$fieldName]), $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                            $carryCreditTotalrpt += $item[$fieldName];
                                        }

                                        if ($item["masterCategory"] == 'BS') {
                                            if ($z == 1) {
                                                //if ($item["documentNarration"] == "CF Balance") {
                                                $carryForwardBSrpt = $item[$fieldName];
                                                //}
                                                echo "<td class='text-right'>" . number_format($item[$fieldName], $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                            } else {
                                                echo "<td class='text-right'>" . number_format(($item[$fieldName] + $carryForwardBSrpt), $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                                $carryForwardBSrpt += $item[$fieldName];
                                            }
                                        } else if ($item["masterCategory"] == 'PL') {
                                            if ($z == 1) {
                                                echo "<td class='text-right'>" . number_format($item[$fieldName], $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                                $carryForwardPLrpt = $item[$fieldName];
                                            } else {
                                                echo "<td class='text-right'>" . number_format(($item[$fieldName] + $carryForwardPLrpt), $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                                $carryForwardPLrpt += $item[$fieldName];
                                            }
                                        }
                                    }

                                }
                                $z++;
                                echo "</tr>";
                            }
                            echo "<tr>";
                            echo "<td colspan='7'></td>";
                            if (!empty($fieldName)) {
                                if ($fieldName == "companyLocalAmount") {
                                    echo "<td class='reporttotal text-right'>" . number_format($carryDebitTotal, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    echo "<td class='reporttotal text-right'>" . number_format(abs($carryCreditTotal), $this->common_data['company_data']['company_default_decimal']) . "</td>";          
                                    echo "<td class='reporttotal text-right'>" . number_format(array_sum($subtotal[$fieldName]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    echo "<td></td>";
                                }
                                if ($fieldName == "companyReportingAmount") {
                                    echo "<td class='reporttotal text-right'>" . number_format($carryDebitTotalrpt, $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    echo "<td class='reporttotal text-right'>" . number_format(abs($carryCreditTotalrpt), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    echo "<td class='reporttotal text-right'>" . number_format(array_sum($subtotal[$fieldName]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    echo "<td></td>";
                                }

                            }
                            echo "</tr>";
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    </tfoot>
                </table>
                <?php
            } else {
                echo warning_message("No Records Found!");
            }
            ?>
            <!--</div>-->
        </div>
    </div>
</div>




<div id="tbl_general_ledger_tb_excel" class="hide">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> General Ledger</div>
            <!--<div
                class="text-center reportHeaderColor"> <?php /*echo "<strong>Date From: </strong>" . $from . " - <strong>Date To: </strong>" . $to */ ?></div>-->
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <!-- <div style="overflow: auto;height: 400px">-->
            <?php if (!empty($output)) { ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                        <th>Segment</th>
                        <th>Doc Number</th>
                        <th>Doc Type</th>
                        <th>Doc Date</th>
                        <th>Narration</th>
                        <th>Party Name</th>
                        <th>Approved By</th>
                        <?php

                        if ($fieldName == "companyLocalAmount") {
                            echo "<th>Amount(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                            echo "<th>Balance(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                        } else if ($fieldName == "companyReportingAmount") {
                            echo "<th>Amount(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                            echo "<th>Balance(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                        }

                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $subtotal = array();
                    $category = array();
                    /* $locKey = array_search('companyLocalAmount', array_column($fieldNameDetails, 'fieldName'));
                     $rptKey = array_search('companyReportingAmount', array_column($fieldNameDetails, 'fieldName'));*/
                    foreach ($output as $val) {
                        $category[$val["GLDescription"]][] = $val;
                    }
                    if (!empty($category)) {
                        $grandTotal = 0;
                        foreach ($category as $key => $mainCategory) {
                            echo "<tr><td colspan='4'><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                            $carryForwardBSrpt = 0;
                            $carryForwardPLrpt = 0;
                            $carryForwardBSloc = 0;
                            $carryForwardPLloc = 0;
                            $z = 1;
                            foreach ($mainCategory as $item) {
                                $i = 1;
                                echo "<tr class='hoverTr'>";
                                echo "<td class=''> <div style='margin-left: 30px'>" . $item["segmentID"] . "</div></td>";
                                if ($item["documentSystemCode"] != "CF Balance") {
                                    if ($type == 'html') {
                                        echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $item["documentCode"] . '\',' . $item["documentMasterAutoID"] . ')">' . $item["documentSystemCode"] . '</a></td>';
                                    }else{
                                        echo '<td><div>' . $item["documentSystemCode"] . '</div></td>';
                                    }
                                } else {
                                    echo '<td>' . $item["documentSystemCode"] . '</td>';
                                }
                                echo "<td>" . $item["document"] . "</td>";
                                echo "<td>" . $item["documentDate"] . "</td>";
                                echo "<td class='hide'>" . $item["documentNarration"] . "</td>";
                                echo "<td>" . $item["partySystemCode"] . "</td>";
                                echo "<td>" . $item["approvedbyEmpName"] . "</td>";
                                if (!empty($fieldName)) {
                                    $subtotal[$fieldName][] = (float)$item[$fieldName];
                                    if ($fieldName == "companyLocalAmount") {
                                        echo "<td class='text-right'>" . number_format($item[$fieldName], $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                        if ($item["masterCategory"] == 'BS') {
                                            if ($z == 1) {
                                                //if ($item["documentNarration"] == "CF Balance") {
                                                $carryForwardBSloc = $item[$fieldName];
                                                //}
                                                echo "<td class='text-right'>" . number_format($item[$fieldName], $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                            } else {
                                                echo "<td class='text-right'>" . number_format(($item[$fieldName] + $carryForwardBSloc), $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                                $carryForwardBSloc += $item[$fieldName];
                                            }
                                        } else if ($item["masterCategory"] == 'PL') {
                                            if ($z == 1) {
                                                echo "<td class='text-right'>" . number_format($item[$fieldName], $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                                $carryForwardPLloc = $item[$fieldName];
                                            } else {
                                                echo "<td class='text-right'>" . number_format(($item[$fieldName] + $carryForwardPLloc), $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                                $carryForwardPLloc += $item[$fieldName];
                                            }
                                        }
                                    } else if ($fieldName == "companyReportingAmount") {
                                        echo "<td class='text-right'>" . number_format($item[$fieldName], $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                        if ($item["masterCategory"] == 'BS') {
                                            if ($z == 1) {
                                                //if ($item["documentNarration"] == "CF Balance") {
                                                $carryForwardBSrpt = $item[$fieldName];
                                                //}
                                                echo "<td class='text-right'>" . number_format($item[$fieldName], $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                            } else {
                                                echo "<td class='text-right'>" . number_format(($item[$fieldName] + $carryForwardBSrpt), $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                                $carryForwardBSrpt += $item[$fieldName];
                                            }
                                        } else if ($item["masterCategory"] == 'PL') {
                                            if ($z == 1) {
                                                echo "<td class='text-right'>" . number_format($item[$fieldName], $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                                $carryForwardPLrpt = $item[$fieldName];
                                            } else {
                                                echo "<td class='text-right'>" . number_format(($item[$fieldName] + $carryForwardPLrpt), $item[$fieldName . "DecimalPlaces"]) . "</td>";
                                                $carryForwardPLrpt += $item[$fieldName];
                                            }
                                        }
                                    }

                                }
                                $z++;
                                echo "</tr>";
                            }
                            echo "<tr>";
                            echo "<td colspan='7'></td>";
                            if (!empty($fieldName)) {
                                if ($fieldName == "companyLocalAmount") {
                                    echo "<td class='reporttotal text-right'>" . number_format(array_sum($subtotal[$fieldName]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    echo "<td></td>";
                                }
                                if ($fieldName == "companyReportingAmount") {
                                    echo "<td class='reporttotal text-right'>" . number_format(array_sum($subtotal[$fieldName]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    echo "<td></td>";
                                }

                            }
                            echo "</tr>";
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                    </tfoot>
                </table>
                <?php
            } else {
                echo warning_message("No Records Found!");
            }
            ?>
            <!--</div>-->
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        /*$("#demo").tableHeadFixer({
         // fix table header
         head: true,
         // fix table footer
         foot: false,
         // fix x left columns
         left: 0,
         // fix x right columns
         right: 0,
         // z-index
         'z-index': 1
         });*/
    });
</script>