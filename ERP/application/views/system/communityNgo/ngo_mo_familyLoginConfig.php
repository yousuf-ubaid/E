<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityNgo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('community_ngo_helper');
echo head_page($_POST['page_name'], false);

$com_master = fetch_comMaster_lead();
$com_area = load_region();
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$companyID = current_companyID();

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
            font-weight: normal;
            color: #5f84b1;
            text-shadow: none;
            background-color: #dcdcdc;
            border-left: 3px solid #5f84b1;
        }

        #list-main .nav-list > .active > a, .nav-list > .active > a:hover, .nav-list > .active > a:focus {
            color: #5f84b1;

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

            color: #222;
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
            border-bottom: 1px solid #e5e5e5;
        }

        #list-main .left-sidenav li {
            border-bottom: 1px solid #e5e5e5;
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
            border: 1px solid #e5e5e5;
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
            border-bottom: 1px solid #e5e5e5;
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
            <form method="post" name="searchForm" id="searchForm" class="">
            <div class="form-group col-md-3" style="margin-left:14px;">
                <div class="box-tools">
                    <div class="has-feedback">
                        <input name="logFemKey" type="text" class="form-control input-sm"
                               placeholder="<?php echo $this->lang->line('communityngo_searchLogFam');?>"
                               id="logFemKey"><!--Search Family-->
                        <span class="glyphicon glyphicon-search form-control-feedback"></span>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-2 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchedFamily()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>

            </div>

            <div class="col-md-4">

            </div>
            <div class="col-md-3">

            </div>
            </form>
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
                                                <div class="toolbar-title"><i class="fa fa-cog" aria-hidden="true"></i> <?php echo $this->lang->line('communityngo_famLoginConf');?>
                                                </div><!--Family Login Configuration-->
                                            </div>

                                            <div class="post-area">

                                                <article class="page-content">

                                                    <div class="system-settings">
                                                        <p><?php echo $this->lang->line('communityngo_famLogConfig_alert');?>.</p><!--Family Login Configuration allows you to set the login config for the families.-->


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

<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <script type="text/javascript">
        $(document).ready(function () {

                fetch_familyLog_list();


            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_mo_familyLoginConfig', '', 'Family Login');
            });

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });

        });

        $('#logFemKey').bind('input', function(){
            startFamLogSearch();
        });

        function fetch_familyLog_list(){

            var logFemKey = $('#logFemKey').val();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'logFemKey': logFemKey},
                url: "<?php echo site_url('CommunityNgo/fetch_familyLog_list'); ?>",
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

        function startFamLogSearch() {
            $('#search_cancel').removeClass('hide');
            fetch_familyLog_list();
        }

        function clearSearchedFamily() {
            $('#search_cancel').addClass('hide');
            $('#logFemKey').val('');
            fetch_familyLog_list();
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


        function redirect_famLogConfPage(cmnte, FamMasterID,LeaderID) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {cmnte: cmnte, masterID: FamMasterID,LeaderID:LeaderID},
                url: "<?php echo site_url('CommunityNgo/comFamily_logConfig'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#load_configuration_view').html(data);
                    $('#list-main li').removeClass('active');
                    $('.' + FamMasterID).addClass('active');


                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    </script>

<?php
