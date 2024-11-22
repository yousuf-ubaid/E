<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], true);


$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
$doc_status = [
    'all' =>$this->lang->line('common_all'), '1' =>$this->lang->line('common_draft'),
    '2' =>$this->lang->line('common_confirmed'), '3' =>$this->lang->line('common_approved'),'4'=>'Refer-back'
];
?>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <?php echo form_open('', 'role="form" id="invoice_commission_form"'); ?>
        <div class="form-group col-sm-8">
        </div>
        <div class="form-group col-sm-3">
                <label for="statusFilter"><?php echo $this->lang->line('common_status');?> </label><br><!--Status-->
                <div style="width: 60%;">
                    <?php echo form_dropdown('statusFilter', $doc_status, '', 'class="form-control" id="statusFilter" onchange="Otable.draw()"'); ?>
                </div>
                
        </div>

        <div class="form-group col-sm-1">
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
        
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="invoice_commission_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 20%"><?php echo $this->lang->line('sales_markating_transaction_invoice_code');?></th> <!--Invoice Code-->
            <th style="min-width: 20%"><?php echo $this->lang->line('common_code');?></th><!--Code-->
            <th style="min-width: 20%"><?php echo $this->lang->line('common_invoice_date');?></th><!--Invoice Date-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_confirmed');?> </th><!--Confirmed-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_approved');?> </th><!--Approved-->
            <th style="min-width: 15%"><?php echo $this->lang->line('common_action');?> </th><!--Action-->
        </tr>
        </thead>
    </table>
</div>
<div class="modal fade" id="documentPageView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-backdrop="static">
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
                <!-- <div class="modal-footer"> -->
                   <!--  <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php //echo $this->lang->line('common_Close'); ?></button> -->
                <!-- </div> -->
            </div>
        </div>
    </div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    invoice_commission_table();
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/delivery_order/invoice_commision','','Invoice Commission');
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

    function invoice_commission_table(selectedID=null) {
         Otable = $('#invoice_commission_table').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Invoices/fetch_invoice_commission'); ?>",
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
                {"mData": "commissionAutoID"},
                {"mData": "invoiceCode"},
                {"mData": "documentSystemCode"},
                {"mData": "invoiceDate"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
                
            ],
             "columnDefs": [{"targets": [4,5,6], "orderable": false},{"targets": [0], "visible": true,"searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
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


    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });
    

    function documentPageView_modal_IC(documentID, para1, para2, approval=1) {

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

            case "IC": // Commisson Scheme
                siteUrl = "<?php echo site_url('Invoices/load_invoice_commission_confirmation'); ?>";
                paramData.push({name: 'commissionAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                paramData.push({name: 'confirmedYN', value: para2});
                title = "Invoice Commission";
                a_link = "<?php echo site_url('Invoices/load_invoice_commission_confirmation'); ?>/" + para1;
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
                $('#documentPageView').modal('show');
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

    function ic_confirmation(commissionAutoID) {
        if (commissionAutoID) {
            $.ajax({
                    url: "<?php echo site_url('Invoices/invoice_commission_confirmation'); ?>",
                    type: 'post',
                    data: {commissionAutoID: commissionAutoID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {

                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $("#documentPageView").modal('hide');
                            setTimeout(function(){  Otable.draw(); }, 500);
                            //fetchPage('system/delivery_order/invoice_commision', '', 'Invoice Commission');
                            
                           
                        }
                        setTimeout(function(){  stopLoad(); }, 500);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
        };
    }


    function referbackic(id) {
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
               //alert('hai');
               $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'commissionAutoID': id},
                    url: "<?php echo site_url('Invoices/referbackic'); ?>",
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
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters(){
        $('#statusFilter').val("all");
        Otable.draw();
    }
</script>