<?php echo head_page($_POST['page_name'], false); ?>



    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
    <div id="filter-panel" class="collapse filter-panel"></div>

    <div id="family_DetailsView"></div>

    <script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/ngo_web_style.css'); ?>">

    <script type="text/javascript">
        $(document).ready(function () {
            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                FamMasterID = p_id;
                get_familyMasterDetailView(FamMasterID);
            }

            var masterID = '<?php if (isset($_POST['data_arr']) && !empty($_POST['data_arr'])) {
                echo json_encode($_POST['data_arr']);
            } ?>';

            if (masterID != null && masterID.length > 0) {
                var masterIDNew = JSON.parse(masterID);
                $('.headerclose').click(function () {

                    fetchPage('system/communityNgo/ngo_mo_communityReport', masterIDNew[0], 'Community Report', 'NGO');

                });
            }
            else {
                $('.headerclose').click(function () {
                    fetchPage('system/communityNgo/ngo_mo_familyMaster', '', 'Family Master');
                });
            }


        });

        function get_familyMasterDetailView(FamMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {FamMasterID: FamMasterID},
                url: "<?php echo site_url('CommunityNgo/load_familyMasterDetails'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#family_DetailsView').html(data);

                    //control_staff_access(0, 'system/communityNgo/ngo_mo_familyMaster', 0);

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    </script>

<?php
