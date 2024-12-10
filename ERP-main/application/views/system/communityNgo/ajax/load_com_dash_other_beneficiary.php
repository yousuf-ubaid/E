<?php
if (!empty($beneMem)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">NAME</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">PHONE NO</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">COUNTRY</td>


            </tr>
            <?php
            $x = 1;

            foreach ($beneMem as $val) {

                ?>
                <tr>
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width=""><?php echo $val['nameWithInitials'] ; ?></td>
                    <td class="mailbox-star" width=""><?php echo $val['contactPhonePrimary']; ?></td>
                    <td class="mailbox-star" width=""><?php echo $val['CountryDes']; ?></td>
                 </tr>
                <?php


                $x++;
            }
            ?>
            </tbody>

        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
    <?php
}
?>

<?php
