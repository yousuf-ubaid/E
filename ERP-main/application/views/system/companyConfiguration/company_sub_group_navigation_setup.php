<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_navigation_access');
echo head_page($title, false);

/*echo head_page('Navigation Access', false);*/

$main_grp = dropdown_subGroup();
?>
<style>
    .header {
        color: #000080;
        font-weight: bolder;
        font-size: 13px;

    }

    .subheader {
        color: black;
        font-weight: bolder;
        font-size: 13px;

    }

    .subdetails {
        /* color: #4e4e4e;*/

        font-size: 12px;
        padding-left: 10px;
    }

    .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
        padding: 4px;
    }

    .highlight {
        background-color: #FFF59D;

    }

    ul {
        list-style-type: none;
    }

    .select2-container {
        box-sizing: border-box;
        display: inline-block;
        margin: 0;
        position: relative;
        vertical-align: middle;
        width: 10% !important;
    }

</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="form-group col-sm-4 hidden">
        <label class="control-label"><?php echo $this->lang->line('config_common_main_group');?> <!--Main Group--></label>
        <div class="">
            <?php echo form_dropdown('companyGroupID', $main_grp, '', 'class="form-control select2" style="" onchange="load_sub_group()" id="companyGroupID"'); ?>
        </div>
    </div>
    <div class="form-group col-sm-4" id="loaduserGroupdropdown">
    </div>
    <!--<div class="form-group col-sm-4" id="">
        <div class="">
            <button type="button" style="margin-top: 28px;" class="btn btn-primary btn-xs pull-left"
                    onclick="loadform()">Load
            </button>
        </div>
    </div>-->
    <hr>
   <div class="col-sm-12">
       <div class="form-group" id="div_reload">

       </div>
   </div>
</div>


<?php

echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/companyConfiguration/company_sub_group_navigation_setup','','Navigation Access');
        });
        $('#userGroupID').select2();

        loadform($('#userGroupID').val());
       load_sub_group()
        $('input[type=checkbox]').click(function () {
            if (this.checked) {
                $(this).parents('li').children('input[type=checkbox]').prop('checked', true);
            }
            $(this).parent().find('input[type=checkbox]').prop('checked', this.checked);
        });

    });

    function load_sub_group() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupID: ''},
            url: "<?php echo site_url('Group_management/load_sub_group'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loaduserGroupdropdown').html(data);
                loadform();

            }, error: function () {

            }
        });

    }

    function loadform() {
        var userGroupID=$('#subGroupID').val();
        var mainGroupID=$('#companyGroupID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {userGroupID: userGroupID,mainGroupID: mainGroupID},
            url: "<?php echo site_url('Group_management/load_navigation_subgroup_setup'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_reload').html(data);
                stopLoad();

            }, error: function () {

            }
        });

    }

    function saveNavigationgroupSetup() {
        var navigationID = [];
        $('.nVal:checked').each(function (i, e) {
            navigationID.push(e.value);
        });
        navigationID = navigationID.join(',');

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {navigationID: navigationID, compaySubGroupID: $('#subGroupID').val()},
            url: "<?php echo site_url('Group_management/saveNavigationgroupSetup'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);

                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }




</script>