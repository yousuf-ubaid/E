<?php
$date_format_policy = date_format_policy();
?>

<div class="col-sm-6">
<div class="row">
    <div class="form-group col-sm-6">
        <label class="title">Leave Opening Balance  :</label>
    </div>
    <div class="form-group col-sm-2">
        <a onclick="show_leavedetail(1)"> <?php echo (($openingbalance['openingbalance']!='')? round(($openingbalance['openingbalance']-$taken_lastyear_leave),2) :0)?></a>

    </div>
</div>
<div class="row">
    <div class="form-group col-sm-6">
        <label class="title">Entitled During This Period :</label>
    </div>
    <div class="form-group col-sm-2">
        <a onclick="show_leavedetail(2)">  <?php echo round($entitiledduringthisperiod,2)?> </a>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-6">
        <label class="title">Total Leave Available :</label>
    </div>
    <div class="form-group col-sm-2">
        <?php echo round((($openingbalance['openingbalance']-$taken_lastyear_leave)+$entitiledduringthisperiod),2) ?>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-6">
        <label class="title">Utilized :</label>
    </div>
    <div class="form-group col-sm-2">
        <a onclick="show_leavedetail(3)">  <?php echo ($taken!='')? round($taken,2) :0?> </a>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-3" style="width: 50%;">
        <label class="title">Balance :</label>
    </div>
    <div class="form-group col-sm-2">
        <?php
        $taken = ($taken!='')?$taken:0;
        $balance_new = ((($openingbalance['openingbalance']-$taken_lastyear_leave)+$entitiledduringthisperiod)-$taken);
        echo round($balance_new,2) ;
        ?>
    </div>
</div>

</div>

<div class="col-sm-6" style="margin-top: -7%">
    <div class="row">
        <div>
            <label>Employee Leaves detailed Report</label>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-10">
            <label class="title">Leave Opening Balance  :</label>
        </div>
        <div class="form-group col-sm-2">
            <b><?php echo (($openingbalance['openingbalance']!='')? round( $openingbalance['openingbalance']-$taken_lastyear_leave,2):0)?></b>
        </div>
    </div>
    <?php
    if($openingbalance['openingbalance']-$taken_lastyear_leave!=0)
    {
        if(!empty($openingbalance_detail)){
            foreach ($openingbalance_detail as $val){?>
                <div class="row leaveopeningbal hide">
                    <div class="form-group col-sm-8">
                        <i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;<?php echo $val['openingBalance'] ?>
                    </div>
                    <div class="form-group col-sm-2">
                        <?php echo $val['daysEntitled']?>
                    </div>
                </div>
            <?php } }
    }
    ?>



    <div class="row">
        <div class="form-group col-sm-10 ">
            <label class="title">Entitled During This Period :</label>
        </div>
        <div class="form-group col-sm-2">
            <b><?php echo $entitiledduringthisperiod?></b>
        </div>
    </div>
    <?php if(!empty($accrued)){
        foreach ($accrued as $val) { ?>
            <div class="row entitleduringperiod hide" >
                <div class="form-group col-sm-8 ">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo ucwords(trim_value( $val['leaveaccrualMasterCode'].' '.$val['description'],28)) ?>
                </div>
                <div class="form-group col-sm-2">
                    <?php echo round( $val['entitle'],2)?>
                </div>
            </div>

        <?php } }?>

    <div class="row">
        <div class="form-group col-sm-10 ">
            <label class="title">Utilized :</label>
        </div>
        <div class="form-group col-sm-2">
            <b> <?php echo ($taken!='')?round($taken,2) :0?></b>
        </div>
    </div>
    <?php if(!empty($taken_leaves)){
        foreach ($taken_leaves as $val){?>
            <div class="row utilized hide">
                <div class="form-group col-sm-8 ">
                    &nbsp;<i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo ucwords(trim_value($val['documentCode'].' ('.  $val['startDate'].' - '.$val['endDate'].') ',28)) ?>
                </div>
                <div class="form-group col-sm-2">
                    <?php echo round($val['days'],2) ?>
                </div>
            </div>
        <?php } }?>

    <div class="row">
        <div class="form-group col-sm-10 " style="width: 83%">
            <b><label class="title">Balance :</label></b>
        </div>
        <div class="form-group col-sm-2">
            <b>   <?php
                $taken = ($taken!='')?$taken:0;
                $balance_new1 = ((($openingbalance['openingbalance']-$taken_lastyear_leave)+$entitiledduringthisperiod)-$taken);
                echo round($balance_new1,2) ;
                ?></b>
        </div>
    </div>

</div>
<script>
    $('#balance-span').html( '<?php echo $balance_new1; ?>');
    $("[rel=tooltip]").tooltip();
</script>