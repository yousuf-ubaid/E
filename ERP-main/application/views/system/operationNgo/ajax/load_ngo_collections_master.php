<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
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

    .numberColoring {
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>
<?php
  if (!empty($master)) {

    ?>
      <br>
      <div class="table-responsive mailbox-messages">
          <table class="table table-hover table-striped">
              <tbody>
              <tr class="task-cat noselect" style="background: white;">
                  <td class="task-cat-upcoming" colspan="10">
                      <div class="task-cat-upcoming-label"><?php echo $this->lang->line('operationngo_latest_collections');?><!--Latest Collections--></div>
                      <div class="taskcount"><?php echo sizeof($master) ?></div>
                  </td>
              </tr>
              <tr>
                  <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>
                  <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_code');?><!--Code--></td>
                  <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('operationngo_donor');?><!--Donor--></td>
                  <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('common_document_date');?><!--Document Date--></td>


                  <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('operationngo_total_amount');?><!--Total Amount--></td>
                  <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('operationngo_confirm_status');?><!--Confirm Status--></td>
                  <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('operationngo_approval_status');?><!--Approval Status--></td>
                  <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>

              </tr>
              <?php
                $x = 1;
                foreach ($master as $val) {
                  ?>
                    <tr>
                        <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                        <td class="mailbox-name"><a href="#"
                                                    onclick="#"><?php
                              echo $val['documentSystemCode'];
                            ?></a>
                        </td>
                        <td class="mailbox-name"><a href="#"><?php echo $val['name']; ?></a></td>
                        <td class="mailbox-name"><a href="#"><?php echo $val['documentDate']; ?></a></td>


                        <td class="mailbox-name"><a href="#"><?php echo $val['transactionCurrency']. ' '.number_format($val['transactionAmount'],$val['transactionCurrencyDecimalPlaces'])  ?></a></td>

                        <td class="mailbox-name">
                          <?php if($val['confirmedYN']==1){
                            ?>
                              <span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></span>
                            <?php
                          }elseif($val['confirmedYN']==2){
?>
                              <span class="label" style="background-color:#ff784f; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('operationngo_referredback');?><!--Referred Back--></span>
                            <?php
                          }else{
                            ?>
                              <span class="label" style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed--></span>
                            <?php

                          }?>


                        </td>

                        <td class="mailbox-name">
                          <?php if($val['approvedYN']==1){
                            ?>
                            <a style="cursor: pointer" onclick="fetch_approval_user_modal('DC','<?php echo $val['collectionAutoId'] ?>')"><span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_approved');?><!--Approved--> <i class="fa fa-external-link" aria-hidden="true"></i></span></a>
                                 <?php
                          }else{

                              if($val['confirmedYN']==2){
                                  ?>
                                  <a style="cursor: pointer" onclick="fetch_approval_reject_user_modal('DC','<?php echo $val['collectionAutoId'] ?>')"> <span class="label" style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_not_approved');?><!--Not Approved--> <i class="fa fa-external-link" aria-hidden="true"></i></span></a>
                                <?php
                              }

                              else if($val['confirmedYN']==1){
                                  ?>
                                  <a  style="cursor: pointer" onclick="fetch_all_approval_users_modal('DC','<?php echo $val['collectionAutoId'] ?>')">  <span class="label" style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_not_approved');?><!--Not Approved --> <i class="fa fa-external-link" aria-hidden="true"></i> </a>  </span>
                                <?php }

                                else{ ?>



                              <span class="label" style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_not_approved');?><!--Not Approved -->  </span>
                            <?php

                          }}?>


                        </td>


                        <td class="mailbox-attachment"><span class="pull-right">
                                 <a onclick="attachment_modal('<?php echo $val['collectionAutoId'] ?>','Donor collection','DC',' <?php echo $val['confirmedYN'] ?>');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;

                                <?php if($val['confirmedYN']!=1) {
                                 ?>
                                <a href="#" onclick="fetchPage('system/operationNgo/create_donor_collections','<?php echo $val['collectionAutoId'] ?>','Edit Donor Collection')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                               <a target="_blank" href="<?php echo site_url('OperationNgo/load_donor_collection_confirmation/') . '/' . $val['collectionAutoId'] ?>"  ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                               &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_commitment_project(<?php echo $val['collectionAutoId'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>
                        </td>
                      <?php
                        }else{
                        if ($val['createdUserID'] == trim(current_userID()) and $val['approvedYN'] == 0 and $val['confirmedYN'] == 1) {
                            ?>
                     <a onclick="referback_donor_collection(<?php echo $val['collectionAutoId'] ?>);"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                    <?php
                        }
                        ?>
                          <a target="_blank" onclick="documentPageView_modal('DC','<?php echo $val['collectionAutoId']?>')"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                          <a target="_blank" href="<?php echo site_url('OperationNgo/load_donor_collection_confirmation/') . '/' . $val['collectionAutoId'] ?>"  ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                        <?php
                      }
                      ?>

                    </tr>
                  <?php
                  $x++;
                }
              ?>

              </tbody>
          </table><!-- /.table -->
      </div>
    <?php
  }
  else { ?>
      <br>
      <div class="search-no-results"><?php echo $this->lang->line('operationngo_there_are_no_rec_to_display');?><!--THERE ARE NO RECORDS TO DISPLAY.--></div>
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