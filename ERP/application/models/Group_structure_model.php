<?php

class Group_structure_model extends ERP_Model
{


    function submit_groupStructure()
    {

        $isActive = $this->input->post('isActive');
        $data['percentage'] = $this->input->post('percentage');
        $data['dateFrom'] = input_format_date($this->input->post('dateFrom'), date_format_policy());
       /* $data['typeID'] = $this->input->post('typeID');*/
        $data['companyID'] = $this->input->post('companyID');
        $data['shareholderName'] = $this->input->post('shareholderName');
        $data['isActive'] = isset($isActive) ? $isActive : 0;
        $insert = $this->db->insert('srp_erp_groupstructure', $data);

        if ($insert) {
            echo json_encode(['s', 'Successfully created']);
            exit;
        } else {
            echo json_encode(['e', 'Failed']);
            exit;
        }


    }

    function get_groupStructure()
    {
        $convertFormat = convert_date_format_sql();
        $hide=$this->input->post('hide');
        if(isset($hide)){
            $hide=false;
        }
        $companyID = $this->input->post('companyID');
        $output = $this->db->query("   SELECT shareholderName,isActive,groupstructureID,percentage,DATE_FORMAT(dateFrom,'{$convertFormat}') AS dateFrom FROM `srp_erp_groupstructure`  WHERE companyID ={$companyID}  ORDER BY dateFrom Desc ")->result_array();
        $arr = [];
        if (!empty($output)) {
            foreach ($output as $element) {
                $arr[$element['isActive']][] = $element;
            }

        }


        ?>
        <table class="table table-bordered table-condensed table-row-select" style="width: 100%">
            <thead>
            <tr>
                <th>Share Holder</th>
                <th>Share holding %</th>

                <th>Date From</th>
                <?php if($hide){?>
                <th>Is Active</th>
                <th></th>
        <?php } ?>
            </tr>
            </thead>

            <?php
            if (!empty($arr[1])) {
                ?>

                <tr>
                    <td colspan="6"><i>Active</i></td>


                </tr>
                <?php

                foreach ($arr[1] as $item) {


                    ?>

                    <tr>
                        <td><?php echo $item['shareholderName'] ?></td>
                        <td style="width: 120px;text-align: center"><?php echo $item['percentage'] ?></td>

                        <td><?php echo $item['dateFrom'] ?></td>
                    <?php if($hide){?>
                        <td style="width: 40px; text-align: center">
                            <input type="checkbox" checked id="isActivex" name="isActive" value="1"
                                   onclick="update_field(this,<?php echo $item['groupstructureID'] ?>)">
                        </td>
                        <td style="width: 40px">
                            <button onclick="deleteshareholding(<?php echo $item['groupstructureID'] ?>)"
                                    class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php
                }
            }
            if (!empty($arr[0])) {


                ?>


                <tr>
                    <td colspan="6"><i>History</i></td>


                </tr>
                <?php
                foreach ($arr[0] as $item) {


                    ?>

                    <tr>
                        <td><?php echo $item['shareholderName'] ?></td>
                        <td style="width: 120px;text-align: center"><?php echo $item['percentage'] ?></td>

                        <td><?php echo $item['dateFrom'] ?></td>
                    <?php if($hide){?>
                        <td style="width: 40px; text-align: center">
                            <input type="checkbox" id="isActivex" name="isActive" value="1"
                                   onclick="update_field(this,<?php echo $item['groupstructureID'] ?>)">
                        </td>
                        <td style="width: 40px">
                            <button onclick="deleteshareholding(<?php echo $item['groupstructureID'] ?>)"
                                    class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                        </td>
                        <?php } ?>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        <?php

    }

    function loadCompanyForm()
    {

        $this->db->select('companyGroupID,description');
        $this->db->from('srp_erp_companygroupmaster');

        $data = $this->db->get()->result_array();
        $company_groupmaster_dropdown = [];
        if (isset($data)) {
            foreach ($data as $row) {
                $company_groupmaster_dropdown[trim($row['companyGroupID'] ?? '')] = trim($row['description'] ?? '');

            }
        }

        ?>



        <div class="form-group">
            <label class="col-sm-4 control-label">Company Group </label>
            <div class="col-sm-6">
                <div class="input-group datepic">

                    <?php echo form_dropdown('companyGroupID', $company_groupmaster_dropdown, '', 'class="form-control select2" id="companyGroupID" required"'); ?>

                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label">Companies </label>
            <div class="col-sm-6">
                <div class="input-group datepic">

                    <select class="form-control " name="companyID[]" id="xcompanyID" multiple>
                        <?php
                        $data = $this->db->query("SELECT company_id,CONCAT(company_code,' | ',company_name) as company FROM srp_erp_company LEFT JOIN srp_erp_companygroupdetails On company_id=srp_erp_companygroupdetails.companyID WHERE companyID IS NULL AND confirmedYN=1")->result_array();
                        foreach ($data as $item) {
                            echo '<option value="' . $item['company_id'] . '">' . $item['company'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <?php
    }

    function submit_groupStructurepullcompany()
    {
        $companyGroupID = $this->input->post('companyGroupID');
        $this->db->select('masterID');

        $this->db->where('companyGroupID', $companyGroupID);
        $this->db->from('srp_erp_companygroupmaster');
        $result = $this->db->get()->row_array();

        $company = $this->input->post('companyID');
        if (!empty($company)) {
            foreach ($company as $key => $item) {
                $data[$key]['companyID'] = $item;
                $data[$key]['companyGroupID'] = $companyGroupID;
                $data[$key]['parentID']=$result['masterID'];

            }
        }


        $insert = $this->db->insert_batch('srp_erp_companygroupdetails', $data);

        if ($insert) {
            echo json_encode(['s', 'Successfully created']);
            exit;
        } else {
            echo json_encode(['e', 'Failed']);
            exit;
        }
    }

    function loadstructurePage()
    {
       echo  $this->load->view('system/group_structure/ajax/ajax-group-structure-load-page','',true);

    }

    function GroupStructurereportingTo()
    {
        $company_groupmaster_dropdown=[];
        $companyGroupID=$this->input->post('companyGroup_ID');
        $parent=$this->input->post('parent');

$selectedID='';
        if(isset($companyGroupID)){
          $data=  $this->db->query("SELECT reportingTo FROM `srp_erp_companygroupmaster` WHERE companyGroupID=$companyGroupID AND masterID=$parent")->row_array();
          if(!empty($data)){
              $selectedID=$data['reportingTo'];
              if($data['reportingTo']==0){
                  $data_arr[0] = 'Parent';
                  $company_groupmaster_dropdown=$data_arr;
              }else{
                  $company_groupmaster_dropdown = company_groupmaster_dropdown($parent);
              }
          }
        }
        else{
            $company_groupmaster_dropdown = company_groupmaster_dropdown($parent);
        }



        ?>
        <?php echo form_dropdown('reportingTo', $company_groupmaster_dropdown, $selectedID, ' class="form-control select2" id="reportingTo" required"'); ?>
        <?php
    }

    function submit_create_group(){

        $companyGroupID=$this->input->post('companyGroupID');
        $data['groupCode'] =$this->input->post('groupCode');
        $data['description']=$this->input->post('description');
        $data['reportingTo']=$this->input->post('reportingTo');
        $data['masterID']=$this->input->post('masterID');
        if(isset($companyGroupID) && $companyGroupID !='null'){
            $this->db->where('companyGroupID', $companyGroupID);
            $insert = $this->db->update('srp_erp_companygroupmaster', $data);
        }else{
            $insert = $this->db->insert('srp_erp_companygroupmaster', $data);
        }



        if ($insert) {
            echo json_encode(['s', 'Successfully created']);
            exit;
        } else {
            echo json_encode(['e', 'Failed']);
            exit;
        }

    }

    function GroupStructuremasterTo()
    {
        $selectedID='';
        $data=  $this->db->query("SELECT companyGroupID,description FROM `srp_erp_companygroupmaster` WHERE reportingTo=0")->result_array();

        $data_arr = [];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['companyGroupID'] ?? '')] = trim($row['description'] ?? '');

            }
        }

        ?>
        <?php echo form_dropdown('masterID', $data_arr, $selectedID, 'onchange="loadreportingTo(this.value);" class="form-control select2" id="masterID" required"'); ?>
        <?php
    }
}