<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_overall_progress_header');
$customer_arr = all_mfq_customer_drop(false);
$date_format_policy = date_format_policy();
$employeedrop_prp_eng = load_employee_drop_mfq(3);
echo head_page($title, true);
$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
]; ?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>

<div id="filter-panel" class="collapse filter-panel">
    <form role="form" id="progress_filter" class="" autocomplete="off">
        <input type="hidden" name="<?= $token_details['name']; ?>" value="<?= $token_details['hash']; ?>"/>
        <div class="row">

            <div class="custom_paddingas">
                <div class="form-group col-sm-4">
                    <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date'); ?><!--Date--></label><br>
                    <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from'); ?><!--Date--></label>
                    <input type="text" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy; ?>'"
                        size="16" onchange="Otable.draw()" value="" id="IncidateDateFrom"
                        class="input-small">
                    <label for="supplierPrimaryCode">&nbsp;&nbsp;<?php echo $this->lang->line('common_to'); ?><!--To-->&nbsp;&nbsp;</label>
                    <input type="text" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy; ?>'"
                        size="16" onchange="Otable.draw()" value="" id="IncidateDateTo"
                        class="input-small">
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"> Client</label> <br>
                <!--Customer Name-->
                <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="oTable.draw()" multiple="multiple"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"> Project Focal </label> <br>
                <?php echo form_dropdown('proposalengID[]', $employeedrop_prp_eng, '', 'class="form-control" id="proposalengID" onchange="oTable.draw()" multiple="multiple"'); ?>
            </div>

            <div class="form-group col-sm-2">
                    <label for="">Category</label>
                    <select name="category[]" class="form-control " id="category" multiple="multiple" onchange="oTable.draw()">
                        <option value="1">EPC</option>
                        <option value="2">WT</option>
                        
                    </select>
            </div>

            <!-- <div class="form-group col-sm-2">
                    <label for="">Current Status</label>
                    <select name="currenttatus[]" class="form-control " id="currenttatus" multiple="multiple" onchange="oTable.draw()">
                        <option value="1">ON GOING </option>
                        <option value="2">HOLD</option>
                        <option value="2">COMPLETED</option>
                        
                    </select>
            </div>

            <div class="form-group col-sm-2 pt-3">
                    <label for="">MIC No</label>
                    <select name="micno[]" class="form-control " id="micno" multiple="multiple" onchange="oTable.draw()">
                        <option value="1">UAE</option>
                        <option value="2">INDIA</option>
                        
                    </select>
            </div> -->

            
            <div class="form-group col-sm-2">
                <label for=""></label>
                <button type="button" class="btn btn-primary "
                        onclick="clear_all_filters()" ><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?>
                </button>
            </div>
            
        </div>
    </form>
</div>
<div class="row" style="margin-top: 2%;">
<!-- <form role="form" id="tenderLog_filter" class="" autocomplete="off"></form> -->
    <div class="col-sm-7">
        <button type="button" data-text="Add" id="btnAdd" class="btn btn-sm btn-primary" onclick="fetchPage('system/mfq/mfq_overall_project_progress',null,'Report','MFQ');">
            Reports
        </button>
        <button type="button" data-text="Add" id="btnAdd" class="btn btn-sm btn-default" onclick="fetchPage('system/mfq/mfq_total_project',null,'Report','MFQ');">
            Total Project
        </button>
    </div>
    <div class="col-md-5 text-right">
        
        <button type="button" data-text="Add" id="btnAdd"
                onclick="project_progress_excel()"
                class="btn btn-sm btn-success">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
            <?php echo $this->lang->line('common_excel') ?><!--Excel-->
        </button>
    </div>
</div>
<div id="" style="margin-top: 10px">
    <div class="table-responsive">
        <table id="progress_entry_table" class="table table-striped table-condensed" width="100%">
            <thead>
            <tr>
            <th style="min-width: 5%">#</th>

            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_mic_no');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_tender_no');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_estimate_no');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_job_num');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_client');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_category');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_client_po_ref');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_project_focal');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_po_value');?></th>

            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_delivery');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_committed_date');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_actual_date');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_month');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_year');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_des');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_c_status');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_engg');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_remark');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_PR');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_re2');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_po');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_re3');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_fab');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_nde');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_hydro');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_paint');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_fat');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_re4');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_mrb');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_pl');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_over_pro');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_total');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_project_with');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_va_amount');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_status_variation');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_estimate_pl');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_pL');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_delivery_note');?></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('manufacturing_overall_progress_clo_goods');?></th>
            </tr>
            </thead>
        </table>
    </div>
</div>

</div>
</div>
</div>
</div>




<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var oTable;
    var allSelected = 0;
    //tenderLog_table();
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_tender_log_templates', 'Test', 'Tender Logs');
        });
        $("#search_cancel").hide();
        
        $(".filter").change(function () {
            oTable.draw();
            $("#search_cancel").show();
        });

        tenderLog_table();

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

       

        $("#search_cancel").click(function () {
            $(".filter").val('');
            oTable.draw();
            $(this).hide();
        });

        $('#customerCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('#proposalengID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            onSelectAll: function () {
                allSelected = 1;
            },
            onChange: function () {
                allSelected = 0;
            },
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#category').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            onSelectAll: function () {
                allSelected = 1;
            },
            onChange: function () {
                allSelected = 0;
            },
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#currenttatus').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            onSelectAll: function () {
                allSelected = 1;
            },
            onChange: function () {
                allSelected = 0;
            },
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });


        $('#micno').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            onSelectAll: function () {
                allSelected = 1;
            },
            onChange: function () {
                allSelected = 0;
            },
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

       
        Inputmask().mask(document.querySelectorAll("input"));
    });


    function tenderLog_table() {
        oTable = $('#progress_entry_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_CustomerInquiry/fetch_project_progress_entry'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "columnDefs": [{"targets": [18], "orderable": false}],
            "aoColumns": [
                {"mData": "ciMasterID"},
                {"mData": "pending_col"},
                {"mData": "ciCode"},
                {"mData": "estimateCode"},
                {"mData": "documentCode"},
                {"mData": "CustomerName"},
                {"mData": "category"},
                {"mData": "poNumber"},
                {"mData": "Ename2"},
                {"mData": "estAmount"},
                {"mData": "poDate"},
                {"mData": "closedDate"},
                {"mData": "pending_col"},
                {"mData": "month_data"},
                {"mData": "year_data"},
                {"mData": "jobdescription"},
                {"mData": "jobStatus"},

                {"mData": "job_process1"},
                {"mData": "job_process2"},
                {"mData": "job_process3"},
                {"mData": "job_process4"},
                {"mData": "job_process5"},
                {"mData": "job_process6"},
                {"mData": "job_process7"},
                {"mData": "job_process8"},
                {"mData": "job_process9"},
                {"mData": "job_process10"},
                {"mData": "job_process11"},
                {"mData": "job_process12"},
                {"mData": "job_process13"},
                {"mData": "job_process14"},

                {"mData": "pending_col"},
                {"mData": "qty"},
                {"mData": "pending_col"},
                {"mData": "pending_col"},
                {"mData": "pending_col"},
                {"mData": "pending_col"},
                {"mData": "pending_col"},
                {"mData": "deliveryNoteCode"},
                {"mData": "pending_col"},

                
                
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({name: 'IncidateDateFrom', value: $('#IncidateDateFrom').val()});
                aoData.push({name: 'IncidateDateTo', value: $('#IncidateDateTo').val()});
                aoData.push({name: 'customerCode', value: $('#customerCode').val()});
                aoData.push({name: 'proposalengID', value: $('#proposalengID').val()});
                aoData.push({name: 'category', value: $('#category').val()});
                // aoData.push({name: 'micno', value: $('#micno').val()});
                // aoData.push({name: 'currenttatus', value: $('#currenttatus').val()});
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

    function clear_all_filters(){
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");

        $('#customerCode').multiselect2('deselectAll', false);
        $('#customerCode').multiselect2('updateButtonText');

        $('#proposalengID').multiselect2('deselectAll', false);
        $('#proposalengID').multiselect2('updateButtonText');

        $('#category').multiselect2('deselectAll', false);
        $('#category').multiselect2('updateButtonText');

        $('#micno').multiselect2('deselectAll', false);
        $('#micno').multiselect2('updateButtonText');


        $('#currenttatus').multiselect2('deselectAll', false);
        $('#currenttatus').multiselect2('updateButtonText');

        oTable.draw();
    }

    

    function project_progress_excel()
    {
        var form = document.getElementById('progress_filter');
        form.target = '_blank';
        form.method = 'post';
        form.action = '<?php echo site_url('MFQ_CustomerInquiry/fetch_project_process_log_excel'); ?>';
        form.submit();

        
    } 
</script>