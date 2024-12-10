<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('profile', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$currentUserID = current_userID();
$convertFormat = convert_date_format_sql();
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
<?php
$x = 1;
$z = 1;
if (!empty($header)) {
    ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;" width="5%"></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;" ><?php echo $this->lang->line('profile_period');?></td><!--Period-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;" width="15%"> <?php echo $this->lang->line('common_currency');?></td><!--Currency-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;" width="20%"> <?php echo $this->lang->line('profile_target_amount');?></td><!--Target Amount-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;" width="20%"><?php echo $this->lang->line('profile_achived_amount');?> </td><!--Achieved Amount-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;" width="20%"> <?php echo $this->lang->line('common_action');?></td><!--Action-->
            </tr>
            <?php
            foreach ($header as $val) {
                ?>
                <tr>
                    <td class="mailbox-name" width="5%"><i class="fa fa-plus-square coll" data-id="<?php echo $val['salesTargetID'] ?>" style="font-size: 18px; color: crimson;"></i>
                    </td>
                    <td class="mailbox-name" width="20%"><a href="#" class="fontColoringMaster"><?php echo $val['formattedDate']; ?></a></td>
                    <td class="mailbox-name" width="15%"><a href="#" class="fontColoringMaster"><?php echo $val['CurrencyCode']; ?></a></td>
                    <td class="mailbox-name" style="text-align: right" width="20%"><a href="#"
                                                                          class="fontColoringMaster"><?php echo number_format($val['targetValue'], 2); ?></a>
                    </td>
                    <td class="mailbox-name" style="text-align: right" width="20%"><a href="#" class="fontColoringMaster">
                            <?php
                            $achievedAmount = $this->db->query("SELECT sum(acheivedValue) as total FROM srp_erp_crm_salestargetacheived WHERE salesTargetID = {$val['salesTargetID']} AND userID = {$currentUserID}")->row_array();

                            if($achievedAmount){
                                echo number_format($achievedAmount['total'], 2);
                            }else{
                                echo '0.00';
                            }

                            ?></a>
                    </td>
                    <td class="mailbox-attachment" style="text-align: right" width="20%">
                        <button type="button" class="btn btn-primary btn-xs pull-right"
                                onclick="employee_SalesTarget_add_modal(<?php echo $val['salesTargetID']; ?>,'<?php echo $val['formattedDate']; ?>', '<?php echo $val['CurrencyCode']; ?>')">
                            <i
                                class="fa fa-plus"></i>
                        </button>
                    </td>
                </tr>
                <?php
                $drilldownData = $this->db->query("SELECT salesTargetAcheivedID,acheivedValue,DATE_FORMAT(documentDate, '" . $convertFormat . "') AS formattedDate,pro.projectName FROM srp_erp_crm_salestargetacheived sta LEFT JOIN srp_erp_crm_project pro ON sta.projectID = pro.projectID WHERE userID = $currentUserID AND salesTargetID = {$val['salesTargetID']} ORDER BY salesTargetAcheivedID DESC")->result_array();
                if (!empty($drilldownData)) { ?>
                    <tr>
                        <table class="table table-hover table-striped" style="margin-left: 8%; width: 80%" id="table_<?php echo $val['salesTargetID']; ?>">
                            <tbody>
                            <tr>
                                <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;"></td>
                                <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;"><?php echo $this->lang->line('common_date');?><!--Date--></td>
                                <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;"><?php echo $this->lang->line('common_project');?><!--Project--></td>
                                <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;"><?php echo $this->lang->line('common_currency');?><!--Currency--></td>
                                <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;text-align: center;">
                                    Achieved
                                    Amount
                                </td>
                                <td class="headrowtitle_sub" style="border-bottom: 1px solid #f76f01;text-align: center;">
                                    Action
                                </td>
                            </tr>
                            <?php
                            $z = 1;
                            foreach ($drilldownData as $row) {
                                ?>
                                <tr>
                                    <td class="mailbox-name"><a href="#" class="fontColoring"><?php echo $z; ?></a>
                                    </td>
                                    <td class="mailbox-name"><a href="#"
                                                                class="fontColoring"><?php echo $row['formattedDate']; ?></a>
                                    </td>
                                    <td class="mailbox-name"><a href="#" class="fontColoring"><?php echo $row['projectName']; ?></a>
                                    </td>
                                    <td class="mailbox-name"><a href="#"
                                                                class="fontColoring"><?php echo $val['CurrencyCode']; ?></a>
                                    </td>
                                    <td class="mailbox-name" style="text-align: right"><a href="#"
                                                                                          class="fontColoring"><?php echo number_format($row['acheivedValue'], 2); ?></a>
                                    </td>
                                    <td class="mailbox-attachment"><span class="pull-right">
                            <a href="#"
                               onclick="edit_salesTarget_achieved(<?php echo $row['salesTargetAcheivedID']; ?>)"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<a
                                                onclick="delete_salesTargetAcheived_profile(<?php echo $row['salesTargetAcheivedID']; ?>);"><span
                                                    title="Delete" rel="tooltip"
                                                    class="glyphicon glyphicon-trash"
                                                    style="color:rgb(209, 91, 71);"></span></a></span>
                                    </td>
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
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results"><?php echo $this->lang->line('profile_there_are_no_sales');?><!--THERE ARE NO SALES TARGET ACHIEVED TO DISPLAY-->.</div>
    <?php
}
?>
<script>
    $('.coll').click(function () {
        var glcode = $(this).attr('data-id');
        var header = $(this).attr('data-head');
        var type = $(this).attr('data-type');
        if ($(this).hasClass('fa fa-plus-square')) {
            $('#table_' + glcode).addClass("hide");
            $(this).removeClass("fa fa-plus-square").addClass("fa fa-minus-square");
        }
        else {
            $(this).removeClass("fa fa-minus-square").addClass("fa fa-plus-square");
            $('#table_' + glcode).removeClass("hide");
        }
    });
</script>