<br>
<header class="head-title">
        <h2>HR Planning</h2>
</header>
<div class="row" style="margin-top: 10px;">
    <input type="hidden" id="headerID" name="headerID" value="<?php echo $headerID?>">
    <input type="hidden" id="boq_detailID" name="boq_detailID" value="<?php echo $boq_detailID?>">
    <div class="form-group col-sm-2">
        <label class="title">Designation</label>
    </div>
    <div class="form-group col-sm-4">
    <span class="input-req" title="Required Field">
         <?php echo form_dropdown('DesignationID', getDesignationDrop(true), '', 'class="form-control searchbox" id="DesignationID" onchange="fetch_no_ofheads(this.value);"'); ?>
    <span class="input-req-inner"></span></span>
    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-2">
        <label class="title">No Of Available Heads</label>
    </div>
    <div class="form-group col-sm-4">
        <input type="text" name="noofavailableheads" id="noofavailableheads" class="form-control" readonly>
    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-2">
        <label class="title">No Of Required Heads</label>
    </div>
    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                 <input type="text" name="noofrequiredheads" id="noofrequiredheads" class="form-control" onchange="validate_no_ofheads(this.value)">
                    <span class="input-req-inner"></span>
                </span>
    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-2">
        <label class="title">HR Planning Action</label>
    </div>
    <div class="form-group col-sm-4">
        <span class="input-req" title="Required Field">
     <?php echo form_dropdown('hrplanningtype',array(''=>'Select A Type','1'=>'New Recruitment','2'=>'Shared Manpower','3'=>'Sub-let Workforce','4'=>'Resource Available'), 4, 'class="form-control select2" id="hrplanningtype"'); ?>
        <span class="input-req-inner"></span></span>
    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="col-md-6">
        <div class="text-right m-t-xs">
            <button onclick="save_hr_planning()" type="button" class="btn btn-sm btn-primary">
                Save  <span
                        class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>

        </div>
    </div>
</div>
<br>
<hr>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th class='theadtr'>#</th>
            <th style="width: 26%" class="text-left theadtr">Designation</th>
            <th  class='theadtr'>HR Planning Action</th>
            <th  class='theadtr'>No Of Available Heads</th>
            <th  class='theadtr'>No Of Required Heads</th>
            <th  class='theadtr'>No Of Remaining Heads</th>
            <th  class='theadtr'>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($hrplanningdet)) {
            foreach ($hrplanningdet as $val) { ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-left"><?php echo $val['DesDescription']; ?></td>
                    <td class="text-left"><?php echo $val['hrplanaction']; ?></td>
                    <td class="text-right"><?php echo ($val['availablenoofheads']); ?></td>
                    <td class="text-right"><?php echo ($val['requirednoofheads']) ; ?></td>
                    <td class="text-right"><?php echo (($val['availablenoofheads']-$val['requirednoofheads'])) ; ?></td>
                    <td class="text-right">   <span class="pull-right">&nbsp;&nbsp;<a
                                    onclick="delete_hr_plning(<?php echo $val['activityplanningID'] ?>);"><span
                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                        style="color:rgb(209, 91, 71);"></span></a> </td>
                </tr>
                <?php
                $num++;
            }
        } else {
            echo '<tr class="danger"><td colspan="7" class="text-center">No Records Found</td></tr>';
        } ?>
        <!--No Records Found-->
        </tbody>

    </table>
</div>
<script type="text/javascript">

function fetch_no_ofheads(designationID) {
 var headerID = $('#headerID').val();
 var boq_detailID =$('#boq_detailID').val();
 if(designationID)
 {
     $.ajax(
         {
             async: false,
             type: 'post',
             dataType: 'json',
             data: {'headerID':headerID,'boq_detailID':boq_detailID,'designationID':designationID},
             url: "<?php echo site_url('Boq/fetch_noofheads'); ?>",
             beforeSend: function () {
             },
             success: function (data) {
                 if (!jQuery.isEmptyObject(data)) {
                     $('#noofavailableheads').val((data['empcount']-data['requirednoofheads']));
                     $('#noofrequiredheads').val('');
                 }
             }
         });
 }else
 {
     $('#noofavailableheads').val('');
 }

}
function validate_no_ofheads(noofeq) {

 var noofavailableheads =  $('#noofavailableheads').val();
    if(parseFloat(noofavailableheads) < parseFloat(noofeq))
    {
        myAlert('w','No Of Required Heads greater than No Of Available Heads');
    }
}

</script>