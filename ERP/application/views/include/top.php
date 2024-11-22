<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('navigation_menu', $primaryLanguage);
$this->load->helper('usermanual_helper');
$companyInfo = get_companyInfo();
$productID = $companyInfo['productID'] ?? null;
$emplangid = $this->common_data['emplangid'];
$companyID = current_companyID();
$curentuser = current_userID();
$locationwisecodegenerate = getPolicyValues('LDG', 'All');
$thirdpartapp = getPolicyValues('TPA','ALL');
$isGroupCom = getusergroupcomapny();
$usermanual = all_modules_array();
$currentcompanycountry=$this->common_data['company_data']['company_country'];
$supportContactInfo=fetch_support_contact_info();

$companyID = current_companyID();
$companyType = $this->session->userdata("companyType");
$isGroupUser = $this->session->userdata("isGroupUser");
$companyTp = $this->session->userdata("companyType");

if ($isGroupUser == 1) {
    $companyType = 1;
}

$this->load->service('NavigationService');
$navigationData = $this->NavigationService->getNavigationHeaderLevelOne(
    current_companyID(),
    current_userID(),
    $this->common_data['userType'],
    $this->session->userdata("companyType"),
    $this->session->userdata("isGroupUser")
);

if (!$this->session->userdata("navigationMasterId")) {
    if (count($navigationData) === 1) {
        $this->session->set_userdata('navigationMasterId', $navigationData[0]['navigationMenuID']);
    }
    else {
        $this->session->set_userdata('navigationMasterId', 329);
    }
}

function handleTopLanguageNotExist($languageRow, $translation)
{
    if (!empty(trim($translation))) {
        return $translation;
    } else {
        return $languageRow;
    }
}

?>

    <style>

        .headStyle2 {
            color: #ffffff !important;
        }
        .headStyle1 {
            color: #fff7f7 !important;
        }

        .headStyle2:hover {
            text-decoration: underline !important;
            color: #3c8dbc !important;;
        }

        .clsGold {
            color:goldenrod;
        }
        .clswhite {
            color:#ffffff;
        }

        .head1 {
            font-size: 14px;
            font-weight: 700;
        }


        /*thirdparty start*/


        .slick-slide {
            margin: 0px 20px;
        }

        .slick-slide img {
            width: 20%;
        }

        .slick-slider
        {
            position: relative;
            display: block;
            box-sizing: border-box;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
            -khtml-user-select: none;
            -ms-touch-action: pan-y;
            touch-action: pan-y;
            -webkit-tap-highlight-color: transparent;
        }

        .slick-list
        {
            position: relative;
            display: block;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        .slick-list:focus
        {
            outline: none;
        }
        .slick-list.dragging
        {
            cursor: pointer;
            cursor: hand;
        }

        .slick-slider .slick-track,
        .slick-slider .slick-list
        {
            -webkit-transform: translate3d(0, 0, 0);
            -moz-transform: translate3d(0, 0, 0);
            -ms-transform: translate3d(0, 0, 0);
            -o-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
        }

        .slick-track
        {
            position: relative;
            top: 0;
            left: 0;
            display: block;
        }
        .slick-track:before,
        .slick-track:after
        {
            display: table;
            content: '';
        }
        .slick-track:after
        {
            clear: both;
        }
        .slick-loading .slick-track
        {
            visibility: hidden;
        }

        .slick-slide
        {
            display: none;
            float: left;
            height: 100%;
            min-height: 1px;
        }
        [dir='rtl'] .slick-slide
        {
            float: right;
        }
        .slick-slide img
        {
            display: block;
        }
        .slick-slide.slick-loading img
        {
            display: none;
        }
        .slick-slide.dragging img
        {
            pointer-events: none;
        }
        .slick-initialized .slick-slide
        {
            display: block;
        }
        .slick-loading .slick-slide
        {
            visibility: hidden;
        }
        .slick-vertical .slick-slide
        {
            display: block;
            height: auto;
            border: 1px solid transparent;
        }
        .slick-arrow.slick-hidden {
            display: none;
        }


        .applicationsusermanual {
            line-height: 0;
            list-style-type: none;
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: transparent;
        }

        .applicationsusermanual li {
            height: 147px;
            margin: 0 8px 8px 0;
            width: 147px;
            background: none repeat scroll 0 0 #FFFFFF;
            display: inline-block;
            border-radius: 15px;
        }

        .applicationsusermanual li a {
            color: #000;
            display: inline-block;
            height: 100%;
            position: relative;
            width: 100%;
            border-radius: 15px;
        }
        .applicationsusermanual li a:hover {
            color: #000;
            display: inline-block;
            height: 100%;
            position: relative;
            width: 100%;
            box-shadow: 0px 0px 20px 0px rgb(76 87 125 / 10%) !important;
        }

        .applicationsusermanual li a span {
            bottom: 20px;
            display: inline-block;
            font-size: 14px;
            position: absolute;
            text-align: center;
            text-transform: uppercase;
            width: 100%;
        }
        .fontcssusermanual {
            font-size: 48px;
            margin-top: 40px;
            margin-left: 50px;
        }

        .boxnameusermanual {

            text-align: center;
            margin-top: 18px;
            padding: 0px 0px 10px 0;
        }
        .listname2usermanual {
            background-color: #696CFF;
            margin: 5px;
            padding: 10px;
            color: white;
            margin-top: 12px;
            font-size: 12px;
            text-align: center;
            margin: 2px 10px;
            border-radius: 5px;
        }
        .fw-600{
            font-weight:600;
        }
        .um img{
            width: 64px;
            margin: auto;
            text-align: center;
            display: flex;
            padding-top: 18px;
        }

        .mr-0{
            margin-right: 0 !important;
        }
        .mr-5{
            margin-right: 5px !important;
        }
        .noLink{
            background-color: #eee;
        }
        .um-box-div{
            width: 150px;
            height: 150px;
            float: left;
            display: inline-block;
            margin: 0.2em;
            position: relative;
            text-align: center;
        }
        .t_video_body{
            background-color:#e5e5e5;
            padding: 10px 10px;
            border-radius:10px;
            text-align: center;
            min-height: 125px;
            display: grid;
            justify-content: center;
            align-items: center;
        }
        .t_video_body:hover i{
            color:#696CFF;
        }
        .t_video_body .t_video_title a h3{
            font-size: 14px;
            line-height: 16px;
            color: #4c4c4c;
            text-align: center;
            padding: 0;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        .t_video_body i{
            font-size: 44px;
            -webkit-transition: all 0.3s ease-in-out;
            -o-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
        }
        .t_video_body i:hover{
            font-size: 44px;
            color:#696CFF;
        }
        /*thirdparty end*/


    </style>

    <div class="wrapper">
    <header class="main-header main-header-mobile">

        <!-- Logo -->

        <a href="<?php echo site_url('dashboard'); ?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><?php echo '<img style="max-height:30px;"  src="' . base_url() . 'images/' . LOGO_SMALL . '"/>'; ?></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg">
                <?php
                    echo '<img style="max-height:30px;"  src="' . base_url() . 'images/' . LOGO . '"/>';
                ?>
            </span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <div id=""></div>
            <!-- Sidebar toggle button-->

            <?php
            if ($extra == 'sidebar-mini') {
                echo
                '<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" onclick="set_navbar_cookie()">
                <span class="sr-only">Toggle navigation</span>
            </a>';
            }

            $query = "SELECT
	`primarylanguageemp`.`description` AS `language`,
	CASE 
	    WHEN primarylanguageemp.description = \"Arabic\" THEN \"Arab League\" 
	    WHEN primarylanguageemp.description = \"English\" THEN \"United States of America (USA)\" 
	    WHEN primarylanguageemp.description = \"French\" THEN \"France\" 
	    END languageview
FROM
	srp_employeesdetails
	INNER JOIN srp_erp_lang_languages AS primarylanguageemp ON primarylanguageemp.languageID = srp_employeesdetails.languageID
WHERE
	EIdNo = $curentuser ";

            $result_employee = $this->db->query($query)->row_array();

            if (!empty($result_employee)) {
                $language = $result_employee['languageview'];
            } else {

                $q = "SELECT primarylang.description AS LANGUAGE,
CASE
    WHEN primarylang.description = \"Arabic\" THEN \"Arab League\" 
    WHEN primarylang.description = \"English\" THEN \"United States of America (USA)\" 
    WHEN primarylang.description = \"French\" THEN \"France\" 
ELSE
	\"\"
END
languageprimary

FROM
	srp_erp_lang_companylanguages
	INNER JOIN srp_erp_lang_languages primarylang ON primarylang.languageID = srp_erp_lang_companylanguages.primaryLanguageID
WHERE
companyID = $companyID ";
                $result = $this->db->query($q)->row_array();
                if (!empty($result)) {
                    $language = $result['languageprimary'];
                } else {
                    $language = 'United States of America (USA)';
                }
            }

            $location = null;
            $companyID = current_companyID();
            $currentuserid = current_userID();

            $locationassign = $this->db->query("SELECT emp.locationID,IFNULL(location.locationCode,'-') as locationCode FROM srp_employeesdetails emp Left join srp_erp_location location on location.locationID = emp.locationID where Erp_companyID  = $companyID And EIdNo = $currentuserid")->row_array();

            if(!empty($locationassign['locationCode']))
            {
                $location = $locationassign['locationCode'];
            }
            ?>

            <div class="col-md-5 pull-left" style="padding-left: 0">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" title="Change Menu">
                            <iconify-icon icon="solar:widget-3-line-duotone" class="icon-menu-selection"></iconify-icon>
                        </a>
                        <ul class="menu-dropdown dropdown-menu dropdown-menu-left d-style-one">
                            <li>
                                <div class="dropdown-content-x-lg py-3 border-light">
                                    <!--<form action="#" method="get" class="sidebar-form">
                                        <input type="text" id="searchMenuInput" class="form-control" placeholder="Search" style="background-color: white" onkeyup="filterMenuItems(this.value)">
                                    </form>-->
                                    <?php
                                    if($navigationData) {
                                        $recordsPerRow = 4;
                                        $count = 0;

                                        echo '<div class="container navigation-menu-box">';

                                        foreach ($navigationData as $record) {
                                            if ($count % $recordsPerRow === 0) {
                                                if ($count > 0) {
                                                    echo '</div>';
                                                }
                                                echo '<div class="row">';
                                            }

                                            echo '<div class="col-md-3">';
                                            echo '<a href="#" class="d-flex align-items-center gap-6 mb-4 pb-3" onclick="onClickNavigation('. $record['navigationMenuID'] .')">';
                                            echo '    <span class="round-50 d-flex align-items-center justify-content-center rounded bg-primary-subtle">';
                                            echo '        <i class="' . $record['pageIcon'] . ' text-primary fa-2x"></i>';
                                            echo '    </span>';
                                            echo '    <h6>' . $record['secondaryDescription'] . '</h6>';
                                            echo '</a>';
                                            echo '</div>';

                                            $count++;
                                        }

                                        if ($count > 0) {
                                            echo '</div>';
                                        }

                                        echo '</div>';
                                    } ?>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="col-md-3 pull-left">
                <ul class="nav navbar-nav" style="margin-top: 15px">
                    <li>
                        <div> <b><?php
                            echo $this->common_data['company_data']['company_name'];
                            ?></b></div>
                    </li>
                </ul>
            </div>

            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" title="Change Company">
                            <i class="fa fa-building" aria-hidden="true" style="margin-left: 10px"></i>
                        </a>
                        <ul class="menu-dropdown dropdown-menu dropdown-menu-top d-style-one">
                            <li>
                                <div class="dropdown-content-x-lg py-3 border-light">
                                    <form action="#" method="get" class="sidebar-form">
                                        <?php
                                            echo form_dropdown('company', drill_down_navigation_dropdown(), $companyID . '-' . $companyTp . '-' . $isGroupUser . '-' . current_userID(), 'id="parentCompanyID" onchange="change_fetchcompany($(\'#parentCompanyID option:selected\').val(),$(\'#parentCompanyID option:selected\').text())" class="form-control select2", required');
                                        ?>
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </li>

                    <li class="support support-menu" style="margin-top: 0px">
                        <a class="cus-p-tb-1" title="Pending Approval Documents" onclick="fetchPage('system/documentallapprovalview',null,'Document Approval','');"> <i class="fa fa-bell-o bx-tada"></i><span class="badge-1 badge-pill" style="background-color:#f1416c;" id="totalapprovalcount">0</span></a>
                    </li>

                    <li class="dropdown user user-menu mr-2">
                        <a href="#" class="dropdown-toggle cus-p-tb-2 text-center-middle" data-toggle="dropdown">
                            <?php
                            $currentEmp_img = $this->session->empImage;
                            ?>
                            <img src="<?php echo $currentEmp_img; ?>" class="user-image current-user-img" alt="User Image">
                            <span class="hidden-xs u-name"><?php echo $name = ucwords($this->session->username); ?> </span> <i class="fa fa-angle-down pull-right"></i>
                        </a>
                        <ul class="menu-dropdown dropdown-menu dropdown-menu-top d-style-one">
                            <li>
                                <div class="dropdown-content dropdown-content-x-lg py-3 border-bottom border-light">
                                    <div class="media-group">
                                        <div class="media media-xl media-middle media-circle">
                                            <img src="<?php echo $currentEmp_img; ?>" alt="" class="img-thumbnail">
                                        </div>
                                        <div class="media-text">
                                            <div class="lead-text name--"><?php echo $name = ucwords($this->session->username); ?></div>
                                            <span class="sub-text username--"><?php echo $name = ucwords($this->session->loginusername); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a href="#" onclick="openlanguagemodel()"><img class="mr-3" id="header-lang-img" src="<?php echo base_url() .'images/flags/' . $language . '.png ' ?>" height="12"> <span>Change Language</span></a>
                            </li>
                            <li>
                                <a href="#"  onclick="openChangePassowrdModel()"><i class="fa fa-lock fs-20 mr-2"></i> <span> Change Password</span></a>
                            </li>

                            <li class="user-footer">
                                <a onclick="removeIFrame()" href="<?php echo site_url('Login/logout'); ?>"
                                   ><i class="fa fa-sign-out fs-20 mr-2"></i>Sign out</a>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <?php if (strtolower(SETTINGS_BAR) == 'on') { ?>
                        <li class="hidden-xs">
                            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>

        </nav>
    </header>

    <!-- Modal chatBot AI -->
	<div aria-hidden="true" class="modal right fade" id="Support" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" data-keyboard="true"
         data-backdrop="static">
		<div class="modal-dialog right-modal" role="document">
			<div class="modal-content">

				<div class="modal-header modal-header-custom">
					<button type="button" class="close close-btn-2" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title d-flex v-align" id="myModalLabel2"><lottie-player src="https://assets6.lottiefiles.com/packages/lf20_9ONI7zjnHV.json"  background="transparent"  speed="1"  style="width: 60px; height: 50px;"  loop  autoplay></lottie-player> Support</h4>
				</div>

			</div><!-- modal-content -->
		</div><!-- modal-dialog -->
	</div><!-- modal -->
    <div aria-hidden="true" role="dialog" id="language_select_modal" class="modal" data-keyboard="true"
         data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form class="form-horizontal">
                    <div class="modal-header languageModalHeader">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-close text-red"></i></button>
                        <h4> Language </h4>
                    </div>
                    <div class="modal-body">
                        <?php
                        $language = drill_down_emp_language();
                        if (!empty($language)) {
                            foreach ($language as $val) {
                                ?>
                                <button
                                        class="btn btn-lg btn-default  btn-block"
                                        onclick="change_emp_language(<?php echo $val['languageID'] ?>)" type="button">
                                    <i class="fa fa-language text-red"></i> <?php echo $val['description'] ?><br>
                                    <small> <?php echo $val['languageshortcode'] ?></small>
                                </button>
                                <?php
                            }
                        } ?>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" role="dialog" id="employee_location_select_modal" class="modal" data-keyboard="true"
         data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form class="form-horizontal">
                    <div class="modal-header languageModalHeader">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-close text-red"></i></button>
                        <h4>  Location </h4>
                    </div>
                    <div class="modal-body">
                        <?php
                        $location = drilldown_emp_location_drop();
                        if (!empty($location)) {
                            foreach ($location as $val) {
                                $val['locationID']; ?>
                                <button
                                        class="btn btn-lg btn-default  btn-block"
                                        onclick="change_emp_location(<?php echo $val['locationID'] ?>)" type="button">
                                    <i class="fa fa-map-marker text-red"></i><small> <?php echo $val['locationCode'] ?> </small>- <small> <?php echo $val['locationName'] ?></small>
                                </button>
                                <?php
                            }
                        } ?>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" role="dialog" id="usermaualmodal" class="modal" data-keyboard="true"
         data-backdrop="static">
        <div class="modal-dialog modal-sm um-w-70">
            <div class="modal-content">
                <form class="form-horizontal">
                    <div class="modal-header languageModalHeader" style="background-color: #f9f6f6f0;">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-close text-red"></i></button>
                        <h4 class="fw-600">User Manual</h4>
                    </div>
                    <div class="modal-body" style="background-color: #f9f6f6f0;">
                        <div class="row">
                            <div class="col-md-12">
                                <?php foreach ($usermanual as $val){
                                        $values = explode('|',$val);
                                        $img_um = $values[1];
                                        $name = $values[0];
                                        $link = $values[2];
                                        ?>
                                    <div class="text-center um-box-div">
                                        <div class="responsiveimg">
                                            <ul class="applicationsusermanual um">
                                                <li>
                                                    <a href="<?php echo $link=='' ?  'javascript:void(0)' : $link ?>"  <?php echo $link=='' ? '' : 'target="_blank"' ?>
                                                    class="<?php echo $link=='' ?  'noLink' : '' ?>">
                                                    <img src="<?php echo base_url('images/um/' . $img_um)?>" />

                                                        <div class="boxnameusermanual"><?php echo $name?></div>

                                                        <div class="listname2usermanual" style="">View</div>
                                                    </a>

                                                </li>
                                            </ul>

                                        </div>
                                    </div>
                                <?php }?>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <div aria-hidden="true" role="dialog" id="training_videos" class="modal" data-keyboard="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="form-horizontal">
                    <div class="modal-header languageModalHeader" style="background-color: #f9f6f6f0;">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-close text-red"></i></button>
                        <h4 class="fw-600">Training Videos</h4>
                    </div>
                    <div class="modal-body" style="background-color: #f9f6f6f0;">


                        <div class="row pb-5">
                            <div class="col-md-12">
                                <ul class="cd-accordion cd-accordion--animated margin-top-lg margin-bottom-lg">

                                    <li class="cd-accordion__item cd-accordion__item--has-children">
                                        <input class="cd-accordion__input" type="checkbox" name ="group-2" id="group-2">
                                        <label class="cd-accordion__label cd-accordion__label--icon-folder" for="group-2"><span>General</span></label>

                                        <div class="cd-accordion__sub cd-accordion__sub--l1 p-5">                                       
                                            <div class="row">
                                                <div class="col-md-2 col-sm-4 col-xs-6 mb-1">
                                                    <div class="t_video_body">
                                                        <a href="https://drive.google.com/file/d/1mvO2mW6Pw6DXbvggutbR0Hp37r-omp1b/view?usp=drive_link" target="_blank"><i class="fa fa-play-circle fs-20 mr-0 clr-1"></i></a>
                                                        <div class="t_video_title">
                                                            <a href="https://drive.google.com/file/d/1mvO2mW6Pw6DXbvggutbR0Hp37r-omp1b/view?usp=drive_link" target="_blank"><h3>Login</h3></a>
                                                        </div>
                                                    </div>
                                                </div>                                                
                                                <div class="col-md-2 col-sm-4 col-xs-6 mb-1">
                                                    <div class="t_video_body">
                                                        <a href="https://drive.google.com/file/d/15RJ7HxPcOslwq8JWahVy6hWZJSbJLbDk/view?usp=drive_link" target="_blank"><i class="fa fa-play-circle fs-20 mr-0 clr-1"></i></a>
                                                        <div class="t_video_title">
                                                            <a href="https://drive.google.com/file/d/15RJ7HxPcOslwq8JWahVy6hWZJSbJLbDk/view?usp=drive_link" target="_blank"><h3>Password Reset</h3></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="cd-accordion__item cd-accordion__item--has-children">
                                        <input class="cd-accordion__input" type="checkbox" name ="group-1" id="group-1">
                                        <label class="cd-accordion__label cd-accordion__label--icon-folder" for="group-1"><span>HRMS</span></label>

                                        <ul class="cd-accordion__sub cd-accordion__sub--l1">
                                            <li class="cd-accordion__item cd-accordion__item--has-children">
                                            <input class="cd-accordion__input" type="checkbox" name ="sub-group-1" id="sub-group-1">
                                            <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-1"><span>Attendance</span></label>
                                            
                                            <div class="cd-accordion__sub cd-accordion__sub--l2 p-5">                                       
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-4 col-xs-6 mb-1">
                                                        <div class="t_video_body">
                                                            <a href="https://drive.google.com/file/d/1Ig252SOhEpgI-mW8NwsiV4tQaymADecH/view?usp=drive_link" target="_blank"><i class="fa fa-play-circle fs-20 mr-0 clr-1"></i></a>
                                                            <div class="t_video_title">
                                                                <a href="https://drive.google.com/file/d/1Ig252SOhEpgI-mW8NwsiV4tQaymADecH/view?usp=drive_link" target="_blank"><h3>Attendance Approval</h3></a>
                                                            </div>
                                                        </div>
                                                    </div>                                                
                                                    <div class="col-md-2 col-sm-4 col-xs-6 mb-1">
                                                        <div class="t_video_body">
                                                            <a href="https://drive.google.com/file/d/1NCzdDOXoWqbsTEuJlzV640rlSR5uOdew/view?usp=drive_link" target="_blank"><i class="fa fa-play-circle fs-20 mr-0 clr-1"></i></a>
                                                            <div class="t_video_title">
                                                                <a href="https://drive.google.com/file/d/1NCzdDOXoWqbsTEuJlzV640rlSR5uOdew/view?usp=drive_link" target="_blank"><h3>Attendance Regularization</h3></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-4 col-xs-6 mb-1">
                                                        <div class="t_video_body">
                                                            <a href="https://drive.google.com/file/d/1vsXgBLNRbKjuDadgfEzt-6Tcc-tSqrO2/view?usp=drive_link" target="_blank"><i class="fa fa-play-circle fs-20 mr-0 clr-1"></i></a>
                                                            <div class="t_video_title">
                                                                <a href="https://drive.google.com/file/d/1vsXgBLNRbKjuDadgfEzt-6Tcc-tSqrO2/view?usp=drive_link" target="_blank"><h3>Approving Variable Component</h3></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </li>
                                            
                                            <li class="cd-accordion__item cd-accordion__item--has-children">
                                            <input class="cd-accordion__input" type="checkbox" name ="sub-group-2" id="sub-group-2">
                                            <label class="cd-accordion__label cd-accordion__label--icon-folder" for="sub-group-2"><span>Leave Management</span></label>

                                            <div class="cd-accordion__sub cd-accordion__sub--l2 p-5">                                       
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-4 col-xs-6 mb-1">
                                                        <div class="t_video_body">
                                                            <a href="https://drive.google.com/file/d/15G6uM2Xrn01NYiID9xv27fJNsSC5P3WS/view?usp=drive_link" target="_blank"><i class="fa fa-play-circle fs-20 mr-0 clr-1"></i></a>
                                                            <div class="t_video_title">
                                                                <a href="https://drive.google.com/file/d/15G6uM2Xrn01NYiID9xv27fJNsSC5P3WS/view?usp=drive_link" target="_blank"><h3>How to Apply Leave</h3></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-4 col-xs-6 mb-1">
                                                        <div class="t_video_body">
                                                            <a href="https://drive.google.com/file/d/1zi9lq6aOybRsz__PhS7Uhvo01sCyLkq4/view?usp=drive_link" target="_blank"><i class="fa fa-play-circle fs-20 mr-0 clr-1"></i></a>
                                                            <div class="t_video_title">
                                                                <a href="https://drive.google.com/file/d/1zi9lq6aOybRsz__PhS7Uhvo01sCyLkq4/view?usp=drive_link" target="_blank"><h3>How to Apply leave for team employee</h3></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-4 col-xs-6 mb-1">
                                                        <div class="t_video_body">
                                                            <a href="https://drive.google.com/file/d/1k3Ergu3SQ4dtajmj4v_XO4ub4_Gn8HcR/view?usp=drive_link" target="_blank"><i class="fa fa-play-circle fs-20 mr-0 clr-1"></i></a>
                                                            <div class="t_video_title">
                                                                <a href="https://drive.google.com/file/d/1k3Ergu3SQ4dtajmj4v_XO4ub4_Gn8HcR/view?usp=drive_link" target="_blank"><h3>Leave Approval</h3></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </li>
                                            
                                        </ul>
                                    </li>

                                    <li class="cd-accordion__item cd-accordion__item--has-children">
                                        <input class="cd-accordion__input" type="checkbox" name ="group-3" id="group-3">
                                        <label class="cd-accordion__label cd-accordion__label--icon-folder" for="group-3"><span>Self Service</span></label>

                                        <div class="cd-accordion__sub cd-accordion__sub--l1 p-5">                                       
                                            <div class="row">
                                                <div class="col-md-2 col-sm-4 col-xs-6 mb-1">
                                                    <div class="t_video_body">
                                                        <a href="https://drive.google.com/file/d/1Q79smTMZ7ReSJ9Y9KIWVcGGw1HcVGBnN/view?usp=drive_link" target="_blank"><i class="fa fa-play-circle fs-20 mr-0 clr-1"></i></a>
                                                        <div class="t_video_title">
                                                            <a href="https://drive.google.com/file/d/1Q79smTMZ7ReSJ9Y9KIWVcGGw1HcVGBnN/view?usp=drive_link" target="_blank"><h3>Employee shift Shift assigning in ESS</h3></a>
                                                        </div>
                                                    </div>
                                                </div>           
                                            </div>
                                        </div>
                                    </li>
                                </ul>        
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div aria-hidden="true" role="dialog" id="support_contact_info_modal" class="modal" data-keyboard="true"
         data-backdrop="static">
        <div class="modal-dialog modal-sm" style="width: 50%;">
            <div class="modal-content">
                <form class="form-horizontal">
                    <div class="modal-header languageModalHeader" style="background-color: #f9f6f6f0;">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-close text-red"></i></button>
                        <h4 class=" text-green">Please contact support team for any inquries</h4>
                    </div>
                    <div class="modal-body" style="background-color: #f9f6f6f0;">
                        <div class="row">
                            <div class="col-md-12" style="margin-left: -9px;">
                                <div class="table-responsive">
                                    <table id="" class="table table-condensed table-borderless table-hover ">
                                        <thead>
                                        <tr>
                                            <th >Contact Person</th>
                                            <th >Email</th>
                                            <th >Telephone</th>
                                        </tr>
                                        </thead>
                                        <tbody id="support_contact_information_body">

                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

