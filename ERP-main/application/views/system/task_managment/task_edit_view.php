<?php echo head_page($_POST['page_name'], false);
$this->load->helper('task_helper');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div id="taskMaster_editView"></div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<script type="text/javascript">
    $(document).ready(function () {
        masterID = '<?php if((isset($_POST['policy_id'])) && !empty($_POST['policy_id'])){ echo $_POST['policy_id']; } ?>';
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            taskID = p_id;
            masterID = masterID;
            getTaskManagement_editView(taskID,masterID);
        }

        pageRedirection = '<?php if((isset($_POST['data_arr'])) && !empty($_POST['data_arr'])){ echo $_POST['data_arr']; } ?>';



        if (pageRedirection == 'Dashboard') {
            $('.headerclose').click(function () {
                if(masterID=='CRMTSK'){
                    fetchPage('system/crm/reports_management','','CRMTSK');
                }else{
                    fetchPage('system/crm/dashboard', '', 'Dashboard','dashboardtask');
                }

            });
        } else if (pageRedirection == 'ProjectsTask') {
            $('.headerclose').click(function () {
                    fetchPage('system/crm/project_edit_view', masterID, 'View Project','projectTask');
            });
        }else if (pageRedirection == 'contactTask')
        {
            $('.headerclose').click(function () {
                fetchPage('system/crm/contact_edit_view', masterID,'View Contact','ContactTask');
            });
        }
            else if(pageRedirection == 'opportunityTaks')
        {
            $('.headerclose').click(function () {
                fetchPage('system/crm/opportunities_edit_view', masterID, 'View Opportunity','OpportunityTask');
            });
        }

        else if (pageRedirection == 'opportunitie') {
            $('.headerclose').click(function () {
                fetchPage('system/crm/opportunities_edit_view', masterID, 'View Opportunity',);
            });
        }
        else if (pageRedirection == 'Lead') {
            $('.headerclose').click(function () {
                if(masterID=='CRMTSK'){
                    fetchPage('system/crm/reports_management','','CRMTSK');
                }else{
                    fetchPage('system/crm/lead_edit_view', masterID, 'View Lead','LeadTask');
                }
            });
        }else if(pageRedirection == 'dashboardtask')
        {
            $('.headerclose').click(function () {
                fetchPage('system/crm/dashboard', '', 'Dashboard');
            });
        }
       /* else if (pageRedirection == 'LeadTask') {
            $('.headerclose').click(function () {
                fetchPage('system/crm/lead_edit_view', masterID, 'View Lead');
            });
        }*/
        else if (pageRedirection == 'organization') {
            $('.headerclose').click(function () {
                if(masterID=='CRMTSK'){
                    fetchPage('system/crm/reports_management','','CRMTSK');
                }else{
                    fetchPage('system/crm/organization_edit_view', masterID, 'View Organization','Organizationtaskedit');
                }
            });
        }
        else {
            $('.headerclose').click(function () {
                if(masterID=='CRMTSK'){
                    fetchPage('system/crm/reports_management','','CRMTSK');
                }else{
                    fetchPage('system/task_managment/task_managemnt_employee', '', 'Tasks');
                }
            });
        }

    });

    function getTaskManagement_editView(taskID,masterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {taskID: taskID,'masterID':masterID},
            url: "<?php echo site_url('Task_management/load_taskManagement_editView'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#taskMaster_editView').html(data);

                if(masterID=='CRM'){
                    $('.projecteditbtn').removeClass('hidden');
                }else if(masterID=='dashboardtask')
                {
                    $('.projecteditbtn').removeClass('hidden');
                }

                else{
                    $('.projecteditbtn').addClass('hidden');
                }
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function task_edit_view_close() {

        fetchPage('system/task_managment/task_management', '', 'Tasks');

    }
</script>