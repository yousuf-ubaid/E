<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);
$this->load->helper('logistics');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$pID = $this->input->post('page_id');
$customer_arr = all_customer_drop(true,1);
$employee = load_employee_drop();
$serviceType_arr = all_logistic_servicetype_drop(true,1);
$staus_arr=all_logistic_status_drop(true,1);

?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"> Step 1 - Job Request Header </a><!--Step 1--><!--Invoice Header-->
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_jobreq_confirmation()"  data-toggle="tab"> Step 2 - Job Request Confirmation</a><!--Step 3--><!--Invoice Confirmation-->
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="job_request_form"'); ?>
        <input type="hidden" id="jobRequestId" name="jobRequestId">
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="customerID">Customer Name <?php required_mark(); ?></label>
                <?php echo form_dropdown('customerID', $customer_arr, '', 'class="form-control select2" id="customerID" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="BLLogisticRefNo">BL/Logistic Reference No <?php required_mark(); ?></label>
                <input type="text" class="form-control " id="BLLogisticRefNo" name="BLLogisticRefNo">
            </div>
            <div class="form-group col-sm-4">
                <label for="ContainerNo">Container No</label>
                <input type="text" class="form-control " id="containerNo" name="containerNo">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="shippingLine"> Shipping Line</label>
                <input type="text" class="form-control " id="shippingLine" name="shippingLine">
           </div>
            <div class="form-group col-sm-4">
                <label for="serviceTypeID">Type of Service<?php required_mark(); ?></label>
                <?php echo form_dropdown('serviceTypeID', $serviceType_arr, '', 'class="form-control " id="serviceTypeID" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="arrivalDate">Arrival Date <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="arrivalDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="arrivalDate" class="form-control arrivaldat" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="bookingNumber">Booking Number<?php required_mark(); ?></label>
                <input type="text" class="form-control " id="bookingNumber" name="bookingNumber">
            </div>
            <div class="form-group col-sm-4">
                <label for="bayanStatusID">Bayan System Status <?php required_mark(); //?></label>
                <?php echo form_dropdown('bayanStatusID',$staus_arr, '', 'class="form-control" id="bayanStatusID" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="encodeByEmpID">Encoded By</label>

                <?php

                echo form_dropdown('encodeByEmpID', $employee, "Select Employee", 'class="form-control select2" id="encodeByEmpID"'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="reminderInDays">Remind in Days </label>
                <input type="number" class="form-control " id="reminderInDays" name="reminderInDays">
            </div>
            <div class="form-group col-sm-4">
                <label for="internalRefNo">House BL / Internal Reference No </label>
                <input type="text" class="form-control " id="internalRefNo" name="internalRefNo">
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button id="save_btn" class="btn btn-primary" type="submit">Add</button>
        </div>
        </form>

    </div>
    <div id="step2" class="tab-pane">
        <div id="logistics_attachment"> </div>
        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-success submitWizard" onclick="job_confirmation()"><?php echo $this->lang->line('common_confirm') ?><!--Confirm--></button>
        </div>
    </div>
    </div>

<!-- <script src="<?php //echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
 -->
<script type="text/javascript">
  var jobID;
  var servID;
  $(document).ready(function () {
      $('.select2').select2();
      $('.headerclose').click(function () {
          fetchPage('system/logistics/job_request', '', 'Job Request');
      });
      jobID = null;
      servID= null;
      number_validation();
      var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
      p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
      if (p_id) {
          jobID =p_id;
          load_job_request_header();
          $('.btn-wizard').removeClass('disabled');
      }else{
          $('.btn-wizard').addClass('disabled');
      }

      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
          $('a[data-toggle="tab"]').removeClass('btn-primary');
          $('a[data-toggle="tab"]').addClass('btn-default');
          $(this).removeClass('btn-default');
          $(this).addClass('btn-primary');
      });

      $('.datepic').datetimepicker({
          useCurrent: false,
          format: date_format_policy,
      });

      $('#job_request_form').bootstrapValidator({
          live: 'enabled',
          message: 'This value is not valid.',
          excluded: [':disabled'],
          fields: {
              customerID : {validators: {notEmpty: {message: 'Customer Name is required.'}}},/*Customer Name is required*/
              BLLogisticRefNo : {validators: {notEmpty: {message: 'Bl/Logistic Reference No is required'}}},/*Bl/Logistic Reference No is required*/
              //containerNo : {validators: {notEmpty: {message: 'Container No is required'}}},/*Container No is required*/
              //shippingLine : {validators: {notEmpty: {message: 'Shipping Line is required'}}},/*Shipping Line is required*/
              serviceTypeID : {validators: {notEmpty: {message: 'Type of Service is required'}}},/*Type of Service is required*/
              arrivalDate : {validators: {notEmpty: {message: 'Arrival Date is required.'}}},/*Arrival Date is required*/
              bookingNumber: {validators: {notEmpty: {message: 'Booking Number is required.'}}},/*Booking Number is required*/
              bayanStatusID: {validators: {notEmpty: {message: 'Bayan System Status is required.'}}},/*Supplier Currency is required*/
              //internalRefNo: {validators: {notEmpty: {message: 'Internal Reference No is required.'}}},/*Internal Reference No is required*/
              },
      }).on('success.form.bv', function (e) {
          e.preventDefault();
          var $form = $(e.target);
          var bv = $form.data('bootstrapValidator');
          var data = $form.serializeArray();
          data.push({'name' : 'jobID', 'value' : jobID });
          //data.push({'name' : 'encodeByEmpID', 'value' : $('#encodeByEmpID option:selected').text()});
          $.ajax(
              {
                  async: false,
                  type: 'post',
                  dataType: 'json',
                  data: data,
                  url: "<?php echo site_url('Logistics/save_job_request'); ?>",
                  beforeSend: function () {
                      startLoad();
                  },
                  success: function (data) {
                      //HoldOn.close();
                      refreshNotifications(true);
                      if (data['status'] == true) {
                          jobID = data['last_id'];
                          servID = $('#serviceTypeID').val();
                          $('[href=#step2]').tab('show');
                          //$("#document").val(null).trigger("change");
                          $('.btn-wizard').removeClass('disabled');
                          $('a[data-toggle="tab"]').removeClass('btn-primary').addClass('btn-default');
                          $('[href=#step2]').removeClass('btn-default').addClass('btn-primary');
                          fetch_jobreq_confirmation();
                         //                          fetch_details(jobID,servID);
                      }
                  },
                  error: function () {
                      alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                      //HoldOn.close();
                      refreshNotifications(true);
                  }
              });
      });
  });

    function load_job_request_header() {
        if (jobID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'jobID': jobID},
                url: "<?php echo site_url('Logistics/load_job_request_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {

                        $('#save_btn').text('Update');/*Update*/
                        jobID = data['jobID'];
                        $('#customerID').val(data['customerID']).change();
                        $('#BLLogisticRefNo').val(data['BLLogisticRefNo']);
                        $('#containerNo').val(data['containerNo']);
                        $('#shippingLine').val(data['shippingLine']);
                        $('#serviceTypeID').val(data['serviceTypeID']).change();
                        $('#bookingNumber').val(data['bookingNumber']);
                        $('#arrivalDate').val(data['arrivalDate']);
                        $('#bookingNumber').val(data['bookingNumber']);
                        $('#bayanStatusID').val(data['bayanStatusID']).change();
                        $('#encodeByEmpID').val(data['encodeByEmpID']).change();
                        $('#reminderInDays').val(data['reminderInDays']);
                        $('#internalRefNo').val(data['internalRefNo']);
                    }
                    stopLoad();
                    //refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

  function tab_active(id) {
      $(".nav-tabs a[href='#tab_" + id + "']").tab('show');
  }

  function fetch_jobreq_confirmation() {
        //alert(jobID);
      $.ajax({
          async: true,
          type: 'post',
          dataType: 'html',
          data: {'jobID': jobID},
          url: '<?php echo site_url("Logistics/job_request_attachment_view"); ?>',
          beforeSend: function () {
              startLoad();
          },
          success: function (data) {
              stopLoad();
              $('#logistics_attachment').html(data);

              fetch_logistic_attachments(jobID);
              stopLoad();

          }, error: function () {
              myAlert('e', 'An Error Occurred! Please Try Again.');
              stopLoad();
          }
      });

  }
  function fetch_logistic_attachments(jobID) {
       // alert(jobID);
      if (jobID) {
          $.ajax({
              type: 'POST',
              url: '<?php echo site_url("Logistics/fetch_logistic_attachments"); ?>',
              dataType: 'json',
              data: {'jobID': jobID},
              success: function (data) {
                  $('#job_request_attachment').empty().append('' +data+ '');

              },
              error: function (xhr, ajaxOptions, thrownError) {
                  $('#ajax_nav_container').html(xhr.responseText);
              }
          });
      }
  }


  function logistic_document_uplode() {
      var formData = new FormData($("#logistic_attachment_form")[0]);
      $.ajax({
          type: 'POST',
          dataType: 'JSON',
          url: "<?php echo site_url('Logistics/attachement_upload'); ?>",
          data: formData,
          contentType: false,
          cache: false,
          processData: false,
      beforeSend: function () {
          startLoad();
      },
      success: function (data) {
          stopLoad();
          myAlert(data['type'], data['message'], 1000);
          if (data['status']) {
              fetch_logistic_attachments(jobID)
          }
      },
      error: function (data) {
          stopLoad();
          swal("Cancelled", "No File Selected :)", "error");
        }
     });
  }
  function delete_job_attachments(id,fileName)
  {
      swal({
              title: "<?php echo $this->lang->line('common_are_you_sure');?>?", /*Are you sure*/
              text: "<?php echo $this->lang->line('common_you_want_to_delete');?>!", /*You want to Delete*/
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "<?php echo $this->lang->line('common_yes');?>!"/*Yes*/
          },
          function () {
              $.ajax({
                  async: true,
                  type: 'post',
                  dataType: 'json',
                  data: {'attachmentID': id, 'myFileName': fileName},
                  url: "<?php echo site_url('Logistics/delete_attachments_AWS_s3_logistics'); ?>",
                  beforeSend: function () {
                      startLoad();
                  },
                  success: function (data) {
                      stopLoad();
                      if (data == true) {
                          myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');
                          fetch_logistic_attachments(jobID)
                      } else {
                          myAlert('e', '<?php echo $this->lang->line('footer_deletion_failed');?>');
                          /*Deletion Failed*/
                      }
                  },
                  error: function () {
                      stopLoad();
                      swal("Cancelled", "Your file is safe :)", "error");
                  }
              });
          });
  }
  function job_confirmation() {
      if (jobID) {
          swal({
                  title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                  text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document!*/
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                  cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
              },
              function () {
                  $.ajax({
                      async: true,
                      type: 'post',
                      dataType: 'json',
                      data: {'jobID': jobID},
                      url: "<?php echo site_url('Logistics/logistics_confirmation'); ?>",
                      beforeSend: function () {
                          startLoad();
                      },
                      success: function (data) {
                          stopLoad();
                          myAlert(data[0], data[1]);
                          if(data[0]=='s')
                          {
                              fetchPage('system/logistics/job_request', '', 'Job Request');
                          }

                      }, error: function () {
                          swal("Cancelled", "Your file is safe :)", "error");
                      }
                  });
              });
      }
  }

</script>