<?php

?>
<style>
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

    .headrowtitle_sub {
        font-size: 11px;
        line-height: 15px;
        height: 15px;
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

    .numberColoring {
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }

    .fontColoring {
        color: #795548;
    }

    .fontColoringMaster {
        font-size: 12px;
    }
</style>
<br>
<ul class="nav nav-tabs" id="main-tabs">
    <li class="active"><a href="#cash" data-toggle="tab"><i class="fa fa-television"></i> Cash</a></li>
    <!--<li><a href="#items" data-toggle="tab"><i class="fa fa-television"></i>Items </a></li>-->
</ul>
<?php
$x = 1;
$z = 1;
$balance = 0;
if (!empty($details)) {
?>
<br>
<div class="tab-content">
    <div class="tab-pane active" id="cash">
        <div class="table-responsive mailbox-messages">
            <table class="table table-hover table-striped">
                <tbody>

                <?php
                foreach ($details

                as $val) {
                if ($val['commitmentTotal'] != 0) {
                    $balance = ($val['commitmentTotal'] - $val['collectionAmount']);
                } else {
                    $balance = 0;
                }
                ?>

                <tr>
                    <td class="mailbox-name" width="1%"><i class="fa fa-minus-square coll"
                                                           data-id="<?php echo $val['collectiondetail'] ?>"
                                                           style="font-size: 18px; color: green;"></i></td>
                    <td class="mailbox-name" width="25%"><a href="#"
                                                            class="fontColoringMaster"><?php echo $val['documentsystemcode']; ?></a>
                    </td>
                    <td class="mailbox-name" width="25%"><a href="#"
                                                            class="fontColoringMaster"><?php echo $val['projectName']; ?></a>
                    </td>
                    <td class="mailbox-name" width="40%"><a href="#" class="fontColoringMaster">
                            <?php
                            if ($val['transactionType'] == 1) {
                                echo 'Commited Amount : ' . number_format($val['commitmentTotal'], $val['transactionCurrencyDecimalPlaces']);
                            } else {
                                echo 'Direct Collection : ' . number_format($val['collectionAmount'], $val['transactionCurrencyDecimalPlaces']);
                            }

                            ?>
                    </td>
                    </a>

                </tr>
                <?php
                $contactid = $val['donorsID'];
                $company_id = $this->common_data['company_data']['company_id'];
                $commitmentautoid = $val['commitmentAutoId'];
                $projectid = $val['ngoProjectID'];
                $tp = $val['transactionType'];
                $cash_collected = $this->db->query("SELECT cm.commitmentAutoId,dcm.collectionAutoId,dcm.donorsID,don.NAME AS donorName,dcm.documentDate,dcm.documentSystemCode,prj.projectName,
	            dcd.projectID,dcm.transactionCurrencyID,dcm.transactionCurrency,'-' commitmentTotal,dcd.transactionAmount AS transactionAmount,dcm.transactionCurrencyDecimalPlaces,'1' AS transactionType
FROM
	srp_erp_ngo_donorcollectionmaster dcm
	JOIN srp_erp_ngo_donors don ON dcm.donorsID = don.contactID
	JOIN srp_erp_ngo_donorcollectiondetails dcd ON dcm.collectionAutoId = dcd.collectionAutoId
	LEFT JOIN srp_erp_ngo_commitmentmasters cm ON cm.commitmentAutoId = dcd.commitmentAutoID
	LEFT JOIN srp_erp_ngo_projects prj ON dcd.projectID = prj.ngoProjectID 
WHERE
	dcm.donorsID = $contactid 
	AND dcm.isDeleted !=1
	AND dcm.approvedYN !=0
	AND cm.commitmentAutoId = $commitmentautoid 
	AND dcd.projectID = $projectid 
	AND dcd.commitmentAutoID != 0
GROUP BY
	dcd.collectionAutoId,
	dcd.commitmentAutoID Having transactionType = $tp UNION ALL
	
SELECT
	'0' AS commitmentAutoId,
	collectionD.collectionAutoId,
	collectionM.donorsID,
	don.NAME AS donorName,
	collectionM.documentDate,
	collectionM.documentsystemcode,
	prj.projectName,
	prj.ngoProjectID,
	collectionM.transactionCurrencyID,
	collectionM.transactionCurrency,
	'0' commitmentTotal,
	sum( collectionD.transactionAmount ) AS collectionAmount,
	collectionM.transactionCurrencyDecimalPlaces,
	'2' AS transactionType
	
FROM
	srp_erp_ngo_donorcollectiondetails collectionD
	LEFT JOIN srp_erp_ngo_donorcollectionmaster collectionM ON collectionD.collectionAutoId = collectionM.collectionAutoId
	LEFT JOIN srp_erp_ngo_donors don ON collectionM.donorsID = don.contactID
	LEFT JOIN srp_erp_ngo_projects prj ON collectionD.projectID = prj.ngoProjectID 
WHERE
	collectionD.companyID = $company_id 
	AND collectionD.type = 1 
	AND collectionM.approvedYN = 1 
	AND collectionM.donorsID = $contactid 
	AND collectionD.projectID = $projectid 
	AND ( collectionD.commitmentAutoID = 0 OR collectionD.commitmentAutoID IS NULL ) 
GROUP BY
	collectionD.collectionAutoId Having transactionType = $tp")->result_array();
                if (!empty($cash_collected)) {
                if ($val['transactionType'] != 2){
                ?>
                <tr>
                    <table class="table table-hover table-striped" style="margin-left: 8%; width: 80%"
                           id="table_<?php echo $val['collectiondetail']; ?>">
                        <tbody>
                        <tr>
                            <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;">#</td>
                            <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;">Document ID</td>
                            <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;"> Project</td>
                            <td class="headrowtitle_sub"
                                style="border-bottom: 1px solid #f76f01;"><?php echo $this->lang->line('common_currency'); ?>
                                Collected amount
                            </td>
                        </tr>
                        <?php
                        $z = 1;
                        foreach ($cash_collected as $row) {

                            ?>

                            <tr>
                                <td class="mailbox-name"><a href="#" class="fontColoring"><?php echo $z; ?></a>
                                </td>
                                <td class="mailbox-name"><a href="#"
                                                            class="fontColoring"><?php echo $row['documentSystemCode']; ?></a>
                                </td>
                                <td class="mailbox-name"><a href="#"
                                                            class="fontColoring"><?php echo $row['projectName']; ?></a>
                                </td>
                                <td class="mailbox-name"><a href="#"
                                                            class="fontColoring"><?php echo number_format($row['transactionAmount'], $row['transactionCurrencyDecimalPlaces']); ?></a>
                                </td>
                            </tr>
                            <?php
                            $z++;


                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="text-right " colspan="3">Balance</td>
                            <td class="mailbox-name"
                                style="text-align: left"><?php echo number_format($balance, 2); ?></td>
                        </tr>
                        </tfoot>
                    </table>
                    <br>
                </tr>
                <table class="table table-hover table-striped" width="100%">
                    <tbody>
                    <?php

                    }
                    }
                    }
                    ?>
                    </tbody>
                </table>
        <?php
       } else { ?>
            <br>
            <div class="search-no-results">THERE ARE NO CASH DONATIONS TO DISPLAY</div>
            <?php
        }
        ?>
        </div>
        </div>
    <div class="tab-pane" id ="items">
        <div class="row">
            <div class="col-md-12">
                <?php
                $x = 1;
                $z = 1;
                $balance = 0;
                if (!empty($item)) {
                ?>
                <div class="tab-content">
                    <div class="tab-pane active" id="cash">
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped">
                                <tbody>

                                <?php
                                foreach ($item as $row) {
                                ?>

                                    <tr>
                                        <td class="mailbox-name" width="1%"><i class="fa fa-minus-square item"
                                                                               data-id="<?php echo $row['collectiondetail'] ?>"
                                                                               style="font-size: 18px; color: green;"></i>
                                        </td>
                                        <td class="mailbox-name" width="25%"><a href="#"
                                                                                class="fontColoringMaster"><?php echo $row['documentsystemcode']; ?></a>
                                        </td>
                                        <td class="mailbox-name" width="80%"><a href="#"
                                                                                class="fontColoringMaster"><?php echo $row['projectName']; ?></a>
                                        </td>
                                        </a>

                                    </tr>
                                <br>
                                <?php
                                $contactid = $row['donorsID'];
                                $company_id = $this->common_data['company_data']['company_id'];
                                $commitmentautoid = $row['commitmentAutoId'];
                                $projectid = $row['ngoProjectID'];
                                $item_rec_data = $this->db->query("select cm.documentSystemCode,cmd.commitmentDetailAutoID,cmd.commitmentAutoId,projectID,type,collectionD.itemAutoID,itemSystemCode,
                                itemDescription,unitOfMeasure,cmd.itemQty,collectionD.collectionDItemQty,projectName FROM srp_erp_ngo_commitmentdetails cmd
                                LEFT JOIN srp_erp_ngo_commitmentmasters cm ON cm.commitmentAutoId = cmd.commitmentAutoId
                                LEFT JOIN srp_erp_ngo_projects ON ngoProjectID = projectID 
                                LEFT JOIN ( SELECT collectionAutoId,itemautoID, commitmentAutoId,transactionAmount AS collectionAmount,itemQty as collectionDItemQty 
                                FROM srp_erp_ngo_donorcollectiondetails WHERE type= 2 GROUP BY itemAutoID,commitmentAutoId,projectID) AS collectionD
                                 ON collectionD.commitmentAutoId = cmd.commitmentAutoId and cmd.itemAutoID=collectionD.itemAutoID
                                WHERE
                                cmd.commitmentAutoId =$commitmentautoid
                                AND projectID = $projectid
                                AND type = 2")->result_array();
                                if (!empty($item_rec_data))
                                {
                                ?>
                                    <tr>
                                        <table class="table table-hover table-striped"
                                               style="margin-left: 8%; width: 80%"
                                               id="table_<?php echo $row['collectiondetail']; ?>">
                                            <tbody>
                                            <tr>
                                                <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;">
                                                    #
                                                </td>
                                                <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;">
                                                    Item System Code
                                                </td>
                                                <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;">
                                                    Item Description
                                                </td>
                                                <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;">
                                                    Unit Of Measure
                                                </td>
                                                <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;">
                                                    Commited Qty
                                                </td>
                                                <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;">
                                                    Received Qty
                                                </td>
                                            </tr>
                                            <?php
                                            $z = 1;
                                            foreach($item_rec_data as $itemrow) { ?>
                                                    <tr>
                                                        <td class="mailbox-name"><a href="#" class="fontColoring"><?php echo $z; ?></a></td>
                                                        <td class="mailbox-name"><a href="#" class="fontColoring"><?php echo $itemrow['itemSystemCode']; ?></a></td>
                                                        <td class="mailbox-name"><a href="#" class="fontColoring"><?php echo $itemrow['itemDescription']; ?></a></td>
                                                        <td class="mailbox-name"><a href="#" class="fontColoring"><?php echo $itemrow['unitOfMeasure']; ?></a></td>
                                                        <td class="mailbox-name"><a href="#" class="fontColoring"><?php echo $itemrow['itemQty']; ?></a></td>
                                                        <td class="mailbox-name"><a href="#" class="fontColoring"><?php echo $itemrow['collectionDItemQty']; ?></a></td>
                                                    </tr>

                                                <?php
                                                $z++;
                                            }

                                                ?>
                                            </tbody>
                                        </table>
                                    </tr>
                                <table class="table table-hover table-striped" width="100%">
                                    <tbody>
                                    <?php

                                    }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <?php
                                } else { ?>
                                    <br>
                                    <div class="search-no-results">THERE ARE NO ITEM DONATIONS TO DISPLAY</div>
                                    <?php
                                }
                                ?>
            </div>
        </div>
    </div>
</div>

<script>
 $('.coll').click(function () {
     var colauto = $(this).attr('data-id');
 if ($(this).hasClass('fa fa-minus-square')) {
  $('#table_' + colauto).addClass("hide");
 $(this).removeClass("fa fa-minus-square").addClass("fa fa-plus-square");
  }
   else {
  $(this).removeClass("fa fa-plus-square").addClass("fa fa-minus-square");
  $('#table_' + colauto).removeClass("hide");
  }
 });

 $('.item').click(function () {
  var itemauto = $(this).attr('data-id');
 if ($(this).hasClass('fa fa-minus-square')) {
$('#table_' + itemauto).addClass("hide");
 $(this).removeClass("fa fa-minus-square").addClass("fa fa-plus-square");
    }
     else {
     $(this).removeClass("fa fa-plus-square").addClass("fa fa-minus-square");
     $('#table_' + itemauto).removeClass("hide");
                }
            });

</script>