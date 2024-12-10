<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page( $this->lang->line('hrms_attendancesummarytemplate'), false); ?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row" >
    <div class="col-md-3">
    <form  id="otcategoriesfrm" name="otcategoriesfrm">
        <div id="otcategories"></div>
    </form>
    </div>
    <div class="col-sm-6">
        <button type="button" id="btndisabled" class="btn btn-primary disabled" style="margin-top: 23px;" onclick="saveottemplatedetail()"><i
            class="fa fa-floppy-o"></i> <?php echo $this->lang->line('common_save')?> <!--Save-->
        </button>
    </div>
</div>
<hr>
<div id="over_time_template_table">

</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/OverTime/erp_ot_template_management', 'Test', 'Over Time');
        });
        over_time_template_table();
        fetch_over_time_template_categories();

    });

    function fetch_over_time_template_categories() {
        var groupSegmentID = 0;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupSegmentID: groupSegmentID, All: 'true'},
            url: "<?php echo site_url('OverTime/load_over_time_template_categories'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#otcategories').html(data);
                //$('.select2').select2();
            }, error: function () {

            }
        });
    }

    function over_time_template_table() {
        var groupSegmentID = 0;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupSegmentID: groupSegmentID, All: 'true'},
            url: "<?php echo site_url('OverTime/fetch_over_time_template'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#over_time_template_table').html(data);
                //$('.select2').select2();
            }, error: function () {

            }
        });
    }

    function saveottemplatedetail() {
        var data = $("#otcategoriesfrm").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OverTime/save_over_time_template'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                over_time_template_table();
                fetch_over_time_template_categories();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });
    }

    function delete_ot_template(id){
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "cancel"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'templatedetailID': id},
                    url: "<?php echo site_url('OverTime/delete_ot_template'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            over_time_template_table();
                            fetch_over_time_template_categories();
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function diablebutton(){
        $('#btndisabled').removeClass('disabled');
    }


</script>