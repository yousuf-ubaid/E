<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('dashboard', $primaryLanguage);
?>
<div class="row">
    <div class="col-md-6">
        <div id="companyupdate_div"></div>
    </div>
    <div class="col-md-6">
        <div id="todolistview_div"></div>
        <div id="shortcutlinks_div"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        load_to_do_list_view();
        load_shortcut_links();
        load_company_updates();
    });

    function load_shortcut_links() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'<?php echo  $this->security->get_csrf_token_name() ?>': '<?php echo $this->security->get_csrf_hash() ; ?>'},
            url: "<?php echo site_url('Finance_dashboard/load_shortcut_links'); ?>",
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
                $("#shortcutlinks_div").html(data);
            }, error: function () {

            }
        });
    }

    function load_to_do_list_view() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/load_to_do_list'); ?>",
            data: {'<?php echo  $this->security->get_csrf_token_name() ?>': '<?php echo $this->security->get_csrf_hash() ; ?>'},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#todolistview_div").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
    }

    function load_company_updates() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('GeneralDashboard/getCompanyUpdates'); ?>",
            data: {'<?php echo  $this->security->get_csrf_token_name() ?>': '<?php echo $this->security->get_csrf_hash() ; ?>'},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#companyupdate_div").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
    }
</script>
