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
                                <i class="fa fa-file-text" aria-hidden="true"></i> <?php echo $this->lang->line('crm_contact_reports');?>
                            </div><!--Contact Reports-->
                        </div>
                        <div class="col-sm-4">
                            <span class="no-print pull-right" style="margin-top: -1%;margin-right: -5%;"> <a class="btn btn-danger btn-sm pull-right" style="padding: 4px 12px;font-size: 9px;" target="_blank" onclick="generateReportPdf('contact')">
                                <span class="fa fa-file-pdf-o" aria-hidden="true"> PDF
            </span> </a></span>
                            <span class="no-print pull-right" style="margin-top: -2%;margin-right: 1%;">
                                      <?php  echo export_buttons('contactrpt', 'Contact Report', True, false)?>
                              </span>
                      <!--  <span class="no-print pull-right" style="margin-top: -3%;margin-right: -7%;"> <a
                                class="btn btn-default btn-sm no-print pull-right" target="_blank" onclick="generateReportPdf('contact')">
                                <span class="glyphicon glyphicon-print" aria-hidden="true"></span> </a></span>-->
                        </div>
                    </div>
                </div>
                <div class="post-area">
                    <article class="page-content">
                        <div class="system-settings">

                            <div class="row">
                                <div class="col-sm-12" id="contactrpt">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th> <?php echo $this->lang->line('common_name');?></th><!--Name-->
                                            <th> <?php echo $this->lang->line('common_email');?></th><!--Email-->
                                            <th> <?php echo $this->lang->line('crm_mobile_no');?></th><!--Mobile No-->
                                            <th> <?php echo $this->lang->line('crm_home_no');?></th><!--Home No-->
                                            <th> <?php echo $this->lang->line('crm_role');?></th><!--Role-->
                                            <th> <?php echo $this->lang->line('crm_organization');?></th><!--Organization-->
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $x = 1;
                                        if (!empty($contact)) {
                                            foreach ($contact as $row) { ?>
                                                <tr>
                                                    <td><?php echo $x; ?></td>
                                                    <td><?php echo $row['fullname'] ?></td>
                                                    <td><?php echo $row['email'] ?></td>
                                                    <td><?php echo $row['phoneMobile'] ?></td>
                                                    <td><?php echo $row['phoneHome'] ?></td>
                                                    <td><?php echo $row['occupation'] ?></td>
                                                    <td><?php
                                                        if ($row['organization'] == '') {
                                                            echo $row['linkedorganization'];
                                                        } else {
                                                            echo $row['organization'];
                                                        }
                                                        ?></td>
                                                </tr>
                                                <?php
                                                $x++;
                                            }
                                        } else { ?>
                                            <tr>
                                                <td colspan="7" style="text-align: center"><?php echo $this->lang->line('common_no_records_found');?> </td><!--No Records Found-->
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


