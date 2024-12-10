<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_navigation_description_setup');
echo head_page($title, false);
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<form id="navigation_description_setup_form" method="post">
    <div class="form-group" id="div_reload">

    </div>
</form>
<button type="submit" onclick="saveNavigationDescriptionSetup()" class="btn btn-primary-new size-lg pull-right"><?php echo $this->lang->line('common_save_change');?>
<?php
echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/navigation/erp_navigation_description_setup','','Navigation Group Setup');
        });
        loadform();
    });

    function loadform() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {},
            url: "<?php echo site_url('Access_menu/getNavigationDescriptionSetup'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_reload').html(data);
                stopLoad();

            }, error: function () {

            }
        });

    }

    function saveNavigationDescriptionSetup() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'json',
            data: $('#navigation_description_setup_form').serializeArray(),
            url: "<?php echo site_url('Access_menu/saveNavigationDescriptionSetup'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data['status'], data['message']);
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

</script>