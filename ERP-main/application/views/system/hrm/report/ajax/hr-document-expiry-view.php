<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('hrms_leave_management_lang', $primaryLanguage);

if($is_view == 'N'){ ?>
    <div class="table-responsive">
        <table style="width: 100%" border="0px">
            <tbody>
            <tr>
                <td style="width:40%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px"
                                     src="<?=mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:60%;" valign="top">
                    <table border="0px">
                        <tr>
                            <td colspan="2">
                                <h2>
                                    <strong><?=$this->common_data['company_data']['company_name']; ?></strong>
                                </h2>
                            </td>
                        </tr>
                        <tr>
                            <td><h4 style="margin-bottom: 0px"><?=$this->lang->line('hrms_reports_document_expiry_report'); ?></h4></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h5 style="margin-bottom: 0px">
                                    <?=(!empty($period))?$period: ''?>
                                </h5>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr>
    <br/>
<?php } ?>

<br/>

<div id="report-response" style="height: 450px;">
    <table class="<?=table_class()?>" id="rpt_tbl">
        <?php if($rpt_type == 'E'){ ?>
            <thead>
            <tr>
                <th class="theadtr">#</th>
                <th class="theadtr" style=""> <?= $this->lang->line('common_employee_name'); ?> </th>
                <th class="theadtr" style=""> <?= $this->lang->line('common_document'); ?> </th>
                <th class="theadtr" style="width: 105px"> <?= $this->lang->line('common_documents_no'); ?> </th>
                <th class="theadtr" style="width: 100px">  <?= $this->lang->line('common_issue_date'); ?> </th>
                <th class="theadtr" style="width: 105px"> <?= $this->lang->line('common_expire_date'); ?> </th>
                <th class="theadtr" style="width: 205px"> <?= $this->lang->line('common_issued_by'); ?> </th>
            </tr>
            </thead>

            <tbody>
            <?php
            $i = 1;
            foreach ($detail as $row) {
                $subType = ($row['sub_typesDes']) ? ' - ' . $row['sub_typesDes'] : '';

                echo '<tr>
                        <td style="text-align: right">' . $i . '</td>                                                                                                   
                        <td >' . $row['empName'] . '</td>
                        <td >' . $row['DocDescription'] . ' ' . $subType . '</td>
                        <td >' . $row['documentNo'] . '</td>
                        <td style="text-align: right">' . $row['issueDate'] . '</td>          
                        <td style="text-align: right">' . $row['expireDate'] . '</td>                                                   
                        <td style="">' . $row['issueDet'] . '</td>                                                   
                      </tr>';
                $i++;
            }
        }
        else{ ?>
            <thead>
            <tr>
                <th class="theadtr">#</th>
                <th class="theadtr" style=""> <?= $this->lang->line('common_employee_name'); ?> </th>
                <th class="theadtr" style=""> <?= $this->lang->line('common_relationship'); ?> </th>
                <th class="theadtr" style=""> <?= $this->lang->line('common_name'); ?> </th>
                <th class="theadtr" style=""> <?= $this->lang->line('common_document'); ?> </th>
                <th class="theadtr" style="width: 105px"> <?= $this->lang->line('common_expire_date'); ?> </th>
            </tr>
            </thead>

            <tbody>
            <?php
                $i = 1;
                foreach ($detail as $row) {

                    echo '<tr>
                            <td style="text-align: right">' . $i . '</td>                                                                                                   
                            <td >' . $row['empName'] . '</td>
                            <td >' . $row['relationship'] . '</td>                                                     
                            <td >' . $row['relName'] . '</td>                                                     
                            <td >' . $row['docType'] . '</td>                                                     
                            <td style="text-align: right">' . $row['expireDate'] . '</td>                                                  
                          </tr>';
                    $i++;
                }
        } ?>
        </tbody>
    </table>
</div>

<script>
    $('#rpt_tbl').tableHeadFixer({
        head: true,
        right: 0,
        'z-index': 10
    });
</script>
