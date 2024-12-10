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
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$d = get_company_currency_decimal();
?>
<span class="pull-right">
    <button type="button" id="btn_print_profitability_report" class="btn btn-default btn-xs">
        <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
    </button>
<a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Category_Wise_Profitability_Report.xls"
   onclick="var file = tableToExcel('print_container_profitability_report', 'Category Wise Profitability Report'); $(this).attr('href', file);">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
        </a>
</span>

<div id="print_container_profitability_report">
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


    <h3 class="text-center">Category Wise Profitability Report</h3>
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
        $cash = $this->lang->line('posr_cashier');

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
        }
        ?>
    </div>

    <table class="<?php echo table_class_pos(5) ?>">
        <thead>
        <tr>
            <th class="al">Menu Category</th>
            <th class="ar">Sales</th>
            <th class="ar">Cost</th>
            <th class="ar">Gross Profit</th>
            <th class="ar">Margin</th>
        </tr>
        </thead>
        <tbody id="report-body">
        <?php
        $grandTotal = 0;

        if (!empty($profitability_report))
        {
            foreach ($profitability_report as $row)
            {
                $sales = isset($row['sales'])?$row['sales']:0;
                $cos = isset($row['cos'])?$row['cos']:0;
                $gp = isset($row['gp'])?$row['gp']:0;
                if($sales != 0 && $gp != 0){
                    $gp_margin = ($gp/$sales)*100;
                }else{
                    $gp_margin = 0;
                }

                ?>
                <tr class="category_row" cat_id="<?php echo $row['menuCategoryID']?>" style="cursor: pointer" >
                    <td><?php echo $row['menuCategoryDescription']; ?></td>
                    <td class="text-right"><?php echo get_numberFormat($sales); ?></td>
                    <td class="text-right"><?php echo get_numberFormat($cos); ?></td>
                    <td class="text-right"><?php echo get_numberFormat($gp) ?></td>
                    <td class="text-right"><?php echo get_gp_margin($gp,$sales); ?></td>

                </tr>
                <?php
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

<!--Drill down report modal-->
<div aria-hidden="true" role="dialog" id="item_wise_profitability" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-money"></i> Item Wise Profitability
                    <span id="ajxLoader" style="display: none;"
                          class="pull-right">
                        <i class="fa fa-refresh fa-2x fa-spin"></i>
                    </span>
                </h4>

            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="<?php echo table_class_pos(); ?>" id="item_wise_profitability_table">
                        <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Sales </th>
                            <th>COS</th>
                            <th>GP</th>
                            <th>GP Margin</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?> </button>
            </div>
        </div>
    </div>
</div>
<!--End of Drill down report modal-->
<script>
    $(document).ready(function (e) {
        $("#btn_print_profitability_report").click(function (e) {
            $.print("#print_container_profitability_report");
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

    $('#report-body').on('click','.category_row',function (e) {
        var cat_id = $(this).attr('cat_id');
        var from_date = $("#filterFrom").val();
        var to_date = $("#filterTo").val();
        var outlets = $('#outletID_f').val();
        var cashiers = $('#cashier2').val();
        $("#item_wise_profitability").modal('show');
         loadItemWiseProfitabilityReport(cat_id,from_date,to_date,outlets,cashiers);
    });

    function loadItemWiseProfitabilityReport(cat_id,from_date,to_date,outlets,cashiers) {
        $('#item_wise_profitability_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_restaurant/loadItemWiseProfitabilityReport'); ?>",
            "aaSorting": [[0, 'asc']],
            bFilter:false,
            "aoColumns": [
                {"mData": "menuMasterDescription","bSearchable": true},
                {"mData": "sales_amount"},
                {"mData": "cos_amount"},
                {"mData": "gp_amount"},
                {"mData": "gp_margin"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name': 'cat_id', 'value': cat_id});
                aoData.push({'name': 'from_date', 'value': from_date});
                aoData.push({'name': 'to_date', 'value': to_date});
                aoData.push({'name': 'outlets', 'value': outlets});
                aoData.push({'name': 'cashiers', 'value': cashiers});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });

    }

</script>