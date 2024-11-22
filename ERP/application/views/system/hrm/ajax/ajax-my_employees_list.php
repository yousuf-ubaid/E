<!--OrgChart-master-->
<link rel="stylesheet" href="<?php echo base_url('plugins/OrgChart-master/dist/css/jquery.orgchart.css'); ?>"/>
<style>
    table {
        border-collapse: inherit;
    }

    .link {
        color: #ffffff;
    }

    /* visited link */
    .link:visited {
        color: #ffffff;
    }

    /* mouse over link */
    .link:hover {
        color: #ffffff;
    }

    /* selected link */
    .link:active {
        color: #ffffff;
    }

    .orgchart .node .content {
        min-height: 0px;
        padding: 0px;
    }

    .orgchart td.left {
        border-left: 1px solid rgba(217, 83, 79, 0.8);
    }

    .orgchart td.top {
        border-top: 2px solid rgba(217, 83, 79, 0.8);
    }

    .orgchart td {
        text-align: center;
        vertical-align: top;
        padding: 0;
    }

    .orgchart td > .down {
        background-color: rgba(217, 83, 79, 0.8);
        margin: 0px auto;
        height: 20px;
        width: 2px;
    }

    .orgchart td.top {
        border-top: 2px solid rgba(217, 83, 79, 0.8);
    }

    .orgchart .node.focused {
        background-color: rgba(238, 217, 54, 0.5);
    }

    .orgchart .node {
        display: inline-block;
        position: relative;
        margin: 0;
        padding: 3px;
        border: 2px dashed transparent;
        text-align: center;
        width: 130px;
    }

    .node {
        transition: all 0.3s;
        opacity: 1;
        top: 0;
        left: 0;
    }

    .orgchart .node .second-menu {
        display: none;
        position: absolute;
        top: 0;
        right: -70px;
        border-radius: 35px;
        box-shadow: 0 0 10px 1px #999;
        background-color: #fff;
        z-index: 1;
    }

    .hidden {
        display: none !important;
    }

    .orgchart {
        display: inline-block;
        min-height: 202px;
        min-width: 202px;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-image: linear-gradient(90deg, rgba(200, 0, 0, 0.15) 10%, rgba(0, 0, 0, 0) 10%), linear-gradient(rgba(200, 0, 0, 0.15) 10%, rgba(0, 0, 0, 0) 10%);
        background-size: 10px 10px;
        border: 1px dashed rgba(0, 0, 0, 0);
        transition: border .3s;
        padding: 20px;
    }

    .orgchart > .spinner {
        font-size: 100px;
        margin-top: 30px;
        color: rgba(68, 157, 68, 0.8);
    }

    .orgchart table {
        border-spacing: 0;
    }

    .orgchart > table:first-child {
        margin: 20px auto;
    }

    .orgchart td {
        text-align: center;
        vertical-align: top;
        padding: 0;
    }

    .orgchart td.top {
        border-top: 2px solid rgba(217, 83, 79, 0.8);
    }

    .orgchart td.right {
        border-right: 1px solid rgba(217, 83, 79, 0.8);
    }

    .orgchart td.left {
        border-left: 1px solid rgba(217, 83, 79, 0.8);
    }

    .orgchart td > .down {
        background-color: rgba(217, 83, 79, 0.8);
        margin: 0px auto;
        height: 20px;
        width: 2px;
    }

    /* node styling */
    .orgchart .node {
        display: inline-block;
        position: relative;
        margin: 0;
        padding: 3px;
        border: 2px dashed transparent;
        text-align: center;
        width: 130px;
    }

    .orgchart .node > .spinner {
        position: absolute;
        top: calc(50% - 15px);
        left: calc(50% - 15px);
        vertical-align: middle;
        font-size: 30px;
        color: rgba(68, 157, 68, 0.8);
    }

    .orgchart .node:hover {
        background-color: rgba(238, 217, 54, 0.5);
        transition: .5s;
        cursor: default;
        z-index: 20;
    }

    .orgchart .node.focused {
        background-color: rgba(238, 217, 54, 0.5);
    }

    .orgchart .node.allowedDrop {
        border-color: rgba(68, 157, 68, 0.9);
    }

    .orgchart .node .title {
        /*position: relative;*/
        text-align: center;
        font-size: 12px;
        font-weight: bold;
        height: 20px;
        line-height: 20px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        background-color: rgba(217, 83, 79, 0.8);
        color: #fff;
        border-radius: 4px 4px 0 0;
    }

    .orgchart .node .title .symbol {
        float: left;
        margin-top: 4px;
        margin-left: 2px;
    }

    .orgchart .node .content {
        position: relative;
        /*width: 100%;*/
        font-size: 11px;
        line-height: 13px;
        padding: 2px;
        border: 1px solid rgba(217, 83, 79, 0.8);
        border-radius: 0 0 4px 4px;
        text-align: center;
        background-color: #fff;
        color: #333;
        overflow: hidden;
    }

    .orgchart .node .edge {
        font-size: 15px;
        position: absolute;
        color: rgba(68, 157, 68, 0.5);
        cursor: default;
        transition: .2s;
        -webkit-transition: .2s;
    }

    .orgchart .edge:hover {
        color: #449d44;
        cursor: pointer;
    }

    .orgchart .node .verticalEdge {
        width: calc(100% - 10px);
        width: -webkit-calc(100% - 10px);
        width: -moz-calc(100% - 10px);
        left: 5px;
    }

    .orgchart .node .topEdge {
        top: -4px;
    }

    .orgchart .node .bottomEdge {
        bottom: -4px;
    }

    .orgchart .node .horizontalEdge {
        width: 15px;
        height: calc(100% - 10px);
        height: -webkit-calc(100% - 10px);
        height: -moz-calc(100% - 10px);
        top: 5px;
    }

    .orgchart .node .rightEdge {
        right: -4px;
    }

    .orgchart .node .leftEdge {
        left: -4px;
    }

    .orgchart .node .horizontalEdge::before {
        position: absolute;
        top: calc(50% - 7px);
        top: -webkit-calc(50% - 7px);
        top: -moz-calc(50% - 7px);
    }

    .orgchart .node .rightEdge::before {
        right: 3px;
    }

    .orgchart .node .leftEdge::before {
        left: 3px;
    }

    .orgchart .node .edge.fa-chevron-up:hover {
        transform: translate(0, -4px);
        -webkit-transform: translate(0, -4px);
    }

    .orgchart .node .edge.fa-chevron-down:hover {
        transform: translate(0, 4px);
        -webkit-transform: translate(0, 4px);
    }

    .orgchart .node .edge.fa-chevron-right:hover {
        transform: translate(4px, 0);
        -webkit-transform: translate(4px, 0);
    }

    .orgchart .node .edge.fa-chevron-left:hover {
        transform: translate(-4px, 0);
        -webkit-transform: translate(-4px, 0);
    }

    .orgchart .node .edge.fa-chevron-right:hover ~ .fa-chevron-left {
        transform: translate(-4px, 0);
        -webkit-transform: translate(-4px, 0);
    }

    .orgchart .node .edge.fa-chevron-left:hover ~ .fa-chevron-right {
        transform: translate(4px, 0);
        -webkit-transform: translate(4px, 0);
    }

    .rightEdgeMoveRight {
        transform: translate(4px, 0);
        -webkit-transform: translate(4px, 0);
    }

    .rightEdgeMoveLeft {
        transform: translate(-4px, 0);
        -webkit-transform: translate(-4px, 0);
    }

    .oc-export-btn {
        display: inline-block;
        position: absolute;
        right: 5px;
        top: 5px;
        padding: 4px 12px;
        margin-bottom: 0;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        color: #fff;
        background-color: #34495e;
        border: 1px solid transparent;
        border-color: #34495e;
        border-radius: 4px;
    }

    .oc-export-btn[disabled] {
        cursor: not-allowed;
        filter: alpha(opacity=30);
        -webkit-box-shadow: none;
        box-shadow: none;
        opacity: 0.3;
    }

    .oc-export-btn:hover, .oc-export-btn:focus, .oc-export-btn:active {
        background-color: #3f5872;
        border-color: #3f5872;
    }

    .orgchart ~ .mask {
        position: absolute;
        top: 0px;
        right: 0px;
        bottom: 0px;
        left: 0px;
        z-index: 999;
        text-align: center;
        background-color: rgba(0, 0, 0, 0.3);
    }

    .orgchart ~ .mask .spinner {
        position: absolute;
        top: calc(50% - 54px);
        left: calc(50% - 54px);
        color: rgba(255, 255, 255, 0.8);
        font-size: 108px;
    }

    .node {
        transition: all 0.3s;
        webkit-transition: all 0.3s;
        opacity: 1;
        top: 0;
        left: 0;
    }

    .slide-down {
        opacity: 0;
        top: 42px;
    }

    .slide-up {
        opacity: 0;
        top: -42px;
    }

    .slide-right {
        opacity: 0;
        left: 130px;
    }

    .slide-left {
        opacity: 0;
        left: -130px;
    }

    .orgchart .second-menu-icon {
        transition: opacity .5s;
        opacity: 0;
        right: -5px;
        top: -5px;
        z-index: 2;
        color: rgba(68, 157, 68, 0.5);
        font-size: 18px;
        position: absolute;
    }

    .orgchart .second-menu-icon:hover {
        color: #449d44;
    }

    .orgchart .node:hover .second-menu-icon {
        opacity: 1;
    }

    .orgchart .node .second-menu {
        display: none;
        position: absolute;
        top: 0;
        right: -70px;
        border-radius: 35px;
        box-shadow: 0 0 10px 1px #999;
        background-color: #fff;
        z-index: 1;
    }

    .orgchart .node .second-menu .avatar {
        width: 60px;
        height: 60px;
        border-radius: 30px;
    }

    .orgchart {
        background: #fff;
    }

    a:hover, a:focus {
        color: #23527c;
        text-decoration: underline;
    }

    #chart-container {
        max-height: 500px;
        overflow-y: scroll;
    }
</style>

<?php
$company_id = current_companyID();
$page = $this->db->query("SELECT createPageLink FROM srp_erp_templatemaster
                              LEFT JOIN srp_erp_templates ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID
                              WHERE srp_erp_templates.FormCatID=89 AND companyID={$company_id}
                              ORDER BY srp_erp_templatemaster.FormCatID")->row('createPageLink');
$employee_names = array();
//CONCAT(e1.Ename2,'~',if(ed1.DesDescription != '',ed1.DesDescription,'-'),'~',e1.EIdNo,'~',if(e1.EmpImage != '',e1.EmpImage,'images/icon-no-image.png')) AS EIdNo1,
$output = $this->db->query("SELECT
	CONCAT(e1.Ename2,'~',if(ed1.DesDescription != '',ed1.DesDescription,'-'),'~',e1.EIdNo,'~',e1.EmpImage,'~',e1.Gender) AS EIdNo1,
	CONCAT(e2.Ename2,'~',if(ed2.DesDescription != '',ed2.DesDescription,'-'),'~',e2.EIdNo,'~',e2.EmpImage,'~',e2.Gender) AS EIdNo2,
	CONCAT(e3.Ename2,'~',if(ed3.DesDescription != '',ed3.DesDescription,'-'),'~',e3.EIdNo,'~',e3.EmpImage,'~',e3.Gender) AS EIdNo3,
	CONCAT(e4.Ename2,'~',if(ed4.DesDescription != '',ed4.DesDescription,'-'),'~',e4.EIdNo,'~',e4.EmpImage,'~',e4.Gender) AS EIdNo4,
	CONCAT(e5.Ename2,'~',if(ed5.DesDescription != '',ed5.DesDescription,'-'),'~',e5.EIdNo,'~',e5.EmpImage,'~',e5.Gender) AS EIdNo5
	FROM srp_erp_employeemanagers AS a1

	left join (
        SELECT managerID,empID
        FROM srp_erp_employeemanagers
        WHERE active = 1
    ) AS a2 ON a2.managerID = a1.empID

	left join (
        SELECT managerID,empID
        FROM srp_erp_employeemanagers
        WHERE active = 1
    ) AS a3  ON a3.managerID = a2.empID

	left join (
        SELECT managerID,empID
        FROM srp_erp_employeemanagers
        WHERE active = 1
    ) AS a4  ON a4.managerID = a3.empID

	left join (
        SELECT managerID,empID
        FROM srp_erp_employeemanagers
        WHERE active = 1
    ) AS a5 ON  a5.managerID = a4.empID

	left join srp_employeesdetails AS e1 ON a1.empID = e1.EIdNo AND e1.isDischarged = 0
	left join srp_employeesdetails AS e2 ON a2.empID = e2.EIdNo AND e2.isDischarged = 0
	left join srp_employeesdetails AS e3 ON a3.empID = e3.EIdNo AND e3.isDischarged = 0
	left join srp_employeesdetails AS e4 ON a4.empID = e4.EIdNo AND e4.isDischarged = 0
	left join srp_employeesdetails AS e5 ON a5.empID = e5.EIdNo AND e5.isDischarged = 0

	left join srp_designation AS ed1 ON e1.EmpDesignationId = ed1.DesignationID
	left join srp_designation AS ed2 ON e2.EmpDesignationId = ed2.DesignationID
	left join srp_designation AS ed3 ON e3.EmpDesignationId = ed3.DesignationID
	left join srp_designation AS ed4 ON e4.EmpDesignationId = ed4.DesignationID
	left join srp_designation AS ed5 ON e5.EmpDesignationId = ed5.DesignationID

	WHERE a1.managerID = '" . current_userID() . "' and a1.empID != '" . current_userID() . "' and a1.active=1 ")->result_array();


$outrootemp = $this->db->query("SELECT Ename2,srp_designation.DesDescription,if(EmpImage != '',EmpImage,'images/icon-no-image.png') as profileImage, EmpImage, Gender 
FROM srp_employeesdetails left join srp_designation  ON srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID  WHERE srp_employeesdetails.EIdNo = '" . current_userID() . "'")->row_array();

if (!empty($output)) {
    foreach ($output as $row) {
        $employee_names[$row['EIdNo1']][$row['EIdNo2']][$row['EIdNo3']][$row['EIdNo4']][] = $row['EIdNo5'];
    }
}


$data = "";
foreach($employee_names as $k1 => $v1) {
    if (!empty($k1)) {
        $val1 = explode("~", $k1);
        $img = empImage_s3($val1[3], $val1[4],  $male_img, $female_img);
        $data .= "{ 'id': \"" . $img . "\",'name': '<a class=\'link\' href=\'#\' rel=\'tooltip\' title=\'" . htmlentities($val1[0], ENT_QUOTES) . "\' onClick=\"loadEmployee(\'" . $val1[2] . "\',\'" . htmlentities($val1[0], ENT_QUOTES) . "\')\">" . htmlentities($val1[0], ENT_QUOTES) . "</a>', 'title': '" . htmlentities($val1[1]) . "',";

        $test1 = array_values($v1);
        unset($v1['']);
        if (!empty($v1)) {
            $data .= "'children': [";
            foreach ($v1 as $k2 => $v2) {
                if (!empty($k2)) {
                    $val2 = explode("~", $k2);
                    $img = empImage_s3($val2[3], $val2[4],  $male_img, $female_img);
                    $data .= "{ 'id': \"" . $img . "\",'name': '<a class=\'link\' href=\'#\' title=\'" . htmlentities($val2[0], ENT_QUOTES) . "\' rel=\'tooltip\' onClick=\"loadEmployee(\'" . $val2[2] . "\',\'" . htmlentities($val2[0], ENT_QUOTES) . "\')\">" . htmlentities($val2[0], ENT_QUOTES) . "</a>', 'title': '" . htmlentities($val2[1]) . "',";
                    $test2 = array_values($v2);
                    unset($v2['']);

                    if ($v2) {
                        $data .= "'children': [";
                        foreach ($v2 as $k3 => $v3) {
                            if (!empty($k3)) {
                                $val3 = explode("~", $k3);
                                $img = empImage_s3($val3[3], $val3[4],  $male_img, $female_img);
                                $data .= "{ 'id': \"" . $img . "\",'name': '<a class=\'link\' href=\'#\' title=\'" . htmlentities($val3[0], ENT_QUOTES) . "\' rel=\'tooltip\' onClick=\"loadEmployee(\'" . $val3[2] . "\',\'" . htmlentities($val3[0], ENT_QUOTES) . "\')\">" . htmlentities($val3[0], ENT_QUOTES) . "</a>', 'title': '" . htmlentities($val3[1]) . "',";
                                $test3 = array_values($v2);
                                unset($v3['']);
                                if ($v3) {
                                    $data .= "'children': [";
                                    foreach ($v3 as $k4 => $v4) {
                                        if (!empty($k4)) {
                                            $val4 = explode("~", $k4);
                                            $img = empImage_s3($val4[3], $val4[4],  $male_img, $female_img);
                                            $data .= "{ 'id': \"" . $img . "\",'name': '<a class=\'link\' href=\'#\' title=\'" . htmlentities($val4[0], ENT_QUOTES) . "\' rel=\'tooltip\' onClick=\"loadEmployee(\'" . $val4[2] . "\',\'" . htmlentities($val4[0], ENT_QUOTES) . "\')\">" . htmlentities($val4[0], ENT_QUOTES) . "</a>', 'title': '" . htmlentities($val4[1]) . "'},";
                                        }
                                    }
                                    $data .= "]";
                                }
                                $data .= "},";
                            }
                        }
                        $data .= "]";
                    }
                    $data .= "},";
                }
            }
            $data .= "]";
        }
        $data .= "},";
    }
}

 //echo "<pre>"; print_r($data); echo "</pre>"; exit;
?>
<div id="chart-container" style="overflow: auto"></div>

<!--OrgChart-master-->
<script src="<?php echo base_url('plugins/OrgChart-master/dist/js/jquery.orgchart.js'); ?>"></script>
<script>
    $(document).ready(function () {
        var datascource = {
            'id': '<?php echo empImage_s3($outrootemp['EmpImage'], $outrootemp['Gender'],  $male_img, $female_img); //$outrootemp['profileImage'] ?>',
            'name': '<?php echo $outrootemp['Ename2'] ?>',
            'title': '<?php echo $outrootemp['DesDescription'] ?>',
            <?php
            if($data) { ?>
            'children': [
                <?php echo $data ?>
            ]
            <?php } ?>
        };

        $('#chart-container').orgchart({
            'data': datascource,
            'depth': 5,
            'nodeTitle': 'name',
            'nodeContent': 'title',
            'exportButton': true,
            'exportFilename': 'Employee List',
            'nodeID': 'id',
            'createNode': function ($node, data) {
                var secondMenuIcon = $('<i>', {
                    'class': 'fa fa-info-circle second-menu-icon',
                    hover: function () {
                        $(this).siblings('.second-menu').toggle();
                    }
                });
                var secondMenu = '<div class="second-menu"><img class="avatar" src="' + data.id + '"></div>';
                $node.append(secondMenuIcon).append(secondMenu);

            }
        });
        $('.oc-export-btn').addClass('hidden');
        $('[rel="tooltip"]').tooltip();
    });

    function loadEmployee(empID, empName) {
        //fetchPage('system/hrm/employee_create_envoy', empID, 'HRMS', 1, '');
        var masterPage = 'system/profile/profile_information';
        fetchPage('<?php echo $page ?>', empID, 'HRMS', 1, '', masterPage);
        //window.open(fetchPage('<?php echo $page ?>', empID, 'HRMS', 1, ''),'_blank');

    }
</script>