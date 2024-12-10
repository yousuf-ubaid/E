<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('dashboard_groupmonitoring_lang', $primaryLanguage);

?>

<div class="row">
    <div class="col-md-12">
        <div>

            <div>

                <!--header contnet -->
                <div id="filter-panel" class="collapse filter-panel"></div>

                <style>
                    ul {
                        margin: 0;
                        padding: 0;
                        list-style: none;
                    }

                    .panel.with-nav-tabs .panel-heading {
                        padding: 5px 5px 0 5px;
                    }

                    .panel.with-nav-tabs .mainpanel {
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

                    .with-nav-tabs.panel-success .mainpanel > li.active > a, .with-nav-tabs.panel-success .mainpanel > li.active > a:hover, .with-nav-tabs.panel-success .mainpanel > li.active > a:focus {
                        color: #000000;
                        background-color: #ecf0f5;
                        border-color: #ecf0f5;
                        border-bottom-color: transparent;
                    }

                    .check {
                        opacity: 0.5;
                        color: #996;

                    }

                    .dataTables_wrapper .dataTables_paginate .paginate_button {
                        font-size: 12px;
                    }
                    .pagination>li>a, .pagination>li>span {
                        padding: 2px 8px;
                    }
                </style>
                <div class="panel with-nav-tabs panel-success" style="border: none;">
                    <div class="panel-heading">
                        <ul class="nav nav-tabs mainpanel">

                            <li class="active">
                                <a id=""
                                   data-id="0"
                                   onclick="group_monitoring_dashboard();"
                                   href="#groupmonitoringtab"
                                   data-toggle="tab"
                                   aria-expanded="true"><span><?php echo $this->lang->line('dashboard_groupmonitoring_heading');?></span>
                                    <span class="pull-right" style="padding-left: 10px;"></span></a>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-body" style="background-color: #ecf0f5;">
                        <div class="tab-content">
                            <div class="tab-pane active" id="groupmonitoringtab">
                                <fieldset class="scheduler-border">
                                    <?php echo form_open('login/loginSubmit', ' name="frm_rpt_customer_invoice" id="frm_rpt_customer_invoice" class="form-group" role="form"'); ?>

                                    <div class="form-group col-sm-2">
                                        <div class="col-sm-12">
                                            <label for=""><?php echo $this->lang->line('dashboard_company');?></label>
                                        </div>
                                        <div class="col-sm-12">
                                        <?php echo form_dropdown('companyID[]', drill_down_navigation_dropdown_dashboard_user(), '', 'multiple  class="form-control" id="companyID" required'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-2">
                                        <label for=""><?php echo $this->lang->line('dashboard_year');?></label>
                                        <?php echo form_dropdown('Year', documentyeardropdown(), '', 'class="form-control select2" id="Year"'); ?>
                                    </div>
                                    <div class="form-group col-sm-1">
                                        <label for="" style="color: #ecf0f5;">Generate</label>
                                        <button type="button" onclick="group_monitoring_dashboard()"
                                                class="btn btn-primary">
                                            <?php echo $this->lang->line('dashboard_generate');?></button>
                                    </div>

                               <div class="form-group col-sm-1">
                                        <label for=""><?php echo $this->lang->line('dashboard_currency') ?></label>
                                        <?php echo form_dropdown('currency[]', all_currency_new_drop_groupmonitoring(), $this->common_data['company_data']['company_default_currencyID'], 'class="form-control" id="currency" required disabled'); ?>
                                    </div>


                                    <div class="form-group col-sm-6">
                                        <div class="row" style="margin-top: 10px;">

                                            <div class="form-group col-sm-3">
                                                <button type="button" class="btn btn-primary pull-right"
                                                        style="margin-top: 18px;"
                                                        onclick="sync_data_groupmontoringrpt()"><i class="fa fa-plus"></i> <?php echo $this->lang->line('dashboard_update')?> <!--New Dashboard-->

                                                </button>
                                            </div>
                                            <div class="form-group col-sm-5" style="padding-left: 15%;">
                                                <label class="title" style="margin-top: 18px;"><?php echo $this->lang->line('dashboard_lastupdatedon')?> :</label>
                                            </div>
                                            <div class="form-group col-sm-3" >
                                                <label style="margin-top: 18px;font-size: 13px;"><strong id="lastupdate">....</strong>

                                            </div>
                                        </div>
                                        </div>





                                    </div>




                            </div>
                            <?php echo form_close(); ?>
                            </fieldset>
                                <div id="groupmonitoring">

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal fade" id="openEditWidgetmodel" role="dialog" data-keyboard="false"
                     data-backdrop="static">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content" id="">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"><?php echo $this->lang->line('dashboard_create_template_setup');?><!--Create Template Setup--> <span id=""></span></h4>
                            </div>
                            <div class="modal-body" style="margin-left: 10px">
                                <?php echo form_open('', 'role="form" id="save_template_setup"'); ?>
                                <div class="row">
                                    <div class="form-group col-sm-5">
                                        <label class="" for=""><?php echo $this->lang->line('common_description');?><!--Description--></label>
                                        <input type="text" class="form-control " id="descriptionEdit"
                                               name="descriptionEdit">
                                        <input type="hidden" name="templateID" id="templateID" value="">
                                        <input type="hidden" name="dashboadrDeleteID" id="dashboadrDeleteID" value="">
                                    </div>
                                    <div class="form-group col-sm-5">
                                        <label class="" for=""><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></label>
                                        <input type="text" class="form-control input-sm" id="sortOrder"
                                               name="sortOrder">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="" for=""><?php echo $this->lang->line('dashboard_template');?><!--Template--></label>
                                    <div class="row listrap" id="userWidgetEdit">

                                    </div>
                                    <div class="form-group">
                                        <label class="" for=""><?php echo $this->lang->line('dashboard_template_design');?><!--Template Design--></label>
                                        <div class="template">
                                            <div class="callout callout-success">
                                                <p><?php echo $this->lang->line('dashboard_click_on_template_to_load_the_design');?><!--Click on Template to load the design-->.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-default btn-sm" data-dismiss="modal" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="delete_dashboard()"><?php echo $this->lang->line('dashboard_delete_dashboard');?><!--Delete Dashboard-->
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm" onclick="save_template_setup()"
                                        id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="modal_template_setup" role="dialog" data-keyboard="false"
                     data-backdrop="static">
                    <div class="modal-dialog" style="width: 80%">
                        <div class="modal-content" id="">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"><?php echo $this->lang->line('dashboard_create_template_setup');?><!--Create Template Setup--> <span id=""></span></h4>
                            </div>
                            <div class="modal-body" style="margin-left: 10px">
                                <?php echo form_open('', 'role="form" id="save_template_setup_add"'); ?>
                                <div class="form-group">
                                    <label class="" for=""><?php echo $this->lang->line('dashboard_description');?><!--Description--></label>
                                    <input type="text" class="form-control input-lg" id="description"
                                           name="description">
                                    <input type="hidden" name="templateIDAdd" id="templateIDAdd" value="">
                                </div>
                                <div class="form-group">
                                    <label class="" for=""><?php echo $this->lang->line('common_sort_order');?><!--Sort Order--></label>
                                    <input type="text" class="form-control input-sm" id="sortOrdersave"
                                           name="sortOrdersave">
                                </div>
                                <div class="form-group">
                                    <label class="" for=""><?php echo $this->lang->line('dashboard_template');?><!--Template--></label>
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
                                        <label class="" for=""><?php echo $this->lang->line('dashboard_template_design');?><!--Template Design--></label>
                                        <div class="templates">
                                            <div class="callout callout-success">
                                                <p><?php echo $this->lang->line('dashboard_click_on_template_to_load_the_design');?><!--Click on Template to load the design-->.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                                <button type="submit" class="btn btn-primary btn-sm" onclick="save_template_setup_add()"
                                        id="btnSave">
                                    <?php echo $this->lang->line('common_save');?>    <!--Save-->
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
                                <h4 class="modal-title"> <?php echo $this->lang->line('dashboard_add_widget');?> <!--Add widget--> <span id=""></span></h4>
                            </div>
                            <div class="modal-body" style="margin-left: 10px">
                                <div class="skin-section row" id="radio_list">
                                    <!--<ul class="list" id="radio_list">
                                    </ul>-->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"> <?php echo $this->lang->line('common_Close');?> <!--Close--></button>
                                <button type="submit" class="btn btn-primary btn-sm" onclick="addWidget()"
                                        id="btnSavewidget"><?php echo $this->lang->line('common_add');?><!--Add-->
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
             <div class="modal fade" id="sumarydrilldownModal" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" style="width: 95%">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titledescription">..</h4>
                    </div>
                    <div class="modal-body" id="sumarydd">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    </div>
                </div>
            </div>
             </div>

        <div class="modal fade" id="balancesheet" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" style="width: 95%">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titledescriptionbalance">..</h4>
                    </div>
                    <div class="modal-body" id="sumaryddbalancesheet">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="localizationempdrilldown" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" style="width: 45%">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="titlelocalizationempdrilldown">..</h4>
                    </div>
                    <div class="modal-body" id="sumarylocalizationemp">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
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
                        $('.select2').select2();
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
                        $('#companyID').multiselect2({
                            enableCaseInsensitiveFiltering: true,
                            includeSelectAllOption: true,
                            selectAllValue: 'select-all-value',
                            selectAllText: '<?php echo $this->lang->line('dashboard_selectall'); ?>',
                            nonSelectedText: '<?php echo $this->lang->line('dashboard_noneselected');?>',
                            allSelectedText: '<?php echo $this->lang->line('dashboard_allselected'); ?>',

                            //enableFiltering: true
                            buttonWidth: 150,
                            maxHeight: 200,
                            numberDisplayed: 1
                        });
                        $("#companyID").multiselect2('selectAll', false);
                        $("#companyID").multiselect2('updateButtonText');



                        group_monitoring_dashboard();

                    });
                    function getTemplate(userDashboardID, pageName) {
                        if ($('#' + userDashboardID + pageName).data('id') == 0) {
                            $.ajax({
                                async: true,
                                type: 'POST',
                                dataType: 'html',
                                data: {userDashboardID: userDashboardID, pageName: pageName, '<?php echo  $this->security->get_csrf_token_name() ?>': '<?php echo $this->security->get_csrf_hash() ; ?>'},
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
                                myAlert('e', 'An error occurred!');
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
                                        $("#overlay" + value['position']).append('<button type="button" class="btn btn-danger btn-xs" onclick="remove_widget(' + value['positn'] + ',' + value['sortOrder'] + ',' + $position + ',' + value['positionID'] + ')"><?php echo $this->lang->line('dashboard_remove');?></button>');<!--Remove-->
                                        userdashboardWidget = dashboardID + "_" + value['positionID'] + "_" + value['sortOrder'] + "_" + value['widgetID'];
                                        $("#overlay" + value['position']).next('input[type="hidden"]').val(userdashboardWidget);
                                        i++;
                                    });
                                }
                                $("#descriptionEdit").val(data['description']);
                                $("#sortOrder").val(data['Order']);

                                stopLoad();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                stopLoad();
                                myAlert('e', 'An error occurred!');
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
                                myAlert('e', 'An error occurred!');
                            }
                        });
                    }

                    function remove_widget(positionID, sortOrder, id, positnID) {
                        $("#body" + id).html('');
                        $positn = "'" + id + "'";
                        $("#overlay" + id).html('<button type="button" class="btn btn-primary btn-xs" onclick="load_widget(' + positionID + ',' + sortOrder + ',' + $positn + ',' + positnID + ')"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add');?></button>');<!--Add-->
                        $("#overlay" + id).next('input[type="hidden"]').val('');
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
                                myAlert('e', 'An error occurred!');
                            }
                        });

                    }


                    function modal_template_setup() {
                        $('#modal_template_setup').modal();
                        $(".templates").html('<div class="callout callout-success"> <p><?php echo $this->lang->line('dashboard_click_on_template_to_load_the_design');?><!--Click on Template to load the design-->.</p> </div>');

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
                                $(".template").html("")
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                stopLoad();
                                myAlert('e', 'An error occurred!');
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
                                myAlert('e', 'An error occurred!');
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
                                        myAlert('e', 'An error occurred!');
                                    }
                                });
                            }
                        );
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
                                        //$("#radio_list").append('<li><input type="radio" name="iCheck" value="' + value.widgetID + '"><label>' + value.widgetName + '</label></li>');
                                        //$("#radio_list").append('<div class="col-xs-8 col-md-4"> <div class="listrap-toggle"> <span></span> <a href="#" class="thumbnail" type="radio" name="iCheck" value="'+value.widgetID+'"> <img src="../images/'+value.widgetImage+'" alt="../images/no-image.png"> </a> </div> </div>');
                                        $("#radio_list").append('<div class="col-md-4" style="margin-top: 5px;"><label class="btn btn-primary" style="padding: 1px;"><img data-toggle="tooltip"  title="' + value.widgetName + '" src="../images/' + value.widgetImage + '" alt="../images/no-image.png" id="imgchk_' + value.widgetID + '" style="height:100px; width: 100%;" class="img-thumbnail img-check imgchkbox"><input onclick="checkimg(' + value.widgetID + ')" type="checkbox" data-widname="' + value.widgetName + '" data-image="' + value.widgetImage + '"  id="iCheck_' + value.widgetID + '" value="' + value.widgetID + '" class="hidden widgetcheck" autocomplete="off"></label></div>');
                                    });
                                    $("#btnSavewidget").attr('disabled', false)
                                }
                                load_widget_modal();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                stopLoad();
                                myAlert('e', 'An error occurred!');
                            }
                        });
                    }

                    function checkimg(id) {
                        $('.imgchkbox').removeClass("check");
                        $(".widgetcheck").attr("name", "");
                        $('.widgetcheck').attr("checked", false);
                        $('#imgchk_' + id).addClass("check");
                        $('#iCheck_' + id).attr("checked", true);
                        $('#iCheck_' + id).attr("name", "iCheck");
                    }

                    function addWidget() {
                        var labelimage = $('[name="iCheck"]').attr('data-image');
                        var labelname = $('[name="iCheck"]').attr('data-widname');
                        if (labelimage != "") {
                            var labeltextValue = $('[name="iCheck"]').val();
                            $("#body" + position2).html('<div style="background-color: #D9D9D9;padding: 5px"><label class="btn btn-primary" style="padding: 1px;"><img src="../images/' + labelimage + '" title="' + labelname + '" alt="No Image" style="height:156px; width: 100%;" class="img-thumbnail img-check"></label> </div>');
                            $("#overlay" + position2).html('<button type="button" style="margin-top: -67px;" class="btn btn-danger btn-xs" onclick="remove_widget(' + position1 + ',' + id + ',\'' + position2 + '\',' + positionsID + ')"> <?php echo $this->lang->line('dashboard_remove');?><!--Remove--></button>');
                            userdashboardWidget = dashboardID + "_" + positionsID + "_" + id + "_" + labeltextValue;

                            $("#overlay" + position2).next('input[type="hidden"]').val(userdashboardWidget);
                            //alert(positionsID);
                            $('#modal_widget').modal("hide");
                            //addToArray(id);
                        } else {
                            notification("Please select a widget", 'w');
                        }
                    }

                    function reloadLocation() {
                        location.reload();
                    }
                    function group_monitoring_dashboard()
                    {
                        var data = $('#frm_rpt_customer_invoice').serializeArray();


                        $.ajax({
                            type: 'POST',
                            dataType: 'html',
                            data: data,
                            url: "<?php echo site_url('Finance_dashboard/load_group_reporting'); ?>",
                            cache: false,
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                $('#groupmonitoring').html(data);
                                load_lastup();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                stopLoad();
                                myAlert('e', 'An error occurred!');
                            }
                        });
                    }


                    function sync_data_groupmontoringrpt() {

                        swal({
                                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                                text: "<?php echo $this->lang->line('dashboard_youwanttoupdaterecordsforthisgroup')?>",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "<?php echo $this->lang->line('dashboard_yes')?>",
                                cancelButtonText: "<?php echo $this->lang->line('dashboard_cancel')?>"
                            },
                            function () {
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "<?php echo site_url('Finance_dashboard/sync_details_group_rpt'); ?>",
                            cache: false,
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                myAlert(data[0], data[1]);
                                if (data[0] == 's') {
                                    group_monitoring_dashboard();

                                } else if (data[0] == 'e') {


                                }
                                stopLoad();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                stopLoad();
                                myAlert('e', 'An error occurred!');
                            }
                        });
                            });
                    }
                    function load_lastup() {
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "<?php echo site_url('Finance_dashboard/load_last_update'); ?>",
                            cache: false,
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                if (!jQuery.isEmptyObject(data)) {
                                    $('#lastupdate').html(data['lastupdate'])
                                } else {
                                    $('#lastupdate').html('....')
                                }
                                stopLoad();

                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                stopLoad();
                                myAlert('e', 'An error occurred!');
                            }
                        });
                    }
                    function incomestatementdrill(accountCategoryTypeID,CategoryTypeDescription) {
                            var companyID = $('#companyID').val();
                            var year = $('#Year').val();
                            $('#titledescription').html('')
                          $.ajax({
                            type: "POST",
                            url: "<?php echo site_url('Finance_dashboard/groupmonitoringdashboard') ?>",
                            data: {'accountCategoryTypeID': accountCategoryTypeID,'companyID': companyID,'year': year},
                            dataType: "html",
                            cache: false,
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                $('#titledescription').html('<?php echo $this->lang->line('dashboard_drilldown')?> - '+CategoryTypeDescription);
                                $("#sumarydd").html(data);
                                $('#sumarydrilldownModal').modal('show');
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                stopLoad();
                                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                            }
                        });



                    }
                    function balancesheetdrilldwn(accountCategoryTypeID,CategoryTypeDescription) {
                        var companyID = $('#companyID').val();
                        var year = $('#Year').val();
                        $('#titledescriptionbalance').html('')
                        $.ajax({
                            type: "POST",
                            url: "<?php echo site_url('Finance_dashboard/groupmonitoringdashboard_balancesheet') ?>",
                            data: {'accountCategoryTypeID': accountCategoryTypeID,'companyID': companyID,'year': year},
                            dataType: "html",
                            cache: false,
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                $('#titledescriptionbalance').html('<?php echo $this->lang->line('dashboard_drilldown');?> - '+CategoryTypeDescription);
                                $("#sumaryddbalancesheet").html(data);
                                $('#balancesheet').modal('show');
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                stopLoad();
                                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                            }
                        });
                    }
                    function view_employee_countrywise(id,emplocal) {
                        var companyID = $('#companyID').val();
                        $('#titlelocalizationempdrilldown').html('')
                        $.ajax({
                            type: "POST",
                            url: "<?php echo site_url('Finance_dashboard/emplocalizationdrilldown') ?>",
                            data: {'type': id,'companyID': companyID},
                            dataType: "html",
                            cache: false,
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                $('#titlelocalizationempdrilldown').html('<?php echo $this->lang->line('dashboard_localization_sim');?>  - '+emplocal);
                                $("#sumarylocalizationemp").html(data);
                                $('#localizationempdrilldown').modal('show');
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                stopLoad();
                                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                            }
                        });

                    }

                </script>


            </div>
        </div>
    </div>
</div>
<?php //echo footer_page('Right foot', 'Left foot', false); ?>
<!-- DO NOT CODE BEYOND THIS LINE  -->
