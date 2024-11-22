<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('config', $primaryLanguage);
$title = 'Policy Setup';
echo head_page($title, false);

?>
<?php $this->load->helpers('buyback_helper');  ?>

<div class="table-responsive" id="buybackPolicyTable"><div>

<script>
    fetch_buyback_policy_table();
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/buyback/configuration/policy', '', 'Policy Setup')
        })
    });

    function fetch_buyback_policy_table() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            url: "<?php echo site_url('Buyback/fetch_buyback_policy'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#buybackPolicyTable').html(data);
                $("#buyback_policy_table").dataTable({
                    "columnDefs": [{"searchable": false, "targets": [0]}]
                });
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function addPolicydetails(element) {
        var id = $(element).attr('id'),
            value = $(element).val(),
            type = $(element).data('type');

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {value: value, buybackPolicyMasterID: id},
            url: "<?php echo site_url('buyback/change_policy'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
            }, error: function () {
                stopLoad();
            }
        });

    }
</script>







<?php
/**
 * Created by PhpStorm.
 * User: l
 * Date: 4/17/2019
 * Time: 10:53 AM
 */