

<!--Translation added by Naseek-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_loan', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_loan_employee_loan');
echo head_page($title  , false);

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
                    / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>
                <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?><!--Refer-back-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-3 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="new_loanCreate()" >
            <i class="fa fa-plus"></i>  <?php echo $this->lang->line('hrms_loan_create_loan');?><!--Create Loan-->
        </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="loanDetailTB" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('hrms_loan_number');?><!--Loan No--></th>
            <th style="min-width: 25%"><?php echo $this->lang->line('hrms_loan_employee_name');?><!--Employee Name--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('hrms_loan_type');?><!--Loan Type--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('hrms_loan_amount');?><!--Loan Amount--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('hrms_loan_date');?><!--Loan Date--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
            <th style="min-width: 6%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>




<div id="loanSetupDet"></div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/loan/emp_loan','Test','HRMS');
        });
        fetchEmployeeLoan();
    });

    function fetchEmployeeLoan(selectedID=null){
        var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
        var Otable = $('#loanDetailTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Loan/fetch_employee_loan'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                /*if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);

                        if( parseInt(oSettings.aoData[i]._aData['ID']) == selectedRowID ){
                            var thisRow = oSettings.aoData[oSettings.aiDisplay[i]].nTr;
                            $(thisRow).addClass('dataTable_selectedTr');
                        }
                    }
                }*/


                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if( parseInt(oSettings.aoData[x]._aData['ID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }

            },
            "aoColumns": [
                {"mData": "ID"},
                {"mData": "loanCode"},
                {"mData": "Employee"},
                {"mData": "description"},
                {"mData": "amount"},
                {"mData": "loanDate"},
                {"mData": "confirm"},
                {"mData": "approved"},
                {"mData": "action"},
            ],
            "columnDefs": [{"searchable": false, "targets": [0,6,7]}],
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

    function new_loanCreate(){
        fetchPage('system/loan/emp_loan_create','Test','HRMS');
    }

    function emp_loan_details(fetchType, loanID){
        var $loanDet = [fetchType, loanID];
        fetchPage('system/loan/emp_loan_create',0,'Employee Loan Create','', $loanDet);
    }

    function emp_loan_delete(delID, loanCode){
        swal(
            {


                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'delID' : delID},
                    url: "<?php echo site_url('Loan/emp_loan_delete'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            fetchEmployeeLoan();
                        }
                    },
                    error : function() {
                        stopLoad();
                        myAlert('e','<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    }
                });
            }
        );
    }

    function referBackConformation(id, loanCode){
        swal(
            {



                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?> [ "+loanCode+" ]",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_refer_back');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"


            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'id' : id},
                    url: "<?php echo site_url('Loan/loan_referBack'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            fetchEmployeeLoan(id);
                        }
                    },
                    error : function() {
                        stopLoad();
                        myAlert('e','<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
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
 * Date: 5/24/2016
 * Time: 4:15 PM
 */


