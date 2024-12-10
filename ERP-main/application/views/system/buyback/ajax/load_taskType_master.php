<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .headrowtitle {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }
</style>
<?php
if (!empty($taskTypes)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Description</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Short Description</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Linked Document Type</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($taskTypes as $val) {
                $companyID = $this->common_data['company_data']['company_id'];
                $taskAssign = $this->db->query("SELECT tasktypeID FROM srp_erp_buyback_tasktypes_details WHERE companyID = {$companyID} AND tasktypeID = {$val['tasktypeID']} AND isActive = 1 ORDER BY tasktypeID ASC")->row_array();
                ?>
                <tr>
                    <td class="mailbox-star" width="10%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width="30%"><?php echo $val['description'] ?></td>
                    <td class="mailbox-star" width="30%"><?php echo $val['shortDescription'] ?></td>
                    <td class="mailbox-star" width="40%"><?php
                        if($val['DocumentCode'] == 'BBDPN'){
                            echo "Dispatch Note";
                        }else if($val['DocumentCode'] == 'BBGRN'){
                            echo "Goods Received Note";
                        } else if($val['DocumentCode'] == 'BBRV'){
                            echo "Receipt Voucher";
                        }else if($val['DocumentCode'] == 'BBPV'){
                            echo "Payment Voucher";
                        }else if($val['DocumentCode'] == 'BBSV'){
                            echo "Settlement";
                        }else if($val['DocumentCode'] == 'BBDR'){
                            echo "Dispatch Return";
                        }else if($val['DocumentCode'] == 'BBFVR'){
                            echo "Farm Visit Report";
                        }
                        ?></td>
                    <?php if(!empty($taskAssign)){?>
                        <td class="mailbox-attachment" width="20%">
                        <span class="pull-right"><a href="#" onclick="edit_taskType(<?php echo $val['tasktypeID'] ?>)"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a></span></a>
                            </span>
                        </td>
                    <?php } else { ?>
                        <td class="mailbox-attachment" width="10%">
                        <span class="pull-right"><a href="#" onclick="edit_taskType(<?php echo $val['tasktypeID'] ?>)"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_taskType(<?php echo $val['tasktypeID'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
                        </span>
                        </td>
                    <?php } ?>
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
    <div class="alert" role="alert" style="background: #9ab9f1">THERE ARE NO TASK TYPES TO DISPLAY, PLEASE CLICK <b>TASK</b> TO CREATE NEW TASK TYPE.</div>
    <?php
}
?>



<?php
