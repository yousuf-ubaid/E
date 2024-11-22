<?php
$primaryLanguage = getPrimaryLanguage();
$this->load->helper('operation_ngo_helper');

?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
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

    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 3px 0 3px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .numberColoring {
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>

<input type="hidden" name="proposalid" id="proposalid" value="<?php echo $proposalID?>">

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Beneficiary" id="mfq_user_groupdetail_model">
    <div class="modal-dialog modal-lg" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Assign Beneficiary </h4>
            </div>
            <br>
            <br>
            <form class="form-horizontal" id="add_beneficiary_form" >
                <input type="hidden" name="proposalid" id="proposalid" value="<?php echo $proposalID?>">
                <input type="hidden" name="projectID" id="projectID" value="<?php echo $subprojectid['masterID']?>">
                <div class="modal-body">
                    <div id="sysnc">
                        <div class="table-responsive">
                            <table id="employee_sync" class="table table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="min-width: 12%">Code</abbr></th>
                                    <th style="min-width: 12%">Beneficiary Name</th>
                                    <th style="min-width: 12%">Total Sqft</th>
                                    <th style="min-width: 12%">Total Cost</th>
                                    <th style="min-width: 12%">Estimated Value</th>
                                    <th style="min-width: 5%">&nbsp;
                                        <button type="button" data-text="Add" onclick="add_beneficiary()"
                                                class="btn btn-xs btn-primary">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Assign Beneficiary
                                        </button>
                                    </th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <strong class="task-cat-upcoming-label">
            PROJECT :
            <?php
            if(isset($project_beneficiaries[0]))
            {
                echo $project_beneficiaries[0]['projectName'];
            } ?></strong>
</div>
</div>

<?php if ($proposaltype == '1' || $proposaltype == '') { ?>
<?php if (!empty($project_beneficiaries)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="10">
                    <div class="task-cat-upcoming-label">Total Beneficiaries</div>
                    <div class="taskcount"><?php echo  sizeof($project_beneficiaries)?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;" width="5%">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;" width="10%">Beneficiary ID</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;" width="15%">Beneficiary Name</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;" width="15%">Address</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;" width="10%">Contact No</td>
            </tr>

            <?php
            $x = 1;
            foreach ($project_beneficiaries as $val) { ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['systemCode']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['nameWithInitials']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['address']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['beneficiarytelephone']; ?></a></td>

                </tr>
                <?php
                $x++;
            } ?>
            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO BENEFICIARIES TO DISPLAY FOR THE PROJECT</div>
    <?php
} ?>

<?php } else { ?>
    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-primary pull-right"
                    onclick="beneficiary_assign_model()" id="beneficiaryassign">
                <i class="fa fa-plus"></i> Assign Beneficiary
            </button>
        </div>
    </div>
    <?php
    $totalcost = 0;
    $date_format_policy = date_format_policy();
    if (!empty($header)) { ?>
        <br>
        <div class="table-responsive mailbox-messages">
            <table class="table table-hover table-striped">
                <tbody>
                <tr class="task-cat-upcoming">
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Beneficiary Code</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Beneficiary Name</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center">Own Land Available</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center">Total Sq.ft</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center">Total Cost(<?php echo $header[0]['CurrencyCode'] ?>)</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: right">Estimated Value(<?php echo $header[0]['CurrencyCode'] ?>)</td>


                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Action</td>
                </tr>

                <?php
                $x = 1;
                $totalcost = 0;
                foreach ($header as $val) {

                    $totalcost += $val['proposaltotalEstimatedValue'];
                    ?>
                    <tr>
                        <input type="hidden" name="proposalbeneficiary" id="proposalbeneficiary"
                               value="<?php echo $val['proposalID'] ?>">
                        <td class="mailbox-star" width="1%"><?php echo $x; ?></td>
                        <td class="mailbox-star" width="10%"><?php echo $val['benCode'] ?></td>
                        <td class="mailbox-star" width="10%"><?php echo $val['name'] ?></td>
                        <td class="mailbox-star"
                            width="10%"><?php echo ownlandavailablestatus_pp($val['ownLandAvailable']); ?></td>

                        <td class="mailbox-star" width="10%"
                            style="text-align: center"><?php echo $val['totalSqFtben'] ?></td>
                        <td class="mailbox-star" width="15%"
                            style="text-align: right"><?php echo $val['totalCostben'] ?></td>
                        <td class="mailbox-star" width="15%"
                            style="text-align: right"><?php echo number_format(floatval($val['proposaltotalEstimatedValue']), 2); ?></td>

                        <td class="mailbox-star" width="1%"><span class="pull-right"><a
                                        onclick="delete_beneficiarys(<?php echo $val['proposalBeneficiaryID'] ?>,<?php echo $val['proposalID'] ?>)"><span
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
                    <td class="text-right" colspan="5">
                        Total
                    </td>
                    <td class="text-right">
                        <?php echo number_format($totalcost, 2) ?>
                    </td>
                    <td colspan="2">

                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <?php
    } else { ?>
        <br>
        <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
        <?php
    }
    ?>

<?php } ?>


<script>
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
    });


    function beneficiary_assign_model() {
        var proposalid = $('#proposalid').val();
        if (proposalid) {
            selectedItemsSync = [];
            template_userGroupDetail(proposalid);
            $('#mfq_user_groupdetail_model').modal('show');
        }
    }

    function add_beneficiary() {
        var proposalid = $('#proposalid').val();
        var data = $('#add_beneficiary_form').serializeArray();
        data.push({'name': 'proposalid', 'value': proposalid});
        data.push({'name': 'selectedItemsSync', 'value': selectedItemsSync});

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("OperationNgo/assign_beneficiary_for_project_direct"); ?>',
            dataType: 'json',
            data: data,
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    beneficiary_details(proposalid);
                    $("#mfq_user_groupdetail_model").modal('hide');
                    $('.extraColumns input').iCheck('uncheck');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function template_userGroupDetail(proposalID) {
        oTable = $('#employee_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
            "sAjaxSource": "<?php echo site_url('OperationNgo/fetch_project_proposal_beneficiary_project'); ?>",
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
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });

                $('input').on('ifChecked', function (event) {
                    ItemsSelectedSync(this);
                });
                $('input').on('ifUnchecked', function (event) {
                    ItemsSelectedSync(this);
                });

                $('input').on('ifChanged', function(){
                    changeMandatory(this);
                });



            },

            "aoColumns": [
                {"mData": "benificiaryID"},
                {"mData": "systemCode"},
                {"mData": "name"},
                {"mData": "totalsqftadd"},
                {"mData": "totalcostadd"},
                {"mData": "estimatedvalue"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "projectID", "value": $('#projectID').val()});
                aoData.push({"name": "proposalID", "value": proposalID});
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
    $('input').on('ifChanged', function(){
        changeMandatory(this);
    });

    function changeMandatory(obj) {
        var status = ($(obj).is(':checked')) ? 1 : 0;
        var str = $(obj).attr('data-value');
        var value = $(obj).val();
        $(obj).closest('tr').closest('tr').find('.changestatus-' + str).val(status);
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
    function delete_beneficiarys(proposalBeneficiaryID) {
        var proposalID = $('#proposalid').val();
        swal({
                title: "Are you sure?",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "Cancel"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'proposalBeneficiaryID': proposalBeneficiaryID},
                    url: "<?php echo site_url('OperationNgo/delete_project_proposal_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            beneficiary_details(proposalID);
                        } else {
                            myAlert('e', 'Deletion Failed');
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

</script>



