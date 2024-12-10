<?php
/**
 * Module : Navigation Menu
 * Created on: 28-June-2017
 * Description : Language File Added , isExternalLink added
 *
 */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('navigation_menu', $primaryLanguage);
$companyID = current_companyID();
$companyType = $this->session->userdata("companyType");
$isGroupUser = $this->session->userdata("isGroupUser");
$empID = current_userID();
$detail = "";

$wareHouseID = $this->db->select('wareHouseID,')->from('srp_erp_warehouse_users')
    ->where([
        'userID' => $empID, 'companyID' => $companyID, 'isActive' => 1
    ])->get()->row('wareHouseID');

$imagePath_arr = $this->db->select('imagePath,isLocalPath')->from('srp_erp_pay_imagepath')->get()->row_array();
if ($imagePath_arr['isLocalPath'] == 1) {
    $imagePath = base_url() . 'images/users/';
} else { // FOR SRP ERP USERS
    $imagePath = $imagePath_arr['imagePath'];
}
$company = $this->db->query("select * from srp_erp_company WHERE company_id={$companyID}")->row_array();

$this->session->set_userdata("companyID", trim($companyID));
$this->session->set_userdata("ware_houseID", trim($wareHouseID));
$this->session->set_userdata("imagePath", trim($imagePath));
$this->session->set_userdata("company_code", trim($company['company_code'] ?? ''));
$this->session->set_userdata("company_name", trim($company['company_name'] ?? ''));
$this->session->set_userdata("company_logo", trim($company['company_logo'] ?? ''));
$detail = "";
$companyTp = $this->session->userdata("companyType");
if ($isGroupUser == 1) {
    $companyType = 1;
}

$userType = $this->common_data['userType'];

if ($userType != 1) {

    if ($companyType == 1) {
        if ($isGroupUser == 1) {

            $this->db->select('userGroupID');
            $this->db->where("companyID", $companyID);
            $this->db->where("empID", $empID);
            $groupdetails = $this->db->get("srp_erp_employeenavigation")->row_array();
            $eidno = $this->db->query("SELECT EIdNo from srp_employeesdetails WHERE Erp_companyID={$companyID} AND isSystemAdmin=1")->row_array();
            $idno = $eidno['EIdNo'];
            if (empty($idno)) {
                $idno = current_userID();
            }
            $userGroupID = $groupdetails['userGroupID'];
            $detail = $this->db->query("SELECT
	srp_erp_navigationmenus.languageID,
	srp_erp_navigationusergroupsetup.*,
	template.TempPageNameLink,
	srp_erp_navigationmenus.isExternalLink 
FROM
	srp_erp_employeenavigation
	INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID
	INNER JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID
	LEFT JOIN (
SELECT
	srp_erp_templates.TempMasterID,
	srp_erp_templates.navigationMenuID,
	srp_erp_templatemaster.TempPageNameLink 
FROM
	srp_erp_templates
	LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID 
WHERE
	srp_erp_templates.companyID = $companyID 
	) AS template ON ( template.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID ) 
inner join 
(SELECT
	navigationMenuID 
FROM
	srp_erp_navigationmenus 
WHERE navigationMenuID=41 or
	masterID = 41 
or masterID in (select navigationMenuID from srp_erp_navigationmenus where masterID=41)) posmenus on posmenus.navigationMenuID=srp_erp_navigationusergroupsetup.navigationMenuID

WHERE
	empID = $empID 
	AND srp_erp_employeenavigation.companyID = $companyID 
	AND srp_erp_employeenavigation.userGroupID = $userGroupID 
ORDER BY
	levelNo,
	sortOrder ASC")->result_array();
        } else {

            $this->db->select('userGroupID');
            $this->db->where("companyID", $companyID);
            $this->db->where("empID", $empID);
            $groupdetails = $this->db->get("srp_erp_employeenavigation")->row_array();
            $userGroupID = $groupdetails['userGroupID'];
            $detail = $this->db->query("SELECT
	srp_erp_navigationmenus.languageID,
	srp_erp_navigationusergroupsetup.*,
	template.TempPageNameLink,
	srp_erp_navigationmenus.isExternalLink 
FROM
	srp_erp_employeenavigation
	INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID
	INNER JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID
	LEFT JOIN (
SELECT
	srp_erp_templates.TempMasterID,
	srp_erp_templates.navigationMenuID,
	srp_erp_templatemaster.TempPageNameLink 
FROM
	srp_erp_templates
	LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID 
WHERE
	srp_erp_templates.companyID = $companyID 
	) AS template ON ( template.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID ) 
inner join 
(SELECT
	navigationMenuID 
FROM
	srp_erp_navigationmenus 
WHERE navigationMenuID=41 or
	masterID = 41 
or masterID in (select navigationMenuID from srp_erp_navigationmenus where masterID=41)) posmenus on posmenus.navigationMenuID=srp_erp_navigationusergroupsetup.navigationMenuID

WHERE
	empID = $empID 
	AND srp_erp_employeenavigation.companyID = $companyID 
	AND srp_erp_employeenavigation.userGroupID = $userGroupID 
ORDER BY
	levelNo,
	sortOrder ASC")->result_array();
        }
    } else {

        $this->db->select('userGroupID');
        $this->db->where("companyID", $companyID);
        $this->db->where("empID", $empID);
        $groupdetails = $this->db->get("srp_erp_employeenavigation")->row_array();
        $userGroupID = $groupdetails['userGroupID'];
        $sql = "SELECT
	srp_erp_navigationmenus.languageID,
	srp_erp_navigationusergroupsetup.*,
	template.TempPageNameLink,
	srp_erp_navigationmenus.isExternalLink 
FROM
	srp_erp_employeenavigation
	INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID
	INNER JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID
	LEFT JOIN (
SELECT
	srp_erp_templates.TempMasterID,
	srp_erp_templates.navigationMenuID,
	srp_erp_templatemaster.TempPageNameLink 
FROM
	srp_erp_templates
	LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID 
WHERE
	srp_erp_templates.companyID = $companyID 
	) AS template ON ( template.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID ) 
inner join 
(SELECT
	navigationMenuID 
FROM
	srp_erp_navigationmenus 
WHERE navigationMenuID=41 or
	masterID = 41 
or masterID in (select navigationMenuID from srp_erp_navigationmenus where masterID=41)) posmenus on posmenus.navigationMenuID=srp_erp_navigationusergroupsetup.navigationMenuID

WHERE
	empID = $empID 
	AND srp_erp_employeenavigation.companyID = $companyID 
	AND srp_erp_employeenavigation.userGroupID = $userGroupID 
ORDER BY
	levelNo,
	sortOrder ASC";
        $detail = $this->db->query($sql)->result_array();
    }
} else {

    if ($companyType == 1) {
        if ($isGroupUser == 1) {

            $this->db->select('userGroupID');
            $this->db->where("companyID", $companyID);
            $this->db->where("empID", $empID);
            $groupdetails = $this->db->get("srp_erp_employeenavigation")->row_array();
            $eidno = $this->db->query("SELECT EIdNo from srp_employeesdetails WHERE Erp_companyID={$companyID} AND isSystemAdmin=1")->row_array();
            $idno = $eidno['EIdNo'];
            if (empty($idno)) {
                $idno = current_userID();
            }
            $userGroupID = $groupdetails['userGroupID'];
            $detail = $this->db->query("SELECT
	srp_erp_navigationmenus.languageID,
	srp_erp_navigationusergroupsetup.*,
	template.TempPageNameLink,
	srp_erp_navigationmenus.isExternalLink 
FROM
	srp_erp_employeenavigation
	INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID
	INNER JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID
	LEFT JOIN (
SELECT
	srp_erp_templates.TempMasterID,
	srp_erp_templates.navigationMenuID,
	srp_erp_templatemaster.TempPageNameLink 
FROM
	srp_erp_templates
	LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID 
WHERE
	srp_erp_templates.companyID = $companyID 
	) AS template ON ( template.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID ) 
inner join 
(SELECT
	navigationMenuID 
FROM
	srp_erp_navigationmenus 
WHERE navigationMenuID=41 or
	masterID = 41 
or masterID in (select navigationMenuID from srp_erp_navigationmenus where masterID=41)) posmenus on posmenus.navigationMenuID=srp_erp_navigationusergroupsetup.navigationMenuID

WHERE
	empID = $empID 
	AND srp_erp_employeenavigation.companyID = $companyID 
	AND srp_erp_employeenavigation.userGroupID = $userGroupID 
ORDER BY
	levelNo,
	sortOrder ASC")->result_array();

        } else {
            $this->db->select('userGroupID');
            $this->db->where("companyID", $companyID);
            $this->db->where("empID", $empID);
            $groupdetails = $this->db->get("srp_erp_employeenavigation")->row_array();
            $userGroupID = $groupdetails['userGroupID'];
            $sql = "SELECT
	srp_erp_navigationmenus.languageID,
	srp_erp_navigationusergroupsetup.*,
	template.TempPageNameLink,
	srp_erp_navigationmenus.isExternalLink 
FROM
	srp_erp_employeenavigation
	INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID
	INNER JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID
	LEFT JOIN (
SELECT
	srp_erp_templates.TempMasterID,
	srp_erp_templates.navigationMenuID,
	srp_erp_templatemaster.TempPageNameLink 
FROM
	srp_erp_templates
	LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID 
WHERE
	srp_erp_templates.companyID = $companyID 
	) AS template ON ( template.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID ) 
inner join 
(SELECT
	navigationMenuID 
FROM
	srp_erp_navigationmenus 
WHERE navigationMenuID=41 or
	masterID = 41 
or masterID in (select navigationMenuID from srp_erp_navigationmenus where masterID=41)) posmenus on posmenus.navigationMenuID=srp_erp_navigationusergroupsetup.navigationMenuID

WHERE
	empID = $empID 
	AND srp_erp_employeenavigation.companyID = $companyID 
	AND srp_erp_employeenavigation.userGroupID = $userGroupID 
ORDER BY
	levelNo,
	sortOrder ASC";
            $detail = $this->db->query($sql)->result_array();
        }
    } else {

        $this->db->select('userGroupID');
        $this->db->where("companyID", $companyID);
        $this->db->where("empID", $empID);
        $groupdetails = $this->db->get("srp_erp_employeenavigation")->row_array();
        $userGroupID = $groupdetails['userGroupID'];
        $sql = "SELECT
	srp_erp_navigationmenus.languageID,
	srp_erp_navigationusergroupsetup.*,
	template.TempPageNameLink,
	srp_erp_navigationmenus.isExternalLink 
FROM
	srp_erp_employeenavigation
	INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID
	INNER JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID
	LEFT JOIN (
SELECT
	srp_erp_templates.TempMasterID,
	srp_erp_templates.navigationMenuID,
	srp_erp_templatemaster.TempPageNameLink 
FROM
	srp_erp_templates
	LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID 
WHERE
	srp_erp_templates.companyID = $companyID 
	) AS template ON ( template.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID ) 
inner join 
(SELECT
	navigationMenuID 
FROM
	srp_erp_navigationmenus 
WHERE navigationMenuID=41 or
	masterID = 41 
or masterID in (select navigationMenuID from srp_erp_navigationmenus where masterID=41)) posmenus on posmenus.navigationMenuID=srp_erp_navigationusergroupsetup.navigationMenuID

WHERE
	empID = $empID 
	AND srp_erp_employeenavigation.companyID = $companyID 
	AND srp_erp_employeenavigation.userGroupID = $userGroupID 
ORDER BY
	levelNo,
	sortOrder ASC";
        $detail = $this->db->query($sql)->result_array();
    }
}


$data = $detail;

function handleIfLanguageNotExist($languageRow, $translation)
{
    if (!empty(trim($translation))) {
        return $translation;
    } else {
        return $languageRow;
    }
}

?>
<script>
    $(document).ready(function (e) {
        $('.navMenu').click(function (e) {
            $('li').removeClass('act');
            $(this).parent('li').addClass('act');
        })
    })
</script>
<aside class="main-sidebar">
    <section class="sidebar">
        <!-- Sidebar user panel -->




        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <?php if ($data) {
            $x = 0;
            foreach ($data

            as $parent) {

            $x++;
            $active = '';
            if ($x == 1) {
                $active = 'active';
            }
            if ($parent['levelNo'] == 0) {
            if ($parent['isSubExist'] == 0) {

            ?>
            <li class="<?php //echo $active; ?>">
                <a href="#" class="navMenu"
                   onclick="fetchPage('<?php echo $parent['TempPageNameLink'] ?>','<?php echo $parent['pageID'] ?>','<?php echo $parent['pageTitle'] ?>'),insert_system_audit_log_nav(<?php echo $parent['navigationMenuID'] ?>,1,'<?php echo $parent['description'] ?>')">
                    <i class="<?php echo $parent['pageIcon'] ?>"></i>
                    <span>
                                    <?php
                                    //echo $parent['description'];
                                    $inputMenuName = language_string_conversion($parent['description'], $parent['languageID']);
                                    $translation = $this->lang->line('navigation_menu_' . $inputMenuName);
                                    echo handleIfLanguageNotExist($parent['description'], $translation);
                                    //echo $inputMenuName;
                                    ?>
                                </span>
                </a>
                <?php
                } else {
                    ?>
                    <ul class="treeview-menu menu-open" style="display: block;">
                        <li>
                            <a class="navMenu"
                               onclick="LoadHome();">
                                <i class="fa fa-home"></i>
                                Home
                            </a>
                        </li>
                        <?php foreach ($data as $child) {
                            if ($child['levelNo'] == 1 && $parent['navigationMenuID'] == $child['masterID']) {
                                if ($child['isSubExist'] == 0) {
                                    if ($child['isExternalLink'] == 1) {
                                        ?>
                                        <li>
                                            <a class="nav-menu2"
                                               onclick="insert_system_audit_log_nav(<?php echo $child['navigationMenuID'] ?>,1,'<?php echo $child['description'] ?>')"
                                               href="<?php echo site_url($child['TempPageNameLink']); ?>"><i
                                                        class="<?php echo $child['pageIcon'] ?>"></i>
                                                <?php
                                                $inputMenuName = language_string_conversion($child['description'], $child['languageID']);
                                                $translation = $this->lang->line('navigation_menu_' . $inputMenuName);
                                                echo handleIfLanguageNotExist($child['description'], $translation);
                                                ?>
                                            </a></li>
                                        <?php
                                    } else {
                                        ?>
                                        <li>
                                            <a class="navMenu"
                                               onclick="fetchPage('<?php echo $child['TempPageNameLink'] ?>','<?php echo $child['pageID'] ?>','<?php echo $child['pageTitle'] ?>'),insert_system_audit_log_nav(<?php echo $child['navigationMenuID'] ?>,1,'<?php echo $child['description'] ?>')">
                                                <i class="<?php echo $child['pageIcon'] ?>"></i>
                                                <?php
                                                $inputMenuName = language_string_conversion($child['description'], $child['languageID']);
                                                $translation = $this->lang->line('navigation_menu_' . $inputMenuName);
                                                echo handleIfLanguageNotExist($child['description'], $translation);
                                                ?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <li>
                                        <a href="#" class="">
                                            <i class="<?php echo $child['pageIcon'] ?> "></i>
                                            <?php
                                            $inputMenuName = language_string_conversion($child['description'], $child['languageID']);
                                            $translation = $this->lang->line('navigation_menu_' . $inputMenuName);
                                            echo handleIfLanguageNotExist($child['description'], $translation);
                                            ?>
                                            <i class="fa fa-angle-left pull-right"></i></a>
                                        <ul class="treeview-menu" style="display: none;">
                                            <?php foreach ($data as $child2) {
                                                if ($child2['levelNo'] == 2 && $child['navigationMenuID'] == $child2['masterID']) {
                                                    ?>
                                                    <li class="">
                                                        <a class="navMenu"
                                                           onclick="fetchPage('<?php echo $child2['TempPageNameLink'] ?>','<?php echo $child2['pageID'] ?>','<?php echo $child2['pageTitle'] ?>',insert_system_audit_log_nav(<?php echo $child2['navigationMenuID'] ?>,1,'<?php echo $child2['description'] ?>'))"><i
                                                                    class="<?php echo $child2['pageIcon'] ?>"></i>
                                                            <?php
                                                            $inputMenuName = language_string_conversion($child2['description'], $child2['languageID']);
                                                            $translation = $this->lang->line('navigation_menu_' . $inputMenuName);
                                                            echo handleIfLanguageNotExist($child2['description'], $translation);
                                                            ?>
                                                        </a>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </li>
                                    <?php
                                }
                            }
                        } ?>
                    </ul>

                    <?php
                }
                }
                }
                }
                ?>
        </ul>
    </section>

</aside>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="min-height: 800px !important;">
    <!-- Content Header (Page header) -->

    <?php if ($this->session->userdata('subscription_expire_notification') == 1) {
        $sub_dates = $this->session->userdata('subscription_dates');
        ?>
        <div>
            <div class="alert alert-warning1 fade in alert-dismissible"
                 style="color: #8a6d3b; background-color: #fcf8e3; border-color: #faebcc; margin-bottom: 0px;"
                 role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                        onclick="hide_subscription_alert()"><span aria-hidden="true">&times;</span></button>
                <!--Your subscription is expired on <b><? /*=date('dS F Y', strtotime($sub_dates['expiry']))*/ ?></b>, Your account will be blocked on <b><? /*=convert_date_format($sub_dates['due'])*/ ?></b>.-->
                Kindly note that your subscription is expiring / has expired on
                <b><?= date('dS F Y', strtotime($sub_dates['expiry'])) ?></b>. We request you to renew the subscription
                before <b><?= date('dS F Y', strtotime($sub_dates['due'])) ?></b> in order to avoid and service
                disruption. You may contact us on the following mail-id to get more details: <a
                        href="mailto:Shabab.riyal@pbs-int.net"><b
                            style="color: #88cfec">Shabab.riyal@pbs-int.net</b></a>. Please ignore the message if you
                have already paid.
            </div>
        </div>
    <?php } ?>

    <!-- Main content -->
    <section class="content" id="ajax_body_container">
