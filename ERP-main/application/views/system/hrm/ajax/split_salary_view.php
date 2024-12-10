<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_payroll', $primaryLanguage);
?>
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
                            <h3><strong><?php echo $this->common_data['company_data']['company_name']?></strong></h3>
                            <h4><?php echo $this->lang->line('hrms_payroll_split_salary')?><!--Split Salary--> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_document_code')?><!--Document Code--> </strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['splitSalaryCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_created_date')?><!--Created Date--> </strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_currency')?><!--Currency--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['CurrencyCode']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%;font-size:12px;">
        <tbody>
        <tr>
            <td style="width:20%;"><strong><?php echo $this->lang->line('common_document_date')?><!--Document Date--> </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:78%;"><?php echo 'FROM' . $extra['master']['startDate'].' TO '.$extra['master']['endDate']; ?></td>
        </tr>
        <tr>
            <td style="width:15%;"><strong><?php echo $this->lang->line('hrms_payroll_no_of_months')?><!--No Of Months--> </strong></td>
            <td><strong>:</strong></td>
            <td style="width:85%;"><?php echo $extra['master']['noOfMonths']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('common_description')?><!--Description--> </strong></td>
            <td><strong>:</strong></td>
            <td colspan="4"><?php echo $extra['master']['description']; ?></td>
        </tr>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <br>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo $this->lang->line('common_employee');?><!--Employee--></th>
            <th><?php echo $this->lang->line('common_start_date');?><!--Start Date--></th>
            <th><?php echo $this->lang->line('common_end_date');?><!--End Date--></th>
            <th><?php echo $this->lang->line('common_account_no');?><!--Account No--></th>
            <th><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <th><?php echo $this->lang->line('hrms_payroll_gross_salary');?><!--Gross Salary--></th>
            <th><?php echo $this->lang->line('common__monthly_deduction');?><!--Monthly Deduction--></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num =1;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;"><?php echo $val['Ename2']; ?></td>
                    <td><?php echo $val['startFrom']; ?></td>
                    <td style="text-align:center;"><?php echo $val['endDate']; ?></td>
                    <td style="text-align:center;"><?php echo $val['bank']; ?></td>
                    <td style="text-align:center;"><?php echo $val['CurrencyCode']; ?></td>
                    <td style="text-align:right;"><?php echo $val['grossSalary']; ?></td>
                    <td style="text-align:right;"><?php echo $val['monthlyDeduction']; ?></td>
                </tr>
                <?php
                $num ++;
            }
        }else{
            $norecfound=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="8" class="text-center">'.$this->lang->line('common_no_records_found').'</td></tr>';
        } ?>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <hr>
    <table style="width: 100%">
        <tbody>
        <?php if($extra['master']['confirmedYN']==1){ ?>
            <tr>
                <td><b><?php echo $this->lang->line('common_confirmed_by')?><!--Confirmed By--> </b></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['confirmedYNn'];?></td>
            </tr>
        <?php } ?>
        <?php if($extra['master']['approvedYN']){ ?>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('transaction_common_electronically_approved_by')?><!--Electronically Approved By--> </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('transaction_common_electronically_approved_date')?><!--Electronically Approved Date --></b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<br>
<br>
<br>
<br>
<?php if($extra['master']['approvedYN']){ ?>
    <?php
    if ($signature) { ?>
        <?php
        if ($signature['approvalSignatureLevel'] <= 2) {
            $width = "width: 50%";
        } else {
            $width = "width: 100%";
        }
        ?>
        <div class="table-responsive">
            <table style="<?php echo $width ?>">
                <tbody>
                <tr>
                    <?php
                    for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) {

                        ?>

                        <td>
                            <span>____________________________</span><br><br><span><b>&nbsp; Authorized Signature</b></span>
                        </td>

                        <?php
                    }
                    ?>
                </tr>


                </tbody>
            </table>
        </div>
    <?php } ?>
<?php } ?>
<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Employee/load_splitSalary_conformation'); ?>/<?php echo $extra['master']['splitSalaryMasterID'] ?>";
    $("#a_link").attr("href",a_link);
</script>
