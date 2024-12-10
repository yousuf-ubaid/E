<style>

</style>

<?php
foreach ($widgets as $val) {
    $id= $val['widgetID'];
    if($val['widgetID'] = $val['widget']){
        echo '
          <div class="col-sm-12">
          <label>
              <input type="checkbox" value="' . $id . '" name="widgetCheck[]" class="minimal" checked >
              ' . $val['widgetName'] . '
            </label>
            <input type="hidden" name="isAlreadySelected[]" value="yes|' . $id . '" />
            </div>';
    }
    else{
        echo '
          <div class="col-sm-12">
          <label>
              <input type="checkbox" value="' . $id . '" name="widgetCheck[]" class="minimal" >
              ' . $val['widgetName'] . '
            </label>
            <input type="hidden" name="isAlreadySelected[]" value="no|' . $id . '" />
        </div>';
    }
}

?>
<script>
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });

</script>
