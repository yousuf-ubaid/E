<?php
$leaveTypeArr = leavetype_bygroup($leaveGroupID);
switch ($policyMasterID) {
    /*month & Annually */

    case 2:
        ?>
        <div class="row">
            <div class="col-xs-6">
                <div class="panel-body" style="padding-bottom: 0;padding-top: 2%;">
                   <!-- <div class="col-xs-5" style="margin-bottom: 5px">
                        <label>Leave Type <?php /*required_mark(); */?></label>
                    </div>
                    <div class="col-xs-7" style="margin-bottom: 5px" id="leaveTypeDropDown">
                        <select name="leaveTypeID" class="form-control frm_input" onchange="getleaveTypeHour(this)"
                                id="leaveTypeID">
                            <option value="">Select a Type</option>
                            <?php /*if (!empty($leaveTypeArr)) {
                                foreach ($leaveTypeArr as $value) {
                                    echo "<option data-isAllowminus='" . $value['isAllowminus'] . "' data-isCalenderDays='" . $value['isCalenderDays'] . "' value='" . $value['leaveTypeID'] . "'>" . $value['description'] . "</option>";
                                }
                            } */?>

                        </select>
                        <?php
/*                        */?>
                    </div>-->




                    <div class="input-daterange input-group" id="datepicker">
                        <div class="col-xs-5" style="margin-bottom: 5px">
                            <label>Start Date <?php required_mark(); ?></label>
                        </div>
                        <div class="col-xs-7" style="margin-bottom: 5px">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="startDatetime" value=""
                                       id="startDatetime" class="form-control dateFields frm_input">
                            </div>
                        </div>
                        <div class="col-xs-5" style="margin-bottom: 5px">
                            <label>End Date <?php required_mark(); ?></label>
                        </div>
                        <div class="col-xs-7" style="margin-bottom: 5px">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="endDatetime" value=""
                                       id="endDatetime" class="form-control dateFields frm_input">

                            </div>
                        </div>
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

                    <div class="col-xs-12 col-sm-12" style="background-color: #f4f4f4">
                        <label>Leave Entitled</label>
                        <span class="pull-right frm_input" id="entitleSpan">-</span>
                    </div>

                    <div class="col-xs-12 col-sm-12">
                        <label>Leave Applied</label>
                        <span class="pull-right frm_input" id="takenSpan">-</span>
                    </div>

                    <div class="col-xs-12 col-sm-12" style="background-color: #f4f4f4">
                        <label>Balance</label> <span style="font-size: 11px" id="baltext"></span>
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
                            <textarea name="comment" class="form-control comment" placeholder="Comment"
                                      style="border-radius: 3px"></textarea>
            </div>
        </div>
        <br>
        <div class="row" id="add_attachemnt_show">

            <div class="col-sm-6" style="margin-left: 50%">
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control" id="leave_attachmentDescription" name="attachmentDescription" placeholder="<?php echo $this->lang->line('common_description');?>..." style="width: 240%;"><!--Description-->
                        <input type="hidden" class="form-control" id="leave_document_name" name="document_name" value="Leave Management">
                    </div>
                </div>
                <div class="col-sm-8" style="margin-top: -8px;">
                    <div class="form-group">
                        <div class="fileinput input-group fileinput-new" data-provides="fileinput" style="margin-top: 8px;">
                            <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file color fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                            <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></span><span class="fileinput-exists"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span></span><input type="file" name="document_file" id="document_file"></span>
                            <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id" data-dismiss="fileinput"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
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
            <div id="confirmedBy" class="col-xs-4" style="padding-bottom: 0;padding-top: 2%;">

            </div>
            <div id="approvedBy" class="col-xs-4" style="padding-bottom: 0;padding-top: 2%;">

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
                        <blockquote><p>Please Contact IT Support Team</p></blockquote>

                    </div>
                </div>
            </div>
        </div>
        <?php

}
?>
<script>
  <?php  if($showYN==1){ ?>
  getLeave_hoursummary(<?php echo $empID ?>, <?php echo $leaveTypeID ?>,<?php echo $policyMasterID;?>);
      <?php } ?>
  policyMasterID=<?php echo $policyMasterID ?>;
  leaveGroupID=<?php echo $leaveGroupID ?>;
    $('#startDatetime').datetimepicker({
        format: 'YYYY-MM-DD hh:mm A'
    });
    $('#endDatetime').datetimepicker({
        format: 'YYYY-MM-DD hh:mm A',
        useCurrent: true
    });
    $("#startDatetime").on("dp.change", function (e) {
        $('#endDatetime').data("DateTimePicker").minDate(e.date);
        validatehourCalender();
    });
    $("#endDatetime").on("dp.change", function (e) {
        $('#startDatetime').data("DateTimePicker").maxDate(e.date);
        validatehourCalender();
    });


    var entitleSpan = $('#entitleSpan');
    var takenSpan =$('#takenSpan');
    var balanceSpan =$('#balanceSpan');
    var no_of_days=$('#no_of_days');
    var workingDaysSpan=$('#workingDaysSpan');
    var leave;
    var isCalenderDays;
    var isAllowminus;
    var empID;
    var leaveTypeID;
    var newLeave_modal = $('#newLeave_modal');


    var balance;
    var appliedLeave;
    var leavebalance;

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



                  //<td class="text-center"><a onclick="delete_attachment('+data[i]['attachmentID']+',\''+data[i]['myFileName']+'\','+data[i]['documentSystemCode']+',\''+data[i]['document_name']+'\',\''+data[i]['documentID']+'\')" ><i class="fa fa-trash-o fa-2x" style="color:rgb(209, 91, 71);" aria-hidden="true"></i></a></td>
                     // }
                  //} else {
                      $('#leave_attachment_modal_body').append('<tr class="danger"><td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td></tr>');
                 // }
                  /*$("#attachment_modal").modal({backdrop: "static", keyboard: true});*/
              },
              error: function (xhr, ajaxOptions, thrownError) {
                  $('#leave_attachment_modal_body').html(xhr.responseText);
              }
          });
      }
  }


    function getleaveTypeHour(obj) {
        leave = $(obj).find('option:selected');
        isAllowminus = leave.attr("data-isAllowminus");
        leaveTypeID = leave.val();
        element = $('#empName').find('option:selected');
        empID = element.val();
        policyMasterID = element.attr("data-policy");
        leaveGroupID = element.attr("date-leaveGroupID");
        if (leaveTypeID == '') {
            /* alerMessage('e', 'Please select a leave Type');*/
            entitleSpan.text('-');
            leavePolicyHourClear();
            return false;
        }
        getLeave_hoursummary(empID, leaveTypeID,policyMasterID)

    }
    function leavePolicyHourClear() {

        takenSpan.text('-');
        balanceSpan.text('-');
        no_of_days.text('-');
        if(isCalenderDays==1){
            $('#workingDaysHide').removeClass('hide');
            $('#baltext').text(' (Leave Entitled - Working Days)');
        }
        else{
            $('#workingDaysHide').addClass('hide');
            $('#baltext').text(' (Leave Entitled - Leave Applied)');
        }
        workingDaysSpan.text('-');
   /*      balance=null;*/
         appliedLeave=null;
         leavebalance=null;

    }
    function getLeave_hoursummary(empID, leaveTypeID,policyMasterID) {
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/employeeLeaveSummery'); ?>',
            data: {empID: empID, leaveType: leaveTypeID,policyMasterID:policyMasterID},
            dataType: 'json',
            beforeSend: function () {
                overlay_show();
            },
            success: function (data) {
                overlay_hide();

                balance=data['balance'];
                entitleSpan.text(display(data['balance']));

                validatehourCalender();



            },
            error: function () {
                overlay_hide();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function validatehourCalender(){

        var startDatetime=$('#startDatetime').val();
        var endDatetime=$('#endDatetime').val();
        if(startDatetime== '' || endDatetime ==''){
            /*if both date not set*/
            takenSpan.text('-');
            balanceSpan.text('-');

            $('#workingDaysHide').addClass('hide');
            $('#baltext').text(' (Leave Entitled - Leave Applied)');


            /*    alerMessage('e','Please select date range.');*/
            return false;
        }

        calculateHourLeaveDays();


    }

    function calculateHourLeaveDays() {
        var startDatetime=$('#startDatetime').val();
        var endDatetime=$('#endDatetime').val();
        /*halfday checked*/

        if(leaveTypeID =='' || $('#leaveTypeID').val()==''){
            $('#startDatetime').val('');
            $('#endDatetime').val('');
            //alerMessage('e','Please select a leave Type');
            leavePolicyHourClear();
            return false;
        }





        var dataObject = {};
        dataObject['leaveTypeID'] = leaveTypeID;
        dataObject['startDate'] = startDatetime;
        dataObject['endDate'] = endDatetime;
        dataObject['isAllowminus'] = isAllowminus;
        dataObject['entitleSpan'] = balance;
        dataObject['policyMasterID'] = policyMasterID;


        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/leaveEmployeeCalculation'); ?>',
            data: dataObject,
            dataType: 'json',
            beforeSend: function () {

            },
            success: function (data) {
                if(data['error']==0){
                    /*if success*/

                  appliedLeave=  data['appliedLeave'];
                  leavebalance=  data['leaveBlance'];


                    takenSpan.text(display(appliedLeave));
                    balanceSpan.text(display(leavebalance));


                        $('#workingDaysHide').addClass('hide');
                        $('#baltext').text(' (Leave Entitled - Leave Applied)');




                }
                if(data['error']==1){
                    /*set blank leave Type*/

                    leavePolicyHourClear();
                    alerMessage('e',data['message']);

                }
                if(data['error']==3){
                    /*maximum leave allowed*/
                    takenSpan.text('-');
                    balanceSpan.text('-');


                    $('#startDatetime').val('');
                    $('#endDatetime').val('');

                        $('#workingDaysHide').addClass('hide');
                        $('#baltext').text(' (Leave Entitled - Leave Applied)');



                    alerMessage('e',data['message']);
                }

            },
            error: function () {

                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });


    }

    function submitform(obj){
        var isSave = $(obj).attr('data-fn');
        var isconfirmed = $(obj).attr('data-value');

        $('#isConfirmed').val(isconfirmed);



        var url = null;
        if( isSave == 'save' ){
            url = "<?php echo site_url('Employee/save_employeesLeave'); ?>";
        }
        else{
            url = "<?php echo site_url('Employee/update_employeesLeave'); ?>";
        }
        proceed_empLeave(url,isconfirmed);
    }

    function proceed_empLeave(url,isconfirmed){
        var formData = new FormData($("#empNewLeave")[0]);


        formData.append('entitleSpan',balance);
        formData.append('appliedLeave',appliedLeave);
        formData.append('leaveBlance',leavebalance);


        formData.append('policyMasterID',policyMasterID);
        formData.append('leaveGroupID',leaveGroupID);
        formData.append('startDate',$('#startDatetime').val());
        formData.append('endDate',$('#endDatetime').val());


   /*     formData.push( {name:'entitleSpan', value:  balance} );
        formData.push( {name:'appliedLeave', value: appliedLeave} );
        formData.push( {name:'leaveBlance', value: leavebalance} );*/
/*
        formData.push( {name:'policyMasterID', value: policyMasterID} );
        formData.push( {name:'leaveGroupID', value: leaveGroupID} );*/
       /* formData.push( {name:'startDate', value: $('#startDatetime').val()} );
        formData.push( {name:'endDate', value: $('#endDatetime').val()} );*/


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
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    newLeave_modal.modal('hide');
                    var masterID = $('#leaveMasterID').val();
                    setTimeout(function(){
                        masterTable.ajax.reload();
                    }, 300);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function display(a){
        if (a >= 0) {
            // Do Something
            var hours = Math.trunc(a/60);
            var minutes = a % 60;

            return hours +"h :"+ minutes+"m";
        }
        else{
            a=Math.abs(a);
            var hours = Math.trunc(a/60);
            var minutes = a % 60;

            return "-"+hours +"h :"+ minutes+"m";
        }


    }

</script>
