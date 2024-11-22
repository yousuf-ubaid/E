

<!--Translation added by Naseek-->
<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_shift_config');
echo head_page($title, false);




$shiftID = $this->input->post('page_id');
$shiftDescription = $this->input->post('data_arr');
$empArray = fetch_employeeShift($shiftID);
?>
    <style type="text/css">
        .img-circle {
            height: 80px;
            width: 80px;
        }

        .users-list > li {
            width: 107px;
            margin-right: 1%;
        }

        .users-list > li img {
            height: 80px
        }

        .userClose {
            float: right;
            margin-bottom: -15px;
        }

        .tempTableContainer {
            height: 396px;
            overflow-y: scroll
        }

        div.tempTableContainer::-webkit-scrollbar, div.smallScroll::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        div.tempTableContainer::-webkit-scrollbar-track, div.smallScroll::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }

        div.tempTableContainer::-webkit-scrollbar-thumb, div.smallScroll::-webkit-scrollbar-thumb {
            margin-left: 30px;
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
            width: 3px;
            position: absolute;
            top: 0px;
            opacity: 0.4;
            border-radius: 7px;
            z-index: 99;
            right: 1px;
            height: 40px;
        }
    </style>

    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="">
        <div class="col-md-12 well well-sm">
            <span class="col-md-3" style="font-size: 15px"><?php echo $shiftDescription ?></span>
            <!--<span class="col-md-7 pull-right">
                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="openEmployeeModal()"><i
                        class="fa fa-plus"></i> Add
                </button>
            </span>-->
            <div class="clearfix visible-xs visible-sm"></div>
        </div>
    </div>
    <div class="row" style="margin: 5px">
        <div class="no-padding">
            <ul class="users-list clearfix">
                <?php
                foreach ($empArray as $row) {
                    $empImg = empImage($row['EmpImage']);
                    //<i class="fa fa-times-circle userClose" aria-hidden="true" onclick="removeEmployee(' . $row['EIdNo'] . ')"></i>
                    echo '<li class="well">

                        <img src="' . $empImg . '" alt="User Image" class="img-circle" />
                        <a class="users-list-name" href="#">' . $row['Ename1'] . '</a>
                        <span class="users-list-date"> ' . $row['ECode'] . ' </span>
                         <button type="button" class="btn btn-xs btn-danger" title="Delete" onclick="deleteempassignshift(' . $row['autoID'] . ')"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                      </li>';
                }
                ?>
            </ul>
        </div>
    </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="add_empModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 75%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_attendance_add_employee');?><!--Add Employees--></h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="isEmpLoad" value="0">
                    <div class="table-responsive col-md-7">
                        <table id="emp_modalTB" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 25%"><?php echo $this->lang->line('hrms_attendance_employee_code');?><!--EMP Code--></th>
                                <th style="width:auto"><?php echo $this->lang->line('common_employee_name');?><!--Employee Name--></th>
                                <th style="width: 5%">
                                    <div style="text-align: center !important;">
                                        <button class="btn btn-primary btn-xs" style="font-size:10px; margin-left: 12px" onclick="add_allEmpToTempTB()">
                                            + <?php echo $this->lang->line('common_add_all');?><!--Add All-->
                                        </button>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="table-responsive col-md-5">
                        <div class="tempTableContainer">
                            <input type="hidden" name="masterID" id="masterID"/>
                            <input type="hidden" name="type_m" value="MA"/>

                            <table class="<?php echo table_class(); ?>" id="tempTB">
                                <thead>
                                <tr>
                                    <th style="width: 15%"><?php echo $this->lang->line('hrms_attendance_employee_code');?><!--EMP CODE--></th>
                                    <th style="width: 70%"><?php echo $this->lang->line('hrms_attendance_employee_name');?><!--EMP NAME--></th>
                                    <th style="width: 5%">
                                        <span class="glyphicon glyphicon-trash" onclick="clearAllRows()"
                                              style="color:rgb(209, 91, 71);"></span>
                                        <!--<button class="btn btn-default btn-xs" onclick="clearAllRows()">- Clear All </button>-->
                                    </th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm" onclick="save_ShiftEmp()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <form id="post_form">
                <input type="hidden" name="employees" id="employees">
                <input type="hidden" name="masterID" id="masterID" value="<?php echo $shiftID; ?>">
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var newLeave_form = $('#newShift_form');
    var add_empModal = $('#add_empModal');
    var tempTB = $('#tempTB').DataTable({
        "bPaginate": false,
        "aaSorting": [[1, 'asc']],
        "aoColumnDefs": [{"bSortable": false, "aTargets": [0, 2]}],
        "fnDrawCallback": function () {
            var tempTB_wrapper = $('#tempTB_wrapper');

            /*var myBtn = '<div id="headerBtn-group" style="padding: 5px 0px">';
             myBtn += '<button class="btn btn-primary btn-xs" onclick="addAllRows()">+ Add All </button>&nbsp;&nbsp;';
             myBtn += '<button class="btn btn-default btn-xs" onclick="clearAllRows()">- Clear All </button>';
             myBtn += '</div>';
             $('#headerBtn-group').remove();
             tempTB_wrapper.find('.row .col-sm-6').first().append(myBtn);*/

            tempTB_wrapper.css('width', '95%');

            $('#tempTB').parent().removeClass('col-sm-12').css('margin-left', '15px');

        }
    });

    var shiftEmployee_arr = [];

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/shift_master', <?php echo $shiftID ?>, 'HRMS');
        });


        newLeave_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                shiftDescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
            }
        })
        .on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form = $(e.target);
            var postData = $form.serializeArray();
            var urlReq = $form.attr('action');


            $.ajax({
                type: 'post',
                url: urlReq,
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        add_empModal.modal('hide');
                        load_shiftMaster($('#editID').val());
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });

        });
    });


    function load_employeeForModal() {
        $('#emp_modalTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/getEmployeesDataTable'); ?>?entryDate=Not_monthly_add_deductions",
            "aaSorting": [[2, 'asc']],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }

                $('.table-row-select tbody').on('click', 'tr', function () {
                    $('.table-row-select tr').removeClass('dataTable_selectedTr');
                    $(this).toggleClass('dataTable_selectedTr');
                });
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "ECode"},
                {"mData": "empName"},
                {"mData": "addBtn"}
            ],
            "aoColumnDefs": [{"bSortable": false, "aTargets": [0, 1, 3]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function add_allEmpToTempTB() {
        var tempTB = $('#tempTB').DataTable();
        var emp_modalTB = $('#emp_modalTB').DataTable();
        var empDet;
        var allReadyExistEmployees = '';

        emp_modalTB.rows().every(function (rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            var thisEmpID = data.EIdNo;

            var inArray = $.inArray(thisEmpID, shiftEmployee_arr);

            if (inArray == -1) {
                shiftEmployee_arr.push(thisEmpID);
                empDet = '<div class="pull-right"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" onclick="removeTempTB(this)"></span></div>';
                tempTB.rows.add([{
                    0: data.ECode,
                    1: data.empName,
                    2: empDet
                }]).draw();
            }
            else {
                allReadyExistEmployees += data.ECode + ' - ' + data.empName + ' </br>';
            }

        });

        if (allReadyExistEmployees != '') {
            myAlert('w', 'Following Employee/s are already exist in this shift. </br>' + allReadyExistEmployees);
        }
    }

    function addTempTB(det) {

        var table = $('#emp_modalTB').DataTable();
        var thisRow = $(det);
        var details = table.row(thisRow.parents('tr')).data();
        var thisEmpID = details.EIdNo;
        var inArray = $.inArray(thisEmpID, shiftEmployee_arr);

        if (inArray == -1) {
            shiftEmployee_arr.push(thisEmpID);
            var empDet = '<div class="pull-right"><span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" style="color:rgb(209, 91, 71);"></span></div>';

            tempTB.rows.add([{
                0: details.ECode,
                1: details.empName,
                2: empDet
            }]).draw();
        }
        else {
            myAlert('w', details.ECode + ' - ' + details.empName + '</br> <?php echo $this->lang->line('hrms_attendance_is_already_exist_in_this_shift');?>.');<!--is already exist in this shift-->
        }

    }

    function openEmployeeModal() {
        add_empModal.modal({backdrop: "static"});
        load_employeeForModal();
    }

    function save_ShiftEmp() {
        $('#employees').val( JSON.stringify(shiftEmployee_arr) );
        var empPostData = $('#post_form').serializeArray();

        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/save_ShiftEmp'); ?>",
            type: 'post',
            dataType: 'json',
            data: empPostData,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    add_empModal.modal('hide');
                    setTimeout(function(){
                        fetchPage('system/hrm/shift_config',<?php echo $shiftID; ?>,'HRMS', 0, '<?php echo $shiftDescription; ?>');
                    }, 300);

                }
            }, error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }


    function delete_shift(delID, des) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Employee/deleteShiftMaster'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'deleteID': delID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_shiftMaster()
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function removeEmployee(id) {

    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function deleteempassignshift(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Employee/deleteEmpAssignedShift'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'autoID': id},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/hrm/shift_config',<?php echo $shiftID; ?>,'HRMS', 0, '<?php echo $shiftDescription; ?>');
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }
</script>


<?php
