<!--Translation added by Naseek-->

<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

$leaveTypeArr = leavetype_bygroup($leaveGroupID);
switch ($policyMasterID) {
    /*month & Annually */
    case 1:
    case 3:
    $showshift='';
    $halfdaysho='';
        if($shortLV==1){
            $showshift='';
            $halfdaysho='hidden';
        }else{
            $showshift='hidden';
            $halfdaysho='';
        }


?>
    <style>
        .bootBox-btn-margin{
            margin-right: 10px;
        }
    </style>
    <div class="row">
        <div class="col-xs-6">
            <div class="panel-body" style="padding-bottom: 0;padding-top: 2%;">


                <div class="input-daterange input-group" id="datepicker">
                    <div class="col-xs-5" style="margin-bottom: 5px">
                        <label>
                          <?php echo $this->lang->line('hrms_leave_management_start_date'); ?><!--Start Date--> <?php required_mark(); ?></label>
                    </div>
                    <div class="col-xs-7" style="margin-bottom: 5px">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="startDate" value="<?php //echo date('Y-m-d');?>" id="startDate"  autocomplete="off" class="form-control dateFields frm_input">
                        </div>
                    </div>
                    <div class="col-xs-5" style="margin-bottom: 5px">
                        <label>
                          <?php echo $this->lang->line('hrms_leave_management_end_date'); ?><!--End Date--> <?php required_mark(); ?></label>
                    </div>
                    <div class="col-xs-7" style="margin-bottom: 5px">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="endDate" value="<?php //echo date('Y-m-d');?>" id="endDate" autocomplete="off" class="form-control dateFields frm_input">
                        </div>
                    </div>
                </div>
                <div class="col-xs-5" <?php echo $halfdaysho ?> style="margin-bottom: 5px">
                    <label><?php echo $this->lang->line('hrms_leave_management_half_day'); ?><!--Half Day--></label>
                </div>
                <div class="col-xs-7" <?php echo $halfdaysho ?> style="margin-bottom: 5px">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" value="1" id="halfDay" name="halfDay" onclick="show_shieft_field()" class="form-check-input">
                        </label>
                    </div>
                </div>

                <div class="col-xs-5 shiftcls" <?php echo $showshift ?> style="margin-bottom: 5px">
                    <label>Shift</label>
                </div>
                <div class="col-xs-7 shiftcls" <?php echo $showshift ?> style="margin-bottom: 5px">
                    <select id="shift" name="shift" class="form-control" tabindex="-1" aria-hidden="true">
                        <option value="0">Select a shift</option>
                        <option value="1">Morning shift</option>
                        <option value="2">Evening shift</option>
                    </select>
                </div>
                <!--  <div class="col-xs-5" style="margin-bottom: 5px">
                      <label>No. of Days</label>
                  </div>
                  <div class="col-xs-7" style="margin-bottom: 5px">
                      <span style="font-weight: bolder" class=" frm_input" id="days">-</span>
                  </div>-->

            </div>
        </div>

        <div class="col-xs-1 col-sm-1"></div>

        <div class="col-xs-5 col-sm-5">
            <div class="panel-body" style="padding-top: 3%; padding-bottom: 2%">
                <!--  <div class="col-xs-12 col-sm-12">
                      <label>Leave Policy</label>
                      <span class="pull-right frm_input" id="policySpan">-</span>
                  </div>-->
                <?php if($applicationType != 2){ ?>
                <div class="col-xs-12 col-sm-12" style="background-color: #f4f4f4">
                    <label>
                      <?php echo $this->lang->line('hrms_leave_management_leave_available'); ?><!--Leave available--></label>
                    <span class="pull-right frm_input" id="entitleSpan">-</span>
                </div>
                <?php } ?>
                <div class="col-xs-12 col-sm-12">
                    <label>
                      <?php echo $this->lang->line('hrms_leave_management_leave_applied'); ?><!--Leave Applied--></label>
                    <span class="pull-right frm_input" id="takenSpan">-</span>
                </div>

                <?php if($applicationType != 2){ ?>
                <div id="workingDaysHide" class="col-xs-12 col-sm-12 hide">
                    <label>
                      <?php echo $this->lang->line('hrms_leave_management_working_days'); ?><!--Working Days--></label>
                    <span class="pull-right frm_input" id="workingDaysSpan">-</span>
                </div>

                <div class="col-xs-12 col-sm-12" style="background-color: #f4f4f4">
                    <label><?php echo $this->lang->line('hrms_leave_management_balance'); ?><!--Balance--></label>
                    <span style="font-size: 11px" id="baltext"></span>
                    <span class="pull-right frm_input" id="balanceSpan">-</span>
                </div>
                <?php } ?>
                <div class="overlay" style="display: none">
                    <i class="fa fa-refresh fa-spin myOverlay-spin"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
                        <textarea name="comment" class="form-control comment"
                                  placeholder="<?php echo $this->lang->line('common_comment'); ?>"
                                  style="border-radius: 3px"></textarea>
        </div>
    </div>
    <br>
    <div class="row" id="add_attachemnt_show">
      <div class="col-sm-6" style="margin-left: 50%">
          <div class="col-sm-4">
              <div class="form-group">
                  <input type="text" class="form-control" id="leave_attachmentDescription" name="attachmentDescription"
                         placeholder="<?php echo $this->lang->line('common_description');?>..." style="width: 240%;"><!--Description-->
                  <input type="hidden" class="form-control" id="leave_document_name" name="document_name" value="Leave Management">
              </div>
          </div>
          <div class="col-sm-8" style="margin-top: -8px;">
              <div class="form-group">
                  <div class="fileinput input-group fileinput-new" data-provides="fileinput" style="margin-top: 8px;">
                      <div class="form-control" data-trigger="fileinput">
                          <i class="glyphicon glyphicon-file color fileinput-exists"></i>
                          <span class="fileinput-filename"></span>
                      </div>
                      <span class="input-group-addon btn btn-default btn-file">
                          <span class="fileinput-new"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                      </span>
                      <span class="fileinput-exists">
                          <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>
                      </span>
                      <input type="file" name="document_file" id="document_file"></span>
                      <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id" data-dismiss="fileinput">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                      </a>
                  </div>
              </div>
          </div>
      </div>
    </div>

    <div style="width: 50%" class="pull-right">
        <table class="table table-striped table-condensed table-hover" style="">
          <thead>
          <tr>
              <th>#</th>
              <th><?php echo $this->lang->line('common_file_name');?><!--File Name--></th>
              <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
              <th><?php echo $this->lang->line('common_type');?><!--Type--></th>
              <th><?php echo $this->lang->line('common_action');?><!--Action--></th>
          </tr>
          </thead>
          <tbody id="leave_attachment_modal_body" class="no-padding">
          <tr class="danger">
              <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
          </tr>
          </tbody>
        </table>
    </div>

    <div class="row">
        <div id="confirmedBy" class="col-xs-6" style="padding-bottom: 0;padding-top: 2%;"> </div>
        <div id="approvedBy" class="col-xs-6" style="padding-bottom: 0;padding-top: 2%;"></div>
    </div>
    <?php
      break;
    case 2:
    ?>
    <div class="row">
        <div class="col-xs-6">
            <div class="panel-body" style="padding-bottom: 0;padding-top: 2%;">

                <div class="input-daterange input-group" id="datepicker">
                    <div class="col-xs-5" style="margin-bottom: 5px">
                        <label>
                          <?php echo $this->lang->line('hrms_leave_management_start_date'); ?><!--Start Date--> <?php required_mark(); ?></label>
                    </div>
                    <div class="col-xs-7" style="margin-bottom: 5px">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="startDate" autocomplete="off" value="<?php echo date('Y-m-d'); ?>"
                                   id="startDate" class="form-control dateFields frm_input">
                        </div>
                    </div>
                    <div class="col-xs-5" style="margin-bottom: 5px">
                        <label>
                          <?php echo $this->lang->line('hrms_leave_management_end_date'); ?><!--End Date--> <?php required_mark(); ?></label>
                    </div>
                    <div class="col-xs-7" style="margin-bottom: 5px">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="endDate" value="<?php echo date('Y-m-d'); ?>" id="endDate" autocomplete="off" class="form-control dateFields frm_input">
                        </div>
                    </div>
                </div>
                <div class="col-xs-5" <?php echo $halfdaysho ?> style="margin-bottom: 5px">
                    <label><?php echo $this->lang->line('hrms_leave_management_half_day'); ?><!--Half Day--></label>
                </div>
                <div class="col-xs-7" <?php echo $halfdaysho ?> style="margin-bottom: 5px">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" value="1" id="halfDay" name="halfDay" onclick="show_shieft_field()"  class="form-check-input">

                        </label>
                    </div>
                </div>

                <div class="col-xs-5 shiftcls" <?php echo $showshift ?> style="margin-bottom: 5px">
                    <label>Shift</label>
                </div>
                <div class="col-xs-7 shiftcls" <?php echo $showshift ?> style="margin-bottom: 5px">
                    <select id="shift" name="shift" class="form-control" tabindex="-1" aria-hidden="true">
                        <option value="">Select a shift</option>
                        <option value="1">Morning shift</option>
                        <option value="2">Evening shift</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-xs-1 col-sm-1"></div>

        <div class="col-xs-5 col-sm-5">
            <div class="panel-body" style="padding-top: 3%; padding-bottom: 2%">
                <!--  <div class="col-xs-12 col-sm-12">
                      <label>Leave Policy</label>
                      <span class="pull-right frm_input" id="policySpan">-</span>
                  </div>-->
                <div class="col-xs-12 col-sm-12" style="background-color: #f4f4f4">
                    <label>
                      <?php echo $this->lang->line('hrms_leave_management_leave_available'); ?><!--Leave available--></label>
                    <span class="pull-right frm_input" id="entitleSpan">-</span>
                </div>

                <div class="col-xs-12 col-sm-12">
                    <label>
                      <?php echo $this->lang->line('hrms_leave_management_leave_applied'); ?><!--Leave Applied--></label>
                    <span class="pull-right frm_input" id="takenSpan">-</span>
                </div>
                <div id="workingDaysHide" class="col-xs-12 col-sm-12 hide">
                    <label>
                      <?php echo $this->lang->line('hrms_leave_management_working_days'); ?><!--Working Days--></label>
                    <span class="pull-right frm_input" id="workingDaysSpan">-</span>
                </div>

                <div class="col-xs-12 col-sm-12" style="background-color: #f4f4f4">
                    <label><?php echo $this->lang->line('hrms_leave_management_balance'); ?><!--Balance--></label>
                    <span style="font-size: 11px" id="baltext"></span>
                    <span class="pull-right frm_input" id="balanceSpan">-</span>
                </div>
                <div class="overlay" style="display: none">
                    <i class="fa fa-refresh fa-spin myOverlay-spin"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <textarea name="comment" class="form-control comment" placeholder="<?php echo $this->lang->line('common_comment'); ?>"
                  style="border-radius: 3px"></textarea>
        </div>
    </div>


<?php
    break;

    default:
?>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="testimonials">
                <div class="active item">
                    <blockquote><p>
                        <?php echo $this->lang->line('hrms_leave_management_please_select_an_employee_and_leave_type_continue'); ?><!--Please select an employee--></p>
                    </blockquote>

                </div>
            </div>
        </div>
    </div>
<?php
}
?>
<script>
  var policyMasterID;
  var  leaveGroupID;
  <?php if($showYN == 1 && !empty($empID) && !empty($leaveTypeID) && !empty($policyMasterID) ){ ?>
  getLeave_summary(<?php echo $empID ?>, <?php echo $leaveTypeID ?>,<?php echo $policyMasterID;?>);
  <?php } ?>

  $('#applicationType').val( <?php echo ($applicationType == 2)? 2: 1 ?> );


   policyMasterID='<?php echo $policyMasterID ?>';
   leaveGroupID='<?php echo $leaveGroupID ?>';

  $(document).ready(function () {
      toastr.clear();
      $("#startDate").datepicker({
          todayBtn: 1,
          autoclose: true,
          format: "yyyy-mm-dd",
          minViewMode: 0,
          pickerPosition: "bottom-left"
      }).on('changeDate', function (selected) {
         /* var minDate = new Date(selected.date.valueOf());
          $('#endDate').datepicker('setStartDate', minDate);*/
          $(this).datepicker('hide');
          if (document.getElementById('halfDay').checked) {
              halfDay = 1;
              $('#endDate').val($('#startDate').val());
          }

          var applicationType = $('#applicationType').val();

          if(applicationType == 1) { //Validate if the application type is leave
              validateCalender();
          }
      });

      $("#endDate").datepicker({
          format: "yyyy-mm-dd",
          minViewMode: 0,
          pickerPosition: "bottom-left"
      }).on('changeDate', function (selected) {
          /*var minDate = new Date(selected.date.valueOf());
          $('#startDate').datepicker('setEndDate', minDate);*/
          $(this).datepicker('hide');
          if (document.getElementById('halfDay').checked) {
              halfDay = 1;
              $('#startDate').val($('#endDate').val());
          }

          var applicationType = $('#applicationType').val();

          if(applicationType == 1){ //Validate if the application type is leave
              validateCalender();
          }
      });


      $('#halfDay').on('click change', function (e) {
          if ($(this).is(':checked')) {
              var startDate = $('#startDate').val();
              var endDate = $('#endDate').val();
              if (startDate.trim() != endDate.trim() && startDate.trim() != '' && endDate.trim() != '') {
                  alerMessage('e', 'Half day only can take within the day');
                  $('#halfDay').attr("checked", false);
                  $('.shiftcls').hide();
                  return false;
              }
          }
          validateCalender();
      });
  });


  /*  $(function () {
   $('.input-daterange').datepicker({
   format: "yyyy-mm-dd",
   minViewMode: 0
   }).on('changeDate', function(ev){
   validateCalender();
   });



   });*/
  var entitleSpan = $('#entitleSpan');
  var takenSpan = $('#takenSpan');
  var balanceSpan = $('#balanceSpan');
  var no_of_days = $('#no_of_days');
  var workingDaysSpan = $('#workingDaysSpan');
  var leave;
  var isCalenderDays;
  var isAllowminus;
  var empID;
  var leaveTypeID;
  var newLeave_modal = $('#newLeave_modal');

   function Leaveattachment_modal(documentSystemCode, document_name, documentID, confirmedYN) {
       $('#attachmentDescription').val('');
       $('#documentSystemCode').val(documentSystemCode);
       $('#document_name').val(document_name);
       $('#documentID').val(documentID);
       $('#confirmYNadd').val(confirmedYN);
       $('#remove_id').click();
       if (documentSystemCode) {
           $.ajax({
               type: 'POST',
               url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
               dataType: 'json',
               data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
               // beforeSend: function () {
               //     check_session_status();
               //     //startLoad();
               // },
               success: function (data) {

                   $('#leave_attachment_modal_body').empty();
                   $('#leave_attachment_modal_body').append('' +data+ '');

                   /*$("#attachment_modal").modal({backdrop: "static", keyboard: true});*/
               },
               error: function (xhr, ajaxOptions, thrownError) {
                   $('#leave_attachment_modal_body').html(xhr.responseText);
               }
           });
       }
   }

  function leavePolicyClear() {
      entitleSpan.text('-');
      takenSpan.text('-');
      balanceSpan.text('-');
      no_of_days.text('-');
      if (isCalenderDays != 1) {
          $('#workingDaysHide').removeClass('hide');
          $('#workingDaysHide').removeClass('hide');
          $('#baltext').text(' (<?php echo $this->lang->line('hrms_leave_management_leave_entitled');?> -  <?php echo $this->lang->line('hrms_leave_management_working_days');?>)');
      }
      else {
          $('#workingDaysHide').addClass('hide');
          $('#baltext').text(' (<?php echo $this->lang->line('hrms_leave_management_leave_entitled');?> -  <?php echo $this->lang->line('hrms_leave_management_leave_applied');?>)');
      }
      workingDaysSpan.text('-');

  }

/*  function getleaveType(obj) {
      leave = $(obj).find('option:selected');
      isCalenderDays = leave.attr("data-isCalenderDays");
      isAllowminus = leave.attr("data-isAllowminus");
      leaveTypeID = leave.val();
      element = $('#empName').find('option:selected');
      empID = element.val();
      policyMasterID = element.attr("data-policy");
      leaveGroupID = element.attr("date-leaveGroupID");
      if (leaveTypeID == '') {
          /!* alerMessage('e', 'Please select a leave Type');*!/
          leavePolicyClear();
          return false;
      }
      getLeave_summary(empID, leaveTypeID, policyMasterID)

  }*/

  function getLeave_summary(empID, leaveTypeID, policyMasterID, leaveBaance = 2) {

      $.ajax({
          type: 'post',
          url: '<?php echo site_url('Employee/employeeLeaveSummery'); ?>',
          data: {empID: empID, leaveType: leaveTypeID, policyMasterID: policyMasterID},
          dataType: 'json',
          beforeSend: function () {
              overlay_show();
          },
          success: function (data) {
              if(leaveBaance == 2){
                overlay_hide();
                bal = parseFloat(data['balance']);
                if (bal != parseInt(bal)) {
                    bal = bal.toFixed(1);
                }
                entitleSpan.text(bal);
                validateCalender();
             }
            
              overlay_hide();
              //leave reason 
              if(data.reasonApplicableYN == 1){

                $('#leaveReason').empty();

                $('#leaveReasonSection').removeClass('hide');

                $('#leaveReason')
                        .append($("<option></option>")
                                    .attr("value", '')
                                    .text('Select Leave Reason')); 
                
                $.each(data.leaveReason, function(key, value) {   
                    $('#leaveReason')
                        .append($("<option></option>")
                                    .attr("value", value.id)
                                    .text(value.reason)); 
                });

              }else{

                $('#leaveReasonSection').addClass('hide');
              }
              
              if(data.isAnnualLeave == 1){
                $('#leaveAnnualCommentSection').removeClass('hide');
              }else{
                $('#leaveAnnualCommentSection').addClass('hide');
              }



          },
          error: function () {
              overlay_hide();
              myAlert('e', 'An Error Occurred! Please Try Again.');
          }
      });
  }

  function validateCalender() {

      var startDate = $('#startDate').val();
      var endDate = $('#endDate').val();
      if (startDate.trim() == '' || endDate.trim() == '') {
          /*if both date not set*/
          takenSpan.text('-');
          balanceSpan.text('-');
          no_of_days.text('-');
          workingDaysSpan.text('-');
          if (isCalenderDays != 1) {
              $('#workingDaysHide').removeClass('hide');
              $('#baltext').text(' (<?php echo $this->lang->line('hrms_leave_management_leave_entitled');?> -  <?php echo $this->lang->line('hrms_leave_management_working_days');?>)');
          }
          else {
              $('#workingDaysHide').addClass('hide');
              $('#baltext').text(' (<?php echo $this->lang->line('hrms_leave_management_leave_entitled');?> -  <?php echo $this->lang->line('hrms_leave_management_leave_applied');?>)');
          }

          /*    alerMessage('e','Please select date range.');*/
          return false;
      }

      /*halfday checked*/
      if (document.getElementById('halfDay').checked) {
          halfDay = 1;
      } else {
          halfDay = 0;
      }

      /*if half day check date*/


      calculateLeaveDays();

  }

  function calculateLeaveDays() {
      var startDate = $('#startDate').val();
      var endDate = $('#endDate').val();
      var empName = $('#empName').val();
      /*halfday checked*/
      if (document.getElementById('halfDay').checked) {
          halfDay = 1;
      } else {
          halfDay = 0;
      }
      /*leave type id set*/
      if (leaveTypeID == '' || $('#leaveTypeID').val() == '') {
          $('#startDate').val('');
          $('#endDate').val('');
          //alerMessage('e','Please select a leave Type');
          leavePolicyClear();
          return false;
      }

      /*if half day check date*/
      if (halfDay == 1) {
          if (startDate.trim() != endDate.trim() && startDate.trim() != '' && endDate.trim() != '') {
              alerMessage('e', 'Half day only can be taken within the day');
              $('#halfDay').attr("checked", false);
              return false;
          }
      }
      if( <?php echo $shortLV ?>==1){
          if (startDate.trim() != endDate.trim()) {
              alerMessage('e', 'Short leave only can be taken within the day');
              $('#endDate').val($('#startDate').val());
              return false;
          }
      }

      if (halfDay == 1  || <?php echo $shortLV ?>==1) {
          /* if(startDate.trim() != endDate.trim() && startDate.trim() != '' && endDate.trim() !='' ){
           alerMessage('e','Half day only can take within the day');
           $('#halfDay').attr("checked",false);
           return false;
           }*/
          if (halfDay == 1 || <?php echo $shortLV ?>==1) {
              $('#endDate').val($('#startDate').val());
          }
      }

      /*    if(startDate.trim() == '' || endDate.trim() ==''){
       /!*if both date not set*!/
       takenSpan.text('-');
       balanceSpan.text('-');
       no_of_days.text('-');
       workingDaysSpan.text('-');
       $('#workingDaysHide').addClass('hide');
       alerMessage('e','Please select date range.');
       return false;
       }*/

      var applicationType = $('#applicationType').val();
      if(applicationType == 1) {

          var dataObject = {};
          dataObject['leaveTypeID'] = leaveTypeID;
          dataObject['halfDay'] = halfDay;
          dataObject['shortLV'] = <?php echo $shortLV ?>;
          dataObject['startDate'] = startDate;
          dataObject['endDate'] = endDate;
          dataObject['isAllowminus'] = isAllowminus;
          dataObject['isCalenderDays'] = isCalenderDays;
          dataObject['entitleSpan'] = entitleSpan.text();
          dataObject['empName'] = empName;


          $.ajax({
              type: 'post',
              url: '<?php echo site_url('Employee/leaveEmployeeCalculation'); ?>',
              data: dataObject,
              dataType: 'json',
              beforeSend: function () {

              },
              success: function (data) {

                  if (data['error'] == 0) {
                      /*if success*/

                      takenSpan.text(data['appliedLeave']);
                      balanceSpan.text(data['leaveBlance']);

                      if (isCalenderDays != 1) {
                          workingDaysSpan.text(data['workingDays']);
                          $('#workingDaysHide').removeClass('hide');
                          $('#baltext').text(' (<?php echo $this->lang->line('hrms_leave_management_leave_entitled');?> -  <?php echo $this->lang->line('hrms_leave_management_working_days');?>)');
                      }

                      else {
                          $('#workingDaysHide').addClass('hide');
                          $('#baltext').text(' (<?php echo $this->lang->line('hrms_leave_management_leave_entitled');?> -  <?php echo $this->lang->line('hrms_leave_management_leave_applied');?>)');
                      }


                  }
                  if (data['error'] == 1) {
                      /*set blank leave Type*/
                      $('#leaveTypeID').val('').change();
                      $('#startDate').val('');
                      $('#endDate').val('');
                      leavePolicyClear();
                      alerMessage('e', data['message']);

                  }
                  if (data['error'] == 3) {
                      /*maximum leave allowed*/
                      takenSpan.text('-');
                      balanceSpan.text('-');
                      no_of_days.text('-');
                      workingDaysSpan.text('-');

                      $('#startDate').val('');
                      $('#endDate').val('');
                      if (isCalenderDays != 1) {
                          $('#workingDaysHide').removeClass('hide');
                          $('#baltext').text(' (<?php echo $this->lang->line('hrms_leave_management_leave_entitled');?> -  <?php echo $this->lang->line('hrms_leave_management_working_days');?> )');
                          /*Leave Entitled - Working Days*/
                      }
                      else {
                          $('#workingDaysHide').addClass('hide');
                          $('#baltext').text(' (<?php echo $this->lang->line('hrms_leave_management_leave_entitled');?> -  <?php echo $this->lang->line('hrms_leave_management_leave_applied');?>)');
                          /*Leave Entitled - Leave Applied*/
                      }


                      alerMessage('e', data['message']);
                  }

              },
              error: function () {

                  myAlert('e', 'An Error Occurred! Please Try Again.');
              }
          });
      }
  }

  function submitform(obj) {
      var isSave = $(obj).attr('data-fn');
      var isconfirmed = $(obj).attr('data-value');

      $('#isConfirmed').val(isconfirmed);
      $('#coveringValidated').val(0);


      var url = null;
      if (isSave == 'save') {
          url = "<?php echo site_url('Employee/save_employeesLeave'); ?>";
      }
      else {

          url = "<?php echo site_url('Employee/update_employeesLeave'); ?>";
      }
      proceed_empLeave(url, isconfirmed);
  }


  function proceed_empLeave(url, isconfirmed) {
      /*var formData = $('#empNewLeave').serializeArray();*/
      var formData = new FormData($("#empNewLeave")[0]);
      /*   var formData = {};*/
      if ($('#halfDay').is(':checked')) {
          halfDay = 1;
      } else {
          halfDay = 0;
      }

      formData.append('isCalenderDays',isCalenderDays);
      formData.append('entitleSpan',entitleSpan.text());
      formData.append('appliedLeave',takenSpan.text());
      formData.append('leaveBlance',balanceSpan.text());


      formData.append('policyMasterID',policyMasterID);
      formData.append('leaveGroupID',leaveGroupID);
      if (isCalenderDays != 1) {
          formData.append('workingDays',workingDaysSpan.text());
      }
     /* formData.push({name: 'isCalenderDays', value: isCalenderDays});
      formData.push({name: 'entitleSpan', value: entitleSpan.text()});
      formData.push({name: 'appliedLeave', value: takenSpan.text()});
      formData.push({name: 'leaveBlance', value: balanceSpan.text()});

      formData.push({name: 'policyMasterID', value: policyMasterID});
      formData.push({name: 'leaveGroupID', value: leaveGroupID});*/


      $.ajax({
          type: 'post',
          url: url,
          data: formData,
          dataType: 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function () {
              startLoad();
          },
          success: function (data) {
              stopLoad();

              if(data[0] == 'w'){
                  covering_employee_warning(data[1], data[2]);
              }
              else{
                  myAlert(data[0], data[1]);
              }

              if (data[0] == 's') {
                  newLeave_modal.modal('hide');
                  var masterID = $('#leaveMasterID').val();
                  setTimeout(function () {
                      masterTable.ajax.reload();
                      leaveBalance();
                  }, 300);
              }
          },
          error: function () {
              stopLoad();
              myAlert('e', 'An Error Occurred! Please Try Again.');
          }
      });
  }

  function covering_employee_warning(msg, data) {
      bootbox.confirm({
          title: 'Warning!',
          message: '<strong>Are you sure?</strong><br/>'+msg,
          buttons: {
              'cancel': {
                  label: 'Cancel',
                  className: 'btn-default pull-right'
              },
              'confirm': {
                  label: 'OK Proceed',
                  className: 'btn-primary pull-right bootBox-btn-margin'
              }
          },
          callback: function(result) {
              if (result) {
                  if(data.covering == 1){
                      $('#coveringValidated').val(1);
                  }
                  else {
                      $('#coveringAvailabilityValidated').val(1);
                  }

                  var isConfirmed = data.isConfirmed;
                  var url = (data.requestType == 'save')? "<?php echo site_url('Employee/save_employeesLeave'); ?>" : "<?php echo site_url('Employee/update_employeesLeave'); ?>";


                  proceed_empLeave(url, isConfirmed)
              }
          }
      });

  }

    function show_shieft_field(){
        if ($('#halfDay').is(':checked')) {
            $('.shiftcls').show()
        } else {
            $('.shiftcls').hide()
        }
    }
</script>
