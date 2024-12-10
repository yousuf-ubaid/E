<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fn_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $_POST["page_name"];

$inv_company_arr = investmentCompany_drop();

echo head_page($title, false); ?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">

    <div id="filter-panel" class="collapse filter-panel"></div>
    <div class="row">
        <div class="col-md-12">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border"><?php echo $this->lang->line('common_filter');?><!--Date--></legend>

                <?php echo form_open('', ' id="frm_statement_rpt" class="form-horizontal" role="form"'); ?>
                <div class="col-md-12">
                    <label for="inv_company" class="col-md-1 control-label"><?php echo $this->lang->line('common_company');?></label>
                    <div class="col-md-4">
                        <?php echo form_dropdown('inv_company', $inv_company_arr, '', ' class="form-control select2" id="inv_company" onchange="load_submission_years(this)"'); ?>
                    </div>

                    <label for="submission_year" class="col-md-1 control-label"><?php echo $this->lang->line('common_year');?></label>
                    <div class="col-md-2" id="submission_year_container">
                        <?php echo form_dropdown('submission_year', [], '', 'class="form-control select2" id="submission_year"'); ?>
                    </div>

                    <button style="margin-top: 5px" type="button" onclick="load_report()" class="btn btn-primary btn-xs">
                        <?php echo $this->lang->line('common_load');?>
                    </button>
                </div>
                <?php echo form_close(); ?>

            </fieldset>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" id="response-container" style="margin-top: 20px;"></div>
    </div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>

    $('#inv_company').select2();

    $('.headerclose').click(function () {
        fetchPage('system/fund-management/reports/income-statement-rpt', '', '<?php echo $title ?>')
    });

    function load_submission_years(obj){
        var fm_companyID = $(obj).val();

        $('#submission_year').empty();

        if(fm_companyID != ''){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'fm_companyID':fm_companyID},
                url: "<?php echo site_url('Fund_management/load_dropDown_submission_years'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#submission_year_container').html(data);

                    setTimeout(function(){
                        $('#submission_year').select2();
                    }, 300);

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', errorThrown);
                }
            });
        }

    }

    function load_report(){
        var postData = $('#frm_statement_rpt').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            cache: false,
            data: postData,
            url: "<?php echo site_url('Fund_management/load_income_statement_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#response-container').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', errorThrown);
            }
        });
    }
</script>

<?php
