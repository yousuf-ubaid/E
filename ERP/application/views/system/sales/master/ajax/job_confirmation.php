<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$secondarycode =  getPolicyValues('SSC', 'All');


?>

<input type="hidden" name="job_id" id="job_id" value="<?php echo $job_id ?>">
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?>.</strong></h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            <br>
                            <h4><?php echo $this->lang->line('sales_marketing_job_invoice');?></h4><!--Sales Invoice -->
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('sales_marketing_job_number');?></strong></td><!--Invoice Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $master['job_code'] ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('sales_marketing_job_date');?></strong></td><!--Document Date-->
                        <td><strong>:</strong></td>
                        <td><?php echo $master['job_date_from'].' - '.$master['job_date_to'] ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('sales_marketing_job_refrence');?></strong></td><!--Reference Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $master['job_reference']  ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('sales_marketing_job_po_number');?></strong></td><!--Reference Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $master['po_number']  ?></td>
                    </tr>
                
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<br><br>

<div class="table-responsive">
    <table class="table" style="width: 100%" id="item_confirm_tbl">
        <thead>
            <th>Item</th>
            <th>Item Description</th>
            <th>Amount</th>
            <th>Quantity</th>
            <th>Discount</th>
            <th>Net Total</th>
        </thead>
        <tbody>
            <?php foreach($item as $val) { ?>
                <tr>
                    <td><?php echo $val['code'] ?></td>
                    <td><?php echo $val['itemDescription'] ?></td>
                    <td><?php echo $val['transactionCurrency'].' '.number_format($val['value'],2); ?></td>
                    <td><?php echo $val['qty'] ?></td>
                    <td><?php echo $val['transactionCurrency'].' '.number_format($val['discount'],2) ?></td>
                    <td><?php echo $val['transactionCurrency'].' '.number_format($val['transactionAmount'],2) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="table-responsive">
    <table class="table" style="width: 100%" id="crew_confirm_tbl">
        <thead>
            <th style="min-width: 43%"><?php echo 'Name'; ?></th>
            <!--Details-->
            <th style="min-width: 15%"><?php echo 'Designation'; ?></th>
            <!--Total Value-->
            <th style="min-width: 5%"><?php echo 'Date From' ?></th>
            <!--Total Value-->
            <th style="min-width: 5%"><?php echo 'Date To' ?></th>
        </thead>
        <tbody>
            <?php foreach($crew as $val) { ?>
                <tr>
                    <td><?php echo $val['name'] ?></td>
                    <td><?php echo $val['designation'] ?></td>
                    <td><?php echo $val['dateFrom'] ?></td>
                    <td><?php echo $val['dateTo'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="pull-right" style="margin-top:25px;">  
    <button class="btn btn-primary-new size-lg" onclick="confirm_job()"><i class="fa fa-check"></i> Confirm</button>
</div>



<script>
    $('#item_confirm_tbl').DataTable();
    $('#crew_confirm_tbl').DataTable();

    function confirm_job(){

        var job_id = $('#job_id').val();
        var checklist = <?php echo json_encode($checklist_status); ?>;
        if (checklist == null) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "Please be aware that there are unclosed checklists. Are you sure you want to proceed with closing this job? ",/*You want to delete this customer!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'job_id': job_id},
                url: "<?php echo site_url('Jobs/job_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    

                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
    } else {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: " Are you sure you want to close the job",/*You want to delete this customer!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'job_id': job_id},
                url: "<?php echo site_url('Jobs/job_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    

                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
    }

}
</script>
