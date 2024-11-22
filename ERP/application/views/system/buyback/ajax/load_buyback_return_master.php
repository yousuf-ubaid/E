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
<?php if (!empty($return)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Return Code</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Details</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center;">Confirmed</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center;">Approved</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;text-align: center;">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($return as $val) {
                $status = '';
                if($val['isDeleted'] == 1){
                    $status = 'line-through';
                }
                ?>
                <tr style="text-decoration: <?php echo $status ?>;">
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width="15%"><?php echo $val['documentSystemCode'] ?></td>
                    <td class="mailbox-name" width="20%">
                        <div class="contact-box">
                            <div class="link-box"><strong class="contacttitle">Farm Name : </strong><a
                                    class="link-person noselect" href="#"><?php echo $val['farmerName'] ?></a>
                                <br>
                                <strong class="contacttitle">Return Date : </strong><a class="link-person noselect" href="#"><?php echo $val['documentDate'] ?></a>
                                <br>
                                <strong class="contacttitle">To : </strong><a class="link-person noselect" href="#"><?php echo $val['wareHouseLocation'] ?></a>
                            </div>
                        </div>
                    </td>
                    <td class="mailbox-name" style="text-align: center" width="10%">
                        <?php if ($val['confirmedYN'] == 1) { ?>
                            <span class="label"
                                  style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Confirmed</span>
                        <?php } else { ?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Confirmed</span>
                        <?php } ?>
                    </td>
                     <td class="mailbox-name" style="text-align: center" width="10%">
                        <?php if ($val['approvedYN'] == 1) { ?>
                            <span class="label"
                                  style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Approved</span>
                        <?php } else { ?>
                            <span class="label"
                                  style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Approved</span>
                        <?php } ?>
                    </td>

                    <td class="mailbox-name" width="10%"><span class="pull-right">
                             <a onclick='attachment_modal(<?php echo $val['returnAutoID'] ?>,"Return","BBDR",<?php echo $val['confirmedYN'] ?>)'>
                                    <span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;
                            <?php
                            if ($val['isDeleted'] == 1) { ?>
                                <a onclick='reOpen_contract(<?php echo $val['returnAutoID'] ?>)'><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat"
                                                                                                       style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <?php  }
                            if ($val['createdUserID'] == trim($this->session->userdata("empID")) && $val['approvedYN'] == 0 && $val['confirmedYN'] == 1 && $val['isDeleted'] == 0) { ?>
                                <a onclick="referback_buyback_return(<?php echo $val['returnAutoID'] ?>);"><span title="Refer Back" rel="tooltip"
                                                                                                                 class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <?php }
                            echo '<a target="_blank" onclick="documentPageView_modal(\'BBDR\',\'' . $val['returnAutoID'] . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

//                            echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('Buyback/load_buyback_return_conformation') . '/' . $val['returnAutoID'] . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
                            echo '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="load_printtemp('. $val['returnAutoID'] .')"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';


                            if ($val['confirmedYN'] != 1 && $val['isDeleted'] == 0) { ?>
                                &nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="#" onclick="fetchPage('system/buyback/create_buyback_return','<?php echo $val['returnAutoID'] ?>','Edit Return','BBDR')"><span
                                        title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>
                            <?php
                                echo '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $val['returnAutoID'] . ',\'Return\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
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
    <div class="alert" role="alert" style="background: #9ab9f1">THERE ARE NO RETURNS TO DISPLAY, PLEASE CLICK THE <B>CREATE RETURN</B> TO CREATE A NEW RETURN.</div>
    <?php
}
?>
<div class="modal fade" id="print_temp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Return Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="returnAutoID">

                <div class="row">
                    <div class="form-group col-sm-12">
                        <label>Print Option</label><!--Type-->
                        <?php echo form_dropdown('printSize', array('0' => 'Half Page', '1' =>'Full Page' ), 1, 'class="form-control select2" id="printSize" required'); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_retun_temp()">Print</button>
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
    function load_printtemp(returnAutoID)
    {
        $('#printSize').val(1);
        $('#returnAutoID').val(returnAutoID);
        $('#print_temp_modal').modal('show');
    }

    function print_retun_temp(){
        var printtype =  $('#printSize').val();
        var returnAutoID =   $('#returnAutoID').val();

        if(returnAutoID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('Buyback/load_buyback_return_conformation') ?>" +'/'+ returnAutoID +'/'+ printtype +'/'+1);
        }
    }
</script>






<?php
/**
 * Created by PhpStorm.
 * User: l
 * Date: 3/29/2019
 * Time: 2:11 PM
 */