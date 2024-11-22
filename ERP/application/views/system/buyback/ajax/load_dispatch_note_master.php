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

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    .contacttitle {
        width: 170px;
        text-align: right;
        color: #525252;
        padding: 4px 10px 0 0;
    }

    .numberColoring {
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
                    <div class="task-cat-upcoming-label">Dispatch Notes</div>
                    <div class="taskcount"><?php echo sizeof($header) ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;"></td>
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;">Code</td>
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;">Detail</td>
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;">Total Value</td>
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;text-align: center">Confirmed</td>
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;text-align: center">Approved</td>
                <td class="headrowtitle" style="border-top: 1px solid #F76F01;text-align: center">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) {
                $status = '';
                if($val['isDeleted'] == 1){
                    $status = 'line-through';
                }
                ?>
                <tr style="text-decoration: <?php echo $status ?>;">
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['documentSystemCode']; ?></a></td>
                    <td class="mailbox-name">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">Farm Name : </strong><a
                                    class="link-person noselect" href="#"><?php echo $val['farmName'] ?></a><br><strong
                                    class="contacttitle">Dispatched Date : </strong><a class="link-person noselect" href="#"><?php echo $val['dispatchedDate'] ?></a><br><strong class="contacttitle">Batch ID : </strong><a class="link-person noselect" href="#"><?php echo $val['batchCode'] ?></a><br><strong class="contacttitle">Narration : </strong><a class="link-person noselect" href="#"><?php echo $val['Narration'] ?></a>
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-name">
                        <div class="arrow-steps clearfix">
                            <?php
                            $detail = $this->db->query("SELECT sum(totalTransferCost) as TransferTotal FROM srp_erp_buyback_dispatchnotedetails WHERE dispatchAutoID ={$val['dispatchAutoID']}")->row_array();
                            if (!empty($detail)) {
                                echo $val['detailCurrency'] . " : " . number_format($detail['TransferTotal'], 2);
                            } else {
                                echo $val['detailCurrency'] . " : 0.00";
                            }

                            ?>
                        </div>
                    </td>
                    <td class="mailbox-name" style="text-align: center">
                        <?php if ($val['confirmedYN'] == 1) { ?>
                            <span class="label"
                                  style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Confirmed</span>
                        <?php } else { ?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Confirmed</span>
                        <?php } ?>
                    </td>
                    <td class="mailbox-name" style="text-align: center">
                        <?php if ($val['approvedYN'] == 1) { ?>
                       <!--     <span class="label"
                                  style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Approved</span> -->
                            <a style="cursor: pointer"
                               onclick="fetch_approval_user_modal('BBDPN','<?php echo $val['dispatchAutoID'] ?>')"><span
                                    class="label"
                                    style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;">Approved <i
                                        class="fa fa-external-link" aria-hidden="true"></i></span></a>
                        <?php } else { ?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Approved</span>
                        <?php } ?>
                    </td>

                    <td class="mailbox-name" width="10%"><span class="pull-right">
                                 <?php if($val['confirmedYN'] != 1) { ?>

                            <?php
                            if ($val['isDeleted'] == 1) {
                                ?>
                                <a onclick='attachment_modal(<?php echo $val['dispatchAutoID'] ?>,"Dispatch Note","BBDPN",<?php echo $val['confirmedYN'] ?>)'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                               <!--<a target="_blank" href="<?php /*echo site_url('buyback/load_dispatchNote_confirmation/') . '/' . $val['dispatchAutoID']. '/' . $val['batchMasterID']  */?>"  ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>-->
                                <a onclick="load_printtemp(<?php echo $val['dispatchAutoID']  ?>,<?php echo $val['batchMasterID'] ?>)"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                                &nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="reOpen_dispatchnote(<?php echo $val['dispatchAutoID'] ?>);"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>
                                <?php
                            } else{
                            ?>
                            <a onclick='attachment_modal(<?php echo $val['dispatchAutoID'] ?>,"Dispatch Note","BBDPN",<?php echo $val['confirmedYN'] ?>)'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <a href="#" onclick="fetchPage('system/buyback/create_dispatch_note','<?php echo $val['dispatchAutoID'] ?>','Edit Dispatch Note','BUYBACK')"><span
                                      title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                            <!--<a target="_blank" href="<?php /*echo site_url('buyback/load_dispatchNote_confirmation/') . '/' . $val['dispatchAutoID']. '/' . $val['batchMasterID']  */?>"  ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>-->
                            <a onclick="load_printtemp(<?php echo $val['dispatchAutoID']  ?>,<?php echo $val['batchMasterID'] ?>)"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                            &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_dispatchnote(<?php echo $val['dispatchAutoID'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>

                        <?php
                        }
                        }else{

                            if ($val['createdUserID'] == trim(current_userID()) && $val['confirmedYN'] == 1 && $val['approvedYN'] != 1) {
                                ?>
                                <a onclick="referback_dispatchnote(<?php echo $val['dispatchAutoID'] ?>);"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                <?php
                            }
                            ?>
                            <a onclick='attachment_modal(<?php echo $val['dispatchAutoID'] ?>,"Dispatch Note","BBDPN",<?php echo $val['confirmedYN'] ?>)'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <a target="_blank" onclick="documentPageView_modal('BBDPN','<?php echo $val['dispatchAutoID']?>','<?php echo $val['batchMasterID']?>')"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                            <!--<a target="_blank" href="<?php /*echo site_url('buyback/load_dispatchNote_confirmation/') . '/' . $val['dispatchAutoID'] . '/' . $val['batchMasterID'] */?>"  ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>-->
                            <a onclick="load_printtemp(<?php echo $val['dispatchAutoID']  ?>,<?php echo $val['batchMasterID'] ?>)"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
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
    <div class="alert" role="alert" style="background: #9ab9f1">THERE ARE NO DISPATCH NOTES TO DISPLAY, PLEASE CLICK <b> NEW DISPATCH NOTE </b>TO CREATE NEW DISPATCH</div>
    <?php
}
?>
<div class="modal fade" id="print_temp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Dispatch Note Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="dispatchAutoID">
                <input type="hidden" id="batchMasterID">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label>Print Option</label><!--Type-->
                        <?php echo form_dropdown('printSize', array('0' => 'Half Page', '1' =>'Full Page' ), 1, 'class="form-control select2" id="printSize" required'); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_dispatch_note_temp()">Print</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });
    function load_printtemp(dispatchAutoID,batchMasterID)
    {
        $('#printSize').val(1);
        $('#dispatchAutoID').val(dispatchAutoID);
        $('#batchMasterID').val(batchMasterID);
        $('#print_temp_modal').modal('show');
    }

    function print_dispatch_note_temp(){
        var printtype =  $('#printSize').val();
        var dispatchAutoID =   $('#dispatchAutoID').val();
        var batchMasterID =   $('#batchMasterID').val();

        if(dispatchAutoID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('buyback/load_dispatchNote_confirmation') ?>" +'/'+ dispatchAutoID +'/'+batchMasterID+'/'+ printtype +'/'+1);
        }
    }
</script>