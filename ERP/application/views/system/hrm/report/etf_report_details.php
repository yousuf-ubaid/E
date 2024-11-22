<?php

echo head_page('ETF Report Configuration', false);

$reportID = $this->input->post('page_id');
$reportData = get_ssoReportConfigData('ETF');
$companyLevel = $reportData['companyLevel'];
$SSO_arr = $reportData['SSO_arr'];
$payGroup_arr = $reportData['payGroup_arr'];
$etf_shortOrder = $reportData['shortOrder'];
$etfH_companyConfig = get_ssoReportFields('C', 'ETF-H');
$etfH_shortOrder = ssoReport_shortOrder('ETF-H');
//echo '<pre>'; print_r($etf_shortOrder); echo '</pre>';

?>

<style type="text/css">
    .config-text{
        height: 25px !important;
        font-size: 11px;
        padding: 2px 4px;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-sm-6"><!--Header config start-->
        <h4 style="border-bottom: 1px solid #333;">Header Config</h4>
        <div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
            <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
                <li class="active">
                    <a href="#companyLevelConf_header" id="" data-toggle="tab" aria-expanded="true">Company level</a>
                </li>
                <li class="">
                    <a href="#shortOrderConf_header" id="" data-toggle="tab" aria-expanded="false">Sort Order & String Length</a>
                </li>
            </ul>
            <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">
                <div class="tab-pane active disabled" id="companyLevelConf_header"> <!-- Start of companyLevelConf_header -->
                    <div class="row">
                        <form id="companyLevelConf_header_form">
                            <div class="table-responsive">
                                <table class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th>Value</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    if(!empty($etfH_companyConfig)){
                                        foreach($etfH_companyConfig as $key=>$row){
                                            $masterTable = $row['masterTable'];
                                            //$inputName = 'value[]'; // 'fieldsValue[]';
                                            $inputName = $row['inputName']; // 'fieldsValue[]';
                                            $input = '';
                                            $reportValue = trim($row['reportValue'] ?? '');

                                            if($masterTable == 'SSO'){
                                                $input = '<select name="'.$inputName.'" class="form-control config-text">';
                                                $input .= '<option></option>';
                                                foreach($SSO_arr as $sso_row){
                                                    $ssoID = $sso_row['payGroupID'];
                                                    $selected = ( $reportValue == $ssoID ) ? 'selected' : '';
                                                    $input .= '<option value="'.$ssoID.'" '.$selected.'>'.$sso_row['titleDes'].'</option>';
                                                }
                                                $input .= '</select>';
                                            }
                                            else if($masterTable == 'payGroup'){
                                                $input = '<select name="'.$inputName.'" class="form-control config-text">';
                                                $input .= '<option></option>';
                                                foreach($payGroup_arr as $payGroup_row){
                                                    $payGroupID = $payGroup_row['payGroupID'];
                                                    $selected = ( $reportValue == $payGroupID ) ? 'selected' : '';
                                                    $input .= '<option value="'.$payGroupID.'" '.$selected.'>'.$payGroup_row['description'].'</option>';
                                                }
                                                $input .= '</select>';
                                            }
                                            else{
                                                $input = '<input type="text" name="'.$inputName.'" value="'.$reportValue.'" class="form-control config-text"/>';
                                            }

                                            $input .= '<input type="hidden" name="fieldsID[]" value="'.$row['id'].'" />';
                                            $input .= '<input type="hidden" name="columnName[]" value="'.$row['inputName'].'" />';

                                            echo '<tr>
                                            <td>'.($key+1).'</td>
                                            <td>'.$row['description'].'</td>
                                            <td>'.$input.'</td>
                                          </tr>';
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <div class="clearfix">&nbsp;</div>
                                <input type="hidden" name="masterID" value="3">
                                <input type="hidden" name="reportType" value="ETF-H">
                                <button type="button" class="btn btn-primary btn-sm pull-right"
                                        onclick="save_companyLevelReportDetails('companyLevelConf_header_form')">Save</button>
                            </div>
                        </form>
                    </div>
                </div> <!-- End of companyLevelConf_header -->

                <div class="tab-pane" id="shortOrderConf_header"> <!-- Start of shortOrder  -->
                    <div class="row">
                        <form id="shortOrderConf_header_form">
                            <div class="table-responsive">
                                <table class="<?php echo table_class(); ?>" id="" style="margin-top: -1px">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th>Sort Order</th>
                                        <th>Length</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    if(!empty($etfH_shortOrder)){
                                        foreach($etfH_shortOrder as $key=>$rowShort){

                                            $shortOrder = $rowShort['shortOrder'];
                                            $length = $rowShort['strLength'];

                                            /* shortOrder, fieldName, strLength, isLeft_strPad, description */

                                            echo '<tr>
                                        <td>'.($key+1).' <input type="hidden" name="reportID[]" value="'.$rowShort['id'].'"/></td>
                                        <td>'.$rowShort['description'].'</td>
                                        <td><input type="number" name="shortOrder[]" value="'.$shortOrder.'" class="form-control config-text number"/></td>
                                        <td><input type="number" name="strLength[]" value="'.$length.'" class="form-control config-text number"/></td>
                                      </tr>';
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <div class="clearfix">&nbsp;</div>
                                <input type="hidden" name="masterID" value="3">
                                <input type="hidden" name="reportType" value="ETF-H">
                                <button type="button" class="btn btn-primary btn-sm pull-right"
                                        onclick="save_epfReportOtherConfig('shortOrderConf_header_form')">Save</button>
                            </div>
                        </form>
                    </div>
                </div> <!-- End of shortOrder  -->
            </div>
        </div>
    </div><!--Header config end-->

    <div class="col-sm-6"><!--Detail config start-->
        <h4 style="border-bottom: 1px solid #333;">Detail Config</h4>
        <div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
            <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
                <li class="active">
                    <a href="#companyLevelConf_det" id="" data-toggle="tab" aria-expanded="true">Company level</a>
                </li>
                <li class="">
                    <a href="#shortOrderConf_det" id="" data-toggle="tab" aria-expanded="false">Sort Order & String Length</a>
                </li>
            </ul>
            <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">

                <div class="tab-pane active disabled" id="companyLevelConf_det"> <!-- Start of companyLevelConf_det -->
                    <div class="row">
                        <form id="companyLevelConf_det_form">
                            <div class="table-responsive">
                                <table class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th>Value</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    if(!empty($companyLevel)){
                                        foreach($companyLevel as $key=>$row){
                                            $masterTable = $row['masterTable'];
                                            //$inputName = 'value[]'; // 'fieldsValue[]';
                                            $inputName = $row['inputName']; // 'fieldsValue[]';
                                            $input = '';
                                            $reportValue = trim($row['reportValue'] ?? '');

                                            if($masterTable == 'SSO'){
                                                $input = '<select name="'.$inputName.'" class="form-control config-text">';
                                                $input .= '<option></option>';
                                                foreach($SSO_arr as $sso_row){
                                                    $ssoID = $sso_row['payGroupID'];
                                                    $selected = ( $reportValue == $ssoID ) ? 'selected' : '';
                                                    $input .= '<option value="'.$ssoID.'" '.$selected.'>'.$sso_row['titleDes'].'</option>';
                                                }
                                                $input .= '</select>';
                                            }
                                            else if($masterTable == 'payGroup'){
                                                $input = '<select name="'.$inputName.'" class="form-control config-text">';
                                                $input .= '<option></option>';
                                                foreach($payGroup_arr as $payGroup_row){
                                                    $payGroupID = $payGroup_row['payGroupID'];
                                                    $selected = ( $reportValue == $payGroupID ) ? 'selected' : '';
                                                    $input .= '<option value="'.$payGroupID.'" '.$selected.'>'.$payGroup_row['description'].'</option>';
                                                }
                                                $input .= '</select>';
                                            }
                                            else{
                                                $input = '<input type="text" name="'.$inputName.'" value="'.$reportValue.'" class="form-control config-text"/>';
                                            }

                                            $input .= '<input type="hidden" name="fieldsID[]" value="'.$row['id'].'" />';
                                            $input .= '<input type="hidden" name="columnName[]" value="'.$row['inputName'].'" />';

                                            echo '<tr>
                                            <td>'.($key+1).'</td>
                                            <td>'.$row['description'].'</td>
                                            <td>'.$input.'</td>
                                          </tr>';
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <div class="clearfix">&nbsp;</div>
                                <input type="hidden" name="masterID" value="2">
                                <input type="hidden" name="reportType" value="ETF">
                                <button type="button" class="btn btn-primary btn-sm pull-right"
                                        onclick="save_companyLevelReportDetails('companyLevelConf_det_form')">Save</button>
                            </div>
                        </form>
                    </div>
                </div> <!-- End of companyLevelConf -->


                <div class="tab-pane" id="shortOrderConf_det"> <!-- Start of shortOrder  -->
                    <div class="row">
                        <form id="shortOrderConf_det_form">
                            <div class="table-responsive">
                                <table class="<?php echo table_class(); ?>" id="" style="margin-top: -1px">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th>Sort Order</th>
                                        <th>Length</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    if(!empty($etf_shortOrder)){
                                        foreach($etf_shortOrder as $key=>$rowShort){

                                            $shortOrder = $rowShort['shortOrder'];
                                            $length = $rowShort['strLength'];

                                            /* shortOrder, fieldName, strLength, isLeft_strPad, description */

                                            echo '<tr>
                                        <td>'.($key+1).' <input type="hidden" name="reportID[]" value="'.$rowShort['id'].'"/></td>
                                        <td>'.$rowShort['description'].'</td>
                                        <td><input type="number" name="shortOrder[]" value="'.$shortOrder.'" class="form-control config-text number"/></td>
                                        <td><input type="number" name="strLength[]" value="'.$length.'" class="form-control config-text number"/></td>
                                      </tr>';
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <div class="clearfix">&nbsp;</div>
                                <input type="hidden" name="masterID" value="2">
                                <input type="hidden" name="reportType" value="ETF">
                                <button type="button" class="btn btn-primary btn-sm pull-right"
                                        onclick="save_epfReportOtherConfig('shortOrderConf_det_form')">Save</button>
                            </div>
                        </form>
                    </div>
                </div> <!-- End of shortOrder  -->
            </div>
        </div>
    </div><!--Detail config end-->
</div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    $('.headerclose').click(function () {
        fetchPage('system/hrm/report/report_master', '', 'Reports Master');
    });

    $(document).ready(function (e) {
        $('#employeeLevelConf_table').tableHeadFixer({
            head: true,
            foot: true,
            left: 0,
            right: 0,
            'z-index': 0
        });
    });

    function save_companyLevelReportDetails(formName){
        var companyLevelConf_form = $('#'+formName);
        var postData = companyLevelConf_form.serializeArray();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: postData,
            url: '<?php echo site_url('Report/save_companyLevelReportDetails'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function save_epfReportOtherConfig(formName){
        var postData = $('#'+formName).serializeArray();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: postData,
            url: '<?php echo site_url('Report/save_epfReportOtherConfig'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
</script>


<?php
