<?php

class WidgetDashboard extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Crm_modal');
        //$this->load->helper('crm_helper');
        //$this->load->library('Pagination');

        $this->load->library('s3');

        ini_set('max_execution_time', 360);
        ini_set('memory_limit', '2048M');
    }

    public function allCalenderEvents()
    {
        //$start = $this->input->get('start');
        //$end = $this->input->get('end');
        $event_array = array();
        $event_array2 = array();
        $companyID = current_companyID();

        $projects = trim($this->input->post('projects') ?? '');


        $filterProject = '';
        if (isset($projects) && !empty($projects)) {
            $wrproject=join(",",$projects);
            $filterProject = " AND srp_erp_projectplanning.headerID IN (" . $wrproject . ")";
        }



        $where = "WHERE srp_erp_projectplanning.companyID = " . $companyID . $filterProject;
        $sql2 = "select * from srp_erp_projectplanning  " . $where . " ";
        $result2 = $this->db->query($sql2)->result_array();

        foreach ($result2 as $record2) {
            $record2['startDate'] = date('Y-m-d h:i:s', strtotime($record2['startDate']));
            $date = strtotime("-1 day", strtotime($record2['endDate']));
            $record2['endDate'] = date('Y-m-d h:i:s', $date);
            if($record2['bgColor']=='gtaskpink'){
                $record2['bgColor']='pink';
            }elseif ($record2['bgColor']=='ggroupblack'){
                $record2['bgColor']='black';
            }elseif ($record2['bgColor']=='gtaskblue'){
                $record2['bgColor']='blue';
            }elseif ($record2['bgColor']=='gtaskred'){
                $record2['bgColor']='red';
            }elseif ($record2['bgColor']=='gtaskpurple'){
                $record2['bgColor']='purple';
            }elseif ($record2['bgColor']=='gtaskgreen'){
                $record2['bgColor']='green';
            }
            $event_array2[] = array(
                'id' => $record2['projectPlannningID'],
                'title' => $record2['description'],
                'start' => $record2['startDate'],
                'end' => $record2['endDate'],
                //'url' => 'fetchPage(\'system/crm/contact_management\',\'1\',\'Contact\')',

                'color' => $record2['bgColor'],
            );
        }
        $arr = array_merge($event_array2);

        /*print_r($arr);*/

        echo json_encode($arr);
    }
}