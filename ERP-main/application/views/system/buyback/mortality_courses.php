<?php echo head_page('Mortality Cause', false);
$date_format_policy = date_format_policy();
$gl_code =fetch_glcode_claim_category();

?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .alpha-box {
        font-size: 14px;
        line-height: 25px;
        list-style: none outside none;
        margin: 0 0 0 12px;
        padding: 0 0 0;
        text-align: center;
        text-transform: uppercase;
        width: 24px;
    }

    ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    .alpha-box li a {
        text-decoration: none;
        color: #555;
        padding: 4px 8px 4px 8px;
    }

    .alpha-box li a.selected {
        color: #fff;
        font-weight: bold;
        background-color: #4b8cf7;
    }

    .alpha-box li a:hover {
        color: #000;
        font-weight: bold;
        background-color: #ddd;
    }
</style>
<div id="filter-panel" class="collapse filter-panel">
</div>
<div class="row">
    <div class="col-md-5">
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="add_mortalityCourse()"><i class="fa fa-plus"></i> Mortality Cause
        </button>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="box-body no-padding">
            <div class="row">
                <div class="col-sm-12">
                    <div id="MortalityCoursesMaster_view"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--modal report-->
<div class="modal fade" id="MortalityCoursesModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Mortality Cause</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="mortalityCourses_form"'); ?>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title">Description</label>
                    </div>
                    <div class="form-group col-sm-8">
                        <input type="text" name="Description" id="Description" class="form-control"
                               placeholder="Description">
                        <input type="hidden" class="form-control" name="causeID"
                               id="causeID">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title">GL Code</label>
                    </div>
                    <div class="form-group col-sm-8">
                        <?php echo form_dropdown('glcode', $gl_code, '', 'class="form-control select2" id="glcode"'); ?>

                    </div>
                </div>
                <!--<div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3">
                        <label class="title">Farmer Charged</label>
                    </div>
                    <div class="form-group col-sm-8">
                        <?php /*echo form_dropdown('glcode', $gl_code, '', 'class="form-control select2" id="glcode"'); */?>

                    </div>
                </div>-->
            </div>
            </form>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" onclick="mortality_state()">Save</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/buyback/mortality_courses', '', 'Mortality Courses');
        });
        getMortalityCoursesManagement_tableView();

    });

    function getMortalityCoursesManagement_tableView() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {},
            url: "<?php echo site_url('Buyback/load_mortalityCourses_Master_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#MortalityCoursesMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function delete_mortalityCourse(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'causeID': id},
                    url: "<?php echo site_url('Buyback/delete_mortalityCourse'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert('s', 'Mortality Cause Deleted Successfully');
                        getMortalityCoursesManagement_tableView();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function mortality_state() {
        var description = $('#Description').val();
        var causeID = $('#causeID').val();
        var glcode = $('#glcode').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {description:description, causeID:causeID, glcode: glcode},
            url: "<?php echo site_url('Buyback/save_mortalityCourses_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    getMortalityCoursesManagement_tableView();
                    $('#MortalityCoursesModal').modal('hide');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function edit_mortalityCourse(causeID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'causeID': causeID},
            url: "<?php echo site_url('Buyback/load_mortalityCourses_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#Description').val(data['Description']);
                    $('#causeID').val(data['causeID']);
                    $('#glcode').val(data['mortalityGLautoID']).change();
                    $("#MortalityCoursesModal").modal({backdrop: "static"});
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function add_mortalityCourse(){
        $('#Description').val('');
        $('#causeID').val('');
        $('#glcode').val('').change();
        $("#MortalityCoursesModal").modal({backdrop: "static"});
    }


</script>