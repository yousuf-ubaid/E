<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="width100p">
    <section class="past-posts">
        <div class="posts-holder settings">
            <div class="past-info">
                <div id="toolbar">
                    <div class="toolbar-title pull-left">
                        <i class="fa fa-file-text" aria-hidden="true"></i> <?php echo $this->lang->line('crm_opportunity_reports');?>
                    </div><!--Opportunity Reports-->

                    <div class="pull-right">
                           <span class="no-print pull-right" style="margin-top: -1%;margin-right: -5%;"> <a class="btn btn-danger btn-xs pull-right" style="" target="_blank" onclick="generateReportPdf('opportunity')">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF </a></span>
                        <span class="no-print pull-right" style="margin-top: -2%;margin-right: 1%;">
                                  <?php  echo export_buttons('opportunityrpt', 'Opportunity Report', True, false)?>
                          </span>
                        <span class="no-print pull-right" style="margin-top: -1%;margin-right: 1%;">
                                   <a href="#" type="button" class="btn btn-excel btn-xs pull-right" onclick="excel_Export()">
                                <i class="fa fa-file-excel-o"></i> Opportunities Detail
                                             </a>
                          </span>
                    </div>
                </div>
                <div class="post-area">
                    <article class="page-content">

                        <div class="system-settings">

                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-striped" id="opportunityrpt">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th style="text-align: left;"><?php echo $this->lang->line('common_name');?></th><!--Name-->
                                            <th style="text-align: left;"><?php echo $this->lang->line('crm_closing_date');?></th><!--Closing Date-->
                                            <th style="text-align: left;">User Responsible</th>
                                            <th style="text-align: left;"><?php echo $this->lang->line('common_status');?></th><!--Status-->
                                            <th  style="text-align: left;"><?php echo $this->lang->line('common_value');?></th><!--Value-->
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <?php

                                        $x = 1;
                                        $total = 0;
                                        $currency = '';
                                        $Grandtotal = 0;

                                        foreach ($opportunity as $val) {
                                            $opportunitynew[$val["CurrencyCode"]][] = $val;

                                        }
                                        if (!empty($opportunitynew)) {

                                            foreach ($opportunitynew as $key => $val1) {
                                                $subtotal = 0;

                                                foreach ($val1 as $key2 => $opportunityrep) {
                                                    $subtotal += $opportunityrep['transactionAmount'];
                                                    echo "<tr class='hoverTr'>";
                                                    echo "<td>" . $x . "</td>";
                                                    echo '<td>' . $opportunityrep['opportunityName'] . '</td>';
                                                    echo '<td>' . $opportunityrep['forcastCloseDate'] . '</td>';
                                                    echo '<td>' . $opportunityrep['responsiblePerson'] . '</td>';
                                                    echo '<td>' . $opportunityrep['statusDescription'] . '</td>';
                                                    echo '<td>' . format_number($opportunityrep['transactionAmount'], 2) . '</td>';
                                                    echo "</tr>";
                                                    $x++;

                                                }
                                                $Grandtotal +=$subtotal;
                                                echo "<tr>";
                                                echo "<td class='' colspan='4' style='font-weight: bold;'> </td>";
                                                echo "<td class=''  style='font-weight: bold;'>Total (".$opportunityrep['CurrencyCode'].")</td>";
                                                echo "<td class='reporttotal' align='left' style='font-weight: bold;'>".format_number($subtotal, 2)."<!--Net Balance--></td>";
                                                echo "</tr>";
                                            }



                                        }
                                        ?>


                                        </tbody>
                                        <br>
                                        <tfoot>

                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    function excel_Export() {
        var form = document.getElementById('frm_report_filter');
        form.target = '_blank';
        form.action = '<?php echo site_url('CrmLead/export_note_excel'); ?>';
        form.submit();
    }
</script>

