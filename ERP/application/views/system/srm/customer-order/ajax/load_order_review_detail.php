<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$orderReviewOption= getPolicyValues('ORO', 'All');
?>
<style>
    .caption p {
        color: #999;
    }

    /* Carousel Control */
    .control-box {
        text-align: right;
        width: 99%;
    }

    .carousel-control {
        background: #666;
        border: 0px;
        border-radius: 0px;
        display: inline-block;
        font-size: 34px;
        font-weight: 200;
        line-height: 18px;
        opacity: 0.5;
        padding: 4px 10px 0px;
        position: static;
        height: 30px;
        width: 15px;
    }

    li {
        list-style-type: none;
    }

    p {
        margin: 0 0 0px;
    }
    .headrowtitle {
        color:#696CFF;
    }

    .tablethcol2 {
        background-color: #ececec;;
        color: black;
        border-bottom: 2px solid #ffffff;
    }

    /* #supplier_col th:nth-child(even),td:nth-child(even) {
     background-color: #D6EEEE;
    } */

</style>
<div class="set-poweredby">Powered by &nbsp;<a href=""><img src="https://ilooopssrm.rbdemo.live/images/logo-dark.png" width="75" alt="MaxSRM"></a></div>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('srm_order_review_header')?><!-- ORDER REVIEW HEADER--> </h2>
        </header>
    </div>
</div>



<?php

if($inquiry_master['templateType']== 1){
if (!empty($item)) {
    foreach ($item as $row) { ?>
        <div class="row">
            <div class="col-sm-2">
                <div class="fff">
                    <div class="thumbnail">
                        <img class="align-left" src="<?php echo $row['awsImage'] ?>"
                             alt="" width="60" height="60">
                    </div>
                    <div class="caption">
                        <h4><?php echo $row['itemName'] ?></h4>

                        <p><?php echo $row['itemSystemCode'] ?></p>

                        <p>QTY : <?php echo $row['requestedQty'] . " (" . $row['UnitShortCode'] . ")" ?></p>
                    </div>
                </div>
            </div>
            <?php
            $supplers = $this->db->query("SELECT *,supplierName,supplierImage from srp_erp_srm_orderinquirydetails JOIN srp_erp_srm_suppliermaster  ON srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID where inquiryMasterID = " . $row['inquiryMasterID'] . " AND itemAutoID = " . $row['itemAutoID'] . " AND isRfqCreated = 1 order by srp_erp_srm_orderinquirydetails.supplierQty DESC")->result_array();
            ?>
            <div class="col-sm-10">
                <div class="carousel slide" id="myCarousel_<?php echo $row['inquiryDetailID'] ?>">
                    <div class="carousel-inner">
                        <?php
                        if (!empty($supplers)) {
                            $y = 0;
                            $tot = 0;
                            $active = "active";
                            foreach ($supplers as $sup) {
                                if ($y == 0) {
                                    echo "<div class='item $active'>";
                                    echo "<ul class='thumbnails'>";
                                }
                                ?>
                                <li class="col-xs-12 col-sm-4">
                                    <div class="box-outline">
                                        <div class="caption">
                                            <h5 class="header-title-or"><?php echo $sup['supplierName']; ?></h5>
                                                                                 
                                            <div class="row">
                                                <label for="text" class="form-group col-sm-7 col-form-label fw-600">Requested QTY</label> 
                                                <div class="form-group  col-sm-5">                                                                                              
                                                    <label for="text" class="form-group fw-400 px-4"><?php echo $sup['requestedQty']; ?></label> 
                                                </div>
                                            </div> 
                                            <div class="row pt-0">
                                                <label for="text" class="form-group col-sm-7 col-form-label fw-600">QTY 
                                                    <?php if($sup['isSupplierSubmited']==1){ ?>
                                                        <button class="btn btn-xs dropdown-toggle btn-d" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="ti-angle-double-down fs-12"></span>
                                                        </button>
                                                        <div class="dropdown-menu set-dropdown-action" aria-labelledby="dropdownMenuButton">
                                                            <a class="btn btn-outline-info btn-circle btn-sm mrb-1" onclick="open_chat_model(<?php echo $sup['inquiryDetailID'] ?>,<?php echo $sup['inquiryMasterID']?>,<?php echo $sup['supplierID'] ?>,<?php echo $row['itemAutoID'] ?> ,1)"><span title="Chat" rel="tooltip" class="glyphicon glyphicon-comment" ></span></a>
                                                            <a class="btn btn-outline-warning btn-circle btn-sm" onclick="referback_rfq_entry_line_wise(<?php echo $sup['inquiryDetailID'] ?>,<?php echo $sup['inquiryMasterID']?>,<?php echo $sup['supplierID'] ?>,<?php echo $row['itemAutoID'] ?> ,1);"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat"></span></a>
                                                        </div>
                                                    
                                                    <?php } ?>
                                                </label> 
                                                <div class="form-group d-flex v-align col-sm-5">                                                                                              
                                                    <input class="form-control" type="number" id="qty_<?php echo $sup['inquiryDetailID']; ?>" name="qty" value="<?php echo ($sup['supplierQty']!='')?$sup['supplierQty']:0 ?>" autofocus placeholder="Qty" onchange="update_sup_qty(<?php echo $sup['inquiryDetailID'] ?>,this.value)" readonly>
                                                    <!-- <span title="" rel="tooltip" class="fa fa-info-circle fa-1x fa-fw fc-1 fs-16" onclick="open_history_orderrev(1,<?php echo $sup['inquiryDetailID']; ?>)" data-original-title="History" aria-describedby="tooltip32578"></span> -->
                                                </div>
                                            </div> 
                                            <div class="row pt-0">
                                                <label for="text" class="form-group col-sm-7 col-form-label fw-600">Unit Price (<?php echo $inquiry_master['CurrencyCode'] ?>)
                                                    <?php if($sup['isSupplierSubmited']==1){ ?>
                                                        <button class="btn btn-xs dropdown-toggle btn-d" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <span class="ti-angle-double-down fs-12"></span>
                                                        </button>
                                                        <div class="dropdown-menu set-dropdown-action" aria-labelledby="dropdownMenuButton">
                                                            <a class="btn btn-outline-info btn-circle btn-sm mrb-1" onclick="open_chat_model(<?php echo $sup['inquiryDetailID'] ?>,<?php echo $sup['inquiryMasterID']?>,<?php echo $sup['supplierID'] ?>,<?php echo $row['itemAutoID'] ?> ,2)"><span title="Chat" rel="tooltip" class="glyphicon glyphicon-comment" ></span></a>
                                                            <a class="btn btn-outline-warning btn-circle btn-sm" onclick="referback_rfq_entry_line_wise(<?php echo $sup['inquiryDetailID'] ?>,<?php echo $sup['inquiryMasterID']?>,<?php echo $sup['supplierID'] ?>,<?php echo $row['itemAutoID'] ?> ,2);"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" ></span></a>
                                                        </div>                                                        
                                                    <?php } ?>
                                                </label> 
                                                <div class="form-group d-flex v-align col-sm-5">                                                                                              
                                                    <input class="form-control" type="number" id="srmunitprice_up_<?php echo $sup['inquiryDetailID']; ?>" name="srmunitprice_up" value="<?php echo ($sup['supplierPrice']!='')?$sup['supplierPrice']:0 ?>" autofocus placeholder="Unit Price" onchange="update_sup_unitprice(<?php echo $sup['inquiryDetailID'] ?>,this.value)" readonly> 
                                                    <!-- <span title="" rel="tooltip" class="fa fa-info-circle fa-1x fa-fw fc-1 fs-16" onclick="open_history_orderrev(2,<?php echo $sup['inquiryDetailID']; ?>)" data-original-title="History" aria-describedby="tooltip32578"></span> -->
                                                </div>
                                            </div> 
                                            <div class="row total-or pt-0">
                                                <label for="text" class="form-group col-sm-7 col-form-label fw-600">Total (<?php echo $inquiry_master['CurrencyCode'] ?>)</label> 
                                                <div class="form-group  col-sm-5">                                                                                              
                                                    <label for="text" class="form-group fw-400 px-4">
                                                        <?php $tot = $sup['supplierQty'] * $sup['supplierPrice'];
                                                            echo "<span>" . number_format($tot, 2) . "</span>";
                                                        ?>
                                                    </label> 
                                                </div>
                                            </div>
                                            <?php if($sup['isSupplierSubmited']==1){ ?>                                            
                                            <div class="row pt-0">
                                                <label for="text" class="form-group col-sm-7 col-form-label fw-600">Technical Specification                                                
                                                </label> 
                                                <div class="form-group  col-sm-5">                                                                                              
                                                   <?php if($sup['supplierTechnicalSpecification'] ){ ?>
                                                    <button class="btn btn-xs dropdown-toggle btn-d" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <span class="ti-angle-double-down fs-12"></span>
                                                    </button>
                                                    <div class="dropdown-menu set-dropdown-action" aria-labelledby="dropdownMenuButton">
                                                        <a class="btn btn-outline-primary btn-circle btn-sm mrb-1" onclick="technicalSpecification_modal_open(<?php echo $sup['inquiryDetailID'] ?>)" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>
                                                        <a class="btn btn-outline-info btn-circle btn-sm mrb-1" onclick="open_chat_model(<?php echo $sup['inquiryDetailID'] ?>,<?php echo $sup['inquiryMasterID']?>,<?php echo $sup['supplierID'] ?>,<?php echo $row['itemAutoID'] ?> ,3)"><span title="Chat" rel="tooltip" class="glyphicon glyphicon-comment" ></span></a>
                                                        <a class="btn btn-outline-warning btn-circle btn-sm" onclick="referback_rfq_entry_line_wise(<?php echo $sup['inquiryDetailID'] ?>,<?php echo $sup['inquiryMasterID']?>,<?php echo $sup['supplierID'] ?>,<?php echo $row['itemAutoID'] ?> ,3);"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat"></span></a>
                                                    </div>
                                                   
                                                    <?php }else{ ?>
                                                        -
                                                        <?php } ?>
                                                </div>
                                            </div>
                                            
                                            <div class="row pt-0">
                                                <label for="text" class="form-group col-sm-7 col-form-label fw-600">Attachments</label> 
                                                <div class="form-group  col-sm-5">                                                                                              
                                                    <button class="btn btn-xs dropdown-toggle btn-d" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <span class="ti-angle-double-down fs-12"></span>
                                                    </button>
                                                    <div class="dropdown-menu set-dropdown-action" aria-labelledby="dropdownMenuButton">
                                                        <a class="btn btn-outline-primary btn-circle btn-sm mrb-1" onclick="open_attachment_model_supplier_view(<?php echo $sup['inquiryMasterID']?>,<?php echo $sup['supplierID'] ?>)" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>
                                                        <a class="btn btn-outline-info btn-circle btn-sm mrb-1" onclick="open_chat_model(<?php echo $sup['inquiryMasterID']?>,<?php echo $sup['supplierID'] ?>,4)"><span title="Chat" rel="tooltip" class="glyphicon glyphicon-comment" ></span></a>
                                                    </div>
                                                    
                                                </div>
                                            </div>

                                            <div class="row pt-0">
                                                <label for="text" class="form-group col-sm-7 col-form-label fw-600">Terms & Condition</label> 
                                                <div class="form-group  col-sm-5">                                                                                              
                                                    <button class="btn btn-xs dropdown-toggle btn-d" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <span class="ti-angle-double-down fs-12"></span>
                                                    </button>
                                                    <div class="dropdown-menu set-dropdown-action" aria-labelledby="dropdownMenuButton">
                                                        <a class="btn btn-outline-primary btn-circle btn-sm mrb-1" onclick="open_terms_model_supplier_view(<?php echo $sup['inquiryMasterID']?>,<?php echo $sup['supplierID'] ?>)" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>                                                   
                                                        <a class="btn btn-outline-info btn-circle btn-sm mrb-1" onclick="open_chat_model_open_req(<?php echo $sup['inquiryMasterID']?>,<?php echo $sup['supplierID'] ?>,5)"><span title="Chat" rel="tooltip" class="glyphicon glyphicon-comment" ></span></a>
                                                        <a class="btn btn-outline-warning btn-circle btn-sm" onclick="referback_rfq_entry(<?php echo $sup['inquiryMasterID']?>,<?php echo $sup['supplierID'] ?>,5);"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" ></span></a>
                                                    </div>                                                    
                                                </div>
                                            </div>
                                            

                                            <?php } ?>
                                            <?php

                                            if($sup['supplierQty']>0){
                                                ?>
                                                 <?php
                                                $pending_reply = $this->db->query("SELECT * from srp_erp_srm_vendor_request_responces  where inquiryDetailID = " . $sup['inquiryDetailID'] . " AND inquiryMasterID = " . $sup['inquiryMasterID'] . " AND itemAutoID=".$sup['itemAutoID']." AND companyID=".$sup['companyID']." AND	supplierID=".$sup['supplierID']." AND isVendorSubmited=1 ")->result_array();
                                               
                                                
                                                ?>
                                                <?php if($sup['isSelected']==1) { ?>
                                                    <div class="skin skin-square position-top-right">
                                                        <div class="skin-section extraColumns"><input
                                                                    id="isSupplier_<?php echo $sup['supplierID'] ?>"
                                                                    type="checkbox"
                                                                <?php if(!empty($supplierIDarr)){if (in_array($sup['supplierID'].'_'.$sup['itemAutoID'], $supplierIDarr)) {
                                                                    echo "checked";
                                                                } }?>
                                                                    data-caption="" class="columnSelected supplier_checkbox"
                                                                    name="isSuppliers"
                                                                    onclick="orderItem_selected_check_supplier_base(this)"
                                                                    value="<?php echo $sup['itemAutoID'] . "_" . $sup['supplierID'] . "_" . $sup['inquiryDetailID'] ?>"><label
                                                                    for="checkbox">&nbsp;</label>
                                                        </div>
                                                    </div>
                                                    <label class="fc-3"> Submitted</label>
                                                <?php }else{ ?>
                                                    <label class="fc-3"> Item Not Selected(PR Price Loaded)</label>
                                                <?php } ?>
                                                <div class="form-group row">
                                                    <div class="col-sm-12 text-right">
                                                        <!-- <?php if(count( $pending_reply)>0){?>
                                                            <button class="btn btn-primary btn-sm" onclick="open_refer_model(<?php echo $sup['inquiryDetailID']?>,<?php echo$sup['inquiryMasterID']?>,<?php echo$sup['supplierID']?>,<?php echo $sup['companyID']?>,<?php echo $sup['itemAutoID']?>)">Message<span class="badge-1 badge-pill" style="background-color:#f1416c;" id="totalapprovalcount"><?php  echo count($pending_reply)?></span></button>
                                                        <?php }else{?>
                                                            <button class="btn btn-primary btn-sm" onclick="open_refer_model(<?php echo $sup['inquiryDetailID']?>,<?php echo$sup['inquiryMasterID']?>,<?php echo$sup['supplierID']?>,<?php echo $sup['companyID']?>,<?php echo $sup['itemAutoID']?>)">Message</button>
                                                        <?php } ?> -->
                                                    </div>
                                                </div>

                                                

                                                <?php
                                            }else{
                                                ?>
                                                <label class="fc-2"> Not Submitted</label>
                                                <?php
                                            }
                                            ?>

                                        </div>
                                    </div>
                                </li>
                                <?php
                                $y++;
                                if ($y == 6) {
                                    $active = '';
                                    echo " </ul>";
                                    echo "</div>";
                                    $y = 0;
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="row" style="margin-top: 10px">
            <div class="col-sm-12">
                <nav>
                    <ul class="control-box pager">
                        <li><a data-slide="prev" href="#myCarousel_<?php echo $row['inquiryDetailID'] ?>" class=""><i
                                    class="glyphicon glyphicon-chevron-left"></i></a></li>
                        <li><a data-slide="next" href="#myCarousel_<?php echo $row['inquiryDetailID'] ?>" class=""><i
                                    class="glyphicon glyphicon-chevron-right"></i></a></li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php
    }
    ?>



    <div class="row">
        <div class="col-sm-12">
            <div class="text-right m-t-xs">
                <!--<button class="btn btn-primary" onclick="generate_review_supplier()">Generate PO</button>-->
                <button class="btn btn-primary-new size-lg" onclick="confirm_order_review()">Confirm</button>
            </div>
        </div>
    </div>
    <?php
}
}else{
    if(!empty($item_supplier)){ ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped order-review-style-tbl">
                    <tbody>
                        <tr>
                            <?php
                              echo "<td class='headrowtitle text-center' colspan='".(11+ (count($supplier_Data)-1) *6)."' style='border-bottom: 1px solid transparent;border-top: 1px solid transparent;'>COMPARATIVE STATEMENT</td>";
                            ?>
                            <td class="headrowtitle text-center" rowspan="3" style="border-bottom: 1px solid transparent;border-top: 1px solid transparent;">Logo</td>
                        </tr>
                        <tr>
                            <td class="" colspan="4"  style="border-bottom: 1px solid transparent">PR No: <span><?php echo $inquiry_master['documentCode'] ?></span></td>
                            <?php
                              echo "<td class='' colspan='".(4+ (((count($supplier_Data)-1)*6)/2))."' style='border-bottom: 1px solid transparent'>Buyer: <span>".$inquiry_master['createdUserName']." </span></td>";
                              echo "<td class='' colspan='".(3+ (((count($supplier_Data)-1)*6)/2))."' style='border-bottom: 1px solid transparent'>Date: </td>";
                            ?>
                            
                        </tr>
                        <tr>
                            <td class="" colspan="4" style="border-bottom: 1px solid transparent">Job Code: </td>
                           
                            <?php
                              echo "<td class='' colspan='".(4+ (((count($supplier_Data)-1)*6)/2))."' style='border-bottom: 1px solid transparent'>Project: </td>";
                              echo "<td class='' colspan='".(3+ (((count($supplier_Data)-1)*6)/2))."' style='border-bottom: 1px solid transparent'>Rev: </td>";
                            ?>
                            
                        </tr>

                        <tr id="supplier_col" class="tbl-bg-1">
                            <td class="text-center" rowspan="2" style="border-bottom: 1px solid transparent">SI No. </td>
                            <td class="text-center" rowspan="2" style="border-bottom: 1px solid transparent">Material Description: </td>
                            <td class="text-center" rowspan="2" style="border-bottom: 1px solid transparent">Qty. </td>
                            <td class="text-center" rowspan="2" style="border-bottom: 1px solid transparent">UOM. </td>
                            <td class="text-center" colspan="2" style="border-bottom: 1px solid transparent">BUDGET (PR Price)</td>

                            <?php foreach($supplier_Data as $val) { 
                                                    
                            ?>
                            <td class="text-center supplier-col" colspan="6" style="border-bottom: 1px solid transparent; <?php if($val['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>"><?php echo $val['supplierName'] ?> 
                                <div class="d-flex justify-content-center align-item-center">
                                    <!-- <div class="skin-section extraColumns pt-5"><input
                                                id="isSupplier_<?php echo $val['supplierID'] ?>"
                                                type="checkbox"
                                                data-caption="" class="columnSelected supplier_base_checkbox"
                                                name="isSuppliers"
                                                onclick="orderItem_selected_check(this)"
                                                value="<?php echo $val['supplierID'] . "_" . $val['inquiryMasterID'] ?>"><label
                                                for="checkbox">&nbsp;</label>
                                    </div> -->
                                    <!-- &nbsp;&nbsp;<button class="btn btn-primary-new size-xs" onclick="open_refer_model_supplier_view(<?php echo $val['inquiryMasterID']?>,<?php echo$val['supplierID']?>)"><i class="fa fa-envelope" aria-hidden="true"></i></button> -->
                                    &nbsp;&nbsp;<a  onclick="open_attachment_model_supplier_view(<?php echo $val['inquiryMasterID']?>,<?php echo$val['supplierID']?>)"><span title="Attachments" rel="tooltip" class="glyphicon glyphicon-paperclip" ></span></a>
                                    &nbsp;&nbsp;<a onclick="open_terms_model_supplier_view(<?php echo $val['inquiryMasterID']?>,<?php echo$val['supplierID']?>)"><span title="Terms & Condition" rel="tooltip" class="glyphicon glyphicon-list-alt" ></span></a>
                                </div>
                                
                            </td>
                            <?php } ?>
                            
                        </tr>

                        <tr class="tbl-bg-2">
                            <td class="" style="border-bottom: 1px solid #eee">Unit Price</td>
                            <td class="" style="border-bottom: 1px solid #eee">Total Price </td>

                            <?php foreach($supplier_Data as $val) { ?>
                            <td class="" style="border-bottom: 1px solid #eee;<?php if($val['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>">Unit Price </td>
                            <td class="" style="border-bottom: 1px solid #eee;<?php if($val['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>">Qty </td>

                            <td class="" style="border-bottom: 1px solid #eee;<?php if($val['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>">Discount</td>
                            <td class="" style="border-bottom: 1px solid #eee;<?php if($val['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>">Tax </td>
                            <td class="" style="border-bottom: 1px solid #eee;<?php if($val['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>">Technical Spec</td>
                            <td class="" style="border-bottom: 1px solid #eee;<?php if($val['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>">Total Price</td>

                           

                            <?php } ?>
                            
                        </tr>

                        <?php                             
                            foreach($item_supplier as $key=>$val) { 
                            $item_a = $this->db->query("SELECT srp_erp_purchaserequestdetails.*,srp_erp_srm_orderinquirymaster.transactionCurrencyID from srp_erp_srm_orderinquirymaster JOIN srp_erp_purchaserequestdetails  ON srp_erp_purchaserequestdetails.purchaseRequestID = srp_erp_srm_orderinquirymaster.purchaseRequestID where srp_erp_srm_orderinquirymaster.inquiryID = " . $inquiry_master['inquiryID'] . " AND srp_erp_purchaserequestdetails.itemAutoID = " . $val['itemAutoID'] . " AND srp_erp_purchaserequestdetails.purchaseRequestID = " . $inquiry_master['purchaseRequestID'] . "")->row_array();                         
                        ?>

                        <tr class="fw-500">
                            <td class="" style="border-bottom: 1px solid #eee"> <?php echo $key+1 ?> </td>
                            <td class="" style="border-bottom: 1px solid #eee "> <?php echo $val['itemName'] ?>
                            <span title="" rel="tooltip" class="fa fa-info-circle fa-1x fa-fw fc-1 fs-16" onclick="open_item_history_details(<?php echo $item_a['transactionCurrencyID']; ?>,<?php echo $val['itemAutoID']; ?>,'<?php echo $inquiry_master['CurrencyCode'];?>')" data-original-title="Last PO Price" aria-describedby="tooltip32578"></span>
                            </td>
                            <td class="text-right" style="border-bottom: 1px solid #eee "> <?php echo  number_format($item_a['requestedQty'],2) ?> </td>
                            <td class="text-right" style="border-bottom: 1px solid #eee "> <?php echo $val['defaultUnitOfMeasure'] ?> </td>
                            <td class="text-right" style="border-bottom: 1px solid #eee "> <?php echo number_format($item_a['unitAmount'],2) ?> </td>
                            <td class="text-right" style="border-bottom: 1px solid #eee "> <?php echo number_format($item_a['totalAmount'],2)?> </td>
                            <?php 
                                $k=0; 
                                $bg=5;
                                foreach($supplier_Data as $val1) { 

                                $submit_value = $this->db->query("SELECT inquiryDetailID,inquiryMasterID,supplierID,supplierQty,supplierPrice,supplierDiscount,supplierTax,supplierOtherCharge,supplierTaxPercentage,supplierDiscountPercentage,isSelected from srp_erp_srm_orderinquirydetails  where srp_erp_srm_orderinquirydetails.supplierID = " . $val1['supplierID'] . " AND srp_erp_srm_orderinquirydetails.itemAutoID = " . $val['itemAutoID'] . " AND srp_erp_srm_orderinquirydetails.isSupplierSubmited =  1 AND srp_erp_srm_orderinquirydetails.inquiryMasterID =".$val['inquiryMasterID']."")->row_array();
                                
                            ?>

                            <?php if($submit_value){ ?>
                                <td class="text-right" style="border-bottom: 1px solid #eee;background: rgb(138 98 124 / <?php echo $bg; ?>%);<?php if($submit_value['isSelected']==0){ ?>background: #f29a7f !important <?php } ?>;<?php if($val1['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>">
                                    <div class="dropdown">
                                        <button class="btn btn-xs dropdown-toggle btn-d" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php echo number_format($submit_value['supplierPrice'],2) ?>
                                        </button>
                                        <div class="dropdown-menu set-dropdown-action" aria-labelledby="dropdownMenuButton">
                                            <a class="btn btn-outline-info btn-circle btn-sm fs-12 mrb-1" onclick="open_chat_model(<?php echo $submit_value['inquiryDetailID'] ?>,<?php echo $submit_value['inquiryMasterID']?>,<?php echo $submit_value['supplierID'] ?>,<?php echo $val['itemAutoID'] ?> ,2)"><span title="Chat" rel="tooltip" class="glyphicon glyphicon-comment fs-12" ></span></a>
                                            <a class="btn btn-outline-warning btn-circle btn-sm fs-12" onclick="referback_rfq_entry_line_wise(<?php echo $submit_value['inquiryDetailID'] ?>,<?php echo $submit_value['inquiryMasterID']?>,<?php echo $submit_value['supplierID'] ?>,<?php echo $val['itemAutoID'] ?> ,2);"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" ></span></a>
                                        </div>
                                    </div>
                                    
                                </td>
                                <td class="text-right" style="border-bottom: 1px solid #eee;background: rgb(138 98 124 / <?php echo $bg; ?>%);<?php if($submit_value['isSelected']==0){ ?>background: #f29a7f !important <?php } ?>;<?php if($val1['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>"> 
                                    <div class="dropdown">
                                        <button class="btn btn-xs dropdown-toggle btn-d" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php echo number_format($submit_value['supplierQty'],2)  ?>
                                        </button>
                                        <div class="dropdown-menu set-dropdown-action" aria-labelledby="dropdownMenuButton">
                                            <a class="btn btn-outline-info btn-circle btn-sm fs-12 mrb-1" onclick="open_chat_model(<?php echo $submit_value['inquiryDetailID'] ?>,<?php echo $submit_value['inquiryMasterID']?>,<?php echo $submit_value['supplierID'] ?>,<?php echo $val['itemAutoID'] ?> ,1)"><span title="Chat" rel="tooltip" class="glyphicon glyphicon-comment fs-12" ></span></a>
                                            <a class="btn btn-outline-warning btn-circle btn-sm fs-12" onclick="referback_rfq_entry_line_wise(<?php echo $submit_value['inquiryDetailID'] ?>,<?php echo $submit_value['inquiryMasterID']?>,<?php echo $submit_value['supplierID'] ?>,<?php echo $val['itemAutoID'] ?> ,1);"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat"></span></a>
                                        </div>
                                    </div>                                    
                                </td>
                                
                                <td class="text-right" style="border-bottom: 1px solid #eee;background: rgb(138 98 124 / <?php echo $bg; ?>%);<?php if($submit_value['isSelected']==0){ ?>background: #f29a7f !important <?php } ?>;<?php if($val1['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>"> <?php echo number_format($submit_value['supplierDiscount'],2) ?> (<?php echo $submit_value['supplierDiscountPercentage'] ?>%)</td>
                                <td class="text-right" style="border-bottom: 1px solid #eee;background: rgb(138 98 124 / <?php echo $bg; ?>%);<?php if($submit_value['isSelected']==0){ ?>background: #f29a7f !important <?php } ?>;<?php if($val1['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>"> <?php echo number_format($submit_value['supplierTax'],2) ?> (<?php echo $submit_value['supplierTaxPercentage'] ?>%)</td>
                                <td class="text-center" style="border-bottom: 1px solid #eee;background: rgb(138 98 124 / <?php echo $bg; ?>%);<?php if($submit_value['isSelected']==0){ ?>background: #f29a7f !important <?php } ?>;<?php if($val1['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>">
                                    <div class="or-set-group">
                                        <div class="dropdown mr-1">
                                            <button class="btn btn-xs dropdown-toggle btn-d" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="ti-angle-double-down fs-12"></span>
                                            </button>
                                            <div class="dropdown-menu set-dropdown-action" aria-labelledby="dropdownMenuButton">
                                                <a class="btn btn-outline-primary btn-circle btn-sm fs-12 mrb-1" onclick="technicalSpecification_modal_open(<?php echo $submit_value['inquiryDetailID'] ?>)" ><span title="View Technical Specification" rel="tooltip" class="glyphicon glyphicon-eye-open" ></span></a> 
                                                <a class="btn btn-outline-info btn-circle btn-sm fs-12 mrb-1" onclick="open_chat_model(<?php echo $submit_value['inquiryDetailID'] ?>,<?php echo $submit_value['inquiryMasterID']?>,<?php echo $submit_value['supplierID'] ?>,<?php echo $val['itemAutoID'] ?> ,3)"><span title="Chat" rel="tooltip" class="glyphicon glyphicon-comment fs-12" ></span></a>
                                                <a class="btn btn-outline-warning btn-circle btn-sm fs-12" onclick="referback_rfq_entry_line_wise(<?php echo $submit_value['inquiryDetailID'] ?>,<?php echo $submit_value['inquiryMasterID']?>,<?php echo $submit_value['supplierID'] ?>,<?php echo $val['itemAutoID'] ?> ,3);"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat"></span></a>
                                            </div>
                                        </div>
                                        <?php if($submit_value['isSelected']==1) { ?>
                                        <div class="skin skin-square ">
                                            <div class="skin-section extraColumns"><input
                                                        id="isSupplier_<?php echo $submit_value['supplierID'] ?>_<?php echo $submit_value['inquiryDetailID'] ?>"
                                                        type="checkbox"
                                                    <?php if(!empty($supplierIDarr)){if (in_array($submit_value['supplierID'].'_'.$val['itemAutoID'], $supplierIDarr)) {
                                                        echo "checked";
                                                    } }?>
                                                        data-caption="" class="columnSelected supplier_checkbox"
                                                        name="isSuppliers"
                                                        onclick="orderItem_selected_check_supplier_base(this)"
                                                        value="<?php echo $val['itemAutoID'] . "_" . $submit_value['supplierID'] . "_" . $submit_value['inquiryDetailID'] ?>"><label
                                                        for="checkbox">&nbsp;</label>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>                                
                                </td>
                                <td class="text-right" style="border-bottom: 1px solid #eee;background: rgb(138 98 124 / <?php echo $bg; ?>%);<?php if($submit_value['isSelected']==0){ ?>background: #f29a7f !important <?php } ?>;<?php if($val1['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>"> <?php echo number_format($submit_value['supplierPrice']*$submit_value['supplierQty'],2) ?> </td>
                            <?php }else{ ?>
                                <td class="text-center" style="border-bottom: 1px solid #eee " colspan="6">-- </td>
                            <?php } ?>
                            
                            <?php 
                                $bg = $bg+6;
                            }                             
                            ?>

                            
                        </tr>
                            <?php $k++;                             
                        }   ?>
                        <tr class="fw-500">
                            <td class="" style="border-bottom: 1px solid #eee ">  </td>
                            <td class="" style="border-bottom: 1px solid #eee " colspan="3"> Total Amount (<?php echo $inquiry_master['CurrencyCode'] ?>)</td>
                            

                            <td class="" colspan="2" style="border-bottom: 1px solid #eee ">  </td>
                            <?php foreach($supplier_Data as $val) {
                                $total_val = $this->db->query("SELECT supplierQty,supplierPrice from srp_erp_srm_orderinquirydetails  where srp_erp_srm_orderinquirydetails.supplierID = " . $val['supplierID'] . " AND srp_erp_srm_orderinquirydetails.isSupplierSubmited =  1 AND srp_erp_srm_orderinquirydetails.inquiryMasterID =".$val['inquiryMasterID']."")->result_array();

                                $sum=0;

                                foreach($total_val as $tot){
                                    $sum =$sum+ ($tot['supplierPrice']*$tot['supplierQty']);
                                }
                                
                                ?>
                                <td class="text-right" colspan="6" style="border-bottom: 1px solid #eee ;<?php if($val['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>"> <?php echo number_format($sum,2) ?> </td>
                            <?php } ?>
                          
                        </tr>

                        <tr class="fw-500">
                            <td class="" style="border-bottom: 1px solid #eee ">  </td>
                            <td class="" style="border-bottom: 1px solid #eee " colspan="3"> TAX (<?php echo $inquiry_master['CurrencyCode'] ?>)</td>
                            

                            <td class="" colspan="2" style="border-bottom: 1px solid #eee ">  </td>
                            <?php foreach($supplier_Data as $val) { 
                                
                                $total_tax = $this->db->query("SELECT supplierQty,supplierPrice,supplierTax,supplierOtherCharge from srp_erp_srm_orderinquirydetails  where srp_erp_srm_orderinquirydetails.supplierID = " . $val['supplierID'] . " AND srp_erp_srm_orderinquirydetails.isSupplierSubmited =  1 AND srp_erp_srm_orderinquirydetails.inquiryMasterID =".$val['inquiryMasterID']."")->result_array();
                            
                                $tax=0;

                                foreach($total_tax as $tot){
                                    $tax =$tax+ ($tot['supplierTax']);
                                }
                            ?>
                                <td class="text-right" colspan="6" style="border-bottom: 1px solid #eee ;<?php if($val['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>"> <?php echo number_format($tax,2) ?>  </td>
                            <?php } ?>
                
                        </tr>

                        <!-- <tr class="fw-500">
                            <td class="" style="border-bottom: 1px solid #eee">  </td>
                            <td class="" style="border-bottom: 1px solid #eee "> Other Charges </td>
                            <td class="" style="border-bottom: 1px solid #eee ">  </td>
                            <td class="" style="border-bottom: 1px solid #eee ">  </td>

                            <td class="" colspan="2" style="border-bottom: 1px solid #eee ">  </td>
                            <?php foreach($supplier_Data as $val) { 
                                
                                $total_tax = $this->db->query("SELECT supplierQty,supplierPrice,supplierTax,supplierOtherCharge from srp_erp_srm_orderinquirydetails  where srp_erp_srm_orderinquirydetails.supplierID = " . $val['supplierID'] . " AND srp_erp_srm_orderinquirydetails.isSupplierSubmited =  1 AND srp_erp_srm_orderinquirydetails.inquiryMasterID =".$val['inquiryMasterID']."")->result_array();
                            
                                $tax=0;

                                foreach($total_tax as $tot){
                                    $tax =$tax+ ($tot['supplierOtherCharge']);
                                }
                            ?>
                                <td class="text-right" colspan="6" style="border-bottom: 1px solid #eee "> <?php echo number_format($tax,2) ?> </td>
                            <?php } ?>
                
                        </tr> -->

                        <tr class="fw-500">
                            <td class="" style="border-bottom: 1px solid #eee">  </td>
                            <td class="" style="border-bottom: 1px solid #eee" colspan="3"> Discount (<?php echo $inquiry_master['CurrencyCode'] ?>)</td>
                           

                            <td class="" colspan="2" style="border-bottom: 1px solid #eee ">  </td>
                            <?php foreach($supplier_Data as $val) { 
                                
                                $total_tax = $this->db->query("SELECT supplierQty,supplierPrice,supplierTax,supplierOtherCharge,supplierDiscount from srp_erp_srm_orderinquirydetails  where srp_erp_srm_orderinquirydetails.supplierID = " . $val['supplierID'] . " AND srp_erp_srm_orderinquirydetails.isSupplierSubmited =  1 AND srp_erp_srm_orderinquirydetails.inquiryMasterID =".$val['inquiryMasterID']."")->result_array();
                            
                                $supplierDiscount=0;

                                foreach($total_tax as $tot){
                                    $supplierDiscount =$supplierDiscount+ $tot['supplierDiscount'];
                                }    
                            ?>
                            <td class="text-right" colspan="6" style="border-bottom: 1px solid #eee ;<?php if($val['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>"> <?php echo number_format($supplierDiscount,2) ?> </td>
                            <?php } ?>
                           
                        </tr>

                        <tr class="fw-500">
                            <td class="" style="border-bottom: 1px solid #eee ">  </td>
                            <td class="" style="border-bottom: 1px solid #eee " colspan="3"> Negotiated Final Amount (<?php echo $inquiry_master['CurrencyCode'] ?>)</td>
                            

                            <td class="" colspan="2" style="border-bottom: 1px solid #eee ">  </td>
                            <?php foreach($supplier_Data as $val) { 
                            
                                $total_amount = $this->db->query("SELECT supplierQty,supplierPrice,supplierTax,supplierOtherCharge,supplierDiscount from srp_erp_srm_orderinquirydetails  where srp_erp_srm_orderinquirydetails.supplierID = " . $val['supplierID'] . " AND srp_erp_srm_orderinquirydetails.isSupplierSubmited =  1 AND srp_erp_srm_orderinquirydetails.inquiryMasterID =".$val['inquiryMasterID']."")->result_array();
                                
                                $lastAmount=0;
                                $sum=0;
                                $tax=0;
                                $supplierDiscount=0;

                                foreach($total_amount as $tot){
                                    $sum =$sum+ ($tot['supplierPrice']*$tot['supplierQty']);
                                    $tax =$tax+ ($tot['supplierTax']+$tot['supplierOtherCharge']);
                                    $supplierDiscount =$supplierDiscount+ $tot['supplierDiscount'];

                                }  

                                $lastAmount=($sum+$tax)-$supplierDiscount;
                            ?>
                            <td class="text-right" colspan="6" style="border-bottom: 1px solid #eee ;<?php if($val['isMin']==1){ ?>background: #0cd0a2 !important <?php } ?>"> <?php echo number_format($lastAmount,2) ?> </td>
                            <?php } ?>
                            
                        </tr>

                        <!-- <tr>
                            <td class="" style="border-bottom: 1px solid "> </td>
                            <td class="" style="border-bottom: 1px solid "> Lowest Quote </td>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid ">  </td>

                            <td class="" colspan="2" style="border-bottom: 1px solid ">  </td>
                            <?php foreach($supplier_Data as $val) { ?>
                            <td class="" colspan="6" style="border-bottom: 1px solid ">  </td>
                            <?php } ?>
                            
                        </tr>

                        <tr>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid "> Payment Terms </td>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid ">  </td>

                            <td class="" colspan="2" style="border-bottom: 1px solid "> </td>
                            <?php foreach($supplier_Data as $val) { ?>
                            <td class="" colspan="6" style="border-bottom: 1px solid ">  </td>
                            <?php } ?>
                            
                        </tr>

                        <tr>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid "> Price Basis</td>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid ">  </td>

                            <td class="" colspan="2" style="border-bottom: 1px solid ">  </td>
                            <?php foreach($supplier_Data as $val) { ?>
                            <td class="" colspan="6" style="border-bottom: 1px solid ">  </td>
                            <?php } ?>
                            
                        </tr>

                        <tr>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid "> Delivery Time </td>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid ">  </td>

                            <td class="" colspan="2" style="border-bottom: 1px solid ">  </td>
                            <?php foreach($supplier_Data as $val) { ?>
                            <td class="" colspan="6" style="border-bottom: 1px solid ">  </td>
                            <?php } ?>
                            
                        </tr>

                        <tr>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid "> Make, Country of Origin </td>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid ">  </td>

                            <td class="" colspan="2" style="border-bottom: 1px solid ">  </td>
                            <?php foreach($supplier_Data as $val) { ?>
                            <td class="" colspan="6" style="border-bottom: 1px solid ">  </td>
                            <?php } ?>
                           
                        </tr>

                        <tr>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid "> INCOTERMS </td>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid ">  </td>

                            <td class="" colspan="2" style="border-bottom: 1px solid ">  </td>
                            <?php foreach($supplier_Data as $val) { ?>
                            <td class="" colspan="6" style="border-bottom: 1px solid "> </td>
                            <?php } ?>
                            
                        </tr>

                        <tr>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid "> Contact Person </td>
                            <td class="" style="border-bottom: 1px solid ">  </td>
                            <td class="" style="border-bottom: 1px solid "> </td>
                            <td class="" colspan="2" style="border-bottom: 1px solid ">  </td>
                            <?php foreach($supplier_Data as $val) { ?>
                            <td class="" colspan="6" style="border-bottom: 1px solid "> </td>
                            <?php } ?>
                            
                        </tr>

                        <tr>
                            <td class="" colspan="3" style="border-bottom: 1px solid ">  </td>
                            <?php echo "<td class='' rowspan='2' colspan='".(4+ (count($supplier_Data)-1) *6)."' style='border-bottom: 1px solid'>Note: </td>";?>
                            <td class="" colspan="5" style="border-bottom: 1px solid ">  </td>
                            
                        </tr>

                        <tr>
                            <td class="" colspan="3" style="border-bottom: 1px solid "> Prepared By (Buyer) </td>
                            
                            <td class="" colspan="5" style="border-bottom: 1px solid "> Procurement Manager </td>
                            
                        </tr> -->

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="text-right m-t-xs">
                <!--<button class="btn btn-primary" onclick="generate_review_supplier()">Generate PO</button>-->
                <button class="btn btn-primary-new size-lg" onclick="confirm_order_review()">Confirm</button>
            </div>
        </div>
    </div>

    <?php }}?>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee"
         id="order_review_management_model">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="order_review_history">History  </h4>
                </div>
                <div class="modal-body">

                    <div id="sysnc">
                        <div class="table-responsive">
                            <table id="order_review_tbl" class="table table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th style="width: 5%;text-align: left;">#</th>
                                    <th style="width: 12%;text-align: left;">Previous Value</abbr></th>
                                    <th style="width: 12%;text-align: left;">Changed Value</abbr></th>
                                    <th style="width: 12%;text-align: left;">Changed By</th>
                                    <th style="width: 12%;text-align: left;">Changed Date</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee" id="refer_model">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="order_review_history"> Message History </h4>
                </div>
                <div class="modal-body">
                   
                    <div class="row">
                        <form role="form" id="refer_detail_form" class="form-horizontal">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                        <h2>Add Message<!-- ORDER REVIEW HEADER--> </h2>
                                    </header>
                                </div>
                            </div>
                            <input type="hidden" name="inquiryDetailID" id="inquiryDetailID">
                            <input type="hidden" name="inquiryMasterID" id="inquiryMasterID">
                            <input type="hidden" name="supplierID" id="supplierID">
                            <input type="hidden" name="itemAutoID" id="itemAutoID">
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-2">
                                    <label class="title">Type<!--Inquiry ID--> </label>
                                </div>
                                <div class="form-group col-sm-4">
                                    <?php echo form_dropdown('referType', '', '', 'class="form-control select2" id="refer_type" onchange=""');
                                    ?>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label class="title"> Comment<!--Narration--> </label>
                                </div>
                                <div class="form-group col-sm-4">
                                    <input type="text" name="comment" id="comment" class="form-control">
                                </div>
                            </div>
                           
                            <div class="row Analysebtn" style="margin-top: 10px;">
                                <div class="form-group col-sm-12">
                                    <div class="text-right m-t-xs">
                                        <button class="btn btn-primary-new size-sm " type="button" onclick="orderReferSubmit()">Generate</button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                        <h2>Refer History<!-- ORDER REVIEW HEADER--> </h2>
                                    </header>
                                </div>
                            </div>

                        </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table id="refer_table" class="<?php echo table_class(); ?>">
                            <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="min-width: 10%">Type</th>
                                    <th style="min-width: 20%">Comment</th>
                                    <th style="min-width: 20%">Send Datetime</th>
                                    <th style="min-width: 20%">Received Date</th>
                                    <th style="min-width: 20%">Vendor Comment</th>
                                    <th style="min-width: 11%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee" id="refer_model_supplier">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="order_review_history"> Message History </h4>
                </div>
                <div class="modal-body">
                   
                    <div class="row">
                        <form role="form" id="refer_detail_form_supplier" class="form-horizontal">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                        <h2>Add Message<!-- ORDER REVIEW HEADER--> </h2>
                                    </header>
                                </div>
                            </div>
                          
                            <input type="hidden" name="inquiryMasterIDSupplier" id="inquiryMasterIDSupplier">
                            <input type="hidden" name="supplierIDSupplier" id="supplierIDSupplier">
                           
                            <div class="row" style="margin-top: 10px;">
                                <div class="form-group col-sm-2">
                                    <label class="title">Type<!--Inquiry ID--> </label>
                                </div>
                                <div class="form-group col-sm-4">
                                    <?php echo form_dropdown('referTypeSupplier', '', '', 'class="form-control select2" id="refer_type_supplier" onchange=""');
                                    ?>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label class="title"> Comment<!--Narration--> </label>
                                </div>
                                <div class="form-group col-sm-4">
                                    <input type="text" name="commentSupplier" id="commentSupplier" class="form-control">
                                </div>

                                <div class="form-group col-sm-2">
                                    <label class="title"> Item<!--Narration--> </label>
                                </div>
                                <div class="form-group col-sm-4">
                                   
                                    <!-- <select name="supplierSelectItem" class="form-control" id="supplierSelectItem">
                                    
                                        <option value="1">Item Base</option>
                                        <option value="2">Supplier Base</option>
                                    </select> -->
                                    <?php echo form_dropdown('supplierSelectItem', '', '', 'class="form-control select2" id="supplierSelectItem" onchange=""');
                                    ?>
                                </div>
                            </div>
                           
                            <div class="row Analysebtn" style="margin-top: 10px;">
                                <div class="form-group col-sm-12">
                                    <div class="text-right m-t-xs">
                                        <button class="btn btn-primary-new size-sm " type="button" onclick="orderReferSubmitSupplierView()">Generate</button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                        <h2>Refer History<!-- ORDER REVIEW HEADER--> </h2>
                                    </header>
                                </div>
                            </div>

                        </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table id="refer_table_supplier_view" class="<?php echo table_class(); ?>">
                            <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="min-width: 10%">Item Name</th>
                                    <th style="min-width: 10%">Type</th>
                                    <th style="min-width: 20%">Comment</th>
                                    <th style="min-width: 20%">Send Datetime</th>
                                    <th style="min-width: 20%">Received Date</th>
                                    <th style="min-width: 20%">Vendor Comment</th>
                                    <th style="min-width: 11%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee" id="attachment_model_supplier">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="order_review_history"> Supplier Attachments </h4>
                </div>
                <div class="modal-body">

                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                                <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                                <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                            </tr>
                            </thead>
                            <tbody id="vendor_attachment" class="no-padding">
                            <tr class="danger">
                                <td colspan="5" class="text-center">
                                    <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee" id="vendor_change_model">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="order_review_history">  </h4>
                </div>
                <div class="modal-body">
                   
                    <div class="row">
                        <form role="form" id="refer_detail_form" class="form-horizontal">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                        <h2> Approve Changes<!-- ORDER REVIEW HEADER--> </h2>
                                    </header>
                                </div>
                            </div>
                            <input type="hidden" name="inquiryDetailID" id="inquiryDetailID">
                            <input type="hidden" name="inquiryMasterID" id="inquiryMasterID">
                            <input type="hidden" name="supplierID" id="supplierID">
                            <input type="hidden" name="itemAutoID" id="itemAutoID">
                            
                           
                            

                        </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table id="refer_tables" class="<?php echo table_class(); ?>">
                            <tbody id ="vendor_change_model_body">
                                <!-- <tr><td style="min-width: 20%">#</td><td style="min-width: 200%">Refer Type</td><td style="min-width: 20%"><a class="btn btn-success-new btn-xs"> approve</a> <a class="btn btn-danger-new btn-xs"> Reject</a></td></tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee" id="vendor_specification">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="">Technical Specification </h4>
                </div>
                <div class="modal-body">
                   
                    <div class="row">
                        
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-12  ">
                                    <p id="techText"></p>
                                </div>
                            </div>

                        </div>
                       
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee" id="vendor_terms">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="">Terms & Condition  </h4>
                    <div id="term_ref_supplier">

                    </div>
                </div>
                <div class="modal-body">
                   
                    <div class="row">
                        
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-12  ">
                                    <p id="techText11"></p>
                                </div>
                            </div>

                        </div>
                       
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal chat -->
    <div class="modal fade chat-modal-custom" id="chatModalSRM" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Chat with us</h5>        
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12 ">
                        <!-- Panel Chat -->
                        <div class="panel" id="chat">                    
                            <div class="panel-body bg-white" id="c_body">
                            
                            </div>
                            <div class="panel-footer">
                            <form role="form" id="chat_form" class="form-horizontal">
                                <input type="hidden" name="inquiryDetailID_chat" id="inquiryDetailID_chat">
                                <input type="hidden" name="inquiryMasterID_chat" id="inquiryMasterID_chat">
                                <input type="hidden" name="supplierID_chat" id="supplierID_chat">
                                <input type="hidden" name="chatType_chat" id="chatType_chat">
                                <input type="hidden" name="itemAutoID_chat" id="itemAutoID_chat">
                            
                                <div class="input-group">
                                <input type="text" class="form-control" placeholder="Say something" id="chat_msg" name="chat_msg"> 
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button" onclick="send_my_message()">Send</button>
                                    </span>
                                </div>
                            </form>
                            </div>
                        </div>
                    <!-- End Panel Chat -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>

    <!-- Modal chat -->
<div aria-hidden="true" role="dialog" tabindex="-1" id="open_item_history_details_model" class="modal fade" style="display: none;">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                
                <h3 class="modal-title" id="">Details</h3>
                
            </div>
            
                <div class="modal-body">
                   <form role="form" id="item_partnumber_form" class="form-horizontal">
                    <div class="row">
                        
                        
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Last Po Price:</label>
                            <div class="col-sm-5" id="last_po_price_text">
                                
                            </div>
                        </div>
                        
                    </div>
                    </form>
            
        </div>
        
    </div>
</div>


<script>// Carousel Auto-Cycle
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        $('.carousel').carousel({
            interval: false
        });
      /*   $('.xEditable').editable({
            success: function () {
                colorLabel($(this).data('related'));
            }
        }); */

       // load_vendorattachment();

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        $('.supplier_checkbox').on('ifChecked', function (event) {
            orderItem_selected_check(this);
        });
        $('.supplier_checkbox').on('ifUnchecked', function (event) {
            orderItem_selected_check(this);
        });

        $('.supplier_base_checkbox').on('ifChecked', function (event) {
            orderItem_selected_check_supplier_base(this);
        });
        $('.supplier_base_checkbox').on('ifUnchecked', function (event) {
            orderItem_selected_check_supplier_base(this);
        });
    });

/*     function colorLabel(labelID) {
        $('#' + labelID).addClass('pendingApproval');
        $('#msg-div').show();

    } */



   /*  $(".srmunitprice").editable({
        url: '<?php echo site_url('Srm_master/update_unit_price_srm') ?>',
        send: 'always',
        ajaxOptions: {
            type: 'post',
            dataType: 'json',
            success: function (data) {
                myAlert(data[0], data[1]);
                if( data[0] == 's'){
                    view_supplierAssignModel();
                }
            },
            error: function (xhr) {
                myAlert('e', xhr.responseText);
            }
        }
    }); */
    function update_sup_unitprice(inquiryDetailID,value){ 
        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'inquiryDetailID':inquiryDetailID,'value':value},
                url: "<?php echo site_url('Srm_master/update_unit_price_srm'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data[0] == 's') {
                        view_supplierAssignModel();
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
    }

    function technicalSpecification_modal_open(id){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'inquiryDetailID': id
            },
            url: "<?php echo site_url('Srm_master/fetch_inquiry_details_view'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data1) {
                stopLoad();
               

                if(data1[0]=='s'){
                    $('#techText').text("");
                    $('#techText').text(data1[1]['supplierTechnicalSpecification']);
                    $('#vendor_specification').modal('show');
                }else{
                    $('#techText').text("");
                    $('#techText').text('No data Found');
                    $('#vendor_specification').modal('show');
                }
               
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function open_terms_model_supplier_view(id,sup){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'inquiryMasterID': id,
                'supplierID':sup
            },
            url: "<?php echo site_url('Srm_master/fetch_inquiry_terms_supplier'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data1) {
                stopLoad();

                if(data1[0]=='s'){
                    $('#techText11').text("");
                    $('#term_ref_supplier').html("");

                    var tech_action = '<a onClick="open_chat_model_open_req('+id+ ','+sup+',5)" class="btn btn-outline-info btn-circle btn-sm"> <span title="Chat" rel="tooltip" class="glyphicon glyphicon-comment" ></span></a> <a onClick="referback_rfq_entry('+id+ ','+sup+',5)" class="btn btn-outline-warning btn-circle btn-sm"> <span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat fs-12" ></span></a>'


                    $('#term_ref_supplier').html(tech_action);
                    $('#techText11').text(data1[1]['terms']);
                    $('#vendor_terms').modal('show');
                }else{
                    $('#techText11').text("");
                    $('#techText11').text('No data Found');
                    $('#vendor_terms').modal('show');
                }
                
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function fetch_change_request(inquiryDetailID,inquiryMasterID,supplierID,companyID) {
        var Otable = $('#refer_table').DataTable({"language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Srm_master/load_change_request_data'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "reqResID"},
                {"mData": "referType"},
                {"mData": "comment"},
                {"mData": "createdDatetime"},
                {"mData": "receivedDate"},
                {"mData": "vendorComment"},
                {"mData": "send"},
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                aoData.push({"name": "inquiryDetailID", "value": inquiryDetailID},{"name": "inquiryMasterID", "value": inquiryMasterID},{"name": "supplierID", "value": supplierID},{"name": "companyID", "value": companyID});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
            });
    }

    
    function fetch_change_request_supplier_view(inquiryMasterID,supplierID) {
        var Otable1 = $('#refer_table_supplier_view').DataTable({"language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Srm_master/load_change_request_data_supplier_view'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "reqResID"},
                {"mData": "itemName"},
                {"mData": "referType"},
                {"mData": "comment"},
                {"mData": "createdDatetime"},
                {"mData": "receivedDate"},
                {"mData": "vendorComment"},
                {"mData": "send"},
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                aoData.push({"name": "inquiryMasterID", "value": inquiryMasterID},{"name": "supplierID", "value": supplierID});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
            });
    }



   /*  $(".srmqty").editable({
        url: '<?php echo site_url('Srm_master/update_qty_srm') ?>',
        send: 'always',
        ajaxOptions: {
            type: 'post',
            dataType: 'json',
            success: function (data) {
                myAlert(data[0], data[1]);
                if( data[0] == 's'){
                    view_supplierAssignModel();
                }
            },
            error: function (xhr) {
                myAlert('e', xhr.responseText);
            }
        }
    }); */
    function update_sup_qty(inquiryDetailID,value){ 
        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'inquiryDetailID':inquiryDetailID,'value':value},
                url: "<?php echo site_url('Srm_master/update_qty_srm'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data[0] == 's') {
                        view_supplierAssignModel();
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
    }

    function open_refer_model(inquiryDetailID,inquiryMasterID,supplierID,companyID,itemAutoID){
        $('#refer_model').modal('show');
        $('#refer_type').empty();
        fetch_change_request(inquiryDetailID,inquiryMasterID,supplierID,companyID);

        $('#inquiryDetailID').val(inquiryDetailID);
        $('#inquiryMasterID').val(inquiryMasterID);
        $('#supplierID').val(supplierID);
        $('#itemAutoID').val(itemAutoID);

        $('#refer_type').append($('<option></option>').val("General").html("General"));
        $('#refer_type').append($('<option></option>').val("Price").html("Price Mofidy"));
    }

    function open_refer_model_supplier_view(inquiryMasterID,supplierID){
        $('#refer_model_supplier').modal('show');
        $('#refer_type_supplier').empty();
        $('#supplierSelectItem').empty();
        fetch_change_request_supplier_view(inquiryMasterID,supplierID);
        $('#inquiryMasterIDSupplier').val(inquiryMasterID);
        $('#supplierIDSupplier').val(supplierID);

        $('#refer_type_supplier').append($('<option></option>').val("General").html("General"));
        $('#refer_type_supplier').append($('<option></option>').val("Price").html("Price Mofidy"));

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'inquiryMasterID':inquiryMasterID,'supplierID':supplierID},
            url: "<?php echo site_url('Srm_master/fetch_item_supplier_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
             
                data.forEach((number, index) => {
                    $('#supplierSelectItem').append($('<option></option>').val(number.itemAutoID).html(number.itemName));
                });
               
              
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function open_attachment_model_supplier_view(inquiryMasterID,supplierID){
        

        if (inquiryMasterID) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Srm_master/fetch_vendor_attachments"); ?>',
                dataType: 'json',
                data: {'inquiryMasterID': inquiryMasterID,'supplierID': supplierID, 'confirmedYN': 4},
                success: function (data) {
    
                    $('#vendor_attachment').empty();

                    $('#vendor_attachment').append('' + data + '');
                    $('#attachment_model_supplier').modal('show');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function orderReferSubmit(){

        var $form = $('#refer_detail_form');
        var data = $form.serializeArray();

        // $('#inquiryDetailID').val(inquiryDetailID);
        // $('#inquiryMasterID').val(inquiryMasterID);
        // $('#supplierID').val(supplierID);

        var inquiryDetailID =$('#inquiryDetailID').val();
        var inquiryMasterID=$('#inquiryMasterID').val();
        var supplierID=$('#supplierID').val();
        var companyID=$('#itemAutoID').val();

        data.push({'name': 'inquiryDetailID', 'value': $('#inquiryDetailID').val()});
        data.push({'name': 'inquiryMasterID', 'value': $('#inquiryMasterID').val()});
        data.push({'name': 'supplierID', 'value': $('#supplierID').val()});
        data.push({'name': 'itemAutoID', 'value': $('#itemAutoID').val()});
    
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Srm_master/save_request_refer'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#refer_model').modal('hide');
                //fetch_change_request(inquiryDetailID,inquiryMasterID,supplierID,companyID);
                if (data['error'] == 2) {
                    myAlert('e', data['message']);
                } else {
                    
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                            //$('#item_detail_modal').modal('hide');
                           
                    }
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function orderReferSubmitSupplierView(){

        var $form = $('#refer_detail_form_supplier');
        var data = $form.serializeArray();

        var inquiryMasterID=$('#inquiryMasterIDSupplier').val();
        var supplierID=$('#supplierIDSupplier').val();
       // var companyID=$('#itemAutoID').val();

        data.push({'name': 'inquiryMasterID', 'value': $('#inquiryMasterIDSupplier').val()});
        data.push({'name': 'supplierID', 'value': $('#supplierIDSupplier').val()});
        data.push({'name': 'itemAutoID', 'value': $('#supplierSelectItem').val()});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Srm_master/save_request_refer_supplier_template'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#refer_model_supplier').modal('hide');
                //fetch_change_request(inquiryDetailID,inquiryMasterID,supplierID,companyID);
                if (data['error'] == 2) {
                    myAlert('e', data['message']);
                } else {
                    
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                            //$('#item_detail_modal').modal('hide');
                        
                    }
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

        }

    function add_new_vendor_price(id){

       // vendor_change_model update_vendor_change_price
        $('#vendor_change_model').modal('show');

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'id':id},
                url: "<?php echo site_url('Srm_master/load_vendor_change_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#vendor_change_model_body').html("");
                    var htprice='';

                    if(data.vendorPrice!=null){

                        if(data.unitPriceApproveYN==1){
                            htprice='<tr><td style="min-width: 20%">Unit Price</td><td style="min-width: 20%">'+data.vendorPrice+'</td><td style="min-width: 20%">Approved</td><td style="min-width: 20%"></td></tr>';
                        }else if(data.unitPriceApproveYN==2){
                            htprice='<tr><td style="min-width: 20%">Unit Price</td><td style="min-width: 20%">'+data.vendorPrice+'</td><td style="min-width: 20%">Rejected</td><td style="min-width: 20%"></td></tr>';
                        }else{
                            htprice='<tr><td style="min-width: 20%">Unit Price</td><td style="min-width: 20%">'+data.vendorPrice+'</td><td style="min-width: 20%"><a onClick="approve_vendpr_changes('+data.reqResID+ ',\'supplierPrice\',1,'+data.vendorPrice+')" class="btn btn-success-new btn-xs"> approve</a> <a onClick="approve_vendpr_changes('+data.reqResID+',\'supplierPrice\',2,'+data.vendorPrice+')" class="btn btn-danger-new btn-xs"> Reject</a></td></tr>';
                        }
                        
                    }else{
                        htprice='<tr><td style="min-width: 20%">Unit Price</td><td style="min-width: 20%">Not Submitted</td><td style="min-width: 20%"></td></tr>';
                    }

                    var htqty='';

                    if(data.vendorQty!=null){

                        if(data.qtyApproveYN==1){
                            htqty='<tr><td style="min-width: 20%">Quantity</td><td style="min-width: 20%">'+data.vendorQty+'</td><td style="min-width: 20%">Approved</td><td style="min-width: 20%"></td></tr>';
                        }else if(data.qtyApproveYN==2){
                            htqty='<tr><td style="min-width: 20%">Quantity</td><td style="min-width: 20%">'+data.vendorQty+'</td><td style="min-width: 20%">Rejected</td><td style="min-width: 20%"></td></tr>';
                        }else{
                            htqty='<tr><td style="min-width: 20%">Quantity</td><td style="min-width: 20%">'+data.vendorQty+'</td><td style="min-width: 20%"><a onClick="approve_vendpr_changes('+data.reqResID+',\'supplierQty\',1,'+data.vendorQty+')" class="btn btn-success-new btn-xs"> approve</a> <a onClick="approve_vendpr_changes('+data.reqResID+',\'supplierQty\',2,'+data.vendorQty+')" class="btn btn-danger-new btn-xs"> Reject</a></td></tr>';
                        }
                        
                        
                    }else{
                        htqty='<tr><td style="min-width: 20%">Quantity</td><td style="min-width: 20%">Not Submitted</td><td style="min-width: 20%"></td></tr>';
                    }

                    var htdate='';

                    if(data.vendorNewDeliveryDate!=null){

                        if(data.dateApproveYN==1){
                            htdate='<tr><td style="min-width: 20%">Delivery Date</td><td style="min-width: 20%">'+data.vendorNewDeliveryDate+'</td><td style="min-width: 20%">Approved</td><td style="min-width: 20%"></td></tr>';
                        }else if(data.dateApproveYN==2){
                            htdate='<tr><td style="min-width: 20%">Delivery Date</td><td style="min-width: 20%">'+data.vendorNewDeliveryDate+'</td><td style="min-width: 20%">Rejected</td><td style="min-width: 20%"></td></tr>';
                        }else{
                            htdate='<tr><td style="min-width: 20%">Delivery Date</td><td style="min-width: 20%">'+data.vendorNewDeliveryDate+'</td><td style="min-width: 20%"><a onClick="approve_vendpr_changes('+data.reqResID+',\'supplierExpectedDeliveryDate\',1,'+data.vendorNewDeliveryDate+')" class="btn btn-success-new btn-xs"> approve</a> <a onClick="approve_vendpr_changes('+data.reqResID+',\'supplierExpectedDeliveryDate\',2,'+data.vendorNewDeliveryDate+')" class="btn btn-danger-new btn-xs"> Reject</a></td></tr>';
                        }
                        
                    }else{
                        htdate='<tr><td style="min-width: 20%">Delivery Date</td><td style="min-width: 20%">Not Submitted</td><td style="min-width: 20%"></td></tr>';
                    }

                    var ht=htprice+htqty+htdate;
                    //     myAlert('s', 'update Successfully');

                    // }
                    $('#vendor_change_model_body').html(ht);
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });

    }

    function approve_vendpr_changes(id,col,type,value) {

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'id':id,'col':col,'type':type,'value':value},
                url: "<?php echo site_url('Srm_master/update_vendor_change_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    add_new_vendor_price(id);
                    myAlert('s', 'update Successfully');
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        
    }

    function open_item_history_details(docCurrency,itemAutoID,code){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID, 'currency' : docCurrency},
            url: "<?php echo site_url('Procurement/fetch_last_PO_price'); ?>",
            success: function (data) {
               
                var lst_amt ='Item price not found';
                if(data) {
                   
                   lst_amt =code +' '+data['unitAmount'];
                }
                
                $('#last_po_price_text').text(lst_amt);
                $('#open_item_history_details_model').modal('show');
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.'); /*An Error Occurred! Please Try Again*/
            }
        });
    }
    
    function open_history_orderrev(type,detail_id){ 

        oTable2 = $('#order_review_tbl').DataTable({
                "ordering": false,
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": false,
                "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
                "sAjaxSource": "<?php echo site_url('Srm_master/fetch_change_history'); ?>",
                language: {
                    paginate: {
                        previous: '‹‹',
                        next: '››'
                    }
                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }

                },

                "aoColumns": [
                    {"mData": "Id"},
                    {"mData": "old_val"},
                    {"mData": "new_val"},
                    {"mData": "name"},
                    {"mData": "chamgedtime"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "type", "value": type});
                    aoData.push({"name": "detail_id", "value": detail_id});
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                }
            });
        $('#order_review_management_model').modal('show');
    }

    function open_chat_model(inquiryDetailID,inquiryMasterID,supplierID,itemAutoID,chatType){

        $('#inquiryDetailID_chat').val('');
        $('#inquiryMasterID_chat').val('');
        $('#supplierID_chat').val('');
        $('#chatType_chat').val('');
        $('#itemAutoID_chat').val('');

        $('#inquiryDetailID_chat').val(inquiryDetailID);
        $('#inquiryMasterID_chat').val(inquiryMasterID);
        $('#supplierID_chat').val(supplierID);
        $('#chatType_chat').val(chatType);
        $('#itemAutoID_chat').val(itemAutoID);

        var html = 'html';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryMasterID: inquiryMasterID,inquiryDetailID:inquiryDetailID, supplierID: supplierID,itemAutoID:itemAutoID,chatType:chatType,documentID:'ORD-RVW', html: true},
            url: "<?php echo site_url('srm_master/load_my_chat_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data1) {
                //$('#documentPageViewTitle').html(title);
                $('#c_body').html(data1);
                $('#chatModalSRM').modal('show');
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
        
    }

    function send_my_message(){
        var msg = $('#chat_msg').val();

        if(msg !=null){

            var $form = $('#chat_form');
            var data = $form.serializeArray();

            var inquiryDetailID_chat =$('#inquiryDetailID_chat').val();
            var inquiryMasterID_chat=$('#inquiryMasterID_chat').val();
            var supplierID_chat=$('#supplierID_chat').val();
            var chatType_chat =$('#chatType_chat').val();
            var itemAutoID_chat =$('#itemAutoID_chat').val();

            data.push({'name': 'inquiryDetailID', 'value': inquiryDetailID_chat});
            data.push({'name': 'inquiryMasterID', 'value': inquiryMasterID_chat});
            data.push({'name': 'supplierID', 'value': supplierID_chat});
            data.push({'name': 'chatType', 'value': chatType_chat});
            data.push({'name': 'itemAutoID', 'value': itemAutoID_chat});
            data.push({'name': 'documentID', 'value': 'ORD-RVW'});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Srm_master/save_my_chat'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data1) {
                    $('#chat_msg').val('');
                    if(chatType_chat==4 || chatType_chat==5){
                        
                        open_chat_model_open_req(inquiryMasterID_chat,supplierID_chat,chatType_chat);
                    }else{
                        open_chat_model(inquiryDetailID_chat,inquiryMasterID_chat,supplierID_chat,itemAutoID_chat,chatType_chat);
                    }
                    stopLoad();
                   
                    //myAlert('s', 'update Successfully');
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

    }

    function open_chat_model_open_req(inquiryMasterID,supplierID,chatType){

        $('#inquiryDetailID_chat').val('');
        $('#inquiryMasterID_chat').val('');
        $('#supplierID_chat').val('');
        $('#chatType_chat').val('');
        $('#itemAutoID_chat').val('');

        // $('#inquiryDetailID_chat').val(inquiryDetailID);
        $('#inquiryMasterID_chat').val(inquiryMasterID);
        $('#supplierID_chat').val(supplierID);
        $('#chatType_chat').val(chatType);
        // $('#itemAutoID_chat').val(itemAutoID);

        var html = 'html';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryMasterID: inquiryMasterID, supplierID: supplierID,chatType:chatType, html: true},
            url: "<?php echo site_url('srm_master/load_my_chat_view_open'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data1) {
                //$('#documentPageViewTitle').html(title);
                $('#c_body').html(data1);
                $('#chatModalSRM').modal('show');
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

</script>