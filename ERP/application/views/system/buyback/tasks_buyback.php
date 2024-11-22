<?php echo head_page('Task Types', false);
$date_format_policy = date_format_policy();
?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
    <style>
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
                    onclick="add_taskType()"><i class="fa fa-plus"></i> Task
            </button>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="box-body no-padding">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="taskTypeMaster_view"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--modal report-->
    <div class="modal fade" id="taskType_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Task Type</h4>
                </div>
                <div class="modal-body">
                    <?php echo form_open('', 'role="form" id="taskType_form" autocomplete="off"'); ?>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Description</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <input type="text" name="description" id="description" class="form-control"
                                   placeholder="Description">
                            <input type="hidden" class="form-control" name="edit_tasktypeID"
                                   id="edit_tasktypeID">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Short Description</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <input type="text" name="shortDesc" id="shortDesc" class="form-control"  maxlength="3"
                                   placeholder="Short Description">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Link Document</label>
                        </div>
                       <div class="form-group col-sm-8">

                               <input id="active" type="checkbox" onclick="linkDocumentType()"
                                              name="active">

                       </div>
                    </div>
                    <div class="row documentType hidden" style="margin-top: 10px;" id="documentType">
                        <div class="form-group col-sm-3">
                            <label class="title">Document Type</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <?php echo form_dropdown('documentCode', array('' => 'Select Document Type','BBDPN' => 'Dispatch Note', 'BBGRN' => 'Goods Received Note', 'BBRV' => 'Receipt Voucher', 'BBPV' => 'Payment Voucher' , 'BBSV' => 'Settlement', 'BBDR' => 'Dispatch Return', 'BBFVR' => 'Farm Visit Report'), '', ' class="form-control select2" id="documentCode"'); ?>
                        </div>
                    </div>
                </div>
                </form>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" onclick="task_type_save()">Save</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <script type="text/javascript">
        var Otable;
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/buyback/tasks_buyback','','Task Types');
            });
            getTaskTypesManagement_tableView();
            $('.select2').select2();

            $('.extraColumnsgreen input').iCheck({
                checkboxClass: 'icheckbox_square_relative-green',
                increaseArea: '20%'
            });
        });

        function linkDocumentType(val) {
            if($('#active').is(':checked')){
                $('.documentType').removeClass('hidden');
            } else {
                $('.documentType').addClass('hidden');
                $('#documentCode').val('').change();
            }
        }

        function getTaskTypesManagement_tableView() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {},
                url: "<?php echo site_url('Buyback/load_taskType_Master_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#taskTypeMaster_view').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_taskType(id) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this task!",
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
                        data: {'tasktypeID': id},
                        url: "<?php echo site_url('Buyback/delete_taskType'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert('s', 'Task Type Deleted Successfully');
                            getTaskTypesManagement_tableView();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function task_type_save() {
            var description = $('#description').val();
            var shortDesc = $('#shortDesc').val();
            var tasktypeID = $('#edit_tasktypeID').val();
            var documentCode = $('#documentCode').val();
            if($('#active').is(':checked')){
                var linkDocument = 1;
            } else {
                var linkDocument = 0;
            }
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {description:description,shortDesc:shortDesc, tasktypeID:tasktypeID, documentCode: documentCode, linkDocument:linkDocument},
                url: "<?php echo site_url('Buyback/save_taskType_master'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        getTaskTypesManagement_tableView();
                        $('#active').iCheck('uncheck');
                        $('#taskType_model').modal('hide');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function edit_taskType(tasktypeID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'tasktypeID': tasktypeID},
                url: "<?php echo site_url('Buyback/load_taskType_master'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#description').val(data['description']);
                        $('#shortDesc').val(data['shortDescription']);
                        $('#edit_tasktypeID').val(data['tasktypeID']);
                        if(data['linkDocument'] == 1){
                            $('#active').iCheck('check');
                            $('.documentType').removeClass('hidden');
                        } else {
                            $('#active').iCheck('uncheck');
                            $('.documentType').addClass('hidden');
                        }
                        $('#documentCode').val(data['DocumentCode']).change();
                        $("#taskType_model").modal({backdrop: "static"});
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function add_taskType(){
            $('#active').iCheck('uncheck');
            linkDocumentType();
            $('#description').val('');
            $('#shortDesc').val('');
            $('#edit_tasktypeID').val('');
            $("#taskType_model").modal({backdrop: "static"});
        }


    </script>




<?php
