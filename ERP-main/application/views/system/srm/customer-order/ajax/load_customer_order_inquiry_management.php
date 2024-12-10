<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

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
</style>
<?php
if (!empty($output)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Inquiry Type</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('srm_inquiry_code');?><!--Inquiry Code--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_customer_name');?><!--Customer Name--></td>
                <!--<td class="headrowtitle" style="border-top: 1px solid #ffffff;">Order Code</td>-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_currency');?><!--Currency--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">RFQ</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center"><?php echo $this->lang->line('common_status');?><!--Status--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_action');?><!--Action--></td>
            </tr>
            <?php
            $x = 1;
            $reqcount = 0;
            $subcount = 0;
            foreach ($output as $val) {
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#"><?php echo $x ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['inquiryType'] ?></a></td>
                    <td class="mailbox-name">
                        <a class="link-person noselect" href="#"  onclick="fetchPage('system/srm/customer-order/order_inquiry_edit_view','<?php echo $val['inquiryID'] ?>','View Order Inquiry','SRM')"><?php echo $val['orderCode'] ?></a>
                    </td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['customerName'] ?></a></td>
                    <!--<td class="mailbox-name"><a href="#"><?php /*echo $val['customerOrderCode'] */?></a></td>-->
                    <td class="mailbox-name"><a href="#"><?php echo $val['CurrencyCode'] ?></a></td>
                    <td class="mailbox-name">
                    <div class="contact-box">
                        <div class="link-box">
                            <?php 
                                $companyID = current_companyID();
                                $reqcount = $this->db->query("SELECT
                                COUNT(t1.inquiryDetailID) as reqcount  FROM (SELECT srp_erp_srm_orderinquirydetails.inquiryDetailID
                                FROM
                                `srp_erp_srm_orderinquirydetails`
                                 LEFT JOIN `srp_erp_srm_suppliermaster` ON `srp_erp_srm_orderinquirydetails`.`supplierID` = `srp_erp_srm_suppliermaster`.`supplierAutoID` 
                                 WHERE
                                `srp_erp_srm_orderinquirydetails`.`companyID` = '{$companyID}' 
                                 AND `srp_erp_srm_orderinquirydetails`.`inquiryMasterID` = '{$val['inquiryID']}' 
                                 AND `isRfqCreated` = 1 
                                --  AND isRfqEmailed = 1 
                                 GROUP BY
                                 `srp_erp_srm_orderinquirydetails`.`supplierID`) t1")->row('reqcount');


                            $subcount = $this->db->query("SELECT
                            COUNT(t1.inquiryDetailID) as subcount  FROM (SELECT srp_erp_srm_orderinquirydetails.inquiryDetailID
                            FROM
                            `srp_erp_srm_orderinquirydetails`
                             LEFT JOIN `srp_erp_srm_suppliermaster` ON `srp_erp_srm_orderinquirydetails`.`supplierID` = `srp_erp_srm_suppliermaster`.`supplierAutoID` 
                             WHERE
                            `srp_erp_srm_orderinquirydetails`.`companyID` = '{$companyID}' 
                             AND `srp_erp_srm_orderinquirydetails`.`inquiryMasterID` = '{$val['inquiryID']}' 
                             AND `isRfqCreated` = 1 
                            --  AND isRfqEmailed = 1 
                             AND isSupplierSubmited = 1 
                             GROUP BY
                             `srp_erp_srm_orderinquirydetails`.`supplierID`) t1")->row('subcount');
                            ?>


                                        <strong class="contacttitle">Requested Count: <?php echo $reqcount?></strong>
                                        <br>
                                        <strong class="contacttitle">Submitted Count : <?php echo $subcount?></strong> 
                                     
                        </div>
                    </td>
                    <td class="mailbox-name" style="text-align: center">
                    <?php if ($val['isOrderReviewConfirmYN'] == 1) { ?>
                        <span class="label"
                                  style="background-color: #4ac3bd; color: #FFFFFF; font-size: 11px;">Order Review Confirmed</span>
                    <?php } else {?>
                        <?php if ($val['inquiryConfirm'] == 1) { ?>
                            <span class="label"
                                  style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">RFQ Confirmed</span>
                        <?php } else {?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">RFQ Not Confirmed<!--Not Confirmed--></span>
                        <?php } ?>
                    <?php } ?>
                    </td>
                    <td class="mailbox-attachment">
                        <span class="pull-right">
                             <?php if ($val['inquiryConfirm'] != 1) { ?>
                            <a href="#"
                               onclick="fetchPage('system/srm/customer-order/create_new_order_inquiry','<?php echo $val['inquiryID'] ?>','<?php echo $this->lang->line('srm_edit_order_inquiry');?>','SRM')"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<!--Edit Order Inquiry-->


                            <a onclick="delete_customer_inquiry_master(<?php echo $val['inquiryID'] ?>);"><span
                                             title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                             style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;
                             <?php } ?>
                            <a onclick="generated_supplier_RFQ_View(<?php echo $val['inquiryID'] ?>);"><span
                                             title="Email" rel="tooltip" class="glyphicon glyphicon-envelope"
                                             style="color:#3c8dbc;"></span></a>

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
    <div class="search-no-results"><?php echo $this->lang->line('srm_there_are_no_customer_order_inquiry');?>.</div><!--THERE ARE NO CUSTOMER ORDER INQUIRY TO DISPLAY-->
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