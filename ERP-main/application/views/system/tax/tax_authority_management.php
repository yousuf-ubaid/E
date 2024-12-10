<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->load->helpers('expense_claim');
$this->lang->load('common', $primaryLanguage);
$this->lang->load('tax', $primaryLanguage);
$title = $this->lang->line('tax_tax_authority');
echo head_page($title, false);
/*echo head_page('Tax Authority', true);*/

/*echo head_page('Supplier Master', true);*/
$supplier_arr = all_authority_drop(false);
$customerCategory    = party_category(2, false);
$currncy_arr    = all_currency_new_drop(false);
?>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_name');?><!--Name--></label><br>
            <?php echo form_dropdown('supplierCode[]', $supplier_arr, '', 'class="form-control" id="supplierCode" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_currency');?><!--Currency--></label><br>
            <?php echo form_dropdown('currency[]', $currncy_arr, '', 'class="form-control" id="currency" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">&nbsp;</label><br>

            <button type="button" class="btn btn-sm btn-primary pull-right"
                    onclick="clear_all_filters()" style=""><i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?><!--Clear-->
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-5">

    </div>
    <div class="col-md-7 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right"
                onclick="fetchPage('system/tax/erp_authority_master_new',null,'<?php echo  $this->lang->line('tax_add_new_authority')?>','AUT');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('tax_create_authority');?><!--Create Authority-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="authority_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('tax_authority_code');?></th><!--Authority Code-->
            <th style="min-width: 40%"><?php echo $this->lang->line('tax_authority_details');?></th><!--Authority Details-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/tax/tax_authority_management', '', 'Authority Master');
        });
        authority_table();
        $('#supplierCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('#currency').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        //$("#supplierCode").multiselect2('selectAll', true);
    });

    function authority_table() {
         Otable = $('#authority_table').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Authority/fetch_authority'); ?>",
            "aaSorting": [[0, 'desc']],
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
            "aoColumns": [
                {"mData": "taxAuthourityMasterID"},
                {"mData": "authoritySystemCode"},
                {"mData": "authority_detail"},
                {"mData": "edit"},
                {"mData": "AuthorityName"},
                {"mData": "authoritySecondaryCode"},
                {"mData": "address"},
                // {"mData": "currencyID"},
                {"mData": "email"},
                {"mData": "telephone"},
                // {"mData": "fax"}
            ],
            "columnDefs": [{"targets": [3], "orderable": false},{"visible":false,"searchable": true, "orderable": false, "targets": [4,5,6,7,8] }, {"searchable": false, "targets": [0,2]}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                aoData.push({"name": "supplierCode", "value": $("#supplierCode").val()});
                aoData.push({"name": "currency", "value": $("#currency").val()});
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

    function supplierbank() {
        Otable = $('#supplierbank_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('supplier/fetch_supplierbank'); ?>",
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
            "aoColumns": [
                {"mData": "supplierBankMasterID"},
                {"mData": "bankName"},
                {"mData": "bankAddress"},
                {"mData": "accountName"},
                {"mData": "accountNumber"},
                {"mData": "CurrencyCode"},
                {"mData": "swiftCode"},
                {"mData": "IbanCode"},




                {"mData": "edit"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {

                aoData.push({"name": "supplierAutoID", "value": supplierAutoID});

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


    function delete_authority(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'taxAuthourityMasterID': id},
                    url: "<?php echo site_url('Authority/delete_authority'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        Otable.draw();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters() {
        $('#supplierCode').multiselect2('deselectAll', false);
        $('#supplierCode').multiselect2('updateButtonText');
        $('#currency').multiselect2('deselectAll', false);
        $('#currency').multiselect2('updateButtonText');
        Otable.draw();
    }



</script>