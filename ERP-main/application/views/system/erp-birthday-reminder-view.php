<table class="<?php echo table_class()?>" id="remaining-tb">
    <thead>
        <th>#</th>
        <th>Name</th>
        <th>Designation</th>
        <!--<th>Segment</th>-->
        <th>DOB</th>
        <th>Remaining Days</th>
    </thead>

    <tbody>
    <?php
    if(empty($employeeList)){

    }else{
        //<td>'.$row['description'].'</td>
        foreach ($employeeList as $key=>$row){

            echo '<tr>
                      <td>'.($key+1).'</td>
                      <td>'.$row['Ename2'].'</td>
                      <td>'.$row['DesDescription'].'</td>
                      
                      <td>'.$row['EDOB'].'</td>
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
