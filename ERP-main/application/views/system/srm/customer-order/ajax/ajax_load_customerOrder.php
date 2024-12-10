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

    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .textClose {
        text-decoration: line-through;
        font-weight: 500;
        text-decoration-color: #3c8dbc;
    }

</style>
<?php
if (!empty($outputs)) {
    ?>
    <div class="tab-pane active" id="pdetail" style="margin-top:12px">
        <div class="row">
            <div class="col-md-12">
                <div class="animated">
                    <div class="">
                        <table class="table  table-striped table-condensed" id="srm_supplier_view"
                               style="background-color: #ffffff;width: 100%">
                            <tbody>
                            <tr class="task-cat noselect">
                                <td><strong>#</strong></td>
                                <!--<td><strong>Document ID </strong></td>-->
                                <td><strong>Name</strong></td>
                                <td><strong>Phone </strong></td>
                                <td><strong>Address </strong></td>
                                <td><strong>Confirmed </strong></td>
                                <!--<td><strong> Approved </strong></td>-->
                                <td></td>
                            </tr>


                            <?php
                            $i = 1;
                            foreach ($outputs as $output) { ?>
                                <tr>
                                    <td><?php echo $i;
                                        $i++ ?></td>
                                    <!--<td> <?php /*echo($output['customerOrderCode']); */ ?> </td>-->
                                    <!--<td> <?php /*echo($output['customerOrderCode']); */ ?> </td>-->
                                    <!--<td> <?php /*echo($output['documentID']); */ ?> </td>-->
                                    <td> <?php echo($output['contactPersonName']); ?>
                                    <td> <?php echo($output['contactPersonNumber']); ?> </td>
                                    <td> <?php echo($output['CustomerAddress']); ?> </td>
                                    <td>
                                        <?php
                                        if ($output['confirmedYN'] == 0) {
                                            echo '<span class="label label-default">Not Confirmed</span>';
                                        } else if ($output['confirmedYN'] == 1) {
                                            echo '<span class="label label-success">Confirmed</span>';
                                        }
                                        ?>
                                    </td>
                                    <!--<td>
                                        <?php
                                    /*                                        if ($output['approvedYN'] == 0) {
                                                                                echo '<span class="label label-default">Not Approved</span>';
                                                                            } else if ($output['approvedYN'] == 1) {
                                                                                echo '<span class="label label-success">Approved</span>';
                                                                            } else if ($output['approvedYN'] == 2) {
                                                                                echo '<span class="label label-danger">rejected</span>';
                                                                            }
                                                                            */ ?>
                                    </td>-->
                                    <td>
                        <span class="pull-right">

                            <a href="#"
                               onclick="fetchPage('system/crm/create_lead','<?php echo $val['leadID'] ?>','Edit Lead','CRM')"><span
                                    title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<a
                                onclick="delete_lead(<?php echo $val['leadID'] ?>);"><span title="Delete" rel="tooltip"
                                                                                           class="glyphicon glyphicon-trash"
                                                                                           style="color:rgb(209, 91, 71);"></span></a>
                        </span>

                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php


} else { ?>
    <br>
    <div class="search-no-results">No Records Found.</div>
    <?php
}
?>

