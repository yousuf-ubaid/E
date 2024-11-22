<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);

?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
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
    .actionicon{
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
    .headrowtitle{
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
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="8">
                    <div class="task-cat-upcoming-label">Campaigns</div><!--Latest Campaigns-->
                    <div class="taskcount"><?php echo sizeof($headercount) ?></div>
                </td>
            </tr>
            <tr>
               <!-- <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>-->
               <!--<td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">CODE</td><!--category-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_category');?></td><!--category-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_name');?></td><!--Name-->

                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: "><?php echo $this->lang->line('crm_start_and_end_date');?></td><!--Start & End Date-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_responsible');?></td><!--Responsible-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Created By</td><!--Name-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_status');?></td><!--Status-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center"><?php echo $this->lang->line('common_action');?></td><!--Action-->
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) {
                $textdecoration = '';
                if($val['status'] == 4){
                    $textdecoration = '';
                }
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#"
                                                onclick="fetchPage('system/crm/campaign_view','<?php echo $val['campaignID'] ?>','View Campaign','CRM')" class="<?php echo $textdecoration; ?>"><?php echo $val['documentSystemCode'] ?></a>
                    </td>
                 <!--   <td class="mailbox-name"><a href="#" class="numberColoring"><?php /*echo $x; */?></a></td>-->
                    <!--<td class="mailbox-star" width="5%"><a href="#"><i class="fa fa-star text-yellow" style="font-size: 16px"></i></a></td>-->
                    <td class="mailbox-star" width="10%"><span class="label" style="background-color:<?php echo $val['categoryBackGroundColor'] ?>; color: <?php echo $val['categoryTextColor'] ?>; font-size: 11px;"><?php echo $val['categoryDescription'] ?></span>
                    </td>
                    <td class="mailbox-name"><a href="#" onclick="fetchPage('system/crm/campaign_view','<?php echo $val['campaignID'] ?>','View Campaign','CRM')" class="<?php echo $textdecoration; ?>"><?php
                            $subject = $val['name'];
                            if (strlen($subject) > 30){
                                $str = substr($subject, 0, 27) . '...';
                            }else{
                                $str = $val['name'];
                            }
                            echo $str;
                            ?></a>
                    </td>
                    <td class="mailbox-name"><a href="#" class="<?php echo $textdecoration; ?>"><?php echo $val['startDate']." | ".$val['endDate']  ?></a>
                    </td>
                    <td class="mailbox-name"><a href="#" class="<?php echo $textdecoration; ?>"><?php
                            $companyID = $this->common_data['company_data']['company_id'];
                            $currentuser = current_userID();
                            $assignees = $this->db->query("SELECT srp_employeesdetails.Ename2 from srp_erp_crm_assignees JOIN srp_employeesdetails ON srp_erp_crm_assignees.empID = srp_employeesdetails.EIdNo where documentID = 1 AND companyID = ".$companyID." AND MasterAutoID = ".$val['campaignID']."")->result_array();
                            if(!empty($assignees)){
                                foreach($assignees as $row){
                                    echo $row['Ename2'].",";
                                }

                            }
                            $assigneesemp = $this->db->query("SELECT empID from srp_erp_crm_assignees where documentID = 1 AND companyID = ".$companyID." AND MasterAutoID = ".$val['campaignID']." AND empID = '{$currentuser}'")->row_array();

                            if(!empty($assigneesemp))
                            {
                                $assignuser = 1;
                            }else
                            {
                                $assignuser = 0;
                            }

                            ?></a>
                    </td>
                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">User: </strong><a class="link-person noselect" href="#"><?php echo $val['createdUsercampaign']; ?></a><br>
                                <strong class="contacttitle"> Date: </strong><a class="link-person noselect" href="#"><?php echo $val['createdDateTimecampaign']; ?></a>
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-star" width="10%"><span class="label"
                                                               style="background-color:<?php echo $val['statusBackGroundColor'] ?>; color:<?php echo $val['statusTextColor'] ?>; font-size: 11px;"><?php echo $val['statusDescription'] ?></span>
                    </td>
                    <td class="mailbox-attachment"><span class="pull-right">
                            <?php
                            if($val['isClosed'] == 1){ ?>
                                <div class="actionicon"><span class="glyphicon glyphicon-ok" style="color:rgb(255, 255, 255);" title="completed"></span</div>
                            <?php
                            }else{ ?>
                            <a href="#" onclick="edit_campaign('<?php echo $val['campaignID'] ?>','<?php echo $val['createdUserIDcampaign']?>','<?php echo $assignuser ?>')"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<a
                                onclick="delete_campaign(<?php echo $val['campaignID'] ?>);"><span title="Delete"
                                                                                                   rel="tooltip"
                                                                                                   class="glyphicon glyphicon-trash"
                                                                                                   style="color:rgb(209, 91, 71);"></span></a></span><!--Edit Campaign-->
                            <?php
                            }
                            ?>
                    </td>

                </tr>
                <?php
                $x++;
            }
            ?>

            </tbody>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results"><?php echo $this->lang->line('crm_there_are_no_campaign_to_dispaly');?>.</div><!--THERE ARE NO CAMPAIGN TO DISPLAY-->
    <?php
}

?>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>