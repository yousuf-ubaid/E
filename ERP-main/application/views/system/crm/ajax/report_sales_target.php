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
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="toolbar-title">
                                <i class="fa fa-file-text" aria-hidden="true"></i> Sales Target Report
                            </div><!--Task Reports-->
                        </div>                       
                    </div>
                </div>
                <div class="post-area">
                    <article class="page-content">

                        <div class="system-settings">

                            <div class="row">
                                <div class="col-sm-12 sales_target_report" id="taskreport">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th rowspan="2" colspan="1" class="align-left">
                                                Segments
                                            </th>
                                            <th rowspan="2" colspan="1" class="align-left">
                                                Products
                                            </th>
                                            <th rowspan="2" colspan="1"  class="align-left">
                                                Employee
                                            </th>
                                            <th rowspan="1" colspan="2" class="bg-1">
                                                Target
                                            </th>
                                            <th rowspan="1" colspan="2" class="bg-2">
                                                Achieved
                                            </th>
                                            <th rowspan="1" colspan="4" class="bg-3" >
                                                Variance
                                            </th>
                                            <th rowspan="1" colspan="2" class="bg-1">
                                                In Opportunity
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="bg-1">Unit</th>
                                            <th class="bg-1">Value</th>
                                            <th class="bg-2">Unit </th>
                                            <th class="bg-2">Value</th>
                                            
                                            <th class="bg-3">Unit</th>
                                            <th class="bg-3">%</th>
                                            <th class="bg-3">Value</th>
                                            <th class="bg-3">%</th>

                                            <th class="bg-1">Unit</th>
                                            <th class="bg-1">Value</th>
                                        </tr>

                                        </thead>
                                        <tbody>
                                        <?php
                                        $x = 1;
                                        if (!empty($sales_target)) {

                                           
                                            foreach ($sales_target as $row) {  ?>
                                                <tr>
                                                <td><?php echo $row['description']; ?></td>
                                                    <td><?php echo $row['productName']; ?></td>
                                                    <td><?php echo $row['employeeName']; ?></td>
                                                    <td class="align-right"><?php echo $row['units']; ?></td>
                                                    <td class="align-right"><?php echo number_format($row['targetValue']);  ?></td>
                                                    <td class="align-right"><?php echo $row['achieved_units']; ?></td>
                                                    <td class="align-right"><?php echo number_format($row['acheivedValue']); ?></td>
                                                    <td class="align-right"><?php echo ($row['achieved_units']-$row['units']); ?></td>
                                                    <?php if($row['units'] > 0) { ?>
                                                        <td class="align-center"><?php echo number_format((($row['achieved_units']/$row['units']))*100,2);  ?>%</td>
                                                    <?php } ?>
                                                    <?php if($row['targetValue'] > 0) { ?>
                                                        <td class="align-right"><?php echo number_format(($row['acheivedValue'] - $row['targetValue'])); ?></td>
                                                    <?php } ?>
                                                    <td class="align-center"><?php echo round((($row['acheivedValue'] / $row['targetValue']) *100),2); ?>%</td>
                                                    <!--<td style="text-align: center"><?php /*echo $row['progress']." %" */?></td>-->
                                                    <td><?php echo $row['count']; ?></td>
                                                    <td><?php echo number_format($row['val']); ?></td>
                                                </tr>
                                        <?php  } ?>                                           
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="6" style="text-align: center"><?php echo $this->lang->line('common_no_records_found');?> </td><!--No Records Found -->
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
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

