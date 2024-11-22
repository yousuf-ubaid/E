<?php $all_crm_status_arr = all_crm_status();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

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


<div class="row">

    <div class="width100p">
        <section class="past-posts">
            <div class="posts-holder settings">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">
                            <i class="fa fa-cubes" aria-hidden="true"></i> Criterias
                        </div><!--Source-->
                        <div class="btn-toolbar btn-toolbar-small pull-right">
                            <button class="btn btn-primary btn-xs bottom10" onclick="open_document_status()"> Add New Criteria
                            </button><!--Add New Source-->
                        </div>
                    </div>


                    <div class="post-area">
                        <article class="page-content">
                            <div class="system-settings">
                                <form class="form-horizontal" role="form">

                                    <br>
                                    <br>
                                </form>


                                <table id="usersTable" class="table ">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th><!--Document-->
                                        <th> </th>
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
                    <h4 class="modal-title">Add New Criteria</h4><!--Add New Source-->
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="crm_documentStatus">

<input type="hidden" id="criteriaID" name="criteriaID">
                        <!-- Select Basic -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="selectbasic"> Criteria Description </label><!--Source-->
                            <div class="col-md-6" id="">
                                <input type="text" id="description" name="description" class="form-control">


                            </div>
                        </div>



                        <!-- Button -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="singlebutton"></label>
                            <div class="col-md-4">
                                <button type="button" id="singlebutton" onclick="submitstatus()" name="singlebutton"
                                        class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_submit');?>
                                </button><!--Submit-->
                            </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                </div>
            </div>

        </div>
    </div>

    <script>
        $('#cp2').colorpicker();
        fetch_doc_status();
        function open_document_status(){
            $('#add-user-modal').modal('show');

            $('#documentID').val('');
            $('#description').val('');
            $('#sourceID').val('');
            $('#color').colorpicker('setValue', '')
        }






        function fetch_doc_status() {
            var Otable = $('#usersTable').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Crm/fetch_crm_closingcritiria'); ?>",
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


                "aoColumns": [
                    {"mData": "criteriaID"},
                    {"mData": "Description"},
                    {"mData": "edit"}

                ],
                //"columnDefs": [{"targets": [2], "orderable": false}],
                "columnDefs": [{"searchable": false, "targets": [0]}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
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
                url: "<?php echo site_url('crm/create_criteria'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data[0] == 's') {
                        $('#add-user-modal').modal('hide');
                        $('#description').val('');

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
        function editDocumentStatuscritiria(criteriaID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'criteriaID': criteriaID},
                url: "<?php echo site_url('Crm/get_critiria_data'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#add-user-modal').modal('show');
                     $('#description').val(data['Description']);

                    $('#criteriaID').val(data['criteriaID']);


                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }
        function deleteDocumentcritiria(criteriaID) {
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
                        data: {'criteriaID': criteriaID},
                        url: "<?php echo site_url('Crm/delete_critiria'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            if(data==true)
                            {
                                myAlert('s', 'Deleted Successfully');
                                fetch_doc_status();
                            }else
                            {
                                myAlert('e', 'This criteria has assigned for a opportunity,you cannot delete this record');
                            }


                            //loaduserDropDown();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    </script>