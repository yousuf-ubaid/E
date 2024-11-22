<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = 'Commission Scheme Master';
echo head_page($title, true);

$department_array=fetch_employee_department2(false);
$designation_array=getDesignationDrop(false);
$doc_status = [
    'all' =>$this->lang->line('common_all'), '1' =>$this->lang->line('common_draft'),
    '2' =>$this->lang->line('common_confirmed'), '3' =>$this->lang->line('common_approved'),'4'=>'Refer-back'
];
?>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <?php echo form_open('', 'role="form" id="commission_scheme_form"'); ?>
        <div class="form-group col-sm-4">
            <label for="departmentFilter"><?php echo $this->lang->line('common_department');?> </label><br><!--Customer Name-->
            <?php echo form_dropdown('departmentFilter[]', $department_array, '', 'class="form-control" id="departmentFilter" onchange="Otable.draw(), OActiveItemtable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-4 schemeTab">
                <label for="statusFilter"><?php echo $this->lang->line('common_status');?> </label><br><!--Status-->
                <div style="width: 60%;">
                    <?php echo form_dropdown('statusFilter', $doc_status, '', 'class="form-control" id="statusFilter" onchange="Otable.draw() "'); ?>
                </div>
        </div>
        <div class="form-group col-sm-4 itemTab hide">
                <label for="designationFilter"><?php echo $this->lang->line('common_designation');?> </label><br><!--Status-->
                <div style="width: 60%;">
                    <?php echo form_dropdown('designationFilter[]', $designation_array, '', 'class="form-control" id="designationFilter" onchange="OActiveItemtable.draw()" multiple="multiple"'); ?>
                </div>
        </div>

        <div class="form-group col-sm-4">
            <label for="">&nbsp;</label><br>
            <button type="button" class="btn btn-sm btn-primary pull-right"
                    onclick="clear_all_filters()" style=""><i class="fa fa-paint-brush"></i><?php echo $this->lang->line('common_clear');?>
            </button><!--Clear-->
        </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?> / <?php echo $this->lang->line('common_approved');?>
                <!--Confirmed--> <!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?>
                    /<?php echo $this->lang->line('common_not_approved');?>                      <!-- Not Confirmed--><!--Not Approved-->
                </td>
                <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?> <!--Refer-back-->
                </td>
            </tr>   
        </table> 
    </div>
    <div class="col-md-7 text-right">
        <a href="#" type="button" class="btn btn-excel " style="margin-left: 2px" onclick="excel_export()">
            <i class="fa fa-file-excel-o"></i> Download Active Items
        </a> &nbsp;&nbsp;
        <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/sales/erp_commission_scheme_new','','Commision Scheme')" ><i class="fa fa-plus"></i> New Commission  </button><!--New Commission-->
    </div>
</div><hr>

<div class="row" style="padding-left: 2%">
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#commissionSchemes" data-toggle="tab" onclick="fetch_filters(1), Otable.draw()"><?php echo $this->lang->line('emp_master_commission_scheme');?></a></li>
        <li><a href="#activeItems" data-toggle="tab" onclick="fetch_filters(2), OActiveItemtable.draw()">Active Items</a></li>
    </ul>
</div>
<br>
<div class="tab-content">
    <div class="tab-pane active" id="commissionSchemes">
        <div class="table-responsive">
            <table id="commission_scheme_table" class="<?php echo table_class(); ?>">
                <thead>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_document_code');?> </th><!--Document Code-->
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_department');?> </th><!--Department-->
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_document_date');?></th><!--Document Date-->
                        <th style="min-width: 20%"><?php echo $this->lang->line('common_narration');?></th><!--Narration-->
                        <th style="min-width: 10%"><?php echo $this->lang->line('common_currency');?></th><!--Currency-->
                        <th style="width: 5%"><?php echo $this->lang->line('common_confirmed');?> <!--Confirmed--></th>
                        <th style="width: 5%"><?php echo $this->lang->line('common_approved');?> <!--Approved--></th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="tab-pane" id="activeItems">
        <div class="table-responsive">
            <table id="active_items_table" class="<?php echo table_class(); ?>">
                <thead>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_document_code');?> </th><!--Document Code-->
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_department');?> </th><!--Department-->
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_designation');?> </th><!--Department-->
                        <th style="min-width: 10%">Item Code<!--Item Code--></th>
                        <th style="min-width: 10%"><?php echo $this->lang->line('common_item_description'); ?><!--Item Description--></th>
                        <th style="min-width: 10%; text-align:right">Amount</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="documentPageView_cs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 1000000000;">
        <div class="modal-dialog" role="document" style="width:90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="documentPageViewTitle">Modal title</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-1">
                                <!-- Nav tabs -->
                            </div>
                            <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                                <!-- Tab panes -->
                                <div class="zx-tab-content">
                                    <div class="zx-tab-pane active" id="home-v">
                                        <div id="loaddocumentPageView" class="col-md-12"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
var schemeID = null;
var Otable;
var OActiveItemtable;
$(document).ready(function() {
    $('.headerclose').click(function(){
        fetchPage('system/sales/commission_scheme','','Commission Scheme');
    });
    $('#departmentFilter').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });
    $('#designationFilter').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });
    commission_scheme_table();
    active_items_table();
});

function fetch_filters(tabID) {
    if(tabID == 1) {
        $('.itemTab').addClass('hide');
        $('.schemeTab').removeClass('hide');
    } else {
        $('.itemTab').removeClass('hide');
        $('.schemeTab').addClass('hide');
    }
}

function commission_scheme_table(){
     Otable = $('#commission_scheme_table').DataTable({
         "language": {
             "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
         },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "StateSave": true,
        "sAjaxSource": "<?php echo site_url('CommissionScheme/fetch_commission_scheme '); ?>",
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
            $('.deleted').css('text-decoration', 'line-through');
            $('.deleted div').css('text-decoration', 'line-through');
                
        },
        "aoColumns": [
            {"mData": "schemeID"},
            {"mData": "documentCode"},
            {"mData": "department"},
            {"mData": "documentDate"},
            {"mData": "Narration"},
            {"mData": "CurrencyCode"},
            {"mData": "confirmed"},
            {"mData": "approved"},
            {"mData": "edit"}
            
        ],
        "columnDefs": [{ "target": [0,6,7,8], "searchable": false}, {"targets": [6,7,8], "orderable": false}],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({"name": "departmentFilter", "value": $("#departmentFilter").val()});
            aoData.push({"name": "statusFilter", "value": $("#statusFilter").val()});
            
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

function delete_commission_scheme(id){
    swal({
        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
        text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this file!*/
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
            data : {'schemeID':id},
            url :"<?php echo site_url('CommissionScheme/delete_commission_scheme'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    Otable.draw();
                    OActiveItemtable.draw();
                }
                
            },error : function(){
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            }
        });
    });        
}

function documentPageView_modal_CS(documentID, para1, para2, approval=1) {

    $("#profile-v").removeClass("active");
    $("#home-v").addClass("active");
    $("#TabViewActivation_attachment").removeClass("active");
    $("#tab_itemMasterTabF").removeClass("active");
    $("#TabViewActivation_view").addClass("active");
    attachment_View_modal(documentID, para1);
    $('#loaddocumentPageView').html('');
    var siteUrl;
    var paramData = new Array();
    var title = '';
    var a_link;
    var de_link;

    $("#itemMasterSubTab_footer_div").html('');
    $(".itemMasterSubTab_footer").hide();

    switch (documentID) {

        case "CS": // Commisson Scheme
            siteUrl = "<?php echo site_url('CommissionScheme/load_commission_scheme_confirmation'); ?>";
            paramData.push({name: 'schemeID', value: para1});
            paramData.push({name: 'approval', value: approval});
            title = "Commission Scheme";
            break;

        default:
            notification('Document ID is not set .', 'w');
            return false;
    }
    paramData.push({name: 'html', value: true});
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'html',
        data: paramData,
        url: siteUrl,
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            refreshNotifications(true);
            $('#documentPageViewTitle').html(title);
            $('#loaddocumentPageView').html(data);
            $('#documentPageView_cs').modal('show');
            $("#a_link").attr("href", a_link);
            $("#de_link").attr("href", de_link);
            $('.review').removeClass('hide');
            stopLoad();

        }, error: function () {
            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            /*An Error Occurred! Please Try Again*/
            stopLoad();
            refreshNotifications(true);
        }
    });
}

function referback_commission_scheme(id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure')?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_refer_back')?>",/*You want to refer back!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes')?>"/*Yes*/
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'schemeID': id},
                        url: "<?php echo site_url('CommissionScheme/referback_commission_scheme'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                commission_scheme_table();
                            }
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function reOpen_commissoinScheme(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_re_open');?>",/*You want to re open!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'schemeID':id},
                    url :"<?php echo site_url('CommissionScheme/re_open_cs'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                Otable.draw();
                                OActiveItemtable.draw();
                            }
                        
                        stopLoad();
                        refreshNotifications(true);
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

 function clear_all_filters(){
    $('#statusFilter').val("all");
    $('#departmentFilter').multiselect2('deselectAll', false);
    $('#departmentFilter').multiselect2('updateButtonText');
    Otable.draw();
    OActiveItemtable.draw();
}

function active_items_table(){
    OActiveItemtable = $('#active_items_table').DataTable({
         "language": {
             "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
         },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "StateSave": true,
        "sAjaxSource": "<?php echo site_url('CommissionScheme/fetch_active_items_cs '); ?>",
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
            //$('.deleted').css('text-decoration', 'line-through');
            //$('.deleted div').css('text-decoration', 'line-through');
                
        },
        "aoColumns": [
            {"mData": "schemeDetailID"},
            {"mData": "DocumentCode"},
            {"mData": "DepartmentDes"},
            {"mData": "DesDescription"},
            {"mData": "seconeryItemCode"},
            {"mData": "itemDescription"},
            {"mData": "amount"}
        ],
        "columnDefs": [{ "target": [0], "searchable": false, "orderable": false}],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({"name": "departmentFilter", "value": $("#departmentFilter").val()});
            aoData.push({"name": "statusFilter", "value": $("#statusFilter").val()});
            aoData.push({"name": "designationFilter", "value": $("#designationFilter").val()});
            
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

    function excel_export() 
    {
        var form = document.getElementById('commission_scheme_form');
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#commission_scheme_form').serializeArray();
        form.action = '<?php echo site_url('CommissionScheme/fetch_active_items_excel'); ?>';
        form.submit();
    }
</script>

