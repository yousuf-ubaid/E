<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_reports_report_master');
echo head_page($title  , false);


$payee_arr = payee_drop();
$payGroup_arr = payGroup_drop('Y');
$defaultPayeeValues = get_defaultPayeeSetup();
$sso_drop = sso_drop('Y');

if(!empty($defaultPayeeValues)){
    $defaultPayeeID = $defaultPayeeValues['payee'];
    $defaultCashBenefitID = $defaultPayeeValues['payGroup'];
    $regNo = $defaultPayeeValues['regNo'];
}
else{
    $defaultPayeeID = 0;
    $defaultCashBenefitID = 0;
    $regNo = 0;
}

$defaultSSOValues = get_defaultSSOSetup();
$sso_employee = 0; $sso_employer = 0;
if(!empty($defaultSSOValues)){
    $sso_employee = $defaultSSOValues['sso_employee'];
    $sso_employer = $defaultSSOValues['sso_employer'];
}

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
    <div class="col-md-5">&nbsp;</div>
    <div class="col-md-2 pull-right">
        <button type="button" onclick="open_model()" class="btn btn-primary btn-sm pull-right" >
            <i class="fa fa-cogs"></i><?php echo $this->lang->line('hrms_reports_employee_configuration');?><!--Employee Configuration-->
        </button>
    </div>
</div>

<hr>
<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive">
            <table id="reportMasterTB" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 50%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
                    <th style="min-width: 5%"></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="empConfig_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('hrms_reports_employee_configuration');?><!--Employee Configuration--></h3>
            </div>
            <div class="modal-body">
                <div id="ajax-content">

                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="masterID" id="masterID" value="1">
                <button type="button" class="btn btn-primary btn-sm" onclick="save_empLevelReportDetails()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="payeeConfig_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('hrms_reports_payee_configurations');?><!--Payee Configuration--></h3>
            </div>
            <form class="form-horizontal" id="payeConfig_form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="payGroupID"  class="control-label col-sm-4"><?php echo $this->lang->line('common_payee');?><!--Payee--><?php  required_mark(); ?></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('payeeID', $payee_arr, $defaultPayeeID,'class="form-control" id="payeeID" required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cashBenefits"  class="control-label col-sm-4"><?php echo $this->lang->line('hrms_reports_cash_benefits');?><!--Cash Benefits--><?php  required_mark(); ?></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('cashBenefits', $payGroup_arr, $defaultCashBenefitID,'class="form-control" id="cashBenefits" required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="regNo"  class="control-label col-sm-4">PAYE Registration No<?php  required_mark(); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="regNo" id="regNo" class="form-control" value="<?php echo $regNo; ?>" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="bnkDetSaveBtn" onclick="save_payeeSetup()">
                        <?php echo $this->lang->line('common_save');?><!--Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="SSO_Config_model" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('hrms_reports_sso_configurations');?><!--SSO configurations--> </h3>
            </div>
            <form class="form-horizontal" id="ssoConfig_form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="payGroupID"  class="control-label col-sm-4"><?php echo $this->lang->line('common_employee');?><!--Employee--> <?php  required_mark(); ?></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('sso_employee', $sso_drop, $sso_employee, 'class="form-control" id="sso_employee" required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cashBenefits"  class="control-label col-sm-4"><?php echo $this->lang->line('common_employer');?><!--Employer--> <?php  required_mark(); ?></label>
                        <div class="col-sm-6">
                            <?php echo form_dropdown('sso_employer', $sso_drop, $sso_employer, 'class="form-control" id="sso_employer" required'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="bnkDetSaveBtn" onclick="save_sso_Setup()">
                        <?php echo $this->lang->line('common_save');?><!--Save-->
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

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
        load_reportMaster();
    });

    function open_model(){
        $.ajax({
            type: 'post',
            dataType: 'html',
            url: '<?php echo site_url('Report/employee_config_view'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#ajax-content').html(data);
                $('#empConfig_model').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_reportMaster(selectedID=null) {
        var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
        $('#reportMasterTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Report/fetch_reportMaster'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if (parseInt(oSettings.aoData[x]._aData['shiftID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "description"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function reportDetails(id, reportCode){
        switch(reportCode){
            case 'payee': openPayeeSetup_model(); break;
            case 'sc':
                fetchPage('system/hrm/report/salary-comparison-report-config', id, 'Salary comparison', '');
            break;
            case 'sso': openSSOSetup_model(); break;
            //case 'slips': fetchPage('system/hrm/report/interbank-payment-system-config', id, 'Salary Comparison Config', ''); break;
            /*As discussed with Hisahm slips configuration is no need*/
            default: //EPF / ETF
                fetchPage('system/hrm/report/'+reportCode+'_report_details', id, reportCode+' Master', '');
        }

    }

    function openSSOSetup_model(){
        $('#SSO_Config_model').modal('show');
    }

    function save_sso_Setup(){
        var sso_employee = $('#sso_employee').val();
        var sso_employer = $('#sso_employer').val();

        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Report/save_sso_setup') ?>',
            data: {'sso_employee' : sso_employee, 'sso_employer': sso_employer},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's' ){
                    $('#SSO_Config_model').modal('hide');
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function openPayeeSetup_model(){
        $('#payeeConfig_model').modal('show');
    }

    function save_payeeSetup(){
        var payeeID = $('#payeeID').val();
        var cashBenefits = $('#cashBenefits').val();
        var regNo = $('#regNo').val();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Report/save_payeeSetUp') ?>',
            data: {'payeeID' : payeeID, 'cashBenefits': cashBenefits, 'regNo' : regNo},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's' ){
                    $('#payeeConfig_model').modal('hide');
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>
