<div class="wrapper">
    <header class="main-header" style="position: static;">
        <!-- Logo -->
        <a href="<?php echo site_url('dashboard'); ?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b><img src="<?php echo base_url('favicon.ico') ?>"/></b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg" style="padding:5px;">
                <?php
                $defaultImagePath = 'images/' . LOGO;
                $logoImage = base_url($defaultImagePath);
                $outletInfo = get_outletInfo();
                $image = $outletInfo['warehouseImage'];
                if (!empty($image)) {
                    $outletImagePath = 'uploads/warehouses/' . $image;
                    $logoImage = base_url($outletImagePath);
                }
                ?>
                <?php echo '<img style="max-height:30px;"  src="' . $logoImage . '"/>' ?>
            </span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <div id=""></div>
            <!-- Sidebar toggle button-->
            <div class="pull-left" id="master-time-div" style="">
                <ul class="nav navbar-nav hidden-xs">
                    <?php
                    $c_rsctheme = $this->input->cookie('_rsctheme', TRUE);
                    if (isset($c_rsctheme)) {
                        switch ($c_rsctheme) {
                            case "the-life":
                                echo ' <li><a href="#" class="sidebar-toggle" id="menubar-toggle-btn" data-toggle="offcanvas" role="button" onclick="set_navbar_cookie()">
                            <span class="sr-only">Toggle navigation</span>
                        </a> </li>';
                                break;
                        }
                    }
                    ?>
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
                    <?php
                    $c_rsctheme = $this->input->cookie('_rsctheme', TRUE);
                    if (isset($c_rsctheme)) {
                        switch ($c_rsctheme) {
                            case "the-life":
                                echo ' <li class="dropdown user user-menu hidden-sm hidden-xs" style="cursor: pointer" id="" onclick="LoadHome();">
                        <a>
                            <label style="margin-bottom: 0px;cursor: pointer;">Home</label>
                        </a>
                    </li>';
                                break;
                        }
                    }
                    ?>
                    <li class="dropdown user user-menu" id="posPreLoader" style="display: none;">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                           style="background-color: rgba(244, 244, 244, 0.3);">
                            <i class="fa fa-refresh fa-spin" style="color:#0b0803; font-size:18px;"></i> <!--Loading-->
                        </a>
                    </li>
                    <li class="dropdown user user-menu" id="">
                        <a href="#" class="dropdown-toggle" id="themechanger" data-toggle="dropdown">
                            <i class="fa fa-bars"></i> Theme
                        </a>
                    </li>

                    <!-- Modal -->
                    <div class="modal fade" id="themeChangerModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background: #444;color: white;">
                                    <h3 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-circle"></i>
                                        Theme Option</h3>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <div class="row">
                                        <div class="btn-lyr" style="width:100%">
                                            <div class="col-md-6">
                                                <h1><a href="javascript:;"
                                                       class="r-s-c-theme btn btn-primary btn-lg btn-block pd-btn"
                                                       id="default-theme">Default Theme</a></h1>
                                            </div>
                                        </div>
                                        <div class="btn-lyr" style="width:100%">
                                            <div class="col-md-6">
                                                <h1><a href="javascript:;"
                                                       class="r-s-c-theme btn btn-primary btn-lg btn-block pd-btn"
                                                       id="glass-theme">Glass Theme</a></h1>
                                            </div>

                                        </div>
                                        <div class="btn-lyr" style="width:100%">
                                            <div class="col-md-6">
                                                <h1><a href="javascript:;"
                                                       class="r-s-c-theme btn btn-primary btn-lg btn-block pd-btn"
                                                       id="classic-theme">Classic Theme</a></h1>
                                            </div>


                                        </div>
                                        <div class="btn-lyr" style="width:100%">
                                            <div class="col-md-6">
                                                <h1><a href="javascript:;"
                                                       class="r-s-c-theme btn btn-primary btn-lg btn-block pd-btn"
                                                       id="material-theme">Material Theme</a></h1>
                                            </div>
                                        </div>
                                        <div class="btn-lyr" style="width:100%">
                                            <div class="col-md-6">
                                                <h1><a href="javascript:;"
                                                       class="r-s-c-theme btn btn-primary btn-lg btn-block pd-btn"
                                                       id="the-life">The Life</a></h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        $(document).ready(function () {
                            $("#themechanger").click(function () {
                                $('#themeChangerModal').modal('show');
                                $(".modal-backdrop").hide();
                            });

                            $(".r-s-c-theme").click(function () {
                                $.ajax({
                                    type: 'POST',
                                    url: "<?php echo site_url('Pos_restaurant/rpos_theme_set_to_ses'); ?>",
                                    data: {rsctheme: this.id},
                                    cache: false,
                                    beforeSend: function () {
                                    },
                                    success: function (data) {
                                        location.reload();
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        if (jqXHR.status == false) {
                                            myAlert('w', 'No Internet, Please try again');
                                        } else {
                                            myAlert('e', '<br>Message: ' + errorThrown);
                                        }
                                    }
                                });
                            });
                        });
                    </script>

                    <?php if (!empty($posData['wareHouseLocation'])) { ?>
                        <li class="dropdown user user-menu hidden-sm hidden-xs" id="">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-placement="bottom">
                                <label style="margin-bottom: 0px"><?php //print_r($posData);
                                    $outletInfo = get_outletInfo();

                                    echo ucwords(trim_value_pos($outletInfo['wareHouseDescription'], 8, 'bottom'));
                                    ///echo $posData['wareHouseLocation']; ?></label>
                            </a>
                        </li>
                    <?php }
                    if (!empty($posData['counterDet'])) { ?>
                        <li class="dropdown user user-menu" id="">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <label style="margin-bottom: 0px">&nbsp;
                                    <?php echo $posData['counterDet']; ?>
                                    &nbsp;
                                </label>
                            </a>
                        </li>
                    <?php } ?>
                    <li class="user user-menu hidden-xs">
                        <a class="dropdown-toggle" data-toggle="dropdown">
                            <span rel="tooltip" data-placement="bottom"
                                  title="<?php
                                  echo $this->common_data['company_data']['company_name'];
                                  // echo trim_value_pos($this->common_data['company_data']['company_name'], 10);
                                  ?>">
                                <?php echo current_companyCode(); ?>
                            </span>
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
                                echo substr($name, 0, 10);
                                ?>

                                </br></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?php echo $currentEmp_img; ?>" class="img-circle" alt="User Image">
                                <p>
                                    <?php echo $name = ucwords($this->session->username); ?>
                                    <!-- <small><?php //$company = $this->cache->get('company_11'); var_dump($company); ?></small> -->
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a onclick="fetchPage('system/profile/profile_information','','Profile')"
                                       class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?php echo site_url('Login/logout'); ?>" class="btn btn-default btn-flat">Sign
                                        out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

