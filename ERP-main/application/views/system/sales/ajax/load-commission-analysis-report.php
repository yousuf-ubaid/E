<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$decimalPlace =get_company_currency_decimal();
$total = 0;
$header = array();
if ($details['icdetails']) { ?>

    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
       <?php
           if ($type == 'html') {
                echo export_buttons('commissionAnalysisReport', 'Commission Analysis Report', True, false);
            }
        ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="commissionAnalysisReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Commission Analysis</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed " style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th ><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <?php
                        $header=array_column($details['category'],'seconeryItemCode','itemAutoID');
                        foreach ($header as $key => $seconeryItemCode) {
                            echo '<th>' . $seconeryItemCode . '</th>';
                        }
                        ?>
                        <th >Commission</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if( $commissionAnalysisType == 1 ) {
                        $icdetails= $details['icdetails'];
                    }elseif( $commissionAnalysisType == 2 ){
                        $icdetails = array_group_by($details['icdetails'], 'DesignationID');
                    }else{
                        $icdetails = array_group_by($details['icdetails'], 'empID');
                    }
                    if( $commissionAnalysisType == 1 ) {
                        $qtytot=0;
                        $total=0;
                        $coltot = [];
                        echo '<tr style="background-color: #d4cccc4d;">';
                        echo '<td><strong></strong></td>';
                        foreach ($header as $key => $seconeryItemCode) {
                            foreach($icdetails as $id => $detail){
                                $coltot[$seconeryItemCode][] = $detail[$seconeryItemCode];
                            }
                            $qtytot=array_sum($coltot[$seconeryItemCode]);
                            echo '<td style="text-align: right" ><strong>'.$qtytot.'</strong></td>';
                        }
                        foreach($icdetails as $id => $detail){
                            $total += $detail['commissionAmount'];
                        }
                        echo '<td  style="text-align: right;"><strong>'.number_format($total, $decimalPlace).'</strong> </td>';
                        echo '</tr>';
                        foreach($icdetails as $id => $detail){
                            echo '<tr>';
                            echo '<td><strong>' . $detail['employee'] . '</strong></td>';
                            foreach ($header as $key => $seconeryItemCode) {
                                echo '<td style="text-align: right" ><strong>' . $detail[$seconeryItemCode] . '</strong></td>';
                            }
                            $total += $detail['commissionAmount'];
                            echo '<td style="text-align: right" ><strong>' . number_format( $detail['commissionAmount'] , $decimalPlace) . '</strong></td>';
                            echo '</tr>';
                        }
                    }else{
                        foreach($icdetails as $id => $detail){
                            $qtytot=0;
                            $total=0;
                            $coltot = [];
                            echo '<tr style="background-color: #d4cccc4d;">';
                            if( $commissionAnalysisType == 2 ) {
                                echo '<td ><strong>' . $detail[0]['DesDescription'] . '</strong></td>';
                            }else{
                                echo '<td ><strong>' . $detail[0]['employee'] . '</strong></td>';
                            }
                            foreach ($header as $key => $seconeryItemCode) {
                                foreach($detail as $id => $val){
                                    $coltot[$seconeryItemCode][] = $val[$seconeryItemCode];
                                }
                                $qtytot=array_sum($coltot[$seconeryItemCode]);
                                echo '<td style="text-align: right"><strong>'.$qtytot.'</strong></td>';
                            }
                            foreach($detail as $id => $val){
                                $total += $val['commissionAmount'];
                            }

                            echo '<td  style="text-align: right"><strong>' . number_format($total, $decimalPlace) . '</strong></td>';
                            echo '</tr>';
                            foreach($detail as $id => $val){
                                echo '<tr >';
                                if( $commissionAnalysisType == 2 ) {
                                    echo '<td>' . $val['employee'] . '</td>';
                                }else{
                                    echo '<td>' . $val['DesDescription'] . '</td>';
                                }
                                foreach ($header as $key => $seconeryItemCode) {
                                    echo '<td style="text-align: right">' . $val[$seconeryItemCode] . '</td>';
                                }
                                echo '<td style="text-align: right">' . number_format($val['commissionAmount'], $decimalPlace) . '</td>';
                                echo '</tr>';
                            }
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>
<?php
} ?>
<script>
    $('#tbl_rpt_salesorder').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });
</script>