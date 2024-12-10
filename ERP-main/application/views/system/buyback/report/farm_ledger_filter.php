<?php
$this->load->helper('buyback_helper');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$date_format_policy = date_format_policy();
if ($type == 1) {
    $from = convert_date_format($this->common_data['company_data']['FYBegin']);
    $to = convert_date_format($this->common_data['company_data']['FYEnd']);
} else {
    $from = convert_date_format($this->session->userdata("FYBeginingDate"));
    $to = convert_date_format($this->session->userdata("FYEndingDate"));
}
?>
<ul class="nav nav-tabs" xmlns="http://www.w3.org/1999/html">
    <li class="active"><a href="#display" data-toggle="tab"><i class="fa fa-television"></i>
            <?php echo $this->lang->line('accounts_receivable_common_display'); ?> <!-- Display--> </a></li>
    <li>
</ul>
<input type="hidden" name="reportID" value="<?php echo $reportID ?>">
<div class="tab-content">
    <div class="tab-pane active" id="display">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">
                        <?php echo $this->lang->line('accounts_receivable_common_date_range'); ?><!--Date Range--></legend>

                    <div class="form-group col-sm-4" style="margin-bottom: 0px">
                        <label class="col-md-3 control-label text-left"
                               for="employeeID">
                            <?php echo $this->lang->line('accounts_receivable_common_as_of'); ?><!--As of-->:</label>

                        <div class="form-group col-md-8">
                            <div class='input-group date filterDate' id="">
                                <input type="text" name="from"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo current_format_date() ?>"
                                       class="form-control filterDate" required>
                                            <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="scheduler-border" style="margin-top: 10px">
                    <legend class="scheduler-border">Farmer</legend>
                    <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                        <div class="col-sm-5">
                            <select name="customerFrom[]" id="search" class="form-control" size="8"
                                    multiple="multiple">
                                <?php
                                if($type == 1){
                                    $farm = load_all_farms_view();
                                }else{
                                    $farm = load_all_groupFarms_view();
                                }
                                unset($farm[""]);
                                if (!empty($farm)) {
                                    foreach ($farm as $val) {
                                        echo '<option value="' . $val['farmID'] . '">' . $val['farmSystemCode'] . " | " . $val['description'] . '</option>';
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
                            <select name="farmerTo[]" id="search_to" class="form-control" size="8"
                                    multiple="multiple">
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="scheduler-border" style="margin-top: 10px">
                    <legend class="scheduler-border">Document Type</legend>

                    <div class="form-group col-sm-4" style="margin-top: 10px">
                        <?php echo form_dropdown('documentCode[]', array(/*'BBDPN' => 'Dispatch Note', 'BBGRN' => 'Goods Received Note',*/ 'BBRV' => 'Receipt Voucher', 'BBPV' => 'Payment Voucher' , 'BBSV' => 'Settlement', 'BBJV' => 'Journal Voucher' /*'BBDR' => 'Dispatch Return'*/), '', ' multiple class="form-control" id="documentCode"'); ?>
                    </div>
                </fieldset>
                <fieldset class="scheduler-border" style="margin-top: 10px">
                    <legend class="scheduler-border">
                        <?php echo $this->lang->line('accounts_receivable_common_extra_columns'); ?><!--Extra Columns--></legend>
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
                                        if ($val["fieldID"] == 212 || $val["isDefault"] == 1) {
                                            $checked = "checked";
                                        }
                                        if($val["fieldID"]!=210 && $val["fieldID"]!=211){
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
                    <div class="col-sm-8" style="margin-bottom: 0px;margin-top:10px">Put a check mark next to each column that you want to appear in the report
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="margin-top: 10px">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="generateReport('<?php echo $formName; ?>')" name="filtersubmit"
                        id="filtersubmit"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_generate'); ?><!--Generate-->
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('#documentCode').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#documentCode").multiselect2('selectAll', false);
    $("#documentCode").multiselect2('updateButtonText');

    $('.filterDate').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    });
    $('#search').multiselect({
        search: {
            left: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />', /*Search*/
            right: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />', <!--Search-->
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

    function maxMinInput(input, min, max) {
        if (input.value < min) input.value = min;
        if (input.value > max) input.value = max;
    }
    /*$('#search_rightAll').trigger('click');*/
</script>