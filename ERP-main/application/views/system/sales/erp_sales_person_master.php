<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_maraketing_masters_sales_person_master');
echo head_page($title, false);


/*echo head_page('Sales Person Master',false);*/
$location_arr   = all_delivery_location_drop();
$gl_code_arr    = supplier_gl_drop();
$currncy_arr    = all_currency_new_drop();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td>
                    <span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_active');?>
                </td><!--Active-->
                <td>
                    <span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_in_active');?>
                </td><!--Inactive-->
            </tr>
        </table> 
    </div>
    <div class="col-md-7 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="fetchPage('system/sales/erp_sales_person_new','','Sales Person')" ><i class="fa fa-plus"></i> <?php echo $this->lang->line('sales_maraketing_masters_new_sales_person');?>  </button><!--New Sales Person-->
    </div>
</div><hr>
<div class="table-responsive">
    <table id="sales_person_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?> </th><!--Code-->
                <th style="min-width: 10%"><?php echo $this->lang->line('sales_maraketing_masters_reference');?></th><!--Reference-->
                <th style="min-width: 60%"><?php echo $this->lang->line('common_details');?></th><!--Details-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?></th><!--Status-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
var salesPersonID = null;
var Otable;
$(document).ready(function() {
    $('.headerclose').click(function(){
        fetchPage('system/sales/erp_sales_person_master','','Sales Person');
    });
    sales_person_table();
});

function sales_person_table(){
     Otable = $('#sales_person_table').DataTable({
         "language": {
             "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
         },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "StateSave": true,
        "sAjaxSource": "<?php echo site_url('Customer/fetch_sales_person'); ?>",
        "aaSorting": [[0, 'desc']],
        "fnInitComplete": function () {

        },
        "fnDrawCallback": function (oSettings) {
            var tmp_i = oSettings._iDisplayStart;
            var iLen = oSettings.aiDisplay.length;
            var x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                x++;
            }
        },
        "aoColumns": [
            {"mData": "salesPersonID"},
            {"mData": "SalesPersonCode"},
            {"mData": "SecondaryCode"},
            {"mData": "SalesPerson_detail"},
            {"mData": "confirmed"},
            {"mData": "edit"},
            {"mData": "SalesPersonName"},
            {"mData": "wareHouseLocation"},
            {"mData": "contactNumber"},
            {"mData": "SalesPersonEmail"}
        ],
        "columnDefs": [{"visible":false,"searchable": true,"targets": [6,7,8,9] }, { "target": [0,3,4,5], "searchable": false}, {"targets": [5], "orderable": false}],
        "fnServerData": function (sSource, aoData, fnCallback) {
            //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
            //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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

function delete_sales_person(id){
    swal({
        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
        text: "<?php echo $this->lang->line('sales_maraketing_masters_you_want_to_delete_this_file');?>",/*You want to delete this file!*/
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
    },
    function () {
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'salesPersonID':id},
            url :"<?php echo site_url('Customer/delete_sales_person'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                if (data['status']) {
                    Otable.draw();
                }
            },error : function(){
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    });        
}
</script>