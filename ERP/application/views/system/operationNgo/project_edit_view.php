<?php echo head_page($_POST['page_name'], false); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>

<div id="projectMaster_editView"></div>

<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<script type="text/javascript">
    $(document).ready(function () {
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            ngoProjectID = p_id;
            getProjectManagement_editView(ngoProjectID);
        }

        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/project_master', '', 'Projects');
        });

    });

    function getProjectManagement_editView(ngoProjectID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {ngoProjectID: ngoProjectID},
            url: "<?php echo site_url('OperationNgo/load_projectManagement_editView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#projectMaster_editView').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function project_edit_view_close() {

        fetchPage('system/crm/project_management', '', 'Projects');

    }
</script>