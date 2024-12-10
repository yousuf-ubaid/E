<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('communityngo', $primaryLanguage);
$this->load->helper('community_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>

    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:40%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px"
                                     src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:60%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3>
                                    <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ')'; ?></strong>
                                </h3>
                                <p><?php echo $this->common_data['company_data']['company_address1'] . ' ' . $this->common_data['company_data']['company_address2'] . ' ' . $this->common_data['company_data']['company_city'] . ' ' . $this->common_data['company_data']['company_country']; ?></p>
                                <h4>
                                    <?php echo $this->lang->line('communityngo_return_items'); ?><!--Rental Items--> </h4>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>
                                    <?php echo $this->lang->line('communityngo_issueNo'); ?><!--Rental Items Request Number--></strong>
                            </td>
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['itemIssueCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_name'); ?><!--Name--></strong></td>
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['requestedMemberName']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>
                                    <?php echo $this->lang->line('communityngo_item_issued_date'); ?><!--Purchase Request Date--></strong>
                            </td>
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['issueDate']; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr>

    <div class="table-responsive">
        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="width:15%;"><strong>
                        <?php echo $this->lang->line('communityngo_item_expected_return_date'); ?><!--Expected Return Date--> </strong>
                </td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:33%;"><?php echo $extra['master']['expectedReturnDate']; ?></td>
                <td style="width:15%;"><strong>
                        <?php echo $this->lang->line('common_currency'); ?><!--Currency--> </strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:33%;"><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>

            </tr>
            <tr>
                <td style="width:15%;"><strong>
                        <?php echo $this->lang->line('communityngo_item_narration'); ?><!--Narration--> </strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:33%;"><?php echo $extra['master']['narration']; ?></td>
                <td style="width:15%;"><strong>
                        <?php echo $this->lang->line('common_segment'); ?><!--Segment--> </strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:33%;"><?php echo $extra['master']['segmentCode']; ?></td>
            </tr>
            </tbody>
        </table>
    </div><br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class='thead'>
            <tr>
                <th style="min-width: 50%" class='theadtr' colspan="7">
                    <?php echo $this->lang->line('communityngo_item_details'); ?><!--Item Details--></th>
                <th style="min-width: 50%" class='theadtr' colspan="4">
                    Cost <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></th>
            </tr>
            <tr>
                <th style="min-width: 4%" class='theadtr'>#</th>
                <th style="min-width: 10%" class='theadtr'>
                    <?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                <th style="min-width: 10%" class='theadtr'>
                    <?php echo $this->lang->line('communityngo_item_expected_return_date'); ?><!--Expected Return Date--></th>
                <th style="min-width: 5%" class='theadtr'>
                    <?php echo $this->lang->line('communityngo_item_no_of_days'); ?><!--No of Days--></th>
                <th style="min-width: 30%" class="text-left theadtr">
                    <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                <th style="min-width: 10%" class='theadtr'>
                    <?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                <th style="min-width: 10%" class='theadtr'>
                    <?php echo $this->lang->line('common_net_cost'); ?><!--Net Cost--></th>
                <th style="min-width: 15%" class='theadtr'>
                    <?php echo $this->lang->line('common_total'); ?><!--Total--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total = 0;
            $gran_total = 0;
            $tax_transaction_total = 0;
            $num = 1;
            if (!empty($extra['detail'])) {
                foreach ($extra['detail'] as $val) { ?>
                    <tr>
                        <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                        <td class="text-center"><?php echo $val['itemSystemCode']; ?></td>
                        <td class="text-center"><?php echo $val['expectedReturnDate']; ?></td>
                        <td class="text-center"><?php echo $val['no_of_days']; ?></td>
                        <td><?php echo $val['itemDescription'] . ' - ' . $val['comment']; ?></td>
                        <td class="text-center"><?php echo $val['unitOfMeasure']; ?></td>
                        <td class="text-right"><?php echo $val['requestedQty']; ?></td>
                        <td class="text-right"><?php echo number_format(($val['unitAmount']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"><?php echo number_format($val['unitAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"><?php echo number_format($val['totalAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php


                    $num++;
                    $total += $val['totalAmount'];
                    $gran_total += $val['totalAmount'];
                    $tax_transaction_total += $val['totalAmount'];
                }
            } else {
                $NoRecordsFound = $this->lang->line('common_no_records_found');
                echo '<tr class="danger"><td colspan="11" class="text-center">' . $NoRecordsFound . '<!--No Records Found--></td></tr>';
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td style="min-width: 85%  !important" class="text-right sub_total" colspan="9">
                    <?php echo $this->lang->line('common_total'); ?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                <td style="min-width: 15% !important"
                    class="text-right total"><?php echo number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
            </tfoot>
        </table>
    </div><br>
    <div class="table-responsive">
        <h5 class="text-right"> <?php echo $this->lang->line('common_total'); ?><!--Total-->
            (<?php echo $extra['master']['transactionCurrency']; ?> )
            : <?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5>
    </div>

    <hr>

    <div>
        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="width:15%;"><h4>
                        <?php echo $this->lang->line('communityngo_isreturn'); ?><!--Is Returned--> </h4>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="row">
                        <div class="form-group col-sm-3 col-xs-6">
                            <label for="isReturned">&nbsp;</label>
                            <div class="input-group">
                                <?php
                                if ($extra['master']['isReturned'] == '1') {
                                    $value = "1";
                                    $checked = 'checked';
                                } else {
                                    $value = '0';
                                    $checked = '';
                                }
                                ?>
                                <span class="input-group-addon">
                                                <input type="checkbox" name="isReturned" id="isReturned"
                                                       value="<?php echo $value; ?>" <?php echo $checked; ?> />
                                            </span>
                                <input type="text" class="form-control" disabled="" value="Yes">
                                <input type="hidden" class="form-control" id="Re_itemIssueAutoID"
                                       name="Re_itemIssueAutoID"
                                       value="<?php echo $extra['master']['itemIssueAutoID']; ?>">
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="row">
                        <div class="form-group col-sm-3 col-xs-6">
                            <label
                                for="ReturnedDate"><?php echo $this->lang->line('communityngo_returned_date'); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="ReturnedDate" onblur="removeBtnDisable()"
                                       value="<?php echo $extra['master']['ReturnedDate']; ?>"
                                       id="ReturnedDate" class="form-control"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

    </div>

    <script>

        $(document).ready(function () {

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });


            var isReturned = '<?php echo $extra['master']['isReturned']; ?>';
            var date = '<?php echo $extra['master']['ReturnedDate']; ?>';

            if (isReturned == null || isReturned == 0 || date == null || date == '') {
                $('#ReturnsubmitBtn').prop('disabled', true);
            } else {
                $('#ReturnsubmitBtn').prop('disabled', false);
            }

        });

        var isReturned = '<?php echo $extra['master']['isReturned']; ?>';
        var ReturnedDate = '<?php echo $extra['master']['ReturnedDate']; ?>';

        if (isReturned == 1 && (ReturnedDate != null || ReturnedDate != '0000-00-00')) {
            $('.ReturnsubmitBtn').addClass('hide');
        } else {
            $('.ReturnsubmitBtn').removeClass('hide');
        }

        $("#isReturned").click(function () {
            if (this.checked) {
                $('#isReturned').val('1');

                var isReturned = document.getElementById('isReturned').value;
                var date = document.getElementById('ReturnedDate').value;

                if (isReturned == 0 || date == null || date == '') {
                    $('#ReturnsubmitBtn').prop('disabled', true);
                } else {
                    $('#ReturnsubmitBtn').prop('disabled', false);
                }

            } else {
                $('#isReturned').val('0');
                $('#ReturnsubmitBtn').prop('disabled', true);

            }
        });


        function removeBtnDisable() {

            var isReturned = document.getElementById('isReturned').value;
            var date = document.getElementById('ReturnedDate').value;

            if (isReturned == null || isReturned == 0 || date == null || date == '') {
                $('#ReturnsubmitBtn').prop('disabled', true);
            } else {
                $('#ReturnsubmitBtn').prop('disabled', false);
            }

        }
    </script>


<?php

/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 5/28/2018
 * Time: 9:57 AM
 */