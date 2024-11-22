<table class="<?php echo table_class()?>" id="remaining-tb2">
    <thead>
        <th>#</th>
        <th>Code</th>
        <th>Customer</th>
        <th>Reference</th>
        <!--<th>Segment</th>-->
        <th>Remaining Days</th>
    </thead>

    <tbody>
    <?php
    if(empty($employeeList)){
        echo '<tr><td colspan="5">No Contracts / Quotation / Sales Orders to Show</td></tr>';
    }else{
        //<td>'.$row['description'].'</td>
  
        foreach ($employeeList as $key=>$row){
            $documentID = "'".$row['documentID']."'";
            echo '<tr>
                      <td>'.($key+1).'</td>
                      <td><a onclick="documentPageView_modal('.$documentID.','.$row['contractAutoID'].')">'.$row['contractCode'].'</a></td>
                      <td>'.$row['customerName'].'</td>
                      <td>'.$row['referenceNo'].'</td>
                      <td style="text-align: right">'.$row['diff'].'</td>
                 </tr>';
        }
    }

    ?>
    </tbody>
</table>

<script>
    var table = $('#remaining-tb').DataTable();
    table.destroy();
    $('#remaining-tb').DataTable();
</script>

<?php
