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
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityNgo', $primaryLanguage);
$date_format_policy = date_format_policy();
if (!empty($header)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Status</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('communityngo_zakat_ageGrp'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('communityngo_zakat_ageLimit'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('communityngo_zakat_points'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><?php echo $this->lang->line('communityngo_zakat_perAmount'); ?></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Total</td>

                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Action</td>
            </tr>
            <?php
            $x = 1;
            foreach ($header as $val) {

                ?>
                <tr>
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width="15%"><?php echo $val['EconStateDes']; ?></td>
                    <td class="mailbox-star" width="15%"><?php echo $val['AgeGroup']; ?></td>
                    <td class="mailbox-star" width="15%"><?php echo $val['AgeLimit']; ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['GrpPoints']; ?></td>
                    <td class="mailbox-star" width="10%"><?php echo format_number($val['ZakatAmount'], $this->common_data['company_data']['company_default_decimal']); ?></td>
                    <td class="mailbox-star" width="15%"><?php echo format_number($val['TotalPerZakat'], $this->common_data['company_data']['company_default_decimal']); ?></td>
                    <td class="mailbox-star" width="10%">
                     &nbsp;&nbsp;
                        <?php  if($val['isZakisActive'] == '1'){ ?>
                            <span><a onclick="edit_zakatSetup(<?php echo $val['proposalZaqathSetID'] ?>,<?php echo $val['proposalID'] ?>)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-edit" style=""></span></a></span>
&nbsp;&nbsp;
                            <span><a onclick="in_active_zaqath(<?php echo $val['proposalZaqathSetID'] ?>,<?php echo $val['proposalID'] ?>)"><span title="Inactive" rel="tooltip" class="glyphicon glyphicon-off" style="color:green;"></span></a></span>

                    <?php  }else{ ?>
                    <span><a onclick="active_zaqath(<?php echo $val['proposalZaqathSetID'] ?>,<?php echo $val['proposalID'] ?>)"><span title="Active" rel="tooltip" class="glyphicon glyphicon-off" style="color:red;"></span></a></span>
                    <?php  } ?>
                    </td>
                </tr>
                <?php
                $x++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
    <?php
}
?>
<script type="text/javascript">
    $('.extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });
</script>

<?php
