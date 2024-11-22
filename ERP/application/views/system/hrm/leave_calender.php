<?php $exist = get_calender();

?>
<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_leave_calender');
$date_format_policy = date_format_policy();
echo head_page($title, false);


?>
<style>

    #calendar {
        max-width: 900px;
        margin: 0 auto;
    }

    .fc-content {

        border: none;
        padding: 2px;
        font-size: 10px;
    }

    #accrual-data-table tbody tr:hover{
        background-color: #B0BED9 !important;
        cursor: pointer;
    }

</style>
<link href='<?php echo base_url('plugins/fullcalender/fullcalendar.min.css'); ?>' rel='stylesheet'/>
<link href='<?php echo base_url('plugins/fullcalender/fullcalendar.print.min.css'); ?>' rel='stylesheet' media='print'/>
<style>

</style>
<!--<script type="text/javascript" src="<?php /*echo base_url('plugins/fullcalender/lib/moment.min.js'); */ ?>"></script>-->
<script type="text/javascript" src="<?php echo base_url('plugins/fullcalender/fullcalendar.min.js'); ?>"></script>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">


    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="createCalender()"><i class="fa fa-plus"></i>
            <?php echo $this->lang->line('hrms_leave_management_create_new'); ?><!--Create New-->
        </button>
    </div>
    <?php if (!empty($exist)) { ?>
        <div class="col-md-4">
            <table class="table table-bordered table-striped table-condensed table-row-select">
                <tbody>
                <tr>
                    <td>
                        <span class="label"
                              style="background-color:rgb(143, 223, 130);padding: 0px 5px ;font-size: 80%;">&nbsp;</span>
                        <?php echo $this->lang->line('hrms_leave_management_weekends'); ?><!--Weekends-->
                    </td>
                    <td>
                        <span class="label"
                              style="background-color:#AB47BC;padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                        <?php echo $this->lang->line('hrms_leave_management_event'); ?><!--Event-->
                    </td>
                    <td>
                        <span class="label"
                              style="background-color:#ff8a80;padding: 0px 5px ;font-size: 100%;">&nbsp;</span>
                        <?php echo $this->lang->line('hrms_leave_management_holiday'); ?><!--Holiday-->
                    </td>

                </tr>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>
<hr>
<div class="row">
    <div class="col-md-12">
        <?php
        if (!empty($exist)) {  ?>
            <div id='calendar'></div><?php
        } else { ?>
            <i>
                <?php echo $this->lang->line('hrms_leave_management_Please_generate_a_calender_for_this_year'); ?><!--Please generate a calender for this year--></i>
            <?php
        }
        ?>
    </div>
</div>
<br>
<br>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div id="calendarModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div id="modalBody" class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="leaveAccrualConf_modal" tabindex="2" role="dialog" aria-labelledby="myModalLabel" style="z-index: 999999"  >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Confirm Leave Accruals <span id="leaveAccrualConf_modal_title"></span></h4>
            </div>
            <div class="modal-body">
                <div id="leaveAccrualConf_data"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="proceedBtn" >Proceed</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCalender" role="dialog" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('hrms_leave_management_generate_calender'); ?><!--Generate Calender--></h4>
            </div>
            <div class="modal-body">
                <form id="calenderform" class="form-horizontal modal-body" role="form">
                    <div class="form-group">

                        <label class="control-label col-sm-3">
                            <?php echo $this->lang->line('hrms_leave_management_year'); ?><!--Year--></label>
                        <div class="col-sm-6">
                            <div class="input-group datepic">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" readonly class="form-control " name="year" id="year" value="<?php echo date('Y') ?>">
                            </div>
                        </div>
                    </div>
                    <?php $weekdays = array($this->lang->line('cal_sunday')/*'Sunday'*/, $this->lang->line('cal_monday')/*'Monday'*/, $this->lang->line('cal_tuesday')/*'Tuesday'*/, $this->lang->line('cal_wednesday')/*'Wednesday'*/, $this->lang->line('cal_thursday')/*'Thursday'*/, $this->lang->line('cal_friday')/*'Friday'*/, $this->lang->line('cal_saturday')/*'Saturday'*/); ?>
                    <div class="form-group">

                        <label class="control-label col-sm-3"> <?php echo $this->lang->line('hrms_leave_management_weekends'); ?><!--Weekends--></label>

                        <div class="col-sm-6">
                            <?php if ($weekdays) {
                                foreach ($weekdays as $day) {
                                    ?>
                                    <div class="checkbox">
                                        <label><input name="<?php echo $day; ?>" type="checkbox" value="1"><?php echo $day ?></label>
                                    </div>
                                    <?php
                                }
                            } ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" onclick="savecalender()" class="btn btn-primary">
                    <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEvent" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_update_add_new'); ?><!--Add New--></h4>
            </div>
            <div class="modal-body">
                <form id="infos" class="form-horizontal">
                    <input type="hidden" id="endDay" name="endDay">
                    <input type="hidden" id="startDay" name="startDay">
                    <fieldset>
                        <div class='form-group'><label class='col-md-4 control-label' for='textinput'>
                                <?php echo $this->lang->line('common_title'); ?><!--Title--></label>
                            <div class='col-md-4'><input id='title' name='title' type='text'
                                                         placeholder='<?php echo $this->lang->line('common_title'); ?>'
                                                         class='form-control input-md'></div>
                        </div>
                        <div class='form-group'><label class='col-md-4 control-label' for='radios'>
                                <?php echo $this->lang->line('common_type'); ?><!--Type--></label>
                            <div class='col-md-4'>
                                <div class='radio'><label for='radios-0'> <input type='radio' name='type' id='type'
                                                                                 value='1' checked='checked'>
                                        <?php echo $this->lang->line('hrms_leave_management_event'); ?><!--Event-->
                                    </label></div>
                                <div class='radio'><label for='radios-1'> <input type='radio' name='type' id='type'
                                                                                 value='2'>
                                        <?php echo $this->lang->line('hrms_leave_management_holiday'); ?><!--Holiday-->
                                    </label></div>
                            </div>
                        </div>
                        <div class='form-group hidetime'><label class='col-md-4 control-label' for='textinput'>
                                <?php echo $this->lang->line('hrms_leave_management_start_time'); ?><!--Start Time--></label>
                            <div class='col-md-4'><input id='startTime' name='startTime' type='text' placeholder=''
                                                         class='form-control input-md time'></div>
                        </div>
                        <div class='form-group hidetime'><label class='col-md-4 control-label' for='textinput'>
                                <?php echo $this->lang->line('hrms_leave_management_end_time'); ?><!--End Time--></label>
                            <div class='col-md-4'><input id='endTime' name='endTime' type='text' placeholder=''
                                                         class='form-control input-md time'></div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" onclick="insertCalender()" class="btn btn-primary">
                    <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
            </div>
        </div>
    </div>
</div>


<script>
    $('.headerclose').click(function () {
        fetchPage('system/hrm/leave_calender', '', 'HRMS');
    });
    $('.time').timepicker({
        template: false,
        showInputs: false,
        minuteStep: 5,
        defaultTime: false
    });

    $("#year").datepicker( {
        format: " yyyy", // Notice the Extra space at the beginning
        viewMode: "years",
        minViewMode: "years"
    });
    $(document).on("keypress", 'form', function (e) {
        var code = e.keyCode || e.which;
        if (code == 13) {
            e.preventDefault();
            return false;
        }
    });
    $(document).ready(function () {
        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });

        $('#infos input').on('change', function () {
            if ($('input[name=type]:checked', '#infos').val() == 2) {
                $('.hidetime').hide();
            }
            else {
                $('.hidetime').show();
            }

        });


        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,listMonth'
            },
            //restricting available dates to 2 moths in future
            viewRender: function (view, element) {
                startDate = "<?php echo $exist['startDate'] ?>";
                endDate = "<?php echo $exist['endDate'] ?>";

                var now = new Date(startDate);
                var end = new Date(endDate);
                if (end <= view.end) {
                    $("#calendar .fc-next-button").hide();
                    return false;
                }
                else {
                    $("#calendar .fc-next-button").show();

                }
                if (view.start <= now) {
                    $("#calendar .fc-prev-button").hide();
                    return false;
                }
                else {
                    $("#calendar .fc-prev-button").show();

                }
            },
            selectable: true,
            selectHelper: true,
            select: function (start, end) {


                $('#modalEvent').modal('show');
                $('#startDay').val(start);
                $('#endDay').val(end);


                var s = start;
                var startDate = moment(s, 'ddd MMM DD YYYY hh:mm:ss [GMT]ZZ').format('h:mm A');

                var e = end;
                var endDate = moment(e, 'ddd MMM DD YYYY hh:mm:ss [GMT]ZZ').format('h:mm A');

                var startTime = $('input[name=startTime]').val(startDate);
                var endTime = $('input[name=endTime]').val(endDate);


                return true;
                var addnew = '<?php echo $this->lang->line('common_update_add_new');?>';
                var title = '<?php echo $this->lang->line('common_title');?>';
                var type = '<?php echo $this->lang->line('common_type');?>';
                var holiday = '<?php echo $this->lang->line('hrms_leave_management_holiday');?>';


                var form = $("<form id='infos' class='form-horizontal'> <fieldset><legend>addnew<!--Add New--></legend><div class='form-group'> <label class='col-md-4 control-label' for='textinput'>title<!--Title--></label> <div class='col-md-4'> " +
                    "<input id='title' name='title' type='text' placeholder='title' class='form-control input-md'> </div> </div> <div class='form-group'> <label class='col-md-4 control-label' for='radios'>type<!--Type--></label> <div class='col-md-4'> " +
                    "<div class='radio'> <label for='radios-0'> <input type='radio' name='type' id='type' value='1' checked='checked'> Event </label> </div> <div class='radio'> <label for='radios-1'> <input type='radio' name='type' id='type' value='2'>" +
                    " holiday<!--Holiday--> </label> </div> </div> </div> <p id='datepairExample'> <input type='text' class='date start' /> <input type='text' class='time start' /> to " +
                    "<input type='text' class='time end' /> <input type='text' class='date end' /> </p></fieldset> </form>");

                bootbox.alert(form, function () {

                    var title = form.find('input[name=title]').val();
                    var type = form.find('input[name=type]:checked').val();
                    var eventData;
                    if (title) {
                        eventData = {
                            title: title,
                            start: start,
                            end: end

                        };

                        // $('#calendar').fullCalendar('renderEvent', eventData, true);
                        insert_calenderUpdates(title, start, end, type);
                        $('#calendar').fullCalendar('refetchEvents'); //refresh current calender
                        // Append
                    }
                });
                // $('#calendar').fullCalendar('unselect');
            },

            eventLimit: true,
            defaultDate: new Date(),
            navLinks: false, // can click day/week names to navigate views
            businessHours: false, // display business hours
            editable: false, //draggable
            events: {
                url: '<?php echo site_url('Employee/leaveCalenderEvent'); ?>',
                cache: false
            },
            eventRender: function (event, element) {
                element.find(".fc-content").append("<i style='color: white; font-size: 12px' class='fa fa-trash-o pull-right closeon' aria-hidden='true'></i>");
                element.find(".closeon").click(function () {
                    /*$('#calendar').fullCalendar('removeEvents',event._id);*/
                    deleteEvent(event._id);

                });
            }


        });

    });


    function savecalender() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $('#calenderform').serializeArray(),
            url: "<?php echo site_url('Employee/generateCalender'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $('#modalCalender').modal('hide');
                    setTimeout(function () {
                        stopLoad();
                        fetchPage('system/hrm/leave_calender', '', 'HRMS');
                    }, 300);


                }
                else {
                    myAlert('e', data['message']);
                }

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function createCalender() {
        $('#modalCalender').modal('show');
    }

    function deleteEvent(id) {

        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
            text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
            cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
        },

        function () {

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Employee/delete_event'); ?>",
                data: {id: id},
                dataType: "json",
                cache: false,
                success: function (data) {
                    //  $('#calendar').fullCalendar( 'refetchEvents' );

                    if(data[0] == 's'){
                        myAlert(data[0], data[1]);
                        $('#calendar').fullCalendar('refetchEvents');
                    }
                    else if(data[0] == 'e'){
                        myAlert(data[0], data[1]);
                    }
                    else if(data[0] == 'c'){
                        $('#leaveAccrualConf_data').html(data[1]);
                        $('#leaveAccrualConf_modal').modal('show');
                        $('#leaveAccrualConf_modal_title').html(' &nbsp; - &nbsp; Deduction');
                        $('#proceedBtn').attr('onclick', "deleteCalenderWithAccrual()");
                    }

                }
            });
            
        });
   
        
        return false;
    }

    function insertCalender(leaveAccruals=null) {
        form = $('#infos');
        var type = form.find('input[name=type]:checked').val();
        var title = form.find('input[name=title]').val();
        var startDay = form.find('input[name=startDay]').val();
        var endDay = form.find('input[name=endDay]').val();
        var startTime = form.find('input[name=startTime]').val();
        var endTime = form.find('input[name=endTime]').val();
        var fruits = [];
        if (title == '') {
            fruits.push("Title");
        }
        if (type == 1) {
            if (startTime == '') {
                fruits.push("Start Time");
            }
            if (endTime == '') {
                fruits.push("end Time");
            }
        }
        if (fruits.length != 0) {
            errormsg = fruits.join("<br> ");
            myAlert('e', 'Please Enter <br>' + errormsg);
            return false;
        }


        if (type == 1) {
            var s = startDay;
            var startDate = moment(s, 'ddd MMM DD YYYY hh:mm:ss [GMT]ZZ').format('YYYY-MM-DD');

            var e = endDay;
            var endDate = moment(e, 'ddd MMM DD YYYY hh:mm:ss [GMT]ZZ').format('YYYY-MM-DD');

            var st = moment(startTime, ["h:mm A"]).format("HH:mm:ss");
            var et = moment(endTime, ["h:mm A"]).format("HH:mm:ss");
            var start = startDate + ' ' + st;
            var end = endDate + ' ' + et;


        }
        else {
            var s = startDay;
            var start = moment(s, 'ddd MMM DD YYYY hh:mm:ss [GMT]ZZ').format('YYYY-MM-DD hh:mm:ss');

            var e = endDay;
            var end = moment(e, 'ddd MMM DD YYYY hh:mm:ss [GMT]ZZ').format('YYYY-MM-DD hh:mm:ss');


        }


        insert_calenderUpdates(title, start, end, type, leaveAccruals)

    }

    function insert_calenderUpdates(title, start, end, type, isWithAccrual) {
        var postData = {title: title, startDate: start, endDate: end, type: type};

        if(isWithAccrual == 'Y'){
            var m = 0;
            var accID_list = [];
            $('.leaveAccruals:checked').each(function(){
                accID_list.push( $(this).val() );
                m++;
            });

            postData['accID_list'] = accID_list;
            postData['postedWithAccrual'] = 'Y';
        }

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/leaveCalender_insert'); ?>",
            data: postData,
            dataType: "json",
            cache: false,
            success: function (data) {
                if(data[0] == 's'){
                    myAlert('s', data[1]);
                    $('#modalEvent, #leaveAccrualConf_modal').modal('hide');
                    $('#title').val('');
                    $('#calendar').fullCalendar('refetchEvents');
                }
                else if(data[0] == 'c'){
                    $('#leaveAccrualConf_data').html(data[1]);
                    $('#leaveAccrualConf_modal').modal('show');
                    $('#leaveAccrualConf_modal_title').html(' &nbsp; - &nbsp; Addition');
                    $('#proceedBtn').attr('onclick', "insertCalender('Y')");
                }
                else if(data[0] == 'e'){
                    myAlert('e', data[1]);
                }
                //data['lastID'];

            }
        });
        return false;

    }

    function deleteCalenderWithAccrual(){
        var postData = $('#holidayAccrual_form').serializeArray();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/delete_eventWithAccrual'); ?>",
            data: postData,
            dataType: "json",
            cache: false,
            success: function (data) {
                myAlert(data[0], data[1]);
                if(data[0] == 's'){
                    $('#leaveAccrualConf_modal').modal('hide');
                    $('#calendar').fullCalendar('refetchEvents');
                }
            }
        });
    }

    function convert(str) {
        var date = new Date(str),
            mnth = ("0" + (date.getMonth() + 1)).slice(-2),
            day = ("0" + date.getDate()).slice(-2);
        hours = ("0" + date.getHours()).slice(-2);
        minutes = ("0" + date.getMinutes()).slice(-2);
        return [date.getFullYear(), mnth, day, hours, minutes].join("-");
    }

    function checkAllLeave(obj){
        if( $(obj).prop('checked') ){
            $('.leaveAccruals').prop('checked', true);
        }else{
            $('.leaveAccruals').prop('checked', false);
        }
    }

    function unCheckLeave(){
        var numItems = $('.leaveAccruals').length;
        var numChecked = $('.leaveAccruals:checked').length;

        $('#checkAllLeave').prop('checked', ( numItems == numChecked ));
    }

    /*
     $(document).ready(function() {

     $('#xcalendar').fullCalendar({
     //restricting available dates to 2 moths in future
     viewRender: function(view,element) {
     startDate = "2017-01-01";
     endDate = "2018-01-01";

     var now = new Date(startDate);
     var end = new Date(endDate);
     /!*end.setMonth(now.getMonth() + 2); //Adjust as needed
     alert(now);*!/


     if ( end <= view.end) {
     $("#calendar .fc-next-button").hide();
     return false;
     }
     else {
     $("#calendar .fc-next-button").show();

     }

     if ( view.start <= now) {
     $("#calendar .fc-prev-button").hide();
     return false;
     }
     else {
     $("#calendar .fc-prev-button").show();

     }
     },
     header: {
     left: 'prev,next today',
     center: 'title',
     right: 'month,agendaWeek,agendaDay,listMonth'
     },
     defaultDate: new Date(),
     navLinks: true, // can click day/week names to navigate views
     businessHours: true, // display business hours
     editable: true,
     events: [
     {
     title: 'Business Lunch',
     start: '2017-12-03T13:00:00',
     constraint: 'businessHours'
     },
     {
     title: 'Meeting',
     start: '2017-12-13T11:00:00',
     constraint: 'availableForMeeting', // defined below
     color: '#257e4a'
     },
     {
     title: 'Conference',
     start: '2017-12-18',
     end: '2017-12-20'
     },
     {
     title: 'Party',
     start: '2017-12-29T20:00:00'
     },

     // areas where "Meeting" must be dropped
     {
     id: 'availableForMeeting',
     start: '2017-12-11T10:00:00',
     end: '2017-12-11T16:00:00',
     rendering: 'background'
     },
     {
     id: 'availableForMeeting',
     start: '2017-12-13T10:00:00',
     end: '2017-12-13T16:00:00',
     rendering: 'background'
     },

     // red areas where no events can be dropped
     {
     start: '2017-12-24',
     end: '2017-12-28',
     overlap: false,
     rendering: 'background',
     color: '#ff9f89'
     },
     {
     start: '2017-12-06',
     end: '2017-12-08',
     overlap: false,
     rendering: 'background',
     color: '#ff9f89'
     }
     ]
     });

     });
     */

</script>
