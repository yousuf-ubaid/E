<style type="text/css">
    .fixHeader_Div {
        height: 240px;
        border: 1px solid #c0c0c0;
    }

    div.fixHeader_Div::-webkit-scrollbar, div.smallScroll::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    div.fixHeader_Div::-webkit-scrollbar-track, div.smallScroll::-webkit-scrollbar-track  {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }

    div.fixHeader_Div::-webkit-scrollbar-thumb, div.smallScroll::-webkit-scrollbar-thumb  {
        margin-left: 30px;
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
        width: 3px;
        position: absolute;
        top: 0px;
        opacity: 0.4;
        border-radius: 7px;
        z-index: 99;
        right: 1px;
        height: 10px;
    }
</style>

<div class="fixHeader_Div">
<?php
$H_count = 0;
$A_count = 0;
$D_count = 0;
$H_viewTD = '';
$A_viewTD = '';
$D_viewTD = '';



foreach($header_det as $det){
    $caption = $det['captionName'];
    $dType = $det['detailType'];
    $count = 0;

    switch ($dType) {
        case 'H':
            $H_count++;
            $count = $H_count;
            $H_viewTD .= '<th class="theadtr" style="z-index: 1000000">'.$caption.'</th>';
            break;

        case 'A':
            $A_count++;
            $count = $A_count;
            $A_viewTD .= '<th class="theadtr" style="z-index: 1000000">'.$caption.'</th>';
            break;

        case 'D':
            $D_count++;
            $count = $D_count;
            $D_viewTD .= '<th class="theadtr" style="z-index: 1000000">'.$caption.'</th>';
            break;
    }


}

if ($H_count == 0) {
    $H_count = 1;
    $H_viewTD = '<th class="theadtr" style="z-index: 1000000">&nbsp;</th>';
}
if ($A_count == 0) {
    $A_count = 1;
    $A_viewTD = '<th class="theadtr" style="z-index: 1000000">&nbsp;</th>';
}
if ($D_count == 0) {
    $D_count = 1;
    $D_viewTD = '<th class="theadtr" style="z-index: 1000000">&nbsp;</th>';
}

$totalTDCount = $H_count + $A_count + $D_count + 1;



echo '
<table class="'.table_class().'" id="tablePaysheet" style="">
    <thead>
    <tr>
        <th class="theadtr" style="width: auto; z-index: 1000000" colspan="'.$H_count.'" > &nbsp; </th>
        <th class="theadtr" style="width: auto; z-index: 1000000" colspan="'.$A_count.'" > Addition </th>
        <th class="theadtr" style="width: auto; z-index: 1000000" colspan="'.$D_count.'" > Deduction </th>
        <th class="theadtr" style="width: auto; z-index: 1000000" rowspan="2" > Net Salary </th>
    </tr>
    <tr>'.$H_viewTD.' '.$A_viewTD.' '.$D_viewTD.'  </tr>
    </thead>';


foreach( $currency_groups as $group ){
    $totalCols =  $totalTDCount - 1;

    echo
        '<tr class="theadtr1" style="font-size:12px">
        <th style="width: auto" colspan="'.$totalTDCount.'"> <strong>Currency : '.$group['currency'].'</strong> </th>
    </tr>';
   // echo '<pre>';print_r($empDet); echo '</pre>';


    foreach( $empDet as $det ){
        $thisEmpDet = $det['empDet'];
        $thisEmpSalaryDet = $det['empSalDec'];
        $thisPayCurr = $thisEmpDet['payCurrency'];
        if( $thisPayCurr == $group['currency'] ){
            echo '<tr>';
            foreach( $header_group['H'] as $h ){
                echo '<td>'.$thisEmpDet[$h['fieldName']].'</td>';
            }
            foreach( $header_group['A'] as $a ){
                $c = ( $a['catID'] != 0 )? $a['catID'] : $a['fieldName'];
                echo displayTR($thisEmpSalaryDet, $c, $group['dPlace']);
            }
            foreach( $header_group['D'] as $d ){
                $c = ( $d['catID'] != 0 )? $d['catID'] : $d['fieldName'];
                echo displayTR($thisEmpSalaryDet, $c, $group['dPlace']);
            }
            echo '<td align="right">'.number_format( $det['netSalary'], $group['dPlace']).'</td>';
            echo '</tr>';
        }
    }

    echo
    '<tr>
         <td class="" colspan="'.$totalCols.'" align="right" style="font-size:12px"> <strong>Total</strong> </td>
         <td class="" align="right" style="font-size:12px !important;"> <strong>'.number_format($group['amount'], $group['dPlace']) .'</strong> </td>
    </tr>';

}
?>
</table>
</div>



<script type="text/javascript">
    $(document).ready(function() {
        $('#tablePaysheet').tableHeadFixer({
            head: true,
            foot: true,
            left: 0,
            right: 0,
            'z-index': 999999
        });
    });
</script>

<?php
