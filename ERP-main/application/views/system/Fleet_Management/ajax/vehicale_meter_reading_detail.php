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


<div class="row">
    <div class="form-group col-sm-3">
        <label class="title">Number :</label>
    </div>
    <div class="form-group col-sm-6">
        <label style="color: #333;"><?php echo $master['VehicleNo']?></label>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-3">
        <label class="title"> Model : </label>
    </div>
    <div class="form-group col-sm-6">
        <label style="color: #333;"><?php echo $master['model_description']?></label>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-3">
        <label class="title"> Brand : </label>
    </div>
    <div class="form-group col-sm-6">
        <label style="color: #333;"><?php echo $master['brand_description']?></label>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-3">
        <label class="title"> Description : </label>
    </div>
    <div class="form-group col-sm-6">
        <label style="color: #333;"><?php echo $master['vehDescription']?></label>
    </div>
</div>

<?php
if (!empty($detail)) {

?>
<br>
    <div class="table-responsive mailbox-messages" id="advancerecid">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">previous usage(KM/hrs)</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Amount usage(KM/hrs)</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Current usage(KM/hrs)</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Updated By</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Updated On</td>
            </tr>
           <?php
            $x = 1;
            foreach ($detail as $val) {
                ?>
            <tr>
                <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                <td class="mailbox-name"><a href="#" class="numberColoring"style="text-align: center;"><?php echo $val['previous_meter_reading']; ?></a></td>
                <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $val['reading_amount']; ?></a></td>
                <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $val['current_meter_reading']; ?></a></td>
                <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $val['createuser']; ?></a></td>
                <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $val['createDateTimeupcon']; ?></a></td>
            </tr>
               <?php
               $x++;
            } ?>
            </tbody>
           <tfoot>

            </tfoot>
        </table>
    </div>
   <?php
} else { ?>
    <br>
    <div class="search-no-results" id="advancerecid">NO RECORDS FOUND.</div><?php
} ?>
<script type="text/javascript">
    $("[rel=tooltip]").tooltip();
    $(document).ready(function () {
        $('.select2').select2();



    });
</script>
