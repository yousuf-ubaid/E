<style>
    ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .panel.with-nav-tabs .panel-heading {
        padding: 5px 5px 0 5px;
    }

    .panel.with-nav-tabs .nav-tabs {
        border-bottom: none;
    }

    .panel.with-nav-tabs .nav-justified {
        margin-bottom: -1px;
    }

    /********************************************************************/
    /*** PANEL SUCCESS ***/
    .with-nav-tabs.panel-success .nav-tabs > li > a,
    .with-nav-tabs.panel-success .nav-tabs > li > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li > a:focus {
        color: #3c763d;
    }

    .with-nav-tabs.panel-success .nav-tabs > .open > a,
    .with-nav-tabs.panel-success .nav-tabs > .open > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > .open > a:focus,
    .with-nav-tabs.panel-success .nav-tabs > li > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li > a:focus {
        color: #3c763d;
        background-color: white;
        border-color: transparent;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.active > a,
    .with-nav-tabs.panel-success .nav-tabs > li.active > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li.active > a:focus {
        color: #3c763d;
        background-color: #fff;
        border-color: #d6e9c6;
        border-bottom-color: transparent;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu {
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a {
        color: #3c763d;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
        background-color: #d6e9c6;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a,
    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
        color: #fff;
        background-color: #3c763d;
    }

    .panel-success > .panel-heading {
        background-color: white;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.active > a, .with-nav-tabs.panel-success .nav-tabs > li.active > a:hover, .with-nav-tabs.panel-success .nav-tabs > li.active > a:focus {
        color: #000000;
        background-color: #ecf0f5;
        border-color: #ecf0f5;
        border-bottom-color: transparent;
    }    
</style>
<div class="panel with-nav-tabs panel-success" style="border: none;">
    <div class="panel-heading">
        <ul class="nav nav-tabs">
            <?php
            if (!empty($dashboardTab)) {
                $z = 0;
                $zactive = "";
                foreach ($dashboardTab as $val) {
                    if ($z == 0) {
                        $zactive = "active";
                    } else {
                        $zactive = "";
                    }
                    ?>
                    <li class="<?php echo $zactive ?>"><a id="<?php echo $val["userDashboardID"] . $val["pageName"] ?>"
                                                          data-id="0"
                                                          onclick="getTemplate(<?php echo $val["userDashboardID"] ?>, '<?php echo $val["pageName"] ?>');"
                                                          href="#template<?php echo $val["userDashboardID"] ?>"
                                                          data-toggle="tab"
                                                          aria-expanded="true"><span><?php echo $val["dashboardDescription"] ?></span>
                            <span class="pull-right" style="padding-left: 10px;"><i class="fa fa-cog"
                                                                                    onclick="openWidgetedit(<?php echo $val["userDashboardID"] ?>)"></i></span></a>
                    </li>
                    <?php $z++;
                }
            }
            if (!empty($dashboardTab)) {
                ?>
                <button type="button" class="btn btn-primary-new size-sm pull-right" style="margin-top: 8px;"
                        onclick="modal_template_setup()"><i class="fa fa-plus"></i> New Dashboard
                </button>
                <?php
            } else {
                ?>
                <button type="button" class="btn btn-primary-new size-sm pull-right" style="margin-bottom: 6px;"
                        onclick="modal_template_setup()"><i class="fa fa-plus"></i> New Dashboard
                </button>
            <?php } ?>
        </ul>
    </div>
    <div class="panel-body" style="background-color: #ecf0f5;">
        <div class="tab-content">
            <?php
            if (!empty($dashboardTab)) {
                $i = 0;
                $active = "";
                foreach ($dashboardTab as $val) {
                    if ($i == 0) {
                        $active = "active";
                    } else {
                        $active = "";
                    }
                    ?>
                    <div class="tab-pane <?php echo $active ?>" id="template<?php echo $val["userDashboardID"] ?>">

                    </div>
                    <?php $i++;
                }
            } ?>
        </div>
    </div>
</div>

<div class="modal fade" id="openEditWidgetmodel" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create Template Setup <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <?php echo form_open('', 'role="form" id="save_template_setup"'); ?>
                <div class="form-group">
                    <label class="" for="">Description</label>
                    <input type="text" class="form-control input-lg" id="descriptionEdit" name="descriptionEdit">
                    <input type="hidden" name="templateID" id="templateID" value="">
                    <input type="hidden" name="dashboadrDeleteID" id="dashboadrDeleteID" value="">
                </div>
                <div class="form-group">
                    <label class="" for="">Template</label>
                    <div class="row listrap" id="userWidgetEdit">

                    </div>
                    <div class="form-group">
                        <label class="" for="">Template Design</label>
                        <div class="template">
                            <div class="callout callout-success">
                                <p>Click on Template to load the design.</p>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="delete_dashboard()">Delete Dashboard
                </button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="save_template_setup()" id="btnSave">Save
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_template_setup" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create Template Setup <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <?php echo form_open('', 'role="form" id="save_template_setup_add"'); ?>
                <div class="form-group">
                    <label class="" for="">Description</label>
                    <input type="text" class="form-control input-lg" id="description" name="description">
                    <input type="hidden" name="templateIDAdd" id="templateIDAdd" value="">
                </div>
                <div class="form-group">
                    <label class="" for="">Template</label>
                    <div class="row listrap">
                        <?php
                        if (!empty(fetch_widget_template())) {
                            foreach (fetch_widget_template() as $val) {
                                $imageName = $val["imageName"];
                                echo '<div class="col-xs-4 col-md-2">
<div class="listrap-toggle">
<span></span>
    <a href="#" class="thumbnail" onclick="loadTemplateAdd(' . $val["templateID"] . ')">
      <img src="' . base_url("images/$imageName") . '" alt="' . $val["panelDescription"] . '">
    </a>
    </div>
  </div>';
                            }
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <label class="" for="">Template Design</label>
                        <div class="templates">
                            <div class="callout callout-success">
                                <p>Click on Template to load the design.</p>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="save_template_setup_add()" id="btnSave">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_widget" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add widget <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="skin-section">
                    <ul class="list" id="radio_list">
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="addWidget()" id="btnSavewidget">Add
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    var id;
    var position1;
    var position2;
    var dashboardID;
    var positionsID;
    var widgetID;
    var sortOrder;
    $(document).ready(function () {
        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
        <?php
        $i = 0;
        if (!empty($dashboardTab)) {
        foreach ($dashboardTab as $val) {
        if($i == 0){
        ?>

        getTemplate(<?php echo $val["userDashboardID"] ?>, '<?php echo $val["pageName"]  ?>');

        <?php
        }
        $i++;
        }
        }
        ?>
    });
    function getTemplate(userDashboardID, pageName) {
        if ($('#' + userDashboardID + pageName).data('id') == 0) {
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {userDashboardID: userDashboardID, pageName: pageName},
                url: "<?php echo site_url('Finance_dashboard/load_template'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#template' + userDashboardID).html(data);
                    $('#' + userDashboardID + pageName).data('id', 1);
                }, error: function () {

                }
            })
        }
    }

    function openWidgetedit(userDashBoardID) {
        dashboardID = userDashBoardID;
        $("#openEditWidgetmodel").modal('show');
        loadTemplateWidget(userDashBoardID);
        $('#dashboadrDeleteID').val(userDashBoardID);
        //loadWidgetEdit(userDashBoardID);
    }

    function loadTemplateWidget(userDashBoardID) {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('DashboardWidget/loadTemplateWidget'); ?>",
            data: {"userDashboardID": userDashBoardID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //$("#templateID").val(templateID);
                $("#userWidgetEdit").html(data);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Template not found");
            }
        });
    }


    function loadWidgetEdit(userDashBoardID) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('DashboardWidget/loadWidgetEdit'); ?>",
            data: {"userDashboardID": userDashBoardID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$(".overlay").empty();
                if ($.isEmptyObject(data['detail'])) {
                    //$("#body" + value['widgetID']).append('<button type="button" class="btn btn-primary btn-xs" onclick="load_widget()"><i class="fa fa-plus"></i> Add</button>');
                } else {
                    var i = 1;
                    $.each(data['detail'], function (key, value) {
                        $("#body" + value['position']).append('<div style="background-color: #D9D9D9;padding: 0px"><label class="btn btn-primary" style="padding: 1px;"><img src="../images/' + value['widgetImage'] + '" alt="No Image" style="height:156px; width: 100%;" class="img-thumbnail img-check"></label> </div>');
                        $position = "'" + value['position'] + "'";
                        $("#overlay" + value['position']).append('<button type="button" class="btn btn-danger btn-xs" onclick="remove_widget(' + value['positn'] + ',' + value['sortOrder'] + ',' + $position + ',' + value['positionID'] + ')"> Remove</button>');
                        userdashboardWidget = dashboardID + "_" + value['positionID'] + "_" + value['sortOrder'] + "_" + value['widgetID'];
                        $("#overlay" + value['position']).next('input[type="hidden"]').val(userdashboardWidget);
                        i++;
                    });
                }
                $("#descriptionEdit").val(data['description']);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Template not found");
            }
        });
    }


    function loadTemplate(templateID, userDashboardID) {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('DashboardWidget/loadTemplate'); ?>",
            data: {"templateID": templateID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#templateID").val(userDashboardID);
                $(".template").html(data);
                loadWidgetEdit(userDashboardID)
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Template not found");
            }
        });
    }

    function addWidget() {
        var labelimage = $('[name="iCheck"]').attr('data-image');
        var labelname = $('[name="iCheck"]').attr('data-widname');
        if (labelimage != "") {
            var labeltextValue = $('[name="iCheck"]').val();
            $("#body" + position2).html('<div style="background-color: #D9D9D9;padding: 5px"><label class="btn btn-primary" style="padding: 1px;"><img src="../images/' + labelimage + '" title="' + labelname + '" alt="No Image" style="height:156px; width: 100%;" class="img-thumbnail img-check"></label> </div>');
            $("#overlay" + position2).html('<button type="button" style="margin-top: -67px;" class="btn btn-danger btn-xs" onclick="remove_widget(' + position1 + ',' + id + ',\'' + position2 + '\',' + positionsID + ')"> Remove</button>');
            userdashboardWidget = dashboardID + "_" + positionsID + "_" + id + "_" + labeltextValue;

            $("#overlay" + position2).next('input[type="hidden"]').val(userdashboardWidget);
            //alert(positionsID);
            $('#modal_widget').modal("hide");
            //addToArray(id);
        } else {
            notification("Please select a widget", 'w');
        }
    }

    function remove_widget(positionID, sortOrder, id, positnID) {
        $("#body" + id).html('');
        $positn = "'" + id + "'";
        $("#overlay" + id).html('<button type="button" class="btn btn-primary btn-xs" onclick="load_widget(' + positionID + ',' + sortOrder + ',' + $positn + ',' + positnID + ')"><i class="fa fa-plus"></i> Add</button>');
        $("#overlay" + id).next('input[type="hidden"]').val('');
    }

    function load_widget(position, sortorder, positionDB, positipnID) {

        id = sortorder;
        position1 = position;
        position2 = positionDB;
        positionsID = positipnID;
        //alert(positionsID);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('DashboardWidget/loadWidget'); ?>",
            data: {"position": position, "sortorder": sortorder},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#radio_list").html("");
                if (jQuery.isEmptyObject(data)) {
                    $("#radio_list").append('<li><input type="radio" name="iCheck"><label>No Widgets Found</label></li>');
                    $("#btnSavewidget").attr('disabled', "btnSavewidget")
                } else {
                    $.each(data, function (index, value) {
                       // $("#radio_list").append('<li><input type="radio" name="iCheck" value="' + value.widgetID + '"><label>' + value.widgetName + '</label></li>');
                        $("#radio_list").append('<div class="col-md-4" style="margin-top: 5px;"><label class="btn btn-primary" style="padding: 1px;"><img data-toggle="tooltip"  title="' + value.widgetName + '" src="../images/' + value.widgetImage + '" alt="../images/no-image.png" id="imgchk_' + value.widgetID + '" style="height:100px; width: 100%;" class="img-thumbnail img-check imgchkbox"><input onclick="checkimg(' + value.widgetID + ')" type="checkbox" data-widname="' + value.widgetName + '" data-image="' + value.widgetImage + '"  id="iCheck_' + value.widgetID + '" value="' + value.widgetID + '" class="hidden widgetcheck" autocomplete="off"></label></div>');
                    });
                    $("#btnSavewidget").prop('disabled', false);
                }
                $('input[type=radio]').iCheck('destroy');
                $('input[type=radio]').each(function () {
                    var self = $(this),
                        label = self.next(),
                        label_text = label.text();

                    label.remove();
                    self.iCheck({
                        checkboxClass: 'icheckbox_line-red',
                        radioClass: 'iradio_line-red',
                        insert: '<div class="icheck_line-icon"></div>' + label_text
                    });
                });
                load_widget_modal();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Template not found");
            }
        });
    }
    function load_widget_modal() {
        $('#modal_widget').modal();
    }

    function save_template_setup() {
        var data = $('#save_template_setup').serializeArray();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('DashboardWidget/save_template_setup'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data[0] == 's') {
                    stopLoad();
                    myAlert('s', 'Message: ' + data[1]);
                    location.reload();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Template not found");
            }
        });

    }

    function modal_template_setup() {
        $('#modal_template_setup').modal();
    }

    function loadTemplateAdd(templateID) {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('DashboardWidget/loadTemplate'); ?>",
            data: {"templateID": templateID},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#templateIDAdd").val(templateID);
                $(".templates").html(data)
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Template not found");
            }
        });
    }


    function save_template_setup_add() {
        var data = $('#save_template_setup_add').serializeArray();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('DashboardWidget/save_template_setup_add'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data[0] == 's') {
                    stopLoad();
                    myAlert('s', 'Message: ' + data[1]);
                    location.reload();
                } else if (data[0] == 'e') {
                    stopLoad();
                    myAlert('e', 'Message: ' + data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Please Select Widget");
            }
        });

    }

    /*function addToArray(id){
     alert(id);
     }*/

    function delete_dashboard() {
        var dashboardID = $('#dashboadrDeleteID').val();
        swal({
                title: "Are you sure?",
                text: "You want to delete this dashboard!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('DashboardWidget/delete_dashboard'); ?>",
                    data: {"dashboardID": dashboardID},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (data[0] == 's') {
                            stopLoad();
                            myAlert('s', 'Message: ' + data[1]);
                            location.reload();
                        } else if (data[0] == 'e') {
                            stopLoad();
                            myAlert('e', 'Message: ' + data[1]);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', 'Message: ' + "Please Contact Support Team");
                    }
                });
            }
        );
    }


</script>