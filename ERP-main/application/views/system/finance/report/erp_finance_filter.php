<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$report = false;
$report_policy = '';
$finaceYearYN = getPolicyValues('HFY','All');

if($finaceYearYN == 1)
{

    if ($reportID == "FIN_IS" || $reportID == "FIN_GL" || $reportID == "FIN_BD" || $reportID == "FIN_TB" || $reportID == "FIN_BS") {
        $report = true;
    }
}else
{
    if ($reportID == "FIN_IS" || $reportID == "FIN_GL" || $reportID == "FIN_BD") {
        $report = true;
    }
}


$date_format_policy = date_format_policy();

if($finaceYearYN == 1){
    $from = convert_date_format(date('Y-01-01'));
    $to =  convert_date_format(date('Y-12-31'));
}else
{
    if($type == 1) {
        $from = convert_date_format($this->common_data['company_data']['FYBegin']);
        $to = convert_date_format($this->common_data['company_data']['FYEnd']);
    }
    else{
        $from = convert_date_format($this->session->userdata("FYBeginingDate"));
        $to = convert_date_format($this->session->userdata("FYEndingDate"));
    }
}




$companyType = $this->session->userdata("companyType");
?>
<ul class="nav nav-tabs" xmlns="http://www.w3.org/1999/html">
    <li class="btn-default-new size-sm tab-style-one mr-1 active"><a href="#display" data-toggle="tab"><i class="fa fa-television"></i>
            <?php echo $this->lang->line('finance_common_display');?><!--Display--> </a></li>
    <li>
</ul>
<input type="hidden" name="reportID" value="<?php echo $reportID ?>">
<div class="tab-content">
    <div class="tab-pane active" id="display">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?php echo $this->lang->line('common_date');?><!--Date--></legend>
                    <?php if ($report) { ?>
                        <div class="col-sm-8" style="">
                            <div class="form-group col-sm-6" style="margin-bottom: 0px">
                                <label class="col-md-3 control-label text-left"
                                       for="employeeID"><?php echo $this->lang->line('common_from');?><!--From-->:</label>
                                <div class="form-group col-md-8">
                                    <div class='input-group date filterDate' id="">
                                        <input type="text" name="from" id="from"
                                               value="<?php echo $from; ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               class="form-control" required>
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
                                        <input type="text" name="to" id="to"
                                               value="<?php echo $to; ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               class="form-control" required>
                                        <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="form-group col-sm-4" style="margin-bottom: 0px">
                            <label class="col-md-3 control-label text-left"
                                   for="employeeID"><?php echo $this->lang->line('common_year');?><!--Year-->:</label>
                            <div class="input-group col-md-8">
                                <?php
                                $financeyear="";
                                if($type == 1){
                                    $financeyear = all_financeyear_report_drop(true);
                                }else{
                                    $financeyear = all_group_financeyear_report_drop(true);
                                }
                                unset($financeyear['']);
                                echo form_dropdown('financeYear[]', $financeyear,$this->common_data['company_data']['companyFinanceYearID'], 'class="financeYear form-control" id="financeYear"');
                                ?>
                            </div>
                        </div>
                        <div class="form-group col-sm-4" style="margin-bottom: 0px">
                            <label class="col-md-3 control-label text-left"
                                   for="employeeID"><?php echo $this->lang->line('finance_common_as_of');?><!--As of-->:</label>
                            <div class="form-group col-md-8">
                                <div class='input-group date filterDate' id="">
                                    <input type="text" name="from" id="from"
                                           value="<?php echo $to; ?>"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           class="form-control asof" required>
                                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-sm-4" style="margin-bottom: 0px;">
                        <?php echo $this->lang->line('finance_common_from_current_financial_period');?> <!--From current financial period-->
                    </div>
                </fieldset>
                <?php if ($report && ($reportID != "FIN_TB" && $reportID != "FIN_BS")) { ?>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border">   <?php echo $this->lang->line('common_segment');?><!--Segment--></legend>
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <?php
                            if($type == 1){
                                //$segment = array_filter(fetch_segment(true));
                                $segment = array_filter_reports(fetch_segment(true));
                            }else{
                                $segment = array_filter(fetch_group_segment(true));
                            }
                            unset($segment['']);
                            echo form_dropdown('segment[]', $segment, '', 'class="segment" id="segment" multiple="multiple"'); ?>
                        </div>
                    </fieldset>
                <?php } ?>
                <?php if ($reportID != "FIN_GL") { ?>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"><?php echo $this->lang->line('finance_common_report_type');?><!--Report Type--></legend>
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <div class="skin skin-square">
                                <div class="skin-section">
                                    <div class="col-sm-6">
                                        <ul class="list" style="list-style: none;">
                                            <?php if ($reportID != "FIN_BD") { ?>
                                            <li><input tabindex="1" type="radio" id="square-radio-1" value="1" checked
                                                       name="rptType">
                                                <label for="square-radio-1"><?php echo $this->lang->line('finance_rs_tb_month_wise');?><!--Month wise--></label></li>
                                            <!--<li><input tabindex="2" type="radio" id="square-radio-2" value="2" name="rptType">
                                                <label for="square-radio-2">Quarter</label></li>-->
                                            <li><input tabindex="3" type="radio" id="square-radio-3" value="3"
                                                       name="rptType"
                                                       >
                                                <label for="square-radio-3"><?php echo $this->lang->line('finance_rs_tb_ytd');?><!--YTD--></label>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <div class="col-sm-6">
                                        <ul class="list" style="list-style: none;">
                                            <?php if ($reportID == "FIN_IS" || $reportID == "FIN_BD") { ?>

                                                <?php if($reportID == "FIN_IS"){?>

                                                    <li><input tabindex="4" type="radio" id="square-radio-4" value="4"
                                                               name="rptType"
                                                               >
                                                        <label for="square-radio-3"><?php echo $this->lang->line('finance_rs_tb_month_wise_budget');?><!--Month wise budget--></label>
                                                    </li>
                                                    <li><input tabindex="5" type="radio" id="square-radio-5" value="5"
                                                               name="rptType"
                                                               >
                                                        <label for="square-radio-3"><?php echo $this->lang->line('finance_rs_tb_ytd_budget');?><!--YTD Budget--></label>
                                                    </li>
                                                    <?php } else { ?>

                                                    <li><input tabindex="4" type="radio" id="square-radio-4" value="4"
                                                               name="rptType"
                                                               checked>
                                                        <label for="square-radio-3"><?php echo $this->lang->line('finance_rs_tb_month_wise_budget');?><!--Month wise budget--></label>
                                                    </li>
                                                    <li><input tabindex="5" type="radio" id="square-radio-5" value="5"
                                                               name="rptType"
                                                               checked>
                                                        <label for="square-radio-3"><?php echo $this->lang->line('finance_rs_tb_ytd_budget');?><!--YTD Budget--></label>
                                                    </li>
                                                    <?php }?>


                                            <?php } ?>
                                        </ul>
                                    </div>

                                    <div class="col-sm-6">
                                        <ul class="list" style="list-style: none;">
                                            <?php if ($reportID == "FIN_IS" && $type==1) { ?>
                                                <li><input tabindex="4" type="radio" id="square-radio-7" value="7"
                                                           name="rptType"
                                                           >
                                                    <label for="square-radio-3">YTD - LYTD</label>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <div class="col-sm-6">
                                        <ul class="list" style="list-style: none;">
                                            <?php if ($reportID == "FIN_IS" && $type==1) { ?>
                                                <li><input tabindex="4" type="radio" id="square-radio-8" value="8"
                                                           name="rptType"
                                                           >
                                                    <label for="square-radio-3"><?php echo $this->lang->line('finance_rs_tb_month_wise');?><!--Month wise--> - LYM</label>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>

                                    <div class="col-sm-6">
                                        <ul class="list" style="list-style: none;">
                                            <?php if ($reportID == "FIN_IS" && $type==1) { ?>
                                                <li><input tabindex="4" type="radio" id="square-radio-9" value="9"
                                                           name="rptType"
                                                    >
                                                    <label for="square-radio-3"><?php echo $this->lang->line('common_segment');?><!--Segment--></label>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                <?php } ?>
                <?php if ($reportID == "FIN_GL") { ?>
                    <fieldset class="scheduler-border" style="margin-top: 10px">
                        <legend class="scheduler-border"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></legend>
                        <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                            <div class="col-sm-12" style="margin-bottom: 0px;margin-top:10px">
                                <div class="col-sm-5">
                                    <select name="glCodeFrom[]" id="search" class="form-control" size="8"
                                            multiple="multiple">
                                        <?php
                                        $glCode="";
                                        if($type == 1){
                                            $glCode = fetch_all_gl_codes_report();
                                        }else{
                                            $glCode = fetch_all_group_gl_codes_report();
                                        }
                                        if (!empty($glCode)) {
                                            foreach ($glCode as $key => $val) {
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
                                    <select name="glCodeTo[]" id="search_to" class="form-control" size="8"
                                            multiple="multiple">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                <?php } ?>
                <fieldset class="scheduler-border" style="margin-top: 10px">
                    <legend class="scheduler-border"><?php echo $this->lang->line('finance_rs_tb_extra_columns');?><!--Extra Columns--></legend>
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
                                        if ($val["fieldID"] == 177 || $val["fieldID"] == 156 || $val["fieldID"] == 158 || $val["isDefault"] == 1) {
                                            $checked = "checked";
                                        }
                                        if($val["fieldID"]!=152 && $val["fieldID"]!=154 && $val["fieldID"]!=155 && $val["fieldID"]!=157){
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
                        <?php echo $this->lang->line('finance_common_put_a_check_mark');?>  <!--Put a check mark next to each column that you want to appear in the report-->
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="margin-top: 10px">
                <button type="button" class="btn btn-primary-new size-sm pull-right"
                        onclick="generateReport('<?php echo $formName; ?>')" name="filtersubmit"
                        id="filtersubmit"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_generate');?><!--Generate-->
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.date').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });
        Inputmask({
            alias: date_format_policy, "oncomplete": function (e) {
                get_finance_date(e.target.value, $('#financeYear').val());
            }
        }).mask(document.querySelectorAll('.asof'));
        //Inputmask({alias: date_format_policy}).mask(document.querySelectorAll('.filterDate'));
        <?php if (!$report) { ?>
        var explode = $('#financeYear').find("option:selected").text().split(' - ');
        var date = explode[1];
        $('#from').val(date.trim());
        get_finance_date($("#from").val(), $('#financeYear').val());
        <?php } ?>
        $('#financeYear').change(function () {
            var explode = $(this).find("option:selected").text().split(' - ');
            var date = explode[1];
            $('#from').val(date.trim());
        });
        $('#search').multiselect({
            search: {
                left: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />',
                right: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />',
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
        $('#segment').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            allSelectedText: 'All Selected',
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#segment").multiselect2('selectAll', false);
        $("#segment").multiselect2('updateButtonText');

    });

</script>