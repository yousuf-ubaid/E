<style>
    .ar { text-align: right !important; }

    .ac { text-align: center !important; }

    .table-custom-bordered>thead>tr>th, .table-custom-bordered>tbody>tr>th, .table-custom-bordered>tfoot>tr>th, .table-custom-bordered>thead>tr>td, .table-custom-bordered>tbody>tr>td, .table-custom-bordered>tfoot>tr>td {
        border: 1px solid #9a9595;
    }

    .table-custom-bordered thead{
        border: 1px solid #9a9595;
    }

    #top_sales_tb tbody > tr:last-child {
        border-bottom: 1px solid #9a9595 !important;
    }
</style>
<?php
$d = $this->common_data['company_data']['company_default_decimal'];

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>

<span class="pull-right">
    <button type="button" id="btn_print_itemizedSales" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
    </button>
</span>
<div id="printContainer_top_sales">

    <div class="text-center">
        <h4 style="margin-top:2px;"><strong><?php echo $companyInfo['company_name'] ?></strong></h4>
    </div>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="pull-right"><?php echo $this->lang->line('common_date'); ?><!--Date-->:
                <strong><?php echo date('d/m/Y'); ?></strong>
                &nbsp; &nbsp; <?php echo $this->lang->line('posr_time'); ?><!--Time-->: <strong>
                    <span class="pcCurrentTime"></span></strong>
            </div>
        </div>
    </div>

    <hr style="margin:2px 0px;">

    <h3 class="text-center">Top Sales Items</h3>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong>
            <br/>
            Date
            <strong>
                <?php
                $filterFrom = $this->input->post('start_date');
                $filterTo = $this->input->post('end_date');
                $from = $this->lang->line('common_from');
                $to = $this->lang->line('common_to');
                $today = $this->lang->line('posr_today');
                if (!empty($filterFrom) && !empty($filterTo)) {
                    echo '  <i>' . $from . '<!--From--> : </i>' . $filterFrom . ' - <i> ' . $to . ': </i>' . $filterTo;/*To*/
                }
                ?>
            </strong>
        </div>
    </div>

    <br>
    <div class="table-responsive">
        <table class="table table-custom-bordered table-striped table-condensed table-row-select" id="top_sales_tb"> <!--table-custom-bordered-->
            <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle">Outlet</th>
                <?php
                $str = '';
                foreach ($master_menus as $menu){
                    echo '<th colspan="3" class="ac">'.$menu['menuCategoryDescription'].'</th>';
                    $str .= '<th>Item</th><th>Qty</th><th>Value</th>';
                }
                ?>
            </tr>
            <tr><?=$str?></tr>
            </thead>

            <tbody>
            <?php
            $range = range(0,9);

            foreach ($ware_house as $row){ ?>
                <tr>
                    <td rowspan="10" style="vertical-align: middle"><b><?=$row['wareHouseCode'].' - '.$row['wareHouseDescription']?></b></td>
                <?php
                foreach ($range as $rn){
                    echo ($rn > 0)? '<tr>': '';
                    foreach ($row['items'] as $menu) {
                        if(array_key_exists($rn, $menu)) {
                            $val = $menu[$rn];
                            ?>
                            <td><?= $val['menu_des'] ?></td>
                            <td class="ac"><?= $val['qty'] ?></td>
                            <td class="ar"><?= number_format($val['net'], $d) ?></td>
                            <?php
                        }
                        else{
                            echo '<td>&nbsp;</td><td></td><td></td>';
                        }
                    }
                }
            } ?>
            </tbody>
        </table>
    </div>
    <br>

    <hr>


    <div style="margin:4px 0px">
        <?php echo $this->lang->line('posr_report_print'); ?><!-- Report print by--> : <?php echo current_user(); ?>
    </div>
</div>
<script>
    $(document).ready(function (e) {
        $("#btn_print_itemizedSales").click(function (e) {
            $.print("#printContainer_top_sales");
        });

        let date = new Date,
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
    });
</script>