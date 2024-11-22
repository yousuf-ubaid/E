<div class="table-responsive">
    <table id="tbl_rfq_types" class="rfqTable table table-striped table-condensed">
        <thead>
        <tr>
            <th style="min-width: 12%" class="text-uppercase">SI No.</th>
           
            <th style="min-width: 12%">Estimator</th>
            <th style="min-width: 3%">Pending</th>
            <th style="min-width: 3%">Complete</th>
            <th style="min-width: 3%">Remarks</th>
            <th style="min-width: 3%">Total</th>                                            
        </tr>
        </thead>
        <tbody>
        <?php

        if ($total) {
            
            foreach ($total as $key=>$val) {
                
                ?>
                <tr>
                    <td><?php echo  $key+1  ?></td>
                   
                    <td class="text-center"><?php echo  $val['estimator']  ?></td>
                    <td class="text-center"><?php echo  $val['pending']  ?></td>
                    <td class="text-center"><?php echo  $val['complete']  ?></td>
                    <td class="text-center"><?php echo  $val['remark']  ?></td>
                    <td class="text-center"><?php echo  $val['total']  ?></td>                                                   
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