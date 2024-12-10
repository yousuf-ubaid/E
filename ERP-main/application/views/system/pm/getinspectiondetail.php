<header class="head-title">
    <h2>Inspection Process</h2>
</header>
<div class="row" style="margin-top: 10px;">
    <div class="col-md-12">
        <div class="text-right m-t-xs">
            <button onclick="add_template_project('<?php echo $headerID ?>')" type="button"
                    class="btn btn-sm btn-primary">
                Add <span
                        aria-hidden="true"></span></button>

        </div>
    </div>
</div>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th class='theadtr'>#</th>
            <th style="width: 26%" class="text-center theadtr">Description</th>
            <th class='theadtr'>Template</th>
            <th class='theadtr'>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $num = 1;
        if (!empty($checklist_tempdetail)) {
            foreach ($checklist_tempdetail as $val) { ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-left"><?php echo $val['description']; ?></td>
                    <td class="text-left"><?php echo $val['checklistDescription']; ?></td>
                    <td class="text-right">   <span class="pull-right"><a href="#"
                                                                          onclick="fetch_assign_template(<?php echo $val['checklisttemplateID'] ?>,<?php echo $val['documentchecklistID'] ?>,<?php echo $val['documentchecklistmasterID'] ?>)"><span
                                        title="<?php echo($val['confirmedYN'] == 1 || $val['confirmedYN'] == 2 ? 'View' : 'Edit') ?>"
                                        rel="tooltip"
                                        class="<?php echo($val['confirmedYN'] == 1 || $val['confirmedYN'] == 2 ? 'glyphicon glyphicon-eye-open' : 'glyphicon glyphicon-pencil') ?>"></span></a>
                            &nbsp;|&nbsp;&nbsp;
                  <?php if ($val['confirmedYN'] != 1 && $val['confirmedYN'] != 2) { ?>
                      <a onclick="project_template_confirm(<?php echo $val['documentchecklistID'] ?>);"><span title=""
                                                                                                              rel="tooltip"
                                                                                                              class="glyphicon glyphicon-ok"
                                                                                                              data-original-title="Confirm"></span></a>
                      &nbsp;&nbsp;|&nbsp;&nbsp;
                  <?php } ?>

                            <a
                                    onclick="delete_project_template(<?php echo $val['documentchecklistID'] ?>);"><span
                                        title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"
                                        style="color:rgb(209, 91, 71);"></span></a></td>
                </tr>
                <?php
                $num++;
            }
        } else {
            echo '<tr class="danger"><td colspan="7" class="text-center">No Records Found</td></tr>';
        } ?>
        <!--No Records Found-->
        </tbody>
        
    </table>
</div>
