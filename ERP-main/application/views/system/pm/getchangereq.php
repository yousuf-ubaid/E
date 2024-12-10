<?php
$date_format_policy = date_format_policy();
?>

<br>
<header class="head-title">
        <h2>Change Requests</h2>
</header>
<div class="row" style="margin-top: 10px;">
    <div class="col-md-12">
        <div class="text-right m-t-xs">
            <button onclick="open_change_req_modal(<?php echo $headerID?>)" type="button" class="btn btn-sm btn-primary">
                Add Change Request  <span  aria-hidden="true"></span></button>

        </div>
    </div>
</div>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th class='theadtr'>#</th>
            <th  class="text-left theadtr">CR</th>
            <th  class='theadtr'>Type of CR</th>
            <th  class='theadtr'>Submitter Name</th>
            <th  class='theadtr'>Brief Description of request</th>
            <th  class='theadtr'>Date Submitted</th>
            <th  class='theadtr'>Date Required</th>
            <th  class='theadtr'>Priority</th>
            <th  class='theadtr'>Reason for change</th>
            <th  class='theadtr'>Assumptions and Notes</th>
            <th  class='theadtr'>Comments</th>
            <th  class='theadtr'>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($changereq)) {
            foreach ($changereq as $val) { ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-left"><?php echo $val['crcode']; ?></td>
                    <td class="text-left"><?php echo $val['typeofcr']; ?></td>
                    <td class="text-left"><?php echo $val['submittername']; ?></td>
                    <td class="text-left"><?php echo $val['descriptionofrequest']; ?></td>
                    <td class="text-left"><?php echo $val['datesubmitted']; ?></td>
                    <td class="text-left"><?php echo $val['daterequired']; ?></td>
                    <td class="text-left"><?php echo $val['priority']; ?></td>
                    <td class="text-left"><?php echo $val['reasonforchange']; ?></td>
                    <td class="text-left"><?php echo $val['assumptionsandnotes']; ?></td>
                    <td class="text-left"><?php echo $val['commentschangereq']; ?></td>

                    <td class="text-right">   <span class="pull-right">
                             <a href="#" onclick="edit_changereq(<?php echo $val['requestID'] ?>)"><span
                                     title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|
                            &nbsp;&nbsp;<a
                                onclick="delete_change_req(<?php echo $val['requestID'] ?>);"><span
                                    title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                    style="color:rgb(209, 91, 71);"></span></a> </td>
                </tr>
                <?php
                $num++;
            }
        } else {
            echo '<tr class="danger"><td colspan="12" class="text-center">No Records Found</td></tr>';
        } ?>
        <!--No Records Found-->
        </tbody>

    </table>
</div>
<br>
<br>
<div class="row">
    <div class="col-md-12">
        <div id="attachments_view_changerequest"> </div>
    </div>
</div>
<br>
<header class="head-title">
    <h2>initial analysis</h2>
</header>
<div class="row" style="margin-top: 10px;">
    <div class="col-md-12">
        <div class="text-right m-t-xs">
            <button onclick="open_initial_analysis_modal(<?php echo $headerID?>,)" type="button" class="btn btn-sm btn-primary">
                Add Initial Analysis  <span  aria-hidden="true"></span></button>

        </div>
    </div>
</div>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th class='theadtr'>#</th>
            <th  class="text-left theadtr">CR Code</th>
            <th  class="text-left theadtr">Category</th>
            <th  class="text-left theadtr">Sub category</th>
            <th  class='theadtr'>Hour Imapact</th>
            <th  class='theadtr'>Duration Imapact</th>
            <th  class='theadtr'>Schedule Impact</th>
            <th  class='theadtr'>Cost Impact</th>
            <th  class='theadtr'>Comments</th>
            <th  class='theadtr'>Recommendations</th>
            <th  class='theadtr'>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($inialanalysis)) {
            foreach ($inialanalysis as $val) { ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-left"><?php echo $val['crcode']; ?></td>
                    <td class="text-left"><?php echo $val['categoryDescription']; ?></td>
                    <td class="text-left"><?php echo $val['description']; ?></td>
                    <td class="text-left"><?php echo $val['hourimpact']; ?></td>
                    <td class="text-left"><?php echo $val['durationimpact']; ?></td>
                    <td class="text-left"><?php echo $val['scheduleimpact']; ?></td>
                    <td class="text-left"><?php echo $val['costimpact']; ?></td>
                    <td class="text-left"><?php echo $val['commentsinitial']; ?></td>
                    <td class="text-left"><?php echo $val['recommendations']; ?></td>

                    <td class="text-right">   <span class="pull-right">
                            &nbsp;&nbsp;<a
                                    onclick="delete_change_req(<?php echo $val['requestID'] ?>);"><span
                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                        style="color:rgb(209, 91, 71);"></span></a> </td>
                </tr>
                <?php
                $num++;
            }
        } else {
            echo '<tr class="danger"><td colspan="12" class="text-center">No Records Found</td></tr>';
        } ?>
        <!--No Records Found-->
        </tbody>

    </table>
</div>
<br>

<header class="head-title">
    <h2>change control board</h2>
</header>
<div class="row" style="margin-top: 10px;">
    <div class="col-md-12">
        <div class="text-right m-t-xs">
            <button onclick="open_decision_modal(<?php echo $headerID?>)" type="button" class="btn btn-sm btn-primary">
                Add  <span  aria-hidden="true"></span></button>

        </div>
    </div>
</div>

<br>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th class='theadtr'>#</th>
            <th  class="text-left theadtr">CR Code</th>
            <th  class='theadtr'>Decision</th>
            <th  class='theadtr'>Decision Date</th>
            <th  class='theadtr'>Decision Explanation</th>
            <th  class='theadtr'>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        $isexit = 0;
        $approvallevelid = ' ';
        if (!empty($changecontrolboard)) {
            foreach ($changecontrolboard as $val) {
                $reqID =fetch_changerequestboardapproval($val['requestID']);
                $isexit = (!empty($reqID['requestID'])?1:0);
                $approvallevelid = ($reqID['approvalLevelID']);
                ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-left"><?php echo $val['crcode']; ?></td>
                    <td class="text-center">
                        <?php if($val['decision']==1  && $val['approvedYN']==1){ ?>
                        <span class="label" style="background-color:#74b86a; color: #ffffff; font-size: 11px;">Approved</span>
                        <?php }else if($val['decision']==2  && $val['approvedYN']==1){?>
                            <span class="label" style="background-color:#9a482f; color: #ffffff; font-size: 11px;">Approved With Conditions</span>

                        <?php }else if($val['decision']==3){?>
                            <span class="label" style="background-color:#dd4b39; color: #FFFFFF; font-size: 11px;">Rejected</span>
                        <?php }else if($val['decision']==4){?>
                            <span class="label" style="background-color:#00c0ef; color: #FFFFFF; font-size: 11px;">More Info</span>

                        <?php }else if($val['decision']==5 && $val['approvedYN']==1){?>
                            <span class="label" style="background-color:#74b86a; color: #ffffff; font-size: 11px;">Confirmed And Approved</span>

                        <?php }else if($val['decision']==6 ){?>
                            <span class="label" style="background-color:#ff5722; color: #ffffff; font-size: 11px;">Sent For Approval</span>
                        <?php }else if(($val['confirmedYN']==1 && $val['approvedYN']!=1)){?>
                            <span class="label" style="background-color:#ff5722; color: #ffffff; font-size: 11px;">Sent For Approval</span>
                        <?php }?>
                    </td>
                    <td class="text-left"><?php echo $val['decisiondate']; ?></td>
                    <td class="text-left"><?php echo $val['decisionexplanation']; ?></td>

                    <td class="text-right">   <span class="pull-right">
                            <?php if($val['decision']==4 || $val['decision']==''||$val['decision']==0){?>
                            <a href="#" onclick="edit_changereqcontrolboard(<?php echo $val['requestID'] ?>,<?php echo $headerID ?>,<?php echo $isexit?>,<?php echo $approvallevelid?>)"><span
                                         title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|
                            &nbsp;&nbsp;<a
                                    onclick="delete_change_req(<?php echo $val['requestID'] ?>);"><span
                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                        style="color:rgb(209, 91, 71);"></span></a> </td>
                    <?php }?>
                    <?php if(($val['decision']==6)){?>
                        <a href="#" onclick="edit_changereqcontrolboard(<?php echo $val['requestID'] ?>,<?php echo $headerID ?>,<?php echo $isexit?>,<?php echo $approvallevelid?>)"><span
                                title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>
                    <?php }?>

                </tr>
                <?php
                $num++;
            }
        } else {
            echo '<tr class="danger"><td colspan="12" class="text-center">No Records Found</td></tr>';
        } ?>
        <!--No Records Found-->
        </tbody>

    </table>
</div>
<script type="text/javascript">
    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {

    });
</script>