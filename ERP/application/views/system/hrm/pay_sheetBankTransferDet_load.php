<?php
$isNonPayroll = $this->input->post('isNonPayroll');
?>


<style>
    .myLabel{
        text-align: left !important;
    }

    .myData{
        font-weight: normal;
    }

    .myFormGroup{
        margin-top: 1px;
        margin-bottom: 0px;
    }
</style>

<div class="col-md-12" style="background: rgba(231, 234, 230, 0.35); padding: 1%; border-radius: 5px; padding-top: 3px">
    <div class="row">
        <div class="form-group col-sm-4 myFormGroup">
            <label class="col-md-4 control-label myLabel"> Bank </label>
            <div class="cols-md-4"> <label class="control-label myData">
                    : <?php echo empty(trim($masterData['bankName'] ?? '')) ? '&nbsp;' : trim($masterData['bankName'] ?? '')  ?> </label>
            </div>
        </div>

        <div class="form-group col-sm-4 myFormGroup">
            <label class="col-md-4 control-label myLabel"> Branch </label>
            <div class="cols-md-4"> <label class="control-label myData">
                    : <?php echo empty(trim($masterData['branchName'] ?? '')) ? '&nbsp;' : trim($masterData['branchName'] ?? '')  ?> </label>
            </div>
        </div>

        <div class="form-group col-sm-4 myFormGroup">
            <label class="col-md-4 control-label myLabel"> Swift Code </label>
            <div class="cols-md-4"> <label class="control-label myData">
                    : <?php echo empty(trim($masterData['swiftCode'] ?? '')) ? '&nbsp;' : trim($masterData['swiftCode'] ?? '')  ?> </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-4 myFormGroup">
            <label class="col-md-4 control-label myLabel"> Account No </label>
            <div class="cols-md-4"> <label class="control-label myData">
                    : <?php echo empty(trim($masterData['accountNo'] ?? '')) ? '&nbsp;' : trim($masterData['accountNo'] ?? '')  ?> </label>
            </div>
        </div>

        <div class="form-group col-sm-4 myFormGroup">
            <label class="col-md-4 control-label myLabel"> Date </label>
            <div class="cols-md-4"> <label class="control-label myData">
                    : <?php echo empty(trim($masterData['transferDate'] ?? '')) ? '&nbsp;' : trim($masterData['transferDate'] ?? '')  ?> </label>
            </div>
        </div>

        <div class="form-group col-sm-4 myFormGroup">
            <label class="col-md-4 control-label myLabel"> Confirmed By </label>
            <div class="cols-md-4"> <label class="control-label myData">
                    : <?php echo empty(trim($masterData['confirmedByName'] ?? '')) ? '&nbsp;' : trim($masterData['confirmedByName'] ?? '')  ?> </label>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="bankTransID" value="<?php echo $bankTransID ;?>">
<div style="margin: 2%"> &nbsp; </div>

<div class="box" style="margin-bottom: 2%">
    <div class="box-header">
        <h3 class="box-title"> Bank Transfer Letter Header </h3>
        <!-- tools box -->
        <div class="pull-right box-tools">
            <button type="button" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                <i class="fa fa-minus"></i></button>
            <?php if( $masterData['confirmedYN'] == 1 ){ ?>
            <button type="button" class="btn btn-default btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
                <i class="fa fa-times"></i></button>
            <?php } ?>
        </div>
        <!-- /. tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body pad">
        <form>
            <textarea name="letterDet" id="letterDet" class="textarea" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" <?php if( $masterData['confirmedYN'] == 1 ){ echo 'disabled'; } ?>>
<?php if( $masterData['confirmedYN'] != 1 ){ ?>
<?php echo $masterData['transferDate']?>

The Manager
<?php echo $masterData['bankName'] ?>

Dear Sir/Madam,

Sub: Salary Transfer <?php echo $payDate ?>


Kindly arrange to transfer the following amounts to the credit of the attached accounts
respectively and debit our current account No. <?php echo $masterData['accountNo'] ?>


Thank You
Your faithfully,
<?php echo $this->common_data['company_data']['company_name']; ?>







Authorized Signature
<?php
}
else{
    echo $masterData['letterDet'];
} ?>

            </textarea> <!--with the total amount of --><?php /*echo $bTransOtherDet */?>
        </form>
    </div>
</div>




<?php
$i = 1;
$j = 0;
$n = 0;
$tot = 0;
$lastBank = null;
$lastCurrency = null;
$lastGroup = null;

foreach($bankTransferDet as $data){

$bankName = trim($data['bankName'] ?? '');
$trCurrency = trim($data['transactionCurrency'] ?? '');
$thisGroup = $bankName.'|'.$trCurrency;

if( $lastBank != $bankName ){
$lastBank = $bankName;

echo '<div style="margin-left: 1%;"><h4>&nbsp;'.$bankName.'</h4></div>';

?>
<div class="table-responsive" >
    <table class="<?php echo table_class(); ?>"  id="bankTransferDetailsTB" style="margin-bottom: 2%">
        <thead>
        <tr style="font-size: 12px;">
            <th class="theadtr" style="width: 5%;"> # </th>
            <th class="theadtr" style="width: 10%"> EMP ID </th>
            <th class="theadtr" style="width: 30%"> Name </th>
            <th class="theadtr" style="width: 30%"> Branch </th>
            <th class="theadtr" style="width: 15%"> Swift Code </th>
            <th class="theadtr" style="width: 20%"> Account No </th>
            <th class="theadtr" style="width: 5%"> Currency </th>
            <th class="theadtr" style="width: 20%"> Amount </th>
        </tr>
        </thead>

        <tbody>
        <?php
        } //end of 1st  if( $lastGroup != $thisGroup ){

        if( $lastCurrency != $trCurrency ){
            $lastGroup = $thisGroup;
            $lastCurrency = $trCurrency;
            echo
                '<tr style="font-size: 12px;">
                <th class="theadtr" colspan="8">'.$trCurrency.'</th>
            </tr>';
        }

        echo '<tr>
                <td align="right">'.$i++.'</td>
                <td>'.$data['ECode'].'</td>
                <td>'.$data['acc_holderName'].'</td>
                <td>'.$data['branchName'].'</td>
                <td>'.$data['swiftCode'].'</td>
                <td>'.$data['accountNo'].'</td>
                <td>'.$trCurrency.'</td>
                <td align="right">'.number_format( $data['transactionAmount'] , $data['transactionCurrencyDecimalPlaces']).'</td>
             </tr>';

        $m = $j + 1;
        if( array_key_exists( $m , $bankTransferDet) ) {
            $nextBank = trim($bankTransferDet[$m]['bankName']);
            $nextGroup = trim($bankTransferDet[$m]['bankName']).'|'.trim($bankTransferDet[$m]['transactionCurrency']);


            if ( $lastGroup != $nextGroup ) {
                $totLine = '';
                if( $n > 0 ){
                    $totThis = number_format( $data['transactionAmount'], $data['transactionCurrencyDecimalPlaces'], '.','') ;
                    $tot += $totThis;
                    $totLine = '<tr> <td colspan="7" align="right">Total</td>  <td class="theadtr" align="right">'. number_format($tot, $data['transactionCurrencyDecimalPlaces']) .'</td></tr>';
                }
                echo $totLine;
                $tot = 0;
                $n = 0;
                $lastCurrency = null;
            }
            else{
                $totThis = number_format( $data['transactionAmount'], $data['transactionCurrencyDecimalPlaces'], '.','') ;
                $tot += $totThis;
                $n++;
            }

            if ( $lastBank != $nextBank ) {
                echo '</tbody></table></div>';
            }
        }
        else{
            $totThis = number_format( $data['transactionAmount'], $data['transactionCurrencyDecimalPlaces'], '.','') ;
            $tot += $totThis;
        }

        $j++;
        }
        if( $n > 0 ){
            echo '<tr> <td colspan="7" align="right">Total</td>  <td class="theadtr" align="right">'. number_format($tot, 2) .'</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<div class="table-responsive">
    <table class="<?php echo table_class(); ?>"  id="bankTransferDetailsTB" style="margin-bottom: 2%">
        <tbody>
        <?php
        foreach($currencySum as $keyCurr=>$currencySumRow){
            $grandTitle = ($keyCurr > 0 )? '' : 'Grand Total';
            echo'<tr>
                    <th style="font-size: 12px; width:85%">'.$grandTitle.'</th>
                    <th style="font-size: 12px; width:5%"> '.$currencySumRow['transactionCurrency'].'</th>
                    <th style="text-align:right;width:10%">'.$currencySumRow['trAmount'].'</th>
                 </tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function(ev){
            $(this).datepicker('hide');
        });

        //$('#bankTransferDetailsTB').dataTable();

        var isConfirmed = "<?php echo $masterData['confirmedYN']; ?>";
        var confirm_bankTransferBtn = $('#confirm_bankTransferBtn');
        var print_bankTransferBtn = $('#print_bankTransferBtn');

        if( isConfirmed == 1 ){
            confirm_bankTransferBtn.hide();
            print_bankTransferBtn.show();
        }else{
            confirm_bankTransferBtn.show();
            print_bankTransferBtn.hide();
        }
    });

    function bankTransfer_print(){
        var url = "<?php echo site_url('Template_paysheet/bankTransfer_print').'/'.$bankTransID.'/'.$isNonPayroll ; ?>";
        window.open(url, '_blank');
    }
</script>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-08-03
 * Time: 10:57 AM
 */