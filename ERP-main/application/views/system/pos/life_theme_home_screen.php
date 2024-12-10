<?php
//$this->load->view('include/header', 'POS');
//$this->load->view('include/top-posr');
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta
                name="viewport"
                content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <meta name="description" content=""/>
        <meta name="author" content=""/>
        <title>Spur | Admin</title>
        <link rel="icon" href="favicon.ico"/>
        <!-- Font Awesome 5 -->
        <link href="<?php echo base_url('plugins/pos/css/font-awesome.css'); ?>" rel="stylesheet"/>
        <!-- Bootstrap core CSS -->
        <link href="<?php echo base_url('plugins/pos/css/bootstrap.css'); ?>" rel="stylesheet"/>
        <!-- Material Design Bootstrap -->
        <link href="<?php echo base_url('plugins/pos/css/mdb.css'); ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('plugins/pos/css/owl.carousel.css'); ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('plugins/pos/css/owl.theme.default.css') ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('plugins/pos/css/hover.css'); ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('plugins/pos/css/animate.css'); ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('plugins/pos/css/jquery.mCustomScrollbar.css'); ?>" rel="stylesheet"/>
        <!-- Custom styles for this template -->
        <link href="<?php echo base_url('plugins/pos/css/style.css'); ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('plugins/pos/themes/the-life-theme.css'); ?>" rel="stylesheet"/>

        <!-- jQuery -->
        <script src="<?php echo base_url('plugins/pos/js/jquery.min.js') ?>"></script>

    </head>
    <body>
    <div class="bg-home"></div>
    <nav class="navbar navbar-expand-lg navbar-main" id="top-navbar" style="visibility: hidden">
        <div class="logo">
            <a href="<?php echo site_url('dashboard') ?>"
            ><img src="<?php echo base_url('images/spur-logo.png'); ?>" alt="Spur Logo"
                /></a>
        </div>
        <button
                class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <div class="time-block">
                <a style="border: none" id="timeBox_style">
                    <div class="hidden-md hidden-sm hidden-xs">
                        <span class="" id="timeBox" style="font-size: 15px; font-weight: 600;"></span> &nbsp;&nbsp;&nbsp;
                        <span class="" id="dateBox" style="font-size: 15px; font-weight: bolder"></span>
                    </div>
                </a>
            </div>
        </div>



        <ul class="navbar-nav ml-auto main-nav">
            <li class="nav-item active">
                <a class="nav-link nav-theme" onclick="theme_change_dialog()"
                >
                    <svg viewBox="0 -53 384 384" xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="m368 154.667969h-352c-8.832031 0-16-7.167969-16-16s7.167969-16 16-16h352c8.832031 0 16 7.167969 16 16s-7.167969 16-16 16zm0 0"
                        />
                        <path
                                d="m368 32h-352c-8.832031 0-16-7.167969-16-16s7.167969-16 16-16h352c8.832031 0 16 7.167969 16 16s-7.167969 16-16 16zm0 0"
                        />
                        <path
                                d="m368 277.332031h-352c-8.832031 0-16-7.167969-16-16s7.167969-16 16-16h352c8.832031 0 16 7.167969 16 16s-7.167969 16-16 16zm0 0"
                        />
                    </svg>
                    Theme</a
                >
            </li>
            <li class="nav-item">
                <a class="nav-link nav-life" href="#">The Life</a>
            </li>
            <li class="nav-item">
                <a class="nav-link nav-theme" href="#"><?php
                    $outletInfo = get_outletInfo();
                    echo ucwords(trim_value_pos($outletInfo['wareHouseDescription'], 8, 'bottom'));
                    ?></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link nav-admin dropdown-toggle"
                   href="#"
                   id="navbarDropdown"
                   role="button"
                   data-toggle="dropdown"
                   aria-haspopup="true"
                   aria-expanded="false">
                    <div class="avatar">
                        <img src="<?php
                        $currentEmp_img = $this->session->empImage;
                        echo $currentEmp_img; ?>" alt="User Image">
                    </div>
                    <?php
                    $name = ucwords($this->session->loginusername);
                    echo substr($name, 0, 10);
                    ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="    min-width: 171px;">
                    <a href="<?php echo site_url('Login/logout'); ?>" class="dropdown-item"
                       style="font-size: 15px;padding: 5px;">Sign
                        out</a>
                </div>
            </li>
        </ul>
    </nav>
    </nav>
    <main class="home-main">
        <div class="container" style="width: 94%;max-width: 1520px;">
            <div class="row">
                <div class="col-12 col-xl-6">
                    <div class="intro-item-cover parallax-eff">
                        <div class="layer" data-depth="0.4">
                            <div class="para-item paraitem-05">
                                <img src="<?php echo base_url('images/para-img-05.png'); ?>" alt="Images"/>
                            </div>
                        </div>
                        <div class="layer" data-depth="0.2">
                            <div class="para-item paraitem-06">
                                <img src="<?php echo base_url('images/para-img-05.png'); ?>" alt="Images"/>
                            </div>
                        </div>
                        <div class="layer" data-depth="0.2">
                            <div class="para-item intro-logo">
                                <img src="<?php echo base_url('images/spur-anm-screen.png'); ?>" alt="Spur"/>
                            </div>
                        </div>
                        <div class="layer" data-depth="0.3">
                            <div class="para-item paraitem-01">
                                <img src="<?php echo base_url('images/para-img-01.png'); ?>" alt="Images"/>
                            </div>
                        </div>
                        <div class="layer" data-depth="-0.2">
                            <div class="para-item paraitem-02">
                                <img src="<?php echo base_url('images/para-img-02.png'); ?>" alt="Images"/>
                            </div>
                        </div>
                        <div class="layer" data-depth="-0.3">
                            <div class="para-item paraitem-03">
                                <img src="<?php echo base_url('images/para-img-03.png'); ?>" alt="Images"/>
                            </div>
                        </div>
                        <div class="layer" data-depth="0.4">
                            <div class="para-item paraitem-04">
                                <img src="<?php echo base_url('images/para-img-04.png'); ?>" alt="Images"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="home-carousel-cover">
                        <div class="home-item-carousel">
                            <div class="home-item-carousel-hd">
                                <h3>Order Type</h3>
                            </div>
                            <div class="carousel-home-item owl-carousel owl-theme">
                                <?php
                                $customerType = getCustomerType();
                                if (!empty($customerType)) {
                                    ?>

                                    <?php
                                    $defaultID = 0;
                                    $isDelivery = 0;
                                    $isDineIn = 0;
                                    foreach ($customerType as $val) {
                                        ?>

                                        <div class="item" data-val="<?php echo $val['customerDescription'] ?>"
                                             onclick="setCustomerType(<?php echo $val['customerTypeID']; ?>,<?php echo $val['isThirdPartyDelivery'] ?>,<?php echo $val['isDineIn'] ?>)"
                                             id="customerTypeID_<?php echo $val['customerTypeID']; ?>">
                                            <div class="home-item">
                                                <figure>
                                                    <i><img src="<?php echo base_url('images/pos/' . $val['imageName']) ?>"
                                                            alt="Icon"/></i>
                                                </figure>
                                                <figcaption>
                                                    <p><?php echo $val['displayDescription']; ?></p>
                                                </figcaption>
                                            </div>
                                        </div>

                                    <?php } ?>

                                <?php } ?>
                            </div>
                        </div>
                        <div class="home-item-carousel">
                            <div class="home-item-carousel-hd">
                                <h3>Transaction</h3>
                            </div>
                            <div class="carousel-home-item owl-carousel owl-theme">
                                <div class="item" onclick="toModalOpen('kitchen')">
                                    <div class="home-item">
                                        <figure>
                                            <i
                                            ><img
                                                        src="<?php echo base_url('images/home-carousel-item-04.png'); ?>"
                                                        alt="Icon"
                                                /></i>
                                        </figure>
                                        <figcaption>
                                            <p>Kitchen</p>
                                        </figcaption>
                                    </div>
                                </div>
                                <div class="item" onclick="toModalOpen('receipt')">
                                    <div class="home-item">
                                        <figure>
                                            <i
                                            ><img
                                                        src="<?php echo base_url('images/home-carousel-item-05.png'); ?>"
                                                        alt="Icon"
                                                /></i>
                                        </figure>
                                        <figcaption>
                                            <p>Receipt</p>
                                        </figcaption>
                                    </div>
                                </div>
                                <div class="item" onclick="toModalOpen('hold')">
                                    <div class="home-item">
                                        <figure>
                                            <i
                                            ><img
                                                        src="<?php echo base_url('images/home-carousel-item-06.png'); ?>"
                                                        alt="Icon"
                                                /></i>
                                        </figure>
                                        <figcaption>
                                            <p>Hold Bill</p>
                                        </figcaption>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="home-item-carousel">
                            <div class="home-item-carousel-hd">
                                <h3>Market</h3>
                            </div>
                            <div class="carousel-home-item owl-carousel owl-theme">
                                <div class="item">
                                    <div class="home-item">
                                        <figure>
                                            <i
                                            ><img
                                                        src="<?php echo base_url('images/home-carousel-item-07.png'); ?>"
                                                        alt="Icon"
                                                /></i>
                                        </figure>
                                        <figcaption>
                                            <p>Gift Card</p>
                                        </figcaption>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="home-item">
                                        <figure>
                                            <i
                                            ><img
                                                        src="<?php echo base_url('images/home-carousel-item-08.png'); ?>"
                                                        alt="Icon"
                                                /></i>
                                        </figure>
                                        <figcaption>
                                            <p>SMS</p>
                                        </figcaption>
                                    </div>
                                </div>
                                <div class="item">
                                    <div class="home-item">
                                        <figure>
                                            <i
                                            ><img
                                                        src="<?php echo base_url('images/home-carousel-item-09.png'); ?>"
                                                        alt="Icon"
                                                /></i>
                                        </figure>
                                        <figcaption>
                                            <p>Loyalty program</p>
                                        </figcaption>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <a href="javascript:;" id="return-top" class="return-top"
    ><span><img src="<?php echo base_url('images/up-icon.svg'); ?>" alt="Up"/></span
        ></a>



    <div class="modal" id="themeChangerModal" tabindex="-1" role="dialog">
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

                        <div class="col-md-6">
                            <h1><a href="javascript:;"
                                   class="r-s-c-theme btn btn-primary btn-lg btn-block pd-btn"
                                   id="default-theme">Default Theme</a></h1>
                        </div>


                        <div class="col-md-6">
                            <h1><a href="javascript:;"
                                   class="r-s-c-theme btn btn-primary btn-lg btn-block pd-btn"
                                   id="glass-theme">Glass Theme</a></h1>
                        </div>

                    </div>
                    <div class="row">


                        <div class="col-md-6">
                            <h1><a href="javascript:;"
                                   class="r-s-c-theme btn btn-primary btn-lg btn-block pd-btn"
                                   id="classic-theme">Classic Theme</a></h1>
                        </div>


                        <div class="col-md-6">
                            <h1><a href="javascript:;"
                                   class="r-s-c-theme btn btn-primary btn-lg btn-block pd-btn"
                                   id="material-theme">Material Theme</a></h1>
                        </div>
                    </div>
                    <div class="row">
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
    </body>

    <!-- Bootstrap tooltips -->
    <script src="<?php echo base_url('plugins/pos/js/popper.min.js') ?>"></script>
    <!-- Bootstrap core JavaScript -->
    <script src="<?php echo base_url('plugins/pos/js/bootstrap.min.js') ?>"></script>
    <!-- MDB core JavaScript -->
    <script src="<?php echo base_url('plugins/pos/js/mdb.min.js') ?>"></script>
    <!-- Owl carousel -->
    <script src="<?php echo base_url('plugins/pos/js/owl.carousel.js" type="text/javascript') ?>"></script>
    <!-- Item parallax -->
    <script src="<?php echo base_url('plugins/pos/js/jquery.parallax.min.js') ?>"></script>
    <!-- Custom Scroll -->
    <script src="<?php echo base_url('plugins/pos/js/jquery.mCustomScrollbar.concat.min.js') ?>"></script>
    <!-- Wow animation -->
    <script src="<?php echo base_url('plugins/pos/js/wow.js') ?>"></script>
    <!-- Custom JS -->
    <script src="<?php echo base_url('plugins/pos/js/main.js') ?>"></script>

    <script src="<?php echo base_url('plugins/pos/r-pos.js') ?>"></script>
    <style>
        new WOW().init();
    </style>
    <script>
        $(document).ready(function () {
            getDate();
            window.dispatchEvent(new Event('resize'));
            setTimeout(function () {
                $('#top-navbar').css('visibility', 'visible');
            }, 100);
        });

        function theme_change_dialog() {
            $("#themeChangerModal").modal('show');
        }

        function setCustomerType(id, isDelivery, isDineIn, ordermd = 0) {

            var customerType = id;
            localStorage.setItem('cusType',customerType);
            localStorage.setItem('isDelivery',isDelivery);
            localStorage.setItem('isHomeRedirect','1');
            localStorage.setItem('buttonID','');
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('Pos_restaurant/updateCustomerTypeH'); ?>",
                data: {customerType: customerType},
                cache: false,
                beforeSend: function () {
                    startLoadPos();
                },
                success: function (data) {
                    document.location.href='<?php echo site_url("restaurant"); ?>';
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad()
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Interent: Please try again');
                    }
                }
            });
        }




        function resize_window() {
            if (!this._items.length) {
                return false;
            }

            if (this._width === this.$element.width()) {
                return false;
            }

            if (!this.$element.is(':visible')) {
                return false;
            }

            this.enter('resizing');

            if (this.trigger('resize').isDefaultPrevented()) {
                this.leave('resizing');
                return false;
            }

            this.invalidate('width');

            this.refresh();

            this.leave('resizing');
            this.trigger('resized');
        };

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

        function open_kitchen_ready() {
            <?php
            if (isset($tablet) && $tablet) {
                $method = 'load_kitchen_ready_tablet';
            } else {
                $method = 'load_kitchen_ready';
            }
            ?>
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_kitchen/' . $method); ?>",
                data: $("#frm_POS_holdReceipt").serialize(),
                cache: false,
                beforeSend: function () {
                    startLoad();
                    $("#pos_open_kitchenStatus").modal("show");
                    $("#kitchenStatus_modalBody").html('');
                },
                success: function (data) {
                    stopLoad();
                    $("#kitchenStatus_modalBody").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    if (jqXHR.status == false) {
                        myAlert('w', 'No Internet, Please try again');
                    } else {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }

                }
            });
        }
    </script>
    <script>
        function toModalOpen($buttonID){
            localStorage.setItem('isHomeRedirect','1');
            localStorage.setItem('buttonID',$buttonID);
            document.location.href='<?php echo site_url("restaurant"); ?>';
        }
    </script>
    </html>
<?php
$this->load->view('include/footer-pos');
