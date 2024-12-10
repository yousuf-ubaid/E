<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ')'; ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('fleet_asset_utilization');?><!--Inspection--> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('fleet_document_Code');?><!-- Document Code --></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['doc_number']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('fleet_document_Date');?><!--Document Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['date']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Job Number</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['job_num']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Description</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['description']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Rig Name</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['rig_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong> Well Name</strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['well_name']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<br>
<br>
<br>

<?php if (!empty($extra['detail'])): ?>
    <?php 
    // Separate assets and components
    $hasAssets = false;
    $hasComponents = false;

    foreach ($extra['detail'] as $val) {
        if ($val['asset_type'] == 1) {
            $hasAssets = true;
        }
        if ($val['asset_type'] == 2) {
            $hasComponents = true;
        }
    }
    ?>

    <?php if ($hasAssets): ?>
    <div class="table-responsive">
        <h6> <strong>Assets </strong></h6>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th style="min-width: 5%">Serial Number</th>
                    <th style="min-width: 15%">Description</th>
                    <th style="min-width: 8%">Thread Condition</th>
                    <th style="min-width: 8%">Physical Condition</th>
                    <th style="min-width: 8%">Status</th>
                    <th style="min-width: 20%">Date From</th>
                    <th style="min-width: 20%">Date To</th>
                    <th style="min-width: 5%">Total Hours</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($extra['detail'] as $val) {
                    if ($val['asset_type'] == 1) { ?>
                        <tr>
                            <td class="text-center"><?php echo $val['serial_number']; ?></td>
                            <td class="text-center"><?php echo $val['description']; ?></td>
                            <td class="text-center"><?php echo $val['tread_status_description']; ?></td>
                            <td class="text-center"><?php echo $val['physical_status_description']; ?></td>
                            <td class="text-center"><?php echo $val['general_status_description']; ?></td>
                            <td class="text-center"><?php echo $val['date_time_from']; ?></td>
                            <td class="text-center"><?php echo $val['date_time_to']; ?></td>
                            <td class="text-center"><?php echo $val['hours']; ?></td>
                        </tr>
                    <?php }
                }
                ?>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>
    <?php endif; ?>

    <?php if ($hasComponents): ?>
    <br>
    <br>
    <br>
    <div class="table-responsive">
        <h6> <strong>Components </strong></h6>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th style="min-width: 5%">Serial Number</th>
                    <th style="min-width: 15%">Description</th>
                    <th style="min-width: 8%">Thread Condition</th>
                    <th style="min-width: 8%">Physical Condition</th>
                    <th style="min-width: 8%">Status</th>
                    <th style="min-width: 20%">Date From</th>
                    <th style="min-width: 20%">Date To</th>
                    <th style="min-width: 5%">Total Hours</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($extra['detail'] as $val) {
                    if ($val['asset_type'] == 2) { ?>
                        <tr>
                            <td class="text-center"><?php echo $val['serial_number']; ?></td>
                            <td class="text-center"><?php echo $val['description']; ?></td>
                            <td class="text-center"><?php echo $val['tread_status_description']; ?></td>
                            <td class="text-center"><?php echo $val['physical_status_description']; ?></td>
                            <td class="text-center"><?php echo $val['general_status_description']; ?></td>
                            <td class="text-center"><?php echo $val['date_time_from']; ?></td>
                            <td class="text-center"><?php echo $val['date_time_to']; ?></td>
                            <td class="text-center"><?php echo $val['hours']; ?></td>
                        </tr>
                    <?php }
                }
                ?>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>
    <?php endif; ?>

<?php else: ?>
    <p><?php echo $this->lang->line('common_no_records_found'); ?></p>
<?php endif; ?>

<script>
    $('.review').removeClass('hide');
    var a_link = "<?php echo site_url('Fleet/load_fleet_inspection_comfirmation'); ?>/<?php echo $extra['master']['id'] ?>";
    var de_link = "<?php echo site_url('Fleet/fetch_double_inspection'); ?>/<?php echo $extra['master']['id'] ?>";
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);
</script>
