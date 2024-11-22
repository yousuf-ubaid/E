<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_leave_management_lang', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_leave_encashment_salary');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$doc_type_arr = [
    1 => 'Leave Encashment',
    2 => 'Leave Salary'
];
$csrf = get_csrf_token_data();
$companyBanks = company_bank_account_drop(1);

echo head_page($title, false);
?>

<style>
    .select2-dropdown--below{
        z-index: 1000000002 !important;
    }

    #toast-container {
        z-index: 1000000003 !important;
    }
</style>

<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> /
                    <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span><?php echo $this->lang->line('common_not_confirmed');?><!-- Not Confirmed-->
                    / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-3 text-center">&nbsp;</div>
    <div class="col-md-3 text-center">
        <form class="form-inline pull-right">
            <div class="form-group" style="margin-right: 20px;">
                <label for="filterType"><?php echo $this->lang->line('common_type');?> &nbsp;</label>
                <select name="filterType" id="filterType" class="form-control" onchange="load_leave_encashment_data()">
                    <option value="" selected>All</option>
                    <option value="1">Leave Encashment</option>
                    <option value="2">Leave Salary</option>
                </select>
            </div>
        </form>
    </div>
    <div class="col-md-1 text-right">
        <button type="button" class="btn btn-primary pull-right btn-sm" onclick="add_encashment()">
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_new');?><!--New-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="leave_encashment_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 3%">#</th>
            <th style="min-width: 8%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?><!--Document Code--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_type');?></th>
            <th style="min-width: 4%"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <th style="min-width: 25%"><?php echo $this->lang->line('common_narration');?><!--Description--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<form id="print_form" method="post" action="" target="_blank">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" id="print_master_id" name="masterID">
</form>

<div class="modal fade" id="leave_encashment_modal" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $title;?></h3>
            </div>
            <form role="form" id="leave_encashment_form" class="form-horizontal" autocomplete="off" action="#">
                <div class="modal-body" >

                    <div class="row" style="">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_date');?><!--Date--></label>
                            <div class="col-sm-3">
                                <div class="input-group date_pic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="doc_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="doc_date"
                                           class="form-control" required value="<?php echo $current_date ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_type');?></label>
                            <div class="col-sm-4">
                                <?php echo form_dropdown('doc_type', $doc_type_arr, '', 'class="form-control select2" id="doc_type" required'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_currency');?></label>
                            <div class="col-sm-4">
                                <?php echo form_dropdown('currencyID', $currency_arr, '', 'class="form-control select2" id="currencyID" required'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_narration');?></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" id="narration" name="narration" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm submitBtn"><?php echo $this->lang->line('common_save');?></button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="leave_en_bank_transfer_modal" role="dialog" data-keyboard="false" data-backdrop="static" style="z-index: 1000000001;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_bank_transfer');?></h4>
            </div>
            <div class="modal-body">
                <form class="" role="form" id="fnBankTransfer_form" autocomplete="off">
                    <input type="hidden" name="autoID" id="autoID" value="">
                    <div class="row">
                        <div class="form-group col-sm-3 col-xs-6">
                            <label class=""><?php echo $this->lang->line('common_transfer_date');?></label>
                            <div class="input-group date_pic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="transDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="transDate" class="form-control date_picker" >
                            </div>
                        </div>

                        <div class="form-group col-sm-3 col-xs-6">
                            <label class=""><?php echo $this->lang->line('common_amount');?></label>
                            <input type="text" name="transfer_amount" id="transfer_amount" class="form-control number" disabled>
                        </div>

                        <div class="form-group col-sm-6 col-xs-6">
                            <label class=""><?php echo $this->lang->line('common__bank_or_cash');?></label>
                            <select name="accountID" id="accountID" class="form-control select-box">
                                <option value="">Select Bank Account</option>
                                <?php
                                foreach($companyBanks as $key=>$row){
                                    $type = ($row['isCash'] == '1') ? ' | Cash' : ' | Bank';
                                    $des = trim($row['bankName'] ?? '') . ' | ' . trim($row['bankBranch'] ?? '') . ' | ' . trim($row['bankSwiftCode'] ?? '') . ' | ' . trim($row['bankAccountNumber'] ?? '') . ' | ' . trim($row['subCategory'] ?? '') . $type;
                                    echo '<option value="'.$row['GLAutoID'].'" data-type="'.$row['isCash'].'"> '.$des.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_bankTransfer()"><?php echo $this->lang->line('common_proceed');?><!--Save--></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var leave_encashment_tb = '';
    $('#currencyID, #doc_type, #accountID').select2();

    var filterType = window.localStorage.getItem('leave-en-cash-filter');
    $("#filterType").val(filterType);

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/leave-encashment','','HRMS');
        });

        load_leave_encashment_data();

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.date_pic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        }).on('dp.change', function (ev) {
            $('#leave_encashment_form').bootstrapValidator('revalidateField', 'doc_date');
        });


        $('#leave_encashment_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                doc_date: {validators: {notEmpty: {message: 'Date is required.'}}},
                currencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                narration: {validators: {notEmpty: {message: 'Narration is required.'}}}
            }
        })
        .on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form      = $(e.target);
            var bv         = $form.data('bootstrapValidator');
            var data       = $form.serializeArray();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_leave_encashment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if(data[0] == 's'){
                        $("#leave_encashment_modal").modal("hide");
                        leave_encashment_tb.ajax.reload();
                        setTimeout(function(){
                            load_details(data['masterID'], data['doc_type']);
                        }, 300);
                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });

    function load_leave_encashment_data(selectedID=null) {
        var filterType = $("#filterType").val();
        window.localStorage.setItem('leave-en-cash-filter', filterType);

        leave_encashment_tb = $('#leave_encashment_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_leave_encashment_masters'); ?>",
            "aaSorting": [[2, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['masterID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>');

                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>');
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>');

            },
            "aoColumns": [
                {"mData": "masterID"},
                {"mData": "docDate"},
                {"mData": "documentCode"},
                {"mData": "document_type_str"},
                {"mData": "trCurrency"},
                {"mData": "narration"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [ {
                "targets": [0,4,5,6,7,8],
                "orderable": false
            }, {"searchable": false, "targets": [0,6,7]} ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "filterType","value": filterType});
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

    function add_encashment() {
        $('#leave_encashment_form').bootstrapValidator('resetForm', true);
        $('#doc_date').val('<?php echo $current_date; ?>');
        $('#doc_type').select2('val', '1');
        $('#currencyID').select2('val', '<?=$this->common_data['company_data']['company_default_currencyID']?>');

        $('#leave_encashment_form').data('bootstrapValidator').resetForm();

        $("#leave_encashment_modal").modal({backdrop: "static"});
    }

    function referBack_document(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'masterID': id},
                    url: "<?php echo site_url('Employee/referBack_leave_encashment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            leave_encashment_tb.ajax.reload();
                        }
                    }, error: function () {
                        myAlert('e', 'Some thing went gone wrong.<p>Please try again.')
                    }
                });
            });
    }

    function delete_document(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'masterID': id},
                    url: "<?php echo site_url('Employee/delete_leave_encashment_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            leave_encashment_tb.ajax.reload();
                        }
                    }, error: function () {
                        myAlert('e', 'Some thing went gone wrong.<p>Please try again.')
                    }
                });
            });
    }

    function load_details(id, dType){
        if(dType == 1){
            fetchPage('system/hrm/ajax/leave-encashment-ajax',id,'HRMS');
        }
        else{
            fetchPage('system/hrm/ajax/leave-salary-ajax',id,'HRMS');
        }

    }

    function view_modal(docID, dType){
        documentPageView_modal('LEC', docID, dType);
    }

    function print_document(docID, docCode){
        $('#print_master_id').val(docID);
        $('#print_form').attr('action', "<?php echo site_url('Employee/leave_encashment_and_salary_view/print'); ?>/"+docCode, "blank");
        $('#print_form').submit();
    }

    function open_bankTransferModal(id){
        $('#fnBankTransfer_form')[0].reset();
        $('#autoID').val( id );
        $('#transfer_amount').val( $('#leave_en_tot').val() );
        $('#accountID').val('').change();
        $('#leave_en_bank_transfer_modal').modal('show');
    }

    function save_bankTransfer(){
        var postData = $('#fnBankTransfer_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: '<?php echo site_url("Employee/leave_encashment_paymentVoucher_generation"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    $('#leave_en_bank_transfer_modal, #documentPageView').modal('hide');
                    leave_encashment_tb.ajax.reload();
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>

<?php
