<?php $all_crm_status_arr = all_task_management_status();
      $assign_task_drop = assign_task_department();
      $assign_emp_assign = assign_department_employee();?>

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
        display:0;
    }
</style>
<?php
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
                            <i class="fa fa-flag"
                               aria-hidden="true"></i> <?php echo $this->lang->line('crm_document_status'); ?>
                        </div><!--Document Status-->
                        <div class="btn-toolbar btn-toolbar-small pull-right">
                            <button class="btn btn-primary btn-xs bottom10"
                                    onclick="open_document_status()"><?php echo $this->lang->line('crm_add_new_status'); ?>
                            </button><!--Add New Status-->
                        </div>
                    </div>


                    <div class="post-area">
                        <article class="page-content">
                            <div class="system-settings">
                                <form class="form-horizontal" role="form">
                                    <div class="col-md-9">
                                        <label for="inputStatus" class="col-md-2 control-label"><b><i
                                                        class="fa fa-filter"></i>Filter</b></label><!--Document-->
                                        <div class="col-md-4">
                                            <?php echo form_dropdown('id', $all_crm_status_arr, '', 'onchange="fetch_doc_status()" class="form-control" id="id"'); ?>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                </form>


                                <table id="usersTable" class="table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $this->lang->line('common_document'); ?></th><!--Document-->
                                        <th><?php echo $this->lang->line('common_status'); ?></th><!--Status-->
                                        <th><?php echo $this->lang->line('common_short_order'); ?></th>
                                        <th><?php echo $this->lang->line('crm_backgroud_color'); ?></th>
                                       
                                        <!--Background Color-->
                                        <th><?php echo $this->lang->line('crm_text_color'); ?></th><!--Text Color-->
                                        <th></th>
                                    </tr>
                                    </thead>
                                </table>
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
                    <h4 class="modal-title">Add New Status </h4><!--Add New User-->
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="crm_documentStatus">

                        <input type="hidden" id="statusID" name="statusID">
                        <!-- Select Basic -->
                        <div class="form-group">
                            <!-- <label class="col-md-4 control-label"
                                   for="selectbasic"> </label> -->
                            <!--Document-->
                            <div class="col-md-6" id="">
                               <input type="hidden" id="documentID" name="documentID" class="form-control" value="2">

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label"
                                   for="selectbasic"><?php echo $this->lang->line('common_status'); ?> </label>
                            <!--Status-->
                            <div class="col-md-6" id="">
                                <input type="text" id="status" name="status" class="form-control">


                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label"
                                   for="selectbasic">Short Order </label>
                            <!--Status-->
                            <div class="col-md-6" id="">
                                <input type="number" id="shortorder" name="shortorder" class="form-control">


                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"
                                   for="selectbasic"><?php echo $this->lang->line('crm_backgroud_color'); ?> </label>
                            <!--Background Color-->
                            <div class="col-md-6" id="">
                                <div id="cp2" class="input-group colorpicker-component">
                                    <input type="text" readonly id="backgroundColor" name="backgroundColor"
                                           value="#000000" class="form-control"/>
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"
                                   for="selectbasic"><?php echo $this->lang->line('crm_status_color'); ?>  </label>
                            <!--Status color-->
                            <div class="col-md-6" id="">
                                <div id="cp3" class="input-group colorpicker-component">
                                    <input type="text" readonly id="color" name="color" value="#000000"
                                           class="form-control"/>
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group otherstatustype">
                            <label class="col-md-4 control-label" for="selectbasic">Type</label><!--Document-->
                            <div class="col-md-6" id="">
                                <?php echo form_dropdown('IsClosestatus', array('0' => 'Select Type', '1' => 'Closed'), '', 'class="form-control" id="IsClosestatus""'); ?>


                            </div>
                        </div>
                        <div class="form-group statuswithoppo hide">
                            <label class="col-md-4 control-label" for="selectbasic">Type</label><!--Document-->
                            <div class="col-md-6" id="">

                                <?php echo form_dropdown('IsClosestatusopportunities', array('0' => 'Select Type', '1' => 'Closed', '2' => 'Convert to project','3' => 'Lost'), '', 'class="form-control" id="IsClosestatusopportunities""'); ?>


                            </div>
                        </div>
                        <div class="form-group statuswithproject hide">
                            <label class="col-md-4 control-label" for="selectbasic">Type</label><!--Document-->
                            <div class="col-md-6" id="">

                                <?php echo form_dropdown('IsClosestatusproject', array('0' => 'Select Type', '1' => 'Closed', '3' => 'cancelled'), '', 'class="form-control" id="IsClosestatusproject""'); ?>


                            </div>
                        </div>
                        <div class="form-group Leadstatus hide">
                            <label class="col-md-4 control-label" for="selectbasic">Type</label><!--Document-->
                            <div class="col-md-6" id="">
                                <?php echo form_dropdown('IsClosestatuslead', array('0' => 'Select Type', '1' => 'Closed', '2' => 'Convert to Opportunitie'), '', 'class="form-control" id="IsClosestatuslead""'); ?>


                            </div>
                        </div>


                        <!-- Button -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="singlebutton"></label>
                            <div class="col-md-4">
                                <button type="button" id="singlebutton" onclick="submitstatus()" name="singlebutton"
                                        class="btn btn-primary btn-xs"> <?php echo $this->lang->line('common_submit'); ?>
                                </button><!--Submit-->
                            </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button><!--Close-->
                </div>
            </div>

        </div>
    </div>

    <script>
        $('#cp2').colorpicker();
        $('#cp3').colorpicker();
        fetch_doc_status();

        function open_document_status() {
            $('#add-user-modal').modal('show');
            $('#documentID').val('2');
            $('#status').val('');
            $('#statusID').val('');
            $('#IsClosestatus').val(0);
            $('#color').colorpicker('setValue', '')
        }

        function editDocumentStatus(statusID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'statusID': statusID},
                url: "<?php echo site_url('Task_management/get_alldocumentStatus'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#add-user-modal').modal('show');
                    $('#documentID').val(data['documentID']);
                    $('#shortorder').val(data['shortorder']);
                    $('#status').val(data['description']);
                    if (data['color'] != '') {
                        $('#color').colorpicker('setValue', data['statusColor'])
                    } else {
                        $('#color').colorpicker('setValue', '')
                    }
                    if (data['backgroundColor'] != '') {
                        $('#backgroundColor').colorpicker('setValue', data['statusBackgroundColor'])
                    } else {
                        $('#backgroundColor').colorpicker('setValue', '')
                    }
                   // $('#IsClosestatus').val(data['statusType']).change();
                    if (data['documentID'] == 2) {

                        $('.otherstatustype').removeClass('hide');
                        $('.Leadstatus').addClass('hide');
                        $('.statuswithoppo').addClass('hide');
                        $('.statuswithproject').addClass('hide');
                        $('#IsClosestatus').val(data['statusType']).change();
                    }
                    $('#statusID').val(data['statusID']);

                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }


        function deleteDocumentStatus(statusID) {
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
                        data: {'statusID': statusID},
                        url: "<?php echo site_url('Task_management/deleteDocumentStatus'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            // if (data['status'] == 1) {
                            //     myAlert('e', data['message']);
                            // } else if (data['status'] == 0) {
                            //     myAlert('s', data['message']);
                            //     fetch_doc_status();
                            // }

                            if (data['error'] == 1) {
                                    
                                    myAlert('e', data['message']);
                                        stopLoad();
    
                                    }
                            else{
                                    refreshNotifications(true);
                                    stopLoad();
                                    myAlert('s', 'Deleted Successfully');
                                    fetch_doc_status();
    
    
                                    }
                            // loaduserDropDown();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }


        function fetch_doc_status() {
            var Otable = $('#usersTable').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Task_management/fetch_doc_status'); ?>",
                "aaSorting": [[0, 'desc']],
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
                    {"width": "2%", "targets": 0},
                    {"width": "6%", "targets": 1},
                    {"width": "6%", "targets": 2},
                    {"width": "2%", "targets": 3},
                    {"width": "2%", "targets": 4},
                    {"width": "2%", "targets": 5},
                    {"width": "2%", "targets": 6}
                ],
                "aoColumns": [
                    {"mData": "statusID"},
                    {"mData": "document"},
                    {"mData": "description"},
                    {"mData": "shortorder"},
                    {"mData": "backgroundColor"},
                    {"mData": "color"},
                    {"mData": "edit"}

                ],
                //"columnDefs": [{"targets": [2], "orderable": false}],
                "columnDefs": [{"searchable": false, "targets": [0]}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                    aoData.push({"name": "masterID", "value": $('#id').val()});
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


        function submitstatus() {
            var data = $('#crm_documentStatus').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Task_management/create_document_status'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data[0] == 's') {
                        $('#add-user-modal').modal('hide');
                        $('#documentID').val('');
                        $('#shortorder').val('');
                        $('#status').val('');
                        $('#statusID').val('');
                        $('#color').colorpicker('setValue', '')
                    }
                    myAlert(data[0], data[1]);

                    fetch_doc_status();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-purple',
            radioClass: 'iradio_square_relative-purple',
            increaseArea: '20%'
        });

        function typedrop(type) {
            if (type == 5) {
                $('.Leadstatus').removeClass('hide');
                $('.otherstatustype').addClass('hide');
                $('.statuswithoppo').addClass('hide');
                $('.statuswithproject').addClass('hide');
            } else if (type == 4) {
                $('.Leadstatus').addClass('hide');
                $('.otherstatustype').addClass('hide');
                $('.statuswithoppo').removeClass('hide');
                $('.statuswithproject').addClass('hide');
            } else if (type == 9) {
                $('.Leadstatus').addClass('hide');
                $('.otherstatustype').addClass('hide');
                $('.statuswithoppo').addClass('hide');
                $('.statuswithproject').removeClass('hide');
            }
            else
             {
            $('.Leadstatus').addClass('hide');
            $('.statuswithoppo').addClass('hide');
            $('.otherstatustype').removeClass('hide');
            $('.statuswithproject').addClass('hide');
             }
        }
    </script>