<?php
$rpt_type = 'E';
?>

<div style="margin-left: 20px; font-size: 12px">Your following documents are about to expire.</div>
<br/>

<?php if(!empty($docs)){ ?>
    <table style="border-collapse: collapse; width: 780px !important;font-size: 12px;">
        <thead>
        <tr>
            <th style="border: 1px solid #bdb2b2; padding:4px 6px; background: #cccccc;">#</th>
            <th style="border: 1px solid #bdb2b2; padding:4px 6px; background: #cccccc;"> Document </th>
            <th style="border: 1px solid #bdb2b2; padding:4px 6px; background: #cccccc; width: 105px"> Document No </th>
            <th style="border: 1px solid #bdb2b2; padding:4px 6px; background: #cccccc; width: 100px"> Issue Date </th>
            <th style="border: 1px solid #bdb2b2; padding:4px 6px; background: #cccccc; width: 105px"> Expiry Date </th>
            <th style="border: 1px solid #bdb2b2; padding:4px 6px; background: #cccccc; width: 205px"> Issued By </th>
        </tr>
        </thead>

        <tbody>
        <?php
        $i = 1;
        foreach ($docs as $row) {
            $subType = ($row['sub_typesDes']) ? ' - ' . $row['sub_typesDes'] : '';

            echo '<tr>
                    <td style="text-align: right;border: 1px solid #bdb2b2; padding:4px 6px;">' . $i . '</td>                                                                                                                       
                    <td style="border: 1px solid #bdb2b2; padding:4px 6px;">' . $row['DocDescription'] . ' ' . $subType . '</td>
                    <td style="border: 1px solid #bdb2b2; padding:4px 6px;">' . $row['documentNo'] . '</td>
                    <td style="text-align: center;border: 1px solid #bdb2b2; padding:4px 6px;">' . $row['issueDate'] . '</td>          
                    <td style="text-align: center;border: 1px solid #bdb2b2; padding:4px 6px;">' . $row['expireDate'] . '</td>                                                   
                    <td style="border: 1px solid #bdb2b2; padding:4px 6px;">' . $row['issueDet'] . '</td>                                                   
                  </tr>';
            $i++;
        } ?>
        </tbody>
    </table>
<?php }?>

<?php if(!empty($dep_docs)){
    if(!empty($docs)){ echo '<div style="">&nbsp;</div>'; }
    ?>
    <span style="font-size: 12px">Dependant Documents</span>
    <table style="border-collapse: collapse; width: 780px !important;font-size: 12px;">
        <thead>
        <tr>
            <th style="border: 1px solid #bdb2b2; padding:4px 6px; background: #cccccc;">#</th>
            <th style="border: 1px solid #bdb2b2; padding:4px 6px; background: #cccccc;"> Relationship </th>
            <th style="border: 1px solid #bdb2b2; padding:4px 6px; background: #cccccc;"> Name </th>
            <th style="border: 1px solid #bdb2b2; padding:4px 6px; background: #cccccc;"> Document </th>
            <th style="border: 1px solid #bdb2b2; padding:4px 6px; background: #cccccc; width: 105px"> Expiry Date </th>
        </tr>
        </thead>

        <tbody>
        <?php
        $i = 1;
        foreach ($dep_docs as $row) {

            echo '<tr>
                    <td style="text-align: right;border: 1px solid #bdb2b2; padding:4px 6px;">' . $i . '</td>
                    <td style="border: 1px solid #bdb2b2; padding:4px 6px;">' . $row['relationship'] . '</td>                                                                                                                          
                    <td style="border: 1px solid #bdb2b2; padding:4px 6px;">' . $row['relName'] . '</td>
                    <td style="border: 1px solid #bdb2b2; padding:4px 6px;">' . $row['docType'] . '</td>                           
                    <td style="text-align: center;border: 1px solid #bdb2b2; padding:4px 6px;">' . $row['expireDate'] . '</td>                                               
                  </tr>';
            $i++;
        }?>
        </tbody>
    </table>
<?php }?>

