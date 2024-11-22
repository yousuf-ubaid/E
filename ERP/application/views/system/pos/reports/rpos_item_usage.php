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

    tbody td {
        font-size: 12px !important;
        padding: 1px 10px;
    }

    thead th {
        font-size: 12px !important;
        padding: 3px 10px;
    }

    tfoot th {
        font-size: 12px !important;
        padding: 3px 10px;
    }

</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);


/*echo '<pre>';
print_r($productMix);
echo '</pre>';*/
?>
<span class="pull-right">
    <button type="button" id="btn_print_item_usage" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
    </button>
    <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Item_Usage_Report.xls"
       onclick="var file = tableToExcel('print_container_item_usage_report', 'Item Usage Report'); $(this).attr('href', file);">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
    </a>
</span>
<div id="print_container_item_usage_report">

    <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="outletInfo">
                <?php
                //$outlets = get_active_outletInfo();
                $outletInput = $this->input->post('outlet');

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

    <h3 class="text-center" style="margin:3px 0px;">Item Usage Report </h3>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br/>
            <span>
         <i>Date From:</i>
      <strong>
            <?php
            $filterFrom = $this->input->post('filterFrom');
            $filterTo = $this->input->post('filterTo');
            $from = $this->lang->line('common_from');
            $to = $this->lang->line('common_to');
            $today = $this->lang->line('posr_today');
            if (!empty($filterFrom) && !empty($filterTo)) {
                echo $filterFrom . '</strong><i> -  ' . $to . ': </i> <strong>' . $filterTo;
            } else {
                $curDate = date('d-m-Y');
                //echo $curDate . ' (' . $today . ')';
            }
            ?>
        </strong>
        </div>
    </div>

    <br>
    <table class="<?php echo table_class_pos(5) ?>">
        <thead>
        <tr>
            <th class="al" style="font-size:13px !important;">Item Code</th>
            <th class="al" style="font-size:13px !important;">Secondary Code</th>
            <th class="al" style="font-size:13px !important;">Description</th>
            <th class="al" style="font-size:13px !important;">UOM</th>
            <th class="al" style="font-size:13px !important;">Usage Qty</th>
        </tr>
        </thead>
        <tbody>

        <?php
        if (!empty($item_usage)) {
            foreach ($item_usage as $row) {
                ?>
                <tr>
                    <td><?php echo $row['itemSystemCode'];?></td>
                    <td><?php echo $row['seconeryItemCode'];?></td>
                    <td><?php echo $row['itemDescription'];?></td>
                    <td><?php echo $row['uom'];?></td>
                    <td class="text-right"><?php echo $row['usage_qty']+0;?></td>
                </tr>
                <?php
            }
        }else{
            ?>
            <tr>
                <td class="text-center" colspan="5" >No records found</td>
            </tr>
        <?php
        }
        ?>
        </tbody>
        <tfoot>

        </tfoot>
    </table>
    <hr>
    <div style="margin:4px 0px">
        <?php echo $this->lang->line('posr_report_print'); ?><!--Report print by--> : <?php echo current_user() ?>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<script>
    $(document).ready(function (e) {
        $("#btn_print_item_usage").click(function (e) {
            $.print("#print_container_item_usage_report");
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

</script>