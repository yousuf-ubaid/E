<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


?>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>" />

<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
    }

    .headrowtitle {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
</style>
<?php
if (!empty($output)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('srm_order_number');?><!--Order Number--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_customer');?><!--Customer--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_narration');?><!--Narration--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('srm_expiry_date');?><!--Expiry Date--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_currency');?><!--Currency--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_value');?><!--Value--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center"><?php echo $this->lang->line('common_status');?><!--Status--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_action');?><!--Action--></td>
            </tr>
            <?php
            $x = 1;
            foreach ($output as $val) {
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#"><?php echo $x ?></a></td>
                    <td class="mailbox-name">
                        <a class="link-person noselect" href="#"  onclick="fetchPage('system/srm/customer-order/order_master_edit_view','<?php echo $val['customerOrderID'] ?>','View Customer Order','SRM')"><?php echo $val['customerOrderCode'] ?></a>
                    </td>
                    <td class="mailbox-name"><a href="#"><?php echo isset($val['customerName']) ? $val['customerName'] : '' ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['narration'] ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['expiryDate'] ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['CurrencyCode']; ?></a></td>
                    <td class="mailbox-name" style="text-align: right"><a href="#"><?php
                            $orderValue = $this->db->query("SELECT SUM(totalAmount) as total FROM srp_erp_srm_customerorderdetails WHERE customerOrderID = {$val['customerOrderID']}")->row_array();
                            echo format_number($orderValue['total'], 2)
                            ?></a></td>
                    <td class="mailbox-name" style="text-align: center">
                    <?php if($val['confirmedYN'] == 1){ ?>
                        <span class="label" style="font-size: 9px;background-color: #2ad688;padding: 0.25rem 0.75rem;">Confirm</span>
                    <?php } else{ ?>
                        <span class="label" style="font-size: 9px;background-color: #f96957;padding: 0.25rem 0.75rem;">Draft</span>
                    <?php } ?>
                    </td>
                    <td class="mailbox-attachment">
                        <span class="pull-right">
                                                        <?php if ($val['confirmedYN'] == 0) { ?>
                                                            <a href="#"
                                                               onclick="fetchPage('system/srm/customer-order/create_new_customer_order','<?php echo $val['customerOrderID'] ?>','Edit Customer Order Inquiry','SRM')"><span
                                                                    title="Edit" rel="tooltip"
                                                                    class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;
                                                            <?php
                                                        } ?>
                            <?php if ($val['customerOrderID'] == NULL || $val['customerOrderID'] == 0) { ?>   

                            <a onclick="delete_customer_order_master(<?php echo $val['customerOrderID'] ?>);"><span
                                    title="Delete" rel="tooltip"
                                    class="glyphicon glyphicon-trash"
                                    style="color:rgb(209, 91, 71);"></span></a>

                            <?php
                            } ?>

                            <a onclick="traceDocument(<?php echo $val['customerOrderID'] ?>, 'SRM-ORD');"><i class="fa fa-search" aria-hidden="true"></i></a>
                            <a target="_blank" href="<?php echo site_url('Srm_master/load_customer_order_confirmation_view') . '/' . $val['customerOrderID'] ?>"><span
                                             title="Print" rel="tooltip" class="glyphicon glyphicon-print glyphicon-print-btn"
                                             style="color:#3c8dbc;"></span></a>


                        </span>
                    </td>
                </tr>
                <?php
                $x++;
            }
            ?>
            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results"><?php echo $this->lang->line('srm_there_are_no_customer_order_to_display');?><!--THERE ARE NO CUSTOMER ORDERS TO DISPLAY-->.</div>
    <?php
}
?>


<div class="modal fade" id="tracing_modal" role="dialog" aria-labelledby="myModalLabel" data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Document Tracing <button class="btn btn-default pull-right" onclick="print_tracing_view()"><i class="fa fa-print"></i> </button>
            </div>
            </h4>
            <div class="modal-body">
                <input type="hidden" id="tracingId" name="tracingId">
                <input type="hidden" id="tracingCode" name="tracingCode">
                <div id="mcontainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="deleteDocumentTracing()">Close</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });

    function traceDocument(coID, DocumentID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'invoiceAutoID': coID,
                'DocumentID': DocumentID
            },
            url: "<?php echo site_url('Tracing/trace_customer_order_document'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                //myAlert(data[0], data[1]);
                $(window).scrollTop(0);
                load_document_tracing(coID, DocumentID);
                },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function load_document_tracing(id, DocumentID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'purchaseOrderID': id,
                'DocumentID': DocumentID
            },
            url: "<?php echo site_url('Tracing/select_tracing_documents'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                $("#mcontainer").empty();
                $("#mcontainer").html(data);
                $("#tracingId").val(id);
                $("#tracingCode").val(DocumentID);

                $("#tracing_modal").modal('show');

            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function deleteDocumentTracing() {
        var purchaseOrderID = $("#tracingId").val();
        var DocumentID = $("#tracingCode").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'purchaseOrderID': purchaseOrderID,
                'DocumentID': DocumentID
            },
            url: "<?php echo site_url('Tracing/deleteDocumentTracing'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                $("#tracing_modal").modal('hide');
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }
</script>