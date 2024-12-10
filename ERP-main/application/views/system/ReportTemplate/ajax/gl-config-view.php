<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);

$gl = gl_description_template($templateID, $reqType, $id, $master_data);
?>

<div class="col-sm-5">
    <?php echo form_dropdown('glAutoID_in[]', $gl, '', 'class="form-control"  id="search" multiple="multiple" size="8"' ); ?>
</div>

<div class="col-sm-2">
    <button type="button" id="search_rightAll" class="btn btn-block btn-sm" ><i class="fa fa-forward"></i></button>
    <button type="button" id="search_rightSelected" class="btn btn-block btn-sm"><i class="fa fa-chevron-right"></i></button>
    <button type="button" id="search_leftSelected" class="btn btn-block btn-sm"><i class="fa fa-chevron-left"></i></button>
    <button type="button" id="search_leftAll" class="btn btn-block btn-sm"><i class="fa fa-backward"></i></button>
</div>

<div class="col-sm-5">
    <select name="glAutoID[]" id="search_to" class="form-control" size="8" multiple="multiple"> </select>
</div>


<script>
    $('#search').multiselect({
        search: {
            left: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />', <!--Search-->
            right: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />', <!--Search-->
        },
        afterMoveToLeft: function ($left, $right, $options) {
            $("#search_to option").prop("selected", "selected");
        }
    });
</script>

<?php
