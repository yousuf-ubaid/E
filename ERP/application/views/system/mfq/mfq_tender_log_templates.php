<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_tender_header');
$customer_arr = all_mfq_customer_drop(false);
$date_format_policy = date_format_policy();
$employeedrop_prp_eng = load_employee_drop_mfq(3);
$country = load_srm_country_mfq(false);
$orderStatusArr=load_cus_inquiry_order_status(false);
$docStatusArr=load_cus_inquiry_document_status(false);
$rfqStatusArr=load_cus_inquiry_rfq_status(false);
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
    <form role="form" id="tenderLog_filter" class="" autocomplete="off">
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
                <label for="supplierPrimaryCode"> Estimator </label> <br>
                <?php echo form_dropdown('proposalengID[]', $employeedrop_prp_eng, '', 'class="form-control" id="proposalengID" onchange="oTable.draw()" multiple="multiple"'); ?>
            </div>

            <div class="form-group col-sm-2">
                    <label for="">RFQ Type</label>
                    <select name="rfqType[]" class="form-control " id="rfqType" multiple="multiple" onchange="oTable.draw()">
                        <option value="1">Tender</option>
                        <option value="2">RFQ</option>
                        <option value="3">SPC</option>
                        
                    </select>
            </div>

            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"> RFQ Status </label> <br>
                <?php echo form_dropdown('rfqstatus[]', $rfqStatusArr, '', 'class="form-control" id="rfqstatus" onchange="oTable.draw()" multiple="multiple"'); ?>
            </div>

            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"> Micoda Operation </label> <br>
                <?php echo form_dropdown('micoda[]', $country, '', 'class="form-control" id="micoda" onchange="oTable.draw()" multiple="multiple"'); ?>
            </div>

            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"> Status </label> <br>
                <?php echo form_dropdown('nstatus[]', $docStatusArr, '', 'class="form-control" id="nstatus" onchange="oTable.draw()" multiple="multiple"'); ?>
            </div>

            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"> Order Status </label> <br>
                <?php echo form_dropdown('orderstatus[]', $orderStatusArr, '', 'class="form-control" id="orderstatus" onchange="oTable.draw()" multiple="multiple"'); ?>
            </div>

            <div class="form-group col-sm-2">
                <label for=""></label>
                <button type="button" class="btn btn-primary "
                        onclick="clear_all_filters()" ><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?>
                </button>
            </div>
            
        </div>
    </form>
</div>
<div class="row pt-0">
<!-- <form role="form" id="tenderLog_filter" class="" autocomplete="off"></form> -->
    <div class="col-sm-7">               
        <?php
           include 'mfq_tender_btn_nav.php';
        ?>
    </div>
    <div class="col-md-5 text-right">
        
        <button type="button" data-text="Add" id="btnAdd"
                onclick="tenderLog_excel()"
                class="btn btn-sm btn-success">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
            <?php echo $this->lang->line('common_excel') ?><!--Excel-->
        </button>
    </div>
</div>
<div id="" style="margin-top: 10px">
    <div class="table-responsive">
        <table id="tenderLog_table" class="table table-striped table-condensed" width="100%">
            <thead>
            <tr>
                <th style="min-width: 2%">#</th>
                <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('manufacturing_tender_SLno') ?></th>
                <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('manufacturing_tender_tender_no') ?></th>
                <th class="text-uppercase" style="min-width: 6%"><?php echo $this->lang->line('manufacturing_tender_client') ?></th>
                <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('manufacturing_tender_description') ?></th>
                <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('manufacturing_tender_category') ?></th>
                <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('manufacturing_tender_price') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_rfq_type') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_micoda_operation') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_rfq_originator') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_source') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_Estimator') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_month') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_year') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_rfq_status') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_status') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_order_status') ?></th>

                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_assigned_date') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_submission_date') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_actual_submission_date') ?></th>

                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_submission_status') ?></th>

                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_alloted_manhours') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_actual_manhours') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_no_of_days_delayed') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_total') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_rev') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_po_received_date') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_po_number') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_project_number') ?></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_tender_remark') ?></th>
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

        $('#rfqType').multiselect2({
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

        $('#rfqstatus').multiselect2({
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

        $('#micoda').multiselect2({
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

        $('#nstatus').multiselect2({
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

        $('#orderstatus').multiselect2({
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

        $("#mfq_job").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        Inputmask().mask(document.querySelectorAll("input"));
    });


    function tenderLog_table() {
        oTable = $('#tenderLog_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_CustomerInquiry/fetch_tender_logs'); ?>",
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
                {"mData": "ciMasterID"},
                {"mData": "ciCode"},
                {"mData": "CustomerName"},
                {"mData": "description"},
                {"mData": "cat"},
                {"mData": "estAmount"},
                {"mData": "rfq_type"},
                {"mData": "CountryDes"},
                {"mData": "confirmedByName"},
                {"mData": "source"},
                {"mData": "Ename2"},
                {"mData": "month_data"},
                {"mData": "year_data"},
                {"mData": "rfq_status"},
                {"mData": "docstatus"},
                {"mData": "order_status"},
                {"mData": "documentDate"},
                {"mData": "dueDate"},
                {"mData": "deliveryDate"},
                {"mData": "submission_status"},

                {"mData": "pending_col"},
                {"mData": "crew"},

                {"mData": "delayed"},

                {"mData": "pending_col"},
                {"mData": "pending_col"},

                {"mData": "poDate"},
                {"mData": "poNumber"},

                {"mData": "jobNumber"},

                {"mData": "materialCertificationComment"},
                
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({name: 'IncidateDateFrom', value: $('#IncidateDateFrom').val()});
                aoData.push({name: 'IncidateDateTo', value: $('#IncidateDateTo').val()});
                aoData.push({name: 'customerCode', value: $('#customerCode').val()});
                aoData.push({name: 'proposalengID', value: $('#proposalengID').val()});
                aoData.push({name: 'rfqType', value: $('#rfqType').val()});
                aoData.push({name: 'rfqstatus', value: $('#rfqstatus').val()});
                aoData.push({name: 'micoda', value: $('#micoda').val()});
                aoData.push({name: 'nstatus', value: $('#nstatus').val()});
                aoData.push({name: 'orderstatus', value: $('#orderstatus').val()});
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

        $('#rfqType').multiselect2('deselectAll', false);
        $('#rfqType').multiselect2('updateButtonText');

        $('#rfqstatus').multiselect2('deselectAll', false);
        $('#rfqstatus').multiselect2('updateButtonText');

        $('#micoda').multiselect2('deselectAll', false);
        $('#micoda').multiselect2('updateButtonText');

        $('#nstatus').multiselect2('deselectAll', false);
        $('#nstatus').multiselect2('updateButtonText');

        $('#orderstatus').multiselect2('deselectAll', false);
        $('#orderstatus').multiselect2('updateButtonText');
        oTable.draw();
    }

    

    function tenderLog_excel()
    {
        var form = document.getElementById('tenderLog_filter');
        form.target = '_blank';
        form.method = 'post';
        form.action = '<?php echo site_url('MFQ_CustomerInquiry/fetch_tender_log_excel'); ?>';
        form.submit();

        
    } 
</script>