<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$grpBycusPol = getPolicyValues('CSG', 'All');
$acknowledgementDateYN = getPolicyValues('SAD', 'All');
$date_format_policy = date_format_policy();
if($type == 1) {
    $customer_category_arr = all_customer_category_report_drop();
    $from = convert_date_format($this->common_data['company_data']['FYBegin']);
    $to = convert_date_format($this->common_data['company_data']['FYEnd']);
}
else{
    $from = convert_date_format($this->session->userdata("FYBeginingDate"));
    $to = convert_date_format($this->session->userdata("FYEndingDate"));
}
?>
<ul class="nav nav-tabs" xmlns="http://www.w3.org/1999/html">
    <li class="active"><a href="#display" data-toggle="tab"><i class="fa fa-television"></i>
          <?php echo $this->lang->line('accounts_receivable_common_display');?> <!-- Display--> </a></li>
    <li>
</ul>
<input type="hidden" name="reportID" value="<?php echo $reportID ?>">
<div class="tab-content">
    <div class="tab-pane active" id="display">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"> <?php echo $this->lang->line('accounts_receivable_common_date_range');?><!--Date Range--></legend>
                    <?php if ($reportID == 'AR_CL') { ?>
                        <div class="col-sm-8" style="">
                            <div class="input-daterange input-group col-sm-12" id="datepicker">
                                <div class="form-group col-sm-6" style="margin-bottom: 0px">
                                    <label class="col-md-3 control-label text-left"
                                           for="employeeID"><?php echo $this->lang->line('common_from');?><!--From-->:</label>
                                    <div class="form-group col-md-8">
                                        <div class='input-group date filterDate' id="">
                                            <input type="text" name="from" id="from" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                   value="<?php echo $from; ?>"
                                                   class="form-control filterDate" required>
                                            <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6" style="margin-bottom: 0px">
                                    <label class="col-md-3 control-label text-left"
                                           for="employeeID"><?php echo $this->lang->line('common_to');?><!--To-->:</label>
                                    <div class="form-group col-md-8">
                                        <div class='input-group date filterDate' id="">
                                            <input type="text" name="to" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                   value="<?php echo current_format_date() ?>"
                                                   class="form-control filterDate" required>
                                            <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="form-group col-sm-4" style="margin-bottom: 0px">
                            <label class="col-md-3 control-label text-left"
                                   for="employeeID"><?php echo $this->lang->line('accounts_receivable_common_as_of');?><!--As of-->:</label>
                            <div class="form-group col-md-8">
                                <div class='input-group date filterDate' id="">
                                    <input type="text" name="from" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo current_format_date() ?>"
                                           class="form-control filterDate" required>
                                            <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="col-sm-4" style="margin-bottom: 0px;">
                        <?php echo $this->lang->line('accounts_receivable_common_current_financial_period');?> <!--From current financial period-->
                    </div>

                    <?php if(!empty($acknowledgementDateYN) && $acknowledgementDateYN == 1) {
                        if ($reportID == 'AR_CS' || $reportID == 'AR_CAD' || $reportID == 'AR_CAS' || $reportID == 'AR_CSR') { ?>
                            <div class="form-group col-sm-4" style="margin-bottom: 0px">
                                <label class="col-md-3 control-label text-left" for="ackGroupBy"><?php echo $this->lang->line('accounts_receivable_common_based_on');?><!-- Based On --> :</label>
                                <div class="form-group col-md-8">
                                    <?php echo form_dropdown('ackGroupBy', array(1 => 'Document Date', 2=> 'Acknowledgement Date') , 1, 'class="form-control select2" id="ackGroupBy"');?>
                                </div>
                            </div>
                    <?php } 
                    } ?>
                </fieldset>
                <?php if ($reportID == 'AR_CS') { ?>
                <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border">   <?php echo $this->lang->line('common_segment');?><!--Segment--></legend>
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <?php
                            if($type == 1){
                               
                                $segment = array_filter_reports(fetch_segment(true));
                            }else{
                                $segment = array_filter(fetch_group_segment(true));
                            }
                            unset($segment['']);
                            echo form_dropdown('location[]', $segment, '', 'class="segment" id="segment" multiple="multiple"'); ?>
                        </div>
                </fieldset>
                <?php } ?>
                <?php if($type == 1) { ?>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><?php echo $this->lang->line('common_customer');?><!--Customer-->  </legend>
                        <div class="col-sm-3">
                            <label> <?php echo $this->lang->line('common_customer_category');?><!--Customer Category--> </label>
                            <!--Customer Category-->
                            <?php
                                echo form_dropdown('customerCategoryID',$customer_category_arr , 'Each', 'class="form-control" id="customerCategoryID" onchange="loadCustomer()"  multiple="multiple"');
                            ?>
                        </div>
                        <div class="col-sm-3">
                        <label for="status_filter"><?php echo $this->lang->line('common_status');?></label>
                            <?php echo form_dropdown('status_filter', array('1'=>'Active','2'=>'Not Active','3'=>'All'), '', '  class="form-control" onchange="loadCustomer()" id="status_filter"  '); ?>
                        </div>
                    </fieldset>
                    
                <?php } ?>

                <fieldset class="scheduler-border" style="margin-top: 10px">
                    <legend class="scheduler-border"><?php echo $this->lang->line('common_customer');?><!--Customer--></legend>
                    <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                        <div class="col-sm-5">
                            <select name="customerFrom[]" id="search" class="form-control" size="8"
                                    multiple="multiple">
                                <?php

                                if($type == 1){
                                    $customer = all_customer_drop(TRUE,1);
                                }else{
                                    $customer = all_group_customer_drop();
                                }
                                unset($customer[""]);
                                if (!empty($customer)) {
                                    foreach ($customer as $key => $val) {
                                        echo '<option value="' . $key . '">' . $val . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <!--<button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">undo</button>-->
                            <button type="button" id="search_rightAll" class="btn btn-block btn-sm"
                            ><i class="fa fa-forward"></i></button>
                            <button type="button" id="search_rightSelected" class="btn btn-block btn-sm"><i
                                    class="fa fa-chevron-right"></i></button>
                            <button type="button" id="search_leftSelected" class="btn btn-block btn-sm"><i
                                    class="fa fa-chevron-left"></i></button>
                            <button type="button" id="search_leftAll" class="btn btn-block btn-sm"><i
                                    class="fa fa-backward"></i></button>
                            <!--<button type="button" id="undo_redo_redo" class="btn btn-warning btn-block">redo</button>-->
                        </div>
                        <div class="col-sm-5">
                            <select name="customerTo[]" id="search_to" class="form-control" size="8"
                                    multiple="multiple">
                            </select>
                        </div>
                    </div>
                </fieldset>
                <?php if ($reportID == 'AR_CAS' || $reportID == 'AR_CAD') { ?>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"><?php echo $this->lang->line('common_days');?><!--Days--></legend>
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <div class="form-group col-sm-4" style="margin-bottom: 0px">
                                <label class="col-md-6 control-label text-left"
                                       for="employeeID"><?php echo $this->lang->line('accounts_receivable_common_interval');?><!--Interval--> (<?php echo $this->lang->line('accounts_receivable_common_days');?><!--days-->)</label>
                                <div class="input-group col-md-3">
                                    <input type="number" name="interval"
                                           value="30" max="99" min="10" onchange="maxMinInput(this,10,99)"
                                           class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group col-sm-5" style="margin-bottom: 0px">
                                <label class="col-md-6 control-label text-left"
                                       for="employeeID"><?php echo $this->lang->line('accounts_receivable_common_through');?><!--Through--> (<?php echo $this->lang->line('accounts_receivable_common_days_past_due');?><!--days past due-->)</label>
                                <div class="input-group col-md-3">
                                    <input type="number" name="through"
                                           value="100" max="1000" onchange="maxMinInput(this,11,1000)"
                                           class="form-control input-xs" required>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                <?php } ?>
                <fieldset class="scheduler-border" style="margin-top: 10px">
                    <legend class="scheduler-border"><?php echo $this->lang->line('accounts_receivable_common_extra_columns');?><!--Extra Columns--></legend>
                    <div class="col-sm-4" style="margin-bottom: 0px;margin-top:10px">
                        <table class="<?php echo table_class(); ?>" id="extraColumns">
                            <?php
                            if (!empty($columns)) {
                                $i = 1;
                                foreach ($columns as $val) {
                                    $checked = "";
                                    if ($val["isDefault"] == 1) {
                                        $checked = "checked";
                                    }
                                    if($type == 1){
                                        if ($val["isMandatory"] == 0) {
                                            ?>
                                            <tr>
                                                <td style="vertical-align: middle"><?php echo $val["caption"] ?></td>
                                                <td>
                                                    <div class="skin skin-square">
                                                        <div class="skin-section">
                                                            <input tabindex="<?php echo $i; ?>"
                                                                   id="checkbox<?php echo $i; ?>" type="checkbox"
                                                                   data-caption="<?php echo $val["caption"] ?>"
                                                                   class="columnSelected" name="fieldName"
                                                                   value="<?php echo $val["fieldName"] ?>" <?php echo $checked ?>>
                                                            <label for="checkbox<?php echo $i; ?>">
                                                                &nbsp;
                                                            </label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        } else {
                                            ?>
                                            <tr class="hide">
                                                <td style="vertical-align: middle"><?php echo $val["caption"] ?></td>
                                                <td>
                                                    <div class="checkbox checkbox-primary">
                                                        <input id="checkbox<?php echo $i; ?>" type="checkbox"
                                                               data-caption="<?php echo $val["caption"] ?>"
                                                               class="columnSelected" name="fieldName"
                                                               value="<?php echo $val["fieldName"] ?>" <?php echo $checked ?>>
                                                        <label for="checkbox<?php echo $i; ?>">
                                                            &nbsp;
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }else{
                                        if ($val["fieldID"] == 191 || $val["fieldID"] == 194 || $val["isDefault"] == 1) {
                                            $checked = "checked";
                                        }
                                        if($val["fieldID"]!=189 && $val["fieldID"]!=190 && $val["fieldID"]!=193 && $val["fieldID"]!=192 && $val["fieldID"]!=204 && $val["fieldID"]!=195 && $val["fieldID"]!=206 && $val["fieldID"]!=197 ){
                                            if ($val["isMandatory"] == 0) {
                                                ?>
                                                <tr>
                                                    <td style="vertical-align: middle"><?php echo $val["caption"] ?></td>
                                                    <td>
                                                        <div class="skin skin-square">
                                                            <div class="skin-section">
                                                                <input tabindex="<?php echo $i; ?>"
                                                                       id="checkbox<?php echo $i; ?>" type="checkbox"
                                                                       data-caption="<?php echo $val["caption"] ?>"
                                                                       class="columnSelected" name="fieldName"
                                                                       value="<?php echo $val["fieldName"] ?>" <?php echo $checked ?>>
                                                                <label for="checkbox<?php echo $i; ?>">
                                                                    &nbsp;
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            } else {
                                                ?>
                                                <tr class="hide">
                                                    <td style="vertical-align: middle"><?php echo $val["caption"] ?></td>
                                                    <td>
                                                        <div class="checkbox checkbox-primary">
                                                            <input id="checkbox<?php echo $i; ?>" type="checkbox"
                                                                   data-caption="<?php echo $val["caption"] ?>"
                                                                   class="columnSelected" name="fieldName"
                                                                   value="<?php echo $val["fieldName"] ?>" <?php echo $checked ?>>
                                                            <label for="checkbox<?php echo $i; ?>">
                                                                &nbsp;
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    $i++;
                                }
                            } ?>
                        </table>
                    </div>
                    <div class="col-sm-8" style="margin-bottom: 0px;margin-top:10px">
                        <?php echo $this->lang->line('accounts_receivable_common_put_a_check_mark_next_to_each');?> <!--Put a check mark next to each column that you want to appear in the report-->
                    </div>
                </fieldset>

                <?php $hidefield=''; if($grpBycusPol!=1) {
                $hidefield="hide";
                }?>
                <?php if ($reportID == 'AR_CL' || $reportID == 'AR_CS'|| $reportID == 'AR_CAS'|| $reportID == 'AR_CAD') { ?>
                <fieldset class="scheduler-border <?php echo $hidefield ?>" style="margin-top: 10px">
                    <legend class="scheduler-border">Group Customer</legend>
                    <div class="col-sm-4" style="margin-bottom: 0px;margin-top:10px">
                        <table class="<?php echo table_class(); ?>" id="extraColumnsgrp">
                                <tr>
                                    <td style="vertical-align: middle">Group by Customers</td>
                                    <td>
                                        <div class="skin skin-square">
                                            <div class="skin-section">
                                                <input tabindex=""
                                                       id="groupbycus" type="checkbox"
                                                       data-caption="groupbycus"
                                                       class="groupbycus" name="groupbycus"
                                                       value="1" >
                                                <label for="groupbycus">
                                                    &nbsp;
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                        </table>
                    </div>
                </fieldset>
            <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="margin-top: 10px">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="generateReport('<?php echo $formName; ?>')" name="filtersubmit"
                        id="filtersubmit"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_generate');?><!--Generate-->
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    var partyCategoryID ='';
    /*$('.input-daterange').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    });
    $('.filterDate').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    });*/
    $('.filterDate').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    });
    $('#search').multiselect({
        search: {
            left: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />',/*Search*/
            right: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />',/*Search*/
        },
        afterMoveToLeft: function ($left, $right, $options) {
            $("#search_to option").prop("selected", "selected");
        }
    });
    $('.skin-square input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });
    $('#extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });
    $('#extraColumnsgrp input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });

    $('#segment').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        maxHeight: '30px',
        allSelectedText: 'All Selected'
    });
    $("#segment").multiselect2('selectAll', false);
    $("#segment").multiselect2('updateButtonText');

    function maxMinInput(input, min, max) {
        if (input.value < min) input.value = min;
        if (input.value > max) input.value = max;
    }
    /*$('#search_rightAll').trigger('click');*/
    $("#customerCategoryID").multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });



    function loadCustomer() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Report/loadCustomer"); ?>',
            dataType: 'json',
            data: {
                customerCategoryID: $('#customerCategoryID').val(),
                type:<?php echo $type; ?>,
                activeStatus: $('#status_filter').val()
            },
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#search').empty();
                    var mySelect = $('#search');
                    $.each(data['details'], function (val, text) {
                    if(data['type']==1) {
                        mySelect.append($('<option></option>').val(text['customerAutoID']).html(text['customerSystemCode'] + ' | ' + text['customerName'] + ' | ' + text['customerCountry']));
                    }else{
                        mySelect.append($('<option></option>').val(text['groupCustomerAutoID']).html(text['groupcustomerSystemCode'] + ' | ' + text['groupCustomerName'] + ' | ' + text['customerCountry']));

                    }
                    });
                } else {
                    $('#search').empty();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


</script>