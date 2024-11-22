<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_master_employee_soft_skills_title');
//echo head_page($title, false);

?>
<style>
    .error-message {
        color: red;
    }

    .act-btn-margin {
        margin: 0 2px;
    }

    .multiselect2.dropdown-toggle {
        width: 100%;
    }

    .btn-group {
        width: 100%;
    }
</style>
<section class="content" id="ajax_body_container">
    <div class="row">
        <div class="col-md-12" id="sub-container">
            <div class="box">
                <div class="box-header with-border" id="box-header-with-border">
                    <h3 class="box-title"
                        id="box-header-title"><?php echo $this->lang->line('appraisal_master_rating_title'); ?></h3>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12" id="sub-container">
                            <div class="row" style=" margin-right: 1px">
                                <div class="col-md-9">
                                    &nbsp;
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary pull-right" onclick="appraisalRatingFormPopup()">
                                        Add New Rating
                                        <!--Add new soft skills template-->
                                    </button>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 10px">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="apr_rating_table" class="<?php echo table_class(); ?>">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Rated Value</th>
                                                <th>Rating</th>
                                                <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                                <th>&nbsp;</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="appraisalRatingForm" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-lg" style="width:33%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="CommonEdit_Title">Create New Rating</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label for="ratedValue">Rated Value</label>
                        <input id="ratedValue" class="form-control"/>
                        <div id="ratedValue_error" class="error-message"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="rating">Rating</label>
                        <input id="rating" class="form-control"/>
                        <div id="rating_error" class="error-message"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="description">Description</label>
                        <input id="description" class="form-control"/>
                        <div id="description_error" class="error-message"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button style="float: right; margin-top: 5px;" class="btn btn-primary"
                                onclick="saveRating.call(this)">
                            <?php echo $this->lang->line('common_save'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    app = {};
    app.company_id = <?php echo current_companyID(); ?>;

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/appraisal/master/soft_skills', '', '<?php echo $title ?>');
        });
        fetchAppraisalRating();
    });

    function fetchAppraisalRating() {
        $('#apr_rating_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Appraisal/fetchAppraisalRating'); ?>",
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "appraisalRatingID"},
                {"mData": "ratedValue"},
                {"mData": "rating"},
                {"mData": "description"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function hide_error(errorDivId) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html("");
    }

    function show_error(errorDivId, errorMessage) {
        var divSelector = "#" + errorDivId;
        $(divSelector).html(errorMessage);
    }

    function appraisalRatingFormPopup() {
        app.formStatus = 'create';
        $("#appraisalRatingForm").modal('show');
    }

    function saveRating() {
        var ratedValue = $("#ratedValue").val();
        var rating = $("#rating").val();
        var description = $("#description").val();
        if (ratingFormValidation()) {
            if(app.formStatus=='create'){
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: "<?php echo site_url('Appraisal/saveRating'); ?>",
                    data: {
                        ratedValue: ratedValue,
                        rating: rating,
                        description: description
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            myAlert('s', data.message);
                            fetchAppraisalRating();
                        } else {
                            myAlert('e', data.message);
                        }
                        $("#appraisalRatingForm").modal('hide');
                        $("#ratedValue").val("");
                        $("#rating").val("");
                        $("#description").val("");
                        stopLoad();
                    }
                });
            }else if(app.formStatus=='modify'){
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: "<?php echo site_url('Appraisal/modifyRating'); ?>",
                    data: {
                        id:app.currentRatingID,
                        ratedValue: ratedValue,
                        rating: rating,
                        description: description
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            myAlert('s', data.message);
                            fetchAppraisalRating();
                        } else {
                            myAlert('e', data.message);
                        }
                        $("#appraisalRatingForm").modal('hide');
                        $("#ratedValue").val("");
                        $("#rating").val("");
                        $("#description").val("");
                        stopLoad();
                    }
                });
            }

        }

    }

    app = {};


    function openRatingEdit(){
        app.formStatus = 'modify';
        hide_error('ratedValue_error');
        hide_error('rating_error');
        hide_error('description_error');

        var id =  $(this).data('id');
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/getRatingDetails'); ?>",
            data: {
                id: id
            },
            success: function (data) {
                app.currentRatingID = data.appraisalRatingID;
                $("#ratedValue").val(data.ratedValue);
                $("#rating").val(data.rating);
                $("#description").val(data.description);
                $("#appraisalRatingForm").modal('show');
                stopLoad();
            }
        });
    }

    function ratingFormValidation() {
        var ratedValue = $("#ratedValue").val();
        var rating = $("#rating").val();
        var description = $("#description").val();
        var isValid = true;
        if (ratedValue.replace(/\s/g, "") == "") {
            isValid = false;
            show_error('ratedValue_error', 'Rated value is required.');
        } else {
            hide_error('ratedValue_error');
        }

        if (rating.replace(/\s/g, "") == "") {
            isValid = false;
            show_error('rating_error', 'Rating is required.');
        } else {
            hide_error('rating_error');
        }

        if (description.replace(/\s/g, "") == "") {
            isValid = false;
            show_error('description_error', 'Description is required.');
        } else {
            hide_error('description_error');
        }
        return isValid;
    }

    function deleteRating(){
        var id =  $(this).data('id');
        bootbox.confirm("Are you sure that you want to delete this?", function (confirmed) {
            if (confirmed) {
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: "<?php echo site_url('Appraisal/deleteRatingDetails'); ?>",
                    data: {
                        id: id
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            myAlert('s', data.message);
                            fetchAppraisalRating();
                        } else {
                            myAlert('e', data.message);
                        }
                        stopLoad();
                    }
                });
            }
        });

    }
</script>
