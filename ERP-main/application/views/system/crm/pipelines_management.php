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
</style>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>


<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
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
                                <form id="form_pipeline">
                                    <table id="fetchpipeline" class="table ">
                                        <thead>
                                        <tr>
                                            <th>#</th>

                                            <th><?php echo $this->lang->line('crm_pipline_name');?> </th><!--Pipeline Name-->
                                            <th><?php echo $this->lang->line('crm_for_opportunities');?></th><!--For Opportunities-->
                                            <th> <?php echo $this->lang->line('crm_for_projects');?> </th><!--For Projects-->
                                            <th><?php echo $this->lang->line('crm_for_lead');?>  </th><!--For Lead-->
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>

                                        <tfoot>
                                        <tr>
                                            <td></td>
                                            <td colspan="">
                                                <input class="text" id="pipeLineName" name="pipeLineName"
                                                       placeholder="<?php echo $this->lang->line('crm_pipline_name');?>" type="text" value=""></td><!--Pipeline Name-->
                                            <td style="text-align: center"><input name="opportunityYN" type="hidden" value="0"><input class="" id="opportunityYN" name="opportunityYN" type="checkbox" value="1"> </td>
                                            <td style="text-align: center"><input name="projectYN" type="hidden" value="0"> <input class="" id="projectYN" name="projectYN" type="checkbox" value="1">
                                            </td>
                                            <td style="text-align: center"><input name="leadYN" type="hidden" value="0"> <input class="" id="leadYN" name="leadYN" type="checkbox" value="1">
                                            </td>
                                            <td colspan=""><a onclick="submitPipeline();" id="AddNewPipeline" class="btn btn-primary btn-xs"><?php echo $this->lang->line('crm_add_pipeline');?></a></td><!--Add Pipeline-->
                                        </tr>

                                        </tfoot>

                                    </table>
                                </form>
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
                    <h4 class="modal-title"><?php echo $this->lang->line('crm_assign_employee');?></h4><!--Assign Employee-->
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
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                </div>
            </div>

        </div>
    </div>


    <script>
        fetch_pipeline();
        function fetch_pipeline() {
            var Otable = $('#fetchpipeline').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Crm/fetch_crm_pipeline'); ?>",
                "aaSorting": [[1, 'desc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    if (oSettings.bSorted || oSettings.bFiltered) {
                        for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                            $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                        }
                    }
                },

                "columnDefs": [

                    {"width": "10%", "targets": 5}
                ],
                "aoColumns": [
                    {"mData": "pipeLineID"},
                    {"mData": "pipeLineName"},
                    {"mData": "opportunityYN"},
                    {"mData": "projectYN"},
                    {"mData": "leadYN"},
                    {"mData": "edit"}

                ],
                //"columnDefs": [{"targets": [2], "orderable": false}],
                "columnDefs": [{"searchable": false, "targets": [0,2,3,4,5]}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                    //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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

            var data = $('#form_pipeline').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('crm/save_pipleline'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    $('#pipeLineName').val('');
                    $('#opportunityYN').attr('checked', false);
                    $('#projectYN').attr('checked', false);
                    $('#leadYN').attr('checked', false);

                    fetch_pipeline();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_pipeline(pipeLineID) {
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
                        data: {'pipeLineID': pipeLineID},
                        url: "<?php echo site_url('Crm/delete_pipeline'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            myAlert('s', 'Deleted Successfully');
                            fetch_pipeline();

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }


    </script>