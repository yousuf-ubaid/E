<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);


?>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>" />

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
        color: rgb(104, 97, 234);
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
</style>
<?php
if (!empty($organized_records)) { ?>
    <div class="table-responsive mailbox-messages">
    <?php   $y=0;
            foreach($organized_records as $supplierID => $records): ?>
            <table class="table table-hover table-striped">
                <tbody>
                <?php if($y==0): ?>
                <tr id="tblHead_<?php echo $y; ?>">
                    <td class="headrowtitle" style="border-top: 1px solid #ffffff;width: 5%;">#</td>
                    <td class="headrowtitle" style="border-top: 1px solid #ffffff;width: 15%;">Supplier Name</td>
                    <td class="headrowtitle" style="border-top: 1px solid #ffffff;width: 15%;">PO No </td>
                    <td class="headrowtitle" style="border-top: 1px solid #ffffff;width: 15%;">Customer Name</td>
                    <td class="headrowtitle" style="border-top: 1px solid #ffffff;width: 15%;">Order No </td>
                    <td class="headrowtitle" style="border-top: 1px solid #ffffff;width: 15%;">Sales Order No</td>
                    <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: right;width: 15%;">Commision Amount</td>
                    <td class="headrowtitle" style="border-top: 1px solid #ffffff;width: 5%;"><?php echo $this->lang->line('common_action');?><!--Action--></td>
                </tr>
                <?php else: ?>
                    <tr id="tblHead_<?php echo $y; ?>">
                        <td class="headrowtitle" style="border-top: 1px solid #ffffff; color: transparent;width: 5%;">#</td>
                        <td class="headrowtitle" style="border-top: 1px solid #ffffff; color: transparent;width: 15%;">Supplier Name</td>
                        <td class="headrowtitle" style="border-top: 1px solid #ffffff; color: transparent;width: 15%;">PO No</td>
                        <td class="headrowtitle" style="border-top: 1px solid #ffffff; color: transparent;width: 15%;">Customer Name</td>
                        <td class="headrowtitle" style="border-top: 1px solid #ffffff; color: transparent;width: 15%;">Order No</td>
                        <td class="headrowtitle" style="border-top: 1px solid #ffffff; color: transparent;width: 15%;">Sales Order No</td>
                        <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: right; color: transparent;width: 15%;">Commission Amount</td>
                        <td class="headrowtitle" style="border-top: 1px solid #ffffff; color: transparent;width: 5%;"><?php echo $this->lang->line('common_action'); ?></td>
                    </tr>
                <?php endif; ?>
                
                <?php
                    $x = 1;
                    $total = null;
                        foreach($records as $record): ?>
                        
                        <tr>
                            <input type="hidden" name="purchaseOrderDetails_ID" id="purchaseOrderDetails_ID" value="<?php echo $record['purchaseOrderDetailsID']; ?>">
                            <input type="hidden" name="purchaseOrder_ID" id="purchaseOrder_ID" value="<?php echo $record['purchaseOrderID']; ?>">

                            <td class="mailbox-name"><a href="#"><?php echo $x ?></a></td>
                            <td class="mailbox-name"><?php echo $record['supplierName']; ?></td>
                            <td class="mailbox-name"><?php echo $record['purchaseOrderCode']; ?></td>
                            <td class="mailbox-name"><?php echo $record['customerName']; ?></td>
                            <td class="mailbox-name"><?php echo $record['customerOrderCode']; ?></td>
                            <td class="mailbox-name"><?php echo $record['contractCode']; ?></td>
                            <td class="mailbox-name text-right"><?php echo $record['commision_value']; ?></td>
                            <td class="mailbox-attachment">
                                <span class="pull-right">
                                    <a onclick="delete_record(<?php echo $record['purchaseOrderDetailsID']; ?>);">
                                        <span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);">
                                        </span>
                                    </a>
                                </span>
                            </td>
                        </tr>
                <?php
                $x++;
                $total += $record['commision_value'];
                endforeach; ?>
                <tr>
                    <td colspan="4">&nbsp;</td>
                    <td><b>Total</b></td>
                    <td colspan="1">&nbsp;</td>
                    <td class="text-right reporttotal">
                        <b>
                            <?php
                                if ($total >= 0) {
                                    echo number_format($total, '.00');
                                } else {
                                    echo '(' . number_format(abs($total), '.00') . ')';
                                }
                            ?>
                        </b>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php   $y++; 
            endforeach; ?>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">No Records Found</div>
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