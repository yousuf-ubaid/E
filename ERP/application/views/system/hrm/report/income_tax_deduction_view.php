<?php
$processDate = $this->input->post('processDate');
$companyData = $this->common_data['company_data'];


foreach($payeeData as $key=>$row){
    ?>

    <div class="row">
        <div class="col-md-12" style="margin-top: 2%">
            <div class="text-center"><strong>SRI LANKA INLAND REVENUE</strong></div>
            <div class="text-center"><strong>CERTIFICATE OF INCOME TAX DEDUCTIONS</strong></div>
            <div class="text-center"><strong>P.A.Y.E</strong></div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class=""> <?php echo $companyData['company_name']; ?> </div>
            <div class=""> <?php echo $companyData['company_address1'];?> </div>
            <div class=""> <?php echo $companyData['company_address2']; ?> </div>
            <div class=""> <?php echo $companyData['company_city']; ?> </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6" style="font-style: italic">Prescribed under Section 103 0f the Inland Revenue Act</div>
        <div class="col-sm-6" align="right">P.A.Y.E./T.10</div>
    </div>


    <table class="<?php echo table_class();?>">
        <tr>
            <td>Full Name of the Employee   </td>
            <td colspan="2"><?php echo $row['fullName'] ?></td>
        </tr>
        <tr>
            <td>NIC.Number</td>
            <td colspan="2"> <?php echo $row['NIC'] ?></td>
        </tr>
        <tr>
            <td>Employer's Registration No.</td>
            <td colspan="2"> </td>
        </tr>
        <tr>
            <td>Period of service for which Remuneration was paid </td>
            <td colspan="2"> From : <?php echo $fromDate; ?> &nbsp;&nbsp;&nbsp; To :<?php echo $toDate; ?></td>
        </tr>
        <tr>
            <td>Total Gross Remuneration as per Pay Sheet</td>
            <td colspan="2" align="right"> </td>
        </tr>
        <tr>
            <td>Cash Benefits</td>
            <td align="right"><?php echo number_format($row['cashBenefit'], 2) ?></td>
            <td>Non Cash Benefits</td>
        </tr>
        <tr>
            <td>Total Amount of Tax Deducted  ( Rs.)</td>
            <td colspan="2" align="right"><?php echo number_format($row['payee'], 2) ?></td>
        </tr>
        <tr>
            <td>In Words</td>
            <td colspan="2"><?php echo $this->numbertowords->convert_number($row['payee']); ?> Rupees Only</td>
        </tr>
        <tr>
            <td>Total Amount remitted to the Dept. of Inland Revenue</td>
            <td colspan="2" align="right"><?php echo number_format($row['payee'], 2) ?></td>
        </tr>
        <tr>
            <td colspan="3" align="center"><strong>I certify the above particulars as correct</strong></td>
        </tr>
        <tr>
            <td>Name of Employer</td>
            <td colspan="2"> <strong><?php echo $companyData['company_name']; ?></strong></td>
        </tr>
        <tr>
            <td>Address</td>
            <td colspan="2"><?php echo  $companyData['company_address1'] . ' ' . $companyData['company_address2']; ?></td>
        </tr>
        <tr>
            <td colspan="3">Date &nbsp;&nbsp;&nbsp;&nbsp; <?php echo $processDate?></td>
        </tr>
        <tr>
            <td colspan="3">Signature of Employer  :......................................</td>
        </tr>
    </table>
    <?php
}
?>

<?php
