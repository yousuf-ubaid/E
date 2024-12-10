<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>

<table class="<?php echo table_class()?>" id="rpt-tb">
    <thead>
    <tr>
        <th>#</th>
        <th><?=$this->lang->line('common_emp_no')?></th>
        <th><?=$this->lang->line('common_employee_name')?></th>
        <th><?=$this->lang->line('common_employee')?> %</th>
        <th><?=$this->lang->line('common_employee')?> </th>
        <th><?=$this->lang->line('common_employer')?> %</th>
        <th><?=$this->lang->line('common_employer')?> </th>
        <th><?=$this->lang->line('common_total')?> </th>
    </tr>
    </thead>

    <tbody>
    <?php
    $employee_tot = 0; $employer_tot = 0; $total = 0;
    if(!empty($SSO_data)){
        foreach ($SSO_data as $key=>$row){
            $line_total = round($row['employee_am'], $dPlace ) + round($row['employer_am'], $dPlace );
            $total += $line_total;
            $employee_tot += round($row['employee_am'], $dPlace );
            $employer_tot += round($row['employer_am'], $dPlace );

            echo '<tr>
                      <td>'.($key+1).'</td>
                      <td>'.$row['ECode'].'</td>
                      <td>'.$row['nameWithIn'].'</td>
                      <td align="right">'.$employee_per.' %</td>
                      <td align="right">'.number_format($row['employee_am'], $dPlace ).'</td>                     
                      <td align="right">'.$employer_per.' %</td>
                      <td align="right">'.number_format($row['employer_am'], $dPlace ).'</td>
                      <td align="right">'.number_format($line_total, $dPlace ).'</td>
                  </tr>';
        }
    }
    ?>
    </tbody>

    <?php
    if(!empty($SSO_data)){
        echo '<tfoot>
                <tr>
                    <td colspan="3">'.$this->lang->line('common_grand_total').'</td>
                    <td></td>
                    <td align="right">'.number_format($employee_tot, $dPlace ).'</td>
                    <td></td>
                    <td align="right">'.number_format($employer_tot, $dPlace ).'</td>
                    <td align="right">'.number_format($total, $dPlace ).'</td>
                </tr>
              </tfoot>';
    }
    ?>
</table>

<script>
    $('#rpt-tb').DataTable({
        "pageLength": 100,
        "aaSorting": [[1, 'asc']],
        "columnDefs": [ {
            "targets": [0,7],
            "orderable": false
        } ],
        "fnDrawCallback": function (oSettings) {
            var tmp_i = oSettings._iDisplayStart;
            var iLen = oSettings.aiDisplay.length;
            var x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                x++;
            }

            $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>');

            $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>');
            $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>');

        },
    });
</script>

<?php
