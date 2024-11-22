<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_salary_advance_request');
echo head_page($title, false);
$date_format_policy = date_format_policy();
$emp_id = current_userID();
$current_date = current_format_date();

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>

<style>
    fieldset {
        border: 1px solid silver;
        border-radius: 0px;
        padding: 1%;
        padding-bottom: 15px;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500
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
    <div class="col-md-4 text-center"> &nbsp; </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right btn-sm" onclick="salary_advance_request_view(0)">
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_new');?><!--New-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="sal_advance_master_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 3%">#</th>
            <th style="min-width: 8%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?><!--Document Code--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_employee_details');?></th>
            <th style="min-width: 4%"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <th style="min-width: 4%"><?php echo $this->lang->line('common_amount');?><!--Amount--></th>
            <th style="min-width: 25%"><?php echo $this->lang->line('common_narration');?><!--Description--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
            <th style="min-width: 6%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<form id="print_form" method="post" action="" target="_blank">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" id="print_master_id" name="masterID">
</form>

<div class="modal fade" id="request_modal" role="dialog" data-keyboard="false" data-backdrop="static" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $title;?></h3>
            </div>
            <form role="form" id="request_form" class="form-horizontal" autocomplete="off" action="#">
                <div class="modal-body" >
                    <div class="row well" style="padding: 10px; margin: 10px">
                        <div class="col-md-4">
                            <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
                                <tr>
                                    <td style="width: 150px;"><?php echo $this->lang->line('common_document_code');?></td>
                                    <td class="bgWhite details-td" id="documentCode" width="200px"> </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-5">
                            <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
                                <tr>
                                    <td style="width: 70px;"><?php echo $this->lang->line('common_employee');?></td>
                                    <td class="bgWhite details-td" id="empNam" width="200px"> </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-3">
                            <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
                                <tr>
                                    <td style="width: 150px;"><?php echo $this->lang->line('common_currency');?></td>
                                    <td class="bgWhite details-td" id="curr_code" width="200px"> </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row" style="">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <fieldset>
                                    <legend><?php echo $this->lang->line('common_salary_declaration_detail');?></legend>

                                    <table class="<?php echo table_class(); ?> add_declarationTB">
                                        <thead>
                                        <tr>
                                            <th> <?php echo $this->lang->line('emp_description');?><!--Description--></th>
                                            <th> <?php echo $this->lang->line('emp_salary_amount');?><!--Amount--></th>
                                        </tr>
                                        </thead>

                                        <tbody id="sal-dec-body"></tbody>

                                        <tfoot>
                                            <tr>
                                                <td align="right" class="total-sd"><?php echo $this->lang->line('emp_salary_total');?></td>
                                            <td align="right" class="total-sd" id="salary_tot"> </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </fieldset>
                            </div>

                            <div class="col-sm-6">
                                <fieldset>
                                    <legend><?php echo $this->lang->line('common_salary_advance_request_form');?></legend>

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_date');?><!--Date--></label>
                                        <div class="col-sm-6">
                                            <div class="input-group datepic">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="text" name="doc_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="doc_date"
                                                       class="form-control" required value="<?php echo $current_date ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_amount');?></label>
                                        <div class="col-sm-6">
                                            <input type="text" name="request_amount" id="request_amount" class="form-control number" value="" />
                                            <input type="hidden" name="emp_id" value="<?=$emp_id?>" />
                                            <input type="hidden" name="masterID" id="masterID" value="" />
                                            <input type="hidden" name="isConfirmed" id="isConfirmed" value="0" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_narration');?></label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" id="narration" name="narration" rows="2"></textarea>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!--<button type="submit" class="btn btn-primary btn-sm submitBtn" data-value="0"><?php /*echo $this->lang->line('common_save');*/?></button>-->
                    <button type="button" class="btn btn-primary btn-sm submitBtn" onclick="save_request(0)"><?php echo $this->lang->line('common_save');?></button>
                    <button type="button" class="btn btn-primary btn-sm submitBtn" onclick="save_request(1)"><?php echo $this->lang->line('common_save_and_confirm');?></button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var sal_advance_tb = '';
    var trDPlace = 2;
    $('.number').numeric({negative: false});

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/salary-advance-request','','HRMS');
        });

        load_salary_advance_data();

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        }).on('dp.change', function (ev) {
            $('#request_form').bootstrapValidator('revalidateField', 'doc_date');
        });

        $('#request_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                request_amount: {validators: {notEmpty: {message: 'Request amount is required.'}}},
                doc_date: {validators: {notEmpty: {message: 'Date is required.'}}},
                narration: {validators: {notEmpty: {message: 'Narration is required.'}}}
            }
        }).on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var $form      = $(e.target);
            var bv         = $form.data('bootstrapValidator');
            var data       = $form.serializeArray();
            var isConform  = $('#isConform').val();
            var req_url    = $form.attr('action');

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: req_url,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if(data[0] == 's'){
                        $("#request_modal").modal("hide");

                        sal_advance_tb.ajax.reload();
                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });

    function load_salary_advance_data(selectedID=null) {
        sal_advance_tb = $('#sal_advance_master_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_salary_advanceMasters'); ?>",
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
                {"mData": "employee"},
                {"mData": "trCurrency"},
                {"mData": "request_amount_str"},
                {"mData": "narration"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [ {
                "targets": [0,4,6,7,8,9],
                "orderable": false
            }, {"targets": [0,7,8,9], "searchable": false}],
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

    function salary_advance_request_view(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': id},
            url: "<?php echo site_url('Employee/load_salary_advance_request_view'); ?>",
            beforeSend: function () {
                $('#documentCode').text( '-' );
                $('#request_form').bootstrapValidator('resetForm', true);
                $('#doc_date').val('<?php echo $current_date; ?>');
                $('#salary-advance-request').html('');
                var req_url = "<?php echo site_url('Employee/save_salary_advance_request'); ?>";
                if(id > 0){
                    req_url = "<?php echo site_url('Employee/update_salary_advance_request'); ?>";
                }
                $('#request_form').attr('action', req_url);
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#isConform').val(0);

                    var emp_data = data['emp_data'];
                    trDPlace = emp_data['trDPlace'];

                    $('#empNam').text( emp_data['empNam'] );
                    $('#curr_code').text( emp_data['curr_code'] );
                    $('#salary_tot').text( data['totPayroll'] );

                    $('#sal-dec-body').html( data['salary_str'] );

                    if(id != 0){
                        $('#masterID').val( id );
                        var masterData = data['masterData'];
                        $('#documentCode').text( masterData['documentCode'] );
                        $('#doc_date').val( masterData['docDate'] );
                        $('#request_amount').val( masterData['request_amount'] );
                        $('#narration').val( masterData['narration'] );
                    }
                }
                else{
                    myAlert(data[0], data[1]);
                }
            }, error: function () {
                myAlert('e', 'Some thing went gone wrong.<p>Please try again.')
            }
        });

        $("#request_modal").modal({backdrop: "static"});
    }

    function save_request(isConf){
        $('#isConfirmed').val(isConf);
        $('#request_form').submit();
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
                    url: "<?php echo site_url('Employee/referBack_salary_advance_request'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            sal_advance_tb.ajax.reload();
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
                    url: "<?php echo site_url('Employee/delete_salary_advance_request'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            sal_advance_tb.ajax.reload();
                        }
                    }, error: function () {
                        myAlert('e', 'Some thing went gone wrong.<p>Please try again.')
                    }
                });
            });
    }

    function load_details(id){
        salary_advance_request_view(id)
    }

    function view_modal( docID ){
        documentPageView_modal('SAR', docID)
    }

    function print_document(docID, docCode){
        $('#print_master_id').val(docID);
        $('#print_form').attr('action', "<?php echo site_url('Employee/load_salary_advance_request_view/print'); ?>/"+docCode, "blank");
        $('#print_form').submit();
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>

<?php
