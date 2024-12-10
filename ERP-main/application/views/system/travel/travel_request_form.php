<?php
/** Translation added */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$type_arr = array('' => 'Select Type', 'Standard' => 'Standard');
$currency_arr = all_currency_new_drop();
$employeeDrop = leaveApplicationEmployee();
$emp_id = current_userID();
$current_date = current_format_date();
$travelTypes = getTravelTypes();
$emp=travelapplicationemployee();
$emp_id = current_userID();
$employeeDrop = getemployee();
$travelTypes=getTravelTypes();
$countries =load_country_drop();
$cities = load_city_drop();
$currencyCodes = getCurrencyCodes();
$airportDestination=load_airportdestination_drop();
$airportDestination_arr = array(
    '' => 'Select a destination' 
);
foreach($airportDestination as $destination){
    $airportDestination_arr[$destination['destinationID']] = $destination['City'];
}
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

$defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .container {
        max-width: 100%;
        overflow-x: auto;
        margin: 0 auto;
        padding: 20px; 
        background: #fff;
        position: fixed; 
        top: -100%; 
        left: 50%;
        transform: translate(-50%, -50%) scale(0.1);
        transition: transform 1s, top 1s;
        visibility: hidden; 
        z-index: 9999; 
    }
    .modal-xxl {
    max-width: 95%; 
    width: 95%; 
    }

    .select2-container {
        z-index: 9999;
    }

    .container-open {
        transform: translate(-50%, -50%) scale(1);
        visibility: visible;
        top:30% ;
    }
    .body-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); 
        display: none;
        z-index: 9998; 
    }
    .editcontainer {
        width:1200px;
        margin: 0 auto;
        padding: 20px; 
        background: #fff;
        position: fixed; 
        top: -100%; 
        left: 50%;
        transform: translate(-50%, -50%) scale(0.1);
        transition: transform 1s, top 1s;
        visibility: hidden; 
        z-index: 9999; 
    }

    .editcontainer-open {
        transform: translate(-50%, -50%) scale(1);
        visibility: visible;
        top:20% ;
    }
    .edit-overlay{
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); 
        display: none;
        z-index: 9998; 
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

        <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab" id="step1tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('common_travel_request_header'); ?><!--Step 1 - Travel Request--> </span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_travel_request_detail_table();" data-toggle="tab" id="step2tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('common_travel_request_details'); ?><!--Step 2 - Travel Request Detail--></span>
            </a>
           <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3"  data-toggle="tab" id="step3tab" onclick="load_conformation();">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('common_travel_request_confirmation'); ?><!--Step 3 - Travel Request Confirmation--></span>
            </a>
           
        </div>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="travelForm"'); ?>
        
        <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>">
        <input type="hidden" name="company_id" id="company_id" value="<?php echo current_companyID(); ?>">
        <input type="hidden" name="request_by_employee_id" id="request_by_employee_id" value="<?php echo current_userID(); ?>">

        <input type="hidden" name="secondary_code" id="secondary_code">
        <input type="hidden" name="department_id" id="department_id">
        <input type="hidden" name="manager_id" id="manager_id">
        <input type="hidden" name="designation_id" id="designation_id">


        <div class="row">
            
            <div class="col-md-3 form-group">
                <label for="employee"><?php echo $this->lang->line('common_Employeee'); required_mark(); ?>:</label>
               <!-- <select id="empName" name="empName" required onchange="getEmpDet(this)">
                    <option data-leaveGroupID="" data-designation="-" data-policy="" data-ecode="-" value="" disabled selected>Select an employee</option>
                    <?php if ($employeeDrop) {
                       foreach ($employeeDrop as $value) {
                        if (current_userID() == $value['EIdNo']) {
                            echo "<option data-designation='" . $value['DesDescription'] . "' data-ecode='" . $value['ECode'] . "' data-department='" . $value['DepartmentDes'] . "' data-leaveGroupID='" . $value['leaveGroupID'] . "'>" . $value['employee'] . "</option>";
                        }
                    }
                    
                    } ?>
                </select>-->
 
                    <?php echo form_dropdown('empID', $emp, current_userID(), 'class="form-control select2" id="empID" disabled onchange="fetch_employee_detail_travel_by_id(this)"'); ?>
            </div>

            <!-- <div class="col-md-3 form-group">
                <label for="trip_type" required><?php echo $this->lang->line('commom_trip_type'); required_mark(); ?>:</label>
                <select name="trip_type" id="trip_type" class="form-control select2" >
                    <?php foreach ($travelTypes as $travel): ?>
                        <option value="<?php echo $travel['id']; ?>"><?php echo $travel['tripType']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div> -->
        </div>
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="secondary_code"><?php echo $this->lang->line('common_Employeee_secondary_code'); ?>:</label>
                <span id="empCodeSpan"></span>
            </div>

            <div class="col-md-6 form-group">
                <label for="department"><?php echo $this->lang->line('common_department'); ?>:</label>
                <span id="department"></span>
            </div>

        </div>
       

        <div class="row">
            <div class="col-md-6 form-group">
                <label for="reportingmanager"><?php echo $this->lang->line('common_reporting_manager'); ?>:</label>
                <span id="reportingmanager"></span>

            </div>

            <div class="col-md-6 form-group">
                <label for="designation"><?php echo $this->lang->line('common_designation'); ?>:</label>
                <span id="designationSpan"></span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label for="date"><?php echo $this->lang->line('common_date'); ?>:</label>
                <span id="date" class="frm_input"><?php echo $current_date; ?></span>
                <input type="hidden" name="date" id="date_input" value="<?php echo $current_date; ?>">
            </div>
            
        </div>
        <hr>
        <div class="row">
             <div class="col-md-4 form-group">
                <label ><?php echo $this->lang->line('common_trip_request_type'); ?></label>
                <select name="requestTypeID" id="requestTypeID" class="form-control" onchange="hideProject()">
                    <option value="Trip Request">Trip Request</option>    
                    <option value="Travel Request">Travel Request</option>
                </select>
            </div>

            <div class="col-md-4 form-group" id="tripTypeDiv">
                <label for="trip_type" required><?php echo $this->lang->line('commom_trip_type'); required_mark(); ?>:</label>
                <select name="trip_type" id="trip_type" class="form-control select2" aria-label="Default" >
                    <?php foreach ($travelTypes as $travel): ?>
                        <option value="<?php echo $travel['id']; ?>"><?php echo $travel['tripType']; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="text-danger" id="trip-error"></span>
            </div>

            <div class="col-md-4 form-group">
                <label for="classType" required><?php echo $this->lang->line('commom_class_type');  ?>:</label>
                <select name="classType" class="form-control" aria-label="Default" id="classType" >
                    <option value="" Selected>Select</option>
                    <option value="Economic"><?php echo $this->lang->line('commom_economic_class'); ?></option>
                    <option value="Business"><?php echo $this->lang->line('commom_business_lass'); ?></option>
                </select>
            </div>
        </div>

        <div class="row">

            <div class="col-md-4 form-group">
                <label for="mobile number" required><?php echo $this->lang->line('common_overseas_mob'); required_mark(); ?>:</label>
                <input type="text" class="form-control" aria-label="Default" name="mobile_number" id="mobile_number" required style="margin-top:15px;>
                <span class="text-danger" id="mobile-error"></span>
            </div>

            <div class="col-md-4 form-group">
                <label for="subject" required><?php echo $this->lang->line('commom_subject'); required_mark(); ?>:</label>
                <textarea type="text" class="form-control" name="subject" id="subject" required ></textarea>
                <span class="text-danger" id="subject-error"></span>
            </div>

            <div class="col-md-4 form-group">
                <label for="description" required><?php echo $this->lang->line('commom_description'); required_mark(); ?>:</label>
                <textarea type="text" class="form-control" name="description" id="description" required ></textarea>
                  <span class="text-danger" id="description-error"></span><br>
            </div>

        </div>

        <div class="row">

            <div class="col-md-4 form-group">
                <label for="frequant flyer if any" required><?php echo $this->lang->line('commom_flyer_no_if_any'); ?>:</label>
                <input type="text" class="form-control" aria-label="Default" name="flyer_no_if_any" id="flyer_no_if_any" >
            </div>

            <div class="col-md-4 form-group">
                <label for="seat preference" required><?php echo $this->lang->line('commom_seat_preference');  ?>:</label>
                <select name="seat_preference" aria-label="Default" class="form-control" id="seat_preference"  >
                    <option value="" >Select</option>
                    <option value="Window">Window</option>
                    <option value="other">other</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="meal preference" required><?php echo $this->lang->line('commom_meal_preference');  ?>:</label>
                <select name="meal_preference" class="form-control" aria-label="Default" id="meal_preference" >
                    <option value="" >Select</option>
                    <option value="Veg">Veg</option>
                    <option value="Non Veg">Non Veg</option>
                </select>
            </div>
            
        </div>

        <div class="row">
            <div class="col-md-4 form-group">
                <label for="typeOfTravel"><?php echo $this->lang->line('common_type_of_travel') ?></label>
                <select name="typeOfTravel" id="typeOfTravel" class="form-control"  >
                    <option value="" > Select a type</option>
                    <option value="One Way"> One Way</option>
                    <option value="Round trip"> Round trip</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="haveVisa"><?php echo $this->lang->line('common_do_you_have_visa') ?></label>
                <select name="haveVisa" id="haveVisa" class="form-control"  >
                    <option value="" > Select a type</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="bookingType"><?php echo $this->lang->line('common_booking_type') ?></label>
                <select name="bookingType" id="bookingType" class="form-control"  >
                    <option value="" > Select a type</option>
                    <option value="New Booking"><?php echo $this->lang->line('common_new_booking') ?></option>
                    <option value="Others"><?php echo $this->lang->line('common_others_pls') ?></option>
                    <option value="Office Staff"><?php echo $this->lang->line('common_office_staff_simple') ?></option>
                    <option value="Filed Staff"><?php echo $this->lang->line('common_filed_staff_simple') ?></option>
                </select>
            </div>
        </div>

        <div class="row">
            
            <div class="col-md-4 form-group" id="projectTypeDiv">
                <label ><?php echo $this->lang->line('common_project_type'); ?></label>
                <select name="projectType" id="projectType" class="form-control" onchange="getProject()">
                    <option value="">Select</option>
                    <option value="1">Operation</option>
                    <option value="2">Manufacturing</option>
                </select>
            </div>

            <div class="col-md-4 form-group" id="projectIdDiv">
                <label ><?php echo $this->lang->line('common_project'); ?></label>
                <select name="projectID" id="projectID" class="form-control" >
                    <option value="">Select</option>
                </select>
            </div>

            <div class="col-md-4 form-group" id="leaveDiv">
                <label><?php echo $this->lang->line('common_link_leave'); ?></label>
                <select name="leaveID" id="leaveID" class="form-control">
                    <option value="">select</option>
                </select>
            </div>
        </div>

        <!-- <div class="row">
            <div class="col-md-4">
                <label for="description" required><?php echo $this->lang->line('commom_description'); required_mark(); ?>:</label>
                <textarea type="text" class="form-control"class="form control" name="description" id="description" required ></textarea>
                  <span class="text-danger" id="description-error"></span><br>
            </div> 
        </div> -->
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit" name="btnsaverequest" id="btnsaverequest">
                <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next-->
            </button>
        </div>
        </form>
    </div>

    <div id="step2" class="tab-pane">
    <input type="hidden" name="requestid" id="requestid" class="requestid">
        <div class="row">
            <div class="col-md-8">
                <h4><i class="fa fa-hand-o-right"></i>
                    <?php echo $this->lang->line('common_travel_request_details'); ?><!--Travel Request Detail-->
                </h4>
            </div>
            <div class="col-md-4">
                <button type="button" onclick="addrequestdetails()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_add'); ?><!--Add-->
                </button>
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped table-condesed">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_employee'); ?></th>
                    <th style="min-width: 10%" id="detailtblFamName"><?php echo $this->lang->line('common_family_name'); ?></th>
                    <!-- <th style="min-width: 25%" class="text-left"><?php echo $this->lang->line('common_Country'); ?></th> -->
                    <th style="min-width: 5%"  id="detailtblFamDob"><?php echo $this->lang->line('commom_birthdate'); ?></th>
                    <th style="min-width: 5%"><?php echo $this->lang->line('commom_from_destination'); ?></th>
                    <th style="min-width: 5%"><?php echo $this->lang->line('commom_to_destination'); ?></th>
                    <th style="min-width: 5%"><?php echo $this->lang->line('common_start_date'); ?></th>
                    <th style="min-width: 5%"><?php echo $this->lang->line('common_end_date'); ?></th>
                    <th style="min-width: 8%"><?php echo $this->lang->line('common_currency'); ?></th>
                    <!-- <th style="min-width: 8%"><?php echo $this->lang->line('common_amount'); ?></th> -->
                    <!-- <th style="min-width: 8%"><?php echo $this->lang->line('common_type'); ?></th> -->
                    <th style="min-width: 8%"><?php echo $this->lang->line('common_comment'); ?></th>
                    <th style="min-width: 8%"><?php echo $this->lang->line('common_action'); ?></th>
                </tr>
                </thead>
                <tbody id="table_body">
                <tr class="danger">
                    <td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td>
                </tr>
                </tbody>

            </table>
        <br>
        <hr>
        <div class="text-right m-t-xs">
            <!-- <button class="btn btn-default prev" onclick="">Previous</button> -->
            <button class="btn btn-primary next" onclick="load_conformation()"><?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next--></button>
        </div>
    </div>

   <div id="step3" class="tab-pane">
        <div id="conform_body"></div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft'); ?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>


<!-- Request Detail Form-->
<div class="modal fade" id="travelRequestModal" tabindex="-1" role="dialog" aria-labelledby="travelRequestModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xxl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="travelRequestModalLabel"><?php echo $this->lang->line('common_add_travel_request_details'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cancelform()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
            <input type="hidden" name="isTrip" id="isTrip">
              <table class="table table-bordered table-condensed no-color" id="requesttbl">
                <thead>
                  <tr>
                    <th><?php echo $this->lang->line('common_Employeee'); ?> <?php required_mark(); ?></th>
                    <th id="famdetailhead"><?php echo $this->lang->line('common_family_name'); ?></th>
                    <th><?php echo $this->lang->line('commom_trip_start_date'); ?> <?php required_mark(); ?></th>
                    <th><?php echo $this->lang->line('commom_trip_end_date'); ?> <?php required_mark(); ?></th>
                    <th><?php echo $this->lang->line('commom_from_destination'); ?> <?php required_mark(); ?></th>
                    <th><?php echo $this->lang->line('commom_to_destination'); ?> <?php required_mark(); ?></th>
                    <th><?php echo $this->lang->line('commom_currency'); ?> <?php required_mark(); ?></th>
                    <!-- <th><?php echo $this->lang->line('common_type'); ?> <?php required_mark(); ?></th> -->
                    <th><?php echo $this->lang->line('commom_reason'); ?> <?php required_mark(); ?></th>
                    <th style="width: 40px;"> <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i class="fa fa-plus"></i></button> </th>
                  </tr>
                </thead>
                <tbody id="detail_tbl">
                <tr>
                    <input type="hidden" id="employeeid" name="employeeid">
                    <td>
                        <select id="empName" name="empName[]" class=" empName form-control" required disabled>
                            <option value="" disabled selected>Select an employee</option>
                            <?php if ($employeeDrop): ?>
                                <?php foreach ($employeeDrop as $value): ?>
                                    <option value="<?php echo $value['EIdNo']; ?>"><?php echo $value['employee']; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </td>
                    <td id="famdetailselect">
                    <select id="famName" name="famName[]" class=" famName form-control select2 select2-dropdown" >
                        <option value="" disabled selected>Select</option>
                    </select>
                    </td>
                    <td><input type="date" name="start_date[]" class="form-control start_date" required></td>
                    <td><input type="date" name="end_date[]" class="form-control end_date" required></td>
                    <td>
                        <input type="hidden" id="fromDestinationID">
                        <select id="from_destination" name="from_destination[]" class="form-control select2 from_destination select2-dropdown ">
                            <option value="" selected>Select an destination</option>
                            <?php if ($airportDestination): ?>
                                <?php foreach ($airportDestination as $value): ?>
                                    <option value="<?php echo $value['destinationID']; ?>"><?php echo $value['City']; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </td>
                    <td>
                        <?php echo form_dropdown('to_destination', $airportDestination_arr,  '', 'class="form-control select2 to_destination select2-dropdown " id="to_destination[]"'); ?>
                    </td>
                    <td>
                    <select name="currency_code[]" id="currency_code" class="form-control select2  select2-dropdown currency_code" required onchange="updateCurrencyId()">
                        <?php foreach ($currencyCodes as $currency): ?>
                            <option value="<?php echo $currency['CurrencyID']; ?>"><?php echo $currency['CurrencyCode']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    </td>
                    <!-- <td>
                        <select name="roundTrip" id="roundTrip[]" class="form-control select2 roundTrip  select2-dropdown" required >
                            <option value=""   selected> Select a type</option>
                            <option value="One Way"> One Way</option>
                            <option value="Round trip"> Round trip</option>
                        </select>
                    </td> -->
                    <td class="hide"><input type="text" name="travel_advance numeric[]"  onkeypress="return validateFloatKeyPress(this,event)" class="form-control travel_advance"  value="0" placeholder="0.00" required></td>
                    <td><textarea type="text" class="form-control trip_reason" name="trip_reason[]" required></textarea></td>
                    <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="cancelform()">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="validateForm()">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit travel request-->
<div id="Edit">
    <div class="edit-overlay" id="editoverlay">
        <div class="editcontainer" id="editcontainer">
        <input type="hidden" name="detailID" id="detailID" class="detailID">

            <div class="row">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="canceleditform()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><?php echo $this->lang->line('common_edit_travel_request_details'); ?></h4>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered table-condensed no-color" id="requesttbl">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('common_Employeee'); ?> <?php required_mark(); ?></th>
                                <th id="fameNameHeaderEdit"><?php echo $this->lang->line('common_family_name'); ?></th>
                                <th><?php echo $this->lang->line('commom_trip_start_date'); ?> <?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('commom_trip_end_date'); ?> <?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('commom_from_destination'); ?> <?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('commom_to_destination'); ?> <?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('commom_currency'); ?> <?php required_mark(); ?></th>
                                <!-- <th><?php echo $this->lang->line('common_type'); ?> <?php required_mark(); ?></th> -->
                                <!-- <th><?php echo $this->lang->line('commom_travel_advance'); ?> </th> -->
                                <th><?php echo $this->lang->line('commom_reason'); ?> <?php required_mark(); ?></th>
                            </tr>
                        </thead>
                        <tbody id="edit_detail_tbl">
                            <tr>
                            <input type="hidden" id="editemployeeid" name="editemployeeid">
                                <td>
                                    <select id="editname" name="empNameditnamee" class=" editname form-control" required disabled>
                                        <option value="" disabled selected>Select an employee</option>
                                        <?php if ($employeeDrop): ?>
                                            <?php foreach ($employeeDrop as $value): ?>
                                                <option value="<?php echo $value['EIdNo']; ?>"><?php echo $value['employee']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td id="fameNameEdit">
                                <select id="famedit" name="famedit" class="select2 famedit form-control" required>
                                    <option value="" disabled selected>Select</option>
                                </select>
                                </td>
                                <td><input type="date" name="edit_st_date" class="form-control edit_st_date" required></td>
                                <td><input type="date" name="edit_ed_date" class="form-control edit_ed_date" required></td>
                                <td>
                                    <select id="from_destinationEdit" name="from_destinationEdit" class="form-control select2 from_destinationEdit">
                                        <option value="" selected>Select an destination</option>
                                        <?php if ($airportDestination): ?>
                                            <?php foreach ($airportDestination as $value): ?>
                                                <option value="<?php echo $value['destinationID']; ?>"><?php echo $value['City']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <?php echo form_dropdown('airport_destination_edit', $airportDestination_arr,  '', 'class="form-control select2 airport_destination_edit " id="airport_destination_edit"'); ?>
                                </td>
                                <td>
                                <select name="edit_cy_code" id="edit_cy_code" class="form-control  edit_cy_code" required onchange="editupdateCurrencyId()">
                                    <?php foreach ($currencyCodes as $currency): ?>
                                        <option value="<?php echo $currency['CurrencyID']; ?>"><?php echo $currency['CurrencyCode']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                </td >
                                <!-- <td>
                                    <select name="roundTripedit" id="roundTripedit" class="form-control select2 roundTripedit " required >
                                        <option value="" selected> Select a type</option>
                                        <option value="One Way"> One Way</option>
                                        <option value="Round trip"> Round trip</option>
                                    </select>
                                </td> -->
                                <td class="hide"><input type="text" name="edit_advance numeric"   class="form-control edit_advance"  value="0" placeholder="0.00" required></td>

                                <td><textarea type="text" class="form-control edit_reason" name="edit_reason" required></textarea></td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>                               
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="text-right">
                    <button type="button" class="btn btn-primary mr-3" onclick="updatedetail()" id="btnupdatedetail"><?php echo $this->lang->line('common_update'); ?></button>
                        <button type="button" class="btn btn-secondary" id="btncancel" onclick="canceleditform()"><?php echo $this->lang->line('common_cancel'); ?></button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Extra details -->
<div class="modal fade" id="moreDetails" tabindex="-1" role="dialog" aria-labelledby="moreDetails" aria-hidden="true">
   <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moreDetailsLabel"><?php echo $this->lang->line('common_add_more_details'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cancelMoreDetail()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div>
                        <input type="hidden" name="requestDeatilId"  id="requestDeatilId">
                        <div class="row">
                            <div class="col-12" style="margin-bottom:10px;">
                                <h5><?php echo $this->lang->line('common_depature_details') ?></h5>
                                <hr style="margin:2px;">
                            </div>
                        
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="departureDate"><?php echo $this->lang->line('common_departure_date') ?></label>
                                    <input type="date" id="departureDate" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="departureSector"><?php echo $this->lang->line('common_sector') ?></label>
                                    <input type="text" id="departureSector" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="departureTime"><?php echo $this->lang->line('common_time') ?></label>
                                    <select id="departureTime" class="form-control">
                                        <option value=""><?php echo $this->lang->line('common_select_a_option') ?></option>
                                        <option value="Morning">Morning</option>
                                        <option value="Afternoon">Afternoon</option>
                                        <option value="Evening">Evening</option>
                                        <option value="Night">Night</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top:10px;">
                            <div class="col-12"style="margin-bottom:10px;">
                                <h5><?php echo $this->lang->line('common_return_details') ?></h5>
                                <hr style="margin:2px;">
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="returnDate"><?php echo $this->lang->line('common_return_date') ?></label>
                                    <input type="date" id="returnDate" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="returnSector"><?php echo $this->lang->line('common_sector') ?></label>
                                    <input type="text" id="returnSector" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="returnTime"><?php echo $this->lang->line('common_time') ?></label>
                                    <select id="returnTime" class="form-control">
                                        <option value=""><?php echo $this->lang->line('common_select_a_option') ?></option>
                                        <option value="Morning">Morning</option>
                                        <option value="Afternoon">Afternoon</option>
                                        <option value="Evening">Evening</option>
                                        <option value="Night">Night</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="cancelMoreDetail()"><?php echo $this->lang->line('common_cancel') ?></button>
                <button type="button" class="btn btn-primary" onclick="saveMoreDetails()"><?php echo $this->lang->line('common_save') ?></button>
            </div>
        </div>
   </div>
</div>



<script>
    $(document).ready(function () {

        var firstEmp=$('#empID').val();
        $('#empID').val(firstEmp).change();
        
        $('#mobile_number').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        hideProject();

        $('#leaveID').select2();
        $('#empID').select2();
        // $('#trip_type').select2();
        $('#famName').select2();
        $('#trip_type').select2();
        $('#projectID').select2();
        // $('#edit_country').select2();
        $('#famedit').select2();
        $('#currency_code').select2();
        $('.from_destination').select2();
        $('.to_destination').select2();
        //  $('.roundTrip').select2();
        $('#airport_destination_edit').select2();
        $('#edit_cy_code').select2();
        $('.from_destinationEdit').select2();
        // $('#roundTripedit').select2();
        $('#famedit').select2();
        $('#test').select2();

        $('#step2tab').prop('disabled', true)
        $('#step3tab').prop('disabled', true)
        $('#travelForm').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            fields: {
                subject: {
                    validators: {
                        notEmpty: {
                            message: 'The subject is required'
                        }
                    }
                },
                mobile_number: {
                    validators: {
                        notEmpty: {
                            message: 'The mobile number is required'
                        }
                    }
                },
              
                
                description: {
                    validators: {
                        notEmpty: {
                            message: 'The description field is required'
                        }
                    }
                }
            }
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            submitForm();
        });

        $('.headerclose').click(function () {
            fetchPage('system/travel/travel_request.php');
        });
        
        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if (p_id) {
            requestid = p_id;
            load_TR_header();
            fetch_travel_request_detail_table();
            $("#a_link").attr("href", "<?php echo site_url('Employee/load_travel_request_conformation'); ?>/" + requestid);
        } 


    });

    function submitForm() {
        var tripTypeID = $('#trip_type').val();
        var empID = $('#empID').val();
        var date = $('#date_input').val();
        var subject = $('#subject').val().trim();
        var description = $('#description').val().trim();
        var manager_id = $('#manager_id').val();
        var designationID = $('#designation_id').val();
        var request_by_employee_id = $('#request_by_employee_id').val();
        var company_id = $('#company_id').val();
        var flyer_no_if_any = $('#flyer_no_if_any').val();
        var meal_preference = $('#meal_preference').val();
        var seat_preference = $('#seat_preference').val();
        var mobile_number = $('#mobile_number').val();
        var requestid = $('#requestid').val();
        var linkLeave=$('#leaveID').val();
        var requestTypeID=$('#requestTypeID').val();
        var projectType=$('#projectType').val();
        var projectID=$('#projectID').val();
        var classType = $('#classType').val();
        var bookingType=$('#bookingType').val();
        var haveVisa=$('#haveVisa').val();
        var typeOfTravel = $('#typeOfTravel').val();

        var formData = {
            requestid: requestid,
            trip_type: tripTypeID,
            empID: empID,
            date: date,
            subject: subject,
            description: description,
            manager_id: manager_id,
            designationID: designationID,
            request_by_employee_id: request_by_employee_id,
            seatPrefernce:seat_preference,
            mealPreference:meal_preference,
            mobile_number:mobile_number,
            frequantFlyNo:flyer_no_if_any,
            company_id: company_id,
            linkLeave:linkLeave,
            projectType:projectType,
            projectID:projectID,
            requestTypeID:requestTypeID,
            classType:classType,
            haveVisa:haveVisa,
            typeOfTravel:typeOfTravel,
            bookingType:bookingType
        };
        $.ajax({
            method: "POST",
            url: "<?php echo site_url('Employee/saverequest'); ?>",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    var requestID = response.requestID;
                    $('#requestid').val(response.requestID);
                    $('[href=#step2]').tab('show');
                    $('#step1tab').prop('disabled', false);
                    $('#step2tab').prop('disabled', false);
                    $('#step3tab').prop('disabled', false);
                    $('a[data-toggle="tab"]').removeClass('btn-primary');
                    $('a[data-toggle="tab"]').addClass('btn-default');
                    $('[href=#step2]').removeClass('btn-default');
                    $('[href=#step2]').addClass('btn-primary');
                    $('#employeeid').val(empID);
                    $('#empName').val(empID);
                    if(requestTypeID=="Trip Request"){
                        $('#isTrip').val(requestTypeID);
                    }else{
                        $('#isTrip').val('');
                    }
                    blockFam();
                    updateFamilyDropdown();
                    myAlert('s','Travel Request Header created successfully');
                }  else {
                    $('#travelForm').bootstrapValidator('disableSubmitButtons', true); 
                    if (response.subject != '' || response.description != '') {
                        $('#subject-error').text(response.subject);
                        $('#description-error').text(response.description);
                    } else {
                        $('#subject-error').text('');
                        $('#description-error').text('');
                    }

                    if (response.success) {
                        myAlert('s','success')
                    }
                }
                
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
    function updateFamilyDropdown() {
        var empID= $('#employeeid').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'empID': empID},
            url: "<?php echo site_url('Employee/family_dropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                var famname = $('.famName');
                famname.empty();
                famname.append($('<option></option>').attr('value', '').text('Select Family Name'));

                data.forEach(function(fam) {
                    var optionText = fam.name + ' (' + fam.relation + ')'; 
                    var option = $('<option></option>').attr('value', fam.empfamilydetailsID).text(optionText);
                    famname.append(option);
                });
                var fromDestination=data[0].fromDestinationID;
                $('#empName').val(empID).prop('disabled', true);
                $('#fromDestinationID').val(fromDestination);
                $('#from_destination').val(fromDestination).change();
            },
            error: function(xhr, status, error) {
                stopLoad();
                console.error(xhr.responseText);
            }
        });
    }
    function updateFamilyDropdownedit(id) {
        var empID= $('#editemployeeid').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'empID': empID},
            url: "<?php echo site_url('Employee/family_dropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                var famname = $('.famedit');
                famname.empty();
                famname.append($('<option></option>').attr('value', '').text('Select Family Name'));

                data.forEach(function(fam) {
                    var optionText = fam.name + ' (' + fam.relation + ')'; 
                    var option = $('<option></option>').attr('value', fam.empfamilydetailsID).text(optionText);
                    famname.append(option);
                    $('#editname').val(empID).prop('disabled', true);
                });
                $('#famedit').val(id).change();
                
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function getEmpDet(obj) {
        var element = $(obj).find('option:selected');
        var designation = element.attr("data-designation");
        var ecode = element.attr("data-ecode");
        var leaveGroupID = element.attr("data-leaveGroupID");
        var empID = element.val();
        var manager = element.attr("data-manager");
        var DateAssumed = element.attr("data-DateAssumed");
        var department = element.attr("data-department");

        $('#empCodeSpan').html(ecode);
        $('#designationSpan').html(designation);
        $('#reportingManager').html(manager); 
        $('#dateofJoin').html(DateAssumed);
        $('#department').html(department);
        
        loadLeaveTypeDropDown(empID);
    }

    function fetch_employee_detail_travel_by_id(th){
        var id = th.value;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'empID': id},
            url: "<?php echo site_url('Employee/fetch_employee_detail_travel_id'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#empCodeSpan').text(data.ECode);
                $('#department').text(data.DepartmentDes);
                $('#reportingmanager').text(data.managerReporting);
                $('#designationSpan').text(data.DesDescription);

                $('#secondary_code').val(data.ECode);
                $('#department_id').val(data.DepartmentDes);
                $('#manager_id').val(data.managerID);
                $('#designation_id').val(data.EmpDesignationId);
            },
            error: function () {
                stopLoad();
                myAlert("e", "error");
            }
        });
    }

    function getRequestIDFromURL() {
        var urlParams = new URLSearchParams(window.location.search);
        requestID = urlParams.get('requestID');
    }
    
    function validateForm() {
        var empNames = $('.empName').map(function() {
            return $(this).val();
        }).get();
        var startDates = $('.start_date').map(function() {
            return $(this).val();
        }).get();
        var endDates = $('.end_date').map(function() {
            return $(this).val();
        }).get();
        var to_destination = $('.to_destination').map(function() {
            return $(this).val();
        }).get();
        var from_destination = $('.from_destination').map(function() {
            return $(this).val();
        }).get();
        // var cities = $('.city').map(function() {
        //     return $(this).val();
        // }).get();
        var currencyCodes = $('.currency_code').map(function() {
            return $(this).val();
        }).get();
        var travelAdvances = $('.travel_advance').map(function() {
            return $(this).val();
        }).get();
        var tripReasons = $('.trip_reason').map(function() {
            return $(this).val();
        }).get();
        var familyname = $('.famName').map(function() {
            return $(this).val();
        }).get();
        // var roundTrip = $('.roundTrip').map(function() {
        //     return $(this).val();
        // }).get();
        
        var requestID = $('#requestid').val(); 

        var datesAreValid = validateDatePairs(startDates, endDates);
        if (!datesAreValid) {
            myAlert('e', 'Please correct the date pairs. End date cannot be before or the same as start date.');
            return false;
        }

        for (var i = 0; i < startDates.length; i++) {
            if (startDates[i] === "" || endDates[i] === "" || to_destination[i] === "" || currencyCodes[i] === "" || tripReasons[i] === ""||from_destination[i]==="") {
                myAlert('e',"Please fill in all fields.");
                return false;
            }
        }
        // document.getElementById('btnsavedetail').disabled = true;

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url('Employee/saveTravelRequestDetails'); ?>',
            data: {
                requestID: requestID,
                empName: empNames,
                start_date: startDates,
                end_date: endDates,
                from_destination:from_destination,
                to_destination: to_destination,
                currency_code: currencyCodes,
                travel_advance: 0,
                trip_reason: tripReasons,
                familyname:familyname,
                // roundTrip:roundTrip,
                <?php echo $csrf['name']; ?>: '<?php echo $csrf['hash']; ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    fetch_travel_request_detail_table();
                    cancelform();    
                    myAlert('s','Travel Request detail saved successfully');
                } else {
                    // document.getElementById('btnsavedetail').disabled = false;
                    myAlert('e','Can not create request without Family name');
                }
            },
            error: function(xhr, status, error) {
                myAlert('e','Error occurred while saving data: ' + error);
            }
        });

        return true; 
    }

    function validateDatePairs(startDates, endDates) {
        var isValid = true;

        for (var i = 0; i < startDates.length; i++) {
            var startDate = new Date(startDates[i]);
            var endDate = new Date(endDates[i]);

            if (endDate <= startDate) {
                isValid = false;
                break;
            }
        }

        return isValid;
    }


    let popup =document.getElementById("container");

    function addrequestdetails(){
        $('#travelRequestModal').modal({backdrop: 'static',keyboard: false});
        updateFamilyDropdown();
        $('#travelRequestModal').on('shown.bs.modal', function () {
            $('#empName').select2({
                dropdownParent: $('#travelRequestModal')
            });
            $('#famName').select2({
                dropdownParent: $('#travelRequestModal')
            });
            $('#currency_code').select2({
                dropdownParent: $('#travelRequestModal')
            });
            $('.from_destination').select2({
                dropdownParent: $('#travelRequestModal')
            });
            $('.to_destination').select2({
                dropdownParent: $('#travelRequestModal')
            });
            // $('.roundTrip').select2({
            //     dropdownParent: $('#travelRequestModal')
            // });
        });
    }

    $(document).on('click', '.remove-tr', function() {
        $(this).closest('tr').remove();
    });

    function add_more() {
        var empID = $('#employeeid').val();
        var fromDestination=$('#fromDestinationID').val();
        var newRow = $('#detail_tbl tr:first').clone();
        newRow.find('.select2').each(function () {
            $(this).val('').trigger('change'); 
        });
       
        newRow.find('.select2-dropdown').removeClass('select2-hidden-accessible').next('.select2').remove();
        
        newRow.find('.to_destination').val('');
        newRow.find('.from_destination').val(fromDestination);
        newRow.find('.empName').val(empID);
        // newRow.find('.roundTrip').val('');
        newRow.find('.start_date').val('');
        newRow.find('.end_date').val('');
        newRow.find('.travel_advance').val('');
        newRow.find('.trip_reason').val('');
        newRow.find('.city').empty().append($('<option>', {
            value: '',
            text: 'Select a city'
        }));
        newRow.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');

        $('tbody#detail_tbl').append(newRow);

        newRow.find('.select2-dropdown').select2();
        newRow.find('.famName').select2({
            dropdownParent: $('#travelRequestModal')
        });
        newRow.find('.from_destination').select2({
            dropdownParent: $('#travelRequestModal')
        });
        newRow.find('.to_destination').select2({
            dropdownParent: $('#travelRequestModal')
        });
        // newRow.find('.roundTrip').select2({
        //     dropdownParent: $('#travelRequestModal')
        // });
        newRow.find('#currency_code').select2({
            dropdownParent: $('#travelRequestModal')
        });
    }


    function cancelform() {

        $('.to_destination').val('').change();
        $('.from_destination').val('').change();
        // $('.roundTrip').val('').change();
        $('.start_date').val('');
        $('.end_date').val('');
        $('#travelRequestModal').modal('hide');
        // document.getElementById('btnsavedetail').disabled = false;

        var form = $("tbody#detail_tbl");
        var rows = form.find("tr");
        
        for (var i = rows.length - 1; i > 0; i--) {
            rows[i].parentNode.removeChild(rows[i]);
        }

        var firstRowInputs = rows[0].getElementsByTagName("input[type='text']");
        for (var i = 0; i < firstRowInputs.length; i++) {
            firstRowInputs[i].value = "";
        }

        var firstRowSelects = rows[0].querySelectorAll(".select2");
        for (var i = 0; i < firstRowSelects.length; i++) {
            firstRowSelects[i].selectedIndex = 0;
        }

        var firstRowTextarea = rows[0].getElementsByTagName("textarea")[0];
        firstRowTextarea.value = "";
        // updateFamilyDropdown();
    }

    function validateFloatKeyPress(el, evt) {

        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }

        if(number.length>1 && charCode == 46){
            return false;
        }


        var dotPos = el.value.indexOf(".");

        return true;
    }

    function editupdateCurrencyId() {
        var currencyId = $('#edit_cy_code').val();
        $('#edit_cy_code').val(currencyId);
    }

    function updateCurrencyId() {
        var currencyId = $('#currency_code').val();
        $('#currency_id').val(currencyId);
    }


    function fetch_travel_request_detail_table() {
        var requestID = $('#requestid').val(); 
        var isTripRequest=$("#isTrip").val();
        
       
        if (requestID) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: { requestID: requestID },
                url: "<?php echo site_url('Employee/fetch_travel_request_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                   $('#detailID').val(data.detailID);
                   $('#employeeid').val(data.empID);
                    $('#table_body').empty();
                    if (data.detail.length === 0) {
                        $('#table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        $.each(data.detail, function (index, value) {
                            var formattedAmount = parseFloat(value.amount).toFixed(value.transactionCurrencyDecimalPlaces);
                            var famname = value.name ? value.name : '-';
                            // var roundTrip = value.roundTrip ? value.roundTrip : '-';
                            var relation = famname !== '-' ? famname + ' (' + value.relationName + ')' : famname;
                            var fromDestination=value.fromDestinationCity?value.fromDestinationCity:'-';
                            
                            var famDOB =  value.name ? value.DOB : '-';
                            $('#employeeid').val(value.empID);
                           var rowHTML = '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + value.employeeName + '</td>';

                        if (isTripRequest != "Trip Request") {
                            rowHTML += '<td>' + relation + '</td>' +
                                       '<td>' + famDOB + '</td>';
                        }
                        
                        rowHTML += '<td>' + fromDestination + '</td>' +
                                   '<td>' + value.cityName + '</td>' +
                                   '<td>' + value.startDate + '</td>' +
                                   '<td>' + value.endDate + '</td>' +
                                   '<td>' + value.CurrencyCode + '</td>' +
                                //    '<td>' + roundTrip + '</td>' +
                                   '<td>' + value.comments + '</td>' +
                                   '<td>' + 
                                        '<a onclick="addMoreDetails(' + value.detailID + ');">' +
                                            '<span title="Add more details" class="glyphicon glyphicon-info-sign"></span>' +
                                        '</a>' +
                                        '</a> &nbsp;&nbsp; | &nbsp;&nbsp;' +
                                        '<a onclick="edit_request(' + value.detailID + ');">' +
                                            '<span class="glyphicon glyphicon-pencil"></span>' +
                                        '</a> &nbsp;&nbsp; | &nbsp;&nbsp;' +
                                        '<a onclick="delete_item(' + value.detailID + ');">' +
                                            '<span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span>' +
                                        '</a>' +
                                   '</td>' +
                                   '</tr>';
                        
                        $('#table_body').append(rowHTML);
                    });
                        updateFamilyDropdown();
                    }
                    stopLoad();
                },
                error: function () {
                    myAlert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function delete_item(detailID) {
        if (detailID) {
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
                    type: 'post',
                    dataType: 'json',
                    data: { detailID: detailID },
                    url: "<?php echo site_url('Employee/delete_travel_request_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        fetch_travel_request_detail_table();
                        stopLoad();
                        refreshNotifications(true);
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + " Error: " + errorThrown);
                    }
                });
            });
        }
    }

    let editpopup =document.getElementById("editcontainer");
    let editoverlay = document.getElementById("editoverlay");
    
    function edit_request(detailID) {
        var isTripRequest=$("#isTrip").val();
        
        if (detailID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {

                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: { detailID: detailID },
                        url: "<?php echo site_url('Employee/edit_travel_request_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if (data.detail && data.detail.length > 0) {
                                var detail = data.detail[0]; 
                                $('#editemployeeid').val(detail.empID);
                             
                                var empId = detail.employeeName;
                                var fam = detail.relationID;
                                var startDate = detail.startDate;
                                var endDate = detail.endDate;
                                var tripCountry = detail.countryID;
                                var city = detail.destinationID;
                                var CurrencyCode = detail.currencyID;
                                var travelAdvance = detail.amount;
                                var tripreason = detail.comments;
                                var detailID = detail.detailID;
                                // var roundTrip=detail.roundTrip;
                                var fromDestionationID=detail.fromDestionationID;
                                
                                $('#detailID').val(detailID).change();
                                $('#editname').val(empId).change();
                                if(isTripRequest!="Trip Request"){
                                    $('#famedit').val(fam).change();
                                }
                                else{
                                    $('#famedit').val('').change();
                                }
                                
                                $('.edit_st_date').val(startDate);
                                $('.edit_ed_date').val(endDate);
                                $('#from_destinationEdit').val(fromDestionationID).change();
                                $('#airport_destination_edit').val(city).change();
                                $('#edit_cy_code').val(CurrencyCode);
                                $('.edit_advance').val(travelAdvance);
                                $('.edit_reason').val(tripreason);
                                // $('#roundTripedit').val(roundTrip).change();

                                editpopup.classList.add("editcontainer-open");
                                editoverlay.style.display = "block";
                            } else {
                                myAlert('e',"No details found"); 
                            }
                            updateFamilyDropdownedit(fam);
                            stopLoad(); 
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad(); 
                            myAlert('e', "Status: " + textStatus + " Error: " + errorThrown);
                        }
                    });

                });
        }
    }
    function canceleditform() {
        editpopup.classList.remove("editcontainer-open");
        editoverlay.style.display = "none";
        document.getElementById('btnupdatedetail').disabled = false;

    }
   
    function updatedetail() {
      
        var empName = $('.editname').val();
        var startDate = $('.edit_st_date').val();
        var endDate = $('.edit_ed_date').val();
        var from_destinationEdit = $('.from_destinationEdit').val();
        var city = $('.airport_destination_edit').val();
        var currencyCode = $('.edit_cy_code').val();
        var travelAdvance = $('.edit_advance').val();
        var tripReason = $('.edit_reason').val();
        var requestID = $('#requestid').val(); 
        var detailID = $('#detailID').val();
        var famedit = $('#famedit').val();
        // var roundTripedit = $('#roundTripedit').val();

        var startDateTime = new Date(startDate);
        var endDateTime = new Date(endDate);

        if (endDateTime < startDateTime || endDateTime.getTime() === startDateTime.getTime()) {
            myAlert('e', 'Please correct the date pairs. End date cannot be before or the same as start date.');
            return false;
        }

        if (startDate === "" || endDate === "" ||  city === "" || currencyCode === "" || tripReason === "") {
            myAlert('e',"Please fill in all fields.");
            return false;
        }

        document.getElementById('btnupdatedetail').disabled = true;

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url('Employee/updateTravelRequestDetails'); ?>',
            data: {
                detailID: detailID,
                requestID: requestID,
                empName: empName,
                start_date: startDate,
                end_date: endDate,
                from_destinationEdit: from_destinationEdit,
                city: city,
                currency_code: currencyCode,
                travel_advance: travelAdvance,
                trip_reason: tripReason,
                famedit:famedit,
                // roundTripedit:roundTripedit,
                '<?php echo $csrf['name']; ?>': '<?php echo $csrf['hash']; ?>'
            },
            dataType: 'json',
            success: function(response) {
                    canceleditform(); 
                    fetch_travel_request_detail_table(); 
            },
            error: function(xhr, status, error) {
                myAlert('Error occurred while saving data.');
            },
            complete: function() {
                document.getElementById('btnupdatedetail').disabled = false;
            }
        });

        return true; 
    }

    function load_conformation() {
     var requestid = $('#requestid').val(); 
        if (requestid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'requestid': requestid,'html': true},
                url: "<?php echo site_url('Employee/load_travel_request_conformation');?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $('[href=#step3]').tab('show');
                    $('a[data-toggle="tab"]').removeClass('btn-primary');
                    $('a[data-toggle="tab"]').addClass('btn-default');
                    $('[href=#step3]').removeClass('btn-default');
                    $('[href=#step3]').addClass('btn-primary');
                   $('#step1tab').prop('disabled', false);
                    $('#step2tab').prop('disabled', false);
                    $('#step3tab').prop('disabled', false);
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    myAlert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }
    function save_draft() {
        var requestid = $('#requestid').val(); 
        if (requestid) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    fetchPage('system/travel/travel_request', requestid, 'Travel Request');
                });
        }
    }
    function confirmation() {
        var requestid = $('#requestid').val(); 
        if (requestid) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'requestid': requestid},
                        url: "<?php echo site_url('Employee/travel_request_conformation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            if (data[0] == 's') {
                                fetchPage('system/travel/travel_request', requestid, 'Travel Request');
                            }
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
    function load_TR_header() {
        if (requestid) {
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'requestid': requestid},
                    url: "<?php echo site_url('Employee/load_travel_request_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            $('#empID').val(data['empID']).change();
                            $('#trip_type').val(data['tripTypeID']).change();
                            $('#projectType').val(data['projectType']).change();
                            $('#requestTypeID').val(data['requestType']);
                            $('#subject').val(data['subject']);
                            $('#description').val(data['descriptions']);
                            $('#flyer_no_if_any').val(data['frequantFlyNo']);
                            $('#meal_preference').val(data['mealPreference']).change();
                            $('#seat_preference').val(data['seatPrefernce']).change();
                            $('#classType').val(data['classType']).change();
                            $('#haveVisa').val(data['haveVisa']).change();
                            $('#typeOfTravel').val(data['typeOfTravel']).change();
                            $('#bookingType').val(data['bookingType']).change();
                            $('#mobile_number').val(data['loaclMobileNumber']);
                            $('#empName').val(data['empID']).change();
                            $('#requestid').val(data['id']);                 
                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');
                            $('#step2tab').prop('disabled', false);
                            $('#step3tab').prop('disabled', false);
                             $('#employeeid').val(data['empID']);
                            fetch_travel_request_detail_table();
                            updateFamilyDropdown();
                            getProject(data['projectID']);
                            get_Leave_by_emp(data['leaveMasterID']);
                            if(data['requestType']=="Trip Request"){
                                $('#isTrip').val(data['requestType']);
                            }else{
                                $('#isTrip').val('');
                            }
                            blockFam();
                            hideProject();
                        }
                        stopLoad();
                    },
                    error: function () {
                        myAlert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            )
            ;
        }
    }

    get_Leave_by_emp();

    function get_Leave_by_emp(leaveMasterID= null){
        var empID=$('#empID').val();
        $.ajax({
            url:'<?php echo site_url('Employee/getLeaveByEmpID') ?>',
            data:{'id':empID},
            type:'post',
            dataType: 'json',
            beforeSend:function(){
                startLoad();
            },
            success:function(data){
                stopLoad();
                var $select = $('#leaveID');
                $select.empty(); 
                if (data.length > 0) {
                    $select.append('<option value="">select</option>'); 
                    $.each(data, function(index, item) {
                        $select.append('<option value="' + item.leaveMasterID + '">' + item.documentCode + '</option>');
                    });

                    if(leaveMasterID){
                        $select.val(leaveMasterID).change();
                    }
                } else {
                    $select.append('<option value="">No leave types available</option>');
                }
            },
            error:function(){
                stopLoad();
                myAlert('e','Error in fetching leaves');
            }
        });
    }
    
    function getProject(selectedProjectID = null){
        var typeId= $('#projectType').val();

        if(typeId != null && typeId !== ""){
            $.ajax({
                url:'<?php echo site_url('Employee/getProject') ?>',
                type:'post',
                data:{'typeID':typeId},
                dataType: 'json',
                beforeSend:function(){
                    startLoad();
                },
                success:function(data){
                    stopLoad();
                    var $select = $('#projectID');
                    $select.empty(); 
                    if (data.length > 0) {
                        $select.append('<option value="">select</option>'); 
                        $.each(data, function(index, item) {
                            if(typeId==1){
                                $select.append('<option value="' + item.id + '">' + item.job_name + '</option>');
                            }
                            else{
                                $select.append('<option value="' + item.workProcessID + '">' + item.description + '</option>');
                            }
                        });

                        if (selectedProjectID) {
                        $select.val(selectedProjectID).change();
                    }
                    } else {
                        $select.append('<option value="">No  types available</option>');
                    }
                },
                error:function(){
                    stopLoad();
                    myAlert('e','Error in fetching leaves');
                }
            });
        }
        else{
            $('#projectID').val('');
            $('#projectID').empty();
        }
    }

    function hideProject(){
        var type=$('#requestTypeID').val();
        if(type=="Travel Request"){

            $('#projectIdDiv').hide();
            $('#projectTypeDiv').hide();
            $('#tripTypeDiv').hide();

            $('#projectID').val('').change();
            $('#projectType').val('').change();
            $('#trip_type').val('').change();

            $('#leaveDiv').show();

        }else if(type=="Trip Request"){

            $('#projectIdDiv').show();
            $('#tripTypeDiv').show();
            $('#projectTypeDiv').show();

            $('#leaveDiv').hide();

            $('#leaveID').val('').change();

        }
    }

    function blockFam(){
        var isTripRequest=$("#isTrip").val();
        
        if(isTripRequest=="Trip Request"){
            $('#famdetailhead').hide();
            $('#famdetailselect').hide();

            $('#detailtblFamName').hide();
            $('#detailtblFamDob').hide();

            $('#fameNameEdit').hide();
            $('#fameNameHeaderEdit').hide();
            
            $('#famName').val('').change();
            $('#famedit').val('').change();
        }else{
            $('#famdetailselect').show();
            $ ('#famdetailhead').show();
            $('#detailtblFamName').show();
            $('#detailtblFamDob').show();
            $('#fameNameEdit').show();
            $('#fameNameHeaderEdit').show();
        }
        fetch_travel_request_detail_table();
    }

    function cancelMoreDetail(){
        $('#requestDeatilId').val('');
        $('#departureDate').val('');
        $('#departureSector').val('');
        $('#departureTime').val('');
        $('#returnDate').val('');
        $('#returnSector').val('');
        $('#returnTime').val('');
        $('#moreDetails').modal('hide');
    }

    function addMoreDetails(id){
        $.ajax({
            url:'<?php echo site_url('Employee/getTravelRequestMoreDetails') ?>',
            type:'post',
            data:{'id':id},
            dataType: 'json',
            success:function(data){
                $('#moreDetails').modal({backdrop:'static'});
                $('#requestDeatilId').val(id);
                $('#departureDate').val(data['startDate']);
                $('#departureSector').val(data['departureSector']);
                $('#departureTime').val(data['departureTime']).change();
                $('#returnDate').val(data['EndDate']);
                $('#returnSector').val(data['returnSector']);
                $('#returnTime').val(data['returnTime']).change();
            },
            error:function( xhr, status, error){
                console.error('AJAX Error:', xhr.responseText);
                myAlert('e','Error in fetching extra details');
            }

        });   
    }

    function saveMoreDetails(){
        
        var requestDeatilId= $('#requestDeatilId').val();
        var departureDate= $('#departureDate').val();
        var departuresector= $('#departureSector').val();
        var departuretime= $('#departureTime').val();
        var returnDate= $('#returnDate').val();
        var returnsector= $('#returnSector').val();
        var returntime= $('#returnTime').val();
        $.ajax({
         url:'<?php echo site_url('Employee/saveTrMoreDetails')?>',
         type:'post',
         dataType:'json',
         data:{
            requestDeatilId:requestDeatilId,
            departureDate:departureDate,
            departuresector:departuresector,
            departuretime:departuretime,
            returnDate:returnDate,
            returnsector:returnsector,
            returntime:returntime
         },
         success:function(data){
            cancelMoreDetail();
            myAlert('s','saving travel request extra details successful');
         },
         error:function(){
            myAlert('e','Error in saving travel request extra details');
         }
        });
    }
</script>
