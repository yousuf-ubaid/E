<?php echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div id="contactMaster_editView"></div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/multipleattachment/jquery.MultiFile.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
<script type="text/javascript">
    $(document).ready(function () {
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        pagenew = '<?php  echo $_POST['policy_id']; ?>';

        if (p_id) {
            contactID = p_id;
            page = pagenew;
            getContactManagement_editView(contactID,page);
        }

        masterID = '<?php if((isset($_POST['data_arr'])) && !empty($_POST['data_arr'])){ echo $_POST['data_arr']; } ?>';


        if(masterID != ''){
            if(masterID == 'dashboardcontact')
            {
                $('.headerclose').click(function () {
                    fetchPage('system/crm/dashboard', '', 'Dashboard');
                });
            }else
            {
                $('.headerclose').click(function () {
                    fetchPage('system/crm/organization_edit_view',masterID, 'View Organizations','organizationcontact');
                });
            }

        }else{
            $('.headerclose').click(function () {
                fetchPage('system/crm/contact_management', '', 'Contacts');
            });
        }
    });

    function getContactManagement_editView(contactID,page) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {contactID: contactID,'page':page},
            url: "<?php echo site_url('crm/load_contactManagement_editView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#contactMaster_editView').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function contact_edit_view_close() {

        fetchPage('system/crm/contact_management', '', 'Contacts');

    }
</script>