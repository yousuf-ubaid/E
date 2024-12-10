<?php

?>

<div class="table-responsive">
  <table class="table pre_job_checklist_area_tbl">
    <thead>
      <tr>
        <th scope="col">Checklist Name</th>
        <th scope="col">Status</th>
        <th scope="col">#</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($checklist as $value) { ?>
          <tr>
              <td><?php echo $value['checklist_name'] ?></td>
              <td><?php if($value['confirmed'] == 1){
                  echo '<span class="label label-success">&nbsp;</span>';
              }else{
                  echo '<span class="label label-danger">&nbsp;</span>';
              } ?></td>
              <td>
                  <a onclick="load_checklist_edit(<?php echo $value['header_id'] ?>)"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a> &nbsp;
                  <a onclick="print_checklist(<?php echo $value['header_id'] ?>)"><span title="" rel="tooltip" class="glyphicon glyphicon glyphicon-print" data-original-title="Print"></span></a> &nbsp;
                  <a onclick="get_external_link_checklist(<?php echo $value['header_id'] ?>)"><span title="" rel="tooltip" class="glyphicon glyphicon glyphicon-envelope" data-original-title="Print"></span></a>
                </td>
          </tr>
      <?php } ?>

    </tbody>
  </table>
</div>