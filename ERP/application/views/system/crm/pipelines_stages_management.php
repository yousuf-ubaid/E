<style>
    .width100p {
        width: 100%;
    }

    .user-table {
        width: 100%;
    }

    .bottom10 {
        margin-bottom: 10px !important;
    }

    .btn-toolbar {
        margin-top: -2px;
    }

    table {
        max-width: 100%;
        background-color: transparent;
        border-collapse: collapse;
        border-spacing: 0;
    }

    .flex {
        display:
    }

    .bottom20 {
        margin-bottom: 20px;
    }

    section.block-pipeline {
        margin-top: 10px;
        margin-bottom: 20px
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
<?php
$pipe = get_pipelineName($masterID);
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="row">

    <div class="width100p">
        <section class="past-posts">
            <div class="posts-holder settings">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">
                            <i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?php echo $this->lang->line('crm_pipelines');?>
                        </div><!--Pipelines-->
                        <div class="btn-toolbar btn-toolbar-small pull-right">

                        </div>
                    </div>


                    <div class="post-area">
                        <article class="page-content">

                            <div class="system-settings">

                                <div class="further-link">
                                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                    <a onclick="configuration_page('pipelines')"><strong> <?php echo $pipe['pipeLineName']; ?> </strong></a>
                                </div>


                                <div id="settingsContainer">
                                    <!-- Old site solution -->
                                    <div id="div_load_pipeline">
                                        <!--<ul id="" class="pipeline bottom20" style="100%">
                                            <li style="width: 10%"><a href="#" title="Prospecting"> Prospecting</a></li>
                                            <li style="width: 10%"><a href="#" title="Qualification"> Qualification</a></li>
                                            <li style="width: 50%"><a href="#" title="Needs Analysis"> Needs Analysis</a>
                                            </li>
                                            <li style="width: 10%"><a href="#" title="Proposal"> Proposal</a></li>
                                            <li style="width: 10%"><a href="#" title="Negotiation"> Negotiation</a></li>
                                            <li style="width: 10%"><a href="#" title="planning"> planning</a></li>
                                        </ul>-->
                                    </div>
                                    <br>

                                    <form id="form_pipelineStage">
                                        <table id="fetchpipeline" class="table ">
                                            <thead>
                                            <tr>
                                                <th>#</th>

                                                <th><?php echo $this->lang->line('crm_pipeline_stage_name');?> </th><!--Pipeline Stage Name-->
                                                <th><?php echo $this->lang->line('crm_probability');?> </th><!--Probability-->

                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>

                                            <tfoot>
                                            <tr>
                                                <td></td>
                                                <td colspan="">
                                                    <input type="hidden" value="<?php echo $masterID ?>"
                                                           name="masterID">
                                                    <input class="text" id="stageName" name="stageName"
                                                           placeholder="<?php echo $this->lang->line('crm_pipeline_stage_name');?>" type="text" value=""></td><!--Pipeline Stage Name-->
                                                <td style=""><input style="width: 120px" max="100" min="0" class="text"
                                                                    id="probability" name="probability"
                                                                    placeholder="<?php echo $this->lang->line('crm_probability');?> %" type="number" value=""><!--Probability-->
                                                </td>


                                                <td colspan=""><a onclick="submitPipeline();" id="AddNewPipeline"
                                                                  class="btn btn-primary btn-xs"><?php echo $this->lang->line('crm_add_stage');?> </a></td><!--Add Stage-->
                                            </tr>

                                            </tfoot>

                                        </table>
                                    </form>


                                </div>


                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal -->
    <div id="add-user-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo $this->lang->line('crm_assign_employee');?> </h4><!--Assign Employee-->
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="crm_employee">
                        <input type="hidden" id="groupID" name="groupID">

                        <!-- Select Basic -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="selectbasic"><?php echo $this->lang->line('crm_select_an_employee');?> </label><!--Select an Employee-->

                            <div class="col-md-6" id="div_loaduser">


                            </div>
                        </div>

                        <!-- Button -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="singlebutton"></label>

                            <div class="col-md-4">
                                <button type="button" id="singlebutton" onclick="submitusersgroupemployee()"
                                        name="singlebutton" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_submit');?>
                                </button><!--Submit-->
                            </div>
                        </div>


                    </form>
                    <table id="assignedEmployee" class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_employee_name');?> </th><!--Employee Name-->
                            <th></th>
                        </tr>
                        </thead>

                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                </div>
            </div>

        </div>
    </div>


    <script>

        fetch_pipelinestage();
        loadpipeline();
        function fetch_pipelinestage() {
            var Otable = $('#fetchpipeline').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Crm/fetch_crm_pipeline_stage'); ?>",
                "aaSorting": [[0, 'asc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    /*  if (oSettings.bSorted || oSettings.bFiltered) {
                     for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                     $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                     }
                     }*/
                },
                "columnDefs": [

                    {"width": "2%", "targets": 3}

                ],
                "aoColumns": [
                    {"mData": "sortOrder"},
                    {"mData": "stageName"},
                    {"mData": "probability"},
                    {"mData": "edit"}

                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "masterID", "value": <?php echo $masterID ?>});
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


        function submitPipeline() {

            var data = $('#form_pipelineStage').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('crm/save_piplelineStage'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#stageName').val('');
                        $('#probability').val('');
                    }
                    loadpipeline();
                    fetch_pipelinestage();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function edit_pipeline(pipeLineDetailID) {
            $('.updatediv').addClass('hide');
            $('.canceldiv').removeClass('hide');
            $('.showinput').removeClass('hide');
            $('.hideinput').addClass('hide');
            $('.xxx_' + pipeLineDetailID).removeClass('hide');
            $('.xx_' + pipeLineDetailID).addClass('hide');
            $('#editpipeline_' + pipeLineDetailID).addClass('hide');
            $('#updatepipeline_' + pipeLineDetailID).removeClass('hide');

        }

        function pipelinestage_cancel(pipeLineDetailID) {

            $('.updatediv').addClass('hide');
            $('.canceldiv').removeClass('hide');

            $('.showinput').removeClass('hide');
            $('.hideinput').addClass('hide');
        }

        function loadpipeline() {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: <?php echo $masterID ?>},
                url: "<?php echo site_url('crm/loadpipeline'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_pipeline').html(data);
                    /*  $('.pipeline li:last-child a').css('background-image', 'none');*/
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_pipeline(pipeLineDetailID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'pipeLineDetailID': pipeLineDetailID},
                        url: "<?php echo site_url('Crm/delete_pipelineDetail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            myAlert('s', 'Deleted Successfully');
                            fetch_pipelinestage();
                            loadpipeline();

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function delete_users(groupID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'groupID': groupID},
                        url: "<?php echo site_url('Crm/delete_usergroup'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            myAlert('s', 'Deleted Successfully');
                            fetch_usersgroups();

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        function pipelinestage_update(pipeLineDetailID) {
            sortOrderID = $('#order_' + pipeLineDetailID).val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    masterID:<?php echo $masterID ?>,
                    pipeLineDetailID: pipeLineDetailID,
                    stageName: $('#stagename_' + pipeLineDetailID).val(),
                    probability: $('#percentage_' + pipeLineDetailID).val(),
                    sortOrder: sortOrderID,
                },
                url: "<?php echo site_url('crm/save_piplelineStage'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#stagename_' + pipeLineDetailID).val('');
                        $('#percentage_' + pipeLineDetailID).val('');
                    }
                    loadpipeline();
                    fetch_pipelinestage();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    </script>