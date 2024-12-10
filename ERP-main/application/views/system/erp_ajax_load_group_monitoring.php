<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('dashboard_groupmonitoring_lang', $primaryLanguage);



?>



<div class="box box-warning">
    <div class="box-body" style="display: block;">

        <h4 class="box-title"><center><?php echo $this->lang->line('dashboard_profitandloss');?><center></h4>
        <div class="col-md-12">
            <div id="overallperformanc" style="height: 200px">

            </div>
        </div>

    </div>
    </div>


<div class="box box-warning">
    <div class="box-header with-border">

    <br>

        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div" style="overflow: auto">

                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <h5><strong><?php echo $this->lang->line('dashboard_incomestatement');?> </strong></h5>
                        <thead class="report-header">
                        <tr>
                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <?php
                            if (!empty($month)) {
                                foreach ($month as $key => $val) {
                                    echo ' <th>' . $val . '</th>';
                                }
                            }
                            ?>
                            <th><?php echo $this->lang->line('dashboard_total')?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $category = array();
                        foreach ($output as $val) {
                            $category[$val["subCategorynew"]][$val["subCategorynew"]][] = $val;
                        }
                        if (!empty($category)) {
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

                                        $total = 0;
                                        $grandtotalnew = 0;
                                        $operatingprofittot = 0;
                                        $netprofittot = 0;
                                        if(isset($item["accountCategoryTypeID"])|| !empty($item["accountCategoryTypeID"]))
                                        {
                                            $accountCategoryTypeID = $item["accountCategoryTypeID"];
                                        }else
                                        {
                                            $accountCategoryTypeID = 0;
                                        }
                                        echo "<tr class='hoverTr'>";
                echo '<td style="text-align: left">

                                        
                                       <a href="#" class="drill-down-cursor" onclick="incomestatementdrill('.$accountCategoryTypeID.',\''.$item["CategoryTypeDescriptionlanguage"].'\')">'.$item["CategoryTypeDescriptionlanguage"].'</a>
                                      
                                       </td>';


                                            foreach ($month as $key5 => $value2) {
                                                //echo '<pre>'; print_r()  echo '</pre>';
                                                $total += $item[$key5];
                                                $subtotal[$key5][] = (float)$item[$key5];
                                                $maintotal[$key5][] = (float)$item[$key5];
                                                $grandTotal[$key5][] = (float)$item[$key5];
                                                    if($item[$key5]<0)
                                                    {
                                                        echo '<td class="text-right">(' . round(abs($item[$key5])) . ')</td>';
                                                    }else
                                                    {
                                                        echo '<td class="text-right">' . round($item[$key5]) . '</td>';
                                                    }


                                            }
                                        if($total<0)
                                        {
                                            echo '<td class="text-right">(' . round(abs($total)) . ')</td>';
                                        }else
                                        {
                                            echo '<td class="text-right">' . round($total) . '</td>';
                                        }
                                        $subtotalHorizontal[] = $total;
                                        $maintotalHorizontal[] = $total;
                                        $grandTotalHorizontal[] = $total;
                                        echo "</tr>";
                                        $i++;
                                    }

                                    if($item['catergorytypenew'] == 2)
                                    {
                                        echo "<tr><td class='reporttotalblack'>".$this->lang->line('dashboard_grossprofit')."</td>";
                                        foreach ($month as $key9 => $value2) {

                                            $sum = array_sum($grandTotal[$key9]);
                                            echo "<td class='reporttotalblack text-right'>" . round($sum) . "</td>";
                                            $grandtotalnew += $sum;

                                        }
                                        echo "<td class='reporttotalblack text-right'>" . round($grandtotalnew) . "</td>";
                                    }
                                    if($item['catergorytypenew']== 3)
                                    {
                                        echo "<tr><td style='font-weight: bold'><strong>".$this->lang->line('dashboard_operatingprofit')."</strong></td>";
                                        foreach ($month as $key9 => $value2) {
                                            $sum = array_sum($grandTotal[$key9]);
                                            $operatingprofittot +=$sum;
                                            if($sum<0)
                                            {
                                                echo "<td class='text-right' style='font-weight: bold'>(" . round(abs($sum)) . ")</td>";
                                            }else {
                                                echo "<td class='text-right' style='font-weight: bold'>" . round($sum) . "</td>";
                                            }


                                        }
                                        if($operatingprofittot<0)
                                        {
                                            echo "<td class='text-right' style='font-weight: bold'>(" . round(abs($operatingprofittot)) . ")</td>";
                                        }else {
                                            echo "<td class='text-right' style='font-weight: bold'>" . round($operatingprofittot) . "</td>";
                                        }

                                    }

                                    if(($item['catergorytypenew'] == 5))
                                    {
                                        echo "<tr><td class='reporttotalblack'>".$this->lang->line('dashboard_netprofit')."</td>";
                                        foreach ($month as $key9 => $value2) {
                                            $sum = array_sum($grandTotal[$key9]);
                                            $netprofittot +=$sum;
                                            echo "<td class='reporttotalblack text-right'>" . round($sum) . "</td>";


                                        }
                                        echo "<td class='reporttotalblack text-right'>" . round($netprofittot) . "</td>";
                                    }
                                   /* foreach ($month as $key9 => $value2) {
                                        $sum = array_sum($grandTotal[$key9]);
                                        echo "<td class='reporttotalblack text-right'>" . number_format($total, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }*/

                                }
                            }


                            echo "<tr><td colspan='5'>&nbsp;</td></tr>";
                            $netpro= $this->lang->line('finance_common_net_profit_loss');

                            echo " </tr > ";
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
                <?php
            } else {
                $norecfound= $this->lang->line('common_no_records_found');
                echo warning_message($norecfound);/*"No Records Found!"*/
            }
            ?>
        </div>






    </div>
</div>



<!--  <div class="overlay" id="overlay1"><i class="fa fa-refresh fa-spin"></i></div>-->


</div>

<?php


?>
<div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-6">
       <?php if ($balancesheet_rpt) {?>
        <div class="box box-warning" style="width: 100%;">
            <div class="box-body" style="display: block;">
                <h5><strong><?php echo $this->lang->line('dashboard_balancesheetextract') ?></strong></h5>
                <table id="tbl_rpt_salesreturn" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th style="width: 30%;">&nbsp;</th>
                        <?php
                        $date = range(($date-2),$date);
                        echo '<th>'.$date[2].'</th> ';
                        echo '<th>'.$date[1].'</th> ';
                        echo '<th>'.$date[0].'</th> ';


                       // $DocYear = $this->db->query("SELECT documentYear FROM `srp_erp_generalledger_groupmonitoring` WHERE documentYear IS NOT NULL AND documentYear <= $date GROUP BY documentYear ORDER BY documentYear DESC LIMIT 3")->result_array();
                       /* foreach ($DocYear as $val)
                        {;
                            echo '<th>'.$val['documentYear'].'</th> ';
                        }*/

                        ?>
                        <!-- <th>2019</th>
                         <th>2018</th>
                         <th>2017</th>-->
                    </tr>
                    </thead>


                    <tbody>
                <!--    <?php
/*
                    if ($balancesheet_rpt) {

                        foreach ($balancesheet_rpt as $val) {

                            echo '<tr>';
                            echo '<td>  <a href="#" class="drill-down-cursor" onclick="balancesheetdrilldwn('.$val['accountCategoryTypeID'].',\''.$val["subCategorynew"].'\')">' . $val['subCategorynew'] . '</a></td>';
                            echo '<td style="text-align: right;">'.number_format($val[$date[2]],$this->common_data['company_data']['company_default_decimal'])  . '</td>';
                            echo '<td style="text-align: right;">'.number_format($val[$date[1]],$this->common_data['company_data']['company_default_decimal']). '</td>';
                            echo '<td style="text-align: right;">'.number_format($val[$date[0]],$this->common_data['company_data']['company_default_decimal'])  . '</td>';




                            */?>
                            --><?php
/*                            echo '</tr>';
                        }


                    }*/?>

                       <?php if ($balancesheet_rpt) {
                        foreach ($balancesheet_rpt as $val) {?>
                    <tr>
                        <td> <a href="#" class="drill-down-cursor" onclick="balancesheetdrilldwn('<?php echo $val['accountCategoryTypeID']?>','<?php echo $val["subCategorynew"]?>')"><?php echo $val['subCategorynew']?></a></td>
                        <td style="text-align: right;"><?php echo round($val[$date[2]])?></td>
                        <td style="text-align: right;"><?php echo round($val[$date[1]])?></td>
                        <td style="text-align: right;"><?php echo round($val[$date[0]])?></td>
                    </tr>
                    <?php } }?>


                    </tbody>

                </table>


            </div>
        </div>
           <?php
       } else {
           $norecfound= $this->lang->line('common_no_records_found');
           echo warning_message($norecfound);/*"No Records Found!"*/
       }
       ?>
    </div>
    <div class="form-group col-sm-6">
        <div class="box box-warning" style="width: 100%;">
            <div class="box-body" style="display: block;">
                <div id="localization_pie" style="min-width: 390px; height: 400px; max-width: 600px; margin: 0 auto"></div>
            </div>
        </div>
    </div>
</div>




<?php
$overallPerformanceArr = array();
$amount = 0;
if (!empty($output_chart)) {
    foreach ($output_chart as $val) {
        $data = array();
        $color = "";
        if ($val["subCategory"] == 'Income') {
            $color = '#00A65A';
        }
        if ($val["subCategory"] == 'Cost of Goods Sold') {
            $color = '#F56954';
        }
        if ($val["subCategory"] == 'Expense') {
            $color = '#3C8DBC';
        }
        if ($val["subCategory"] == 'Net Profit') {
            $color ='#00C0EF';
        }
        $series = "{name:'" . $val["subCategorynew"] . "',color: '$color',data:[";
        if (!empty($month)) {
            foreach ($month as $key => $val2) {
                $amount += abs($val[$key]);
                $data[] = abs($val[$key]);
            }
        }
        $series .= join(",", $data);
        $series .= "]}";
        $overallPerformanceArr[] = $series;
    }
}
?>
<script>
    var reflowoverallperformanceWidget = 0;
    var overallperformance;

    $(document).ready(function () {
        $('.modal').on('hidden.bs.modal', function (e) {
            if($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }

        });
        Highcharts.chart('overallperformanc', {
            chart: {
                type: 'spline'
            },
            title: {
                text: '',
                x: -20 //center
            },
            subtitle: {
                text: '',
                x: -20
            },
            xAxis: {
                categories: [<?php echo "'" . join("','", $months2) . "'" ?>],
                labels: {
                    rotation: -60
                },
                gridLineColor: '#F0F0F0',
                gridLineWidth: 1
            },
            yAxis: {
                title: {
                    text: ''
                },
                gridLineColor: '#F0F0F0',
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }],
                tickInterval: 50000
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:2px">{series.name}: </td>' +
                    ' <td style="padding:2px"><b>{point.y:,.0f}</b></td></tr>',
                footerFormat: '</table>',
                shared: false,
                useHTML: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        enabled: true
                    }
                }
            },
            series: [<?php echo join(',', $overallPerformanceArr) ?>]
        });



        generPichart();
        });

    function generPichart() {

        var pieColors = (function () {
            var colors = [],
                house = '#ec7110',
                humaninjury = '#2f4fd8',
                i;

            colors.push(Highcharts.Color(house).get());
            colors.push(Highcharts.Color(humaninjury).get());
            return colors;
        }());

        chart = new Highcharts.Chart(
            {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: "<?php echo $this->lang->line('dashboard_localization')?>"
                },
                tooltip: {
                    pointFormat: '{point.percentage:.1f} %<br>Count: {point.y}'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: false,
                        cursor: 'pointer',
                        colors: pieColors,
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>:<br>{point.percentage:.1f} %<br><?php echo $this->lang->line('dashboard_count')?> : {point.y}<br>',
                            distance: -50,
                            filter: {
                                property: 'percentage',
                                operator: '>',
                                value: 4
                            }
                        },
                    }
                },

                series:[
                    {
                        name: '',
                        colorByPoint: true,
                        data:  [
                            <?php foreach ($localization_pie['empcount'] as $val){?>
                            {"name":"<?php echo trim($val['national'] ?? '')?>","y":<?php echo $val['localEmployee']?>,"id": 0 },
                            {"name":"<?php echo $this->lang->line('dashboard_others'); ?>","y":<?php echo $val['expatriateEmployee']?>,"id": 1 }
                            <?php  }?>

                        ],
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',
                        point:{
                            events:{
                                click: function (event) {
                                  view_employee_countrywise(this.id,this.name);
                                }
                            }
                        }
                    }
                ],
                "chart":{
                    "renderTo":"localization_pie"
                },
            });


    }


</script>

