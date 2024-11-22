<style>
    /* MENU-LEFT
-------------------------- */
    /* layout */
    #left ul.nav {
        margin-bottom: 2px;
        font-size: 12px; /* to change font-size, please change instead .lbl */
    }

    #left ul.nav ul,
    #left ul.nav ul li {
        list-style: none !important;
        list-style-type: none !important;
        margin-top: 1px;
        margin-bottom: 1px;
    }

    #left ul.nav ul {
        padding-left: 0;
        width: auto;
    }

    #left ul.nav ul.children {
        padding-left: 12px;
        width: auto;
    }

    #left ul.nav ul.children li {
        margin-left: 0px;
    }

    #left ul.nav li a:hover {
        text-decoration: none;
    }

    #left ul.nav li a:hover .lbl {
        color: #999 !important;
    }

    #left ul.nav li.current > a .lbl {
        background-color: #999;
        color: #fff !important;
    }

    /* parent item */
    #left ul.nav li.parent a {
        padding: 0px;
        color: #ccc;
    }

    #left ul.nav > li.parent > a {
        border: solid 1px #999;
        text-transform: uppercase;
    }

    #left ul.nav li.parent a:hover {
        background-color: rgba(156, 223, 230, 0.57);
        -webkit-box-shadow: inset 0 3px 8px rgba(0, 0, 0, 0.125);
        -moz-box-shadow: inset 0 3px 8px rgba(0, 0, 0, 0.125);
        box-shadow: inset 0 3px 8px rgba(0, 0, 0, 0.125);
    }

    /* link tag (a)*/
    #left ul.nav li.parent ul li a {
        color: #222;
        border: none;
        display: block;
        padding-left: 5px;
    }

    #left ul.nav li.parent ul li a:hover {
        background-color: rgba(156, 223, 230, 0.57);
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
    }

    /* sign for parent item */
    #left ul.nav li .sign {
        display: inline-block;
        width: 28px;
        padding: 5px 8px;
        background-color: transparent;
        color: #fff;
    }

    #left ul.nav li.parent > a > .sign {
        margin-left: 0px;
        background-color: #999;
    }

    /* label */
    #left ul.nav li .lbl {
        padding: 5px 12px;
        display: inline-block;
    }

    #left ul.nav li.current > a > .lbl {
        color: #fff;
    }

    #left ul.nav li a .lbl {
        font-size: 12px;
    }

    /* THEMATIQUE
    ------------------------- */
    /* theme 1 */
    #left ul.nav > li.item-1.parent > a {
        border: solid 1px #51c3eb;
    }

    #left ul.nav > li.item-1.parent > a > .sign,
    #left ul.nav > li.item-1 li.parent > a > .sign {
        margin-left: 0px;
        background-color: #51c3eb;
    }

    #left ul.nav > li.item-1 .lbl {
        color: #51c3eb;
    }

    #left ul.nav > li.item-1 li.current > a .lbl {
        background-color: #51c3eb;
        color: #fff !important;
    }

    /* theme 2 */
    #left ul.nav > li.item-8.parent > a {
        border: solid 1px #51c3eb;
    }

    #left ul.nav > li.item-8.parent > a > .sign,
    #left ul.nav > li.item-8 li.parent > a > .sign {
        margin-left: 0px;
        background-color: #51c3eb;
    }

    #left ul.nav > li.item-8 .lbl {
        color: #51c3eb;
    }

    #left ul.nav > li.item-8 li.current > a .lbl {
        background-color: #51c3eb;
        color: #fff !important;
    }

    /* theme 3 */
    #left ul.nav > li.item-15.parent > a {
        border: solid 1px #94cf00;
    }

    #left ul.nav > li.item-15.parent > a > .sign,
    #left ul.nav > li.item-15 li.parent > a > .sign {
        margin-left: 0px;
        background-color: #94cf00;
    }

    #left ul.nav > li.item-15 .lbl {
        color: #94cf00;
    }

    #left ul.nav > li.item-15 li.current > a .lbl {
        background-color: #94cf00;
        color: #fff !important;
    }

    /* theme 4 */
    #left ul.nav > li.item-22.parent > a {
        border: solid 1px #ef409c;
    }

    #left ul.nav > li.item-22.parent > a > .sign,
    #left ul.nav > li.item-22 li.parent > a > .sign {
        margin-left: 0px;
        background-color: #ef409c;
    }

    #left ul.nav > li.item-22 .lbl {
        color: #ef409c;
    }

    #left ul.nav > li.item-22 li.current > a .lbl {
        background-color: #ef409c;
        color: #fff !important;
    }

    .small, small {
        font-size: 95%;
    }
</style>

<div class="row">
    <div id="left" class="col-sm-12">
        <ul id="menu-group-1" class="nav menu">
            <?php
            if (!empty($country)) {
            foreach ($country as $level1) { ?>
            <li class="item-1 deeper parent" style="margin-top: 1%;">
                <a class="" href="#">
                                <span data-toggle="collapse"
                                      data-parent="#menu-group-<?php echo $level1['countryID']; ?>"
                                      href="#sub-item-<?php echo $level1['countryID']; ?>"
                                      class="sign"><i
                                        class="fa fa-plus" aria-hidden="true" style="color:white;font-size: 13px;"></i></span>
                    <span class="lbl"><strong><?php echo $level1['CountryDes']; ?></strong></span>
                                                                                <span
                                                                                    class="btn btn-default btn-xs pull-right"
                                                                                    onclick="load_save_province(<?php echo $level1['countryID']; ?>,'<?php echo $level1['CountryDes'] ?>')"><i
                                                                                        class="fa fa-plus"></i> Add</span>
                </a>
                <?php
                $stageTwo = $this->db->query("SELECT * FROM srp_erp_statemaster WHERE type = 1 AND countyID = {$level1['countryID']} ORDER BY Description ASC")->result_array();
                if ($stageTwo) {
                    ?>
                    <ul class="children nav-child unstyled small collapse"
                        id="sub-item-<?php echo $level1['countryID']; ?>">
                        <?php
                        foreach ($stageTwo as $level2) {
                        ?>
                        <li class="item-2 deeper parent active">
                            <a class="" href="#">
                                <span data-toggle="collapse"
                                      data-parent="#menu-group-<?php echo $level1['countryID']; ?>"
                                      href="#sub-item-<?php echo $level2['stateID']; ?>-<?php echo $level2['countyID']; ?>"
                                      class="sign"><i
                                        class="fa fa-plus" aria-hidden="true"
                                        style="color:white;font-size: 13px;"></i></span>
                                <span class="lbl"><?php echo $level2['Description']; ?></span>&nbsp;
                                                                                                    <span
                                                                                                        onclick="editAreaSetup(<?php echo $level2['stateID']; ?>,'province')"><i
                                                                                                            class="fa fa-pencil"
                                                                                                            style="color: #3c8dbc"></i></span>
                                                                    <span class="btn btn-default btn-xs pull-right"
                                                                          onclick="load_save_district(<?php echo $level1['countryID']; ?>,<?php echo $level2['stateID']; ?>,'<?php echo $level2['Description'] ?>')"><i
                                                                            class="fa fa-plus"></i> Add</span></a>
                            <?php
                            $stageThree = $this->db->query("SELECT * FROM srp_erp_statemaster WHERE type = 2 AND countyID = {$level1['countryID']} AND masterID = {$level2['stateID']} ORDER BY Description ASC")->result_array();
                            if ($stageThree) { ?>
                                <ul class="children nav-child unstyled small collapse"
                                    id="sub-item-<?php echo $level2['stateID']; ?>-<?php echo $level2['countyID']; ?>">
                                    <?php
                                    foreach ($stageThree as $level3) { ?>
                                        <li class="item-2 deeper parent active">
                                            <a class="" href="#">
                                        <span data-toggle="collapse"
                                              data-parent="#menu-group-<?php echo $level1['countryID']; ?>"
                                              href="#sub-item-<?php echo $level3['stateID']; ?>-<?php echo $level3['countyID']; ?>"
                                              class="sign"><i
                                                class="fa fa-plus" aria-hidden="true"
                                                style="color:white;font-size: 13px;"></i></span>
                                                <span class="lbl"><?php echo $level3['Description']; ?></span>&nbsp;
                                                                                                    <span
                                                                                                        onclick="editAreaSetup(<?php echo $level3['stateID']; ?>,'district')"><i
                                                                                                            class="fa fa-pencil"
                                                                                                            style="color: #3c8dbc"></i></span>
                                                                                <span
                                                                                    class="btn btn-default btn-xs pull-right"
                                                                                    onclick="load_save_division(<?php echo $level1['countryID']; ?>,<?php echo $level3['stateID']; ?>,'<?php echo $level3['Description'] ?>')"><i
                                                                                        class="fa fa-plus"></i> Add</span>
                                            </a>
                                            <?php
                                            $stageFour = $this->db->query("SELECT * FROM srp_erp_statemaster WHERE type = 3 AND countyID = {$level1['countryID']} AND masterID = {$level3['stateID']} ORDER BY Description ASC")->result_array();
                                            //echo $this->db->last_query();
                                            if ($stageFour) { ?>
                                                <ul class="children nav-child unstyled small collapse"
                                                    id="sub-item-<?php echo $level3['stateID']; ?>-<?php echo $level3['countyID']; ?>">
                                                    <?php
                                                    foreach ($stageFour as $level4) {
                                                        $divisionTypeCode_level4 = $level4['divisionTypeCode'];
                                                        ?>
                                                        <li class="item-2 deeper parent active">
                                                            <a class="" href="#">
                                        <span data-toggle="collapse"
                                              data-parent="#menu-group-<?php echo $level1['countryID']; ?>"
                                              href="#sub-item-<?php echo $level4['stateID']; ?>-<?php echo $level4['countyID']; ?>"
                                              class="sign"><i
                                                class="fa fa-plus" aria-hidden="true"
                                                style="color:white;font-size: 13px;"></i></span>
                                                                    <span
                                                                        class="lbl"><?php echo $divisionTypeCode_level4 .' - '. $level4['Description']; ?></span>&nbsp;
                                                                                                    <span
                                                                                                        onclick="editAreaSetup(<?php echo $level4['stateID']; ?>,'division')"><i
                                                                                                            class="fa fa-pencil"
                                                                                                            style="color: #3c8dbc"></i></span>
                                                                <?php if ($divisionTypeCode_level4 != 'JD') { ?>
                                                                    <span
                                                                        class="btn btn-default btn-xs pull-right"
                                                                        onclick="load_save_sub_division(<?php echo $level1['countryID']; ?>,<?php echo $level4['stateID']; ?>,'<?php echo $level4['Description'] ?>')"><i
                                                                            class="fa fa-plus"></i> Add</span> <?php } ?>
                                                            </a>
                                                            <?php
                                                            $stageFive = $this->db->query("SELECT * FROM srp_erp_statemaster WHERE type = 4 AND countyID = {$level1['countryID']} AND masterID = {$level4['stateID']} ORDER BY Description ASC")->result_array();
                                                            //echo $this->db->last_query();
                                                            if ($stageFive) { ?>
                                                                <ul class="children nav-child unstyled small collapse"
                                                                    id="sub-item-<?php echo $level4['stateID']; ?>-<?php echo $level4['countyID']; ?>">
                                                                    <?php
                                                                    foreach ($stageFive as $level5) {
                                                                        $divisionTypeCode_level5 = $level5['divisionTypeCode'];
                                                                        ?>
                                                                        <li class="item-3">
                                                                            <a class="" href="#">
                                        <span data-toggle="collapse"
                                              data-parent="#menu-group-<?php echo $level1['countryID']; ?>"
                                              href="#sub-item-<?php echo $level5['stateID']; ?>-<?php echo $level5['countyID']; ?>"
                                              class="sign"><i
                                                class="fa fa-caret-right fa-lg" style="color: black;"></i></span>
                                                                    <span
                                                                        class="lbl"><?php echo $divisionTypeCode_level5. "-" .$level5['Description']; ?></span>&nbsp;
                                                                                                    <span
                                                                                                        onclick="editAreaSetup(<?php echo $level5['stateID']; ?>,'subdivision')"><i
                                                                                                            class="fa fa-pencil"
                                                                                                            style="color: #3c8dbc"></i></span>
                                                                            </a>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </ul>
                                                                <?php
                                                            }
                                                            ?>
                                                        </li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                                <?php
                                            }
                                            ?>
                                        </li>
                                        <?php
                                    }//closing stage 3 for loop
                                    ?>
                                </ul>
                                <?php
                            } //closing stage 3 if
                            }
                            ?>
                        </li>
                    </ul>
                    <?php
                }
                }
                }
                ?>
        </ul>
    </div>
</div>
<script>
    $(document).ready(function (e) {
        $(".sign").click();
    });

    !function ($) {

        $(document).on("click", "#left ul.nav li.parent > a > span.sign", function () {
            $(this).find('i:first').toggleClass("fa-minus");
        });

        // Open Le current menu
        $("#left ul.nav li.parent.active > a > span.sign").find('i:first').addClass("fa fa-minus");
        $("#left ul.nav li.current").parents('ul.children').addClass("in");

    }(window.jQuery);

</script>
