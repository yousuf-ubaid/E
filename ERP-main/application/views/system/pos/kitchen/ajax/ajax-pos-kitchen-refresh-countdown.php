<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$outletInfo = get_outletInfo();

?>
<style>
    .timeContainer {
        padding: 0px 5px;
        color: #15d80b;
        text-align: center;
        font-weight: 800;
    }

    .bgBlack {
        background-color: #000000;
        color: #fdff05;
    }

    .qty {
        vertical-align: top;
        min-width: 50px;
        text-align: center
    }

    .qtyTitle{
        font-weight: 800 !important;
        min-width: 50px;
    }
    .fontStyle {
        font-size: 25px;
        font-weight: 700 !important;
        font-family: Calibri;
    }

</style>
<script>
    function changeKOTLocation(id) {
        window.location.replace("<?php echo site_url('kot_countdown') ?>/" + id.value );
    }
</script>
<div class="row">
    <div class="col-md-2">
        <h4 class="text-purple text-left">
            &nbsp;&nbsp;<i class="fa fa-building-o"></i>
            <?php echo $outletInfo['wareHouseDescription'] ?>
        </h4>
    </div>
    <div class="col-md-7">
        <h4 class="text-yellow text-center">
            <i class="fa fa-cutlery"></i> <?php echo $this->lang->line('posr_pending_order'); ?><!--Pending Order-->
        </h4>
    </div>

    <div class="col-md-2">
        <?php $kotID = $this->input->post('kotID'); ?>
        <select name="" id="" class="form-control"
                style="margin:4px 0px; margin: 4px 0px; font-size: 19px; padding: 0px; font-weight: 600; color: #1d549a;"
                onchange="changeKOTLocation(this)">
            <option value="0">Please select</option>
            <?php
            $kotLocation = get_kitchenLocation();
            if (!empty($kotLocation)) {
                foreach ($kotLocation as $item) {
                    if (isset($kotID) && $kotID == $item['kitchenLocationID']) {
                        $selected = ' selected ';
                    } else {
                        $selected = ' ';
                    }

                    $url = site_url('kot_manual');
                    echo '<option ' . $selected . ' value="' . $item['kitchenLocationID'] . '">' . $item['description'] . '</option>';
                }
            }

            ?>
        </select>
    </div>

    <div class="col-md-1">
        <a style="border-radius: 0px;" href="<?php echo site_url('dashboard') ?>" class="btn btn-danger pull-right"><i
                    class="fa fa-remove fa-2x"></i> </a>
    </div>

</div>


<div class="row">

    <div class="col-md-12" style="border:1px dashed #b30300; min-height: 500px;">

        <!--Pending Orders -->
            <?php
            if (!empty($pendingOrders)) {
                $titleID = 0;
                $i = 0;
                //echo "<pre>";print_r($pendingOrders);exit;
                foreach ($pendingOrders as $pendingOrder) {
                    ?>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <?php
                        ?>
                        <div class="row" style="margin-right: -10px;">
                            <div class="media">
                                <div class="media-body">
                                    <div class="row bgBlack" style="margin-right: 0px !important;">
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h4 class="title fontStyle">
                                                <i class="fa fa-tag"></i>
                                                <span
                                                        style="font-weight: 800"> <?php echo $pendingOrder['invoiceCode']; ?> </span>
                                            </h4>
                                        </div>

                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <?php
                                            $colr="";
                                            $preparationTime= $pendingOrder['preparationTime'];
                                            if(empty($preparationTime)){
                                                $preparationTime=0;
                                            }
                                            //$curTime = date('Y-m-d H:i:s');
                                            $curTime = "$currdtTime";
                                            $datetime1 = date_create($curTime);
                                            $datetime2 = date_create($pendingOrder['KOTStartDateTime']);
                                            $interval = date_diff($datetime1, $datetime2);

                                            $prephour= $interval->format('%H');
                                            $prepminuite= $interval->format('%i');
                                            $prepsec= $interval->format('%s');
                                            $curmin= $prepminuite+($prephour*60)+($prepsec/60);
                                            $totmin=$preparationTime-$curmin;
                                            $minusmark="";
                                            if($totmin<0){
                                                $totmin=$curmin-$preparationTime;
                                                $colr="color: red;";
                                                $minusmark="-";
                                            }
                                            ?>
                                            <h2 class="timeContainer" style="margin: 0px; padding:5px; <?php echo $colr; ?>">
                                                <?php
                                                /*echo "&nbsp;&nbsp;&nbsp;";
                                                echo "&nbsp;&nbsp;&nbsp;";
                                                echo "&nbsp;&nbsp;&nbsp;";
                                                echo "&nbsp;&nbsp;&nbsp;";
                                                echo $interval->format('%h : %i : %s');
                                                echo "&nbsp;&nbsp;&nbsp;";
                                                echo "&nbsp;&nbsp;&nbsp;";
                                                echo $pendingOrder['KOTStartDateTime'];
                                                echo "&nbsp;&nbsp;&nbsp;";
                                                echo "&nbsp;&nbsp;&nbsp;";
                                                echo date('Y-m-d H:i:s');*/

                                                echo $minusmark.gmdate("i", $totmin * 60).' : '.gmdate("s", $totmin * 60);
                                                //echo round($totmin,2);
                                                ?>
                                            </h2>
                                        </div>

                                        <?php
                                        $pendingOrder['kotID'] = isset($pendingOrder['kotID']) ? $pendingOrder['kotID'] : 0;
                                        ?>

                                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center fontStyle" style="padding-left: 0px;">
                                            <?php echo $this->lang->line('posr_qyt'); ?><!--QTY-->
                                        </div>

                                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                            <input class="mySwitch" type="checkbox" value="1"
                                                   id="isPax_<?php echo $pendingOrder['menuSalesID'] ?>" name="pending"
                                                   onchange="updateToCurrent(<?php echo $pendingOrder['menuSalesID'] ?>,this,<?php echo $pendingOrder['kotID'] ?>)"
                                                   data-size="small"
                                                   data-on-text="<i class='fa fa-check text-green'></i> &nbsp;"
                                                   data-on-color="default"
                                                   data-handle-width="50"
                                                   data-off-color="default"
                                                   data-off-text="<i class='fa fa-cutlery text-green'></i> Done"
                                                   data-label-width="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    $warehouseID = get_outletID(); //$counterInfo['wareHouseID'];
                    $companyID = current_companyID(); //$counterInfo['companyID'];
                    $kitchenLocationID = $pendingOrder['kitchenLocationID']; //$counterInfo['companyID'];
                    $menuSalesID = $pendingOrder['menuSalesID']; //$counterInfo['companyID'];

                    $q = "SELECT
                    salesMaster.menuSalesID,
                    salesItem.menuSalesItemID,
                    menuMaster.menuMasterID,
                    salesMaster.preparationTime,
                    menuMaster.menuMasterDescription,
                    menuCategory.menuCategoryID,
                    menuCategory.menuCategoryDescription,
                    salesItem.qty,
                    salesMaster.KOTStartDateTime,
                    menuMaster.menuImage,
                    salesItem.kitchenNote,
                    salesMaster.invoiceCode as invoiceCode,
                    salesItem.kotID as kotID,
                    salesMaster.KOTAlarm as KOTAlarm,
                    salesMaster.createdDateTime,
                    ms.description as menuDescription,
                    warehouse.warehouseMenuID
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                LEFT JOIN srp_erp_pos_menusalesitems salesItem ON salesItem.menuSalesID = salesMaster.menuSalesID
                LEFT JOIN srp_erp_pos_warehousemenumaster warehouse ON warehouse.warehouseMenuID = salesItem.warehouseMenuID
                LEFT JOIN srp_erp_pos_menumaster menuMaster ON menuMaster.menuMasterID = warehouse.menuMasterID
                LEFT JOIN srp_erp_pos_menucategory menuCategory ON menuCategory.menuCategoryID = menuMaster.menuCategoryID
                LEFT JOIN srp_erp_pos_kitchenlocation kitchen ON kitchen.kitchenLocationID =  warehouse.kotID
                LEFT JOIN srp_erp_pos_menusize ms ON ms.menuSizeID = menuMaster.menuSizeID
                WHERE
                salesItem.isOrderPending = 1
                AND salesMaster.menuSalesID = $menuSalesID
                AND salesItem.isOrderInProgress = 0
                AND salesMaster.companyID='$companyID' AND salesMaster.wareHouseAutoID='$warehouseID'  
                AND kitchen.kitchenLocationID = '" . $kitchenLocationID . "' AND salesMaster.createdDateTime >= DATE_SUB(NOW(),INTERVAL 3 HOUR)
                ORDER BY
                    salesMaster.menuSalesID";
                    $result = $this->db->query($q)->result_array();
                    foreach ($result as $val){
                        $comboSub=get_pos_combos($val['menuSalesID'],$val['menuSalesItemID'],$val['warehouseMenuID']);
                    ?>
                        <div class="row" style="margin-right: -10px;">
                            <div class="detailList col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                <img src="<?php echo base_url($val['menuImage']) ?>"
                                     style="height: 40px; width: 40px;" alt="">
                                <?php echo $val['menuMasterDescription']; //print_r($pendingOrder);?>
                                &nbsp;&nbsp;
                                <span style="font-weight: bold; color:red"><?php echo $val['menuDescription']; ?></span>
                                <?php
                                echo !empty($val['kitchenNote']) ? '<br/>&nbsp;&nbsp;&nbsp;<i class="fa fa-star" style="color:#d54136"></i> ' . $val['kitchenNote'] : '';
                                $menuSalesItemID = $val['menuSalesItemID'];
                                $output = get_add_on_byItem($menuSalesItemID);
                                if (!empty($output)) {
                                    foreach ($output as $valu) {
                                        echo '<br/>&nbsp;&nbsp;&nbsp;<i class="fa fa-star" style="color:#d54136"></i> ' . $valu['menuMasterDescription'];
                                    }
                                }
                                ?>
                            </div>
                            <div class="text-center fontStyle col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                <?php echo $val['qty']; ?>
                            </div>


                        </div>
                        <div class="row" style="margin-right: -10px;">
                            <?php
                            if(!empty($comboSub)){
                                foreach($comboSub as $cmbo){
                                    ?>
                                    <div  align="left" style="font-size: 15px!important; padding-left: 100px !important;">* <?php echo $cmbo['menuMasterDescription'] ?></div>
                                    <div  align="right" class="qty fontStyle" style="font-size: 15px!important;padding-left: 280px !important;"> <?php echo $cmbo['qty'] ?></div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                  <?php
                    }
                    ?>
                    <?php
                ?>
                </div>
                <?php
                }
            }
            ?>



</div>


<audio id="buzzer" src="<?php echo base_url('uploads/music/nokia_message2.m4r') ?>" type="audio/m4r"></audio>
<script type="text/javascript">
    $(document).ready(function () {
        var p_or_c = $(".alarm_tracker").val();
        var n_or_c = '<?php echo count($pendingOrders); ?>';

        if(p_or_c !== "0" || p_or_c !== ""){
            if(p_or_c < n_or_c){
                var buzzer = $('#buzzer')[0];
                buzzer.play();
                $(".alarm_tracker").val(n_or_c);
            } else {
                $(".alarm_tracker").val(n_or_c);
            }
        } else {
            $(".alarm_tracker").val("");
        }

        $(".mySwitch").bootstrapSwitch();
    });
</script>