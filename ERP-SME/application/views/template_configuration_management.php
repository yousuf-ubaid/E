<?php /*echo head_page('Template Setup', false);
$companyID = current_companyID();
*/ ?>
<section class="content">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Template Setup</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive" id="policyTable">
                    <table id="company_template_configuration"
                           class="table table-bordered table-striped table-condensed">
                        <thead>
                        <tr>
                            <th style="width: 35px;">#</th>
                            <th>Description</th>
                            <th>Default Value</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <!-- /.row -->
            </div>
            <!-- ./box-body -->
        </div>
        <!-- /.box -->
    </div>
</section>
<?php /*echo footer_page('Right foot', 'Left foot', false); */ ?>

<script type="text/javascript">
    $(document).ready(function () {
        fetch_template_configuration_table();

    });

    function fetch_template_configuration_table() {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: <?php echo $companyID; ?>},
            url: "<?php echo site_url('CompanyTemplate/fetch_template_configuration'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#policyTable').html(data);
                $('#company_template_configuration').DataTable();
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }


    $('.table-row-select tbody').on('click', 'tr', function () {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });


    function saveTemplate(id,companyID) {
        var valu = $(id).val();
        var res = valu.split("|");
        var TempMasterID = res[0];
        var FormCatID = res[1];
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {TempMasterID: TempMasterID, FormCatID: FormCatID,companyID:companyID},
            url: "<?php echo site_url('CompanyTemplate/saveTemplate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }


</script>