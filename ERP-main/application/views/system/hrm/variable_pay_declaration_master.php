<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_variable_pay_declaration');
echo head_page($title, false);


$current_date = format_date(current_date());
$currency_arr = all_currency_new_drop();
$defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>

<style>
    .right-align{ text-align: right; }

    .total-sd {
        border-top: 1px double #151313 !important;
        border-bottom: 3px double #101010 !important;
        font-weight: bold;
        font-size: 12px !important;
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
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right btn-sm" onclick="openNewDeclaration_modal()">
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_new');?><!--New-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="declaration_master_table" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_document_code');?><!--Declaration Code--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <th style="min-width: 25%"><?php echo $this->lang->line('common_description');?><!--Description--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    var dec_table = null;
    $('.select2').select2();

    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    var n = null;

    $(document).ready(function () {

        $('.headerclose').click(function(){
            fetchPage('system/hrm/variable_pay_declaration_master','','HRMS');
        });

        load_variable_pay_declaration();

        Inputmask().mask(document.querySelectorAll("input"));
        $('.date_pic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        }).on('dp.change', function (ev) {
            /*$('#declaration_form').bootstrapValidator('revalidateField', 'documentDate');
            $(this).datepicker('hide');*/
        });


        $('#documentDate_er').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $('#declaration_form').bootstrapValidator('revalidateField', 'documentDate');
            $(this).datepicker('hide');
        });

        $('#declaration_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                currencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                description: {validators: {notEmpty: {message: 'Description is required.'}}}
            }
        }).
        on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/save_variable_pay_declaration_master'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#newDeclaration_modal').modal('hide');
                        setTimeout(function(){
                            load_details(data['id']);
                        }, 300);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '' + errorThrown);
                }
            });
        });

    });

    function openNewDeclaration_modal(){
        $('#newDeclaration_modal').modal('show');
    }

    function load_variable_pay_declaration(selectedID=null) {
        dec_table = $('#declaration_master_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/variable_pay_declaration_master_data_table'); ?>",
            "aaSorting": [[2, 'desc']],
            "fnInitComplete": function () {

            },
            "columnDefs": [
                {"targets": [0,5,6,7], "orderable": false}, {"searchable": false, "targets": [0]}
            ],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['vpMasterID']) == selectedRowID) {
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
                {"mData": "vpMasterID"},
                {"mData": "documentDate"},
                {"mData": "documentCode"},
                {"mData": "trCurrency"},
                {"mData": "description"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
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

    function delete_declaration(id, value) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'masterID': id},
                    url: "<?php echo site_url('Employee/delete_variable_pay_declaration_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            dec_table.ajax.reload();
                        }
                    }, error: function () {
                        myAlert('e', 'Some thing went wrong please contact system support');
                    }
                });
            });
    }

    function referBackDeclaration(id) {
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
                    url: "<?php echo site_url('Employee/refer_back_variable_pay_declaration_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            dec_table.ajax.reload();
                        }
                    }, error: function () {
                        stopLoad();
                        data('e', 'Error in refer back process');
                    }
                });
            });
    }

    function load_details(id){
        fetchPage('system/hrm/ajax/variable_pay_declaration_detail',id,'HRMS');
    }

    function view_modal( docID ){
        documentPageView_modal('VD', docID)
    }

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function print_SD(docID, docCode){
        window.open("<?php echo site_url('Employee/variable_pay_approval_confirmation_view'); ?>/"+docID+'/'+docCode, "blank");
    }
</script>

<div class="modal fade" id="newDeclaration_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="" role="document">
        <div class="modal-content">
            <form class="" id="declaration_form" autocomplete="off">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $title ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row" style="margin-left: 2px">
                        <div class="form-group col-sm-3">
                            <label><?php echo $this->lang->line('hrms_payroll_document_date');?><!--Document Date--></label>
                        </div>
                        <div class="form-group col-sm-5">
                            <div class="input-group date_pic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type='text' class="form-control" id="documentDate" name="documentDate" value="<?php echo $current_date; ?>"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" />
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left: 2px">
                        <div class="form-group col-sm-3">
                            <label><?php echo $this->lang->line('common_currency');?><!--Currency--></label>
                        </div>
                        <div class="form-group col-sm-5">
                            <?php echo form_dropdown('currencyID', $currency_arr, $defaultCurrencyID, 'class="form-control select2" id="currencyID" required');?>
                        </div>
                    </div>
                    <div class="row" style="margin-left: 2px">
                        <div class="form-group col-sm-3">
                            <label><?php echo $this->lang->line('hrms_payroll_initial_declaration');?><!--Initial Declaration--></label>
                        </div>
                        <div class="form-group col-sm-5">
                            <select name="isInitialDeclaration" id="isInitialDeclaration" class="form-control">
                                <option value="0"><?php echo $this->lang->line('common_no');?><!--No--></option>
                                <option value="1"><?php echo $this->lang->line('common_yes');?><!--Yes--></option>
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-left: 2px">
                        <div class="form-group col-sm-3">
                            <label><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        </div>
                        <div class="form-group col-sm-8">
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
