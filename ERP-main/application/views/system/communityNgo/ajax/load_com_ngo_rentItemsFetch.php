<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
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

        .numberColoring {
            font-size: 13px;
            font-weight: 600;
            color: saddlebrown;
        }
    </style>
<?php
if (!empty($rentalMas)) {

    ?>
    <br>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat noselect" style="background: white;">
                <td class="task-cat-upcoming" colspan="10">
                    <div class="task-cat-upcoming-label"><?php echo $this->lang->line('CommunityNgo_latest_rentItem');?><!--Latest Rental Item--></div>
                    <div class="taskcount"><?php echo sizeof($rentalMas) ?></div>
                </td>
            </tr>
            <tr>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;">#</td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('communityngo_renItemType');?><!--Item Type--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('communityngo_rentItemCode');?><!--Item Code--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('communityngo_Description');?><!--Description--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('communityngo_CurrentStock');?><!--Current Stock--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('communityngo_rentPrice');?><!--Rental Price--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"><?php echo $this->lang->line('communityngo_com_member_header_Status');?><!--Status--></td>
                <td class="headrowtitle" style="border-top: 1px solid #ffffff;"></td>

            </tr>
            <?php

            $x = 1;
            foreach ($rentalMas as $val) {

                if($val['rentalItemType']==1){

                    $rentItemType ="Products / Goods";
                    $rtItemCode =$val['itemSystemCode'];
                    $rentItemDes =$val['itemDescription'];


                }else{
                    $rentItemType ="Fixed Assets";
                    $rtItemCode =$val['faCode'];
                    $rentItemDes =$val['assetDescription'];
                }

                ?>
                <tr>
                    <td class="mailbox-name"><a href="#" class="numberColoring"><?php echo $x; ?></a></td>
                    <td class="mailbox-name"><a href="#" onclick="#"><?php echo $rentItemType; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $rtItemCode; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $rentItemDes; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['currentStock'] .' '.$val['defaultUnitOfMeasure']; ?></a></td>
                    <td class="mailbox-name"><a href="#"><?php echo $val['RentalPrice']; ?></a></td>
                    <td class="mailbox-name">
                        <?php if($val['rentalStatus']==1){
                            ?>
                            <span class="label" style="background-color:#8bc34a; color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_active');?><!--Active--></span>
                            <?php
                        }else{
                            ?>
                            <span class="label" style="background-color: rgba(255, 72, 49, 0.96); color: #FFFFFF; font-size: 11px;"><?php echo $this->lang->line('common_in_active');?><!--Inactive--></span>
                            <?php

                        }?>

                    </td>

                    <td class="mailbox-attachment"><span class="pull-right">
                               <a onclick="edit_rentalSetup('<?php echo $val['rentalItemID'] ?>', this)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>
                             &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_rentalItm(<?php echo $val['rentalItemID'] ?>);"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>

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

}
else { ?>
    <br>

    <div class="search-no-results"><?php echo $this->lang->line('community_there_are_no_rec_to_display');?><!--THERE ARE NO RECORDS TO DISPLAY-->.</div>
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

<?php
