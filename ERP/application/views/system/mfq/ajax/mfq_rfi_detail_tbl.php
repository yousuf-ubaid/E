

<?php
    $rfi_status = $master['status'];
    $readonly = '';
    if($rfi_status == 'Close'){
        $readonly = true;
    }
?>

<?php foreach($details as $detail){ ?>
    <tr>
        <td></td>
        <td>
            <input type="text" class="form-control" onchange="add_remarks(<?php echo $detail['id'] ?>,this,'ItemDescription')" value="<?php echo $detail['itemDescription'] ?>" />
        </td>
        <td><?php echo $detail['itemStatus'] ?></td>
       
        <?php if($approval == 1) { ?>
            <td><input type="text" class="form-control" onchange="add_remarks(<?php echo $detail['id'] ?>,this)" value="<?php echo $detail['remarks'] ?>" readonly/></td>
            <td><input type="text" class="form-control" onchange="add_remarks(<?php echo $detail['id'] ?>,this,'QC')" value="<?php echo $detail['qc_comment'] ?>" <?php echo $readonly ?>/></td>
            <td><?php echo $detail['inspectedBy'] ?></td>
        <?php } else {  ?>
            <td><input type="text" class="form-control" onchange="add_remarks(<?php echo $detail['id'] ?>,this)" value="<?php echo $detail['remarks'] ?>" /></td>
            <td><?php echo $detail['inspectedBy'] ?></td>
            <?php if($rfi_status == 'Open') { ?>
                <td><?php echo '<a onclick=\'deleteRfi(' . $detail['id'] . ')\'><span class="glyphicon text-danger glyphicon-trash"></span></a>'?></td>
            
        <?php } } ?>

        
    </tr>

<?php } ?>

<script>

    function add_remarks(id,ev,type=null){
        $.ajax({
            async: true,
            type: 'post',
            // dataType: 'html',
            data: {'id': id,'remark':$(ev).val(),'type':type},
            url: "<?php echo site_url('MFQ_Job/add_remark_for_item'); ?>",
            beforeSend: function () {
                startLoad();

            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
             
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    

</script>