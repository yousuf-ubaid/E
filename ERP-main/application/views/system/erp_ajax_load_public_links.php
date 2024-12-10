<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);


?>
<style>
    .boederclr{
        outline: -webkit-focus-ring-color auto 5px;
        outline-color: -webkit-focus-ring-color;
        outline-style: auto;
        outline-width: 5px;
    }

</style>
<div class="box box-success">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_shortcut_links');?><!--Shortcut Links--></h4>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                    class="fa fa-minus"></i>
            </button>
            <button type="button" onclick="openPublicLinkModal<?php echo $userDashboardID; ?>()" title="Add Links" class="btn btn-box-tool"><i
                    class="fa fa-plus-square-o"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: block;width: 100%">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_public_2" onclick="setshortcuttab(2)" data-toggle="tab" aria-expanded="false">Reports</a></li>
                <li class=""><a href="#tab_public_1" onclick="setshortcuttab(1)" data-toggle="tab" aria-expanded="true">Documents</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane " id="tab_public_1">
                    <ul class="todo-list">

                        <?php
                        if (!empty($publiclist)) {
                            $report = '';
                            foreach ($publiclist as $val) {
                                if($val['type']==0) {
                                    ?>
                                    <li id="pbLink_<?php echo $val['linkID'] ?>_<?php echo $userDashboardID; ?>"
                                        style="padding: 6px;">
                  <span class="">
                    <i class="fa fa-link"></i>
                  </span>
                                        <?php
                                        if($val['UserGroupSetupID']==0){
                                            ?>
                                            <span class="text" style="    text-decoration: line-through"><a onclick="link_error_message()" style="cursor: pointer;" title="<?php echo $val['title'] ?>" target=""><?php echo $val['description'] ?></a></span>
                                            <?php
                                        }else{
                                            ?>
                                            <span class="text"><a onclick="fetchPage('<?php echo $val['hyperlink'] ?>','','<?php echo $val['title'] ?>')" style="cursor: pointer;" title="<?php echo $val['title'] ?>" target=""><?php echo $val['description'] ?></a></span>
                                            <?php
                                        }
                                        ?>

                                        <span class="text" style="color:red;"><?php echo $report ?></span>

                                        <div class="tools">
                                            <!--<i class="fa fa-edit"></i>-->
                                            <i class="fa fa-trash-o"
                                               onclick="deletePublicLink<?php echo $userDashboardID; ?>(<?php echo $val['linkID'] ?>,<?php echo $userDashboardID; ?>)"></i>
                                        </div>
                                    </li>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </ul>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane active" id="tab_public_2">
                    <ul class="todo-list">

                        <?php
                        if (!empty($publiclist)) {
                            $report = '';
                            foreach ($publiclist as $val) {
                                if($val['type']==1){
                                ?>
                                <li id="pbLink_<?php echo $val['linkID'] ?>_<?php echo $userDashboardID; ?>" style="padding: 6px;">
                  <span class="">
                    <i class="fa fa-link"></i>
                  </span>
                                    <?php
                                    if($val['UserGroupSetupID']==0){
                                        ?>
                                        <span class="text" style="text-decoration: line-through"><a onclick="link_error_message()" style="cursor: pointer;" title="<?php echo $val['title'] ?>" target=""><?php echo $val['description'] ?></a></span>
                                        <?php
                                    }else{
                                        ?>
                                        <span class="text"><a onclick="fetchPage('<?php echo $val['hyperlink'] ?>','<?php echo $val['pageID'] ?>','<?php echo $val['title'] ?>')" style="cursor: pointer;" title="<?php echo $val['title'] ?>" target=""><?php echo $val['description'] ?></a></span>
                                        <?php
                                    }
                                    ?>
                                    <span class="text" style="color:red;"><?php echo $report ?></span>

                                    <div class="tools">
                                        <!--<i class="fa fa-edit"></i>-->
                                        <i class="fa fa-trash-o" onclick="deletePublicLink<?php echo $userDashboardID; ?>(<?php echo $val['linkID'] ?>,<?php echo $userDashboardID; ?>)"></i>
                                    </div>
                                </li>
                                <?php
                                }
                            }
                        }
                        ?>
                    </ul>
                </div>
                <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
        </div>

    </div>
    <div class="overlay" id="overlay9<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
    <!-- /.box-body -->
</div>

<div class="modal fade" id="addPublicLinkModal<?php echo $userDashboardID; ?>" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" onclick="calnce_link()">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('dashboard_add_link');?><!--Add Link--><span id="rpttypelink"></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <?php echo form_open('', 'role="form" id="public_link_form'.$userDashboardID.'"'); ?>
                <?php
                $colorcount=0;
                foreach ($publiclinks as $val) {
                    $backcolor='white;';
                    if($colorcount%2==0){
                        $backcolor='#d3e7ff;';
                    }
                    $reporttyp='';
                    $reporttypid='doc';
                    if($val['type']==1){
                        $reporttyp='( Report )';
                        $reporttypid='rpt';
                    }
                    $id= $val['linkID'];
                    $desc=$val['linkID'];

                    if(!empty($val['dtldesc'])){
                        $descp=$val['dtldesc'];
                    }else{
                        $descp= $val['masterdesc'];
                    }
                    if($val['linkID'] == $val['linkMasterID']){
                         //$dashboardLinkId=$val['dashboardLinkId'];
                        echo '
           <div class="row ' . $reporttypid . ' " style="background-color: ' . $backcolor . ' padding: 2px;">
          <div class="col-sm-12">
          <label>
              <input type="checkbox" value="' . $id . '" name="widgetCheck[]"  class="minimal" checked >
              ' . $val['masterdesc'] . '
            </label>
            <input type="text" value="' . $descp . '" name="description[]" onchange="updateLinkDescription(' . $id . ')" id="description_' . $id . '" class="form-group pull-right" >
            </div></div>';
                    }
                    else{
                        echo '
          <div class="row ' . $reporttypid . ' " style="background-color: ' . $backcolor . ' padding: 2px;">
          <div class="col-sm-12">
          <label>
              <input type="checkbox" value="' . $id . '" name="widgetCheck[]" class="minimal" >
              ' . $val['masterdesc'] . '
            </label>
            <input type="text" value="' . $descp . '" name="description[]" onchange="updateLinkDescription(' . $id . ')" id="description_' . $id . '" class="form-group pull-right" >
        </div></div>';
                    }
                    $colorcount++;
                }
                ?>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" onclick="calnce_link()"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="save_Public_link<?php echo $userDashboardID; ?>()" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var shorcutlnktabid=2;
    setshortcuttab(2);
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue boederclr',
        radioClass: 'iradio_minimal-blue'
    });
    function openPublicLinkModal<?php echo $userDashboardID; ?>() {
        //$('#public_link_form')[0].reset();
        if(shorcutlnktabid==1){
            $('.doc').removeClass('hidden');
            $('.rpt').addClass('hidden');
        }else{
            $('.doc').addClass('hidden');
            $('.rpt').removeClass('hidden');
        }
        $('#public_link_form<?php echo $userDashboardID; ?>').bootstrapValidator('resetForm', true);
        $('#addPublicLinkModal<?php echo $userDashboardID; ?>').modal("show");
    }

    $('.minimal').on('ifChecked', function (event) {
        updateLink(this.value, 1);

    });

    $('.minimal').on('ifUnchecked', function (event) {
        updateLink(this.value, 0);

    });

    function save_Public_link<?php echo $userDashboardID; ?>() {
        var data = $('#public_link_form<?php echo $userDashboardID; ?>').serializeArray();
        var selected = [];
        var desc = [];
        $('.minimal:checked').each(function () {
                selected.push($(this).val());
                desc.push($('#description_' + $(this).val()).val());
        });

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Finance_dashboard/save_public_link'); ?>",
            //data: data,
            data: {'widgetCheck': selected, 'description': desc},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#addPublicLinkModal<?php echo $userDashboardID; ?>').modal('hide');
                    myAlert('s', 'Message: ' + data[1]);
                    location.reload();
                } else if (data[0] == 'e') {
                    myAlert('e', 'Message: ' + data[1]);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Select Widget");
            }
        });

    }

    function deletePublicLink<?php echo $userDashboardID; ?>(id,userDashboardID) {
        if (id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this Record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "<?php echo site_url('Finance_dashboard/deletePrivateLink'); ?>",
                        data: {linkID: id},
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data[0] == 's') {
                                $('#pbLink_' + id+'_'+userDashboardID).hide();
                                myAlert('s', 'Message: ' + data[1]);
                            } else if (data[0] == 'e') {
                                myAlert('e', 'Message: ' + data[1]);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', 'Message: ' + "Select Widget");
                        }
                    });
                });
        };
    }


    function link_error_message(){
        swal(
            'Warning!',
            'You do not have permission to access this link. Please contact system administrator.',
            'warning'
        )
    }

    function setshortcuttab(id){
        shorcutlnktabid=id;
        if(id==1){
            $('#rpttypelink').html(' - Documents');
        }else{
            $('#rpttypelink').html(' - Reports');
        }
    }

    function updateLink(id,val){
        var description=$('#description_'+id).val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Finance_dashboard/updatePBLink'); ?>",
            data: {linkID: id,description: description,valu: val},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Select Widget");
            }
        });
    }

    function updateLinkDescription(id){
        var description=$('#description_'+id).val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Finance_dashboard/updateLinkDescription'); ?>",
            data: {linkID: id,description: description},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Select Widget");
            }
        });
    }

    function calnce_link(){
        location.reload();
    }
</script>
