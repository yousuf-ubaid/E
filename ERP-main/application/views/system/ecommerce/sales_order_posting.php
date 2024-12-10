<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('common', $primaryLanguage);
    $this->lang->load('ecommerce', $primaryLanguage);
    $title = $this->lang->line('ecommerce_sales_data');
    $this->lang->line($title, $primaryLanguage);
    echo head_page('Order postings', false);
?>


<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class() ?>">
            <button class="btn btn-success" onclick="create_manual_posting_ui()"><i class="fa fa-plus"></i>&nbsp Create Manual Posting</button>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-center">
    </div>
</div>
<hr>

<div class="table-responsive">
    <table id="clent_general" class="<?php echo table_class() ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%">DOC ID</th><!--Code-->
                <th style="min-width: 10%">Service Type</th><!--Code-->
                <th style="min-width: 10%">Mode of Collection</th><!--Code-->
                <th style="min-width: 10%">Added Date</th><!--Code-->
                <th style="min-width: 10%">Date From</th><!--Code-->
                <th style="min-width: 10%">Date To</th><!--Code-->
                <th style="min-width: 10%">Description</th><!--Code-->
                <th style="min-width: 10%">Posting Type</th><!--Code-->
                <th style="min-width: 10%">Status</th><!--Code-->
                <th style="min-width: 10%">Action</th><!--Code-->
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
</div>

<div class="modal fade" id="posting_action_log_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_markating_view_sales_return_approval');?></h4><!--Invoice Approval-->
            </div>
            <form class="form-horizontal" id="pv_approval_form">
                <div class="modal-body">
                    <div class="col-sm-1">
                        <!-- Nav tabs -->
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="slr_attachement_approval_Tabview_v" class="active">
                                <a href="#Tab-home-v" data-toggle="tab" onclick="">Log</a><!--View-->
                            </li>
                         
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                <div id="de_view"></div>
                            </div>

                            <div class="pull-right mt-3 btn-section" id="btn-section">

                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                                <!-- <button type="button" class="btn btn-primary" onclick="proceed_double_entry()"><i class="fa fa-cog"></i> Proceed</button> -->
                                <!-- <button type="button" class="btn btn-primary" onclick="proceed_customer_double_entry()">Customer</button> -->
                                <!-- <button type="button" class="btn btn-primary" onclick="proceed_3pL_double_entry()">3PL Vendor</button> -->
                                <!-- <button type="button" class="btn btn-primary" onclick="proceed_3pL_customer_double_entry()">3PL Customer</button> -->
                                <!-- <button type="button" class="btn btn-primary" onclick="proceed_direct()">Direct Receipt</button> -->
                                <!-- <button type="button" class="btn btn-primary" onclick="proceed_journel_voucher()">Journel Voucher</button> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripting area -->
<script type="text/javascript">

    function create_manual_posting_ui(){
        fetchPage('system/ecommerce/sales_data_manual_posting','Test','Sales Data Mapping');
    }

    sales_client_mapping_table();

    function sales_client_mapping_table() {
        var Otable = $('#clent_general').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('DataSync/fetch_sales_sytem_postings'); ?>",
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
                {"mData": "id"},
                {"mData": "doc_id"},
                {"mData": "service_type"},
                {"mData": "mode_collection"},
                {"mData": "added_date"},
                {"mData": "date_from"},
                {"mData": "date_to"},
                {"mData": "description"},
                {"mData": "type"},
                {"mData": "status"},
                {"mData": "action"},
                // {"mData": "switch"},
                // {"mData": "erp_c"},
                // {"mData": "delete"},
                // {"mData": "edit"}
            ],
            "columnDefs": [{"searchable": false, "targets": [0]}],
            "fnServerData": function (sSource, aoData, fnCallback) {

             //   aoData.push({ "name": "posting_id","value": $("#posting_id").val()});
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

    function proceed_posting(id){
        
        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "Do you want to proceed",/*You want to delete this customer!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Confirm",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            
            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {'id': id},
                url: "<?php echo site_url('DataSync/run_daily_posting_automate'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    sales_client_mapping_table();
                }, error: function () {
                    stopLoad();
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });
            
        });
    }

    function edit_posting(id){
        fetchPage('system/ecommerce/sales_data_manual_posting','Test','Sales Data Mapping','',id);
    }

    function action_posting_log(id){

        $.ajax({
            async: true,
            type: 'post',
            //dataType: 'json',
            data: {'id': id},
            url: "<?php echo site_url('DataSync/get_posting_action_log'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#de_view').empty();
                $('#de_view').html(data);
                $('#posting_action_log_modal').modal();
                // refreshNotifications(true);
                // sales_client_mapping_table();
            }, error: function () {
                stopLoad();
                //swal("Cancelled", "Your file is safe :)", "error");
            }
        });

       
    }

    function delete_posting(id){

        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "Do you want to proceed",/*You want to delete this customer!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Confirm",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            
            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {'id': id},
                url: "<?php echo site_url('DataSync/delete_manual_posting_record'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    sales_client_mapping_table();
                }, error: function () {
                    stopLoad();
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });
            
        });
    }

    function show_processed_records(id){
        fetchPage('system/ecommerce/sales_order_processed_view','Test','Sales Data Proccessed','',id);
    }

//////////////////////////////////////////////////////////////

    function data_edit_posting(id){
        fetchPage('system/ecommerce/sales_new_data_mapping','Test','Sales Data Mapping','',id);
    }

</script>