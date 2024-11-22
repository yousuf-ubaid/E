<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$view = '';

$AYTD_data = $sub_data['AYTD'];
$LYTD_data = $sub_data['LYTD'];
$last_month = $sub_data['last_month'];
$selected_month = $sub_data['selected_month'];

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
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

    #fm-rpt-table tr.val-row:hover{
        background-color: rgb(222, 232, 252) !important;
    }
</style>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <form id="comapny_filterform" method="post" action="" target="_blank">
        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
        <input type="hidden" id="company_id" name="ComapnyID" value="<?php echo $Company?>">
    </form>




<?php
if($uncateInex!=1)
{?>

<?php


if(!empty($confirmation))

{?>

<table class="borderSpace report-table-condensed" id="fm-rpt-table" style="width: 100% !important; border: 0px solid">
    <tr>
        <td></td>
        <?php
        foreach ($periods as $row_period){
            echo '<td class="td-column" style="font-weight: bold; width: 65px"><span class="td-header">'.$row_period['title'].'</span></td>';
        }
        ?>
    </tr>
    <?php
    foreach ($rpt_data as $row){
        if($row['type'] == 'header'){
           // $view .= '<tr><td class="td-group" style="width: 220px"><span class=""><i class="fa fa-minus-square"></i>  '.$row['des'].'</span></td>';
            $view .= '<td colspan="11" class=" td-column" style="text-align: center; width: 100px"></td></tr>';
        }
        elseif($row['type'] == 'subCategory'){
            $row_autoID = $row['row_autoID'];

            $view .= '<tr class="val-row"><td class=""><div style="width: 200px"><a href="#" class="drill-down-cursor" onclick="opendrilldown_mpr('.$row_autoID.','.$period.','.$temMasterID.')">'.$row['des'].'</a></div> </td>';
            foreach ($periods as $period_key=>$row_period) {
                $val = 0;
                if(in_array($period_key, $percentage_cols)){
                    switch($period_key){
                        case 'selected_month_last_month_rev':
                            $last_month_amount = (array_key_exists($row_autoID, $last_month))? $last_month[$row_autoID]: 0;
                            $selected_month_amount = (array_key_exists($row_autoID, $selected_month))? $selected_month[$row_autoID]: 0;

                            if($last_month_amount != 0 && $selected_month_amount != 0){
                                $amount = ($last_month_amount /$selected_month_amount );
                                if($amount>=0){
                                    $val = number_format( $amount, 0);
                                }else
                                {
                                    $val = '('.number_format(abs($amount), 0).')';
                                }

                            }
                        break;

                        case 'AYTD_LYTD':
                            $AYTD_amount = (array_key_exists($row_autoID, $AYTD_data))? $AYTD_data[$row_autoID]: 0;
                            $LYTD_amount = (array_key_exists($row_autoID, $LYTD_data))? $LYTD_data[$row_autoID]: 0;
                            $amount = $AYTD_amount - $LYTD_amount;
                            if($amount>=0)
                            {
                                $val = number_format($amount, $dPlace);
                            }else
                            {
                                $val = '('.number_format(abs($amount) , $dPlace).')';
                            }

                        break;

                        case 'Var':
                            $AYTD_amount = (array_key_exists($row_autoID, $AYTD_data))? $AYTD_data[$row_autoID]: 0;
                            $LYTD_amount = (array_key_exists($row_autoID, $LYTD_data))? $LYTD_data[$row_autoID]: 0;
                            $AYTD_LYTD_amount = $AYTD_amount - $LYTD_amount;

                            if($LYTD_amount != 0 && $AYTD_LYTD_amount != 0){
                                $amount = (($AYTD_LYTD_amount/ $LYTD_amount) * 100);
                                if($amount>=0)
                                {
                                    $val = number_format( (($AYTD_LYTD_amount/ $LYTD_amount) * 100), 0);
                                }else
                                {
                                    $val = '('.number_format( abs(($AYTD_LYTD_amount/ $LYTD_amount) * 100), 0).')';
                                }


                            }
                        break;

                        default:
                            $rev_key = $row_period['calculation'];
                            if(array_key_exists($rev_key, $row['amount'])){
                                $amount = $row['amount'][$rev_key];

                                if(array_key_exists($rev_key, $gross_val)){
                                    $gross = $gross_val[$rev_key];
                                    $amount = ($gross == 0)? 0: ($amount/$gross) * 100;
                                    if($amount>=0)
                                    {
                                        $val = number_format($amount,0);
                                    }else
                                    {
                                        $val = '('.number_format(abs($amount),0).')';
                                    }

                                }
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

                }

                $view .= '<td class="td-column" style="width: 100px">' . $val . '</td> ';
            }
            $view .= '</tr>';
        }
        elseif($row['type'] == 'groupTotal'){
            $row_autoID = $row['row_autoID'];
            $view .= '<tr class="val-row"><td class="td-group"><span class="">  '.$row['des'].'</span></td>';
            foreach ($periods as $period_key=>$row_period) {
                if(in_array($period_key, $percentage_cols)){
                    $amount = 0;
                    switch($period_key){
                        case 'selected_month_last_month_rev':
                            $last_month_amount = (array_key_exists($row_autoID, $last_month))? $last_month[$row_autoID]: 0;
                            $selected_month_amount = (array_key_exists($row_autoID, $selected_month))? $selected_month[$row_autoID]: 0;

                            if($last_month_amount != 0 && $selected_month_amount != 0){
                                $amount = ($last_month_amount /$selected_month_amount);
                                if($amount>=0)
                                {
                                    $val = number_format( $amount, 0);
                                }else
                                {
                                    $val = '('.number_format(abs($amount), 0).')';
                                }

                            }
                        break;

                        case 'AYTD_LYTD':
                            $AYTD_amount = (array_key_exists($row_autoID, $AYTD_data))? $AYTD_data[$row_autoID]: 0;
                            $LYTD_amount = (array_key_exists($row_autoID, $LYTD_data))? $LYTD_data[$row_autoID]: 0;
                            $amount = $AYTD_amount - $LYTD_amount;
                            if($amount>=0)
                            {
                                $val = number_format($amount, $dPlace);
                            }else
                            {
                                $val = '('.number_format(abs($amount), $dPlace).')';
                            }


                        break;

                        case 'Var':
                            $AYTD_amount = (array_key_exists($row_autoID, $AYTD_data))? $AYTD_data[$row_autoID]: 0;
                            $LYTD_amount = (array_key_exists($row_autoID, $LYTD_data))? $LYTD_data[$row_autoID]: 0;
                            $AYTD_LYTD_amount = $AYTD_amount - $LYTD_amount;

                            if($LYTD_amount != 0 && $AYTD_LYTD_amount != 0){
                                $amount = (($AYTD_LYTD_amount/ $LYTD_amount) * 100);
                                if($amount>=0)
                                {
                                    $val = number_format( $amount, 0);
                                }else
                                {
                                    $val = '('.number_format(abs($amount) , 0).')';
                                }


                            }
                        break;

                        default:

                            if($gross_rows == $row_autoID){ //Gross row
                                $val = '-';
                            }
                            else{
                                $rev_key = $row_period['calculation'];

                                if(array_key_exists($rev_key, $row['amount'])){
                                    $amount = $row['amount'][$rev_key];

                                    if(array_key_exists($rev_key, $gross_val)){
                                        $gross = $gross_val[$rev_key];
                                        $amount = ($gross == 0)? 0: ($amount/$gross) * 100;
                                        if($amount>=0)
                                        {
                                            $val = number_format($amount, $dPlace);
                                        }else
                                        {
                                            $val = '('.number_format(abs($amount) , $dPlace).')';
                                        }

                                    }
                                }
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

                }

                $view .= '<td class="td-group td-column " style="width: 100px">' . $val . '</td>';
            }
            $view .= '</tr>';
        }
    }

    echo $view;
    ?>
</table>
    <?php } else {?>
    <div class="row" style="margin: 0 auto; border: 0px solid">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
               Template Configuration Not Completed.
            </div>
        </div>
    </div>
    <?php }?>

    <?php }else {?>
    <div class="row" style="margin: 0 auto; border: 0px solid">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $fieldname?>
            </div>
        </div>
    </div>

    <?php }?>


<script type="text/javascript">
    function opendrilldown_mpr(id,period,temMasterID) {
        $('#comapny_filterform').attr('action', "<?php echo site_url('Finance_dashboard/mpr_drilldown'); ?>/"+id+'/'+period+'/'+temMasterID, "blank");
        $('#comapny_filterform').submit();
    }

    </script>

<?php

