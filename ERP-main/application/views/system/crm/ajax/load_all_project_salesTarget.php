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
        background-color: #fff;
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
        background-color: #fff;
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
        border: solid 1px #ff6307;
    }

    #left ul.nav > li.item-1.parent > a > .sign,
    #left ul.nav > li.item-1 li.parent > a > .sign {
        margin-left: 0px;
        background-color: #ff6307;
    }

    #left ul.nav > li.item-1 .lbl {
        color: #ff6307;
    }

    #left ul.nav > li.item-1 li.current > a .lbl {
        background-color: #ff6307;
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

    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .headrowtitle {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }
    .total {
        border-top: 1px double #8a7c1a !important;
        border-bottom: 3px double #b7a318 !important;
        font-weight: bold;
        font-size: 12px !important;
    }
</style>
<?php //var_dump($header); ?>
<div class="row">
    <div class="col-sm-12">
        <div id="left" class="span3">
            <ul id="menu-group-1" class="nav menu">
                <?php if (!empty($header)) {
                    $x = 1;
                    $varientsTotal = 0;
                    $targetTotal = 0;
                    $achivedTotal = 0;
                    $collectionTotal = 0;
                    $convertFormat = convert_date_format_sql();
                    $companyID = $this->common_data['company_data']['company_id'];
                    foreach ($header as $val) {
                        ?>
                        <li class="item-1 deeper parent" style="margin-top: 1%;">
                            <a class="" href="#">
                                <span data-toggle="collapse" data-parent="#menu-group-1"
                                      href="#sub-item-<?php echo $val['projectID']; ?>-<?php echo $val['userID']; ?>"
                                      class="sign"><i
                                        class="fa fa-plus" aria-hidden="true" style="color:white;font-size: 13px;"></i></span>
                                <span class="lbl"><strong><?php echo $val['employeeName']; ?></strong></span>
                            </a>
                            <ul class="children nav-child unstyled small collapse"
                                id="sub-item-<?php echo $val['projectID']; ?>-<?php echo $val['userID']; ?>">
                                <div class="table-responsive mailbox-messages">
                                    <table class="table table-hover table-striped">
                                        <tbody>
                                        <tr class="task-cat-upcoming">
                                            <td class="headrowtitle"
                                                style="border-bottom: solid 1px #f76f01;">#
                                            </td>
                                            <td class="headrowtitle"
                                                style="border-bottom: solid 1px #f76f01;">Date From
                                            </td>
                                            <td class="headrowtitle"
                                                style="border-bottom: solid 1px #f76f01;">Date To
                                            </td>
                                            <td class="headrowtitle"
                                                style="border-bottom: solid 1px #f76f01;">Currency
                                            </td>
                                            <td class="headrowtitle text-right"
                                                style="border-bottom: solid 1px #f76f01;text-align: right;">Target
                                                Value
                                            </td>
                                            <td class="headrowtitle text-right"
                                                style="border-bottom: solid 1px #f76f01;text-align: right;">Achieved
                                            </td>
                                            <td class="headrowtitle"
                                                style="border-bottom: solid 1px #f76f01;text-align: right;">Variance
                                            </td>
                                            <td class="headrowtitle"
                                                style="border-bottom: solid 1px #f76f01;text-align: right;">Collection
                                            </td>
                                        </tr>
                                        <?php
                                        $employeeSalestarget = $this->db->query('SELECT srp_erp_crm_salestarget.salesTargetID,targetValue,DATE_FORMAT(dateFrom,\'' . $convertFormat . '\') AS dateFrom, DATE_FORMAT(dateTo,\'' . $convertFormat . '\') AS dateTo,CurrencyCode FROM srp_erp_crm_salestarget LEFT JOIN srp_erp_currencymaster  ON srp_erp_crm_salestarget.transactionCurrencyID = srp_erp_currencymaster.currencyID WHERE srp_erp_crm_salestarget.companyID = ' . $companyID . ' AND projectID = ' . $val['projectID'] . ' AND userID =' . $val['userID'] . '')->result_array();
                                        $x = 1;
                                        if (!empty($employeeSalestarget)) {
                                            foreach ($employeeSalestarget as $tar) {
                                                ?>
                                                <tr>
                                                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                                                    <td class="mailbox-star"
                                                        width="5%"><?php echo $tar['dateFrom']; ?></td>
                                                    <td class="mailbox-star"
                                                        width="5%"><?php echo $tar['dateTo']; ?></td>
                                                    <td class="mailbox-star"
                                                        width="5%"><?php echo $tar['CurrencyCode']; ?></td>
                                                    <td class="mailbox-star text-right"
                                                        width="5%"><?php echo number_format($tar['targetValue'], 2);
                                                        $targetTotal += $tar['targetValue'];
                                                        ?></td>
                                                    <td class="mailbox-star text-right" width="5%">
                                                        <?php
                                                        $achivedvalue = $this->db->query('SELECT SUM(acheivedValue) as varientsAmount FROM srp_erp_crm_salestargetacheived WHERE srp_erp_crm_salestargetacheived.companyID = ' . $companyID . ' AND projectID = ' . $val['projectID'] . ' AND userID =' . $val['userID'] . ' AND salesTargetID = ' . $tar['salesTargetID'] . '')->row_array();
                                                        $achivedTotal +=  $achivedvalue['varientsAmount'];
                                                        echo number_format($achivedvalue['varientsAmount'], 2);
                                                        ?>
                                                    </td>
                                                    <td class="mailbox-star text-right"
                                                        width="5%"><?php echo number_format(($tar['targetValue'] - $achivedvalue['varientsAmount']), 2);
                                                        $varientsTotal += ($tar['targetValue'] - $achivedvalue['varientsAmount']);
                                                        ?></td>
                                                    <td class="mailbox-star text-right"
                                                        width="5%"><?php
                                                        $collectionvalue = $this->db->query('SELECT SUM(collectionAmount) as collectionAmount FROM srp_erp_crm_salestargetacheived WHERE srp_erp_crm_salestargetacheived.companyID = ' . $companyID . ' AND projectID = ' . $val['projectID'] . ' AND userID =' . $val['userID'] . ' AND salesTargetID = ' . $tar['salesTargetID'] . '')->row_array();
                                                        $collectionTotal +=  $collectionvalue['collectionAmount'];
                                                        echo number_format($collectionvalue['collectionAmount'], 2);
                                                        ?></td>
                                                </tr>
                                                <?php
                                                $x++;
                                            }
                                        }
                                        ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="total" colspan="4">Total</td>
                                                <td class="total text-right"><?php echo number_format($targetTotal, 2); ?></td>
                                                <td class="total text-right"><?php echo number_format($achivedTotal, 2); ?></td>
                                                <td class="total text-right"><?php echo number_format($varientsTotal, 2); ?></td>
                                                <td class="total text-right"><?php echo number_format($collectionTotal, 2); ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </ul>
                        </li>
                        <?php
                    }
                } else { ?>
                    <strong class="attachemnt_title">
                                <span
                                    style="text-align: center;font-size: 15px;font-weight: 800;">NO SALES TARGET ASSIGNED FOR THIS PROJECT</span>
                    </strong>
            <?php    }?>
            </ul>
        </div>
    </div>
</div>

<script>

    $( document ).ready(function() {
        $("#left ul.nav li.parent.active > a > span.sign").find('i:first').addClass("icon-minus");
        $("#left ul.nav li.current").parents('ul.children').addClass("in");
    });

    !function ($) {


        $(document).on("click", "#left ul.nav li.parent > a > span.sign", function () {
            $(this).find('i:first').toggleClass("icon-minus");
        });

    }(window.jQuery);
</script>