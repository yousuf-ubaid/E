<?php echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div id="orderMaster_editView"></div>
<div class="modal fade" id="srm_rfq_modelView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle">Request For Quotation</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="srm_rfqPrint_Content"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<script type="text/javascript">
    $(document).ready(function () {
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            customerOrderID = p_id;
            getOrderMaster_editView(customerOrderID);
        }

        pageRedirection = '<?php if(!empty($_POST['data_arr'])){ echo $_POST['data_arr']; } ?>';

        masterID = '<?php echo $_POST['policy_id']; ?>';

        $('.headerclose').click(function () {
            fetchPage('system/srm/srm_customer_order','','Customer Order');
        });


    });

    function getOrderMaster_editView(customerOrderID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {customerOrderID: customerOrderID},
            url: "<?php echo site_url('Srm_master/load_order_master_editView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#orderMaster_editView').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function view_rfq_printModel(inquiryMasterID, supplierID) {
        var html = 'html';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryMasterID: inquiryMasterID, supplierID: supplierID, html: html},
            url: "<?php echo site_url('srm_master/supplier_rfq_print_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('#documentPageViewTitle').html(title);
                $('#srm_rfqPrint_Content').html(data);
                $("#srm_rfq_modelView").modal({backdrop: "static"});
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
</script>