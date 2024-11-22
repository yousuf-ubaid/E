<?php
$allprojectdrop = load_all_project_pm();
$projectrelationship = project_relationship();
?>


<style>

    #cTable tbody tr.highlight td {
        background-color: #FFEE58;
    }


</style>
<div style="padding-bottom: 27px;">

</div>
<table id="cTable" class="table " style="width: 100%">
    <thead>
    <tr>
        <th style="">Task</th>
        <th style="">Note</th>
        <th style="">Start Date</th>
        <th style="">End Date</th>
        <th>Color</th>
        <th style="width: 30px">completed %</th>
        <th style="">Assigned Employee</th>
        <th style="">Sort Order</th>
        <th style="width: 83px"></th>
    </tr>
    </thead>
    <tbody>
    <?php

    if ($header) {

        foreach ($header as $value) {

            if ($value['masterID'] == 0) {

                ?>


                <tr class="header">
                    <?php if ($master['confirmedYN'] != 1) { ?>
                        <td colspan=""><a href="#"><i class="fa fa-minus-square"
                                                      aria-hidden="true"></i></a>

                            <a href="#" data-type="text"
                               data-url="<?php echo site_url('Boq/update_project_planning') ?>"
                               data-pk="<?php echo $value['projectPlannningID'] ?>"
                               data-name="description"
                               data-title="Task" class="xeditable "
                               data-value="<?php echo $value['description'] ?>">
                                <?php echo $value['description'] ?>
                            </a>
                        </td>

                    <?php } else { ?>
                        <td colspan=""><a href="#"><i class="fa fa-minus-square"
                                                      aria-hidden="true"></i></a>

                            <?php echo $value['description'] ?>
                        </td>
                    <?php } ?>

                    <?php if ($master['confirmedYN'] != 1) { ?>

                        <td>
                            <a href="#" data-type="text"
                               data-url="<?php echo site_url('Boq/update_project_planning') ?>"
                               data-pk="<?php echo $value['projectPlannningID'] ?>"
                               data-name="note"
                               data-title="Note" class="xeditable "
                               data-value="<?php echo $value['note'] ?>">
                                <?php echo $value['note'] ?>
                            </a>
                        </td>
                    <?php } else { ?>
                        <td colspan="">
                            <?php echo $value['note'] ?>
                        </td>
                    <?php } ?>


                    <td style="text-align: center"><?php echo $value['startDate'] ?></td>
                    <td style="text-align: center"><?php echo $value['endDate'] ?></td>
                    <?php if ($master['confirmedYN'] != 1) { ?>
                        <td style="text-align: center"><a href="#" data-type="select"
                                                          data-url="<?php echo site_url('Boq/update_project_planning') ?>"
                                                          data-pk="<?php echo $value['projectPlannningID'] ?>"
                                                          data-name="bgColor"
                                                          data-title="Task" class="status"
                                                          data-value="<?php echo $value['bgColor'] ?>">
                                <?php //echo $subvalue['bgColor'] ?>
                            </a></td>
                    <?php } else { ?>
                        <td style="text-align: center"><?php echo $value['bgColor'] ?></td>
                    <?php } ?>


                    <td style="text-align: center">

                        <!--   <a href="#" data-type="number"
                         data-url="<?php /*echo site_url('Boq/update_project_planning') */ ?>"
                         data-pk="<?php /*echo $value['projectPlannningID'] */ ?>"
                         data-name="percentage"
                         data-title="Percentage" class="xeditable "
                         data-value="<?php /*echo $value['percentage'] */ ?>">
                        <?php /*echo $value['percentage'] */ ?>
                      </a>-->
                    </td>
                    <td><?php echo $value['ename2'] ?></td>
                    <td>
                        <?php if ($master['confirmedYN'] != 1) { ?>
                            <select name="sortOrder" id="inlinesortOrder"
                                    onchange="changeSortOrder('m',this.value,<?php echo $value['projectPlannningID'] ?>)">
                                <?php if (!empty($sortOrder)) {
                                    foreach ($sortOrder as $s) {
                                        $select = '';
                                        if ($value['sortOrder'] == $s['sortOrder']) {
                                            $select = 'selected';
                                        }
                                        ?>
                                        <option <?php echo $select ?>
                                                value="<?php echo $s['sortOrder'] ?>"><?php echo $s['sortOrder'] ?></option>
                                        <?php
                                    }
                                } ?>
                            </select>
                        <?php } else { ?>
                            <select name="sortOrder" id="inlinesortOrder"
                                    onchange="changeSortOrder('m',this.value,<?php echo $value['projectPlannningID'] ?>)"
                                    disabled>
                                <?php if (!empty($sortOrder)) {
                                    foreach ($sortOrder as $s) {
                                        $select = '';
                                        if ($value['sortOrder'] == $s['sortOrder']) {
                                            $select = 'selected';
                                        }
                                        ?>
                                        <option <?php echo $select ?>
                                                value="<?php echo $s['sortOrder'] ?>"><?php echo $s['sortOrder'] ?></option>
                                        <?php
                                    }
                                } ?>
                            </select>
                        <?php } ?>

                    </td>
                    <?php if ($master['confirmedYN'] != 1) { ?>
                        <td class="pull-right" colspan=""><a
                                    onclick='attachment_modal(<?php echo $value['projectPlannningID'] ?>,"<?php echo $value['description'] ?>","PLC", 0);'><i
                                        class="glyphicon glyphicon-paperclip" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;<a
                                    onclick="addplanningSub(<?php echo $value['projectPlannningID'] ?>,'<?php echo $value['description'] ?>')"><i
                                        class="fa fa-plus" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
                                    onclick="deleteplanningTask(<?php echo $value['projectPlannningID'] ?>)">
                                <span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
                                    onclick='relationship_pm(<?php echo $value['projectPlannningID'] ?>,"<?php echo $value['description'] ?>",<?php echo $value['headerID'] ?>)'>
                                <span class="glyphicon glyphicon-random"></span></a>


                        </td>
                    <?php } ?>


                </tr>


                <?php
            }

            foreach ($header as $subvalue) {
                if ($value['projectPlannningID'] == $subvalue['masterID']) {?>
                    <tr class="subheader">
                        <td style="padding-left: 35px">
                            <?php if ($master['confirmedYN'] != 1) { ?>
                                <a href="#" data-type="text"
                                   data-url="<?php echo site_url('Boq/update_project_planning') ?>"
                                   data-pk="<?php echo $subvalue['projectPlannningID'] ?>"
                                   data-name="description"
                                   data-title="Task" class="xeditable "
                                   data-value="<?php echo $subvalue['description'] ?>">
                                    <?php echo $subvalue['description'] ?>
                                </a>
                            <?php } else { ?>
                                <?php echo $subvalue['description'] ?>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($master['confirmedYN'] != 1) { ?>
                                <a href="#" data-type="text"
                                   data-url="<?php echo site_url('Boq/update_project_planning') ?>"
                                   data-pk="<?php echo $subvalue['projectPlannningID'] ?>"
                                   data-name="note"
                                   data-title="Task" class="xeditable "
                                   data-value="<?php echo $subvalue['note'] ?>">
                                    <?php echo $subvalue['note'] ?>
                                </a>
                            <?php } else { ?>
                                <?php echo $subvalue['note'] ?>
                            <?php } ?>


                        </td>
                        <td style="text-align: center"><?php echo $subvalue['startDate'] ?></td>
                        <td style="text-align: center"><?php echo $subvalue['endDate'] ?></td>
                        <td style="text-align: center">
                            <?php if ($master['confirmedYN'] != 1) { ?>
                                <a href="#" data-type="select"
                                   data-url="<?php echo site_url('Boq/update_project_planning') ?>"
                                   data-pk="<?php echo $subvalue['projectPlannningID'] ?>"
                                   data-name="bgColor"
                                   data-title="Task" class="status"
                                   data-value="<?php echo $subvalue['bgColor'] ?>">
                                    <?php echo $subvalue['bgColor'] ?>
                                </a>
                            <?php } else { ?>
                                <?php echo $subvalue['bgColor'] ?>
                            <?php } ?>


                        </td>
                        <td style="text-align: center">

                            <?php if ($master['confirmedYN'] != 1) { ?>
                                <a href="#" data-type="number"
                                   data-url="<?php echo site_url('Boq/update_project_planning') ?>"
                                   data-pk="<?php echo $subvalue['projectPlannningID'] ?>"
                                   data-name="percentage"
                                   data-title="Percentage" class="xeditable "
                                   data-value="<?php echo $subvalue['percentage'] ?>">
                                    <?php echo $subvalue['percentage'] ?>
                                </a>
                            <?php } else { ?>
                                <?php echo $subvalue['percentage'] ?>
                            <?php } ?>
                        </td>
                        <td><?php echo $subvalue['ename2'] ?></td>
                        <td>
                            <?php if ($master['confirmedYN'] != 1) { ?>
                                <select name="sortOrder" id="inlinesortOrder"
                                        onchange="changeSortOrder('s',this.value,<?php echo $subvalue['masterID'] ?>,<?php echo $subvalue['projectPlannningID'] ?>)">

                                    <?php
                                    $CI = get_instance();
                                    $sortOrder2 = $CI->db->query("select sortOrder from srp_erp_projectplanning where masterID={$subvalue['masterID']}")->result_array();
                                    if (!empty($sortOrder2)) {
                                        foreach ($sortOrder2 as $so) {
                                            $select = '';
                                            if ($subvalue['sortOrder'] == $so['sortOrder']) {
                                                $select = 'selected';
                                            }
                                            ?>
                                            <option <?php echo $select ?>
                                                    value="<?php echo $so['sortOrder'] ?>"><?php echo $so['sortOrder'] ?></option>
                                            <?php
                                        }
                                    } ?>
                                </select>
                            <?php } else { ?>
                                <select name="sortOrder" id="inlinesortOrder"
                                        onchange="changeSortOrder('s',this.value,<?php echo $subvalue['masterID'] ?>,<?php echo $subvalue['projectPlannningID'] ?>)"
                                        disabled>

                                    <?php
                                    $CI = get_instance();
                                    $sortOrder2 = $CI->db->query("select sortOrder from srp_erp_projectplanning where masterID={$subvalue['masterID']}")->result_array();
                                    if (!empty($sortOrder2)) {
                                        foreach ($sortOrder2 as $so) {
                                            $select = '';
                                            if ($subvalue['sortOrder'] == $so['sortOrder']) {
                                                $select = 'selected';
                                            }
                                            ?>
                                            <option <?php echo $select ?>
                                                    value="<?php echo $so['sortOrder'] ?>"><?php echo $so['sortOrder'] ?></option>
                                            <?php
                                        }
                                    } ?>
                                </select>

                            <?php } ?>
                        </td>
                        <?php if ($master['confirmedYN'] == 1) { ?>
                            <td colspan="">
                                <a onclick="attachment_modal(<?php echo $subvalue['projectPlannningID'] ?>, 'Sub Task','P-Task',<?php echo $master['confirmedYN'] ?>)"><span
                                            title="Attachment" rel="tooltip"
                                            class="glyphicon glyphicon-paperclip"></span></a>
                            </td>
                        <?php } ?>
                        <?php if ($master['confirmedYN'] != 1) { ?>
                            <td>
                                <a onclick='relationship_pm(<?php echo $value['projectPlannningID'] ?>,"<?php echo $value['description'] ?>",<?php echo $value['headerID'] ?>)'>
                                    <span class="glyphicon glyphicon-random"></span></a> |


                                <a onclick="attachment_modal(<?php echo $subvalue['projectPlannningID'] ?>, 'Sub Task','P-Task',<?php echo $master['confirmedYN'] ?>)"><span title="Attachment" rel="tooltip"
                                class="glyphicon glyphicon-paperclip"></span></a>&nbsp;| <a onclick="deleteplanningTask(<?php echo $subvalue['projectPlannningID'] ?>)"><span style="color:#ff3f3a;width: 40%;" class="glyphicon pull-right glyphicon-trash "></span></a>

                        <?php } ?>
                    </tr>
                    <?php
                }
            }


        }

    }
    ?>
    </tbody>
</table>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="relationship_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="projectName"></h4>
            </div>
            <?php echo form_open('', 'role="form" id="pm_relationship"'); ?>
            <div class="modal-body">
                <input type="hidden" id="projectplanningID" name="projectplanningID">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Depended Task</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div id="div_load_project_edit">
                            <select name="relatedprojectID_edit" class="form-control select2"
                                    id="relatedprojectID_edit">
                                <option value="" selected="selected">Select Project</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Relationship</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('relationship', $projectrelationship, '', 'class="form-control select2" id="relationship" '); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary" onclick="save_pm_realationship()"><span
                                class="glyphicon glyphicon-floppy-disk"
                                aria-hidden="true"></span> Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        $('#relationship').select2();
    });

    function changeSortOrder(type, value, id, masterID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'type': type, value: value, id: id, masterID: masterID},
            url: "<?php echo site_url('Boq/change_projectplanningSortOrder'); ?>",
            beforeSend: function () {
                HoldOn.open({
                    theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                });
            },
            success: function (data) {

                loadTaskData($('#headerID').val());
                getchart();
                HoldOn.close();
                refreshNotifications(true);

            }, error: function () {

                HoldOn.close();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }

    $('.tags').editable({
        inputclass: 'input-large',
        select2: {
            tags: ['html', 'javascript', 'css', 'ajax'],
            tokenSeparators: [",", " "]
        }
    });

    $('.status').editable({

        source: [
            {value: "ggroupblack", text: 'Black'},
            {value: "gtaskblue", text: 'Blue'},
            {value: "gtaskred", text: 'Red'},
            {value: "gtaskpurple", text: 'Purple'},
            {value: "gtaskgreen", text: 'Green'},
            {value: "gtaskpink", text: 'Pink'}

        ]
    });
    $('.xeditable').editable();
    $('#cTable').on('click', 'tr', function (e) {
        $('#cTable').find('tr.highlight').removeClass('highlight');
        $(this).addClass('highlight');
    });


    function highlightSearch(searchtext) {
        $('#cTable tr').each(function () {
            $(this).removeClass('highlight');
        });
        if (searchtext !== '') {
            $('#cTable tr').each(function () {
                if ($(this).find('td').text().toLowerCase().indexOf(searchtext.toLowerCase()) == -1) {

                    $(this).removeClass('highlight');
                } else {
                    $(this).addClass('highlight');
                }
            });
        }
    }

    function deleteplanningTask(projectPlannningID) {

        swal({
                title: "Are you sure?",
                text: "Your will not be able to recover this data",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'projectPlannningID': projectPlannningID},
                    url: "<?php echo site_url('Boq/deleteplanning'); ?>",
                    beforeSend: function () {
                        HoldOn.open({
                            theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                        });
                    },
                    success: function (data) {

                        loadTaskData($('#headerID').val());
                        getchart();
                        HoldOn.close();
                        refreshNotifications(true);

                    }, error: function () {

                        HoldOn.close();
                        alert('An Error Occurred! Please Try Again.');
                        refreshNotifications(true);
                    }
                });
            });


    }

    function relationship_pm(projectPlannningID, projectname, headerID) {
        $('#projectName').html(projectname);
        $('#projectplanningID').val(projectPlannningID);
        get_relatedproject_edit(headerID);
        $('#relationship_modal').modal('show');
    }


    function get_relatedproject_edit(projectPlannningID) {

        $('#div_load_project_edit').html('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {projectPlannningID: projectPlannningID},
            url: "<?php echo site_url('Boq/get_project_relatedtask_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_project_edit').html(data);
                $('#relatedprojectID_edit').select2();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function save_pm_realationship() {
        var data = $('#pm_relationship').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/update_projectrelationship'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    loadTaskData($('#headerID')).val();
                    $('#relationship_modal').modal('hide');
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
                refreshNotifications(true);
                stopLoad();
            }
        });
    }


</script>