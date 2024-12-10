<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$secondary_code = getPolicyValues('SCAC', 'All');
?>
<style>

    #cTable tbody tr.highlight td {
        background-color: #FFEE58;
    }



</style>
<div style="padding-bottom: 27px;">
<?php echo export_buttons('cTable', 'Chart of Account',true,false) ?>
</div>
<table id="cTable" class="table " style="width: 100%">
    <thead>
    <tr>
        <!-- <th>Account</th>-->
        <th style="width: 200px"><?php echo $this->lang->line('common_system_code')?><!--System Code--></th>
        <th style="width: 150px"><?php echo $this->lang->line('config_secondary_code')?><!--Secondary Code--></th>
        <th><?php echo $this->lang->line('config_gl_descriprion')?><!--GL Description--></th>
        <th style="width: 50px;"><?php echo $this->lang->line('common_type')?><!--Type--></th>
        <th style="width: 100px;"><?php echo $this->lang->line('common_balance')?><!--Balance--></th>
        <th><?php echo $this->lang->line('common_status')?><!--Status--></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($header) {

        foreach ($header as $value) {
            ?>


            <tr class="header">
                <td colspan="3"><i class="fa fa-minus-square"
                                   aria-hidden="true"></i> <?php echo $value['CategoryTypeDescription'] ?></td>

                <td><?php echo $value['Type'] ?></td>

                <td colspan="3"></td>


            </tr>


            <?php
            if ($details) {
                foreach ($details as $account) {
                    if ($account['masterAccountYN'] == 1 && $account['accountCategoryTypeID'] == $value['accountCategoryTypeID']) {

                        ?>

                        <tr class="subheader">
                            <!--         <td></td>-->
                            <td style="padding-left: 30px"><i class="fa fa-minus"
                                                              aria-hidden="true"></i> <?php echo $account['systemAccountCode'] ?>
                            </td>
                            <td><?php echo $account['GLSecondaryCode'] ?></td>
                            <td><?php echo $account['GLDescription'] ?></td>
                            <td><?php echo $account['masterCategory'] ?></td>
                            <td style="text-align: right">0.00</td>
                            <td style="text-align: center"><?php if ($account['isActive'] == 1) {
                                    ?><span class="label label-success">&nbsp;</span><?php
                                } else {
                                    ?>
                                    <span class="label label-danger">&nbsp;</span>
                                    <?php
                                } ?></td>
                            <td><span class="pull-right"><a
                                        onclick="load_duplicate_chart_of_accont(<?php echo $account['GLAutoID'] ?>,1)"><span title="Replicate" rel="tooltip"
                                                                                                                   class="glyphicon glyphicon-duplicate"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
                                        onclick="link_chart_of_accont(<?php echo $account['GLAutoID'] ?>,1)"><span title="Link" rel="tooltip"
                                                                                                                    class="glyphicon glyphicon-link"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
                                        onclick="edit_chart_of_accont(<?php echo $account['GLAutoID'] ?>)"><span title="Edit" rel="tooltip"
                                            class="glyphicon glyphicon-pencil"></span></a></span></td>
                        </tr>
                        <?php
                        if ($details) {
                            foreach ($details as $subAccount) {
                                if ($subAccount['masterAutoID'] == $account['GLAutoID']) {
                                    ?>
                                    <tr class="subdetails">
                                        <!--    <td></td>-->
                                        <td style="padding-left: 60px"><?php echo $subAccount['systemAccountCode'] ?></td>
                                        <td><?php echo $subAccount['GLSecondaryCode'] ?></td>
                                        <td><?php echo $subAccount['GLDescription'] ?></td>
                                        <td><?php echo $subAccount['masterCategory'] ?></td>
                                        <td style="text-align: right">0.00</td>
                                        <td style="text-align: center"><?php if ($subAccount['isActive'] == 1) {
                                                ?><span class="label label-success">&nbsp;</span><?php
                                            } else {
                                                ?>
                                                <span class="label label-danger">&nbsp;</span>
                                                <?php
                                            } ?></td>
                                        <td><span class="pull-right"><a
                                                    onclick="load_duplicate_chart_of_accont(<?php echo $subAccount['GLAutoID'] ?>,0)"><span title="Replicate" rel="tooltip"
                                                                                                                                         class="glyphicon glyphicon-duplicate"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
                                                    onclick="link_chart_of_accont(<?php echo $subAccount['GLAutoID'] ?>,0)"><span title="Link" rel="tooltip"
                                                                                                                                class="glyphicon glyphicon-link"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
                                                    onclick="edit_chart_of_accont(<?php echo $subAccount['GLAutoID'] ?>)"><span title="Edit" rel="tooltip"
                                                        class="glyphicon glyphicon-pencil"></span></a></span></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                    }
                }
            }
        }

    }
    ?>
    </tbody>
</table>

<script>

    $('#cTable').on('click', 'tr', function (e) {
        $('#cTable').find('tr.highlight').removeClass('highlight');
        $(this).addClass('highlight');
    });


    function highlightSearch(searchtext) {
        $('#cTable tr').each(function () {
            $(this).removeClass('highlight');
        });
        if(searchtext !==''){
        $('#cTable tr').each(function () {
            if ($(this).find('td').text().toLowerCase().indexOf(searchtext.toLowerCase()) == -1) {

                $(this).removeClass('highlight');
            }
            else {
                $(this).addClass('highlight');
            }
        });
        }
    }


</script>