<ul class="nav nav-stacked" style="max-height: 300px;overflow-y: scroll;">
    <?php
    if (!empty($customer_con)) {
        for ($i = 0; $i < count($customer_con); $i++) {
//                                            if($customer_con[$i]['Total'] > 0){
            $con_id = $customer_con[$i]['contractAutoID'];
            echo '<li id="pull-'.$con_id.'" class="pull-li"><a onclick="fetch_con_detail_table(' . $con_id . ')">' . $customer_con[$i]['contractCode'] . ' <br> <strong> Date : </strong>' . $customer_con[$i]['contractDate'] . ' <br>  <strong> Ref : </strong>' . $customer_con[$i]['referenceNo'] . '<span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>';
//                                            }
        }
    } else {
        echo '<li><a>No records found</a></li>';
    }
    ?>
    
    <!--No Records found-->
</ul>