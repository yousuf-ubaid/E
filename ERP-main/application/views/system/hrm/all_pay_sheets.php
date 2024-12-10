
<!--Translation added by Naseek-->

<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_paysheets');
echo head_page($title, false);

?>
<style type="text/css">

</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="table table-bordered table-striped table-condensed ">
            <tbody><tr>
                <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> /
                    <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed-->
                    /<?php echo $this->lang->line('common_not_approved');?><!-- Not Approved-->
                </td>
                <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?><!--Refer-back-->
                </td>
            </tr>
            </tbody></table>
    </div>
    <div class="col-md-3 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="new_payroll()" >
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('hrms_payroll_create_payroll');?><!--Create Payroll-->
        </button>
    </div>
</div><hr>
<div class="col-sm-12 table-responsive">
    <table class="<?php echo table_class(); ?>"  id="paySheetsTB" style="margin-top: 1%">
        <thead>
            <tr>
                <th style="width: auto"> # </th>
                <th style="width: auto"> <?php echo $this->lang->line('common_code');?><!--Code--> </th>
                <th style="width: auto"> <?php echo $this->lang->line('hrms_payroll_payroll_month');?><!--Payroll Month--> </th>
                <th style="width: auto"> <?php echo $this->lang->line('hrms_payroll_narration');?><!--Narration--> </th>
                <th style="width: auto"> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed-->  </th>
                <th style="width: auto"> <?php echo $this->lang->line('common_approved');?><!--Approved--> </th>
                <th style="width: 70px">  </th>
            </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<script type="text/javascript">
    $(document).ready(function(){
        $('.headerclose').click(function(){
            fetchPage('system/hrm/all_pay_sheets','Test','HRMS');
        });
        load_paySheets();
    });

    function load_paySheets(selectedID=null){
        var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
        var Otable = $('#paySheetsTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Template_paysheet/fetch_paySheets'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {


                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if( parseInt(oSettings.aoData[x]._aData['payrollMasterID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
            },
            "aoColumns": [
                {"mData": "payrollMasterID"},
                {"mData": "documentCode"},
                {"mData": "payrollMonth"},
                {"mData": "narration"},
                {"mData": "confirm"},
                {"mData": "approve"},
                {"mData": "action"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0,4,5,6]}],
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

    function new_payroll(){
        fetchPage('system/hrm/pay_sheet','Test','HRMS');
    }

    function payroll_details(fetchType, loanID){
        var $loanDet = [fetchType, loanID];
        fetchPage('system/hrm/pay_sheet',0,'Load', '', $loanDet);
    }

    function payroll_delete(delID){

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('hrms_payroll_you_want_to_delete_payroll');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'delID' : delID ,'isNonPayroll' : 'N'},
                    url: "<?php echo site_url('Template_paysheet/payroll_delete'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            load_paySheets();
                        }
                    },
                    error : function() {
                        stopLoad();
                        myAlert('e','An Error Occurred! Please Try Again.');
                    }
                });
            }
        );
    }

    function referBackConformation(payrollID, details){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>  [ "+details+" ]",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_refer_back');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"

            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: { 'referID': payrollID },
                    url: "<?php echo site_url('Template_paysheet/referBackPayroll'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){
                            load_paySheets();
                        }
                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        );
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

</script>



<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-07-14
 * Time: 2:43 PM
 */