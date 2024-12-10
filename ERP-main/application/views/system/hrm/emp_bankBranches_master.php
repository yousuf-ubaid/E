<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('bank_master_lang', $primaryLanguage);
$this->lang->load('common_lang', $primaryLanguage);
echo head_page($this->lang->line('new_bank_employees_bank_branches'),false);

$bankID = $this->input->post('page_id');
$bankData = bankMasterData($bankID);

$countries =load_country_drop();

/*bankID, bankCode, bankName*/

?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-7 col-xs-4">
            <?php
            if(!empty($bankData)){
                echo '<span style="font-size: 14px; font-weight: bold">'.$bankData['bankCode'] .' | '.$bankData['bankName'].'</span>';
            }
            ?>
        </div>
        <div class="col-md-5 col-xs-3 pull-right">
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="newBank()" ><i class="fa fa-plus-square"></i>&nbsp; Add </button>
        </div>
    </div><hr>
    <div class="table-responsive">
        <table id="emp_bankBranchTB" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('new_bank_employees_bank_branch_code') ?> <!--Branch Code--></th>
                <th style="min-width: 25%"><?php echo $this->lang->line('new_bank_employees_bank_branch_name') ?> <!--Branch Name--></th>
                <th style="min-width: 5%"></th>
            </tr>
            </thead>
        </table>
    </div>
<?php echo footer_page('Right foot','Left foot',false); ?>


<div class="modal fade" id="bankModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title bankMaster-title" id="myModalLabel">New Bank</h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="bankBranchesMaster_form" autocomplete="off"'); ?>
            <div class="modal-body">

                <div class="form-group">
                <label class="col-sm-4 control-label" for="bankName"><?php echo $this->lang->line('bank_code')?> <!--Bank Code--> <?php required_mark(); ?> </label>
                    <div class="col-sm-6">
                        <input type="text" name="bankName" value="<?php echo $bankData['bankCode'] ?>"  class="form-control" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="bankCode"><?php echo $this->lang->line('bank_name')?> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <input type="text" name="bankName" value="<?php echo $bankData['bankName'] ?>"  class="form-control" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="branchCode"><?php echo $this->lang->line('new_bank_employees_bank_branch_code')?> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <input type="text" name="branchCode"  id="branchCode" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="branchName"><?php echo $this->lang->line('new_bank_employees_bank_branch_name')?> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                        <input type="text" name="branchName"  id="branchName" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label" for="countryname"><?php echo $this->lang->line('common_Country')?> <?php required_mark(); ?></label>
                    <div class="col-sm-6">
                    <select name="country" id="country" class="form-control country" required >
                                <option value=""  disabled selected> Select a country</option>
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?php echo $country['countryID']; ?>"><?php echo $country['CountryDes']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="hiddenID"  id="hiddenID" />
            <button type="submit" class="btn btn-primary btn-sm" id="saveBtn" ><?php echo $this->lang->line('common_save')?> </button>
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close')?> </button>
            </div>
            <?php echo form_close();?>
        </div>
    </div>
</div>



<script type="text/javascript">
    var bankBranchesMaster_form = $('#bankBranchesMaster_form');
    var hiddenID = $('#hiddenID');

    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/emp_bank_master', '<?php echo $bankID; ?>', 'HRMS');
        });

        $('#country').select2();

        bankBranchesMaster_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                branchCode: {validators: {notEmpty: {message: 'Branch code is required.'}}},
                branchName: {validators: {notEmpty: {message: 'Branch name is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form      = $(e.target);
            var bv         = $form.data('bootstrapValidator');

            var requestUrl = $form.attr('action');
            var postData = $form.serializeArray();
            postData.push({"name": "bankID", "value": '<?php echo $bankID; ?>'});
            $.ajax({
                type: 'post',
                url: requestUrl,
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#bankModal').modal('hide');
                        emp_bankBranchTB(data[2]);
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                }
            });


        });

        emp_bankBranchTB();
    });

    function emp_bankBranchTB(selectedRowID=null){
         $('#emp_bankBranchTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_empBankBranches'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {

                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if( parseInt(oSettings.aoData[x]._aData['branchID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
            },
            "aoColumns": [
                {"mData": "branchID"},
                {"mData": "branchCode"},
                {"mData": "branchName"},
                {"mData": "edit"}
            ],
             "columnDefs": [ {
                 "targets": [0,3],
                 "orderable": false
             } ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "bankID", "value": '<?php echo $bankID; ?>'});
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

    function newBank(){
        $('.bankMaster-title').text('<?php echo $this->lang->line('new_bank_employees_bank_new_branch_name') ?>');
        bankBranchesMaster_form[0].reset();
        bankBranchesMaster_form.attr('action', '<?php echo site_url('Employee/save_empBranchBank'); ?>');

        bankBranchesMaster_form.bootstrapValidator('resetForm', true);
        hiddenID.val('');
        $('#bankModal').modal({backdrop: "static"});
    }

    function edit_empBranchBank(obj){
        $('.bankMaster-title').text('<?php echo $this->lang->line('new_bank_employees_update_branch_details') ?>');
        hiddenID.val('');
        bankBranchesMaster_form[0].reset();
        bankBranchesMaster_form.attr('action', '<?php echo site_url('Employee/update_empBranchBank'); ?>');
        bankBranchesMaster_form.bootstrapValidator('resetForm', true);

        var details = getTableRowData(obj);

        $('#branchCode').val( $.trim(details.branchCode ) );
        $('#swiftCode').val( $.trim(details.swiftCode ) );
        $('#branchName').val( $.trim(details.branchName) );
        $('#country').val( $.trim(details.country) ).change();
        $('#hiddenID').val(  $.trim(details.branchID) );

        $('#bankModal').modal({backdrop: "static"});
    }

    function delete_empBranchBank(obj){
        var details = getTableRowData(obj);

        swal(
            {
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async : true,
                    url :"<?php echo site_url('Employee/delete_empBranchBank'); ?>",
                    type : 'post',
                    dataType : 'json',
                    data : {'hiddenID': details.branchID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if( data[0] == 's'){ emp_bankBranchTB() }
                    },error : function(){
                        stopLoad();
                        myAlert('e', 'error');

                    }
                });
            }
        );
    }


    function getTableRowData(obj){
        var table = $('#emp_bankBranchTB').DataTable();
        var thisRow = $(obj);
        return table.row(thisRow.parents('tr')).data();
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>



<?php
