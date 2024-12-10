<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


$assetTypes = array('1' => 'Own Asset', '2' => 'Third Party');
?>
    <div style="margin-bottom: 7px;">
        <img class="img-responsive" style="height: 141px !important;"
             src="<?php

             if (is_null($extra['image'])) {
                 echo base_url('images/item/no-image.png');
             } elseif (file_exists(__DIR__."../../../../uploads/assets/{$extra['image']}")) {
                 echo base_url("uploads/assets/{$extra['image']}");
             } else {
                 echo base_url("uploads/assets/{$extra['image']}");
             };
             ?>"
             id="asset_img" alt="...">
    </div>
    <div class="table-responsive" style="padding: 0 !important; margin: 0 !important;">
        <table style="width: 100%" class="table table-bordered table-striped table-condensed ">

            <tbody>
            <tr>
                <th style="background-color: #c8d7e0;" colspan="4">
                    <?php echo $this->lang->line('assetmanagement_asset_details');?><!-- Asset Details-->
                </th>
            </tr>
            <tr>
                <td class="theadtr" style="width: 16%"><strong> <?php echo $this->lang->line('assetmanagement_asset_code');?><!--Asset Code--> :</strong></td>
                <td style="width: 33%" id="AssetCode"><?php echo $extra['faCode'] ?></td>
                <td class="theadtr" style="width: 17%"><strong><?php echo $this->lang->line('assetmanagement_serial_no');?><!--Serial No--> : </strong></td>
                <td style="width: 32%"><?php echo $extra['faUnitSerialNo'] ?></td>
            </tr>
            <tr>
                <td class="theadtr" style="width: 16%"><strong><?php echo $this->lang->line('assetmanagement_bar_code');?><!--Barcode--></strong></td>
                <td style="width: 33%" id="AssetCode"><?php echo $extra['barcode'] ?></td>
                <td class="theadtr" style="width: 17%"><strong><?php echo $this->lang->line('assetmanagement_manufacture');?><!--Manufacture--> :</strong></td>
                <td style="width: 32%"><?php echo $extra['manufacture'] ?></td>
            </tr>
            <tr>
                <td class="theadtr" style="width: 16%"><strong><?php echo $this->lang->line('common_description');?><!--Description--> :</strong></td>
                <td style="width: 33%" colspan="3" id="AssetCode"><?php echo $extra['assetDescription'] ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('common_comments');?><!--comments--> :</strong></td>
                <td colspan="3"><?php echo $extra['comments'] ?></td>
            </tr>


            <tr>
                <td><strong><?php echo $this->lang->line('assetmanagement_date_acquired');?><!--Date Acquired--> :</strong></td>
                <td><?php echo date('Y-m-d', strtotime($extra['dateAQ'])) ?></td>
                <td><strong><?php echo $this->lang->line('assetmanagement_depreciation_date_start');?><!--Depreciation Date Start--> :</strong></td>
                <td><?php echo date('Y-m-d', strtotime($extra['dateDEP'])) ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('assetmanagement_asset_capitalized_date');?><!--Asset Capitalized Date--> :</strong></td>
                <td><?php echo date('Y-m-d', strtotime($extra['postDate'])) ?></td>
                <td><strong><?php echo $this->lang->line('assetmanagement_life_time_in_month');?><!--Life time in months--> :</strong></td>
                <td><?php echo $extra['depMonth'] ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('assetmanagement_dep');?><!--DEP--> % :</strong></td>
                <td><?php echo $extra['DEPpercentage'] ?></td>
                <td><strong><?php echo $this->lang->line('assetmanagement_unit_price');?><!--Unit Price--> :</strong></td>
                <td><?php echo number_format($extra['companyLocalAmount'], $extra['companyLocalCurrencyDecimalPlaces']) . ' ' . $extra['companyLocalCurrency'] ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('assetmanagement_asset_location');?><!--Asset Location--> : </strong></td>
                <td><?php echo $extra['locationName'] ?></td>
            </tr>
            <tr>
                <th style="background-color: #c8d7e0;" colspan="4">
                   <?php echo $this->lang->line('assetmanagement_asset_categorization');?> <!--Asset Categorization-->
                </th>
            </tr>
            <tr>
                <td class="theadtr" style="width: 17%"><strong><?php echo $this->lang->line('assetmanagement_asset_type');?><!--Asset Type----> :</strong></td>
                <td style="width: 32%"><?php echo $assetTypes[$extra['assetType']] ?></td>
                <td><strong><?php echo $this->lang->line('common_supplier');?><!--Supplier--> :</strong></td>
                <td><?php echo $extra['supplierName'] ?></td>
            </tr>
            <tr>
                <td class="theadtr" style="width: 17%"><strong><?php echo $this->lang->line('assetmanagement_segment_code');?><!--Segment Code--> :</strong></td>
                <td style="width: 32%"><?php echo $extra['segmentCode'] ?></td>
                <td></td>
                <td></td>
            </tr>

            <tr>
                <td><strong><?php echo $this->lang->line('assetmanagement_main_category');?><!--Main Category--> :</strong></td>
                <td><?php echo $extra['MaincatDescription'] ?></td>
                <td><strong><?php echo $this->lang->line('assetmanagement_sub_category');?><!--Sub Category--> :</strong></td>
                <td><?php echo $extra['SubcatDescription'] ?></td>
            </tr>

            <tr>
                <td><strong><?php echo $this->lang->line('assetmanagement_cost_account');?><!--Cost Account--> :</strong></td>
                <td><?php echo $extra['costGLCode'] ?> | <?php echo $extra['costGLCodeDes'] ?></td>
                <td><strong><?php echo $this->lang->line('assetmanagement_acc_dep_gl_code');?><!--Acc Dep GL Code--> :</strong></td>
                <td><?php echo $extra['ACCDEPGLCODE'] ?>  | <?php echo $extra['ACCDEPGLCODEdes'] ?> </td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('assetmanagement_dep_gl_code');?><!--Dep GL Code--> :</strong></td>
                <td><?php echo $extra['DEPGLCODE'] ?> | <?php echo $extra['DEPGLCODEdes'] ?></td>
                <td><strong><?php echo $this->lang->line('assetmanagement_disposal_gl');?><!--Disposal GL Code--> :</strong></td>
                <td><?php echo $extra['DISPOGLCODE'] ?> | <?php echo $extra['DISPOGLCODEdes'] ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('assetmanagement_post_gl');?><!--Post GL--></strong></td>
                <td><?php echo $extra['postGLCode'] ?> | <?php echo $extra['postGLCodeDes'] ?></td>
             <?php if ($type == true){?>
                <td><strong>Origin Document :<strong></td>
                <td>
                <?php if(!empty($systemcode)){?>
                    <a href="#" class="drill-down-cursor"
                       onclick="documentPageView_modal('<?php echo $extra["docOrigin"] ?>',<?php echo $extra["docOriginSystemCode"] ?>)"><?php echo $systemcode ?></a>
                <?php }else {?>
                        <?php echo '-'?>
                <?php }?>


                </td>
                 <?php }?>

            </tr>

            </tbody>
        </table>
    </div>

<?php
//print_r($extra);