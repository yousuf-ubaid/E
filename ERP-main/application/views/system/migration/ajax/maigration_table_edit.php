<table id="attendanceReview" class="table tb " style="max-width: 1750px !important;">
    <thead class="">
        <tr style="white-space: nowrap">

        <?php
            if (!empty($table_header)) { 
                foreach($table_header as $val){
                ?>


            <th style="width: 15px;"><?php echo $val ?></th>
            <?php } } ?>

            
        </tr>
    </thead>

    <tbody>
        <?php
        if (!empty($table_body)) { 
                foreach($table_body as $val){
                ?>
            <tr>
                    <?php
                foreach($val as $val1){ ?>
                    <td ><?php echo $val1 ?></td>
                <?php } ?>
            </tr>
        <?php } }else{ ?>

            <tr>
                <td colspan="21">
                    No data available in table
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

