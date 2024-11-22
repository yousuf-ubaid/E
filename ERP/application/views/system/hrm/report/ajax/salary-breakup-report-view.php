<?php
$dPlace = 3;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<style>
    .sponsor_row{
        background: #6f6767b8 !important; //#3c8dbc #e40913b8;
        color: #fff2e1;
        font-weight: bold;
        font-size: 11px;
    }

    .amount{
        text-align: right;
    }

    .odd_column {
        background-color: #e0e2e6;
    }

    .tbl-th{
        background: #303a4a !important;
        color: #fff2e1;
    }

    #break-down-tbl tbody tr:hover td{
        background: #cc9a1c !important;
        cursor: pointer;
        color: #fff;
    }

    #break-down-tbl2 tbody tr:hover td{
        background: #cc9a1c !important;
        cursor: pointer;
        color: #fff;
    }
</style>

<div style="max-width: 100%; height: 450px">
<table class="<?=table_class()?>" id="break-down-tbl">
    <thead>
    <tr>
        <th class="tbl-th"><?php echo $this->lang->line('hrms_reports_contractor')?><!--Contractor--></th>
        <?php
        $full_tot = []; $summery_tot = [];
        $header_str = '';
        foreach ($period_arr as $key=>$row){
            $full_tot[$row] = 0;
            $summery_tot[$row] = 0;
            $header_str .= '<th class="tbl-th">'.date('Y - M', strtotime($row)).'</th>';
        }
        echo $header_str;
        ?>
        <th class="tbl-th"><?php echo $this->lang->line('common_total')?> YTD</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $amount_array = array_column($details, 'am_key');
    foreach ($sponsor as $s_row){
        $sponsor_id = $s_row['sponsorID'];
        $cols_pan = count($period_arr)+2;

        echo '<tr><th class="sponsor_row" colspan="'.$cols_pan.'">'.$s_row['sponsorName'].' <span class="pull-right">'.$s_row['sponsorName'].'</span></th></tr>';


        foreach ($s_cats as $cat){
            $cat_id = $cat['salaryCategoryID'];
            echo '<tr><td>'.$cat['salaryDescription'].'</td>';

            $tot_amount = 0;
            foreach ($period_arr as $key=>$row){
                $search_key = "{$row}-{$sponsor_id}-{$cat_id}";
                $am_key = array_search($search_key, $amount_array);
                $amount = ($am_key !== false)? $details[$am_key]['amount'] : 0;
                $tot_amount += $amount;

                $full_tot[$row] = ($full_tot[$row] + $amount);

                $class = ($key%2 == 1)? 'odd_column': '';
                echo '<td class="amount '.$class.'">'.number_format($amount, $dPlace).'</td>';
            }

            $class = ($class == '')? 'odd_column': '';
            echo '<td class="amount '.$class.'">'.number_format($tot_amount, $dPlace).'</td></tr>';
        }
    }
    ?>
    </tbody>

    <tfoot>
    <tr>
        <td></td>
        <?php
        foreach ($period_arr as $key=>$row){
            echo '<th class="amount ">'.number_format($full_tot[$row], $dPlace).'</th>';
        }
        ?>
        <td></td>
    </tr>
    </tfoot>
</table>
</div>


<div class="col-md-12" style="height: 50px">&nbsp;</div>

<h4>&nbsp;</h4>
<table class="<?=table_class()?>" id="break-down-tbl2">
    <thead>
    <tr>
        <th class="tbl-th"><?php echo $this->lang->line('hrms_reports_summary_cost')?><!--Summary Cost--></th>
        <?=$header_str?>
        <th class="tbl-th"><?php echo $this->lang->line('common_total')?> YTD</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $amount_array = array_column($sum_det, 'am_key');

    foreach ($s_cats as $cat){
        $cat_id = $cat['salaryCategoryID'];
        echo '<tr><td>'.$cat['salaryDescription'].'</td>';

        $tot_amount = 0;
        foreach ($period_arr as $key=>$row){
            $search_key = "{$row}-{$cat_id}";
            $am_key = array_search($search_key, $amount_array);
            $amount = ($am_key !== false)? $sum_det[$am_key]['amount'] : 0;
            $tot_amount += $amount;

            $summery_tot[$row] = ($summery_tot[$row] + $amount);

            $class = ($key%2 == 1)? 'odd_column': '';
            echo '<td class="amount '.$class.'">'.number_format($amount, $dPlace).'</td>';
        }

        $class = ($class == '')? 'odd_column': '';
        echo '<td class="amount '.$class.'">'.number_format($tot_amount, $dPlace).'</td></tr>';
    }
    ?>
    </tbody>

    <tfoot>
    <tr>
        <td></td>
        <?php
        foreach ($period_arr as $key=>$row){
            echo '<th class="amount ">'.number_format($summery_tot[$row], $dPlace).'</th>';
        }
        ?>
        <td></td>
    </tr>
    </tfoot>
</table>

<script>
    $('#break-down-tbl').tableHeadFixer({
        head: true,
        footer: true,
    });
</script>
