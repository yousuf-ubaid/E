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
if($inquiryheader['inquiryType']=='Customer'){
if (!empty($header)) {
    ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Name</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Price</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Qty</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center">
                    <!--<div class="skin skin-square">
                        <div class="skin-section extraColumns">
                            <input id="orderItem_MasterCheck" type="checkbox"
                                   data-caption="" class="columnSelected"
                                   name="isActive" onclick=""
                                   value="">
                        </div>
                    </div>-->
                </td>
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) {
                ?>
                <tr>
                    <td class="mailbox-name">
                        <?php echo $x ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 100px">
                        <div class="contact-box d-flex">

                            <img class="align-left" src="<?php echo $val['awsImage'] ?>"
                                 alt="" width="40" height="40">

                            <div class="link-box"><strong class="contacttitle"><a class="link-person noselect"
                                                                                  href="#"><?php echo $val['itemName'] ?><br><?php echo $val['itemSystemCode'] ?><br><?php echo $val['customerOrderCode'] ?>
                                </strong></div>
                        </div>
                    </td>
                    
                    <td class="mailbox-name" style="min-width: 100px">
                    <div class="link-box"><strong class="contacttitle"><a class="link-person noselect" href="#"><?php echo $val['uamount'] ?> (<?php echo $val['CurrencyCode'] ?>) </a>
                    </strong>
                    </div>
                    </td>

                    <td class="mailbox-name" style="min-width: 100px">
                    <div class="link-box"><strong class="contacttitle"><a class="link-person noselect" href="#"><?php echo $val['requestedQty'] ?></a>
                    </strong>
                    </div>
                    
                    </td>
                    
                    <td class="mailbox-name" style="min-width: 50px">
                        <textarea class="form-control" rows="2" name="lineWiseComment" id="lineWiseComment"></textarea>
                    </td>
                   
                    <td width="5%">
                        <?php
                        $orderValue = $this->db->query("SELECT isChecked FROM srp_erp_srm_inquiryitem INNER JOIN srp_erp_srm_orderinquirymaster ON srp_erp_srm_orderinquirymaster.inquiryID=srp_erp_srm_inquiryitem.inquiryMasterID where itemAutoID = ".$val['itemAutoID']." AND srp_erp_srm_orderinquirymaster.inquiryType='Customer' AND orderMasterID = ".$val['customerOrderID']." AND srp_erp_srm_orderinquirymaster.inquiryID = $inquiryID")->row_array();
                        $disabled ='';
                        if($orderValue['isChecked']){
                            $disabled = "disabled";
                        }
                        ?>
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns"><input
                                        id="isAttended_<?php echo $val['itemAutoID'] ?>" type="checkbox" <?php echo $disabled ?>
                                        data-caption="" class="columnSelected isitem_checkbox"
                                        name="isActive" onclick="orderItem_selected_check(this)"
                                        value="<?php echo $val['itemAutoID']."_".$val['customerOrderID'] ?>"><label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
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
    <div class="search-no-results">THERE ARE NO CUSTOMER ORDER ITEMS TO DISPLAY.</div>
    <?php
}
?>
    <?php
}elseif ($inquiryheader['inquiryType']=='PRQ'){
    ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Name</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Price</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Qty</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Comment</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Attachment From PR</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Other Attachment</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center">
                   <!-- <div class="skin skin-square">
                        <div class="skin-section extraColumns">
                            <input id="orderItem_MasterCheck" type="checkbox"
                                   data-caption="" class="columnSelected"
                                   name="isActive" onclick=""
                                   value="">
                        </div>
                    </div>-->
                </td>
            </tr>
            <?php
            $x = 1;
            foreach ($prqdetail as $key=>$val) {
                ?>
                <tr>
                    <td class="mailbox-name">
                        <?php echo $x ?>
                    </td>
                    <td class="mailbox-name" style="min-width: 100px">
                        <div class="contact-box d-flex">
                            <img class="align-left" src="<?php echo $val['awsImage'] ?>"
                                 alt="" width="60" height="60">

                            <div class="link-box"><strong class="contacttitle"><a class="link-person noselect"
                                                                                  href="#"><?php echo $val['itemDescription'] ?><br><?php echo $val['itemSystemCode'] ?>
                                </strong></div>
                        </div>
                    </td>
                    <td class="mailbox-name" style="min-width: 100px">
                    <div class="link-box"><strong class="contacttitle"><a class="link-person noselect" href="#"><?php echo $val['totalAmount'] ?> (<?php echo $val['transactionCurrency'] ?>) </a>
                    </strong>
                    </div>
                    </td>

                    <td class="mailbox-name" style="min-width: 100px">
                    <div class="link-box"><strong class="contacttitle"><a class="link-person noselect" href="#"><?php echo $val['srmqty'] ?></a>
                    </strong>
                    </div>
                    
                    </td>

                    <?php
                    $orderValue = $this->db->query("SELECT isChecked FROM srp_erp_srm_inquiryitem INNER JOIN srp_erp_srm_orderinquirymaster ON srp_erp_srm_orderinquirymaster.inquiryID=srp_erp_srm_inquiryitem.inquiryMasterID where itemAutoID = ".$val['itemAutoID']." AND srp_erp_srm_orderinquirymaster.inquiryType='PRQ'  AND orderMasterID = ".$val['purchaseRequestID']." AND srp_erp_srm_orderinquirymaster.inquiryID = $inquiryID AND purchaseRequestDetailsID = ".$val['purchaseRequestDetailsID']." ")->row_array();
                    $linewiscmnt = $this->db->query("SELECT lineWiseComment FROM srp_erp_srm_orderinquirydetails where itemAutoID = ".$val['itemAutoID']." AND inquiryMasterID= $inquiryID   AND customerOrderID = ".$val['purchaseRequestID']." AND customerOrderDetailID = ".$val['purchaseRequestDetailsID']." ")->row_array();
                    $disabled ='';
                    $cmnt ='';
                    if($orderValue['isChecked']){
                        //$disabled = "disabled";
                    }

                    if(!empty($linewiscmnt['lineWiseComment'])){
                        $cmnt = $linewiscmnt['lineWiseComment'];
                    }
                    ?>
                    <td class="mailbox-name" style="min-width: 50px">
                        <textarea class="form-control" rows="2" name="lineWiseComment" <?php echo $disabled ?> id="lineWiseComment_<?php echo $val['itemAutoID']; ?>" ><?php echo $cmnt; ?></textarea>
                    </td>

                    <td class="mailbox-name" style="min-width: 50px">
                        <select name="pr_doc" class="form-control frm_input select2" id="pr_doc_<?php echo $val['itemAutoID'] ?>">
                            <option value="">Select PR Document</option>
                            <?php
                            foreach($pr_document as $leave){
                                echo '<option value="'.$leave['myFileName'].'" data-value="'.$leave['myFileName'].'">'.$leave['myFileName'].' | '.$leave['attachmentDescription'].'</option>';
                            }
                            ?>
                        </select>
                    </td>

                    <td class="mailbox-name" style="min-width: 100px">
                        <div class="" id="old_<?php echo $key ?>">
                            <?php  if($val['url_doc'] == null){ ?>
                                <form id="srm_vendor_portal_attachment_uplode_form_<?php echo $key ?>" class="form-inline" enctype="multipart/form-data" method="post">
                                        
                                        <div class="form-group">
                                            <!-- <label for="attachmentDescription">Description</label> -->
                                            <input type="hidden" name="itemAutoID_doc" id="itemAutoID_doc" value="<?php echo $val['itemAutoID'] ?>">
                                            <input type="hidden" name="inquiryID_doc" id="inquiryID_doc" value="<?php echo $inquiryID ?>">
                                            <input type="hidden" value="<?php echo $key ?>" class ="doc_key">
                                        
                                        
                                        </div>
                                        <div class="form-group">
                                            <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                                style="margin-top: 8px;">
                                                <div class="form-control" data-trigger="fileinput"><i
                                                            class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                                            class="fileinput-filename set-w-file-name"></span></div>
                                                <span class="input-group-addon btn btn-default btn-file"><span
                                                            class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                                        aria-hidden="true"></span></span><span
                                                            class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                                            aria-hidden="true"></span></span><input
                                                            type="file" name="document_file" id="document_file"></span>
                                                <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                                    data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                                aria-hidden="true"></span></a>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-default" onclick="srm_rfq_document_upload_line_wise(<?php echo $key ?>,<?php echo $val['itemAutoID'] ?>)"><span
                                                    class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                                </form>
                            <?php }else{ ?>
                                <a class="" onclick="srm_rfq_document_delete_line_wise('<?php echo $val['line_doc_id']?>',<?php echo $key ?>)"><span
                                        class="glyphicon glyphicon-trash glyphicon-trash-btn color" aria-hidden="true"></span></a>&nbsp;&nbsp;
                                <a target="_blank" href="<?php echo $val['url_doc'] ?>" ><i class="fa fa-download fa-download-btn" aria-hidden="true"></i></a>
                            <?php } ?>
                        </div>

                        <div class="hide" id="not_submit_<?php echo $key ?>">
                           
                                <form id="srm_vendor_portal_attachment_uplode_form_<?php echo $key ?>" class="form-inline" enctype="multipart/form-data" method="post">
                                        
                                        <div class="form-group">
                                            <!-- <label for="attachmentDescription">Description</label> -->
                                            <input type="hidden" name="itemAutoID_doc" id="itemAutoID_doc" value="<?php echo $val['itemAutoID'] ?>">
                                            <input type="hidden" name="inquiryID_doc" id="inquiryID_doc" value="<?php echo $inquiryID ?>">
                                            <input type="hidden" value="<?php echo $key ?>" class ="doc_key">
                                        
                                        
                                        </div>
                                        <div class="form-group">
                                            <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                                style="margin-top: 8px;">
                                                <div class="form-control" data-trigger="fileinput"><i
                                                            class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                                            class="fileinput-filename set-w-file-name"></span></div>
                                                <span class="input-group-addon btn btn-default btn-file"><span
                                                            class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                                        aria-hidden="true"></span></span><span
                                                            class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                                            aria-hidden="true"></span></span><input
                                                            type="file" name="document_file" id="document_file"></span>
                                                <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                                    data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                                aria-hidden="true"></span></a>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-default" onclick="srm_rfq_document_upload_line_wise(<?php echo $key ?>,<?php echo $val['itemAutoID'] ?>)"><span
                                                    class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                                </form>
                         
                        </div>

                        <div class="" id="submit_<?php echo $key ?>">
                            
                        </div>
                    </td>
                    <td width="5%">

                        <div class="skin skin-square">
                            <div class="skin-section extraColumns"><input
                                        id="isAttended_<?php echo $val['itemAutoID'] ?>" type="checkbox" <?php echo $disabled ?>
                                        data-caption="" class="columnSelected isitem_checkbox"
                                        name="isActive" onclick="orderItem_selected_check(this)"
                                        value="<?php echo $val['itemAutoID']."_".$val['purchaseRequestID']."_".$val['purchaseRequestDetailsID'] ?>"><label for="checkbox">&nbsp;</label>
                            </div>
                        </div>
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
}else{

}
?>



<script type="text/javascript">
    $(document).ready(function () {

       /* $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });*/

        $('.isitem_checkbox').on('ifChecked', function (event) {
            orderItem_selected_check(this);
        });
        $('.isitem_checkbox').on('ifUnchecked', function (event) {
            orderItem_selected_check(this);
        });

        $('#orderItem_MasterCheck').on('ifChecked', function (event) {
            $('.isitem_checkbox').iCheck('check');
        });

        $('#orderItem_MasterCheck').on('ifUnchecked', function (event) {
            $('.isitem_checkbox').iCheck('uncheck');
        });

    });
</script>