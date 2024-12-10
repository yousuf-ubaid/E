<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('communityngo_donor_collections');
echo head_page('Community Project Proposal', false);

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
            <button type="button" class="btn btn-primary pull-right"
                    onclick="fetchPage('system/communityNgo/ngo_mo_comProject_create_proposal',null,'Add Project Proposal'/*Add New Donor Collections*/,'NGO');">
                <i
                    class="fa fa-plus"></i> Community Project Proposal
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
<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <script type="text/javascript">
        var Otable;
        var selectedDonorsEmailSync = [];
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_mo_comProject_proposal', '', 'Community Project Proposal');
            });

            getProjectProposalTable();

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
                url: "<?php echo site_url('CommunityNgo/load_project_proposal_master_view'); ?>",
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
                        url: "<?php echo site_url('CommunityNgo/delete_project_proposal'); ?>",
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
                    text: "You want to reopen this Community Project Proposal!",
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
                        url: "<?php echo site_url('CommunityNgo/referback_project_proposal'); ?>",
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
                "sAjaxSource": "<?php echo site_url('CommunityNgo/fetch_project_proposal_donors_email_send'); ?>",
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
                url: '<?php echo site_url("CommunityNgo/send_project_proposal_email"); ?>',
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


    </script>
<?php
