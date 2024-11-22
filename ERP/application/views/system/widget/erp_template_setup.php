<?php echo head_page('Template Setup', false); ?>
<style>
    ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/draggable/css/main.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <button style="margin-right: 15px" type="button" class="btn btn-primary pull-right "
            onclick="modal_template_setup()"><i class="fa fa-plus"></i> Create Template
    </button>
</div>
<hr style="margin-top: 10px">
<div class="row">
    <div class="col-sm-12" id="div_reload">
        <table id="table_template_setup" class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <!-- <th style="width: 10px">#</th>-->
                <th style="min-width: 30%">Template Description</th>
                <th style="min-width: 20%">Template Type</th>
                <th style="min-width: 20%"></th>
            </tr>
            </thead>
        </table>
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
                <?php echo form_open('', 'role="form" id="save_template_setup"'); ?>
                <div class="form-group">
                    <label class="" for="">Description</label>
                    <input type="text" class="form-control input-lg" id="description" name="description">
                    <input type="hidden" name="templateID" id="templateID" value="">
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
    <a href="#" class="thumbnail" onclick="loadTemplate(' . $val["templateID"] . ')">
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
                        <div class="template"><div class="callout callout-success">
                                <p>Click on Template to load the design.</p>
                            </div></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="save_template_setup()" id="btnSave">Save
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
                <button type="submit" class="btn btn-primary btn-sm" onclick="addWidget()" id="btnSave">Add
                </button>
            </div>
        </div>
    </div>
</div>
<?php
echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var id;
    var position1;
    var position2;
    $(document).ready(function () {
        template_setup();
    });

    function template_setup() {
        window.Otable = $('#table_template_setup').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "recordsFiltered": 10,
            "sAjaxSource": "<?php echo site_url('DashboardWidget/fetch_userdashboardmaster'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {

            },
            "aoColumns": [
                {"mData": "dashboardDescription"},
                {"mData": "templateDescription"},
                {"mData": "templateID"}
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

    function loadTemplate(templateID) {
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
                $("#templateID").val(templateID);
                $(".template").html(data)
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Template not found");
            }
        });
    }

    function load_widget(position,sortorder,positionDB) {
        id = sortorder;
        position1 = position;
        position2 = positionDB;
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('DashboardWidget/loadWidget'); ?>",
            data: {"position": position,"sortorder":sortorder},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#radio_list").html("");
                $.each(data, function (index, value) {
                    //$("#radio_list").append('<li><input type="radio" name="iCheck" value="'+value.widgetID+'"><label>'+ value.widgetName +'</label></li>');
                    $("#radio_list").append('<div class="col-md-4" style="margin-top: 5px;"><label class="btn btn-primary" style="padding: 1px;"><img title="' + value.widgetName + '" src="../images/'+value.widgetImage+'" alt="../images/no-image.png" id="imgchk_'+value.widgetID+'" style="height:100px; width: 100%;" class="img-thumbnail img-check imgchkbox"><input onclick="checkimg('+value.widgetID+')" type="checkbox" data-widname="' + value.widgetName + '" data-image="'+value.widgetImage+'"  id="iCheck_'+value.widgetID+'" value="'+value.widgetID+'" class="hidden widgetcheck" autocomplete="off"></label></div>');
                });
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

    function remove_widget(level,id) {
        $("#body"+id).html('');
        $("#overlay"+id).html('<button type="button" class="btn btn-primary btn-xs" onclick="load_widget('+level+','+id+',\''+position2+'\')"><i class="fa fa-plus"></i> Add</button>');
    }

    function modal_template_setup() {
        $('#modal_template_setup').modal();
    }

    function load_widget_modal() {
        $('#modal_widget').modal();
    }
</script>