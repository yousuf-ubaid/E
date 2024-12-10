<style type="text/css">
    .saveInputs{ height: 25px; font-size: 11px }
    #setup-add-tb td, #master-setup-add-tb td{ padding: 2px; }
    #setup-edit-tb td{ padding: 2px; }
    .number{ width: 90px !important;}
    legend{ font-size: 16px !important;}

    #filter_div .select2-container{
        width: 180px !important;
    }
</style>

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_others_master', $primaryLanguage);
$this->lang->load('fn_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fn_man_financials');
echo head_page($title  , false);

$currency_arr = all_currency_new_drop();
$report_arr = fetch_report_type();
$inv_company_arr = investmentCompany_drop(1);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$masterID = trim($this->input->post('page_id'));

$month_arr = payrollCalender(date('Y'), 1);

?>
<style>
    .label-circle-danger{
        background-color: #c14031 !important;
        border-radius: 50%;
        font-size: 11px;
    }

    .label-circle-success{
        background-color: #00a65a !important;
        border-radius: 50%;
        font-size: 11px;
    }

    .label-circle-warning{
        background-color: #f39c12 !important;
        border-radius: 50%;
        font-size: 11px;
    }

    .label-circle-danger:hover,.label-circle-warning:hover{
        cursor: pointer;
    }

    #periodYear{
        width: 50px;
        padding: 0px;
        border: none;
        height: 27px;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-1 col-sm-2">
        <label class="col-sm-1 control-label" for="inv_company"><?php echo $this->lang->line('common_company');?></label>
    </div>
    <div class="col-md-3 col-sm-3">
        <select name="inv_company[]" class="form-control" id="companyFilter" multiple="multiple">
            <?php
            foreach($inv_company_arr as $key=>$item){
               echo '<option value="'.$item['id'].'" selected="selected">'.$item['company_name'].'</option>';
            }
            ?>
        </select>
    </div>
    <!--<div class="col-md-1 col-sm-2" style="min-width: 125px;">
        <label class="col-sm-1 control-label" for="inv_company">
            <?php /*echo $this->lang->line('common_document').'&nbsp;'.$this->lang->line('common_status');*/?>
        </label>
    </div>
    <div class="col-md-3 col-sm-3">
        <?php /*echo form_dropdown('doc_status', document_status_drop(), '', 'class="form-control select2" id="doc_status" '); */?>
    </div>-->
    <div class="col-md-1 col-sm-1 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="add_type()" >
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_new');?>
        </button>
    </div>
    <div class="col-md-1 col-sm-1 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="load_financial()" >
            <i class="fa fa-filter"></i> <?php echo $this->lang->line('common_load');?>
        </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="inv-table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_document_code');?></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('fn_man_investment_company');?></th>
            <th style="min-width: 7%"><?php echo $this->lang->line('common_period');?></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_currency');?></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_statement');?></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('fn_man_submission_date');?></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_narration');?></th>
            <th style="min-width: 70px"></th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    var financial_table = null;
    var selectedRowID = <?php echo json_encode($masterID); ?>;
    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.picDate').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    }).on('dp.change', function (ev) {

    });

    $('#inv_company, #templateID, #currencyID').select2();

    $('#companyFilter').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/fund-management/financials-master-fm', '', 'Financials');
        });

        load_financial(selectedRowID);
    });

    function load_financial(selectedRowID=null){
        financial_table = $('#inv-table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Fund_management/fetch_financial'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {
            },
            "columnDefs": [ {
                "targets": [0,8],
                "orderable": false
            }, {"searchable": false, "targets": [0]}],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();

                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if( parseInt(oSettings.aoData[x]._aData['id']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "documentCode"},
                {"mData": "company_name"},
                {"mData": "fn_period"},
                {"mData": "CurrencyCode"},
                {"mData": "reportDes"},
                {"mData": "submissionDate"},
                {"mData": "narration"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'companyFilter', 'value':$('#companyFilter').val()});

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

    function add_type(){
        $('#financial_form')[0].reset();
        $('#inv_company, #templateID, #currencyID').val('').change();
        $('#invest-frm-btn').attr('onclick', 'save_financial()');
        $('#inv-type-modal-title').text('<?php echo $this->lang->line('fn_man_financial_header');?>');


        $('#fn-master-model').modal('show');
    }

    function save_financial(){
        var postData = $('#financial_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Fund_management/save_financial'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#fn-master-model').modal('hide');

                    setTimeout(function(){
                        edit_financial(data['id']);
                    }, 300);
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function edit_financial(id){
        fetchPage('system/fund-management/ajax/financials-doc-edit', id, '','FM')
    }

    function view_financial(id){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'returnType': 'view'},
            url: "<?php echo site_url('Fund_management/finance_submission_print'); ?>/"+id,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#viewFinancial-modal').modal('show');
                $('#viewFinancial_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_financial (id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'delID' : id },
                    url: "<?php echo site_url('Fund_management/delete_financial'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            financial_table.ajax.reload();
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

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    $(document).on('keypress', '.number',function (event) {
        var amount = $(this).val();
        if(amount.indexOf('.') > -1) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }
        else {
            if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }

    });

    function selectCurrency(obj) {
        var curID = $('#inv_company :selected').attr('data-val');

        $('#currencyID').val(curID).change();
    }
</script>


<div id="fn-master-model" class="modal fade" role="dialog" data-backdrop="true">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="inv-type-modal-title"></h4>
            </div>
            <?php echo form_open('', 'role="form" id="financial_form" class="form-horizontal" autocomplete="off"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inv_company"><?php echo $this->lang->line('fn_man_investment_company');?></label>
                            <div class="col-sm-6">
                                <select name="inv_company" class="form-control select2" id="inv_company" onchange="selectCurrency(this)">
                                    <?php
                                    echo '<option value="" selected="selected">'.$this->lang->line('common_select_a_option').'</option>';
                                    foreach($inv_company_arr as $key=>$item){
                                        echo '<option value="'.$item['id'].'" data-val="'.$item['currencyID'].'">'.$item['company_name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="currencyID"><?php echo $this->lang->line('common_currency');?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('currencyID', $currency_arr, '', 'class="form-control select2" id="currencyID" disabled'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="submission_date"><?php echo $this->lang->line('common_period');?></label>
                            <div class="col-sm-6">
                                <div class="input-group" style="">
                                    <span class="input-group-addon" style="padding: 0px">
                                        <input type="number" name="periodYear" class="input-group-addon form-control" id="periodYear" value="<?php echo date('Y'); ?>" />
                                    </span>
                                        <?php echo form_dropdown('periodMonth', $month_arr, '', 'class="form-control" id="periodMonth" required'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="templateID"><?php echo $this->lang->line('common_statement');?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('reportID', $report_arr, '', 'class="form-control select2" id="templateID"'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="submission_date"><?php echo $this->lang->line('fn_man_submission_date');?></label>
                            <div class="col-sm-6">
                                <div class="input-group picDate">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="submission_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" class="form-control" required readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="narration"><?php echo $this->lang->line('common_narration');?></label>
                            <div class="col-sm-6">
                                <input type="text" name="narration" id="narration" class="form-control" value="" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" id="invest-frm-btn"><?php echo $this->lang->line('common_save');?></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div id="viewFinancial-modal" class="modal fade" role="dialog" data-backdrop="true">
    <div class="modal-dialog modal-lg" style="">
        <div class="modal-content">
            <!--<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="inv-type-modal-title"></h4>
            </div>-->
            <div class="modal-body" id="viewFinancial_view">

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>


<?php
