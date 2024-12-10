<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
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
                            <h4><?php echo $this->lang->line('assetmanagement_asset_request_note');?><!--Asset Request Note--> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('assetmanagement_requested_by_emp_name');?><!--Request By Emp Name--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['requestedByName']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('assetmanagement_requested_emp_code');?><!--Request Employee code--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['ECode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('assetmanagement_requested_asset_number');?><!--Request Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentID']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('assetmanagement_requested_asset_date');?><!--Request Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_comments');?><!--Comments--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['comments']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th style="min-width: 4%" class='theadtr'>#</th>
            <th style="width: auto"><?php echo $this->lang->line('common_description');?><!--Description--><?php required_mark(); ?></th>
            <th style="width: auto"><?php echo $this->lang->line('common_project');?><!--Project--></th>
            <th style="width: auto"><?php echo $this->lang->line('common_requested_qty');?><!--Requested QTY--><?php required_mark(); ?></th>
            <th style="width: auto"><?php echo $this->lang->line('common_comments');?><!--Comments--></th>
        </tr>
        </thead>
        <tbody>
        <?php
$num = 1;
if (!empty($extra['detail'])) {
    foreach ($extra['detail'] as $val) {
?>
        <tr>
            <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
            <td class="text-center"><?php echo $val['itemDescription']; ?></td>
            <td class="text-center"><?php echo $val['contractCode']; ?></td>
            <td class="text-center"><?php echo $val['requestedQTY']; ?></td>
            <td class="text-center"><?php echo $val['comments']; ?></td>
      </tr>
<?php
        $num++;
    }
    ?>
    
<?php
} else {
    $NoRecordsFound = $this->lang->line('common_no_records_found');
    echo '<tr class="danger"><td colspan="6" class="text-center">' . $NoRecordsFound . '<!--No Records Found--></td></tr>';
}
?>

        </tbody>
    </table>
</div>

<?php if ($extra['master']['approvedYN']) { ?>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:28%;"><strong><?php echo $this->lang->line('hrms_expanse_claim_electronically_approved_by');?><!--Electronically Approved By--> </strong></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedByEmpName']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('hrms_expanse_claim_electronically_approved_date');?><!--Electronically Approved Date--> </strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
<?php } ?>
<script>



    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('AssetManagement/load_asset_request_confirmation'); ?>/<?php echo $extra['master']['masterID'] ?>";
    $("#a_link").attr("href", a_link);
</script>











