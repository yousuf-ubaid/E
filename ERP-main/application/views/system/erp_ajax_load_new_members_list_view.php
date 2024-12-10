<?php
    foreach ($newmembers as $key => $val) {
        if ($key <= 9) {
            if(empty($val['EmpImage'])) 
            {
                $val['EmpImage'] = $defaultImg; 
            }
            $defaultImg = 'default.gif';
            $defaultImgFull = base_url('images/users/default.gif');
            $link = $this->s3->createPresignedRequest($val['EmpImage'], '1 hour');
?>
            <li>
                <img src="<?php echo  $link?>" alt="No Image">
                <a class="users-list-name" href="#" title="<?php echo $val['Ename2'] ?>"><?php echo $val['Ename2'] ?></a>
                <span class="users-list-date" style="cursor: pointer;"><?php echo trim_value($val['DesDescription'],15)  ?></span>
            </li>
<?php
        }
    }
?>