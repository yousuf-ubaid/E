<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('operationngo_donor_collections');
echo head_page('Project Proposal', false);

/*echo head_page('Donor Collections', false);*/
$date_format_policy = date_format_policy();
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
    .labelconvert {
        color: #f15727;
    }
    .alpha-box {
        font-size: 14px;
        line-height: 25px;
        list-style: none outside none;
        margin: 0 0 0 12px;
        padding: 0 0 0;
        text-align: center;
        text-transform: uppercase;
        width: 24px;
    }

    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .alpha-box {
        font-size: 14px;
        line-height: 25px;
        list-style: none outside none;
        margin: 0 0 0 12px;
        padding: 0 0 0;
        text-align: center;
        text-transform: uppercase;
        width: 24px;
    }

    ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    .alpha-box li a {
        text-decoration: none;
        color: #555;
        padding: 4px 8px 4px 8px;
    }

    .alpha-box li a.selected {
        color: #fff;
        font-weight: bold;
        background-color: #4b8cf7;
    }

    .alpha-box li a:hover {
        color: #000;
        font-weight: bold;
        background-color: #ddd;
    }

    .arrow-steps .step {
        font-size: 12px !important;
        padding: 3px 10px 7px 30px !important;
    }
    .arrow-steps .step:after, .arrow-steps .step:before {
        border-top: 13px solid transparent !important;
        border-bottom: 14px solid transparent !important;
    }
</style>

<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" style="margin-left:30px;" class="btn btn-success pull-left"
                onclick="fetchPage('system/communityNgo/ngo_mo_comProject_create_proposal',null,'Add Project Proposal'/*Add New Donor Collections*/,'NGO');">
            <i
                class="fa fa-plus"></i> Zakat
        </button>
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/operationNgo/create_project_proposal',null,'Add Project Proposal'/*Add New Donor Collections*/,'NGO');">
            <i
                class="fa fa-plus"></i> Project Proposal
        </button>
    </div>
</div>
<div class="row" style="margin-top: 2%;">
    <div class="col-sm-4" style="margin-left: 2%;">

        <div class="col-sm-12">
            <div class="box-tools">
                <div class="has-feedback">
                    <input name="searchTask" type="text" class="form-control input-sm"
                           placeholder="Code, Name"
                           id="searchTask" onkeypress="startMasterSearch()"><!--Search by Code--><!--Donor-->
                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-7">
        <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive mailbox-messages" id="DonorCollectionMaster_view">
            <!-- /.table -->
        </div>

    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Beneficiary" id="ngo_project_proposal_email_model">
    <div class="modal-dialog modal-lg" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Send Email For Donors </h4>
            </div>
            <div class="modal-body">
                <div id="sysnc">
                    <div class="table-responsive">
                        <input type="hidden" name="proposalID" id="proposalID_edit">
                        <table id="donors_email_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 12%">Donor Name</th>
                                <th style="min-width: 5%">&nbsp;
                                    <button type="button" data-text="Add" onclick="check_all_donors()"
                                            class="btn btn-xs btn-primary">Check All
                                    </button>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="send_project_proposal_email()">Send Email
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="proposal_convert_to_project">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="title_proposal"> </h4>
            </div>
            <?php echo form_open('', 'role="form" id="proposal_convert_to_project_master_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="proposalid_convert"  name="proposalid">
                <input type="hidden" id="project_id_proposal"  name="projectid">

                <input type="hidden" id="commited_amt_prop"  name="commited_amt_prop">
                <input type="hidden" id="total_amt"  name="total_amt">

                <div class="projectconvert hide">
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label class="title">Convert To Project</label>
                    </div>
                    <div class="form-group col-sm-6">


                        <div class="skin skin-square item-iCheck">
                            <div class="skin-section extraColumns"><input id="IsActive" type="checkbox"
                                                                          class="IsActive" value="1"><label
                                        for="checkbox">&nbsp;</label></div>
                        </div>
                    </div>
                </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Qualified</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <div class="form-group col-sm-3">

                                <strong id="qualified_count"> </strong>
                            </div>

                        </div>
                    </div>


                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Commited Amount</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <strong id="commited_amt"> </strong>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Comment</label>
                        </div>
                        <div class="form-group col-sm-6">
                    <textarea class="form-control richtext" id="description"
                              name="description"
                              rows="2"></textarea>
                            <span class="input-req-inner"></span>

                        </div>
                    </div>



                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span> Convert
                    </button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                        Close</button>
                </div>
                </div>

                <div class="projectconvertview hide">
                    <input type="hidden" id="proposalid_reopen"  name="proposalid_reopen">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Is Converted To Project : </label>
                        </div>
                        <div class="form-group col-sm-3">

                            <strong id="proposal_status"> </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Close The Proposal  : </label>
                        </div>
                        <div class="form-group col-sm-3">

                            <strong id="close_proposal"> </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Closed Date : </label>
                        </div>
                        <div class="form-group col-sm-3">

                            <strong id="converted_date"> </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Closed By : </label>
                        </div>
                        <div class="form-group col-sm-3">

                            <strong id="converted_by"> </strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Comment : </label>
                        </div>
                        <div class="form-group col-sm-5">

                            <strong id="comment"> </strong>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-primary hide" id="proposalreopen" onclick="proposal_reopen()"><span class="glyphicon glyphicon-repeat"
                                                                                   aria-hidden="true"></span> Re-Open
                        </button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    var selectedDonorsEmailSync = [];
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/project_proposal', '', 'Project Proposal');
        });

        getProjectProposalTable();

    });

    $('.extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-purple',
        radioClass: 'iradio_square_relative-purple',
        increaseArea: '20%'
    });

    $('#searchTask').bind('input', function () {
        getProjectProposalTable();
    });

    function getProjectProposalTable() {
        var searchTask = $('#searchTask').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'q': searchTask},
            url: "<?php echo site_url('OperationNgo/load_project_proposal_master_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#DonorCollectionMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_project_proposal(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'proposalID': id},
                    url: "<?php echo site_url('OperationNgo/delete_project_proposal'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        getProjectProposalTable();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getProjectProposalTable();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.donorsorting').removeClass('selected');
        $('#searchTask').val('');
        $('#sorting_1').addClass('selected');
        getProjectProposalTable();
    }

    function referback_project_proposal(id) {
        swal({
                title: "Are you sure?",
                text: "You want to reopen this Project Proposal!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('crm_reopen');?>", /*Reopen*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'proposalID': id},
                    url: "<?php echo site_url('OperationNgo/referback_project_proposal'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        getProjectProposalTable();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_project_proposal_email(proposalID) {
        if (proposalID) {
            selectedDonorsEmailSync = [];
            fetch_ngo_projectProposal_donors_email(proposalID);
             $('#proposalID_edit').val(proposalID);

            $('#ngo_project_proposal_email_model').modal('show');
        }
    }

    function fetch_ngo_projectProposal_donors_email(proposalID) {
        oTable = $('#donors_email_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
            "sAjaxSource": "<?php echo site_url('OperationNgo/fetch_project_proposal_donors_email_send'); ?>",
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
                if (selectedDonorsEmailSync.length > 0) {
                    $.each(selectedDonorsEmailSync, function (index, value) {
                        $("#selectDonorsEmail_" + value).iCheck('check');
                    });
                }
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });
                $('input').on('ifChecked', function (event) {
                    DonorsSelectedEmailSync(this);
                });
                $('input').on('ifUnchecked', function (event) {
                    DonorsSelectedEmailSync(this);
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

    function DonorsSelectedEmailSync(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedDonorsEmailSync);
            if (inArray == -1) {
                selectedDonorsEmailSync.push(value);
            }
        } else {
            var i = selectedDonorsEmailSync.indexOf(value);
            if (i != -1) {
                selectedDonorsEmailSync.splice(i, 1);
            }
        }
    }

    function send_project_proposal_email() {
        var proposalID = $('#proposalID_edit').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("OperationNgo/send_project_proposal_email"); ?>',
            dataType: 'json',
            data: {'selectedDonorsEmailSync': selectedDonorsEmailSync, 'proposalID': proposalID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $("#ngo_project_proposal_email_model").modal('hide');
                    $('.extraColumns input').iCheck('uncheck');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
function project_proposal_convertion(id,projectid) {
    $('#proposalid_convert').val(id);
    $('#IsActive').iCheck('uncheck');
    $('#project_id_proposal').val(projectid);
    $('#title_proposal').html('Close Proposal');
    $('#proposal_convert_to_project_master_form')[0].reset();
    $('#proposal_convert_to_project_master_form').bootstrapValidator('resetForm', true);;
    $('.projectconvert').removeClass('hide');
    $('.projectconvertview').addClass('hide');

    proposal_details(id);
    $('#proposal_convert_to_project').modal('show');

}


$('#proposal_convert_to_project_master_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            description: {validators: {notEmpty: {message: 'Description is required.'}}},

            },
    }).on('success.form.bv', function (e) {
    var commitedamt =  $('#commited_amt_prop').val();
    var totalamt =  $('#total_amt').val();
    e.preventDefault();
    var $form = $(e.target);
    var bv = $form.data('bootstrapValidator');
    var data = $form.serializeArray();
    var isDefault;

    if ($("#IsActive").is(':checked')) {
            IsActive = 1;
        }else
             {
        IsActive = 0;
         }
        data.push({name: "IsActive", value: IsActive});
        swal({
                title: "Are You Sure",
                text: "You want to Close this proposal",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('OperationNgo/convert_project_proposal_to_project'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $('#proposal_convert_to_project').modal('hide');
                            $('#IsActive').iCheck('uncheck');
                            getProjectProposalTable();
                        } else {
                            $('.btn-primary').prop('disabled', false);
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });

    });

    function view_converted_proposal(proposalid) {
        $('.projectconvertview').removeClass('hide');
        $('.projectconvert').addClass('hide');
        $('#title_proposal').html('Close Proposal Details');
        $('#proposalreopen').removeClass('hide');

        $('#proposalid_reopen').val(proposalid);


        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {proposalid: proposalid},
            url: "<?php echo site_url('OperationNgo/load_project_proposal_to_project'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#proposal_status').html(data['isConvertedToProject'])
                    $('#converted_date').html(data['closedDate'])
                    $('#close_proposal').html(data['closedYN'])
                    $('#converted_by').html(data['username'])
                    if(data['proposalConvertingComment'])
                    {
                        $('#comment').html(data['proposalConvertingComment'])
                    }else
                    {
                        $('#comment').html(' - ')
                    }

                    $('#proposal_convert_to_project').modal('show');
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function proposal_details(id) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {proposalid: id},
            url: "<?php echo site_url('OperationNgo/load_proposal_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                   $('#qualified_count').html(data['Bentotalqual']);
                   $('#commited_amt').html(data['total']);
                    $('#commited_amt_prop').val(data['netTotal']);
                    $('#total_amt').val(data['commitedamt']);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function proposal_reopen()
    {
        var proposalid = $('#proposalid_reopen').val();
        swal({
                    title: "Are You Sure",
                    text: "You want to reopen this proposal",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No"
            },
                function () {
                    $.ajax({
                        url: "<?php echo site_url('OperationNgo/closedproposal_reopen'); ?>",
                        type: 'post',
                        data: {'proposalid': proposalid},
                        dataType: 'json',
                        cache: false,

                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                getProjectProposalTable();
                                $('#proposal_convert_to_project').modal('hide');
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            stopLoad();
                            myAlert('e', xhr.responseText);
                        }
                    });
                });
    }

</script>