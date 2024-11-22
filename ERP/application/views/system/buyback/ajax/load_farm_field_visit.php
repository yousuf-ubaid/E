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
</style>
<?php

if (!empty($batch)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">System Code</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Farmer</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Field Officer</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Document Date</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Batch Code</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Visit No</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center;">Confirmation</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center;">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($batch as $val) {

                ?>
                <tr>
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['fvrSystemCode'] ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['farmerName'] ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['fieldOfficer'] ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['documentDate'] ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['batchCode'] ?></td>
                    <td class="mailbox-star text-center" width="10%"><?php echo $val['visitNumber'] ?></td>
                    <td class="mailbox-name" style="text-align: center" width="10%">
                        <?php if ($val['fvrConfirmedYN'] == 1) { ?>
                            <span class="label"
                                  style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Confirmed</span>
                        <?php } else { ?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Confirmed</span>
                        <?php } ?>
                    </td>
                    <td class="mailbox-name" width="15%">
                        <span class="pull-right">
                           <?php if ($val['fvrConfirmedYN'] != 1) { ?>
                            <a onclick='attachment_modal(<?php echo $val['farmerVisitID'] ?>,"Farm Visit Report","BBFVR",<?php echo $val['fvrConfirmedYN'] ?>)'><span
                                    title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <a href="#"
                               onclick="fetchPage('system/buyback/create_new_farm_visit_report','<?php echo $val['farmerVisitID'] ?>','Edit Farm Visit Report','BUYBACK')"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                              <!-- <a target="_blank"
                                  href="<?php /*echo site_url('buyback/load_farmVisitReport_confirmation/') . '/' . $val['farmerVisitID'] */?>"><span
                                       title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>-->
                            <a onclick="load_printtemp(<?php echo $val['farmerVisitID']  ?>)"><span
                                        title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
                               &nbsp;&nbsp;|&nbsp;&nbsp;<a
                                onclick="delete_farmVisitReport(<?php echo $val['farmerVisitID'] ?>);"><span
                                    title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                    style="color:rgb(209, 91, 71);"></span></a>
                        </span>
                        <?php
                        } else {
                               if($val['isclosed'] == 1){
                                   echo '<span class="">Closed</span>&nbsp;&nbsp;|&nbsp;&nbsp;';
                               } else if ($val['fvrCreatedUserID'] == trim(current_userID())) {
                                   if(empty($val['farmerVisitDetailID']) || $policy['value'] == 0){
                                       ?>
                                       <a onclick="referback_farmVisitReport(<?php echo $val['farmerVisitID'] ?>);"><span
                                                   title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat"
                                                   style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                       <?php
                                   }
                            }
                            ?>
                            <a onclick='attachment_modal(<?php echo $val['farmerVisitID'] ?>,"Dispatch Note","BBDPN",<?php echo $val['fvrConfirmedYN'] ?>)'><span
                                    title="Attachment" rel="tooltip"
                                    class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <a target="_blank"
                               onclick="documentPageView_modal('BBFVR','<?php echo $val['farmerVisitID'] ?>')"><span
                                    title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                            <!--<a target="_blank"
                               href="<?php /*echo site_url('buyback/load_farmVisitReport_confirmation/') . '/' . $val['farmerVisitID'] */?>"><span
                                    title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>-->
                            <a onclick="load_printtemp(<?php echo $val['farmerVisitID']  ?>)"><span
                                        title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>
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
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="alert" role="alert" style="background: #9ab9f1">THERE ARE NO FARM VISITS TO DISPLAY. CLICK ON NEW FARM VISIT, TO CREATE NEW</div>
    <?php
}
?>
<div class="modal fade" id="print_temp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Farm Visit Report Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="farmerVisitID">

                <div class="row">
                    <div class="form-group col-sm-12">
                        <label>Print Option</label><!--Type-->
                        <?php echo form_dropdown('printSize', array('0' => 'Half Page', '1' =>'Full Page' ), 1, 'class="form-control select2" id="printSize" required'); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_farm_visit_report_temp()">Print</button>
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
    function load_printtemp(farmerVisitID)
    {
        $('#printSize').val(1);
        $('#farmerVisitID').val(farmerVisitID);
        $('#print_temp_modal').modal('show');
    }

    function print_farm_visit_report_temp(){
        var printtype =  $('#printSize').val();
        var farmerVisitID =   $('#farmerVisitID').val();

        if(farmerVisitID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('buyback/load_farmVisitReport_confirmation') ?>" +'/'+ farmerVisitID +'/'+ printtype +'/'+1);
        }
    }
</script>