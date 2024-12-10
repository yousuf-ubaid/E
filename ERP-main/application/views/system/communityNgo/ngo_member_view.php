<?php echo head_page($_POST['page_name'], false); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
    <div id="filter-panel" class="collapse filter-panel"></div>

    <div id="member_DetailsView"></div>

    <script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">

    <script type="text/javascript">
        $(document).ready(function () {
            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                Com_MasterID = p_id;
                get_member_DetailsView(Com_MasterID);
            }

            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_hi_communityMaster', '', 'Members');
            });

        });

        function get_member_DetailsView(Com_MasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {Com_MasterID: Com_MasterID},
                url: "<?php echo site_url('CommunityNgo/load_memberDetailsView'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#member_DetailsView').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    </script>

<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 1/30/2018
 * Time: 1:13 PM
 */