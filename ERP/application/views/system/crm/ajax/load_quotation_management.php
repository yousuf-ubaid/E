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
    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 3px 0 3px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }
    .numberColoring{
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if (!empty($header)) {
    //print_r($header);
    ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="10">
                    <div class="task-cat-upcoming-label">Proposal </div><!--Latest Quotations-->
                    <div class="taskcount"><?php echo sizeof($header) ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_code');?></td><!--Code-->
                <?php
                if($page == "master"){ ?>
                    <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_opportunity');?></td><!--Opportunity-->
                <?php } ?>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('crm_organization');?> </td><!--Organization-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_document_date');?></td><!--Document Date-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">Expiry Date</td><!--Expire Date-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_currency');?></td><!--Currency-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_value');?></td><!--Value-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center;"><?php echo $this->lang->line('common_status');?></td><!--Status-->
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;text-align: center"><?php echo $this->lang->line('common_action');?></td><!--Action-->
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) {
                $org_name = ($val['fullname'] == '' || $val['fullname'] == null)?'-': $val['fullname'];
                
                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"
                                                onclick="#" ><?php
                            echo $val['quotationCode'];
                            ?></a>
                    </td>
                    <?php if($page == "master"){ ?>
                    <td class="mailbox-name"><a href="#"><?php echo $val['opportunityName']; ?></a></td>
                    <?php } ?>
                    <td class="mailbox-name"><a href="#"><?php echo $org_name; ?></a></td>
                    <td class="mailbox-name"><a href="#" ><?php echo $val['quotationDate'];  ?></a></td>
                    <td class="mailbox-name"><a href="#" ><?php echo $val['quotationExpDate'];  ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['CurrencyCode']; ?></a></td>
                    <td class="mailbox-name" style="text-align: right"><a href="#"><?php
                            $detailValue = $this->db->query("SELECT sum(((requestedQty*unittransactionAmount)-discountAmount)+taxAmount) AS totalValue FROM srp_erp_crm_quotationdetails WHERE contractAutoID = {$val['quotationAutoID']}")->row_array();
                            if(!empty($detailValue)){
                                    echo format_number($detailValue['totalValue'], 2);
                            }else {
                                echo number_format(0, 2);
                            }
                             ?>
                        </a></td>
                    <td class="mailbox-name" style="text-align: center">
                        <?php if (($val['confirmedYN'] == 1) && ($val['approvedYN'] != 1) && ($val['approvedYN'] != 2)  && ($val['approvedYN'] != 1) && ($val['approvedYN'] != 3)&& ($val['approvedYN'] != 1) && ($val['approvedYN'] != 4)&& ($val['approvedYN'] != 1) && ($val['approvedYN'] != 5)) { ?>
                            <span class="label"
                                  style="background-color:#00c0ef; color: #FFFFFF; font-size: 11px;"> [2] | Submitted to Customer</span><!--Confirmed-->

                        <?php } else if(($val['confirmedYN'] == 1)&&($val['approvedYN'] == 1)) {?>
                            <span class="label"

                                  style="background-color:#89de27; color: #FFFFFF; font-size: 11px;">[3] | Accepted by Customer</span>
                        <?php } else if(($val['confirmedYN'] == 1)&&($val['approvedYN'] == 2)) {?>
                            <span class="label"
                                  style="background-color:#f39c12; color: #FFFFFF; font-size: 11px;">[4] | Rejected by Customer</span>

                        <?php } else if(($val['confirmedYN'] == 1)&&($val['approvedYN'] == 3)) {?>
                            <span class="label"
                                  style="background-color:#00a65a; color: #FFFFFF; font-size: 11px;">[5] | Quotation Generated </span>

                        <?php } else if(($val['confirmedYN'] == 1)&&($val['approvedYN'] == 4)) {?>
                            <span class="label"
                                  style="background-color:#00a65a; color: #FFFFFF; font-size: 11px;">[5] | Salese Order Generated </span>
                        <?php }else if(($val['confirmedYN'] == 1)&&($val['approvedYN'] == 5)){?>
                            <span class="label"
                                  style="background-color:#00a65a; color: #FFFFFF; font-size: 11px;">[5] | Contract Generated </span>


                      <?php } else {?>
                            <span class="label"
                                  style="background-color:#dd4b39; color: #FFFFFF; font-size: 11px;">[1] | Open </span><!--Not Confirmed-->
                        <?php } ?>
                    </td>

                    <td class="mailbox-attachment"><span class="pull-right">
                    <?php if (($val['confirmedYN'] == 1) && ($val['approvedYN'] != 1) && ($val['approvedYN'] != 2)  && ($val['approvedYN'] != 1) && ($val['approvedYN'] != 3)&& ($val['approvedYN'] != 1) && ($val['approvedYN'] != 4)&& ($val['approvedYN'] != 1) && ($val['approvedYN'] != 5)) { ?>
                        <a target="_blank" onclick="get_estimate_quotation_link(<?php echo $val['quotationAutoID'] ?>)"><span title="" rel="tooltip" class="glyphicon glyphicon-envelope" data-original-title="Estimate Quotation Link"></span></a>
                    <?php } ?>
                            <?php
                            if(($val['confirmedYN'] == 1 && $val['approvedYN'] == 1) ){ ?>
                                <a href="#"
                                   onclick="generate_qut_so(<?php echo $val['quotationAutoID'] ?>)"><span
                                            title="Generate" rel="tooltip"
                                            class="glyphicon glyphicon-ok"></span></a>&nbsp;<a target="_blank" onclick="view_quotation_printModel_new_view(<?php echo $val['quotationAutoID'] ?>)"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>&nbsp;&nbsp;<a target="_blank" href="<?php echo site_url('crm/quotation_print_view/'). '/' .$val['quotationAutoID'] ?>"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a></span>
                                <?php
                            } else if (($val['confirmedYN'] == 1 && $val['approvedYN'] == 2)){?>
                            <a target="_blank" onclick="view_quotation_printModel_new_view(<?php echo $val['quotationAutoID'] ?>)"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>&nbsp;&nbsp;<a target="_blank" href="<?php echo site_url('crm/quotation_print_view/'). '/' .$val['quotationAutoID'] ?>"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a></span>

                        </span></a>
                        <?php
                        } else if (($val['confirmedYN'] == 1 && $val['approvedYN'] == 3) || ($val['confirmedYN'] == 1 && $val['approvedYN'] == 4)|| ($val['confirmedYN'] == 1 && $val['approvedYN'] == 5)){?>

                                   <?php if($val['approvedYN'] == 3){?>
                                    <a target="_blank" onclick="documentPageView_modal('QUT','<?php echo $val['erp_contractAutoID']?>')"><span title="" rel="tooltip" class="glyphicon glyphicon-list-alt" data-original-title="View Quotation"></span></a>
                                    <?php } else if($val['approvedYN'] == 4) {?>
                                        <a target="_blank" onclick="documentPageView_modal('SO','<?php echo $val['erp_contractAutoID']?>')"><span title="" rel="tooltip" class="glyphicon glyphicon-list-alt" data-original-title="View Sales Order"></span></a>
                                       <?php } else { ?>
                                        <a target="_blank" onclick="documentPageView_modal('CNT','<?php echo $val['erp_contractAutoID']?>')"><span title="" rel="tooltip" class="glyphicon glyphicon-list-alt" data-original-title="View Contract"></span></a>
                                          <?php }?>


                                    <a target="_blank" onclick="view_quotation_printModel_new_view(<?php echo $val['quotationAutoID'] ?>)"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>&nbsp;&nbsp;<a target="_blank" href="<?php echo site_url('crm/quotation_print_view/'). '/' .$val['quotationAutoID'] ?>"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                        &nbsp;
                            <div class="actionicon"><span class="glyphicon glyphicon-ok" style="color:rgb(255, 255, 255);" title="completed"></span></div>

                        </span></a>

                          <?php } else if($val['confirmedYN'] == 1  && $val['approvedYN'] == 0) {?>
                              <a target="_blank" onclick="view_quotation_printModel_new_view(<?php echo $val['quotationAutoID'] ?>)"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>&nbsp;&nbsp;<a target="_blank" href="<?php echo site_url('crm/quotation_print_view/'). '/' .$val['quotationAutoID'] ?>"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                         <?php } else { ?>

                              <?php if($crmedittype==1){?>
                                  <a href="#" onclick="fetchPage('system/crm/create_new_quotation','<?php echo $val['quotationAutoID'] ?>','Edit Quotation - <?php echo $val['quotationCode'] ?>', 6,0)"><span
                                              title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;
                          <?php }else { ?>
                                  <a href="#" onclick="fetchPage('system/crm/create_new_quotation','<?php echo $val['quotationAutoID'] ?>','Edit Quotation - <?php echo $val['quotationCode'] ?>', 4, <?php echo $val['opportunityID'] ?>)"><span
                                              title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;

                          <?php }?>

                            <?php /*}
                            */?>
                            <a target="_blank" onclick="view_quotation_printModel_new_view(<?php echo $val['quotationAutoID'] ?>)"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>&nbsp;&nbsp;<a target="_blank" href="<?php echo site_url('crm/quotation_print_view/'). '/' .$val['quotationAutoID'] ?>"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>&nbsp;&nbsp;<a
                                onclick="delete_crm_quotation(<?php echo $val['quotationAutoID'] ?>);"><span title="Delete"
                                                                                                   rel="tooltip"
                                                                                                   class="glyphicon glyphicon-trash"
                                                                                                   style="color:rgb(209, 91, 71);"></span></a></span>
                        <?php
                        }
                        ?>


                        <!-- Modal -->
                        <div class="modal fade" id="getLinkModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">                                        
                                        <h4 class="modal-title" id="myModalLabel2"> Estimate Quotation Link </h4>
                                    </div>
                                    <div class="modal-body" id="getLink" style="color: #696CFF;">
                                        //Estimate Quotation Link content here.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" id="closeGetLinkModal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>



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
    <div class="search-no-results"><?php echo $this->lang->line('crm_there_are_no_quotation_to_display');?> .</div><!--THERE ARE NO QUOTATION TO DISPLAY-->
    <?php
}

?>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
</script>

<script>
    function get_estimate_quotation_link(quotationAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'quotationAutoID': quotationAutoID},
            url: "<?php echo site_url('crm/get_estimate_quotation_link'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#getLink").html(data);
                $("#getLinkModal").modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
</script>

<script>
    $(document).ready(function(){
        $('#closeGetLinkModal').on('click', function(){
            $('#getLinkModal').modal('hide');
        });        
    });
</script>