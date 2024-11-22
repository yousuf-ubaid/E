<table class="table table-condensed" style="background-color: #f1f1f1;" width="100%">
    <tbody>
    <?php
    $x= 1;
    foreach ($mfq_subsegment as $val) { ?>
        <tr>
            <td style="width: 11%;color: black">&nbsp;</td>
            <td style="width: 20%;color: black"><?php echo $val['segmentCode']?></td>
            <td style="color: black"><?php echo $val['description']?></td>
            <td style="width: 33%;color: black"><?php echo '-'?></td>
            <td style="width: 50px;"> </td>
        </tr>
        <?php
        $x++;
    } ?>
    </tbody>
</table>
<script>
    $("[rel=tooltip]").tooltip();
</script>