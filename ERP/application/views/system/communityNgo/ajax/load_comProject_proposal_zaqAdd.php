<?php
$this->load->helper('community_ngo_helper');
$fam_ageGroup = fetch_fam_ageGroup();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityNgo', $primaryLanguage);
?>
<!--hidden feild to capture proposal-->
<input type="number" name="proposalID" id="proposalID" value="<?php echo $proposalID; ?>" style="display: none;">
<input type="number" name="EconStateID" id="EconStateID" value="<?php echo $EconStateID; ?>" style="display: none;">
<div class="table-responsive">
    <table id="zaqath_sync" class="table table-striped table-condensed">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 12%"><?php echo $this->lang->line('communityngo_zakat_ageGrp'); ?></abbr></th>
            <th style="min-width: 12%"><?php echo $this->lang->line('communityngo_zakat_ageLimit'); ?></th>
            <th style="min-width: 12%"><?php echo $this->lang->line('communityngo_zakat_points'); ?></th>
            <th style="min-width: 12%"><?php echo $this->lang->line('communityngo_zakat_perAmount'); ?></th>
            <th style="min-width: 5%">&nbsp;
                <button type="button" data-text="Add" onclick="add_zaqthContribution()"
                        class="btn btn-xs btn-primary">
                    <i class="fa fa-plus" aria-hidden="true"></i> Assign Zakat Contribution
                </button>
            </th>
        </tr>
        </thead>
        <tbody>
<?php

$e = 1;
foreach ($fam_ageGroup as $val) {
    ?>

    <tr>
        <td><?php echo $e; ?></td>
        <td><input type="text" class="form-control" value="<?php echo $val['AgeGroup']?>" id="AgeGroup" name="AgeGroup[]"
                   style="width:100%;" disabled><input type="number" name="AgeGroupID[]" id="AgeGroupID" value="<?php echo $val['AgeGroupID']?>" style="display: none;"></td>
        <td><input type="text" class="form-control" value="<?php echo $val['AgeLimit']?>" id="AgeLimit" name="AgeLimit[]" style="width:100%;"
                   disabled></td>
        <td><input type="number" class="form-control GrpPoints" value="" id="GrpPoints" name="GrpPoints[]" onkeyup="cal_totalZaqath(this);" onfocus="this.select();" style="width:100%;"></td>
        <td><input type="text" class="form-control ZakatAmount" value="0" id="ZakatAmount" name="ZakatAmount[]" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="cal_totalZaqath(this);" onfocus="this.select();" style="width:100%;"></td>
        <td><input type="text" class="form-control TotalPerZakat" value="0" id="TotalPerZakat" name="TotalPerZakat[]" onfocus="this.select();" style="width:100%;" readonly></td>

    </tr>
    <?php
    $e++;
}
?>
        </tbody>
    </table>
</div>

<script>

    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }

    function cal_totalZaqath(element) {
        
            var GrpPoints = parseFloat($(element).closest('tr').find('.GrpPoints').val());
            var TotalAmount = parseFloat($(element).closest('tr').find('.ZakatAmount').val());

            if (GrpPoints) {
                $(element).closest('tr').find('.TotalPerZakat').val(GrpPoints * TotalAmount)
            }

    }
</script>
<?php
