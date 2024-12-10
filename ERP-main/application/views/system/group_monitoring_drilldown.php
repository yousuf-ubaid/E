<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('dashboard_groupmonitoring_lang', $primaryLanguage);
if (!empty($outputdrilldown)) {
    ?>

    <!--<div class="row" style="margin-top: 5px">

        <div class="col-md-6">
            <?php /*if (!empty($customers)) { */ ?>
                <div style="font-size: 12px;"><strong>Customer Name</strong> : <?php /*echo $customers['customerName'] */ ?> </div>
            <?php /*}*/ ?>
        </div>
    </div>-->
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderReport">

            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <!--<div class="reportHeader reportHeaderColor" style="">
                <span style="font-size: 12px; color: black; font-weight: normal;" class="pull-left">Customer Name</strong> : <?php /*echo $customers['customerName'] */ ?></span> <span style="    padding-left: 22%;"><strong>Collection Summary Drill Down</strong></span></div>
            <div style="">-->


            <table class="borderSpace report-table-condensed" id="tbl_report">
               <!-- <h5><strong>Income Statement </strong></h5>-->
                <thead class="report-header">
                <tr>
                    <th><?php echo $this->lang->line('dashboard_companyname') ?></th>
                    <?php
                    if (!empty($month)) {
                        foreach ($month as $key => $val) {
                            echo ' <th>' . $val . '</th>';
                        }
                    }
                    ?>
                    <th><?php echo $this->lang->line('dashboard_total') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $category = array();
                foreach ($outputdrilldown as $val) {
                    $category[$val["companyname"]][$val["companyname"]][] = $val;
                }
                if (!empty($category)) {
                    $total = 0;
                    $grandTotal = array();
                    $grandTotalHorizontal = array();
                    foreach ($category as $key => $mainCategory) {
                        echo "<tr><td><div class='mainCategoryHead'></div></td></tr>";
                        $maintotal = array();
                        $maintotalHorizontal = array();
                        foreach ($mainCategory as $key2 => $subCategory) {
                            // echo "<tr><td><div style='margin-left: 30px' class='subCategoryHead'>" . $key2 . "</div></td></tr>";
                            $subtotal = array();
                            $subtotalHorizontal = array();
                            $i = 1;
                            $count = count($subCategory);
                            foreach ($subCategory as $item) {

                                $total1 = 0;
                                $totallinewise = 0;
                                $graandtotalsum = 0;
                                echo "<tr class='hoverTr'>";

                                echo '<td style="text-align: left">
                                        ' . $item["companyname"] . '
                                      
                                       </td>';


                                foreach ($month as $key5 => $value2) {
                                    //echo '<pre>'; print_r()  echo '</pre>';
                                    $total1 += $item[$key5];
                                    $subtotal[$key5][] = (float)$item[$key5];
                                    $maintotal[$key5][] = (float)$item[$key5];
                                    $grandTotal[$key5][] = (float)$item[$key5];

                                    echo '<td class="text-right">' . round($item[$key5]) . '</td>';




                                }
                                if($total1<0)
                                {
                                    echo '<td class="text-right">(' . round(abs($total1)) . ')</td>';
                                }else
                                {
                                    echo '<td class="text-right">' . round($total1) . '</td>';
                                }



                                $subtotalHorizontal[] = $total;
                                $maintotalHorizontal[] = $total;
                                $grandTotalHorizontal[] = $total;
                                echo "</tr>";
                                $i++;
                            }
                        }
                    }
                    echo "<tr><td class='reporttotalblack'>".$this->lang->line('dashboard_total') ."</td>";
                    foreach ($month as $key9 => $value2) {
                        $sum = array_sum($grandTotal[$key9]);

                        echo "<td class='reporttotalblack text-right'>" . round($sum). "</td>";
                        $graandtotalsum+=$sum;
                    }

                    echo "<td class='reporttotalblack text-right'>" . round($graandtotalsum). "</td>";

                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3"></td>
                </tr>
                </tfoot>
            </table>

        </div>
    </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
<script>

    $('#tbl_rpt_salesorder').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>