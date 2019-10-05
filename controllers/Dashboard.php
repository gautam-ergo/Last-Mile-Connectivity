<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('login_model', 'webservice_model', 'Apply_subscribe_model', 'User_booking_model', 'Rider_registration_model','Subscribe_details_model','Emergency_model','User_booking_attempts_model','Rider_groups_model'));
        $admin_id = $_SESSION['admin_id'];
        if (!isset($admin_id)) {
            $this->session->set_flashdata('error', 'Session Expired!! Please Login Again!!');
            redirect('login');
        }
    }

    public function index() {
    }
    public function rider_groups() {
        $data['groups']=$this->Rider_groups_model->get_all();
        $data['active'] = 1;
        $this->load->view('inc/header', $data);
        $this->load->view('rider/groups/index', $data);
        $this->load->view('inc/footer');
    }
    function add_riderGroup() {
        $group=array(
            'group_name'=>$this->input->post('group_name')
        );
        $result=$this->Rider_groups_model->insert($group);
        if($result) {
            $this->session->set_flashdata('success', 'Added successfully!!!');
            redirect('dashboard/rider_groups');
        } else {
            redirect('dashboard/rider_groups');
        }
    }
    function delete_grouplist() {
            $id = $this->uri->segment(3);
            if ($this->Rider_groups_model->delete($id) != false) {
                $this->session->set_flashdata('success', 'Deleted successfully!!!');
                redirect('dashboard/rider_groups');
            } else {
                redirect('dashboard/rider_groups');
            }
    }
    function update_riderGroup() {
        $id = $this->uri->segment(3);
        $group=array(
            'group_name'=>$this->input->post('group_name')
        );
        $result=$this->Rider_groups_model->update($group,$id);
        if($result) {
            $this->session->set_flashdata('success', 'Updated successfully!!!');
            redirect('dashboard/rider_groups');
        } else {
            redirect('dashboard/rider_groups');
        }
    }
    function view_riderGroup() {
        $grouplist=$this->Rider_groups_model->get_all();
        $riderlist_count=0;
        if($grouplist){
        foreach($grouplist as $list) {
        $riderlist=unserialize($list->rider_ids);
        $riderlist_count=($riderlist!=null) ? count($riderlist) : 0;
        $list->counts=$riderlist_count;
        }
        }
        $data['groups']=$grouplist;
        $data['results'] = $this->Rider_groups_model->add_rider_grouplist();
        $data['active']=14;
        $this->load->view('inc/header', $data);
        $this->load->view('rider/groups/list', $data);
        $this->load->view('inc/footer');
    }
    function add_riderToGroup() {
        $rider_id=$this->uri->segment(3);
        $group_name=$this->input->post('group_name');
        if($group_name) {
            foreach($group_name as $group) {
                $grouplist=$this->Rider_groups_model->get($group);
                if(!$grouplist->rider_ids) {
                    $grouping=array(
                        'rider_ids'=>serialize(array($rider_id))
                    );
                    $result=$this->Rider_groups_model->update($grouping,$group);
                    $this->session->set_flashdata('success','Added successfully to the group');
                } else {
                    $riderlist=unserialize($grouplist->rider_ids);
                    if (!in_array("$rider_id", $riderlist)) {
                    array_push($riderlist,$rider_id);
                    }
                    $grouping=array(
                        'rider_ids'=>serialize($riderlist)
                    );
                    $result=$this->Rider_groups_model->update($grouping,$group);
                    $this->session->set_flashdata('success','Added successfully to the group');
                }
            }
        }
        redirect('view-riderGroup');
    }
    function remove_rider() {
        $rider_id=$this->uri->segment(3);
        $group_id=$this->uri->segment(4);
        if($group_id) {
            $grouplist=$this->Rider_groups_model->get($group_id);
                $riderlist=unserialize($grouplist->rider_ids);
                if (in_array("$rider_id", $riderlist)) {
                    if (($key = array_search("$rider_id", $riderlist)) !== false) {
                        unset($riderlist[$key]);
                    }
                }
                $grouping=array(
                    'rider_ids'=>serialize($riderlist)
                );
                $result=$this->Rider_groups_model->update($grouping,$group_id);
                if($result) {
                    $this->session->set_flashdata('success','Rider removed successfully');
                    redirect("single-groups/$group_id");
                } else {
                    $this->session->set_flashdata('error','Failed, Try again later!!!');
                    redirect("single-groups/$group_id");
                }
        } else {
            $this->session->set_flashdata('error','Please try again');
            redirect('view-riderGroup');
        }
    }
    function single_groups() {
        $group_id=$this->uri->segment(2);
        $groups=$this->Rider_groups_model->get($group_id);
        $riderids=unserialize($groups->rider_ids);
        if($riderids!=null) {
        $data['groups']=$groups;
        $data['results'] = $this->Rider_groups_model->get_rider_grouplist($riderids);
        } else {
        $data['groups']=$groups;
        $data['results'] ="";
        }
        $data['active']=14;
        $data['riderids']=$riderids;
        $this->load->view('inc/header', $data);
        $this->load->view('rider/groups/single', $data);
        $this->load->view('inc/footer');
    }

    public function approve_user_detail() {
        $id = $this->uri->segment(3);
        if ($this->login_model->approve_user_detail($id) != false) {
            $this->session->set_flashdata('success', 'moved successfully!!!');
            redirect('dashboard/user_failedlist');
        } else {
            redirect('dashboard/user_failedlist');
        }
    }

    public function user_failedlist() {
            $data['results'] = $this->login_model->get_user_failedlist();
            $data['active'] = 3;
            $this->load->view('inc/header', $data);
            $this->load->view('user/failedlist', $data);
            $this->load->view('inc/footer');
    }
    public function approve_riderlist() {
            $id = $this->uri->segment(3);
            if ($this->login_model->approve_failedRider($id) != false) {
                $this->session->set_flashdata('success','approved successfully');
                redirect('rider-failedlist');
            } else {
                redirect('rider-failedlist');
            }
    }

    public function rider_failedlist() {
        $data['results'] = $this->login_model->get_rider_failed();
            $data['active'] = 4;
            $this->load->view('inc/header', $data);
            $this->load->view('rider/riderfailedlist', $data);
            $this->load->view('inc/footer');
    }

    public function rider_payment() {
        $id = $this->uri->segment(3);
        $data['active'] = 1;
        if ($id) {
            $ratecard_maxkilometer=$this->input->post('ratecard_maxkilometer', TRUE);
            ($ratecard_maxkilometer!=null) ? "$ratecard_maxkilometer"+" km" : "";
            $a = "$ratecard_maxkilometer";
            $b = $a . " km";
            $update_rate = array(
                'ratecard_basefare' => $this->input->post('ratecard_basefare', TRUE),
                'ratecard_kilometer' => $this->input->post('ratecard_kilometer', TRUE),
                'ratecard_perkilometer' => $this->input->post('ratecard_perkilometer', TRUE),
                'ratecard_maxkilometer' => $b,
                'ratecard_time' => $this->input->post('ratecard_time', TRUE),
                'ratecard_tax' => $this->input->post('ratecard_tax', TRUE),
            );
            $update_result = $this->login_model->ratecard_update($update_rate, $id);
            if ($update_result) {
                $this->session->set_flashdata('success', 'The User RateCard Updated!!!');
                redirect('login/dashboard');
            } else {
                redirect('dashboard/rider_payment');
            }
        }
        $data['result'] = $this->login_model->user_ratecard();
        $this->load->view('inc/header', $data);
        $this->load->view('payment/index', $data);
        $this->load->view('inc/footer');
    }
    function user_attempts() {
            $customer_count=0;
            $data['daily_attempts'] = $this->User_booking_attempts_model->get_dailyattempts_count();
            $data['overall_attempts'] = $this->User_booking_attempts_model->count_rows();
            // $customers = $this->User_booking_attempts_model->group_by('book_userid')->count_rows();
            $customers = $this->User_booking_attempts_model->get_user_count();
            if($customers!=null) {
            $customer_count=$customers;
            }
            $data['customers']=$customer_count;
            $data['results'] = $this->User_booking_attempts_model->order_by('attempt_id','Desc')->get_all();
            if ($data['results']) {
                foreach ($data['results'] as $result) {
                    $result->user_details = "";
                    $result->rider_details = "";
                    $result->user_details = $this->login_model->get_user_name($result->book_userid);
                    $result->rider_details = $this->login_model->get_rider_all($result->allocate_riderid,$result->receive_riderid,$result->book_decline_id);
                    $result->cancel_reason = $this->login_model->get_cancel_reason($result->book_cancelreason);
                    $result->rider_allocate_details = $this->login_model->get_rider_name($result->allocate_riderid);
                    $date_a = new DateTime(date('Y-m-d H:i:s',strtotime($result->book_starttime)));
                    $date_b = new DateTime(date('Y-m-d H:i:s',strtotime($result->book_endtime)));
                    $interval = date_diff($date_a,$date_b);
                    $result->intervaltaken=$interval->format('%H: %i: %s');
                }
            }
        $data['active'] = 13;
        $this->load->view('inc/header', $data);
        $this->load->view('attempts/index', $data);
        $this->load->view('inc/footer');
    }

    //Rider Profile..!!!
    public function rider_profile() {
        $rider_id = $this->uri->segment(3);
        if (!$rider_id) {
            $this->session->set_flashdata('error', 'Something went wrong please try again!!!');
            redirect('rider-list');
        } else {
            //Get Rider Details
            $data['rider'] = $this->login_model->get_rider_name($rider_id);
            $data['rider']->rider_count = $this->login_model->get_rider_total($rider_id);
            // Completed ride kilometers
            $data['rider']->rider_kilometer_count = $this->Apply_subscribe_model->get_single_rider_kms($rider_id);
            $data['rider']->rider_subscription = $this->Apply_subscribe_model->get_maxOne($rider_id);
            $data['rider']->total_earnings = $this->User_booking_model->total_earnings($rider_id);
            $data['rider']->ride_count = $this->User_booking_model->count_rows(array('allocate_riderid' => $rider_id));
            $data['rider']->annual_rides = $this->User_booking_model->get_yearly_rides($rider_id);
            $data['rider']->month_rides = $this->User_booking_model->get_month_rides($rider_id);
            $data['rider']->today_rides = $this->User_booking_model->get_today_rides($rider_id);
            $data['rider']->subscribe_count = $this->Apply_subscribe_model->count_rows(array('apply_riderid' => $rider_id));
            if ($data['rider']) {
                $data['active'] = 4;
                $this->load->view('inc/header', $data);
                $this->load->view('rider/profile/index', $data);
                $this->load->view('inc/footer');
            } else {
                $this->session->set_flashdata('error', 'No records found !!!');
                redirect('rider-list');
            }
        }
    }

    public function rider_catalogue() {
        $rider_id = $this->input->get('rider_id');
        if (!$rider_id) {
            $this->session->set_flashdata('error', 'Something went wrong, Please try again !!!');
            redirect('rider-list');
        } else {
            $data['rider'] = $this->Rider_registration_model->get($rider_id);
            $results = $this->login_model->get_ride_catelogue($rider_id);
            if (!$results) {
                $this->session->set_flashdata('error', 'No Rides Yet !!!');
                redirect('dashboard/rider_profile/' . $rider_id);
            } else {
                foreach ($results as $result) {
                    $result->user_details = "";
                    $result->rider_details = "";
                    $result->user_details = $this->login_model->get_user_name($result->book_userid);
                }
            }
            $data['results'] = $results;
            $data['active'] = 4;
            $this->load->view('inc/header', $data);
            $this->load->view('rider/profile/riderlist', $data);
            $this->load->view('inc/footer');
        }
    }

    public function rider_subscribtionList() {
        $rider_id = $this->input->get('rider_id');
        if (!$rider_id) {
            $this->session->set_flashdata('error', 'Something went wrong, Please try again !!!');
            redirect('rider-list');
        } else {
            $results = $this->Apply_subscribe_model->order_by('apply_status','Desc')->get_all(array('apply_riderid' =>$rider_id));
            if (!$results) {
                $this->session->set_flashdata('error', 'No Subscription Yet !!!');
                redirect('dashboard/rider_profile/' . $rider_id);
            } else {
                foreach ($results as $result) {
                    $result->subscribe_details = $this->Apply_subscribe_model->get_subscribe_details($result->apply_subscribeid);
                }
            }
            // echo "<pre>";print_r($results);exit;
            $data['results'] = $results;
            $data['active'] = 4;
            $this->load->view('inc/header', $data);
            $this->load->view('rider/ridersubscribe/index', $data);
            $this->load->view('inc/footer');
        }
    }

    public function heat_map() {
        $data['active'] = 10;
        $riders = $this->login_model->get_onlineRiderlist();
        $users = $this->login_model->get_onlineUserlist();
        if ($riders) {
            $lat = $riders[0]->rider_latitude;
            $long = $riders[0]->rider_longitude;
            $rider_id=$riders[0]->rider_id;
            $user = 'parking'; // To show Customer.!!!
            $rider = 'info'; // To show Rider.!!!
            $location = array("{ position: new google.maps.LatLng($lat, $long),type: '$rider',userid:'$rider_id' },");
            for ($i = 0; $i < count($users); $i++) {
                $userid=$users[$i]->user_id;
                $lat = $users[$i]->user_latitude;
                $long = $users[$i]->user_longitude;
                array_push($location, " { position: new google.maps.LatLng($lat, $long),type: '$user',userid:'$userid' },");
            }
            for ($i = 0; $i < count($riders); $i++) {
                $lat = $riders[$i]->rider_latitude;
                $long = $riders[$i]->rider_longitude;
                array_push($location, " { position: new google.maps.LatLng($lat, $long),type: '$rider',userid:'$rider_id' },");
            }
        }
        $data['location'] = $location;
        $this->load->view('inc/header', $data);
        $this->load->view('heatmap/index', $data);
        $this->load->view('inc/footer');
    }

    public function get_subscription() {
        $sub_id=$_POST['subscibe_id'];
        if($sub_id) {
            $subscribe_details=$this->Subscribe_details_model->get($sub_id);
        }
        echo json_encode($subscribe_details);
    }


    /*
     * EMERGENCY NOTIFY
     * Current Develop : R.K !!!
     * Date: 23-07-2018 searchridercancel
     * created_by: Rakesh.R
     */

     public function emergency_notify() {
        $emergency=$this->Emergency_model->order_by('id','Desc')->get_all();
        if($emergency) {
            $this->login_model->update_emergency_status();
            foreach($emergency as $s) {
                $s->user_details=$this->login_model->get_user_name($s->user_id);
                $s->rider_details=$this->login_model->get_rider_name($s->rider_id);
                $s->booking_details=$this->login_model->get_booking_name($s->rider_id);
            }
        }
        $data['active'] = 12;
        $data['emergency']=$emergency;
        $this->load->view('inc/header', $data);
        $this->load->view('emergency/index', $data);
        $this->load->view('inc/footer');
     }

     public function get_emergency_notify() {
        $result=$this->login_model->get_notify();
        $total_emergency = $this->login_model->get_emergency_count();
        $this->session->set_userdata('emergency',($total_emergency!=null) ? $total_emergency : "0");
        $emergency->result=$result;
        $emergency->total_count=$total_emergency;
         echo json_encode($emergency);
     }
     public function get_newBooking() {
         $booking_count=$_SESSION['booking_count'];
         $total_great=$this->login_model->get_booking_count();
         if($total_great > $booking_count) {
            $emergency=array('total_count'=>$total_great,'new_book'=>1);
            $this->session->set_userdata('booking_count',$total_great);
         } else {
            $emergency=array('total_count'=>$booking_count,'new_book'=>0);
         }
        echo json_encode($emergency);
     }
     public function update_emergency_status() {
         $id=$this->input->post('emergency_id');
         if($id) {
             $result=$this->login_model->update_emergency($id);
         }
         echo json_encode($result);
     }
     public function mapliveview() {
         $id=$this->uri->segment(3);
         if($id) {
             $data['active']=2;
             $this->load->view('transaction/tracker');
         }
     }
     public function getbookingdetails() {
         $id = $this->input->post('id');
         echo $id;
     }
     function total_amount() {
         $results=$this->Subscribe_details_model->get_subscribe_list_total();
         $sum =0;
         if($results) {
             foreach($results as $result) {
            $sum +=$result->total_rider_amount;
             }
         }

         $data['results']=$results;
         $data['total']=$sum;
         $data['active'] = 1; //Navbar section: Active!!!
         $this->load->view('inc/header', $data);
         $this->load->view('dashboard/total_amount/index',$data);
         $this->load->view('inc/footer');
     }
     function total_kilometers() {
         $rider_list=$this->Subscribe_details_model->get_rider_list();
         $total=0;
         foreach($rider_list as $list) {
        $results=$this->Subscribe_details_model->get_riderkilometer_total($list->rider_id);
            $sum =0;
            if($results) {
                foreach($results as $result) {
                    $s = substr($result->book_distance, 0, -3);
                    $sum +=$s;
                }
                $list->rider_distance=$sum;
            }
            $total +=$list->rider_distance;
        }
        $data['results']=$rider_list;
        $data['total']=$total;
        $data['active'] = 1; //Navbar section: Active!!!
        $this->load->view('inc/header', $data);
        $this->load->view('dashboard/total_kilometer/index',$data);
        $this->load->view('inc/footer');
     }
     function get_address() {
        $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=12.978365,80.238757&sensor=false";

        // Make the HTTP request
        $data = @file_get_contents($url);
        // Parse the json response
        $jsondata = json_decode($data,true);
        echo "<pre>";
        print_r($jsondata);
        exit;
     }
     function get_rider_location() {
         echo "<pre>";
        $s = $this->Apply_subscribe_model->get_subscribe_details(1);
        print_r($s);exit;
     }

     function sending_curl() {
                //API URL
        $url = 'http://192.168.1.21/Rakesh/index.php';
        //create a new cURL resource
        $ch = curl_init($url);
        //setup request to send json via POST
        $data = array(
            'from' => 'codexworld',
            'action' => '123456'
        );
        $payload = json_encode(array("user" => $data));
        //attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        //set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        //return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //execute the POST request
        $result = curl_exec($ch);
        //close cURL resource
        curl_close($ch);

                // $url="http://192.168.1.21/Rakesh/index.php";
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL, $url);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // $response = curl_exec($ch);
                // curl_close($ch);
     }
     function rider_tracker($rider_id) {
            $data['active'] = 4;
            $data['rider_id']=$rider_id;
            $data['riderdetails']=$this->Rider_registration_model->get($rider_id);
            $this->load->view('inc/header', $data);
            $this->load->view('map/ridertracker', $data);
            $this->load->view('inc/footer');
     }
     function get_rider_latlong() {
        $rider_id = $this->input->post('rider_id', TRUE);
        $latitude=$this->input->post('latitude', TRUE);
        $longitude=$this->input->post('longitude', TRUE);
        if ($rider_id == null && $type == null) {
            $data = file_get_contents("php://input");
            $data = (array) json_decode($data);
            $rider_id = $data['rider_id'];
            $type = $data['type'];
            $latitude= $data['latitude'];
            $longitude= $data['longitude'];
        }
        $_SESSION['rider_latitude']=$latitude;
        $_SESSION['rider_longitude']=$longitude;
        $res=$this->Rider_registration_model->get($rider_id);
        $rider_lat=$_SESSION['rider_latitude'];
        $rider_long=$_SESSION['rider_longitude'];
        if($rider_lat==$res->rider_latitude) {
            $data['action']="2";
        } else {
            $data['action']="1";
        }
        $data['rider_lat']=$rider_lat;
        $data['rider_long']=$rider_long;
        $data['result']=$res;
        $data['status'] = '1';
        $data['message'] = 'Latitude and Longitude';
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
        $_SESSION['rider_latitude']=$res->rider_latitude;
        $_SESSION['rider_longitude']=$res->rider_longitude;
    }
    public function update_rider_latlong() {
        $id=$this->input->post('rider_id');
        $result=$this->Rider_registration_model->testing_rider_location($this->input->post('update_id'));
        $r=$this->webservice_model->rider_latitudelongitude_check($id,$result[0]->rider_latitude,$result[0]->rider_longitude);
        if($r) {
            $data['status'] = '1';
        } else {
            $data['status'] = '2';
        }
        $data['message'] = 'Latitude and Longitude update';
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
    }
    public function get_userinfo() {
        /*
        NOTE: Rider/User Based on ICON we are defining the Usertype and sending the respone
        parking == User
        info == Rider
        */
        $type=substr($this->input->post('title'), strpos($this->input->post('title'), ",") + 1);
        $id=$this->input->post('userid');
        if($type=='parking') {
            $data['result']=$this->login_model->get_user_name($id);
        } else {
            $data['result']=$this->login_model->get_rider_name($id);
        }
        $data['status'] = '1';
        $data['message'] = 'Latitude and Longitude update';
        $data = array('response' => $data);
        $this->load->view('webservices/json', $data);
    }
    public function distance() {
        $lat1=$this->input->get('lat1');
        $lon1=$this->input->get('long1');
        $lat2=$this->input->get('lat2');
        $lon2=$this->input->get('long2');
        $unit=$this->input->get('unit');
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
          return 0;
        }
        else {
          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $unit = strtoupper($unit);

          if ($unit == "K") {
            echo  ($miles * 1.609344);
          } else if ($unit == "N") {
            echo ($miles * 0.8684);
          } else {
            echo $miles;
          }
        }
      echo distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
      echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
      echo distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";
    }
    function sending_mail() {
        echo 1;
        // $this->load->library('email');
        // $this->email->to('rakesh@thinksynq.in');
        // $this->email->from('rakesh@thinksynq.in','MuscleMemory');
        // $this->email->subject('Muscle Memory');
        // $this->email->message('Text email testing by CodeIgniter Email library.');
        // $this->email->send();
        $msg = "First line of text\nSecond line of text";
        // use wordwrap() if lines are longer than 70 characters
        $msg = wordwrap($msg,70);
        // send email
        $result= mail("parthiban@thinksynq.in","OK fine ",$msg);
        print_r($result);exit;
    }
    function rider_location_update() {
        $riderdetails=$this->Rider_registration_model->get_last_updates();
        foreach($riderdetails as $rider) {
        $logindetails = $this->webservice_model->getrideriddetail($rider->rider_id);
        if ($logindetails) {
            $ids = $logindetails->gcm_userid;
            $apikeyid = $this->webservice_model->getapikey();
            $apiKey = $apikeyid->apikey;
            $registatoin_ids = $logindetails->gcm_regid;
            $message = "Please update your current location to get rides";
            $task = "location_riderupdate";
            $title = "PiggyBack";
            $url = $apikeyid->url;
            $fields = array(
                'to' => $registatoin_ids,
                'priority' => "high",
                'data' => array("message" => $message, "task" => $task, "title" => $title)
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
    function subtract_time() {
        echo $hour_ago = date('Y-m-d H:i:s',strtotime('-30 minute'));exit;
    }

}
