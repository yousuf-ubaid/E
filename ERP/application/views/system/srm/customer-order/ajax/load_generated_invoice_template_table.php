<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
?>

<?php
if (!empty($detail)) { ?>
<table class="table table-striped table-bordered nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Template Name</th>
                                                <th>Visible</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $x = 1;
                                        foreach ($detail as $val) {
                                            
                                            ?>
                                            <tr>
                                                <td><?php echo $x; ?></td>
                                                <td><?php echo $val['invoiceTemplateName']; ?></td>
                                                <td>
                                                    <div class="check-box" id="invoice-check">
                                                        <input id="invoice_checkbox_<?php echo $val['invoiceTemplateMasterID']; ?>" name="invoice_checkbox" onchange="updateInvoiceTempStatus(<?php echo $val['invoiceTemplateMasterID'] ?>)" type="checkbox" <?php echo ($val['status'] == 1) ? "checked" : "" ?>>
                                                    </div>
                                                </td>
                                                <td align="center"><!--<a href="javascript:void(0);" onclick="load_invoice_page_setup(10,10)"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>--> <a href="javascript:void(0);" onclick="editInvoiceTemplate(<?php echo $val['invoiceTemplateMasterID']; ?>)"><span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="View"></span></a> <a href="javascript:void(0);" onclick="delete_company_invoice_template(<?php echo $val['invoiceTemplateMasterID']; ?>);"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" data-original-title="View"></span></a></td>
                                            </tr>
                                        <?php 
                                        $x++;
                                        } 
                                        ?>    
                                        </tbody>   
                                    </table> 
<?php
} else { ?>
    <div class="search-no-results">Not available.</div>
<?php
}
?>




<!-- Change status invoice templates -->
<script type="text/javascript">  

function updateInvoiceTempStatus(id) {
        if ($("#invoice_checkbox_" + id).is(':checked')) {
            var chkdVal = 1
        } else {
            var chkdVal = 0
        }

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'invoiceTemplateMasterID': id, 'temp_status': chkdVal},
            url: "<?php echo site_url('Company/change_status_company_invoice_template'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data == true) {
                    myAlert('s', 'Status Changed Successfully');
                } else {
                    myAlert('e', 'Please try again later');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });   

}


</script>   