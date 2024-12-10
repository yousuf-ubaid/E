<?php echo head_page($_POST['page_name'], false); ?>


    <div id="div_familyRelationships" style="text-align: center;"></div>




<script type="text/javascript">
    $(document).ready(function () {
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            FamMasterID = p_id;
            fetch_familyRelationships_list(FamMasterID);
        }

        $('.headerclose').click(function () {
            fetchPage('system/communityNgo/ngo_mo_familyMaster', '', 'Family');
        });

    });

    function fetch_familyRelationships_list(FamMasterID){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {FamMasterID: FamMasterID},
            url: "<?php echo site_url('CommunityNgo/fetch_familyRelationships_list'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_familyRelationships").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                //$("#familydetails_tab").html('<div class="alert alert-danger">An Error Occurred! Please Try Again.<br/><strong>Error Message: </strong>' + errorThrown + '</div>');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }
</script>

<?php
