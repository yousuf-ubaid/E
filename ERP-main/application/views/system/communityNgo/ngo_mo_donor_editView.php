<?php echo head_page($_POST['page_name'], false); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
    <div id="filter-panel" class="collapse filter-panel"></div>

    <div id="donorMaster_editView"></div>

    <script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
    <script type="text/javascript">
        $(document).ready(function () {
            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                contactID = p_id;
                getDonorManagement_editView(contactID);
            }

            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_mo_donors_master', '', 'Community Donors');
            });


        });

        function getDonorManagement_editView(contactID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {contactID: contactID},
                url: "<?php echo site_url('CommunityNgo/load_comDonorManage_editView'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#donorMaster_editView').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function donor_edit_view_close() {

            fetchPage('system/crm/donor_management', '', 'Donors');

        }
    </script>
<?php
