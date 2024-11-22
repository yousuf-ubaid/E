<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('treasury_ap_br_bank_reconcilation');

echo head_page($title, false);
/*echo head_page('Bank Reconciliation', false);*/
$pageID = trim($this->input->post('page_id'));
  $date_format_policy = date_format_policy();
$pageID = explode('|', $pageID);
$GLAutoID = $pageID[0];
$bankRecAutoID = $pageID[1];

?>
<div id="filter-panel" class="collapse filter-panel"></div>
<!--<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Bank Rec Header</a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_journal_entry_detail()" data-toggle="tab">Step 2 - Bank Rec Detail</a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation()" data-toggle="tab">Step 3 - Bank Rec Confirmation</a>
</div><hr>-->


<div id="step1" class="active">


    <div class="row">

        <div class="col-sm-12" id="load_generated_table">

        </div>

    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        GLAutoID = <?php echo trim($GLAutoID); ?>;
        bankRecAutoID = <?php echo trim($bankRecAutoID); ?>;


        if (GLAutoID) {
            generateload();
        }

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';



    });

    $('.headerclose').click(function () {

        fetchPage("system/bank_rec/erp_bank_reconciliation_bank_summary", "<?php echo trim($GLAutoID); ?>", "Bank Reconciliation ", "Bank Reconciliation");
    });

    function generateload() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {GLAutoID: GLAutoID, bankRecAutoID: bankRecAutoID},
            url: "<?php echo site_url('Bank_rec/viewbankrec_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#load_generated_table').html(data);
                var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
               /* $('.datepics').datetimepicker({
                    useCurrent: false,
                    format: date_format_policy,
                });*/

                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['dateFrom'] + '|' + text['dateTo']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }


    function jv_detail_modal() {
        if (JVMasterAutoId) {
            $('#jv_detail_form')[0].reset();
            $('#jv_detail_form').bootstrapValidator('resetForm', true);
            $("#jv_detail_modal").modal({backdrop: "static"});
        }
    }


</script>