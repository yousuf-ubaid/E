<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$status_arr = all_opportunities_status();

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

    .actionicon_project {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #de7a7a;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 4px 6px 5px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #fdfdfd;
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
    .numberColoring{
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>
<?php
if (!empty($header)) {
    $issuperadmin = crm_isSuperAdmin();
    ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect taskHeading_tr" style="background: white;">
                <td class="task-cat-upcoming" colspan="12">
                    <div class="task-cat-upcoming-label">Opportunities</div><!--Latest Tasks-->
                    <div class="taskcount"><?php echo sizeof($headercount); ?></div>
                </td>
            </tr>
            <tr>
              <!--  <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>-->

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Description</td><!--Name-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center"><?php echo $this->lang->line('crm_pipeline');?></td><!--pipeline-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Created By</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_status');?></td><!--status-->

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center"><?php echo $this->lang->line('common_action');?></td><!--Action-->
            </tr>
            <?php
            $tyr = 1;
            foreach ($header as $val) {

                $companyID = current_companyID();
                $opportunityID =  $val['opportunityID'];
                $opportunityTotal = $this->Crm_lead_model->opportunity_products_value($opportunityID);

                $product = $this->db->query("SELECT

                        SUM((price / companyLocalCurrencyExchangeRate)+(subscriptionAmount / companyLocalCurrencyExchangeRate)+(ImplementationAmount / companyLocalCurrencyExchangeRate)) AS Total,srp_erp_currencymaster.CurrencyCode as CurrencyCode
                    FROM
                        srp_erp_crm_opportunityproducts
                        INNER JOIN srp_erp_currencymaster ON srp_erp_crm_opportunityproducts.companyLocalCurrencyID = srp_erp_currencymaster.currencyID 
                    WHERE
                        companyID = {$companyID}
                        AND opportunityID = '{$val['opportunityID']}' 
                        GROUP BY
                        productID ")->row_array();

                $textdecoration = '';
                if ($val['closeStatus'] != 0) {
                    $textdecoration = '';
                    //$textdecoration = 'textClose';
                }
                ?>
                <tr>
                    <!--<td class="mailbox-name"><a href="#" class="numberColoring"><?php /*echo $tyr; */?></a></td>-->

                    <td class="mailbox-name" >
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">

                                    <a
                                        class="link-person noselect <?php echo $textdecoration; ?>" href="#"
                                        onclick="fetchPage('system/crm/opportunities_edit_view','<?php echo $val['opportunityID'] ?>','View Opportunity','CRM')">
                                        <?php echo $val['documentSystemCode'] ?> <br>
                                        

                                </strong></a> <?php echo $val['organizationName'] ?> 
                                <br> 
                                    <?php echo $val['opportunityName'] ?>
                                <br>
                                    <?php echo $val['CurrencyCode'] ." ".format_number($opportunityTotal, $val['transactionDecimalPlaces'])  ?><br> <?php echo $this->lang->line('crm_closing_date');?> <?php echo $val['forcastCloseDate'] ?>
                                <br>
                                    <?php echo '<div style="margin-top: 3%;color: #6b0a0a;font-weight: 700; font-size:10px;">'.$val['leadsSystemCode'].'</div>' ?>
                                
                                </strong></div>
                            </a>
                            <!-- $val['transactionAmount'] -->
                        </div>
                    </td>

                    <td class="mailbox-name" style="width: 50%;">
                        <div class="arrow-steps clearfix">
                            <?php
                            if (!empty($val['pipelineID'])) {
                                $pipeline = $this->db->query("SELECT * FROM srp_erp_crm_pipelinedetails WHERE pipeLineID={$val['pipelineID']}")->result_array();
                                $html = '';
                                if (!empty($pipeline)) {
                                    $count = count($pipeline);
                                    $percentage = 100 / $count;
                                    $x = 1;
                                    foreach ($pipeline as $pipe) {
                                        $active = 'not-current';
                                        if ($pipe['pipeLineDetailID'] == $val['pipelineStageID']) {
                                            $active = "current";
                                        }
                                        echo '<div class="step ' . $active . '" style="margin-top: 3px !important;"><span class="' . $textdecoration . '" title="' . $pipe['stageName'] . '">' . substr($pipe['stageName'], 0, 5) . '</span></div>';
                                        $x++;
                                    }

                                }
                            }
                            ?>
                        </div>
                    </td>
                    <td class="mailbox-name">
                        <strong class="contacttitle">
                            <a class="link-person noselect" href="#">User: <?php echo $val['campaigncreateduser'] ?>
                                <br>Date: <?php echo $val['createdDatetimeopportunity'] ?>
                        </strong></a>
                    </td>
                    <td class="mailbox-name">

                        <?php if($val['closeStatus']!=0){?>
                        <span class="label" style="background-color:<?php echo $val['statusBackGroundColor'] ?>; color:<?php echo $val['statusTextColor'] ?>; font-size: 11px;"><?php echo $val['statusDescription'] ?></span>
                        <?php } else {?>


                            <?php echo form_dropdown('statusID', $status_arr, $val['statusIDoppor'], 'class="form-control select2" id="statusID_"'.$val['opportunityID'].' onchange="statuscheack(this.value,'.$val['opportunityID'].',\''. $filtervalue .'\','.$pageID.','.$uriSegment.')"'); ?>
                            
                        <?php }?>

                        <br>
                        <?php
                        if ($val['closeStatus'] == 2) { ?>

                            <div style="margin-top: 3%;color: #de7a7a;font-weight: 700;"><?php echo $this->lang->line('crm_closed_and_converted');?><br>
                            <?php echo '<div style="margin-top: 3%;color: #6b0a0a;font-weight: 700; font-size:10px;">'.$val['projectSystemCode'].'</div>' ?>
                            </div><!--Closed & Converted-->
                            <?php
                        }
                        ?>
                        <?php
                        if ($val['closeStatus'] == 1) { ?>

                            <div style="margin-top: 3%;color: #de7a7a;font-weight: 700;">Closed
                            </div><!--Closed & Converted-->
                            <?php
                        }
                        ?>
                    </td>

                    <td class="mailbox-attachment">
                        <span class="pull-right">
                                                        <?php
                                                        if ($val['closeStatus'] == 1 || $val['closeStatus'] == 2|| $val['closeStatus'] == 3) { ?>
                                                            <div class="actionicon"><span class="glyphicon glyphicon-ok"
                                                                                          style="color:rgb(255, 255, 255);"
                                                                                          title="completed"></span>
                                                            </div>
                                                            <?php
                                                        } else { ?>
                                                            <a href="#"
                                                               onclick="fetchPage('system/crm/create_opportunity','<?php echo $val['opportunityID'] ?>','<?php echo $this->lang->line('crm_edit_opportunity');?>','CRM')"><span
                                                                    title="Edit" rel="tooltip"
                                                                    class="glyphicon glyphicon-pencil"></span></a>&nbsp;<!--Edit Opportunity-->
                                                            <a
                                                                onclick="delete_opportunity(<?php echo $val['opportunityID'] ?>);"><span
                                                                    title="Delete"
                                                                    rel="tooltip"
                                                                    class="glyphicon glyphicon-trash"
                                                                    style="color:rgb(209, 91, 71);"></span></a>
                                                        <?php } ?>
                        </span>
                    </td>
                    <?php
                    if ($issuperadmin['isSuperAdmin'] == 1 && ($val['closeStatus'] == 1 || $val['closeStatus'] == 2 || $val['closeStatus'] == 3)) {
                        ?>
                        <td class="mailbox-attachment">
                            <div class="actionicon" style="background-color: red">
                                <a href="#" onclick="reopenOpportunities(<?php echo $val['opportunityID'] ?>)" <i
                                    class="fa fa-repeat" aria-hidden="true" style="color: white"></i>
                            </div>
                        </td>
                    <?php } ?>

                </tr>
                <?php
                $tyr++;
            }
            ?>

            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results"><?php echo $this->lang->line('crm_there_are_no_opportunities_to_dispalay');?>.</div><!--THERE ARE NO OPPORTUNITIES TO DISPLAY-->
    <?php
}
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.select2').select2();
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
    function statuscheack(statusid,opportunityID,filtervalue,pageID,uriSegment)
    {
        if(statusid!='')
        {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'statusid':statusid},
            url: "<?php echo site_url('CrmLead/crm_opportunity'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['isexist'] == 1)
                {
                    swal({
                            title: "Are you sure?",
                            text: "You want to close this Opportunity!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                save_opportunitie_status(statusid,opportunityID,filtervalue,pageID,uriSegment)
                                $('.closedatehideshow').removeClass('hide');
                            } else {
                                $("#statusID").val(null).trigger("change");

                                $('.closedatehideshow').addClass('hide');
                            }
                        });
                }else if(data['isexist'] == 2)
                {
                    swal({
                            title: "Are you sure?",
                            text: "You want to Covert this Opportunity to Project!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                save_opportunitie_status(statusid,opportunityID,filtervalue,pageID,uriSegment);
                                $('.closedatehideshow').removeClass('hide');
                            } else {
                                $("#statusID").val(null).trigger("change");
                            }
                        });
                }else if(data['isexist'] == 3)
                {
                    swal({
                            title: "Are you sure?",
                            text: "You want to change the status as lost!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                save_opportunitie_status(statusid,opportunityID,filtervalue,pageID,uriSegment);
                                $('.closedatehideshow').removeClass('hide');
                            } else {
                                $("#statusID").val(null).trigger("change");
                            }
                        });
                }

                else if(data['isexist'] == 0)
                {
                    save_opportunitie_status(statusid,opportunityID,filtervalue,pageID,uriSegment);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    }
    function save_opportunitie_status(statusid,opportunityID,filtervalue,pageID,uriSegment)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'statusid':statusid,'opportunityID':opportunityID},
            url: "<?php echo site_url('CrmLead/save_opportunitie_statusid'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if(data[0] == 's'){
                    getOpportunityManagement_tableView(filtervalue,pageID,uriSegment);
                }else if(data[0] == 'e')
                {
                    $('#statusID_'+opportunityID).val(null).trigger("change");

                }

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
</script>