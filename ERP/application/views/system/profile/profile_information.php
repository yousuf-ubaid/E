<?php
$fromemployee = $this->input->post('policy_id');
$fromemployee = (empty($fromemployee))?0:$fromemployee;
?>
<div id="profileDiv">

</div>
<script>
    var fromemployee = <?php echo $fromemployee ?>;

    $(document).ready(function () {
        loadProfile();
    });

    function loadProfile() {
        $.ajax({
            async: true,
            url: "<?php echo site_url('Profile/empProfile'); ?>",
            type: 'post',
            dataType: 'html',
            data: '',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#profileDiv').html(data);
                //alert(fromemployee);
                if(fromemployee == 1){
                    $('#emplist').click();
                    $('li').removeClass('active');
                    $('.tab-pane').removeClass('active');
                    $('#emplist').addClass('active');
                    $('#myEmployeeTab').addClass('active');
                }
                stopLoad();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }
</script>