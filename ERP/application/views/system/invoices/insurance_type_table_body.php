<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);


?>



<style>

    #insurance_type_tbl tbody tr.highlight td {
        background-color: #FFEE58;
    }

    .subheader {
        color: black;
        font-weight: bolder;
        font-size: 11px;
        background-color: #fbfbfb;
    }

    .subdetails {
        /* color: #4e4e4e;*/

        font-size: 11px;
    }

</style>
<div style="padding-bottom: 27px;">
<?php echo export_buttons('insurance_type_tbl', 'Insurance Type',true,false) ?>
</div>
<div class="table-responsive">
    <table id="insurance_type_tbl" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 15%"><?php echo $this->lang->line('sales_maraketing_masters_insurance_type')?><!--Insurance Type--></th>
            <!--<th style="min-width: 15%">Master Type</th>-->
            <th style="min-width: 15%"><?php echo $this->lang->line('sales_maraketing_masters_margin_percentage')?><!--Margin Percentage--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('sales_maraketing_masters_no_of_months')?><!--No Of Months--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_gl_code')?><!--GL Code--></th>
            <th style="min-width: 7%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
        </tr>
        </thead >
        <tbody >
        <?php
        if ($details) {
            foreach ($details as $account) {
                if ($account['masterTypeID'] == 0) {

                    ?>

                    <tr class="subheader">
                        <!--         <td></td>-->
                        <td style="padding-left: 10px"><?php echo $account['insuranceType'] ?></td>
                        <td><?php echo $account['marginPercentage'] ?></td>
                        <td><?php echo $account['noofMonths'] ?></td>
                        <td><?php echo $account['GLDescription'] ?></td>
                        <td><span class="pull-right"><span class="pull-right"><a href="#" onclick="sub_insurance_type(<?php echo $account['insuranceTypeID'] ?>)"><span title="Sub Type" rel="tooltip" class="glyphicon glyphicon-plus" ></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<span class="pull-right"><a href="#" onclick="openinsuranceeditmodel(<?php echo $account['insuranceTypeID'] ?>)"><span title="Edit" rel="tooltip" class="fa fa-pencil"></span></a> |&nbsp;&nbsp;<span class="pull-right"><a href="#" onclick="delete_insurancetype(<?php echo $account['insuranceTypeID'] ?>)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>&nbsp;&nbsp;</a></td>
                    </tr>
                    <?php
                }
                if ($details) {
                    foreach ($details as $subAccount) {
                        if ($subAccount['masterTypeID'] == $account['insuranceTypeID']) {
                            ?>
                            <tr class="subdetails">
                                <td style="padding-left: 40px"><?php echo $subAccount['insuranceType'] ?></td>
                                <td><?php echo $subAccount['marginPercentage'] ?></td>
                                <td><?php echo $subAccount['noofMonths'] ?></td>
                                <td><?php echo $subAccount['GLDescription'] ?></td>
                                <td><span class="pull-right"><a href="#" onclick="opensubinsuranceeditmodel(<?php echo $subAccount['insuranceTypeID'] ?>,<?php echo $subAccount['masterTypeID'] ?>)"><span title="Edit" rel="tooltip" class="fa fa-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<span class="pull-right"><a href="#" onclick="delete_insurancetype(<?php echo $subAccount['insuranceTypeID'] ?>)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>&nbsp;&nbsp;</a></td>
                            </tr>
                            <?php
                        }
                    }
                }

            }
        }
        ?>
        </tbody>
    </table>
</div>


<script>

    $('#insurance_type_tbl').on('click', 'tr', function (e) {
        $('#insurance_type_tbl').find('tr.highlight').removeClass('highlight');
        $(this).addClass('highlight');
    });


    function highlightSearch(searchtext) {
        $('#insurance_type_tbl tr').each(function () {
            $(this).removeClass('highlight');
        });
        if(searchtext !==''){
        $('#insurance_type_tbl tr').each(function () {
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