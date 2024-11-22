<?php echo head_page($_POST['page_name'], false); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
    <div id="filter-panel" class="collapse filter-panel"></div>

    <div id="vehicle_DetailsView"></div>

    <script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">

    <script type="text/javascript">
        $(document).ready(function () {
            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                vehicleMasterID = p_id;
                get_vehicle_DetailsView(vehicleMasterID);
            }

            $('.headerclose').click(function () {
                fetchPage('system/Fleet_Management/fleet_saf_vehicleMaster', '','Vehicle Master');
            });

        });

        function get_vehicle_DetailsView(vehicleMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {vehicleMasterID: vehicleMasterID},
                url: "<?php echo site_url('Fleet/load_vehicleDetailsView'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#vehicle_DetailsView').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    </script>

