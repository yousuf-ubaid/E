<br>
<header class="head-title">
    <h2>Equipment Planning</h2>
</header>

<div class="row" style="margin-top: 10px;">
    <div class="col-md-12">

        <div class="col-md-9">
            <a  href="<?php echo site_url('Boq/downloadExcel'); ?>" type="button" class="btn btn-success btn-sm pull-right" style="margin-right: -2%">
                <i class="fa fa-file-excel-o"></i>Download Excel
            </a>
        </div>
        <div class="col-md-1">
            <button type="button" onclick="upload_excel_equipmentplanning(<?php echo $headerID?>,<?php echo $boq_detailID?>)" class="btn btn-primary btn-sm pull-right" id="exceluploadattdance">
                <i class="fa fa-arrow-circle-up"></i>Upload
            </button>
        </div>
        <div class="col-md-1">
            <button onclick="asset_detail_modal()" type="button" class="btn btn-sm btn-primary pull-right">
                Add Asset  <span class="glyphicon" aria-hidden="true"></span></button>
        </div>



    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="col-sm-11" style="margin-left: 3%">
    <div class="table-responsive" style="width: 100%">
        <table class="table table-striped table-condensed table-hover">
            <thead>
            <tr>
                <th class='theadtr'>#</th>
                <th class='theadtr'>Type</th>
                <th class='theadtr'>Supplier</th>
                <th class="theadtr" style="text-align: left" width="1%;">Fa Code</th>
                <th class='theadtr' style="text-align: left" width="22%">Asset Description</th>
                <th class='theadtr' style="text-align: left"> Description</th>
                <th class='theadtr' style="text-align: left"> Rented Period</th>
                <th class='theadtr' style="text-align: left">Cost</th>
                <th class='theadtr' style="text-align: left">Operator YN</th>
                <th  class='theadtr'>Action</th>

            </tr>
            </thead>
            <tbody>
            <?php
            $num = 1;
            if (!empty($asset_view)) {
                foreach ($asset_view as $val) { ?>
                    <tr>
                        <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                        <td class="text-left"><?php echo $val['equipmentType']; ?></td>
                        <td class="text-left"><?php echo $val['supplierName']; ?></td>
                        <td class="text-left"><?php echo $val['faCode']; ?></td>
                        <td class="text-left"><?php echo $val['assetDescription']; ?></td>
                        <td class="text-left"><?php echo $val['equipmentDescription']; ?></td>
                        <td class="text-left"><?php echo $val['rentedPeriod']; ?></td>
                        <td class="text-left"><?php echo $val['cost']; ?></td>
                        <td class="text-left"><?php echo $val['operatoravailability']; ?></td>

                        <td class="text-right">

                            <?php if($val['equipmentTypeID']=='2'){?>
                                <a
                                    onclick="linkasset_equipment(<?php echo $val['activityplanningID'] ?>);"><span
                                        title="Link Asset" rel="tooltip"
                                        style="color:rgb(209, 91, 71);"></span>  <i class="fa fa-link" aria-hidden="true"></i></a>
                                &nbsp;|
                            <?php }?>
                            <span class="pull-right">&nbsp;
                                <a
                                    onclick="delete_equipment_plning(<?php echo $val['activityplanningID'] ?>);"><span
                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                        style="color:rgb(209, 91, 71);"></span></a>




                        </td>
                    </tr>

                    <?php
                    $num++;
                }
            } else {
                echo '<tr class="danger"><td colspan="10" class="text-center">No Records Found</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
        </table>
    </div>
</div>
</div>
