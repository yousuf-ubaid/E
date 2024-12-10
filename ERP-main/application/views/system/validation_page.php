<?php echo head_page('Validation',false); ?>
<form role="form" id="vehicle_transmission_form" class="form-horizontal">
    <div class="modal-body">
        <div class="form-group">
            <label for="enterbodytype" class="col-sm-3 control-label">Transmission</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="entertransmission" name="entertransmission" placeholder="Transmission">
           	</div>
        </div>             
    </div>
    <div class="modal-footer">
        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        <button class="btn btn-primary" type="submit">Save changes</button>
    </div>
</form>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
$(document).ready(function () {
    $('#vehicle_transmission_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            entertransmission: {validators: {notEmpty: {message: 'Transmission is required.'}}},
        },
    }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('dashboard/save_transmission'); ?>",
                    beforeSend: function () {
                        // HoldOn.open({
                        //     theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                        // });
                    },
                    success: function (data) {
                        //HoldOn.close();
                        //refreshNotifications(true);
                        $form.bootstrapValidator('resetForm', true);
                        $("#vehicle_transmission_modal").modal('hide');
                        if (data['status']) {
                            load_table_model();
                        };
                    }, error: function () {
                        //HoldOn.close();
                        alert('An Error Occurred! Please Try Again.');
                        refreshNotifications(true);
                    }
            });
        });
});
</script>