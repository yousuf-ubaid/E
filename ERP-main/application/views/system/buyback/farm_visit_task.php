<?php echo head_page('Visit Task Types', false);
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
                    onclick="add_visitTaskType()"><i class="fa fa-plus"></i> New Task Type
            </button>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="box-body no-padding">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="visitTaskTypeMaster_view"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--modal report-->
    <div class="modal fade" id="visitTaskType_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Visit Task Type</h4>
                </div>
                <div class="modal-body">
                    <?php echo form_open('', 'role="form" id="visitTaskType_form"'); ?>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Description</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <input type="text" name="description" id="description" class="form-control"
                                   placeholder="Description">
                            <input type="hidden" class="form-control" name="visitTaskTypeID"
                                   id="edit_visitTaskTypeID">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Short Code</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <input type="text" name="shortCode" id="shortCode" class="form-control"  maxlength="3"
                                   placeholder="Short Code">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Is Active</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns">
                                    <input id="isActive" type="checkbox" data-caption="" class="columnSelected" name="isActive" value="1">
                                    <label for="checkbox">&nbsp;</label></div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" onclick="visitTask_type_save()">Save</button>
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
                fetchPage('system/buyback/farm_visit_task','','Visit Task Types');
            });
            getVisitTaskTypes_tableView();

            $('.extraColumns input').iCheck({
                checkboxClass: 'icheckbox_square_relative-green',
                radioClass: 'iradio_square_relative-blue',
                increaseArea: '20%'
            });
        });

        function getVisitTaskTypes_tableView() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {},
                url: "<?php echo site_url('Buyback/load_visitTaskType_Master_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#visitTaskTypeMaster_view').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_visitTaskType(visitTaskTypeID) {
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
                        data: {'visitTaskTypeID': visitTaskTypeID},
                        url: "<?php echo site_url('Buyback/delete_visitTaskType'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert('s', 'Visit Task Type Deleted Successfully');
                            getVisitTaskTypes_tableView();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function visitTask_type_save() {
            var description = $('#description').val();
            var shortCode = $('#shortCode').val();
            var isActive = $("#isActive").is(":checked");
            var visitTaskTypeID = $('#edit_visitTaskTypeID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {description: description, shortCode: shortCode, isActive: isActive, visitTaskTypeID: visitTaskTypeID},
                url: "<?php echo site_url('Buyback/save_visitTaskTypes_master'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        getVisitTaskTypes_tableView();
                        $('#visitTaskType_model').modal('hide');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function edit_visitTaskType(visitTaskTypeID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'visitTaskTypeID': visitTaskTypeID},
                url: "<?php echo site_url('Buyback/load_visitTaskType_master'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#description').val(data['description']);
                        $('#shortCode').val(data['shortCode']);
                        $('#edit_visitTaskTypeID').val(visitTaskTypeID);
                        if(data['isActive'] == 1){
                            $('#isActive').iCheck('check');
                        } else {
                            $('#isActive').iCheck('uncheck');
                        }
                        $("#visitTaskType_model").modal({backdrop: "static"});
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

        function add_visitTaskType(){
            $('#description').val('');
            $('#shortCode').val('');
            $('#edit_visitTaskTypeID').val('');
            $('#isActive').iCheck('uncheck');
            $("#visitTaskType_model").modal({backdrop: "static"});
        }

    </script>

<?php
