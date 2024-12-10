<div>
    <div class="table-responsive">
        <table style="width: 90%">
            <tbody>
            <tr>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td style="text-align:center;font-size: 18px;font-family: tahoma;">
                                <strong style="font-weight: bold;"><?php echo $this->common_data['company_data']['company_name']; ?>.</strong><br>

                                <strong style="font-weight: bold;">  <?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?>.</strong><br>
                                <strong style="font-weight: bold;">  Tel : <?php echo $this->common_data['company_data']['company_phone'] ?></strong><br>
                                <br>
                                Buyback Pending Tasks
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">( As of Date&nbsp; &nbsp;: <?php echo $TodoDate; ?> )</td>
                        </tr>
                    </table>
                </td>
            </tr>

            </tbody>
        </table>
    </div>
<br>
    <div id="to-do_List">
        <ul class="todo-list ui-sortable">
            <?php if (!empty($details))
            {
                $a = 1;
                foreach ($details as $val)
                {
                    ?>
                    <li>
                        <?php if(empty($val['taskID'])){ ?>
                        <span class="text"><?php echo $val['farm'] . ' | ' . $val['batch'] . ' | ' . $val['description']?></span>
                        <?php } ?>

                    </li>


                    <?php $a++; } ?>
            <?php } else { ?>
                <li>
                <span class="handle ui-sortable-handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                    <span class="text-center">No Tasks Available</span>
                </li>
            <?php } ?>
        </ul>
    </div>



</div>

<?php
