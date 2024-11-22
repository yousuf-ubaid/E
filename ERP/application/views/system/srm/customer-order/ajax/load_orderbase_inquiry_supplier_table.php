
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
?>
<style>
    /* MENU-LEFT
-------------------------- */
    /* layout */
    #left ul.nav {
        margin-bottom: 2px;
        font-size: 12px; /* to change font-size, please change instead .lbl */
    }

    #left ul.nav ul,
    #left ul.nav ul li {
        list-style: none !important;
        list-style-type: none !important;
        margin-top: 1px;
        margin-bottom: 1px;
    }

    #left ul.nav ul {
        padding-left: 0;
        width: auto;
    }

    #left ul.nav ul.children {
        padding-left: 12px;
        width: auto;
    }

    #left ul.nav ul.children li {
        margin-left: 0px;
    }

    #left ul.nav li a:hover {
        text-decoration: none;
    }

    #left ul.nav li a:hover .lbl {
        color: #999 !important;
    }

    #left ul.nav li.current > a .lbl {
        background-color: #999;
        color: #fff !important;
    }

    /* parent item */
    #left ul.nav li.parent a {
        padding: 0px;
        color: #ccc;
    }

    #left ul.nav > li.parent > a {
        border: solid 1px #999;
        text-transform: uppercase;
    }

    #left ul.nav li.parent a:hover {
        background-color: #fff;
        -webkit-box-shadow: inset 0 3px 8px rgba(0, 0, 0, 0.125);
        -moz-box-shadow: inset 0 3px 8px rgba(0, 0, 0, 0.125);
        box-shadow: inset 0 3px 8px rgba(0, 0, 0, 0.125);
    }

    /* link tag (a)*/
    #left ul.nav li.parent ul li a {
        color: #222;
        border: none;
        display: block;
        padding-left: 5px;
    }

    #left ul.nav li.parent ul li a:hover {
        background-color: #fff;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
    }

    /* sign for parent item */
    #left ul.nav li .sign {
        display: inline-block;
        width: 28px;
        padding: 5px 8px;
        background-color: transparent;
        color: #fff;
    }

    #left ul.nav li.parent > a > .sign {
        margin-left: 0px;
        background-color: #999;
    }

    /* label */
    #left ul.nav li .lbl {
        padding: 5px 12px;
        display: inline-block;
    }

    #left ul.nav li.current > a > .lbl {
        color: #fff;
    }

    #left ul.nav li a .lbl {
        font-size: 12px;
    }

    /* THEMATIQUE
    ------------------------- */
    /* theme 1 */
    #left ul.nav > li.item-1.parent > a {
        border: solid 1px #3fbdf9;
    }

    #left ul.nav > li.item-1.parent > a > .sign,
    #left ul.nav > li.item-1 li.parent > a > .sign {
        margin-left: 0px;
        background-color: #3fbdf9;
    }

    #left ul.nav > li.item-1 .lbl {
        color: #24272d;
    }

    #left ul.nav > li.item-1 li.current > a .lbl {
        background-color: #24272d;
        color: #fff !important;
    }

    /* theme 2 */
    #left ul.nav > li.item-8.parent > a {
        border: solid 1px #51c3eb;
    }

    #left ul.nav > li.item-8.parent > a > .sign,
    #left ul.nav > li.item-8 li.parent > a > .sign {
        margin-left: 0px;
        background-color: #51c3eb;
    }

    #left ul.nav > li.item-8 .lbl {
        color: #51c3eb;
    }

    #left ul.nav > li.item-8 li.current > a .lbl {
        background-color: #51c3eb;
        color: #fff !important;
    }

    /* theme 3 */
    #left ul.nav > li.item-15.parent > a {
        border: solid 1px #94cf00;
    }

    #left ul.nav > li.item-15.parent > a > .sign,
    #left ul.nav > li.item-15 li.parent > a > .sign {
        margin-left: 0px;
        background-color: #94cf00;
    }

    #left ul.nav > li.item-15 .lbl {
        color: #94cf00;
    }

    #left ul.nav > li.item-15 li.current > a .lbl {
        background-color: #94cf00;
        color: #fff !important;
    }

    /* theme 4 */
    #left ul.nav > li.item-22.parent > a {
        border: solid 1px #ef409c;
    }

    #left ul.nav > li.item-22.parent > a > .sign,
    #left ul.nav > li.item-22 li.parent > a > .sign {
        margin-left: 0px;
        background-color: #ef409c;
    }

    #left ul.nav > li.item-22 .lbl {
        color: #ef409c;
    }

    #left ul.nav > li.item-22 li.current > a .lbl {
        background-color: #ef409c;
        color: #fff !important;
    }

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

    .total {
        border-top: 1px double #8a7c1a !important;
        border-bottom: 3px double #b7a318 !important;
        font-weight: bold;
        font-size: 12px !important;
    }

    #left ul.nav li.parent a {
        padding: 0px;
        color: #4365a2;
    }
</style>
<?php //var_dump($header); ?>
<div class="row">
    <div class="col-sm-11">
        &nbsp
    </div>
    <div class="col-sm-1">
        <!--<div class="skin skin-square">
            <div class="skin-section extraColumns">
                <input id="orderSupplier_MasterCheck" type="checkbox"
                       data-caption="" class="columnSelected"
                       name="isActive" onclick=""
                       value="">
            </div>
        </div>-->
    </div>
</div>
<?php if($master_header['templateType']==1){?>
<div class="row">
    <div class="col-sm-12">
        <div id="left" class="span3">
            <ul id="menu-group-1" class="nav menu">
                <?php if (!empty($header)) {
                    $x = 1;
                    foreach ($header as $val) {
                        ?>
                        <li class="item-1 deeper parent" style="margin-top: 1%;">
                           
                        <a class="" href="#">
                                <span data-toggle="collapse" data-parent="#menu-group-1"
                                      href="#sub-item-<?php echo $val['inquiryMasterID']; ?>-<?php echo $val['itemAutoID']; ?>"
                                      class="sign"><i
                                        class="fa fa-plus" aria-hidden="true" style="color:white;font-size: 13px;"></i></span>
                                <span class="lbl"><strong style="font-size: 15px;"><?php echo $val['itemSystemCode'] . " - " . $val['itemName']; ?></strong> &nbsp&nbsp&nbsp | &nbsp&nbsp&nbsp
                                
                                <strong style="font-size: 15px;"> Current Stock -  <?php echo $val['currentStock']; ?></strong> &nbsp;&nbsp;&nbsp;
                                    
                                    <div class="actionicon"
                                        onclick="view_supplierAssignModel(<?php echo $val['itemAutoID']; ?>,'0',null)"><i
                                            class="fa fa-repeat" aria-hidden="true" style="color: white"
                                            title="Assign Supplier"></i>&nbsp;&nbsp;Assign Supplier</div></span>
                            </a>
                            <ul class="children nav-child unstyled small collapse"
                                id="sub-item-<?php echo $val['inquiryMasterID']; ?>-<?php echo $val['itemAutoID']; ?>">
                                <div class="table-responsive mailbox-messages">
                                    <table class="table table-hover table-striped">
                                        <tbody>
                                        <tr class="task-cat-upcoming">
                                            <td class="headrowtitle"
                                                style="border-bottom: solid 1px #f76f01;">#
                                            </td>
                                            <td class="headrowtitle"
                                                style="border-bottom: solid 1px #f76f01;">Supplier Name
                                            </td>
                                            <td class="headrowtitle"
                                                style="border-bottom: solid 1px #f76f01;">Supplier Code
                                            </td>
                                            <td class="headrowtitle"
                                                style="border-bottom: solid 1px #f76f01;">Qty
                                            </td>
                                            <td class="headrowtitle"
                                                style="border-bottom: solid 1px #f76f01;">Expected Delivery Date
                                            </td>
                                            <td class="headrowtitle"
                                                style="border-bottom: solid 1px #f76f01;">
                                            </td>
                                        </tr>
                                        <?php
                                        $companyID = current_companyID();
                                        $suppliers = $this->db->query("SELECT requestedQty,expectedDeliveryDate,inquiryDetailID,supplierName,supplierSystemCode,srp_erp_srm_suppliermaster.supplierAutoID,srp_erp_srm_orderinquirydetails.isRfqCreated FROM srp_erp_srm_orderinquirydetails INNER JOIN srp_erp_srm_suppliermaster ON srp_erp_srm_orderinquirydetails.supplierID = srp_erp_srm_suppliermaster.supplierAutoID WHERE inquiryMasterID = {$val['inquiryMasterID']} AND srp_erp_srm_orderinquirydetails.itemAutoID = '{$val['itemAutoID']}'")->result_array();
                                        $x = 1;
                                        if (!empty($suppliers)) {
                                            foreach ($suppliers as $tar) {
                                                ?>
                                                <tr>
                                                    
                                                    <td class="mailbox-star"><?php echo $x; ?></td>
                                                    <td class="mailbox-star"
                                                    ><?php echo $tar['supplierName']; ?></td>
                                                    <td class="mailbox-star"
                                                    ><?php echo $tar['supplierSystemCode'] ?></td>
                                                    
                                                    <td>
                                                        <a href="#" data-type="text"
                                                           data-placement="bottom"
                                                           data-url="<?php echo site_url('Srm_master/ajax_update_orderInquiry_supplier') ?>"
                                                           data-pk="<?php echo $tar['inquiryDetailID'] ?>"
                                                           data-name="requestedQty"
                                                           data-title="Name"
                                                           class="xeditable"
                                                           data-value="<?php echo $tar['requestedQty']; ?>">
                                                            <?php echo $tar['requestedQty'] ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="#" data-type="combodate"
                                                           data-url="<?php echo site_url('Srm_master/ajax_update_orderInquiry_supplier') ?>"
                                                           data-pk="<?php echo $tar['inquiryDetailID'] ?>"
                                                           data-name="expectedDeliveryDate"
                                                           data-title="Expected Delivery Date"
                                                           class="xeditableDate"
                                                           data-value="<?php if (!empty($tar['expectedDeliveryDate']) && $tar['expectedDeliveryDate'] != '0000-00-00 00:00:00') {
                                                               echo format_date($tar['expectedDeliveryDate']);
                                                           } ?>">
                                                        </a>
                                                        &nbsp;
                                                    </td>
                                                    <td style="text-align: center">
                                                        <div class="skin skin-square">
                                                            <div class="skin-section extraColumns"><input
                                                                    id="isSupplier_<?php echo $tar['supplierAutoID'] ?>"
                                                                    type="checkbox"
                                                                    data-caption=""
                                                                    <?php if($tar['isRfqCreated'] == 1){
                                                                        echo "checked";
                                                                    } ?>
                                                                    class="columnSelected isSupplier_checkbox"
                                                                    name="supplierCheckbox"
                                                                
                                                                    value="<?php echo $tar['inquiryDetailID'] ?>"><label
                                                                    for="checkbox">&nbsp;</label>
                                                            </div>
                                                            <div>
                                                            <a onclick="delete_supplier_srm(<?php echo $tar['inquiryDetailID'] ?>,this)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                $x++;
                                            }
                                        } else { ?>
                                        <tr>
                                            <td class="mailbox-star" colspan="5" style="text-align: center">No Suppliers Assigned</td>
                                        <tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </ul>
                        </li>
                        <?php
                    }
                } else { ?>
                    <strong class="attachemnt_title">
                                <span style="text-align: center;font-size: 15px;font-weight: 800;">NO SUPPLIERS ASSIGNED FOR THIS ITEM</span>
                    </strong>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 10px;">
        <div class="col-sm-2">
            <label class="title"><?php echo $this->lang->line('srm_delivery_terms');?><!--Delivery Terms--></label>
        </div>
        <div class="col-sm-9">
            <textarea class="form-control" rows="3" name="deliveryTerms" id="deliveryTerms"></textarea>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-sm-12">
            <div
                style="font-size: 13px;font-weight: 700;color: #ff4d4d;padding: 4px 10px 0 0;margin-left: 5%;">
                <?php echo $this->lang->line('srm_rfq_will_be_generate');?> <!-- RFQ WILL BE GENERATE TO ONLY SELECTED SUPPLIERS-->
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="col-sm-12">
            <div class="text-right m-t-xs">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary-new size-lg " onclick="draft_order_inquiry()"><?php echo $this->lang->line('common_save_as_draft');?><!--Save as Draft--></button>
                    <button class="btn btn-success-new size-lg submitWizard" onclick="confirm_order_inquiry()"><?php echo $this->lang->line('common_confirm');?><!--Confirm-->
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php }else{ ?>
    <div class="row">
    <div class="col-sm-12 text-right">
        <button class="btn btn-primary-new size-sm submitWizard" onclick="view_supplierViewAssignModel('0',null)">Add Supplier</button>
    </div>
    
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped item-supplier-style-tbl">
                    <tbody>

                        <tr id="supplier_col">
                            <td class="text-center" rowspan="2" style="border-bottom: 1px solid #ddd "># </td>
                            <td class="text-center" rowspan="2" style="border-bottom: 1px solid #ddd ">Material Description: </td>
                            
                            <?php if (!empty($suppliers)) {
                            $x = 1;
                            foreach ($suppliers as $val) {
                                
                            ?>
                            <td class="text-center supplier-col" colspan="3" style="border-bottom: 1px solid #ddd "><?php echo $val['supplierName'] ?> 
                                <!-- <div class="">
                                    <div class="skin-section extraColumns"><input
                                            id="isSupplier_<?php echo $val['supplierAutoID'] ?>"
                                            type="checkbox"
                                            data-caption=""
                                            <?php if($val['isRfqCreated'] == 1){
                                                echo "checked";
                                            } ?>
                                            class="columnSelected isSupplier_checkbox_supplier_view"
                                            name="supplierCheckbox"
                                        
                                            value="<?php echo $val['supplierAutoID'] ?>"><label
                                            for="checkbox">&nbsp;</label>
                                    </div>
                                </div> -->
                            </td>
                            <?php }}?>
                            
                        </tr>

                        <tr>
                            <?php if (!empty($suppliers)) {
                            $x = 1;
                            foreach ($suppliers as $val) {
                            ?>
                            <td class="" style="border-bottom: 1px solid #ddd "> Qty</td>
                            <td class="" style="border-bottom: 1px solid #ddd "> Expected Delivery Date </td>
                            <td class="" style="border-bottom: 1px solid #ddd ">  </td>

                            <?php }}?>
                        </tr>
                        <?php if (!empty($header)) {
                        $x = 1;
                        foreach ($header as $key=>$val) {
                            ?>
                            <tr class="fw-500">
                                <td class="" style="border-bottom: 1px solid #ddd "> <?php echo $key+1 ?> </td>
                                <td class="" style="border-bottom: 1px solid #ddd "> <?php echo $val['itemSystemCode'] . " - " . $val['itemName'] ?> </td>

                                <?php if (!empty($suppliers)) {
                                $x = 1;
                                foreach ($suppliers as $tar) {
                                    $suppliers_item = $this->db->query("SELECT requestedQty,expectedDeliveryDate,inquiryDetailID,supplierName,supplierSystemCode,srp_erp_srm_suppliermaster.supplierAutoID,srp_erp_srm_orderinquirydetails.isRfqCreated FROM srp_erp_srm_orderinquirydetails INNER JOIN srp_erp_srm_suppliermaster ON srp_erp_srm_orderinquirydetails.supplierID = srp_erp_srm_suppliermaster.supplierAutoID WHERE inquiryMasterID = {$val['inquiryMasterID']} AND srp_erp_srm_orderinquirydetails.itemAutoID = '{$val['itemAutoID']}' AND srp_erp_srm_orderinquirydetails.supplierID = '{$tar['supplierAutoID']}'")->row_array();

                                ?>
                                    <?php if($suppliers_item['requestedQty']){ ?>
                                        <td class="" style="border-bottom: 1px solid #ddd ">
                                            <a href="#" data-type="text"
                                                data-placement="bottom"
                                                data-url="<?php echo site_url('Srm_master/ajax_update_orderInquiry_supplier') ?>"
                                                data-pk="<?php echo $suppliers_item['inquiryDetailID'] ?>"
                                                data-name="requestedQty"
                                                data-title="Qty"
                                                class="xeditable"
                                                data-value="<?php echo $suppliers_item['requestedQty']; ?>">
                                                <?php echo $suppliers_item['requestedQty'] ?>
                                            </a>
                                        </td>
                                        <td class="" style="border-bottom: 1px solid #ddd ">
                                            <a href="#" data-type="combodate"
                                                data-url="<?php echo site_url('Srm_master/ajax_update_orderInquiry_supplier') ?>"
                                                data-pk="<?php echo $suppliers_item['inquiryDetailID'] ?>"
                                                data-name="expectedDeliveryDate"
                                                data-title="Expected Delivery Date"
                                                class="xeditableDate"
                                                data-value="<?php if (!empty($suppliers_item['expectedDeliveryDate']) && $suppliers_item['expectedDeliveryDate'] != '0000-00-00 00:00:00') {
                                                    echo format_date($suppliers_item['expectedDeliveryDate']);
                                                } ?>">
                                            </a>

                                            
                                        </td>
                                        <td class="" style="border-bottom: 1px solid #ddd ">
                                            <div class="skin-section extraColumns"><input
                                                id="isSupplier_<?php echo $suppliers_item['supplierAutoID'] ?>_<?php echo $suppliers_item['inquiryDetailID'] ?>"
                                                type="checkbox"
                                                data-caption=""
                                                <?php if($suppliers_item['isRfqCreated'] == 1){
                                                    echo "checked";
                                                } ?>
                                                class="columnSelected isSupplier_checkbox"
                                                name="supplierCheckbox"
                                            
                                                value="<?php echo $suppliers_item['inquiryDetailID'] ?>"><label
                                                for="checkbox">&nbsp;</label>
                                            </div>
                                        </td>
                                    <?php }else{ ?>
                                        <td class="text-center" style="border-bottom: 1px solid #ddd " colspan="3">Not assigned</td>
                                        <!-- <td class="" style="border-bottom: 1px solid #ddd ">
                                            <a href="#" data-type="text"
                                                data-placement="bottom"
                                                data-url="<?php echo site_url('Srm_master/ajax_update_orderInquiry_supplier') ?>"
                                                data-pk="<?php echo $suppliers_item['inquiryDetailID'] ?>"
                                                data-name="requestedQty"
                                                data-title="Qty"
                                                class="xeditable"
                                                data-value="<?php echo $suppliers_item['requestedQty']; ?>">
                                                <?php echo $suppliers_item['requestedQty'] ?>
                                            </a>
                                        </td>
                                        <td class="" style="border-bottom: 1px solid #ddd ">
                                            <a href="#" data-type="combodate"
                                                data-url="<?php echo site_url('Srm_master/ajax_update_orderInquiry_supplier') ?>"
                                                data-pk="<?php echo $suppliers_item['inquiryDetailID'] ?>"
                                                data-name="expectedDeliveryDate"
                                                data-title="Expected Delivery Date"
                                                class="xeditableDate"
                                                data-value="<?php if (!empty($suppliers_item['expectedDeliveryDate']) && $suppliers_item['expectedDeliveryDate'] != '0000-00-00 00:00:00') {
                                                    echo format_date($suppliers_item['expectedDeliveryDate']);
                                                } ?>">
                                            </a>
                                        </td> -->
                                    <?php } ?>



                                <?php }}?>
                            
                            </tr>
                        <?php }}?>
                    

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="col-sm-2">
            <label class="title"><?php echo $this->lang->line('srm_delivery_terms');?><!--Delivery Terms--></label>
        </div>
        <div class="col-sm-9">
            <textarea class="form-control" rows="3" name="deliveryTerms" id="deliveryTerms"></textarea>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-sm-12">
            <div
                style="font-size: 13px;font-weight: 700;color: #ff4d4d;padding: 4px 10px 0 0;margin-left: 5%;">
                <?php echo $this->lang->line('srm_rfq_will_be_generate');?> <!-- RFQ WILL BE GENERATE TO ONLY SELECTED SUPPLIERS-->
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="col-sm-12">
            <div class="text-right m-t-xs">
                <div class="text-right m-t-xs">
                    <!-- <button class="btn btn-primary " onclick="draft_order_inquiry()"><?php echo $this->lang->line('common_save_as_draft');?></button> -->
                    <button class="btn btn-success-new size-lg submitWizard" onclick="confirm_order_inquiry()"><?php echo $this->lang->line('common_confirm');?><!--Confirm-->
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php }?>

<script>

    $(document).ready(function () {

        $('.children').addClass('in');

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        $('.isSupplier_checkbox').on('ifChecked', function (event) {
            supplier_selected_check(this);
        });
        $('.isSupplier_checkbox').on('ifUnchecked', function (event) {
            supplier_selected_check(this);
        });

        $('.isSupplier_checkbox_supplier_view').on('ifChecked', function (event) {
            supplier_selected_check_supplier_view(this);
        });
        $('.isSupplier_checkbox_supplier_view').on('ifUnchecked', function (event) {
            supplier_selected_check_supplier_view(this);
        });

        $('.xeditable').editable();

        $('.xeditableDate').editable({
            format: 'YYYY-MM-DD',
            viewformat: 'DD.MM.YYYY',
            template: 'D / MMMM / YYYY',
            combodate: {
                minYear: <?php echo format_date_getYear() - 80 ?>,
                maxYear: <?php echo format_date_getYear() + 12 ?>,
                minuteStep: 1
            }
        });
        $('#orderSupplier_MasterCheck').on('ifChecked', function (event) {
            $('.isSupplier_checkbox').iCheck('check');
        });

        $('#orderSupplier_MasterCheck').on('ifUnchecked', function (event) {
            $('.isSupplier_checkbox').iCheck('uncheck');
        });

    });

</script>
