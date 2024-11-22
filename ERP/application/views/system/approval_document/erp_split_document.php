<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('reversing');
$title = 'Split Document Amount';
echo head_page($title, true);

$doc_arr = [
    'PO'=>'PO | Purchase Order','GRV'=>'GRV | Goods Received Voucher','SR'=>'SR | Stock Return','ST'=>'ST | Stock Transfer','BSI'=>'BSI | Supplier Invoice',
    'PV'=>'PV | Payment Voucher','MI'=>'MI | Material Issue','DN'=>'DN | Debit Note','CINV'=>'CINV | Customer Invoice','RV'=>'RV | Receipt Voucher',
    'MRV'=>'MRV | Material Receipt Voucher','MRN'=>'MRN | Material Receipt Note','CN'=>'CN | Credit Note','QUT'=>'QUT | Quotation','CNT'=>'CNT | Contract',
    'SO'=>'SO | Sales Order','SP'=>'SP | Salary Process','SPN'=>'SPN | Non Salary Process','SD'=>'SD | Salary Declaration','JV'=>'JV | Journal Voucher',
    'BT'=>'BT | Bank Transfer','SLR'=>'SLR | Sales Return','FA'=>'FA | Fixed Asset','ATS'=>'ATS | Attendance Summary','PRQ'=>'PRQ | Purchase Request',
    'MR'=>'MR | Material Request','DO'=>'DO | Delivery Order','HCINV'=>'HCINV | Invoice (Buyback)','BRC'=>'BRC | Bank Reconciliation','INV'=>'INV | Item Master',
    'SUP'=>'SUP | Supplier Master', 'EC'=> 'EC | Expense Claim', 'P'=> 'P | Project', 'FAD' => 'FAD | Fixed Asset Depreciation', 'SC' => 'SC | Sales Commission',
    'RJV' => 'RJV | Recurring Journal Voucher', 'BD' => 'BD | Budget', 'ADSP' => 'ADSP | Asset Disposal'
]

?>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-4">
            <div class="custom_padding">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_date');?><!--Date--></label><br>
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from');?><!--From--></label>
                <input class="input-small" id="IncidateDateFrom" data-date="" data-date-format="dd-mm-yyyy" size="11"
                       type="text" name="IncidateDateFrom" placeholder="DD-MM-YYYY" value="" onchange="fetch_reversing_approval_table()">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_to');?><!--To-->&nbsp;&nbsp;</label>
                <input class="input-small" id="IncidateDateTo" data-date="" data-date-format="dd-mm-yyyy" size="11"
                       type="text" name="IncidateDateTo" placeholder="DD-MM-YYYY" value="" onchange="fetch_reversing_approval_table()">
            </div>
        </div>
        <div class="form-group col-sm-2">
            <label for="documentID"> <?php echo $this->lang->line('config_document_type');?><!--Document type--></label><br>
            <?php echo form_dropdown('documentID[]', $doc_arr, '', 'class="form-control" id="documentID" onchange="fetch_reversing_approval_table()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-2">
            <label for="">&nbsp;</label><br> &nbsp;&nbsp;&nbsp;
            <button type="button" class="btn btn-primary"
                    onclick="clear_all_filters()" style="/*margin-top: -10%;*/"><i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?><!--Clear-->
            </button>

           
        </div>

        <button type="button" class="btn btn-primary"
                    onclick="load_split_modal()" style="/*margin-top: -10%;*/"><i class="fa fa-paint-brush"></i> <?php echo 'Split'?><!--Clear-->
            </button>
    </div>
</div>
<div class="table-responsive">
    <table id="reversing_approval_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th colspan="4"><?php echo $this->lang->line('common_document');?><!--Document--></th>
            <th colspan="4"><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
        </tr>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_employee');?><!--Employee--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th style="min-width: 20%"> <?php echo $this->lang->line('common_comments');?><!--Comments--></th>
            <th style="min-width: 5%">&nbsp;</th>
        </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="split_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
       
            </div>
            <div class="modal-body">
                <div class="" id="split_modal_body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="submitSplitDocument()"><?php echo $this->lang->line('common_submit'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    fetch_reversing_approval_table();
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/approval_document/erp_approval_document','','Reversing Approved Document');
        });

        $('#supplierPrimaryCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#documentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#date').datepicker({
            format: 'yyyy-mm-dd'
        });
        $('#IncidateDateTo').datepicker({
            format: 'yyyy-mm-dd'
        });
        $('#IncidateDateFrom').datepicker({
            format: 'yyyy-mm-dd'
        });

        $('#reversing_approval_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                auto_id       : {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_auto_id_is_required');?>.'}}},/*Auto ID is required*/
                comments      : {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_comments_are_required');?>.'}}},/*Comments are required*/
                document_id   : {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_document_id_is_required');?>.'}}},/*Document ID is required*/
                document_code : {validators: {notEmpty: {message: '<?php echo $this->lang->line('config_document_code_is_required');?>.'}}}/*Document Code is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            var document_code = $('#document_code').val();

            var massage = (document_code == 'SP')? "All the bank transfers related with this payroll will be deleted" : "<?php echo $this->lang->line('config_you_want_to_reverse_this_record');?>";/*You want to reverse this document!*/

            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: massage,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Reversing_approval/reversing_approval_document'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            if(document_code =='BRC')
                            {
                               $('#myModalLabel_title').html('You cannot process this document reversal. Because Bank Reconciliation documents for this selected bank are created with future dates');
                            }else
                            {
                                $('#myModalLabel_title').html('You cannot process this document reversal. Because this document has been pulled for documents listed below.');
                            }
                            refreshNotifications(true);
                            if (data['status']=='A') {
                                    $('#access_denied_body').empty();x = 1;
                                    if (jQuery.isEmptyObject(data['data'])) {
                                        $('#access_denied_body').append('<tr class="danger"><td colspan="2" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                                    }
                                    else {
                                        $.each(data['data'], function (key, value) {
                                            $('#access_denied_body').append('<tr><td>' + x + '</td><td>' + value['system_code'] + '</td></tr>');
                                            x++;
                                        });
                                    }
                                    $('#access_denied').modal('show');

                            }else{
                                $('#reversing_approval_document').modal('hide');
                                fetch_reversing_approval_table();
                                $('#reversing_approval_form')[0].reset();
                                $('#reversing_approval_form').bootstrapValidator('resetForm', true);
                            }
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        });
    });

    function fetch_reversing_approval_table(selectedID=null) {
        var Otable = $('#reversing_approval_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Reversing_approval/fetch_reversing_split_approval'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null)? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if( parseInt(oSettings.aoData[x]._aData['documentApprovedID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "invoiceID"},
                {"mData": "documentSystemCode"},
                {"mData": "invoiceCode"},
                {"mData": "invoiceDate"},
                {"mData": "wareHouseDescription"},
                {"mData": "invoiceDate"},
                {"mData": "documentCode"},
                {"mData": "action"}
                //{"mData": "edit"},
            ],
            "columnDefs": [{"visible":true,"searchable": true,"targets": [1,2,3,4,5,6] }, {"visible":true,"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "supplierPrimaryCode", "value": $("#supplierPrimaryCode").val()});
                aoData.push({"name": "documentID", "value": $("#documentID").val()});
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

    function reversing_split_amount(documentSystemCode){
       
        $.ajax({
            async: true,
            type: 'post',
            //dataType: 'json',
            data: {'documentSystemCode': documentSystemCode},
            url: "<?php echo site_url('Reversing_approval/load_reversing_split_amount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#split_modal_body').html(data);
                $('#split_modal').modal('show');
            }, error: function () {
                stopLoad();
                //swal("Cancelled", "Your file is safe :)", "error");
            }
        });


    }

 

   

</script>