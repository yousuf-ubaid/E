<?php
//echo '<pre>'; print_r($header_det); echo '</pre>';
//print_r($currency_groups);

?>
<style>
    .fixHeader_Div {
        height: 400px;
        border: 1px solid #c0c0c0;
    }
</style>


<hr>

<?php

$viewTD = '';

foreach($header_det as $det){
    $caption = $det['captionName'];
    $columnName = $det['fieldName']; //columnName
    $salaryCatID = $det['catID'];
    $payGroupID = $det['payID'];
    $dType = $det['detailType'];
    $count = 0;
    $salaryCatID = ( $salaryCatID == 0 ) ? $columnName : $salaryCatID;  //if it is not from salary declaration
    switch ($dType) {
        case 'H':
            $viewTD .= '<th class="thCols" data-column="'.$columnName.'" data-dtype="'.$dType.'">'.$caption.'</th>';
        break;

        case 'A':
            $viewTD .= '<th class="thCols" data-column="'.$salaryCatID.'" data-dtype="'.$dType.'">'.$caption.'</th>';
        break;

        case 'D':
            $viewTD .= '<th class="thCols" data-column="'.$salaryCatID.'" data-dtype="'.$dType.'">'.$caption.'</th>';
        break;

        case 'G':
            $viewTD .= '<th class="thCols" data-column="G_'.$payGroupID.'" data-dtype="'.$dType.'">'.$caption.'</th>';
        break;
    }


}

$totalTDCount = count($header_det);

$tableID = 'payTB';

echo '
<!--<div class="col-md-6" style="margin-top: 10px; margin-left: -10px; font-size: 15px;"></div>-->
<div class="fixHeader_Div" style="">
    <table class="'.table_class().' paySheetTB"  id="'.$tableID.'" style="">
        <thead>

        <tr class="designTR"  id="headerDetTR">
            '.$viewTD.'
            <th style="width: auto" id="netSalaryTH" data-column="netSalary" rowspan="2"> Net Salary </th>
        </tr>
        </thead>';

    /*<tr class="designTR"  id="headerDetTR">'.$H_viewTD.' '.$A_viewTD.' '.$D_viewTD.' '.$G_viewTD.'  </tr>*/

    foreach( $currency_groups as $group ){

        $totalCols = $totalTDCount;

        echo '<tr id="tr_'.$group['currency'].'" class="currencyHeader" style="font-size:12px">
               <th style="width: auto" colspan="'.($totalTDCount +1).'"> <strong>Currency : '.$group['currency'].'</strong> </th>
             </tr>
             <tr>
                <td colspan="'.$totalCols.'" align="right" style="font-size:12px"> <strong>Total</strong> </td>
                <td align="right" style="font-size:12px !important;"> <strong>'.number_format($group['amount'], $group['dPlace']) .'</strong> </td>
             </tr>';

    }
    ?>
    </table>
</div>
<div style="margin-bottom: 30px">&nbsp;</div>


<script type="text/javascript">

    var detTB = $('.paySheetTB');

    $(document).ready(function() {
        appendDataToTable();
    });

    function appendDataToTable(){
        var payID = $('#hidden_payrollID').val();
        var templateId = '<?php echo $this->input->post('templateId'); ?>';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'hidden_payrollID': payID, 'templateId': templateId},
            url: "<?php echo site_url('Template_paysheet/fetchPaySheetData'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if( data[0] == 'e' ){
                    myAlert(data[0], data[1]);
                }else{

                    if (data[0] == 's') {
                        var j = 1;
                        var det = data[1];
                        $.each(det, function () {
                            var tableDet = '';
                            var netSalary = 0;
                            var dPlace = det[j]['empDet']['dPlaces'];
                            var headerCount = 0;
                            //var table = $('#payTB_'+det[j]['empDet']['payCurrency']);
                            var table = $('#payTB');
                            var tableTh = $('#tr_'+det[j]['empDet']['payCurrency']);
                            var urlMore = $('#payrollHeaderDet').text()+' - '+det[j]['empDet']['ECode'];
                            //var urlMore = $('#payrollHeaderDet').text();

                            table.find('.thCols').each(function () {
                                var thisTD = $.trim($(this).attr('data-column'));
                                var dType = $.trim($(this).attr('data-dtype'));
                                var val = '';

                                if (dType == 'H') {
                                    var E_ID = det[j]['empDet']['E_ID'];
                                    val = ( $.trim(det[j]['empDet'][thisTD]) == '' ) ? '' : det[j]['empDet'][thisTD];
                                    if( headerCount == 0 ){
                                        var fontColor = ( det[j]['netSalary'] < 0 )? 'style="color: #0e23c7"' : '';
                                        val = '<a href="<?php echo site_url('Template_paysheet/pay_slip'); ?>/'+payID+'/'+E_ID+'/'+urlMore+'" target="_blank" '+fontColor+'>'+val+'</a>';
                                    }

                                    headerCount++;
                                }
                                else {
                                    var salDec = det[j]['empSalDec'];
                                    var notCount = 0;
                                    var arrayLength = salDec.length;

                                    $.each(salDec, function () {
                                        if (this.catID == thisTD) {
                                            val = this.amount;
                                            val = '<div align="right"  data-value="' + val + '">' + commaSeparateNumber(val, dPlace) + '</div>';

                                            if (this.catType == 'A') {
                                                netSalary += parseFloat(this.amount);
                                            } else {
                                                netSalary -= parseFloat(this.amount);
                                            }
                                        } else {
                                            notCount++;
                                        }
                                    });

                                    if (notCount == arrayLength) {
                                        val = '<div align="right"> - </div>';
                                    }

                                }

                                tableDet += '<td>' + val + '</td>';


                            });

                            var redTR = ( det[j]['netSalary'] < 0 )? 'style="background : red; color:#FFF"' : '';
                            //var redTR = '';
                            tableDet += '<td><div align="right"> ' + commaSeparateNumber(det[j]['netSalary'], dPlace) + ' </div></td>';
                            tableTh.after('<tr class="detailTR" '+redTR+'>' + tableDet + '</tr>');
                            //tableDet  +='<td><div align="right"> '+ commaSeparateNumber( netSalary, dPlace)+' </div></td>';

                            j++;

                        });

                        /*setTimeout(function () {
                            detTB.dataTable({
                                "destroy": true,
                                "paging": false,
                                "ordering": false,
                                "info": false
                            });
                        }, 500);*/

                        detTB.tableHeadFixer({
                            head: true,
                            foot: true,
                            left: 0,
                            right: 0,
                            'z-index': 0
                        });
                    }
                }

            },
            error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

</script>



<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-08-11
 * Time: 10:36 AM
 */