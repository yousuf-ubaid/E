<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$date_format_policy = date_format_policy();
$current_date = current_format_date();

$assets_drop = fetch_emp_asset_category_drop();
$condition_drop = fetch_emp_asset_condition_drop();
?>
<div class="row" style="margin-bottom: 1%;">
    <div class="col-md-6 pull-left">&nbsp;</div>
    <div class="col-md-6 pull-right">
        <button type="button" class="btn btn-primary size-sm pull-right"
                onclick="add_assets()"><i class="fa fa-plus"></i>&nbsp; <?php echo $this->lang->line('emp_add');?><!--Add-->
        </button>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <table class="table table-bordered" id="bankAccDetTb" >
            <thead>
            <tr>
                <th><?php echo $this->lang->line('common_category');?> </th>
                <th><?php echo $this->lang->line('common_description');?> </th>
                <th><?php echo $this->lang->line('common_serial_no');?></th>
                <th><?php echo $this->lang->line('emp_master_asset_condition');?></th>
                <th><?php echo $this->lang->line('emp_master_asset_hand_over_date');?></th>
                <th><?php echo $this->lang->line('emp_master_asset_returned_date');?></th>
                <th><?php echo $this->lang->line('emp_master_asset_return_comment');?></th>
                <th style="width:70px" class="hidbtn"> &nbsp; </th>
            </tr>
            </thead>
            <tbody>
            <?php

            if(!empty($asset_det)){
                $empID = $this->input->post('empID');
                foreach($asset_det as $row){

                    $editFn = 'edit_asset(\'' . $row['masterID'] . '\',\'' . $row['assetTypeID'] . '\',\''.$row['description'].'\',';
                    $editFn .= '\''.$row['asset_serial_no'].'\', \''.$row['assetConditionID'].'\', \''.$row['handOverDate'].'\',';
                    $editFn .= '\''.$row['returnStatus'].'\',\''.$row['returnDate'].'\', \''.$row['returnComment'].'\')';

                    $action = '<a onclick="'.$editFn.'" title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil"></span></a>';
                    $action .= '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_employee_assets('.$row['masterID'].')" title="Delete" rel="tooltip">';
                    $action .= '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

                    echo '<tr>
                            <td>'.$row['assetType'].'</td>
                            <td>'.$row['description'].'</td>
                            <td>'.$row['asset_serial_no'].'</td> 
                            <td>'.$row['con_des'].'</td> 
                            <td>'.$row['handOverDate'].'</td> 
                            <td>'.$row['returnDate'].'</td> 
                            <td>'.$row['returnComment'].'</td>                             
                            <td align="right" class="hidbtn">' . $action . '</td>
                        </tr>';
                }
            }
            else{
                echo '<tr><td colspan="8">&nbsp;</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="asset_modal" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title asset_modalTitle" id="myModalLabel"><?php echo $this->lang->line('emp_master_add_assets');?></h4>
            </div>
            <form class="form-horizontal" id="asset_form">
                <div class="modal-body">
                    <input type="hidden" name="empID" id="empID" value="<?=$empID?>">
                    <input type="hidden" name="asset_id" id="asset_id">

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="asset_category"><?php echo $this->lang->line('common_category');?></label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="add-category" onclick="add_category()"
                                            style="height: 27px; padding: 2px 10px;">
                                        <i class="fa fa-plus" style="font-size: 11px"></i>
                                    </button>
                                </span>
                                <?php echo form_dropdown('asset_category', $assets_drop, '', 'class="form-control select2" id="asset_category"'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="description"><?php echo $this->lang->line('common_description');?></label>
                        <div class="col-sm-6">
                            <input type="text" name="description" id="description" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="serial_no"><?php echo $this->lang->line('common_serial_no');?></label>
                        <div class="col-sm-6">
                            <input type="text" name="serial_no" id="serial_no" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="condition_id"><?php echo $this->lang->line('emp_master_asset_condition');?></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('condition_id', $condition_drop, '', 'class="form-control select2" id="condition_id"'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="hand_over_date"><?php echo $this->lang->line('emp_master_asset_hand_over_date');?></label>
                        <div class="col-sm-6">
                            <div class="input-group date_pic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="hand_over_date" value="<?php echo $current_date; ?>" id="hand_over_date" class="form-control"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label" for="is_returned"><?php echo $this->lang->line('emp_master_asset_is_returned');?></label>
                        <div class="col-sm-6">
                            <input type="checkbox" name="is_returned" id="is_returned" class="check-box" value="1"/>
                        </div>
                    </div>

                    <div class="form-group return-content">
                        <label class="col-sm-4 control-label" for="returned_date"><?php echo $this->lang->line('emp_master_asset_returned_date');?></label>
                        <div class="col-sm-6">
                            <div class="input-group date_pic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="returned_date" value="<?php echo $current_date; ?>" id="returned_date"
                                       class="form-control" data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>
                        </div>
                    </div>

                    <div class="form-group return-content">
                        <label class="col-sm-4 control-label" for="serial_no"><?php echo $this->lang->line('emp_master_asset_return_comment');?></label>
                        <div class="col-sm-6">
                            <input type="text" name="returnComment" id="returnComment" class="form-control"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="save_assets()">
                        <?php echo $this->lang->line('emp_save');?><!-- Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('emp_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="new_assets_category_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('emp_master_new_asset_category');?> </h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?> </label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="assets_category_des" name="assets_category_des">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="create_assets_category()"><?php echo $this->lang->line('common_save');?> </button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?> </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    <?php if($is_frm_profile == 1){ ?>
        var fromHiarachy = 1;
    <?php } ?>

    var asset_form = $('#asset_form');
    var newEmpID = <?php echo $empID ?>;

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.check-box').iCheck({
        checkboxClass: 'icheckbox_minimal-blue'
    });

    $('.check-box').on('ifChecked', function(event){
        $('.return-content').show();
    });

    $('.check-box').on('ifUnchecked', function(event){
        $('.return-content').hide();
    });

    $('.date_pic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
        widgetPositioning: {
            vertical: 'bottom'
        }
    });

    $('.select2').select2();

    function add_assets(){
        $('.asset_modalTitle').html('<?php echo $this->lang->line('emp_master_add_assets');?>');
        asset_form[0].reset();
        asset_form.attr('action', '<?php echo site_url('Employee/save_emp_assets') ?>');

        $('#empID').val(newEmpID);
        $('#asset_category').val('').change();
        $('#condition_id').change();
        $('.check-box').iCheck('update');
        $('.return-content').hide();

        $('#asset_modal').modal('show');
    }

    function edit_asset(asset_id, cateID, description, serial, condition_id, hand_over, returnStatus, return_date, return_comment) {
        $('.asset_modalTitle').html('<?php echo $this->lang->line('emp_master_edit_assets');?>');
        asset_form[0].reset();
        asset_form.attr('action', '<?php echo site_url('Employee/edit_emp_assets') ?>');

        $('#asset_id').val(asset_id);
        $('#asset_category').val(cateID).change();
        $('#description').val(description);
        $('#serial_no').val(serial);
        $('#condition_id').val(condition_id).change();
        $('#hand_over_date').val(hand_over);
        $('.check-box').iCheck('update');

        if(returnStatus == 1){
            $('.check-box').iCheck('check');
            $('.return-content').show();
        }
        else{
            $('.check-box').iCheck('uncheck');
            $('.return-content').hide();
        }


        $('#returned_date').val(return_date);
        $('#returnComment').val(return_comment);

        $('.accountStatusContainer, .payrollType-in-update-container').show();
        $('.payrollTypeContainer').hide();


        if( status == 0 ){
            $('#accStatusInAct').prop('checked', true);
        }else{
            $('#accStatusAct').prop('checked', true);
        }

        $('#asset_modal').modal('show');
    }

    function save_assets() {
        var postData = asset_form.serialize();
        var url = asset_form.attr('action');
        $.ajax({
            type: 'post',
            url: url,
            data: postData,
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if ( data[0] == 's') {
                    $('#asset_modal').modal('hide');
                    setTimeout(function(){
                        fetch_employee_assets();
                    }, 300);

                }
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });

    }

    function delete_employee_assets(asset_id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure ?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record ?*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    type: 'post',
                    url: '<?php echo site_url('Employee/delete_employee_assets') ?>',
                    data: {'asset_id': asset_id},
                    dataType: 'json',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if( data[0] == 's' ){
                            setTimeout(function(){
                                fetch_employee_assets();
                            },300);
                        }
                    },
                    error: function () {
                        stopLoad();
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    }
                });
            }
        );
    }

    function add_category(){
        $('#assets_category_des').val('');
        $('#new_assets_category_modal').modal({backdrop: 'static'});
    }

    function create_assets_category() {
        var description = $.trim($('#assets_category_des').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'description': description},
            url: '<?php echo site_url("Employee/new_assets_category"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                var asset_category_drop = $('#asset_category');
                if (data[0] == 's') {
                    asset_category_drop.append('<option value="' + data['id'] + '">' + data['description'] + '</option>');
                    asset_category_drop.val(data['id']).change();
                    $('#new_assets_category_modal').modal('hide');
                }


            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    if(fromHiarachy == 1){
        $('.btn ').addClass('hidden');
        $('.hidbtn ').addClass('hidden');
        $('.navdisabl ').removeClass('hidden');
    }
</script>

<?php
