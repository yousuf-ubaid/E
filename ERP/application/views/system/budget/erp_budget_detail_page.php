<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('finance_tr_bt_budget_detai');
echo head_page($title, false);

/*echo head_page('Budget Detail', false);*/
$page_id=trim($this->input->post('page_id'));
?>
<div id="filter-panel" class="collapse filter-panel"></div>




<div class="row" id="detailData">



</div>





<script type="text/javascript">
    $('.headerclose').click(function(){
        fetchPage('system/finance/Budget_management','','Budget');
    });
    budgetAutoID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
    get_budget_detail(budgetAutoID);

        function get_budget_detail(budgetAutoID) {
            var viewtype='edit';
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'budgetAutoID': budgetAutoID,'viewtype': viewtype},
                url: "<?php echo site_url('Budget/get_budget_detail_data'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                        $('#detailData').html(data);
                    stopLoad();
                    refreshNotifications(true);
                },
                error: function () {

                }
            });
        }





</script>