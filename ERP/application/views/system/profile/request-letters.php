<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_request_letters');
echo head_page($title, false);
$date_format_policy = date_format_policy();
$emp_id = current_userID();
$current_date = current_format_date();
$letter_types = hr_letter_types();
$identity_type = [''=>'Select type', '2'=>'Resident Card/ID No', '4'=>'Passport'];
$letter_language = ['E'=> 'English', 'A'=> 'Arabic'];
$bank_acc = employee_bank_drop($emp_id,true);

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>

<style>
    .form-div{
        margin-left: 15px;
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
                    / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved--> <!--common_not_completed-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center"> &nbsp; </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right btn-sm" onclick="new_request()">
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_new');?><!--New-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="hr_letters_master_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 3%">#</th>
            <th style="min-width: 6%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('common_code');?><!--Document Code--></th>
            <th style="min-width: 12%"><?php echo $this->lang->line('common_letter_type');?></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_letter_addressed');?></th>
            <th style="min-width: 4%"><?php echo $this->lang->line('common_language');?></th>
            <th style="min-width: 25%"><?php echo $this->lang->line('common_narration');?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th> <?php //echo $this->lang->line('common_completed');?>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $title;?> <span id="head_document_code"></span></h3>
            </div>
            <form role="form" id="request_form" class="form-horizontal" autocomplete="off" action="#">
                <div class="modal-body" >
                    <div class="row">
                        <div class="col-sm-3 form-div">
                            <div class="form-group">
                                <label class="control-label"><?php echo $this->lang->line('common_date');?><!--Date--></label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="doc_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="doc_date"
                                           class="form-control req-frm-inputs" required value="<?php echo $current_date ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3 form-div">
                            <div class="form-group">
                                <label class="control-label"><?php echo $this->lang->line('common_letter_type');?></label>
                                <?=form_dropdown('letter_type', $letter_types, 0, 'class="form-control req-frm-inputs" id="letter_type"
                                    onchange="bank_visibility()"'); ?>
                            </div>
                        </div>

                        <div class="col-sm-5 form-div">
                            <div class="form-group">
                                <label class="control-label"><?php echo $this->lang->line('common_letter_addressed');?></label>
                                <input type="text" name="letter_addressed" id="letter_addressed" class="form-control req-frm-inputs" value="" />
                            </div>
                        </div>

                        <div class="col-sm-3 form-div">
                            <div class="form-group">
                                <label class="control-label"><?php echo $this->lang->line('common_identity');?></label>
                                <?=form_dropdown('identity_type', $identity_type, '', 'class="form-control req-frm-inputs" id="identity_type"
                                        onchange="load_identityNo()"'); ?>
                            </div>
                        </div>

                        <div class="col-sm-3 form-div">
                            <div class="form-group">
                                <label class="control-label"><?php echo $this->lang->line('common_identity_no');?></label>
                                <input type="text" name="identity_no" id="identity_no" class="form-control req-frm-inputs" value="" readonly/>
                            </div>
                        </div>

                        <div class="col-sm-5 form-div" id="bank-div">
                            <div class="form-group">
                                <label class="control-label"><?php echo $this->lang->line('common_bank');?></label>
                                <?=form_dropdown('bank_acc', $bank_acc, '', 'class="form-control req-frm-inputs" id="bank_acc"'); ?>
                            </div>
                        </div>

                        <div class="col-sm-3 form-div">
                            <div class="form-group">
                                <label class="control-label"><?php echo $this->lang->line('common_language');?></label>
                                <?php echo form_dropdown('letter_language', $letter_language, 'E', 'class="form-control req-frm-inputs" id="letter_language"'); ?>
                            </div>
                        </div>

                        <div class="col-sm-8 form-div">
                            <div class="form-group">
                                <label class="control-label"><?php echo $this->lang->line('common_narration');?></label>
                                <textarea class="form-control req-frm-inputs" id="narration" name="narration" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="emp_id" id="emp_id" value="<?=$emp_id?>" />
                    <input type="hidden" name="masterID" id="masterID" value="" />
                    <input type="hidden" name="isConfirmed" id="isConfirmed" value="0" />
                    <button type="button" class="btn btn-primary btn-sm submitBtn" onclick="save_request(0)"><?php echo $this->lang->line('common_save');?></button>
                    <button type="button" class="btn btn-primary btn-sm submitBtn" onclick="save_request(1)"><?php echo $this->lang->line('common_save_and_confirm');?></button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    let hr_letters_tb = '';

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/profile/request-letters','','HRMS');
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

            }
        })
            .on('success.form.bv', function (e) {
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

                        hr_letters_tb.ajax.reload();
                    }
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });
    });

    function load_salary_advance_data(selectedID=null) {
        hr_letters_tb = $('#hr_letters_master_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_hr_letter_requests'); ?>",
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
                    if (parseInt(oSettings.aoData[x]._aData['request_id']) == selectedRowID) {
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
                {"mData": "request_id"},
                {"mData": "docDate"},
                {"mData": "documentCode"},
                {"mData": "letter_type"},
                {"mData": "address_to"},
                {"mData": "letter_language"},
                {"mData": "narration"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [
                {"targets": [0,4,6,7,8,9], "orderable": false},
                {"targets": [0,7,8,9], "searchable": false}],
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

    function new_request() {
        $('#bank-div').hide();
        $('.req-frm-inputs').prop('disabled', false);
        $('#head_document_code').text( '' );
        $('#request_form')[0].reset();
        $('#request_form').bootstrapValidator('resetForm', true);
        $('#doc_date').val('<?php echo $current_date; ?>');
        $('#request_form').attr('action', "<?php echo site_url('Employee/hr_letter_request_create'); ?>");
        $("#request_modal").modal({backdrop: "static"});
    }

    function bank_visibility() {
        let letter_type = $('#letter_type').val();

        if(letter_type == 1){
            $('#bank-div').show();
            return false;
        }

        $('#bank-div').hide();
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
                    url: "<?php echo site_url('Employee/hr_letter_request_referBack'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            hr_letters_tb.ajax.reload();
                        }
                    }, error: function () {
                        myAlert('e', 'Some thing went gone wrong.<p>Please try again.')
                    }
                });
            });
    }

    function load_identityNo() {
        $('#identity_no').val('');
        let identity_type = $('#identity_type').val();

        if(identity_type === ''){
            return false;
        }

        let emp_id = $('#emp_id').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'identity_type': identity_type, 'emp_id': emp_id},
            url: "<?php echo site_url('Employee/get_identityNo'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#identity_no').val(data['documentNo']);
                }
                else{
                    myAlert(data[0], data[1]);
                }
            }, error: function () {
                myAlert('e', 'Some thing went gone wrong.<p>Please try again.')
            }
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
                    url: "<?php echo site_url('Employee/delete_request'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            hr_letters_tb.ajax.reload();
                        }
                    }, error: function () {
                        myAlert('e', 'Some thing went gone wrong.<p>Please try again.')
                    }
                });
            });
    }

    function load_details(id){
        $('#request_form')[0].reset();
        $('#request_form').bootstrapValidator('resetForm', true);
        $('#request_form').attr('action', "<?php echo site_url('Employee/hr_letter_request_update'); ?>");

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': id},
            url: "<?php echo site_url('Employee/load_hr_letter_request'); ?>",
            beforeSend: function () {
                $('.req-frm-inputs').prop('disabled', false);
                $('#bank-div').hide();
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#isConform').val(0);

                    $('#masterID').val( id );
                    var masterData = data['masterData'];
                    $('#head_document_code').text( ' - '+masterData['documentCode'] );
                    $('#doc_date').val( masterData['request_date'] );
                    $('#letter_type').val( masterData['letter_type'] );
                    $('#identity_type').val( masterData['identity_type'] );
                    $('#identity_no').val( masterData['identity_no'] );
                    $('#letter_addressed').val( masterData['address_to'] );
                    $('#letter_language').val( masterData['letter_language'] );
                    $('#narration').val( masterData['narration'] );


                    if(masterData['letter_type'] == 1){
                        $('#bank-div').show();
                        $('#bank_acc').val( masterData['bank_acc'] );
                    }

                    if(masterData['confirmedYN'] == 1){
                        $('.req-frm-inputs').prop('disabled', true);
                    }


                    $("#request_modal").modal({backdrop: "static"});
                }
                else{
                    myAlert(data[0], data[1]);
                }
            }, error: function () {
                myAlert('e', 'Some thing went gone wrong.<p>Please try again.')
            }
        });

    }

    function view_modal( id ){
        documentPageView_modal('HDR', id);
    }

    function print_document(docID, docCode){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': docID},
            url: "<?php echo site_url('Employee/request_letter_template_checkAvailability'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 'e') {
                    myAlert(data[0], data[1]);
                } else {
                    $('#print_master_id').val(docID);
                    $('#print_form').attr('action', "<?php echo site_url('Employee/print_hr_letter_request'); ?>/"+docCode, "blank");
                    $('#print_form').submit();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
</script>
