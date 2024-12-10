<?php echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div id="organizationMaster_editView"></div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<script type="text/javascript">
    $(document).ready(function () {

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        pagenew = '<?php  echo $_POST['policy_id']; ?>';
        if (p_id) {
            organizationID = p_id;
            page = pagenew;
            getOrganizationManagement_editView(organizationID,page);
        }

        pageRedirection = '<?php if((isset($_POST['data_arr'])) && !empty($_POST['data_arr'])){ echo $_POST['data_arr']; } ?>';

        masterID = '<?php if((isset($_POST['policy_id'])) && !empty($_POST['data_arr'])){ echo $_POST['policy_id']; } ?>';

        if(pageRedirection == 'Contact'){
            $('.headerclose').click(function () {
                fetchPage('system/crm/contact_edit_view', masterID, 'View Contact','CRM');
            });
        } else if(pageRedirection == 'Project'){
            $('.headerclose').click(function () {
                fetchPage('system/crm/project_edit_view', p_id, 'View Project','CRM');
            });
        }else if(pageRedirection == 'Lead'){
            $('.headerclose').click(function () {
                fetchPage('system/crm/lead_edit_view', masterID, 'View Lead','CRM');
            });
        }else if(pageRedirection == 'dashboardorganization')
        {
            $('.headerclose').click(function () {
                fetchPage('system/crm/dashboard', '', 'Dashboard');
            });
        } else {
            $('.headerclose').click(function () {
                fetchPage('system/crm/organization_management', '', 'Organizations');
            });
        }
    });

    function getOrganizationManagement_editView(organizationID,page) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {organizationID: organizationID,page:page},
            url: "<?php echo site_url('crm/load_organizationManagement_editView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#organizationMaster_editView').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function organization_edit_view_close() {

        fetchPage('system/crm/organization_management', '', 'Organizations');

    }
</script>