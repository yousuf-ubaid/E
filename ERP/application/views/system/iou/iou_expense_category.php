<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('iou', $primaryLanguage);

echo head_page( $this->lang->line('iou_category') , false);
$date_format_policy = date_format_policy();
$gl_code =fetch_glcode_claim_category();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .alpha-box {
        font-size: 14px;
        line-height: 25px;
        list-style: none outside none;
        margin: 0 0 0 12px;
        padding: 0 0 0;
        text-align: center;
        text-transform: uppercase;
        width: 24px;
    }

    ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    .alpha-box li a {
        text-decoration: none;
        color: #555;
        padding: 4px 8px 4px 8px;
    }

    .alpha-box li a.selected {
        color: #fff;
        font-weight: bold;
        background-color: #4b8cf7;
    }

    .alpha-box li a:hover {
        color: #000;
        font-weight: bold;
        background-color: #ddd;
    }
</style>
<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right"
                onclick="add_iou_category()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('iou_category'); ?>
        </button>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-sm-12">
                    <div id="iou_catergory_view"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="ioucategorymodal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close modalclose1" id="modalclose" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">IOU Expense Category</h4>
            </div>
            <?php echo form_open('', 'role="form" id="iou_category_form"'); ?>
                <div class="modal-body">
                    
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title"><?php echo $this->lang->line('common_description'); ?></label>
                        </div>
                        <div class="form-group col-sm-8">
                            <input type="text" name="Description" id="Description" class="form-control"
                                placeholder="Description">
                            <input type="hidden" class="form-control" name="expenseClaimCategoriesAutoID" id="expenseClaimCategoriesAutoID">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title"><?php echo $this->lang->line('common_gl_code'); ?></label>
                        </div>
                        <div class="form-group col-sm-6">
                            <?php echo form_dropdown('glcode', $gl_code, '', 'class="form-control select2" id="glcode"'); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title"><?php echo $this->lang->line('iou_is_fuel_usage'); ?></label>
                        </div>
                        <div class="form-group col-sm-6">
                            <div class="skin skin-square">
                                <div class="skin-section" id="extraColumns">
                                    <input id="isfueluage" type="checkbox"
                                        data-caption="" class="columnSelected" name="isfueluage" value="1">
                                    <label for="checkbox">
                                        &nbsp;
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3">
                            <label class="title">Requires Mandatory Fields</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns1" id="extraColumns">
                                    <input id="textmfield" type="checkbox" data-caption="" class="columnSelected textmfield1" name="textmfield" value="1">
                                    <label for="checkbox">&nbsp;</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div style="margin-top: 0px;" class="form-group" id="mainfieldcontainer1">
                        <div class="row" id="fieldcontainer" >
                            <div class="form-group col-sm-3" id="fieldlabel">
                                <label class="title"></label>
                            </div>
                            <div class="form-group col-sm-9" id="inputcontainer">
                                    <div class="form-group col-sm-8" id="textInputContainer">
                                        <input type="text" class="form-control mandatoryInputfField1" id="sub-add-tb" name="mandatoryInputfField[]" placeholder="Input Mandetory Field Here">
                                    </div>
                                    <div class="form-group col-sm-1" id="buttnDev1">
                                        <button type="button" class="btn btn-primary add-button" id="add1">+</button>
                                    </div> 
                            </div>
                        </div>
                    </div> -->

                    <div id="new_field" class="">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-10">
                            <table class="table table-bordered" id="sub-add-tb" style="margin-bottom:10px;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th><button type="button" class="btn btn-primary btn-xs" onclick="add_more_sub()" ><i class="fa fa-plus"></i></button></th>
                                        </tr>
                                    </thead>
                                    <tbody id="field_tbody">
                                        <tr>
                                            <td><input type="text" class="form-control new-items" name="mandatoryInputfField[]" id="mandatoryInputfField" placeholder="Enter Field Here"></td>
                                        </tr>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                         
                </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" onclick="save_iou_category()" id="save_btn"><?php echo $this->lang->line('common_save'); ?></button>
                <button type="button" class="btn btn-default btn-sm modalclose1" id="modalclose" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    var Type = 2;
    $(document).ready(function () {
        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%',
        });
        
        $('.select2').select2();

        $('#new_field').hide();
        
        $('.headerclose').click(function () {
            fetchPage('system/iou/iou_category', '', '<?php echo $this->lang->line('iou_category'); ?>');
        });
        getioucategory_tableView();

    });


    // when model close
    $('#modalclose').on('click',function(){
        $('#textmfield').iCheck('uncheck');
        $('.new-items').val('');
        $(this).closest('.tb-tr').remove();
        
    });

    // Appears required fields when check box triggered as check
    $('.textmfield1').on('ifChanged', function () {
        $('#new_field').toggle(this.checked);
  

    });
    // Remove added required fields when check Box triggered back as uncheck
    $('.textmfield1').on('ifChanged', function () {
        $('.new-items').val('');
        $('.tb-tr').remove();
    });

   
    function getioucategory_tableView() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'type': Type},
            url: "<?php echo site_url('Iou/iou_categorymaster_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#iou_catergory_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function delete_iou_category(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'expenseClaimCategoriesAutoID': id},
                    url: "<?php echo site_url('Iou/delete_ioucategory'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert('s', '<?php echo $this->lang->line('iou_category_deleted_successfully'); ?>');
                        getioucategory_tableView();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function save_iou_category() {
        var data = $('#iou_category_form').serializeArray();
        data.push({'name': 'gldes', 'value': $('#glcode option:selected').text()});
        data.push({'name': 'type', 'value': Type});
        // data.push({'name': 'isfueluage', 'value': $('#isfueluage').prop('checked') ? 1 : 0});
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Iou/save_iou_category'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data['status'] = true) {
                    getioucategory_tableView();
                    $('#ioucategorymodal').modal('hide');
                }
                stopLoad();
                refreshNotifications(true);
            },
            error: function (/*jqXHR, textStatus, errorThrown*/) {
                myAlert('e','common_an_error_occurred_on_save_iou_category_Please_try_again');
                //myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function edit_iou_catergory(expenseClaimCategoriesAutoID) 
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'expenseClaimCategoriesAutoID': expenseClaimCategoriesAutoID},
            url: "<?php echo site_url('Iou/iou_categoryheader'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#field_tbody').html('');//for mendatory fields

                if (!jQuery.isEmptyObject(data)) {
                    if(data.expenseclaimcategories){
                        $('#Description').val(data.expenseclaimcategories['claimcategoriesDescription']);
                        $('#expenseClaimCategoriesAutoID').val(data.expenseclaimcategories['expenseClaimCategoriesAutoID']);
                        $('#glcode').val(data.expenseclaimcategories['glAutoID']).change();
                        if (data.expenseclaimcategories['fuelUsageYN'] == 1) {
                            $('#isfueluage').iCheck('check');
                        } else {
                            $('#isfueluage').iCheck('uncheck');
                        }
                    }  
                    
                    //for mendatory fields.............................
                        $('#textmfield').iCheck('check');
                        var appendData1 = '';
                            appendData1 = '<tr class="tb-tr"><td><input type="text" value="" name="mandatoryInputfField[]" id="mandatoryInputfField" class="form-control new-items" placeholder="Enter Field Here" /></td>';
                        $('#field_tbody').append(appendData1);

                    $.each(data.documentcustomfields, function (i, v) {
                        // toastr[v.t](v.m, v.h);
                        var appendData = '';
                        appendData = '<tr class="tb-tr"><td><input type="text" value="'+ v.fieldName + '" name="mandatoryInputfField_exist[]" id="mandatoryInputfField_exist" class="form-control new-items" placeholder="Enter Field Here" /><input type="hidden" value="'+ v.id + '" name="mandatoryInputfField_exist_ID[]" id="mandatoryInputfField_exist_ID" class="form-control new-items" /></td>';
                        appendData += '<td align="center" style="vertical-align: middle">';
                        appendData += '<a onclick="delete_field(' + v.id + ')"><span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></a></td></tr>';
                        $('#sub-add-tb').append(appendData);
                        
                        });
                    //.............................................

                }

                $('#ioucategorymodal').modal({ backdrop: "static" });

                stopLoad();
                refreshNotifications(true);

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again') ?>');
                stopLoad();
                refreshNotifications(true);
            }
        });

        $('#new-plus-button-container2').show();
    }
    
    function add_iou_category(){
        $('.textmfield').iCheck('uncheck');
        $('#Description').val('');
        $('#glcode').val(null).trigger("change");
        $('#expenseClaimCategoriesAutoID').val('');

        //$('#field_tbody').empty();
        $('#field_tbody').html('');

        var appendData1 = '';
            appendData1 = '<tr class="tb-tr"><td><input type="text" value="" name="mandatoryInputfField[]" id="mandatoryInputfField" class="form-control new-items" placeholder="Enter Field Here" /></td>';       
            $('#field_tbody').append(appendData1);

        $("#ioucategorymodal").modal({backdrop: "static"});
    }


    function add_more_sub(){
        var appendData = '<tr class="tb-tr"><td><input type="text" name="mandatoryInputfField[]" id="mandatoryInputfField" class="form-control new-items" placeholder="Enter Field Here" /></td>';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';

        $('#sub-add-tb').append(appendData);
    }

    function delete_field(id){
        //if(!empty(id) || id !=''){ }
        if(id){
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'id': id},
                        url: "<?php echo site_url('Iou/delete_ioucategory_field'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert('s', 'Mandatory Field Deleted Successfully');
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        else{
            $('.remove-tr').closest('tr').remove();
        }
        
    };

</script>