<style>
    .customPad {
        padding: 3px 0px;
    }

    .al {
        text-align: left !important;
    }

    .ar {
        text-align: right !important;
    }

</style>

<?php
$locations = load_pos_location_drop();
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$d = get_company_currency_decimal();
?>
<span class="pull-right">
    <button type="button" id="btn_print_kot_countdown_report" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
    </button>
<a href="" class="btn btn-excel btn-xs" id="btn-excel" download="KOT_Countdown_Report.xls"
   onclick="var file = tableToExcel('print_container_kot_countdown_report', 'KOT Countdown Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </a>
</span>

<div id="print_container_kot_countdown_report">
    <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="outletInfo">
                <?php

                $outletInput = $this->input->post('outletID_f');
                echo get_outletFilterInfo($outletInput);
                ?>
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="pull-right"><?php echo $this->lang->line('common_date'); ?><!--Date-->:
                <strong><?php echo date('d/m/Y'); ?></strong>
                <br/><?php echo $this->lang->line('posr_time'); ?><!--Time-->: <strong>
                    <span class="pcCurrentTime"></span></strong>
            </div>
        </div>
    </div>

    <hr style="margin:2px 0px;">


    <h3 class="text-center">KOT Countdown Report</h3>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br/>

            <?php
            $filterFrom = $this->input->post('filterFrom');
            $filterTo = $this->input->post('filterTo');
            $today = $this->lang->line('posr_today');
            if (!empty($filterFrom) && !empty($filterTo)) {
                echo '  <i>Date from : </i>' . $filterFrom . ' - <i> To: </i>' . $filterTo;
            } else {
                $curDate = date('d-m-Y');
                echo $curDate . ' (' . $today . ')';/*Today*/
            }
            ?>
            </strong>

        </div>
    </div>
    <div style="margin:4px 0px;">
        <?php
       /* $cash = $this->lang->line('posr_cashier');

        if (isset($cashier) && !empty($cashier)) {
            echo '' . $cash . ' ';
            $tmpArray = array();
            foreach ($cashier as $c) {
                //$tmpArray[] = isset($cashierTmp[$c]) ? $cashierTmp[$c] : '';
            }
            $eidno =join(', ', $cashier);
            $q = "SELECT Ename2 as empName FROM srp_employeesdetails  WHERE EIdNo IN($eidno) ";
            $result = $this->db->query($q)->result_array();

            $cashrarr=array_column($result, 'empName');
            echo join(', ', $cashrarr);
            //echo join(', ', $tmpArray);
        }*/
        ?>
    </div>

    <table class="<?php echo table_class_pos(5) ?>">
        <thead>
        <tr>

            <th class="al">Date & Time</th>
            <th class="al">Bill No.</th>
            <th class="al">Cashier</th>
            <th class="al">Bill Start Time</th>
            <th class="al">Bill End Time</th>
            <th class="al">Time taken <br> (Minutes)</th>
            <th class="al">Default Duration <br> (Minutes) </th>
            <th class="al">Early / <span style="color:red;">Exceeded Time</span> <br> (Minutes)</th>
        </tr>
        </thead>
        <tbody id="report-body">
        <?php

        if($profitability_report)
        //if (!empty($profitability_report))
        {
            $code='';
            $desc='';
            $profitability_report = array_group_by($profitability_report, 'wareHouseAutoID');
        foreach ($profitability_report as  $key => $value) {
            foreach ($locations as $loc) {
                if($loc['wareHouseAutoID'] == $key)
                    $code=$loc['wareHouseCode'];
                    $desc= $loc['wareHouseDescription'] ;

            }
            ?>
            <tr>
                <td style="text-align: "><b><span><?php echo $code ." - ". $desc  ?></span></b></span></td>
            </tr>
            <?php
            foreach ($value as $row) {
                ?>
                <tr class="" style="cursor: pointer">
                    <td><?php echo $row['datentime']; ?></td>
                    <td class="text-left"><?php echo $row['billNo']; ?></td>
                    <td class="text-left"><?php echo $row['Cashier']; ?></td>
                    <td class="text-left"><?php echo $row['billStartTime']; ?></td>
                    <td class="text-left"><?php echo $row['billEndTime']; ?></td>
                    <td class="text-right"><?php echo number_format($row['timetaken']); ?></td>
                    <td class="text-right"><?php echo number_format($row['defaultDuration']); ?></td>
                    <?php
                    $diff = $row['defaultDuration'] - $row['timetaken'];
                    if ($diff < 0) { ?>
                        <td class="text-right" style="color:red"><?php echo number_format($diff) ?></td>
                    <?php } else { ?>
                        <td class="text-right"><?php echo number_format($diff) ?></td>
                    <?php } ?>
                </tr>
                <?php
            }
            }
        }
        ?>
        </tbody>

        <tfoot>
        <tr style="font-size:15px !important;">
        </tr>
        </tfoot>
    </table>
    <hr>
    <div style="margin:4px 0px">
        <?php echo $this->lang->line('posr_report_print'); ?><!--Report print by--> : <?php echo current_user() ?>
    </div>
</div>



<!--End of Drill down report modal-->
<script>
    $(document).ready(function (e) {
        $("#btn_print_kot_countdown_report").click(function (e) {
            $.print("#print_container_kot_countdown_report");
        });

        var date = new Date,
            hour = date.getHours(),
            minute = date.getMinutes(),
            seconds = date.getSeconds(),
            ampm = hour > 12 ? "PM" : "AM";

        hour = hour % 12;
        hour = hour ? hour : 12; // zero = 12

        minute = minute > 9 ? minute : "0" + minute;
        seconds = seconds > 9 ? seconds : "0" + seconds;
        hour = hour > 9 ? hour : "0" + hour;


        date = hour + ":" + minute + " " + ampm;
        $(".pcCurrentTime").html(date);
    })

</script><?php
