<style type="text/css">
    .spanWithClick{ color: #00a7d0; }
    .spanWithClick:hover { cursor: pointer; }
</style>

<div class="col-sm-6 pull-left" style="margin: 2% 0; padding: 0px;">
    <table class="table table-bordered table-striped table-condensed" style="width: 100%;">
        <tbody>
        <tr>
            <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Processing </td>
            <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Closed </td>
            <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> Not Approved </td>
        </tr>
        </tbody>
    </table>
</div>
<table class="<?php echo table_class() ?> "  id="" style="margin-top: 3%">
    <thead>
    <tr>
        <th style="width: auto" rowspan="2"> Code </th>
        <th style="width: auto" rowspan="2"> Description </th>
        <th style="width: auto" rowspan="2"> Start Date </th>
        <!--<th style="width: auto" rowspan="2"> Currency </th>-->
        <th style="width: auto" rowspan="2"> Amount <br> [ <?php echo get_employee_currency($empID, 'c_code'); ?> ] </th>
        <th style="width: auto" colspan="4"> Installment Details </th>
        <th style="width: auto" rowspan="2"> Status </th>
    </tr>
    <tr>
        <th style="width: auto"> Settled </th>
        <th style="width: auto"> Pending </th>
        <th style="width: auto"> Skipped </th>
        <th style="width: auto"> Amount  </th>
    </tr>
    </thead>

    <tbody>
<?php
if( !empty($loanData['empCurrentLoans'])) {
    foreach ($loanData['empCurrentLoans'] as $currentLoan) {
    $header = $currentLoan['header'];
    $int = $currentLoan['installment'];
    $dPlace = $currentLoan['installment']['dPlace'];
    $loanID = $header['ID'];
    $status = '';
    $intTD = '';
    $isNotApproved = false;

    if( $header['isClosed'] != 1 ){
        if( $header['confirmedYN'] == 1 && $header['approvedYN'] == 1  ){
            $status = '<span class="label label-success">&nbsp;</span>';
        }
        else{
            $status = '<span class="label label-warning">&nbsp;</span>';
            $isNotApproved = true;
        }
    }
    else{
        $status = '<span class="label label-danger">&nbsp;</span>';
    }


    if( $isNotApproved == false ){
        $loanMoreDet = "'".$header['loanCode']."','".$header['loanDescription']."'";
        $settledAmount = number_format( ($int['settled'] * $int['intAmount']) , $dPlace);
        $pendingAmount = number_format( ($int['pending'] * $int['intAmount']) , $dPlace);
        $intTD ='<td>
                    <span class="spanWithClick" onClick="fetchInstallmentDet(\'settled\','.$loanID.','.$loanMoreDet.')">[ '.$int['settled'].' ]</span>
                    <i class="fa fa-fw fa-long-arrow-right"></i>
                    <span class="pull-right">'.$settledAmount.'</span>
                 </td>
                 <td>
                    <span class="spanWithClick" onClick="fetchInstallmentDet(\'pending\','.$loanID.','.$loanMoreDet.')">[ '.$int['pending'].' ]</span>
                    <i class="fa fa-fw fa-long-arrow-right"></i>
                    <span class="pull-right">'.$pendingAmount.'</span>
                 </td>
                 <td><span class="spanWithClick" onClick="fetchInstallmentDet(\'skipped\','.$loanID.','.$loanMoreDet.')">'.$int['skipped'].'</span></td>';
    }
    else{
        $intTD = '<td>'.$int['settled'].'</td> <td>'.$int['pending'].'</td><td>'.$int['skipped'].'</td> ';
    }

    echo
    '<tr>
        <td><span class="spanWithClick" onclick="load_loanDetails('.$loanID.')">'. $header['loanCode'].'</span> </td>
        <td>'.$header['loanDescription'].'</td>
        <td>'.$header['dDate'].'</td>
        <td align="right"> '.number_format($header['transactionAmount'], $dPlace).'</td>
        '.$intTD.'
        <td align="right">'.number_format($int['intAmount'], $dPlace).'</td>
        <td align="center">'.$status.'</td>
    </tr>';
    }
}
else{
    echo '<tr> <td colspan="10">No records found</td> </tr>';
}
?>
    </tbody>
</table>

<div class="modal fade" id="loanInstallment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title loanInstallment_title" id="myModalLabel"></h4>
            </div>

            <div class="modal-body">
                <div class="row col-sm-12" style="margin-bottom: 2%">
                    <div class="col-sm-3" style="font-weight: 700;">Loan Code </div> <div class="col-sm-9">: <span id="loanCodeSpan"></span></div>
                    <div class="col-sm-3" style="font-weight: 700;">Description </div> <div class="col-sm-9">: <span id="loanDescriptionSpan"></span></div>
                </div>

                <div style="/*max-height: 400px;overflow-y: scroll;*/">
                    <table class="table table-bordered installmentDetTB" style="display: none;" id="settledAndPendingTB">
                        <thead>
                            <tr>
                                <th>Schedule Date</th>
                                <th>Installment No</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered installmentDetTB" style="display: none;" id="skippedTB">
                        <thead>
                            <tr>
                                <th>Schedule Date</th>
                                <th>Installment No</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function load_loanDetails(loanID){
        var $loanDet = ['view', loanID];
        fetchPage('system/loan/emp_loan_create',0,'HRMS','', $loanDet);
    }

    function fetchInstallmentDet(detType, loanID, code, description){
        var installmentDetTB = $(".installmentDetTB");
        var settledAndPendingTB = $('#settledAndPendingTB');
        var skippedTB = $('#skippedTB');
        var title = '';

        installmentDetTB.hide();
        installmentDetTB.find("tr:not(:first)").remove();

        switch( detType ){
            case 'settled':
                title = 'Settled Installments';
                settledAndPendingTB.show();
            break;

            case 'pending':
                title = 'Pending Installments';
                settledAndPendingTB.show();
            break;

            case 'skipped':
                title = 'Skipped Installments';
                skippedTB.show();
            break;
        }

        $('.loanInstallment_title').html(title);
        $('#loanCodeSpan').html(code);
        $('#loanDescriptionSpan').html(description);
        $('#loanInstallment_modal').modal({backdrop:'static'});

        $.ajax({
            type: 'post',
            dataType: 'json',
            data : { 'loanID': loanID, 'dType': detType},
            url: "<?php echo site_url('Loan/empLoan_installmentDetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (data[0] == 'e') {
                    myAlert(data[0], data[1]);
                }
                else{
                    var tr = '';
                    if( detType == 'skipped' ){
                        $.each(data, function(elm, val){
                           tr += '<tr><td>'+val['scheduleDate']+'</td> <td>'+val['installmentNo']+'</td><td>'+val['skippedDescription']+'</td></tr>'
                        });
                        skippedTB.append(tr);
                    }
                    else{
                        $.each(data, function(elm, val){
                            tr += '<tr><td>'+val['scheduleDate']+'</td> <td>'+val['installmentNo']+'</td></td></tr>'
                        });
                        settledAndPendingTB.append(tr);
                    }

                }
            },
            error : function() {
                stopLoad();
                myAlert('e','An Error Occurred! Please Try Again.');
            }
        });
    }
</script>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-08-17
 * Time: 3:43 PM
 */