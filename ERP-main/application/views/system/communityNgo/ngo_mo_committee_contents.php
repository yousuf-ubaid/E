<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityNgo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('community_ngo_helper');
echo head_page($_POST['page_name'], false);

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

$com_master = fetch_comMaster_lead();
$com_area = load_region();
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$companyID = current_companyID();

 $query= $this->db->query("SELECT CommitteeAreawiseID,CommitteeID,CommitteeAreawiseDes FROM srp_erp_ngo_com_committeeareawise WHERE companyID='".$companyID."' AND CommitteeID ='".$_POST['page_id']."'");
  $subCommittees = $query->result();
?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/commtNgo_style.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/ngo_web_style.css'); ?>">

    <style>
        #list-main .left-sidenav > .active > a {
            position: relative;
            z-index: 2;
            border-right: 0 !important;
        }

        #list-main .nav-list > .active > a, .nav-list > .active > a:hover {
            padding-left: 12px;
            font-weight: bold;
            color: #36b453;
            text-shadow: none;
            background-color: #dcdcdc;
            border-left: 5px solid #36b453;
        }

        #list-main .nav-list > .active > a, .nav-list > .active > a:hover, .nav-list > .active > a:focus {
            color: #36b453;

            background-color: rgba(239, 239, 239, 0.75);
        }

        #list-main .left-sidenav > li > a {
            display: block;
            width: 176px \9;
            margin: 0;
            padding: 4px 7px 4px 15px;
        !important;
            padding: 6px;
            font-size: 13px;

        }

        #list-main .nav-list > li > a {

            color: #5f84b1;
            font-weight: bolder;
            background-color: rgba(224, 224, 224, 0.75);

        }

        #list-main .nav-list > li > a, .nav-list .nav-header {

            text-shadow: 0 1px 0 rgba(255, 255, 255, .5);
        }

        #list-main .nav > li > a {
            display: block;
        }

        #list-main a, a:hover, a:active, a:focus {
            outline: 0;
        }

        #list-main .left-sidenav > .active {
            border-right: none;
            background-color: #f5f5f5;
        }

        #list-main.left-sidenav li {
            border-bottom: 1px solid #b4b4b4;
        }

        #list-main .left-sidenav li {
            border-bottom: 1px solid #b4b4b4;
        }

        #list-main li {
            line-height: 20px;
        }

        #list-main .nav-list {
            padding-right: 0px;
            padding-left: 0px;
        }

        #list-main a {
            text-decoration: none;
        }

        #list-main .left-sidenav .icon-chevron-right {
            float: right;
            margin-top: 2px;
            margin-left: -6px;
            opacity: .25;
            padding-right: 4px;

        }

        .flex {
            display: flex;
        }

        #list-main .sidebar-left {
            float: left;
        }

        article, aside, details, figcaption, figure, footer, header, hgroup, nav, section {
            display: block;
        }

        #list-main .left-sidenav {
            width: 200px;
            padding: 0;
            background-color: #fff;
            border-radius: 3px;
            -webkit-border-radius: 3px;
            border: 1px solid #b4b4b4;
        }

        #list-main.nav-list {
            padding-right: 15px;
            padding-left: 15px;
            margin-bottom: 0;
        }

        #list-main .nav {
            margin-bottom: 20px;
            margin-left: 0;
            list-style: none;
        }

        #list-main ul, ol {
            padding: 0;
            margin: 0 0 10px 25px;
        }

        #list-main .left-sidenav li {
            border-bottom: 1px solid #b4b4b4;
        }

        form {
            margin: 0 0 20px;
        }

        fieldset {
            padding: 0;
            margin: 0;
            border: 0;
        }

        section {
            padding-top: 0;
        }

        article, aside, details, figcaption, figure, footer, header, hgroup, nav, section {
            display: block;
        }

        .past-posts .posts-holder {
            padding: 0 0 10px 4px;
            margin-right: 10px;
        }

        .past-info {
            background: #fff;
            border-radius: 3px;
            -webkit-border-radius: 3px;
            padding: 0 0 8px 10px;
            margin-left: 2px;
        }

        .title-icon {
            margin-right: 8px;
            vertical-align: text-bottom;
        }

        article, aside, details, figcaption, figure, footer, header, hgroup, nav, section {
            display: block;
        }

        .system-settings-item {
            margin-top: 20px;
        }

        .fa-chevron-right {
            color: rgba(149, 149, 149, 0.75);
            margin-top: 4px;
        }

        .system-settings-item {
            margin-top: 20px;
        }

        .system-settings-item img {
            vertical-align: middle;
            padding-right: 5px;
            margin: 2px;
        }

        .system-settings-item a {
            padding: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .past-info #toolbar, .past-info .toolbar {
            background: #f8f8f8;
            font-size: 13px;
            font-weight: bold;
            color: #000;
            border-radius: 3px 3px 0 0;
            -webkit-border-radius: 3px 3px 0 0;
            border: #dcdcdc solid 1px;
            padding: 5px 15px 12px 10px;
            line-height: 2;
            height: 29px;
        }

        .system-settings-item .fa {
            text-decoration: none;
            color: black;
            font-size: 16px;
            padding-right: 5px;
        }

        .system-settings-item .fa {
            text-decoration: none;
            color: black;
            font-size: 16px;
            padding-right: 5px;
        }

        .width100p {
            width: 100%;
        }

        .user-table {
            width: 100%;
        }

        .bottom10 {
            margin-bottom: 10px !important;
        }

        .btn-toolbar {
            margin-top: -2px;
        }

        table {
            max-width: 100%;
            background-color: transparent;
            border-collapse: collapse;
            border-spacing: 0;
        }

    </style>
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url('plugins/bootstrapcolorpicker/dist/css/bootstrap-colorpicker.css'); ?>">
    <script src="<?php echo base_url('plugins/bootstrapcolorpicker/dist/js/bootstrap-colorpicker.js'); ?>"></script>
<div id="div1">
    <div id="filter-panel" class="collapse filter-panel">
    </div>
    <div class="row">
        <div class="col-md-6">
        </div>
        <div class="col-md-2 text-right">

        </div>
        <div class="col-md-4">
            <button type="button" class="btn btn-primary btn-sm CA_Alter_btn" onclick="openAddSubCom_modal()" style=""><i class="fa fa-plus-square"></i>&nbsp; <?php echo $this->lang->line('communityngo_addSubCommittee');?><!--Add--> </button>

            <a href="#" type="button" style="margin-left: 2px;" class="btn btn-danger btn-sm CA_Print_Excel_btn" onclick="generate_committeePdf()">
                <i class="fa fa-file-pdf-o"></i> PDF
            </a>
            <a href="#" type="button" style="margin-left: 2px;" class="btn btn-success btn-sm CA_Print_Excel_btn" onclick="excelCommittee_Export()">
                <i class="fa fa-file-excel-o"></i> Excel
            </a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div id="list-main" class="top15 ">
                <aside class="sidebar-left col-md-3 " style="width: 21%;">
                    <div id="Committees_list">
                    </div>

                </aside>

                <div id="load_configuration_view" class="col-md-9" style="width: 79%;">
                    <form action="#" class="form-box">
                        <fieldset>
                            <section class="past-posts">
                                <div class="posts-holder">
                                    <div class="past-info">

                                        <div id="toolbar">
                                            <div class="toolbar-title"><i class="fa fa-cog" aria-hidden="true"></i> <?php echo $this->lang->line('communityngo_CommitMemAssign');?>
                                            </div><!--Committee Member Assign-->
                                        </div>

                                        <div class="post-area">

                                            <article class="page-content">

                                                <div class="system-settings">
                                                    <p><?php echo $this->lang->line('communityngo_CommittSetup_alert');?>.</p><!--Member assigning allows you to change system wide settings for all members-->


                                                    <div class="system-settings-item">

                                                    </div>

                                                </div>

                                            </article>

                                        </div>
                                    </div>
                                </div>
                            </section>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <form id="cmtePdfForm">

        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

        <input type="text" name="CommitteeID1" id="CommitteeID1" value="" style="display:none ;">

    </form>

    <div class="modal fade" id="add_subCmtModel" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeAddCommit();"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('communityngo_addSubCommittee');?><!--Add Sub Committee--></h4>
                </div>
                <form class="form-horizontal" id="add-subCmt_form" >
                    <div class="row modal-body">
                        <div class="col-md-12">
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_Description');?><!--Description--> <?php required_mark(); ?></label>
                                <input type="text" step="any" class="form-control" id="subCmtDesc"
                                       name="subCmtDesc" value="">
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label for="areaSubCmnt"><?php echo $this->lang->line('communityngo_region');?><!--Area / Mahalla-->  <?php required_mark(); ?></label>
                            <?php echo form_dropdown('areaSubCmnt', $com_area, '', 'class="form-control select2" id="areaSubCmnt" onchange="get_comMaserHd();" required '); ?>
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_CommitteeHead');?><!--Committee Head--> <?php required_mark(); ?></label>
                                <select onchange="" id="subCmtHead" class="form-control select2"
                                        name="subCmtHead">
                                    <option>Select Committee Head</option>
                                    <option></option>

                                </select>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('CommunityNgo_famAddedDate'); ?> <?php required_mark(); ?></label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input onchange="$('#subCmtAddedDate').val(this.value);" type="text" name="subCmtAddedDate"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="subCmtAddedDate" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_ExpiryDate');?><!--Expiry Date--></label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input onchange="$('#subCmtExrDate').val(this.value);" type="text" name="subCmtExrDate"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" id="subCmtExrDate" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label>Is Active</label>
                                <br>
                                <input type="checkbox" name="isRequired" class="requiredCheckbox" value="1" checked>

                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-12" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_Remarks');?><!--Remark--></label>

                                <input type="text" step="any" class="form-control" id="subCmtRemark" name="subCmtRemark" value="">

                            </div>
                        </div>
                    </div>
                    <input type="text" name="CommitteeID" id="CommitteeID" value="" style="display:none ;">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_subCommittee()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" onclick="closeAddCommit();"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_subCmtModel" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeEditSubCommit();"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('communityngo_editSubCommittees');?><!--Edit Sub Committee--></h4>
                </div>
                <form class="form-horizontal" id="edit-subCmt_form" >
                    <div class="row modal-body">
                        <div class="col-md-12">
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_Description');?><!--Description--> <?php required_mark(); ?></label>
                                <input type="text" step="any" class="form-control" id="editsubCmtDesc"
                                       name="editsubCmtDesc" value="">
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label for="editareaSubCmnt">Area <?php required_mark(); ?></label>
                                <?php echo form_dropdown('editareaSubCmnt', $com_area, '', 'class="form-control select2" id="editareaSubCmnt" onchange="get_editComMaserHd();" required '); ?>
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('communityngo_CommitteeHead');?><!--Committee Head--> <?php required_mark(); ?></label>
                                <select onchange="" id="editsubCmtHead" class="form-control select2"
                                        name="editsubCmtHead">
                                    <option>Select Committee Head</option>
                                    <option></option>
                                </select>

                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label><?php echo $this->lang->line('CommunityNgo_famAddedDate'); ?> <?php required_mark(); ?></label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input onchange="$('#editCmtAddedDate').val(this.value);" type="text" name="editCmtAddedDate"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="editCmtAddedDate" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_ExpiryDate');?><!--Expiry Date--></label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input onchange="$('#editCmtExrDate').val(this.value);" type="text" name="editCmtExrDate"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" id="editCmtExrDate" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group col-sm-4" style="margin-right: 3px;">
                                <label>Is Active</label>
                                <br>
                                <input type="checkbox" id="isVowel2b"  name="isVowel" onchange="isitvowel(this);" checked>
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <input class="form-control" type="number" name="vowel" id="vowel2" value="1"
                                           style="display: none ;">
                                </div>

                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-sm-12" style="margin-right: 3px;">
                                <label for=""><?php echo $this->lang->line('communityngo_Remarks');?><!--Remark--></label>

                                <input type="text" step="any" class="form-control" id="editCmtRemark" name="editCmtRemark" value="">

                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="hidden-id" name="hidden-id" value="0">
                    <input type="text" name="editCommitteeID" id="editCommitteeID" value="" style="display:none ;">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm CA_Submit_btn" onclick="save_editSubCommittee()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" onclick="closeEditSubCommit();"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <script type="text/javascript">
        $(document).ready(function () {

            //control_staff_access(0, 'system/communityNgo/ngo_mo_committeesMaster', 0);

            cmt_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (cmt_id) {
                CommitteeID = cmt_id;
             fetch_subCommittees_list(CommitteeID);
            }
            document.getElementById('CommitteeID').value = CommitteeID;
            document.getElementById('editCommitteeID').value = CommitteeID;
            document.getElementById('CommitteeID1').value = CommitteeID;

            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_mo_committeesMaster', '', 'Committees');
            });

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });

        });

        function fetch_subCommittees_list(CommitteeID){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {CommitteeID: CommitteeID},
                url: "<?php echo site_url('CommunityNgo/fetch_subCommittees_list'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $("#Committees_list").html(data);

                    stopLoad();

                }, error: function (jqXHR, textStatus, errorThrown) {
                    //$("#familydetails_tab").html('<div class="alert alert-danger">An Error Occurred! Please Try Again.<br/><strong>Error Message: </strong>' + errorThrown + '</div>');
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }

        function openAddSubCom_modal(){

            $('.saveInputs').val('');
            $('.saveInputs').change();
            $('#add_subCmtModel').modal({backdrop: "static"});
        }

        function get_comMaserHd() {

            var areaSubCmnt = document.getElementById('areaSubCmnt').value;
            if (areaSubCmnt == "" || areaSubCmnt == null) {
            } else {

                $.ajax({
                    type: "POST",
                    url: "CommunityNgo/get_comMaserHd",
                    data: {'areaSubCmnt': areaSubCmnt},
                    success: function (data) {

                        $('#subCmtHead').html(data);
                    }
                });
            }
        }

        function save_subCommittee(){

            var postData = $('#add-subCmt_form').serialize();

            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/saveCommittee_sub'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){

                        $('#subCmtDesc').val('').change();
                        $('#areaSubCmnt').val('').change();
                        $('#subCmtHead').val('').change();
                        $('#subCmtAddedDate').val(<?php echo $current_date; ?>).change();
                        $('#subCmtExrDate').val('').change();
                        $('#subCmtRemark').val('').change();

                        $('#add_subCmtModel').modal('hide');
                        fetch_subCommittees_list(CommitteeID);
                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })

        }

        function closeAddCommit() {

            $('#subCmtDesc').val('').change();
            $('#areaSubCmnt').val('').change();
            $('#subCmtHead').val('').change();
            $('#subCmtAddedDate').val(<?php echo $current_date; ?>).change();
            $('#subCmtExrDate').val('').change();
            $('#subCmtRemark').val('').change();

            $('#add_subCmtModel').modal({backdrop: "static"});

        }

        function isitvowel(y){
            var yid= y.id;
            var vid= yid.replace(/[^\d.]/g, '');
            //  alert(vid);
            var f=document.getElementById('isVowel'+vid+'b').checked;

            if(f==true) {
                document.getElementById('vowel' + vid).value = 1;
            }if(f==false){
                document.getElementById('vowel' + vid).value = 0;
            }
        }

        function edit_subCommittee(CommitteeAreawiseID){

            $('#hidden-id').val( $.trim(CommitteeAreawiseID) );

            $.ajax({

                async : true,
                type: 'POST',
                url :"<?php echo site_url('CommunityNgo/fetchEdit_subComt'); ?>",
                data: {'id': CommitteeAreawiseID},
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,

                success: function (data) {
                    $('#edit_subCmtModel').modal('show');

                    $("#editsubCmtDesc" ).val(data.editsubCmtDesc);

                    $('#editareaSubCmnt').val(data.editareaSubCmnt).change();
                    $('#editsubCmtHead').val(data.editsubCmtHead).change();
                    $("#editCmtAddedDate" ).val(data.editCmtAddedDate);
                    $("#editCmtExrDate" ).val(data.editCmtExrDate);
                    $("#editCmtRemark" ).val(data.editCmtRemark);

                    if((data.vowel) == '1'){
                        document.getElementById('isVowel2b').checked=true;
                        document.getElementById('vowel2').value='1';
                    }else{
                        document.getElementById('isVowel2b').checked=false;
                        document.getElementById('vowel2').value='0';
                    }

                }
            });

        }

        function get_editComMaserHd() {

            var areaEditCmnt = document.getElementById('editareaSubCmnt').value;
            var editIdCmt = document.getElementById('hidden-id').value;
            if (areaEditCmnt == "" || areaEditCmnt == null) {
            } else {

                $.ajax({
                    type: "POST",
                    url: "CommunityNgo/get_editComMaserHd",
                    data: {'editIdCmt':editIdCmt,'areaEditCmt': areaEditCmnt},
                    success: function (data) {

                        $('#editsubCmtHead').html(data);
                    }
                });
            }
        }

        function save_editSubCommittee(){

            var postData = $('#edit-subCmt_form').serialize();
            $.ajax({
                type: 'post',
                url: '<?php echo site_url('CommunityNgo/edit_subComtSave'); ?>',
                data: postData,
                dataType: 'json',
                beforeSend: function () {
                    startLoad();
                },
                success :function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);


                    if(data[0] == 's'){
                        $('#edit_subCmtModel').modal('hide');

                        fetch_subCommittees_list(($('#editCommitteeID').val()));

                        redirect_cmtMemPage(1, ($('#hidden-id').val()),($('#editCommitteeID').val()));

                        //closeEditSubCommit();
                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            })

        }

        function closeEditSubCommit() {

            $('#editsubCmtDesc').val('').change();
            $('#editareaSubCmnt').val('').change();
            $('#editsubCmtHead').val('').change();
            $('#editCmtAddedDate').val(<?php echo $current_date; ?>).change();
            $('#editCmtExrDate').val('').change();
            $('#editCmtRemark').val('').change();

            $('#edit_subCmtModel').modal({backdrop: "static"});

        }
        
        function redirect_cmtMemPage(cmnte, CommitteeAreawiseID,CommitteeID) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {cmnte: cmnte, masterID: CommitteeAreawiseID,CommittID:CommitteeID},
                url: "<?php echo site_url('CommunityNgo/comitt_members'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#load_configuration_view').html(data);
                    $('#list-main li').removeClass('active');
                    $('.' + CommitteeAreawiseID).addClass('active');


                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function generate_committeePdf() {

            var form = document.getElementById('cmtePdfForm');
            form.target = '_blank';
            form.method = 'post';
            form.post = $('#cmtePdfForm').serializeArray();
            form.action = '<?php echo site_url('CommunityNgo/get_committeeStatus_pdf'); ?>';
            form.submit();

        }

        function excelCommittee_Export() {
            var form = document.getElementById('cmtePdfForm');
            form.target = '_blank';
            form.method = 'post';
            form.post = $('#searchForm').serializeArray();
            form.action = '<?php echo site_url('CommunityNgo/excel_committeeExport'); ?>';
            form.submit();
        }

    </script>
<?php
