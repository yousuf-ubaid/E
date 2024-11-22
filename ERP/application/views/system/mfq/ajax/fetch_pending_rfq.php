<div class="table-responsive">
    <table id="tbl_rfq_types" class="rfqTable table table-striped table-condensed">
        <thead>
        <tr>
            <th style="min-width: 12%" class="text-uppercase">SI No.</th>
            <th style="min-width: 12%">Estimator</th>
            <th style="min-width: 3%">Firm</th>
            <th style="min-width: 3%">Budget</th>
            <th style="min-width: 3%">Remarks</th>
            <th style="min-width: 3%">Total Pending RFQ</th>                                            
        </tr>
        </thead>
        <tbody>
        <?php

        if ($pending_rfq) {
            
            foreach ($pending_rfq as $key=>$val) {
                
                ?>
                <tr>
                    <td><?php echo  $key+1  ?></td>
                    <td class="text-center"><?php echo  $val['estimator']  ?></td>
                    <td class="text-center"><?php echo  $val['firm']  ?></td>
                    <td class="text-center"><?php echo  $val['budget']  ?></td>
                    <td class="text-center"><?php echo  $val['remark']  ?></td>
                    <td class="text-center"><?php echo  $val['tot_rfq']  ?></td>                                                   
                </tr>
                <?php
            } ?>
        <?php }else{ ?>
            <tr>
                    <td class="text-center" colspan="6">No data to display</td>
                                                                       
                </tr>
        <?php } ?>
        </tbody>
    </table>
</div>    