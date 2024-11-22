<ol class="breadcrumb">
    <li>
        <a style="cursor:pointer !important;" onclick="backToCompanyList();"><i class="fa fa-backward"></i> Back </a>
    </li>
    <li class="active">Company Admin List</li>
    <li class="active">CompanyName:
        <strong><?php echo $companyInfo['company_name'] . '</strong> - Code:  <strong>' . $companyInfo['company_code'] . '</strong>' ?>
    </li>
    <li class="pull-right">
        <button class="btn btn-xs btn-primary" type="button" onclick="toggleAdminForm()">Add Admin</button>
    </li>
</ol>
<!--<h4>CompanyName: <strong><?php /*echo $companyInfo['company_name'] . '</strong> - Code:  <strong>' . $companyInfo['company_code'].'</strong>' */ ?></h4>-->
<div id="container_addAdminFrm" style="border:1px dashed darkgray; padding:10px; margin-bottom: 10px; display: none; ">
    <form class="form-horizontal" id="addCompanyAdminFrm">

        <input type="hidden" id="companyID_cAdmin" name="companyID" value="0">
        <div class="form-group">
            <label class="col-md-4 control-label" for="adminName">Name</label>
            <div class="col-md-5">
                <input id="adminName" name="adminName" type="text" required placeholder="Name"
                       class="form-control input-md">
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-4 control-label" for="adminEmail">Email</label>
            <div class="col-md-4">
                <input id="adminEmail" name="adminEmail" type="email" required placeholder="Email"
                       class="form-control input-md">

            </div>
        </div>


        <div class="form-group">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-4">
                <button class="btn btn-primary btn-sm" type="submit">Save</button>
            </div>
        </div>


    </form>

</div>


<table id="company_table" class="table table-bordered table-striped table-condensed table-hover">
    <thead>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email Address</th>
        <th>isActive</th>
        <th>&nbsp;</th>
    </tr>
    </thead>

    <tbody>
    <?php
    if (!empty($adminList)) {
        $i = 1;
        foreach ($adminList as $val) {
            ?>
            <tr>
                <td><?php echo $i ?></td>
                <td><?php echo $val['adminName'] ?></td>
                <td><?php echo $val['adminEmail'] ?></td>
                <td>
                    <?php
                    if ($val['isActive'] == 1) {
                        echo 'Active';
                    } else {
                        echo 'in-Active';
                    }
                    ?>
                </td>
                <td style="width:150px;">
                    <!--<button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>-->
                    <button class="btn btn-xs btn-default" type="button" onclick="request_pin(<?php echo $val['adminMasterID'] ?>)">Request PIN</button>
                </td>
            </tr>
            <?php
            $i++;
        }
    }
    ?>

    </tbody>
</table>
<script>
    $(document).ready(function (e) {
        $("#company_table").dataTable();
    });

    function toggleAdminForm() {
        $("#container_addAdminFrm").toggle()
    }

    $("#addCompanyAdminFrm").submit(function (event) {
        save_companyAdmin();
        event.preventDefault();
        return false;
    });
</script>
<?php

