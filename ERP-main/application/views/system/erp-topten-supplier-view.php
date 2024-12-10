<table class="<?php echo table_class()?>" id="suppliertopten-tb-<?php echo $userDashboardID?>">
    <thead>
        <th>#</th>
        <th>Supplier Code</th>
        <th>Supplier Name</th>
        <th>Amount</th>
    </thead>
     <tbody>
    <?php
    if(empty($toptensupplierlist)){

    }else{
        $currencydecimal = 2;
        if($currentcyID == 1)
        {
            $currencydecimal = $this->common_data['company_data']['company_default_decimal'];
        }else
        {
            $currencydecimal = $this->common_data['company_data']['company_reporting_decimal'];
        }
        foreach ($toptensupplierlist as $key=>$row){

            echo '<tr>
                      <td>'.($key+1).'</td>
                      <td><a href="#" onclick="drilldowntoptensupplier(\'' . $row['supplierAutoID'] . '\',\'' . $row['suppliername'] . '\',\'' . $currentcyID . '\')">'.$row['supplierSystemCode'].'</a></td>
                      <td>'.$row['suppliername'].'</td>
                      <td style="text-align: right">'.number_format( $row['transactionAmount'],$currencydecimal).'</td>
               </tr>';
        }
    }
    ?>
    </tbody>
</table>


<div class="modal fade" id="drilldownModal_toptensuppliers" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title drilldown-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <div id="toptensupplier_drilldown"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




<script>
    var table = $('#suppliertopten-tb-<?php echo $userDashboardID?>').DataTable({
        "pageLength": 10,
        "lengthChange": false
    });
    table.destroy();
    $('#suppliertopten-tb-<?php echo $userDashboardID?>').DataTable({
        "pageLength": 10,
        "lengthChange": false
    });

    
    function drilldowntoptensupplier(supplierAutoID,suppliername,currenyID) {
        $.ajax({
            type: "POST",
            url: '<?php echo site_url('Finance_dashboard/get_toptensupplierrdd'); ?>',
            data:{'supplierAutoID':supplierAutoID,'suppliername':suppliername,'currenyID':currenyID},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#toptensupplier_drilldown").html(data);
                $('#drilldownModal_toptensuppliers').modal({backdrop: "static"});
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });

    }
    
</script>

<?php
