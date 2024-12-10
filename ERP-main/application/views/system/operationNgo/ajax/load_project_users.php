<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('operation_ngo_helper');

?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .headrowtitle {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }

    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 3px 0 3px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .numberColoring {
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>
<br>
<?php
if (!empty($userassignloadview)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="10">
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;"></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;">Employee Name</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;"
                ">Add</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;"
                ">Edit</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;"
                ">Delete</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;">Confirm</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;">Approval</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;">View</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;"> </td>
            </tr>
            <?php
            $x = 1;
            foreach ($userassignloadview as $row) {

                if ($row['isAdd'] == 1) {
                    $status = "checked";
                } else {
                    $status = "";
                }
                if ($row['isEdit'] == 1) {
                    $status_is_edit = "checked";
                } else {
                    $status_is_edit = "";
                }
                if ($row['isConfirm'] == 1) {
                    $status_is_confirm = "checked";
                } else {
                    $status_is_confirm = "";
                }
                if ($row['isApproval'] == 1) {
                    $status_is_approval = "checked";
                } else {
                    $status_is_approval = "";
                }
                if ($row['isView'] == 1) {
                    $status_is_view = "checked";
                } else {
                    $status_is_view = "";
                }
                if ($row['isDelete'] == 1) {
                    $status_is_delete= "checked";
                } else {
                    $status_is_delete = "";
                }
                ?>

                <tr>
                    <input type="hidden" name="proposaldonor" id="ngo_project_id"
                           value="<?php echo $row['projectID'] ?>">
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width="10%" style="text-align: center"><?php echo $row['Ename2']; ?></td>
                    <td class="mailbox-star" width="10%">
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns" style="text-align: center">
                                <input id="isAdd_<?php echo $row['projectOwnerID'] ?>"
                                       type="checkbox"<?php echo $status ?>
                                       data-caption=""
                                       class="columnSelected isadd"
                                       name="isadd"
                                       value="<?php echo $row['projectOwnerID'] ?>">
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-star" width="10%" style="text-align: center">
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns" style="text-align: center">
                                <input id="isAdd_<?php echo $row['projectOwnerID'] ?>"
                                       type="checkbox"<?php echo $status_is_edit ?>
                                       data-caption=""
                                       class="columnSelected isedit"
                                       name="isedit"
                                       value="<?php echo $row['projectOwnerID'] ?>">
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-star" width="10%" style="text-align: center">
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns" style="text-align: center">
                                <input id="isAdd_<?php echo $row['projectOwnerID'] ?>"
                                       type="checkbox"<?php echo $status_is_delete ?>
                                       data-caption=""
                                       class="columnSelected isdelete"
                                       name="isdelete"
                                       value="<?php echo $row['projectOwnerID'] ?>">
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-star" width="10%" style="text-align: center">
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns" style="text-align: center">
                                <input id="isAdd_<?php echo $row['projectOwnerID'] ?>"
                                       type="checkbox"<?php echo $status_is_confirm ?>
                                       data-caption=""
                                       class="columnSelected isconfirm"
                                       name="isconfirm"
                                       value="<?php echo $row['projectOwnerID'] ?>">
                            </div>
                        </div>


                    </td>
                    <td class="mailbox-star" width="10%" style="text-align: center">
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns" style="text-align: center">
                                <input id="isAdd_<?php echo $row['projectOwnerID'] ?>"
                                       type="checkbox"<?php echo $status_is_approval ?>
                                       data-caption=""
                                       class="columnSelected isapproval"
                                       name="isapproval"
                                       value="<?php echo $row['projectOwnerID'] ?>">
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-star" width="10%" style="text-align: center">
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns" style="text-align: center">
                                <input id="isAdd_<?php echo $row['projectOwnerID'] ?>"
                                       type="checkbox"<?php echo $status_is_view ?>
                                       data-caption=""
                                       class="columnSelected isview"
                                       name="isview"
                                       value="<?php echo $row['projectOwnerID'] ?>">
                            </div>
                        </div>

                    </td>

                    <td class="mailbox-star" width="10%" style="text-align: center">
                         <span title="Delete">
                            <a onclick="delete_assign_user_ngo_projects(<?php echo $row['projectOwnerID'] ?>)"><span
                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                        style="color:rgb(209, 91, 71);"></span></a>
                        </span>
                    </td>
                </tr>
                <?php
                $x++;
            } ?>
            </tbody>
        </table>
    </div>
<?php } else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
<?php } ?>


<script>
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        $('.select2').select2();
    });
    $('.extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });
    $('input').on('ifChecked', function (event) {
        if ($(this).hasClass('isadd')) {
            update_is_add_stauts(this.value, 1);

        } else if ($(this).hasClass('isedit')) {
            update_is_edit_stauts(this.value, 1)
        } else if ($(this).hasClass('isconfirm')) {
            update_is_confirm_stauts(this.value, 1)
        }
        else if ($(this).hasClass('isapproval')) {
            update_is_approval_stauts(this.value, 1)
        }
        else if ($(this).hasClass('isview')) {
            update_is_view_stauts(this.value, 1)
        }
        else if ($(this).hasClass('isdelete')) {
            update_is_delete_stauts(this.value, 1)
        }
    });

    $('input').on('ifUnchecked', function (event) {
        if ($(this).hasClass('isadd')) {
            update_is_add_stauts(this.value, 0);

        } else if ($(this).hasClass('isedit')) {
            update_is_edit_stauts(this.value, 0)
        } else if ($(this).hasClass('isconfirm')) {
            update_is_confirm_stauts(this.value, 0)
        }
        else if ($(this).hasClass('isapproval')) {
            update_is_approval_stauts(this.value, 0)
        }
        else if ($(this).hasClass('isview')) {
            update_is_view_stauts(this.value, 0)
        }else if ($(this).hasClass('isdelete')) {
            update_is_delete_stauts(this.value, 0)
        }

    });

    function update_is_add_stauts(projectOwnerID, status) {
        var projectid = $('#ngo_project_id').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {projectid: projectid, projectOwnerID: projectOwnerID, status: status},
            url: "<?php echo site_url('OperationNgo/update_is_add_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                fetch_user_assign();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }

    function update_is_edit_stauts(projectOwnerID, statusisedit) {
        var projectid = $('#ngo_project_id').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {projectid: projectid, projectOwnerID: projectOwnerID, statusisedit: statusisedit},
            url: "<?php echo site_url('OperationNgo/update_is_edit_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                fetch_user_assign();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }

    function update_is_confirm_stauts(projectOwnerID, statusisconfirm) {
        var projectid = $('#ngo_project_id').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {projectid: projectid, projectOwnerID: projectOwnerID, statusisconfirm: statusisconfirm},
            url: "<?php echo site_url('OperationNgo/update_is_confirm_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                fetch_user_assign();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }

    function update_is_approval_stauts(projectOwnerID, statusisapproval) {
        var projectid = $('#ngo_project_id').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {projectid: projectid, projectOwnerID: projectOwnerID, statusisapproval: statusisapproval},
            url: "<?php echo site_url('OperationNgo/update_is_approval_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                fetch_user_assign();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }

    function update_is_view_stauts(projectOwnerID, statusisview) {
        var projectid = $('#ngo_project_id').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {projectid: projectid, projectOwnerID: projectOwnerID, statusisview: statusisview},
            url: "<?php echo site_url('OperationNgo/update_is_view_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                fetch_user_assign();
            }, error: function () {
                stopLoad();
                myAlert('e', 'error,Please contact support team');
            }
        });
    }
    function update_is_delete_stauts(projectOwnerID,statusisdelete){
        var projectid = $('#ngo_project_id').val();
        $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {projectid: projectid, projectOwnerID: projectOwnerID, statusisdelete: statusisdelete},
        url: "<?php echo site_url('OperationNgo/update_is_delete_status'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            myAlert(data[0], data[1]);
            fetch_user_assign();
        }, error: function () {
            stopLoad();
            myAlert('e', 'error,Please contact support team');
        }
    });
    }
</script>
