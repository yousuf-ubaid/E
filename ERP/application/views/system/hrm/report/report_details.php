<?php
$postData = $this->input->post('data_arr');
$title = $postData[0];
echo head_page($title .' - Configuration', false);

$reportID = $this->input->post('page_id');
$reportData = get_ssoReportConfigData($reportID);
$companyLevel = $reportData['companyLevel'];
$empLevel = $reportData['empLevel'];
$SSO_arr = $reportData['SSO_arr'];
$payGroup_arr = $reportData['payGroup_arr'];
$empLevelConfig = $reportData['empLevelConfig'];
$epf_shortOrder = $reportData['epf_shortOrder'];

//echo '<pre>'; print_r($empLevelConfig); echo '</pre>';

?>

<style type="text/css">
    .config-text{
        height: 25px !important;
        font-size: 11px;
        padding: 2px 4px;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
    <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
        <li class="active">
            <a href="#companyLevelConf" id="" data-toggle="tab" aria-expanded="true">Company level</a>
        </li>
        <li class="">
            <a href="#shortOrderConf" id="" data-toggle="tab" aria-expanded="false">Short Order & String Length</a>
        </li>
    </ul>
    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">

        <div class="tab-pane active disabled" id="companyLevelConf"> <!-- Start of companyLevelConf -->
            <div class="row">
                <form id="companyLevelConf_form">
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
                                            $ssoID = $sso_row['socialInsuranceID'];
                                            $selected = ( $reportValue == $ssoID ) ? 'selected' : '';
                                            $input .= '<option value="'.$ssoID.'" '.$selected.'>'.$sso_row['Description'].'</option>';
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
                        <input type="hidden" name="masterID" value="<?php echo $reportID;?>">
                        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="save_companyLevelReportDetails()">Save</button>
                    </div>
                </form>
            </div>
        </div> <!-- End of companyLevelConf -->


        <div class="tab-pane" id="shortOrderConf"> <!-- Start of shortOrder  -->
            <div class="row">
                <form id="shortOrderConf_form">
                    <div class="table-responsive">
                        <table class="<?php echo table_class(); ?>" id="" style="margin-top: -1px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Description</th>
                                <th>Short Order</th>
                                <th>Length</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php
                            if(!empty($epf_shortOrder)){
                                foreach($epf_shortOrder as $key=>$rowShort){

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
                        <input type="hidden" name="masterID" value="<?php echo $reportID;?>">
                        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="save_epfReportOtherConfig()">Save</button>
                    </div>
                </form>
            </div>
        </div> <!-- End of shortOrder  -->
    </div>
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

    function save_companyLevelReportDetails(){
        var companyLevelConf_form = $('#companyLevelConf_form');
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

    function save_empLevelReportDetails(){
        var postData = $('#employeeLevelConf_form').serializeArray();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: postData,
            url: '<?php echo site_url('Report/save_employeeLevelReportDetails'); ?>',
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

    function save_epfReportOtherConfig(){
        var postData = $('#shortOrderConf_form').serializeArray();
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
