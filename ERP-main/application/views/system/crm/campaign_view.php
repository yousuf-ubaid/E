<?php echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div id="campaignMaster_View"></div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/crm/campaign_management', '', 'Tasks');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            campaignID = p_id;
            getCampaignManagement_editView(campaignID);
        }
    });

    function getCampaignManagement_editView(campaignID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {campaignID: campaignID},
            url: "<?php echo site_url('crm/load_campaign_Management_editView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#campaignMaster_View').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function campaign_attendees_close() {

        fetchPage('system/crm/campaign_management', '', 'Campaigns');

    }
</script>