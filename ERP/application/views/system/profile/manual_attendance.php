
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_manual_attendance_management');
echo head_page($title  , false);


//$current_date = format_date($this->common_data['current_date']);
$floors_arr   = floors_drop(true);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$deparments = fetch_emp_departments(true);
$location = fetch_emp_departments(true);

?>

<style type="text/css">
    #empSearchLabel {
        float: right !important;
        font-weight: 600
    }

    @media (max-width: 767px) {
        #empSearchLabel {
            float: left !important;
        }

        #new-attBtn{
            margin-bottom: 30px;
        }
    }

    .trInputs{
        width: 100%;
        padding: 2px 4px;
        height: 22px;
        font-size: 12px;
    }

    .hideTr{ display: none }

    .oddTR td{ background: #f9f9f9 !important; }

    .evenTR td{ background: #ffffff !important; }

    .fixHeader_Div {
        height: 340px;
        border: 1px solid #c0c0c0;
    }

    #attendanceReview td { vertical-align: middle; }
    #attendanceReview th { z-index: 10; }

    #attendanceReview tr:hover > td{
        background: #96e277 !important;  /*#2c4762*/
        color: #ffffff;
    }

    #attendanceReview tr:hover > td .trInputs{
        color: #000;
    }

    #attendanceReview tr:hover > td.fixed-td{
        background: #96e277 !important; /*#2c4762*/
        color: #ffffff;
    }

    .timeBox{
        text-align: right;
        padding: 2px;
    }
    .attType{
        height: 22px;
        padding: 2px;
        font-size: 12px;
    }

    .fixed-td{ z-index: 10; }
    /*.oddTR>.fixed-td{ background: #3cd6e6 !important; z-index: 10; color: #f3f3f3 }
    .evenTR>.fixed-td{ background: #97eaf4 !important; z-index: 10 }*/
    #attendanceReview tr:hover {background-color: #FFFFAA;}
    #attendanceReview tr.selected td {
        background: none repeat scroll 0 0 #FFCF8B;
        color: #000000;
    }
    .table-striped tbody tr.highlight td {
        background-color: rgba(167, 251, 132, 0.35) !important;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
    <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
        <li class="active"><a href="#attendanceTab" id="accountsTab" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('hrms_attendance_attendance');?><!--Attendance--></a></li>
       <!-- <li class=""><a href="#pullingTab" id="pulling" data-toggle="tab" aria-expanded="false">Pulling Data</a></li>-->
    </ul>
    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">

        <div class="tab-pane active disabled" id="attendanceTab"> <!-- Start of attendanceTab -->
            <div class="row">
                <div class="col-md-5">
                    <table class="<?php echo table_class(); ?>">
                        <tr>
                            <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_closed');?><!--Closed--></td>
                            <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_not_closed');?><!--Not Closed--></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4 text-center">
                    &nbsp;
                </div>
                <div class="col-md-3 text-right">
                    <button type="button" class="btn btn-primary pull-right" onclick="open_attendanceModal()" id="new-attBtn">
                        <i class="fa fa-plus"></i> <?php echo $this->lang->line('hrms_attendance_create_attendance');?><!--Create Attendance-->
                    </button>
                </div>
                <div class="clearfix">&nbsp;</div>
                <div class="table-responsive">
                    <table id="attendanceMasterTB" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('hrms_attendance_done_by');?><!--Done By--></th>
                            <th><?php echo $this->lang->line('hrms_attendance_done_by_name');?><!--Done By Name--></th>
                            <th><?php echo $this->lang->line('hrms_attendance_attendance_date');?><!--Attendance Date--></th>
                            <th><?php echo $this->lang->line('hrms_attendance_floor');?><!--Attendance Time--></th>
                            <th><?php echo $this->lang->line('hrms_attendance_is_closed');?><!--Is Closed--></th>
                            <th style="width: 80px"></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div> <!-- End of attendanceTab -->

 <!-- End of pullingTab -->
    </div>
</div>


<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="newAttendance" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="salary-cat-title"><?php echo $this->lang->line('hrms_attendance_new_attendance');?><!--New Attendance--></h4>
            </div>
            <?php echo form_open('','role="form" class="" id="newAttendance_form" autocomplete="off"' ); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label class="control-label" for="description"><?php echo $this->lang->line('hrms_attendance_attendance_date');?><!--Attendance Date--> <?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <!--<input type="text" name="attendanceDate" value="<?php /*echo $current_date; */?>" id="attendanceDate" class="form-control dateField">-->
                                <input type='text' class="form-control" id="attendanceDate" name="attendanceDate" value="<?php echo $current_date; ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-10">
                        <div class="form-group">
                            <label class="control-label" for="floorID"><?php echo $this->lang->line('hrms_attendance_floor');?> <?php required_mark(); ?></label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-map-marker"></i></div>
                                <?php echo form_dropdown('floor', $floors_arr, '', 'class="form-control select2" id="floor" '); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-10">
                        <div class="form-group">
                            <label class="control-label" for="category">Employee Selection</label>
                            <div class="input-group">

                                <input type="radio" name="empSelfOrOthers" id="empSelf" value="2" /> <label for="empSelf">&nbsp Self</label><br>
                                
                                <input type="radio" name="empSelfOrOthers" id="empSelfOrOthers" value="1" /> <label for="empSelfOrOthers">&nbsp Reporting</label><br>

                                <input type="radio" name="empSelfOrOthers" id="empSelf" value="0" /> <label for="empSelf">&nbsp All</label><br>

                                <input type="radio" name="empSelfOrOthers" id="empSelf" value="3" /> <label for="empSelfAttendee">&nbsp My Attendees</label><br>
                                
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm" id="saveBtn" ><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>

<div class="modal fade" id="attendanceDetail" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="padding: 10px 5px">
                <div class="row">
                    <div class="col-sm-4">
                        <h4 class="modal-title" id="salary-cat-title">
                            <?php echo $this->lang->line('hrms_attendance_staff_attendance_list');?><!--Staff Attendance List--> &nbsp;&nbsp;&nbsp; <span class="hidden-lg attendance-date-time"></span>
                        </h4>
                    </div>
                    <div class="col-sm-5 hidden-md hidden-sm hidden-xs">
                        <h4 class="modal-title attendance-date-time"></h4>
                    </div>
                    <div class="clearfix visible-xs">&nbsp;</div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="searchItem" id="searchItem" value="" style="height: 28px">
                            <span class="input-group-addon" style="">
                                <i class="glyphicon glyphicon-search" style="font-size:12px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_open('','role="form" class="" id="attendanceDetail_form" autocomplete="off"' ); ?>
            <div class="modal-body" style="padding: 0px;">
                <div class="box" style="margin-bottom: 0px; border: 0px;">
                    <div class="box-body" id="attDetails_div" style="min-height: 300px; padding: 0px">

                    </div>
                    <div class="overlay" id="overlay" style="background: rgba(0, 0, 0, 0.59)"><i class="fa fa-refresh fa-spin"></i></div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-3">
                        <label class="pull-left">
                            Showing <span id="showingCount"> 0 </span> of
                            <span id="totalRowCount"> 0 </span>  entries
                        </label>
                    </div>

                    <div class="col-sm-9 hide" style="">
                        <b> <?php echo $this->lang->line('hrms_attendance_close_attendance');?><!--Close Attendance--> :</b>
                        <input type="checkbox" name="isComplete" id="isComplete"  />
                            &nbsp;&nbsp;&nbsp;
                        <input type="hidden" name="attendMasterID"  id="attendMasterID" />
                        <button type="button" class="btn btn-primary btn-sm modalBtn" id="saveAatDetail_btn" onclick="saveAttendance()" ><?php echo $this->lang->line('common_save');?><!--Save--></button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>

                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>




<script type="text/javascript">
    
    var newAttendance = $('#newAttendance');
    var attendanceDetail = $('#attendanceDetail');
    var newAttendance_form = $('#newAttendance_form');
    var attMasterTB;
    var attendanceReview = $('#attendanceReview');

    $(document).ready(function () {

        $('#departments').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            allSelectedText: 'All Selected',
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('.headerclose').click(function(){
            fetchPage('system/profile/attendance-view','Test','HRMS');
        });

        $('#attendanceTime').timepicker({
            minuteStep: 1,
            template: 'dropdown',
            appendWidgetTo: 'body',
            showSeconds: false,
            showMeridian: true
        }).change(function () {
            newAttendance_form.bootstrapValidator('revalidateField', 'attendanceTime');
        });

        $('.dateField').datepicker({ format: 'yyyy-mm-dd' }) .on('changeDate', function (ev) {
            $(this).datepicker('hide');
            if(this.id == 'attendanceDate'){
                newAttendance_form.bootstrapValidator('revalidateField', 'attendanceDate');
            }
            if(this.id == 'fromDate'){
                $('#attFetching_form').bootstrapValidator('revalidateField', 'fromDate');
            }
            else{
                $('#attFetching_form').bootstrapValidator('revalidateField', 'toDate');
            }
        });

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });

        newAttendance_form.bootstrapValidator({
            live            : 'enabled',
            message         : 'This value is not valid.',
            excluded        : [':disabled'],
            fields          : {
                attendanceDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_attendance_attendance_date_is_required');?>.'}}},/*Attendance date is required*/
                attendanceTime: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_attendance_attendance_time_is_required');?>.'}}}/*Attendance time is required*/
            }
        })
        .on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();


            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : data,
                url :"<?php echo site_url('Employee/new_attendance_self_service'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $form.bootstrapValidator('resetForm', true);
                        newAttendance.modal('hide');
                        var attID = data[2];
                        load_attendanceTB(attID);
                        /*setTimeout(function(){
                            open_attendanceDetailModal(attID);
                        }, 400);*/

                    }
                },error : function(){
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        });


        $('#attPulling_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                attendanceFile: {
                    validators: {
                        file: {
                            maxSize: 4096 * 1024,   // 2 MB
                            message: '<?php echo $this->lang->line('common_the_selected_file_is_not_valid');?>'/*The selected file is not valid*/
                        },
                        notEmpty: {message: '<?php echo $this->lang->line('common_file_is_required');?>.'}/*File is required*/
                    }
                },
                upload_fromDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_from_date_is_required');?>.'}}},/*From date is required*/
                upload_toDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_to_date_is_required');?>.'}}},/*To date is required*/
                floorID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_floor_is_required');?>.'}}}/*Floor is required*/
            },
            floorID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_floor_is_required');?>.'}}}/*Floor is required*/
        })
           .on('success.form.bv', function (e) {
            e.preventDefault();

            var formData = new FormData($("#attPulling_form")[0]);

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: formData,
                url: '<?php echo site_url('Employee/attendancePulling'); ?>',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if( data[0] == 's'){
                        /*setTimeout(function () { fetch_document(); },400);*/
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        });

        $('#attFetching_form').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {
                    fromDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_date_is_required');?>.'}}},/*Date is required*/
                    toDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_date_is_required');?>.'}}}/*Date is required*/
                }
            })
            .on('success.form.bv', function (e) {
                $('#loadBtn').prop('disabled', false);
                e.preventDefault();
                loadDataFromTemptable();
        });

        load_attendanceTB();

        attendanceReview.tableHeadFixer({
            head: true,
            foot: true,
            /*left: 3,
            right: 0,*/
            'z-index': 10
        });
    });


    function load_attendanceTB(selectedRowID=null){
        var selectedRowID = (selectedRowID == null)?  '<?php echo $this->input->post('page_id'); ?>' : selectedRowID;

        attMasterTB = $('#attendanceMasterTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_attendance_self_service'); ?>",
            "aaSorting": [[3, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if( parseInt(oSettings.aoData[x]._aData['EmpAttMasterID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "EmpAttMasterID"},
                {"mData": "doneByCode"},
                {"mData": "doneByName"},
                {"mData": "AttDate"},
                {"mData": "floorDescription"},
                {"mData": "isClosed"},
                {"mData": "action"}
            ],
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

    function open_attendanceModal(){
        newAttendance_form[0].reset();
        newAttendance_form.bootstrapValidator('resetForm', true);
        newAttendance.modal('show');
    }

    // function open_attendanceDetailModal(attID, obj){
    //     $('#attendMasterID').val(attID);
    //     var attDetails_div = $('#attDetails_div');
    //     attDetails_div.append('');
    //     attendanceDetail.modal('show');

    //     var thisRow = $(obj);
    //     var details = attMasterTB.row(thisRow.parents('tr')).data() ;

    //     $('.attendance-date-time').html( details.AttDate +' | '+ details.AttTime );


    //     $.ajax({
    //         async : true,
    //         type : 'post',
    //         dataType : 'html',
    //         data : {'attID': attID},
    //         url :"<?php //cho site_url('Employee/load_attendanceEmployees_new'); ?>",
    //         beforeSend: function () {
    //             $("#overlay").show();
    //             attDetails_div.html('');
    //         },
    //         success : function(data){
    //             $("#overlay").hide();
    //             attDetails_div.html(data);
    //             if (data[0] == 'e') {
    //                 myAlert(data[0], data[1]);
    //             }
    //         },error : function(){
    //             myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
    //             attDetails_div.html('');
    //             $("#overlay").hide();
    //         }
    //     });
    // }

    function delete_attendanceMaster(attID){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"

            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('Employee/delete_attendanceMaster'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hidden-id':attID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ load_attendanceTB() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function saveAttendance(){
        var postData = $('#attendanceDetail_form').serializeArray();

        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : postData,
            url :"<?php echo site_url('Employee/save_attendanceDetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0], data[1]);
                if( data[0] == 's'){
                    attendanceDetail.modal('hide');
                    load_attendanceTB()
                }
            },error : function(){
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    /*** Attendance review  functions****/
    function loadDataFromTemptable(){
        var postData = $('#attFetching_form').serializeArray();

        $.ajax({
            type : 'post',
            dataType : 'json',
            data :postData,
            url: '<?php echo site_url('Employee/load_empAttDataView'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#attendanceReview >tbody').html( data['tBody'] );
                $('#attReview-showingCount').text( data['rowCount'] );
                $('#attReview-totalRowCount').text( data['rowCount'] );

                makeDate_dropDown( data['date_arr'] );
                unAssignedData_manipulation( data['unAssignedMachineID'], data['unAssignedShifts'] );

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                $('#attendanceReview >tbody').html('');
                $('#attendanceReview').append('<tr><td colspan="21">No data available in table </td></tr>');

                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function makeDate_dropDown(date_arr){
        var searchDate = $('#searchDate');
        var options = '<option value="" selected="selected"><?php echo $this->lang->line('hrms_attendance_search_date');?></option>';<!--Search Date-->

        searchDate.empty();
        $.each(date_arr, function (val) {
            options += '<option value="'+val+'" >'+val+'</option>';
        });
        searchDate.append(options);
    }

    function unAssignedData_manipulation(unAssignedMachineID_arr, unAssignedShifts_arr){
        var unAssignedShift_div = $('#unAssignedShift-div');
        var unAssignedMachine_div = $('#unAssignedMachine-div');

        unAssignedShift_div.hide();
        unAssignedMachine_div.hide();

        if( unAssignedMachineID_arr.length > 0 ){
            unAssignedMachine_div.show();

            $('#unAssignedMachine tbody').remove();
            var unAssignedMachineTB = $('#unAssignedMachine');
            var machineDet = '';

            $.each(unAssignedMachineID_arr, function(i, row){
                machineDet += '<tr>';
                machineDet += '<td>'+(i+1)+'</td>';
                machineDet += '<td>'+row['ECode']+'</td>';
                machineDet += '<td>'+row['Ename1']+'</td>';
                machineDet += '<td></td>';
                machineDet += '</tr>';
            });

            unAssignedMachineTB.append(machineDet);
        }

        if( unAssignedShifts_arr.length > 0 ){
            unAssignedShift_div.show();

            $('#unAssignedShift tbody').remove();
            var unAssignedShiftTB = $('#unAssignedShift');
            var shiftDet = '';

            $.each(unAssignedShifts_arr, function(i, row){
                shiftDet += '<tr >';
                shiftDet += '<td>'+(i+1)+'</td>';
                shiftDet += '<td>'+row['ECode']+'</td>';
                shiftDet += '<td>'+row['Ename1']+'</td>';
                shiftDet += '<td></td>';
                shiftDet += '</tr>';
            });

            unAssignedShiftTB.append(shiftDet);
        }
    }

    function save_attReview(){
        var postData = $('#attendanceReview_form').serializeArray();
        $.ajax({
            type : 'post',
            dataType : 'json',
            data :postData,
            url: '<?php echo site_url('Employee/save_attendanceReviewData'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });


    }

    $('#attendanceReview').on('click', 'tbody tr', function(event) {
        $(this).addClass('highlight').siblings().removeClass('highlight');
    });




</script>
<?php
