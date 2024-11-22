<table class="table" style="width: 100%;">
    <tr>
        <td style="width: 15%;">
            <img alt="Logo" style="height: 130px"
                 src="<?php echo isset($extra['company']['company_logo']) ? server_path('images/logo/') . '/' . $extra['company']['company_logo'] : ''; ?>">
        </td>
        <td style="width: 85%;">
            <table style="width: 100%;">
                <tr>
                    <th>Company Name</th>
                    <td>
                        <?php
                        echo isset($extra['company']['company_name'], $extra['company']['company_code']) ?
                            $extra['company']['company_name'] . ' ( ' . $extra['company']['company_code'] . ' )' : '';
                        ?>
                    </td>
                    <th>Legal Name</th>
                    <td>
                        <?php echo isset($extra['company']['legalName']) ? $extra['company']['legalName'] : ''; ?>
                    </td>
                </tr>
                <tr>
                    <th>Default Currency</th>
                    <td>
                        <?php
                        echo isset($extra['company']['company_default_currency']) ?
                            fetch_currency_dec($extra['company']['company_default_currency']) . ' ( ' . $extra['company']['company_default_currency'] . ' )' : '';
                        ?>
                    </td>
                    <th>Industry</th>
                    <td>
                        <?php echo isset($extra['company']['industry']) ? $extra['company']['industry'] : ''; ?>
                    </td>
                </tr>
                <tr>
                    <th>Reporting Currency</th>
                    <td>
                        <?php
                        echo isset($extra['company']['company_reporting_currency']) ?
                            fetch_currency_dec($extra['company']['company_reporting_currency']) . ' ( ' . $extra['company']['company_reporting_currency'] . ' )' : '';
                        ?>
                    </td>
                    <th>TIN NO</th>
                    <td>
                        <?php
                        echo isset($extra['company']['textIdentificationNo'], $extra['company']['textYear']) ?
                            $extra['company']['textIdentificationNo'] . ' ( ' . $extra['company']['textYear'] . ' )' : '';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>
                        <?php echo isset($extra['company']['company_phone']) ? $extra['company']['company_phone'] : ''; ?>
                    </td>
                    <th>Address</th>
                    <td>
                        <?php
                        echo isset($extra['company']['company_address1'], $extra['company']['company_city'], $extra['company']['company_province'], $extra['company']['company_postalcode'], $extra['company']['company_country']) ?
                            $extra['company']['company_address1'] . ' ' . $extra['company']['company_city'] . ' ' . $extra['company']['company_province'] . ' ( ' . $extra['company']['company_postalcode'] . ' ) ' . $extra['company']['company_country'] : '';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>
                        <?php echo isset($extra['company']['company_email']) ? $extra['company']['company_email'] : ''; ?>
                    </td>
                    <th>&nbsp;</th>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<hr>
<table class="table table-striped table-condensed table-bordered" id="com_table">
    <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%">UserName</th>
            <th style="min-width: 10%">Code</th>
            <th style="min-width: 10%">Gender</th>
            <th style="min-width: 50%">Employee Name</th>
            <th style="min-width: 10%">Date Joined</th>
        </tr>
    </thead>
    <tbody id="company_users">
        <?php  
        if (!empty($extra['employee'])) {        
            for ($i=0; $i < count($extra['employee']); $i++) { 
                echo "<tr>";
                echo "<td>".($i+1)."</td>";
                echo "<td>".$extra['employee'][$i]['UserName']."</td>";
                echo "<td>".$extra['employee'][$i]['ECode']."</td>";
                echo "<td>".($extra['employee'][$i]['Gender'] == 2 ? 'Female' : 'Male')."</td>";
                echo "<td>".$extra['employee'][$i]['Ename1'].' '.$extra['employee'][$i]['Ename2']."</td>";
                echo "<td>".$extra['employee'][$i]['EDOJ']."</td>";
                echo "</tr>";
            }
        }else{
        ?>
        <tr class="danger">
            <td colspan="6" class="text-center"><b>No Records Found</b></td>
        </tr>
        <?php } ?>
    </tbody>
</table>