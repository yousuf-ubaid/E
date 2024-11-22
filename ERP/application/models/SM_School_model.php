<?php

class SM_School_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('sm_school');
    }

    function save_student()
    {
        $StuImage = $this->input->post('StuImage');
        $stuID = $this->input->post('stuID');
        $serial_no = $this->input->post('SerialNo');
        $student_code = $this->input->post('StuCode');
        $name = $this->input->post('Stu_Name');
        $other_name = $this->input->post('otherName');
        $surname = $this->input->post('Stu_surname');
        $name_with_initials = $this->input->post('initial') . $this->input->post('Ename4');
        $dob = $this->input->post('StuDob');
        $age = $this->input->post('age');
        $gender = $this->input->post('Stu_gender');
        $category = $this->input->post('Stu_category');
        $nationality = $this->input->post('Stu_Nationality');
        $religion = $this->input->post('Stu_religion');
        $nic_civil_rc = $this->input->post('NIC');
        $bloodgroup = $this->input->post('Stu_blood_group');
        $email = $this->input->post('Stu_email');
        $last_school_attended = $this->input->post('LastSchoolAttended');
        $admitted_grade = $this->input->post('AdmittedGrade');
        $admission_date = $this->input->post('admissionDate');
        $admitted_year = $this->input->post('AdmittedYear');
        $present_class = $this->input->post('grade').$this->input->post('group');
        $home_address = $this->input->post('HomeAddress');
        $birth_place = $this->input->post('BirthPlace');
        $remarks = $this->input->post('Remarks');
        $contact_person = $this->input->post('contact_person');
        $parental_status = $this->input->post('parental_status');
        $country = $this->input->post('Country');
        $area = $this->input->post('Area');
        $journey_type = $this->input->post('Journey_Type');
        $drop_location = $this->input->post('Drop_Location');
        $travel_by = $this->input->post('Travelled_By');
        $vehicle_number = $this->input->post('Vehicle_No');
        $fingerprint_device_id = $this->input->post('Finger_Print_Device_ID');
        $machine_user_id = $this->input->post('Machine_User_ID');
        $speech_status = $this->input->post('Speech_Status');
        $special_care = $this->input->post('Special_Care');
        $physical_disability = $this->input->post('Physical_Disability');
        $no_of_brothers = $this->input->post('Boy');
        $no_of_sisters = $this->input->post('Girl');
        $sibling_order = $this->input->post('Siblings_Order');
        $attachment_description = $this->input->post('Attachement_Description');
        $attachment = $this->input->post('Attachement');
        $document_description = $this->input->post('document_description');
        $document = $this->input->post('document');
        $expired_date = $this->input->post('expired_date');
        $remind_before = $this->input->post('remind_before');

        $this->db->trans_begin();
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $stuID = $this->input->post('stuID');
        if ($stuID != NULL) {
            $this->db->update('srp_erp_sm_studentmaster', $data, array('stuID' => $stuID));

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Successfully Updated'));
            }
        } else {
            $StuImage = $this->input->post('StuImage');
            $stuID = $this->input->post('stuID');
            $serial_no = $this->input->post('SerialNo');
            $student_code = $this->input->post('StuCode');
            $name = $this->input->post('Stu_Name');
            $other_name = $this->input->post('otherName');
            $surname = $this->input->post('Stu_surname');
            $name_with_initials = $this->input->post('initial') . $this->input->post('Ename4');
            $dob = $this->input->post('StuDob');
            $age = $this->input->post('age');
            $gender = $this->input->post('Stu_gender');
            $category = $this->input->post('Stu_category');
            $nationality = $this->input->post('Stu_Nationality');
            $religion = $this->input->post('Stu_religion');
            $nic_civil_rc = $this->input->post('NIC');
            $bloodgroup = $this->input->post('Stu_blood_group');
            $email = $this->input->post('Stu_email');
            $last_school_attended = $this->input->post('LastSchoolAttended');
            $admitted_grade = $this->input->post('AdmittedGrade');
            $admission_date = $this->input->post('admissionDate');
            $admitted_year = $this->input->post('AdmittedYear');
            $present_class = $this->input->post('grade').$this->input->post('group');
            $home_address = $this->input->post('HomeAddress');
            $birth_place = $this->input->post('BirthPlace');
            $remarks = $this->input->post('Remarks');
            $contact_person = $this->input->post('contact_person');
            $parental_status = $this->input->post('parental_status');
            $country = $this->input->post('Country');
            $area = $this->input->post('Area');
            $journey_type = $this->input->post('Journey_Type');
            $drop_location = $this->input->post('Drop_Location');
            $travel_by = $this->input->post('Travelled_By');
            $vehicle_number = $this->input->post('Vehicle_No');
            $fingerprint_device_id = $this->input->post('Finger_Print_Device_ID');
            $machine_user_id = $this->input->post('Machine_User_ID');
            $speech_status = $this->input->post('Speech_Status');
            $special_care = $this->input->post('Special_Care');
            $physical_disability = $this->input->post('Physical_Disability');
            $no_of_brothers = $this->input->post('Boy');
            $no_of_sisters = $this->input->post('Girl');
            $sibling_order = $this->input->post('Siblings_Order');
            $attachment_description = $this->input->post('Attachement_Description');
            $attachment = $this->input->post('Attachement');
            $document_description = $this->input->post('document_description');
            $document = $this->input->post('document');
            $expired_date = $this->input->post('expired_date');
            $remind_before = $this->input->post('remind_before');

            if (isset($_FILES['stuImage']['name']) && !empty($_FILES['stuImage']['name'])) {
                //$imgData = $this->imageUpload($student_code);
                $imgData = $this->image_upload_s3($student_code);
            } else {
                $img = ($other_name == 2) ? 'images/users/female.png' : 'images/users/male.png';
                $imgData = array('s', $img);
            }

            if (isset($_FILES['stuSignatureImage']['name']) && !empty($_FILES['stuSignatureImage']['name'])) {
                //$imgDataSignature = $this->imageUploadSignature($student_code . 'Signature');
                $imgDataSignature = $this->image_signature_s3("{$student_code}Signature");
            } else {
                $imgDataSignature = array('s', 'no-logo.png');
            }


            if ($imgData[0] != 's' || $imgDataSignature[0] != 's') {
                return ($imgData[0] != 's') ? $imgData : $imgDataSignature;
            } else {
                $data = array(
                    'StuImage' => $StuImage,
                    'student_code' => $student_code,
                    'stuID' => $stuID,
                    'serial_no' => $serial_no,
                    'student_code' => $student_code,
                    'name' => $name,
                    'other_name' => $other_name,
                    'surname' => $surname,
                    'name_with_initials' => $name_with_initials,
                    'dob' => $dob,
                    'age' => $age,
                    'gender' => $gender,
                    'category' => $category,
                    'nationality' => $nationality,
                    'religion' => $religion,
                    'nic_civil_rc' => $nic_civil_rc,
                    'bloodgroup' => $bloodgroup,
                    'email' => $email,
                    'last_school_attended' => $last_school_attended,
                    'admitted_grade' => $admitted_grade,
                    'admission_date' => $admission_date,
                    'admitted_year' => $admitted_year,
                    'present_class' => $present_class,
                    'home_address' => $home_address,
                    'birth_place' => $birth_place,
                    'remarks' => $remarks,
                    'contact_person' => $contact_person,
                    'parental_status' => $parental_status,
                    'country' => $country,
                    'area' => $area,
                    'journey_type' => $journey_type,
                    'drop_location' => $drop_location,
                    'travel_by' => $travel_by,
                    'vehicle_number' => $vehicle_number,
                    'fingerprint_device_id' => $fingerprint_device_id,
                    'machine_user_id' => $machine_user_id,
                    'speech_status' => $speech_status,
                    'special_care' => $special_care,
                    'physical_disability' => $physical_disability,
                    'no_of_brothers' => $no_of_brothers,
                    'no_of_sisters' => $no_of_sisters,
                    'sibling_order' => $sibling_order,
                    'attachment_description' => $attachment_description,
                    'attachment' => $attachment,
                    'document_description' => $document_description,
                    'document' => $document,
                    'expired_date' => $expired_date,
                    'remind_before' => $remind_before

                );

                $this->db->insert('srp_erp_sm_studentmaster', $data);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('e', 'failed'));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('s', 'Successfully Saved'));
                }
            }
        }
    }
    function delete_student()
    {

        $this->db->trans_begin();
        $stuID = $this->input->post('stuID');
        $header = $this->db->query("select * from srp_erp_sm_studentmaster where stuID=$stuID ")->row_array();
        if (!empty($header)) {
            echo json_encode(array('e', 'You cannot delete, please delete all assigned documents to continue'));
            exit;
        }
        $category = $this->db->query("select * from srp_erp_sm_studentmaster where stuID=$stuID ")->row_array();
        if (!empty($category)) {
            echo json_encode(array('e', 'You cannot delete, please delete all assigned category to continue'));
            exit;
        }
        $this->db->delete('srp_erp_sm_studentmaster', array('stuID' => $this->input->post('stuID')));


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Deleted'));
        }
    }
    function image_upload_s3($student_code)
    {
        $fileName = str_replace(' ', '', strtolower($student_code)) . '_' . time();
        $file = $_FILES['stuImage'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($file['error'] == 1) {
            die(json_encode(['e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)."]));
        }

        $allowed_types = 'gif|png|jpg|jpeg';
        $allowed_types = explode('|', $allowed_types);
        if (!in_array($ext, $allowed_types)) {
            die(json_encode(['e', "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
        }

        $size = $file['size'];
        $size = number_format($size / 1048576, 2);

        if ($size > 1) {
            die(json_encode(['e', "The file you are attempting to upload is larger than the permitted size. ( Maximum 1MB )"]));
        }

        $fileName = "images/users/$fileName.$ext";
        $s3Upload = $this->s3->upload($file['tmp_name'], $fileName);

        if (!$s3Upload) {
            return array('e', 'Student image upload failed ');
        } else {
            return array('s', $fileName);
        }
    }

    function image_signature_s3($file_description)
    {
        $fileName = str_replace(' ', '', strtolower($file_description)) . '_' . time();
        $file = $_FILES['stuSignatureImage'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($file['error'] == 1) {
            die(json_encode(['e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)."]));
        }

        $allowed_types = 'gif|png|jpg|jpeg';
        $allowed_types = explode('|', $allowed_types);
        if (!in_array($ext, $allowed_types)) {
            die(json_encode(['e', "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
        }

        $size = $file['size'];
        $size = number_format($size / 1048576, 2);

        if ($size > 1) {
            die(json_encode(['e', "The file you are attempting to upload is larger than the permitted size. ( Maximum 1MB )"]));
        }

        $fileName = "images/users/$fileName.$ext";
        $s3Upload = $this->s3->upload($file['tmp_name'], $fileName);

        if (!$s3Upload) {
            return array('e', 'Student image upload failed ');
        } else {
            return array('s', $fileName);
        }
    }
}
