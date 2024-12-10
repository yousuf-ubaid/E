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
    $totalcost = 0;
    $date_format_policy = date_format_policy();
    if (!empty($header)) { ?>
        <div class="table-responsive mailbox-messages">
            <table class="table table-hover table-striped">
                <tbody>
                <tr class="task-cat-upcoming">
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Beneficiary Code</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Beneficiary Name</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Own Land Available</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Comments</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Total Sqft</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Total Cost</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Estimated Value</td>

                    <?php if ($header[0]['confirmedYN'] == 1 && $header[0]['approvedYN'] == 1) { ?>
                        <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Is Qualified</td>
                    <?php } ?>

                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Action</td>
                </tr>

                <?php
                $x = 1;
                foreach ($header as $val) {

                if ($val['isQualified'] == 1) {
                    $status = "checked";
                    $totalcost += $val["proposaltotalEstimatedValue"];
                } else {
                    $status = "";
                }

                ?>
                <tr>
                    <input type="hidden" name="proposalbeneficiary" id="proposalbeneficiary"
                           value="<?php echo $val['proposalID'] ?>">
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['benCode'] ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['name'] ?></td>
                    <td class="mailbox-star"
                        width="10%"><?php echo ownlandavailablestatus_pp($val['ownLandAvailable']); ?></td>
                    <td class="mailbox-star" width="10%"
                        style="text-align: center"><?php echo $val['ownLandAvailableComments'] ?></td>
                    <td class="mailbox-star" width="10%"
                        style="text-align: center"><?php echo $val['totalSqFtben'] ?></td>
                    <td class="mailbox-star" width="10%"
                        style="text-align: center"><?php echo $val['totalCostben'] ?></td>
                    <td class="mailbox-star" width="10%"
                        style="text-align: right"><?php echo number_format(floatval($val['proposaltotalEstimatedValue']), 2); ?></td>

                    <?php if (($val['confirmedYN'] == 1 && $val['isConvertedToProject'] == 0) || ($val['confirmedYN'] == 1 && $val['approvedYN'] == 1 && $val['isConvertedToProject'] == 0)) { ?>
                        <td class="mailbox-star test" width="10%" style="text-align: center">
                            <div class="skin-section extraColumns" style="text-align: center">
                                <input id="isqualified_<?php echo $val['proposalBeneficiaryID'] ?>"
                                       type="checkbox"<?php echo $status ?>
                                       data-caption=""
                                       class="columnSelected isQualifiedCls"
                                       name="isqualified" value="<?php echo $val['proposalBeneficiaryID'] ?>">
                            </div>
                        </td>
                    <?php }
                    else if(($val['isConvertedToProject'] == 1)) {
                        echo '<td class="mailbox-star"
                        width="10%">'.qualifiedstatusbeneficiary($val['isQualified']);'</td>';
                    }
                        ?>

                        <td class="mailbox-star" width="5%"><span class="pull-right"><a
                                        onclick="delete_beneficiary(<?php echo $val['proposalBeneficiaryID'] ?>,<?php echo $val['proposalID'] ?>)"><span
                                            title="Delete" rel="tooltip"
                                            class="glyphicon glyphicon-trash"
                                            style="color:rgb(209, 91, 71);"></span></a></span>
                        </td>
                    </tr>
                    <?php
                    $x++;
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>
                    <td class="text-right" colspan="6">
                        Total Cost
                    </td>
                    <td class="text-right">
                        <?php echo number_format($totalcost, 2) ?>
                    </td>
                    <td colspan="2">

                    </td>
                </tr>
                </tfoot>
            </table>
            <br>
            <?php if (($val['confirmedYN'] == 1 && $val['approvedYN'] == 1 && $val['isConvertedToProject'] != 1)){?>
            <div class="form-group col-sm-12">
                <div class="text-right m-t-xs">
                    <button type="button" class="btn btn-primary" id="savebtn"
                            onclick="update_pp_beneficiary_qualified()">Save
                    </button>
                </div>
            </div>
            <?php } ?>

        </div>
        <?php
    } else { ?>
        <br>
        <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
        <?php
    }
    ?>
    <script type="text/javascript">
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });


        function update_pp_beneficiary_qualified() {
            var proposalID = $('#proposalbeneficiary').val();
            var checkedItem = '';
            var uncheckedItem = '';
            $('.isQualifiedCls').each(function () {
                thisVal = $(this).val();
                if ($(this).is(":checked")) {
                    checkedItem += (checkedItem != '') ? ',' + thisVal : thisVal;
                }
                else {
                    uncheckedItem += (uncheckedItem != '') ? ',' + thisVal : thisVal;
                }

            });
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {proposalID: proposalID, checkedItem: checkedItem, uncheckedItem: uncheckedItem},
                url: "<?php echo site_url('OperationNgo/update_pp_beneficiary_qualified'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    fetch_beneficery_details();
                }, error: function () {
                    stopLoad();
                    myAlert('e', 'error,Please contact support team');
                }
            });


        }


        /*$('input').on('ifChecked', function (event) {
            update_pp_beneficiary_qualified(this.value, 1);

        });

        $('input').on('ifUnchecked', function (event) {
            update_pp_beneficiary_qualified(this.value, 0);

        });*/

    </script>
