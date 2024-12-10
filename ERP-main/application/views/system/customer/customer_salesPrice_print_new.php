<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<style>
    .tdIn {
        width: 140px;
        padding: 2px;

    }

    .customerPriceAmount {
        display: inline;
        height: 24px;
        padding: 0px;
        padding-right: 2px;
        padding-left: 2px;
        font-size: 11px;
        width: 70px;
    }

    tr:hover, tr.selected {
        background-color: #E3E1E7;
        opacity: 1;
        z-index: -1;
    }

    .table-striped tbody tr.highlight td {
        background-color: #E3E1E7;
    }

    body table tr.selectedRow {
        background-color: #fff2e1;
    }

    tfoot {
        font-size: 11px;
    }

    .customerPriceAmount {
        text-align: right;
    }

    a.hoverbtn {
        margin-left: 5px;
    }

</style>


<div class="col-md-12" style="">
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="width:60%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px" src="<?php
                                echo mPDFImage.$this->common_data['company_data']['company_logo'];  ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:40%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').';  ?></strong></h3>
                                <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                                <h4><?php echo $this->lang->line('sales_maraketing_masters_customer_sales_prices_details');?><!--Customer Sales Price Details--></h4>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_document_code');?><!--Document Code--></strong></td>
                            <td><strong>:&nbsp;</strong></td>
                            <td><?php echo $master['documentSystemCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('sales_maraketing_masters_customer_code');?><!--Customer Code--></strong></td>
                            <td><strong>:&nbsp;</strong></td>
                            <td><?php echo $master['customer']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_document_date');?><!--Document Date--></strong></td>
                            <td><strong>:&nbsp;</strong></td>
                            <td><?php echo $master['documentDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_narration');?><!--Narration--></strong></td>
                            <td><strong>:&nbsp;</strong></td>
                            <td><?php echo $master['narration']; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <br>
    <hr style="margin-top: 0%">

    <div class="table-responsive">
        <table id="customerPriceDetails_view" style="width: 100%">
            <thead>
            <tr>
                <th style="min-width: 40px; border: 2px solid #ffffff; padding: 8px">#</th>
                <th style="border: 2px solid #ffffff; min-width: 150px; padding: 8px"><?php echo $this->lang->line('common_customer');?><!--Customer--></th>
                <?php
                if ($header) {
                    foreach ($header as $items) {?>
                        <th style="border: 2px solid #ffffff; padding: 8px"><span title="" rel="tooltip" class=""><?php echo $items['itemSystemCode'] . "<br>(" . $items['seconeryItemCode'] . ")<br>" . $items['itemDescription']?></span></th>
                    <?php    }
                } ?>
            </tr>
            </thead>

            <tbody>
            <?php

            if ($details) {
                $a = 1;
                $category = array_group_by($details, 'partyCategoryID');
            foreach ($category as $key => $partyCategoryID) {
                echo "<tr style='line-height: 24px; font-weight: bold;'><td colspan='13'><div style='color: darkblue'><strong>" . $key . "</strong></div></td></tr>";
                foreach ($partyCategoryID as $key => $det) {
                    echo '<tr>';
                    echo '<td>' . $a .  '</td>';
                    echo '<td>' . $det['customerSystemCode'] .  '</td>';

                    if ($header) {
                        $b = 1;
                        foreach ($header as $items) {
                            ?>
                            <td class="tdIn" style="text-align:center; <?php if(($b % 2) == 1) { echo 'background-color: #f2f2f2'; }?>">
                                <input class="form-control customerPriceAmount" type="text" name="customerPriceAmount"
                                       data-cpsAutoID="<?php echo $det['cpsAutoID']?>"
                                       data-customerAutoID="<?php echo $det['customerAutoID'] ?>"
                                       data-customerPriceID="<?php echo $det[$items['itemAutoID'] . '_customerPriceID'] ?>"
                                       data-itemID="<?php echo ($det[$items['itemAutoID'] . '_1'])?>"
                                       value="<?php echo number_format($det[$items['itemAutoID']],$this->common_data['company_data']['company_default_decimal'],'.', '')?>" readonly
                                >
                            </td>
                            <?php
                            $b++;
                        }
                    }

                    echo '</tr>';
                    $a++;
                }
            }

            } ?>

            </tbody>
        </table>
    </div>
    <br>
</div>

<script>
    $('#customerPriceDetails_view').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

    $('.hoverbtn').hide();
    $('table').on('click', 'tbody tr', function (event) {
        $(this).addClass('highlight').siblings().removeClass('highlight');
        $('.hoverbtn').hide();
        $(this).find(".hoverbtn").show();
    });

</script>