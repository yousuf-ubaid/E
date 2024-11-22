<?php
/*var_dump($visittasktypes);*/
if($visittasktypes){
?>

    <div class="row" style="margin-top: 10px;">
        <?php foreach ($visittasktypes as $tasks){
            if(empty($farmerVisitID)){
                $tasks['value'] = 0;
            }?>
            <div class="col-sm-6">
                <div class="form-group col-sm-5">
                    <label class="quickmanagement"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;<?php echo $tasks['description']?></label>
                </div>
                <div class="form-group col-sm-7 col-xs-12">
                    <div class="skin-section extraColumns">
                        <label class="radio-inline">
                            <div class="skin-section extraColumnsgreen">
                                <input id="taskOne<?php echo $tasks['visitTaskTypeID'];?>" type="radio" data-caption="" class="columnSelected"
                                       name="task<?php echo $tasks['visitTaskTypeID'];?>" value="1" <?php if ( $tasks['value'] == 1) {echo 'checked';}?>>
                                <label for="checkbox">&nbsp;&nbsp;A</label></div>
                        </label>
                        <label class="radio-inline">
                            <div class="skin-section extraColumnsyellow">
                                <input id="taskTwo<?php echo $tasks['visitTaskTypeID'];?>" type="radio" data-caption="" class="columnSelected"
                                       name="task<?php echo $tasks['visitTaskTypeID'];?>" value="2" <?php if ( $tasks['value'] == 2) {echo 'checked';}?>>
                                <label for="checkbox">&nbsp;&nbsp;B</label></div>
                        </label>
                        <label class="radio-inline">
                            <div class="skin-section extraColumnsred">
                                <input id="taskThree<?php echo $tasks['visitTaskTypeID'];?>" type="radio" data-caption="" class="columnSelected"
                                       name="task<?php echo $tasks['visitTaskTypeID'];?>" value="3" <?php if ( $tasks['value'] == 3) {echo 'checked';}?>>
                                <label for="checkbox">&nbsp;&nbsp;C</label></div>
                        </label>
                    </div>
                </div>
            </div>
        <?php  }?>
    </div>
<?php } else {
    echo '<div class="search-no-results" style="margin-left: 15%; margin-right: 15%">No Tasks To Display</div>';
} ?>
<script>
    /* Add colours To Radio Button(Green,Yellow,Red)*/
    $('.extraColumnsgreen input').iCheck({
        checkboxClass: 'icheckbox_square_relative-green',
        radioClass: 'iradio_square_relative-green',
        increaseArea: '20%'
    });
    $('.extraColumnsyellow input').iCheck({
        checkboxClass: 'icheckbox_square_relative-yellow',
        radioClass: 'iradio_square_relative-yellow',
        increaseArea: '20%'
    });

    $('.extraColumnsred input').iCheck({
        checkboxClass: 'icheckbox_square_relative-red',
        radioClass: 'iradio_square_relative-red',
        increaseArea: '20%'
    });
    $(document).ready(function () {


    });
</script>


<?php
/**
 * Created by PhpStorm.
 * User: l
 * Date: 4/12/2019
 * Time: 9:59 AM
 */