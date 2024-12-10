<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('srm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('srm_helper');
$title="Vendor Registration";
echo head_page($title,false);
$address=load_addresstype_drop();
$customer_arr=all_srm_supplier_drop();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-6">
        <h4>Request Status</h4>
        <table class="<?php echo table_class(); ?>">
                <tr>  
                <td><span class="label label-primary" style ="background-color:#f4ec67 !important">&nbsp;</span>
                    Confirmed<!--Closed--> </td>                  
                    <td><span class="label label-success">&nbsp;</span>
                    Approved<!--Confirmed--></td>
                    <td><span class="label label-danger">&nbsp;</span>
                    Rejected<!--Not Confirmed--></td>
                    <td><span class="label label-warning">&nbsp;</span>
                    Refer Back<!--Refer-back--></td>
                    <td><span class="label label-info">&nbsp;</span>
                    Pending<!--Closed--> </td>
                    <td><span class="label label-primary" style ="background-color:#22a6b3 !important">&nbsp;</span>
                    Resubmited<!--Closed--> </td>
                </tr>
            </table>
    </div>
    <div class="col-md-9 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-right">
        
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <h4>Supplier Status</h4>
        <table class="<?php echo table_class(); ?>">
                <tr>  
                               
                    <td><span class="label label-success">&nbsp;</span>
                    Active<!--Confirmed--></td>
                    <td><span class="label label-danger">&nbsp;</span>
                    Inactive<!--Not Confirmed--></td>
                    
                </tr>
            </table>
    </div>
    <div class="col-md-9 text-center">
        &nbsp; 
    </div>
    <div class="col-md-3 text-right">
        
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="company_req_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center">#</th>
                <th class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center"><?php echo $this->lang->line('common_name');?><!--Name--></th>
                <th class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center">Email</th>
                <th class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center">Contact Number</th>
                <th class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center"><?php echo $this->lang->line('common_Country');?><!--Country--></th>
                <th class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center">Supplier Status</th>
                <th class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center">Request Status</th>
                <th class="headrowtitle" style="border-top: 1px solid #ffffff; text-align: center"><?php echo $this->lang->line('common_action');?><!--Action--></th>
               
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<div class="modal fade" id="company_request_approve_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Approve</h4>
            </div>
            <form class="form-horizontal" id="company_rq_form">
                <div class="modal-body">
                    <div id="conform_body"></div><hr>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Supplier</label>
                        <div class="col-sm-4">
                        <?php echo form_dropdown('sup', $customer_arr, '', 'class="form-control" id="sup" '); ?> 
                            <input type="hidden" name="requestMasterID" id="requestMasterID">
                        </div>


                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" rows="3" name="comments" id="comments" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
                    <a onclick="saveItemPricingDetail()" class="btn btn-primary">Save</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function(){
            //fetchPage('system/srm/srm_vendor_company_request','company','Company Request');
            send_checking_api();
        });
        fetch_company_request();

        
    });

    function fetch_company_request() {
        var Otable = $('#company_req_table').DataTable({"language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Srm_master/fetch_company_request_vendor'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "companyReqID"},
                {"mData": "profile"},
                {"mData": "contactPersonEmail"},
                {"mData": "pointContactphone"},
                {"mData": "CountryDes"},
                {"mData": "supplierApprovalStatus"},
                {"mData": "status"},
                {"mData": "action"},
            ],
            "columnDefs": [{"targets": [2], "orderable": false}],
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

    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function approve_vendor_company_request(id){
        $("#company_request_approve_modal").modal("show");
        $('#requestMasterID').val(id);
    }

    function reject_vendor_company_request(id) {
        if (id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'requestID': id},
                        url: "<?php echo site_url('srm_master/reject_company_request'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            // if(data[0]=='s'){
                            //     fetchPage('system/srm/srm_order_review_management', '', 'Order Review Master');
                            // }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function saveItemPricingDetail() {
        var data = $('#company_rq_form').serializeArray();

        var itemAutoID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

      

            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Srm_master/save_company_request_approve'); ?>",
                    beforeSend: function () {
                        startLoad();
                        // $('.umoDropdown').prop("disabled", true);
                    },
                    success: function (data) {
                        stopLoad();
                        
                        if(data[0]){
                            myAlert(data[0], data[1]);
                        }
                        
                        refreshNotifications(true);
                        if (data) {
                            // setTimeout(function () {
                            //     tab_active(tabID);
                            // }, 300);
                           
                            // $('#item_price_model').modal('hide');
                            // $('#item_price_form')[0].reset();
                            // //$('.select2').select2('');
                            // fetch_address();
                        } else {
                            // $('.discount').prop('disabled', true);
                            // $('.discount_amount').prop('disabled', true);
                        }
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
    }

    function send_checking_api() {
        

      

            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {"arr":1},
                    url: "<?php echo site_url('Srm_master/send_checking_api'); ?>",
                    beforeSend: function () {
                        startLoad();
                        // $('.umoDropdown').prop("disabled", true);
                    },
                    success: function (data) {
                        stopLoad();

                        }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
    }

   
</script>