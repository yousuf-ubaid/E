<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fn_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fn_man_investments');
echo head_page($title, false);

$currency_arr = all_currency_new_drop();
$invType_arr = investmentType_drop();
$inv_company_arr = investmentCompany_drop();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$investID = trim($this->input->post('page_id'));

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
                if($key != ''){
                    echo '<option value="'.$key.'" selected="selected">'.$item.'</option>';
                }
            }
            ?>
        </select>
    </div>
    <div class="col-md-1 col-sm-2" style="min-width: 125px;">
        <label class="col-sm-1 control-label" for="inv_company">
            <?php echo $this->lang->line('common_document').'&nbsp;'.$this->lang->line('common_status');?>
        </label>
    </div>
    <div class="col-md-3 col-sm-3">
        <?php echo form_dropdown('doc_status', document_status_drop(), '', 'class="form-control select2" id="doc_status" '); ?>
    </div>
    <div class="col-md-1 col-sm-1 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="add_type()" >
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_new');?>
        </button>
    </div>
    <div class="col-md-1 col-sm-1 pull-right">
        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="load_investments()" >
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
            <th style="min-width: 15%"><?php echo $this->lang->line('fn_man_investment_types');?></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_details');?></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('fn_man_amount');?></th>
            <th style="min-width: 15%">
                <?php echo $this->lang->line('common_document').'&nbsp;'.$this->lang->line('common_status'); ?>
            </th>
            <th style="min-width: 7%"></th>
        </tr>
        </thead>
    </table>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>
    var inv_table = null;
    var selectedRowID = <?php echo json_encode($investID); ?>;
    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.picDate').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    }).on('dp.change', function (ev) {

    });

    $('#inv_company, #invType, #currencyID').select2();

    $('#companyFilter').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/fund-management/investment', '', 'Investment');
        });

        load_investments(selectedRowID);
    });

    function load_investments(selectedRowID=null){
        inv_table = $('#inv-table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Fund_management/fetch_investment'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {
            },
            "columnDefs": [ {
                "targets": [0,4,5,6,7],
                "orderable": false,
                "searchable": false
            },{
                "targets": [8,9,10,11,12,13],
                "orderable": false,
                "searchable": true,
                "visible": false
            } ],
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
                {"mData": "invDes"},
                {"mData": "investment_det"},
                /*{"mData": "invDate"},*/
                {"mData": "invAmount_str"},
                {"mData": "status"},
                {"mData": "action"},
                {"mData": "invDate"},
                {"mData": "narration"},
                {"mData": "CurrencyCode"},
                {"mData": "trAmount_search"},
                {"mData": "disburseAmount_search"},
                {"mData": "balance_search"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'companyFilter', 'value':$('#companyFilter').val()});
                aoData.push({'name':'doc_status', 'value':$('#doc_status').val()});

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
        $('#invest_form')[0].reset();
        $('#inv_company, #invType, #currencyID').val('').change();
        $('#invest-frm-btn').attr('onclick', 'save_investment()');
        $('#inv-type-modal-title').text('<?php echo $this->lang->line('fn_man_new_investment');?>');


        $('#invest-model').modal('show');
    }

    function save_investment(){
        var postData = $('#invest_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Fund_management/save_investment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#invest-model').modal('hide');

                    setTimeout(function(){
                        edit_investment(data['id']);
                    }, 300);
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function edit_investment(invTypID){
        fetchPage('system/fund-management/ajax/investment-detail', invTypID, '','FM')
    }

    function get_document_status_more_details(sysType, documentSystemCode, statusType){
        var title_str = '<?php echo $this->lang->line('common_document');?>';

        switch (statusType){
            case 'pending': title_str = '<?php echo $this->lang->line('fn_man_pending');?>'+' '+title_str; break;
            case 'elapse': title_str = '<?php echo $this->lang->line('fn_man_expire_expire');?>'+' '+title_str; break;
            case 'expiry': title_str = '<?php echo $this->lang->line('fn_man_expiry_remain');?>'+' '+title_str; break;
            default : title_str = 'Not a  valid selection';
        }

        $('#document_status_more_details-title').text(title_str);

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'sysType': sysType, 'documentSystemCode':documentSystemCode, 'statusType':statusType},
            url: "<?php echo site_url('Fund_management/get_document_status_more_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#document_status_more_details-model').modal('show');
                $('#document_status_view').html(data);
                stopLoad();
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
</script>


<div id="invest-model" class="modal fade" role="dialog" data-backdrop="true">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="inv-type-modal-title"></h4>
            </div>
            <?php echo form_open('', 'role="form" id="invest_form" class="form-horizontal" autocomplete="off"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inv_company"><?php echo $this->lang->line('fn_man_investment_company');?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('inv_company', $inv_company_arr, '', 'class="form-control select2" id="inv_company"'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="invType"><?php echo $this->lang->line('fn_man_investment_types');?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('invType', $invType_arr, '', 'class="form-control select2" id="invType"'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="invDate"><?php echo $this->lang->line('fn_man_new_investment_date');?></label>
                            <div class="col-sm-6">
                                <div class="input-group picDate">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="invDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="currencyID"><?php echo $this->lang->line('common_currency');?></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('currencyID', $currency_arr, '', 'class="form-control select2" id="currencyID"'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="amount"><?php echo $this->lang->line('fn_man_amount');?></label>
                            <div class="col-sm-6">
                                <input type="text" name="amount" id="amount" class="form-control number" value="" >
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

                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" id="invest-frm-btn"><?php echo $this->lang->line('common_save');?></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php
