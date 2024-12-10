<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$view = '';

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
$rpt_curr = $this->common_data['company_data']['company_reporting_currencyID'];
$dPlace = fetch_currency_desimal_by_id($rpt_curr);
?>
<style>
    .td-column{
        text-align: right !important;
    }

    .td-group{
        font-weight: bold;
        font-size: 12px !important;
    }

    .td-header{
        border-bottom: 1px solid;
    }
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    #fm-rpt-table tr.val-row:hover{
        background-color: rgb(222, 232, 252) !important;
    }
   .ex3 {
        background-color: lightblue;

        height: 750px;
        overflow: auto;
    }
</style>
<br>
<br>
<br>
<br>
<br>

<?php
if(!empty($rpt_data_drilldown)) {
    $AYTD_data = $sub_data['AYTD'];
    $LYTD_data = $sub_data['LYTD'];
    $last_month = $sub_data['last_month'];
    $selected_month = $sub_data['selected_month'];

    ?>
    <?php echo footer_page('Right foot', 'Left foot', false); ?>

    <form id="comapny_filterform" method="post" action="" target="_blank">
        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
        <input type="hidden" id="company_id" name="ComapnyID" value="<?php echo $Company_ID?>">
    </form>

    <div class="box box-warning ex3"style="width: 90%;margin: 0 auto;background: #f3f4f5;">
        <div class="box-header with-border">
            <div class="row" style="width: 100%;">
                <div class="col-md-10">
                    <h4 class="box-title">MPR - Monthly Performance Report Drill Down - <?php echo $SubName['drilldownsubdescription'] ?></h4>
                </div>
                <div class="col-md-2">
                    <h4 class="box-title" style="font-size: 14px;"><strong>Curreny : <?php echo $this->common_data['company_data']['company_reporting_currency']; ?></strong></h4>
                </div>
            </div>



        </div>
        <br>
    <table class="borderSpace report-table-condensed" id="fm-rpt-table" style="width: 95%; !important;margin: 0 auto; border: 0px solid">
    <tr>
        <td>

        </td>
        <?php
        $total = [];
        foreach ($periods as $period_key1=>$row_period){
            $total[$period_key1] = 0;
            echo '<td class="td-column" style="font-weight: bold; width: 65px"><span class="td-header">'.$row_period['title'].'</span></td>';
        }
        //echo '<pre>';print_r($total); die();
        ?>
    </tr>
    <?php
    foreach ($rpt_data_drilldown as $row){

        $row_autoID = $row['row_autoID'];
        $view .= '<tr><td class="td-group" style="width: 220px"><span class=""></i>  '.$row['description'].'</span></td>';
        foreach ($periods as $period_key=>$row_period) {
            $val = 0;
            if(in_array($period_key, $percentage_cols)){
                switch($period_key){
                    case 'selected_month_last_month_rev':
                        $last_month_amount = (array_key_exists($row_autoID, $last_month))? $last_month[$row_autoID]: 0;
                        $selected_month_amount = (array_key_exists($row_autoID, $selected_month))? $selected_month[$row_autoID]: 0;

                        if($last_month_amount != 0 && $selected_month_amount != 0){
                            $val = number_format( ( $last_month_amount /$selected_month_amount ), 0);
                            $total[$period_key]= $total[$period_key] + round( ( $last_month_amount /$selected_month_amount ), 0);
                        }

                        break;

                    case 'AYTD_LYTD':
                        $AYTD_amount = (array_key_exists($row_autoID, $AYTD_data))? $AYTD_data[$row_autoID]: 0;
                        $LYTD_amount = (array_key_exists($row_autoID, $LYTD_data))? $LYTD_data[$row_autoID]: 0;
                        $amount = $AYTD_amount - $LYTD_amount;
                        $total[$period_key]= $total[$period_key] + $amount;
                        $val = number_format($amount, $dPlace);
                        break;

                    case 'Var':
                        $AYTD_amount = (array_key_exists($row_autoID, $AYTD_data))? $AYTD_data[$row_autoID]: 0;
                        $LYTD_amount = (array_key_exists($row_autoID, $LYTD_data))? $LYTD_data[$row_autoID]: 0;
                        $AYTD_LYTD_amount = $AYTD_amount - $LYTD_amount;

                        if($LYTD_amount != 0 && $AYTD_LYTD_amount != 0){
                            $val = number_format( (($AYTD_LYTD_amount/ $LYTD_amount) * 100), 0);
                            $total[$period_key]= $total[$period_key] + round( (($AYTD_LYTD_amount/ $LYTD_amount) * 100), 0);
                        }
                        break;

                    default:
                        $rev_key = $row_period['calculation'];
                        if(array_key_exists($rev_key, $row['amount'])){
                            $amount = $row['amount'][$rev_key];

                            $total[$period_key]= $total[$period_key] + $amount;
                         /*   if(array_key_exists($rev_key, $gross_val)){
                                $gross = $gross_val[$rev_key];
                                $amount = ($gross == 0)? 0: ($amount/$gross) * 100;
                                $val = number_format($amount,0);
                            }*/
                        }
                }

            }
            else{
                $amount = $row['amount'][$period_key];
                if($amount>=0)
                {
                    $val = number_format($amount, $dPlace);
                }else
                {
                    $val = '('.number_format(abs($amount) , $dPlace).')';
                }


                $total[$period_key]= $total[$period_key] + $amount;
            }
            $datestart = '';
            $dateend = '';
            if($period_key == 'last_month')
            {
                $datestart = explode('-',$row_period['start']) ;
                $dateEnd = explode('-',$row_period['end']) ;

                $view .= '<td class="td-column" style="width: 100px"><a href="#" class="drill-down-cursor" onclick="drilldown_docmentts('.$row['glAutoID'].','.$datestart[0].','.$datestart[1].','.$datestart[2].','.$dateEnd[0].','.$dateEnd[1].','.$dateEnd[2].')">' . $val . '</a></td> ';
            }
            else if($period_key == 'selected_month')
            {
                $datestart = explode('-',$row_period['start']) ;
                $dateEnd = explode('-',$row_period['end']) ;

                $view .= '<td class="td-column" style="width: 100px"><a href="#" class="drill-down-cursor" onclick="drilldown_docmentts('.$row['glAutoID'].','.$datestart[0].','.$datestart[1].','.$datestart[2].','.$dateEnd[0].','.$dateEnd[1].','.$dateEnd[2].')">' . $val . '</a></td> ';

                }else if($period_key == 'AYTD')
            {
                $datestart = explode('-',$row_period['start']) ;
                $dateEnd = explode('-',$row_period['end']) ;
                $view .= '<td class="td-column" style="width: 100px"><a href="#" class="drill-down-cursor" onclick="drilldown_docmentts('.$row['glAutoID'].','.$datestart[0].','.$datestart[1].','.$datestart[2].','.$dateEnd[0].','.$dateEnd[1].','.$dateEnd[2].')">' . $val . '</a></td> ';
            }


            else {
                $view .= '<td class="td-column" style="width: 100px">' . $val . '</td> ';
            }


        }
        $view .= '</tr>';



    }

    $view .= '<tr class="val-row"><td class="td-group"><span class="">Total</span></td>';
    foreach ($periods as $period_key=>$row_period) {
        $n = '0';
        if(in_array($period_key, $percentage_cols)){
            if($period_key == 'selected_month_last_month_rev'){
                if($total[$period_key]>=0)
                {
                    $n = number_format($total[$period_key],$dPlace) ;
                }else
                {
                    $n = '('.(number_format(abs($total[$period_key]),$dPlace) ).')' ;
                }

            }
        }
        else{
            if($total[$period_key]>=0)
            {
                 $n = number_format($total[$period_key],$dPlace) ;
            }else
            {
                  $n = '('.(number_format(abs($total[$period_key]),$dPlace) ).')' ;
            }
        }
        $view .= '<td class="td-column reporttotal" style="width: 100px">' . $n . '</td>';
    }
    $view .= '</tr>';


    echo $view;
    ?>
</table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results" style="width: 90%;margin: 0 auto;background: #f3f4f5;">There Are No Records To Display.</div>
    <?php
}
?>

<script type="text/javascript">
    function drilldown_docmentts(glAutoID,startY,startM,startD,EndY,EndM,EndD) {
        $('#comapny_filterform').attr('action', "<?php echo site_url('Finance_dashboard/mpr_drilldown_docuemt'); ?>/"+glAutoID+'/'+startY+'/'+startM+'/'+startD+'/'+EndY+'/'+EndM+'/'+EndD, "blank");
        $('#comapny_filterform').submit();
    }

    </script>

<?php




