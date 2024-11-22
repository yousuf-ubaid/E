<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>

                <td style="width:50%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').'; ?></strong></h3>
                                <h4> Bank Reconsiliation</h4>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Month</strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $master['month']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Year</strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $master['year']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>As of</strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $master['bankRecAsOf']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Description</strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $master['description']; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<hr>

<div id="confrimDiv">
    <div class="page-header">
        <h4 style="color:#3c8dbc;font-weight: bolder">Receipt</h4>
    </div>
    <div class="table-responsive">
    <table id="table1" class="table table-bordered table-striped>">
        <thead>
        <th class="theadtr">Document Date</th>
        <th class="theadtr">Document Code</th>
        <th class="theadtr">Party ID</th>
        <th class="theadtr">Party No</th>
        <th class="theadtr">Cheque No</th>
        <th class="theadtr">Cheque Date</th>
        <th class="theadtr">Cleared Amount</th>


        </thead>
        <tbody>
        <?php
        $recieptTotal=0;
        $numformat=2;
        if ($details) {

            foreach ($details as $value) {

                if ($value['transactionType'] == 1) {
                    $recieptTotal +=$value['bankCurrencyAmount'];
                    $numformat=$value['bankCurrencyDecimalPlaces'];



                    ?>

                    <tr>
                        <td><?php echo format_date($value['documentDate']) ?></td>
                        <td><?php echo $value['documentSystemCode'] ?></td>
                        <td><?php echo $value['partyCode'] ?></td>
                        <td><?php echo $value['partyName'] ?></td>
                        <td><?php echo $value['chequeNo'] ?></td>
                        <td><?php echo $value['chequeDate'] ?></td>
                        <td style="text-align: right"><?php echo number_format($value['bankCurrencyAmount'],$value['bankCurrencyDecimalPlaces']); ?></td>

                    </tr>
                    <?php

                }


            }
        }

        ?>

        </tbody>
        <tfoot>
            <tr>
                <td class="sub_total" style="text-align: right;" colspan="6">Total</td>
                <td class="total" style="text-align: right"><?php echo number_format($recieptTotal,$numformat) ?></td>
             <tr>
        </tfoot>


        <table>
            <div class="page-header">
                <h4 style="color:#3c8dbc;font-weight: bolder">Payment </h4>
            </div>

            <table id="table2" class="<?php echo table_class() ?>">
                <thead>
                <th class="theadtr">Document Date</th>
                <th class="theadtr">Document Code</th>
                <th class="theadtr">Party ID</th>
                <th class="theadtr">Party No</th>
                <th class="theadtr">Cheque No</th>
                <th class="theadtr">Cheque Date</th>


                <th class="theadtr">Cleared Amount</th>


                </thead>
                <tbody>
                <?php
                $pvTotal=0;
                $num=2;
                if ($details) {
                    foreach ($details as $val) {
                        if ($val['transactionType'] == 2) {
                            $pvTotal +=$val['bankCurrencyAmount'];
                            $num=$val['bankCurrencyDecimalPlaces'];

                            ?>

                            <tr>
                                <td><?php echo format_date($val['documentDate']) ?></td>
                                <td><?php echo $val['documentSystemCode'] ?></td>
                                <td><?php echo $val['partyCode'] ?></td>
                                <td><?php echo $val['partyName'] ?></td>
                                <td><?php echo $val['chequeNo'] ?></td>
                                <td><?php echo $val['chequeDate'] ?></td>
                                <td style="text-align: right"><?php echo number_format($val['bankCurrencyAmount'],$val['bankCurrencyDecimalPlaces']); ?></td>


                            </tr>
                            <?php

                        }
                    }
                }
                ?>
                </tbody>
                <tfooter>
                    <tr>
                        <td class="sub_total" style="text-align: right" colspan="6">Total</td>
                        <td class="total" style="text-align: right"><?php echo number_format($pvTotal,$num) ?></td>

                </tfooter>
                <table>
                    </div>
</div>
<br>


<?php exit; ?>

<?php if($extra['master']['approvedYN']){ ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:30%;"><b>Electronically Approved By </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b>Electronically Approved Date </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
        </tbody>
    </table>
</div>
<?php } ?>