<table class="<?php echo table_class()?>" id="customertopten-tb-<?php echo $userDashboardID?>">
    <thead>
        <th>#</th>
        <th>Customer Code</th>
        <th>Customer Name</th>
        <th>Amount</th>
    </thead>
     <tbody>
    <?php
    if(empty($toptencustomerlist)){

    }else{
        $currencydecimal = 2;
        if($currentcyID == 1)
        {
        $currencydecimal = $this->common_data['company_data']['company_default_decimal'];
        }else
        {
        $currencydecimal = $this->common_data['company_data']['company_reporting_decimal'];
        }

        foreach ($toptencustomerlist as $key=>$row){

            echo '<tr>
                      <td>'.($key+1).'</td>
                      <td><a href="#" onclick="drilldowntoptencustomers(\'' . $row['customerAutoID'] . '\',\'' . $row['customerName'] . '\',\'' . $currentcyID . '\')">'.$row['customerSystemCode'].'</a></td>
                      <td>'.$row['customerName'].'</td>
                      <td style="text-align: right">'.number_format( $row['transactionAmount'],$currencydecimal).'</td>
               </tr>';
        }
    }
    ?>
    </tbody>
</table>


<div class="modal fade" id="drilldownModal_toptencustomers" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title drilldown-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <div id="toptencustomers_drilldown"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




<script>
    var table = $('#customertopten-tb-<?php echo $userDashboardID?>').DataTable({
        "pageLength": 10,
        "lengthChange": false
    });
    table.destroy();
    $('#customertopten-tb-<?php echo $userDashboardID?>').DataTable({
        "pageLength": 10,
        "lengthChange": false
    });

    
    function drilldowntoptencustomers(customerAutoID,customerName,currenyID) {
        $.ajax({
            type: "POST",
            url: '<?php echo site_url('Finance_dashboard/get_toptencustomerdd'); ?>',
            data:{'customerAutoID':customerAutoID,'customerName':customerName,'currenyID':currenyID},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#toptencustomers_drilldown").html(data);
                $('#drilldownModal_toptencustomers').modal({backdrop: "static"});
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });

    }
    
</script>

<?php
