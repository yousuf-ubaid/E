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
                            <h4>Contract </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Contract No</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['ContractNumber']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Department</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['Department']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Contact Type</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['conType']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Contract Start Date</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['ContStartDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Contract End Date</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['ContEndDate']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<br>
<h5>Pricing Details </h5>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'>Client Ref</th>
            <th style="min-width: 30%" class="text-left theadtr">Company Ref</th>
            <th style="min-width: 5%" class='theadtr'>Client Item Description</th>
            <th style="min-width: 5%" class='theadtr'>Type</th>
            <th style="min-width: 5%" class='theadtr'>Unit</th>
            <th style="min-width: 10%" class='theadtr'>Currency</th>
            <th style="min-width: 10%" class='theadtr'>Product Rate</th>

        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $value) {
                    $Typedesc='Product';
                    if($value['TypeID']==2){
                        $Typedesc='Service';
                    }
                $decimal=fetch_currency_desimal($value['CurrencyCode'])
                    ?>
                    <tr>
                        <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                        <td class="text-center"><?php echo $value['ClientRef']; ?></td>
                        <td class="text-center"><?php echo $value['OurRef']; ?></td>
                        <td class="text-left"><?php echo $value['ItemDescrip']; ?></td>
                        <td class="text-center"><?php echo $Typedesc; ?></td>
                        <td class="text-center"><?php echo $value['UnitDes']; ?></td>
                        <td class="text-center"><?php echo $value['CurrencyCode']; ?></td>
                        <td class="text-right"><?php echo number_format($value['standardRate'], $decimal); ?></td>
                    </tr>
                <?php
                $num++;
            }
        } else {
            $NoRecordsFound =   $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="8" class="text-center">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
    </table>
</div>
<br>
<h5>Call Off </h5>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="min-width: 10%" class='theadtr'>Description</th>
            <th style="min-width: 20%" class="text-left theadtr">Created Date</th>
            <th style="min-width: 10%" class='theadtr'>Expiry Date</th>
            <th style="min-width: 10%" class='theadtr'>Location</th>
            <th style="min-width: 10%" class='theadtr'>RDX</th>
            <th style="min-width: 10%" class='theadtr'>Length</th>
            <th style="min-width: 10%" class='theadtr'>Joints</th>
            <th style="min-width: 10%" class='theadtr'>Well No</th>
            <th style="min-width: 10%" class='theadtr'>Drawing No</th>
            <th style="min-width: 10%" class='theadtr'>Completion</th>

        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($extra['calloff'])) {
            foreach ($extra['calloff'] as $calof) {

                ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-left"><?php echo $calof['description']; ?></td>
                    <td class="text-center"><?php echo $calof['createdDate']; ?></td>
                    <td class="text-center"><?php echo $calof['expiryDate']; ?></td>
                    <td class="text-center"><?php echo $calof['fieldName']; ?></td>
                    <td class="text-center"><?php echo $calof['RDX']; ?></td>
                    <td class="text-center"><?php echo $calof['length']; ?></td>
                    <td class="text-center"><?php echo $calof['joints']; ?></td>
                    <td class="text-center"><?php echo $calof['WellNo']; ?></td>
                    <td class="text-center"><?php echo $calof['drawingNo']; ?></td>
                    <td class="text-center"><?php echo $calof['percentage']; ?>%</td>

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
    a_link = "<?php echo site_url('Operation/load_contract_master_view'); ?>/<?php echo $extra['master']['contractUID'] ?>";
    $("#a_link").attr("href", a_link);
</script>



