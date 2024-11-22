<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .entity-detail .ralign, .property-table .ralign {
        text-align: right;
        color: gray;
        padding: 3px 10px 4px 0;
        width: 150px;
        max-width: 200px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .title {
        color: #aaa;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .tddata {
        color: #333;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .nav-tabs > li > a {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
    }

    .nav-tabs > li > a:hover {
        background: rgb(230, 231, 234);
        font-size: 12px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        border-radius: 3px 3px 0 0;
        border-color: transparent;
    }

    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:hover,
    .nav-tabs > li.active > a:focus {
        color: #c0392b;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #f15727;
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

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
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
</style>
<?php
if (!empty($header)) {
    ?>
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>Order Inquiry Details</h2>
            </header>
        </div>
    </div>
    <table class="property-table">
        <tbody>
        <tr>
            <td class="ralign"><span class="title">Inquiry ID</span></td>
            <td><span class="tddata"><?php echo $header['documentCode'] ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Customer Name</span></td>
            <td><span class="tddata"><?php echo $header['CustomerName'] ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Currency</span></td>
            <td><span class="tddata"><?php echo $header['CurrencyCode'] ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Document Date</span></td>
            <td><span class="tddata"><?php echo $header['documentDate'] ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Order Code</span></td>
            <td><span class="tddata"><?php  ?></span></td>
        </tr>
        <tr>
            <td class="ralign"><span class="title">Narration</span></td>
            <td><span class="tddata"><?php echo $header['narration'] ?></span></td>
        </tr>

        <tr>
            <td class="ralign"><span class="title">Status</span></td>
            <td><span class="tddata">
                                    <?php if ($header['confirmStatus'] == 1) { ?>
                                        <span class="label"
                                              style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Confirmed</span>
                                    <?php } else {?>
                                        <span class="label"
                                              style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Confirmed</span>
                                    <?php } ?>
                </span></td>
        </tr>
        </tbody>
    </table>
    <br>

    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>GENERATED RFQ</h2>
            </header>
        </div>
    </div>
    <?php
    if (!empty($detailrfq)) { ?>
        <div class="row">
            <div class="col-md-10">
                <div class="table-responsive mailbox-messages">
                    <table class="table table-hover table-striped">
                        <tbody>
                        <tr>
                            <td class="headrowtitle" style="border-bottom: 1px solid #d21d1d;">#</td>
                            <td class="headrowtitle" style="border-bottom: 1px solid #d21d1d;">Code</td>
                            <td class="headrowtitle" style="border-bottom: 1px solid #d21d1d;">Supplier Name</td>
                            <td class="headrowtitle" style="border-bottom: 1px solid #d21d1d; text-align: center">
                                Action
                            </td>
                        </tr>
                        <?php
                        $x = 1;
                        foreach ($detailrfq as $val) {
                            ?>
                            <tr>
                                <td class="mailbox-name">
                                    <?php echo $x; ?>
                                </td>
                                <td class="mailbox-star"><?php echo $val['supplierSystemCode']; ?></td>
                                <td class="mailbox-star"><?php echo $val['supplierName']; ?></td>
                                <td class="mailbox-star"><span class="pull-right"><div class="actionicon"><a
                                                target="_blank"
                                                onclick="view_rfq_printModel(<?php echo $val['inquiryMasterID']; ?>,<?php echo $val['supplierID']; ?>)"><span
                                                    title="" rel="tooltip" class="glyphicon glyphicon-eye-open"
                                                    data-original-title="View" style="color: white;"></span></a></div></span></td>
                            </tr>
                            <?php
                            $x++;
                        }
                        ?>

                        </tbody>
                    </table><!-- /.table -->
                </div>
            </div>
        </div>
    <?php } else { ?>
        <br>
        <div class="search-no-results">THERE ARE NO GENERATED RFQ TO DISPLAY.</div>
        <?php
    }

}

?>


