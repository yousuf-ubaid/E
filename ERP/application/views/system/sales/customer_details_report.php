<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = 'Customer Detail Report';
echo head_page($title, true);

$customer_arr =all_customer_drop(false);
$customerCategory    = party_category(1, false);
$currncy_arr    = all_currency_new_drop(false);
$usergroupcompanywiseallow = getPolicyValuesgroup('CM','All');

?>

<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <?php echo form_open('', 'role="form" id="customermaster_filter_form"'); ?>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_customer_name');?> </label><br><!--Customer Name-->
            <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="Otable.draw(), ODeltable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_category');?> </label><br><!--Category-->
            <?php echo form_dropdown('category[]', $customerCategory, '', 'class="form-control" id="category" onchange="Otable.draw(), ODeltable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_currency');?> </label><br><!--Currency-->
            <?php echo form_dropdown('currency[]', $currncy_arr, '', 'class="form-control" id="currency" onchange="Otable.draw(), ODeltable.draw()" multiple="multiple"'); ?>
        </div>

        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">&nbsp;</label><br>

            <button type="button" class="btn btn-sm btn-primary pull-right"
                    onclick="clear_all_filters()" style=""><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?>
            </button><!--Clear-->
        </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <div>
            <h5><span class="glyphicon glyphicon-stop" style="color:green; font-size:15px;"></span>&nbsp<b><?php echo $this->lang->line('common_active');?></b></h5>
            <h5><span class="glyphicon glyphicon-stop" style="color:red; font-size:15px;"></span>&nbsp<b><?php echo $this->lang->line('common_in_active');?></b></h5>
        </div>
     
    </div>
    <div class="col-md-7 text-right">
        <a href="#" type="button" class="btn btn-excel " style="margin-left: 2px" onclick="excel_export()">
            <i class="fa fa-file-excel-o"></i> Excel <!--Excel-->
        </a>
    </div>
</div><hr>

<div class="row" style="padding-left: 2%">
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#customers" data-toggle="tab" onclick="Otable.draw()"><?php echo $this->lang->line('sales_maraketing_masters_customer');?></a></li>
        <li><a href="#deleted" data-toggle="tab" onclick="ODeltable.draw()"><?php echo $this->lang->line('sales_maraketing_masters_deleted_customer');?></a></li>
    </ul>
</div>
<br>
<div class="tab-content">
    <div class="tab-pane active" id="customers">
        <div class="table-responsive">
            <table id="customer_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 20px">#</th>
                    <th style="min-width: 120px">Customer Code</th>
                    <th style="min-width: 100px">Name</th>
                    <th style="min-width: 100px">Address</th>
                    <th>Secondary Code</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    <th>ID Card</th>
                    <th>Credit Period</th>
                    <th>Credit Limit</th>
                    <th>VAT Eligible</th>
                    <th>VAT No</th>
                    <th>Rebate(%)</th>
                    <th>Category</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th style="min-width: 80px">Action</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="tab-pane" id="deleted">
        <div class="table-responsive">
            <table id="customer_deleted_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                <th style="min-width: 20px">#</th>
                    <th style="min-width: 120px">Customer Code</th>
                    <th style="min-width: 100px">Name</th>
                    <th style="min-width: 100px">Address</th>
                    <th>Secondary Code</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    <th>ID Card</th>
                    <th>Credit Period</th>
                    <th>Credit Limit</th>
                    <th>VAT Eligible</th>
                    <th>VAT No</th>
                    <th>Rebate(%)</th>
                    <th>Category</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot','Left foot',false); ?>

<script type="text/javascript">
    var Otable;
    var ODeltable;
$(document).ready(function() {
    $('.headerclose').click(function(){
        fetchPage('system/sales/customer_details_report');
    });
    $('#customerCode').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });
    $('#category').multiselect2({
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
    customer_table();
    customer_deleted_table();
});

function customer_table(){
     Otable = $('#customer_table').DataTable({
         "language": {
             "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
         },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "StateSave": true,
        "sAjaxSource": "<?php echo site_url('Customer/customer_detailed_report'); ?>",
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
            {"mData": "customerAutoID"},
            {"mData": "customerSystemCode"},
            {"mData": "customerName"},
            {"mData": "customerAddress1"},
            {"mData": "secondaryCode"},
            {"mData": "customerEmail"},
            {"mData": "customerTelephone"},
            {"mData": "IdCardNumber"},
            {"mData": "customerCreditPeriod"},
            {"mData": "customerCreditLimit"},
            {"mData": "vatEligible",
                render: function(data) {
                    if(data == 1) {
                        return 'No'
                    } else if(data == 2) {
                        return 'Yes' 
                    } else {
                        return '-'
                    }
                }    
            },   
            {"mData": "vatIdNo"},
            {"mData": "rebatePercentage"},
            {"mData": "categoryDescription"},
            {"mData": "amt"},
            {"mData": "confirmed"},
            {"mData": "edit"}   
                
        ],

        "fnServerData": function (sSource, aoData, fnCallback) {
            
            aoData.push({"name": "customerCode", "value": $("#customerCode").val()});
            aoData.push({"name": "category", "value": $("#category").val()});
            aoData.push({"name": "currency", "value": $("#currency").val()});
            aoData.push({"name": "deletedYN", "value": 0});
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

function customer_deleted_table(){
    ODeltable = $('#customer_deleted_table').DataTable({
         "language": {
             "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
         },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "StateSave": true,
        "sAjaxSource": "<?php echo site_url('Customer/customer_detailed_report'); ?>",
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
            {"mData": "customerAutoID"},
            {"mData": "customerSystemCode"},
            {"mData": "customerName"},
            {"mData": "customerAddress1"},
            {"mData": "secondaryCode"},
            {"mData": "customerEmail"},
            {"mData": "customerTelephone"},
            {"mData": "IdCardNumber"},
            {"mData": "customerCreditPeriod"},
            {"mData": "customerCreditLimit"},
            {"mData": "vatEligible"},   
            {"mData": "vatIdNo"},
            {"mData": "rebatePercentage"},
            {"mData": "categoryDescription"},
            {"mData": "amt"},
            {"mData": "confirmed"}, 
        ],
   
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({"name": "customerCode", "value": $("#customerCode").val()});
            aoData.push({"name": "category", "value": $("#category").val()});
            aoData.push({"name": "currency", "value": $("#currency").val()});
            aoData.push({"name": "deletedYN", "value": 1});
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

function delete_customer(id){
    swal({
        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
        text: "<?php echo $this->lang->line('sales_maraketing_masters_you_want_to_delete_this_customer');?>",/*You want to delete this customer!*/
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
            data : {'customerAutoID':id},
            url :"<?php echo site_url('Customer/delete_customer'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                refreshNotifications(true);
                Otable.draw();
                ODeltable.draw();
            },error : function(){
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    });        
}

function clear_all_filters() {
    $('#customerCode').multiselect2('deselectAll', false);
    $('#customerCode').multiselect2('updateButtonText');
    $('#category').multiselect2('deselectAll', false);
    $('#category').multiselect2('updateButtonText');
    $('#currency').multiselect2('deselectAll', false);
    $('#currency').multiselect2('updateButtonText');
    Otable.draw();
    ODeltable.draw();
}
function createcustomer() {
    swal(" ", "You do not have permission to create  customer master at company level,please contact your system administrator.", "error");
}


function excel_export() {

    var form = document.getElementById('customermaster_filter_form');
    form.target = '_blank';
    form.method = 'post';
    form.post = $('#customermaster_filter_form').serializeArray();
    form.action = '<?php echo site_url('Customer/export_excel_customer_master'); ?>';
    form.submit();
}

</script>