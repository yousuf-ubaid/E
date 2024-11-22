<table>
    <tr>
        <td>
            <img alt="Logo" style="height: 80px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
        </td>
    </tr>
</table>
<pre style="background: none;border: none; margin-top: 5%; font-family: 'Calibri (Body)'">
<?php echo $masterData['transferDate']; ?>

The Manager
<?php echo $masterData['bankName'] ?>

Dear Sir/Madam,

<b>Sub: Salary Transfer <?php echo $payDate ?></b>


Kindly arrange to transfer the following amounts to the credit of the attached accounts
respectively and debit our current account No. <b><?php echo $masterData['accountNo']; ?></b> with the total amount of
<b><?php echo $bTransOtherDet;?></b>.


Thank You
Your faithfully,
<?php echo $this->common_data['company_data']['company_name']; ?>

</br>
</br>
</br>
</br>
</br>
Authorized Signature
</pre>


