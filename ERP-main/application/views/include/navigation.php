<?php
/**
 * Module : Navigation Menu
 * Created on: 28-June-2017
 * Description : Language File Added , isExternalLink added
 *
 */
$companyID = current_companyID();
$companyType = $this->session->userdata("companyType");
$isGroupUser = $this->session->userdata("isGroupUser");
$navigationMasterId = $this->session->userdata("navigationMasterId");
$empID = current_userID();
$detail = "";
$ci =& get_instance();

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
$this->session->set_userdata("ware_houseID", trim($wareHouseID ?? ''));
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

$ci->load->service('NavigationService');

$data = $ci->NavigationService->getNavigationHeader($companyID, $empID, $userType, $companyType, $isGroupUser);
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
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu fs-i-md pt-10">
            <?php
            if($navigationMasterId) {
                echo renderMenu($data, $navigationMasterId);
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
                before <b><?= date('dS F Y', strtotime($sub_dates['due'])) ?></b>
                 in order to avoid and service disruption. You may contact us on the following mail-id to get more details: 
                <a href="mailto:Shabab.riyal@pbs-int.net"><b style="color: #88cfec">Shabab.riyal@pbs-int.net</b></a>. 
                        Please ignore the message if you have already paid. 
                <a href="<?php echo site_url("subscription"); ?>" target="_blank"><b style="color: #f11c1c">Click here to pay</b></a>
            </div>
        </div>
    <?php } ?>

    <?php
    function renderMenu($data, $parentID = null)
    {
        $html = '';

        foreach ($data as $parent) {
            if ($parent['navigationMenuID'] == $parentID) {
                if ($parent['isSubExist'] == 0) {
                    // Render single item
                    $html .= renderMenuItem($parent);
                } else {
                    // Render treeview item
                    $html .= renderTreeviewItem($parent, $data);
                }
            }
        }

        return $html;
    }

    function renderMenuItem($parent)
    {
        return '<li>
                <a href="#" class="navMenu" 
                   onclick="fetchPage(\'' . $parent['TempPageNameLink'] . '\', \'' . $parent['pageID'] . '\', \'' . $parent['pageTitle'] . '\'), insert_system_audit_log_nav(' . $parent['navigationMenuID'] . ', 1, \'' . $parent['secondaryDescription'] . '\')">
                    <i class="' . $parent['pageIcon'] . '"></i>
                    <span>' . $parent['secondaryDescription'] . '</span>
                </a>
            </li>';
    }

    function renderTreeviewItem($parent, $data)
    {
        $html = '<li class="treeview active">
                <a href="#">
                    <i class="' . $parent['pageIcon'] . '"></i>
                    <span>' . $parent['secondaryDescription'] . '</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu" style="display: block;">';

        foreach ($data as $child) {
            if ($child['levelNo'] == 1 && $parent['navigationMenuID'] == $child['masterID']) {
                if ($child['isSubExist'] == 0) {
                    $html .= renderChildItem($child);
                } else {
                    $html .= renderChildTreeview($child, $data);
                }
            }
        }

        $html .= '</ul></li>';
        return $html;
    }

    function renderChildItem($child)
    {
        $html = '';

        if ($child['isExternalLink'] == 1) {
            $html .= '<li>
                    <a onclick="insert_system_audit_log_nav(' . $child['navigationMenuID'] . ', 1, \'' . $child['secondaryDescription'] . '\')" 
                       href="' . site_url($child['TempPageNameLink']) . '">
                        <i class="' . $child['pageIcon'] . '"></i>
                        ' . $child['secondaryDescription'] . '
                    </a>
                  </li>';
        } else {
            $html .= '<li>
                    <a class="navMenu" 
                       onclick="fetchPage(\'' . $child['TempPageNameLink'] . '\', \'' . $child['pageID'] . '\', \'' . $child['pageTitle'] . '\'), insert_system_audit_log_nav(' . $child['navigationMenuID'] . ', 1, \'' . $child['secondaryDescription'] . '\')">
                        <i class="' . $child['pageIcon'] . '"></i>
                        ' . $child['secondaryDescription'] . '
                    </a>
                  </li>';
        }

        return $html;
    }

    function renderChildTreeview($child, $data)
    {
        $html = '<li>
                <a href="#">
                    <i class="' . $child['pageIcon'] . '"></i>
                    ' . $child['secondaryDescription'] . '
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu" style="display: none;">';

        foreach ($data as $child2) {
            if ($child2['levelNo'] == 2 && $child['navigationMenuID'] == $child2['masterID']) {
                $html .= '<li>
                        <a class="navMenu" 
                           onclick="fetchPage(\'' . $child2['TempPageNameLink'] . '\', \'' . $child2['pageID'] . '\', \'' . $child2['pageTitle'] . '\'), insert_system_audit_log_nav(' . $child2['navigationMenuID'] . ', 1, \'' . $child2['secondaryDescription'] . '\')">
                            <i class="' . $child2['pageIcon'] . '"></i>
                            ' . $child2['secondaryDescription'] . '
                        </a>
                      </li>';
            }
        }

        $html .= '</ul></li>';
        return $html;
    }

    function handleIfLanguageNotExist($languageRow, $translation)
    {
        if (!empty(trim($translation))) {
            return $translation;
        } else {
            return $languageRow;
        }
    }

    ?>

    <!-- Main content -->
    <section class="content" id="ajax_body_container">
