<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
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
if (!empty($cause)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Description</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">GL Code</td>
                <!--<td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Charged</td>-->
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($cause as $val) {
                ?>
                <tr>
                    <td class="mailbox-star" width="10%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width="50%"><?php echo $val['Description'] ?></td>
                    <td class="mailbox-star" width="60%"><?php
                        if(!empty($val['mortalityGLautoID']))
                        echo $val['systemGLCode'] . ' | ' . $val['GLDescription'] ?>
                    </td>
                   <!-- <td class="mailbox-star" width="30%"><?php
/*                        if($val['isFarmerCharged'] == 1){

                        } else{
                            echo $val['systemGLCode'] . ' | ' . $val['GLDescription'];
                        }
                         */?></td>-->
                    <td class="mailbox-attachment" width="20%">
                        <span class="pull-right">
                            <a href="#" onclick="edit_mortalityCourse(<?php echo $val['causeID'] ?>)">
                                <span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span>
                            </a>

                            <?php
                            $id = $val['causeID'];
                            $data = $this->db->query("select * from srp_erp_buyback_mortalitydetails WHERE causeID = {$id} ")->row_array();
                            if(empty($data)){ ?>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <a onclick="delete_mortalityCourse(<?php echo $val['causeID'] ?>);">
                                <span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>
                            </a>
                       <?php }
                            ?>
                        </span>
                    </td>
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
    <div class="alert" role="alert" style="background: #9ab9f1">THERE ARE NO MORTALITY COURSES TO DISPLAY, PLEASE CREATE MORTALITY COURSE USING <b>MORTALITY COURSE</b>.</div>
    <?php
}
?>