<?php



?>



<script>
    $(document).ready(function (e) {
        sync_customer_invoice();
    });

    
    function sync_customer_invoice() {
        var yieldPreparationID=0;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'yieldPreparationID': yieldPreparationID},
            url: "<?php echo site_url('Pos/sync_customer_invoice'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
               stopLoad();
                myAlert(data[0],data[1]);

                setTimeout(function(){
                    location.reload();
                }, 50);
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

</script>
