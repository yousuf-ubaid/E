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
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;" width="29%;">Maintenance Type </td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Qty </td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Unit Cost</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Total</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Crew Members</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Done YN</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Comment</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Action</td>
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
                        echo form_dropdown('maintenancecriteria', $maintenancecriteria, $selected, 'class="form-control select2" name ="maintenancecriteria" id ="maintenancecriteria" onchange="maintenacetypedes(this.value,'.$val['maintenanceDetailID'].')"'); ?>
                    </td>
                   <?php if($val['maintenanceBy']==1){?>

                    <td class="mailbox-star" width="15%"><?php /*echo $val['maintenanceQty'] */?>
                        <input type="text" name="maintenaceqty" placeholder="QTY" id="maintenaceqty<?php echo $val['maintenanceDetailID']?> " onchange="maintenaceqtyupdate(this.value,<?php echo $val['maintenanceDetailID']?>,<?php echo  $val['maintenanceMasterID']?>)" value="<?php echo $val['maintenanceQty']?>" style="width: 50%;" readonly>
                    </td>
                    <td class="mailbox-star" width="15%"><input type="text" name="unitcost" placeholder="UNIT COST" id="unitcost<?php echo $val['maintenanceDetailID']?> " onchange="maintenaceunitcostupdate(this.value,<?php echo $val['maintenanceDetailID']?>,<?php echo  $val['maintenanceMasterID']?>)" value="<?php echo $val['unitCost']?>" style="width: 70%;" readonly></td>

                   <?php } else {?>
                       <td class="mailbox-star" width="15%"><?php /*echo $val['maintenanceQty'] */?>
                           <input type="text" name="maintenaceqty" placeholder="QTY" id="maintenaceqty<?php echo $val['maintenanceDetailID']?> " onchange="maintenaceqtyupdate(this.value,<?php echo $val['maintenanceDetailID']?>,<?php echo  $val['maintenanceMasterID']?>)" value="<?php echo $val['maintenanceQty']?>" style="width: 50%;">
                       </td>
                       <td class="mailbox-star" width="15%"><input type="text" name="unitcost" placeholder="UNIT COST" id="unitcost<?php echo $val['maintenanceDetailID']?> " onchange="maintenaceunitcostupdate(this.value,<?php echo $val['maintenanceDetailID']?>,<?php echo  $val['maintenanceMasterID']?>)" value="<?php echo $val['unitCost']?>" style="width: 70%;"></td>

                    <?php  }?>

                    <td class="mailbox-star" width="10%"><?php echo number_format($val['maintenanceAmount'],$val['transactionCurrencyDecimalPlacesmaster']) ?></td>


                    <td class="mailbox-star" width="10%">

                        <?php

                        $selected = $val['maintenanceCriteriaID'] ;
                        $companyid = current_companyID();

                        $maintenacecrew = $this->db->query("SELECT * FROM `fleet_maintenancecrewdetails` where maintenanceMasterID = {$val['maintenanceMasterIDmaster']} And companyID = {$companyid}")->result_array();
                        $data_arr = array('' => 'Select a Crew Member');
                        if (!empty($maintenacecrew)) {
                            foreach ($maintenacecrew as $row) {
                                $data_arr[trim($row['maintenanceCrewID'] ?? '')] = trim($row['name'] ?? '');
                            }
                        }
                        $crewid = $val['crewID'];



                        echo form_dropdown('maintenacecrewid', $data_arr, $crewid, 'class="form-control select2" name ="maintenacecrewid" id ="maintenacecrewid" onchange="maintenacecrewupdate(this.value,'.$val['maintenanceDetailID'].')"'); ?>

                        <?php /*echo $val['crewmembername'] */?>


                    </td>


                    <td class="mailbox-star" width="10%">
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns" style="text-align: center">
                                <input id="isdone_<?php echo $val['maintenanceDetailID'] ?>"
                                       type="checkbox"<?php echo $status ?>
                                       data-caption=""
                                       class="columnSelected isdone"
                                       name="isdone"
                                       value="<?php echo $val['maintenanceDetailID'] ?>">
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-star" width="10%"><input type="text" name="commentsmaintenace" placeholder="Comment" id="commentsmaintenace_<?php echo $val['maintenanceDetailID']?> " onchange="maintenacedetailscommet(this.value,<?php echo $val['maintenanceDetailID']?>)" value="<?php echo $val['comment']?>"></td>
                    <td class="mailbox-attachment taskaction_td" width="5%">
                        <a
                            onclick="delete_vmaintenace_details(<?php echo $val['maintenanceDetailID'] ?>)"><span
                                title="Delete" rel="tooltip"
                                class="glyphicon glyphicon-trash"
                                style="color:rgb(209, 91, 71);"></span></a>

                        <?php if($val['maintenanceBy']==1){?>
                        &nbsp;&nbsp;|&nbsp;&nbsp;


                        <a
                                onclick="add_spare_parts(<?php echo $val['maintenanceCriteriaID'] ?>,<?php echo $maintenanceMasterID ?>,<?php echo $val['maintenanceDetailID'] ?>)"><span
                                    title="Add parts" rel="tooltip"
                                    class="fa fa-wrench"></span></a>
                        </span>
                        <?php }?>
                    </td>
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
