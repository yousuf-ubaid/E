<?php
if (!empty($detail)) {
    foreach ($detail as $row) { ?>
        <div class="col-sm-1 text-center">
            <?php
            if(empty($row['EmpImage'])){
                ?>
                <img class="img-circle img-responsive img-center"
                     src="<?php echo base_url("images/crm/no-profile-img.gif") ?>"
                alt="">
                <?php
            }else{
                ?>
                <img class="img-circle img-responsive img-center"
                     src="<?php echo base_url("images/users/").$row['EmpImage'] ?>"
                alt="">
                <?php
            }
            ?>

            <?php echo $row['employeeName'] ?>
        </div>
        <?php
    }
}
?>
&nbsp; &nbsp; &nbsp;