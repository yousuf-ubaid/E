<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$jobNumberMandatory = getPolicyValues('JNP', 'All');
$assignBuyersPolicy = getPolicyValues('ABFC', 'All');
echo fetch_account_review(false,true,$approval); 
?>

<div class="table-responsive" style=" max-width: 100%; overflow-x: auto;">
  <table border="1" style=" table-layout: fixed; width: 100%; word-wrap: break-word;border: 1px solid #ccc;">
    <tr>

      <td colspan="3" style="text-align:center; padding: 10px;vertical-align: middle;border: 1px solid #ccc;">
          <img alt="Logo" style="height: 60px; max-width: 100%; "
           src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
         <strong><?php echo $this->common_data['company_data']['company_name'] ?></strong>
          <input type="hidden" name="request_id" value="<?php echo $extra['detail']['id']; ?>" id="requestid">
      </td>

      <td colspan="4" style="text-align:center; padding: 10px;vertical-align: middle;border: 1px solid #ccc;">
          <h3><?php echo $this->lang->line('common_travel_request');?><!--Travel Request--> </h3>
      </td>

      <td colspan="3" style="text-align:center; padding: 10px;vertical-align: middle;border: 1px solid #ccc;">
        <p><b> <?php echo $this->lang->line('common_Div-Country_Initials_S.No_Year'); ?></b></p>
        <p><b><?php echo $this->lang->line('common_reference_no');?>:</b>

          <?php echo $extra['detail']['company_code']; ?>-<?php echo $extra['detail']['countryShortCode']; ?>/<?php echo $extra['detail']['initial']; ?>/<?php echo $extra['detail']['travelRequestCode']; ?>/<?php echo date('Y', strtotime($extra['detail']['requestDate'])); ?>

        </p>
      </td>

      <td colspan="2" style="text-align:center; padding: 10px;vertical-align: middle;border: 1px solid #ccc;">
        <b><?php echo $this->lang->line('common_date'); ?>: </b><?php echo $extra['detail']['requestDate']; ?>
      </td>

    </tr>

    <tr>
      <td colspan="3" style="text-align:center; padding: 10px; vertical-align: middle; border: 1px solid #ccc; border-right:none;">
          <?php $isNewBooking = ($extra['detail']['bookingType'] == 'New Booking');
          if($type == 'html') { ?>
              <input type="checkbox" name="newbooking" id="newbooking" disabled <?php echo $isNewBooking ? 'checked' : ''; ?>>
          <?php } else { 
             echo $isNewBooking ? '☑' : '☐'; 
          } ?>
          <label for=""><?php echo $this->lang->line('common_newbooking'); ?></label>
      </td>

      <td colspan="3" style="text-align:center; padding: 10px; vertical-align: middle; border: 1px solid #ccc; border-right:none; border-left:none;">
          <?php $isOther = ($extra['detail']['bookingType'] == 'Others'); 
          if($type == 'html') { ?>
              <input type="checkbox" name="other" id="other" disabled <?php echo $isOther ? 'checked' : ''; ?>>
          <?php } else { 
            echo $isOther ? '☑' : '☐'; 
          } ?>
          <label for=""><?php echo $this->lang->line('common_OTHERS_pls'); ?></label>
      </td>

      <td colspan="3" style="text-align:center; padding: 10px; vertical-align: middle; border: 1px solid #ccc; border-right:none; border-left:none;">
          <?php $isOfficeStaff = ($extra['detail']['bookingType'] == 'Office Staff');
          if($type == 'html') { ?>
              <input type="checkbox" name="officeStaff" id="officeStaff" disabled <?php echo $isOfficeStaff ? 'checked' : ''; ?>>
          <?php } else { 
            echo $isOfficeStaff ? '☑' : '☐'; 
          } ?>
          <label for=""><?php echo $this->lang->line('common_office_staff'); ?></label>
      </td>

      <td colspan="3" style="text-align:center; padding: 10px; vertical-align: middle; border: 1px solid #ccc; border-left:none;">
          <?php $isFieldStaff = ($extra['detail']['bookingType'] == 'Filed Staff');
          if($type == 'html') { ?>
              <input type="checkbox" name="fieldStaff" id="fieldStaff" disabled <?php echo $isFieldStaff ? 'checked' : ''; ?>>
          <?php } else {
            echo $isFieldStaff ? '☑' : '☐';
          } ?>
          <label for=""><?php echo $this->lang->line('common_filed_staff'); ?></label>
      </td>
    </tr>

    <tr>
      <td class="col-12"  colspan="4" style="text-align:center; padding: 10px;vertical-align: middle;border: 1px solid #ccc;">
        <label for=""><?php echo $this->lang->line('common_focal_person');?>:</label>
        <?php echo $extra['detail']['employeeName']; ?>
      </td>

      <td  colspan="4" style="text-align:center; padding: 10px;vertical-align: middle;border: 1px solid #ccc;">
        <label for=""><?php echo $this->lang->line('common_department');?>:</label>
        <?php echo $extra['detail']['DepartmentDes']; ?>
      </td>

      <td  colspan="4" style="text-align:center; padding: 10px;vertical-align: middle;border: 1px solid #ccc;">
          <label for=""><?php echo $this->lang->line('common_contact');?>:</label>
          <?php echo $extra['detail']['loaclMobileNumber']; ?>
      </td>
      </td>
    </tr>

    <tr style="text-align:center;">
        <td  colspan="12" style="text-align:center; padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><h5><?php echo $this->lang->line('common_traveller_information');?></h5></td>
    </tr>

    <tr>
      <tr  style="text-align:center;">
        <th style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_title');?></th>
        <th style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_last_name');?></th>
        <th style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_emp_first_name');?></th>
        <th style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_middle_name');?></th>
        <th style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_date_of_birth');?></th>
        <th style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_emp_no');?></th>
        <th style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_purpose_of_travel');?></th>
        <th style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_code_div_territory');?></th>
        <th style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_expense_code');?></th>
        <th style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_project_code');?></th>
        <th style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_designation');?></th>
        <th style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_age');?></th>
      </tr>

      <tr  style="text-align:center;">
       <td style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $extra['detail']['empTitle']; ?></td>
        <td style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $extra['detail']['lastname']; ?></td>
        <td style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $extra['detail']['employeeName']; ?></td>
        <td style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $extra['detail']['middeleName']; ?></td>
        <td style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $extra['detail']['EDOB']; ?></td>
        <td style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $extra['detail']['EmpSecondaryCode']; ?></td>
        <td style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $extra['detail']['descriptions']; ?></td>
        <td style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;">code/<?php echo $extra['detail']['company_code']; ?>/<?php echo $extra['detail']['countryShortCode']; ?></td>
        <td style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;">Cost</td>
        <td style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $extra['detail']['job_name']; ?></td>
        <td style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $extra['detail']['DesDescription']; ?></td>
        <td style="font-size:11px;text-align:center;padding: 4px;vertical-align: middle;border: 1px solid #ccc;"><?php 
              $dob = new DateTime($extra['detail']['EDOB']);
              $currentDate = new DateTime();
              $age = $currentDate->diff($dob)->y;
              echo $age;
            ?>
        </td>
      </tr>
    </tr>

    <tr>
      <td colspan="4" style="text-align:center; padding: 10px; font-size:12px; vertical-align: middle; border: 1px solid #ccc; border-right:none;">
        <label for=""><?php echo $this->lang->line('common_type_of_travel'); ?> : </label>
        <?php 
        $isOneWay = ($extra['detail']['typeOfTravel'] == "One Way");
        $isRoundTrip = ($extra['detail']['typeOfTravel'] == "Round trip");
        if($type == 'html') { ?>
            <input type="checkbox" name="oneWay" id="oneWay" style="margin-left:5px;" disabled <?php echo $isOneWay ? 'checked' : ''; ?>>
        <?php } else { 
            echo $isOneWay ? '☑' : '☐';
        } ?>
        <label for=""><?php echo $this->lang->line('common_one_way'); ?></label>

        <?php if($type == 'html') { ?>
            <input type="checkbox" name="returnWay" id="returnWay" style="margin-left:5px;" disabled <?php echo $isRoundTrip ? 'checked' : ''; ?>>
        <?php } else { 
            echo $isRoundTrip ? '☑' : '☐'; 
        } ?>
        <label for=""><?php echo $this->lang->line('common_return'); ?></label>
      </td>

      <td colspan="3" style="text-align:center; padding: 10px; font-size:12px; vertical-align: middle; border: 1px solid #ccc; border-left:none; border-right:none;">
        <label for=""><?php echo $this->lang->line('common_class'); ?> :</label>
        <?php 
        $isEconomy = ($extra['detail']['classType'] == 'Economic');
        $isBusiness = ($extra['detail']['classType'] == 'Business');

        if($type == 'html') {?>
            <input type="checkbox" name="economy" id="economy" style="margin-left:5px;"  <?php echo $isEconomy ? 'checked' : ''; ?> disabled>
        <?php } else {
            echo $isEconomy ? '☑' : '☐';
        }
        ?>
        <label for=""><?php echo $this->lang->line('common_economy'); ?></label>

        <?php 
        if($type == 'html') {?>
            <input type="checkbox" name="economy" id="economy" style="margin-left:5px;"  <?php echo $isBusiness ? 'checked' : ''; ?> disabled>
        <?php } else {
            echo $isBusiness ? '☑' : '☐';
        }
        ?>
        <label for=""><?php echo $this->lang->line('common_business'); ?></label>
      </td>

      <td colspan="5" style="text-align:center; padding: 10px; font-size:12px; vertical-align: middle; border: 1px solid #ccc; border-left:none;">
          <label for=""><?php echo $this->lang->line('common_business_trip_approved'); ?> :</label>

          <input type="checkbox" name="businessTripYes" id="businessTripYes" style="margin-left:5px;" disabled>
          <label for=""><?php echo $this->lang->line('common_yes'); ?></label>

          <input type="checkbox" name="businessTripNo" id="businessTripNo" style="margin-left:5px;" disabled>
          <label for=""><?php echo $this->lang->line('common_no'); ?></label>

          <input type="checkbox" name="businessTripNA" id="businessTripNA" style="margin-left:5px;" disabled>
          <label for=""><?php echo $this->lang->line('common_Na'); ?></label>
      </td>
    </tr>

    <tr>
       <td colspan="2" style="text-align:right;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-right: none;">
          <label for=" Approval Type"><?php echo $this->lang->line('common_approval_Type'); ?> : </label>
        </td>

        <td colspan="2" style="text-align:left;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-right: none;border-left: none;">
            <input type="checkbox" name="leaveApprove" id="leaveApprove" style="margin-left:5px;" disabled>
            <?php echo $this->lang->line('common_leave_approve'); ?>  
        </td>

        <td colspan="2" style="text-align:left;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-right: none;border-left: none;">
            <input type="checkbox" name="tripApprove" id="tripApprove" style="margin-left:5px;" disabled>
            <?php echo $this->lang->line('common_trip_approve'); ?>
        </td>

        <td colspan="2" style="text-align:left;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-left: none; border-right: none;">
            <input type="checkbox" name="familyTravel" id="familyTravel" style="margin-left:5px;" disabled>
            <?php echo $this->lang->line('common_family_travel'); ?> 
        </td>
        <td colspan="4" style="text-align:left;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-left: none; "></td>
    </tr>

    <tr>
      <td colspan="12"  style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;">
        <h5><?php echo $this->lang->line('common_itinerary_required'); ?></h5>
      </td>
    </tr>

    <tr>
      <tr >
        <th colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_departure_date'); ?></th>
        <th colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_sector'); ?></th>
        <th colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_time'); ?></th>
        <th colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_return_date'); ?></th>
        <th colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_sector'); ?></th>
        <th colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_time'); ?></th>
      </tr>
      <?php   
         foreach ($extra['details'] as $detail):?>
        <tr style="text-align:center;">

        <td colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $detail['startDate']?$detail['startDate']:'-'; ?></td>
        <td colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $detail['departureSector']?$detail['departureSector']:'-'; ?></td>
        <td colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $detail['departureTime']?$detail['departureTime']:'-'; ?></td>
        <td colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $detail['endDate']?$detail['endDate']:'-'; ?></td>
        <td colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $detail['returnSector']?$detail['returnSector']:'-'; ?></td>
        <td colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $detail['returnTime']?$detail['returnTime']:'-'; ?></td>
      </tr>
      <?php
      endforeach;
      ?>
    </tr>

    <tr>
      <td colspan="3" style="text-align:left;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-right: none;">
        <label for="">
          <?php echo $this->lang->line('commom_local_mobile'); ?> : <?php echo $extra['detail']['EpMobile']; ?>
        </label>
      </td>

      <td colspan="4" style="text-align:left;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-right: none;border-left: none;">
        <label for="">
          <?php echo $this->lang->line('common_overseas_mob'); ?> : <?php echo $extra['detail']['loaclMobileNumber']; ?>
        </label>
      </td>

      <td colspan="3" style="text-align:left;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-right: none;border-left: none;">
        <label for="">
          <?php echo $this->lang->line('commom_seat_preference'); ?> :<?php echo $extra['detail']['seatPrefernce']; ?>
        </label>
      </td>

      <td colspan="2" style="text-align:left;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-left: none;">
        <label for="">
          <?php echo $this->lang->line('commom_meal_preference'); ?> : <?php echo $extra['detail']['mealPreference']; ?>
        </label>
      </td>
    </tr>

    <tr>
      <td colspan="6" style="text-align:center;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-right: none;">
        <label for=""><?php echo $this->lang->line('common_do_you_have_travel_visa'); ?> :</label>

        <?php 
          $haveVisa=($extra['detail']['haveVisa']=='Yes');
          $noVisa=($extra['detail']['haveVisa']=='No');

          if($type=='html'){?>
            <input type="checkbox" name="visaYes" id="visaYes" <?php echo $haveVisa?'checked':''; ?> disabled>
          <?php } else{
            echo $haveVisa? '☑' : '☐';
          } ?>
          <label for="" style="margin-right:10px;"><?php echo $this->lang->line('common_yes'); ?></label>

          <?php if($type=='html'){?>
            <input type="checkbox" name="visaNo" id="visaNo" <?php echo $noVisa?'checked':''; ?> disabled>
          <?php } else{
              echo $noVisa? '☑' : '☐';
          } ?>
        <label for=""><?php echo $this->lang->line('common_no'); ?></label>
      </td>

      <td colspan="6" style="text-align:center;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-left: none;">
        <label for=""><?php echo $this->lang->line('commom_flyer_no_if_any'); ?>:</label>
        <?php echo $extra['detail']['frequantFlyNo']; ?>
      </td>
    </tr>

    <tr>
      <td colspan="12" style="background-color:#e4e4e4; text-align:center; border: 1px solid #ccc;">
        <h5 style="color:#0c0194"><?php echo $this->lang->line('commom_LPO_to_be_filled'); ?></h5>
      </td>
    </tr>

    <tr style="background-color:#e4e4e4; text-align:center; color:#0c0194">
      <td colspan="6" style="text-align:center;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-right: none;">
        <label for=""><?php echo $this->lang->line('commom_LPO_no') ?> : -</label>
      </td>
      <td colspan="6" style="text-align:center;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-left: none;">
        <label for=""><?php echo $this->lang->line('commom_currency') ?> : -</label>
      </td>
    </tr>


    <tr>
      <tr style="background-color:#e4e4e4; color:#0c0194">
        <th style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('commom_airline') ?></th>
        <th style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_sector') ?></th>
        <th style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_travel_date') ?></th>
        <th colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_ticket_no') ?></th>
        <th style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_base_fare') ?></th>
        <th style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_taxes') ?></th>
        <th colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_currency_total') ?></th>
        <th  colspan="3" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_remarks') ?>: <?php echo $this->lang->line('common_to_be_filled_by_MSE') ?></th>
      </tr>
      <tr style="background-color:#e4e4e4; text-align:center; color:#0c0194">
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;">-</td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;">-</td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;">-</td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;" colspan="2">-</td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;">-</td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;">-</td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;" colspan="2">-</td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;" colspan="3" >-</td>
      </tr>
    </tr>

    <tr style="background-color:#e4e4e4; color:#0c0194">
      <td colspan="6" style="text-align:center;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-right: none;">
        <label for=""><?php echo $this->lang->line('common_total_amount') ?> : -</label>
      </td>

      <td colspan="6" style="text-align:left;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-left: none;">
        <label for=""><?php echo $this->lang->line('common_total_in_words') ?> : -</label>
      </td>
    </tr>

    <tr>
      <td colspan="6" style="text-align:center;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-right: none;">
        <label for=""><?php echo $this->lang->line('common_approved_details') ?> :</label>
      </td>

      <td colspan="6" style="text-align:center;padding: 10px;vertical-align: middle; border: 1px solid #ccc; border-left: none;">
          <label for=""><?php echo $this->lang->line('common_po_number') ?> :<?php echo $extra['detail']['purchaseOrderCode']; ?></label>
      </td>
    </tr>

    <tr>
      <tr>
        <th style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_levels') ?></th>
        <th colspan="2" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_employee') ?></th>
        <th colspan="3" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_name') ?></th>
        <th style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_status') ?></th>
        <th colspan="3" style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_comments') ?></th>
        <th style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_date') ?></th>
        <th style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $this->lang->line('common_time') ?></th>
      </tr>
      
        <?php 
        if(!empty($pendingApproval['approved'])){
         foreach ($pendingApproval['approved'] as $detail):
         $status= $detail['approvedYN']==1?'Approved':'Not Approved';?>
      <tr>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $detail['approvalLevelID']?$detail['approvalLevelID'] :'-' ?></td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;" colspan="2"><?php echo $detail['ECode']?$detail['ECode'] :'-' ?></td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;" colspan="3"><?php echo $detail['Ename2']?$detail['Ename2']:'-' ?></td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;" ><?php echo $status ?></td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;" colspan="3"><?php echo $detail['approvedComments']?$detail['approvedComments']:'-' ?></td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"><?php echo $detail['approveDate']?$detail['approveDate']:'-' ?></td>
        <td style="text-align:center;padding: 10px;vertical-align: middle;border: 1px solid #ccc;"> <?php echo $detail['approveTime']?$detail['approveTime']:'-' ; ?></td>
      </tr>
      
      <?php 
      endforeach; }
      else{?>
        <tr>
          <td colspan="12" style="height:25px; border: 1px solid #ccc;">No records Found</td>
        </tr>
      <?php } ?>
    <tr>

    <tr>
      <td colspan="12" style="height:25px; border: 1px solid #ccc;"></td>
    </tr>
  </table>
</div>

<div class="table-responsive">
  <?php if ($extra['detail']['approvedYN']) { ?>
    <table style="width: 100%">
      <tbody>
        <tr>
          <td style="width:28%;"><strong><?php echo $this->lang->line('common_electronically_approved_by');?><!--Electronically Approved By--> </strong></td>
          <td style="width:2%;"><strong>:</strong></td>
          <td style="width:70%;"><?php echo $extra['detail']['approvedbyEmpName']; ?></td>
        </tr>
        <tr>
          <td><strong><?php echo $this->lang->line('common_electronically_approved_date');?><!--Electronically Approved Date--> </strong></td>
          <td><strong>:</strong></td>
          <td><?php echo $extra['detail']['approvedDate']; ?></td>
        </tr>
      </tbody>
    </table>
  <?php } ?>
</div>

<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Employee/load_travel_request_conformation'); ?>/<?php echo $extra['detail']['id'] ?>";
    $("#a_link").attr("href", a_link);
</script>

