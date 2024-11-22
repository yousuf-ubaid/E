<?php echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div id="leadMaster_editView"></div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<script type="text/javascript">
    $(document).ready(function () {
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        pagenew = '<?php  echo $_POST['policy_id']; ?>';
        if (p_id) {
            leadID = p_id;
            page = pagenew;
            getLeadManagement_editView(leadID,page);
        }
        masterID = '<?php if(isset($_POST['data_arr']) && !empty($_POST['data_arr'])){ echo $_POST['data_arr']; }; ?>';

        if(masterID != ''){

            if(masterID == 'dashboardlead')
            {
                $('.headerclose').click(function () {
                    fetchPage('system/crm/dashboard', '', 'Dashboard');
                });
            }else
            {
                $('.headerclose').click(function () {
                    fetchPage('system/crm/organization_edit_view',masterID, 'View Organizations');
                });
            }

        }else{
            $('.headerclose').click(function () {
                fetchPage('system/crm/lead_management', '', 'Leads');
            });
        }
    });

    function getLeadManagement_editView(leadID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {leadID: leadID,'page':page},
            url: "<?php echo site_url('CrmLead/load_leadManagement_editView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#leadMaster_editView').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function lead_edit_view_close() {

        fetchPage('system/crm/lead_management', '', 'Leads');

    }
</script>