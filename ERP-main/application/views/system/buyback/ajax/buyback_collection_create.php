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
    .center {
        text-align: center;
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
if (!empty($collection)) { ?>
<div class="table-responsive mailbox-messages">
    <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="8">
                    <div class="task-cat-upcoming-label">Live Collections</div>
                    <div class="taskcount"><?php echo sizeof($collection) ?></div>
                </td>
            </tr>

            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Code</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Detail</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Confirmed</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Action</td>
            </tr>

            <?php
            $x = 1;
            foreach ($collection as $val) { ?>
                <tr>
                    <td class="mailbox-name" style="width: 5%"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name" style="width: 10%"><a href="#"><?php echo $val['collectionCode']; ?></a></td>
                    <td class="mailbox-name" style="width: 20%"><a href="#">

                            <div class="contact-box">
                                <div class="link-box"><strong class="contacttitle">Driver and helper : </strong><a
                                            class="link-person noselect" href="#"><?php echo $val['driverhelper']?></a><br><strong
                                            class="contacttitle">Document date  : </strong><a class="link-person noselect" href="#"> <?php echo $val['createdDate']?> </a><br><strong class="contacttitle">Narration : </strong><a class="link-person noselect" href="#"><?php echo $val['Narration']?>  </a>

                                    <a class="link-person noselect" href="#"> </a><br><strong class="contacttitle">Collection Total : </strong><a class="link-person noselect" href="#"><?php echo $val['collection']?></a>
                                </div>
                            </div>

                        </a></td>
                    <td class="mailbox-name" style="width: 20%"><a href="#">
                            <?php if ($val['confirmedYN'] == 1) { ?>
                                <span class="label"
                                      style="background-color: #8bc34a; color: #FFFFFF; font-size: 11px;">Confirmed</span>
                            <?php } else { ?>
                                <span class="label"
                                      style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;">Not Confirmed</span>
                            <?php } ?>
                        </a></td>
                    <td class="mailbox-name" style="width: 10%" >
                        <?php if ($val['confirmedYN'] != 1) { ?>
                            <a href="#" onclick="fetchPage('system/buyback/create_new_collection','<?php echo $val['collectionID'] ?>','Edit Live Collection','BUYBACK')"><span
                                        title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a> &nbsp;|&nbsp;
                      <?php }?>

                        <a target="_blank" onclick="documentPageView_modal('BBCR','<?php echo $val['collectionID']?>')"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>&nbsp;|&nbsp;  <!--<a target="_blank" href="<?php /*echo site_url('buyback/load_buyback_collection_confirmation/') . '/' . $val['collectionID'] */?>"  ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>-->
                        <a onclick="load_printtemp(<?php echo $val['collectionID']  ?>)"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>




                        </span>



                    </td>
                </tr>

                <?php
                $x++;
            } ?>
<?php }else {?>
    <br>
    <div class="alert" role="alert" style="background: #9ab9f1">THERE ARE NO COLLECTIONS TO DISPLAY, PLEASE CLICK THE <b>NEW COLLECTION</b> TO CREATE A NEW COLLECTION</div>
    <?php
}
?>
<div class="modal fade" id="print_temp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Live Collection Template</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="collectionID">

                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label>Print Option</label><!--Type-->
                                    <?php echo form_dropdown('printSize', array('0' => 'Half Page', '1' =>'Full Page' ), 1, 'class="form-control select2" id="printSize" required'); ?>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" onclick="print_live_collection_temp()">Print</button>
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
    function load_printtemp(collectionID)
    {
        $('#printSize').val(1);
        $('#collectionID').val(collectionID);
        $('#print_temp_modal').modal('show');
    }

    function print_live_collection_temp(){
        var printtype =  $('#printSize').val();
        var collectionID =   $('#collectionID').val();

        if(collectionID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('buyback/load_buyback_collection_confirmation') ?>" +'/'+ collectionID +'/'+ printtype +'/'+1);
        }
    }
</script>