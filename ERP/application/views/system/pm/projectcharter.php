<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<style>
    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        font-weight: 500;
        color: black;
        padding: 4px 10px 0 0;
    }
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
        color: #000000;
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

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
    }

    .numberColoring {
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }
</style>





<div class="row">
    <div class="col-md-12">
        <input type="hidden" name="headerID" id="headerID" value="<?php echo $headerID?>">
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2 md-offset-2">
                <label class="title">Description Of The Project</label>
            </div>
            <div class="form-group col-sm-4">
                <textarea class="form-control" rows="3" id="descriptionoftheproject" name="descriptionoftheproject"><?php echo $header['charterprojectDescription'] ?></textarea>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2 md-offset-2">
                <label class="title">Delayed By Client</label>
            </div>
            <div class="form-group col-sm-4">
                <textarea class="form-control" rows="3" id="delayedbyclient" name="delayedbyclient"><?php echo $header['delayedbyClient'] ?></textarea>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-2 md-offset-2">
                <label class="title">Delayed By Contractor</label>
            </div>
            <div class="form-group col-sm-4">
                <textarea class="form-control" rows="3" id="delayedbycontractor" name="delayedbycontractor"><?php echo $header['delayedbyContractor'] ?></textarea>
            </div>
        </div>
    </div>
</div>
<div class="row pull-right" style="margin-top: 10px;">
    <div class="form-group col-sm-12 md-offset-2">
        <button class="btn btn-primary submitWizard" onclick="save_project_charter()">Save</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <header class="head-title">
            <h2>Timeline For project</h2>
        </header>
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="open_project_timeline(<?php echo $headerID?>);"><i
                class="fa fa-plus"></i>Add Project Timeline
        </button>
    </div>
    <div class="col-md-12" style="margin-top: 1%">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class='thead'>
                <tr>
                    <th class='theadtr'>#</th>
                    <th class="text-left theadtr">Phase </th>
                    <th class='theadtr'>Planned Completion Date</th>
                    <th class='theadtr'>Actual Completion Date</th>
                    <th class='theadtr'>Action</th>
                </tr>
                </thead>
                <tbody>
                <tbody>
                <?php
                $num = 1;
                if (!empty($timelineforproject)) {
                    foreach ($timelineforproject as $val) { ?>
                        <tr>
                            <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                            <td class="text-left"><?php echo $val['phaseDescription'] ; ?></td>
                            <td class="text-left"><?php echo $val['plannedcompletionDate']; ?></td>
                            <td class="text-left"><?php echo $val['actualcompletionDate']; ?></td>
                            <td class="text-right"> <a href="#"
                                                       onclick="edit_phase('<?php echo $val['timelineID']?>');"><span
                                        title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;|&nbsp;<a
                                    onclick="delete_phase('<?php echo $val['timelineID']?>');"><span title="Delete" rel="tooltip"
                                                                                               class="glyphicon glyphicon-trash"
                                                                                               style="color:rgb(209, 91, 71);"></span></a></span></td>
                        </tr>
                        <?php
                        $num++;
                    }
                } else {
                    echo '<tr class="danger"><td colspan="5" class="text-center">No Records Found</td></tr>';
                } ?>
                <!--No Records Found-->
                </tbody>
                </tbody>

            </table>
        </div>
    </div>
</div>


<div class="row" style="margin-top: 2%">
    <div class="col-md-12">
        <header class="head-title">
            <h2>PROJECT TEAM</h2>
        </header>
    </div>
<div class="col-md-12 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="open_projectteam_add(<?php echo $headerID?>);"><i
                class="fa fa-plus"></i>Add Project Team
        </button>
</div>
    <div class="col-md-12" style="margin-top: 1%">
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class='thead'>
            <tr>
                <th class='theadtr'>#</th>
                <th class="text-left theadtr">Name</th>
                <th class='theadtr'>Organization</th>
                <th class='theadtr'>Role</th>
                <th class='theadtr'>Action</th>
            </tr>
            </thead>
            <tbody>
            <tbody>
            <?php
            $num = 1;
            if (!empty($detail)) {
                foreach ($detail as $val) { ?>
                    <tr>
                        <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                        <td class="text-left"><?php echo $val['empName'] ; ?></td>
                        <td class="text-left"><?php echo $val['Organization']; ?></td>
                        <td class="text-left"><?php echo $val['roleDescription']; ?></td>
                        <td class="text-right"> <a href="#"
                                                  onclick="edit_team('<?php echo $val['teamID'] ?>');"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;|&nbsp;<a
                                onclick="delete_team(<?php echo $val['teamID'] ?>);"><span title="Delete" rel="tooltip"
                                                                                           class="glyphicon glyphicon-trash"
                                                                                           style="color:rgb(209, 91, 71);"></span></a></span></td>
                    </tr>
                    <?php
                    $num++;
                }
            } else {
                echo '<tr class="danger"><td colspan="5" class="text-center">No Records Found</td></tr>';
            } ?>
            <!--No Records Found-->
            </tbody>
            </tbody>

        </table>
    </div>
    </div>
</div>
<script type="text/javascript">

    function save_project_charter()
    {
        var headerID = $('#headerID').val();
        var descriptionoftheproject = $('#descriptionoftheproject').val();
        var delayedbyclient = $('#delayedbyclient').val();
        var delayedbycontractor = $('#delayedbycontractor').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'headerID': headerID,'descriptionoftheproject':descriptionoftheproject,'delayedbyclient':delayedbyclient,'delayedbycontractor':delayedbycontractor},
            url: "<?php echo site_url('Boq/save_boq_charter'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);

            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

</script>