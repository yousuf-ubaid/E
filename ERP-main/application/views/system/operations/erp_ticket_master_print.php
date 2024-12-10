<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_expanse_claim', $primaryLanguage);
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(false, true, $approval);

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
                                <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'] . ' ' . $this->common_data['company_data']['company_address2'] . ' ' . $this->common_data['company_data']['company_city'] . ' ' . $this->common_data['company_data']['company_country']; ?></p>
                            <h4>Job Ticket </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Job No</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['ticketNo']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Job Created Date</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['createdDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Description</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['comments']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Location</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['fieldName']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Well No</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['wellNo']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<br>
<h5>Product Details </h5>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'>Date</th>
            <th style="min-width: 30%" class="text-left theadtr">Client Ref</th>
            <th style="min-width: 5%" class='theadtr'>Description</th>
            <th style="min-width: 5%" class='theadtr'>Comments</th>
            <th style="min-width: 5%" class='theadtr'>Unit</th>
            <th style="min-width: 10%" class='theadtr'>Unit Rate</th>
            <th style="min-width: 10%" class='theadtr'>%per</th>
            <th style="min-width: 10%" class='theadtr'>Qty</th>
            <th style="min-width: 10%" class='theadtr'>Discount</th>
            <th style="min-width: 10%" class='theadtr'>Amount</th>

        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($extra['product'])) {
            foreach ($extra['product'] as $value) {
                    ?>
                    <tr>
                        <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                        <td class="text-center"><?php echo $value['addedDat']; ?></td>
                        <td class="text-center"><?php echo $value['clientReference']; ?></td>
                        <td class="text-left"><?php echo $value['Description']; ?></td>
                        <td class="text-center"><?php echo $value['comments']; ?></td>
                        <td class="text-center"><?php echo $value['UnitShortCode']; ?></td>
                        <td class="text-right"><?php echo $value['UnitRate']; ?></td>
                        <td class="text-center"><?php echo $value['percentage']; ?></td>
                        <td class="text-center"><?php echo $value['Qty']; ?></td>
                        <td class="text-center"><?php echo $value['discount']; ?></td>
                        <td class="text-right"><?php echo $value['TotalCharges']; ?></td>
                    </tr>
                <?php
                $num++;
            }
        } else {
            $NoRecordsFound =   $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="11" class="text-center">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
    </table>
</div>
<br>
<h5>Service Details </h5>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'>Date</th>
            <th style="min-width: 30%" class="text-left theadtr">Client Ref</th>
            <th style="min-width: 5%" class='theadtr'>Description</th>
            <th style="min-width: 5%" class='theadtr'>Comments</th>
            <th style="min-width: 5%" class='theadtr'>Unit</th>
            <th style="min-width: 10%" class='theadtr'>Unit Rate</th>
            <th style="min-width: 10%" class='theadtr'>%per</th>
            <th style="min-width: 10%" class='theadtr'>Qty</th>
            <th style="min-width: 10%" class='theadtr'>Discount</th>
            <th style="min-width: 10%" class='theadtr'>Amount</th>

        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($extra['service'])) {
            foreach ($extra['service'] as $values) {

                ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-center"><?php echo $values['addedDat']; ?></td>
                    <td class="text-center"><?php echo $values['clientReference']; ?></td>
                    <td class="text-left"><?php echo $values['Description']; ?></td>
                    <td class="text-center"><?php echo $values['comments']; ?></td>
                    <td class="text-center"><?php echo $values['UnitShortCode']; ?></td>
                    <td class="text-right"><?php echo $values['UnitRate']; ?></td>
                    <td class="text-center"><?php echo $values['percentage']; ?></td>
                    <td class="text-center"><?php echo $values['Qty']; ?></td>
                    <td class="text-center"><?php echo $values['discount']; ?></td>
                    <td class="text-right"><?php echo $values['TotalCharges']; ?></td>
                </tr>
                <?php
                $num++;
            }
        } else {
            $NoRecordsFound =   $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="11" class="text-center">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
    </table>
</div>


<br>
<table>
    <tbody>
    <tr>
        <td>
            <h5>Crew  </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class='thead'>
                    <tr>
                        <th style="min-width: 4%" class='theadtr'>#</th>
                        <th style="min-width: 10%" class='theadtr'>Employee Name</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $num = 1;
                    if (!empty($extra['crew'])) {
                        foreach ($extra['crew'] as $valuec) {

                            ?>
                            <tr>
                                <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                                <td class="text-center"><?php echo $valuec['crewname']; ?></td>
                            </tr>
                            <?php
                            $num++;
                        }
                    } else {
                        $NoRecordsFound =   $this->lang->line('common_no_records_found');
                        echo '<tr class="danger"><td colspan="2" class="text-center">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
                    } ?>
                    </tbody>
                </table>
            </div>
        </td>

        <td>
            <h5>Asset</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class='thead'>
                    <tr>
                        <th style="min-width: 4%" class='theadtr'>#</th>
                        <th style="min-width: 10%" class='theadtr'>Asset Unit</th>
                        <th style="min-width: 10%" class='theadtr'>Comment</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $num = 1;
                    if (!empty($extra['asset'])) {
                        foreach ($extra['asset'] as $valuea) {

                            ?>
                            <tr>
                                <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                                <td class="text-center"><?php echo $valuea['assetDescription']; ?></td>
                                <td class="text-center"><?php echo $valuea['Comment']; ?></td>
                            </tr>
                            <?php
                            $num++;
                        }
                    } else {
                        $NoRecordsFound =   $this->lang->line('common_no_records_found');
                        echo '<tr class="danger"><td colspan="3" class="text-center">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
                    } ?>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
</table>







<br>
<?php if ($extra['master']['approvedYN']==1) { ?>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:28%;"><strong>Approved By </strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td><strong> Approved Date </strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
<?php } ?>
<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Operation/load_ticket_master_view'); ?>/<?php echo $extra['master']['ticketidAtuto'] ?>";
    $("#a_link").attr("href", a_link);
</script>



