<?php
$this->load->helper('usermanual_helper');
$companyInfo = get_companyInfo();
$productID = $companyInfo['productID'];
$emplangid = $this->common_data['emplangid'];
$companyID = current_companyID();
$curentuser = current_userID();
$locationwisecodegenerate = getPolicyValues('LDG', 'All');
$thirdpartapp = getPolicyValues('TPA','ALL');
$isGroupCom = getusergroupcomapny();
$usermanual = all_modules_array();
?>


<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <a href="<?php echo site_url('dashboard'); ?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>ERP</b></span>
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
            <div class="col-md-4 pull-left" id="master-time-div" style="">
                <ul class="nav navbar-nav hidden-xs">
                    <li>
                        <a style="border: none" id="timeBox_style">

                            <div class="hidden-md hidden-sm hidden-xs">
                                <span class="" id="timeBox" style="font-size: 15px; font-weight: bolder"></span> &nbsp;&nbsp;&nbsp;
                                <span class="" id="dateBox"></span>
                            </div>
                            <div class="hidden-lg">
                                <span
                                        class="hidden-sm hidden-xs">Date : </span><strong><?php echo date('d/m/Y') ?></strong>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu" id="posPreLoader" style="display: none;">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                           style="background-color: rgba(244, 244, 244, 0.3);">
                            <i class="fa fa-refresh fa-spin" style="color:#0b0803; font-size:18px;"></i> <!--Loading-->
                        </a>
                    </li>
                    <li class="user user-menu">
                        <a class="dropdown-toggle" data-toggle="dropdown">
                            <?php
                            if ($this->session->userdata("companyType") == 1) {
                                ?>
                                <span
                                        class="hidden-xs"><?php echo '( ' . current_companyCode() . ' ) ' . ucwords(trim_value($this->common_data['company_data']['company_name'], 10)); ?></span>
                            <?php } else { ?>
                                <span
                                        class="hidden-xs"><?php echo ucwords($this->session->userdata("company_name")); ?></span>
                            <?php } ?>
                        </a>
                    </li>

                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?php
                            /*$filePath = imagePath() . $this->session->empImage;
                            $currentEmp_img = checkIsFileExists($filePath);*/
                            $currentEmp_img = $this->session->empImage;
                            ?>
                            <img src="<?php echo $currentEmp_img; ?>" class="user-image" alt="User Image">
                            <span
                                    class="hidden-xs">
                                <?php
                                $name = ucwords($this->session->loginusername);
                                echo substr($name,0,10);
                                ?>

                                </br></span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</div>


