<?php
$companies = fetch_all_companies();
?>
<section class="content">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Invoices</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="Type">Company </label>
                        <?php echo form_dropdown('companyID', $companies, '', 'class="form-control"  id="companyID" required'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="company_invoices_view">

                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- ./box-body -->
        </div>
        <!-- /.box -->
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function () {
        $('#companyID').change(function () {
            get_company_module_view();
        });

    });
    function get_company_module_view() {
        $.ajax({
            type: 'POST',
            dataType: 'HTML',
            url: "<?php echo site_url('Dashboard/showAllInvoicesView'); ?>",
            data: {companyid: $('#companyID').val()},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#company_invoices_view').html(data);
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No Database Selected :)", "error");
            }
        });
        return false;
    }
</script>
