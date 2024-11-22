<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>

<?php
if (!empty($cmtteeMas)) {

    $committeeDesc = $cmteMasRow['CommitteeDes'];

    ?>
    <br>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td style="border-bottom: solid 1px #f76f01;" colspan="7">
                    <div style="display: inline;  text-align:center ;  color: #f76f01;font-weight: bold; margin-top: 5px; font-size: 15px;"><?php echo $this->lang->line('communityngo_Committee');?><!--Committee--></div>
                    <div style="display: inline-block;
            font-weight: normal;
            font-size: 14px;
            background-color: #eee;
            -moz-border-radius: 2px;
            -khtml-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            padding: 1px 3px 0 3px;
            line-height: 14px;
            margin-left: 8px;
            margin-top: 10px;
            vertical-align: text-bottom;
            box-shadow: inset 0 -1px 0 #ccc;
            color: #727272;"><?php echo $committeeDesc; ?></div>
                </td>
            </tr>
            <tr>
                <td  style="border-top: 1px solid #ffffff;">#</td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;"><?php echo $this->lang->line('communityngo_SubCommittees');?><!--Sub Committees--></td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;"><?php echo $this->lang->line('communityngo_region');?><!--Area / Mahalla--></td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;"><?php echo $this->lang->line('communityngo_CommitteeHead');?><!--Committee Head--></td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;"><?php echo $this->lang->line('CommunityNgo_famAddedDate');?><!--Added Date--></td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;"><?php echo $this->lang->line('communityngo_ExpiryDate');?><!--Expiry Date--></td>
                <td style="border-top: 1px solid #ffffff;font-size:11px;text-transform: uppercase;">Is Active</td>

            </tr>
            <?php

            $x = 1;

            foreach ($cmtteeMas as $val) {

                ?>
                <tr>
                    <td class="mailbox-name" style="font-weight: 600; color: saddlebrown;"><?php echo $x; ?></td>
                    <td class="mailbox-name" style="color: #469bda;"><?php echo $val['CommitteeAreawiseDes']; ?></td>
                    <td class="mailbox-name" style="color: #469bda;"><?php echo $val['stDescription']; ?></td>
                    <td class="mailbox-name" style="color: #469bda;"><?php echo $val['CName_with_initials']; ?></td>
                    <td class="mailbox-name" style="color: #469bda;"><?php echo $val['startDatet']; ?></td>
                    <td class="mailbox-name" style="color: #469bda;"><?php echo $val['endDatet']; ?></td>
                    <td class="mailbox-name"><a href="#">
                            <?php if($val['SbActive']==1){
                                ?>
                                <span data-toggle="tooltip" title="Enrolled" style="background-color: #009688; color: #f1f0f0;font-size:10px;" class="badge">Yes</span>
                                <?php
                            }else{
                                ?>
                                <span data-toggle="tooltip" title="Not Enrolled" style="background-color: #8bc34a; color: #f1f0f0;font-size:10px;" class="badge">No</span>
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
            &nbsp;
            <tfoot>
            <tr style="background: white;"><td  class="mailbox-name" style="border-top: solid 1px saddlebrown;font-size: 12px;" colspan="7">Total sub committee enrolled : <?php echo sizeof($cmtteeMas); ?></td></tr>
            </tfoot>

        </table><!-- /.table -->
    </div>
    <?php

}
else { ?>
    <br>

    <div><?php echo $this->lang->line('community_there_are_no_rec_to_display');?><!--THERE ARE NO RECORDS TO DISPLAY-->.</div>
    <?php
}

?>


<?php
