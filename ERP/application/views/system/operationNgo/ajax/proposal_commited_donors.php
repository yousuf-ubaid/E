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

<div class="row">
    <div class="col-sm-12">
        <strong class="task-cat-upcoming-label">PROJECT :

            <?php
            if(isset($project_donors[0]))
            {
                echo $project_donors[0]['projectName'];
            } ?></strong>
    </div>
</div>
<input type="hidden" name="proposaldonorpro" id="proposaldonorpro" value="<?php echo $proposalid ?>">
<?php if($proposaltype == '1' || $proposaltype == '') {?>

<br>
<?php if (!empty($project_donors)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="10">
                    <div class="task-cat-upcoming-label">Total Donors</div>
                    <div class="taskcount"><?php echo  sizeof($project_donors)?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;" width="3%">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;" width="5%">Donor Name</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;" width="5%">Contact No</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;" width="5%">Contribution Amount</td>
            </tr>

          <?php
           $x = 1;
          $total = 0;
            foreach ($project_donors as $val) {
                $total +=$val['commitedAmount'];
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['donorname']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['donortelephoneno']; ?></a></td>
                    <td class="mailbox-name" style="text-align: left"><a href="#"> <?php echo $val['CurrencyCode'] . ' ' . number_format($val['commitedAmount'],2) ?></a></td>
                </tr>
              <?php
                $currencyCode = $val['CurrencyCode'];
            $x++;
            } ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right" colspan="3" >
                    Total Contribution
                </td>
                <td class="text">
                    <?php echo number_format($total, 2) . '(' . $currencyCode . ')'?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO DONORS TO DISPLAY FOR THE PROJECT</div>
   <?php
} ?>
<?php }else { ?>
    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-primary pull-right"
                    onclick="donors_assign_model_project()">
                <i class="fa fa-plus"></i> Assign Donors
            </button>
        </div>
    </div>
    <br>
<form name="test" id="test" class="form-horizontal">

    <?php
    if (!empty($header)) { ?>

    <div class="mailbox-messages">
        <table class="table table-hover table-striped" width="100%" id="test">
            <tbody>

                <tr class="task-cat-upcoming">

                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Donor Name</td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center">
                        Amount(<?php echo $header[0]['CurrencyCode'] ?>)
                    </td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center">

                    </td>
                    <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Action</td>
                </tr>

            <?php
            $x = 1;
            $total = 0;

            foreach ($header as $val) {
                $total +=$val['commitedAmount'];

                ?>

                <tr>

                <input type="hidden" name="proposaldonor" id="proposaldonor"
                       value="<?php echo $val['proposalID'] ?>">
                <input type="hidden" name="donor[]" id="donor_<?php echo $val['donorID']; ?>"
                       value="<?php echo $val['donorID'] ?>">
                    <td class="mailbox-star" width="5%" ><?php echo $x; ?></td>
                     <td class="mailbox-star"  width="10%"><?php echo $val['name'] ?></td>
                     <td class="mailbox-star" width="5%" ><input type="text" class="form-control number addcommitments"
                                                name="addcommitments[]"
                                                id="add_commitment_<?php echo $val['donorID'] ?>"
                                                data-id="<?php echo $val['donorID'] ?>"
                                                value="<?php echo $val['commitedAmount'] ?>"/></td>
                    <td class="mailbox-star" width="5%" > </td>

                  <td class="mailbox-star" width="5%">
                      <span title="Delete">
                            <a onclick="delete_donor_project(<?php echo $val['proposalDonourID'] ?>,<?php echo $val['proposalID'] ?>,<?php echo $val['donorID'] ?>);"><span
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
            Commited Amount :
        </td>
        <td class="text-right">
            <?php echo number_format($total, 2); ?>
        </td>
        <td class="text-right">

        </td>
        <td class="text-right">

        </td>



    </tr>


    </tfoot>
    </table>
    <br>
    <div class="form-group col-sm-12">
        <div class="text-right m-t-xs">
                <button type="button" class="btn btn-primary" id="save-btn-proposalupdate" onclick="update_donor_details_project()">
                    Save
                </button>
            </div>
    </div>
    </div>
</form>
<?php } else{
        echo '
         <br>
         <div class="search-no-results">THERE ARE NO DONORS TO DISPLAY FOR THE PROJECT</div>';
    } } ?>


<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Beneficiary" id="ngo_load_donors_model">
    <div class="modal-dialog modal-lg" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Assign Donors </h4>
            </div>
            <div class="modal-body">
                <div id="sysnc">
                    <div class="table-responsive">
                        <table id="donors_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 12%">Donor Name</th>
                                <th style="min-width: 5%">&nbsp;
                                    <button type="button" data-text="Add" onclick="add_donors()"
                                            class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i> Assign Donors
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
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
    });
   function donors_assign_model_project() {
        var proposalID = $('#proposaldonorpro').val();
        if (proposalID) {
            selectedDonorsSync = [];
            fetch_ngo_projectProposal_donors(proposalID);
            $('#ngo_load_donors_model').modal('show');
        }
    }
    function fetch_ngo_projectProposal_donors(proposalID) {
        oTable = $('#donors_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
            "sAjaxSource": "<?php echo site_url('OperationNgo/fetch_project_proposal_donors'); ?>",
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
                if (selectedDonorsSync.length > 0) {
                    $.each(selectedDonorsSync, function (index, value) {
                        $("#selectDonors_" + value).iCheck('check');
                    });
                }
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });
                $('input').on('ifChecked', function (event) {
                    DonorsSelectedSync(this);
                });
                $('input').on('ifUnchecked', function (event) {
                    DonorsSelectedSync(this);
                });
            },

            "aoColumns": [
                {"mData": "contactID"},
                {"mData": "donorName"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
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


    function update_donor_details_project() {
        var data = $('#test').serializeArray();
        var proposalID = $('#proposaldonorpro').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OperationNgo/update_donors_issubmited_status_project'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                donor_details(proposalID);
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }

    function DonorsSelectedSync(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedDonorsSync);
            if (inArray == -1) {
                selectedDonorsSync.push(value);
            }
        }
        else {
            var i = selectedDonorsSync.indexOf(value);
            if (i != -1) {
                selectedDonorsSync.splice(i, 1);
            }
        }
    }

    function add_donors() {
        var proposalID = $('#proposaldonorpro').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("OperationNgo/assign_donors_for_project_proposal"); ?>',
            dataType: 'json',
            data: {'selectedDonorsSync': selectedDonorsSync, 'proposalID': proposalID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    donor_details(proposalID);
                    $("#ngo_load_donors_model").modal('hide');
                    $('.extraColumns input').iCheck('uncheck');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function delete_donor_project(proposalDonourID, proposalID, donorID) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
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
                    data: {'proposalDonourID': proposalDonourID, 'proposalID': proposalID, 'donorID': donorID},
                    url: "<?php echo site_url('OperationNgo/delete_project_proposal_donors_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['status'] == 1) {
                            myAlert('e', data['message']);
                        } else if (data['status'] == 0) {
                            myAlert('s', data['message']);
                            donor_details();
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

