<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_request_letters');
echo head_page($title, false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$letter_types = hr_letter_types();
$letter_language = ['E'=> 'English', 'A'=> 'Arabic'];

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

/*$status_arr = [
    '0' => $this->lang->line('common_pending'),
    '1' => $this->lang->line('common_approved')
];*/

$appDrop = [
    ''=>$this->lang->line('common_please_select'),
    '1'=>$this->lang->line('common_approved'),
    '2'=>$this->lang->line('common_refer_back')
];

$identity_type = [''=>'Select type', '2'=>'Resident Card/ID No', '4'=>'Passport'];

$signature_drop = request_letter_drop();
?>

<style>
    .form-div{
        margin-left: 15px;
    }
</style>

<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tbody>
            <tr>
                <td>
                    <span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_approved'); ?><!--Approved-->
                </td>
                <td>
                    <span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span>  <?php echo $this->lang->line('common_not_approved'); ?> <!--Not Approved-->
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
   <!-- <div class="col-md-3 text-center">
        <?php /*echo form_dropdown('approvedYN', $status_arr, '','class="form-control" id="approvedYN" required onchange="load_hr_letters()"'); */?>
    </div>-->
</div>
<hr>
<div class="table-responsive">
    <table id="hr_letters_master_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 3%">#</th>
            <th style="min-width: 8%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?><!--Document Code--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_employee_name');?></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_letter_type');?></th>
            <th style="min-width: 25%"><?php echo $this->lang->line('common_narration');?></th>
            <th style="min-width: 3%"><?php echo $this->lang->line('common_level');?></th>
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

<div class="modal fade" id="request_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?></span></h4>
            </div>
            <form class="form-horizontal" id="request_form">
                <div class="modal-body">

                    <div class="panel-body" id="ajax-container">
                        <div class="row well" style="padding: 10px; margin: 0px 0px 10px;">
                            <div class="col-md-4">
                                <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
                                    <tr>
                                        <td style="width: 150px;"><?php echo $this->lang->line('common_document_code');?></td>
                                        <td class="bgWhite details-td" id="documentCode" width="200px"></td>
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
                        </div>

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
                                    <?php echo form_dropdown('letter_type', $letter_types, 0, 'class="form-control req-frm-inputs" id="letter_type"'); ?>
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

                            <div class="col-sm-3 form-div" id="bank-div">
                                <div class="form-group">
                                    <label class="control-label"><?php echo $this->lang->line('common_bank');?></label>
                                    <input type="text" name="bank_acc" id="bank_acc" class="form-control req-frm-inputs" value="" readonly/>
                                </div>
                            </div>

                            <div class="col-sm-3 form-div">
                                <div class="form-group">
                                    <label class="control-label"><?php echo $this->lang->line('common_language');?></label>
                                    <?php echo form_dropdown('letter_language', $letter_language, 'E', 'class="form-control req-frm-inputs" id="letter_language"'); ?>
                                </div>
                            </div>

                            <div class="col-sm-3 form-div signature-div">
                                <div class="form-group">
                                    <label class="control-label"><?php echo $this->lang->line('common_signature');?></label>
                                    <input type="text" name="signature_txt" id="signature_txt" class="form-control req-frm-inputs" value="" readonly/>
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
                    <hr class="form_items">
                    <div class="form-group form_items">
                        <label for="status" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status'); ?><!--Status--></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('status', $appDrop, '','class="form-control controlCls" id="status" required'); ?>
                            <input type="hidden" name="level" id="level">
                            <input type="hidden" name="masterID" id="app_masterID">
                        </div>
                    </div>

                    <div class="form-group form_items">
                        <label for="signature_drop" class="col-sm-2 control-label"><?php echo $this->lang->line('common_signature');?></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('signature_drop', $signature_drop, null, 'class="form-control controlCls" id="signature_drop"'); ?>
                        </div>
                    </div>

                    <div class="form-group form_items">
                        <label for="comments" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comment'); ?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control controlCls" rows="3" name="comments" id="comments"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm form_items"><?php echo $this->lang->line('common_submit'); ?><!--Submit--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    let hr_letters_tb = '';

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/request-letters-verification','','HRMS');
        });

        load_hr_letters();

        $('#request_form').bootstrapValidator({
            live            : 'enabled',
            message         : 'This value is not valid.',
            excluded        : [':disabled'],
            fields          : {
                status     	   : {validators : {notEmpty:{message:'<?php echo $this->lang->line('common_status_is_required'); ?>.'}}},
                Level          : {validators : {notEmpty:{message:'<?php echo $this->lang->line('common_level_order_status_is_required'); ?>.'}}},
                signature_drop : {validators : {notEmpty:{message:'<?php echo $this->lang->line('common_signature_is_required'); ?>.'}}}
            }
        }).
        on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : data,
                url :"<?= site_url('Employee/hr_letter_requests_approval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if( data[0] == 's') {
                        $("#request_modal").modal('hide');
                        hr_letters_tb.ajax.reload();
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }

                },error : function(){
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });

    });

    function load_hr_letters(selectedID=null) {
        hr_letters_tb = $('#hr_letters_master_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_hr_letter_requests_approvals'); ?>",
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
                {"mData": "employee_det"},
                {"mData": "letter_type"},
                {"mData": "narration"},
                {"mData": "level"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [ {
                "targets": [0,4,6,7,8],
                "orderable": false
            } ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({'name':'approvedYN', 'value': $('#approvedYN').val()});
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

    function load_approvalView(id, level, isApproved){
        $('#app_masterID').val(id);
        $('#level').val(level);
        $('.form_items').show();
        $('.signature-div').hide();

        if(isApproved == 1){
            $('.form_items').hide();
            $('.signature-div').show();
        }

        $('#request_form')[0].reset();
        $('#request_form').bootstrapValidator('resetForm', true);
        $('.req-frm-inputs').prop('disabled', true);

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': id},
            url: "<?php echo site_url('Employee/load_hr_letter_request'); ?>",
            beforeSend: function () {
                $('#bank-div').hide();
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#isConform').val(0);

                    $('#masterID').val( id );
                    var masterData = data['masterData'];
                    $('#documentCode').text( masterData['documentCode'] );
                    $('#empNam').text( masterData['ECode']+' - '+masterData['Ename2'] );
                    $('#doc_date').val( masterData['request_date'] );
                    $('#letter_type').val( masterData['letter_type'] );
                    $('#identity_type').val( masterData['identity_type'] );
                    $('#identity_no').val( masterData['identity_no'] );
                    $('#letter_addressed').val( masterData['address_to'] );
                    $('#letter_language').val( masterData['letter_language'] );
                    $('#narration').val( masterData['narration'] );

                    if(masterData['letter_type'] == 1){
                        $('#bank-div').show();

                        if(masterData['bankData'] != null){
                            $('#bank_acc').val( masterData['bankData']['bankName']+' | '+masterData['bankData']['accountNo'] );
                        }
                    }

                    if(isApproved == 1){
                        $('#signature_txt').val(masterData['signature_data']);
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



    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function print_document_letter(docID, docCode){
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
</script>
