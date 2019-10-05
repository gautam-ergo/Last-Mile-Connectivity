<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Webservice extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'date'));
        $this->load->database();
        $this->load->model('webservice_model');
        $this->load->model('User_booking_model');
        $this->load->model('Rider_registration_model');
        date_default_timezone_set('Asia/Kolkata');
        // print $myarray;
    }

    public function index() {
        //echo 'This is index';
        $data['status'] = 1;
        $data['message'] = 'Webservice Working';
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
    }

    public function rider_registration() {
        $email = $this->input->post('rider_email', TRUE);
        $mobileno = $this->input->post('rider_mobileno', TRUE);
        $rider_liceneno = strtolower($this->input->post('rider_licenseno', TRUE));
        $rider_vehicle_bikeno = strtolower($this->input->post('rider_bikeno', TRUE));
        if ($email == null && $mobileno == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $email = $data['email'];
            $mobileno = $data['mobileno'];
            $rider_liceneno = strtolower($data['rider_licenseno']);
            $rider_vehicle_bikeno = strtolower($data['rider_bikeno']);
        }
        $riderresult = $this->webservice_model->rider_registration_check($email, $mobileno, $rider_liceneno, $rider_vehicle_bikeno);
        if (isset($riderresult) && ($riderresult->rider_email == $email)) {
            $data['status'] = '0';
            $data['message'] = 'Email-Id or Mobile number already exist!!!';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else if (isset($riderresult) && ($riderresult->rider_mobileno == $mobileno)) {
            $data['status'] = '0';
            $data['message'] = 'Email-Id or Mobile number already exist!!!';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else if (isset($riderresult) && ($riderresult->rider_liceneno == $rider_liceneno)) {
            $data['status'] = '0';
            $data['message'] = 'License already exist!!!';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else if (isset($riderresult) && ($riderresult->rider_vehicle_bikeno == $rider_vehicle_bikeno)) {
            $data['status'] = '0';
            $data['message'] = 'Vehicle number already exist!!!';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $rider_name = $this->input->post('rider_name', TRUE);
            $rider_gender = $this->input->post('rider_gender', TRUE);
            $rider_email = $this->input->post('rider_email', TRUE);
            $rider_password = md5($this->input->post('rider_password', TRUE));
            $rider_profession = $this->input->post('rider_profession', TRUE);
            $rider_address = $this->input->post('rider_address', TRUE);
            $rider_vehicle_manufacturing = $this->input->post('rider_vehicle_manufacturing', TRUE);
            $rider_vehicle_model = $this->input->post('rider_vehicle_model', TRUE);
            $rider_vehicle_year = $this->input->post('rider_vehicle_year', TRUE);
            $rider_vehicle_color = $this->input->post('rider_vehicle_color', TRUE);
            $rider_econtactname = $this->input->post('rider_econtactname', TRUE);
            $rider_emobileno = $this->input->post('rider_emobileno', TRUE);
            if ($rider_name == null && $rider_email == null) {
                $data = file_get_contents("php://input");
                $data = (array) json_decode($data);
                $rider_name = $data['rider_name'];
                $rider_gender = $data['rider_gender'];
                $rider_email = $data['rider_email'];
                $rider_password = md5($data['rider_password']);
                $rider_profession = $data['rider_profession'];
                $rider_address = $data['rider_address'];
                $rider_vehicle_manufacturing = $data['rider_vehicle_manufacturing'];
                $rider_vehicle_model = $data['rider_vehicle_model'];
                $rider_vehicle_year = $data['rider_vehicle_year'];
                $rider_vehicle_color = $data['rider_vehicle_color'];
                $rider_econtactname = $data['rider_econtactname'];
                $rider_emobileno = $data['rider_emobileno'];
            }

            $riderdata = array(
                'rider_name' => $rider_name,
                'rider_mobileno' => $mobileno,
                'rider_gender' => $rider_gender,
                'rider_email' => $rider_email,
                'rider_password' => $rider_password,
                'rider_liceneno' => $rider_liceneno,
                'rider_profession' => $rider_profession,
                'rider_address' => $rider_address,
                'rider_vehicle_manufacturing' => $rider_vehicle_manufacturing,
                'rider_vehicle_model' => $rider_vehicle_model,
                'rider_vehicle_year' => $rider_vehicle_year,
                'rider_vehicle_color' => $rider_vehicle_color,
                'rider_vehicle_bikeno' => $rider_vehicle_bikeno,
                'rider_econtactname' => $rider_econtactname,
                'rider_emobileno' => $rider_emobileno,
                'rider_date' => date('Y-m-d'),
                'rider_time' => date('H:i:s')
            );
            $riderid = $this->webservice_model->rider_registration_insert($riderdata);
            if ($riderid != null) {
                //$riderid = $this->webservice_model->getriderid();
                $bookid1 = $riderid;
                if ($referralcode == "") {
                    $Caracteres = 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789';
                    $QuantidadeCaracteres = strlen($Caracteres);
                    $QuantidadeCaracteres--;
                    $qtd = 3;
                    $Hash = NULL;
                    for ($x = 1; $x <= $qtd; $x++) {
                        $Posicao = rand(0, $QuantidadeCaracteres);
                        $Hash .= substr($Caracteres, $Posicao, 1);
                    }
                    $code1 = "HBCHN" . $Hash;
                    $code = $code1 . $bookid1 . "R";
                    $received = "";
                } else {
                    $code = $referralcode;
                    $v2 = $this->webservice_model->get_referralcode($code);
                    $received = $v2->rider_id;
                }
            // $id = $this->webservice_model->getriderid();
            $i = $riderid;
            $referralcode = $this->input->post('rider_referralcode', TRUE);
            $profilepicture = $this->input->post('rider_picture', TRUE);
            if ($referralcode == null && $profilepicture == null) {
                $data = file_get_contents("php://input");
                $data = (array) json_decode($data);
                $referralcode = $data['rider_referralcode'];
                $profilepicture = $data['rider_picture'];
            }
            // PROFILE Image.!!!
            $profile_name = $_FILES['rider_picture']['name'];
            if (!$profile_name) {
                $data1 = 'data:image/png;base64,' . $profilepicture . '';
                list($type, $data1) = explode(';', $data1);
                list(, $data1) = explode(',', $data1);
                $data1 = base64_decode($data1);
                $profile = "Profile_" . $i . ".png";
            } else {
                $image = addslashes($_FILES['rider_picture']['tmp_name']);
                $image = file_get_contents($image);
                $dataUri = base64_encode($image);
                $data1 = 'data:image/png;base64,' . $dataUri . '';
                list($type, $data1) = explode(';', $data1);
                list(, $data1) = explode(',', $data1);
                $data1 = base64_decode($data1);
                $profile = "Profile_" . $i . ".png";
            }

            $licenecopy = $this->input->post('rider_licenecopy', TRUE);
            if ($licenecopy == null) {
                $data = file_get_contents("php://input");
                $data = (array) json_decode($data);
                $licenecopy = $data['rider_licenecopy'];
            }
            // License Image.!!!
            $licenecopy = $_FILES['rider_licenecopy']['name'];
            if (!$profile_name) {
                $data2 = 'data:image/png;base64,' . $licenecopy . '';
                list($type, $data2) = explode(';', $data2);
                list(, $data2) = explode(',', $data2);
                $data2 = base64_decode($data2);
                $licene = "Licence_" . $i . ".png";
            } else {
                $image1 = addslashes($_FILES['rider_licenecopy']['tmp_name']);
                $image1 = file_get_contents($image1);
                $dataUri1 = base64_encode($image1);
                $data2 = 'data:image/png;base64,' . $dataUri1 . '';
                list($type, $data2) = explode(';', $data2);
                list(, $data2) = explode(',', $data2);
                $data2 = base64_decode($data2);
                $licene = "Licence_" . $i . ".png";
            }
            $insurancecopy = $this->input->post('rider_insurancecopy', TRUE);
            if ($insurancecopy == null) {
                $data = file_get_contents("php://input");
                $data = (array) json_decode($data);
                $insurancecopy = $data['rider_insurancecopy'];
            }
            // Insurance Image.!!!
            $insurancecopy = $_FILES['rider_insurancecopy']['name'];
            if (!$insurancecopy) {
                $data3 = 'data:image/png;base64,' . $insurancecopy . '';
                list($type, $data3) = explode(';', $data3);
                list(, $data3) = explode(',', $data3);
                $data3 = base64_decode($data3);
                $insurance = "Insurance_" . $i . ".png";
            } else {
                $image2 = addslashes($_FILES['rider_insurancecopy']['tmp_name']);
                $image2 = file_get_contents($image2);
                $dataUri2 = base64_encode($image2);
                $data3 = 'data:image/png;base64,' . $dataUri2 . '';
                list($type, $data3) = explode(';', $data3);
                list(, $data3) = explode(',', $data3);
                $data3 = base64_decode($data3);
                $insurance = "Insurance_" . $i . ".png";
            }

            $rcbookcopy = $this->input->post('rider_rcbookcopy', TRUE);
            if ($rcbookcopy == null) {
                $data = file_get_contents("php://input");
                $data = (array) json_decode($data);
                $rcbookcopy = $data['rider_rcbookcopy'];
            }

            // RC-Book Image.!!!
            $rcbookcopy = $_FILES['rider_rcbookcopy']['name'];
            if (!$rcbookcopy) {
                $data4 = 'data:image/png;base64,' . $rcbookcopy . '';
                list($type, $data4) = explode(';', $data4);
                list(, $data4) = explode(',', $data4);
                $data4 = base64_decode($data4);
                $rcbook = "Rcbook_" . $i . ".png";
            } else {
                $image3 = addslashes($_FILES['rider_rcbookcopy']['tmp_name']);
                $image3 = file_get_contents($image3);
                $dataUri3 = base64_encode($image3);
                $data4 = 'data:image/png;base64,' . $dataUri3 . '';
                list($type, $data4) = explode(';', $data4);
                list(, $data4) = explode(',', $data4);
                $data4 = base64_decode($data4);
                $rcbook = "Rcbook_" . $i . ".png";
            }
            $v = $this->webservice_model->rider_update_image($bookid1,$profile,$licene,$insurance,$rcbook);
                $v = $this->webservice_model->rider_update_referralcode($bookid1, $code, $received);
                file_put_contents('./uploads/profile/' . "Profile_" . $i . '.png', $data1);
                file_put_contents('./uploads/License/' . "Licence_" . $i . '.png', $data2);
                file_put_contents('./uploads/Insurance/' . "Insurance_" . $i . '.png', $data3);
                file_put_contents('./uploads/Rcbook/' . "Rcbook_" . $i . '.png', $data4);
                $profile = "Profile_" . $i . '.png';
                $licene = "Licence_" . $i . '.png';
                $insurance = "Insurance_" . $i . '.png';
                $rccopy = "Rcbook_" . $i . '.png';
                $ids = $this->webservice_model->getrideriddata($riderid);
                $dataid = $ids->rider_id;
                $dataprofile = $ids->rider_picture;
                $datalicene = $ids->rider_licenecopy;
                $datainsurance = $ids->rider_insurancecopy;
                $datarccopy = $ids->rider_rcbookcopy;
                $this->load->library('image_lib');
                if ($dataid == $i && $dataprofile == $profile) {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = './uploads/profile/' . "Profile_" . $i . '.png';
                    $config['height'] = "50";
                    $config['width'] = "50";
                    $config['new_image'] = './tempuploads/Tempprofile/';
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                }
                if ($dataid == $i && $datalicene == $licene) {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = './uploads/License/' . "Licence_" . $i . '.png';
                    $config['height'] = "50";
                    $config['width'] = "50";
                    $config['new_image'] = './tempuploads/TempLicense/';
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                }
                if ($dataid == $i && $datainsurance == $insurance) {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = './uploads/Insurance/' . "Insurance_" . $i . '.png';
                    $config['height'] = "50";
                    $config['width'] = "50";
                    $config['new_image'] = './tempuploads/TempInsurance/';
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                }
                if ($dataid == $i && $datarccopy == $rccopy) {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = './uploads/Rcbook/' . "Rcbook_" . $i . '.png';
                    $config['height'] = "50";
                    $config['width'] = "50";
                    $config['new_image'] = './tempuploads/TempRcbook/';
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                }
                $data['status'] = '1';
                $data['message'] = 'Rider Registration Successfully';
                $data['id'] = $ids->rider_id;
                $data['mobile_no'] = $ids->rider_mobileno;
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            } else {
                $data['status'] = '0';
                $data['message'] = 'Rider Registration Failed';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        }
    }

    public function rider_registration_update() {
        $rider = $this->input->post('id', TRUE);
//        $profilepicture = $this->input->post('rider_picture', TRUE);
        $rider_name = $this->input->post('rider_name', TRUE);
        $rider_mobileno = $this->input->post('rider_mobileno', TRUE);
        $rider_gender = $this->input->post('rider_gender', TRUE);
        $rider_email = $this->input->post('rider_email', TRUE);
        $rider_profession = $this->input->post('rider_profession', TRUE);
        $rider_address = $this->input->post('rider_address', TRUE);
        $rider_vehicle_manufacturing = $this->input->post('rider_vehicle_manufacturing', TRUE);
        $rider_vehicle_model = $this->input->post('rider_vehicle_model', TRUE);
        $rider_vehicle_year = $this->input->post('rider_vehicle_year', TRUE);
        $rider_vehicle_color = $this->input->post('rider_vehicle_color', TRUE);
        $rider_econtactname = $this->input->post('rider_econtactname', TRUE);
        $rider_emobileno = $this->input->post('rider_emobileno', TRUE);
        //IPHONE Values
        if ($rider == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $rider = $data['id'];
//            $profilepicture = $data['rider_picture'];
            $rider_name = $data['rider_name'];
            $rider_mobileno = $data['rider_mobileno'];
            $rider_gender = $data['rider_gender'];
            $rider_email = $data['rider_email'];
            $rider_profession = $data['rider_profession'];
            $rider_address = $data['rider_address'];
            $rider_vehicle_manufacturing = $data['rider_vehicle_manufacturing'];
            $rider_vehicle_model = $data['rider_vehicle_model'];
            $rider_vehicle_year = $data['rider_vehicle_year'];
            $rider_vehicle_color = $data['rider_vehicle_color'];
            $rider_econtactname = $data['rider_econtactname'];
            $rider_emobileno = $data['rider_emobileno'];
        }
//        $i = $rider;
//        $data1 = 'data:image/png;base64,' . $profilepicture . '';
//        list($type, $data1) = explode(';', $data1);
//        list(, $data1) = explode(',', $data1);
//        $data1 = base64_decode($data1);
//        $profile = "Profile_" . $i . ".png";

        $riderdata = array(
            'rider_name' => $rider_name,
            'rider_mobileno' => $rider_mobileno,
            'rider_gender' => $rider_gender,
            'rider_email' => $rider_email,
            'rider_profession' => $rider_profession,
            'rider_address' => $rider_address,
            'rider_vehicle_manufacturing' => $rider_vehicle_manufacturing,
            'rider_vehicle_model' => $rider_vehicle_model,
            'rider_vehicle_year' => $rider_vehicle_year,
            'rider_vehicle_color' => $rider_vehicle_color,
            'rider_econtactname' => $rider_econtactname,
            'rider_emobileno' => $rider_emobileno,
            'rider_date' => date('Y-m-d'),
            'rider_time' => date('H:i:s')
        );

        if ($this->webservice_model->rider_registration_updateprocess($riderdata, $rider) != false) {
//            file_put_contents('./uploads/profile/' . "Profile_" . $i . '.png', $data1);
//            file_put_contents('./uploads/License/' . "Licence_" . $i . '.png', $data2);
//            file_put_contents('./uploads/Insurance/' . "Insurance_" . $i . '.png', $data3);
//            file_put_contents('./uploads/Rcbook/' . "Rcbook_" . $i . '.png', $data4);
//            $profile = "Profile_" . $i . '.png';
//            $licene = "Licence_" . $i . '.png';
//            $insurance = "Insurance_" . $i . '.png';
//            $rccopy = "Rcbook_" . $i . '.png';
//            $ids = $this->webservice_model->getrideriddata($i);
//            $dataid = $ids->rider_id;
//            $dataprofile = $ids->rider_picture;
//            $datalicene = $ids->rider_licenecopy;
//            $datainsurance = $ids->rider_insurancecopy;
//            $datarccopy = $ids->rider_rcbookcopy;
//            $this->load->library('image_lib');
//            if ($dataid == $i && $dataprofile == $profile) {
//                $config['image_library'] = 'gd2';
//                $config['source_image'] = './uploads/profile/' . "Profile_" . $i . '.png';
//                $config['height'] = "50";
//                $config['width'] = "50";
//                $config['new_image'] = './tempuploads/Tempprofile/';
//                $this->image_lib->initialize($config);
//                $this->image_lib->resize();
//            }
            $data['status'] = '1';
            $data['message'] = 'Rider Registration updated';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Rider Registration not update';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function user_registration() {
        /* $id = $this->webservice_model->getuserid();
          $i = $id->user_id + 1;
          $userimage1 = $this->input->post('user_profile_image', TRUE);
          $data4 = 'data:image/png;base64,'. $userimage1 .'';
          list($type, $data4) = explode(';', $data4);
          list(, $data4)      = explode(',', $data4);
          $data4 = base64_decode($data4);
          $userimage ="userprofile_".$i.".png"; */

        $email = $this->input->post('user_email', TRUE);
        $referralcode = $this->input->post('user_referralcode', TRUE);
        $user_name = $this->input->post('user_name', TRUE);
        $user_mobileno = $this->input->post('user_mobileno', TRUE);
        $user_gender = $this->input->post('user_gender', TRUE);
        $user_email = $this->input->post('user_email', TRUE);
        $user_password = md5($this->input->post('user_password', TRUE));
        $user_econtactname = $this->input->post('user_econtactname', TRUE);
        $user_emobileno = $this->input->post('user_emobileno', TRUE);
        //IPHONE Get Values.!!!!
        if ($user_email == null && $user_name == null && $user_mobileno == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $email = $data['user_email'];
            $referralcode = $data['user_referralcode'];
            $user_name = $data['user_name'];
            $user_mobileno = $data['user_mobileno'];
            $user_gender = $data['user_gender'];
            $user_email = $data['user_email'];
            $user_password = md5($data['user_password']);
            $user_econtactname = $data['user_econtactname'];
            $user_emobileno = str_replace(" ","",$data['user_emobileno']);
        }
        $userresult = $this->webservice_model->user_registration_check($email, $user_mobileno);
        if (isset($userresult) && $userresult->user_email == $email) {
            $data['status'] = '0';
            $data['message'] = 'Email-Id already exist.';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else if (isset($userresult) && $userresult->user_mobileno == $user_mobileno) {
            $data['status'] = '0';
            $data['message'] = 'Mobile Number already exist.';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $userdata = array(
                'user_name' => $user_name,
                'user_mobileno' => $user_mobileno,
                'user_gender' => $user_gender,
                'user_email' => $user_email,
                'user_password' => $user_password,
                'user_ename' => $user_econtactname,
                'user_emobileno' => $user_emobileno,
                // 'user_image' => $userimage,
                'user_date' => date('Y-m-d'),
                'user_time' => date('H:i:s')
            );
            if ($this->webservice_model->user_registration_proces($userdata) != false) {
                $uid = $this->webservice_model->getuserid();
                $um = $uid->user_id;
                if ($referralcode == "") {
                    $Caracteres = 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789';
                    $QuantidadeCaracteres = strlen($Caracteres);
                    $QuantidadeCaracteres--;
                    $qtd = 3;
                    $Hash = NULL;
                    for ($x = 1; $x <= $qtd; $x++) {
                        $Posicao = rand(0, $QuantidadeCaracteres);
                        $Hash .= substr($Caracteres, $Posicao, 1);
                    }
                    $code1 = "HBCHN" . $Hash;
                    $code = $code1 . $um . "U";
                    $received = "";
                } else {
                    $code = $referralcode;
                    $v2 = $this->webservice_model->get_referralcode_user($code);
                    $received = $v2->rider_id;
                }
                //  file_put_contents('./uploads/userprofile/'."userprofile_".$i.'.png', $data4);
                $v = $this->webservice_model->user_update_referralcode($um, $code, $received);
                $m = $this->webservice_model->getusermn($um);
                /* $profile ="userprofile_".$i.'.png';
                  $dataid = $m->user_id;
                  $dataprofile = $m->user_image;
                  $this->load->library('image_lib');
                  if($dataid == $i && $dataprofile == $profile)
                  {
                  $config['image_library'] = 'gd2';
                  $config['source_image'] = './uploads/userprofile/'."userprofile_".$i.'.png';
                  $config['height'] = "50";
                  $config['width'] = "50";
                  $config['new_image'] = './tempuploads/Tempuserprofile/';
                  $this->image_lib->initialize($config);
                  $this->image_lib->resize();
                  } */

                $data['id'] = $m->user_id;
                $data['status'] = '1';
                $data['message'] = 'User Registration Successfully';
                $data['mobile_no'] = $m->user_mobileno;
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            } else {
                $data['status'] = '0';
                $data['message'] = 'User Registration Failed';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        }
    }

    public function user_profileimage_update() {
        $id = $this->input->post('userid', TRUE);
        $userimage1 = $this->input->post('userimage', TRUE);
        $data4 = 'data:image/png;base64,' . $userimage1 . '';
        list($type, $data4) = explode(';', $data4);
        list(, $data4) = explode(',', $data4);
        $data4 = base64_decode($data4);
        $userimage = "userprofile_" . $id . ".png";

        if ($this->webservice_model->user_profileimage_proces($id, $userimage) != false) {
            file_put_contents('./uploads/userprofile/' . "userprofile_" . $id . '.png', $data4);
            $um = $id;
            $m = $this->webservice_model->getusermn($um);
            $profile = "userprofile_" . $id . '.png';
            $dataid = $m->user_id;
            $dataprofile = $m->user_image;
            $this->load->library('image_lib');
            if ($dataid == $i && $dataprofile == $profile) {
                $config['image_library'] = 'gd2';
                $config['source_image'] = './uploads/userprofile/' . "userprofile_" . $id . '.png';
                $config['height'] = "50";
                $config['width'] = "50";
                $config['new_image'] = './tempuploads/Tempuserprofile/';
                $this->image_lib->initialize($config);
                $this->image_lib->resize();
            }

            $data['status'] = '1';
            $data['message'] = 'User profile update Successfully';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'User profile update Failed';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function user_registration_update() {
        $user = $this->input->post('id', TRUE);
        $user_name = $this->input->post('user_name', TRUE);
        $user_mobileno = $this->input->post('user_mobileno', TRUE);
        $user_gender = $this->input->post('user_gender', TRUE);
        $user_email = $this->input->post('user_email', TRUE);
        $user_econtactname = $this->input->post('user_econtactname', TRUE);
        $user_emobileno = $this->input->post('user_emobileno', TRUE);
        if ($user == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $user = $data['id'];
            $user_name = $data['user_name'];
            $user_mobileno = $data['user_mobileno'];
            $user_gender = $data['user_gender'];
            $user_email = $data['user_email'];
            $user_econtactname = $data['user_econtactname'];
            $user_emobileno = $data['user_emobileno'];
        }
        /* $userimage1 = $this->input->post('user_profile_image', TRUE);
          $data4 = 'data:image/png;base64,'. $userimage1 .'';
          list($type, $data4) = explode(';', $data4);
          list(, $data4)      = explode(',', $data4);
          $data4 = base64_decode($data4);
          $userimage ="userprofile_".$user.".png"; */
        $userdata = array(
            'user_name' => $user_name,
            'user_mobileno' => $user_mobileno,
            'user_gender' => $user_gender,
            'user_email' => $user_email,
            //'user_password' => md5($this->input->post('user_password', TRUE)),
            'user_ename' => $user_econtactname,
            'user_emobileno' => $user_emobileno,
            // 'user_image' => $userimage,
            'user_date' => date('Y-m-d'),
            'user_time' => date('H:i:s')
        );
        if ($this->webservice_model->user_registration_updateprocess($userdata, $user) != false) {

            /* 	file_put_contents('./uploads/userprofile/'."userprofile_".$user.'.png', $data4);
              $um = $user;
              $m = $this->webservice_model->getusermn($um);
              $profile ="userprofile_".$user.'.png';
              $dataid = $m->user_id;
              $dataprofile = $m->user_image;
              $this->load->library('image_lib');
              if($dataid == $i && $dataprofile == $profile)
              {
              $config['image_library'] = 'gd2';
              $config['source_image'] = './uploads/userprofile/'."userprofile_".$user.'.png';
              $config['height'] = "50";
              $config['width'] = "50";
              $config['new_image'] = './tempuploads/Tempuserprofile/';
              $this->image_lib->initialize($config);
              $this->image_lib->resize();
              }
             */
            $data['status'] = '1';
            $data['message'] = 'User Registration updated';

            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'User Registration not updated';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /*     * ********************************** */

    public function onride_user_information_torider() {
        $bookid = $this->input->post('bookid', TRUE);
        //IPHONE Values
        if ($bookid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid = $data['bookid'];
        }
        if (($results = $this->webservice_model->get_bookiddetails($bookid)) != false) {
            $pictureprofile = base_url('tempuploads/Tempuserprofile');
            $data['status'] = '1';
            $data['message'] = 'Ride User details success';
            $data['user_id'] = $results->user_id;
            $data['user_name'] = $results->user_name;
            $data['user_image'] = $pictureprofile . "/" . $results->user_image;
            $data['user_ratings'] = $results->user_rating;
            $data['from'] = $results->book_fromlocation;
            $data['destination'] = $results->book_tolocation;
            $frmlatlong = array($results->book_fromlatitude, $results->book_fromlongitude);
            $data['from_latlong'] = implode(',', $frmlatlong);
            $tolatlong = array($results->book_tolatitude, $results->book_tolongitude);
            $data['to_latlong'] = implode(',', $tolatlong);

            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Not Recevied Ride User details ';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /*     * ************************************* */
    /*     * ************************************ */

    public function rider_find_user_information() {

        $bookid1 = $this->input->post('bookid', TRUE);
        $riderid1 = $this->input->post('riderid', TRUE);
        //IPHONE Values
        if ($bookid1 == null && $riderid1 == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid1 = $data['bookid'];
            $riderid1 = $data['riderid'];
        }
        if (($results = $this->webservice_model->get_bookidrideriddetails($bookid1, $riderid1)) != false) {
            $pictureprofile = base_url('tempuploads/Tempuserprofile');
            $data['status'] = '1';
            $data['message'] = 'Rider find User details success';
            $data['user_id'] = $results->user_id;
            //$data['book_id'] = $results->book_id;
            $data['user_name'] = $results->user_name;
            $data['user_image'] = $pictureprofile . "/" . $results->user_image;
            $data['user_ratings'] = $results->user_rating;
            $data['confirm_user_otp'] = $results->confirm_user_otp;
            $data['pickup_location'] = $results->book_fromlocation;
            $data['pickup_latlong'] = $results->book_fromlatitude . "," . $results->book_fromlongitude;
            $data['book_status'] = $results->book_status; //If User- Cancel. In frontend redirect to Home page
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Not find Ride User details ';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    //hgkjashgkjladgl
    public function rider_stop_ride() {
        $bookid1 = $this->input->post('bookid', TRUE);
        $riderid1 = $this->input->post('riderid', TRUE);
        $end_latitude = $this->input->post('end_latitude', TRUE);
        $end_longitude = $this->input->post('end_longitude', TRUE);
        //IPHONE Values
        if ($bookid1 == null && $riderid1 == null && $end_latitude == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid1 = $data['bookid'];
            $riderid1 = $data['riderid'];
            $end_latitude = $data['end_latitude'];
            $end_longitude = $data['end_longitude'];
        }
        if (($results = $this->webservice_model->get_bookidrideriddetails($bookid1, $riderid1)) != false) {
            $id = $results->book_userid;
            if (($logindetails = $this->webservice_model->getusernotification($id)) != false) {
                if ($logindetails->gcm_mobileos == 'iOS') {
                    if ($id == $logindetails->gcm_userid) {
                        $riderinfo = $this->webservice_model->getRiderandBookdetails($results->book_id, $results->allocate_riderid);
                        $startTimer = new DateTime("$riderinfo->rider_latlongtime");
                        $endTimer = new DateTime("$riderinfo->book_time");
                        $duration = $startTimer->diff($endTimer);
                        $only_distance = substr($riderinfo[0]->book_distance, 0, -3); // For ios only.!!!
                        $linkprofile = base_url('uploads/profile');
                        $riderpicture = $linkprofile . "/" . $riderinfo[0]->rider_picture;
                        $bookinfo = array('code' => 'ride_success', 'duration' => $duration->format("%I min"), 'only_distance' => "$only_distance", 'book_distance' => $riderinfo[0]->book_distance, 'rider_picture' => $riderpicture, 'bookid' => $riderinfo[0]->book_id, 'rider_id' => $riderinfo[0]->rider_id, 'book_fromlocation' => $riderinfo[0]->book_fromlocation, 'book_tolatitude' => $riderinfo[0]->book_tolatitude, 'book_tolongitude' => $riderinfo[0]->book_tolongitude, 'book_tolocation' => $riderinfo[0]->book_tolocation, 'book_distance' => $riderinfo[0]->book_distance, 'rider_latitude' => $riderinfo[0]->rider_latitude, 'rider_longitude' => $riderinfo[0]->rider_longitude, 'rider_name' => $riderinfo[0]->rider_name, 'rider_mobileno' => $riderinfo[0]->rider_mobileno, 'rider_vehicle_manufacturing' => $riderinfo[0]->rider_vehicle_manufacturing, 'rider_vehicle_model' => $riderinfo[0]->rider_vehicle_model, 'rider_vehicle_bikeno' => $riderinfo[0]->rider_vehicle_bikeno, 'rider_vehicle_color' => $riderinfo[0]->rider_vehicle_color, 'book_fromlatitude' => $riderinfo[0]->book_fromlatitude, 'book_fromlongitude' => $riderinfo[0]->book_fromlongitude, 'book_date' => $riderinfo[0]->book_date, 'book_finalamount' => $riderinfo[0]->book_finalamount, 'book_userrating' => $riderinfo[0]->book_userrating, 'book_riderrating' => $riderinfo[0]->book_riderrating, 'book_distance' => $riderinfo[0]->book_distance);
                        $apikeyid = $this->webservice_model->getapiserverkey($logindetails->gcm_mobileos);
                        $url = $apikeyid->url;
                        $token = $logindetails->gcm_regid;
                        $serverKey = $apikeyid->apikey;
                        $title = "Ride Stopped";
                        $body = "Destination Reached";
                        $notification = array('title' => $title, 'text' => $body, 'sound' => 'default', 'badge' => '1', "rider_booking" => $bookinfo, 'click_action' => 'in.efficienza.piggyback.User_Conformation_Screen');
                        $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority' => 'high', "message" => $body, "id" => $id, "title" => $title);
                        $headers = array(
                            'Authorization: key=' . $serverKey,
                            'Content-Type: application/json'
                        );
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
                        //Send the request
                        $response = curl_exec($ch);
                        //Close requestrider_accept
                        if ($response === FALSE) {
                            die('FCM Send Error: ' . curl_error($ch));
                        }
                        curl_close($ch);
                    }
                } else {
                    if ($id == $logindetails->gcm_userid) {
                        $apikeyid = $this->webservice_model->getapikey();
                        $apiKey = $apikeyid->apikey;
                        $registatoin_ids = $logindetails->gcm_regid;
                        $id = $results->book_id;
                        $message = "Rider stoped the ride";
                        //$message = "Rider stoped the ride#ride_finished_user#piggyback#".$id;
                        $task = "ride_finished_user";
                        $url = $apikeyid->url;
                        $title = "piggyback";
                        $fields = array(
                            'to' => $registatoin_ids,
                            'priority' => "high",
                            'notification' => array("body" => $message, "sound" => "bike_start_up.mp3", 'click_action' => 'in.efficienza.piggyback.User_Conformation_Screen'),
                            'data' => array("message" => $message, "task" => $task, "id" => $id, "title" => $title)
                        );
                        $headers = array(
                            'Authorization: key=' . $apiKey,
                            'Content-Type: application/json'
                        );
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                        $result = curl_exec($ch);
                        curl_close($ch);
                    }
                }
            }
            $userdata = array(
                'book_endlatitude' => $end_latitude,
                'book_endlongitude' => $end_longitude,
                'book_enddate' => date('Y-m-d'),
                'book_endtime' => date('Y-m-d H:i:s'),
                'book_status' => 'completed'
            );

            $this->webservice_model->stop_book_latitudelongitude($bookid1, $userdata);
            $book_details=$this->webservice_model->booking_status_return($user_id,$bookid1,'User');
            $startride_time = new DateTime("$book_details->book_starttime");
            $endride_time = new DateTime("$book_details->book_endtime");
            $duration_time = $startride_time->diff($endride_time);
            $duration=$duration_time->format("%I");
            $rt = $this->webservice_model->ratecarddetails();
            $amt = $rt->ratecard_basefare;
            $defaultkilometer = $rt->ratecard_kilometer;
            $meter13 = substr($book_details->book_distance, 0, -3);
            if ($defaultkilometer >= $meter13) {
                $amount = $book_details->book_amount + ($rt->ratecard_time * $duration);
            } else {
                $amount = $book_details->book_amount + ($rt->ratecard_time * $duration);
            }
            $bookingamount = array(
                'book_amount' => $amount,
                'book_finalamount' => $amount
            );
            $kilo = $this->webservice_model->update_kilometer_details($bookid1, $bookingamount);

            $data['status'] = '1';
            $data['message'] = 'Rider Stop Ride details success';
            $data['book_id'] = $results->book_id;
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Rider Stop Ride details Failed ';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function rider_bill_detail() {
        $bookid1 = $this->input->post('bookid', TRUE);
        $riderid1 = $this->input->post('riderid', TRUE);
        //IPHONE Values
        if ($bookid1 == null && $riderid1 == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid1 = $data['bookid'];
            $riderid1 = $data['riderid'];
        }
        if (($result = $this->webservice_model->get_bookidrideriddetails($bookid1, $riderid1)) != false) {
            //$kilometer = $this->webservice_model->get_kilometer_details($bookid1);
            /* $lat1 = $results->book_fromlatitude;
              $lon1 = $results->book_fromlongitude;
              $lat2 = $results->book_tolatitude;
              $lon2 = $results->book_tolongitude;
              /*$lat1 = '13.02';
              $lon1 = '80.2181';
              $lat2 = '13.0823';
              $lon2 = '80.2754';
              $riderlatitude  = $results->book_riderlatitude;
              $riderlongitude  = $results->book_riderlongitude;
              $riderlat= explode(",", $riderlatitude);
              $riderlong= explode(",", $riderlongitude);
              for($i=0;$i<$count;$i++)
              {
              $getriderid =$riderlat[$i];
              }
              $theta = $lon1 - $lon2;
              $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
              $dist = acos($dist);
              $dist = rad2deg($dist);
              $miles = $dist * 60 * 1.1515;
              $s =$miles * 1.60934; */
            $riderlatitude = $result->book_riderlatitude;
            $riderlongitude = $result->book_riderlongitude;
            $s = substr($result->book_distance, 0, -3);
            $results = $this->webservice_model->ratecarddetails();
            $amt = $results->ratecard_basefare;
            $defaultkilometer = $results->ratecard_kilometer;
            if ($defaultkilometer >= $s) {
                $amount = $results->ratecard_basefare;
            } else {
                for ($i = $defaultkilometer; $i < $s; $i++) {
                    $amtdt = $amtdt + 5;
                }
                $amount = $amtdt + $amt + $results->ratecard_tax;
            }
            $speed = '18';
            $time = (($s * 1000) / ($speed * 5 / 18));
            $timemin = ($time / 60);
            $data['status'] = '1';
            $data['message'] = 'Rider bill details success';
            $data['distance'] = $result->book_distance;
            $data['timing'] = $timemin;
            $data['amount'] = $result->book_amount;
            //$data['sd'] =$riderlatitude;
            //$data['sd1'] =$riderlongitude;
            $data['from_location'] = $result->book_fromlocation;
            $data['to_location'] = $result->book_tolocation;
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Rider Stop Ride details Failed ';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function rider_bill_paid() {
        $userbookid = $this->input->post('bookid', TRUE);
        $userriderid = $this->input->post('riderid', TRUE);
        //IPHONE Values
        if ($userbookid == null && $userriderid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $userbookid = $data['bookid'];
            $userriderid = $data['riderid'];
        }
        if (($results = $this->webservice_model->rider_bill_paidprocess($userbookid, $userriderid)) != false) {
            $res = $this->webservice_model->rider_bill_paid_latlong($userbookid);
            $data['status'] = '1';
            $data['message'] = 'Rider bill paid success';
            $data['bookid'] = $userbookid;
            $data['from_location'] = $res->book_fromlocation;
            $data['to_location'] = $res->book_tolocation;

            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $result = $this->webservice_model->rider_bill_paiddetail($userbookid, $userriderid);
            if ($result->book_paidstatus == "Paid") {
                $data['status'] = '2';
                $data['message'] = 'Bill paid already';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            } else {
                $data['status'] = '0';
                $data['message'] = 'Information not available';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        }
    }

    public function user_bill_paid() {
        $userbookid = $this->input->post('bookid', TRUE);
        $userriderid = $this->input->post('userid', TRUE);
        //IPHONE Values
        if ($userbookid == null && $userriderid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $userbookid = $data['bookid'];
            $userriderid = $data['userid'];
        }
        if (($results = $this->webservice_model->user_bill_paidprocess($userbookid, $userriderid)) != false) {
            $res = $this->webservice_model->rider_bill_paid_latlong($userbookid);
            $data['status'] = '1';
            $data['message'] = 'User bill paid success';
            $data['bookid'] = $userbookid;
            /* $data['from_location'] =$res->book_fromlocation;
              $data['to_location'] =$res->book_tolocation; */

            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $result = $this->webservice_model->rider_bill_paiddetail($userbookid, $userriderid);
            if ($result->book_paidstatus == "Paid") {
                $data['status'] = '2';
                $data['message'] = 'Bill paid already';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            } else {
                $data['status'] = '0';
                $data['message'] = 'Information not available';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        }
    }

    /*
     *  After Ride is accepted from Rider:
     *      This method is used to get User Information from
     */

    public function rider_getting_user_mobile_number() {
        $userbkid = $this->input->post('bookid', TRUE);
        $userrid = $this->input->post('riderid', TRUE);
        //IPHONE Values
        if ($userbkid == null && $userrid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $userbkid = $data['bookid'];
            $userrid = $data['riderid'];
        }
        if (($results = $this->webservice_model->rider_getting_user_mobile_numberprocess($userbkid, $userrid)) != false) {
            $data['user_mobile_no'] = $results->user_mobileno;
            $data['status'] = '1';
            $data['message'] = 'User mobileno';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $result = $this->webservice_model->getcustomerno();
            $data['user_mobile_no'] = $result->ccareno;
            $data['status'] = '0';
            $data['message'] = 'Mobile no is not available.Call customer care';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function rider_get_rate_user_detail() {
        $userbookid = $this->input->post('bookid', TRUE);
        //IPHONE Values
        if ($userbookid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $userbookid = $data['bookid'];
        }
        if (($results = $this->webservice_model->rider_get_rate_user_detailprocess($userbookid)) != false) {
            $data['status'] = '1';
            $data['message'] = 'Rider get user details';
            $data['user_name'] = $results->user_name;
            /* not show image */
            $data['user_image'] = $results->user_image;
            $data['user_rating'] = $results->user_rating;
            $data['rate_my_service_message'] = 'How do you rate this user.:)';
            $data['from_location'] = $results->book_fromlocation;
            $data['to_location'] = $results->book_tolocation;

            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Rider Not Paid ';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function rider_located_user() {
        $bookid2 = $this->input->post('bookid', TRUE);
        $riderid2 = $this->input->post('riderid', TRUE);
        $located_status = $this->input->post('located_status', TRUE);
        $start_latitude = $this->input->post('start_latitude', TRUE);
        $start_longitude = $this->input->post('start_longitude', TRUE);
        //IPHONE Values
        if ($bookid2 == null && $riderid2 == null && $located_status == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid2 = $data['bookid'];
            $riderid2 = $data['riderid'];
            $located_status = $data['located_status'];
            $start_latitude = $data['start_latitude'];
            $start_longitude = $data['start_longitude'];
        }
        if (($results = $this->webservice_model->get_bookingsid($bookid2, $riderid2, $located_status)) != false) {
            $res = $this->webservice_model->get_booking_pickup($bookid2);
            $bookid1 = $bookid2;
            $userdata = array(
                'book_startlatitude' => $start_latitude,
                'book_startlongitude' => $start_longitude,
                'book_startdate' => date('Y-m-d'),
                'book_starttime' => date('Y-m-d H:i:s')
            );
            $this->webservice_model->stop_book_latitudelongitude($bookid1, $userdata);
            $data['book_startlatitude'] = $start_latitude; //Only for iOS
            $data['book_startlongitude'] = $start_longitude; //Only for iOS
            $data['bookid'] = $results->book_id; //Only for iOS
            $data['book_userid'] = $results->book_userid; //Only for iOS
            $data['book_status'] = $results->book_status; //Only for iOS
            $data['rider_id'] = $results->allocate_riderid; //Only for iOS
            $data['book_riderlatitude'] = $results->book_riderlatitude; //Only for iOS
            $data['book_riderlongitude'] = $results->book_riderlongitude; //Only for iOS
            $data['rider_latitude'] = $results->rider_latitude; //Only for iOS
            $data['rider_longitude'] = $results->rider_longitude; //Only for iOS
            $data['status'] = '1';
            $data['message'] = 'Rider Located User details success';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
            /*
             *  PUSH NOTIFICATION for ONLY IOS
             */
            if (($logindetails = $this->webservice_model->getusernotification($results->book_userid)) != false) {
                if ($logindetails->gcm_mobileos == 'iOS') {
                    $id = $results->book_userid;
                    if ($id == $logindetails->gcm_userid) {
                        $riderinfo = $this->webservice_model->getRiderandBookdetails($results->book_id, $results->allocate_riderid);
                        $bookinfo = array('code' => 'user_located', 'book_startlatitude' => "$start_latitude", 'book_startlongitude' => "$start_longitude", 'bookid' => $riderinfo[0]->book_id, 'book_userid' => $riderinfo[0]->book_userid, 'book_riderlatitude' => $riderinfo[0]->book_riderlatitude, 'book_riderlongitude' => $riderinfo[0]->book_riderlongitude, 'book_tolongitude' => $riderinfo[0]->book_tolongitude, 'book_tolocation' => $riderinfo[0]->book_tolocation, 'book_distance' => $riderinfo[0]->book_distance, 'rider_latitude' => $riderinfo[0]->rider_latitude, 'rider_longitude' => $riderinfo[0]->rider_longitude, 'rider_id' => $riderinfo[0]->rider_id, 'rider_name' => $riderinfo[0]->rider_name, 'rider_mobileno' => $riderinfo[0]->rider_mobileno, 'book_status' => $riderinfo[0]->book_status, 'confirm_user_otp' => $riderinfo[0]->confirm_user_otp);
                        $apikeyid = $this->webservice_model->getapiserverkey($logindetails->gcm_mobileos);
                        $url = $apikeyid->url;
                        $token = $logindetails->gcm_regid;
                        $serverKey = $apikeyid->apikey;
                        $title = "Rider Located User";
                        $body = "Rider Located User";
                        $notification = array('title' => $title, 'text' => $body, 'sound' => 'default', 'badge' => '1', "rider_booking" => $bookinfo, 'click_action' => 'in.efficienza.piggyback.User_Conformation_Screen');
                        $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority' => 'high', "message" => $body, "id" => $id, "title" => $title);
                        $headers = array(
                            'Authorization: key=' . $serverKey,
                            'Content-Type: application/json'
                        );
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
                        //Send the request
                        $response = curl_exec($ch);
                        //Close requestrider_accept
                        if ($response === FALSE) {
                            die('FCM Send Error: ' . curl_error($ch));
                        }
                        curl_close($ch);
                    }
                }
            }
        } else {
            $data['status'] = '0';
            $data['message'] = 'Not find Ride User details ';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function rider_submit_rating() {
        $rider_bookid = $this->input->post('bookid', TRUE);
        $rider_riderid = $this->input->post('riderid', TRUE);
        $rider_rating = $this->input->post('rating_to_user', TRUE);
        //IPHONE Values
        if ($rider_bookid == null && $rider_riderid == null && $rider_rating == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $rider_bookid = $data['bookid'];
            $rider_riderid = $data['riderid'];
            $rider_rating = $data['rating_to_user'];
        }
        if (($results = $this->webservice_model->rider_submit_ratingprocess($rider_bookid, $rider_riderid, $rider_rating)) != false) {
            $this->webservice_model->rider_submit_ratingprocess1($rider_bookid, $rider_riderid, $rider_rating);
            $this->webservice_model->change_bookingstatusrider1($rider_riderid);
            $data['status'] = '1';
            $data['message'] = 'Rating done';
            //$data['user_name'] = $results->user_name;
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $result = $this->webservice_model->rider_submit_ratingdetail($rider_bookid, $rider_riderid, $rider_rating);
            if ($result->book_userrating != "") {
                $this->webservice_model->rider_submit_ratingprocess1($rider_bookid, $rider_riderid, $rider_rating);
                $data['status'] = '2';
                $data['message'] = 'Already rated';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            } else {
                $this->webservice_model->rider_submit_ratingprocess1($rider_bookid, $rider_riderid, $rider_rating);
                $data['status'] = '0';
                $data['message'] = 'Information not available';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        }
    }

    public function user_submit_rating() {
        $user_bookid = $this->input->post('bookid', TRUE);
        $user_riderid = $this->input->post('userid', TRUE);
        $user_rating = $this->input->post('rating_to_rider', TRUE);
        //IPHONE Values
        if ($user_bookid == null && $user_riderid == null && $user_rating == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $user_bookid = $data['bookid'];
            $user_riderid = $data['userid'];
            $user_rating = $data['rating_to_rider'];
        }
        if (($results = $this->webservice_model->user_submit_ratingprocess($user_bookid, $user_riderid, $user_rating)) != false) {
            $this->webservice_model->user_submit_ratingprocess1($user_bookid, $user_riderid, $user_rating);
            $data['status'] = '1';
            $data['message'] = 'user rating detailed updated';
            //$data['user_name'] = $results->user_name;
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $result = $this->webservice_model->user_submit_ratingdetail($user_bookid, $user_riderid, $user_rating);
            if ($result->book_riderrating != "") {
                $this->webservice_model->user_submit_ratingprocess1($user_bookid, $user_riderid, $user_rating);
                $data['status'] = '2';
                $data['message'] = 'Already rated';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            } else {
                $this->webservice_model->user_submit_ratingprocess1($user_bookid, $user_riderid, $user_rating);
                $data['status'] = '0';
                $data['message'] = 'Information not available';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        }
    }

    public function userbilldetails_with_riderinfo() {
        $bookid = $this->input->post('bookid', TRUE);
        //IPHONE Values
        if ($bookid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid = $data['bookid'];
        }
        if (($results = $this->webservice_model->userbilldetails_with_riderinfoprocess($bookid)) != false) {
            /* /* $lat1 = $results->book_fromlatitude;
              $lon1 = $results->book_fromlongitude;
              $lat2 = $results->book_tolatitude;
              $lon2 = $results->book_tolongitude;
              $lat1 = '13.030013071992087';
              $lon1 = '80.24524133652449';
              $lat2 = '13.00117726849827';
              $lon2 = '80.25649555027486';
              $theta = $lon1 - $lon2;
              $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
              $dist = acos($dist);
              $dist = rad2deg($dist);
              $miles = $dist * 60 * 1.1515;
              $s =$miles * 1.60934; */
            $s = substr($results->book_distance, 0, -3);
            /* $result = $this->webservice_model->ratecarddetails();
              $amount = $result->ratecard_basefare;
              $defaultkilometer=$result->ratecard_kilometer;

              if($defaultkilometer > $s)
              {
              $amount = $result->ratecard_basefare;
              }

              for($i=$defaultkilometer; $i< $s; $i++)
              {
              $amount =$amount + 5;
              } */
            $start_time = $results->book_starttime;
            $end_time = $results->book_endtime;
            $now = new DateTime("$start_time");
            $ref = new DateTime("$end_time");
            $diff = $now->diff($ref); //"$diff->d :"; print_r($diff);exit;
            $data['total_min'] = "$diff->h:" . "$diff->i";
            $speed = '18';
            $time = (($s * 1000) / ($speed * 5 / 18)); // Distance Divided by Speed is calculated for TIME.!!!
            $timemin = ($time / 60);
            $linkprofile = base_url('uploads/profile');
            $data['status'] = '1';
            $data['message'] = 'Bill information reterived';
            $data['rider_id'] = $results->rider_id;
            $data['rider_name'] = $results->rider_name;
            $data['rider_image'] = $linkprofile . "/" . $results->rider_picture;
            $data['rider_rating'] = $results->rider_rating;
            $data['bike_manufacture'] = $results->rider_vehicle_manufacturing;
            $data['bike_modal'] = $results->rider_vehicle_model;
            $data['bike_color'] = $results->rider_vehicle_color;
            $data['bike_number'] = $results->rider_vehicle_bikeno;
            $data['total_km'] = $results->book_distance;
            $data['total_time'] = $timemin;
            $data['total_amount'] = $results->book_finalamount;
            $data['rating_message'] = 'Rate my service for further improvements.:)';
            $data['from_location'] = $results->book_fromlocation;
            $data['to_location'] = $results->book_tolocation;
            $timestamp1 = strtotime("$results->book_endtime");
            $time1 = date('H:i a', $timestamp1);
            $datestamp1 = strtotime("$results->book_enddate");
            $date1 = date('D, d M y', $datestamp1);
            $data['date_time'] = $date1 . ", " . $time1;
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'user rating detailed already updated';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /*     * ************************************ */

    public function login_proces() {
        $sub = $this->webservice_model->remove_subscripe_count();
        $emailid = $this->input->post('useremail', TRUE);
        $password = $this->input->post('userpassword', TRUE);
        $type = $this->input->post('userlogintype', TRUE);
        $mobileSerialNo = $this->input->post('mobileSerialNo', TRUE);
        if ($emailid == null && $password == null && $type == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $emailid = $data['useremail'];
            $password = $data['userpassword'];
            $type = $data['userlogintype'];
            $mobileSerialNo = $data['mobileSerialNo'];
        }
        if (($logindetails = $this->webservice_model->login_check($emailid, $password, $type)) != false) {
            $user_id = (isset($logindetails->user_id)) ? $logindetails->user_id : $logindetails->rider_id;
            $user_status = (isset($logindetails->user_status)) ? $logindetails->user_status : $logindetails->rider_status;
            $gcm_details = $this->webservice_model->get_ridergcmdetail($user_id, $type);
            $gcm_serialno = ($gcm_details != null) ? $gcm_details->gcm_serialno : "";
            if ($gcm_serialno == $mobileSerialNo || $gcm_serialno == null) {
                if ($mobileSerialNo) {
                    $gcm_update = $this->webservice_model->update_gcmserialno($user_id, $mobileSerialNo, $type);
                }
                if ($type == 'User') {
                    if ($logindetails->user_otpverify == 1) {
                        // Check whether the RIDER on the same number Logged in this mobile.!!
                        $users_riderCheck = $this->webservice_model->checkUsersRiderLogged($emailid, $type);
                        if ($users_riderCheck) {
                            $data['status'] = '0';
                            $data['message'] = 'Please Logout your Rider Account';
                            $data['gcm_serialno'] = $gcm_serialno;
                            $data = array('response' => $data);
                            $this->load->view('webservices/json', $data);
                        } else {
                            $userid = $logindetails->user_id;
                            $l = $this->webservice_model->login_userupdatestatus($userid);
                            $data['userid'] = $logindetails->user_id;
                            $data['referralcode'] = $logindetails->user_referralcode;
                            $data['username'] = $logindetails->user_name;
                            $data['usermbno'] = $logindetails->user_mobileno;
                            $data['usergender'] = $logindetails->user_gender;
                            $data['useremail'] = $logindetails->user_email;
                            $data['userename'] = $logindetails->user_ename;
                            $data['userembno'] = $logindetails->user_emobileno;
                            /* 	$data['userpicture'] = $linkprofile ."/".$logindetails->user_image;
                              $data['userpicturetemp'] = $linkprofiletemp ."/".$logindetails->user_image; */
                            $data['status'] = '1';
                            $data['gcm_serialno'] = $gcm_serialno;
                            $data['message'] = 'Login Success';
                            $data = array('response' => $data);
                            $this->load->view('webservices/json', $data);
                        }
                    } else {
                        $data['id'] = $logindetails->user_id;
                        $data['mobile_no'] = $logindetails->user_mobileno;
                        $data['Type'] = "User";
                        $data['status'] = '2';
                        $data['gcm_serialno'] = $gcm_serialno;
                        $data['message'] = 'Verify your mobilno by entering otp';
                        $data = array('response' => $data);
                        $this->load->view('webservices/json', $data);
                    }
                } elseif ($type == 'Rider') {
                    if ($logindetails->rider_otpverify == 1) {
                        $id = $logindetails->rider_id;
                        $status = '1';
                        $this->webservice_model->rider_status_check($id, $status);
                        if ($logindetails->rider_adminaccept == 1) {
                            // Check whether the RIDER on the same number Logged in this mobile.!!
                            $users_riderCheck = $this->webservice_model->checkUsersRiderLogged($emailid, $type);
                            if ($users_riderCheck) {
                                $data['status'] = '0';
                                $data['message'] = 'Please Logout your User Account';
                                $data['gcm_serialno'] = $gcm_serialno;
                                $data = array('response' => $data);
                                $this->load->view('webservices/json', $data);
                            } else {
                                //$image_link = urlencode("http://13.76.240.201/images/");
                                $linkprofile = base_url('uploads/profile');
                                $linklicense = base_url('uploads/License');
                                $linkinsurance = base_url('uploads/Insurance');
                                $linkrcbook = base_url('uploads/Rcbook');
                                $linkprofiletemp = base_url('tempuploads/Tempprofile');
                                $linklicensetemp = base_url('tempuploads/TempLicense');
                                $linkinsurancetemp = base_url('tempuploads/TempInsurance');
                                $linkrcbooktemp = base_url('tempuploads/TempRcbook');
                                $data['riderid'] = $logindetails->rider_id;
                                $data['referralcode'] = $logindetails->rider_referralcode;
                                $data['ridername'] = $logindetails->rider_name;
                                $data['ridermbno'] = $logindetails->rider_mobileno;
                                $data['ridergender'] = $logindetails->rider_gender;
                                $data['rideremail'] = $logindetails->rider_email;
                                $data['riderliceneno'] = $logindetails->rider_liceneno;
                                $data['riderprofession'] = $logindetails->rider_profession;
                                $data['rideraddress'] = $logindetails->rider_address;
                                $data['ridervehicle'] = $logindetails->rider_vehicle_manufacturing;
                                $data['ridervehiclemodel'] = $logindetails->rider_vehicle_model;
                                $data['ridervehicleyear'] = $logindetails->rider_vehicle_year;
                                $data['ridervehiclecolor'] = $logindetails->rider_vehicle_color;
                                $data['ridervehiclebikeno'] = $logindetails->rider_vehicle_bikeno;
                                $data['riderecontactname'] = $logindetails->rider_econtactname;
                                $data['rideremobileno'] = $logindetails->rider_emobileno;
                                $data['riderpicture'] = $linkprofile . "/" . $logindetails->rider_picture;
                                $data['riderlicenecopy'] = $linklicense . "/" . $logindetails->rider_licenecopy;
                                $data['riderinsurancecopy'] = $linkinsurance . "/" . $logindetails->rider_insurancecopy;
                                $data['riderrcbookcopy'] = $linkrcbook . "/" . $logindetails->rider_rcbookcopy;
                                $data['riderpicturetemp'] = $linkprofiletemp . "/" . $logindetails->rider_picture;
                                $data['riderlicenecopytemp'] = $linklicensetemp . "/" . $logindetails->rider_licenecopy;
                                $data['riderinsurancecopytemp'] = $linkinsurancetemp . "/" . $logindetails->rider_insurancecopy;
                                $data['riderrcbookcopytemp'] = $linkrcbooktemp . "/" . $logindetails->rider_rcbookcopy;
                                $data['riderstatus'] = $logindetails->rider_status;
                                $data['riderlatitude'] = $logindetails->rider_latitude;
                                $data['riderlongitude'] = $logindetails->rider_longitude;
                                $data['rider_rating'] = $logindetails->rider_rating;
                                $data['status'] = '1';
                                $data['message'] = 'Login Success';
                                $data['gcm_serialno'] = $gcm_serialno;
                                $data = array('response' => $data);
                                $this->load->view('webservices/json', $data);
                            }
                        } else {
                            $result = $this->webservice_model->getcustomerno();
                            $data['status'] = '0';
                            $data['gcm_serialno'] = $gcm_serialno;
                            $data['message'] = "You are not activated wait for sometime or call customer care-" . "$result->ccareno";
                            $data = array('response' => $data);
                            $this->load->view('webservices/json', $data);
                        }
                    } else {
                        $data['id'] = $logindetails->rider_id;
                        $data['mobile_no'] = $logindetails->rider_mobileno;
                        $data['Type'] = "Rider";
                        $data['status'] = '2';
                        $data['gcm_serialno'] = $gcm_serialno;
                        $data['message'] = 'Verify your mobilno by entering otp';
                        $data = array('response' => $data);
                        $this->load->view('webservices/json', $data);
                    }
                }
            } else {
                if ($user_status != 0) {
                    $data['status'] = '0';
                    $data['message'] = 'This User Already Logged In!!!';
                    $data['login_id'] = "$user_id";
                    $data['gcm_serialno'] = $gcm_serialno;
                    $data = array('response' => $data);
                    $this->load->view('webservices/json', $data);
                } else {
                    $data['status'] = '0';
                    $data['message'] = 'Please contact Admin. To Activate!!!';
                    $data['login_id'] = "$user_id";
                    $data['gcm_serialno'] = $gcm_serialno;
                    $data = array('response' => $data);
                    $this->load->view('webservices/json', $data);
                }
            }
        } else {
            /*
             *  Need to check whether the Given Number/Email
             *  already Exist in the System.!!!
             */
            $result = $this->webservice_model->check_user_exists($emailid,$type);
            if($result) {
            $data['status'] = '0';
            $data['message'] = 'Incorrect Email-Id or Password';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
            } else {
                $data['status'] = '3'; // NOTE: 1-status=Login,2-status=OTP,3-GivenEmailorMobileNo doesnt Exist.!!!
                $data['type']="$type";
                $data['message'] = "Email-Id or Mobile Number doesn't Exist in System";
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        }
    }

    public function OTP_verification_from_login() {
        $id = $this->input->post('email', TRUE);
        $type = $this->input->post('type', TRUE);
        if ($type == null && $id == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $type = $data['type'];
            $id = $data['email'];
        }
        if (($logindetails = $this->webservice_model->OTP_verification_from_login($id, $type)) != false) {
            if ($type == "Rider") {
                $mbno = $logindetails->rider_mobileno;
                $otp = $logindetails->rider_otp;
                $iddetails = $logindetails->rider_id;
            } else {
                $mbno = $logindetails->user_mobileno;
                $otp = $logindetails->user_otp;
                $iddetails = $logindetails->user_id;
            }

            $data['status'] = '1';
            $data['mobile_no'] = $mbno;
            $data['id'] = $iddetails;
            $data['message'] = 'Otp send Successfully';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Verify your mobile number';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function otpsendproces() {
        $mbno = $this->input->post('otpmobileno', TRUE);
        $id = $this->input->post('id', TRUE);
        $type = $this->input->post('type', TRUE);
        if ($mbno == null && $id == null && $type == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $mbno = $data['otpmobileno'];
            $id = $data['id'];
            $type = $data['type'];
        }
        //$otp = rand(100000, 999999);
        $otp = 1234;
        if ($this->webservice_model->otp_sendproces($id, $otp, $type, $mbno) != false) {
           $dlr_url = "";
           $type = "xml";
           $time = '';
           $unicode = '';
           $to = $mbno;
           $message = "Your%20piggyback%20verification%20code%20:%20" . $otp;
           $url = "http://hpsms.dial4sms.com/api/web2sms.php?workingkey=Aee31e1af01a8d305496d795179a5a8a0&sender=HTHBKR&to=$to&message=$message&type=$type&dlr_url=$dlr_url$time$unicode";
           //$url = 'http://hpsms.dial4sms.com/api/web2sms.php?username=hthbkrds&password=Admin@17&to=$m&sender=HTHBKR&message=$g';
           $ch = curl_init();
           // curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           $output = curl_exec($ch);
           if (curl_errno($ch)) {
               echo 'error:' . curl_error($ch);
           }
           curl_close($ch);
            $data['status'] = '1';
            $data['message'] = 'Otp send Successfully';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = "$id";//'Verify your mobile number';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function sms() {
        /* $phone="8428487893";
          //$msg="your otp pinno = ".$_SESSION['username']";
          $msg = "65576";
          echo "<script type='text/javascript'>window.open('http://bulksmsindia.mobi/sendurlcomma.aspx?user=20064973&pwd=tarman9614&senderid=PROJEC&mobileno=$phone&msgtext=$msg&smstype=4&priority=High')</script>";
         */




        $dlr_url = "";
        $type = "xml";
        $time = '';
        $unicode = '';
        $to = "8428487893";
        $message = "Your%20piggyback%20verification%20code%20:%20122346";
        $url = "http://hpsms.dial4sms.com/api/web2sms.php?workingkey=Aee31e1af01a8d305496d795179a5a8a0&sender=HTHBKR&to=$to&message=$message&type=$type&dlr_url=$dlr_url$time$unicode";
        //$url = 'http://hpsms.dial4sms.com/api/web2sms.php?username=hthbkrds&password=Admin@17&to=$m&sender=HTHBKR&message=$g';
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'error:' . curl_error($ch);
        }
        curl_close($ch);


        $data['output'] = $output;
        $data['status'] = '1';
        $data['message'] = 'Otp send Successfully';
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
    }

    /* public function sms()
      {
      try
      {
      $api_key = '55763CDD52D7A0';
      $contacts = "8939897706";
      $from ='WEBPRO';

      $text = "Height:180t\nWeight:56l\nAutogenerated Please do not reply";


      $sms_text = urlencode($text);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "http://open.vtel.in/app/smsapi/index.php");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, "key=" . $api_key . "&routeid=6&type=text&contacts=" . $contacts . "&senderid=" . $from . "&msg=" . $sms_text);
      $response = curl_exec($ch);
      curl_close($ch);
      }
      catch (Exception $e)
      {
      log_message("error", $e->getMessage());
      }
      } */

    public function user_information_detail() {
        $userid = $this->input->post('id', TRUE);
        if ($userid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $userid = $data['id'];
        }
        if (($results = $this->webservice_model->userinformationdetailprocess($userid)) != false) {
            $pictureprofile = base_url('tempuploads/Tempuserprofile');
            $data['user_id'] = $results->user_id;
            $data['user_name'] = $results->user_name;
            $data['user_image'] = $pictureprofile . "/" . $results->user_image;
            $data['user_mbno'] = $results->user_mobileno;
            $data['user_gender'] = $results->user_gender;
            $data['user_email'] = $results->user_email;
            $data['user_ename'] = $results->user_ename;
            $data['user_emobileno'] = $results->user_emobileno;
            $data['user_rating'] = $results->user_rating;
            $data['status'] = '1';
            $data['message'] = 'information reterived';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Not Avaliable';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function rider_information_detail() {
        $riderid = $this->input->post('id', TRUE);
        //IPHONE Values
        if ($riderid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $riderid = $data['id'];
        }
        if (($logindetails = $this->webservice_model->riderinformationdetailprocess($riderid)) != false) {
            $linkprofile = base_url('uploads/profile');
            $linklicense = base_url('uploads/License');
            $linkinsurance = base_url('uploads/Insurance');
            $linkrcbook = base_url('uploads/Rcbook');
            $linkprofiletemp = base_url('tempuploads/Tempprofile');
            $linklicensetemp = base_url('tempuploads/TempLicense');
            $linkinsurancetemp = base_url('tempuploads/TempInsurance');
            $linkrcbooktemp = base_url('tempuploads/TempRcbook');
            $data['riderid'] = $logindetails->rider_id;
            $data['ridername'] = $logindetails->rider_name;
            $data['ridermbno'] = $logindetails->rider_mobileno;
            $data['ridergender'] = $logindetails->rider_gender;
            $data['rideremail'] = $logindetails->rider_email;
            $data['riderliceneno'] = $logindetails->rider_liceneno;
            $data['riderprofession'] = $logindetails->rider_profession;
            $data['rideraddress'] = $logindetails->rider_address;
            $data['ridervehicle'] = $logindetails->rider_vehicle_manufacturing;
            $data['ridervehiclemodel'] = $logindetails->rider_vehicle_model;
            $data['ridervehicleyear'] = $logindetails->rider_vehicle_year;
            $data['ridervehiclecolor'] = $logindetails->rider_vehicle_color;
            $data['ridervehiclebikeno'] = $logindetails->rider_vehicle_bikeno;
            $data['riderecontactname'] = $logindetails->rider_econtactname;
            $data['rideremobileno'] = $logindetails->rider_emobileno;
            $data['riderpicture'] = $linkprofile . "/" . $logindetails->rider_picture;
            $data['riderlicenecopy'] = $linklicense . "/" . $logindetails->rider_licenecopy;
            $data['riderinsurancecopy'] = $linkinsurance . "/" . $logindetails->rider_insurancecopy;
            $data['riderrcbookcopy'] = $linkrcbook . "/" . $logindetails->rider_rcbookcopy;
            $data['riderpicturetemp'] = $linkprofiletemp . "/" . $logindetails->rider_picture;
            $data['riderlicenecopytemp'] = $linklicensetemp . "/" . $logindetails->rider_licenecopy;
            $data['riderinsurancecopytemp'] = $linkinsurancetemp . "/" . $logindetails->rider_insurancecopy;
            $data['riderrcbookcopytemp'] = $linkrcbooktemp . "/" . $logindetails->rider_rcbookcopy;
            $data['riderstatus'] = $logindetails->rider_status;
            $data['riderlatitude'] = $logindetails->rider_latitude;
            $data['riderlongitude'] = $logindetails->rider_longitude;
            $data['riderrating'] = $logindetails->rider_rating;
            $data['status'] = '1';
            $data['message'] = 'information reterived';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Not Avaliable';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function onride_rider_locations() {
        $bookid = $this->input->post('bookid', TRUE);
        $riderlatitude = $this->input->post('riderlatitude', TRUE);
        $riderlongitude = $this->input->post('riderlongitude', TRUE);
        if ($this->webservice_model->onride_rider_locationsprocess($bookid, $riderlatitude, $riderlongitude) != false) {
            $data['status'] = '1';
            $data['message'] = 'send';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Not send';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function onride_user_locations() {
        $bookid = $this->input->post('bookid', TRUE);
        $userlatitude = $this->input->post('userlatitude', TRUE);
        $userlongitude = $this->input->post('userlongitude', TRUE);
        if ($this->webservice_model->onride_user_locationsprocess($bookid, $userlatitude, $userlongitude) != false) {
            $data['status'] = '1';
            $data['message'] = 'send';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Not send';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function getride_user_details() {
        $bookid = $this->input->post('bookid', TRUE);
        if (($result = $this->webservice_model->getride_user_detailsprocess($bookid)) != false) {
            $riderlatitude = $result->book_riderlatitude;
            $riderlongitude = $result->book_riderlongitude;
            $riderlat = explode(",", $riderlatitude);
            $riderlong = explode(",", $riderlongitude);
            for ($i = 0; $i < $count; $i++) {
                $getriderid = $riderlat[$i];
            }
            $data['status'] = $result->book_riderlatitude;
            $data['status1'] = $result->book_riderlongitude;
            $data['message'] = 'send';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Not send';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function otploginproces() {
        $otpno = $this->input->post('otpno', TRUE);
        $id = $this->input->post('id', TRUE);
        $type = $this->input->post('type', TRUE);
        if ($otpno == null && $id == null && $type == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $otpno = $data['otpno'];
            $id = $data['id'];
            $type = $data['type'];
        }
        if ($this->webservice_model->otplogin_proces($id, $otpno, $type) != false) {
            $result = $this->webservice_model->otplogin_proces($id, $otpno, $type);
            $riderid = (isset($result->rider_id)) ? $result->rider_id : "";
            $userid = (isset($result->user_id)) ? $result->user_id : "";
            /*
             *  If User OTP get Verified previously taken to the Login page.
             *  By now it will take you the Map page in Android
             *  Below information are required to take user to Map Page.!!!!
             */
            if($type=='User') {
            $data['status'] = '1';
            $data['type'] = "$type";
            $data['user_referralcode']="$result->user_referralcode";
            $data['user_name']="$result->user_name";
            $data['user_mobileno']="$result->user_mobileno";
            $data['user_gender']="$result->user_gender";
            $data['user_email']="$result->user_email";
            $data['user_ename']="$result->user_ename";
            $data['user_emobileno']="$result->user_emobileno";
            $data['user_rating']="$result->user_rating";
            $data['user_status']="$result->user_status";
            $data['user_id'] = ("$riderid" == null) ? "$userid" : "$riderid";
            } else {
            $data['status'] = '1';
            $data['type'] = "$type";
            $data['user_id'] = ("$riderid" == null) ? "$userid" : "$riderid";
            }
            $data['message'] = 'Otp Login Successfully';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Otp Login Failed';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function rider_statusproces() {
        $id = $this->input->post('riderid', TRUE);
        $status = $this->input->post('status', TRUE);
        if ($this->webservice_model->rider_status_check($id, $status) != false) {
            $data['status'] = '1';
            $data['message'] = 'Rider is Online';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Rider is Offline';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function user_ratecard() {
        if (($results = $this->webservice_model->ratecarddetails()) != false) {
            $data['basefare'] = $results->ratecard_basefare;
            $data['basekilometer'] = $results->ratecard_kilometer;
            $data['baseperkilometer'] = $results->ratecard_perkilometer;
            $data['maximumkilometer'] = $results->ratecard_maxkilometer;
            $data['ratecard_tax'] = $results->ratecard_tax;
            $data['ratecard_time'] = $results->ratecard_time;
            $data['status'] = '1';
            $data['message'] = 'User ratecard details send success';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function rider_updatelatitudelongitude() {
        $id = $this->input->post('riderid', TRUE);
        $latitude = $this->input->post('latitude', TRUE);
        $longitude = $this->input->post('longitude', TRUE);
        //IPHONE Values
        if ($id == null && $latitude == null && $longitude == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $latitude = $data['latitude'];
            $id = $data['riderid'];
            $longitude = $data['longitude'];
        }
        $getdet=$this->Rider_registration_model->get($id);
        if (($results = $this->webservice_model->sendpushnotificationUser()) != false) {
            $data['userlist'] = array();
            foreach ($results as $result) {
                $lat1 = $latitude;
                $lon1 = $longitude;
                $lat2 = $result->user_latitude;
                $lon2 = $result->user_longitude;
                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $s = $miles * 1.60934;
                $resf = round($s, 2);
                $s = substr($meter12, 0, -3);
                // Note: (!=) Making Changes due to Minus Subscribtions
                if (ucfirst($getdet->rider_gender) === $result->user_gender) {
                    if ($resf <= 2) {
                        //if(7.00 <= date('H:i') && date('H:i') <= 21.00)
                        //{
                        $s = date('H:i:s');
                        $time = strtotime($s);
                        $startTime = date("H:i:s", strtotime('-30 minutes', $time));
                        //if($startTime <= $result->rider_latlongtime && $result->rider_latlongdate == date('Y-m-d'))
                        //{
                        $temp['userid'] = $result->user_id;
                        $temp['username'] = $result->user_name;
                        $temp['userlatitude'] = $result->user_latitude;
                        $temp['userlongitude'] = $result->user_longitude;
                        $user[] = $temp;
                        //}
                        //}
                    }
                }
            }
            $data['userlist'] = ($user != null) ? $user : "";
        }
        if ($this->webservice_model->rider_latitudelongitude_check($id, $latitude, $longitude) !== FALSE) {
            $data['status'] = '1';
            $data['message'] = 'Rider update latitude longitude';
        } else {
            $data['status'] = '0';
            $data['message'] = 'Rider not update latitude longitude';
        }
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
    }

    public function rider_updatelatitudelongitude_booking() {
        $id = $this->input->post('riderid', TRUE);
        $latitude = $this->input->post('latitude', TRUE);
        $longitude = $this->input->post('longitude', TRUE);
        //IPHONE Values
        if ($id == null && $latitude == null && $longitude == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $latitude = $data['latitude'];
            $id = $data['riderid'];
            $longitude = $data['longitude'];
        }
        if ($this->webservice_model->rider_latitudelongitude_check_booking($id, $latitude, $longitude) != false) {
            $data['status'] = '1';
            $data['message'] = 'Ride latitude and longitude updated';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Rider latitude and longitude not updated';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function riderlocationtouser() {
        $bookid = $this->input->post('bookid', TRUE);
        if ($bookid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid = $data['bookid'];
        }
        if (($result = $this->webservice_model->getride_user_detailsprocess($bookid)) != false) {
            $data['riderid'] = "$result->allocate_riderid";
            $data['latitude'] = "$result->rider_latitude";
            $data['longitude'] = "$result->rider_longitude";
            $data['pickupstatus'] = "$result->book_pickupstatus";
            $data['status'] = '1';
            $data['message'] = 'Ride latitude and longitude updated';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Rider latitude and longitude not updated';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function rider_ride_details() {
        $id = $this->input->post('id', TRUE);
        if (($results = $this->webservice_model->resend_ride_detailsprocess($id)) != false) {
            $data['rider_recent_rider_list'] = array();
            foreach ($results as $res) {
                /* $lat1 = $res->book_fromlatitude;
                  $lon1 = $res->book_fromlongitude;
                  $lat2 = $res->book_tolatitude;
                  $lon2 = $res->book_tolongitude;
                  /*$lat1 = '13.02';
                  $lon1 = '80.2181';
                  $lat2 = '13.0823';
                  $lon2 = '80.2754';
                  $theta = $lon1 - $lon2;
                  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                  $dist = acos($dist);
                  $dist = rad2deg($dist);
                  $miles = $dist * 60 * 1.1515;
                  $s =$miles * 1.60934; */
                $s = substr($res->book_distance, 0, -3);
                /* $result = $this->webservice_model->ratecarddetails();
                  $amount = $result->ratecard_basefare;
                  $defaultkilometer=$result->ratecard_kilometer;

                  if($defaultkilometer > $s)
                  {
                  $amount = $result->ratecard_basefare;
                  }

                  for($i=$defaultkilometer; $i< $s; $i++)
                  {
                  $amount =$amount + 5;
                  } */
                $speed = '18';
                $time = (($s * 1000) / ($speed * 5 / 18));
                $timemin = ($time / 60);
                $temp['bookid'] = $res->book_id;
                $temp['total_km'] = $results->book_distance;
                $temp['total_time'] = $timemin;
                $temp['total_amount'] = $res->book_finalamount;
                $temp['processstatus'] = $res->book_status;
                $distance[] = $temp;
            }
            $data['rider_recent_rider_list'] = $distance;
            $data['status'] = '1';
            $data['message'] = 'Rider is Online';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Rider is Offline';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function user_ride_details_list() {
        $id = $this->input->post('id', TRUE);
        if (($results = $this->webservice_model->resend_user_detailsprocess($id)) != false) {
            $data['user_recent_rider_list'] = array();
            foreach ($results as $res) {
                /* $lat1 = $res->book_fromlatitude;
                  $lon1 = $res->book_fromlongitude;
                  $lat2 = $res->book_tolatitude;
                  $lon2 = $res->book_tolongitude;
                  /*$lat1 = '13.02';
                  $lon1 = '80.2181';
                  $lat2 = '13.0823';
                  $lon2 = '80.2754';
                  $theta = $lon1 - $lon2;
                  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                  $dist = acos($dist);
                  $dist = rad2deg($dist);
                  $miles = $dist * 60 * 1.1515;
                  $s =$miles * 1.60934; */
                $s = substr($res->book_distance, 0, -3);
                /* $result = $this->webservice_model->ratecarddetails();
                  $amount = $result->ratecard_basefare;
                  $defaultkilometer=$result->ratecard_kilometer;

                  if($defaultkilometer > $s)
                  {
                  $amount = $result->ratecard_basefare;
                  }

                  for($i=$defaultkilometer; $i< $s; $i++)
                  {
                  $amount =$amount + 5;
                  } */
                $speed = '18';
                $time = (($s * 1000) / ($speed * 5 / 18));
                $timemin = ($time / 60);
                $temp['bookid'] = $res->book_id;
                $temp['total_km'] = $results->book_distance;
                $temp['total_time'] = $timemin;
                $temp['total_amount'] = $res->book_finalamount;
                $temp['processstatus'] = $res->book_status;
                $distance[] = $temp;
            }

            $data['user_recent_rider_list'] = $distance;
            $data['status'] = '1';
            $data['message'] = 'Rider is Online';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Rider is Offline';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /* get kilometer details */

    public function user_kilometerdetails() {
        $userlatitude = $this->input->post('userlatitude', TRUE);
        $userlongitude = $this->input->post('userlongitude', TRUE);
        if (($results = $this->webservice_model->user_latitudelongitude($userlatitude, $userlongitude)) != false) {
            $data['distance1km'] = array();
            $data['distance3km'] = array();
            $data['distance5km'] = array();
            foreach ($results as $result) {
                if ($result->distance > 0 && $result->distance < 1) {
                    $temp['distance'] = $result->distance;
                    $temp['ridername'] = $result->rider_name;
                    $temp['riderid'] = $result->rider_id;
                    $distance[] = $temp;
                }
                if ($result->distance >= 1 && $result->distance < 3) {
                    $temp['distance'] = $result->distance;
                    $temp['ridername'] = $result->rider_name;
                    $temp['riderid'] = $result->rider_id;
                    $distance1[] = $temp;
                }
                if ($result->distance >= 3 && $result->distance <= 5) {
                    $temp['distance'] = $result->distance;
                    $temp['ridername'] = $result->rider_name;
                    $temp['riderid'] = $result->rider_id;
                    $distance2[] = $temp;
                }
            }
            $data['distance1km'] = $distance;
            $data['distance3km'] = $distance1;
            $data['distance5km'] = $distance2;
            if ($distance == null) {
                $data['1kmstatus'] = '0';
            } else {
                $data['1kmstatus'] = '1';
            }
            if ($distance1 == null) {
                $data['3kmstatus'] = '0';
            } else {
                $data['3kmstatus'] = '1';
            }
            if ($distance2 == null) {
                $data['5kmstatus'] = '0';
            } else {
                $data['5kmstatus'] = '1';
            }
            $data['status'] = '1';
            $data['message'] = 'Rider is Online';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Rider is Offline';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function user_ride_details() {
        $id = $this->input->post('id', TRUE);
        if (($results = $this->webservice_model->user_ride_detailsprocess($id)) != false) {
            $linkprofile = base_url('uploads/profile');
            $data['user_recent_ride_list'] = array();
            foreach ($results as $result) {
                $temp['book_id'] = $result->book_id;
                $temp['rider_id'] = $result->rider_id;
                $temp['rider_name'] = $result->rider_name;
                $temp['rider_image'] = $linkprofile . "/" . $result->rider_picture;
                $temp['from_location'] = $result->book_fromlocation;
                $temp['to_location'] = $result->book_tolocation;
                $timestamp = strtotime("$result->book_time");
                $time = date('H:i a', $timestamp);
                $datestamp = strtotime("$result->book_date");
                $date = date('D, d M y', $datestamp);
                $temp['date_and_time'] = $date . ", " . $time;
                $temp['book_status'] = $result->book_status;
                $distance[] = $temp;
            }
            $data['user_recent_ride_list'] = $distance;
            $data['status'] = '1';
            $data['message'] = 'get user ride details';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Not information';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /* get bike make details */

    public function getbikemakedetails() {
        if (($results = $this->webservice_model->user_bikegetmakedetails()) != false) {
            $data['bikemake'] = array();
            $data['profession'] = array();
            $bike = array();
            $profession = array();
            foreach ($results as $result) {
                $temp['bikemakename'] = $result->bike_name;
                $bike[] = $temp;
                if ($result->bike_profession != '') {
                    $profession['profession'] = $result->bike_profession;
                    $profession1[] = $profession;
                }
            }
            $data['bikemake'] = $bike;
            $data['profession'] = $profession1;
            $data['status'] = '1';
            $data['message'] = 'Recevied Successfully';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /* get bike model details */

    public function getbikemodeldetails() {
        $bikemakename = $this->input->post('bikemakename', TRUE);
        if ($bikemakename == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bikemakename = $data['bikemakename'];
        }
        if (($results = $this->webservice_model->user_bikegetmodeldetails($bikemakename)) != false) {
            $data['bikemodel'] = array();
            $model = array();
            foreach ($results as $result) {
                $temp['bikemodelname'] = $result->bikemodel_model;
                $model[] = $temp;
            }
            $data['bikecolors'] = array();
            $color = array();
            $lines = $this->webservice_model->user_bikegetmakedetails();
            foreach ($lines as $line) {
                $temp1['bikecolor'] = $line->bike_color;
                $color[] = $temp1;
            }
            $data['bikecolors'] = $color;
            $data['bikemodel'] = $model;
            $data['status'] = '1';
            $data['message'] = 'Recevied Successfully';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    function finding_nearest_rider() {
        $fromlatitude = $this->input->post('latitude', TRUE);
        $fromlongitude = $this->input->post('longitude', TRUE);
        $currentlatitude = $this->input->post('current_latitude', TRUE);
        $currentlongitude = $this->input->post('current_longitude', TRUE);
        $userid = $this->input->post('user_id', TRUE);
        if ($userid == null && $fromlatitude == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $fromlatitude = $data['latitude'];
            $fromlongitude = $data['longitude'];
            $currentlatitude = $data['current_latitude'];
            $currentlongitude = $data['current_longitude'];
            $userid = $data['user_id'];
        }
        $this->webservice_model->user_latitudelongitude_update($userid,$currentlatitude,$currentlongitude);
        if (($results = $this->webservice_model->sendpushnotification()) != false) {
            $data['riderlist'] = array();
            foreach ($results as $result) {
                $lat1 = $fromlatitude;
                $lon1 = $fromlongitude;
                $lat2 = $result->rider_latitude;
                $lon2 = $result->rider_longitude;
                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $s = $miles * 1.60934;
                $resf = round($s, 2);
                $s = substr($meter12, 0, -3);
                $getdet = $this->webservice_model->getting_userratingsprocess($userid);
                // Note: (!=) Making Changes due to Minus Subscribtions
                if ($result->rider_subscribe > 0 && $getdet->user_gender == $result->rider_gender) {
                    if ($resf <= 3) {
                        //if(7.00 <= date('H:i') && date('H:i') <= 21.00)
                        //{
                        $s = date('H:i:s');
                        $time = strtotime($s);
                        $startTime = date("H:i:s", strtotime('-30 minutes', $time));
                        //if($startTime <= $result->rider_latlongtime && $result->rider_latlongdate == date('Y-m-d'))
                        //{
                        $temp['riderid'] = $result->rider_id;
                        $temp['ridername'] = $result->rider_name;
                        $temp['rideridlatitude'] = $result->rider_latitude;
                        $temp['rideridlongitude'] = $result->rider_longitude;
                        $temp['rider_km'] = $resf;
                        $rider[] = $temp;
                        //}
                        //}
                    }
                }
            }
            if ($rider == null) {
                $s = '2';
            } else {
                $s = '1';
            }
            $data['riderlist'] = ($rider != null) ? $rider : "";
            $data['status'] = $s;
            $data['message'] = 'Nearest Rider list';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $ride = array("");    //Only for IOS
            $data['riderlist'] = $ride; //Only for IOS
            $data['status'] = '2';
            $data['message'] = 'No Riders Available';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /*
     *  User Booking.!!!
     *      1. Push Notification will be send for the Nearest Riders and the rider_status will be changed to 2.
     *      2. In user_booking number of riders, Got push notification will be mentioned!!!
     *      3. No of the Riders will accept the booking.!!!
     *      4. Remaining Riders status will be set back to Normal.!!!
     */

    public function userbookingdetails() {
        $fromlatitude = $this->input->post('fromlatitude', TRUE);
        $fromlongitude = $this->input->post('fromlongitude', TRUE);
        $tolatitude = $this->input->post('tolatitude', TRUE);
        $tolongitude = $this->input->post('tolongitude', TRUE);
        $coupon_id = $this->input->post('coupon_code', TRUE);
        $userid = $this->input->post('userid', TRUE);
        $book_fromlocation = $this->input->post('fromlocation', TRUE);
        $book_tolocation = $this->input->post('tolacation', TRUE);
        $otp = rand(1000, 9999);
        $confirm_user_otp = $otp;
        if ($fromlatitude == null && $fromlongitude == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $fromlatitude = $data['fromlatitude'];
            $fromlongitude = $data['fromlongitude'];
            $tolatitude = $data['tolatitude'];
            $tolongitude = $data['tolongitude'];
            $coupon_id = $data['coupon_code'];
            $userid = $data['userid'];
            $book_fromlocation = $data['fromlocation'];
            $book_tolocation = $data['tolacation'];
        }
        $booking = array(
            'book_userid' => $userid,
            'book_fromlatitude' => $fromlatitude,
            'book_fromlongitude' => $fromlongitude,
            'book_fromlocation' => $book_fromlocation,
            'book_tolatitude' => $tolatitude,
            'book_tolongitude' => $tolongitude,
            'book_tolocation' => $book_tolocation,
            'confirm_user_otp' => $confirm_user_otp,
            //'book_distance' => $this->input->post('distance', TRUE),
            'book_date' => date('Y-m-d'),
            'book_time' => date('H:i:s')
        );
        /* if ($this->webservice_model->user_bookingdetails($booking) != false)
          { */
        //if (($results = $this->webservice_model->sendpushnotification($fromlatitude, $fromlongitude)) != false)

        /*
         *  Get Rider_online from rider_registration
         */
        if (($results = $this->webservice_model->sendpushnotification()) != false) {
            $y = 1;
            $ty = 0;
            foreach ($results as $rds) {
                $id = $rds->rider_id;
                $lat1 = $fromlatitude;
                $lon1 = $fromlongitude;
                $lat2 = $rds->rider_latitude;
                $lon2 = $rds->rider_longitude;
                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad(floatval($lat1))) * sin(deg2rad(floatval($lat2))) + cos(deg2rad(floatval($lat1))) * cos(deg2rad(floatval($lat2))) * cos(deg2rad(floatval($theta)));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $s1 = $miles * 1.60934;
                $resf = round($s1, 2);
                $getdet = $this->webservice_model->getting_userratingsprocess($userid);
                // Note: (!=) Making Changes due to Minus Subscribtions
                if ($rds->rider_subscribe > 0 && $getdet->user_gender == $rds->rider_gender) {
                    if ($resf >= 0 && $resf <= 2) {

                        /* if(00.01 <= date('H:i') && date('H:i') <= 23.59)
                          {
                          /* $s = date('H:i:s');
                          $time = strtotime($s);
                          $startTime = date("H:i:s", strtotime('-30 minutes', $time));
                          if($startTime <= $rds->rider_latlongtime && $rds->rider_latlongdate == date('Y-m-d'))
                          {
                          $ty = $y + 1;
                          $y++;
                          }
                          else
                          {
                          $ty = 0;
                          }
                          $ty = $y + 1;
                          $y++;
                          }
                          else
                          {
                          $ty = 0;
                          } */
                        $ty += $y + 1;
                        $y++;
                    }
                }
            }
            if ($ty == 0) {
                $this->booking_attempts($booking);
                $data['status'] = '2';
                $data['message'] = 'No Riders near by';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
//                $res = $this->webservice_model->getbookid();
            } else {
                $book_id_return = $this->webservice_model->user_bookingdetails($booking);
                if ($book_id_return) {
                    $s = $this->webservice_model->getbookdetails($book_id_return);
                    $bookid1 = $book_id_return;
                }
                $fromlocation = $fromlatitude . "," . $fromlongitude;
                $tolocation = $tolatitude . "," . $tolongitude;
                $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$fromlocation&destinations=$tolocation&mode=driving&language=English&key=AIzaSyBaF59OzAUa6OwPLUxzuZdnb-fpXR0SZU0";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($ch);
                curl_close($ch);
                $response_a = json_decode($response, true);
                $meter12 = $response_a['rows'][0]['elements'][0]['distance']['text'];
                // GET TIME TAKEN
                $google_time = $response_a['rows'][0]['elements'][0]['duration']['text'];
                $split = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $google_time, -1, PREG_SPLIT_NO_EMPTY);
                if ($split[0] >= 1 && $split[0] < 60 && $split[1] == 'min'){
                    $duration = $split[0];
                } elseif ($split[0] >= 1 && $split[0] < 60 && $split[1] == 'mins'){
                    $duration = $split[0];
                } else {
                    $durationHours = $split[0]*60;
                    $durationMin = $split[2];
                    $duration = $durationHours + $durationMin;
                    $duration;
                }
                $rt = $this->webservice_model->ratecarddetails();
                $amt = $rt->ratecard_basefare;
                $defaultkilometer = $rt->ratecard_kilometer;
                $meter13 = substr($meter12, 0, -3);
                if ($defaultkilometer >= $meter13) {
                    $amount = $rt->ratecard_basefare + $rt->ratecard_tax;
                } else {
                    $amtdt = ($meter13 - $rt->ratecard_kilometer) * $rt->ratecard_perkilometer + $rt->ratecard_tax;
                    $amount = $amtdt + $amt;
                }
                $bookingamount = array(
                    'book_distance' => $meter12,
                    'book_amount' => $amount
                );
                $kilo = $this->webservice_model->update_kilometer_details($bookid1, $bookingamount);
                $bookid = $bookid1;
                $getuser = $this->webservice_model->get_acceptuserdetails($bookid);
                if (($totres = $this->webservice_model->getuser_coupon($coupon_id)) != FALSE) {
                    if ($totres->coupon_rtype == 'Rs') {
                        $tot = $getuser->book_amount - $totres->coupon_offername;
                        $deduct = $totres->coupon_offername;
                        $value = $totres->coupon_offername;
                        $s = $totres->coupon_rtype;
                        $w = "1";
                        $coupon = $totres->coupon_code;
                    } else {
                        $t = (( $totres->coupon_offername / 100) * $getuser->book_amount);
                        $tot = $getuser->book_amount - $t;
                        $deduct = $t;
                        $value = $totres->coupon_offername;
                        $s = $totres->coupon_rtype;
                        $w = "1";
                        $coupon = $totres->coupon_code;
                    }
                } else {
                    $tot = $getuser->book_amount;
                    $deduct = '0';
                    $value = '0';
                    $s = 'No coupons';
                    $w = "0";
                    $coupon = "0";
                }
                $bookingcouponamount = array(
                    'book_promocode' => $coupon,
                    'book_promovalue' => $value,
                    'book_promotype' => $s,
                    'book_promodeductamt' => $deduct,
                    'book_finalamount' => $tot,
                );
                $coupondetail = $this->webservice_model->update_coupon_details($bookid1, $bookingcouponamount);
                $data['bookid'] = "$bookid1";
                $data['status'] = '1';
                $data['message'] = 'User booking Successfully';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
//                $res = $this->webservice_model->getbookid();
                $res = $this->webservice_model->getbookdetails($bookid1);
                $bkid = $res->book_id;
                $agencynamelist = array();
                foreach ($results as $ress) {

                    $id = $ress->rider_id;
                    $lat1 = $fromlatitude;
                    $lon1 = $fromlongitude;
                    $lat2 = $ress->rider_latitude;
                    $lon2 = $ress->rider_longitude;
                    $theta = $lon1 - $lon2;
                    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                    $dist = acos($dist);
                    $dist = rad2deg($dist);
                    $miles = $dist * 60 * 1.1515;
                    $s1 = $miles * 1.60934;
                    $resf = round($s1, 2);
                    if ($resf >= 0 && $resf <= 2) {
                        //if(00.01 <= date('H:i') && date('H:i') <= 23.59)
                        //{
                        $s = date('H:i:s');
                        $time = strtotime($s);
                        $startTime = date("H:i:s", strtotime('-30 minutes', $time));
                        //if($startTime <= $ress->rider_latlongtime && $ress->rider_latlongdate == date('Y-m-d'))
                        //{
                        $getdet = $this->webservice_model->getting_userratingsprocess($userid);
                        if ($ress->rider_subscribe > 0 && $getdet->user_gender == $ress->rider_gender) {
                            $iddetail = $ress->rider_id;
                            if (($logindetails = $this->webservice_model->getrideriddetail($id)) != false) {
                                if ($logindetails->gcm_mobileos == 'iOS') {
                                    // PUSH NOTIFICATION FOR IOS !!!!!
                                    // WILL BE HERE.!!!
                                } else {
                                    $ids = $logindetails->gcm_userid;
                                    $apikeyid = $this->webservice_model->getapikey();
                                    $apiKey = $apikeyid->apikey;
                                    $registatoin_ids = $logindetails->gcm_regid;
                                    $id = $bkid;
                                    //$message = "New_booking_details#user_new_ride#piggyback#".$id;
                                    $message = "New booking arrived";
                                    //$message = $id;
                                    $task = "user_new_ride";
                                    // $task = "user_confirm";
                                    $title = "piggyback";
                                    $url = $apikeyid->url;
                                    $sound="bike_start_up.mp3";
                                    $fields = array(
                                        'to' => $registatoin_ids,
                                        'priority' => "high",
                                        //'notification' => array("body" => $message, "sound" => $sound),
                                        // 'notification' => array("body" => $message, "sound" => $sound), //, 'click_action' => 'in.efficienza.piggyback.RiderSide.RiderRideAcceptScreen'
                                        'data' => array("message" => $message, "task" => $task, "bookid" => $id, "title" => $title)
                                    );
                                    $headers = array(
                                        'Authorization: key=' . $apiKey,
                                        'Content-Type: application/json'
                                    );
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_POST, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                                    $result = curl_exec($ch);
                                    curl_close($ch);
                                }
                            }
                            $agencynamelist[] = $iddetail;
                            $rider = $iddetail;
                            //Rider status changes here.!!!
                            $this->webservice_model->change_bookingstatusrider($rider);
                            $this->webservice_model->notification_bookid($rider, $bookid1);
                        }
                        //}
                        //}
                    }

                    //}
                    //$agencynamelist[] = $iddetail;
                    /* 	if($id == "" )
                      {
                      $id = $result->rider_id;
                      }
                      else
                      {
                      $id = $id.",".$result->rider_id;
                      } */
                }

                $idd = implode(',', $agencynamelist);
                $this->webservice_model->updatereceiveid($bkid, $idd);
            }
        } else {
            $this->booking_attempts($booking);
            $data['status'] = '2';
            $data['message'] = 'No Riders Available';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }

        /* }
          else
          {
          $data['status'] = '0';
          $data['message'] = 'User booking Failed';
          $data = array('response' => $data);
          $this->load->view('webservices/json', $data);
          } */
    }

    function booking_attempts($booking) {
                $book_id = $this->webservice_model->user_bookingattemptsdetails($booking);
                $fromlatitude=$booking['book_fromlatitude'];
                $fromlongitude=$booking['book_fromlongitude'];
                $tolatitude=$booking['book_tolatitude'];
                $tolongitude=$booking['book_tolongitude'];

                $fromlocation = $fromlatitude . "," . $fromlongitude;
                $tolocation = $tolatitude . "," . $tolongitude;

                $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$fromlocation&destinations=$tolocation&mode=driving&language=English&key=AIzaSyBaF59OzAUa6OwPLUxzuZdnb-fpXR0SZU0";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($ch);
                curl_close($ch);
                $response_a = json_decode($response, true);
                $meter12 = $response_a['rows'][0]['elements'][0]['distance']['text'];
                // GET LOCAL_DETAILS
                $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$fromlocation&sensor=true_or_false&key=AIzaSyCddUuW05eK7ZMG08LsCO7Qt3YN4IFv4n0";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($ch);
                curl_close($ch);
                $response_origin = json_decode($response, true);
                // print_r($response_origin);exit;
                $type_0=$response_origin['results'][0]['address_components'][0]['types'][0];
                if($type_0=='locality') {
                    $neighborhood=$response_origin['results'][0]['address_components'][0]['long_name'];
                } else if($type_0=='street_number') {
                    $street_number=$response_origin['results'][0]['address_components'][0]['long_name'];
                } else if($type_0=='political') {
                    $street_route=$response_origin['results'][0]['address_components'][0]['long_name'];
                } else if($type_0=='administrative_area_level_1') {
                    $state=$response_origin['results'][0]['address_components'][0]['long_name'];
                } else if($type_0=='administrative_area_level_2') {
                    $district=$response_origin['results'][0]['address_components'][0]['long_name'];
                } else if($type_0=='country') {
                    $country=$response_origin['results'][0]['address_components'][0]['long_name'];
                }

                $type_1=$response_origin['results'][0]['address_components'][1]['types'][0];
                if($type_1=='administrative_area_level_1') {
                    $state=$response_origin['results'][0]['address_components'][1]['long_name'];
                } else if($type_1=='administrative_area_level_2') {
                    $district=$response_origin['results'][0]['address_components'][1]['long_name'];
                } else if($type_1=='political') {
                    $street_route=$response_origin['results'][0]['address_components'][1]['long_name'];
                } else if($type_1=='country') {
                    $country=$response_origin['results'][0]['address_components'][1]['long_name'];
                }


                $type_2=$response_origin['results'][0]['address_components'][2]['types'][0];
                if($type_2=='administrative_area_level_2') {
                    $district=$response_origin['results'][0]['address_components'][2]['long_name'];
                } else if($type_2=='administrative_area_level_1') {
                    $state=$response_origin['results'][0]['address_components'][2]['long_name'];
                } else if($type_2=='country') {
                    $country=$response_origin['results'][0]['address_components'][2]['long_name'];
                } else if($type_2=='locality') {
                    $neighborhood=$response_origin['results'][0]['address_components'][2]['long_name'];
                }

                $type_3=$response_origin['results'][0]['address_components'][3]['types'][0];
                if($type_3=='administrative_area_level_1') {
                    $state=$response_origin['results'][0]['address_components'][3]['long_name'];
                }  else if($type_3=='country') {
                    $country=$response_origin['results'][0]['address_components'][3]['long_name'];
                }  else if($type_3=='postal_code') {
                    $pincode=$response_origin['results'][0]['address_components'][3]['long_name'];
                }  else if($type_3=='administrative_area_level_2') {
                    $district=$response_origin['results'][0]['address_components'][3]['long_name'];
                }  else if($type_3=='locality') {
                    $neighborhood=$response_origin['results'][0]['address_components'][3]['long_name'];
                }

                $type_4=$response_origin['results'][0]['address_components'][4]['types'][0];
                if($type_4=='country') {
                    $country=$response_origin['results'][0]['address_components'][4]['long_name'];
                } else if($type_4=='postal_code') {
                    $pincode=$response_origin['results'][0]['address_components'][4]['long_name'];
                }  else if($type_4=='administrative_area_level_1') {
                    $state=$response_origin['results'][0]['address_components'][4]['long_name'];
                }  else if($type_4=='administrative_area_level_2') {
                    $district=$response_origin['results'][0]['address_components'][4]['long_name'];
                }  else if($type_4=='locality') {
                    $neighborhood=$response_origin['results'][0]['address_components'][4]['long_name'];
                }

                $type_5=$response_origin['results'][0]['address_components'][5]['types'][0];
                if($type_5=='country') {
                    $country=$response_origin['results'][0]['address_components'][5]['long_name'];
                } else if($type_5=='administrative_area_level_2') {
                    $district=$response_origin['results'][0]['address_components'][5]['long_name'];
                } else if($type_5=='administrative_area_level_1') {
                    $state=$response_origin['results'][0]['address_components'][5]['long_name'];
                } else if($type_5=='postal_code') {
                    $pincode=$response_origin['results'][0]['address_components'][5]['long_name'];
                } else if($type_5=='locality') {
                    $neighborhood=$response_origin['results'][0]['address_components'][5]['long_name'];
                }

                $type_6=$response_origin['results'][0]['address_components'][6]['types'][0];
                if($type_6=='postal_code') {
                    $pincode=$response_origin['results'][0]['address_components'][6]['long_name'];
                } else if($type_6=='administrative_area_level_1') {
                    $state=$response_origin['results'][0]['address_components'][6]['long_name'];
                } else if($type_6=='administrative_area_level_2') {
                    $district=$response_origin['results'][0]['address_components'][6]['long_name'];
                } else if($type_6=='country') {
                    $country=$response_origin['results'][0]['address_components'][6]['long_name'];
                }

                $type_7=$response_origin['results'][0]['address_components'][7]['types'][0];
                if($type_7=='postal_code') {
                    $pincode=$response_origin['results'][0]['address_components'][7]['long_name'];
                } else if($type_7=='administrative_area_level_1') {
                    $state=$response_origin['results'][0]['address_components'][7]['long_name'];
                } else if($type_7=='administrative_area_level_2') {
                    $district=$response_origin['results'][0]['address_components'][7]['long_name'];
                 } else if($type_7=='country') {
                        $country=$response_origin['results'][0]['address_components'][7]['long_name'];
                    }

                $type_8=$response_origin['results'][0]['address_components'][8]['types'][0];
                if($type_8=='postal_code') {
                    $pincode=$response_origin['results'][0]['address_components'][8]['long_name'];
                } else if($type_8=='country') {
                    $country=$response_origin['results'][0]['address_components'][8]['long_name'];
                } else if($type_8=='administrative_area_level_1') {
                    $state=$response_origin['results'][0]['address_components'][8]['long_name'];
                } else if($type_8=='administrative_area_level_2') {
                    $district=$response_origin['results'][0]['address_components'][8]['long_name'];
                }

                $type_9=$response_origin['results'][0]['address_components'][9]['types'][0];
                if($type_9=='postal_code') {
                    $pincode=$response_origin['results'][0]['address_components'][9]['long_name'];
                } else if($type_9=='country') {
                    $country=$response_origin['results'][0]['address_components'][9]['long_name'];
                } else if($type_9=='administrative_area_level_2') {
                    $district=$response_origin['results'][0]['address_components'][9]['long_name'];
                } else if($type_9=='administrative_area_level_1') {
                    $state=$response_origin['results'][0]['address_components'][9]['long_name'];
                }

                $bookingamount = array(
                    'book_distance' => $meter12,
                    'street_number' =>$street_number,
                    'street' =>$street_route,
                    'area' =>$neighborhood,
                    'district' =>($district!=null) ? $district : $neighborhood,
                    'state' =>$state,
                    'country' =>$country,
                    'pincode' =>$pincode
                );
                $this->webservice_model->update_bkattempt_details($book_id, $bookingamount);
    }

    /* insert gcm details */
    public function gcmdetails() {
        //NOTE: gcmid is a User-ID.
        $gcmid = $this->input->post('gcmid', TRUE);
        $gcmtype = $this->input->post('gcmtype', TRUE);
        $gcmmodel = $this->input->post('gcmmobilemodel', TRUE);
        $gcmos = $this->input->post('gcmmobileos', TRUE);
        $gcmregid = $this->input->post('gcmregid', TRUE);
        $gcmmobileserial = $this->input->post('gcmmobileserial', TRUE);
        //IPHONE Get Values.!!!!
        if ($gcmid == null && $gcmtype == null && $gcmos == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $gcmid = $data['gcmid'];
            $gcmtype = $data['gcmtype'];
            $gcmmodel = $data['gcmmobilemodel'];
            $gcmos = $data['gcmmobileos'];
            $gcmregid = $data['gcmregid'];
            $gcmmobileserial = $data['gcmmobileserial'];
        }
        $gcmdetail = array(
            'gcm_userid' => $gcmid,
            'gcm_type' => $gcmtype,
            'gcm_mobilemodel' => $gcmmodel,
            'gcm_mobileos' => $gcmos,
            'gcm_regid' => $gcmregid,
            'gcm_serialno' => $gcmmobileserial,
            'gcm_date' => date('Y-m-d'),
            'gcm_time' => date('H:i:s')
        );
        if ($this->webservice_model->insert_gcmdetails($gcmdetail, $gcmid, $gcmtype, $gcmmodel, $gcmos, $gcmregid) != false) {
            $data['status'] = '1';
            $data['message'] = 'GCM details Added Successfully';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'GCM details Failed';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function update_gcmdetails() {
        $gcmid = $this->input->post('gcmid', TRUE);
        $gcmtype = $this->input->post('gcmtype', TRUE);
        $gcmdetail = array(
            'gcm_userid' => $this->input->post('gcmid', TRUE),
            'gcm_type' => $this->input->post('gcmtype', TRUE),
            'gcm_mobilemodel' => $this->input->post('gcmmobilemodel', TRUE),
            'gcm_mobileos' => $this->input->post('gcmmobileos', TRUE),
            'gcm_regid' => $this->input->post('gcmregid', TRUE),
            'gcm_serialno' => $this->input->post('gcmmobileserial', TRUE),
            'gcm_date' => date('Y-m-d'),
            'gcm_time' => date('H:i:s')
        );
        if ($this->webservice_model->update_gcmdetails($gcmdetail, $gcmid) != false) {
            $data['status'] = '1';
            $data['message'] = 'GCM details Update Successfully';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'GCM Update Failed';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /* Rider status change */

    function riderstatuschange() {
        $riderid = $this->input->post('riderid', TRUE);
        $riderstatus = $this->input->post('riderstatus', TRUE);
        if ($riderid == null && $riderstatus == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $riderid = $data['riderid'];
            $riderstatus = $data['riderstatus'];
        }
        if ($this->webservice_model->rider_status($riderid, $riderstatus) != false) {
            if ($riderstatus == 1) {
                $status = 'Now Rider is online';
            } else {
                $status = 'Now Rider is offline';
            }
            $data['riderstatus'] = $riderstatus;
            $data['status'] = '1';
            $data['message'] = $status;
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Status not change';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /* book id details */

    function get_user_booking_details() {
        $bookid = $this->input->post('bookid', TRUE);
        //IPHONE Values
        if ($bookid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid = $data['bookid'];
        }
        if (($results = $this->webservice_model->get_bookiddetails($bookid)) != false) {
            $linkprofile = base_url('tempuploads/Tempuserprofile');
            $data['user_id'] = $results->user_id;
            $data['user_image'] = $linkprofile . "/" . $results->user_image;
            $data['username'] = $results->user_name;
            if ($results->user_rating == "") {
                $rt = 0;
            } else {
                $rt = $results->user_rating;
            }
            $book_status=($results->book_cancel1!=null) ? $results->book_cancel1 : $results->book_status;
            $data['rating'] = $rt;
            $data['fromlocation'] = $results->book_fromlocation;
            $data['tolocation'] = $results->book_tolocation;
            $data['status'] = '1';
            $data['book_status'] = $book_status;
            $data['message'] = 'Recevied details success';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Not Recevied details ';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /*
     *  Rider Accept Booking ***!
     *      1. Rider will get a Pushnotification., Who are close to the Customer.
     *      2. One of the Rider will accept the Ride and remaining will be sent back to active state.!
     *      3. If Rider tries to accepts||Decline for  once accepted by rider should be   not be allowed
     *      4. Only one rider can accept the Ride.!
     */

    function rider_accept_booking() {
        $bookid = $this->input->post('bookid', TRUE);
        $rider = $this->input->post('riderid', TRUE);
        $acceptstatus = $this->input->post('rideracceptstatus', TRUE);
        //IPHONE Values
        if ($bookid == null && $rider == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid = $data['bookid'];
            $rider = $data['riderid'];
            $acceptstatus = $data['rideracceptstatus'];
        }
        if ($acceptstatus == "accept") {
            $riderdetail = array(
                'allocate_riderid' => $rider,
                'book_status' => $acceptstatus
            );
        } else {
            $we = $this->webservice_model->get_acceptuserdetails($bookid);
            $decid = $we->book_decline_id;
            if ($decid == "") {
                $sd = $rider;
            } else {
                $sd = $decid . "," . $rider;
            }
            $riderdetail = array(
                'book_decline_id' => $sd
            );
        }
        $rowdetail = $this->webservice_model->get_acceptuserdetails($bookid);
        if ($rowdetail->book_cancel1 != "") {
            $this->webservice_model->change_bookingstatusrider1($rider);
            $data['msg_title'] = 'Cancelled customer';
            $data['status'] = '0';
            $data['message'] = 'booking is not valid already cancelled by customer';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            if ($this->webservice_model->change_bookingstatus($riderdetail, $bookid, $acceptstatus) != false) {
                if ($acceptstatus == "accept") {
                    $this->webservice_model->change_bookingstatusrider($rider);
                    $title = 'Success!';
                    $st = '1';
                    $msg = 'booking accepted successfully';
                    /*
                     *  PUSH Notification:
                     *    notification send to -> 5.Riders.
                     *    1.Rider ->Accepted
                     *    Remaing 4.Riders Notification should be Removed, Code Below.!!!
                     */
                    $userbooking = $this->User_booking_model->get($bookid);
                    if ($userbooking) {
                        $alloted_riderid = $userbooking->receive_riderid;
                        $remove_id = $userbooking->allocate_riderid;
                        $notify_riderid = str_replace("$remove_id", "0", $alloted_riderid);
                        $rider_id = explode(',', $notify_riderid);
                        $results = $this->webservice_model->get_cancel_bookriderlist($rider_id,$bookid);
                        foreach ($results as $ress) {
                            $id = $ress->rider_id;
                            if (($logindetails = $this->webservice_model->getrideriddetail($id)) != false) {
                                $ids = $logindetails->gcm_userid;
                                $apikeyid = $this->webservice_model->getapikey();
                                $apiKey = $apikeyid->apikey;
                                $registatoin_ids = $logindetails->gcm_regid;
                                $message = "Remove remaining Riders";
                                $task = "remove_remaining_bookedrider";
                                $title = "piggyback";
                                $url = $apikeyid->url;
                                $fields = array(
                                    'to' => $registatoin_ids,
                                    'priority' => "high",
                                    'notification' => array("body" => $message, "sound" => "bike_start_up.mp3"), //, 'click_action' => 'in.efficienza.piggyback.RiderSide.RiderRideAcceptScreen'
                                    'data' => array("message" => $message, "task" => $task, "bookid" => $bookid, "title" => $title)
                                );
                                $headers = array(
                                    'Authorization: key=' . $apiKey,
                                    'Content-Type: application/json'
                                );
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                                $result = curl_exec($ch);
                                curl_close($ch);
                            }
                        }
                    }
                } else {
                    $this->webservice_model->change_bookingstatusrider1($rider);
                    $this->webservice_model->remove_riderfrom_booking($rider, $bookid);
                    $title = 'Failure!';
                    $st = '0';
                    $msg = 'booking declined successfully';
                }
                $we = $this->webservice_model->get_acceptuserdetails($bookid);
                $userid = $we->book_userid;
                $f = $this->webservice_model->view_userstatus($userid);
                $linkprofile = base_url('tempuploads/Tempuserprofile');
                $profile = base_url('uploads/Profile.png');
                if ($f->user_image == "") {
                    $r = $profile;
                } else {
                    $r = $linkprofile . "/" . $f->user_image;
                }
                $data['user_image'] = "$r";
                $data['msg_title'] = "$title";
                $data['status'] = "$st";
                $data['message'] = "$msg";
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
                if ($acceptstatus == "accept") {
                    if (($result = $this->webservice_model->get_acceptuserdetails($bookid)) != false) {
                        $riderid = $result->receive_riderid;
                        $value = explode(",", $riderid);
                        $count = count($value);
                        for ($i = 0; $i < $count; $i++) {
                            $getriderid = $value[$i];
                            if ($rider == $value[$i]) {

                            } else {
                                $fd = $value[$i];
                                $this->webservice_model->change_bookingstatusrider2($fd);
                            }
                        }
                        $id = $result->book_userid;
                        if (($logindetails = $this->webservice_model->getusernotification($id)) != false) {
                            if ($logindetails->gcm_mobileos == 'iOS') {
                                if ($id == $logindetails->gcm_userid) {
                                    $riderinfo = $this->webservice_model->getRiderandBookdetails($result->book_id, $result->allocate_riderid);
                                    $startTimer = new DateTime("$res->rider_latlongtime");
                                    $endTimer = new DateTime("$res->book_time");
                                    $duration = $startTimer->diff($endTimer);
                                    $only_distance = substr($riderinfo[0]->book_distance, 0, -3); // For ios only.!!!
                                    $linkprofile = base_url('uploads/profile');
                                    $riderpicture = $linkprofile . "/" . $riderinfo[0]->rider_picture;
                                    $bookinfo = array('code' => 'user_confirm', 'only_distance' => "$only_distance", 'book_distance' => $riderinfo[0]->book_distance, 'rider_rating' => $riderinfo[0]->rider_rating, 'rider_picture' => $riderpicture, 'bookid' => $riderinfo[0]->book_id, 'book_userid' => $riderinfo[0]->book_userid, 'book_fromlocation' => $riderinfo[0]->book_fromlocation, 'book_tolatitude' => $riderinfo[0]->book_tolatitude, 'book_tolongitude' => $riderinfo[0]->book_tolongitude, 'book_tolocation' => $riderinfo[0]->book_tolocation, 'book_distance' => $riderinfo[0]->book_distance, 'rider_latitude' => $riderinfo[0]->rider_latitude, 'rider_longitude' => $riderinfo[0]->rider_longitude, 'rider_name' => $riderinfo[0]->rider_name, 'rider_mobileno' => $riderinfo[0]->rider_mobileno, 'rider_vehicle_manufacturing' => $riderinfo[0]->rider_vehicle_manufacturing, 'rider_vehicle_model' => $riderinfo[0]->rider_vehicle_model, 'rider_vehicle_bikeno' => $riderinfo[0]->rider_vehicle_bikeno, 'rider_vehicle_color' => $riderinfo[0]->rider_vehicle_color, 'book_fromlatitude' => $riderinfo[0]->book_fromlatitude, 'book_fromlongitude' => $riderinfo[0]->book_fromlongitude, 'confirm_user_otp' => $riderinfo[0]->confirm_user_otp,'book_status' => $riderinfo[0]->book_status,'book_riderrating'=> $riderinfo[0]->book_riderrating,'book_userrating'=> $riderinfo[0]->book_userrating);
                                    $apikeyid = $this->webservice_model->getapiserverkey($logindetails->gcm_mobileos);
                                    $url = $apikeyid->url;
                                    $token = $logindetails->gcm_regid;
                                    $serverKey = $apikeyid->apikey;
                                    $title = "Booking Confirmed";
                                    $body = "Your booking is confirmed by rider";
                                    $notification = array('title' => $title, 'text' => $body, 'sound' => 'default', 'badge' => '1', "rider_booking" => $bookinfo, 'click_action' => 'in.efficienza.piggyback.User_Conformation_Screen');
                                    $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority' => 'high', "message" => $body, "id" => $id, "title" => $title);
                                    $headers = array(
                                        'Authorization: key=' . $serverKey,
                                        'Content-Type: application/json'
                                    );
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
                                    //Send the request
                                    $response = curl_exec($ch);
                                    //Close requestrider_accept
                                    if ($response === FALSE) {
                                        die('FCM Send Error: ' . curl_error($ch));
                                    }
                                    curl_close($ch);
                                }
                            } else {
                                if ($id == $logindetails->gcm_userid) {
                                    $apikeyid = $this->webservice_model->getapikey();
                                    $apiKey = $apikeyid->apikey;
                                    $registatoin_ids = $logindetails->gcm_regid;
                                    $id = $result->book_id;
                                    $message = "Your booking is confirmed by rider";
                                    $task = "user_confirm";
                                    $title = "piggyback";
                                    $url = $apikeyid->url;
                                    $fields = array(
                                        'to' => $registatoin_ids,
                                        'priority' => "high",
                                        'notification' => array("body" => $message, "sound" => "bike_start_up.mp3", 'click_action' => 'in.efficienza.piggyback.User_Conformation_Screen'),
                                        'data' => array("message" => $message, "task" => $task, "id" => $id, "title" => $title)
                                    );
                                    $headers = array(
                                        'Authorization: key=' . $apiKey,
                                        'Content-Type: application/json'
                                    );
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_POST, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                                    $result = curl_exec($ch);
                                    curl_close($ch);
                                }
                            }
                        }
                    }
                }
            } else {
                $data['msg_title'] = 'Sorry!';
                $data['status'] = '0';
                $data['message'] = 'Booking is already accepted by other rider';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        }
    }

    function usercancelbooking() {
        $bookid = $this->input->post('bookid', TRUE);
        $status = $this->input->post('cancelreason', TRUE);
        $typecancel = $this->input->post('cancelstatus', TRUE);
        $cancelreason = $this->input->post('cancelreason', TRUE);

        if ($bookid == null && $status == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid = $data['bookid'];
            $status = $data['cancelreason'];
            $typecancel = $data['cancelstatus'];
            $cancelreason = $data['cancelreason'];
        }
        // Once Rider is Located with User. Then Rider Cannot cancel the Ride.!!!
        $booking_details = $this->User_booking_model->get($bookid);
        if ($booking_details != null && $booking_details->book_pickupstatus == '1') {
            $data['status'] = '0';
            $data['message'] = "Ride cannot be cancelled";
            $data['book_status'] = $booking_details->book_status;
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            if ($typecancel == "1") {
                $rt = 'Cancel';
            }
            $type = "User";
            $cancel = array(
                'book_status' => $rt,
                'book_cancelreason' => $cancelreason,
                'book_canceltype' => $type,
                'book_canceldate' => date('Y-m-d'),
                'book_canceltime' => date('H:i:s')
            );
            if (($result = $this->webservice_model->get_riderid($bookid)) != false) {
                //$result = $this->webservice_model->get_riderid($bookid);
                $id = $result->allocate_riderid;
                $rider = $result->allocate_riderid;
                $logindetails = $this->webservice_model->getrideriddetail($id);
                $apikeyid = $this->webservice_model->getapikey();
                $apiKey = $apikeyid->apikey;
                $registatoin_ids = $logindetails->gcm_regid;
                $reason = $this->webservice_model->getcancel_reason($status);
                //$message = "Your booking is cancelled, Reason : " . $reason->book_creason;
                $id = $bookid;
                $message = "Your booking is cancelled";
                //$message = "Your booking is cancelled#rider_cancel#piggyback#".$id;
                //$message = "Your booking is cancelled  " . $status1;
                $task = "rider_cancel";
                $title = "piggyback";
                $url = $apikeyid->url;
                $fields = array(
                    'to' => $registatoin_ids,
                    'priority' => "high",
                    'notification' => array("body" => $message, "sound" => "bike_start_up.mp3", 'click_action' => 'in.efficienza.piggyback.RiderSide.Rider_Finding_User'), //in.efficienza.piggyback.RiderSide.RiderRideAcceptScreen
                    'data' => array("message" => $message, "task" => $task, "id" => $id, "title" => $title)
                );
                $headers = array(
                    'Authorization: key=' . $apiKey,
                    'Content-Type: application/json'
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
                curl_close($ch);
                $userdetail = $this->webservice_model->cancel_booking($bookid, $cancel);
                $userdet = $this->webservice_model->change_bookingstatusrider1($rider);
                $data['status'] = '1';
                $data['message'] = 'Cancel success';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            } else {
                $data['status'] = '0';
                $data['message'] = 'Booking is already cancelled';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        }
    }

    function searchridercancel() {
        $bookid = $this->input->post('bookid', TRUE);
        $userid = $this->input->post('userid', TRUE);
        $cancelbooking = $this->input->post('cancelstatus', TRUE);

        if ($bookid == null && $userid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid = $data['bookid'];
            $userid = $data['userid'];
            $cancelbooking = $data['cancelstatus'];
        }
        $rowdetail = $this->webservice_model->get_acceptuserdetails($bookid);
        if ($rowdetail->book_status == "accept") {
            $data['status'] = '2';
            $data['message'] = 'Rider Already accept your booking please wait for few minutes';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            if ($this->webservice_model->rider_cancel_booking($bookid, $userid, $cancelbooking) != false) {
                $data['status'] = '1';
                $data['message'] = 'Rider Cancel success';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
                /*
                 *  PUSH Notification to Remove if User cancels Booking || Timeout.!!!
                 */
                $userbooking = $this->User_booking_model->get($bookid);
                if ($userbooking) {
                    $rider_id = explode(',', $userbooking->receive_riderid);
                    $results = $this->webservice_model->get_cancel_bookriderlist($rider_id,$bookid);
                    foreach ($results as $ress) {
                        $id = $ress->rider_id;
                        if (($logindetails = $this->webservice_model->getrideriddetail($id)) != false) {
                            $ids = $logindetails->gcm_userid;
                            $apikeyid = $this->webservice_model->getapikey();
                            $apiKey = $apikeyid->apikey;
                            $registatoin_ids = $logindetails->gcm_regid;
                            $message = "User cancelled booking";
                            //$message = $id;
                            $task = "user_cancel_booking";
                            $title = "piggyback";
                            $url = $apikeyid->url;
                            $fields = array(
                                'to' => $registatoin_ids,
                                'priority' => "high",
                                'notification' => array("body" => $message, "sound" => "bike_start_up.mp3"), //, 'click_action' => 'in.efficienza.piggyback.RiderSide.RiderRideAcceptScreen'
                                'data' => array("message" => $message, "task" => $task, "bookid" => $bookid, "title" => $title)
                            );
                            $headers = array(
                                'Authorization: key=' . $apiKey,
                                'Content-Type: application/json'
                            );
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                            $result = curl_exec($ch);
                            curl_close($ch);
                        }
                    }
                }
            } else {
                $data['status'] = '0';
                $data['message'] = 'Not Cancel';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        }
    }

    /* function ridercancelbooking()
      {
      $bookid = $this->input->post('bookid', TRUE);
      $typecancel = $this->input->post('cancelstatus', TRUE);
      $status = $this->input->post('cancelreason', TRUE);
      if($typecancel == "1")
      {
      $rt = 'Cancel';
      }
      $type ="Rider";
      $cancel = array(
      'book_status' => $rt,
      'book_cancelreason' => $this->input->post('cancelreason', TRUE),
      'book_canceltype' => $type
      );
      if (($ress = $this->webservice_model->get_riderid($bookid)) != false)
      {
      //$result = $this->webservice_model->get_riderid($bookid);
      $id = $ress->book_userid;
      $logindetails = $this->webservice_model->getusernotification($id);
      $apikeyid = $this->webservice_model->getapikey();
      $apiKey = $apikeyid->apikey;
      $registatoin_ids = $logindetails->gcm_regid;
      $task = "user_cancel";
      $reason = $this->webservice_model->getcancel_reason($status);
      $message = "Your booking is cancelled, Reason : " . $reason->book_creason;
      //$message = "Your booking is cancelled, Reason : " . $status;

      $id =$bookid;
      $url = $apikeyid->url;
      $fields = array(
      'registration_ids' => array($registatoin_ids),
      'data' => array( "message" => $message, "task" => $task, "id" => $id ),
      );
      $headers = array(
      'Authorization: key=' . $apiKey,
      'Content-Type: application/json'
      );
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
      $result = curl_exec($ch);
      curl_close($ch);
      $userdetail = $this->webservice_model->cancel_booking($bookid, $cancel);
      $rider = $ress->allocate_riderid;
      $this->webservice_model->change_bookingstatusrider1($rider);
      $data['status'] = '1';
      $data['message'] = 'Cancel Success';
      $data = array('response' => $data);
      $this->load->view('webservices/json', $data);
      }
      else
      {
      $data['status'] = '0';
      $data['message'] = 'Booking is already cancelled';
      $data = array('response' => $data);
      $this->load->view('webservices/json', $data);
      }
      } */

    function ridercancelbooking() {
        $bookid = $this->input->post('bookid', TRUE);
        $typecancel = $this->input->post('cancelstatus', TRUE);
        $status = $this->input->post('cancelreason', TRUE);
        if ($bookid == null && $typecancel == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid = $data['bookid'];
            $typecancel = $data['cancelstatus'];
            $status = $data['cancelreason'];
        }
        if ($typecancel == "1") {
            $rt = 'Cancel';
        }
        $type = "Rider";
        $cancel = array(
            'book_status' => $rt,
            'book_cancelreason' => $status,
            'book_canceltype' => $type,
            'book_canceldate' => date('Y-m-d'),
            'book_canceltime' => date('H:i:s')
        );
        if (($ress = $this->webservice_model->get_riderid($bookid)) != false) {
            //$rt = $this->webservice_model->get_riderid($bookid);
            $id = $ress->book_userid;
            $rider = $ress->allocate_riderid;
            $logindetails = $this->webservice_model->getusernotification($id);
            if ($logindetails->gcm_mobileos == 'iOS') {
                if ($id == $logindetails->gcm_userid) {
                    $bookinfo = array('code' => 'rider_cancelled_ride');
                    $apikeyid = $this->webservice_model->getapiserverkey($logindetails->gcm_mobileos);
                    $url = $apikeyid->url;
                    $token = $logindetails->gcm_regid;
                    $serverKey = $apikeyid->apikey;
                    $title = "Booking Canceled";
                    $body = "Your booking is cancelled by rider";
                    $notification = array('title' => $title, 'text' => $body, 'sound' => 'default', 'badge' => '1', "rider_booking" => $bookinfo, 'click_action' => 'in.efficienza.piggyback.User_Conformation_Screen');
                    $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority' => 'high', "message" => $body, "id" => $id, "title" => $title);
                    $headers = array(
                        'Authorization: key=' . $serverKey,
                        'Content-Type: application/json'
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
                    //Send the request
                    $response = curl_exec($ch);
                    //Close requestrider_accept
                    if ($response === FALSE) {
                        die('FCM Send Error: ' . curl_error($ch));
                    }
                    curl_close($ch);
                }
            } else {
                $apikeyid = $this->webservice_model->getapikey();
                $apiKey = $apikeyid->apikey;
                $registatoin_ids = $logindetails->gcm_regid;
                $task = "user_cancel";
                $reason = $this->webservice_model->getcancel_reason($status);
                //$message = "Your booking is cancelled, Reason : " . $reason->book_creason;
                $id = $bookid;
                $message = "Your booking is cancelled";
//$message = "Your booking is cancelled#user_cancel#piggyback#".$id;
                $url = $apikeyid->url;
                $title = "piggyback";
                $fields = array(
                    'to' => $registatoin_ids,
                    'priority' => "high",
                    'notification' => array("body" => $message, "sound" => "bike_start_up.mp3", 'click_action' => 'in.efficienza.piggyback.User_Conformation_Screen'),
                    'data' => array("message" => $message, "task" => $task, "id" => $id, "title" => $title)
                );
                $headers = array(
                    'Authorization: key=' . $apiKey,
                    'Content-Type: application/json'
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
                curl_close($ch);
                $userdetail = $this->webservice_model->cancel_booking($bookid, $cancel);
                $rider = $ress->allocate_riderid;
                //$data['userid'] = $logindetails;
            }
            $this->webservice_model->change_bookingstatusrider1($rider);
            $data['status'] = '1';
            $data['message'] = 'Cancel Success';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Booking is already cancelled';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    function getting_userratings() {
        $userid = $this->input->post('userid', TRUE);
        //IPHONE Values
        if ($userid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $userid = $data['userid'];
        }
        if (($user = $this->webservice_model->getting_userratingsprocess($userid)) != false) {
            $riderid = $userid;
            $type = 'User';
            $resrider = $this->webservice_model->get_ridergcmdetail($riderid, $type);
            $data['mobile_model'] = $resrider->gcm_mobileos;
            $data['gcmid'] = $resrider->gcm_regid;
            $data['mobile_serial'] = $resrider->gcm_serialno;
            $data['user_ratings'] = $user->user_rating;
            $data['status'] = '1';
            $data['message'] = 'Rating details Displayed';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Rating details Not Displayed';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    function viewriderstatus() {
        $riderid = $this->input->post('riderid', TRUE);
        if ($riderid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $riderid = $data['riderid'];
        }
        if (($res = $this->webservice_model->view_riderstatus($riderid)) != false) {
            if ($res->rider_status == 1) {
                $s = "Rider status is online";
            } else {
                $s = "Rider status is offline";
            }
            $type = 'Rider';
            $resrider = $this->webservice_model->get_ridergcmdetail($riderid, $type);
            $data['mobile_model'] = $resrider->gcm_mobileos;
            $data['gcmid'] = $resrider->gcm_regid;
            $data['mobile_serial'] = $resrider->gcm_serialno;
            $data['riderstatus'] = $res->rider_status;
            $data['rider_adminaccept'] = $res->rider_adminaccept;
            $data['remaining_days'] = ($res->rider_subscribe > 0) ? $res->rider_subscribe : "0";
            $data['status'] = '1';
            $data['message'] = $s;
            $data['rider_rating'] = $res->rider_rating;
            $data['notify_bookid'] = $res->rider_notify_bookid;
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Not view rider status ';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /*
     * Conditions : taking to browser.
     * 	Try to make window.close function work from browser
     */
    /* 	function urllink() {
      $id = $this->uri->segment(4);
      $type = $this->uri->segment(3);
      $path=base_url('Webservice/forgot_page/'.$type.'/'.$id.'');
      echo "<script>function open_forgotPasswordPage() { window.open('$path') } open_forgotPasswordPage();</script>";
      }
     */

    function urllink() {
        $this->load->view('forgot');
    }

    function forgotpassword() {
        $email = $this->input->post('emailid', TRUE);
        $type = $this->input->post('usertype', TRUE);
        if ($email == null && $type == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $email = $data['emailid'];
            $type = $data['usertype'];
        }
        if (($s = $this->webservice_model->forgot_password($email, $type)) != false) {
            $riderid = (isset($s->rider_id)) ? $s->rider_id : "";
            $userid = (isset($s->user_id)) ? $s->user_id : "";
            $data['user_id'] = ($riderid == null) ? $userid : $riderid;
            $data['status'] = '1';
            $data['mobile_no'] = ($s->user_mobileno == null) ? $s->rider_mobileno : $s->user_mobileno;
            $data['message'] = 'OTP will send to you shortly';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Enter valid Mobile Number';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    function update_forgotpassword() {
        $type = $this->input->post('usertype', TRUE);
        $new_password = md5($this->input->post('newpassword', TRUE));
        $confirm_password = md5($this->input->post('confirmpassword', TRUE));
        $user_id = $this->input->post('user_id', TRUE);
        if ($user_id == null && $type == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $user_id = $data['user_id'];
            $confirm_password = md5($data['confirmpassword']);
            $new_password = md5($data['newpassword']);
            $type = $data['usertype'];
        }
        if ($new_password == null && $user_id == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $type = $data['usertype'];
            $new_password = md5($data['newpassword']);
            $confirm_password = md5($data['confirmpassword']);
            $user_id = $data['user_id'];
        }
        if ($new_password == $confirm_password) {
            $result = $this->webservice_model->update_passwordprocess($user_id, $new_password, $type);
            if (!$result) {
                $data['status'] = '0';
                $data['message'] = 'Existing and new password are same please try again';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            } else {
                $data['status'] = '1';
                $data['message'] = 'Password updated successfully';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        } else {
            $data['status'] = '0';
            $data['message'] = 'Password does not match';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /* function changepassword()
      {
      $rider_id = $this->input->post('riderid', TRUE);
      $rider_password = $this->input->post('newpassword', TRUE);
      if ($this->webservice_model->change_passwordprocess($rider_id, $rider_password) != false)
      {
      $data['status'] = '1';
      $data['message'] = 'Password change success';
      $data = array('response' => $data);
      $this->load->view('webservices/json', $data);
      }
      else
      {
      $data['status'] = '0';
      $data['message'] = 'Not change password';
      $data = array('response' => $data);
      $this->load->view('webservices/json', $data);
      }
      } */

    function changepassword() {
        $opass = $_POST['opass'];
        $riderid = $_POST['riderid'];
        $type = $_POST['type'];
        if ($this->webservice_model->change_passwordprocess($riderid, $opass, $type) != false) {
            $v = "Your password has been changed";
            echo json_encode($v);
        } else {
            $v = "Enter valid E-mail";
            echo json_encode($v);
        }
    }

    function onride_rider_information_touser() {
        $bookid = $this->input->post('bookid', TRUE);
        //IPHONE Values
        if ($bookid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $bookid = $data['bookid'];
        }
        if (($res = $this->webservice_model->Rider_DriverInformation($bookid)) != false) {
            $only_distance = substr($res->book_distance, 0, -3); // For ios only.!!!
            $linkprofile = base_url('uploads/profile');
            $data['status'] = '1';
            $data['message'] = 'Information received successfully';
            $data['rider_id'] = $res->rider_id;
            $data['rider_name'] = $res->rider_name;
            $data['mobile_number'] = $res->rider_mobileno;
            $data['rider_image'] = $linkprofile . "/" . $res->rider_picture;
            $data['rider_ratings'] = $res->rider_rating;
            $data['confirm_user_otp'] = $res->confirm_user_otp;
            $data['bike_manufacture'] = $res->rider_vehicle_manufacturing;
            $data['bike_modal'] = $res->rider_vehicle_model;
            $data['bike_color'] = $res->rider_vehicle_color;
            $data['bike_number'] = $res->rider_vehicle_bikeno;
            $data['from'] = $res->book_fromlocation;
            $data['destination'] = $res->book_tolocation;
            $data['only_distance'] = "$only_distance"; //Only for iOS only
            $data['book_distance'] = "$res->book_distance"; // For iOS only
            $data['from_latlong'] = $res->book_fromlatitude . "," . $res->book_fromlongitude;
            $data['from_lat'] = "$res->book_fromlatitude"; //For iOS only
            $data['from_long'] = "$res->book_fromlongitude"; //For iOS only
            $data['to_latlong'] = $res->book_tolatitude . "," . $res->book_tolongitude;
            $data['to_lat'] = "$res->book_tolatitude"; //For iOS only
            $data['to_long'] = "$res->book_tolongitude"; //For iOS only
            $data['book_status'] = "$res->book_status"; //For iOS only
            $data['book_riderrating'] = "$res->book_riderrating"; //For iOS only
            $data['book_userrating'] = "$res->book_riderrating"; //For iOS only
            $data['book_finalamount'] = "$res->book_finalamount"; //For iOS only
            $startTimer = new DateTime("$res->rider_latlongtime");
            $endTimer = new DateTime("$res->book_time");
            $duration = $startTimer->diff($endTimer);
            $data['duration'] = $duration->format("%I min");
            $data['book_date'] = "$res->book_date"; //For iOS only
            $data['rider_arrival_time'] = "Your booking is confirmed,Please wait rider will arrive within " . $duration->format("%I min");
            $data['book_status'] = $res->book_status;
            $data['book_pickupstatus'] = $res->book_pickupstatus; // Once Rider started Ride.! User can't cancel Ride.!!!
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'No information found';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    function send_push_notification() {
        $apiKey = "AIzaSyBaF59OzAUa6OwPLUxzuZdnb-fpXR0SZU0";
        $registatoin_ids = "APA91bEmhumxkRj1WFm5uNL4Y3svpOX1SKyNQxcm63eMRjuLnIZOgb8OKL_9dKvt8uuJHz2DT4KQh8UsbinE-C7oaDIwUwwFruzolJ3PBEx7yZCBh5miBMUmthICv4tLaV8_BbJuebme";
        $message = "Welcome piggyback";
        $task = "cancel_status_u";
        //$task = "user_confirm";
        //$task = "ride_finished_user";
        $id = "3";
        $url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            'registration_ids' => array($registatoin_ids),
            //'data' => array( "message" => $message ),
            'data' => array("message" => $message, "task" => $task, "id" => $id),
        );
        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        $data['status'] = $result;
        $data['message'] = 'GCM details success';
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);

        //echo $result;
    }

    public function paid_conform() {
        $id = $this->input->post('bookid', TRUE);
        if ($bookid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $id = $data['bookid'];
        }
        //if (($logindetails = $this->webservice_model->user_paid_status($id)) != false)
        //{
        //$s = $this->webservice_model->gcm_logout_process($id, $type, $status);
        $logindetails = $this->webservice_model->user_paid_status($id);
        if ($logindetails->book_paidstatus == "") {
            $s = "0";
            $msg = "User not paid ";
        } else {
            $s = "1";
            $msg = "User paid successfully";
        }
        $data['status'] = $s;
        $data['message'] = $msg;
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
        /* }
          else
          {
          $data['status'] = '0';
          $data['message'] = 'Logout Failed';
          $data = array('response' => $data);
          $this->load->view('webservices/json', $data);
          } */
    }

    public function recent_userride_details() {
        $userid = $this->input->post('userid', TRUE);
        $count = $this->input->post('count', TRUE);
        //IPHONE Values
        if ($userid == null && $count == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $userid = $data['userid'];
            $count = $data['count'];
        }
        if (($logindetails = $this->webservice_model->recent_userride_details($userid)) != false) {
            if ($count == 10) {
                $data['user_recent_ride_list'] = array();
                $t = 1;
                foreach ($logindetails as $logindetail) {
                    $riderid = $logindetail->allocate_riderid;
                    $details = $this->webservice_model->view_riderstatus($riderid);
                    $linkprofile = base_url('uploads/profile');
                    if ($t <= 10) {
                        $temp['book_id'] = $logindetail->book_id;
                        $temp['book_fromlocation'] = $logindetail->book_fromlocation;
                        $temp['book_tolocation'] = $logindetail->book_tolocation;
                        $temp['book_distance'] = $logindetail->book_distance;
                        $temp['book_date'] = $logindetail->book_date;
                        $temp['rider_id'] = $logindetail->allocate_riderid;
                        $temp['rider_name'] = ($details->rider_name !=null) ? $details->rider_name : "";
                        $temp['rider_image'] = $linkprofile . "/" . $details->rider_picture;
                        $temp['rider_bikename'] = $details->rider_vehicle_manufacturing . "-" . $details->rider_vehicle_model;
                        $temp['book_status'] = $logindetail->book_status;
                        if ($logindetail->book_riderrating == "") {
                            $rate2 = "--";
                        } else {
                            $rate2 = $logindetail->book_riderrating . "/5";
                        }
                        $temp['book_riderrating'] = $rate2;
                        if ($logindetail->book_userrating == "") {
                            $rate3 = "--";
                        } else {
                            $rate3 = $logindetail->book_userrating . "/5";
                        }
                        $temp['book_userrating'] = $rate3;
                        if ($logindetail->book_status == "Cancel") {
                            $timestamp = strtotime("$logindetail->book_canceltime");
                            $time = date('H:i a', $timestamp);
                            $datestamp = strtotime("$logindetail->book_canceldate");
                            $date = date('D, d M y', $datestamp);
                            $date1 = "0000-00-00";
                            $time1 = "00:00";
                        } else {
                            $timestamp = strtotime("$logindetail->book_starttime");
                            $time = date('H:i a', $timestamp);
                            $datestamp = strtotime("$logindetail->book_startdate");
                            $date = date('D, d M y', $datestamp);

                            $timestamp1 = strtotime("$logindetail->book_endtime");
                            $time1 = date('H:i a', $timestamp1);
                            $datestamp1 = strtotime("$logindetail->book_enddate");
                            $date1 = date('D, d M y', $datestamp1);
                        }
                        $temp['book_starttime'] = $date . ", " . $time;
                        $temp['book_endtime'] = $date1 . ", " . $time1;
                        $temp['book_orignialamount'] = $logindetail->book_amount;
                        $temp['promo_code'] = $logindetail->book_promocode;
                        $temp['promo_value'] = $logindetail->book_promovalue;
                        $temp['promo_type'] = $logindetail->book_promotype;
                        $temp['book_finalamount'] = $logindetail->book_finalamount;
                        $startTimer = new DateTime("$logindetail->book_starttime");
                        $endTimer = new DateTime("$logindetail->book_endtime");
                        $duration = $startTimer->diff($endTimer);
                        $temp['book_ride_timing'] = $duration->format("%H hr:%I min");
                        $rider[] = $temp;
                    }
                    $t++;
                }
            } else {
                $data['user_recent_ride_list'] = array();
                $t = 1;
                $count1 = $count + 10;
                foreach ($logindetails as $logindetail) {
                    $riderid = $logindetail->allocate_riderid;
                    $detail = $this->webservice_model->view_riderstatus($riderid);
                    $linkprofile = base_url('uploads/profile');
                    if ($t <= $count && $logindetail->book_id > 10) {
                        $temp['book_id'] = $logindetail->book_id;
                        $temp['book_fromlocation'] = $logindetail->book_fromlocation;
                        $temp['book_tolocation'] = $logindetail->book_tolocation;
                        $temp['book_distance'] = $logindetail->book_distance;
                        $temp['book_date'] = $logindetail->book_date;
                        $temp['rider_id'] = $logindetail->allocate_riderid;
                        if ($detail->rider_name == null) {
                            $v = "";
                        } else {
                            $v = $detail->rider_name;
                        }
                        $temp['rider_name'] = ($detail->rider_name !=null) ? $detail->rider_name : "";
                        $temp['rider_image'] = $linkprofile . "/" . $detail->rider_picture;
                        $temp['rider_bikename'] = $detail->rider_vehicle_manufacturing . "-" . $detail->rider_vehicle_model;
                        $temp['book_status'] = $logindetail->book_status;
                        if ($logindetail->book_riderrating == "") {
                            $rate2 = "--";
                        } else {
                            $rate2 = $logindetail->book_riderrating . "/5";
                        }
                        $temp['book_riderrating'] = $rate2;
                        if ($logindetail->book_userrating == "") {
                            $rate3 = "--";
                        } else {
                            $rate3 = $logindetail->book_userrating . "/5";
                        }
                        $temp['book_userrating'] = $rate3;
                        if ($logindetail->book_status == "Cancel") {
                            $timestamp = strtotime("$logindetail->book_canceltime");
                            $time = date('H:i a', $timestamp);
                            $datestamp = strtotime("$logindetail->book_canceldate");
                            $date = date('D, d M y', $datestamp);
                            $date1 = "0000-00-00";
                            $time1 = "00:00";
                        } else {
                            $timestamp = strtotime("$logindetail->book_starttime");
                            $time = date('H:i a', $timestamp);
                            $datestamp = strtotime("$logindetail->book_startdate");
                            $date = date('D, d M y', $datestamp);

                            $timestamp1 = strtotime("$logindetail->book_endtime");
                            $time1 = date('H:i a', $timestamp1);
                            $datestamp1 = strtotime("$logindetail->book_enddate");
                            $date1 = date('D, d M y', $datestamp1);
                        }
                        $temp['book_starttime'] = $date . ", " . $time;
                        $temp['book_endtime'] = $date1 . ", " . $time1;
                        $temp['book_orignialamount'] = $logindetail->book_amount;
                        $temp['promo_code'] = $logindetail->book_promocode;
                        $temp['promo_value'] = $logindetail->book_promovalue;
                        $temp['promo_type'] = $logindetail->book_promotype;
                        $temp['book_finalamount'] = $logindetail->book_finalamount;
                        $startTimer = new DateTime("$logindetail->book_starttime");
                        $endTimer = new DateTime("$logindetail->book_endtime");
                        $duration = $startTimer->diff($endTimer);
                        $temp['book_ride_timing'] = $duration->format("%H hr:%I min");
                        $rider[] = $temp;
                    }
                    $t++;
                }
            }
            $data['user_recent_ride_list'] = $rider;
            $data['status'] = '1';
            $data['message'] = 'Recent Ride user list';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '1';
            $data['message'] = 'No Recent Ride user list';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function recent_riderride_details() {
        $riderid = $this->input->post('riderid', TRUE);
        $count = $this->input->post('count', TRUE);
        //IPHONE Values
        if ($riderid == null && $count == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $riderid = $data['riderid'];
            $count = $data['count'];
        }
        if (($logindetails = $this->webservice_model->recent_riderride_details($riderid)) != false) {
            if ($count == 10) {
                $data['rider_recent_ride_list'] = array();
                $t = 1;
                foreach ($logindetails as $logindetail) {
                    $userid = $logindetail->book_userid;
                    $details = $this->webservice_model->view_userstatus($userid);
                    $riderid = $logindetail->allocate_riderid;
                    $detail = $this->webservice_model->view_riderstatus($riderid);
                    $linkprofile = base_url('tempuploads/Tempuserprofile');
                    $profile = base_url('uploads/Profile.png');
                    if ($t <= 10) {
                        $temp['book_id'] = $logindetail->book_id;
                        $temp['book_fromlocation'] = $logindetail->book_fromlocation;
                        $temp['book_tolocation'] = $logindetail->book_tolocation;
                        $temp['book_distance'] = $logindetail->book_distance;
                        $temp['rider_id'] = $logindetail->allocate_riderid;
                        if ($details->user_name == null) {
                            $v = "";
                        } else {
                            $v = $details->user_name;
                        }
                        $temp['user_name'] = $v;
                        $temp['rider_bikename'] = $detail->rider_vehicle_manufacturing . "-" . $detail->rider_vehicle_model;
                        $temp['book_status'] = $logindetail->book_status;
                        if ($logindetail->book_userrating == "") {
                            $rate2 = "--";
                        } else {
                            $rate2 = $logindetail->book_userrating . "/5";
                        }
                        $temp['book_userrating'] = $rate2;
                        if ($logindetail->book_riderrating == "") {
                            $rate3 = "--";
                        } else {
                            $rate3 = $logindetail->book_riderrating . "/5";
                        }
                        $temp['book_riderrating'] = $rate3;
                        if ($logindetail->book_status == "Cancel") {
                            $timestamp = strtotime("$logindetail->book_canceltime");
                            $time = date('H:i a', $timestamp);
                            $datestamp = strtotime("$logindetail->book_canceldate");
                            $date = date('D, d M y', $datestamp);
                            $date1 = "0000-00-00";
                            $time1 = "00:00";
                        } else {
                            $timestamp = strtotime("$logindetail->book_starttime");
                            $time = date('H:i a', $timestamp);
                            $datestamp = strtotime("$logindetail->book_startdate");
                            $date = date('D, d M y', $datestamp);

                            $timestamp1 = strtotime("$logindetail->book_endtime");
                            $time1 = date('H:i a', $timestamp1);
                            $datestamp1 = strtotime("$logindetail->book_enddate");
                            $date1 = date('D, d M y', $datestamp1);
                        }
                        $temp['book_starttime'] = $date . ", " . $time;
                        $temp['book_endtime'] = $date1 . ", " . $time1;
                        $temp['book_orignialamount'] = $logindetail->book_amount;
                        $temp['promo_code'] = $logindetail->book_promocode;
                        $temp['promo_value'] = $logindetail->book_promovalue;
                        $temp['promo_type'] = $logindetail->book_promotype;
                        $temp['book_finalamount'] = $logindetail->book_finalamount;
                        if ($details->user_image == "") {
                            $r = $profile;
                        } else {
                            $r = $linkprofile . "/" . $details->user_image;
                        }
                        $temp['user_image'] = $r;
                        $startTimer = new DateTime("$logindetail->book_starttime");
                        $endTimer = new DateTime("$logindetail->book_endtime");
                        $duration = $startTimer->diff($endTimer);
                        $temp['book_ride_timing'] = $duration->format("%H hr:%I min");
                        $rider[] = $temp;
                    }
                    $t++;
                }
            } else {
                $data['rider_recent_ride_list'] = array();
                $t = 1;
                $count1 = $count + 10;
                foreach ($logindetails as $logindetail) {
                    $userid = $logindetail->book_userid;
                    $details = $this->webservice_model->view_userstatus($userid);
                    $riderid = $logindetail->allocate_riderid;
                    $detail = $this->webservice_model->view_riderstatus($riderid);
                    $linkprofile = base_url('tempuploads/Tempuserprofile');
                    $profile = base_url('uploads/Profile.png');
                    if ($t <= $count && $logindetail->book_id > 10) {
                        $temp['book_id'] = $logindetail->book_id;
                        $temp['book_fromlocation'] = $logindetail->book_fromlocation;
                        $temp['book_tolocation'] = $logindetail->book_tolocation;
                        $temp['book_distance'] = $logindetail->book_distance;
                        $temp['rider_id'] = $logindetail->allocate_riderid;
                        if ($details->user_name == null) {
                            $v = "";
                        } else {
                            $v = $details->user_name;
                        }
                        $temp['user_name'] = $v;
                        //$temp['rider_image'] = $linkprofile ."/".$details->rider_picture;
                        $temp['rider_bikename'] = $detail->rider_vehicle_manufacturing . "-" . $detail->rider_vehicle_model;
                        	$temp['book_status'] = $logindetail->book_status;
                        if ($logindetail->book_userrating == "") {
                            $rate2 = "--";
                        } else {
                            $rate2 = $logindetail->book_userrating . "/5";
                        }
                        $temp['book_userrating'] = $rate2;
                        if ($logindetail->book_riderrating == "") {
                            $rate3 = "--";
                        } else {
                            $rate3 = $logindetail->book_riderrating . "/5";
                        }
                        $temp['book_riderrating'] = $rate3;
                        if ($logindetail->book_status == "Cancel") {
                            $timestamp = strtotime("$logindetail->book_canceltime");
                            $time = date('H:i a', $timestamp);
                            $datestamp = strtotime("$logindetail->book_canceldate");
                            $date = date('D, d M y', $datestamp);
                            $date1 = "0000-00-00";
                            $time1 = "00:00";
                        } else {
                            $timestamp = strtotime("$logindetail->book_starttime");
                            $time = date('H:i a', $timestamp);
                            $datestamp = strtotime("$logindetail->book_startdate");
                            $date = date('D, d M y', $datestamp);

                            $timestamp1 = strtotime("$logindetail->book_endtime");
                            $time1 = date('H:i a', $timestamp1);
                            $datestamp1 = strtotime("$logindetail->book_enddate");
                            $date1 = date('D, d M y', $datestamp1);
                        }
                        $temp['book_starttime'] = $date . ", " . $time;
                        $temp['book_endtime'] = $date1 . ", " . $time1;
                        $temp['book_orignialamount'] = $logindetail->book_amount;
                        $temp['promo_code'] = $logindetail->book_promocode;
                        $temp['promo_value'] = $logindetail->book_promovalue;
                        $temp['promo_type'] = $logindetail->book_promotype;
                        $temp['book_finalamount'] = $logindetail->book_finalamount;
                        if ($details->user_image == "") {
                            $r = $profile;
                        } else {
                            $r = $linkprofile . "/" . $details->user_image;
                        }
                        $temp['user_image'] = $r;
                        $startTimer = new DateTime("$logindetail->book_starttime");
                        $endTimer = new DateTime("$logindetail->book_endtime");
                        $duration = $startTimer->diff($endTimer);
                        $temp['book_ride_timing'] = $duration->format("%H hr:%I min");
                        $rider[] = $temp;
                    }
                    $t++;
                }
            }
            $data['rider_recent_ride_list'] = $rider;
            $data['status'] = '1';
            $data['message'] = 'Recent Ride Rider list';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '1';
            $data['message'] = 'No Recent Ride Rider list';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function cancelreasons_for_userorrider() {
        $type = $this->input->post('type', TRUE);
        if ($type == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $type = $data['type'];
        }
        if (($logindetails = $this->webservice_model->get_cancel_details($type)) != false) {
            $data['cancel_list'] = array();
            foreach ($logindetails as $logindetail) {
                $temp['book_cid'] = $logindetail->book_cid;
                $temp['book_ctype'] = $logindetail->book_ctype;
                $temp['book_creason'] = $logindetail->book_creason;
                $rider[] = $temp;
            }
            $data['cancel_list'] = $rider;
            $data['status'] = '1';
            $data['message'] = 'cancel list';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'No cancel list';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function apply_subscribe_torider() {
        $subscribe_id = $this->input->post('subscribe_id', TRUE);
        $rider_id = $this->input->post('rider_id', TRUE);
        $status = $this->input->post('paytm_status', TRUE);
        $amount = $this->input->post('paytm_amount', TRUE);
        $txnid = $this->input->post('paytm_txn_id', TRUE);
        $datetime = $this->input->post('paytm_txn_date', TRUE);
        if ($subscribe_id == null && $rider == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $subscribe_id = $data['subscribe_id'];
            $rider = $data['rider_id'];
            $status = $data['paytm_status'];
            $amount = $data['paytm_amount'];
            $txnid = $data['paytm_txn_id'];
            $datetime = $data['paytm_txn_date'];
        }
        $rider_details=$this->Rider_registration_model->get($rider_id);
        if($rider_details) {
        $rider_subscribe=($rider_details->rider_subscribe > 0) ? $rider_details->rider_subscribe : "0";
        $details = $this->webservice_model->apply_subscribe_torider($subscribe_id);
        $subscribe_days=$details->subscribe_days + $rider_subscribe;
        $applysubscribe = array(
            'apply_riderid' => $rider_id,
            'apply_subscribeid' => $subscribe_id,
            'apply_days' => $details->subscribe_days,
            'apply_kilometers' => $details->subscribe_kilometers,
            'apply_startdate' => date('Y-m-d'),
            'apply_enddate' => date('Y-m-d', strtotime('+' . $subscribe_days . ' days')),
            'apply_paid' => $status,
            'apply_paytm_amount' => $amount,
            'apply_paytm_txn_id' => $txnid,
            'apply_paytm_txn_date' => $datetime
        );
        $apply = $details->subscribe_days;
        $sub_apply = $this->webservice_model->insert_subscribe_details($applysubscribe);
        }
        // $applyid = $this->webservice_model->getapplyid();
        // $sub_apply = $applyid->apply_id;
        $applydetail = $this->webservice_model->getapplyid_details($sub_apply);
        if ($applydetail->apply_paid == "TXN_SUCCESS") {
            $ds = $this->webservice_model->update_apply_subscribe($sub_apply,$rider_id);
            $ds = $this->webservice_model->update_subscribe($rider_id, $apply);
            $data['status'] = '1';
            $data['message'] = 'Your payment is successful';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'Payment failure ';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function subscribe_details() {
        $id = $this->input->post('id', TRUE);
        if ($id == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $id = $data['id'];
        }
        if (($logins = $this->webservice_model->get_subscribe_riderdetails($id)) != false) {
            $logindetails = $this->webservice_model->get_subscribe_details();
            $data['subscribe_list'] = array();
            foreach ($logins as $login) {
                $v = $login->apply_subscribeid;
                $detail = $this->webservice_model->get_subscribe_detailsrider($v);
                $temp['subscribe_id'] = $detail->subscribe_id;
                $temp['subscribe_name'] = $detail->subscribe_name;
                $temp['subscribe_amount'] = $detail->subscribe_amount;
                $temp['subscribe_days'] = $detail->subscribe_days;
                if ($detail->subscribe_offer == "" && $detail->subscribe_type == "") {
                    $w = "0";
                    $r = "0";
                } else {
                    $w = $detail->subscribe_offer;
                    $r = $detail->subscribe_type;
                }
                $temp['subscribe_offer'] = $w;
                $temp['subscribe_type'] = $r;
                $temp['subscribe_finalamount'] = $detail->subscribe_finalamt;
                $temp['subscribe_message'] = "Message for " . $detail->subscribe_name;
                $temp['subscribe_startdate'] = $login->apply_startdate;
                $temp['subscribe_enddate'] = $login->apply_enddate;
                $temp['subscribe_expiryon'] = $detail->subscribe_expdate;
                if ($login->apply_status == "1") {
                    $temp['activate'] = "1";
                } else {
                    $temp['activate'] = "0";
                }
                $temp['subscribe_paid'] = $login->apply_paid;
                $temp['apply_status'] = $login->apply_status;
                $rider[] = $temp;
            }
            /* foreach($logindetails as $logindetail)
              {
              $s = "0000-00-00";
              $temp['subscribe_id'] = $logindetail->subscribe_id;
              $temp['subscribe_name'] = $logindetail->subscribe_name;
              $temp['subscribe_amount'] = $logindetail->subscribe_amount;
              $temp['subscribe_days'] = $logindetail->subscribe_days;
              if($logindetail->subscribe_offer == "" && $logindetail->subscribe_type == "")
              {
              $w ="0";
              $r = "0";
              }
              else
              {
              $w =$logindetail->subscribe_offer;
              $r =$logindetail->subscribe_type;
              }
              $temp['subscribe_offer'] = $w;
              $temp['subscribe_type'] = $r;
              $temp['subscribe_finalamount'] = $logindetail->subscribe_finalamt;
              $temp['subscribe_message'] = "Message for ".$logindetail->subscribe_name;
              $temp['subscribe_startdate'] = $s;
              $temp['subscribe_enddate'] = $s;
              $temp['subscribe_expiryon'] = $logindetail->subscribe_expdate;
              $temp['activate'] = "0";
              $temp['subscribe_paid'] = "0";
              $rider[] = $temp;
              } */
            $riderid = $id;
            $log = $this->webservice_model->view_riderstatus($riderid);
            $data['subscribe_list'] = $rider;
            $data['remaining_days'] = ($log->rider_subscribe > 0) ? $log->rider_subscribe : "0";
            $data['status'] = '1';
            $data['message'] = 'subscribe list';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            /* $logindetails = $this->webservice_model->get_subscribe_details();
              $data['subscribe_list'] = array();
              foreach($logindetails as $logindetail)
              {
              $s = "0000-00-00";
              $temp['subscribe_id'] = $logindetail->subscribe_id;
              $temp['subscribe_name'] = $logindetail->subscribe_name;
              $temp['subscribe_amount'] = $logindetail->subscribe_amount;
              $temp['subscribe_days'] = $logindetail->subscribe_days;
              if($logindetail->subscribe_offer == "" && $logindetail->subscribe_type == "")
              {
              $w ="0";
              $r = "0";
              }
              else
              {
              $w =$logindetail->subscribe_offer;
              $r =$logindetail->subscribe_type;
              }
              $temp['subscribe_offer'] = $w;
              $temp['subscribe_type'] = $r;
              $temp['subscribe_finalamount'] = $logindetail->subscribe_finalamt;
              $temp['subscribe_message'] = "Message for ".$logindetail->subscribe_name;
              $temp['subscribe_startdate'] = $s;
              $temp['subscribe_enddate'] = $s;
              $temp['subscribe_expiryon'] = $logindetail->subscribe_expdate;
              $temp['activate'] = "0";
              $temp['subscribe_paid'] = "0";
              $rider[] = $temp;
              } */
            $riderid = $id;
            $log = $this->webservice_model->view_riderstatus($riderid);
            $data['subscribe_list'] = $rider;
            $data['remaining_days'] = ($log->rider_subscribe > 0) ? $log->rider_subscribe : "0";
            $data['status'] = '0';
            $data['message'] = 'No subscription applied';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function subscribe_list() {
        $logindetails = $this->webservice_model->get_subscribe_details();
        $data['subscribe_list'] = array();
        foreach ($logindetails as $logindetail) {
            $s = "0000-00-00";
            $temp['subscribe_id'] = $logindetail->subscribe_id;
            $temp['subscribe_name'] = $logindetail->subscribe_name;
            $temp['subscribe_amount'] = $logindetail->subscribe_amount;
            $temp['subscribe_offer'] = $logindetail->subscribe_offer;
            $temp['subscribe_days'] = $logindetail->subscribe_days;
            $temp['subscribe_finalamt'] = $logindetail->subscribe_finalamt;
            $temp['subscribe_kilometers'] = $logindetail->subscribe_kilometers;
            /* if($logindetail->subscribe_offer == "" && $logindetail->subscribe_type == "")
              {
              $w ="0";
              $r = "0";
              }
              else
              {
              $w =$logindetail->subscribe_offer;
              $r =$logindetail->subscribe_type;
              }
              $temp['subscribe_offer'] = $w;
              $temp['subscribe_type'] = $r;
              $temp['subscribe_finalamount'] = $logindetail->subscribe_finalamt;
              $temp['subscribe_message'] = "Message for ".$logindetail->subscribe_name;
              $temp['subscribe_startdate'] = $s;
              $temp['subscribe_enddate'] = $s;
              $temp['subscribe_expiryon'] = $logindetail->subscribe_expdate;
              $temp['activate'] = "0";
              $temp['subscribe_paid'] = "0"; */
            $rider[] = $temp;
        }
        /* $riderid = $id;
          $log = $this->webservice_model->view_riderstatus($riderid); */
        $data['subscribe_list'] = $rider;
        /* $data['remaining_days'] = $log->rider_subscribe; */
        $data['status'] = '1';
        $data['message'] = 'subscribe list';
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
    }

    public function subscribe_riderdetails() {
        $i = $this->input->post('riderid', TRUE);
        //$Amount = $this->input->post('Subs_Amount', TRUE);
        $subscribe_id = $this->input->post('Subs_Id', TRUE);
        if ($i == null && $subscribe_id == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $i = $data['riderid'];
            $subscribe_id = $data['Subs_Id'];
        }
        if (($logindetails = $this->webservice_model->getrideriddata($i)) != false) {
            $logindetail = $this->webservice_model->get_merchant_details();
            $subscribedetails = $this->webservice_model->apply_subscribe_torider($subscribe_id);
            $this->load->view('config_paytm');
            $this->load->view('encdec_paytm');
            $checkSum = "";
            // ORDER ID Generate.!!!
            $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
            $string = str_shuffle($string);
            $random_text = substr($string, 0, 8);
            $s = "HB" . $random_text;
            // Ends here...
            //CUSTOMER ID Generate.!!!
            $cust_id="HBK".$i;

            $paramList = array();
            $paramList["MID"] = "$logindetail->merchant_mid";
            $paramList["ORDER_ID"] = "$s";
            $paramList["CUST_ID"] = "$cust_id";
            $paramList["INDUSTRY_TYPE_ID"] = "$logindetail->merchant_industry";
            $paramList["CHANNEL_ID"] = "$logindetail->merchant_channel";
            // $paramList["TXN_AMOUNT"] = "$subscribedetails->subscribe_amount";
            $paramList["TXN_AMOUNT"] = "$subscribedetails->subscribe_finalamt";// 05-01-19 R.K
            $paramList["WEBSITE"] = "$logindetail->merchant_website";
            $paramList["CALLBACK_URL"] = "https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=$s";
            // $paramList["CALLBACK_URL"] = "https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=$s";

            $checkSum = getChecksumFromArray($paramList, PAYTM_MERCHANT_KEY);

            $checkdata = array(
                'check_sum' => $checkSum,
                'check_date' => date('Y-m-d'),
                'check_time' => date('H:i:s')
            );
            $inscheck = $this->webservice_model->insert_checksum($checkdata);


            $data['MID'] = "$logindetail->merchant_mid";
            $data['ORDER_ID'] = "$s";
            $data['CUST_ID'] = "$cust_id";
            $data['INDUSTRY_TYPE_ID'] = "$logindetail->merchant_industry";
            $data['CHANNEL_ID'] = "$logindetail->merchant_channel";
            // $data['TXN_AMOUNT'] = "$subscribedetails->subscribe_amount";
            $data['TXN_AMOUNT'] = "$subscribedetails->subscribe_finalamt";// 05-01-19 R.K
            $data['WEBSITE'] = "$logindetail->merchant_website";
            $data['CALLBACK_URL'] = "https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=$s";
            // $data["CALLBACK_URL"] = "https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=$s";

            $data['check_sum'] = $checkSum;

            $data['status'] = '1';
            $data['message'] = 'subscribe Rider details';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '1';
            $data['message'] = 'subscribe list';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function paytmCheckSumHash() {
        $this->load->view('config_paytm.php');
        $this->load->view('encdec_paytm');
        $checkSum = "";
        $merchantkey = 'YEQXSw_mnCAF9F5L';
        $findme = 'REFUND';
        $findmepipe = '|';
        $string = "abcdefghijklmnopqrstuvwxyz1234567890!@#$%^&*";
        $string = str_shuffle($string);
        $random_text = substr($string, 0, 8);
        $s = "HB" . $random_text;

        $paramList = array();

        $paramList["MID"] = 'BlueCo34587080518572';
        $paramList["ORDER_ID"] = '1';
        $paramList["RIDER_ID"] = '1';
        $paramList["INDUSTRY_TYPE_ID"] = 'Retail';
        $paramList["CHANNEL_ID"] = 'WAP';
        $paramList["MOBILE_NO"] = '8939897706';
        $paramList["EMAIL"] = 'gomathisri2@gmail.com';
        $paramList["TXN_AMOUNT"] = '20';
        $paramList["WEBSITE"] = 'APP_STAGING';

        foreach ($_POST as $key => $value) {
            $pos = strpos($value, $findme);
            $pospipe = strpos($value, $findmepipe);
            if ($pos === false || $pospipe === false) {
                $paramList[$key] = $value;
            }
        }
        $checkSum = getChecksumFromArray($paramList, $merchantkey);
        $data['onlinestatus'] = $checkSum;
        $data['status'] = '1';
        $data['message'] = 'Logout Success';
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
    }

    /* public function get_paytmresponse_detail()
      {
      $response = $this->input->post('response', TRUE);
      if (($logindetails = $this->webservice_model->get_paytmresponse()) != false)
      {
      $data['status'] = '1';
      $data['message'] = 'Your payment is successful';
      $data = array('response' => $data);
      $this->load->view('webservices/json', $data);
      }
      else
      {
      $data['status'] = '0';
      $data['message'] = 'Payment failure';
      $data = array('response' => $data);
      $this->load->view('webservices/json', $data);
      }

      } */

    public function logout_process() {
        $id = $this->input->post('id', TRUE);
        $type = $this->input->post('type', TRUE);
        $status = $this->input->post('status', TRUE);
        //IPHONE Values
        if ($id == null && $type == null && $status == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $status = $data['status'];
            $id = $data['id'];
            $type = $data['type'];
        }
        if (($logindetails = $this->webservice_model->rider_logout_process($id, $type, $status)) != false) {
            $s = $this->webservice_model->gcm_logout_process($id, $type, $status);
            $data['id'] = $id;
            $data['type'] = $type;
            $data['onlinestatus'] = $status;
            $data['status'] = '1';
            $data['message'] = 'Logout Success';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            if ($status == '0') {
                $s = $this->webservice_model->gcm_logout_process($id, $type, $status);
                $data['id'] = $id;
                $data['type'] = $type;
                $data['onlinestatus'] = $status;
                $data['status'] = '1';
                $data['message'] = 'Logout Success';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            } else {
                $data['status'] = '0';
                $data['message'] = 'Logout Failed';
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        }
    }

    function user_onride() {
        $bookid = $this->input->post('bookid', TRUE);
        if ($bookid == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $id = $data['bookid'];
        }
        if (($res = $this->webservice_model->Rider_DriverInformation($bookid)) != false) {
            $data['status'] = '1';
            $data['message'] = 'Information received successfully';
            $data['rider_name'] = $res->rider_name;
            $data['destination'] = $res->book_tolocation;
            $data['from_latlong'] = $res->book_fromlatitude . "," . $res->book_fromlongitude;
            $data['to_latlong'] = $res->book_tolatitude . "," . $res->book_tolongitude;
            $data['emergency_contactname'] = $res->rider_emobileno;
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else {
            $data['status'] = '0';
            $data['message'] = 'No information found';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    function fcmsend_push_notification() {

//        $apiKey = "AAAA8KGcCcM:APA91bFVGMUGBzyQ0heSbVW7y5wOAQy9dcO8UzCI_-o98Bx0_UPtRPu4MWLWvGMkvAlN_FFHsybVadl5tWQLpmWyBNL85mGebw3_dZYDZ1T89l6M6gbKjZnP6chhMPr_scOIzF_ZYDMg";
        $apiKey = "AAAAmGv8mso:APA91bFonGRyPZBz5fZb9H-OyM5gGKMUoI1pqZC71nw-eFZ-c7nKVrQPyt5oTdMbth4KxHsYMeyMHUutI_9LJ2Av1aKejWVE6QQzcAtt3jAXBSTZv5TlQPDKPLYb6_oxUKLWE49nRyAT";
        //$registatoin_ids="c2hWeq1MlQM:APA91bF4cmCZcD0jOVGUINVNer2zltEGcOldk2n4eNmU2eslgzzm71hTjzj9Fn9Cz1-eUuMg4onxFY_umdNUyljl-sGHB4q_Qk1Vp6A4SxAg2p64pVntfVBY0l2vGtWqopsFqCpvONgW";
        $registration_ids = $this->input->post('reg_id', TRUE);
        $id = "3";
        $message = "New_booking_details#user_confirm#piggyback#" . $id;
        //$task = "cancel_status_u";
        $task = "user_new_ride";
        //$task = "ride_finished_user";

        $url = 'https://fcm.googleapis.com/fcm/send';

        /* $fields = array(
          'registration_ids' => array($registatoin_ids),
          //'data' => array( "message" => $message ),
          'data' => array( "message" => $message, "task" => $task, "id" => $id ),
          ); */
        /* 	$fields = array(
          'registration_ids' => array($registration_ids),
          'data' => array( "message" => $message, "task" => $task, "id" => $id ),
          ); */
        $fields = array(
            'to' => $registration_ids,
            'priority' => "high",
            'notification' => array("body" => $message, "sound" => "bike_start_up.mp3", "vibrate" => true, 'click_action' => 'in.efficienza.piggyback.RiderSide.RiderRideAcceptScreen'),
            'data' => array("message" => $message, "task" => $task, "id" => $id)
        );
        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        $data['status'] = $result;
        //$data['data'] = array( "message" => $message, "task" => $task, "id" => $id );
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
    }

    public function user_status() {
        $user_id = $this->input->post('user_id', TRUE);
        $type = $this->input->post('type', TRUE);
        if ($user_id == null && $type == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $user_id = $data['user_id'];
            $type = $data['type'];
        }
        if ($user_id != null && $type != null) {
            $result = $this->webservice_model->User_loginStatus($user_id, $type);
            if ($type == 'User') {
                $data['user_id'] = $result->user_id;
                $data['status'] = $result->user_status;
                $data['referralcode'] = $result->user_referralcode;
                $data['username'] = $result->user_name;
                $data['usermbno'] = $result->user_mobileno;
                $data['usergender'] = $result->user_gender;
                $data['useremail'] = $result->user_email;
                $data['userename'] = $result->user_ename;
                $data['userembno'] = $result->user_emobileno;
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            } else {
                $data['user_id'] = $result->rider_id;
                $data['status'] = $result->rider_status;
                $data['referralcode'] = $result->rider_referralcode;
                $data['ridername'] = $result->rider_name;
                $data['ridermbno'] = $result->rider_mobileno;
                $data['ridergender'] = $result->rider_gender;
                $data['rideremail'] = $result->rider_email;
                $data['riderliceneno'] = $result->rider_liceneno;
                $data['riderprofession'] = $result->rider_profession;
                $data['rideraddress'] = $result->rider_address;
                $data['ridervehicle'] = $result->rider_vehicle_manufacturing;
                $data['ridervehiclemodel'] = $result->rider_vehicle_model;
                $data['ridervehicleyear'] = $result->rider_vehicle_year;
                $data['ridervehiclecolor'] = $result->rider_vehicle_color;
                $data['ridervehiclebikeno'] = $result->rider_vehicle_bikeno;
                $data['riderecontactname'] = $result->rider_econtactname;
                $data['rideremobileno'] = $result->rider_emobileno;
                $data = array('response' => $data);
                $this->load->view('webservices/json', $data);
            }
        } else {
            $data['message'] = "Invalid Parameters";
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    /*
     *  It is created to Update the status of Booking for the Rider || Customer
     *  Newly created.!!!
     */

    public function user_booking_status() {
        $user_id = $this->input->post('user_id', TRUE); //Riderid | Userid
        $bookid = $this->input->post('bookid', TRUE);
        $type = $this->input->post('type', TRUE);
        if ($user_id == null && $type == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $user_id = $data['user_id'];
            $type = $data['type'];
            $ride_status = $data['ride_status'];
        }
        $results = $this->webservice_model->booking_status_return($user_id, $bookid, $type);
        if ($results->book_status == 'Cancel' && $results->book_canceltype == 'User') {
            $data['message'] = "Booking Canceled by User";
            $data['book_status'] = $results->book_status;
            $data['status'] = '1';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        } else if ($results->book_cancel1 == 'cancel') {
            $data['message'] = "Booking Canceled by Rider";
            $data['book_status'] = $results->book_cancel1;
            $data['status'] = '1';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }
    }

    public function emergency_insert() {
        $user_id = $this->input->post('user_id', TRUE);
        $rider_id = $this->input->post('rider_id', TRUE);
        $book_id = $this->input->post('book_id', TRUE);
        $call_number = $this->input->post('call_number', TRUE);
        $caller_name = $this->input->post('caller_name', TRUE);
        $call_time = $this->input->post('call_time', TRUE);
        $location = $this->input->post('location', TRUE);
        $call_latitude = $this->input->post('call_latitude', TRUE);
        $call_longitude = $this->input->post('call_longitude', TRUE);
        $reference_text = $this->input->post('reference_text', TRUE);
        $status = $this->input->post('status', TRUE);
        if ($user_id == null && $rider_id == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $user_id = $data['user_id'];
            $rider_id = $data['rider_id'];
            $book_id = $data['book_id'];
            $call_number = $data['call_number'];
            $caller_name = $data['caller_name'];
            $call_time = $data['call_time'];
            $location = $data['location'];
            $call_latitude = $data['call_latitude'];
            $call_longitude = $data['call_longitude'];
            $reference_text = $data['reference_text'];
            $status = $data['status'];
        }
        $emergency=array(
            'user_id' =>$user_id,
            'rider_id' =>$rider_id,
            'book_id' =>$book_id,
            'call_number' =>$call_number,
            'caller_name' =>$caller_name,
            'call_time' =>date('Y-m-d H:i:s'),
            'location' =>$location,
            'call_latitude' =>$call_latitude,
            'call_longitude' =>$call_longitude,
            'reference_text' =>$reference_text,
            'created_at' =>date('Y-m-d H:i:s')
        );
        $result=$this->webservice_model->insert_emergency($emergency);
        if($result) {
            $data['status'] = '1';
            $data['message'] = 'Emergency call details are stored';
            $data = array('response' => $data);
            $this->load->view('webservices/json', $data);
        }

    }
    public function app_version() {
        $data['app'] = $this->webservice_model->app_version();
        $data['status'] = '1';
        $data['message'] = 'App version';
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
    }
    public function cancel_reason() {
        $user_id = $this->input->post('user_id', TRUE); //Riderid | Userid
        $type = $this->input->post('type', TRUE);
        if ($user_id == null && $type == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $user_id = $data['user_id'];
            $type = $data['type'];
        }
        $reason=$this->webservice_model->cancel_reason_type($type);
        $data['result']=$reason;
        $data['status'] = '1';
        $data['message'] = 'Cancel reason';
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
    }
    public function user_gcm_details() {
        $user_id = $this->input->post('user_id', TRUE);
        $type = $this->input->post('type', TRUE);
        if ($user_id == null && $type == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $user_id = $data['user_id'];
            $type = $data['type'];
        }
        $data['result']=$this->webservice_model->getgcmdetails($user_id,$type);
        $data['status'] = '1';
        $data['message'] = 'Cancel reason';
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
    }

}
