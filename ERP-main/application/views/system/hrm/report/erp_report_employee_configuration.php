<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$empLevel = get_ssoReportFields('E');
$epf_empLevelConfig = get_ssoEmpLevelConfig(1);
$etf_empLevelConfig = get_ssoEmpLevelConfig(2);

?>

<style type="text/css">
    .config-text{
        height: 25px !important;
        font-size: 11px;
        padding: 2px 4px;
    }
</style>

<div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
    <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
        <li class="active">
            <a href="#emp-EPF-conf" id="" data-toggle="tab" aria-expanded="true" onclick="setMasterID(1)">EPF <?php echo $this->lang->line('hrms_reports_configurations') ?><!--Configuration--></a>
        </li>
        <li class="">
            <a href="#emp-ETF-conf" id="" data-toggle="tab" aria-expanded="false" onclick="setMasterID(2)">ETF <?php echo $this->lang->line('hrms_reports_configurations') ?><!--Configuration--></a>
        </li>
    </ul>
    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">

        <div class="tab-pane active disabled" id="emp-EPF-conf"> <!-- Start of emp-EPF-conf -->
            <div class="row">
                <form id="epf_employeeLevelConf_form">
                    <div class="table-responsive">
                        <div class="fixHeader_Div" style="max-width: 100%; height: 400px">
                            <table class="<?php echo table_class(); ?>" id="epf_employeeLevelConf_table" style="margin-top: -1px">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>E <?php echo $this->lang->line('common_code') ?><!--E-code--></th>
                                    <th><?php echo $this->lang->line('common_name') ?><!--Name--></th>
                                    <?php
                                    if(!empty($empLevel)) {
                                        foreach ($empLevel as $key => $row) {
                                            echo '<th>'.$row['description'].'</th>';
                                        }
                                    }
                                    ?>
                                </tr>
                                </thead>

                                <tbody>
                                <?php
                                if(!empty($epf_empLevelConfig)){
                                    foreach($epf_empLevelConfig as $key=>$rowEmp){

                                        $columnValue = $rowEmp['columnValue'];
                                        $column = '';

                                        //if($key > 1){ continue; }
                                        foreach($columnValue as $keyCol=>$valColRow){
                                            $column .= '<td>';
                                            $column .= '<input type="text" name="'.$keyCol.'[]" value="'.$valColRow.'" class="form-control config-text"/>';
                                            $column .= '</td>';
                                        }

                                        echo '<tr>
                                                <td>'.($key+1).' <input type="hidden" name="empID[]" value="'.$rowEmp['empID'].'"/></td>
                                                <td>'.$rowEmp['eCode'].'</td>
                                                <td>'.$rowEmp['eName'].'</td>
                                                '.$column.'
                                              </tr>';
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- End of emp-EPF-conf  -->

        <div class="tab-pane" id="emp-ETF-conf"> <!-- Start of emp-ETF-conf  -->
            <div class="row">
                <form id="etf_employeeLevelConf_form">
                    <div class="table-responsive">
                        <div class="fixHeader_Div" style="max-width: 100%; height: 400px">
                            <table class="<?php echo table_class(); ?>" id="etf_employeeLevelConf_table" style="margin-top: -1px">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>E <?php echo $this->lang->line('common_code') ?><!--E-code--></th>
                                    <th><?php echo $this->lang->line('common_name') ?><!--Name--></th>
                                    <?php
                                    if(!empty($empLevel)) {
                                        foreach ($empLevel as $key => $row) {
                                            echo '<th>'.$row['description'].'</th>';
                                        }
                                    }
                                    ?>
                                </tr>
                                </thead>

                                <tbody>
                                <?php
                                if(!empty($etf_empLevelConfig)){
                                    foreach($etf_empLevelConfig as $key=>$rowEmp){

                                        $columnValue = $rowEmp['columnValue'];
                                        $column = '';

                                        //if($key > 1){ continue; }
                                        foreach($columnValue as $keyCol=>$valColRow){
                                            $column .= '<td>';
                                            $column .= '<input type="text" name="'.$keyCol.'[]" value="'.$valColRow.'" class="form-control config-text"/>';
                                            $column .= '</td>';
                                        }

                                        echo '<tr>
                                                <td>'.($key+1).' <input type="hidden" name="empID[]" value="'.$rowEmp['empID'].'"/></td>
                                                <td>'.$rowEmp['eCode'].'</td>
                                                <td>'.$rowEmp['eName'].'</td>
                                                '.$column.'
                                              </tr>';
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div> <!-- End of emp-ETF-conf  -->
    </div>
</div>


<script>
    $(document).ready(function (e) {
        $('#epf_employeeLevelConf_table, #etf_employeeLevelConf_table').tableHeadFixer({
            head: true,
            foot: true,
            left: 0,
            right: 0,
            'z-index': 0
        });
    });

    function save_empLevelReportDetails(){
        var masterID = $('#masterID').val();
        var formID = ( masterID == 1 )? $('#epf_employeeLevelConf_form'): $('#etf_employeeLevelConf_form');
        var postData = formID.serializeArray();
        postData.push({'name':'masterID', 'value':masterID});

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
                if(data[0] == 's'){
                    $('#empConfig_model').modal('hide');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function setMasterID(ssoType){
        $('#masterID').val(ssoType); // EPF => 1 | ETF => 2
    }
</script>
