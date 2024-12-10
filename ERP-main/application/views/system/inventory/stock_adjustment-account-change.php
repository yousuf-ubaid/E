<form role="form" id="stock_adjustment_gl_form" class="form-horizontal">
    <input type="hidden" id="detailID" name="detailID">

    <div class="form-group"><label for="inputEmail3" class="col-sm-3 control-label">Cost GL Account</label>
        <div class="col-sm-6"> <?php echo form_dropdown('PLGLAutoID', $costGL, $PLGLAutoID,
            'class="form-control select2" id="PLGLAutoID" required') ?>
        </div>
    </div>
  <?php
    $readonly = 'disabled';
    if ($masterAccountYN == 0) {
      $readonly = '';
    }
  ?>
<!--    <div class="form-group"><label for="inputEmail3" class="col-sm-3 control-label">Asset GL Account</label>
        <div class="col-sm-6"> <?php /*echo form_dropdown('BLGLAutoID', $costGL, $BLGLAutoID,
            ' ' . $readonly . ' class="form-control select2" id="BLGLAutoID" required') */?>
        </div>
    </div>-->

</form>
<script>
    $('.select2').select2();

</script>



