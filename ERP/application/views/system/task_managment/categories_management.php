<?php $all_crm_status_arr = all_task_management_status();
$primaryLanguage = getPrimaryLanguage();
$assign_task_drop = assign_task_department();
$assign_emp_assign = assign_department_employee();
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
        display:0;
    }
</style>


<div class="row">

    <div class="width100p">
        <section class="past-posts">
            <div class="posts-holder settings">
                <div class="past-info">
                    <div id="toolbar">
                        <div class="toolbar-title">
                            <i class="fa fa-bars" aria-hidden="true"></i> <?php echo $this->lang->line('crm_catergories');?>
                        </div><!--Categories-->
                        <div class="btn-toolbar btn-toolbar-small pull-right">
                            <button class="btn btn-primary btn-xs bottom10" onclick="modal_addCategory()"> <?php echo $this->lang->line('crm_add_new_category');?>
                            </button><!--Add New Category-->
                        </div>
                    </div>


                    <div class="post-area">
                        <article class="page-content">
                            <div class="system-settings">
                                <form class="form-horizontal" role="form">
                                    <div class="col-md-9">
                                        <label for="inputStatus" class="col-md-3 control-label"><b><i
                                                        class="fa fa-filter"></i><?php echo $this->lang->line('common_document');?> </b></label><!--Document-->
                                        <div class="col-md-4">
                                            <?php echo form_dropdown('id', $all_crm_status_arr, '', 'onchange="fetch_cat_status()" class="form-control" id="id"'); ?>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                </form>

                                <table id="usersTable" class="table ">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $this->lang->line('common_document');?></th><!--Document-->
                                        <th><?php echo $this->lang->line('common_category');?></th><!--Category-->
                                        <th>SLA Days</th><!--Category-->
                                        <th><?php echo $this->lang->line('crm_backgroud_color');?></th><!--Background Color-->
                                        <th><?php echo $this->lang->line('crm_text_color');?></th><!--Text Color-->
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
                    <h4 class="modal-title"><?php echo $this->lang->line('crm_add_new_category');?></h4><!--Add New Category-->
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="crm_documentStatus">

<input type="hidden" id="categoryID" name="categoryID">
                        <!-- Select Basic -->
                        <div class="form-group">
                            <!-- <label class="col-md-4 control-label" for="selectbasic"></*?php echo $this->lang->line('common_document');*/?> </label>Document -->
                            <div class="col-md-6" id="">
                                <!-- <?php /*echo form_dropdown('documentID', $all_crm_status_arr, '', 'class="form-control" id="documentID""'); */?> -->
                                <input type="hidden"  id="documentID" name="documentID" class="form-control" value="2">


                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="selectbasic"><?php echo $this->lang->line('common_description');?> </label><!--Description-->
                            <div class="col-md-6" id="">
                                <input type="text" id="description" name="description" class="form-control">


                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="selectbasic">SLA Days </label>
                            <div class="col-md-6" id="">
                                <input type="number" id="sldays" name="sldays" class="form-control">


                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="selectbasic"><?php echo $this->lang->line('crm_text_color');?> </label><!--Text Color-->
                            <div class="col-md-6" id="">
                                <div id="cp2" class="input-group colorpicker-component">
                                    <input type="text" readonly id="textColor" name="textColor" value="#000000" class="form-control"/>
                                    <span class="input-group-addon"><i></i></span>
                                </div>


                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="selectbasic"><?php echo $this->lang->line('crm_backgroud_color');?>  </label><!--Background Color-->
                            <div class="col-md-6" id="">
                                <div id="cp3" class="input-group colorpicker-component">
                                    <input type="text" readonly id="backgroundColor" name="backgroundColor" value="#000000" class="form-control"/>
                                    <span class="input-group-addon"><i></i></span>
                                </div>


                            </div>
                        </div>

                        <!-- Button -->
                        <!-- <div class="form-group">
                            <label class="col-md-4 control-label" for="singlebutton"></label>
                            <div class="col-md-4">
                                <button type="button" id="singlebutton" onclick="submitstatus()" name="singlebutton"
                                        class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_submit');?>
                                </button>
                            </div>
                        </div> -->


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="singlebutton" onclick="submitstatus()" name="singlebutton"
                                        class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_submit');?>
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                </div>
            </div>

        </div>
    </div>

           <!-- Assigne Modal -->
           <div id="add-assigne-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Employee </h4><!--Add New User-->
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="crm_assignhod">

                    <!-- <input type="hidden" id="categoryID" name="categoryID"> -->

                    <div class="form-group">
                            <label class="col-md-4 control-label" for="selectbasic"><?php echo $this->lang->line('crm_select_an_employee');?> </label><!--Select an Employee-->
                            <div class="col-md-6" id="div_loaduser">
                            </div>
                        </div>

                    
                        <!-- Button -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="singlebutton"></label>
                            <div class="col-md-4">
                                <button type="button" id="singlebutton" onclick="savetaskhod()" name="singlebutton" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_submit');?> </button><!--Submit-->
                            </div>
                        </div>


                    </form>

                    <table id="assignedEmployee" class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_employee_name');?> </th><!--Employee Name-->
                            <th>Is Default</th>
                            <th></th>
                        </tr>
                        </thead>

                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button><!--Close-->
                </div>
            </div>

        </div>
    </div>

    <script>
        loaduserDropDown();
        $('#cp2').colorpicker();
        $('#cp3').colorpicker();
        fetch_cat_status();

        function modal_addCategory(){
            $('#documentID').val('2');
            $('#description').val('');
            $('#categoryID').val('');
            $('#textColor').colorpicker('setValue', '');
            $('#backgroundColor').colorpicker('setValue', '');
            $('#add-user-modal').modal('show');
        }


        function editDocumentStatus(categoryID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'categoryID': categoryID},
                url: "<?php echo site_url('Task_management/get_all_categories'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();


                    if(!data['category_assign'] || Object.keys(data['category_assign']).length === 0){
                        
                    $('#add-user-modal').modal('show');
                    $('#documentID').val(data['category_data']['documentID']);
                    $('#description').val(data['category_data']['description']);
                    $('#sldays').val(data['category_data']['sladays']);
                    $('#categoryID').val(data['category_data']['categoryID']);
                    $('#textColor').colorpicker('setValue', data['category_data']['textColor']);
                    $('#backgroundColor').colorpicker('setValue', data['category_data']['backGroundColor']);

                    }else{

                    $('#add-user-modal').modal('show');
                    $('#documentID').val(data['category_data']['documentID']);
                    $('#description').val(data['category_data']['description']);
                    $('#sldays').val(data['category_data']['sladays']);
                    $('#categoryID').val(data['category_data']['categoryID']);
                    $('#textColor').colorpicker('setValue', data['category_data']['textColor']);
                    $('#backgroundColor').colorpicker('setValue', data['category_data']['backGroundColor']);
                    $('#description').prop('readOnly', true);

                    }
                  


                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

        function deleteDocumentStatus(categoryID) {
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
                        data: {'categoryID': categoryID},
                        url: "<?php echo site_url('Task_management/deleteCaegory'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if (data['error'] == 1) {
                                    
                                myAlert('e', data['message']);
                                    // refreshNotifications(true);
                                    stopLoad();

                                }
                            else{
                                refreshNotifications(true);
                                stopLoad();
                                myAlert('s', 'Deleted Successfully');
                                fetch_cat_status();


                            }
                           
                        }, error: function () {

                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }


        function fetch_cat_status() {
            var Otable = $('#usersTable').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Task_management/fetch_cat_status'); ?>",
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

                    {"width": "10%", "targets": 5}, {"searchable": false, "targets": [0,3,4,5]}
                ],
                "aoColumns": [
                    {"mData": "categoryID"},
                    {"mData": "document"},
                    {"mData": "category"},
                    {"mData": "sladays"},
                    {"mData": "backGroundColor"},
                    {"mData": "textColor"},
                    {"mData": "edit"}

                ],
                //"columnDefs": [{"targets": [2], "orderable": false}],
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
                url: "<?php echo site_url('Task_management/create_category_status'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data[0] == 's') {
                        $('#add-user-modal').modal('hide');
                        $('#documentID').val('2');
                        $('#description').val('');
                        $('#categoryID').val('');
                        $('#sldays').val('');
                        $('#textColor').colorpicker('setValue', '');
                        $('#backgroundColor').colorpicker('setValue', '');

                    }
                    myAlert(data[0], data[1]);

                    fetch_cat_status();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }


function savetaskhod() {
        var data = $('#crm_assignhod').serializeArray();
        data.push({name: 'categoryID', value: $('#categoryID').val()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Task_management/savetaskhod'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
              myAlert(data[0],data[1]);
                loaduserDropDown();
                fetch_assignedEmployee();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function resetModal() {
    // $('#div_loaduser').empty();
    $('#assignedEmployee tbody').empty();
}

    function fetch_assignedEmployee() {
        fetch_cat_status()
            $('#assignedEmployee').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Task_management/load_assigned_employee'); ?>",
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
                    {"width": "2%", "targets": 0},

                    {"width": "6%", "targets": 1},
                    {"width": "6%", "targets": 2},
                    {"width": "7%", "targets": 3}
                ],
                "aoColumns": [
                    {"mData": "categoryID"},

                    {"mData": "employeeName"},
                    {"mData": "isdefault"},
                    {"mData": "edit"}

                ],
                //"columnDefs": [{"targets": [2], "orderable": false}],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "categoryID", "value": $("#categoryID").val()});
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

        function delete_userdetail(categoryID, inchageempID) {
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
            data: {'categoryID': categoryID,'inchageempID':inchageempID},
            url: "<?php echo site_url('Task_management/delete_userdetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                stopLoad();
                myAlert('s', 'Deleted Successfully');
                // loaduserDropDown($('#groupID').val());
                fetch_assignedEmployee();
            }, 
            error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    });
}


        function assignedepartment(categoryID) {
         
           
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'categoryID': categoryID},
                    url: "<?php echo site_url('Task_management/Assigne_task_department'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        resetModal()
                        $('#add-assigne-modal').modal('show');
                        fetch_assignedEmployee();
                         geteditdetails(categoryID);
                        $('#taskhodid').val(data['inchageempID']);
                        $('#categoryID').val(data['categoryID']);
                    },
                    error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
        }

        function activateUser(thisID,inchageempID){
            value=0;
        if ($(thisID).is(':checked')) {
            value=1;
        }

        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'inchageempID':inchageempID,value:value},
            url :"<?php echo site_url('Task_management/activateUser'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                refreshNotifications(true);
                stopLoad();
                myAlert('s','Sucessfully updated');
            },error : function(){
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
        
    }

        function geteditdetails(categoryID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'categoryID': categoryID},
                    url: "<?php echo site_url('Task_management/get_details_hod'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        resetModal()
                        $('#add-assigne-modal').modal('show');
                        $('#categoryID').val(data['categoryID']);
                        // $('#departmentid').val(data['departmentID']);
                        $('#taskhodid').val(data['inchageempID']);
                        fetch_assignedEmployee();
                    },
                    error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
        }
 
            function loaduserDropDown() {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {},
            url: "<?php echo site_url('Task_management/fetch_userDropdown_employee'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
            $('#div_loaduser').html(data);
                $('#employeesID').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    numberDisplayed: 1,
                    buttonWidth: '180px',
                    maxHeight: '30px'
                });
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        }




    </script>