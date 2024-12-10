<?php
///var_dump($extra['message']);
?>
<style>
    .c-h {
        margin-top: 50px;
    }
</style>
<div class="c-h">
    <div class="alert  <?php
    if (isset($extra['type']) && ($extra['type'] == 'w')) {
        echo 'alert-warning';
    } else if (isset($extra['type']) && ($extra['type'] == 'e')) {
        echo 'alert-danger';
    } else if (isset($extra['type']) && ($extra['type'] == 'i')) {
        echo 'alert-info';
    } else if (isset($extra['type']) && ($extra['type'] == 's')) {
        echo 'alert-success';
    } else {
        echo 'alert-default';
    }
    ?>">
        <h5 class="text-center">
            <?php echo isset($extra['message']) ? $extra['message'] : ''; ?>
        </h5>
    </div>

    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Redirecting.. </strong> This page will redirect in 5 seconds.
    </div>
</div>

<script>
    var url = '<?php echo site_url('Dashboard') ?>';
    $(document).ready(function (e) {
        setTimeout(function () {
            window.location.href = url;
        }, 5000);

    })
</script>