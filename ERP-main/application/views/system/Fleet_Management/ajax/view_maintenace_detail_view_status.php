<?php
$maintenancecriteria = load_all_maintenacecriteria();
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
if (!empty($detail)) { ?>

    <div class="table-responsive mailbox-messages" id="advancerecid">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;" width="29%;">Maintenace Type </td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Qty </td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Unit Cost</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Total</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Crew Members</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Done YN</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Comment</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">&nbsp;</td>
            </tr>
            <?php
            $x = 1;
            $total = 0;
            $selected;
            foreach ($detail as $val) {

                if ($val['doneYN'] == 1) {
                    $status = "checked";
                } else {
                    $status = "";
                }
                ?>
                <tr>
                    <td class="mailbox-star" width="1%"> <?php echo $x; ?></td>
                    <td class="mailbox-star" width="10%">
                        <?php
                        $selected = $val['maintenanceCriteriaID'] ;
                        echo form_dropdown('maintenancecriteria', $maintenancecriteria, $selected, 'class="form-control select2" name ="maintenancecriteria" id ="maintenancecriteria" onchange="maintenacetypedes(this.value,'.$val['maintenanceDetailID'].')" disabled' ); ?>
                    </td>
                    <td class="mailbox-star" width="10%"><?php /*echo $val['maintenanceQty'] */?>
                        <input type="text" name="maintenaceqty" placeholder="QTY" id="maintenaceqty<?php echo $val['maintenanceDetailID']?> " onchange="maintenaceqtyupdate(this.value,<?php echo $val['maintenanceDetailID']?>)" value="<?php echo $val['maintenanceQty']?>" style="width: 50%;" readonly>
                    </td>
                    <td class="mailbox-star" width="10%"><input type="text" name="unitcost" placeholder="UNIT COST" id="unitcost<?php echo $val['maintenanceDetailID']?> " onchange="maintenaceunitcostupdate(this.value,<?php echo $val['maintenanceDetailID']?>)" value="<?php echo $val['unitCost']?>" style="width: 70%;" readonly> </td>
                    <td class="mailbox-star" width="10%"><?php echo $val['maintenanceAmount'] ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['crewmembername'] ?></td>
                    <td class="mailbox-star" width="10%">
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns" style="text-align: center">
                                <input id="isdone_<?php echo $val['maintenanceDetailID'] ?>"
                                       type="checkbox"<?php echo $status ?>
                                       data-caption=""
                                       class="columnSelected isdone"
                                       name="isdone"
                                       value="<?php echo $val['maintenanceDetailID'] ?>" disabled>
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-star" width="10%"><input type="text" name="commentsmaintenace" placeholder="Comment" id="commentsmaintenace_<?php echo $val['maintenanceDetailID']?> " onchange="maintenacedetailscommet(this.value,<?php echo $val['maintenanceDetailID']?>)" value="<?php echo $val['comment']?>" readonly></td>

                    <?php if($val['maintenanceBy']==1){?>
                    <td class="mailbox-attachment taskaction_td" width="5%">
                        <a
                                onclick="add_spare_parts_status(<?php echo $val['maintenanceCriteriaID'] ?>,<?php echo $maintenanceMasterID ?>,<?php echo $val['maintenanceDetailID'] ?>)"><span
                                    title="View parts" rel="tooltip"
                                    class="fa fa-wrench"></span></a>
                        </span>
                    </td>
                    <?php }?>

                </tr>
                <?php
                $x++;
                $total += $val ['maintenanceAmount'];
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right" colspan="4">
                    Total
                </td>
                <td class="text">
                    <?php echo number_format($total,2) ?>
                </td >
                <td colspan="4">

                </td >
            </tr>
            </tfoot>
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results" id="advancerecid">NO RECORDS FOUND.</div>
    <?php
}
?>
<script>
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        $('.select2').select2();
    });
    $('.extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });
    $('input').on('ifChecked', function (event) {
        if ($(this).hasClass('isdone')) {
            update_is_doneyn_stauts(this.value, 1);

        }
    });

    $('input').on('ifUnchecked', function (event) {
        if ($(this).hasClass('isdone')) {
            update_is_doneyn_stauts(this.value, 0);

        }
    });
</script>
