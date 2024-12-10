<div class="row" style="margin-top: 10px">
    <div class="col-md-12" style="display: none">
        <?php $companyData = $this->common_data['company_data']; ?>
        <div class=""> <?php echo $companyData['company_name']; ?> </div>
        <div class=""> <?php echo $companyData['company_address1'];?> </div>
        <div class=""> <?php echo $companyData['company_address2']; ?> </div>
        <div class=""> <?php echo $companyData['company_city']; ?> </div>
    </div>
</div>
<style>
    #rpt-tbl tr:hover{
        background: #B0BED9;
    }
</style>

<div class="row" style="margin-bottom: 10px; padding-right: 15px;">
    <?php echo form_open('', 'id="excel_form" class="form-inline"'); ?>
        <input type="hidden" name="from_date" value="<?=$this->input->post('from_date')?>"/>
        <input type="hidden" name="to_date" value="<?=$this->input->post('to_date')?>"/>
        <input type="hidden" name="processDate" value="<?=$this->input->post('processDate')?>"/>
        <button class="btn btn-xs btn-excel pull-right" onclick="excel_download()">
            <i class="fa fa-file-excel-o"></i> &nbsp; Excel
        </button>
    <?php echo form_close(); ?>
</div>

<div class="fixHeader_Div" style="height: 450px">
<table class="<?=table_class()?>" id="rpt-tbl">
    <thead>
    <tr>
        <th>#</th>
        <th>Name of Employee with Initials</th>
        <th>Designation</th>
        <th><abbr title="Employment From Date">From Date</abbr></th>
        <th><abbr title="Employment To Date">To Date</abbr></th>
        <th>Cash Payment</th>
        <th>Non-Cash Benefits</th>
        <th>Total Remuneration</th>
        <th>Total Tax Exempt / Excluded Income</th>
        <th><abbr title="Tax deducted under Primary Employment">Primary Employment</abbr></th>
        <th><abbr title="Tax deducted under Secondary Employment">Secondary Employment</abbr></th>
        <th>Total Tax Deducted</th>
        <th>Employee NIC</th>
        <th>Passport No.</th>
        <th>TIN</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $processDate = $this->input->post('processDate');
    $tot = 0;
    foreach($payeeData as $key=>$row){
        $tot += round($row['payee'], 2);
        ?>
            <tr>
                <td ><?=($key+1)?></td>
                <td ><?php echo $row['fullName'] ?></td>
                <td > <?php echo $row['designation_str'] ?></td>
                <td><div style="width: 60px"><?=$fromDate;?></div></td>
                <td><div style="width: 60px"><?=$toDate;?></div></td>
                <td style="text-align: right"><?php echo number_format($row['cashBenefit'], 2) ?></td>
                <td style="text-align: right"><?php echo number_format(0, 2) ?></td>
                <td style="text-align: right"><?php echo number_format($row['cashBenefit'], 2) ?></td>
                <td style="text-align: right"><?php echo number_format(0, 2) ?></td>
                <td style="text-align: right"><?=($row['payee_emp_type'] == 1)? number_format($row['payee'], 2): number_format(0, 2); ?></td>
                <td style="text-align: right"><?=($row['payee_emp_type'] != 1)? number_format($row['payee'], 2): number_format(0, 2); ?></td>
                <td style="text-align: right"><?= number_format($row['payee'], 2) ?></td>
                <td > <?php echo $row['NIC'] ?></td>
                <td > <?php echo $row['passportNo'] ?></td>
                <td > <?=$row['ssoNo'] ?></td>
            </tr>
        <?php
    }
    ?>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="11" class="reporttotalblack">&nbsp;</td>
            <td class="reporttotalblack"><?=number_format($tot, 2)?></td>
            <td colspan="3" class="reporttotalblack">&nbsp;</td>
        </tr>
    </tfoot>
</table>
</div>

<script>
    $('#rpt-tbl').tableHeadFixer({
        head: true,
        foot: true
    });

    function excel_download(){
        let form= document.getElementById('excel_form');
        form.target='_blank';
        form.method='post';
        form.action='<?php echo site_url('Report/income_tax_deduction/excel'); ?>';
        form.submit();
    }
</script>