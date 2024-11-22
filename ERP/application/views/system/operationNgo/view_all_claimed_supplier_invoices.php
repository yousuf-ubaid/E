<?php

?>
<div class="table-responsive">
    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th class='theadtr' style="min-width: 5%">#</th>
            <th class='theadtr' style="min-width: 15%">Invoice Code</th>
            <th class='theadtr' style="min-width: 15%">Supplier Name</th>
            <th class='theadtr' style="min-width: 10%">Narration</th>
            <th class='theadtr' style="min-width: 15%">Total Amount</th>
            <th class='theadtr' style="min-width: 15%">Paid Amount</th>
        </tr>
        </thead>
        <tbody id="grv_table_body">
        <?php
         $totalamt = 0;
         $totalamtpaid = 0;
        $x=1;

        if (!empty($header)) {

            foreach ($header as $val){

                    $paidamount = $this->db->query("select IFNULL(SUM(paymentvoucherdetails.transactionAmount),0)as transactionAmounttotalpaid,paymentvoucherdetails.payVoucherAutoId,paymentvouchermaster.confirmedYN,paymentvouchermaster.approvedYN
from 
srp_erp_paymentvoucherdetail paymentvoucherdetails
Left JOIN srp_erp_paymentvouchermaster paymentvouchermaster on paymentvouchermaster.payVoucherAutoId = paymentvoucherdetails.payVoucherAutoId where InvoiceAutoID = '{$val['claimedInvoiceAutoID']}' AND paymentvouchermaster.confirmedYN = 1 AND paymentvouchermaster.approvedYN = 1")->row_array();


                echo '<tr>';
                echo '<td>'.$x.'</td>';
                echo '<td><a href="#"  onclick="load_calime_invoices('.$val['claimedInvoiceAutoID'].')">&nbsp;&nbsp;&nbsp;'.$val['bookingInvCode'].'</a></td>';
                echo '<td>'.$val['supplierName'].'</td>';
                echo '<td>'.$val['comments'].'</td>';

                echo '<td class="text-right">'.number_format($val['transactionAmount'],2) .'</td>';

                if($paidamount['transactionAmounttotalpaid']!=0)
                {
                    echo '<td class="text-right"><a href="#" onclick="load__paymentvoucher_drildown('.$val['claimedInvoiceAutoID'].')">&nbsp;&nbsp;&nbsp;'.number_format($paidamount['transactionAmounttotalpaid'],2) .'</a></td>';
                }else
                {
                    echo '<td class="text-right">'.number_format($paidamount['transactionAmounttotalpaid'],2) .'</a></td>';
                }
                echo '</tr>';
                $x++;
                $totalamt += $val['transactionAmount'];
                $totalamtpaid += $paidamount['transactionAmounttotalpaid'];
            }

        }else {

            echo '<tr class="danger"><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>';
        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="4">Total</td>
            <td class="text-right sub_total"><?php echo number_format($totalamt,2) ?></td>
            <td class="text-right sub_total"><?php echo number_format($totalamtpaid,2) ?> </td>

        </tr>
        </tfoot>
    </table>

</div>
