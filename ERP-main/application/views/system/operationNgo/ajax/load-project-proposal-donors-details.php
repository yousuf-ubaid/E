<?php
$total = 0;
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$totalcostben = 0;
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

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }
</style>
<form name="test" id="test" class="form-horizontal">
    <?php if (!empty($beneficiary)) {

        foreach ($beneficiary as $val) {
            if($val['isQualified']==1)
            $totalcostben += $val["totalEstimatedValue"];
            ?>
        <?php }
    } ?>
    <?php
    if (!empty($header)) { ?>

    <div class="mailbox-messages">
        <table class="table table-hover table-striped" width="100%" id="test">
            <tbody>
            <?php if ($header[0]['approvedYN'] == 1 && $header[0]['confirmedYN'] == 1) { ?>
                <tr class="task-cat-upcoming">
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Donor Name</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"></td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Is Submited</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center"></td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Is Approved</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center">
                        Amount(<?php echo $header[0]['CurrencyCode'] ?>)
                    </td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Action</td>
                </tr>
            <?php } else { ?>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Donor Name</td>
                <?php if ($header[0]['confirmedYN'] != 1) { ?>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Action</td>
                <?php } ?>
            <?php } ?>
            <?php if ($header[0]['approvedYN'] == 1 && $header[0]['confirmedYN'] == 1)
            {
            ?>
            <?php
            $x = 1;
            foreach ($header

            as $val) {
            if ($val['isSubmitted'] == 1) {
                $status = "checked";
            } else {
                $status = "";
            }
            if ($val['isApproved'] == 1) {
                $statusapproved = "checked";
            } else {
                $statusapproved = "";
            }
            $total += $val['commitedAmount']
            ?>
            <tr>

             <?php
             if($val['isConvertedToProject'] == 1) {
                 $disable = 'disabled';
             } else {
                 $disable = ' ';
             }?>

                <input type="hidden" name="proposaldonor" id="proposaldonor"
                       value="<?php echo $val['proposalID'] ?>">
                <input type="hidden" name="donor[]" id="donor_<?php echo $val['donorID']; ?>"
                       value="<?php echo $val['donorID'] ?>">


                <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                <td class="mailbox-star" width="10%"><?php echo $val['name'] ?></td>

                <!--******  IS Submiited ******-->
                <td class="mailbox-star" width="5%">
                    <div class="skin skin-square">
                        <div class="skin-section extraColumns" style="text-align: right">
                            <input id="isSubmited_<?php echo $val['donorID'] ?>"
                                   type="checkbox" <?php echo $status ?>
                                   data-caption=""
                                   class="columnSelected issubmitted"
                                   name="issubmitted[]"
                                   value="<?php echo $val['donorID'] ?>" <?php echo $disable?>>
                        </div>
                    </div>
                </td>
                <td class="mailbox-star" width="10%">
                    <div class="input-group issubmited" id="issubmited"
                    ">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="submiteddate[]"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           id="submiteddateID_<?php echo $val['donorID']; ?>"
                           data-id="<?php echo $val['donorID']; ?>"
                           class="form-control issubmitteddate"
                           value="<?php echo $val['submittedDate']; ?>" <?php echo $disable?>>

                    </div>
    </div>
    </td>

    <td class="mailbox-star" width="5%">

        <div class="skin-section extraColumns" style="text-align: right">
            <input id="isApproved_<?php echo $val['donorID'] ?>"
                   type="checkbox" <?php echo $statusapproved ?> data-caption=""
                   class="columnSelected isApprovedCls"
                   name="isapproved[]" value="<?php echo $val['donorID'] ?>" <?php echo $disable?>
            >
        </div>

    </td>
    <td class="mailbox-star" width="10%">
        <?php if ($val['isSubmitted'] == 1) { ?>
            <?php if ($val['isApproved'] == 1) { ?>

                <div class="input-group approveddatecls" id="approveddatecls"
                ">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="approveddate[]"
                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                       id="approveddateID_<?php echo $val['donorID']; ?>"
                       data-id="<?php echo $val['donorID']; ?>"
                       class="form-control isapproveddate"
                       value="<?php echo $val['approvedDate']; ?>" <?php echo $disable?>>

                </div>

                <!-- <div class="input-group approveddatecls" id="approveddate"
            ">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <input type="text" name="approveddate[]"
                   data-inputmask="'alias': '<?php /*echo $date_format_policy */ ?>'"
                   id="approveddateID_<?php /*echo $val['donorID']; */ ?>"
                   data-id="<?php /*echo $val['donorID']; */ ?>"
                   class="form-control approveddate"
                   value="<?php /*echo $val['approvedDate']; */ ?>">

            </div>-->
            <?php } ?>
        <?php } ?>
    </td>
<!-- ****************-->
    <td class="mailbox-star" width="10%"><input type="text" class="form-control number addcommitments"
                                                name="addcommitments[]"
                                                id="add_commitment_<?php echo $val['donorID'] ?>"
                                                data-id="<?php echo $val['donorID'] ?>"
                                                value="<?php echo $val['commitedAmount'] ?>" <?php echo $disable?>></td>


    <td class="mailbox-star" width="5%" style="text-align: center">
        <a onclick="send_email_proposal(<?php echo $val['proposalID'] ?>,<?php echo $val['donorID'] ?>);"><span
                    title="Send Email" rel="tooltip"
                    class="glyphicon glyphicon-envelope"></span></a>&nbsp;|

        <a onclick="open_donors_add(<?php echo $val['proposalID'] ?>,<?php echo $val['donorID'] ?>);"><span
                    title="Add Beneficiaries" rel="tooltip"
                    class="glyphicon glyphicon-plus"></span></a>&nbsp;|
        <span title="Delete">
                            <a onclick="delete_donor(<?php echo $val['proposalDonourID'] ?>,<?php echo $val['proposalID'] ?>,<?php echo $val['donorID'] ?>);"><span
                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                        style="color:rgb(209, 91, 71);"></span></a>

                        </span>
    </td>
    </tr>
<?php
$x++;

}
?>
    </tbody>
    <tfoot>
    <tr>
        <td class="text-right" colspan="2">
            Total Estimated Cost : <?php echo number_format($totalcostben, 2); ?>
        </td>
        <td class="text-right">

        </td>
        <td class="text-right" style="color: red" colspan="">
            (Balance :<?php echo number_format(($totalcostben - $total), 2); ?>)
        </td>
        <td class="text-right" style="color: red">

        </td>
        <td class="text-right">
            Commited Amount :
        </td>
        <td class="text-right">
            <?php echo number_format($total, 2); ?>
        </td>
        <td>

        </td>
    </tr>
    <?php } else { ?>
        <?php
        $x = 1;
        foreach ($header as $val) { ?>
            <tr>
                <input type="hidden" name="proposaldonor" id="proposaldonor"
                       value="<?php echo $val['proposalID'] ?>">
                <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                <td class="mailbox-star" width="10%"><?php echo $val['name'] ?></td>
                <?php if ($val['confirmedYN'] != 1) { ?>
                    <td class="mailbox-star" width="5%" style="text-align: center">

                        <span title="Delete">
                            <a onclick="delete_donor(<?php echo $val['proposalDonourID'] ?>,<?php echo $val['proposalID'] ?>,<?php echo $val['donorID'] ?>);"><span
                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                        style="color:rgb(209, 91, 71);"></span></a>
                        </span>
                    </td>
                    </span>
                <?php } ?>
            </tr>
            <?php
            $x++;
        }
        ?>
    <?php } ?>
    </tfoot>
    </table>
    <br>
    <div class="form-group col-sm-12">
        <?php if($val['isConvertedToProject'] != 1) {?>
        <div class="text-right m-t-xs">
            <button type="button" class="btn btn-primary" id="save-btn-proposalupdate" onclick="update_donor_details()">
                Save
            </button>
        </div>
        <?php }?>
    </div>
    </div>
</form>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Donors"
         id="pp_donors_add_beneficiary">
        <div class="modal-dialog modal-lg" style="width:50%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="fetch_project_donors_details()"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Assign Benificairy</h4>
                </div>
                <div class="modal-body">
                    <div id="sync">
                        <div class="table-responsive">
                            <input type="hidden" name="projectid" id="projectid">
                            <input type="hidden" name="donorid" id="donorid">
                            <table id="beneficiary_sync" class="table table-striped table-condensed">
                                <thead>
                                <th>#</th>
                                <th>BENEFICIARY CODE</th>
                                <th>BENEFICIARY NAME</th>
                                <th>
                                    <button type="button" data-text="Add" onclick="adddonorbeneficiary()"
                                            class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i> Add Benificiary
                                    </button>
                                </th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <h4 class="modal-title">&nbsp;&nbsp;&nbsp; Added Benficiaries</h4>
                <hr>
                <div id="sysnc">
                    <div class="table-responsive">
                        <input type="hidden" name="groupform" id="group_employee">
                        <table id="savedbeneficiaries_donors" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 12%">BENEFICIARY CODE</abbr></th>
                                <th style="min-width: 12%">BENEFICIARY NAME</th>
                                <th style="min-width: 5%">&nbsp;

                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"
                            onclick="fetch_project_donors_details()">Close
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
    <?php
}
?>
<script type="text/javascript">


    var oTable;
    var oTable1;
    var selectedItemsSync = [];
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $(document).ready(function () {
        Inputmask().mask(document.querySelectorAll("input"));

/*
        $('input').on('ifChecked', function (event) {
            if ($(this).hasClass("issubmitted")) {
                $('.issubmited').removeClass('hide');
                  }



        });
        $('input').on('ifUnchecked', function (event) {
            if ($(this).hasClass("issubmitted")) {
                $('.issubmited').addClass('hide')
            }

        });*/

        $('.issubmited').datetimepicker({
            format: date_format_policy,
            widgetPositioning: {horizontal: 'left', vertical: 'bottom'},
            maxDate: 'now'
        });

        $('.approveddatecls').datetimepicker({
            format: date_format_policy,
            widgetPositioning: {horizontal: 'left', vertical: 'bottom'},
            maxDate: 'now'
        });

        $("[rel=tooltip]").tooltip();
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });

    function update_donor_details() {
        var data = $('#test').serializeArray();
        var issubmitcheack = '';
        var issubmituncheack = '';
        var isapprovedchk = '';
        var isapprovedunchk = '';
        var alldonors = '';
        $('.isApprovedCls').each(function () {
            thisVal = $(this).val();

            alldonors += (alldonors != '') ? ',' + thisVal : thisVal;

            if ($(this).is(":checked")) {
                isapprovedchk += (isapprovedchk != '') ? ',' + thisVal : thisVal;
            }
            else {
                isapprovedunchk += (isapprovedunchk != '') ? ',' + thisVal : thisVal;

            }
        });
        $('.issubmitted').each(function () {
            thisVal = $(this).val();
            if ($(this).is(":checked")) {
                issubmitcheack += (issubmitcheack != '') ? ',' + thisVal : thisVal;
            }
            else {
                issubmituncheack += (issubmituncheack != '') ? ',' + thisVal : thisVal;

            }
        });

        data.push({'name': 'issubmitcheack', 'value': issubmitcheack});
        data.push({'name': 'issubmituncheack', 'value': issubmituncheack});
        data.push({'name': 'isapprovedchk', 'value': isapprovedchk});
        data.push({'name': 'isapprovedunchk', 'value': isapprovedunchk});
        data.push({'name': 'alldonors', 'value': alldonors});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OperationNgo/update_donors_issubmited_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                update_date_donors();
                update_date_donors_appproved();
                myAlert(data[0], data[1]);
                fetch_project_donors_details();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }


    function open_donors_add(proposalDonourID, donorID) {
        selectedItemsSync = [];
        $('#projectid').val(proposalDonourID);
        $('#donorid').val(donorID);
        $('#pp_donors_add_beneficiary').modal('show');
        fetch_benificiaries(proposalDonourID, donorID);
        added_benificiaries_by_donor(proposalDonourID, donorID)
    }

    function send_email_proposal(proposalDonourID, donorID) {
        var form_data = $("#Send_Email_form").serialize();
        swal({
                title: "Are You Sure?",
                text: "You Want To Send This Mail",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "Yes",
                cancelButtonText: "No"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'proposalDonourID': proposalDonourID, 'donorID': donorID},
                    url: "<?php echo site_url('OperationNgo/send_proposal_donors'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {

                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function fetch_benificiaries(proposalID, donorID) {
        oTable = $('#beneficiary_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
            "sAjaxSource": "<?php echo site_url('OperationNgo/fetch_benificaries_donors'); ?>",
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
                $('.item-iCheck').iCheck('uncheck');
                if (selectedItemsSync.length > 0) {
                    $.each(selectedItemsSync, function (index, value) {
                        $("#selectItem_" + value).iCheck('check');
                    });
                }
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-blue',
                    radioClass: 'iradio_square_relative-blue',
                    increaseArea: '20%'
                });
                $('input').on('ifChecked', function (event) {
                    ItemsSelectedSync(this);

                });
                $('input').on('ifUnchecked', function (event) {
                    ItemsSelectedSync(this);
                });
            },

            "aoColumns": [
                {"mData": "benificiaryID"},
                {"mData": "systemCode"},
                {"mData": "nameWithInitials"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "proposalID", "value": proposalID});
                aoData.push({"name": "donorID", "value": donorID});
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

    function ItemsSelectedSync(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync);
            if (inArray == -1) {
                selectedItemsSync.push(value);
            }
        }
        else {
            var i = selectedItemsSync.indexOf(value);
            if (i != -1) {
                selectedItemsSync.splice(i, 1);
            }
        }
    }

    function adddonorbeneficiary() {
        var project = $('#projectid').val();
        var donorid = $('#donorid').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("OperationNgo/add_donorbeneficiary"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync, 'project': project, 'donorid': donorid},
            async: false,
            success: function (data) {
                if (data['status']) {
                    refreshNotifications(true);
                    oTable.draw();
                    oTable1.draw();
                    $('.donor').iCheck('uncheck');
                    $("#pp_donors_add_beneficiary").modal('show');
                } else {
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function added_benificiaries_by_donor(proposalID, donorID) {
        oTable1 = $('#savedbeneficiaries_donors').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
            "sAjaxSource": "<?php echo site_url('OperationNgo/fetch_savedbeneficiaries'); ?>",
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
                {"mData": "beneficiaryID"},
                {"mData": "systemCode"},
                {"mData": "nameWithInitials"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "proposalID", "value": proposalID});
                aoData.push({"name": "donorID", "value": donorID});
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

    function delete_donor_assign_beneficiaries(beneficiaryDonorID) {
        swal({
                title: "Are You Sure",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "cancel"
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('OperationNgo/delete_assign_beneficiaries'); ?>",
                    type: 'post',
                    data: {'beneficiaryDonorID': beneficiaryDonorID},
                    dataType: 'json',
                    cache: false,

                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            oTable.draw();
                            oTable1.draw();
                            $('.donor').iCheck('uncheck');
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }

    function update_date_donors() {
        var proposalID = $('#proposaldonor').val();
        var donorid = '';
        var date = '';
        var data = $('#test').serialize()
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data, /*{proposalID: proposalID, date: date, donorid: donorid}*/
            url: "<?php echo site_url('OperationNgo/update_donors_date'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                // myAlert(data[0], data[1]);

            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }

    function update_date_donors_appproved() {
        var proposalID = $('#proposaldonor').val();
        var data = $('#test').serialize()
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OperationNgo/update_donors_date_approved'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //  myAlert(data[0], data[1]);
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }
</script>
