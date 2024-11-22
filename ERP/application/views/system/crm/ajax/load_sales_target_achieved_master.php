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

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
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
    .numberColoring{
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>
<?php
if (!empty($header)) {
    ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Employee</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Date From</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Date To</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Currency</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Product</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;">No of Units</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;">Target Amount</td>                
                <!--<td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;">Achieved Amount</td>-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) {
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#" class=""><?php echo $val['employee']; ?></a></td>
                    <td class="mailbox-name"><a href="#" class=""><?php echo $val['salesDateFrom']; ?></a></td>
                    <td class="mailbox-name"><a href="#" class=""><?php echo $val['salesDateTo']; ?></a></td>
                    <td class="mailbox-name"><a href="#" class=""><?php echo $val['CurrencyCode']; ?></a></td>      
                    <td class="mailbox-name"><a href="#" class=""><?php echo $val['productName']; ?></a></td>              
                    <td class="mailbox-name"><a href="#" class=""><?php echo $val['units']; ?></a></td>
                    <td class="mailbox-name" style="text-align: right"><a href="#" class=""><?php echo number_format($val['targetValue'], 2); ?></a></td>
                    <!--<td class="mailbox-name" style="text-align: right"><a href="#" class="">
                            <?php/*
                            $achievedAmount = $this->db->query("SELECT sum(acheivedValue) as total FROM srp_erp_crm_salestargetacheived WHERE salesTargetID = {$val['salesTargetID']} AND userID = {$val['userID']}")->row_array();

                            if($achievedAmount){
                                echo number_format($achievedAmount['total'], 2);
                            }else{
                                echo '0.00';
                            }
                                */
                            ?></a>
                    </td>-->
                    <td class="mailbox-attachment" style="text-align: right">
                        <a href="#"
                           onclick="edit_salesTarget_achieved('<?php echo $val['salesTargetID'] ?>')"><span
                                title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<a
                            onclick="delete_salesTargetAcheived(<?php echo $val['salesTargetID'] ?>);"><span title="Delete" rel="tooltip"
                                                                                       class="glyphicon glyphicon-trash"
                                                                                       style="color:rgb(209, 91, 71);"></span></a>
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
    <div class="search-no-results">THERE ARE NO SALES TARGET ACHIEVED TO DISPLAY.</div>
    <?php
}
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>