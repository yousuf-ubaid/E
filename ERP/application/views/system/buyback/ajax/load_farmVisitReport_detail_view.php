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
    .tableHeader {
        border: solid 1px #e6e6e6 !important;
    }
    .center {
        text-align: center;
    }
</style>
<?php
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <thead style="border: 1px solid #da9393;">
            <tr>
                <th rowspan="2" class="headrowtitle tableHeader" style="border-bottom: solid 1px #f76f01;">#</th>
                <th rowspan="2" class="headrowtitle tableHeader" style="border-bottom: solid 1px #f76f01;">Age(Days)</th>
                <th rowspan="2" class="headrowtitle tableHeader" style="border-bottom: solid 1px #f76f01;">No Of Birds</th>
                <th colspan="2" class="headrowtitle tableHeader center" style="border-bottom: solid 1px #f76f01;">Mortality</th>
                <th rowspan="2" class="headrowtitle tableHeader" style="border-bottom: solid 1px #f76f01;">Total Feed(Kg)</th>
                <th rowspan="2" class="headrowtitle tableHeader" style="border-bottom: solid 1px #f76f01;">Av.Feed Per Bird</th>
                <th rowspan="2" class="headrowtitle tableHeader" style="border-bottom: solid 1px #f76f01;">Av.Body Weight</th>
                <th rowspan="2" class="headrowtitle tableHeader" style="border-bottom: solid 1px #f76f01;">FCR</th>
                <th rowspan="2" class="headrowtitle tableHeader" style="border-bottom: solid 1px #f76f01;">Remarks</th>
                <th rowspan="2" class="headrowtitle tableHeader" style="border-bottom: solid 1px #f76f01;"></th>
            </tr>
            <tr class="task-cat-upcoming">
                <th class="headrowtitle tableHeader center">No</th>
                <th class="headrowtitle tableHeader center">Percent</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $x = 1;
            foreach ($header as $val) {
                ?>
                <tr>
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star"><?php echo $val['age'] ?></td>
                    <td class="mailbox-star"><?php echo $val['numberOfBirds'] ?></td>
                    <td class="mailbox-star"><?php echo $val['mortalityNumber'] ?></td>
                    <td class="mailbox-star"><?php echo $val['mortalityPercent'] ?></td>
                    <td class="mailbox-star"><?php echo $val['totalFeed'] ?></td>
                    <td class="mailbox-star"><?php echo $val['avgFeedperBird'] ?></td>
                    <td class="mailbox-star"><?php echo $val['avgBodyWeight'] ?></td>
                    <td class="mailbox-star"><?php echo $val['fcr'] ?></td>
                    <td class="mailbox-star"><?php echo $val['remarks'] ?></td>
                    <td class="mailbox-attachment taskaction_td"><span class="pull-right">
                          <!--  <a onclick="edit_mortality_bird(<?php /*echo $val['farmerVisitDetailID'] */?>)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>-->&nbsp;&nbsp;<a
                                onclick="delete_farmVisitReport_detail(<?php echo $val['farmerVisitDetailID'] ?>)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>
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
    <div class="search-no-results">THERE ARE NO FARM VISIT REPORT DETAILS TO DISPLAY.</div>
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