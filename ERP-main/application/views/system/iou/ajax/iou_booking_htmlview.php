<div>
   <input type="hidden" id="bookingDetailsID" name="bookingDetailsID[]" value="<?php echo $bookingDetailsID ?>">
   <input type="hidden" id="bookingMasterID" name="bookingMasterID[]" value="<?php echo $bookingMasterID ?>">
   <input type="hidden" id="documentDetailID" name="documentDetailID[]" value="<?php echo $documentDetailID ?>">
   
   <div class="form-group" id="fieldDev" style="margin-top:10px;">
      <div class="col-sm-4" id="lbl" style="text-align: left;">
         <label for="fieldvalue" class="text-capitalize"><?php echo $fieldName;?></label>
      </div>
      <div class="col-sm-5" id="inpt">
      <?php if($confirmedYN == 1 ){?> 
         <input class="form-control readonly" id="fieldvalue" name="fieldvalue[]" value="<?php echo $requiredFieldValue;?>">
      <?php }else{ ?>
         <input class="form-control" id="fieldvalue" name="fieldvalue[]" value="<?php echo $requiredFieldValue;?>">
      <?php }?>
         <input class="form-control hidden" id="requiredFieldID" name="requiredFieldID[]" value="<?php echo $requiredFieldID;?>">
      </div>                    
   </div>
</div>
