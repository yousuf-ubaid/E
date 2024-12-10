<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
/*$title = $this->lang->line('sales_markating_transaction_sales_commission');
echo head_page($title, false);*/
echo head_page($_POST['page_name'], false);


$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
?>
<div id="filter-panel" class="collapse filter-panel">
  
</div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?> / <?php echo $this->lang->line('common_approved');?>

                </td><!--Confirmed--><!--Approved-->
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?>
                    / <?php echo $this->lang->line('common_not_approved');?>
                </td><!--Not Confirmed--><!--Not Approved-->
                <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?>
                </td><!--Refer-back-->
            </tr>
        </table>
    </div>
    <div class="col-md-7 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="fetchPage('system/sales/sale_commission_generate',null,' Generate Sales Commission','SC');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('sales_markating_transaction_sales_commission_generate_sales_commission');?>
        </button><!--Generate Sales Commission-->
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="sales_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_code');?></th><!--Code-->
            <th style="min-width: 15%"><?php echo $this->lang->line('common_as_of_date');?></th><!--As Of Date-->
            <th style="min-width: 25%"><?php echo $this->lang->line('common_details');?></th><!--Details-->
            <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_total_value');?> </th><!--Total Value-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?> </th><!--Confirmed-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved');?> </th><!--Approved-->
            <th style="min-width: 6%"><?php echo $this->lang->line('common_action');?> </th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    sales_table();
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/sales/sales_commission','','Sales Commission');
        });

        // $('#supplierPrimaryCode').multiselect2({
        //     enableCaseInsensitiveFiltering: true,
        //     includeSelectAllOption: true,
        //     numberDisplayed: 1,
        //     buttonWidth: '180px',
        //     maxHeight: '30px'
        // });

        // Inputmask().mask(document.querySelectorAll("input"));
    });

    function sales_table(selectedID=null) {
         Otable = $('#sales_table').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Sales/fetch_sales_commission'); ?>",
            "aaSorting": [[0, 'desc']],
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
                    if( parseInt(oSettings.aoData[x]._aData['salesCommisionID']) == selectedRowID ){
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "salesCommisionID"},
                {"mData": "salesCommisionCode"},
                {"mData": "asOfDate"},
                {"mData": "detail"},
                {"mData": "total_value"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"},
                {"mData": "Description"},
                {"mData": "referenceNo"},
                {"mData": "transactionAmount_search"}
            ],
             "columnDefs": [{"targets": [6], "orderable": false},{"visible":false,"searchable": true,"targets": [8,9,10] },{"targets": [0], "visible": true,"searchable": false}],

            // "columnDefs": [{"visible": true, "searchable": true,"targets": [1,2,3,4]},{"targets": [0], "searchable": false},{"visible":false,"searchable": true,"targets": [8,9,10] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "datefrom", "value": $("#IncidateDateFrom").val()});
                aoData.push({"name": "dateto", "value": $("#IncidateDateTo").val()});
                aoData.push({"name": "status", "value": $("#status").val()});
                aoData.push({"name": "supplierPrimaryCode", "value": $("#supplierPrimaryCode").val()});
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


    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
    

    function delete_item(id, value) {
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
                    data: {'salesCommisionID': id},
                    url: "<?php echo site_url('Sales/delete_sc'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data['type'], data['message'], 1000);;
                        Otable.draw();
                        stopLoad();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function referbacksc(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back*/
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
                    data: {'salesCommisionID': id},
                    url: "<?php echo site_url('Sales/referbacksc'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otable.draw();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters(){
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#status').val("all");
        $('#supplierPrimaryCode').multiselect2('deselectAll', false);
        $('#supplierPrimaryCode').multiselect2('updateButtonText');
        Otable.draw();
    }

    function reOpen_contract(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_re_open');?>",/*You want to re open!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'salesCommisionID':id},
                    url :"<?php echo site_url('Sales/re_open_salescommishion'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        Otable.draw();
                        stopLoad();
                        refreshNotifications(true);
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>