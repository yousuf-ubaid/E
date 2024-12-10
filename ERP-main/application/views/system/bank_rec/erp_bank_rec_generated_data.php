<style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {

        width: 272px;

    }
    </style>
<?php
if ($confirmedYN == 1) {
    $btnDisable = 'disabled';
} else {
    $btnDisable = '';
}
$date_format_policy = date_format_policy();
$current_date = current_format_date();

$CI = get_instance();
$companyID = current_companyID();
$data = $CI->db->query("SELECT c.systemAccountCode, c.GLSecondaryCode, c.GLDescription, c.isBank, c.bankName, c.bankBranch, c.bankSwiftCode, c.bankAccountNumber, c.bankCurrencyID, c.bankCurrencyCode, b.bankCurrencyDecimalPlaces As jdecimal, GLAutoID, SUM(IF(transactionType = 2, - 1 * COALESCE(bankCurrencyAmount, 0), 0))+ SUM(IF(transactionType = 1, COALESCE(bankCurrencyAmount, 0), 0)) AS SumbankAmount FROM `srp_erp_chartofaccounts` AS `c` LEFT JOIN `srp_erp_bankledger` AS `b` ON `c`.`GLAutoID` = `b`.`bankGLAutoID` WHERE `c`.`isBank` = 1 AND `c`.`companyID` = {$companyID}  AND GLAutoID={$this->input->post('GLAutoID')} GROUP BY `c`.`systemAccountCode`, `c`.`GLSecondaryCode`, `c`.`GLDescription`, `c`.`isBank`, `c`.`bankName`, `c`.`bankBranch`, `c`.`bankSwiftCode`, `c`.`bankAccountNumber`, `c`.`bankCurrencyID`, `c`.`bankCurrencyCode` ")->row_array();

?>
<?php $convertFormat = convert_date_format(); ?>

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


?>

<style>
    .select2-container{
        width: 190%!important;
    }
</style>

<strong><?php echo $this->lang->line('treasury_common_bank_name');?><!--Bank Name--></strong> : <?php echo $data['bankName']; ?>
&nbsp;  &nbsp; &nbsp; <strong><?php echo $this->lang->line('common_account_no');?><!--Account No--></strong> : <?php echo $data['bankAccountNumber'] ?>
&nbsp;  &nbsp; &nbsp;  <strong><?php echo $this->lang->line('common_currency');?><!--Currency-->:</strong> : <?php echo $data['bankCurrencyCode'] ?>


<div id="confrimDiv" style="margin-bottom: 60px">
    <div class="col-sm-12 ">

        <button class="pull-right btn btn-primary btn-sm" onclick="insert_bank_rec_data()"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;<?php echo $this->lang->line('treasury_common_add_new_entry');?> <!--Add New Entry-->
        </button>
    </div>
    </br>

    <div class="col-sm-12 ">
        <p style="font-size: 16px" class="pull-right"><?php echo $this->lang->line('treasury_bta_opening_balance');?><!--Opening Balance--> <span
                    style="font-weight: bolder"><?php echo(!empty($openingbalance) ? number_format($openingbalance, 2) : '0.00') ?></span>
        </p>

    </div>

    <div class="page-header">
        <p style=" font-size:16px;color:#3c8dbc;font-weight: bolder"><?php echo $this->lang->line('treasury_bta_receipts');?><!--Receipts--></p>
    </div>


    <!---- testing area------->
    <div hidden >
        <div data-name="popover-content">
            <div class="input-group">
                <input type="text" class="form-control form-control-sm" placeholder="Search" name="search">
                <div class="input-group-btn">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search fa fa-search"></i>
                </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
        </div>
        <div class="col-md-3">
            <form role="form" method="POST">
                <div class="form-group row">            
                    <div class="col-md-9">
                            <div class="input-group datepics">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="clearedDate" id="clearedDate" placeholder="Cleared Dates"
                                                data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value=""
                                                class="form-control" required>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary btn-sm" onclick="updateClearedDate()">Update<!--Save--></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <table id="table1" class="<?php echo table_class() ?>">
        <thead>
        <th><?php echo $this->lang->line('common_document_date');?><!--Document Date--></th>
        <th><?php echo $this->lang->line('common_document_code');?><!--Document Code--></th>
        <th><?php echo $this->lang->line('treasury_ap_br_un_party_id');?><!--Party ID--></th>
        <th><?php echo $this->lang->line('treasury_ap_br_un_party_no');?><!--Party No--></th>
        <th><?php echo $this->lang->line('treasury_common_cheque_no');?><!--Cheque No--></th>
        <th><?php echo $this->lang->line('treasury_common_cheque_date');?><!--Cheque Date--></th>
        <th><?php echo $this->lang->line('common_narration');?><!--Narration--></th>

        <th><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
        <th><?php echo $this->lang->line('treasury_common_cleared_yn');?><!--Cleared YN--></th>
        <th><?php echo $this->lang->line('treasury_common_cleared_date');?> 
        <button id="popover-btn" type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" data-bs-title="Popover title" data-bs-content="And here's some amazing content. It's very engaging. Right?">
            <i class="fa fa-calendar chat-circle"></i>
        </button>
        <!--Cleared Date--></th>   
        <th><?php echo $this->lang->line('treasury_common_cleared_amount');?><!--Cleared Amount--></th>


        </thead>
        <tbody>
        <?php


        $clearedYN = array();
        $recieptTotal = 0;
        $numformat = 2;
        $recieptcheckedTotal = 0;

        if ($details) {

            foreach ($details as $value) {

                if ($value['transactionType'] == 1) {
                    $recieptTotal += $value['bankCurrencyAmount'];
                    $numformat = $value['bankCurrencyDecimalPlaces'];

                    if ($value['clearedYN'] == 1) {
                        array_push($clearedYN, $value['bankLedgerAutoID']);
                        $recieptcheckedTotal += $value['bankCurrencyAmount'];
                    }


                    ?>

                    <tr>
                        <td><?php echo format_date($value['documentDate'], $convertFormat) ?></td>
                        <td><?php echo $value['documentSystemCode'] ?></td>
                        <td><?php echo $value['partyCode'] ?></td>
                        <td><?php echo $value['partyName'] ?></td>
                        <td><?php echo $value['chequeNo'] ?></td>
                        <td><?php echo $value['chequeDate'] ?></td>
                        <td><?php echo $value['memo'] ?></td>
                        <td style="text-align: right"><?php echo number_format($value['bankCurrencyAmount'], $value['bankCurrencyDecimalPlaces']); ?></td>
                        <?php if ($value['clearedYN'] == 1) { ?>
                            <td style="text-align: center"><input <?php echo $btnDisable ?> type="checkbox"
                                                                                            onchange="clearreceipt(this,'<?php echo $value['bankCurrencyAmount'] ?>','<?php echo $value['bankCurrencyDecimalPlaces'] ?>','<?php echo $value['bankLedgerAutoID'] ?>')"
                                                                                            name="clearedYN[]" checked
                                                                                            id="clearedYN"
                                                                                            value="<?php echo $value['bankLedgerAutoID'] ?>">
                            </td>
                            <td>
                                <div class="input-group datepics">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="clearedDate" id="clearedDate_<?php echo $value['bankLedgerAutoID'] ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo format_date($value['clearedDate'], $convertFormat) ?>"/><a href="#" onclick="updateClearDatebyID(<?php echo $value['bankLedgerAutoID'] ?>)" class="update-date"><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span></a>
                                </div>
                            </td>
                            <td class="" style="text-align: right"><input readonly type="hidden"
                                                                          value="<?php echo $value['bankCurrencyAmount'] ?>"
                                                                          id="amount" class="amount" name="amount[]">
                                <div class="two"><?php echo number_format($value['bankCurrencyAmount'], $value['bankCurrencyDecimalPlaces']); ?></div>
                            </td>
                        <?php } else { ?>
                            <td style="text-align: center"><input <?php echo $btnDisable ?> type="checkbox"
                                                                                            onchange="clearreceipt(this,'<?php echo $value['bankCurrencyAmount'] ?>','<?php echo $value['bankCurrencyDecimalPlaces'] ?>','<?php echo $value['bankLedgerAutoID'] ?>')"
                                                                                            name="clearedYN[]"
                                                                                            id="clearedYN"
                                                                                            value="<?php echo $value['bankLedgerAutoID'] ?>">
                            </td>
                            <td>
                                <div class="input-group datepics">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="clearedDate" id="clearedDate_<?php echo $value['bankLedgerAutoID'] ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo format_date($value['clearedDate'], $convertFormat) ?>"/><a href="#" onclick="updateClearDatebyID(<?php echo $value['bankLedgerAutoID'] ?>)" class="update-date"><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span></a>
                                </div>
                            </td>
                            <td class="" style="text-align: right"><input readonly type="hidden" value="0" id="amount"
                                                                          class="amount" name="amount[]">
                                <div class="two"></div>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php

                }


            }
        }

        ?>

        </tbody>
        <tfooter>
            <tr>
                <td style="text-align: right" colspan="7"><?php echo $this->lang->line('common_total');?><!--Total--></td>
                <td style="text-align: right"><?php echo number_format($recieptTotal, $numformat) ?></td>
                <td></td>
                <td>
                    <div id="totalrecipet"
                         style="text-align: right"><?php echo number_format($recieptcheckedTotal, $numformat) ?></div>
                </td>
            <tr>

        </tfooter>

        <table>
            <div class="page-header">
                <p style=" font-size:16px;color:#3c8dbc;font-weight: bolder"><?php echo $this->lang->line('treasury_common_payments');?><!--Payments--> </p>
            </div>

            <table id="table2" class="<?php echo table_class() ?>">
                <thead>
                <th><?php echo $this->lang->line('common_document_date');?><!--Document Date--></th>
                <th><?php echo $this->lang->line('treasury_common_document_code');?><!--Document Code--></th>
                <th><?php echo $this->lang->line('treasury_ap_br_un_party_id');?><!--Party ID--></th>
                <th><?php echo $this->lang->line('treasury_ap_br_un_party_no');?><!--Party No--></th>
                <th><?php echo $this->lang->line('treasury_common_cheque_no');?><!--Cheque No--></th>
                <th><?php echo $this->lang->line('treasury_common_cheque_date');?><!--Cheque Date--></th>
                <th><?php echo $this->lang->line('common_narration');?><!--Narration--></th>
                <th><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                <th><?php echo $this->lang->line('treasury_common_cleared_yn');?><!--Cleared YN--></th>
                <th><?php echo "Cleared Date"; ?><!--CLRD--> </th>
                <th><?php echo $this->lang->line('treasury_common_cleared_amount');?><!--Cleared Amount--></th>


                </thead>
                <tbody>
                <?php
                $pvTotal = 0;
                $paymentcheckedTotal = 0;
                $num = 2;
                if ($details) {
                    foreach ($details as $val) {
                        if ($val['transactionType'] == 2) {
                            $pvTotal += $val['bankCurrencyAmount'];
                            $num = $val['bankCurrencyDecimalPlaces'];

                            if ($val['clearedYN'] == 1) {
                                array_push($clearedYN, $val['bankLedgerAutoID']);
                                $paymentcheckedTotal += $val['bankCurrencyAmount'];
                            }

                            ?>

                            <tr>
                                <td><?php echo format_date($val['documentDate'], $convertFormat) ?></td>
                                <td><?php echo $val['documentSystemCode'] ?></td>
                                <td><?php echo $val['partyCode'] ?></td>
                                <td><?php echo $val['partyName'] ?></td>
                                <td><?php echo $val['chequeNo'] ?></td>
                                <td><?php echo $val['chequeDate'] ?></td>
                                <td><?php echo $val['memo'] ?></td>
                                <td style="text-align: right"><?php echo number_format($val['bankCurrencyAmount'], $val['bankCurrencyDecimalPlaces']); ?></td>
                                <?php if ($val['clearedYN'] == 1) { ?>
                                    <td style="text-align: center"><input <?php echo $btnDisable ?> type="checkbox" onchange="clearpayment(this,'<?php echo $val['bankCurrencyAmount'] ?>','<?php echo $val['bankCurrencyDecimalPlaces'] ?>','<?php echo $val['bankLedgerAutoID'] ?>')" name="clearedYN[]" checked id="clearedYN" value="<?php echo $val['bankLedgerAutoID'] ?>"></td>
                                    <td>
                                        <div class="input-group datepics">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" name="clearedDate" id="clearedDate_<?php echo $value['bankLedgerAutoID'] ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo format_date($value['clearedDate'], $convertFormat) ?>"/><a href="#" onclick="updateClearDatebyID(<?php echo $value['bankLedgerAutoID'] ?>)" class="update-date"><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span></a>
                                        </div>
                                    </td>
                                    <td class="" style="text-align: right"><input readonly type="hidden" value="<?php echo $val['bankCurrencyAmount'] ?>" id="paymentamount" class="paymentamount" name="paymentamount[]">
                                        <div class="three"><?php echo number_format($val['bankCurrencyAmount'], $val['bankCurrencyDecimalPlaces']); ?></div>
                                    </td>
                                <?php } else {
                                    ?>
                                    <td style="text-align: center"><input <?php echo $btnDisable ?> type="checkbox" onchange="clearpayment(this,'<?php echo $val['bankCurrencyAmount'] ?>','<?php echo $val['bankCurrencyDecimalPlaces'] ?>','<?php echo $val['bankLedgerAutoID'] ?>')" name="clearedYN[]" id="clearedYN" value="<?php echo $val['bankLedgerAutoID'] ?>">
                                    </td>
                                    <td>
                                        <div class="input-group datepics">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" name="clearedDate" id="clearedDate_<?php echo $value['bankLedgerAutoID'] ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo format_date($value['clearedDate'], $convertFormat) ?>"/><a href="#" onclick="updateClearDatebyID(<?php echo $value['bankLedgerAutoID'] ?>)" class="update-date"><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span></a>
                                        </div>
                                    </td>
                                    <td class="" style="text-align: right"><input readonly type="hidden" value="0" id="paymentamount" class="paymentamount" name="paymentamount[]">
                                        <div class="three"></div>
                                    </td>
                                    <?php
                                } ?>
                            </tr>
                            <?php

                        }
                    }
                }
                ?>
                </tbody>
                <tfooter>
                    <tr>
                        <td style="text-align: right" colspan="7"><?php echo $this->lang->line('common_total');?><!--Total--></td>
                        <td style="text-align: right"><?php echo number_format($pvTotal, $num) ?></td>
                        <td></td>
                        <td>
                            <div id="totalpaymentamount"
                                 style="text-align: right"><?php echo number_format($paymentcheckedTotal, $num) ?></div>

                        </td>
                    <tr>

                </tfooter>
            </table>
            <input type="hidden" id="openingBalance"
                   value="<?php echo(!empty($openingbalance) ? $openingbalance : 0) ?>" name="openingBalance">
            <input type="hidden" id="clearedpayment" name="clearedpayment" value="<?php echo $paymentcheckedTotal ?>">
            <input type="hidden" id="clearedreceipt" name="clearedreceipt" value="<?php echo $recieptcheckedTotal ?>">

            <?php
            $paymentcheckedTotal = $num = -1 * abs($paymentcheckedTotal);
            $closingbalance = $openingbalance + $recieptcheckedTotal + $paymentcheckedTotal;
            ?>
            <input type="hidden" id="closinbalance" value="<?php echo $closingbalance ?>" name="closinbalance">

            <br>


            <div class="main-footer navbar-fixed-bottom "
                 style="padding: 0px; padding-bottom: 50px;text-align: right;padding-right: 25px"><p
                        style="font-size: 16px"><?php echo $this->lang->line('treasury_common_closing_balance');?><!--Closing Balance--> <span style="font-weight: bolder"
                                                                      id="closingbal"><?php echo number_format($closingbalance, 2) ?></span>
                </p></div>
</div>

<div class="pull-right" style="margin-top: -45px">
    <button type="button" id="prebtnheader" class="btn btn-default btn-md"
            onclick="fetchPage('system/bank_rec/erp_bank_reconciliation_bank_summary','<?php echo $this->input->post('GLAutoID') ?>',' Bank Reconciliation','Bank Reconciliation');">
        <?php echo $this->lang->line('common_previous');?> <!--Previous-->
    </button>
    <button type="button" id="prebtn" class="btn btn-default btn-md hide"
            onclick="fetchPage('system/bank_rec/erp_bank_reconciliation_new','<?php echo $this->input->post('GLAutoID') ?>|<?php echo $this->input->post('bankRecAutoID') ?>',' Bank Reconciliation','Bank Reconciliation');">
        <?php echo $this->lang->line('common_previous');?><!--Previous-->
    </button>
    <button id="btnnext" onclick="fetch_generate_details()" class="btn btn-primary btn-md" type="button">Save & Next
    </button>
    <button type="button" id="btnsummary" class="btn btn-primary btn-md hide"
            onclick="fetchPage('system/bank_rec/erp_bank_reconciliation_bank_summary','<?php echo $this->input->post('GLAutoID') ?>',' Bank Reconciliation','Bank Reconciliation');">
        <?php echo $this->lang->line('common_save_as_draft');?><!-- Save as Draft-->
    </button>
    <?php if ($confirmedYN != 1) { ?>
        <button id="submitWizard" class="btn btn-success hide submitWizard" onclick="confirmation()"> <?php echo $this->lang->line('common_confirmation');?><!--Confirmation-->
        </button>
    <?php } ?>
</div>

<br>
<br>


<script>

    $(".amount").on("keypress keyup blur", function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    Inputmask().mask(document.querySelectorAll("input"));

    $('.dateFields').datepicker({
        format: 'yyyy-mm-dd',
        endDate: '<?php echo $bankRecAsOf ?>',
    }).on('changeDate', function (ev) {
        $(this).datepicker('hide');
        $('#bankrecForm_row').bootstrapValidator('revalidateField', 'documentDate');
    });



    $('.date_md').datepicker({
        format: 'yyyy-mm-dd'
    }).on('changeDate', function (ev) {
        $(this).datepicker('hide');
    });

    $('.datepics').datetimepicker({
        format: date_format_policy,
    });

    var inputs = <?php echo json_encode($clearedYN); ?>;

    bankRecAutoID = <?php echo json_encode(trim($this->input->post('bankRecAutoID'))); ?>;
    GLAutoID =<?php echo json_encode(trim($this->input->post('GLAutoID'))); ?>;
    $('#table1').DataTable({
        "ordering": false
    });
    $('#table2').DataTable({
        "ordering": false
    });

    function insert_bank_rec_data(type) {
        /*    if(type=='RV'){
         documentDate=$('#documentDate').val();
         memo=$('#memo').val();
         amount=$('#amount').val();
         }
         if(type=='PV'){
         documentDate=$('#pdocumentDate').val();
         memo=$('#pmemo').val();
         amount=$('#pamount').val();
         }
         */
        $('#bankRec_row').modal('show');

    }

    $('#bankrecForm_row').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        //feedbackIcons   : { valid: 'glyphicon glyphicon-ok',invalid: 'glyphicon glyphicon-remove',validating: 'glyphicon glyphicon-refresh' },
        excluded: [':disabled'],
        fields: {
            documentDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_document_date_is_required');?>.'}}},/*Document date is required*/
            /*  month      : {validators : {notEmpty:{message:'Month is required.'}}},*/
            narration: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_narration_is_required');?>.'}}},/*Narration is required*/
            amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_amount_is_required');?>.'}}},/*Amount is required*/

        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'GLAutoID', 'value': GLAutoID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Bank_rec/save_bank_rec_add_row'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                refreshNotifications(true);
                if(data==true){
                    $('#bankRec_row').modal('hide');
                    setTimeout(function () {
                        fetchPage("system/bank_rec/erp_bank_reconciliation_new", GLAutoID + '|' + bankRecAutoID, "Bank Reconciliation  ", "Bank Reconciliation ", "BR");
                    }, 400);
                }



            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });


    function clearreceipt(thisTD, amount, decimal, bankLedgerAutoID) {
        var clearedreceipt = parseFloat($('#clearedreceipt').val());
        if (thisTD.checked) {
            inputs.push(bankLedgerAutoID);
            Totalclearedreceipt = clearedreceipt + parseFloat(amount);
            $('#clearedreceipt').val(Totalclearedreceipt);
            Totalclearedreceipt = parseFloat(Totalclearedreceipt).toFixed(decimal);
            $('#totalrecipet').html(commaSeparateNumber(Totalclearedreceipt));
            amountformat = parseFloat(amount).toFixed(decimal);
            $(thisTD).closest('td').next().find('.two').text(commaSeparateNumber(amountformat));
            $(thisTD).closest('td').next().find('.amount').val(amount);
        } else {
            inputs = jQuery.grep(inputs, function (value) {
                return value != bankLedgerAutoID;
            });

            Totalclearedreceipt = clearedreceipt - parseFloat(amount);
            $('#clearedreceipt').val(Totalclearedreceipt);
            Totalclearedreceipt = parseFloat(Totalclearedreceipt).toFixed(decimal);
            $('#totalrecipet').html(commaSeparateNumber(Totalclearedreceipt));
            $(thisTD).closest('td').next().find('.two').text('');
            $(thisTD).closest('td').next().find('.amount').val(0);
        }

        getclosingAmount(decimal);
    }

    function clearpayment(thisTD, amount, decimal, bankLedgerAutoID) {
        var clearedpayment = parseFloat($('#clearedpayment').val());
        if (thisTD.checked) {
            inputs.push(bankLedgerAutoID);
            Totalclearedpayment = clearedpayment + parseFloat(amount);
            $('#clearedpayment').val(Totalclearedpayment);
            Totalclearedpayment = parseFloat(Totalclearedpayment).toFixed(decimal);
            $('#totalpaymentamount').html(commaSeparateNumber(Totalclearedpayment));
            amountformat = parseFloat(amount).toFixed(decimal);
            $(thisTD).closest('td').next().find('.three').text(commaSeparateNumber(amountformat));
            $(thisTD).closest('td').next().find('.paymentamount').val(amount);
        } else {
            inputs = jQuery.grep(inputs, function (value) {
                return value != bankLedgerAutoID;
            });
            Totalclearedpayment = clearedpayment - parseFloat(amount);
            $('#clearedpayment').val(Totalclearedpayment);
            Totalclearedpayment = parseFloat(Totalclearedpayment).toFixed(decimal);
            $('#totalpaymentamount').html(commaSeparateNumber(Totalclearedpayment));
            $(thisTD).closest('td').next().find('.three').text('');
            $(thisTD).closest('td').next().find('.paymentamount').val(0);
        }

        getclosingAmount(decimal);
    }

    function getclosingAmount(decimal) {

        var finalopeningBalance = parseFloat($('#openingBalance').val());

        var finalclearedpayment = parseFloat($('#clearedpayment').val());
        var finalclearedreceipt = parseFloat($('#clearedreceipt').val());
        if (!isNaN(finalclearedpayment) && finalclearedpayment > 0) {
            finalclearedpayment = -Math.abs(finalclearedpayment);
        }
        var finalclosingamount = finalopeningBalance + finalclearedreceipt + finalclearedpayment;
        finalclosingamount = finalclosingamount.toFixed(decimal);
        $('#closingbal').html(commaSeparateNumber(finalclosingamount));

    }


    function fetch_generate_details() {
        var clearedYN = inputs;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'clearedYN': clearedYN, 'bankRecAutoID': bankRecAutoID},
            url: "<?php echo site_url('Bank_rec/save_bank_rec_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                // fetch_confirmation();
                myAlert('s', 'Successfully updated');
                fetch_bookbalance();
                stopLoad();
                /*  refreshNotifications(true);*/
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function updateClearedDate() {
        //e.preventDefault();
        var bankRecAutoID = <?php echo json_encode(trim($this->input->post('bankRecAutoID'))); ?>;
        var GLAutoID =<?php echo json_encode(trim($this->input->post('GLAutoID'))); ?>;
        var clearedDate = $("#clearedDate").val();
        var bankRecAsOf = <?php echo $bankRecAsOf ?>;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'clearedDate': clearedDate, 'bankRecAutoID': bankRecAutoID,'GLAutoID': GLAutoID},
            url: "<?php echo site_url('Bank_rec/update_cleared_date'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                // fetch_confirmation();
                myAlert('s', 'Successfully updated');
                fetchPage("system/bank_rec/erp_bank_reconciliation_bank_summary", GLAutoID, "Bank Reconciliation ", "Bank Reconciliation");
                stopLoad();
                
                /*  refreshNotifications(true);*/
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

    }

    function confirmation() {
        if (bankRecAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {bankRecAutoID: bankRecAutoID, GLAutoID: GLAutoID},
                        url: "<?php echo site_url('bank_rec/bank_rec_confirm'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);

                            fetchPage("system/bank_rec/erp_bank_reconciliation_bank_summary", GLAutoID, "Bank Reconciliation ", "Bank Reconciliation");
                            stopLoad();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
    function fetch_confirmation() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {bankRecAutoID: bankRecAutoID, html: 'html'},
            url: "<?php echo site_url('Bank_rec/bank_rec_confirmation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {


                refreshNotifications(true);
                $('#prebtn').removeClass('hide');
                $('#submitWizard').removeClass('hide');
                $('#prebtnheader').addClass('hide');
                $('#btnnext').addClass('hide');
                $('#btnsummary').removeClass('hide');


                $('#confrimDiv').html(data);
                stopLoad();

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function fetch_bookbalance() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {bankRecAutoID: bankRecAutoID, GLAutoID: GLAutoID, html: 'html'},
            url: "<?php echo site_url('Bank_rec/bank_rec_book_balance'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                $('#prebtn').removeClass('hide');
                $('#submitWizard').removeClass('hide');
                $('#prebtnheader').addClass('hide');
                $('#btnnext').addClass('hide');
                $('#btnsummary').removeClass('hide');


                $('#confrimDiv').html(data);
                stopLoad();

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function commaSeparateNumber(val) {
        while (/(\d+)(\d{3})/.test(val.toString())) {
            val = val.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
        }
        return val;
    }
</script>

<div class="modal fade" id="bankRec_row" role="dialog" aria-labelledby="myModalLabel" data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('treasury_ap_br_bank_reconcilation');?><!--Bank Reconciliation--><span id=""></span> </h4>
            </div>

            <form role="form" id="bankrecForm_row" name="bankrecForm_row" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('treasury_common_bank_name');?><!--Bank Name--> </label>
                            <div class="col-sm-6" id="recBankname">
                                <input type="text" readonly value="<?php echo $data['bankName']; ?>" id=""
                                       class="form-control" required>

                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_document_date');?><!--Document Date--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <div class="input-group datepics">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="documentDate"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value=""
                                           id="sk"
                                           class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_account');?><!--Account--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <?php echo form_dropdown('bankAccountID', company_PL_bank_account_drop(), '', 'class="form-control select2 id="bankAccountID" required'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_segment');?><!--Segment--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <?php echo form_dropdown('segmentID', fetch_segment(), '', 'class="form-control select2" id="segmentID" required'); ?>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_type');?><!--Type--></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <select id="type" name="type" class="form-control select2">
                                        <option value="1"><?php echo $this->lang->line('treasury_common_receipt');?><!--Receipt--></option>
                                        <option value="2"><?php echo $this->lang->line('treasury_common_payment');?><!--Payment--></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('treasury_common_reference_no');?><!--Reference No--></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" id="reference" rows="1" name="reference"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_narration');?><!--Narration--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" id="narration" rows="1" name="narration"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_amount');?><!--Amount--> <?php required_mark(); ?></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon"><?php echo $data['bankCurrencyCode']; ?></div>
                                    <input type="text" name="amount" style="text-align: right" class="form-control"
                                           id="amount">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSave_row"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="invoice_con_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title invoice_con_title">&nbsp;</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-yellow">
                                <h4 class="invoice_con_title">&nbsp;</h4>
                            </div>
                            <div class="box-footer no-padding">
                                <ul class="nav nav-stacked">
                                    <li><a><?php echo $this->lang->line('common_no_records_found');?><!--No Records found--></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>
                            <tr>
                                <th colspan="5"><?php echo $this->lang->line('common_item');?><!--Item--></th>
                                <th colspan="2"> <?php echo $this->lang->line('common_item');?><!--Item--> <span class="currency">(USD)</span></th>
                                <th colspan="4"><?php echo $this->lang->line('treasury_common_invoiced_item');?><!--Invoiced Item--> <span class="currency">(USD)</span></th>
                            </tr><tr>
                            </tr><tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_code');?><!--Code--></th>
                                <th class="text-left"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                                <th><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
                                <th><?php echo $this->lang->line('common_warehouse');?><!--Ware House--></th>
                                <th><?php echo $this->lang->line('common_qty');?><!--Qty--></th>
                                <th><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                                <th><?php echo $this->lang->line('common_qty');?><!--Qty--></th>
                                <th><?php echo $this->lang->line('common_price');?><!--Price--></th>
                                <th><?php echo $this->lang->line('common_tax');?><!--Tax--></th>
                                <th><?php echo $this->lang->line('common_total');?><!--Total--></th>
                                <th style="display: none;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="table_body">
                            <tr class="danger">
                                <td colspan="11" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td>
                            </tr>
                            </tbody>
                            <tfoot id="table_tfoot">

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="save_con_base_items()"><?php echo $this->lang->line('common_save_change');?><!--Save changes--></button>
            </div>
        </div>
    </div>
</div>


<script>
    $('.select2').select2();
</script>

<script>
function updateClearDatebyID(bankLedgerAutoID){
        var clearedDate = $("#clearedDate_" + bankLedgerAutoID).val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'clearedDate': clearedDate, 'bankLedgerAutoID': bankLedgerAutoID}, 
            url: "<?php echo site_url('Bank_rec/updateClearDatebyID'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                // fetch_confirmation();
                myAlert('s', 'Successfully updated');
                //fetchPage("system/bank_rec/erp_bank_reconciliation_bank_summary", GLAutoID, "Bank Reconciliation ", "Bank Reconciliation");
                stopLoad();
                
                /*  refreshNotifications(true);*/
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
}
</script>